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
class InoutListSql {

    use ListSqlTrait;

    const MAIN_TABLE = 'sl_3plStockInOut';

    public function getList($searchData){
        $tableList= [
            'a' => //메인
                [
                    'data' => [ self::MAIN_TABLE ]
                    , 'field' => ['*']
                ]
            , 'b' => //상품 정보
                [
                    'data' => [ 'sl_3plProduct', 'JOIN', 'a.productSno = b.sno' ]
                    , 'field' => ['productName', 'optionName', 'scmName']
                ]
            , 'c' => //등록자
                [
                    'data' => [ DB_MANAGER, 'LEFT OUTER JOIN', 'a.managerSno = c.sno' ]
                    , 'field' => ['managerNm']
                ]
            , 'd' => //회원타입
                [
                    'data' => [ 'sl_setMemberConfig', 'LEFT OUTER JOIN', 'a.memNo = d.memNo' ]
                    , 'field' => ['memberType']
                ]
            , 'e' => //주문정보
                [
                    'data' => [ 'es_orderInfo', 'LEFT OUTER JOIN', 'a.orderNo = e.orderNo' ]
                    , 'field' => ['receiverCellPhone', 'receiverPhone']
                ]
        ];
        $table = DBUtil2::setTableInfo($tableList);

        //Search
        $searchVo = $this->createDefaultSearchVo($searchData);
        $searchVo = $this->setCondition($searchData, $searchVo); //기본 외 조건 추가 검색

        //정렬 설정
        //$searchVo->setOrder($searchData['sort']);

        return DBUtil2::getComplexListWithPaging($table ,$searchVo, $searchData, false);
    }

    public function getListSummaryData($searchData){
        $tableList= [
            'a' => //메인
                [
                    'data' => [ self::MAIN_TABLE ]
                    , 'field' => ["sum(if(1=a.inOutType,a.quantity,0)) as inTotal"]
                ]
            , 'b' => //상품 정보
                [
                    'data' => [ 'sl_3plProduct', 'JOIN', 'a.productSno = b.sno' ]
                    , 'field' => ["sum(if(2=a.inOutType,a.quantity,0)) as outTotal"]
                ]
        ];
        $table = DBUtil2::setTableInfo($tableList, false);

        //Search
        $searchVo = $this->createDefaultSearchVo($searchData);
        $searchVo = $this->setCondition($searchData, $searchVo); //기본 외 조건 추가 검색

        //정렬 설정
        $searchVo->setOrder($searchData['sort']);

        return DBUtil2::getComplexList($table,$searchVo);
    }


    /**
     * @param $searchData
     * @param $searchVo
     * @return mixed
     */
    public function setCondition($searchData, $searchVo){
        //1. 공급사
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
        //2. 유형
        if( !empty($searchData['inOutType']) ){
            $searchVo->setWhere( 'a.inOutType = ?');
            $searchVo->setWhereValue( $searchData['inOutType']  );
        }
        //3. 사유
        if( !empty($searchData['inOutReason'])  && 'all' !== $searchData['inOutReason'][0]  ){
            $searchVo->setWhere(DBUtil::bind('a.inOutReason', DBUtil::IN, count($searchData['inOutReason']) ));
            $searchVo->setWhereValueArray( $searchData['inOutReason'] );
        }
        
        //4. 파트너
        if( !empty($searchData['memberType'])  && 'all' !== $searchData['memberType'][0] && '' !== $searchData['memberType'][0]  ){
            $searchVo->setWhere( 'd.memberType = ?');
            $searchVo->setWhereValue( $searchData['memberType']  );
        }
        return $searchVo;
    }

}