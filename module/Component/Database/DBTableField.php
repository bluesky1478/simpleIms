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

namespace Component\Database;

use Component\Ims\ImsDB;
use Component\Ims\ImsJsonSchema;
use Session;
use SiteLabUtil\SlCommonUtil;
use SlComponent\Util\SlCodeMap;

/**
 * DB Table 기본 Field 클래스 - DB 테이블의 기본 필드를 설정한 클래스 이며, prepare query 생성시 필요한 기본 필드 정보임
 * @package Component\Database
 * @static  tableConfig
 */
class DBTableField extends \Bundle\Component\Database\DBTableField{

    use DBIms;
    use DBNkIms;

    /**
     * 테이블 필드 반환
     * @param $tableName
     * @param array $unsetList
     * @return array
     * @throws \Exception
     */
    public static function getDefaultFieldList($tableName,array $unsetList=[]){
        $extFieldList = DBTableField::getTableKey($tableName);
        $extFieldList = array_combine($extFieldList, $extFieldList);
        $unsetList = array_merge($unsetList, ['sno','regDt','modDt']);
        return SlCommonUtil::unsetByList($extFieldList, $unsetList);
    }

    /**
     * 데이터를 불러올 때 필요한 데이터 기본 정제작업
     * @param $tableName
     * @param $data
     * @return
     * @throws \Exception
     */
    public static function refineGetData($tableName, $data){
        $tableInfoList = DBTableField::callTableFunction($tableName); //sl_XXX
        foreach($tableInfoList as $tableData){
            if( true === $tableData['strip'] ){
                $data[$tableData['val']] = gd_htmlspecialchars_stripslashes($data[$tableData['val']]);
            }
            if( true === $tableData['json'] ){
                $data[$tableData['val']] = json_decode($data[$tableData['val']], true);
            }
            if( true === $tableData['isFile'] && ( empty($data[$tableData['val']]) || 0 >= count($data[$tableData['val']])  ) ){
                $data[$tableData['val']] = ImsJsonSchema::getDefaultFileSchema();
            }
        }
        return $data;
    }

    /**
     * 값의 개행문자 치환
     * @param $tableName
     * @param $data
     * @return mixed
     * @throws \Exception
     */
    public static function fieldStrip($tableName, $data){
        $tableInfoList = DBTableField::callTableFunction($tableName);
        foreach($tableInfoList as $tableData){
            if( true === $tableData['strip'] ){
                $data[$tableData['val']] = gd_htmlspecialchars_stripslashes($data[$tableData['val']]);
            }
        }
        return $data;
    }

    /**
     * 값의 JSON Array로 변경
     * @param $tableName
     * @param $data
     * @param string $type
     * @return mixed
     * @throws \Exception
     */
    public static function parseJsonField($tableName, $data, $type='decode'){
        $tableInfoList = DBTableField::callTableFunction($tableName);
        foreach($tableInfoList as $tableData){
            if( true === $tableData['json']){ //여파가 좀 있다.
                //SitelabLogger::logger('=====================');
                //SitelabLogger::logger($data[$tableData['val']]);

                if( 'decode' === $type ){
                    $data[$tableData['val']] = json_decode($data[$tableData['val']], true);
                }else{
                    $data[$tableData['val']] = json_encode($data[$tableData['val']]);
                }
                //SitelabLogger::logger($data[$tableData['val']]);
                //SitelabLogger::logger('=====================');
            }
        }
        return $data;
    }

    public static function setJsonField($tableName, $data, $type='decode'){
        $tableInfoList = DBTableField::callTableFunction($tableName);
        foreach($tableInfoList as $tableData){
            if( true === $tableData['json'] && isset($data[$tableData['val']]) ){ //여파가 좀 있다.
                if( 'decode' === $type ){
                    $data[$tableData['val']] = json_decode($data[$tableData['val']], true);
                }else{
                    $data[$tableData['val']] = json_encode($data[$tableData['val']]);
                }
            }
        }
        return $data;
    }

    public static function parseJsonFieldList($tableName, $list){
        foreach($list as $key => $each){
            $list[$key] = DBTableField::parseJsonField($tableName, $each);
        }
        return $list;
    }

    /**
     * 필수값 체크.
     * @param $tableName
     * @param $params
     * @throws \Exception
     */
    public static function checkRequired($tableName, $params){
        $tableInfoList = DBTableField::callTableFunction($tableName);
        foreach($tableInfoList as $tableData){
            if( true === $tableData['required'] ){
                if( empty($params[$tableData['val']]) ){
                    throw new \Exception($tableData['name'] . '은 필수값 입니다.');
                }
            }
        }
    }

    /**
     * 필수값 체크하면서 JSON 인코딩
     * @param $tableName
     * @param $params
     * @return mixed
     * @throws \Exception
     */
    public static function checkRequiredWithJsonEncode($tableName, $params){
        $tableInfoList = DBTableField::callTableFunction($tableName);
        foreach($tableInfoList as $tableData){
            if( true === $tableData['required'] ){
                if( empty($params[$tableData['val']]) ){
                    throw new \Exception($tableData['name'] . '은 필수값 입니다.');
                }
            }
            if( true === $tableData['json'] ){
                $params[$tableData['val']] = json_encode($params[$tableData['val']]);
            }
        }
        return $params;
    }


    /**
     * 저장시 기본 체크
     * ( 저장시 필수값 체크및 json encode , 레거시 소스때문에 기본 save에 넣기 애매함.  )
     * @param $tableName
     * @param $saveData
     * @return mixed
     * @throws \Exception
     */
    public static function checkAndRefineSaveData($tableName, $saveData){
        DBTableField::checkRequired($tableName, $saveData);
        return DBTableField::parseJsonField($tableName, $saveData, 'encode');
    }

    /**
     * 테이블 구조를 키로하고 데이터는 공백으로 전달
     * @param $funcName
     * @return array
     */
    public static function getTableBlankData($funcName){
        $tableKeyList = SlCommonUtil::getArrayKeyData(self::$funcName(), 'val'  );
        $result = [];
        foreach( $tableKeyList as $each ){
            $result[$each] = null;
        }
        return $result;
    }

    /**
     * 필드의 타입을 알아온다.
     * 지정한 타입이 없다면 전체 필드의 타입 배열을 반환한다.
     * @param $funcName
     * @param null $field
     * @return array|null
     */
    public static function getType($funcName, $field=null){
        $result = null;
        $arrData = self::$funcName();
        $map = Array();
        foreach( $arrData as $each ){
            if( $field != null && $each['val'] === $field  ){
                $result = $each['typ'];
                break;
            }else{
                $map[$each['val']] = $each['typ'];
            }
        }

        if( $result === null && $field !== null ){
            $result = 's';
        }else if( $result === null ){
            $result = $map;
        }

        return $result;
    }

    public static function callTableFunction($tableName){
        $funcName = 'table'.ucfirst(explode('_',$tableName)[1]);
        //이게 있는지 체크
        $map = SlCommonUtil::getMethodMap('Component\\Database\\DBTableField');
        if(!empty($map[$funcName])){
            return  self::$funcName();            
        }else{
            throw new \Exception('정의되지 않은 DB정보 : ' . $tableName);
        }
    }

    /**
     * 키 반환
     * @param $tableName
     * @return array
     * @throws \Exception
     */
    public static function getTableKey($tableName){

        if( empty($tableName) ) throw new \Exception('테이블 명 없음');

        return  SlCommonUtil::getArrayKeyData(DBTableField::callTableFunction($tableName), 'val');
    }

    /**
     * 테이블 키와 공백값
     * @param $tableName
     * @return array
     */
    public static function getTableKeyAndBlankValue($tableName){
        $tableFieldList = DBTableField::callTableFunction($tableName);
        $rslt = [];
        foreach($tableFieldList as $each){
            $rslt[$each['val']] = $each['def'];
        }
        return $rslt;
    }

    public static function tableManagerWithSno(){
        $arrField = parent::tableManager();
        $arrField[]  = ['val' => 'sno', 'typ' => 'i', 'def' => null,'name'=>'일련번호'];
        return $arrField;
    }

    /**
     * 주문추가정보
     * @return array
     */
    public static function table3plProductExclude(){
        $arrField[]  = ['val' => 'thirdPartyProductCode', 'typ' => 's', 'def' => null,'name'=>'제외 상품 코드'];
        return $arrField;
    }

    /**
     * 주문추가정보
     * @return array
     */
    public static function tableOrderAddedData(){
        $arrField[]  = ['val' => 'sno', 'typ' => 'i', 'def' => null,'name'=>'일련번호'];
        $arrField[]  = ['val' => 'orderNo', 'typ' => 's', 'def' => null,'name'=>'주문번호'];
        $arrField[]  = ['val' => 'giftAmount', 'typ' => 'i', 'def' => null,'name'=>'선물금액'];
        $arrField[]  = ['val' => 'settlePrice', 'typ' => 'i', 'def' => null,'name'=>'구매금액'];
        $arrField[]  = ['val' => 'addDeposit', 'typ' => 'i', 'def' => null,'name'=>'적립예치금'];
        $arrField[]  = ['val' => 'reqDeliveryDt', 'typ' => 's', 'def' => null,'name'=>'배송요청일'];
        $arrField[]  = ['val' => 'memberType', 'typ' => 'i', 'def' => 0,'name'=>'회원유형']; //오픈패키지(0), 재계약(1) 고객
        $arrField[]  = ['val' => 'storeType', 'typ' => 'i', 'def' => null,'name'=>'매장유형'];//T-Station, 한국타이어..
        return $arrField;
    }

    public static function tableOrderExtend(){
        $arrField[]  = ['val' => 'sno', 'typ' => 'i', 'def' => null,'name'=>'일련번호'];
        $arrField[]  = ['val' => 'orderNo', 'typ' => 's', 'def' => null,'name'=>'주문번호'];
        $arrField[]  = ['val' => 'deliveryBoxType', 'typ' => 'i', 'def' => null,'name'=>'배송박스타입'];//1. 폴리백
        return $arrField;
    }

    //무상정책
    public static function tablePolicyFreeSale(){
        // @formatter:off
        $arrField = [
            ['val' => 'sno', 'typ' => 'i', 'def' => null],          // 일련번호
            ['val' => 'policyName', 'typ' => 's', 'def' => null],   // 정책명
            ['val' => 'freeBuyerCount', 'typ' => 'i', 'def' => 0],  // 무상횟수
            ['val' => 'useFl', 'typ' => 's', 'def' => 'y']          // 사용여부
        ];
        // @formatter:on
        return $arrField;
    }

    //할인정책
    public static function tablePolicySale(){
        // @formatter:off
        $arrField = [
            ['val' => 'sno', 'typ' => 'i', 'def' => null],              // 일련번호
            ['val' => 'policyName', 'typ' => 's', 'def' => null],       // 정책명
            ['val' => 'companyRatio', 'typ' => 'd', 'def' => '0.00'],   // 회사비율
            ['val' => 'buyerRatio', 'typ' => 'd', 'def' => '0.00'],     // 구매자비율
            ['val' => 'useFl', 'typ' => 's', 'def' => 'y']              // 사용여부
        ];
        // @formatter:on
        return $arrField;
    }

    //설문정책
    public static function tablePolicySurvey(){
        // @formatter:off
        $arrField = [
            ['val' => 'sno', 'typ' => 'i', 'def' => null],              // 일련번호
            ['val' => 'policyName', 'typ' => 's', 'def' => null],       // 정책명
            ['val' => 'surveyDayCount', 'typ' => 's', 'def' => null],   // 설문일자수(구매후)
            ['val' => 'surveyAddress', 'typ' => 's', 'def' => null],    // 설문주소
            ['val' => 'useFl', 'typ' => 's', 'def' => 'y']              // 사용여부
        ];
        // @formatter:on
        return $arrField;
    }

    // 상품 정책
    public static function tableGoodsPolicy(){
        // @formatter:off
        $arrField = [
            ['val' => 'sno', 'typ' => 'i', 'def' => null],              // 일련번호
            ['val' => 'goodsNo', 'typ' => 'i', 'def' => null],          // 상품번호
            ['val' => 'policyFreeSaleSno', 'typ' => 'i', 'def' => null],// 무상정책번호
            ['val' => 'policySaleSno', 'typ' => 'i', 'def' => null],    // 할인정책번호
            ['val' => 'policySurveySno', 'typ' => 'i', 'def' => null],  // 설문정책번호
        ];
        // @formatter:on
        return $arrField;
    }

    // 상품 정책 적용 회원
    public static function tableGoodsPolicyMember(){
        // @formatter:off
        $arrField = [
            ['val' => 'sno', 'typ' => 'i', 'def' => null],              // 일련번호
            ['val' => 'goodsNo', 'typ' => 'i', 'def' => null],          // 상품번호
            ['val' => 'memNo', 'typ' => 'i', 'def' => null],            // 회원번호
        ];
        // @formatter:on
        return $arrField;
    }

    // 상품 정책 적용 이력
    public static function tableOrderGoodsPolicy(){
        // @formatter:off
        $arrField = [
            ['val' => 'sno', 'typ' => 'i', 'def' => null],              // 일련번호
            ['val' => 'goodsNo', 'typ' => 'i', 'def' => null],          // 상품번호
            ['val' => 'optionNo', 'typ' => 'i', 'def' => null],         // 옵션번호
            ['val' => 'memNo', 'typ' => 'i', 'def' => null],            // 회원번호
            ['val' => 'orderNo', 'typ' => 's', 'def' => null],          // 주문번호
            ['val' => 'orderGoodsSno', 'typ' => 'i', 'def' => null],    // 주문상품번호
            ['val' => 'freeDcCount', 'typ' => 'i', 'def' => null],      // 무상제공숫자
            ['val' => 'freeDcAmount', 'typ' => 'i', 'def' => null],     // 무상제공가격
            ['val' => 'companyPayment', 'typ' => 'i', 'def' => null],   // 본사지불금액
            ['val' => 'buyerPayment', 'typ' => 'i', 'def' => null],     // 구매자지불금액
            ['val' => 'cancelReason', 'typ' => 's', 'def' => null],       // 취소사유
            ['val' => 'policyInfo', 'typ' => 's', 'def' => null],       // 적용정책
        ];
        // @formatter:on
        return $arrField;
    }

    // 상품 재고 이력
    public static function tableGoodsStock(){
        // @formatter:off
        $arrField = [
            ['val' => 'sno', 'typ' => 'i', 'def' => null],               // 일련번호
            ['val' => 'goodsNo', 'typ' => 'i', 'def' => null],           // 상품번호
            ['val' => 'optionNo', 'typ' => 'i', 'def' => null],          // 옵션번호
            ['val' => 'memNo', 'typ' => 'i', 'def' => null],             // 회원번호
            ['val' => 'orderNo', 'typ' => 's', 'def' => null],           // 주문번호
            ['val' => 'orderGoodsSno', 'typ' => 'i', 'def' => null],     // 주문상품번호
            ['val' => 'stockType', 'typ' => 's', 'def' => null],         // 재고유형
            ['val' => 'stockReason', 'typ' => 's', 'def' => null],       // 재고사유
            ['val' => 'stockCnt', 'typ' => 'i', 'def' => null],          // 추가/차감수량
            ['val' => 'beforeCnt', 'typ' => 'i', 'def' => null],         // 이전수량
            ['val' => 'afterCnt', 'typ' => 'i', 'def' => null],          // 현재수량
        ];
        // @formatter:on
        return $arrField;
    }


    // 사이트랩 코드 관리
    public static function tableSlCode(){
        // @formatter:off
        $arrField = [
            ['val' => 'sno', 'typ' => 'i', 'def' => null],       // 일련번호
            ['val' => 'codeType', 'typ' => 's', 'def' => null],  // 코드 타입
            ['val' => 'codeValue', 'typ' => 's', 'def' => null], // 코드 값
            ['val' => 'codeNm', 'typ' => 's', 'def' => null],    // 코드 명
            ['val' => 'codeSort', 'typ' => 'i', 'def' => null],  // 코드 순서
        ];
        // @formatter:on
        return $arrField;
    }

    // 주문 - SCM 연결
    public static function tableOrderScm(){
        // @formatter:off
        $arrField = [
            ['val' => 'sno', 'typ' => 'i', 'def' => null],       // 일련번호
            ['val' => 'orderNo', 'typ' => 's', 'def' => null],  // 주문번호
            ['val' => 'scmNo', 'typ' => 's', 'def' => null], // 공급사 번호
            ['val' => 'branchDept', 'typ' => 'i', 'def' => null], // 지점번호
            ['val' => 'scmDeliverySno', 'typ' => 'i', 'def' => null], // 배송지점
        ];
        // @formatter:on
        return $arrField;
    }

    // 튜닝 추가 클레임 이력
    public static function tableClaimHistory(){
        // @formatter:off
        $arrField = [
            ['val' => 'sno', 'typ' => 'i', 'def' => null],       // 일련번호
            ['val' => 'orderNo', 'typ' => 's', 'def' => null],  // 주문번호
            ['val' => 'memNo', 'typ' => 'i', 'def' => null],  // 회원번호
            ['val' => 'scmNo', 'typ' => 's', 'def' => null], // 공급사 번호
            ['val' => 'claimType', 'typ' => 's', 'def' => null], // 요청 구분
            ['val' => 'reqContents', 'typ' => 's', 'def' => null], // 요청내용
            ['val' => 'reqType', 'typ' => 'i', 'def' => null], // 요청분류
            ['val' => 'procStatus', 'typ' => 'i', 'def' => 1], // 처리상태
            ['val' => 'procContents', 'typ' => 's', 'def' => null], // 처리내용
            ['val' => 'procDt', 'typ' => 's', 'def' => null], // 처리일자
            ['val' => 'adminMemo', 'typ' => 's', 'def' => null], // 관리자메모
            ['val' => 'memberMemo', 'typ' => 's', 'def' => null], // 기타 개선의견
            ['val' => 'handleGroupCd', 'typ' => 'i', 'def' => null], // 클레임 처리 그룹
        ];
        // @formatter:on
        return $arrField;
    }
    // 튜닝 추가 클레임 상품 이력
    public static function tableClaimHistoryGoods(){
        // @formatter:off
        $arrField = [
            ['val' => 'sno', 'typ' => 'i', 'def' => null],       // 일련번호
            ['val' => 'claimSno', 'typ' => 'i', 'def' => null],       // 클레임번호
            ['val' => 'orderNo', 'typ' => 's', 'def' => null],  // 주문번호
            ['val' => 'handleGroupCd', 'typ' => 'i', 'def' => null], // 클레임 처리 그룹
            ['val' => 'handleSno', 'typ' => 'i', 'def' => null], // 처리 연결 번호 정보
            ['val' => 'reqGoodsSno', 'typ' => 'i', 'def' => null], // 요청 상품
            ['val' => 'reqGoodsCnt', 'typ' => 'i', 'def' => null], // 요청수량
            ['val' => 'reqGoodsIdx', 'typ' => 'i', 'def' => null], // 요청상품인덱스
        ];
        // @formatter:on
        return $arrField;
    }

    // 요청 분류
    public static function tableClaimRequestType(){
        // @formatter:off
        $arrField = [
            ['val' => 'sno', 'typ' => 'i', 'def' => null],       // 일련번호
            ['val' => 'claimType', 'typ' => 's', 'def' => null],  // 클레임 타입
            ['val' => 'reqTypeContents', 'typ' => 's', 'def' => null], // 요청분류내용
        ];
        // @formatter:on
        return $arrField;
    }

    // 주문승인
    public static function tableOrderAccept(){
        // @formatter:off
        $arrField = [
            ['val' => 'sno', 'typ' => 'i', 'def' => null],       // 일련번호
            ['val' => 'scmNo', 'typ' => 's', 'def' => null], // 공급사 번호
            ['val' => 'orderNo', 'typ' => 's', 'def' => null],  // 주문번호
            ['val' => 'orderAcctStatus', 'typ' => 's', 'def' => null], // 승인여부
            ['val' => 'managerSno', 'typ' => 's', 'def' => null], // 승인자
            ['val' => 'acctDt', 'typ' => 's', 'def' => null], // 승인시간
            ['val' => 'reason', 'typ' => 's', 'def' => null], // 사유
            ['val' => 'regDt', 'typ' => 's', 'def' => null], //
            ['val' => 'modDt', 'typ' => 's', 'def' => null], //
        ];
        // @formatter:on
        return $arrField;
    }

    // 클레임 게시판 추가 정보
    public static function tableClaimBoardData(){
        // @formatter:off
        $arrField = [
            ['val' => 'sno', 'typ' => 'i', 'def' => null],           // 일련번호
            ['val' => 'bdId', 'typ' => 's', 'def' => null],         // 게시판아이디
            ['val' => 'bdSno', 'typ' => 'i', 'def' => null],       // 게시물번호
            ['val' => 'orderNo', 'typ' => 's', 'def' => null],       // 주문번호
            ['val' => 'claimBoardType', 'typ' => 's', 'def' => null],       // 클레임게시판타입
            ['val' => 'goodsIdx', 'typ' => 'i', 'def' => null],   // 상품인덱스
            ['val' => 'optionIdx', 'typ' => 'i', 'def' => null],  // 옵션인덱스
            ['val' => 'etcRequest', 'typ' => 's', 'def' => null],  // 기타의견
            ['val' => 'goodsNo', 'typ' => 'i', 'def' => null],   // 상품번호
            ['val' => 'goodsName', 'typ' => 's', 'def' => null],   // 상품명
            ['val' => 'optionName', 'typ' => 's', 'def' => null],  // 옵션명
            ['val' => 'optionCount', 'typ' => 'i', 'def' => null],  // 옵션수량
            ['val' => 'claimReason', 'typ' => 'i', 'def' => null],    // 클레임사유
        ];
        // @formatter:on
        return $arrField;
    }

    /**
     * [ 신클레임처리 - 관리자 등록 클레임 데이터 ]
     * @return array[]
     */
    public static function tableScmClaimData(){
        // @formatter:off
        $arrField = [
            ['val' => 'sno', 'typ' => 'i', 'def' => null],          // 일련번호
            ['val' => 'scmNo', 'typ' => 'i', 'def' => null],        // 공급사번호
            ['val' => 'bdId', 'typ' => 's', 'def' => null],         // 게시판아이디
            ['val' => 'bdSno', 'typ' => 'i', 'def' => null],        // 게시물번호
            ['val' => 'orderNo', 'typ' => 's', 'def' => null],      // 주문번호
            ['val' => 'claimType', 'typ' => 's', 'def' => null],     // 클레임 타입
            ['val' => 'claimGoods', 'typ' => 's', 'def' => null],    // 클레임 상품 정보
            ['val' => 'claimGoodsCnt', 'typ' => 'i', 'def' => 0],    // 클레임 상품 수량(통계용)
            ['val' => 'exchangeGoods', 'typ' => 's', 'def' => null], // 교환 상품 정보
            ['val' => 'refundData', 'typ' => 's', 'def' => null],    // 환불정보
            ['val' => 'claimStatus', 'typ' => 'i', 'def' => 0], //클레임 처리 상태
            ['val' => 'claimRegDt', 'typ' => 's', 'def' => null], //클레임 등록일 (수정가능)
            ['val' => 'claimCompleteDt', 'typ' => 's', 'def' => null], //클레임 처리 완료일 (수정가능)
            ['val' => 'memo', 'typ' => 's', 'def' => null], //클레임 메모
            ['val' => 'regDt', 'typ' => 's', 'def' => null], //
            ['val' => 'modDt', 'typ' => 's', 'def' => null], //
        ];
        // @formatter:on
        return $arrField;
    }

    /**
     * [ 신클레임처리 - 관리자 등록 클레임 데이터 ]
     * @return array[]
     */
    /*public static function tableAdminClaimData(){
        // @formatter:off
        $arrField = DBTableField::tableAdminClaimData();
        $arrField[] = ['val' => 'claimStatus', 'typ' => 's', 'def' => null]; //클레임 처리 상태
        // @formatter:on
        return $arrField;
    }*/


    /**
     * [ 신클레임처리 - 사용자 요청 데이터 ]
     * @return array[]
     */
    public static function tableScmClaimCustomerData(){
        // @formatter:off
        $arrField = [
            ['val' => 'sno', 'typ' => 'i', 'def' => null],          // 일련번호
            ['val' => 'scmNo', 'typ' => 'i', 'def' => null],        // 공급사번호
            ['val' => 'bdId', 'typ' => 's', 'def' => null],         // 게시판아이디
            ['val' => 'bdSno', 'typ' => 'i', 'def' => null],        // 게시물번호
            ['val' => 'orderNo', 'typ' => 's', 'def' => null],      // 주문번호
            ['val' => 'claimType', 'typ' => 's', 'def' => null],     // 클레임 타입
            ['val' => 'claimGoods', 'typ' => 's', 'def' => null],    // 클레임 상품 정보
            ['val' => 'exchangeGoods', 'typ' => 's', 'def' => null], // 교환 상품 정보
            ['val' => 'refundData', 'typ' => 's', 'def' => null],    // 환불정보
        ];
        // @formatter:on
        return $arrField;
    }
    //customerClaimData : sno, scmNo, bdId, bdSno, orderNo, claimType, claimData(json), exchangeData(json), refundData(text),
    //아래는 통계? 를 위해 --> 어차피 어드민으로 넣어야함.
    //customerClaimGoodsData : sno, claimSno, goodsNo, claimGoodsCnt, claimReason

    // 클레임 게시판 추가 정보
    public static function tableClaimBoardAdminData(){
        // @formatter:off
        $arrField = [
            ['val' => 'sno', 'typ' => 'i', 'def' => null],           // 일련번호
            ['val' => 'claimSno', 'typ' => 'i', 'def' => null],       // 클레임 번호
            ['val' => 'bdId', 'typ' => 's', 'def' => null],         // 게시판아이디
            ['val' => 'bdSno', 'typ' => 'i', 'def' => null],       // 게시물번호
            ['val' => 'orderNo', 'typ' => 's', 'def' => null],       // 주문번호
            ['val' => 'claimBoardType', 'typ' => 's', 'def' => null],       // 클레임게시판타입
            ['val' => 'goodsIdx', 'typ' => 'i', 'def' => null],   // 상품인덱스
            ['val' => 'optionIdx', 'typ' => 'i', 'def' => null],  // 옵션인덱스
            ['val' => 'etcRequest', 'typ' => 's', 'def' => null],  // 기타의견
            ['val' => 'goodsNo', 'typ' => 'i', 'def' => null],   // 상품번호
            ['val' => 'goodsName', 'typ' => 's', 'def' => null],   // 상품명
            ['val' => 'optionName', 'typ' => 's', 'def' => null],  // 옵션명
            ['val' => 'optionCount', 'typ' => 'i', 'def' => null],  // 옵션수량
            ['val' => 'claimReason', 'typ' => 'i', 'def' => null],    // 클레임사유
        ];
        // @formatter:on
        return $arrField;
    }

    // 주문파일
    public static function tableOrderAttFile(){
        // @formatter:off
        $arrField = [
            ['val' => 'sno', 'typ' => 'i', 'def' => null],       // 일련번호
            ['val' => 'orderNo', 'typ' => 's', 'def' => null], // 파일명
            ['val' => 'fileName', 'typ' => 's', 'def' => null], // 파일명
            ['val' => 'fileDirPath', 'typ' => 's', 'def' => null], // 파일경로
        ];
        // @formatter:on
        return $arrField;
    }

    // 상품 안전 재고
    public static function tableGoodsSafeStock(){
        // @formatter:off
        $arrField = [
            ['val' => 'sno', 'typ' => 'i', 'def' => null],        // 일련번호
            ['val' => 'goodsNo', 'typ' => 'i', 'def' => null],    // 상품번호
            ['val' => 'optionNo', 'typ' => 'i', 'def' => null],  // 옵션번호
            ['val' => 'safeCnt', 'typ' => 'i', 'def' => null],    // 안전재고
            ['val' => 'shareNotCnt', 'typ' => 'i', 'def' => null],    // 공유제한재고
        ];
        // @formatter:on
        return $arrField;
    }

    // 공급사별 팝업
    public static function tableScmPopup(){
        // @formatter:off
        $arrField = [
            ['val' => 'sno', 'typ' => 'i', 'def' => null],        // 일련번호
            ['val' => 'scmNo', 'typ' => 'i', 'def' => null],    // 공급사번호
            ['val' => 'popupSno', 'typ' => 'i', 'def' => null],  // 팝업번호
        ];
        // @formatter:on
        return $arrField;
    }

    public static function tableMailListOfSafeCnt(){
        // @formatter:off
        $arrField = [
            ['val' => 'sno', 'typ' => 'i', 'def' => null],        // 일련번호
            ['val' => 'scmNo', 'typ' => 'i', 'def' => null],    // 공급사번호
            ['val' => 'email', 'typ' => 's', 'def' => null],
            ['val' => 'receiverName', 'typ' => 's', 'def' => null],
        ];
        // @formatter:on
        return $arrField;
    }

    public static function tableMailList(){
        // @formatter:off
        $arrField = [
            ['val' => 'sno', 'typ' => 'i', 'def' => null],        // 일련번호
            ['val' => 'scmNo', 'typ' => 'i', 'def' => null],    // 공급사번호
            ['val' => 'mailType', 'typ' => 's', 'def' => null],
            ['val' => 'email', 'typ' => 's', 'def' => null],
            ['val' => 'receiverName', 'typ' => 's', 'def' => null],
        ];
        // @formatter:on
        return $arrField;
    }

    /**
     * 회원 별 설정
     * @return array[]
     */
    public static function tableSetMemberConfig(){
        return [
            ['val' => 'sno', 'typ' => 'i', 'def' => null],        // 일련번호
            ['val' => 'memNo', 'typ' => 'i', 'def' => null],
            ['val' => 'memberType', 'typ' => 'i', 'def' => null],
            ['val' => 'buyLimitCount', 'typ' => 'i', 'def' => null],
            ['val' => 'teamName', 'typ' => 's', 'def' => null],
            ['val' => 'deliveryName', 'typ' => 's', 'def' => null],
            ['val' => 'repFl', 'typ' => 's', 'def' => 'n'],
        ];
    }

    public static function tableMember(){
        $arrField = parent::tableMember();
        $arrField[]  = ['val' => 'freeFl', 'typ' => 'i', 'def' => 'n','name'=>'무료회원여부'];
        $arrField[]  = ['val' => 'hankookType', 'typ' => 'i', 'def' => 'n','name'=>'한국타이어 매장 유형'];
        return $arrField;
    }

    public static function tableGoods($conf = null){
        $arrField = parent::tableGoods($conf);
        $arrField[]  = ['val' => 'memberType', 'typ' => 'i', 'def' => null, 'name'=>'회원유형 - 파트너 ,  정규  '];
        $arrField[]  = ['val' => 'hankookType', 'typ' => 'i', 'def' => null, 'name'=>'한국타이어 매장유형'];
        $arrField[]  = ['val' => 'addReason', 'typ' => 's', 'def' => null, 'name'=>'추가사유'];
        $arrField[]  = ['val' => 'isOpenFl', 'typ' => 's', 'def' => '0', 'name'=>'오픈패키지 여부'];
        $arrField[]  = ['val' => 'openGoodsNo', 'typ' => 's', 'def' => null, 'name'=>'오픈패키지 상품번호'];
        $arrField[]  = ['val' => 'soldOutMemo', 'typ' => 's', 'def' => null, 'name'=>'품절시 메모'];
        $arrField[]  = ['val' => 'sizeFilePath', 'typ' => 's', 'def' => null, 'name'=>'사이즈표 파일 패스'];

        $arrField[]  = ['val' => 'groupBuyStart', 'typ' => 's', 'def' => null, 'name'=>'공구시작'];
        $arrField[]  = ['val' => 'groupBuyEnd', 'typ' => 's', 'def' => null, 'name'=>'공구종료'];
        $arrField[]  = ['val' => 'groupBuyCount', 'typ' => 'i', 'def' => null, 'name'=>'적용수량'];
        $arrField[]  = ['val' => 'groupBuyPrice', 'typ' => 'i', 'def' => null, 'name'=>'할인가격'];
        $arrField[]  = ['val' => 'groupBuyComment', 'typ' => 's', 'def' => null, 'name'=>'안내문구'];
        return $arrField;
    }

    /**
     * 카카오 템플릿 정보
     * @return array
     */
    public static function tableKakaoMsg(){
        return [
            ['val' => 'templateId', 'typ' => 'i', 'def' => null],     //템플릿ID
            ['val' => 'category', 'typ' => 's', 'def' => null],        //템플릿 분류
            ['val' => 'title', 'typ' => 's', 'def' => null],              //템플릿 제목
            ['val' => 'contents', 'typ' => 's', 'def' => null],        //템플릿 내용
            ['val' => 'pramName', 'typ' => 's', 'def' => null],    //템플릿 파리미터
            ['val' => 'useFl', 'typ' => 's', 'def' => 'n'],              //사용 여부
        ];
    }

    /**
     * 카카오 메세지 발송 내역
     * @return array[]
     */
    public static function tableKakaoMsgHistory(): array
    {
        return [
            ['val' => 'sno', 'typ' => 'i', 'def' => null],                //일련번호
            ['val' => 'templateId', 'typ' => 'i', 'def' => null],      //템플릿ID
            ['val' => 'contents', 'typ' => 's', 'def' => null],         //전송내용
            ['val' => 'resultCode', 'typ' => 'i', 'def' => null],      //결과
            ['val' => 'resultData', 'typ' => 's', 'def' => null],       //결과JSON
        ];
    }

    /**
     * 주문 메세지 발송 내역
     * @return array[]
     */
    public static function tableOrderMsgHistory(){
        return [
            ['val' => 'sno', 'typ' => 'i', 'def' => null],                //일련번호
            ['val' => 'orderNo', 'typ' => 's', 'def' => null],         //주문번호
            ['val' => 'momoNo', 'typ' => 'i', 'def' => null],        //모모티 주문번호
            ['val' => 'templateId', 'typ' => 'i', 'def' => null],      //템플릿ID
        ];
    }

    /**
     * 주문 메세지 발송 내역
     * @return array[]
     */
    public static function tableResearchMsgHistory(){
        return [
            ['val' => 'sno', 'typ' => 'i', 'def' => null],                //일련번호
            ['val' => 'cellPhone', 'typ' => 's', 'def' => null],         //주문번호
        ];
    }

    /**
     * 공급사 커스텀 설정
     * @return array[]
     */
    public static function tableSetScmConfig(): array
    {
        return [
            ['val' => 'sno', 'typ' => 'i', 'def' => null],                //일련번호
            ['val' => 'scmNo', 'typ' => 'i', 'def' => null],            //공급사 번호
            ['val' => 'cateCd', 'typ' => 's', 'def' => null],            //카테고리 번호
            ['val' => 'stockManageFl', 'typ' => 's', 'def' => 'n'],      //재고관리
            ['val' => 'orderAcceptFl', 'typ' => 's', 'def' => 'n'],      //주문승인여부
            ['val' => 'memberAcceptFl', 'typ' => 's', 'def' => 'n'],  //회원승인여부
            ['val' => 'deliverySelectFl', 'typ' => 's', 'def' => 'n'],     //배송지 선택 여부
            ['val' => 'directAddressFl', 'typ' => 's', 'def' => 'n'],     //배송지 선택시 배송지 직접입력여부
            ['val' => 'memo', 'typ' => 's', 'def' => null],               //기타사항
            ['val' => 'files', 'typ' => 's', 'def' => null],               //첨부파일
        ];
    }

    public static function tableSetScmDeliveryList(): array
    {
        return [
            ['val' => 'sno', 'typ' => 'i', 'def' => null],                //일련번호
            ['val' => 'scmNo', 'typ' => 'i', 'def' => null],            //공급사 번호
            ['val' => 'subject', 'typ' => 's', 'def' => null],         //제목
            ['val' => 'receiverZipcode', 'typ' => 's', 'def' => null],         //구 우편번호
            ['val' => 'receiverZonecode', 'typ' => 's', 'def' => null],         //우편번호
            ['val' => 'receiverAddress', 'typ' => 's', 'def' => null],         //주소
            ['val' => 'receiverAddressSub', 'typ' => 's', 'def' => '-'],         //주소상세
            ['val' => 'receiverCellPhone', 'typ' => 's', 'def' => null],         //수령자 전화번호
            ['val' => 'receiverName', 'typ' => 's', 'def' => null],         //수령자명
        ];
    }


    /********************************/
    /*   업무공유 시스템 영역    */
    /********************************/

    /**
     * 프로젝트 테이블
     * @return array[]
     */
    public static function tableProject(): array
    {
        return [
            ['val' => 'sno', 'typ' => 'i', 'def' => null],
            ['val' => 'projectName', 'typ' => 's', 'def' => null],      //프로젝트 이름
            ['val' => 'projectType', 'typ' => 'i', 'def' => null],      //프로젝트 타입
            ['val' => 'projectStatus', 'typ' => 'i', 'def' => 10],       //프로젝트 상태
            ['val' => 'description', 'typ' => 's', 'def' => null],      //설명
            ['val' => 'companySno', 'typ' => 'i', 'def' => null],       //고객사 번호
            ['val' => 'companyDiv', 'typ' => 'i', 'def' => null],       //고객사 구분
            ['val' => 'meetingDt', 'typ' => 's', 'def' => null],        //미팅일
            ['val' => 'hopeDeliveryDt', 'typ' => 's', 'def' => null],   //희망납기
            ['val' => 'deadlineDt', 'typ' => 's', 'def' => null],       //발주 데드라인
            ['val' => 'planData', 'typ' => 's', 'def' => null],         //내부일정
            ['val' => 'customerPlanStatus', 'typ' => 'i', 'def' => 0],  //고객 안내 상태
            ['val' => 'customerPlanDt', 'typ' => 's', 'def' => null],   //고객 안내 일정
            ['val' => 'productData', 'typ' => 's', 'def' => null],      //상품정보
            ['val' => 'regManagerSno', 'typ' => 'i', 'def' => null],    //등록자
            ['val' => 'salesManagerSno', 'typ' => 'i', 'def' => null],  //영업 담당
            ['val' => 'designManagerSno', 'typ' => 'i', 'def' => null], //디자인 담당
        ];
    }

    /**
     * 프로젝트 코멘트
     * @return array[]
     */
    public static function tableProjectComment(): array
    {
        return [
            ['val' => 'sno', 'typ' => 'i', 'def' => null],
            ['val' => 'projectSno', 'typ' => 'i', 'def' => null],
            ['val' => 'writerName', 'typ' => 's', 'def' => null],
            ['val' => 'comment', 'typ' => 's', 'def' => null],
        ];
    }

    /**
     * 거래처
     * @return array[]
     */
    public static function tableWorkCompany(): array
    {
        return [
            ['val' => 'sno', 'typ' => 'i', 'def' => null],
            ['val' => 'salesManagerSno', 'typ' => 'i', 'def' => null],
            ['val' => 'companyName', 'typ' => 's', 'def' => null],
            ['val' => 'companyType', 'typ' => 'i', 'def' => null],
            ['val' => 'companyTypeEtc', 'typ' => 's', 'def' => null],
            ['val' => 'busiNo', 'typ' => 's', 'def' => null],
            ['val' => 'ceo', 'typ' => 's', 'def' => null],
            ['val' => 'service', 'typ' => 's', 'def' => null],
            ['val' => 'item', 'typ' => 's', 'def' => null],
            ['val' => 'phone', 'typ' => 's', 'def' => null],
            ['val' => 'fax', 'typ' => 's', 'def' => null],
            ['val' => 'address', 'typ' => 's', 'def' => null],
            ['val' => 'etc', 'typ' => 's', 'def' => null],
            ['val' => 'companyManager', 'typ' => 's', 'def' => null],
            ['val' => 'regDt', 'typ' => 's', 'def' => null],
            ['val' => 'modDt', 'typ' => 's', 'def' => null],
        ];
    }

    /**
     * 업무 문서
     * @return array[]
     */
    public static function tableWorkDocument(): array
    {
        return [
            ['val' => 'sno', 'typ' => 'i', 'def' => null],          //문서번호
            ['val' => 'projectSno', 'typ' => 'i', 'def' => null],   //프로젝트번호
            ['val' => 'docDept', 'typ' => 's', 'def' => null],      //문서사용부서
            ['val' => 'docType', 'typ' => 'i', 'def' => null],      //문서타입
            ['val' => 'version', 'typ' => 'i', 'def' => null],      //문서버전
            ['val' => 'docData', 'typ' => 's', 'def' => null],      //문서데이터
            ['val' => 'isCustomerApplyFl', 'typ' => 's', 'def' => 'n'],    //고객 승인 여부
            ['val' => 'isCustomerApplyDt', 'typ' => 's', 'def' => null],    //고객 승인 시간
            ['val' => 'isApplyFl', 'typ' => 's', 'def' => 'n'],    //승인 여부
            ['val' => 'applyManagers', 'typ' => 's', 'def' => null],//승인 정보
            ['val' => 'tempFl', 'typ' => 's', 'def' => null],       //임시 여부
            ['val' => 'delFl', 'typ' => 's', 'def' => 'n'],         //삭제 여부
            ['val' => 'modifyHistory', 'typ' => 's', 'def' => null],//수정 여부
            ['val' => 'regManagerSno', 'typ' => 'i', 'def' => null],//등록자 번호
            ['val' => 'regDt', 'typ' => 's', 'def' => null],
            ['val' => 'modDt', 'typ' => 's', 'def' => null],
        ];
    }

    /**
     * 문서 수정 이력
     * @return array[]
     */
    public static function tableWorkDocumentHistory(): array
    {
        return [
            ['val' => 'sno', 'typ' => 'i', 'def' => null],
            ['val' => 'documentSno', 'typ' => 'i', 'def' => null],
            ['val' => 'managerSno', 'typ' => 'i', 'def' => null],
            ['val' => 'diffData', 'typ' => 's', 'def' => null],
            ['val' => 'diffTransData', 'typ' => 's', 'def' => null],
            ['val' => 'regDt', 'typ' => 's', 'def' => null],
            ['val' => 'modDt', 'typ' => 's', 'def' => null],
        ];
    }

    /**
     * 업무요청
     * @return array[]
     */
    public static function tableWorkRequest(): array
    {
        return [
            ['val' => 'sno', 'typ' => 'i', 'def' => null],
            ['val' => 'writeManagerSno', 'typ' => 'i', 'def' => null],
            ['val' => 'documentSno', 'typ' => 'i', 'def' => null],
            ['val' => 'targetDeptNo', 'typ' => 's', 'def' => null],
            ['val' => 'reqContents', 'typ' => 's', 'def' => null],
            ['val' => 'resContents', 'typ' => 's', 'def' => null],
            ['val' => 'completeRequestDt', 'typ' => 's', 'def' => null],
            ['val' => 'procManagerSno', 'typ' => 'i', 'def' => null],
            ['val' => 'isProcFl', 'typ' => 's', 'def' => 'n'],
            ['val' => 'procDt', 'typ' => 's', 'def' => null],
            ['val' => 'isDelFl', 'typ' => 's', 'def' => 'n'],
            ['val' => 'regDt', 'typ' => 's', 'def' => null],
            ['val' => 'modDt', 'typ' => 's', 'def' => null],
        ];
    }

    /**
     * 환경평가항목
     * @return array[]
     */
    public static function tableWorkRatingItem(): array
    {
        return [
            ['val' => 'sno', 'typ' => 'i', 'def' => null],
            ['val' => 'ratingSubject', 'typ' => 's', 'def' => 'n'],
            ['val' => 'ratingItem', 'typ' => 's', 'def' => 'n'],
        ];
    }

    /**
     * 고객 코멘트
     * @return array[]
     */
    public static function tableWorkCustomerComment(): array
    {
        return [
            ['val' => 'sno', 'typ' => 'i', 'def' => null],
            ['val' => 'documentSno', 'typ' => 'i', 'def' => null],
            ['val' => 'writeManagerSno', 'typ' => 'i', 'def' => null],
            ['val' => 'contents', 'typ' => 's', 'def' => null],
            ['val' => 'regDt', 'typ' => 's', 'def' => null],
            ['val' => 'modDt', 'typ' => 's', 'def' => null],
        ];
    }

    /**
     * 문서별 승인라인
     * sl_workAcceptLine
     */
    public static function tableWorkAcceptLine(): array
    {
        return [
            ['val' => 'sno', 'typ' => 'i', 'def' => null],
            ['val' => 'docDept', 'typ' => 's', 'def' => null],
            ['val' => 'docType', 'typ' => 's', 'def' => null],
            ['val' => 'title', 'typ' => 's', 'def' => null],
            ['val' => 'idx', 'typ' => 'i', 'def' => null],
            ['val' => 'managerSno', 'typ' => 'i', 'def' => null],
            ['val' => 'regDt', 'typ' => 's', 'def' => null],
            ['val' => 'modDt', 'typ' => 's', 'def' => null],
        ];
    }

    /**
     * 문서 승인 이력
     * sl_workAcceptLine
     */
    public static function tableWorkAcceptHistory(): array
    {
        return [
            ['val' => 'sno', 'typ' => 'i', 'def' => null],
            ['val' => 'docSno', 'typ' => 'i', 'def' => null],
            ['val' => 'idx', 'typ' => 'i', 'def' => null],
            ['val' => 'title', 'typ' => 's', 'def' => null],
            ['val' => 'status', 'typ' => 's', 'def' => null],
            ['val' => 'managerSno', 'typ' => 'i', 'def' => null],
            ['val' => 'comment', 'typ' => 's', 'def' => null],
            ['val' => 'regDt', 'typ' => 's', 'def' => null],
            ['val' => 'modDt', 'typ' => 's', 'def' => null],
        ];
    }

    /**
     * 계획 수정 히스토리
     * @return array[]
     */
    public static function tableWorkPlanHistory(){
        return [
            ['val' => 'sno', 'typ' => 'i', 'def' => null],              //일련번호
            ['val' => 'projectSno', 'typ' => 'i', 'def' => null],       //프로젝트 번호
            ['val' => 'beforeStepData', 'typ' => 's', 'def' => null],   //수정 전 계획
            ['val' => 'afterStepData', 'typ' => 's', 'def' => null],    //수정 후 계획
            ['val' => 'reasonType', 'typ' => 'i', 'def' => null],       //사유 구분
            ['val' => 'reasonText', 'typ' => 's', 'def' => null],       //사유 상세
            ['val' => 'managerSno', 'typ' => 'i', 'def' => null],       //수정자
            ['val' => 'regDt', 'typ' => 's', 'def' => null],
            ['val' => 'modDt', 'typ' => 's', 'def' => null],
        ];
    }

    /**
     * 샘플실정보
     * @return array[]
     */
    public static function tableWorkSampleFactory(){
        return [
            ['val' => 'sno', 'typ' => 'i', 'def' => null],      //일련번호
            ['val' => 'factoryName', 'typ' => 's', 'def' => null],  //샘플실 명
            ['val' => 'factoryType', 'typ' => 's', 'def' => null],  //샘플실 타입
            ['val' => 'factoryPhone', 'typ' => 's', 'def' => null],  //샘플실 전화
            ['val' => 'factoryAddress', 'typ' => 's', 'def' => null],  //샘플실 주소
            ['val' => 'regDt', 'typ' => 's', 'def' => null],
            ['val' => 'modDt', 'typ' => 's', 'def' => null],
        ];
    }


    /**
     * 스타일별 데이터 (리뉴얼)
     * @return array[]
     */
    public static function tableWorkStyle(){
        return [
            ['val' => 'sno', 'typ' => 'i', 'def' => null],              //일련번호
            ['val' => 'season', 'typ' => 'i', 'def' => null],           //시즌
            ['val' => 'specCheckInfo', 'typ' => 's', 'def' => null],    //스펙 체크 데이터 (구분, 측정부위, 기준스펙-[슬림, 기본, 루즈] )
            ['val' => 'checkList', 'typ' => 's', 'def' => null],        //체크리스트 (피팅체크리스트)
            ['val' => 'styleName', 'typ' => 's', 'def' => null],        //시즌
            ['val' => 'regDt', 'typ' => 's', 'def' => null],
            ['val' => 'modDt', 'typ' => 's', 'def' => null],
        ];
    }

    /**
     * 스타일별 데이터 (추가될 수 있음)
     * @return array[]
     */
    public static function tableWorkStyleData(){
        return [
            ['val' => 'sno', 'typ' => 'i', 'def' => null],          //일련번호
            ['val' => 'styleType', 'typ' => 'i', 'def' => null],   //스타일 (0. 춘추점퍼, 1. 동계...
            ['val' => 'specCheck', 'typ' => 's', 'def' => null],   //스펙 체크 데이터
            ['val' => 'guideSpec', 'typ' => 's', 'def' => null],   //기준 스펙 값
            ['val' => 'checkList', 'typ' => 's', 'def' => null],   //체크리스트
            ['val' => 'regDt', 'typ' => 's', 'def' => null],
            ['val' => 'modDt', 'typ' => 's', 'def' => null],
        ];
    }

    /**
     * POST 요청 히스토리
     * @return array[]
     */
    public static function tablePostReqHistory(){
        return [
            ['val' => 'sno', 'typ' => 'i', 'def' => null],      //일련번호
            ['val' => 'reqDiv', 'typ' => 's', 'def' => null],  //전송구분
            ['val' => 'reqUrl', 'typ' => 's', 'def' => null],  //요청URL
            ['val' => 'reqHeader', 'typ' => 's', 'def' => null],  //요청헤더
            ['val' => 'reqData', 'typ' => 's', 'def' => null],      //요청내용
            ['val' => 'resData', 'typ' => 's', 'def' => null],      //회신내용
            ['val' => 'resData2', 'typ' => 's', 'def' => null],      //회신내용
            ['val' => 'regDt', 'typ' => 's', 'def' => null],
            ['val' => 'modDt', 'typ' => 's', 'def' => null],
        ];
    }

    /**
     * 메일 발송이력
     * @return array[]
     */
    public static function tableSendMailHistory(){
        return [
            ['val' => 'sno', 'typ' => 'i', 'def' => null],          //일련번호
            ['val' => 'documentSno', 'typ' => 'i', 'def' => null],  //문서번호
            ['val' => 'mailReceiverName', 'typ' => 's', 'def' => null],  //메일수신자
            ['val' => 'sendEmail', 'typ' => 's', 'def' => null],  //수신 이메일
            ['val' => 'managerSno', 'typ' => 's', 'def' => null], //전송구분
            ['val' => 'regDt', 'typ' => 's', 'def' => null],
            ['val' => 'modDt', 'typ' => 's', 'def' => null],
        ];
    }

    /**
     * 수금 주문
     * @return array[]
     */
    public static function tableCollectOrder(){
        return [
            ['val' => 'sno', 'typ' => 'i', 'def' => null ,'name' => '일련번호'],
            ['val' => 'orderNo', 'typ' => 's', 'def' => null,'name' => '원주문번호'],
            ['val' => 'goodsNo', 'typ' => 'i', 'def' => null,'name' => '수금상품'],
            ['val' => 'collectOrderNo', 'typ' => 's', 'def' => null,'name' => '수금주문번호'],
            ['val' => 'reqPrice', 'typ' => 'i', 'def' => null,'name' => '결제요청금액'],
            ['val' => 'receiptKind', 'typ' => 's', 'def' => null,'name' => '영수형태'],
            ['val' => 'paymentSubject', 'typ' => 's', 'def' => null,'name' => '결제제목'],
            ['val' => 'delFl', 'typ' => 's', 'def' => 'n','name' => '삭제여부'],
            ['val' => 'regDt', 'typ' => 's', 'def' => null,'name' => '등록일'],
        ];
    }

    /**
     * 품절상품 요청 리스트
     * @return array[]
     */
    public static function tableSoldOutReqList(){
        // @formatter:off
        $arrField = [
            ['val' => 'sno', 'typ' => 'i', 'def' => null],               // 일련번호
            ['val' => 'scmNo', 'typ' => 'i', 'def' => null],             // 공급사번호
            ['val' => 'reqName', 'typ' => 's', 'def' => null],           // 신청자명
            ['val' => 'cellPhone', 'typ' => 's', 'def' => null],           // 전화번호
            ['val' => 'memNo', 'typ' => 'i', 'def' => null],             // 회원번호
            ['val' => 'goodsNo', 'typ' => 'i', 'def' => null],           // 상품번호
            ['val' => 'deliveryName', 'typ' => 's', 'def' => null],       // 배송지점
            ['val' => 'deliveryCode', 'typ' => 's', 'def' => null],       // 배송지점코드
            ['val' => 'sendType', 'typ' => 'i', 'def' => 0],           // 전송타입
            ['val' => 'sendDt', 'typ' => 's', 'def' => null],           // 알림일자
            ['val' => 'regDt', 'typ' => 's', 'def' => null,'name' => '등록일'],
            ['val' => 'modDt', 'typ' => 's', 'def' => null,'name' => '수정일'],
        ];
        // @formatter:on
        return $arrField;
    }

    /**
     * 품절상품 요청 리스트
     * @return array[]
     */
    public static function tableSoldOutReqOptionList(){
        // @formatter:off
        $arrField = [
            ['val' => 'sno', 'typ' => 'i', 'def' => null],               // 일련번호
            ['val' => 'reqSno', 'typ' => 'i', 'def' => null],             // 공급사번호
            ['val' => 'optionSno', 'typ' => 's', 'def' => null],          // 옵션번호
            ['val' => 'optionInfo', 'typ' => 's', 'def' => null],         // 옵션정보
            ['val' => 'reqCnt', 'typ' => 'i', 'def' => null],             // 요청수량
            ['val' => 'sendType', 'typ' => 'i', 'def' => 0],           // 전송타입
            ['val' => 'sendDt', 'typ' => 's', 'def' => null],           // 알림일자
            ['val' => 'regDt', 'typ' => 's', 'def' => null,'name' => '등록일'],
            ['val' => 'modDt', 'typ' => 's', 'def' => null,'name' => '수정일'],
        ];
        // @formatter:on
        return $arrField;
    }

    /**
     * 3PL 상품 정보
     * @return array[]
     */
    public static function table3plProduct() {
        return [
            ['val' => 'sno', 'typ' => 'i', 'def' => null, 'name' => '일련번호'],
            ['val' => 'scmNo', 'typ' => 'i', 'def' => null, 'name' => '회원사 일련번호'],
            ['val' => 'scmName', 'typ' => 's', 'def' => null, 'name' => '회원사명'],
            ['val' => 'thirdPartyCode', 'typ' => 's', 'def' => null, 'name' => '3PL업체코드'],
            ['val' => 'thirdPartyProductCode', 'typ' => 's', 'def' => null, 'name' => '3PL상품코드'],
            ['val' => 'productName', 'typ' => 's', 'def' => null, 'name' => '상품명'],
            ['val' => 'optionName', 'typ' => 's', 'def' => null, 'name' => '옵션명'],
            ['val' => 'stockCnt', 'typ' => 'i', 'def' => null, 'name' => '재고수량'],
            ['val' => 'payedFl', 'typ' => 'i', 'def' => 'n', 'name' => '유료판매'],
            ['val' => 'workPayedFl', 'typ' => 'i', 'def' => 'y', 'name' => '작업비청구'],
            ['val' => 'attr1', 'typ' => 's', 'def' => null, 'name' => '구분'],
            ['val' => 'attr2', 'typ' => 's', 'def' => null, 'name' => '시즌'],
            ['val' => 'attr3', 'typ' => 's', 'def' => null, 'name' => '상품타입(카라티,바지..)'],
            ['val' => 'attr4', 'typ' => 's', 'def' => null, 'name' => '색상'],
            ['val' => 'attr5', 'typ' => 's', 'def' => null, 'name' => '년도'],
            ['val' => 'prdPrice', 'typ' => 'i', 'def' => null, 'name' => '상품정산금액'],
            ['val' => 'inCnt', 'typ' => 'i', 'def' => null, 'name' => '입고수량'],
            ['val' => 'outCnt', 'typ' => 'i', 'def' => null, 'name' => '출고수량'],
            ['val' => 'regDt', 'typ' => 's', 'def' => null,'name' => '등록일'],
            ['val' => 'modDt', 'typ' => 's', 'def' => null,'name' => '수정일'],
        ];
    }

    /**
     * 3PL 입출고기록
     * @return array[]
     */
    public static function table3plStockInOut() {
        return [
            ['val' => 'sno', 'typ' => 'i', 'def' => null, 'name' => '일련번호'],
            ['val' => 'productSno', 'typ' => 'i', 'def' => null, 'name' => '3PL상품번호'],
            ['val' => 'thirdPartyProductCode', 'typ' => 's', 'def' => null, 'name' => '3PL상품코드'],
            ['val' => 'inOutType', 'typ' => 'i', 'def' => null, 'name' => '입출고구분', 'code'=>SlCodeMap::STOCK_TYPE],
            ['val' => 'inOutReason', 'typ' => 's', 'def' => null, 'name' => '입출고사유'],
            ['val' => 'inOutDate', 'typ' => 's', 'def' => null, 'name' => '입/출고일자'],
            ['val' => 'closingDate', 'typ' => 's', 'def' => null, 'name' => '마감일자'],
            ['val' => 'closingSno', 'typ' => 'i', 'def' => null, 'name' => '마감일련번호'],
            ['val' => 'quantity', 'typ' => 'i', 'def' => null, 'name' => '수량'],
            ['val' => 'memo', 'typ' => 's', 'def' => null, 'name' => '메모'],
            ['val' => 'orderNo', 'typ' => 's', 'def' => null, 'name' => '주문번호'],
            ['val' => 'orderGoodsSno', 'typ' => 'i', 'def' => null, 'name' => '고도몰주문상품번호'],
            ['val' => 'orderDeliverySno', 'typ' => 'i', 'def' => null, 'name' => '배송번호'], //주문 롤백용
            ['val' => 'memNo', 'typ' => 'i', 'def' => null, 'name' => '회원번호'],
            ['val' => 'identificationText', 'typ' => 's', 'def' => null, 'name' => '입출고식별문자'],
            ['val' => 'invoiceNo', 'typ' => 's', 'def' => null, 'name' => '송장번호'],
            ['val' => 'managerSno', 'typ' => 'i', 'def' => null, 'name' => '등록자'],
            ['val' => 'customerName', 'typ' => 's', 'def' => null, 'name' => '고객명'],
            ['val' => 'address', 'typ' => 's', 'def' => null, 'name' => '주소'],
            ['val' => 'phone', 'typ' => 's', 'def' => null, 'name' => '전화번호'],
            ['val' => 'cellphone', 'typ' => 's', 'def' => null, 'name' => '핸드폰번호'],
            ['val' => 'payedFl', 'typ' => 'i', 'def' => 'n', 'name' => '유료판매'],
            ['val' => 'workPayedFl', 'typ' => 'i', 'def' => 'y', 'name' => '작업비청구'],
            ['val' => 'seq', 'typ' => 'i', 'def' => null, 'name' => '식별번호'],
            ['val' => 'regDt', 'typ' => 's', 'def' => null,'name' => '등록일'],
            ['val' => 'modDt', 'typ' => 's', 'def' => null,'name' => '수정일'],
        ];
    }
    public static function table3plStockInOutDelete() {
        $parentResult = self::table3plStockInOut();
        array_shift($parentResult);
        array_unshift($parentResult, ['val' => 'deleteSno', 'typ' => 'i', 'def' => null, 'name' => '삭제 일련번호']);
        return $parentResult;
    }

    /**
     * 주문 했으나 코드의 정보가 없는 주문 이력
     * @return array[]
     */
    public static function table3plOrderNotProduct() {
        return [
            ['val' => 'sno', 'typ' => 'i', 'def' => null, 'name' => '일련번호'],
            ['val' => 'thirdPartyProductCode', 'typ' => 's', 'def' => null, 'name' => '3PL상품코드'],
            ['val' => 'orderNo', 'typ' => 's', 'def' => null, 'name' => '주문번호'],
            ['val' => 'orderDeliverySno', 'typ' => 'i', 'def' => null, 'name' => '배송번호'], //주문 롤백용
            ['val' => 'regDt', 'typ' => 's', 'def' => null,'name' => '등록일'],
            ['val' => 'modDt', 'typ' => 's', 'def' => null,'name' => '수정일'],
        ];
    }

    /**
     * 마감 리스트
     * @return array[]
     */
    public static function table3plStockClosing(){
        return [
            ['val' => 'sno', 'typ' => 'i', 'def' => null],        // 일련번호
            ['val' => 'stockInQty', 'typ' => 'i', 'def' => null], // 입고수량
            ['val' => 'stockInMemo', 'typ' => 's', 'def' => null], // 입고내역
            ['val' => 'stockOutQty', 'typ' => 'i', 'def' => null], // 출고수량
            ['val' => 'stockOutMemo', 'typ' => 's', 'def' => null], // 출고내역
            ['val' => 'totalQty', 'typ' => 'i', 'def' => null], // 전체수량
            ['val' => 'totalMemo', 'typ' => 's', 'def' => null], // 전체내역
            ['val' => 'managerSno', 'typ' => 'i', 'def' => null, 'name' => '처리자'],
            ['val' => 'regDt', 'typ' => 's', 'def' => null,'name' => '등록일'],
            ['val' => 'modDt', 'typ' => 's', 'def' => null,'name' => '수정일'],
        ];
    }


    /**
     * 3PL Code 기준 판매상품 재고 수정 이력
     * @return array[]
     */
    public static function table3plSaleGoodsModifyHistory() {
        return [
            ['val' => 'sno', 'typ' => 'i', 'def' => null, 'name' => '일련번호'],
            ['val' => 'optionSno', 'typ' => 'i', 'def' => null, 'name' => '옵션번호'],
            ['val' => 'optionCode', 'typ' => 's', 'def' => null, 'name' => '3PL상품코드'],
            ['val' => 'optionValue1', 'typ' => 's', 'def' => null, 'name' => '옵션명'],
            ['val' => 'optionValue2', 'typ' => 's', 'def' => null, 'name' => '옵션명'],
            ['val' => 'optionValue3', 'typ' => 's', 'def' => null, 'name' => '옵션명'],
            ['val' => 'optionValue4', 'typ' => 's', 'def' => null, 'name' => '옵션명'],
            ['val' => 'optionValue5', 'typ' => 's', 'def' => null, 'name' => '옵션명'],
            ['val' => 'beforeStockCnt', 'typ' => 's', 'def' => null, 'name' => '이전 재고수량'],
            ['val' => 'afterStockCnt', 'typ' => 's', 'def' => null, 'name' => '이후 재고수량'],
            ['val' => 'managerSno', 'typ' => 's', 'def' => null, 'name' => '수정 관리자'],
            ['val' => 'regDt', 'typ' => 's', 'def' => null,'name' => '등록일'],
            ['val' => 'modDt', 'typ' => 's', 'def' => null,'name' => '수정일'],
        ];
    }

    /**
     * 3PL 주문
     * @return array
     */
    public static function table3plOrderTmp(){
        $arrField[]  = ['val' => 'sno', 'typ' => 'i', 'def' => null, 'name' => '일련번호'];
        $arrField[]  = ['val' => 'memo', 'typ' => 's', 'def' => null, 'name' => '메모(운송회사)'];
        $arrField[]  = ['val' => 'invoiceNo', 'typ' => 's', 'def' => null, 'name' => '송장번호'];
        $arrField[]  = ['val' => 'orderNo', 'typ' => 's', 'def' => null, 'name' => '주문번호'];
        $arrField[]  = ['val' => 'customerName', 'typ' => 's', 'def' => null, 'name' => '고객명'];
        $arrField[]  = ['val' => 'zipCode', 'typ' => 's', 'def' => null, 'name' => '우편번호'];
        $arrField[]  = ['val' => 'address', 'typ' => 's', 'def' => null, 'name' => '주소'];
        $arrField[]  = ['val' => 'phone', 'typ' => 's', 'def' => null, 'name' => '전화'];
        $arrField[]  = ['val' => 'mobile', 'typ' => 's', 'def' => null, 'name' => '핸드폰'];
        $arrField[]  = ['val' => 'productCode', 'typ' => 's', 'def' => null, 'name' => '제품코드'];
        $arrField[]  = ['val' => 'productName', 'typ' => 's', 'def' => null, 'name' => '제품명'];
        $arrField[]  = ['val' => 'qty', 'typ' => 'i', 'def' => null, 'name' => '수량'];
        $arrField[]  = ['val' => 'remark', 'typ' => 's', 'def' => null, 'name' => '비고'];
        $arrField[]  = ['val' => 'scmName', 'typ' => 's', 'def' => null, 'name' => '사이트'];
        $arrField[]  = ['val' => 'scmNo', 'typ' => 'i', 'def' => null, 'name' => '고객사번호'];
        $arrField[]  = ['val' => 'regDt', 'typ' => 's', 'def' => null,'name' => '등록일'];
        $arrField[]  = ['val' => 'modDt', 'typ' => 's', 'def' => null,'name' => '수정일'];
        return $arrField;
    }

    /**
     * 3PL 주문 이력
     * @return array
     */
    public static function table3plOrderHistory(){
        $arrField = self::table3plOrderTmp();
        $arrField[]  = ['val' => 'orderDt', 'typ' => 's', 'def' => null, 'name' => '출고일자'];
        return $arrField;
    }

    /**
     * 3PL 창고 반품 요청 리스트.
     * @return array[]
     */
    public static function table3plReturnList(){
        return [
            ['val' => 'sno', 'typ' => 'i', 'def' => null, 'name' => '일련번호'],
            ['val' => 'scmNo', 'typ' => 'i', 'def' => null, 'name' => '요청업체번호'],
            ['val' => 'scmName', 'typ' => 's', 'def' => null, 'name' => '요청업체'],
            ['val' => 'returnStatus', 'typ' => 'i', 'def' => 1, 'name' => '처리상태'], // 1: 접수, 2:접수확인, 3:회수완료
            ['val' => 'prdStatus', 'typ' => 'i', 'def' => 1, 'name' => '제품상태'], // 1: 확인, 2:최상, 3:양호, 4:상태불량
            ['val' => 'prdInfo', 'typ' => 's', 'def' => null, 'name' => '제품정보'],
            ['val' => 'totalQty', 'typ' => 'i', 'def' => null, 'name' => '총수량'],
            ['val' => 'customerName', 'typ' => 's', 'def' => null, 'name' => '고객명'],
            ['val' => 'address', 'typ' => 's', 'def' => null, 'name' => '주소'],
            ['val' => 'phone', 'typ' => 's', 'def' => null, 'name' => '전화'],
            ['val' => 'mobile', 'typ' => 's', 'def' => null, 'name' => '핸드폰'],
            ['val' => 'claimSno', 'typ' => 'i', 'def' => null, 'name' => '폐쇄몰 클레임번호'],
            ['val' => 'innoverMemo', 'typ' => 's', 'def' => null, 'name' => '이노버 메모'],
            ['val' => 'partnerMemo', 'typ' => 's', 'def' => null, 'name' => '파트너 메모'],
            ['val' => 'invoiceNo', 'typ' => 's', 'def' => null, 'name' => '원송장번호'],
            ['val' => 'returnDt', 'typ' => 's', 'def' => null, 'name' => '회수일자'],
            ['val' => 'regDt', 'typ' => 's', 'def' => null, 'name' => '등록시간'],
            ['val' => 'modDt', 'typ' => 's', 'def' => null, 'name' => '수정시간'],
        ];
    }

    /**
     * sl_3plInoviceRegHistory
     * 추후 메일 발송 여부등 ... 추가 가능성 있음
     * 3PL 송장 등록 이력
     * @return array[]
     */
    public static function table3plInoviceRegHistory(){
        return [
            ['val' => 'sno', 'typ' => 'i', 'def' => null, 'name' => '일련번호'],
            ['val' => 'outDate', 'typ' => 's', 'def' => null, 'name' => '출고일자'],
            ['val' => 'outCnt', 'typ' => 'i', 'def' => null, 'name' => '출고건수'],
            ['val' => 'outPrdQty', 'typ' => 'i', 'def' => null, 'name' => '출고건수'],
            ['val' => 'scmOutHistory', 'typ' => 's', 'def' => null, 'name' => '업체별 출고건수'],
            ['val' => 'regDt', 'typ' => 's', 'def' => null, 'name' => '등록시간'],
            ['val' => 'modDt', 'typ' => 's', 'def' => null, 'name' => '수정시간'],
        ];
    }

    /**
     * TKE 수기 주문
     * @return array[]
     */
    public static function tableTkeOrder() {
        return [
            ['val' => 'sno', 'typ' => 'i', 'def' => null, 'name' => '번호'],
            ['val' => 'memId', 'typ' => 's', 'def' => null, 'name' => '회원아이디'],
            ['val' => 'receiverName', 'typ' => 's', 'def' => null, 'name' => '주문번호'],
            ['val' => 'receiverPhone', 'typ' => 's', 'def' => null, 'name' => '전화번호'],
            ['val' => 'receiverCellPhone', 'typ' => 's', 'def' => null, 'name' => '휴대폰번호'],
            ['val' => 'receiverZipcode', 'typ' => 's', 'def' => null, 'name' => '우편번호'],
            ['val' => 'receiverAddress', 'typ' => 's', 'def' => null, 'name' => '주소'],
            ['val' => 'memo', 'typ' => 's', 'def' => null, 'name' => '배송메모'],
            ['val' => 'regDt', 'typ' => 's', 'def' => null, 'name' => '등록시간'],
            ['val' => 'modDt', 'typ' => 's', 'def' => null, 'name' => '수정시간'],
        ];
    }

    /**
     * TKE 수기 주문 상품
     * @return array[]
     */
    public static function tableTkeOrderGoods() {
        return [
            ['val' => 'sno', 'typ' => 'i', 'def' => null, 'name' => '번호'],
            ['val' => 'tkeOrderSno', 'typ' => 'i', 'def' => null, 'name' => 'TKE주문번호'],
            ['val' => 'goodsNo', 'typ' => 'i', 'def' => null, 'name' => '상품번호'],
            ['val' => 'optionName', 'typ' => 's', 'def' => null, 'name' => '옵션명'],
            ['val' => 'goodsCnt', 'typ' => 'i', 'def' => null, 'name' => '상품수량'],
            ['val' => 'regDt', 'typ' => 's', 'def' => null, 'name' => '등록시간'],
            ['val' => 'modDt', 'typ' => 's', 'def' => null, 'name' => '수정시간'],
        ];
    }


    public static function tableTkeMember() {
        return [
            ['val' => 'sno', 'typ' => 'i', 'def' => null, 'name' => '번호'],
            ['val' => 'memId', 'typ' => 's', 'def' => null, 'name' => 'tmp1'],
            ['val' => 'memNm', 'typ' => 's', 'def' => null, 'name' => 'tmp2'],
            ['val' => 'nickNm', 'typ' => 's', 'def' => null, 'name' => 'tmp3'],
            ['val' => 'cellPhone', 'typ' => 's', 'def' => null, 'name' => 'tmp4'],
            ['val' => 'zipcode', 'typ' => 's', 'def' => null, 'name' => 'tmp5'],
            ['val' => 'address', 'typ' => 's', 'def' => null, 'name' => 'tmp5'],
            ['val' => 'email', 'typ' => 's', 'def' => null, 'name' => 'tmp5'],
            ['val' => 'groupName', 'typ' => 's', 'def' => null, 'name' => 'tmp5'],
            ['val' => 'teamName', 'typ' => 's', 'def' => null, 'name' => 'tmp5'],
            ['val' => 'teamRep', 'typ' => 's', 'def' => null, 'name' => 'tmp5'],
            ['val' => 'buyLimitCount', 'typ' => 's', 'def' => null, 'name' => 'tmp5'],
            ['val' => 'memPw', 'typ' => 's', 'def' => null, 'name' => 'tmp2'],
            ['val' => 'regDt', 'typ' => 's', 'def' => null, 'name' => '등록시간'],
            ['val' => 'modDt', 'typ' => 's', 'def' => null, 'name' => '수정시간'],
        ];
    }

    /**
     * 휴일 정보
     * @return array[]
     */
    public static function tableHoliday(): array
    {
        return [
            ['val' => 'sno', 'typ' => 'i', 'def' => null],
            ['val' => 'dateKind', 'typ' => 'i', 'def' => null],
            ['val' => 'dateName', 'typ' => 's', 'def' => null],
            ['val' => 'isHoliday', 'typ' => 's', 'def' => null],
            ['val' => 'locdate', 'typ' => 's', 'def' => null],
            ['val' => 'seq', 'typ' => 'i', 'def' => null],
            ['val' => 'regDt', 'typ' => 's', 'def' => null],
            ['val' => 'modDt', 'typ' => 's', 'def' => null],
        ];
    }

    /**
     * 상품 집계/검색 속성
     * @return array[]
     */
    public static function tableGoodsFindAttribute(): array
    {
        return [
            ['val' => 'sno', 'typ' => 'i', 'def' => null],
            ['val' => 'goodsNo', 'typ' => 'i', 'def' => null],
            ['val' => 'goodsPart', 'typ' => 's', 'def' => null],
            ['val' => 'produceYear', 'typ' => 's', 'def' => null],
            ['val' => 'season', 'typ' => 's', 'def' => null],
            ['val' => 'goodsType', 'typ' => 's', 'def' => null],
            ['val' => 'regDt', 'typ' => 's', 'def' => null],
            ['val' => 'modDt', 'typ' => 's', 'def' => null],
        ];
    }


    public static function tableRecap(){
        $arrField[]  = ['val' => 'sno', 'typ' => 'i', 'def' => null, 'name' => '일련번호'];
        $arrField[]  = ['val' => 'isNew', 'typ' => 'i', 'def' => 0, 'name' => '신규/기존'];
        $arrField[]  = ['val' => 'customer', 'typ' => 's', 'def' => null, 'name' => '고객사'];
        $arrField[]  = ['val' => 'salesManager', 'typ' => 's', 'def' => null, 'name' => '영업 담당자'];
        $arrField[]  = ['val' => 'project', 'typ' => 's', 'def' => null, 'name' => '프로젝트'];
        $arrField[]  = ['val' => 'styleCode', 'typ' => 's', 'def' => null, 'name' => '스타일 품목 코드'];
        $arrField[]  = ['val' => 'salesStartDt', 'typ' => 's', 'def' => null, 'name' => '업무 시작'];
        $arrField[]  = ['val' => 'customerOrderDt', 'typ' => 's', 'def' => null, 'name' => '고객 발주일'];
        $arrField[]  = ['val' => 'customerDeliveryDt', 'typ' => 's', 'def' => null, 'name' => '고객 납기일'];
        $arrField[]  = ['val' => 'styleName', 'typ' => 's', 'def' => null, 'name' => '스타일'];
        $arrField[]  = ['val' => 'qty', 'typ' => 's', 'def' => null, 'name' => '수량'];
        $arrField[]  = ['val' => 'confirmed', 'typ' => 's', 'def' => null, 'name' => '확정'];
        $arrField[]  = ['val' => 'targetCost', 'typ' => 's', 'def' => null, 'name' => '타겟단가'];
        $arrField[]  = ['val' => 'estimateCost', 'typ' => 's', 'def' => null, 'name' => '가견적'];
        $arrField[]  = ['val' => 'produceCost', 'typ' => 's', 'def' => null, 'name' => '생산견적'];
        $arrField[]  = ['val' => 'margin', 'typ' => 's', 'def' => null, 'name' => '마진율'];
        $arrField[]  = ['val' => 'orderDeadLineDt', 'typ' => 's', 'def' => null, 'name' => '발주 D/L'];
        $arrField[]  = ['val' => 'bid', 'typ' => 's', 'def' => null, 'name' => '입찰'];
        $arrField[]  = ['val' => 'recommend', 'typ' => 's', 'def' => null, 'name' => '제안형태'];
        $arrField[]  = ['val' => 'recommendDt', 'typ' => 's', 'def' => null, 'name' => '제안 마감일'];
        $arrField[]  = ['val' => 'designManager', 'typ' => 's', 'def' => null, 'name' => '디자인 담당자'];
        $arrField[]  = ['val' => 'designStartDt', 'typ' => 's', 'def' => null, 'name' => '업무 시작'];
        $arrField[]  = ['val' => 'designEndDt', 'typ' => 's', 'def' => null, 'name' => '업무 마감'];
        $arrField[]  = ['val' => 'planDt', 'typ' => 's', 'def' => null, 'name' => '기획서'];
        $arrField[]  = ['val' => 'planEndDt', 'typ' => 's', 'def' => null, 'name' => '기획서'];
        $arrField[]  = ['val' => 'proposalDt', 'typ' => 's', 'def' => null, 'name' => '제안서'];
        $arrField[]  = ['val' => 'proposalEndDt', 'typ' => 's', 'def' => null, 'name' => '제안서'];
        $arrField[]  = ['val' => 'sampleStartDt', 'typ' => 's', 'def' => null, 'name' => '샘플 의뢰일'];
        $arrField[]  = ['val' => 'sampleEndDt', 'typ' => 's', 'def' => null, 'name' => '샘플 완료일'];
        $arrField[]  = ['val' => 'poDt', 'typ' => 's', 'def' => null, 'name' => '발주서'];
        $arrField[]  = ['val' => 'prdStartDt', 'typ' => 's', 'def' => null, 'name' => '업무 시작'];
        $arrField[]  = ['val' => 'prdEndDt', 'typ' => 's', 'def' => null, 'name' => '업무 마감'];
        $arrField[]  = ['val' => 'prdStartDt2', 'typ' => 's', 'def' => null, 'name' => '업무 시작'];
        $arrField[]  = ['val' => 'prdEndDt2', 'typ' => 's', 'def' => null, 'name' => '업무 마감'];
        $arrField[]  = ['val' => 'bt', 'typ' => 's', 'def' => null, 'name' => 'BT의뢰'];
        $arrField[]  = ['val' => 'similar', 'typ' => 's', 'def' => null, 'name' => '유사퀄리티'];
        $arrField[]  = ['val' => 'btStartDt', 'typ' => 's', 'def' => null, 'name' => 'BT의뢰 시작'];
        $arrField[]  = ['val' => 'btEndDt', 'typ' => 's', 'def' => null, 'name' => 'BT의뢰 종료'];
        $arrField[]  = ['val' => 'similarStartDt', 'typ' => 's', 'def' => null, 'name' => '유사퀄리티 시작'];
        $arrField[]  = ['val' => 'similarEndDt', 'typ' => 's', 'def' => null, 'name' => '유사퀄리티 종료'];
        $arrField[]  = ['val' => 'bluePrintStartDt', 'typ' => 's', 'def' => null, 'name' => '작지 시작'];
        $arrField[]  = ['val' => 'bluePrintEndDt', 'typ' => 's', 'def' => null, 'name' => '작지 종료'];
        $arrField[]  = ['val' => 'recapStatus', 'typ' => 'i', 'def' => 0, 'name' => 'recap상태'];
        $arrField[]  = ['val' => '3pl', 'typ' => 'i', 'def' => 0, 'name' => '3pl여부'];
        $arrField[]  = ['val' => 'orderConfirm', 'typ' => 'i', 'def' => 0, 'name' => '사양서 확정'];
        $arrField[]  = ['val' => 'memo', 'typ' => 's', 'def' => null, 'name' => '메모'];
        $arrField[]  = ['val' => 'regDt', 'typ' => 's', 'def' => null];
        $arrField[]  = ['val' => 'modDt', 'typ' => 's', 'def' => null];
        return $arrField;
    }

    public static function tableRecapFile(): array
    {
        return [
            ['val' => 'sno', 'typ' => 'i', 'def' => null, 'name' => '일련번호'],
            ['val' => 'projectSno', 'typ' => 'i', 'def' => null, 'name' => '프로젝트번호'],
            //영업
            ['val' => 'fileSales1', 'typ' => 's', 'def' => null, 'name' => '미팅준비'],
            ['val' => 'fileSales2', 'typ' => 's', 'def' => null, 'name' => '미팅보고서'],
            ['val' => 'fileSales3', 'typ' => 's', 'def' => null, 'name' => '견적서'],
            ['val' => 'fileSales4', 'typ' => 's', 'def' => null, 'name' => '계약서'],
            ['val' => 'fileSales5', 'typ' => 's', 'def' => null, 'name' => '영업확정서'],
            ['val' => 'memoSales1', 'typ' => 's', 'def' => null, 'name' => '미팅준비'],
            ['val' => 'memoSales2', 'typ' => 's', 'def' => null, 'name' => '미팅보고서'],
            ['val' => 'memoSales3', 'typ' => 's', 'def' => null, 'name' => '견적서'],
            ['val' => 'memoSales4', 'typ' => 's', 'def' => null, 'name' => '계약서'],
            ['val' => 'memoSales5', 'typ' => 's', 'def' => null, 'name' => '영업확정서'],

            //디자인
            ['val' => 'fileDesign1', 'typ' => 's', 'def' => null, 'name' => '기획서'],
            ['val' => 'fileDesign2', 'typ' => 's', 'def' => null, 'name' => '제안서'],
            ['val' => 'fileDesign3', 'typ' => 's', 'def' => null, 'name' => '샘플의뢰서'],
            ['val' => 'fileDesign4', 'typ' => 's', 'def' => null, 'name' => '샘플구매서'],
            ['val' => 'fileDesign5', 'typ' => 's', 'def' => null, 'name' => '샘플웨어링'],
            ['val' => 'fileDesign6', 'typ' => 's', 'def' => null, 'name' => '원부자재내역'],
            ['val' => 'fileDesign7', 'typ' => 's', 'def' => null, 'name' => '사양서'],
            ['val' => 'fileDesign8', 'typ' => 's', 'def' => null, 'name' => '작업지시서'],
            ['val' => 'memoDesign1', 'typ' => 's', 'def' => null, 'name' => '기획서'],
            ['val' => 'memoDesign2', 'typ' => 's', 'def' => null, 'name' => '제안서'],
            ['val' => 'memoDesign3', 'typ' => 's', 'def' => null, 'name' => '샘플의뢰서'],
            ['val' => 'memoDesign4', 'typ' => 's', 'def' => null, 'name' => '샘플구매서'],
            ['val' => 'memoDesign5', 'typ' => 's', 'def' => null, 'name' => '샘플웨어링'],
            ['val' => 'memoDesign6', 'typ' => 's', 'def' => null, 'name' => '원부자재내역'],
            ['val' => 'memoDesign7', 'typ' => 's', 'def' => null, 'name' => '사양서'],
            ['val' => 'memoDesign8', 'typ' => 's', 'def' => null, 'name' => '작업지시서'],

            //QC
            ['val' => 'fileQc1', 'typ' => 's', 'def' => null, 'name' => '가발주'],
            ['val' => 'fileQc2', 'typ' => 's', 'def' => null, 'name' => '생산단가확정'],
            ['val' => 'fileQc3', 'typ' => 's', 'def' => null, 'name' => '가견적요청서'],
            ['val' => 'fileQc4', 'typ' => 's', 'def' => null, 'name' => '가견적'],
            ['val' => 'fileQc5', 'typ' => 's', 'def' => null, 'name' => 'BT의뢰서'],
            ['val' => 'fileQc6', 'typ' => 's', 'def' => null, 'name' => 'BT제안서'],
            ['val' => 'memoQc1', 'typ' => 's', 'def' => null, 'name' => '가발주'],
            ['val' => 'memoQc2', 'typ' => 's', 'def' => null, 'name' => '생산단가확정'],
            ['val' => 'memoQc3', 'typ' => 's', 'def' => null, 'name' => '가견적요청서'],
            ['val' => 'memoQc4', 'typ' => 's', 'def' => null, 'name' => '가견적'],
            ['val' => 'memoQc5', 'typ' => 's', 'def' => null, 'name' => 'BT의뢰서'],
            ['val' => 'memoQc6', 'typ' => 's', 'def' => null, 'name' => 'BT제안서'],

            ['val' => 'memo', 'typ' => 's', 'def' => null, 'name' => '메모'],

            ['val' => 'regDt', 'typ' => 's', 'def' => null],
            ['val' => 'modDt', 'typ' => 's', 'def' => null],
        ];
    }

    public static function tableRecapProduce(): array
    {
        return [
            ['val' => 'sno', 'typ' => 'i', 'def' => null, 'name' => '일련번호'],
            ['val' => 'customer', 'typ' => 's', 'def' => null, 'name' => '고객사'],
            ['val' => 'project', 'typ' => 's', 'def' => null, 'name' => '프로젝트'],
            ['val' => 'styleCode', 'typ' => 's', 'def' => null, 'name' => '스타일 품목 코드'],
            ['val' => 'styleName', 'typ' => 's', 'def' => null, 'name' => '스타일 품목'],
            ['val' => 'innoverOrderDt', 'typ' => 's', 'def' => null, 'name' => '이노버 발주일'],
            ['val' => 'customerDeliveryDt', 'typ' => 's', 'def' => null, 'name' => '고객 납기일'],
            ['val' => 'qty', 'typ' => 's', 'def' => null, 'name' => '수량'],
            ['val' => 'salePrice', 'typ' => 's', 'def' => null, 'name' => '판매가'],
            ['val' => 'prdPrice', 'typ' => 's', 'def' => null, 'name' => '생산가'],
            ['val' => 'confirmed', 'typ' => 's', 'def' => null, 'name' => '스케쥴 확정'],
            ['val' => 'stepDt10', 'typ' => 's', 'def' => null, 'name' => '세탁 및 이화학검사ⓒ'],
            ['val' => 'stepDt20', 'typ' => 's', 'def' => null, 'name' => '원부자재 확정ⓒ'],
            ['val' => 'stepDt30', 'typ' => 's', 'def' => null, 'name' => '원부자재 선적'],
            ['val' => 'stepDt40', 'typ' => 's', 'def' => null, 'name' => 'QCⓒ'],
            ['val' => 'stepDt50', 'typ' => 's', 'def' => null, 'name' => '인라인ⓒ'],
            ['val' => 'stepDt60', 'typ' => 's', 'def' => null, 'name' => '선적'],
            ['val' => 'stepDt70', 'typ' => 's', 'def' => null, 'name' => '도착'],
            ['val' => 'stepDt80', 'typ' => 's', 'def' => null, 'name' => '입고 제품 검수ⓒ'],
            ['val' => 'stepDt90', 'typ' => 's', 'def' => null, 'name' => '공장납기'],

            ['val' => 'stepDt10End', 'typ' => 's', 'def' => null, 'name' => '세탁 및 이화학검사ⓒ'],
            ['val' => 'stepDt20End', 'typ' => 's', 'def' => null, 'name' => '원부자재 확정ⓒ'],
            ['val' => 'stepDt30End', 'typ' => 's', 'def' => null, 'name' => '원부자재 선적'],
            ['val' => 'stepDt40End', 'typ' => 's', 'def' => null, 'name' => 'QCⓒ'],
            ['val' => 'stepDt50End', 'typ' => 's', 'def' => null, 'name' => '인라인ⓒ'],
            ['val' => 'stepDt60End', 'typ' => 's', 'def' => null, 'name' => '선적'],
            ['val' => 'stepDt70End', 'typ' => 's', 'def' => null, 'name' => '도착'],
            ['val' => 'stepDt80End', 'typ' => 's', 'def' => null, 'name' => '입고 제품 검수ⓒ'],
            ['val' => 'stepDt90End', 'typ' => 's', 'def' => null, 'name' => '공장납기'],

            ['val' => 'etc', 'typ' => 's', 'def' => null, 'name' => '비고'],
            ['val' => 'recapStatus', 'typ' => 'i', 'def' => null, 'name' => 'recap상태'],
            ['val' => 'regDt', 'typ' => 's', 'def' => null, 'name' => '등록시간'],
            ['val' => 'modDt', 'typ' => 's', 'def' => null, 'name' => '수정시간'],
        ];
    }

    public static function tableRecapParCommon(): array
    {
        return [
            ['val' => 'sno', 'typ' => 'i', 'def' => null, 'name' => '일련번호'],
            ['val' => 'projectSno', 'typ' => 'i', 'def' => null, 'name' => '프로젝트번호'],
            ['val' => 'reqDt', 'typ' => 's', 'def' => null, 'name' => '의뢰일'],
            ['val' => 'completeDt', 'typ' => 's', 'def' => null, 'name' => '완료일'],
            ['val' => 'isAccept', 'typ' => 's', 'def' => null, 'name' => '승인여부'], //가견적
            ['val' => 'etc', 'typ' => 's', 'def' => null, 'name' => '비고'],
            ['val' => 'regDt', 'typ' => 's', 'def' => null, 'name' => '등록시간'],
            ['val' => 'modDt', 'typ' => 's', 'def' => null, 'name' => '수정시간'],
        ];
    }


    //생산단가확정
    public static function tableRecapPrdCost(): array
    {
        $arrField = DBTableField::tableRecapParCommon();
        $arrField[]  = ['val' => 'customerConfirmCost', 'typ' => 's', 'def' => null, 'name' => '고객사확정견적'];
        $arrField[]  = ['val' => 'confirmCost', 'typ' => 's', 'def' => null, 'name' => '확정견적'];
        return $arrField;
    }

    //가견적
    public static function tableRecapFakeEstimate(): array
    {
        $arrField = DBTableField::tableRecapParCommon();
        return $arrField;
    }
    //가발주
    public static function tableRecapFakeOrder(): array
    {
        $arrField = DBTableField::tableRecapParCommon();
        return $arrField;
    }
    //작지사양서
    public static function tableRecapWork(): array
    {
        $arrField = DBTableField::tableRecapParCommon();
        return $arrField;
    }

    //BT
    public static function tableRecapBt(): array
    {
        $arrField = DBTableField::tableRecapParCommon();
        $arrField[]  = ['val' => 'sendType', 'typ' => 's', 'def' => null, 'name' => '발송형태'];
        $arrField[]  = ['val' => 'sendInfo', 'typ' => 's', 'def' => null, 'name' => '발송정보'];
        return $arrField;
    }

    public static function tableRecapPrdFile(): array
    {
        return [
            ['val' => 'sno', 'typ' => 'i', 'def' => null, 'name' => '일련번호'],
            ['val' => 'projectSno', 'typ' => 'i', 'def' => null, 'name' => '프로젝트번호'],
            //영업
            ['val' => 'filePrd1', 'typ' => 's', 'def' => null, 'name' => '세탁및이화학검사'],
            ['val' => 'filePrd2', 'typ' => 's', 'def' => null, 'name' => '원부자재확정'],
            ['val' => 'filePrd3', 'typ' => 's', 'def' => null, 'name' => 'QC'],
            ['val' => 'filePrd4', 'typ' => 's', 'def' => null, 'name' => '인라인'],
            ['val' => 'filePrd5', 'typ' => 's', 'def' => null, 'name' => '선적'],
            ['val' => 'filePrd6', 'typ' => 's', 'def' => null, 'name' => '입고제품검수'],
            ['val' => 'filePrd7', 'typ' => 's', 'def' => null, 'name' => '고객납기'],
            ['val' => 'memoPrd1', 'typ' => 's', 'def' => null, 'name' => '메모1'],
            ['val' => 'memoPrd2', 'typ' => 's', 'def' => null, 'name' => '메모2'],
            ['val' => 'memoPrd3', 'typ' => 's', 'def' => null, 'name' => '메모3'],
            ['val' => 'memoPrd4', 'typ' => 's', 'def' => null, 'name' => '메모4'],
            ['val' => 'memoPrd5', 'typ' => 's', 'def' => null, 'name' => '메모5'],
            ['val' => 'memoPrd6', 'typ' => 's', 'def' => null, 'name' => '메모6'],
            ['val' => 'memoPrd7', 'typ' => 's', 'def' => null, 'name' => '메모7'],
            ['val' => 'memo', 'typ' => 's', 'def' => null, 'name' => '메모'],
            ['val' => 'regDt', 'typ' => 's', 'def' => null],
            ['val' => 'modDt', 'typ' => 's', 'def' => null],
        ];
    }


    /**
     * 프로젝트 코멘트
     * @return array[]
     */
    public static function tableOrderSelectedDeliveryName(): array
    {
        return [
            ['val' => 'sno', 'typ' => 'i', 'def' => null],
            ['val' => 'orderNo', 'typ' => 's', 'def' => null],
            ['val' => 'orderDeliveryName', 'typ' => 's', 'def' => null],
            ['val' => 'regDt', 'typ' => 's', 'def' => null],
            ['val' => 'modDt', 'typ' => 's', 'def' => null],
        ];
    }

    /**
     * 주문 변경 로그
     * @return array[]
     */
    public static function tableOrderChangeHistory(): array
    {
        return [
            ['val' => 'sno', 'typ' => 'i', 'def' => null],              //일련번호
            ['val' => 'orderNo', 'typ' => 's', 'def' => null],       //주문번호
            ['val' => 'beforeOrder', 'typ' => 's', 'def' => null],   //이전 주문 데이터
            ['val' => 'beforeGoods', 'typ' => 's', 'def' => null],   //이전 주문 상품 데이터
            ['val' => 'afterOrder', 'typ' => 's', 'def' => null],     //이후 주문 데이터
            ['val' => 'afterGoods', 'typ' => 's', 'def' => null],     //이후 주문 상품 데이터
            ['val' => 'logComment', 'typ' => 's', 'def' => null],     //로그 코멘트
            ['val' => 'regDt', 'typ' => 's', 'def' => null],
            ['val' => 'modDt', 'typ' => 's', 'def' => null],
        ];
    }

    /**
     * 주문 변경 로그
     * @return array[]
     */
    public static function table3plGolfInvoiceHistory(): array
    {
        return [
            ['val' => 'sno', 'typ' => 'i', 'def' => null],
            ['val' => 'orderCnt', 'typ' => 'i', 'def' => null],
            ['val' => 'goodsCnt', 'typ' => 'i', 'def' => null],
            ['val' => 'regDt', 'typ' => 's', 'def' => null],
            ['val' => 'modDt', 'typ' => 's', 'def' => null],
        ];
    }


    /**
     * 현대 엘리베이터 수기 주문
     * @return array[]
     */
    public static function tableHyundaeOrder() {
        return [
            ['val' => 'sno', 'typ' => 'i', 'def' => null, 'name' => '일련번호'],
            ['val' => 'deliveryNo', 'typ' => 'i', 'def' => null, 'name' => '배송번호'],

            ['val' => 'dept1', 'typ' => 's', 'def' => null, 'name' => '부서1'],
            ['val' => 'dept2', 'typ' => 's', 'def' => null, 'name' => '부서2'],
            ['val' => 'pos', 'typ' => 's', 'def' => null, 'name' => '직급'],

            ['val' => 'memId', 'typ' => 's', 'def' => null, 'name' => '사번'],
            ['val' => 'memNm', 'typ' => 's', 'def' => null, 'name' => '이름'],
            ['val' => 'receiver', 'typ' => 's', 'def' => null, 'name' => '수령자'],
            ['val' => 'address', 'typ' => 's', 'def' => null, 'name' => '주소'],
            ['val' => 'cellPhone', 'typ' => 's', 'def' => null, 'name' => '연락처'],

            ['val' => 'goods1', 'typ' => 's', 'def' => null, 'name' => '상의'],
            ['val' => 'goods1Cnt', 'typ' => 'i', 'def' => null, 'name' => '상의'],
            ['val' => 'goods2', 'typ' => 's', 'def' => null, 'name' => '조끼'],
            ['val' => 'goods2Cnt', 'typ' => 'i', 'def' => null, 'name' => '조끼'],
            ['val' => 'goods3', 'typ' => 's', 'def' => null, 'name' => '하의'],
            ['val' => 'goods3Cnt', 'typ' => 'i', 'def' => null, 'name' => '하의'],

            ['val' => 'regDt', 'typ' => 's', 'def' => null,'name' => '등록일'],
            ['val' => 'modDt', 'typ' => 's', 'def' => null,'name' => '수정일'],
        ];
    }

    /**
     * 아시아나 상품
     * @return array[]
     */
    public static function tableAsianaItem(): array
    {
        return [
            ['val' => 'sno', 'typ' => 'i', 'def' => null, 'name' => '번호'],
            ['val' => 'prdName', 'typ' => 's', 'def' => null, 'name' => '품명'],
            ['val' => 'prdOption', 'typ' => 's', 'def' => null, 'name' => '규격(옵션)'],
            ['val' => 'prdCode', 'typ' => 's', 'def' => null, 'name' => '상품코드'],
            ['val' => 'cate1', 'typ' => 's', 'def' => null, 'name' => '카테고리1'],
            ['val' => 'cate2', 'typ' => 's', 'def' => null, 'name' => '카테고리2'],
            ['val' => 'cateCd', 'typ' => 's', 'def' => null, 'name' => '카테고리코드(폐쇄몰)'],
            ['val' => 'goodsNo', 'typ' => 'i', 'def' => null, 'name' => '상품번호'],
            ['val' => 'optionSno', 'typ' => 'i', 'def' => null, 'name' => '옵션번호'],
            ['val' => 'price', 'typ' => 'i', 'def' => null, 'name' => '판매가(부가세제외)'],
            ['val' => 'initStock', 'typ' => 'i', 'def' => null, 'name' => '초기수량'],
            ['val' => 'prvCnt', 'typ' => 'i', 'def' => null, 'name' => '연별 제공수량'],
            ['val' => 'regDt', 'typ' => 's', 'def' => null, 'name' => '등록일'],
            ['val' => 'modDt', 'typ' => 's', 'def' => null, 'name' => '수정일']
        ];
    }

    /**
     * 아시아나 사원리스트 + 지급이력
     * @return array[]
     */
    public static function tableAsianaEmployee(): array
    {
        return [
            ['val' => 'sno', 'typ' => 'i', 'def' => null, 'name' => '번호'],
            ['val' => 'companyId', 'typ' => 's', 'def' => null, 'name' => '사번'],
            ['val' => 'empName', 'typ' => 's', 'def' => null, 'name' => '이름'],
            ['val' => 'empRank', 'typ' => 's', 'def' => null, 'name' => '직급'],
            ['val' => 'empTeam', 'typ' => 's', 'def' => null, 'name' => '팀명'],
            ['val' => 'empPart1', 'typ' => 's', 'def' => null, 'name' => '파트명'],
            ['val' => 'empPart2', 'typ' => 's', 'def' => null, 'name' => '소부문명'],
            ['val' => 'provideInfo', 'typ' => 's', 'def' => null, 'name' => '제공내역'],
            ['val' => 'retiredFl', 'typ' => 's', 'def' => 'n', 'name' => '퇴사자'],
            ['val' => 'regDt', 'typ' => 's', 'def' => null, 'name' => '등록시간'],
            ['val' => 'modDt', 'typ' => 's', 'def' => null, 'name' => '수정시간']
        ];
    }

    /**
     * 아시아나 카트
     * @return array[]
     */
    public static function tableAsianaCart(): array
    {
        return [
            ['val' => 'sno', 'typ' => 'i', 'def' => null, 'name' => '번호'],
            ['val' => 'memNo', 'typ' => 'i', 'def' => null, 'name' => '주문자번호'],
            ['val' => 'companyId', 'typ' => 's', 'def' => null, 'name' => '사번'],
            ['val' => 'name', 'typ' => 's', 'def' => null, 'name' => '이름'],
            ['val' => 'optionSno', 'typ' => 'i', 'def' => null, 'name' => '상품코드'],
            ['val' => 'orderCnt', 'typ' => 'i', 'def' => null, 'name' => '수량'],
            ['val' => 'isValid', 'typ' => 'i', 'def' => null, 'name' => '오류체크'], //0 정상, 1 오류 (나중에는 코드로써 활용)
            ['val' => 'isValidMsg', 'typ' => 's', 'def' => null, 'name' => '오류체크 내용'],
            ['val' => 'regDt', 'typ' => 's', 'def' => null, 'name' => '등록시간'],
            ['val' => 'modDt', 'typ' => 's', 'def' => null, 'name' => '수정시간']
        ];
    }

    /**
     * 아시아나 주문이력
     * @return array[]
     */
    public static function tableAsianaOrderHistory(): array
    {
        return [
            ['val' => 'sno', 'typ' => 'i', 'def' => null, 'name' => '번호'],
            ['val' => 'empTeam', 'typ' => 's', 'def' => null, 'name' => '팀명'],
            ['val' => 'empPart1', 'typ' => 's', 'def' => null, 'name' => '파트명'],
            ['val' => 'empPart2', 'typ' => 's', 'def' => null, 'name' => '소부문명'],
            ['val' => 'companyId', 'typ' => 's', 'def' => null, 'name' => '사번'],
            ['val' => 'name', 'typ' => 's', 'def' => null, 'name' => '성명'],
            ['val' => 'requestDt', 'typ' => 's', 'def' => null, 'name' => '신청날짜'],
            ['val' => 'prdName', 'typ' => 's', 'def' => null, 'name' => '품목'],
            ['val' => 'prdOption', 'typ' => 's', 'def' => null, 'name' => '옵션'],
            ['val' => 'orderCnt', 'typ' => 'i', 'def' => null, 'name' => '신청수량'],

            ['val' => 'orderGoodsSno', 'typ' => 'i', 'def' => null, 'name' => '고도몰주문정보'],
            ['val' => 'optionSno', 'typ' => 'i', 'def' => null, 'name' => '고도몰옵션번호'],
            ['val' => 'optionInfo', 'typ' => 's', 'def' => null, 'name' => '고도몰옵션정보'], //비상용 복구용

            ['val' => 'delFl', 'typ' => 's', 'def' => 'n', 'name' => '취소여부'],

            ['val' => 'regDt', 'typ' => 's', 'def' => null, 'name' => '등록시간'],
            ['val' => 'modDt', 'typ' => 's', 'def' => null, 'name' => '수정시간']
        ];
    }


    /**
     * 삼영 입출고 이력
     * @return array
     */
    public static function tableSyInOut(): array
    {
        return [
            ['val' => 'sno', 'typ' => 'i', 'def' => null, 'name' => '일련번호'],
            ['val' => 'ioType', 'typ' => 's', 'def' => 'out', 'name' => '입/출고 타입'],
            ['val' => 'seq', 'typ' => 'i', 'def' => null, 'name' => '삼영 일련번호'],
            ['val' => 'slipNo', 'typ' => 's', 'def' => null, 'name' => '구분'],
            ['val' => 'slipNo1', 'typ' => 's', 'def' => null, 'name' => '구분1'],
            ['val' => 'slipNo2', 'typ' => 's', 'def' => null, 'name' => '구분2'],
            ['val' => 'slipNo3', 'typ' => 's', 'def' => null, 'name' => '구분3'],
            ['val' => 'inOutDt', 'typ' => 's', 'def' => null, 'name' => '입출고일자'],
            ['val' => 'inOutDts', 'typ' => 's', 'def' => null, 'name' => '입출고일자2'],
            ['val' => 'code', 'typ' => 's', 'def' => null, 'name' => '코드'],
            ['val' => 'name', 'typ' => 's', 'def' => null, 'name' => '상품명'],
            ['val' => 'prdOption', 'typ' => 's', 'def' => null, 'name' => '옵션'],
            ['val' => 'qty', 'typ' => 'i', 'def' => null, 'name' => '수량'],
            ['val' => 'custName', 'typ' => 's', 'def' => null, 'name' => '수령자'],
            ['val' => 'invoiceNo', 'typ' => 's', 'def' => null, 'name' => '송장번호'],
            ['val' => 'zipCode', 'typ' => 's', 'def' => null, 'name' => '우편번호'],
            ['val' => 'address', 'typ' => 's', 'def' => null, 'name' => '주소'],
            ['val' => 'telNo', 'typ' => 's', 'def' => null, 'name' => '전화번호'],
            ['val' => 'phoneNo', 'typ' => 's', 'def' => null, 'name' => '휴대폰번호'],
            ['val' => 'orderNo', 'typ' => 's', 'def' => null, 'name' => '주문번호'],
            ['val' => 'scmName', 'typ' => 's', 'def' => null, 'name' => '고객사명'],
            ['val' => 'remark', 'typ' => 's', 'def' => null, 'name' => '기타/비고'],
            ['val' => 'memo', 'typ' => 's', 'def' => null, 'name' => '메모'],
            ['val' => 'sysDate', 'typ' => 's', 'def' => null, 'name' => '입출고등록일자'],
            ['val' => 'sysTime', 'typ' => 's', 'def' => null, 'name' => '입출고등록시간'],
            ['val' => 'regDt', 'typ' => 's', 'def' => null, 'name' => '등록시간'],
            ['val' => 'modDt', 'typ' => 's', 'def' => null, 'name' => '수정시간']
        ];
    }

    /**
     * 재고관리 코멘트
     * @return array[]
     */
    public static function tableStockReportComment(): array
    {
        return [
            ['val' => 'sno', 'typ' => 'i', 'def' => null, 'name' => '일련번호'],
            ['val' => 'scmNo', 'typ' => 'i', 'def' => null, 'name' => '업체번호', 'required' => true ],
            ['val' => 'comment', 'typ' => 's', 'def' => null, 'name' => '관리코멘트','required' => true],
            ['val' => 'regManagerSno', 'typ' => 'i', 'def' => null, 'name' => '등록자'],
            ['val' => 'regDt', 'typ' => 's', 'def' => null, 'name' => '등록시간'],
            ['val' => 'modDt', 'typ' => 's', 'def' => null, 'name' => '수정시간']
        ];
    }

    /*public static function tableMember(){
        $arrField = parent::tableMember();
        $arrField[]  = ['val' => 'freeFl', 'typ' => 'i', 'def' => 'n','name'=>'무료회원여부'];
        $arrField[]  = ['val' => 'hankookType', 'typ' => 'i', 'def' => 'n','name'=>'한국타이어 매장 유형'];
        return $arrField;
    }*/

}
