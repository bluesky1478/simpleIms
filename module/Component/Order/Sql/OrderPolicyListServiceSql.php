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
use SlComponent\Database\DBUtil;
use SlComponent\Database\SearchVo;
use SlComponent\Database\TableVo;
use SlComponent\Util\SitelabLogger;


/**
 * 주문 class
 * @author Shin Donggyu <artherot@godo.co.kr>
 */
class OrderPolicyListServiceSql {

    public function getList($searchData){

        $table['a'] = new TableVo('sl_orderGoodsPolicy','tableOrderGoodsPolicy','a');
        $table['a']->setField(' 
            a.sno
            , a.goodsNo
            , a.optionNo
            , a.memNo
            , a.orderNo
            , a.orderGoodsSno
            , a.freeDcCount
            , a.freeDcAmount
            , a.companyPayment
            , a.buyerPayment
            , a.cancelReason
            , a.policyInfo
            , ( a.freeDcAmount + a.companyPayment + a.buyerPayment ) AS totalPayment
            , a.regDt  
        ');

        $table['b'] = new TableVo(DB_GOODS,'tableGoods','b');
        $table['b']->setField('
            b.goodsNm                    
        ');

        $table['c'] = new TableVo(DB_GOODS_OPTION,'tableGoodsOption','c');
        $table['c']->setField('
            concat(c.optionValue1,c.optionValue2,c.optionValue3,c.optionValue4,c.optionValue5) AS optionNm
        ');

        $table['d'] = new TableVo(DB_MEMBER,'tableMember','d');
        $table['d']->setField('
            d.memNm
            , d.memId                    
        ');

        $table['e'] = new TableVo(DB_SCM_MANAGE,'tableScmManage','e');
        $table['e']->setField('
            e.companyNm
        ');

        $table['f'] = new TableVo(DB_ORDER_GOODS,'tableOrderGoods','f');
        $table['f']->setField('
            f.orderStatus
            , f.goodsCnt
        ');

        //JoinType
        $table['b']->setJoinType('LEFT OUTER JOIN');
        $table['c']->setJoinType('LEFT OUTER JOIN');
        $table['d']->setJoinType('LEFT OUTER JOIN');
        $table['e']->setJoinType('LEFT OUTER JOIN');
        $table['f']->setJoinType('LEFT OUTER JOIN');
        //Join Condition
        $table['b']->setJoinCondition('a.goodsNo = b.goodsNo');
        $table['c']->setJoinCondition('a.goodsNo = c.goodsNo AND a.optionNo = c.optionNo');
        $table['d']->setJoinCondition('a.memNo = d.memNo');
        $table['e']->setJoinCondition('b.scmNo = e.scmNo');
        $table['f']->setJoinCondition('a.orderGoodsSno = f.sno AND a.orderNo = f.orderNo ');

        //Search
        $searchVo = new SearchVo();

        //정렬 설정
        $searchVo->setOrder($searchData['sort']);

        //1. 공급사
        //scmNo
        if( !empty($searchData['scmFl']) && 'all' !== $searchData['scmFl']  ){
            if( 'n' === $searchData['scmFl']){
                //본사
                $searchVo->setWhere('b.scmNo = ?');
                $searchVo->setWhereValue('1');
            }else{
                //공급사 검색
                $searchVo->setWhere(DBUtil::bind('b.scmNo', DBUtil::IN, count($searchData['scmNo']) ));
                $searchVo->setWhereValueArray( $searchData['scmNo']  );
            }
        }

        //2. 검색어
        if( !empty($searchData['keyword']) ){
            $searchVo->setWhere( DBUtil::bind( $searchData['key'], DBUtil::AFTER_LIKE ) );
            $searchVo->setWhereValue( $searchData['keyword'] );
        }

        //5. 기간
        if( !empty( $searchData['searchDate'][0] )  && !empty( $searchData['searchDate'][1] ) ){
            $searchVo->setWhere( DBUtil::bind( $searchData['searchDateFl']  , DBUtil::GTS_EQ ) );
            $searchVo->setWhereValue( $searchData['searchDate'][0] );
            $searchVo->setWhere( DBUtil::bind( $searchData['searchDateFl']  , DBUtil::LTS_EQ ) );
            $searchVo->setWhereValue( $searchData['searchDate'][1]  );
        }

        //gd_debug($searchData);
        return DBUtil::getComplexListWithPaging($table ,$searchVo, $searchData);
    }

}