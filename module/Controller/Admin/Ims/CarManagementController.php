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

//기초정보 관리
class CarManagementController extends \Controller\Admin\Controller{

    use ImsPsNkTrait;
    use ImsControllerTrait;

    private $imsNkService;

    public function __construct() {
        parent::__construct();
        $this->imsNkService = SlLoader::cLoad('imsv2', 'imsNkService');
    }

    public function index() {
        //엑셀 다운로드. 1 == 운행일지, 2 == 정비일지
        $aRequest = \Request::request()->toArray();
        if(isset($aRequest['simple_excel_download'])) {
            $iTabType = (int)$aRequest['simple_excel_download'];
            if ($iTabType > 0) {
                $aRequest['page'] = 1;
                $aRequest['pageNum'] = 15000;
                //frontend에서 배열로 저장된 검색필터만 아래와 같이 json_decode()
                $aRequest['multiKey'] = json_decode($aRequest['multiKey'], true);
                unset($aRequest['simple_excel_download']);

                $this->simpleExcelDownload($aRequest, $iTabType);
                exit();
            }
        }

        $this->callMenu('ims', 'etc', 'carManage');
        $this->setDefault();

        //검색항목
        $search['combineSearch'] = [
            [
                'managerNm' => '등록자',
                'b.addrName' => '출발지',
                'c.addrName' => '도착지',
            ],
            [
                'managerNm' => '등록자',
            ]
        ];
        $this->setData('search', $search);
    }

    public function simpleExcelDownload($request, $iTabType) {
        $imsService = SlLoader::cLoad('ims', 'ImsEtcCarService');
        $sExcelFileName = '';
        $aAllData = [];
        switch($iTabType) {
            case 1:
                $sExcelFileName = date('Y_m_d').'_운행일지';
                $aAllData = $imsService->getListEtcCarDrive($request);
                break;
            case 2:
                $sExcelFileName = date('Y_m_d').'_정비일지';
                $aAllData = $imsService->getListEtcCarMaintain($request);
                break;
            default: break;
        }
        if (count($aAllData) > 0) {
            $title = ['번호'];
            $dpData = [];
            foreach($aAllData['fieldData'] as $val) {
                if ($val['type'] != 'etc' && (!isset($val['skip']) || $val['skip'] !== true)) { //엑셀파일에 필요없는 필드는 이 if문에서 걸러냄
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