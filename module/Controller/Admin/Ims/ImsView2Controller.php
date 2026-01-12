<?php

namespace Controller\Admin\Ims;

use App;
use Component\Category\BrandAdmin;
use Component\Category\CategoryAdmin;
use Component\Ims\EnumType\PREPARED_TYPE;
use Component\Ims\ImsCodeMap;
use Component\Ims\ImsDBName;
use Component\Ims\ImsJsonSchema;
use Component\Stock\StockListService;
use Component\Work\WorkCodeMap;
use Controller\Admin\Erp\AdminErpControllerTrait;
use Controller\Admin\Erp\ControllerService\InoutListService;
use Controller\Admin\Ims\Step\ImsStepTrait;
use Globals;
use Request;
use SiteLabUtil\SlCommonUtil;
use SlComponent\Database\DBUtil2;
use SlComponent\Godo\ControllerService;
use SlComponent\Util\ExcelCsvUtil;
use SlComponent\Util\SlCodeMap;
use SlComponent\Util\SlLoader;
use function SlComponent\Util\SlLoader;

/**
 * 프로젝트 상세 ( 최신 버전 25/04/18 )
 */
class ImsView2Controller extends \Controller\Admin\Controller
{

    use ImsControllerTrait;
    use ImsStepTrait;

    public function index()
    {
        ControllerService::setReloadData($this);

        $request = \Request::request()->toArray();
        $this->setData('projectKey', SlCommonUtil::aesEncrypt($request['sno']));

        $imsService = SlLoader::cLoad('ims', 'imsService');
        $imsService->setSyncStatus($request['sno']);

        $this->setDefault();

        //메뉴 설정 및 TabMode 설정
        $requestParam = $this->getData('requestParam');

        //영업상태 확인 (완료 건은 기획으로) , TabMode가 강제로 들어오지 않으면 처리 .
        //all  sales  design reorder  qc complete finish

        $current = gd_isset($request['current'], 'all');

        if (!empty($request['status'])) {
            if (90 > $request['status']) {
                $current = 'sales';
            } else {
                $current = 'complete';
            }
        } else if (!empty($request['currentStatus'])) {
            if (60 > $request['currentStatus']) {
                $current = 'reorder';
            } else {
                $current = 'qc';
            }
        }


        $this->callMenu('ims', 'prj', $current);

        /*if( empty($requestParam['tabMode']) ){
            if('reorder' == $requestParam['status']){
                //리오더 리스트
                $this->callMenu('ims', 'prj', 'reorder'); //리오더
                $requestParam['tabMode'] = 'reorder';
                $this->setData('requestParam',$requestParam);
            }else if('90' == $requestParam['status']){
                $this->callMenu('ims', 'prj', 'complete'); //메뉴 단계
                $requestParam['tabMode'] = 'order';
                $this->setData('requestParam',$requestParam);
            }else if('60' == $requestParam['status']){
                $this->callMenu('ims', 'prj', 'qc'); //메뉴 단계
                $requestParam['tabMode'] = 'order';
                $this->setData('requestParam',$requestParam);
            }else if( 'complete' !== $requestParam['status'] ){
                $this->callMenu('ims', 'prj', 'sales'); //메뉴 단계
                $requestParam['tabMode'] = 'sales';
                $this->setData('requestParam',$requestParam);
            }else{
                $this->callMenu('ims', 'prj', 'design'); //메뉴 단계
                $requestParam['tabMode'] = 'design';
                $this->setData('requestParam',$requestParam);
            }
        }*/


        $this->setData('designField', ImsCodeMap::PROJECT_DESIGN_FIELD);
        $this->setData('addedInfo', ImsJsonSchema::ADD_INFO);

        if (!empty($request['popup']) || !empty($request['modify'])) {
            $this->getView()->setDefine('layout', 'layout_blank.php');
        }

        if (isset($request['testmode']) && $request['testmode'] == 1) $this->getView()->setPageName("ims/ims_view2_dev_0918.php");
        else $this->getView()->setPageName("ims/ims_view2.php");

        $customerInfoField = $this->setCustInfoField();
        $this->setData('customerInfoField', $customerInfoField);

        $this->setData($request['status'], 'text-danger');
        $this->setData('isProjectViewPage', true);

        $this->setData('prdSetupData2', $this->setupProductListType2()); //스타일 기본
        $this->setData('prdSetupDataSample', $this->setupProductListTypeSample()); //샘플
        $this->setData('prdSetupDataAssort', $this->setupProductListTypeAssort()); //아소트 타이틀
        $this->setData('prdSetupDataOrder', $this->setupProductListTypeOrder()); //아소트 타이틀
        $this->setData('prdSetupDataReorder', $this->setupProductListTypeReorder()); //아소트 타이틀

        $this->setData('guideUrl', SlCommonUtil::getHost() . '/ics/ics_guide.php'); //사양서
        $this->setData('assortUrl', SlCommonUtil::getHost() . '/ics/ics_assort.php'); //아소트
        $this->setData('eworkUrl', SlCommonUtil::getHost() . '/ics/ics_work.php'); //작지
    }

    /**
     * 스타일 탭 : 스타일 기본 (VIEW2 신버전 사용)
     * @return \array[][]
     */
    public function setupProductListType2()
    {
        return [
            'list' => [
                ['이미지', 5],
                ['상품명', 16],
                ['샘플', 5],
                ['납기일', 9],
                ['수량', 7],
                ['생산가<br><span class="font-11 font-normal">(부가세제외)</span>', 7],
                ['판매단가<br><span class="font-11 font-normal">(부가세제외)</span>', 7],
                ['마진', 3],
                /* ['발주수량 변동',6],*/
                ['MOQ', 4],
                /*['재고 보유 현황',6],*/

                ['퀄리티', 4],
                ['BT', 4],
                ['작업지시서', 6],
                ['기획정보', 4],

            ]
        ];
    }

    /**
     * 스타일 탭 : 스타일 리오더
     * @return \array[][]
     */
    public function setupProductListTypeReorder()
    {
        return [
            'list' => [
                ['이미지', 5],
                ['상품명', 13],
                ['수량', 5],
                ['납기일', 8],
                ['판매단가<br><span class="font-11 font-normal">(부가세제외)</span>', 5],
                ['생산가<br><span class="font-11 font-normal">(부가세제외)</span>', 5],
                ['마진', 3],
                ['고객MOQ', 4],
                ['생산MOQ', 4],
                ['단가MOQ', 4],
                ['작업지시서', 4],
            ]
        ];
    }

    /**
     * 스타일 탭 : 샘플
     * @return \array[][]
     */
    public function setupProductListTypeSample()
    {
        return [
            'list' => [
                ['스타일', 10],
                ['번호', 3],
                ['제작차수', 5],
                ['샘플구분', 5],
                ['스타일기획', 8],
                ['샘플명', 10],
                ['패턴실', 8],
                ['샘플실', 8],
                ['수량', 4],
                ['제작비용', 5],
                ['샘플지시서',5], //파일
                ['샘플리뷰서',5], //파일
                ['샘플확정서',5], //파일
                ['샘플투입일', 5], //날짜
                ['샘플실마감일', 5], //날짜
                ['샘플위치', 5],
                ['메모', 0], //메모
            ]
        ];
    }

    /**
     * 스타일 탭 : 아소트
     * @return \array[][]
     */
    public function setupProductListTypeAssort()
    {
        return [
            'list' => [
                ['이미지', 5],
                ['시즌년도', 4],
                ['상품명', 15],
                ['수량', 5],
                ['MOQ', 5],
                ['고객 발주수량', 68],
            ]
        ];
    }

    /**
     * 발주 상품
     * @return \array[][]
     */
    public function setupProductListTypeOrder()
    {
        return [
            'list' => [
                ['이미지', 5],
                ['상품명', 13],
                ['생산처', 5],
                ['<span class="font-12">생산형태<span>', 3],
                ['국가', 3],
                ['샘플', 3],
                ['수량', 5],
                ['납기일', 7],
                ['판매단가<br><span class="font-11 font-normal">(부가세제외)</span>', 5],
                ['생산가<br><span class="font-11 font-normal">(부가세제외)</span>', 5],
                ['마진', 3],
                ['고객MOQ', 4],
                ['생산MOQ', 4],
                ['단가MOQ', 4],
                ['작업지시서', 4],
                ['QB', 6],
                /*['기획정보',4],*/
            ]
        ];
    }

    /**
     * 고객 정보 필드 셋팅
     * @return array
     */
    public function setCustInfoField()
    {
        $rslt = [];

        $setField = [
            'info089' => ['type' => 'text'],
            'info088' => ['type' => 'radio', 'code' => 'existType3'],
            //근무환경
            'etc1' => ['type' => 'text'],
            'etc4' => ['type' => 'text'],
            //성향
            'info009' => ['type' => 'radio', 'code' => 'ratingType'],
            'info010' => ['type' => 'radio', 'code' => 'ratingType'],
            'info011' => ['type' => 'radio', 'code' => 'ratingType'],
            'info012' => ['type' => 'radio', 'code' => 'ratingType'],
            //미팅 취득 정보
            'info015' => ['type' => 'radio', 'code' => 'ratingType'],
            'etc3' => ['type' => 'text'],
            'info003' => ['type' => 'radio', 'code' => 'ableType'],
            'info072' => ['type' => 'radio', 'code' => 'ableType'],
            'info004' => ['type' => 'radio', 'code' => 'existType'],
            'info101' => ['type' => 'text'],
            'info102' => ['type' => 'text'],
            'info103' => ['type' => 'radio', 'code' => 'ableType'],


            'info108' => ['type' => 'text'],
            'info109' => ['type' => 'text'],
            'info110' => ['type' => 'text'],
            'info111' => ['type' => 'radio', 'code' => 'ratingType'],
            'info112' => ['type' => 'text'],
            'info113' => ['type' => 'text'],
            'info114' => ['type' => 'text'],
            'info115' => ['type' => 'text'],
            'info116' => ['type' => 'text'],
            'info117' => ['type' => 'text'],


            'etc5' => ['type' => 'text'],
            'etc7' => ['type' => 'text'],
            //안내/제한사항
            'info104' => ['type' => 'text'],
            'info105' => ['type' => 'text'],
            'info106' => ['type' => 'text'],
            'info107' => ['type' => 'text'],
        ];

        foreach ($setField as $key => $value) {
            if (!empty(ImsJsonSchema::CUSTOMER_ADDINFO[$key])) {
                $rslt[$key] = [
                    'title' => ImsJsonSchema::CUSTOMER_ADDINFO[$key],
                    'valueKey' => $key,
                    'type' => $value['type'],
                    'code' => $value['code'],
                ];
            }
        }
        return $rslt;
    }

}