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
class StockCompareSql {

    use ListSqlTrait;

    const MAIN_TABLE = 'sl_3plProduct';

    //창고 재고 총수량
    public function getTotalCount($searchData){
        $sql = "select sum(stockCnt) as stockCnt from sl_3plProduct  where scmNo = {$searchData['scmNo']}";
        return DBUtil2::runSelect($sql)[0]['stockCnt'];
    }

    //창고 미판매 재고 수량
    public function getTotalNotSaleCount($searchData){
        $sql = "select sum(stockCnt) as stockCnt from sl_3plProduct 
                 where scmNo = {$searchData['scmNo']}
                   and thirdPartyProductCode not in ( 
                     select distinct optionCode from es_goodsOption a  
                     join es_goods b on a.goodsNo = b.goodsNo 
                     where b.scmNo = {$searchData['scmNo']}
                     and b.delFl = 'n'   
                 )";
        return DBUtil2::runSelect($sql)[0]['stockCnt'];
    }

    //폐쇄몰 판매 재고 수량
    public function getTotalMallCount($searchData){
        $sql = "select sum(a.stockCnt) as stockCnt 
from es_goodsOption a 
join es_goods b 
on a.goodsNo = b.goodsNo     
join sl_3plProduct e on a.optionCode = e.thirdPartyProductCode
where b.delFl = 'n'
and b.stockFl = 'y'
--  and b.goodsSellFl = 'y'
and b.scmNo = {$searchData['scmNo']}";
        return DBUtil2::runSelect($sql)[0]['stockCnt'];
    }

    //출고 대기 수량
    public function getTotalWaitCount($searchData){
        $sql = "select sum(goodsCnt) as stockCnt
from es_orderGoods  a
join es_goods b on a.goodsNo = b.goodsNo
left outer join sl_orderAccept c on a.orderNo = c.orderNo
join es_goodsOption d on a.optionSno = d.sno     
join sl_3plProduct e on d.optionCode = e.thirdPartyProductCode
where  a.scmNo = {$searchData['scmNo']}
and a.orderStatus in ( 'o1','p1','g1','p2','p3' )
and b.stockFl = 'y'";

        //제일건설 + 오티스의 경우 승인.
        $scmConfig = DBUtil2::getOne('sl_setScmConfig', 'scmNo', $searchData['scmNo']);
        //orderAcceptFl
        if( 'y' == $scmConfig['orderAcceptFl'] ){
            $sql .= ' and 3 != c.orderAcctStatus';
        }

        return DBUtil2::runSelect($sql)[0]['stockCnt'];
    }




    public function getList($searchData){
        $tableList= [
            'a' => //상품
                [
                    'data' => [ self::MAIN_TABLE ]
                    , 'field' => ['*']
                ]
        ];
        $table = DBUtil2::setTableInfo($tableList);

        //Search
        $searchVo = $this->createDefaultSearchVo($searchData);
        $searchVo = $this->setCondition($searchData, $searchVo); //기본 외 조건 추가 검색

        return ['listData' => DBUtil2::getComplexList($table ,$searchVo)];
    }

    /**
     * @param $searchData
     * @param $searchVo
     * @return mixed
     */
    public function setCondition($searchData, $searchVo){
        //공급사 검색
        $searchVo->setWhere('a.scmNo=?');
        $searchVo->setWhereValue( $searchData['scmNo'] );
        return $searchVo;
    }

     public function selectFindAttributeList($scmNo){
        $sql = "SELECT  distinct c.goodsPart
		, c.produceYear
		, c.season
		, c.goodsType		
FROM es_goods  a   
  JOIN sl_goodsFindAttribute c 
    ON a.goodsNo = c.goodsNo   
WHERE scmNo = {$scmNo}  
order by  c.goodsPart
		, c.produceYear
		, c.season
		, c.goodsType";
        return DBUtil2::runSelect($sql);
     }

}