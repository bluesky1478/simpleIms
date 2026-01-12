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
namespace Component\Order\Sql;

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
use SiteLabUtil\SlCommonUtil;
use SlComponent\Database\DBUtil;
use SlComponent\Database\DBUtil2;
use SlComponent\Database\SearchVo;
use SlComponent\Database\TableVo;
use SlComponent\Util\SitelabLogger;

class OrderServiceSql {

    /**
     * 주문상품 일련번호 가져오기
     * @param $orderNo
     * @param $goodsNo
     * @param $optionSno
     * @return mixed
     */
    public function getOrderGoodsSno($orderNo,$goodsNo,$optionSno){
        $searchVo = new SearchVo();
        $searchVo->setWhereArray(['orderNo=?','goodsNo=?','optionSno=?']);
        $searchVo->setWhereValueArray([$orderNo,$goodsNo,$optionSno]);
        $tableVo = new TableVo('es_orderGoods','tableOrderGoods');
        $tableVo->setField('sno');
        return DBUtil::getComplexList([$tableVo],$searchVo)[0]['sno'];
    }

    /**
     * 주문에 적용된 정책 가져오기
     * @param $orderNo
     * @param $orderGoodsSno
     * @return mixed
     */
    public function getOrderGoodsPolicyApplyInfo($orderNo,$orderGoodsSno){
        $tableVo = new TableVo('sl_orderGoodsPolicy','tableOrderGoodsPolicy');
        $tableVo->setField( '
                SUM(freeDcCount) AS freeDcCount
                , SUM(freeDcAmount) AS freeDcAmount 
                , SUM(companyPayment) AS companyPayment 
                , SUM(buyerPayment) AS buyerPayment
                , MAX(policyInfo) AS policyInfo 
        ' );
        $searchVo = new SearchVo(['orderNo=?','orderGoodsSno=?'],[$orderNo,$orderGoodsSno]);
        return DBUtil::getOneBySearchVo($tableVo, $searchVo);
    }

    /**
     * 주문 상품과 상품 옵션을 가져온다.
     * @param $orderNo
     * @return mixed
     */
    public function getOrderGoodsAndGoodsOption($orderNo){
        $table['table1'] = new TableVo(DB_ORDER_GOODS,'tableOrderGoods','a');
        $table['table1']->setField('
            a.*
        ');
        //$orderGoods['taxSupplyGoodsPrice'] + $orderGoods['taxVatGoodsPrice'] + $orderGoods['realTaxFreeGoodsPrice'];
        $table['table2'] = new TableVo(DB_GOODS_OPTION,'tableGoodsOption','b');
        $table['table2']->setJoinType('JOIN');
        $table['table2']->setJoinCondition('a.goodsNo = b.goodsNo AND a.optionSno = b.sno'); //b.sno는 변경될 수 있지만 입력당시만 조인해서 넣으면 된다.
        $searchVo = new SearchVo('a.orderNo=?',$orderNo);
        return DBUtil::getComplexList($table,$searchVo);
    }

    /**
     * @param $orderGoodsSno
     * @return mixed
     */
    public function getOneOrderGoodsPolicy($orderGoodsSno){
        $searchVo = new SearchVo('orderGoodsSno=?',orderGoodsSno);
        $searchVo->setLimit('0,1'); //top 1
        $searchVo->setOrder('regDt');
        return DBUtil::getOneBySearchVo('sl_orderGoodsPolicy', $searchVo);
    }

    /**
     * 주문정보 가져오기
     * @param $orderNo
     * @return mixed
     */
    public function getOrderInfo($orderNo){
        return DBUtil::getOne(DB_ORDER,'orderNo',$orderNo);
    }


    /**
     * 주문 상품 옵션 목록
     * @param $orderNo
     * @return mixed
     */
    public function getOrderGoodsOptionList($orderNo){

        $table['a'] = new TableVo(DB_ORDER_GOODS,'tableOrderGoods','a');
        $table['a']->setField('a.*');
        $table['b'] = new TableVo(DB_GOODS_OPTION,'tableGoodsOption','b');
        $table['b']->setField('
            b.optionNo                    
            , concat(a.goodsNo,a.orderStatus, LPAD( if(b.optionNo is null,a.optionSno,b.optionNo)  ,6,\'0\') ) AS orderGoodsOptionKey
        ');
        $table['b']->setJoinType('LEFT OUTER JOIN');
        $table['b']->setJoinCondition('a.goodsNo = b.goodsNo');
        $table['b']->setJoinCondition('a.optionSno = b.sno');

        $table['c'] = new TableVo(DB_GOODS,'tableGoods','c');
        $table['c']->setField('
            c.imagePath                    
            , c.imageStorage                    
        ');
        $table['c']->setJoinType('LEFT OUTER JOIN');
        $table['c']->setJoinCondition('a.goodsNo = c.goodsNo');

        $table['d'] = new TableVo(DB_GOODS_IMAGE,'tableGoodsImage','d');
        $table['d']->setField('
            d.imageName
        ');
        $table['d']->setJoinType('LEFT OUTER JOIN');
        $table['d']->setJoinCondition('a.goodsNo = d.goodsNo AND d.imageKind = \'list\' ');

        $searchVo = new SearchVo();
        $searchVo->setWhere( " 'r' <> left(a.orderStatus,1) and 'e' <> left(a.orderStatus,1) and a.orderNo = ?");
        $searchVo->setWhereValue( $orderNo  );
        $searchVo->setOrder('orderCd');

        return DBUtil2::getComplexList($table, $searchVo);
    }


    /**
     * 결제이력 가져오기
     * @param $orderNo
     * @return mixed
     */
    public function getPaymentsHistory($orderNo, $isWithoutCancel = false){
        $strSQL = "
                SELECT a.*
                     , b.orderStatus -- 결제 상태
                     , b.totalGoodsPrice + b.totalDeliveryCharge AS totalPayed -- 총지불금액 + b.useMileage + b.useDeposit  
                     , b.useMileage
                     , b.useDeposit
                     , b.totalCouponOrderDcPrice AS totalCoupon
                     , b.settlePrice
                     , b.settleKind -- 결제 형태
                     , b.orderStatus AS collectOrderStatus
                     , b.paymentDt -- 결제일자
                     , b.pgTid -- 결제 트랜잭션
                     , c.statusFl       AS cashIssueFl -- 현금 영수증 발행상태
                     , t.issueStatusFl AS taxIssueFl  -- 세금 계산서 발행상태
                     , b.bankAccount AS bankAccount
                  FROM sl_collectOrder a
                  LEFT OUTER JOIN es_order b  
                    ON a.collectOrderNo = b.orderNo
                  LEFT OUTER JOIN es_orderCashReceipt c
                    ON a.collectOrderNo = c.orderNo
                  LEFT OUTER JOIN es_orderTaxIssue t 
                    ON a.collectOrderNo = t.orderNo
                 WHERE a.orderNo = ? -- 발주 주문번호  -- AND b.orderStatus = 'p1' -- 결제 완료된 것만
                 ";
        if( true === $isWithoutCancel ){
            $strSQL .= " AND a.delFl = 'n'   ";
        }
        $strSQL .= "ORDER BY a.regDt";

        $arrBind = [];
        $db = \App::getInstance('DB');
        $db->bind_param_push($arrBind, 's', $orderNo);
        return DBUtil2::runSelect($strSQL, $arrBind);
    }


    /**
     * 상품 주문일자 동기화
     * @param $orderNo
     */
    public function updateSyncRegDt( $orderNo ){
        $strSQL = "UPDATE es_orderGoods A INNER JOIN es_order B
                              ON A.orderNo = B.orderNo
                              SET A.regDt = B.regDt
                        WHERE B.orderNo = {$orderNo}   
        ";
        DBUtil2::runSql($strSQL);
    }

}