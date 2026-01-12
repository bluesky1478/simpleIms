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

namespace Controller\Front\Warehouse;

use App;
use Component\Claim\ReturnListService;
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
class ReturnListController extends \Controller\Front\Controller
{

    use WarehouseTrait;

    public function workIndex() {
        $this->setMenu('PROJECT', 2);
        $this->setData('today', date('Y-m-d'));

        $controllerService = SlLoader::cLoad('godo','controllerService','sl');
        $controllerService->setReturnListData($this);
    }

    public function simpleExcelDownload($getData){
        $today = gd_date_format('Y-m-d',$getData['search']['searchDate'][0]).'_'.gd_date_format('Y-m-d',$getData['search']['searchDate'][1]);
        $data = $getData['data'];
        //$page = $getData['page'];
        $excelBody = '';
        foreach ($data as $key => $val) {
            $prdInfo = [];
            $prdInfo[] = '<table>';
            foreach($val['returnGoods'] as $returnGoods) {
                $prdInfo[] = "
                        <tr>
                            <td>{$returnGoods['prdCode']}</td>
                            <td>{$returnGoods['prdName']}_{$returnGoods['optionName']}</td>
                            <td>{$returnGoods['prdCnt']}개</td>
                        </tr>
                ";
            }
            $prdInfo[] = '</table>';

            $fieldData = array();
            $fieldData[] = ExcelCsvUtil::wrapTd($val['sno']);
            $fieldData[] = ExcelCsvUtil::wrapTd($val['returnStatusKr']);
            $fieldData[] = ExcelCsvUtil::wrapTd($val['prdStatusKr']);
            $fieldData[] = ExcelCsvUtil::wrapTd($val['scmName']);
            $fieldData[] = ExcelCsvUtil::wrapTd($val['invoiceNo'],'text','mso-number-format:\'\@\'');
            $fieldData[] = ExcelCsvUtil::wrapTd($val['customerName']);
            $fieldData[] = ExcelCsvUtil::wrapTd($val['address']);
            $fieldData[] = ExcelCsvUtil::wrapTd(implode('',$prdInfo));
            $fieldData[] = ExcelCsvUtil::wrapTd(nl2br(str_replace('\n',"\n",$val['innoverMemo'])));
            $fieldData[] = ExcelCsvUtil::wrapTd(nl2br(str_replace('\n',"\n",$val['partnerMemo'])));
            $fieldData[] = ExcelCsvUtil::wrapTd(gd_date_format('Y-m-d',$val['regDt']));
            $fieldData[] = ExcelCsvUtil::wrapTd(gd_date_format('Y-m-d',$val['returnDt']));
            $excelBody .=  "<tr>". implode('',$fieldData) . "</tr>";
        }
        $simpleExcelComponent = SlLoader::cLoad('Excel','SimpleExcelComponent','sl');
        $simpleExcelComponent->simpleDownload('반품리스트_'.$today,ReturnListService::LIST_TITLES,$excelBody);
    }

}

