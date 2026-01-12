<?php

namespace Controller\Admin\Provider\Statistics;

use App;
use Component\Category\BrandAdmin;
use Component\Category\CategoryAdmin;
use Component\Ims\ImsDBName;
use Component\Stock\StockListService;
use Component\Work\WorkCodeMap;
use Controller\Admin\Erp\AdminErpControllerTrait;
use Controller\Admin\Erp\ControllerService\InoutListService;
use Controller\Admin\Ims\ImsControllerTrait;
use Globals;
use Request;
use SiteLabUtil\SlCommonUtil;
use SlComponent\Database\DBUtil2;
use SlComponent\Database\SearchVo;
use SlComponent\Util\ExcelCsvUtil;
use SlComponent\Util\PhpExcelUtil;
use SlComponent\Util\SlCodeMap;
use SlComponent\Util\SlLoader;
use function SlComponent\Util\SlLoader;

/**
 * 문서 리스트
 */
class IcsOrderController extends \Controller\Admin\Controller{

    use ImsControllerTrait;

    public function index(){
        $iCustomerSno = (int)6; //namkuuu 후순위작업. 향후 DB에서 가져와야함

        //엑셀 다운로드
        $aRequest = \Request::request()->toArray();
        if(isset($aRequest['simple_excel_download'])) {
            $aRequest['page'] = 1;
            $aRequest['pageNum'] = 15000;
            $aRequest['sort'] = 'sortNum,asc';
            $aRequest['sRadioSchCustomerSno'] = $iCustomerSno;
            unset($aRequest['simple_excel_download']);

            $this->simpleExcelDownload($aRequest);
            exit();
        }

        //엑셀 업로드
        $sExcelUploadResultHtml = '';
        $aAppendFile = Request::files()->toArray();
        if (count($aAppendFile) > 0) {
            $bFlagErr = false;
            $aExcelList = PhpExcelUtil::readToArray($aAppendFile, 1);
            if (!isset($aExcelList[0]) || count($aExcelList[0]) !== 7) {
                $sExcelUploadResultHtml = "엑셀양식이 올바르지 않습니다. (필드갯수 안맞음)".count($aExcelList[0]);
                $bFlagErr = true;
            }
            if ($bFlagErr === false) {
                $imsService = SlLoader::cLoad('ims', 'ImsCustomerReceiverService');
                $aTmpFlds = $imsService->getDisplay();
                $aInsertFlds = [];
                foreach ($aTmpFlds as $val) $aInsertFlds[] = $val['name'];

                $iRegManagerSno = \Session::get('manager.sno');
                $sCurrDt = date('Y-m-d H:i:s');
                $aInsert = [];
                foreach ($aExcelList as $key => $val) {
                    if ($key > 0 && $val[3] != '' && $val[7] != '') {
                        $aTmp = ['customerSno'=>$iCustomerSno, 'regManagerSno'=>$iRegManagerSno, 'sortNum'=>$key, 'regDt'=>$sCurrDt];
                        foreach ($val as $key2 => $val2) {
                            if (isset($aInsertFlds[$key2-1])) {
                                $aTmp[$aInsertFlds[$key2-1]] = trim($val2);
                            }
                        }
                        $aInsert[] = $aTmp;
                    }
                }
                if (count($aInsert) > 0) {
                    DBUtil2::delete(ImsDBName::CUSTOMER_RECEIVER, new SearchVo('customerSno=?',$iCustomerSno));
                    foreach ($aInsert as $val) {
                        DBUtil2::insert(ImsDBName::CUSTOMER_RECEIVER, $val);
                    }
                    $sExcelUploadResultHtml = '총 '.count($aInsert).'명 등록완료';
                } else $sExcelUploadResultHtml = '등록 가능한 담당자정보가 없습니다. 담당자명,주소는 필수입니다.';
            }
        }

        $this->setData('customerSno', $iCustomerSno);
        $this->setData('sExcelUploadResultHtml', $sExcelUploadResultHtml);

        $this->callMenu('statistics', 'b2b', 'order');
        $this->setDefault();
    }


    public function simpleExcelDownload($request) {
        $sExcelFileName = date('Y_m_d').'_담당자리스트';
        $imsService = SlLoader::cLoad('ims', 'ImsCustomerReceiverService');
        $aAllData = $imsService->getListCustomerReceiver($request);
        if (count($aAllData) > 0) {
            $title = ['번호'];
            $dpData = [];
            foreach($aAllData['fieldData'] as $val) {
                if (!isset($val['skip']) || $val['skip'] !== true) { //엑셀파일에 필요없는 필드는 이 if문에서 걸러냄
                    $title[] = $val['title'];
                    $dpData[] = $val;
                }
            }

            $contents = [];
            foreach($aAllData['list'] as $key => $val) {
                $contentsRows = [];
                $contentsRows[] = ExcelCsvUtil::wrapTd($key+1);
                foreach($dpData as $val2) {
                    $contentsRows[] = ExcelCsvUtil::wrapTd($val[$val2['name']]);
                }
                $contents[] = '<tr>'.implode('',$contentsRows).'</tr>';
            }
            $simpleExcelComponent = SlLoader::cLoad('Excel','SimpleExcelComponent','sl');
            $simpleExcelComponent->simpleDownload($sExcelFileName, $title, implode('',$contents));
        }
    }

}