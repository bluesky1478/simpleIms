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
class ScmAsianaSql {
    public function getCartList($searchData){
        $tableList = [
            'a' => //메인 상품
                [
                    'data' => [ 'sl_asianaCart' ]
                    , 'field' => ['a.*']
                ]
            , 'b' => [
                'data' => [ DB_GOODS_OPTION, 'LEFT OUTER JOIN', 'a.optionSno = b.sno' ]
                , 'field' => ['b.optionValue1', 'b.stockCnt']
            ]
            , 'c' => [
                'data' => [ DB_GOODS, 'LEFT OUTER JOIN', "b.goodsNo = c.goodsNo and c.delFl='n'" ]
                , 'field' => ['c.goodsNm','c.goodsAccess','c.goodsAccessGroup']
            ]
            , 'd' => [
                'data' => [ 'sl_asianaEmployee', 'LEFT OUTER JOIN', 'a.companyId = d.companyId' ]
                , 'field' => ['d.provideInfo', 'd.retiredFl', 'd.empTeam', 'd.empPart1', 'd.empPart2']
            ]
        ];
        $table = DBUtil2::setTableInfo($tableList, false);
        $searchVo = new SearchVo();
        $searchVo->setWhere('a.memNo = ?');
        if(empty($searchData['memNo'])){
            $searchVo->setWhereValue('-1');
        }else{
            $searchVo->setWhereValue($searchData['memNo']);
        }
        //$searchVo->setWhere('c.delFl = \'n\'');
        $searchVo->setOrder('a.companyId , a.name, a.optionSno');

        return DBUtil2::getComplexList($table ,$searchVo);
    }

}