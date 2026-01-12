<?php

namespace Controller\Admin\Erp;

use App;
use Component\Category\BrandAdmin;
use Component\Category\CategoryAdmin;
use Controller\Admin\Erp\ControllerService\StockCurrentService;
use Controller\Admin\Erp\ControllerService\StockTableService;
use Globals;
use Request;
use SiteLabUtil\SlCommonUtil;
use SlComponent\Database\DBUtil2;
use SlComponent\Util\ExcelCsvUtil;
use SlComponent\Util\SlLoader;
use function SlComponent\Util\SlLoader;

/**
 * 문서 리스트
 */
class StockCompareController extends \Controller\Admin\Controller{

    use AdminErpControllerTrait;

    public function workIndex(){
        $this->setData('isDev', SlCommonUtil::isDevIp());
        $this->callMenu('erp', 'stock', 'stockCompare');
        $controllerListService = SlLoader::controllerServiceLoad(__NAMESPACE__,'stockCompare');
        $listService = SlLoader::cLoad('godo','listService','sl');
        $listService->setList($controllerListService, $this);

        $getData = $controllerListService->getSummaryTotalList();

        $this->setData('checked', $getData['checked']);

        //검색정보
        $this->setData('search', $getData['search']);

        //페이지
        $this->setData('page', $getData['page']);

        //리스트 데이터
        $this->setData('data',$getData['data']);

        $this->setData('listAllData',$getData); //전체 데이타

        $this->setData('requestUrl', basename(\Request::getPhpSelf()) .'?simple_excel_download=1&'.  \Request::getQueryString() );
        
        
        //ScmList : 분류 완료된 고객사만.
        $this->setData('scmList', [
            3 => '혼다코리아',
            6 => '한국타이어',
            8 => 'TKE(티센크루프)',
            10 => '무영건설',
            11 => '오티스(OSE)',
            12 => '영구크린',
            14 => '미쓰비시(서비스)',
            16 => '미쓰비시(설치)',
            20 => '한전산업개발',
            21 => '오티스(OEK)',
            22 => '한국공항',
            23 => '동양건설',
            24 => '반도건설',
            25 => '빙그레',
            26 => '오티스(FOD)',
            29 => '반도건설(총무)',
            30 => '퍼시스',
            31 => '타타대우',
            32 => '현대엘리베이터',
        ]);

    }


    public function simpleExcelDownload($getData){
        $this->setListAfterData($getData);
        $summaryTitles = $this->getData('summaryTitles');
        $optionTitles = $this->getData('optionTitles');
        $listAllData = $getData['totalData'];
        $optionList = $this->getData('optionList');
        $data = $getData['data'];

        $htmlList = [];

        $htmlList[] = '<table class="table table-rows" border="1">';

        //TITLE
        $htmlList[] = '<tr>';
        foreach ($summaryTitles as $titleValue) {
            $htmlList[] =ExcelCsvUtil::wrapTh($titleValue);
        }
        $htmlList[] = ExcelCsvUtil::wrapTh("TOTAL",null,"color:red");
        foreach ($optionTitles as $titleValue) {
            $htmlList[] =ExcelCsvUtil::wrapTh($titleValue);
        }
        $htmlList[] = '</tr>';

        //List Data
        foreach($data as $index => $val) {
            $htmlList[] = '<tr>';
            foreach($summaryTitles as $listTitleKey => $listTitle) {
                $htmlList[] = ExcelCsvUtil::wrapTd($val['info']['attr'.$listTitleKey]); //Summary
            }
            $htmlList[] = ExcelCsvUtil::wrapTd(number_format($listAllData['keyStockCnt'][$index]),null,"color:red"); //TOTAL
            foreach($optionList as $optionValue) {
                $htmlList[] = ExcelCsvUtil::wrapTd(number_format($val['stockCnt'][$optionValue])); //Stock.
            }
            $htmlList[] = '</tr>';
        }

        $htmlList[] = '</table>';
        $excelBody =  implode('',$htmlList);
        //gd_debug($excelBody);
        $simpleExcelComponent = SlLoader::cLoad('Excel','SimpleExcelComponent','sl');
        $simpleExcelComponent->downloadCommon($excelBody, '고객사재고현황_'.date('Y-m-d'));
    }

}