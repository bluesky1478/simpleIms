<?php
/**
 * This is commercial software, only users who have purchased a valid license
 * and accept to the terms of the License Agreement can install and use this
 * program.
 *
 * Do not edit or add to this file if you wish to upgrade Godomall5 to newer
 * versions in the future.
 *
 * @copyright ⓒ 2016, NHN godo: Corp.
 * @link http://www.godo.co.kr
 */

namespace Controller\Front\Partner;

use App;
use Component\Work\WorkCodeMap;
use Controller\Admin\Erp\ControllerService\InoutListService;
use Controller\Front\Partner\ControllerService\ThreePlOrderListService;
use Controller\Front\Work\WorkControllerTrait;
use Framework\Debug\Exception\AlertCloseException;
use Globals;
use Request;
use Session;
use SiteLabUtil\SlCommonUtil;
use SlComponent\Database\DBUtil2;
use SlComponent\Util\ExcelCsvUtil;
use SlComponent\Util\SitelabLogger;
use SlComponent\Util\SlLoader;
use SlComponent\Util\SlProjectCodeMap;
use UserFilePath;

/**
 * 프로젝트 리스트
 * @author Lee Hakyoung <haky2@godo.co.kr>
 */
class OrderListController extends \Controller\Front\Controller
{

    use PartnerTrait;

    public function workIndex() {
        $this->setMenu('PROJECT', 2);
        $this->setData('today', date('Y-m-d'));

        $controllerListService = SlLoader::controllerServiceLoad(__NAMESPACE__,'threePlOrderList');
        $listService = SlLoader::cLoad('godo','listService','sl');
        $listService->setList($controllerListService, $this);
    }

    public function simpleExcelDownload($getData){
        $today = gd_date_format('Y-m-d',$getData['search']['treatDate'][0]).'_'.gd_date_format('Y-m-d',$getData['search']['treatDate'][1]);
        $data = $getData['data'];
        //$page = $getData['page'];
        $excelBody = '';
        foreach ($data as $key => $val) {
            $fieldData = array();
            $fieldData[] = ExcelCsvUtil::wrapTd($val['no']);
            $fieldData[] = ExcelCsvUtil::wrapTd($val['orderDt']);
            $fieldData[] = ExcelCsvUtil::wrapTd($val['productCode']);
            $fieldData[] = ExcelCsvUtil::wrapTd($val['productName']);
            $fieldData[] = ExcelCsvUtil::wrapTd(number_format($val['qty']));
            $fieldData[] = ExcelCsvUtil::wrapTd($val['customerName']);
            $fieldData[] = ExcelCsvUtil::wrapTd($val['address']);
            $fieldData[] = ExcelCsvUtil::wrapTd($val['orderNo'],'text','mso-number-format:\'\@\'');
            $fieldData[] = ExcelCsvUtil::wrapTd($val['invoiceNo'],'text','mso-number-format:\'\@\'');
            $excelBody .=  "<tr>". implode('',$fieldData) . "</tr>";
        }
        $simpleExcelComponent = SlLoader::cLoad('Excel','SimpleExcelComponent','sl');
        $simpleExcelComponent->simpleDownload('출고리스트_'.$today,ThreePlOrderListService::LIST_TITLES,$excelBody);
    }

}

