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
class ImsProductionListController extends \Controller\Admin\Controller{

    use ImsControllerTrait;

    public function index(){
        $request = \Request::request()->toArray();

        $initStatus = gd_isset($request['initStatus'],'');
        if( 0 == $initStatus ) $initStatus='';
        $this->callMenu('ims', 'request', 'production'.$initStatus);
        $this->setDefault();

        $search['combineSearch'] = [
            'cust.customerName' => '고객명',
            'prj.sno' => '프로젝트번호',
            'prd.styleCode' => '스타일코드',
            'prd.productName' => '상품명',
            'prd.sno' => '스타일번호',
        ];
        $this->setData('search', $search);

        $titleMap = [
            ''=>'생산스케쥴관리',
            '2'=>'스케쥴입력요청',
            '3'=>'스케쥴확정대기',
            '4'=>'기성복관리',
        ];
        $this->setData('productionTitle',$titleMap[$initStatus]);

        if(  !empty($request['simple_excel_download'])  ){

            $request['multiKey'] = json_decode($request['multiKey'],true);

            if( 2 == $request['listType'] ){
                $this->simpleExcelDownload2($request);
            }else{
                $this->simpleExcelDownload($request);
            }

            exit();
        }

    }


    public function simpleExcelDownload2($request){
        $title = [
            '고객사',
            '프로젝트',
            '프로젝트타입',
            '프로젝트상태',
            '스타일번호',
            '스타일명',
            '제품명',
            '스타일코드',
            '생산처',
            '수량',
            '판매가',
            '생산가',
            '시즌',
        ];
        $excelBody = '';
        $imsService = SlLoader::cLoad('ims', 'imsService');
        $list = $imsService->getListProduction(['condition'=>$request]);
        //기본 rowspan.
        foreach($list['list'] as $key => $val){

            $excelBody .= "<tr>";

            $excelBody .= ExcelCsvUtil::wrapTd($val['customerName']);
            $excelBody .= ExcelCsvUtil::wrapTd($val['projectNo']);
            $excelBody .= ExcelCsvUtil::wrapTd(ImsCodeMap::PROJECT_TYPE[$val['projectType']]);
            $excelBody .= ExcelCsvUtil::wrapTd($val['projectStatusKr']);
            $excelBody .= ExcelCsvUtil::wrapTd($val['styleSno']);
            $excelBody .= ExcelCsvUtil::wrapTd($val['styleFullName']);
            $excelBody .= ExcelCsvUtil::wrapTd($val['styleName'].$val['productName']);
            $excelBody .= ExcelCsvUtil::wrapTd($val['styleCode']);
            $excelBody .= ExcelCsvUtil::wrapTd($val['reqFactoryNm']);
            $excelBody .= ExcelCsvUtil::wrapTd(number_format($val['totalQty']));
            $excelBody .= ExcelCsvUtil::wrapTd(number_format($val['salePrice']));
            $excelBody .= ExcelCsvUtil::wrapTd(number_format($val['prdCost']));
            $excelBody .= ExcelCsvUtil::wrapTd(substr($val['prdYear'],2,2).$val['prdSeason']);

            $excelBody .= "</tr>";
        }

        $simpleExcelComponent = SlLoader::cLoad('Excel','SimpleExcelComponent','sl');
        $simpleExcelComponent->simpleDownload('생산스타일', $title, $excelBody);
    }


    public function simpleExcelDownload($request){
        $title = [
            '고객사',
            '프로젝트',
            '스타일명',
            '스타일코드',
            '이노버납기일',
            '스케쥴명',
        ];
        foreach(ImsCodeMap::PRODUCE_STEP_MAP as $key => $step){
            $title[] = $step;
        }
        $title[] = '생산처';
        $title[] = '입고지';
        $title[] = '폐쇄몰출고가능일';
        $title[] = '운송';
        $excelBody = '';
        $imsService = SlLoader::cLoad('ims', 'imsService');
        $list = $imsService->getListProduction(['condition'=>$request]);
        //기본 rowspan.
        foreach($list['list'] as $key => $val){
            $defaultRowspan = 4;
            $excelBody .= "<tr>";
            //wrapTd($str , $class=null, $style=null, $etcTag=null){

            if( $val['customerRowspan'] > 0 ){
                $excelBody .= ExcelCsvUtil::wrapTd($val['customerName'],null,null,'rowspan=' . $val['customerRowspan']*$defaultRowspan);
            }

            if( $val['projectRowspan'] > 0 ){
                $excelBody .= ExcelCsvUtil::wrapTd($val['customerName'].'<br>'.$val['projectNo'],null,null,'rowspan=' . $val['projectRowspan']*$defaultRowspan);
            }

            $excelBody .= ExcelCsvUtil::wrapTd($val['styleFullName'],null,null,'rowspan=' . $defaultRowspan);
            $excelBody .= ExcelCsvUtil::wrapTd($val['styleCode'],null,null,'rowspan=' . $defaultRowspan);
            $excelBody .= ExcelCsvUtil::wrapTd($val['msDeliveryDt'],null,null,'rowspan=' . $defaultRowspan);
            $excelBody .= ExcelCsvUtil::wrapTd('최초예정일');
            foreach(ImsCodeMap::PRODUCTION_STEP as $step){
                if(!empty($val['firstData']['schedule'][$step]['Memo'])){
                    $excelBody .=  ExcelCsvUtil::wrapTd($val['firstData']['schedule'][$step]['Memo']);
                }else{
                    $excelBody .=  ExcelCsvUtil::wrapTd($val['firstData']['schedule'][$step]['ConfirmExpectedDt']);
                }
            }
            $excelBody .= ExcelCsvUtil::wrapTd($val['reqFactoryNm'],null,null,'rowspan=' . $defaultRowspan);
            $excelBody .= ExcelCsvUtil::wrapTd($val['deliveryPlace'],null,null,'rowspan=' . $defaultRowspan);
            $excelBody .= ExcelCsvUtil::wrapTd($val['privateMallDeliveryDt'],null,null,'rowspan=' . $defaultRowspan);
            if( !empty($val['globalDeliveryDiv']) ){
                $excelBody .= ExcelCsvUtil::wrapTd(ImsCodeMap::GLOBAL_DELIVERY_DIV[$val['globalDeliveryDiv']],null,null,'rowspan=' . $defaultRowspan);
            }else{
                $excelBody .= ExcelCsvUtil::wrapTd('',null,null,'rowspan=' . $defaultRowspan);
            }
            $excelBody .= "</tr>";

            //현재 예정일
            $excelBody .= "<tr>";
            $excelBody .= ExcelCsvUtil::wrapTd('현재예정일');
            foreach(ImsCodeMap::PRODUCTION_STEP as $step){
                if(!empty($val[$step]['Memo'])){
                    $excelBody .=  ExcelCsvUtil::wrapTd($val[$step]['Memo']);
                }else{
                    $excelBody .=  ExcelCsvUtil::wrapTd($val[$step]['ExpectedDt']);
                }
            }
            $excelBody .= "</tr>";
            
            //완료일
            $excelBody .= "<tr>";
            $excelBody .= ExcelCsvUtil::wrapTd('완료일');
            foreach(ImsCodeMap::PRODUCTION_STEP as $step){
                if(!empty($val[$step]['Memo2'])){
                    $excelBody .=  ExcelCsvUtil::wrapTd($val[$step]['Memo2']);
                }else{
                    $excelBody .=  ExcelCsvUtil::wrapTd($val[$step]['CompleteDt']);
                }
            }
            $excelBody .= "</tr>";
            
            //상태
            $excelBody .= "<tr>";
            $excelBody .= ExcelCsvUtil::wrapTd('상태');
            foreach(ImsCodeMap::PRODUCTION_STEP as $step){
                $acceptKr = ImsCodeMap::PROJECT_CONFIRM_TYPE[$val[$step]['Confirm']];
                $excelBody .= ExcelCsvUtil::wrapTd($acceptKr);
            }
            $excelBody .= "</tr>";
        }

        $simpleExcelComponent = SlLoader::cLoad('Excel','SimpleExcelComponent','sl');
        $simpleExcelComponent->simpleDownload('생산스케쥴관리', $title, $excelBody);
    }

}
