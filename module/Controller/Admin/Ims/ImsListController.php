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
use SlComponent\Util\ExcelCsvUtil;
use SlComponent\Util\SitelabLogger;
use SlComponent\Util\SlCodeMap;
use SlComponent\Util\SlLoader;
use function SlComponent\Util\SlLoader;

/**
 * 문서 리스트
 */
class ImsListController extends \Controller\Admin\Controller{

    use ImsControllerTrait;
    use ImsStepTrait;

    public function index(){

        $current_page = Request::getRequestUri();
        //gd_debug( Request::getReferer() );

        if (!empty( \Session::get('last_page') ) && \Session::get('last_page') === $current_page) {
            $this->setData('isReload', 'y');
        } else {
            \Session::set('last_page',$current_page);
            $this->setData('isReload', 'n');
        }

        $request = \Request::request()->toArray();
        $midType = 'project';

        $initStatus = gd_isset($request['status'],'');
        if( 0 == $initStatus ) {
            $initStatus='';
            $title = '전체 목록';
            $allProgress = ImsCodeMap::PROJECT_STATUS;
            unset($allProgress[98]); //보류 상태 제외
            unset($allProgress[99]);
            $this->setData('chkOrderProgress',implode(',',array_keys($allProgress)));
        }else{
            if( $initStatus > 90 ){
                $midType = 'reserved';
            }
            $title = ImsCodeMap::PROJECT_STATUS[$initStatus];
            $this->setData('chkOrderProgress',\Request::get()->get('status'));
        }



        //FIXME
        //$this->callMenu('ims', $midType, 'step'.$initStatus);

        if( 60 == $initStatus ){
            $imsi = 'qc';
        }else{
            $imsi = 'complete';
        }
        $this->callMenu('ims', 'prj', $imsi);


        $this->setDefault();

        $search['combineSearch'] = [
            'cust.customerName' => '고객명',
            'prj.sno' => '프로젝트번호',
            'sales.managerNm' => '영업담당자',
            'desg.managerNm' => '디자인담당자',
            'prd.styleCode' => '스타일코드',
            'prd.productName' => '스타일명',
        ];
        $this->setData('search', $search);


        //리스트 셋업
        $listSetupFnc = 'setList'.$initStatus;
        $methods = SlCommonUtil::getMethodMap(__CLASS__);
        if( !empty($methods[$listSetupFnc]) ){
            $listSetup = $this->$listSetupFnc();
        }else{
            $listSetup = $this->setList();
        }

        $this->setData('listSetupData', $listSetup);
        $this->setData('qbAvailProjectType',[0,6,2]); //신규, 공개입찰, 리오더 개선
        $this->setData('defaultRowspan', $listSetup['defaultRowspan']);

        $this->setData('title', $title);

        if(  !empty($request['simple_excel_download'])  ){
            $this->simpleExcelDownload($request);
            exit();
        }
    }


    /**
     * 엑셀 다운로드.
     * @param $request
     */
    public function simpleExcelDownload($request){

        $qbAvailProjectType = $this->getData('qbAvailProjectType');
        $listSetupData = $this->getData('listSetupData');

        $imsService = SlLoader::cLoad('ims', 'imsService');

        $list = $imsService->getListProjectWithAddInfo(['condition'=>$request]);

        $excelBody = '';

        //타이틀 설정
        $title[] = '번호';
        foreach($listSetupData['list'] as $key => $each) {
            if('고객사/프로젝트번호' === $each['title']){
                $title[] = '상태';
                $title[] = '프로젝트번호';
                $title[] = '고객사';
            }else{
                $title[] = $each['title'];
            }
        }

        //본문
        foreach($list['list'] as $key => $val){
            $excelBody .= "<tr>";

            // --- fixed begin
            $excelBody .= ExcelCsvUtil::wrapTd( count($list['list']) - $key );
            $excelBody .= ExcelCsvUtil::wrapTd( $val['regDt'] );
            $excelBody .= ExcelCsvUtil::wrapTd( $val['projectYear'].' '.$val['projectSeason'] );
            $excelBody .= ExcelCsvUtil::wrapTd( $val['projectTypeKr'].' '.$val['bidType'] );
            $excelBody .= ExcelCsvUtil::wrapTd( $val['projectStatusKr'] );
            $excelBody .= ExcelCsvUtil::wrapTd( $val['projectNo'] );
            $excelBody .= ExcelCsvUtil::wrapTd( $val['customerName'] );

            if( 2 === $val['productionStatus'] ){
                $excelBody .= ExcelCsvUtil::wrapTd( '생산완료' );
                $excelBody .= ExcelCsvUtil::wrapTd( '생산완료' );
            }else{

                if( '0000-00-00' == $val['customerDeliveryDt'] || empty($val['customerDeliveryDt']) ){
                    $excelBody .= ExcelCsvUtil::wrapTd( '미정' );
                }else{
                    $excelBody .= ExcelCsvUtil::wrapTd( $val['customerDeliveryDt'] );
                }

                if( '0000-00-00' == $val['customerOrderDeadLine'] || empty($val['customerOrderDeadLine']) ){
                    $excelBody .= ExcelCsvUtil::wrapTd( '미정' );
                }else{
                    $excelBody .= ExcelCsvUtil::wrapTd( $val['customerOrderDeadLine'] );
                }
            }

            $excelBody .= ExcelCsvUtil::wrapTd( $val['customerSizeKr'] );

            //스타일
            if( 0 >= $val['prdCnt'] ){
                $excelBody .= ExcelCsvUtil::wrapTd( '스타일 파악중' );
            }else{
                $excelBody .= ExcelCsvUtil::wrapTd( $val['styleName'] );
            }

            if( empty($requestParam['status']) || $requestParam['status'] >= 20 ) {
                if(in_array($val['projectType'], $qbAvailProjectType)){
                    $excelBody .= ExcelCsvUtil::wrapTd( $val['fabricStatusKr'] );
                    $excelBody .= ExcelCsvUtil::wrapTd( $val['btStatusKr'] );
                }else{
                    $excelBody .= ExcelCsvUtil::wrapTd( '해당없음' );
                    $excelBody .= ExcelCsvUtil::wrapTd( '해당없음' );
                }
            }
            // --- fixed end

            //전체/발주완료....
            $excelBody .= ExcelCsvUtil::wrapTd( number_format($val['totalQty']) );
            $excelBody .= ExcelCsvUtil::wrapTd( number_format($val['prdCost']) );
            $excelBody .= ExcelCsvUtil::wrapTd( number_format($val['prdPrice']) );
            $excelBody .= ExcelCsvUtil::wrapTd( number_format($val['prdMargin']) );
            $excelBody .= ExcelCsvUtil::wrapTd( 'y' === $val['use3pl'] ? '예':'아니오'  );
            $excelBody .= ExcelCsvUtil::wrapTd( 'y' === $val['useMall'] ? '예':'아니오'  );

            // --- 가변

            //한줄 값 . TODO 작업 / 우선 전체 리스트 및 발주 완료에서만 다운로드 가능.
            /*foreach($listSetupData['list'] as $eachKey => $each) {
                if(!empty($each['field']) && empty($each['split']) && 'split' !== $each['field'] && !isset($each['split'])  ) {
                    if( 'manager' === $each['field']) {
                        $excelBody .= ExcelCsvUtil::wrapTd($val['salesManagerNm'] . ' ' . $val['designManagerNm']);
                    }else{
                        if( 10 == $requestParam['status'] ) {

                        }else{

                        }
                    }
                }
            }*/


            $excelBody .= "</tr>";
        }

        $simpleExcelComponent = SlLoader::cLoad('Excel','SimpleExcelComponent','sl');
        $simpleExcelComponent->simpleDownload('프로젝트리스트', $title, $excelBody);

    }

}
