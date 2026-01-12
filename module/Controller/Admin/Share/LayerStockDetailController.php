<?php

namespace Controller\Admin\Share;

use Component\Stock\StockStatService;
use Request;
use SiteLabUtil\SlCommonUtil;
use SlComponent\Util\ExcelCsvUtil;
use SlComponent\Util\SitelabLogger;
use SlComponent\Util\SlCodeMap;
use SlComponent\Util\SlLoader;

/**
 * 재고 상세
 * Class LayerPolicyLinkMemberController
 * @package Controller\Admin\Share
 */
class LayerStockDetailController extends \Controller\Admin\Controller{

	/**
	 * {@inheritdoc}
	 */
	public function index()
	{

        $stockListService = SlLoader::cLoad('Stock','StockListService');

	    $getValue = Request::get()->toArray();

		$this->addScript([
			'bootstrap/bootstrap-table.js',
			'jquery/jquery.tablednd.js',
			'bootstrap/bootstrap-table-reorder-rows.js',
		]);

        $getValue = SlCommonUtil::getRefineValueAndExcelDownCheck($getValue);
        $getData = $stockListService->getStatToList($getValue);
        if(  !empty($getValue['simple_excel_download'])  ){
            $this->simpleExcelDownload($getData);
            exit();
        }

		// --- 관리자 디자인 템플릿
		$this->getView()->setDefine('layout', 'layout_layer.php');

		// 공급사와 동일한 페이지 사용
		$this->getView()->setPageName('sitelab/layer/layer_stock_detail.php');

        //리스트 데이터
        $this->setData('data',$getData['data']);

        //부모로 부터 넘겨받은 검색 데이터
        $this->setData('searchDate',$getValue);

        //재고 유형 맵
        $this->setData('stockTypeMap',SlCodeMap::STOCK_TYPE);
        $this->setData('stockReasonMap',SlCodeMap::STOCK_REASON);

        $base = \Request::getScheme()."://gdadmin.".\Request::getDefaultHost() . \Request::getPhpSelf();;
        $this->setData('requestUrl', $base .'?simple_excel_download=1&'.  \Request::getQueryString() );

	}

    public function simpleExcelDownload($getData){
        $data = $getData['data'];

        $excelBody = '';

        $stockTypeMap = SlCodeMap::STOCK_TYPE;
        $stockReasonMap = SlCodeMap::STOCK_REASON;

        foreach ($data as $key => $val) {
            $fieldData = array();
            $fieldData[] = ExcelCsvUtil::wrapTd($val['regDt']);
            $fieldData[] = ExcelCsvUtil::wrapTd($stockTypeMap[$val['stockType']]);
            $fieldData[] = ExcelCsvUtil::wrapTd($stockReasonMap[$val['stockReason']]);
            $fieldData[] = ExcelCsvUtil::wrapTd(number_format($val['stockCnt']));
            $fieldData[] = ExcelCsvUtil::wrapTd($val['orderNo'],'text','mso-number-format:\'\@\'');;
            $fieldData[] = ExcelCsvUtil::wrapTd($val['memNm']);
            $fieldData[] = ExcelCsvUtil::wrapTd($val['memId']);
            $excelBody .=  "<tr>". implode('',$fieldData) . "</tr>";
        }
        $simpleExcelComponent = SlLoader::cLoad('Excel','SimpleExcelComponent','sl');
        $simpleExcelComponent->simpleDownload('재고이력',['등록일','유형','사유','수량','주문번호','회원명','회원ID'],$excelBody);
    }

}
