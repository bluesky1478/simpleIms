<?php

namespace Controller\Admin\Ims;

use App;
use Component\Category\BrandAdmin;
use Component\Category\CategoryAdmin;
use Component\Ims\EnumType\PREPARED_TYPE;
use Component\Ims\ImsCodeMap;
use Component\Ims\ImsJsonSchema;
use Component\Stock\StockListService;
use Component\Work\WorkCodeMap;
use Controller\Admin\Erp\AdminErpControllerTrait;
use Controller\Admin\Erp\ControllerService\InoutListService;
use Controller\Admin\Ims\Step\ImsStepTrait;
use Globals;
use Request;
use SiteLabUtil\SlCommonUtil;
use SlComponent\Godo\ControllerService;
use SlComponent\Util\ExcelCsvUtil;
use SlComponent\Util\SlCodeMap;
use SlComponent\Util\SlLoader;
use function SlComponent\Util\SlLoader;

/**
 * 프로젝트 상세 ( 최신 버전 24/07/25 )
 */
class ImsProjectViewController extends \Controller\Admin\Controller{

    use ImsControllerTrait;
    use ImsStepTrait;

    public function index(){
        ControllerService::setReloadData($this);

        $request = \Request::request()->toArray();
        $this->setData('projectKey', SlCommonUtil::aesEncrypt($request['sno']));

        $status = empty($request['status']) ? '' : $request['status'];
        if('99' === $status || '98' === $status){
            $midType = 'reserved';
        }else{
            $midType = 'project';
        }

        $status = 'step'.$status;
        //$this->callMenu('ims', $midType, $status); //메뉴 단계
        $this->callMenu('ims', 'prj', 'all');

        $this->setDefault();
        $this->setData('designField',ImsCodeMap::PROJECT_DESIGN_FIELD);
        $this->setData('addedInfo', ImsJsonSchema::ADD_INFO);

        if( !empty($request['popup']) || !empty($request['modify']) ){
            $this->getView()->setDefine('layout', 'layout_blank.php');
        }
        if( $this->getData('isProduceCompany') ){
            $this->getView()->setDefine('layout', 'layout_blank.php');
            $this->getView()->setPageName("ims/ims_project_view_produce.php");
        }

        if( !empty($request['modify']) ){
            $this->getView()->setPageName("ims/ims_pop_simple_project.php");
        }else{
            //TODO : 공장은 별도 페이지로 보여줄 수 있게 한다.
            $this->getView()->setPageName("ims/ims_project_view.php"); // Template 확인

            //FIXME : 추후 작업할 때는 각 상태별 어떤 요소가 들어가는 형태로 유동적 변환 시킨다.
            $this->setData('prdSetupDataSample', $this->setupProductListTypeSample()); //샘플
            $this->setData('prdSetupData2', $this->setupProductListType2()); //스타일 기본
            $this->setData('prdSetupData', $this->setupProductListType1()); //단독입찰
            $this->setData('prdSetupDataAssort', $this->setupProductListTypeAssort()); //스타일 기본
            /*
            $this->setData('prdSetupData3', $this->setupProductListType3());
            */
        }
        $this->setData($request['status'], 'text-danger');
        $imsService = SlLoader::cLoad('ims', 'imsService');
        $imsService->setSyncStatus($request['sno']);
        $this->setData('isProjectViewPage', true);

        $imsProjectViewService = SlLoader::cLoad('ims', 'imsProjectViewService');
        $imsProjectViewService->setProjectViewData($this);
    }

    public function setupProductListType1(){
        return [
            'list' => [
                ['이미지',5],
                /*['프로젝트타입',3],*/
                /*['시즌년도',4],*/
                ['상품명',18],
                ['제작수량',5],
                ['타겟 판매가',5],
                ['타겟 생산가',5],
                ['타겟 마진',5],
                /*['예상발주',5],*/
                ['희망납기',5],
                //['현 유니폼 불편 사항',15],
                //['지급기준',10],
            ]
        ];
    }

    public function setupProductListType2(){
        return [
            'list' => [
                ['이미지',5],
                //['프로젝트타입',3],
                /*['시즌년도',4],*/
                ['상품명',18],
                ['제작수량',5],
                ['고객 희망 납기일',5],
                ['타겟값',8],
                //['생산타입',5],
                //['생산기간',4],
                ['생산가',5],
                ['판매단가',5],
                ['마진',5],
                ['Q/B',8],
            ]
        ];
    }

    public function setupProductListType3(){
        return [
            'list' => [
                ['이미지',5],
                ['프로젝트타입',3],
                ['시즌년도',4],
                ['상품명',18],
                ['제작수량',5],
                ['고객 희망 납기일',5],
                ['예가',6],
                ['타겟생산가',5],
                ['마진',5],
                ['예상발주일',5],
                ['현 유니폼<br>불편 사항',8],
                ['샘플<br>확보유무',8],
                ['발주 물량 변동',8],
            ]
        ];
    }

    public function setupProductListTypeSample(){
        return [
            'list' => [
                ['스타일',10],
                ['번호',3],
                ['샘플명',15],
                ['샘플실',10],
                ['수량',4],
                ['제작비용',5],
//                ['기능',10],
                ['샘플지시서',12], //파일
                ['샘플리뷰서',12], //파일
                ['샘플투입일',5], //날짜
                ['샘플실마감일',5], //날짜
                ['메모',11], //메모
            ]
        ];
    }


    /**
     * 아소트
     * @return \array[][]
     */
    public function setupProductListTypeAssort(){
        return [
            'list' => [
                ['이미지',5],
                ['시즌년도',4],
                ['상품명',15],
                ['수량',5],
                ['MOQ',5],
                ['고객 발주수량',68],
            ]
        ];
    }

}