<?php

namespace Controller\Admin\Order;

use App;
use Component\Claim\ClaimListService;
use Component\Claim\ClaimService;
use Component\Stock\StockListService;
use Exception;
use Component\Category\CategoryAdmin;
use Component\Category\BrandAdmin;
use Globals;
use Request;
use SiteLabUtil\SlCommonUtil;
use SlComponent\Util\ApiTrait;
use SlComponent\Util\ExcelCsvUtil;
use SlComponent\Util\SitelabLogger;
use SlComponent\Util\SlCodeMap;
use SlComponent\Util\SlLoader;
use Component\Member\Manager;
use function SlComponent\Util\SlLoader;

class ClaimListController extends \Controller\Admin\Controller{

    use ApiTrait;

    public function index(){

        $getValue = Request::get()->toArray();

        //TODO : 1만 로우 이상 다운로드 관리자 문의 (성능에 문제될 수 있음) 기능 추가
        $getValue = SlCommonUtil::getRefineValueAndExcelDownCheck($getValue);
        //gd_debug($getValue);


        $isProvider = Manager::isProvider();

        if($isProvider){
            $this->callMenu('statistics', 'accept', 'claim_list');
            $this->setData('isProvider',$isProvider);
            $scmNo = \Session::get('manager.scmNo');
            $companyNm = \Session::get('manager.companyNm');
            $this->setData('companyNm', $companyNm);

            $getValue['scmFl'] =  1;
            $getValue['scmNo'][] =  $scmNo;
            $getValue['scmNoNm'][] =  $companyNm;

        }else{
            $this->callMenu('order', 'order', 'claim_list');
        }

        $this->addScript([
            'jquery/jquery.multi_select_box.js',
        ]);

        $claimListService = \App::load('\\Component\\Claim\\ClaimListService');
        $getData = $claimListService->getClaimList($getValue);
        if(  !empty($getValue['simple_excel_download'])  ){
            $this->simpleExcelDownload($getData);
            exit();
        }

        //라디오 체크
        $this->setData('checked', $getData['checked']);
        //검색정보
        $this->setData('search', $getData['search']);
        //페이지
        $this->setData('page', $getData['page']);

        //타이틀
        $this->setData('listTitles',ClaimListService::LIST_TITLES);
        //리스트 데이터
        $this->setData('data',$getData['data']);

        //리스트당 상품 수량
        $this->setData('reqGoodsCnt',$getData['reqGoodsCnt']);

        //클레임 유형 맵
        $this->setData('claimTypeMap',SlCodeMap::CLAIM_TYPE);
        $this->setData('requestUrl', basename(\Request::getPhpSelf()) .'?simple_excel_download=1&'.  \Request::getQueryString() );
        $this->setClaimApiUrl($this);

        $this->getView()->setPageName('order/claim_list.php');

        $deliveryCompanyMap = SlCommonUtil::getDeliveryCompanyMap();
        $this->setData('deliveryCompanyMap',$deliveryCompanyMap);
    }

    public function simpleExcelDownload($getData){
        $data = $getData['data'];
        $page = $getData['page'];
        $excelBody = '';
        foreach ($data as $key => $val) {
            $fieldData = array();
            $fieldData[] = ExcelCsvUtil::wrapTd($page->idx--);
            $fieldData[] = ExcelCsvUtil::wrapTd($val['claimRegDt']);
            $fieldData[] = ExcelCsvUtil::wrapTd($val['claimTypeKr']);
            $fieldData[] = ExcelCsvUtil::wrapTd($val['companyNm']);
            $fieldData[] = ExcelCsvUtil::wrapTd($val['memId']);
            $fieldData[] = ExcelCsvUtil::wrapTd($val['memNm']);
            $fieldData[] = ExcelCsvUtil::wrapTd($val['nickNm']);
            $fieldData[] = ExcelCsvUtil::wrapTd($val['masterCellPhone']);
            $fieldData[] = ExcelCsvUtil::wrapTd($val['orderNo'],'text','mso-number-format:\'\@\'');
            $fieldData[] = ExcelCsvUtil::wrapTd($val['bdSno']);
            
            $claimGoodsHtml = "<table>";
            foreach( $val['claimGoods'] as $claimGoods ){
                foreach( $claimGoods['option'] as $claimOption ){
                    if( !empty($claimOption['optionCnt']) ){
                        $claimGoodsHtml .= "<tr><td>{$claimOption['optionCode']}</td><td>{$claimGoods['goodsNm']}";
                        if(!empty($claimOption['optionName'])) {
                            $claimGoodsHtml .= $claimOption['optionName'];
                        }
                        $claimGoodsHtml .= "</td>";
                        $claimGoodsHtml .= "<td>".number_format($claimOption['optionCnt'])."</td></tr>";
                    }
             }
            }
            $claimGoodsHtml .= "</table>";
            
            $exchangeGoodsHtml = "<table>";
            foreach( $val['exchangeGoods'] as $exchangeGoods ){
                foreach( $exchangeGoods['goodsOptionList'] as $claimOption ){
                    if( !empty($claimOption['optionCount']) ){
                        $exchangeGoodsHtml .= "<tr><td>{$claimOption['optionCode']}</td><td>{$exchangeGoods['goodsNm']}";
                        if(!empty($claimOption['optionName'])) {
                            $exchangeGoodsHtml .= $claimOption['optionName'];
                        }
                        $exchangeGoodsHtml .= "</td>";
                        $exchangeGoodsHtml .= "<td>".number_format($claimOption['optionCount'])."</td></tr>";
                    }
             }
            }
            $exchangeGoodsHtml .= "</table>";

            $fieldData[] = ExcelCsvUtil::wrapTd($claimGoodsHtml);
            $fieldData[] = ExcelCsvUtil::wrapTd($exchangeGoodsHtml);
            $fieldData[] = ExcelCsvUtil::wrapTd($val['claimStatusKr']);
            $fieldData[] = ExcelCsvUtil::wrapTd($val['refundData']['refundTypeKr']);
            $fieldData[] = ExcelCsvUtil::wrapTd( str_replace("\\n",'',nl2br($val['memo'])) );
            $excelBody .=  "<tr>". implode('',$fieldData) . "</tr>";
        }
        $simpleExcelComponent = SlLoader::cLoad('Excel','SimpleExcelComponent','sl');
        $simpleExcelComponent->simpleDownload('클레임리스트',ClaimListService::EXCEL_LIST_TITLES,$excelBody);
    }

}