<?php
namespace Controller\Admin\Erp\ControllerService;

use App;
use Component\Erp\ErpCodeMap;
use Component\Member\Manager;
use Component\Scm\ScmConst;
use Component\Storage\Storage;
use Component\Work\WorkCodeMap;
use Framework\Debug\Exception\AlertBackException;
use Framework\Utility\DateTimeUtils;
use LogHandler;
use Request;
use Session;
use SiteLabUtil\SlCommonUtil;
use SlComponent\Database\DBConst;
use SlComponent\Database\DBUtil;
use SlComponent\Database\DBUtil2;
use SlComponent\Database\SearchVo;
use SlComponent\Godo\ListInterface;
use SlComponent\Util\ListUtil;
use SlComponent\Util\SitelabLogger;
use SlComponent\Util\SlCodeMap;
use SlComponent\Util\SlCommonTrait;
use SlComponent\Util\SlLoader;

/**
 * 재고표
 * Class GoodsStock
 * @package Component\Goods
 */
class ScmCalcService {

    use SlCommonTrait;

    private $sql;
    private $search;

    public function __construct(){
        $this->sql =  SlLoader::sqlLoad(__CLASS__);
    }
    /**
     * 검색 데이터 설정
     * @param $searchData
     */
    public function _setSearch($searchData){
        SlCommonUtil::refineTrimData($searchData);
        //기본값 설정.
        if(empty($searchData['scmNo'])){
            if( Manager::isProvider() ){
                $this->search['scmNo'] = \Session::get('manager.scmNo');
            }else{
                $this->search['scmNo'] = 8; //Default
            }
        }else{
            $this->search['scmNo'] = $searchData['scmNo'];
        }

        //날짜 기간 설정
        $period = 25; //전달의.
        $this->search['treatDate'][0] = gd_isset($searchData['treatDate'][0],date('Y-m-01', strtotime('-' . $period . ' day')));
        $this->search['treatDate'][1] = gd_isset($searchData['treatDate'][1],date('Y-m-t', strtotime('-' . $period . ' day')));

        if(!empty($searchData['detail'])){
            $this->search['detail'] = $searchData['detail'];
        }
        if(!empty($searchData['exchange'])){
            $this->search['exchange'] = $searchData['exchange'];
        }

    }

    /**
     * 정산 리스트
     * @param mixed  $searchData   검색 데이타
     * @return array 주문 리스트 정보
     */
    public function getList($searchData){
        $this->_setSearch($searchData);
        $data['search'] = $this->search;

        $scmCalcData = ScmConst::SCM_ITEM[$this->search['scmNo']]['calc'];
        $data['calcData'] = $scmCalcData;
        $workAmount = $scmCalcData['workAmount'];
        $packageAmount = $scmCalcData['packageAmount'];
        $packageBegin = $scmCalcData['packageBegin'];
        $polyAmount = $scmCalcData['polyAmount'];
        $polyBoxGuide = $scmCalcData['polyBoxGuide'];
        $boxAmount = $scmCalcData['boxAmount'];
        $exchangeAmount = $scmCalcData['exchangeAmount'];

        $totalData = [];

        $list = $this->sql->getList($this->search);
        //SitelabLogger::logger2(__METHOD__, $list);

        $refineList = [];
        foreach($list as $key => $each){
            //$each['prdPrice'] = $each['qty'] * $each['prdPrice'];
            if( $each['orderSettlePrice'] > 0 ){ //직접 구매건
                $each['prdPrice'] = 0;
                $totalData['prdPrice'] += ($each['qty'] * $each['prdPrice']);
            }
            $totalData['prdPrice'] += ($each['qty'] * $each['prdPrice']);
            //SitelabLogger::logger2(__METHOD__, "{$each['productName']} :  {$each['qty']} * {$each['prdPrice']} = {$totalData['prdPrice']}");

            //합포장 : 4장 이상
            if( $each['qty'] >= $packageBegin && ('y' == $each['workPayedFl'] || empty($each['workPayedFl'])) ){
                $packageGoodsCount = $each['qty']-($packageBegin-1);
                $packagePrice = $packageGoodsCount * $packageAmount;
                $totalData['packageCount']++;
                $totalData['packageGoodsCount']+=$packageGoodsCount;
            }else{
                $packagePrice = 0;
            }
            $each['packagePrice'] = $packagePrice;

            $polyPrice = 0;
            if( 'y' == $each['workPayedFl'] || empty($each['workPayedFl']) ){
                if( $each['qty'] > $polyBoxGuide  ){
                    //박스 10장 4천원, 폴리백 10500원
                    //$boxCount = floor($each['qty']/($polyBoxGuide+1));
                    $boxCount = 1;

                    $boxPrice = $boxCount * $boxAmount;
                    $each['boxPrice'] = $boxPrice;
                    $each['boxCount'] = $boxCount;
                    $totalData['boxCount'] += $boxCount;
                    $totalData['boxOrderCount']++;
                    $totalData['boxPrice']+=$boxPrice;
                    //잔여 폴리백으로.
                    /*$polyCount = $each['qty']%($polyBoxGuide+1);
                    if(!empty($polyCount)){
                        $polyPrice = $polyAmount;
                        $each['polyPrice'] = $polyPrice;
                        $totalData['polyCount']++;
                    }*/
                }else{
                    //폴리백
                    $polyPrice = $polyAmount;
                    $each['polyPrice'] = $polyPrice;
                    $totalData['polyCount']++;
                }
            }

            if( 'y' == $each['workPayedFl'] || empty($each['workPayedFl']) ){
                $totalData['orderCount']++;
                $totalData['goodsCount']+=$each['qty'];
                $totalData['packagePrice']+=$packagePrice;
                $totalData['polyPrice']+=$polyPrice;
                $refineList[] = $each;
            }

            /*if(SlCommonUtil::isDevId()){
                gd_debug($each['invoiceNo'] . ' : ' . $each['workPayedFl'].' : ' . $totalData['orderCount']);
            }*/

        }

        //작업비용
        $totalData['workPrice'] = $totalData['orderCount'] * $workAmount;

        //교환 수량
        $exchangeCondition = $this->search;
        $exchangeCondition['exchange'] = true;
        $exchangeCondition['detail'] = true;
        //unset($exchangeCondition['detail']);
        $exchangeList = $this->sql->getExchangeListHistory($exchangeCondition);

        $totalData['exchangeCount'] = count($exchangeList);
        $totalData['exchangePrice'] = count($exchangeList) * $exchangeAmount;
        //$totalData['prdExchangePrice'] = count($exchangeList) * 10000;
        $totalData['workPrice'] = $totalData['orderCount'] * $workAmount;

        $data['data'] = $refineList;
        $data['totalData'] = $totalData;
        //gd_debug($data);
        //gd_debug($totalData);

        return $data;
    }

    /**
     * @param $searchData
     * @return array
     */
    public function getListHistory($searchData){
        $this->_setSearch($searchData);
        $data['search'] = $this->search;

        $scmCalcData = ScmConst::SCM_ITEM[$this->search['scmNo']]['calc'];
        $data['calcData'] = $scmCalcData;

        $totalData = [];
        $list = $this->sql->getListHistory($this->search);
        foreach($list as $key => $each){
            //$each['prdPrice'] = $each['qty'] * $each['prdPrice'];
            //$totalData['prdPrice'] += $each['prdPrice'];
            if( $each['orderSettlePrice'] > 0 ){ //직접 구매건
                $each['prdPrice'] = 0;
                $totalData['prdPrice'] += ($each['qty'] * $each['prdPrice']);
            }
            $totalData['prdPrice'] += ($each['qty'] * $each['prdPrice']);

            $list[$key] = $each;
            $totalData['orderCount']++;
        }

        $data['data'] = $list;
        $data['totalData'] = $totalData;
        return $data;
    }
    public function getListExHistory($searchData){
        $this->_setSearch($searchData);
        $data['search'] = $this->search;

        $scmCalcData = ScmConst::SCM_ITEM[$this->search['scmNo']]['calc'];
        $data['calcData'] = $scmCalcData;

        $totalData = [];
        $list = $this->sql->getExchangeListHistory($this->search);
        foreach($list as $key => $each){
            //$each['prdPrice'] = $each['qty'] * $each['prdPrice'];
            //$totalData['prdPrice'] += $each['prdPrice'];
            $list[$key] = $each;
            $totalData['orderCount']++;
        }

        $data['data'] = $list;
        $data['totalData'] = $totalData;
        return $data;
    }

    public function getListDetail($searchData){
        $searchData['detail'] = true;
        return $this->getList($searchData);
    }

}
