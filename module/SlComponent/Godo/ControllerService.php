<?php
namespace SlComponent\Godo;

use Component\Claim\ReturnListService;
use Component\Erp\ErpCodeMap;
use Component\Member\Util\MemberUtil;
use SiteLabUtil\SlCommonUtil;
use SlComponent\Database\DBUtil;
use SlComponent\Database\DBUtil2;
use SlComponent\Database\SearchVo;
use SlComponent\Util\ExcelCsvUtil;
use SlComponent\Util\SitelabLogger;
use SlComponent\Util\SlCodeMap;
use SlComponent\Util\SlLoader;

/**
 * 컨트롤러 셋팅 서비스
 * ★ 순환 참조 가능성 조심! ★
 * Class SlCode
 * @package SlComponent\Godo
 */
class ControllerService {
    /**
     * Popup Restock set
     * @param $controller
     */
    public function setRestockData($controller){
        $optionCountList = [];
        for($i=0; 20>=$i; $i++){
            $optionCountList[] = $i;
        }
        $goodsData = $controller->getData('goodsData');
        foreach( $goodsData['option'] as $key => $each){
            $each['optionCount'] = $optionCountList;
            $goodsData['option'][$key] = $each;
        }
        $controller->setData('goodsData', $goodsData);
        $scmService=SlLoader::cLoad('godo','scmService','sl');
        $scmService->setDeliverySelectFl(MemberUtil::getMemberScmNo(), $controller);
    }

    public function setReturnListData($controller){
        $returnListService = \App::load('\\Component\\Claim\\ReturnListService');

        $getValue = \Request::request()->toArray();
        $getValue = SlCommonUtil::getRefineValueAndExcelDownCheck($getValue);
        $getData = $returnListService->getReturnList($getValue);
        if(  !empty($getValue['simple_excel_download'])  ){
            $controller->simpleExcelDownload($getData);
            exit();
        }

        //라디오 체크
        $controller->setData('checked', $getData['checked']);
        //검색정보
        $controller->setData('search', $getData['search']);
        //페이지
        $controller->setData('page', $getData['page']);

        //타이틀
        $controller->setData('listTitles',ReturnListService::LIST_TITLES);
        $controller->setData('listTitlesFront',ReturnListService::LIST_TITLES_FRONT);
        //리스트 데이터
        $controller->setData('data',$getData['data']);

        //리스트당 상품 수량
        $controller->setData('reqGoodsCnt',$getData['reqGoodsCnt']);

        //클레임 유형 맵
        $controller->setData('claimTypeMap',SlCodeMap::CLAIM_TYPE);
        $controller->setData('requestUrl', basename(\Request::getPhpSelf()) .'?simple_excel_download=1&'.  \Request::getQueryString() );

        $controller->setData('warehouseReturnMap',ErpCodeMap::WAREHOUSE_RETURN);
        $controller->setData('warehouseReturnPrdMap',ErpCodeMap::WAREHOUSE_RETURN_PRD);
    }


    /**
     * 리로드 설정
     * @param $controller
     */
    public static function setReloadData($controller){
        $current_page = \Request::getRequestUri();
        if (!empty( \Session::get('view_last_page') ) && \Session::get('view_last_page') === $current_page) {
            $controller->setData('isReload', 'y');
        } else {
            \Session::set('view_last_page',$current_page);
            $controller->setData('isReload', 'n');
        }
    }

}
