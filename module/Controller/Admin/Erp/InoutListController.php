<?php

namespace Controller\Admin\Erp;

use App;
use Component\Category\BrandAdmin;
use Component\Category\CategoryAdmin;
use Component\Stock\StockListService;
use Component\Work\WorkCodeMap;
use Controller\Admin\Erp\AdminErpControllerTrait;
use Controller\Admin\Erp\ControllerService\InoutListService;
use Globals;
use Request;
use SiteLabUtil\SlCommonUtil;
use SlComponent\Util\ExcelCsvUtil;
use SlComponent\Util\SlCodeMap;
use SlComponent\Util\SlLoader;
use function SlComponent\Util\SlLoader;

/**
 * 문서 리스트
 */
class InoutListController extends \Controller\Admin\Controller{

    use AdminErpControllerTrait;

    public function workIndex(){
        $this->callMenu('erp', 'stock', 'inoutList');

        //복붙하기 좋게 - Default에 재정의?
        $controllerListService = SlLoader::controllerServiceLoad(__NAMESPACE__,'inoutList');
        $listService = SlLoader::cLoad('godo','listService','sl');
        $listService->setList($controllerListService, $this);

        $this->setData('isDev', SlCommonUtil::isDevIp());
        $this->setData('adminHost', SlCommonUtil::getAdminHost());

        $erpService = SlLoader::cLoad('erp','erpService');
        $latestData = $erpService->getLatestInOutDate();
        $this->setData('latestData', $latestData);

    }

    public function simpleExcelDownload($getData){

        $LIST_TITLES = [
            '번호'
            ,'입/출고일자'   //inOutDate
            ,'구분'    //입고출고
            ,'사유'     //inOutReason Kr.
            ,'고객사'   //scmName
            ,'상품코드' //thirdPartyProductCode
            ,'상품명'  //productName
            ,'옵션'    //optionName
            ,'수량'    //quantity
            ,'메모'    //memo
            ,'주문번호' //orderNo
            ,'송장번호' //invoiceNo
            ,'고객명' //orderNo
            ,'주소' //orderNo
            ,'연락처' //orderNo
            //,'등록자'   //managerNm Kr.
            ,'등록일'   //regDt
        ];

        $data = $getData['data'];
        $page = $getData['page'];
        $excelBody = '';
        foreach ($data as $key => $val) {

            $cellPhone = $val['cellphone'];
            if(empty($cellPhone)){
                $cellPhone = $val['receiverCellPhone'];
            }
            if(empty($cellPhone)){
                $cellPhone = $val['phone'];
            }
            if(empty($cellPhone)){
                $cellPhone = $val['receiverPhone'];
            }

            $fieldData = array();
            $fieldData[] = ExcelCsvUtil::wrapTd($page->idx--);
            $fieldData[] = ExcelCsvUtil::wrapTd($val['inOutDate'],'text','mso-number-format:\'\@\'');
            $fieldData[] = ExcelCsvUtil::wrapTd($val['inOutTypeKr']);
            $fieldData[] = ExcelCsvUtil::wrapTd($val['inOutReasonKr']);
            $fieldData[] = ExcelCsvUtil::wrapTd($val['scmName']);
            $fieldData[] = ExcelCsvUtil::wrapTd($val['thirdPartyProductCode']);
            $fieldData[] = ExcelCsvUtil::wrapTd($val['productName']);
            $fieldData[] = ExcelCsvUtil::wrapTd($val['optionName']);
            $fieldData[] = ExcelCsvUtil::wrapTd(number_format($val['quantity']));
            $fieldData[] = ExcelCsvUtil::wrapTd($val['memo'],'text','mso-number-format:\'\@\'');
            $fieldData[] = ExcelCsvUtil::wrapTd($val['orderNo'],'text','mso-number-format:\'\@\'');
            $fieldData[] = ExcelCsvUtil::wrapTd($val['invoiceNo'],'text','mso-number-format:\'\@\'');
            $fieldData[] = ExcelCsvUtil::wrapTd($val['customerName']);
            $fieldData[] = ExcelCsvUtil::wrapTd($val['address']);
            $fieldData[] = ExcelCsvUtil::wrapTd($cellPhone,'text','mso-number-format:\'\@\'');
            $fieldData[] = ExcelCsvUtil::wrapTd($val['regDt']);
            $excelBody .=  "<tr>". implode('',$fieldData) . "</tr>";
        }
        $simpleExcelComponent = SlLoader::cLoad('Excel','SimpleExcelComponent','sl');
        $simpleExcelComponent->simpleDownload('입출고리스트',$LIST_TITLES,$excelBody);
    }

}