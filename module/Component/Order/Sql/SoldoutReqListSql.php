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
use SlComponent\Database\DBUtil2;
use SlComponent\Database\SearchVo;
use SlComponent\Database\TableVo;
use SlComponent\Util\SitelabLogger;


/**
 * 주문 class
 * @author Shin Donggyu <artherot@godo.co.kr>
 */
class SoldoutReqListSql {

    public function getList($searchData, $type){

        $tableList = [
            'a' => //메인 - 클레임 데이타
                [
                    'data' => [ 'sl_soldOutReqList' ]
                    , 'field' => [
                        'distinct a.scmNo',
                        'a.reqName',
                        'a.cellPhone',
                        'a.memNo',
                        'a.goodsNo',
                        'a.deliveryName',
                        'a.deliveryCode',
                        'a.sendType',
                        'a.regDt',
                        'a.sendDt',
                        'a.sno as reqSno',
                    ]
                ]
            , 'c' => [
                'data' => [ DB_MEMBER, 'JOIN', 'a.memNo = c.memNo' ]
                , 'field' => ['c.memNm', 'c.memId', 'c.nickNm' ]
            ]
            , 'd' => [
                'data' => [ DB_SCM_MANAGE, 'JOIN', 'a.scmNo = d.scmNo' ]
                , 'field' => ['d.companyNm' ]
            ]
            , 'e' => [
                'data' => [ DB_GOODS, 'JOIN', 'a.goodsNo = e.goodsNo' ]
                , 'field' => ['e.goodsNm' ]
            ]
        ];

        if( 'goods' == $type ) {
            $tableList['b'] = [
                'data' => [ 'sl_soldOutReqOptionList', 'LEFT OUTER JOIN', 'a.sno = b.reqSno' ]
                , 'field' => ['b.reqCnt','b.optionInfo'] //조건 처리시 주석 해제.
            ];
        }

        $table = DBUtil2::setTableInfo($tableList, false);

        //Search
        $searchVo = new SearchVo();

        //정렬 설정
        $searchVo->setOrder($searchData['sort']);

        //발송 상태 (전체,미전송,기성복,자동)
        if( isset($searchData['sendType']) && 'all' !== $searchData['sendType'] ){
            if( $searchData['sendType'] > 0 ){
                $searchVo->setWhere('a.sendType>?');
            }else{
                $searchVo->setWhere('a.sendType=?');
            }
            $searchVo->setWhereValue( $searchData['sendType']  );
        }

        //클레임 타입
        if( !empty($searchData['claimType']) && 'all' !== $searchData['claimType'][0] ){
            $searchVo->setWhere(DBUtil::bind('a.claimType', DBUtil::IN, count($searchData['claimType']) ));
            $searchVo->setWhereValueArray( $searchData['claimType']  );
        }

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
        //검색어
        if( !empty($searchData['keyword']) ){
            $searchVo->setWhere( DBUtil::bind( $searchData['key'], DBUtil::AFTER_LIKE ) );
            $searchVo->setWhereValue( $searchData['keyword'] );
        }
        //기간
        if( !empty( $searchData['searchDate'][0] )  && !empty( $searchData['searchDate'][1] ) ){
            $searchVo->setWhere( DBUtil::bind( $searchData['searchDateFl']  , DBUtil::GTS_EQ ) );
            $searchVo->setWhereValue( $searchData['searchDate'][0].' 00:00:00' );
            $searchVo->setWhere( DBUtil::bind( $searchData['searchDateFl']  , DBUtil::LTS_EQ ) );
            $searchVo->setWhereValue( $searchData['searchDate'][1].' 23:59:59'  );
        }

        return DBUtil2::getComplexListWithPaging($table ,$searchVo, $searchData, false, false);

    }

}