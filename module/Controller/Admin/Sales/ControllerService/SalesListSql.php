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
namespace Controller\Admin\Sales\ControllerService;

use App;
use Component\Database\DBIms;
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
 * Recap Customer 리스트 SQL
 */
class SalesListSql {

    use ListSqlTrait;

    const MAIN_TABLE = 'sl_salesCustomerInfo';

    public function getList($searchData){
        $tableList= [
            'a' => //메인
                [
                    'data' => [ self::MAIN_TABLE ]
                    , 'field' => ['*']
                ]
        ];
        $table = DBUtil2::setTableInfo($tableList);

        //Search
        $searchVo = $this->createDefaultSearchVo($searchData);
        $searchVo = $this->setCondition($searchData, $searchVo); //기본 외 조건 추가 검색
        //정렬 설정
        $searchVo->setOrder($searchData['sort']);

        return DBUtil2::getComplexListWithPaging($table ,$searchVo, $searchData, false);
    }

    /**
     * @param $searchData
     * @param $searchVo
     * @return mixed
     */
    public function setCondition($searchData, $searchVo){
        $step = \Request::request()->get('step');
        if(!empty($step) && 'all' !== $step){
            $searchVo->setWhere('a.customerType = ?');
            $searchVo->setWhereValue($step);
        }
        //5. 기간
        if( !empty( $searchData['searchDate'][0] )  && !empty( $searchData['searchDate'][1] ) ){
            //$searchData['searchDateFl'] = 'a.contactDt';
            $searchVo->setWhere( DBUtil::bind( $searchData['searchDateFl']  , DBUtil::GTS_EQ ) );
            $searchVo->setWhereValue( $searchData['searchDate'][0] );
            $searchVo->setWhere( DBUtil::bind( $searchData['searchDateFl']  , DBUtil::LTS_EQ ) );
            $searchVo->setWhereValue( $searchData['searchDate'][1]  );
        }

        //현재 url
        /*if (strpos(Request::getFullFileUri(), 'produce_complete') !== false) {
            $searchVo->setWhere( 'a.recapStatus = 1');
        }else{
            $searchVo->setWhere( 'a.recapStatus = 0');
        }*/

        return $searchVo;
    }

}