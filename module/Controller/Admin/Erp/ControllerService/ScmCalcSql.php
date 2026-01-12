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
namespace Controller\Admin\Erp\ControllerService;

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
use SlComponent\Database\DBConst;
use SlComponent\Database\DBUtil;
use SlComponent\Database\DBUtil2;
use SlComponent\Database\SearchVo;
use SlComponent\Database\TableVo;
use SlComponent\Util\ListSqlTrait;
use SlComponent\Util\SitelabLogger;
use SlComponent\Util\SlLoader;


/**
 * Inout 리스트 SQL
 */
class ScmCalcSql {

    use ListSqlTrait;

    const MAIN_TABLE = 'sl_3plStockInOut';

    public function setExcludeOrderList(&$searchVo){
        $searchVo->setWhere("a.invoiceNo not in ( 
        '657253703660'
        ,'681824274620'
        ,'681746666843'
        )");
    }

    public function getList($searchData){

        if( empty($searchData['detail']) ){
            $field = ['a.inOutDate','a.invoiceNo','a.workPayedFl'];
        }else{
            $field = ['a.inOutDate','a.invoiceNo','b.productName','b.attr5','b.prdPrice','a.workPayedFl'];
        }

        $tableList= [
            'a' => //상품
                [
                    'data' => [ self::MAIN_TABLE ]  //sl_3plStockInOut
                    , 'field' => $field
                ],
            'b' => //프로젝트
                [
                    'data' => [ 'sl_3plProduct', 'JOIN', 'a.thirdPartyProductCode = b.thirdPartyProductCode' ]
                    , 'field' => ['sum(a.quantity) as qty']
                ],
            'c' => //주문
                [
                    'data' => [ 'es_order', 'LEFT OUTER JOIN', 'a.orderNo = c.orderNo' ]
                    , 'field' => ['c.settlePrice as orderSettlePrice']
                ],
            'd' => //회원타입
                [
                    'data' => [ 'sl_setMemberConfig', 'LEFT OUTER JOIN', 'c.memNo = d.memNo' ]
                    , 'field' => []
                ],
            'e' => //회원
                [
                    'data' => [ 'es_member', 'LEFT OUTER JOIN', 'c.memNo = e.memNo' ]
                    , 'field' => []
                ],
            'f' => //그룹
                [
                    'data' => [ 'es_memberGroup', 'LEFT OUTER JOIN', 'e.groupSno = f.sno' ]
                    , 'field' => []
                ]
        ];
        
        $table = DBUtil2::setTableInfo($tableList, false);

        //Search
        $searchData['treatDateFl'] = 'a.inOutDate';
        $searchVo = $this->createDefaultSearchVo($searchData);
        $searchVo->setGroup(implode(',', $field));
        //$searchVo->setWhere('a.orderNo != \'\'');

        $scmNo = gd_isset($searchData['scmNo'],8);
        if( !empty($searchData['exchange']) ){
            $searchVo->setWhere('a.inoutReason = 4 and a.inOutType = 2 and b.scmNo=?');
        }else{
            $searchVo->setWhere('a.inoutReason = 2 and a.inOutType = 2 and b.scmNo=?');
        }
        $searchVo->setWhereValue($scmNo);
        if( 8 == $scmNo){
            $searchVo->setWhere("( 1 = memberType or e.groupSno in (1,2,12) )"); //파트너 제외 (특별회원도 포함)
            $this->setExcludeOrderList($searchVo);
        }
        $searchVo->setWhere("( a.payedFl <> 'y' or 4991 = c.memNo  )"); //유료구매 제외, 단 마스터는 무료.

        return DBUtil2::getComplexList($table ,$searchVo);
    }

    public function getListHistory($searchData){
        $field = ['a.inOutDate','a.invoiceNo','b.productName','b.attr5','b.prdPrice','a.orderNo','a.customerName', 'a.inOutReason','a.workPayedFl']; //주문번호 추가.
        $tableList= [
            'a' => //상품
                [
                    'data' => [ self::MAIN_TABLE ]
                    , 'field' => $field
                ],
            'b' => //프로젝트
                [
                    'data' => [ 'sl_3plProduct', 'JOIN', 'a.thirdPartyProductCode = b.thirdPartyProductCode' ]
                    , 'field' => ['sum(a.quantity) as qty']
                ],
            'c' => //주문
                [
                    'data' => [ 'es_order', 'LEFT OUTER JOIN', 'LEFT(a.orderNo,16) = c.orderNo' ]
                    , 'field' => ['c.settlePrice as orderSettlePrice']
                ],
            'd' => //회원타입
                [
                    'data' => [ 'sl_setMemberConfig', 'LEFT OUTER JOIN', 'c.memNo = d.memNo' ]
                    , 'field' => ['d.memberType']
                ],
            'e' => //회원
                [
                    'data' => [ 'es_member', 'LEFT OUTER JOIN', 'c.memNo = e.memNo' ]
                    , 'field' => ['e.memNm']
                ],
            'f' => //그룹
                [
                    'data' => [ 'es_memberGroup', 'LEFT OUTER JOIN', 'e.groupSno = f.sno' ]
                    , 'field' => ['f.groupNm']
                ]
        ];

        $field[] = 'd.memberType';
        $field[] = 'e.memNm';
        $field[] = 'f.groupNm';

        $table = DBUtil2::setTableInfo($tableList, false);
        //Search
        $searchData['treatDateFl'] = 'a.inOutDate';
        $searchVo = $this->createDefaultSearchVo($searchData);

        $searchVo->setGroup(implode(',', $field));

        //$searchVo->setWhere('a.orderNo != \'\'');
        $scmNo = gd_isset($searchData['scmNo'],8);
        if( !empty($searchData['exchange']) ){
            $searchVo->setWhere('a.inoutReason = 4 and a.inOutType = 2 and b.scmNo=?');
        }else{
            $searchVo->setWhere('a.inoutReason = 2 and a.inOutType = 2 and b.scmNo=?');
        }
        $searchVo->setWhereValue($scmNo);

        //TKE 정산 전용
        if( 8 == $scmNo){
            $searchVo->setWhere("(1 = memberType or e.groupSno in (1,2,12) )"); //파트너 제외
            $this->setExcludeOrderList($searchVo);
        }
        $searchVo->setWhere("( a.payedFl <> 'y' or 4991 = c.memNo  )"); //유료구매 제외, 단 마스터는 무료.

        return DBUtil2::getComplexList($table ,$searchVo, false, true);
    }

    /**
     * @param $searchData
     * @return mixed
     */
    public function getExchangeListHistory($searchData){
        $field = ['a.inOutDate','a.invoiceNo','a.orderNo','a.customerName', 'a.inOutReason']; //주문번호 추가.
        $tableList= [
            'a' => //상품
                [
                    'data' => [ self::MAIN_TABLE ]
                    , 'field' => $field
                ],
            'b' => //프로젝트
                [
                    'data' => [ 'sl_3plProduct', 'JOIN', 'a.thirdPartyProductCode = b.thirdPartyProductCode' ]
                    , 'field' => ['sum(a.quantity) as qty']
                ],
            'c' => //주문
                [
                    'data' => [ 'es_order', 'LEFT OUTER JOIN', 'LEFT(a.orderNo,16) = c.orderNo' ]
                    , 'field' => ['c.settlePrice as orderSettlePrice', 'GROUP_CONCAT(b.productName) as productName']
                ],
            'd' => //회원타입
                [
                    'data' => [ 'sl_setMemberConfig', 'LEFT OUTER JOIN', 'a.memNo = d.memNo' ]
                    , 'field' => ['d.memberType']
                ],
            'e' => //회원
                [
                    'data' => [ 'es_member', 'LEFT OUTER JOIN', 'a.memNo = e.memNo' ]
                    , 'field' => ['e.memNm']
                ],
        ];
        $table = DBUtil2::setTableInfo($tableList, false);
        //Search
        $searchData['treatDateFl'] = 'a.inOutDate';
        $searchVo = $this->createDefaultSearchVo($searchData);
        $searchVo->setGroup(implode(',', $field));
        $scmNo = gd_isset($searchData['scmNo'],8);
        $searchVo->setWhere('a.inoutReason = 4 and a.inOutType = 2 and b.scmNo=?');
        $searchVo->setWhereValue($scmNo);

        if( 8 == $scmNo){
            $searchVo->setWhere("(1 = memberType or e.groupSno in (1,2,12) )"); //파트너 제외
            $this->setExcludeOrderList($searchVo);
        }

        return DBUtil2::getComplexList($table ,$searchVo);
    }


}