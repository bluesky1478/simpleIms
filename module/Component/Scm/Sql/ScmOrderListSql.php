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
use SiteLabUtil\SlCommonUtil;
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
class ScmOrderListSql {

    public function getList($searchData){

        $table['a'] = new TableVo(DB_ORDER,'tableOrder','a');
        $table['a']->setField(' 
            a.orderNo
            , a.memNo
            , (a.realTaxSupplyPrice + a.realTaxVatPrice ) as settlePrice
            , a.orderStatus
            , a.orderGoodsNm
            , a.regDt
            , a.settleKind           
            , a.addField  
            , a.totalDeliveryCharge            
        ');
        $table['b'] = new TableVo(DB_ORDER_INFO,'tableOrderInfo','b');
        $table['b']->setField('
            b.orderName
            , b.orderEmail 
            , b.orderPhone           
            , b.orderCellPhone            
            , b.receiverName             
            , b.receiverPhone              
            , b.receiverCellPhone
            , concat(b.receiverAddress,b.receiverAddressSub) as receiverFullAddress              
        ');
        $table['c'] = new TableVo('sl_orderAccept','tableOrderAccept','c');
        $table['c']->setField('
            c.acctDt
            , c.orderAcctStatus
            , c.reason
            , c.scmNo
        ');
        $table['d'] = new TableVo(DB_MANAGER,'tableManager','d');
        $table['d']->setField('
            d.managerId
            , d.managerNm                    
        ');

        $table['e'] = new TableVo(DB_MEMBER,'tableMember','e');
        $table['e']->setField('
            e.memNm
            , e.memId
            , e.nickNm                    
            , e.groupSno                    
        ');

        $table['f'] = new TableVo('sl_setMemberConfig','tableSetMemberConfig','f');
        $table['f']->setField('
            f.memberType, f.teamName
        ');

/*        $table['g'] = new TableVo('sl_orderScm','tableOrderScm','g');
        $table['g']->setField('
            g.scmDeliverySno
        ');*/

        /*$table['h'] = new TableVo('sl_setScmDeliveryList','tableSetScmDeliveryList','h');
        $table['h']->setField('
            h.subject as deliverySubject
        ');*/

        $table['i'] = new TableVo('es_orderGoods','tableOrderGoods','i');
        $table['i']->setField('i.orderNo as goodsOrderNo');

        //JoinType
        $table['b']->setJoinType('JOIN'); //orderInfo
        $table['c']->setJoinType('LEFT OUTER JOIN'); //orderAccept
        $table['d']->setJoinType('LEFT OUTER JOIN'); // manger
        $table['e']->setJoinType('LEFT OUTER JOIN'); // member
        $table['f']->setJoinType('LEFT OUTER JOIN'); // memberConfig
        //$table['g']->setJoinType('LEFT OUTER JOIN'); // orderScm
        //$table['h']->setJoinType('LEFT OUTER JOIN'); // orderScm
        $table['i']->setJoinType('JOIN'); // orderScm
        //Join Condition
        $table['b']->setJoinCondition('a.orderNo = b.orderNo');
        $table['c']->setJoinCondition('a.orderNo = c.orderNo');
        $table['d']->setJoinCondition('c.managerSno = d.sno');
        $table['e']->setJoinCondition('a.memNo = e.memNo');
        $table['f']->setJoinCondition('a.memNo = f.memNo');
        //$table['g']->setJoinCondition('a.orderNo = g.orderNo and c.scmNo = g.scmNo ');
        //$table['h']->setJoinCondition('g.scmDeliverySno = h.sno and g.scmNo = h.scmNo ');
        $table['i']->setJoinCondition('a.orderNo = i.orderNo');

        //Search
        $searchVo = new SearchVo();

        //정렬 설정
        $searchVo->setOrder($searchData['sort']);
        
        //그룹설정
        $searchVo->setGroup($this->getGroup());

        //특정 아이디는 제외.
        $excludeList = SlCodeMap::SCM_ORDER_EXCLUDE_MEM_NO;
        $searchVo->setWhere(DBUtil2::bind('a.memNo', DBUtil::NOT_IN, count($excludeList) ));
        $searchVo->setWhereValueArray( $excludeList );

        //1. 공급사
        //scmNo
        if( !empty($searchData['scmFl']) && 'all' !== $searchData['scmFl']  ){
            if( 'n' === $searchData['scmFl']){
                //본사
                $searchVo->setWhere('i.scmNo = ?');
                $searchVo->setWhereValue('1');
            }else{
                //공급사 검색
                $searchVo->setWhere(DBUtil::bind('i.scmNo', DBUtil::IN, count($searchData['scmNo']) ));
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

        //4. 승인상태
        if( !empty($searchData['orderAcctStatus']) ){
            $searchVo->setWhere( 'c.orderAcctStatus = ?');
            $searchVo->setWhereValue( $searchData['orderAcctStatus']  );
        }
        //4. 아시아나 승인상태
        if( !empty($searchData['asianaStatus']) ){
            if( 2 == $searchData['asianaStatus'] ){
                //승인완료
                $searchVo->setWhere( " left(a.orderStatus,1) IN ( 'o', 'p' ) and c.orderAcctStatus = ?");
                $searchVo->setWhereValue( $searchData['asianaStatus']  );
            }else if( 4 == $searchData['asianaStatus'] ){
                //출고처리
                $searchVo->setWhere( " left(a.orderStatus,1) IN ( 'g', 'd', 's' )");
            }else if( 5 == $searchData['asianaStatus'] ){
                //준비중
                $searchVo->setWhere( " left(a.orderStatus,1) IN ( 'g' )");
            }else{
                //출고불가/승인대기
                $searchVo->setWhere( "c.orderAcctStatus = ?");
                $searchVo->setWhereValue( $searchData['asianaStatus']  );
            }
        }

        //5. 배송지점
        if( !empty($searchData['scmOrderDelivery']) ){
            $searchVo->setWhere( "b.receiverAddress LIKE concat( '%' ,?, '%') ");
            $searchVo->setWhereValue( $searchData['scmOrderDelivery']  );
        }

        //6. 주문상태
        if( !empty($searchData['orderStatus']) && 'all' !== $searchData['orderStatus']  ){
            if( 'd2' === $searchData['orderStatus'] ){
                //배송완료는 구매확정 포함
                $searchVo->setWhere(" a.orderStatus IN ('d2','s1' ) " );
            }else{
                $searchVo->setWhere('a.orderStatus = ?' );
                $searchVo->setWhereValue( $searchData['orderStatus']  );
            }
        }

        //7. 회원타입
        if( !empty($searchData['memberType']) && 'all' !== $searchData['memberType']  ){
            $searchVo->setWhere('(12 = f.memberType OR f.memberType = ? )' );
            $searchVo->setWhereValue( $searchData['memberType']  );
        }

        $searchVo->setWhere( " left(a.orderStatus,1) IN ( 'o', 'p' , 'g' , 'd' , 's'  ) " );

        //gd_debug($searchData);
        return DBUtil2::getComplexListWithPaging($table ,$searchVo, $searchData);
    }

    public function getGoodsListOld($orderNo){
        $table = new TableVo('es_orderGoods','tableOrderGoods','a');
        //Search
        $searchVo = new SearchVo();
        $searchVo->setWhere( "orderNo = ?" );
        $searchVo->setWhereValue( $orderNo  );
        $searchVo->setWhere( " left(orderStatus,1) IN ( 'o', 'p' , 'g' , 'd' , 's'  ) " );
        return DBUtil::getListBySearchVo($table, $searchVo);
    }

    public function getGoodsList($orderNo, $searchData){
        $tableList = [
            'a' => //메인 상품
                [
                    'data' => [ DB_ORDER_GOODS ]
                    , 'field' => ['a.*']
                ]
            , 'b' => [
                'data' => [ 'sl_asianaOrderHistory', 'LEFT OUTER JOIN', 'a.sno = b.orderGoodsSno' ]
                , 'field' => ['b.companyId','b.name']
            ]
            , 'c' => [
                'data' => [ DB_GOODS_OPTION, 'LEFT OUTER JOIN', 'a.optionSno = c.sno' ]
                , 'field' => ['c.optionValue1']
            ]
            , 'd' => [
                'data' => [ 'sl_asianaEmployee', 'LEFT OUTER JOIN', 'b.companyId = d.companyId' ]
                , 'field' => ['d.provideInfo','d.empTeam','d.empPart1','d.empPart2',]
            ]
        ];
        $table = DBUtil2::setTableInfo($tableList, false);
        //$table = new TableVo('es_orderGoods','tableOrderGoods','a');

        //Search
        $searchVo = new SearchVo();
        $searchVo->setWhere( "a.orderNo = ?" );
        $searchVo->setWhereValue( $orderNo  );

        //아시아나 주문상태
        if( !empty($searchData['asianaStatus']) ){
            if( 2 == $searchData['asianaStatus'] ){
                //승인완료
                $searchVo->setWhere( " left(a.orderStatus,1) IN ( 'o', 'p' )");
            }else if( 4 == $searchData['asianaStatus'] ){
                //출고처리
                $searchVo->setWhere( " left(a.orderStatus,1) IN ( 'g', 'd', 's' )");
            }else if( 5 == $searchData['asianaStatus'] ){
                //준비중
                $searchVo->setWhere( " left(a.orderStatus,1) IN ( 'g' )");
            }
        }

        return DBUtil2::getComplexList($table ,$searchVo);
    }

    public function getGroup(){
        return "a.orderNo
            , a.memNo
            , (a.realTaxSupplyPrice + a.realTaxVatPrice )
            , a.orderStatus
            , a.orderGoodsNm
            , a.regDt
            , a.settleKind           
            , a.addField  
            , a.totalDeliveryCharge
            , b.orderName
            , b.orderEmail 
            , b.orderPhone           
            , b.orderCellPhone            
            , b.receiverName             
            , b.receiverPhone              
            , b.receiverCellPhone
            , concat(b.receiverAddress,b.receiverAddressSub)      
            , c.acctDt
            , c.orderAcctStatus
            , c.reason
            , c.scmNo
            , d.managerId
            , d.managerNm      
            , e.memNm
            , e.memId
            , e.nickNm
            , i.orderNo                     
            ";
    }

}