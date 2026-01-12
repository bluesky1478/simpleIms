<?php

/**
 * This is commercial software, only users who have purchased a valid license
 * and accept to the terms of the License Agreement can install and use this
 * program.
 *
 * Do not edit or add to this file if you wish to upgrade Godomall5 to newer
 * versions in the future.
 *
 * @copyright ⓒ 2016, NHN godo: Corp.
 * @link      http://www.godo.co.kr
 */
namespace Component\Scm\Sql;

use App;
use Component\Mail\MailAutoObserver;
use Component\Godo\NaverPayAPI;
use Component\Member\Member;
use Component\Naver\NaverPay;
use Component\Database\DBTableField;
use Component\Delivery\OverseasDelivery;
use Component\Deposit\Deposit;
use Component\ExchangeRate\ExchangeRate;
use Component\Mail\MailMimeAuto;
use Component\Mall\Mall;
use Component\Mall\MallDAO;
use Component\Member\Manager;
use Component\Member\Util\MemberUtil;
use Component\Mileage\Mileage;
use Component\Policy\Policy;
use Component\Sms\Code;
use Component\Sms\SmsAuto;
use Component\Sms\SmsAutoCode;
use Component\Sms\SmsAutoObserver;
use Component\Validator\Validator;
use Component\Goods\SmsStock;
use Component\Goods\KakaoAlimStock;
use Component\Goods\MailStock;
use Encryptor;
use Exception;
use Framework\Application\Bootstrap\Log;
use Framework\Debug\Exception\AlertOnlyException;
use Framework\Debug\Exception\AlertRedirectException;
use Framework\Helper\MallHelper;
use Framework\Utility\ArrayUtils;
use Framework\Utility\ComponentUtils;
use Framework\Utility\NumberUtils;
use Framework\Utility\StringUtils;
use Framework\Utility\UrlUtils;
use Globals;
use Logger;
use LogHandler;
use Request;
use Session;
use Framework\Utility\DateTimeUtils;
use SlComponent\Database\DBUtil;
use SlComponent\Database\DBUtil2;
use SlComponent\Database\SearchVo;
use SlComponent\Database\TableVo;
use SlComponent\Util\SitelabLogger;
use SlComponent\Util\SlCodeMap;


/**
 * 주문 class
 * @author Shin Donggyu <artherot@godo.co.kr>
 */
class HkStockListSql {
    public function getGoodsList($searchData){

        $tableList = [
            'a' => //메인 상품
                [
                    'data' => [ DB_GOODS ]
                    , 'field' => [
                        'a.goodsNo',
                        'a.goodsNm',
                        'a.isOpenFl',
                    ]
                ]
            , 'b' => [
                'data' => [ DB_GOODS_OPTION, 'JOIN', 'a.goodsNo = b.goodsNo' ]
                , 'field' => ['b.optionCode', 'b.optionNo','b.optionValue1','b.optionValue2','b.optionValue3','b.optionValue4','b.optionValue5','b.stockCnt']
            ]
        ];

        $table = DBUtil2::setTableInfo($tableList, false);

        $searchVo = new SearchVo();
        //검색어
        if( !empty($searchData['keyword']) ){
            $searchVo->setWhere( DBUtil::bind( $searchData['key'], DBUtil::BOTH_LIKE ) );
            $searchVo->setWhereValue( $searchData['keyword'] );
        }

        //상품번호
        if( !empty($searchData['goodsNo']) && 'all' != $searchData['goodsNo'] ){
            $searchVo->setWhere( 'goodsNo=?' );
            $searchVo->setWhereValue( $searchData['goodsNo'] );
        }

        $searchVo->setWhere('a.delFl = \'n\'');
        $searchVo->setWhere('a.scmNo = 6');

        return DBUtil2::getComplexList($table ,$searchVo);
    }



    public function getSearchTableAndCondition($goodsNoList){
        //과거 : 출고량/기존재고 ( for 취합 마지막 재고 가져오기 )
        $tableList = [
            'a' => [
                'data' => [ 'sl_goodsStock' ]
                , 'field' => ['*']
            ],
            'b' => [
                'data' => [ 'es_order', 'JOIN', 'a.orderNo = b.orderNo' ]
                , 'field' => ['orderStatus']
            ],
        ];
        $table = DBUtil2::setTableInfo($tableList);
        $searchVo = new SearchVo();
        $searchVo->setWhere('a.stockType = 2');
        $searchVo->setWhere('a.stockReason = 6');//구매 출고 Only
        $searchVo->setWhere("left(b.orderStatus,1) IN ('o','g','d','s')");//구매 출고 Only
        $searchVo->setWhere(DBUtil2::bind('a.goodsNo', DBUtil2::IN, count($goodsNoList) ));
        $searchVo->setWhereValueArray( $goodsNoList );
        return [
            'table' => $table,
            'searchVo' => $searchVo,
        ];
    }


    /**
     * 과거 현재 공통 조회 로직
     * @param $goodsNoList
     * @param $searchData
     * @param string $searchDateType
     * @return mixed
     */
    public function getCommonSearchList($goodsNoList, $searchData, $searchDateType = 'searchDate'){
        //과거 : 출고량/기존재고 ( for 취합 마지막 재고 가져오기 )
        $cond = $this->getSearchTableAndCondition($goodsNoList);
        $table = $cond['table'];
        $searchVo = $cond['searchVo'];

        $startDate = $searchData[$searchDateType][0] .  ' 00:00:00';
        $endDate = $searchData[$searchDateType][1].  ' 23:59:59';

        $searchVo->setWhere( DBUtil::bind( 'a.regDt', DBUtil::GTS_EQ ) );
        $searchVo->setWhereValue( $startDate );
        $searchVo->setWhere( DBUtil::bind( 'a.regDt', DBUtil::LTS_EQ ) );
        $searchVo->setWhereValue( $endDate );

        if( 'searchDate' === $searchDateType ){
            $searchVo->setOrder('a.regDt');
        }

        return DBUtil2::getComplexList($table ,$searchVo);
    }

    /**
     * 과거 조회기간 데이터 (과거출고, 과거재고)
     * @param $goodsNoList
     * @param $searchData
     * @return mixed
     */
    public function getPastList($goodsNoList, $searchData){
        return $this->getCommonSearchList($goodsNoList, $searchData);
    }

    /**
     * 조회기간 데이터 ( 조회출고 )
     * @param $goodsNoList
     * @param $searchData
     * @return mixed
     */
    public function getCurrentList($goodsNoList, $searchData){
        return $this->getCommonSearchList($goodsNoList, $searchData, 'searchDate2');
    }

    /**
     * 과거일로부터 잔여 재고 리스트
     * @param $goodsNoList
     * @param $searchData
     * @return mixed
     */
    public function getRemainStockList($goodsNoList, $searchData){
        //과거 : 출고량/기존재고 ( for 취합 마지막 재고 가져오기 )
        $cond = $this->getSearchTableAndCondition($goodsNoList);
        $table = $cond['table'];
        $searchVo = $cond['searchVo'];

        $timeStamp = strtotime("{$searchData['searchDate'][1]} +1 day");
        $startDate = date("Y-m-d", $timeStamp) .  ' 00:00:00';
        $searchVo->setWhere( DBUtil::bind( 'a.regDt', DBUtil::GTS_EQ ) );
        $searchVo->setWhereValue( $startDate );

        return DBUtil2::getComplexList($table ,$searchVo);
    }

    /**
     * 누적 출고 리스트
     * @param $goodsNoList
     * @return mixed
     */
    public function getAccStockList($goodsNoList){
        //과거 : 출고량/기존재고 ( for 취합 마지막 재고 가져오기 )
        $cond = $this->getSearchTableAndCondition($goodsNoList);
        $table = $cond['table'];
        $searchVo = $cond['searchVo'];
        return DBUtil2::getComplexList($table ,$searchVo);
    }

    /**
     * 대상 상품 리스트
     * @param $searchData
     * @return mixed
     */
    public function getList($searchData){
        $table['a'] = new TableVo(DB_GOODS,'tableGoods','a');
        $table['a']->setField('a.goodsNo, a.goodsNm');

        $searchVo = new SearchVo();
        //공급사
        if( !empty($searchData['scmFl']) && 'all' !== $searchData['scmFl']  ){
            if( 'n' === $searchData['scmFl']){
                //본사
                $searchVo->setWhere('a.scmNo = ?');
                $searchVo->setWhereValue('1');
            }else{
                //공급사 검색
                $searchVo->setWhere(DBUtil::bind('a.scmNo', DBUtil::IN, count($searchData['scmNo']) ));
                $searchVo->setWhereValueArray( $searchData['scmNo']  );
            }
        }

        $searchVo->setWhere('a.stockFl = \'y\'');
        $searchVo->setWhere('a.delFl = \'n\'');

        //gd_debug($searchData);
        //getComplexList(array $tableList,SearchVo $searchVo, $isAllCountMode=false){
        return DBUtil::getComplexList($table ,$searchVo);
    }

}