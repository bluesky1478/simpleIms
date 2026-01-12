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
namespace Component\Imsv2\Sql;

use App;
use Component\Ims\ImsDBName;
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
class ImsCustomerListSql {

    use ListSqlTrait;

    const MAIN_TABLE = ImsDBName::CUSTOMER;

    public function getCustomerListTableInfo(){
        $tableInfo = [
            'cust' => //고객정보(메인)
                [
                    'data' => [ ImsDBName::CUSTOMER ]
                    , 'field' => ['cust.*']
                ]
            , 'b' => //등록자
                [
                    'data' => [ DB_MANAGER, 'LEFT OUTER JOIN', 'cust.regManagerSno = b.sno' ]
                    , 'field' => ['b.managerNm as regManagerNm']
                ]
            , 'sales' => //영업 담당자
                [
                    'data' => [ DB_MANAGER, 'LEFT OUTER JOIN', 'cust.salesManagerSno = sales.sno' ]
                    , 'field' => ['sales.managerNm as salesManagerNm']
                ]
            , 'prj' => //영업 담당자
                [
                    'data' => [ ImsDBName::PROJECT, 'LEFT OUTER JOIN', 'cust.sno = prj.customerSno' ]
                    , 'field' => ['prj.customerSno as projectCustomerSno']
                ]
        ];
        return DBUtil2::setTableInfo($tableInfo, false);
    }

    //레거시
    public function getList($searchData){
        $tableList= [
            'a' => //메인
                [
                    'data' => [ self::MAIN_TABLE ]
                    , 'field' => ['*']
                ]
            , 'b' => //상품 정보
                [
                    'data' => [ DB_MANAGER, 'LEFT OUTER JOIN', 'a.salesManagerSno = b.sno' ]
                    , 'field' => ['managerNm as salesManagerNm']
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
        return $searchVo;
    }

}