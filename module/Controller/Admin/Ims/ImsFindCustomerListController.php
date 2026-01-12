<?php

namespace Controller\Admin\Ims;

use App;
use Component\Category\BrandAdmin;
use Component\Category\CategoryAdmin;
use Component\Database\DBTableField;
use Component\Ims\EnumType\PREPARED_TYPE;
use Component\Ims\ImsCodeMap;
use Component\Ims\ImsDBName;
use Component\Ims\ImsJsonSchema;
use Component\Ims\NkCodeMap;
use Component\Stock\StockListService;
use Component\Work\WorkCodeMap;
use Controller\Admin\Erp\AdminErpControllerTrait;
use Controller\Admin\Erp\ControllerService\InoutListService;
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

//신규고객 발굴 리스트
class ImsFindCustomerListController extends \Controller\Admin\Controller{

    use ImsPsNkTrait;
    use ImsControllerTrait;

    public function __construct() {
        parent::__construct();
    }

    public function index() {
        //엑셀 다운로드
        $aRequest = \Request::request()->toArray();
        if(isset($aRequest['simple_excel_download'])) {
            $aRequest['page'] = 1;
            $aRequest['pageNum'] = 15000;
            //frontend에서 배열로 저장된 검색필터만 아래와 같이 json_decode()
            $aRequest['multiKey'] = json_decode($aRequest['multiKey'], true);
            $aRequest['aChkboxSchCustomerType'] = json_decode($aRequest['aChkboxSchCustomerType'], true);
            $aRequest['aChkboxSchBuyDiv'] = json_decode($aRequest['aChkboxSchBuyDiv'], true);
            $aRequest['aChkboxSchBuyMethod'] = json_decode($aRequest['aChkboxSchBuyMethod'], true);
            unset($aRequest['simple_excel_download']);

            $this->simpleExcelDownload($aRequest);
            exit();
        }


        $this->callMenu('ims', 'sales', 'custFind');
        $this->setDefault();

        //검색항목
        $search['combineSearch'] = [
            'a.customerName' => '고객명',
            'sale_manager.managerNm' => '영업 담당자',
        ];
        $this->setData('search', $search);
    }

    //엑셀 다운로드
    public function simpleExcelDownload($request) {
        $sExcelFileName = date('Y_m_d').'_신규고객발굴리스트';
        $imsService = SlLoader::cLoad('ims', 'ImsCustomerService');
        $aAllData = $imsService->getListFindCustomer($request);
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