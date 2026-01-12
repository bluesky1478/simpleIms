<?php

namespace Controller\Admin\Erp;

use App;
use Component\Category\BrandAdmin;
use Component\Category\CategoryAdmin;
use Component\Ims\ImsCodeMap;
use Component\Ims\ImsDBName;
use Component\Scm\ScmAsianaService;
use Component\Stock\StockListService;
use Component\Work\WorkCodeMap;
use Controller\Admin\Erp\AdminErpControllerTrait;
use Controller\Admin\Erp\ControllerService\InoutListService;
use Controller\Admin\Ims\ImsControllerTrait;
use Controller\Admin\Ims\ImsListControllerTrait;
use Controller\Admin\Ims\Step\ImsStepTrait;
use Globals;
use Request;
use SiteLabUtil\SlCommonUtil;
use SlComponent\Database\DBUtil2;
use SlComponent\Database\SearchVo;
use SlComponent\Util\ExcelCsvUtil;
use SlComponent\Util\SitelabLogger;
use SlComponent\Util\SlCodeMap;
use SlComponent\Util\SlLoader;
use function SlComponent\Util\SlLoader;

/**
 * 폐쇄몰 재고 관리
 */
class StockManageController extends \Controller\Admin\Controller{

    use ImsControllerTrait;
    use ImsStepTrait;
    use ImsListControllerTrait;

    public function index(){
        $this->callMenu('erp', 'stock', 'stockManage');

        $this->setDefault();

        $search['combineSearch'] = [
            'goods.goodsNm' => '상품명',
            'goods.goodsNo' => '상품번호',
        ];
        $this->setData('search', $search);

        //재고 관리 사이트
        $searchVo = new SearchVo('b.stockManageFl=?','y');
        $searchVo->setOrder('a.scmNo ');

        $list=DBUtil2::getJoinList(DB_SCM_MANAGE, [
            'b' => [ 'sl_setScmConfig', 'a.scmNo=b.scmNo', 'b.cateCd, b.stockManageFl, b.memo, b.files' ]
        ],$searchVo);

        $scmMapCate = SlCommonUtil::arrayAppKeyValue($list, 'scmNo', 'cateCd');
        $scmMap = SlCommonUtil::arrayAppKeyValue($list, 'scmNo', 'companyNm');

        //순서 강제 변경
        if(SlCommonUtil::isDev()){
            $order = [6,8]; // 앞에 정렬할 키만 지정
        }else{
            $order = [32, 34, 8,6]; // 앞에 정렬할 키만 지정
        }

        $sorted = [];
        // 1. 우선순위 있는 항목 추가
        foreach ($order as $key) {
            if (isset($scmMap[$key])) {
                $sorted[$key] = $scmMap[$key];
            }
        }
        // 2. 나머지 항목 추가 (순서 유지)
        foreach ($scmMap as $key => $value) {
            if (!isset($sorted[$key])) {
                $sorted[$key] = $value;
            }
        }

        $this->setData('scmMapCate', $scmMapCate);
        $this->setData('scmMap', $sorted);
        $this->setData('scmList', $list);
        $this->setData('firstScm', $order[0]);

        $this->addScript([
            'jquery/jquery.multi_select_box.js',
        ]);
        $cate = \App::load('\\Component\\Category\\CategoryAdmin');
        $this->setData('cate', $cate);

        $this->setData('asianaWaitDeliveryCnt', ScmAsianaService::getDeliveryWaitCount() );

        $request = $this->getData('requestParam');
        if(1 == $request['simple_excel_download']){
            $this->downloadType1($request);
            exit();
        }
    }

    public function downloadType1($params){
        $params['scmCate'] = json_decode($params['scmCate'], true);
        $params['multiKey'] = json_decode($params['multiKey'], true);

        $stockService = SlLoader::cLoad('imsv2','ImsStockService');
        $list = $stockService->getGoodsStockTotalInfo($params);
        //SitelabLogger::logger2(__METHOD__, $list);

        $contents=[];
        $target=0;
        $name=1;
        $type=2;

        $dataMap = [
            '상품번호' => ['goods','goodsNo','s'],
            '상품명' => ['goods','goodsNm','s'],
            '옵션명' => ['each','optionName','s'],
            '판매수량' => ['each','stockCnt','i'],
            '창고수량' => ['each','realCnt','i'],
            '예약수량' => ['each','reserveCnt','i'],
            '입고수량' => ['each','inCnt','i'],
            '출고수량' => ['each','outCnt','i'],
            '출고율' => ['each','outRate','c'],
            '연도별수량' => ['each','realCntOfYear','c'],
            '24년도' => ['each','year24','c'],
            '25년도' => ['each','year25','c'],
        ];
        $titles = [];
        foreach($dataMap as $title => $data){
            $titles[] = $title;
        }

        foreach($list['goodsList'] as $listData){
            foreach($listData['option'] as $option){
                $contentsRows = [];
                foreach($dataMap as $title => $data){
                    if( 'goods' === $data[$target] ){
                        $contentsRows[] = ExcelCsvUtil::wrapTd($listData[$data[$name]]);
                    }else{
                        if( 'c' === $data[$type] && 'outRate' === $data[$name] ){
                            $rate = round($option['outCnt']/$listData['outCnt']*100);
                            $contentsRows[] = ExcelCsvUtil::wrapTd($rate.'%');
                        }else if( 'c' === $data[$type] && 'realCntOfYear' === $data[$name] ){
                            $yearCntTextList = [];
                            $yearCntList = explode(',',$option['realCntOfYear']);
                            foreach( $yearCntList as $yearCntRaw ){
                                if(!empty($yearCntRaw) && strpos($yearCntRaw,':')!==false ){
                                    $yearCntData = explode(':',$yearCntRaw);
                                    $yearCntTextList[] = $yearCntData[0].'년:'.$yearCntData[1];
                                }
                            }
                            $contentsRows[] = ExcelCsvUtil::wrapTd(implode(' ',$yearCntTextList));
                        }else if( 'c' === $data[$type] && 'year24' === $data[$name] ){
                            $yearCntTextList = [];
                            $yearCntList = explode(',',$option['realCntOfYear']);
                            foreach( $yearCntList as $yearCntRaw ){
                                if(!empty($yearCntRaw) && strpos($yearCntRaw,':')!==false ){
                                    $yearCntData = explode(':',$yearCntRaw);
                                    if( '24' == $yearCntData[0] ){
                                        $yearCntTextList[] = $yearCntData[1];
                                    }
                                }
                            }
                            $contentsRows[] = ExcelCsvUtil::wrapTd(implode(' ',$yearCntTextList));
                        }else if( 'c' === $data[$type] && 'year25' === $data[$name] ){
                            $yearCntTextList = [];
                            $yearCntList = explode(',',$option['realCntOfYear']);
                            foreach( $yearCntList as $yearCntRaw ){
                                if(!empty($yearCntRaw) && strpos($yearCntRaw,':')!==false ){
                                    $yearCntData = explode(':',$yearCntRaw);
                                    if( '25' == $yearCntData[0] ){
                                        $yearCntTextList[] = $yearCntData[1];
                                    }
                                }
                            }
                            $contentsRows[] = ExcelCsvUtil::wrapTd(implode(' ',$yearCntTextList));
                        }else{
                            $contentsRows[] = ExcelCsvUtil::wrapTd($option[$data[$name]]);
                        }
                    }
                }
                $contents[] = '<tr>'.implode('',$contentsRows).'</tr>';
            }

        }

        $simpleExcelComponent = SlLoader::cLoad('Excel','SimpleExcelComponent','sl');
        $simpleExcelComponent->simpleDownload('재고관리_RAWDATA', $titles, implode('',$contents));
    }

}