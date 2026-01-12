<?php

namespace Controller\Admin\Ims;

use App;
use Component\Category\BrandAdmin;
use Component\Category\CategoryAdmin;
use Component\Ims\ImsCodeMap;
use Component\Scm\HkStockListService;
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
 * 프로젝트 리스트
 */
class ImsProduceListController extends \Controller\Admin\Controller{

    use ImsControllerTrait;

    public function index(){
        $request=\Request::get()->toArray();

        if( empty($request['view']) ){
            \Request::get()->set('view','all');
            $request['view'] = 'all';
        }
        $this->setProduceListRelatedController($request);
        $this->setDefault();

        //재할당
        if( $request['view'] === 'all' ){
            $requestParam = $this->getData('requestParam');
            $requestParam['view'] = 'all';
            $this->setData('requestParam',$requestParam);
        }
        //gd_debug($request['view']);

        $controllerListService = SlLoader::controllerServiceLoad(__NAMESPACE__,'imsProduceList');
        //$this->getView()->setPageName('ims/ims_prepared_list.php');

        $listService = SlLoader::cLoad('godo','listService','sl');
        $listService->setList($controllerListService, $this);

        if( 'all' !== $request['view'] ){
            $rowspan = 1;
        }else{
            $rowspan = 3;
        }
        if( 'n' !== $request['showMemo'] ){
            $rowspan += 1;
        }

        $this->setData('rowspan', $rowspan);

        $this->setData('requestUrl', SlCommonUtil::getCurrentPageUrl("simple_excel_download=1&"));

        if(!empty($requestParam['simple_excel_download'])){
            $this->simpleExcelDownload($this->getData('data'));
            exit();
        }

    }


    public function simpleExcelDownload($getData){
        $request=\Request::get()->toArray();
        $fileName = '생산스케쥴관리_'. date('Y-m-d');

        $LIST_TITLES = [
            '번호'
            ,'생산처'
            ,'고객사'
            ,'프로젝트번호'
            ,'이노버발주일'
            ,'이노버납기일'
            ,'납기D-Day'
            ,'스타일'
            ,'수량'
        ];

        if( 'all' === $request['view'] ){
            $LIST_TITLES[] = '구분';
        }

        foreach(ImsCodeMap::PRODUCE_STEP_MAP as $stepKey => $stepTitle){
            $LIST_TITLES[] = $stepTitle;
        }

        $LIST_TITLES[] = '이노버메모';
        $LIST_TITLES[] = '생산처메모';

        $data = $getData['data'];
        $page = $getData['page'];

        $excelBody = '';
        foreach ($data as $key => $val) {
            $fieldData = array();
            $fieldData[] = ExcelCsvUtil::wrapTd($page->idx--);
            $fieldData[] = ExcelCsvUtil::wrapTd($val['produceCompanyName']);
            $fieldData[] = ExcelCsvUtil::wrapTd($val['customerName']);
            $fieldData[] = ExcelCsvUtil::wrapTd($val['projectNo']);
            $fieldData[] = ExcelCsvUtil::wrapTd($val['msOrderDt']);
            $fieldData[] = ExcelCsvUtil::wrapTd($val['msDeliveryDt']);
            $fieldData[] = ExcelCsvUtil::wrapTd($val['msDeliveryRemainDt']);
            $fieldData[] = ExcelCsvUtil::wrapTd($val['style'].' '.$val['styleCountNm']);
            $fieldData[] = ExcelCsvUtil::wrapTd(number_format($val['prdExQty']));

            if( 'all' !== $request['view'] ){
                foreach(ImsCodeMap::PRODUCE_STEP_MAP as $stepKey => $stepTitle){
                    $expectedDt = $val['prdStep'.$stepKey]['expectedDt'];
                    //$completeDt = $val['prdStep'.$stepKey]['completeDt'];
                    $memo = $val['prdStep'.$stepKey]['memo'];
                    if( !empty($memo) ){
                        $fieldData[] = ExcelCsvUtil::wrapTd($memo);
                    }else{
                        $fieldData[] = ExcelCsvUtil::wrapTd($expectedDt);
                    }
                }
            }else{

                $trData=[];
                $trData[] = "<tr><td>예정일▶</td></tr>";
                $trData[] = "<tr><td>완료일▶</td></tr>";
                $trData[] = "<tr><td>승인여부▶</td></tr>";
                $trStr = implode('',$trData);

                $tableData = "<table>{$trStr}</table>";
                $fieldData[] = ExcelCsvUtil::wrapTd($tableData);

                foreach(ImsCodeMap::PRODUCE_STEP_MAP as $stepKey => $stepTitle){
                    $expectedDt = $val['prdStep'.$stepKey]['expectedDt'];
                    $completeDt = $val['prdStep'.$stepKey]['completeDt'];
                    $confirmYn = $val['prdStep'.$stepKey]['confirmYnKr'];
                    $memo = $val['prdStep'.$stepKey]['memo'];
                    if( !empty($memo) ){
                        $expectedDt = $memo;
                    }
                    $trData=[];
                    $trData[] = "<tr><td>{$expectedDt}</td></tr>";
                    $trData[] = "<tr><td>{$completeDt}</td></tr>";
                    $trData[] = "<tr><td>{$confirmYn}</td></tr>";
                    $trStr = implode('',$trData);

                    $tableData = "<table>{$trStr}</table>";
                    $fieldData[] = ExcelCsvUtil::wrapTd($tableData);
                }
            }

            $fieldData[] = ExcelCsvUtil::wrapTd($val['msMemo']);
            $fieldData[] = ExcelCsvUtil::wrapTd($val['memo']);

            /*foreach(ImsCodeMap::PRODUCE_STEP_MAP as $stepKey => $stepTitle){
                $expectedDt = $val['prdStep'.$stepKey]['expectedDt'];
                $completeDt = $val['prdStep'.$stepKey]['completeDt'];
                $memo = $val['prdStep'.$stepKey]['memo'];
                if( !empty($memo) ){
                    $fieldData[] = ExcelCsvUtil::wrapTd($memo);
                }else{
                    $fieldData[] = ExcelCsvUtil::wrapTd($expectedDt);
                }
            }*/
            //$fieldData[] = ExcelCsvUtil::wrapTd($val['invoiceNo'],'text','mso-number-format:\'\@\'');
            $excelBody .=  "<tr>". implode('',$fieldData) . "</tr>";
        }
        $simpleExcelComponent = SlLoader::cLoad('Excel','SimpleExcelComponent','sl');
        $simpleExcelComponent->simpleDownload($fileName,$LIST_TITLES,$excelBody);

    }


}