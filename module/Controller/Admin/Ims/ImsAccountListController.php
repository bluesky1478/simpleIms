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

/**
 * 정산관리 리스트
 * Class ImsAccountListController
 * @package Controller\Admin\Ims
 */
class ImsAccountListController extends \Controller\Admin\Controller{

    use ImsPsNkTrait;
    use ImsControllerTrait;

    private $imsNkService;

    public function __construct() {
        parent::__construct();
        $this->imsNkService = SlLoader::cLoad('imsv2', 'imsNkService');
    }

    public function index() {
        //엑셀 다운로드
        $request = \Request::request()->toArray();
        if(isset($request['simple_excel_download']) && $request['simple_excel_download'] == 1) {
            $request['page'] = 1;
            $request['pageNum'] = 15000;
            $request['multiKey'] = json_decode($request['multiKey'], true);
            $request['aChkboxSchProjectStatus'] = json_decode($request['aChkboxSchProjectStatus'], true);
            unset($request['simple_excel_download']);

            $this->simpleExcelDownload($request);
            exit();
        }

        $this->callMenu('ims', 'customer', 'accountList');
        $this->setDefault();

        //검색항목
        //namku(chk) 다중검색필터에 고객정보나 프로젝트정보를 추가할때 ImsNkService.php파일의 $aSchFldNmProject배열에 컬럼명 추가해야함 and 검색필터key값은 반드시 alias명.컬럼명 으로(explode 하는 부분있음)
        $search['combineSearch'] = [
            'cust.customerName' => '고객명',
            'a.sno' => '프로젝트번호',
            'b.productName' => '스타일명',
        ];
        $this->setData('search', $search);
    }


    //엑셀 다운로드
    public function simpleExcelDownload($request) {
        //필드 정리
        $aIntFlds = ['totalOriginAmt','totalAmt','prdQty','prdMsQty','prdOriginAmt','prdAmt','prdOriginAmtMultiplyQty','prdAmtMultiplyQty',];
        $dpData = [
            'no'=>['name'=>'번호','skip'=>true],
            'customerName'=>['name'=>'고객명'],
            'totalOriginAmt'=>['name'=>'총 생산가'],
            'totalAmt'=>['name'=>'총 판매가'],
            'totalMargin'=>['name'=>'총 마진'],
            'isBookRegisteredDt'=>['name'=>'회계 반영일'],
            'prdName'=>['name'=>'스타일명/부가항목'],
            'prdQty'=>['name'=>'제작수량'],
            'prdMsQty'=>['name'=>'미청구수량'],
            'prdOriginAmt'=>['name'=>'생산단가'],
            'prdAmt'=>['name'=>'판매단가'],
            'prdOriginAmtMultiplyQty'=>['name'=>'생산가'],
            'prdAmtMultiplyQty'=>['name'=>'판매가'],
            'accountingMessage'=>['name'=>'회계 전달메시지'],
        ];
        $title = [];
        foreach($dpData as $dpKey => $dpValue) $title[] = $dpValue['name'];
        //데이터 가져오기, 값 정리
        $list = $this->imsNkService->getListAccount($request);
        //rowspan으로 된거 나누기
        $aList = [];
        $aProductFlds = ['prdAmt','prdAmtMultiplyQty','prdMsQty','prdName','prdOriginAmt','prdOriginAmtMultiplyQty','prdQty'];
        foreach($list['list'] as $key => $val) { //프로젝트 반복
            $aTmp = $val;
            unset($aTmp['prdMargin'], $aTmp['styleSno']);
            foreach ($val['prdAmt'] as $key2 => $val2) { //스타일 반복
                foreach ($aProductFlds as $val3) { //스타일의 필드명 반복
                    unset($aTmp[$val3]);
                    $aTmp[$val3] = $val[$val3][$key2];
                }
                $aList[] = $aTmp;
            }
        }
        //값 정리
        foreach($aList as $key => $val) {
            $aList[$key]['isBookRegisteredDt'] = in_array($val['isBookRegisteredDt'], ["0000-00-00 00:00:00", '', null]) ? '-' : explode(' ', $val['isBookRegisteredDt'])[0];
            $aList[$key]['prdName'] = str_replace('<span class="text-danger">(판매)</span> ','(판매) ', str_replace('<span class="sl-blue">(구매)</span> ','(구매) ',$val['prdName']));
            foreach($val as $key2 => $val2) {
                if (in_array($key2, $aIntFlds)) $aList[$key][$key2] = number_format($val2);
            }
        }
        //엑셀 데이터 구성
        $contents = [];
        foreach($aList as $key => $val) {
            $contentsRows = [];
            $contentsRows[] = ExcelCsvUtil::wrapTd($key+1);
            foreach($dpData as $dpKey => $dpValue) {
                if(true !== $dpValue['skip']) {
                    $contentsRows[] = ExcelCsvUtil::wrapTd($val[$dpKey]);
                }
            }
            $contents[] = '<tr>'.implode('',$contentsRows).'</tr>';
        }
        $simpleExcelComponent = SlLoader::cLoad('Excel','SimpleExcelComponent','sl');
        $simpleExcelComponent->simpleDownload('정산리스트', $title, implode('',$contents));
    }
}