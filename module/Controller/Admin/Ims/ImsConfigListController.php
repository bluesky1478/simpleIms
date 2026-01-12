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
class ImsConfigListController extends \Controller\Admin\Controller{

    use ImsPsNkTrait;
    use ImsControllerTrait;

    private $imsNkService;

    public function __construct() {
        parent::__construct();
        $this->imsNkService = SlLoader::cLoad('imsv2', 'imsNkService');
    }

    public function index() {
        //엑셀 다운로드
//        $request = \Request::request()->toArray();
//        if(isset($request['simple_excel_download']) && $request['simple_excel_download'] == 1) {
//            $request['page'] = 1;
//            $request['pageNum'] = 15000;
//            $request['multiKey'] = json_decode($request['multiKey'], true);
//            unset($request['simple_excel_download']);
//
//            $this->simpleExcelDownload($request);
//            exit();
//        }

        $this->callMenu('ims', 'customer', 'configList');
        $this->setDefault();

        $iTabNum = (int)Request::get()->get('tabNum');
        $iTabNum = $iTabNum === 0 ? 1 : $iTabNum;
        $this->setData('iTabNum', $iTabNum);

        //검색항목
        switch($iTabNum) {
            case 2:
                $search['combineSearch'] = [
                    'a.fitStyle' => '스타일',
                    'a.optionName' => '부위명',
                ];
                break;
            case 3:
                $search['combineSearch'] = [
                    'a.costName' => '구분명',
                    'a.costCode' => '코드',
                ];
                break;
            case 4:
                $search['combineSearch'] = [
                    'a.factoryName' => '이름',
                    'a.factoryPhone' => '전화번호',
                ];
                break;
            case 5:
                $search['combineSearch'] = [
                    'parent.cateName' => '상위업종명',
                    'a.cateName' => '업종명',
                ];
                break;
            case 7:
                $search['combineSearch'] = [
                    'a.guideName' => '양식명',
                ];
                break;
            default:
                $search['combineSearch'] = [
                    'a.fitStyle' => '스타일',
                    'a.fitName' => '핏이름',
                ];
                break;
        }
        $this->setData('search', $search);
    }

    //엑셀 다운로드
//    public function simpleExcelDownload($request) {
//        //데이터(+필드Arr) 가져오기
//        $list = $this->imsNkService->getListCustomerEstimateNk($request);
//        //필드 정리
//        $aFldList = $list['fieldData'];
//        $dpData = [];
//        $aIntFlds = [];
//        foreach ($aFldList as $key => $val) {
//            if ($val['type'] == 'i') $aIntFlds[] = $val['name'];
//            $dpData[$val['name']] = ['name'=>$val['title']];
//        }
//        $dpData = array_merge(['no'=>['name'=>'번호','skip'=>true]], $dpData);
//        $title = [];
//        foreach($dpData as $dpKey => $dpValue) $title[] = $dpValue['name'];
//        //값 정리
//        foreach($list['list'] as $key => $val) {
//            foreach($val as $key2 => $val2) {
//                if (in_array($key2, $aIntFlds)) $list['list'][$key][$key2] = number_format($val2);
//            }
//        }
//        //엑셀 데이터 구성
//        $contents = [];
//        foreach($list['list'] as $key => $val) {
//            $contentsRows = [];
//            $contentsRows[] = ExcelCsvUtil::wrapTd($key+1);
//            foreach($dpData as $dpKey => $dpValue) {
//                if(true !== $dpValue['skip']) {
//                    $contentsRows[] = ExcelCsvUtil::wrapTd($val[$dpKey]);
//                }
//            }
//            $contents[] = '<tr>'.implode('',$contentsRows).'</tr>';
//        }
//        $simpleExcelComponent = SlLoader::cLoad('Excel','SimpleExcelComponent','sl');
//        $simpleExcelComponent->simpleDownload('고객견적리스트', $title, implode('',$contents));
//    }

}