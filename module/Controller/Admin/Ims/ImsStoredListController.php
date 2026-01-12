<?php

namespace Controller\Admin\Ims;

use App;
use Component\Category\BrandAdmin;
use Component\Category\CategoryAdmin;
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
use SlComponent\Util\ExcelCsvUtil;
use SlComponent\Util\SlCodeMap;
use SlComponent\Util\SlLoader;
use function SlComponent\Util\SlLoader;

/**
 * 비축 원부자재 정보
 */
class ImsStoredListController extends \Controller\Admin\Controller{

    use ImsControllerTrait;

    private $imsStoredService;

    public function __construct() {
        parent::__construct();
        $this->imsStoredService = SlLoader::cLoad('ims', 'ImsStoredService');
    }

    public function index(){
        $request = \Request::request()->toArray();
        $this->callMenu('ims', 'customer', 'storedList');
        $this->setDefault();

        //검색항목
        $search['combineSearch'] = [
            'a.fabricName' => '비축 자재명',
            'a.fabricMix' => '혼용율',
            'a.color' => '색상',
            'custUsage.customerName' => '사용처고객명',
        ];
        $this->setData('search', $search);

        //table의 thead에만 $dpStyle를 쓰기 때문에 $dpStyle의 key name은 여기에서는 안쓰인다
        $dpStyle = $this->imsStoredService->getDisplayStored();
        $tableTitleDate = SlCommonUtil::createHtmlTableTitle($dpStyle);
        $this->setData('tableTitleData',$tableTitleDate);

        //직접 view페이지에 옮겼음
//        $tableBodyData = SlCommonUtil::createHtmlTableVueBody($dpStyle); //
//        $this->setData('tableBodyData',$tableBodyData);

        if(isset($request['simple_excel_download']) && $request['simple_excel_download'] == 1){
            $request['page'] = 1;
            $request['pageNum'] = 15000;
            $request['multiKey'] = json_decode($request['multiKey'], true);
            $request['aChkboxSchInputOwn'] = json_decode($request['aChkboxSchInputOwn'], true);
            unset($request['simple_excel_download']);

            $this->simpleExcelDownload($request, $dpStyle);
            exit();
        }
    }

    /**
     * 엑셀 다운로드
     */
    public function simpleExcelDownload($request, $dpData){
        unset($dpData['no'], $dpData['fabricInfo'], $dpData['customerUsageName'], $dpData['delFabric'], $dpData['btn_update'], $dpData['btn_input'], $dpData['total_remain'], $dpData['no_input'], $dpData['btn_out']);
        $aTmp['no'] = ['name'=>'번호','col'=>'2','skip'=>true];
        $aTmp['customerUsageName'] = ['name'=>'사용처<br/>고객명','col'=>'5'];
        $aTmp['fabricName'] = ['name'=>'비축 자재명','col'=>'2'];
        $aTmp['fabricMix'] = ['name'=>'혼용율','col'=>'2'];
        $aTmp['color'] = ['name'=>'색상','col'=>'2'];
        $dpData = array_merge($aTmp,$dpData);
        foreach ($dpData as $key => $val) {
            $dpData[$key]['name'] = str_replace('<br/>',' ',$val['name']);
        }
        $title = [];
        foreach($dpData as $dpValue){
            $title[] = $dpValue['name'];
        }

        $list = $this->imsStoredService->getListStored($request);
        foreach ($list['list'] as $key => $val) {
            $list['list'][$key]['inputDt'] = $val['inputDt'] == '0000-00-00' ? '-' : $val['inputDt'];
            $list['list'][$key]['expireDt'] = $val['expireDt'] == '0000-00-00' ? '-' : $val['expireDt'];
//소유고객->안씀            $list['list'][$key]['customerName'] = $val['customerName'] == null ? $list['list'][$key]['inputOwn'] : $val['customerName'];
        }
        foreach ($list['list'] as $key => $val) {
            foreach ($val as $key2 => $val2) {
                if (in_array($key2, ['total_input_price','remain_qty','unitPrice','inputQty','total_out_qty'])) {
                    $list['list'][$key][$key2] = number_format((int)$list['list'][$key][$key2]);
                }
            }
        }

        $contents = [];
        foreach($list['list'] as $key => $val){
            $contentsRows = [];
            $contentsRows[] = ExcelCsvUtil::wrapTd($key+1);
            foreach($dpData as $dpKey => $dpValue){
                if(true !== $dpValue['skip']){
                    $contentsRows[] = ExcelCsvUtil::wrapTd($val[$dpKey]);
                    //$excelBody .= ExcelCsvUtil::wrapTd($val['styleFullName'],null,null,'rowspan=' . $defaultRowspan);
                }
            }
            $contents[] = '<tr>'.implode('',$contentsRows).'</tr>';
        }
        $simpleExcelComponent = SlLoader::cLoad('Excel','SimpleExcelComponent','sl');
        $simpleExcelComponent->simpleDownload('비축원부자재리스트', $title, implode('',$contents));
    }


}