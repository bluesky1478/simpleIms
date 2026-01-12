<?php

namespace Controller\Admin\Ims;

use App;
use Component\Category\BrandAdmin;
use Component\Category\CategoryAdmin;
use Component\Ims\ImsCodeMap;
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
class ImsFactoryEstimateViewController extends \Controller\Admin\Controller{

    use ImsControllerTrait;

    public function index(){

        $request = \Request::request()->toArray();

        //$this->callMenu('ims', 'project', 'all'); //TODO : 리스트에 따라 변경될 수 있음.
        $this->setDefault();

        if(!empty($this->getData('requestParam')['sno'])){
            $this->setData('title', '스타일 정보');
            $this->setData('saveBtnTitle', '수정');
        }else{
            $this->setData('title', '스타일 등록');
            $this->setData('saveBtnTitle', '저장');
        }

        $this->getView()->setDefine('layout', 'layout_blank.php');

        $imsService = SlLoader::cLoad('ims', 'imsService');

        $year = 2020;
        $yearMap = [];
        for($i=0;15>=$i;$i++){
            $yearMap[$year+$i] = $year+$i;
        }
        $this->setData('codeYear', $yearMap);
        //시즌
        $this->setData('codeSeason', $imsService->getCode('style','시즌'));

        //성별
        $this->setData('codeGender', $imsService->getCode('style','성별'));
        //스타일
        $this->setData('codeStyle', $imsService->getCode('style','스타일'));
        //색상
        $this->setData('codeColor', $imsService->getCode('style','색상'));


        $this->setData('thumbnailFieldList', [
            [
                'title' => '1.기획(임시) 이미지',
                'field' => 'fileThumbnail',
            ],
            /*[
                'title' => '2.작업 이미지',
                'field' => 'fileThumbnailWork',
            ],*/
            [
                'title' => '2.촬영(실물) 이미지',
                'field' => 'fileThumbnailReal',
            ],
        ]);

        //28
        $this->setData('sizeOptionStandard', json_encode($this->getStandardSizeOption2()) );

        if( !empty($request['simple_excel_download']) ){
            $this->simpleExcelDownload($request);
            exit();
        }

    }

    public function getStandardSizeOption2(){
        $standardList = [];
        for($i=0; 9 > $i; $i++){
            $standardList['top'][] = 85 + ($i*5);
            $standardList['bottom'][] = 28 + ($i*2);
        }

        $map = [
            0 => 'S',
            1 => 'M',
            2 => 'L',
            3 => 'LL',
            4 => 'LLL',
        ];
        for($i=0; 20>$i; $i++){
            for($j=0; 5>$j; $j++){
                $standardList['bottomKepid'][] = (24 + $j) . $map[$j];
            }
        }

        return $standardList;
    }
    public function getStandardSizeOption(){
        return [
            'top' => [
                '85',
                '90',
                '95',
                '100',
                '105',
                '110',
                '115',
                '120',
                '125'
            ],
            'bottom' => [
                '28',
                '30',
                '32',
                '34',
                '36',
                '38',
                '40',
                '42',
                '44'
            ],
            'bottomKepid' => [
                '24S',
                '26',
                '28',
                '30',
                '32',
                '34',
                '36',
                '38',
                '40',
                '42',
                '44',
                '24S',
                '26',
                '28',
                '30',
                '32',
                '34',
                '36',
                '38',
                '40',
                '42',
                '44',
                '44',
                '24S',
                '26',
                '28',
                '30',
                '32',
                '34',
                '36',
                '38',
                '40',
                '42',
                '44',
                '24S',
                '26',
                '28',
                '30',
                '32',
                '34',
                '36',
                '38',
                '40',
                '42',
                '44',
                '44',
                '24S',
                '26',
                '28',
                '30',
                '32',
                '34',
                '36',
                '38',
                '40',
                '42',
                '44',
                '24S',
                '26',
                '28',
                '30',
                '32',
                '34',
                '36',
                '38',
                '40',
                '42',
                '44',
                '44',
                '24S',
                '26',
                '28',
                '30',
                '32',
                '34',
                '36',
                '38',
                '40',
                '42',
                '44',
                '24S',
                '26',
                '28',
                '30',
                '32',
                '34',
                '36',
                '38',
                '40',
                '42',
                '44',
                '44',
                '24S',
                '26',
                '28',
                '30',
                '32',
                '34',
                '36',
                '38',
                '40',
                '42',
                '44',
                '24S',
                '26',
                '28',
                '30',
                '32',
                '34',
                '36',
                '38',
                '40',
                '42',
                '44',
                '44',
            ],
        ];
    }

    public function simpleExcelDownload($request){

        $imsService = SlLoader::cLoad('ims', 'imsService');
        $data = $imsService->getFactoryEstimate($request);
        //gd_debug($data);

        $simpleExcelComponent = SlLoader::cLoad('Excel','SimpleExcelComponent','sl');
        $prdName = $data['productName'];
        $customerName = $data['customerName'];

        $title = 'estimate' === $data['estimateType'] ? '생산가견적' : '생산가';
        $fileTitle = $title.'_'.$customerName.'_'.$prdName.'_'.date('Ymd');
        $fileName = str_replace('/','_',urlencode($fileTitle));

        $titles1 = [ //12개
            '생산가(VAT별도)', '원자재 소계', '부자재 소계', '공임', '환율', '환율기준일', '달러변환', '마진', '물류 및 관세', '관리비', '생산MOQ', '단가MOQ', 'MOQ미달 추가금'
        ];
        $titles2 = [ //11개
            '부위', '부착위치', '자재(or원단)명', '혼용율', '컬러', '규격', '가요척(or수량)', '단가', '금액', '제조국', '비고'
        ];
        $titles3 = [ //9개 (3)
            '부위', '자재명', '컬러', '규격', '수량', '단가', '금액', '부자재업체', '비고'
        ];

        //Excel Body
        $excelBody = "<table border='1'>";

        //열 제목
        $excelBody .= "<tr><td colspan='13' style='font-size:20px;font-weight: bold;text-align: center; '>{$customerName} {$prdName} {$title}</td></tr>";

        // # 1 . 생산가 정보
        $excelBody .= "<tr><td colspan='13' style='font-size:17px;font-weight: bold;text-align: left; '>생산가정보</td></tr>";
        $excelBody .= "<tr>";
        foreach($titles1 as $cellTitle){
            $excelBody .= ExcelCsvUtil::wrapTh($cellTitle);
        }
        $excelBody .= "</tr>";
        $excelBody .= "<tr>";
        $excelBody .= ExcelCsvUtil::wrapTd(number_format($data['contents']['totalCost']));
        $excelBody .= ExcelCsvUtil::wrapTd(number_format($data['contents']['fabricCost']));
        $excelBody .= ExcelCsvUtil::wrapTd(number_format($data['contents']['subFabricCost']));
        $excelBody .= ExcelCsvUtil::wrapTd(number_format($data['contents']['laborCost']));
        $excelBody .= ExcelCsvUtil::wrapTd(number_format($data['contents']['exchange']));
        $excelBody .= ExcelCsvUtil::wrapTd($data['contents']['exchangeDt']);
        if( !empty($data['contents']['laborCost']) && !empty($data['contents']['exchange']) ){
            $excelBody .= ExcelCsvUtil::wrapTd( round($data['contents']['laborCost'] / $data['contents']['exchange'],2) . '$'  );
        }else{
            $excelBody .= ExcelCsvUtil::wrapTd( '-'  );
        }
        $excelBody .= ExcelCsvUtil::wrapTd(number_format($data['contents']['marginCost']));
        $excelBody .= ExcelCsvUtil::wrapTd(number_format($data['contents']['dutyCost']));
        $excelBody .= ExcelCsvUtil::wrapTd(number_format($data['contents']['managementCost']));
        $excelBody .= ExcelCsvUtil::wrapTd(number_format($data['contents']['prdMoq']));
        $excelBody .= ExcelCsvUtil::wrapTd(number_format($data['contents']['priceMoq']));
        $excelBody .= ExcelCsvUtil::wrapTd(number_format($data['contents']['addPrice']));
        $excelBody .= "</tr>";


        // # 2 . 원자재 정보
        $excelBody .= "<tr><td colspan='13' style='font-size:17px;font-weight: bold;text-align: left; '>원단정보</td></tr>";
        foreach($titles2 as $cellTitle){
            if( '비고' == $cellTitle ){
                $excelBody .= ExcelCsvUtil::wrapTh($cellTitle,'title',null,'colspan=3');
            }else{
                $excelBody .= ExcelCsvUtil::wrapTh($cellTitle);
            }
        }

        foreach($data['contents']['fabric'] as $content){
            $excelBody .= "<tr>";
            $excelBody .= ExcelCsvUtil::wrapTd($content['no']);
            $excelBody .= ExcelCsvUtil::wrapTd($content['attached']);
            $excelBody .= ExcelCsvUtil::wrapTd($content['fabricName']);
            $excelBody .= ExcelCsvUtil::wrapTd($content['fabricMix']);
            $excelBody .= ExcelCsvUtil::wrapTd($content['color']);
            $excelBody .= ExcelCsvUtil::wrapTd($content['spec']);
            $excelBody .= ExcelCsvUtil::wrapTd($content['meas']);
            $excelBody .= ExcelCsvUtil::wrapTd(number_format($content['unitPrice']));
            $excelBody .= ExcelCsvUtil::wrapTd(number_format($content['price']));
            $excelBody .= ExcelCsvUtil::wrapTd(ImsCodeMap::PRD_NATIONAL_CODE[$content['makeNational']]);
            $excelBody .= ExcelCsvUtil::wrapTd($content['memo'],null,null,'colspan=3');
            $excelBody .= "</tr>";
        }

        // # 3 . 부자재 정보
        $excelBody .= "<tr><td colspan='13' style='font-size:17px;font-weight: bold;text-align: left; '>부자재정보</td></tr>";
        foreach($titles3 as $cellTitle){
            if( '비고' == $cellTitle ){
                $excelBody .= ExcelCsvUtil::wrapTh($cellTitle,'title',null,'colspan=5');
            }else{
                $excelBody .= ExcelCsvUtil::wrapTh($cellTitle);
            }
        }
        //'부위', '자재명', '컬러', '규격', '수량', '단가', '금액', '부자재업체', '비고'
        foreach($data['contents']['subFabric'] as $content){
            $excelBody .= "<tr>";
            $excelBody .= ExcelCsvUtil::wrapTd($content['no']);
            $excelBody .= ExcelCsvUtil::wrapTd($content['subFabricName']);
            $excelBody .= ExcelCsvUtil::wrapTd($content['color']);
            $excelBody .= ExcelCsvUtil::wrapTd($content['spec']);
            $excelBody .= ExcelCsvUtil::wrapTd($content['meas']);
            $excelBody .= ExcelCsvUtil::wrapTd(number_format($content['unitPrice']));
            $excelBody .= ExcelCsvUtil::wrapTd(number_format($content['price']));
            $excelBody .= ExcelCsvUtil::wrapTd($content['company']);
            $excelBody .= ExcelCsvUtil::wrapTd($content['memo'],null,null,'colspan=5');
            $excelBody .= "</tr>";
        }

        $excelBody .= "</table>";
        $simpleExcelComponent->downloadCommon($excelBody, $fileName);
    }


}