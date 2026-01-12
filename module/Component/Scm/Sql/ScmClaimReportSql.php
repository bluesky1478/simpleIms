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
use SlComponent\Database\SearchVo;
use SlComponent\Database\TableVo;
use SlComponent\Util\SitelabLogger;


/**
 * 공급사 재고 리포트 SQL
 * @author Shin Donggyu <artherot@godo.co.kr>
 */
class ScmClaimReportSql {

    /**
     * 공통 조건 Bind 설정
     * @param $db
     * @param $arrBind
     * @param $searchData
     */
    public function commonBind($db,&$arrBind,$searchData){
        $startDate = $searchData['searchDate'][0] .  ' 00:00:00';
        $endDate = $searchData['searchDate'][1].  ' 23:59:59';
        $db->bind_param_push($arrBind, 's', $startDate);
        $db->bind_param_push($arrBind, 's', $endDate);
        $db->bind_param_push($arrBind, 'i', $searchData['scmNo']);
    }

    /**
     * 공통 실행
     * @param $strSQL
     * @param $searchData
     * @return mixed
     */
    public function commonRun($strSQL, $searchData){
        $arrBind = [];
        $db = \App::getInstance('DB');
        $this->commonBind($db,$arrBind,$searchData);
        return DBUtil::runSelect($strSQL, $arrBind);
    }

    /**
     * 클레임 정보 반환
     * @param $searchData
     * @return mixed
     */
    public function getClaimInfo($searchData){
        $strSQL = "
SELECT
SUM(optionCount) AS claimCnt
, a.goodsNo
, a.claimBoardType
, a.claimReason
, b.goodsNm
, b.imageStorage
, b.imagePath
, b.goodsPrice
, d.imageName
  FROM sl_claimBoardAdminData a
   JOIN es_goods b
     ON a.goodsNo = b.goodsNo
   JOIN es_goodsImage d
    ON a.goodsNo = d.goodsNo
  AND d.imageKind = 'list'
  JOIN es_order c 
    ON c.orderNo = a.orderNo 
WHERE c.regDt >= ?
  AND c.regDt <= ?
  AND b.scmNo = ?
GROUP BY a.goodsNo
, a.claimBoardType
, a.claimReason
, b.goodsNm
, b.imageStorage
, b.imagePath
, b.goodsPrice
, d.imageName
ORDER BY a.claimBoardType, a.goodsNo, claimCnt desc
        ";
        return $this->commonRun($strSQL, $searchData);
    }

//-----------------------------------------
    /**
     * 전체 출고 현황 (1)
     * @param $searchData
     * @return mixed
     */
    public function getTotalClaimCnt($searchData){
        $strSQL = "
SELECT SUM(backCnt) AS backCnt
         , SUM(refundCnt) AS refundCnt
         , SUM(exchangeCnt) AS exchangeCnt
         , SUM(asCnt) AS asCnt
         , SUM(claimCnt) AS claimCnt
         , COUNT(1) AS acctCnt
FROM (
    SELECT SUM(IF(a.claimBoardType='back',optionCount,0))       AS backCnt
             , SUM(IF(a.claimBoardType='refund',optionCount,0))     AS refundCnt     
             , SUM(IF(a.claimBoardType='exchange',optionCount,0)) AS exchangeCnt
             , SUM(IF(a.claimBoardType='as',optionCount,0))           AS asCnt
             , SUM(optionCount)                                                AS claimCnt
             , a.bdId
             , a.bdSno    
      FROM sl_claimBoardAdminData a
        JOIN es_order b 
         ON a.orderNo = b.orderNo
       JOIN sl_orderScm c 
         ON a.orderNo = c.orderNo
    WHERE b.regDt >= ?
       AND b.regDt <= ?
       AND c.scmNo = ?
   GROUP BY a.bdId, a.bdSno        
) a   
";
        return $this->commonRun($strSQL, $searchData)[0];
    }
    /**
     * 전체 출고 현황 (2)
     * @param $searchData
     * @return mixed
     */
    public function getTotalGoodsCnt($searchData){
        $strSQL = "
SELECT SUM(goodsCnt) AS goodsCnt 
         , SUM(settlePrice) AS settlePrice 
         , COUNT(1) AS orderCnt
FROM  ( SELECT SUM(a.goodsCnt) AS goodsCnt 
                     , SUM(b.settlePrice) AS settlePrice 
                     , b.orderNo                   
              FROM es_orderGoods a
                JOIN es_order b
                  ON a.orderNo = b.orderNo  
             WHERE a.regDt >= ?
               AND a.regDt <= ?
               AND a.scmNo = ?
               AND left(a.orderStatus,1) in ( 'p',  'd' , 's'  )
            GROUP BY b.orderNo 
) a
";
        return $this->commonRun($strSQL, $searchData)[0];
    }
//-----------------------------------------

    /**
     * 공급사 상품 정보
     * @param $searchData
     * @return mixed
     */
    public function getScmGoodsInfo($searchData){
        $strSQL = "
SELECT a.goodsNo
         , a.optionNo
         , b.goodsNm
         , b.imageStorage
         , b.imagePath
         , c.imageName
         , b.totalStock -- 현재 재고
         , b.goodsPrice
         , a.optionValue1
         , a.optionValue2
         , a.optionValue3
         , a.optionValue4
         , a.optionValue5
         , a.optionPrice
         , a.stockCnt
         , d.safeCnt
         , md5(b.optionName) as optionClass
 FROM es_goodsOption a
   JOIN es_goods b
    ON a.goodsNo = b.goodsNo   
  LEFT OUTER JOIN es_goodsImage c
    ON a.goodsNo = c.goodsNo
  AND c.imageKind = 'list'
  LEFT OUTER JOIN sl_goodsSafeStock d 
    ON a.goodsNo = d.goodsNo
  AND a.optionNo = d.optionNo  
WHERE b.scmNo = {$searchData['scmNo']}
  AND b.delFl = 'n'
        ";
        return $this->commonRun($strSQL, $searchData);
    }


}