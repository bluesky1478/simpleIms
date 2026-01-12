<?php
namespace SiteLabUtil;

use Component\Database\DBTableField;
use Component\Ims\ImsCodeMap;
use Component\Ims\ImsJsonSchema;
use Component\Work\WorkCodeMap;
use SlComponent\Database\DBUtil;
use SlComponent\Database\DBUtil2;
use SlComponent\Database\SearchVo;
use SlComponent\Database\TableVo;
use SlComponent\Util\SitelabLogger;
use SlComponent\Util\SlCodeMap;
use SlComponent\Util\SlPostRequestUtil;
use DateTime;
use ReflectionClass;
use ReflectionException;

class SlCommonUtil {
    /**
     * 등록일 업데이트
     * @param $tableName
     * @param $regDt
     * @param $conditionStr
     */
    public static function setRegDtUpdate($tableName, $regDt, $conditionStr){
        DBUtil2::runSql("update {$tableName} set regDt='{$regDt}' where {$conditionStr}");//RegDt Update
    }

    /**
     * 기본 JSON 값 설정
     * @param $data
     * @param array $scheme
     * @return array
     */
    public static function setJsonField($data, array $scheme) {
        $refineData = [];
        foreach($scheme as $key => $value){
            $refineData[$key] = empty($data[$key])?$value:$data[$key]; //없으면 기본값 .
        }
        return $refineData;
    }

    /**
     * 문자열에서 숫자만 추출
     * @param $string
     * @return string
     */
    public static function extractNumbers($string) {
        // 정규 표현식을 사용하여 문자열에서 숫자만 추출
        preg_match_all('/\d+/', $string, $matches);
        // 추출된 숫자들을 하나의 문자열로 합침
        return implode('', $matches[0]);
    }

    public static function isCancel($status){
        //반품은 추후
        if( 'c' === substr($status,0,1) || 'e5' === $status || 'r3' === $status ){
            return true;
        }else{
            return false;
        }
    }

    /**
     * 주문 상태 반환
     * @param $orderStatus
     * @return array
     */
    public static function getOrderStatusName($orderStatus){
        return self::getOrderStatusAllMap_momotee()[$orderStatus];
    }
    public static function getOrderStatusAllMap_momotee($includeStatus = ''){
        $orderPolicy = gd_policy('order.status');
        $realOrderStatusMap = array();
        foreach($orderPolicy as $key1 => $each){
            if( is_array($each) ) {
                foreach ($each as $orderStatusKey => $orderStatus) {
                    if (is_array($orderStatus)) {
                        if (!empty($includeStatus)) {
                            if (strpos($includeStatus, substr($orderStatusKey, 0, 1)) !== false) {
                                if ('y' === $orderStatus['useFl']) {
                                    $realOrderStatusMap[$orderStatusKey] = $orderStatus['user'];
                                }
                            }
                        } else {
                            $realOrderStatusMap[$orderStatusKey] = $orderStatus['user'];
                        }
                    }
                }
            }
        }
        return $realOrderStatusMap;
    }

    /**
     * 주문 상태 반환
     * @param $orderStatus
     * @return array
     */
    public static function getOrderStatusName2($orderStatus){
        return SlCommonUtil::getOrderStatusAllMap2()[$orderStatus];
    }
    public static function getOrderStatusAllMap2($includeStatus = ''){
        $orderPolicy = gd_policy('order.status');
        $realOrderStatusMap = array();
        foreach($orderPolicy as $key1 => $each){
            if( is_array($each) ) {
                foreach ($each as $orderStatusKey => $orderStatus) {
                    if (is_array($orderStatus)) {
                        if (!empty($includeStatus)) {
                            if (strpos($includeStatus, substr($orderStatusKey, 0, 1)) !== false) {
                                if ('y' === $orderStatus['useFl']) {
                                    $realOrderStatusMap[$orderStatusKey] = $orderStatus['user'];
                                }
                            }
                        } else {
                            $realOrderStatusMap[$orderStatusKey] = $orderStatus['user'];
                        }
                    }
                }
            }
        }
        return $realOrderStatusMap;
    }


    /**
     * 전체 주문 상태 반환
     * @return array
     */
    public static function getOrderStatusAllMap(){
        $order = \App::load('\\Component\\Order\\OrderAdmin');
        $realOrderStatusMap = $order->getOrderStatusList();
        $realOrderStatusMap = array_merge($realOrderStatusMap,$order->getOrderStatusList('o'));
        $realOrderStatusMap = array_merge($realOrderStatusMap,$order->getOrderStatusList('f'));
        $realOrderStatusMap = array_merge($realOrderStatusMap,$order->getOrderStatusList('p'));
        $realOrderStatusMap = array_merge($realOrderStatusMap,$order->getOrderStatusList('g'));
        $realOrderStatusMap = array_merge($realOrderStatusMap,$order->getOrderStatusList('d'));
        $realOrderStatusMap = array_merge($realOrderStatusMap,$order->getOrderStatusList('s'));
        $realOrderStatusMap = array_merge($realOrderStatusMap,$order->getOrderStatusList('c'));
        $realOrderStatusMap = array_merge($realOrderStatusMap,$order->getOrderStatusList('r'));
        $realOrderStatusMap = array_merge($realOrderStatusMap,$order->getOrderStatusList('e'));
        $realOrderStatusMap = array_merge($realOrderStatusMap,$order->getOrderStatusList('b'));
        /*'r'=>'3'    //환불로 인한 입고
        , 'e'=>'4'  //교환으로 인한 입고
        , 'c'=>'8'  //취소로 인한 입고
        , 'b'=>'9'  //반품으로 인한 입고*/

        $realOrderStatusMap['f1'] = '결제시도';
        return $realOrderStatusMap;
    }

    public static function a(){

    }

    /**
     * 조회 데이터 엑셀 다운로드 값으로 정제
     * TODO : 1만 로우 이상 다운로드 관리자 문의 (성능에 문제될 수 있음) 기능 추가
     * @param $getValue
     * @return mixed
     */
    public static function getRefineValueAndExcelDownCheck($getValue){
        if(  !empty($getValue['simple_excel_download'])  ){
            $getValue['pageNum'] = 20000;
            $getValue['page'] = 1;
        }
        return $getValue;
    }

    /**
     * 현재시간 반환
     * @return false|string
     */
    public static function getNow(){
        return date("Y-m-d H:i:s");
    }

    /**
     * 현재 날짜 반환
     * @return false|string
     */
    public static function getNowDate(){
        return date("Y-m-d");
    }

    /**
     * 상품 옵션 정제해서 가져오기
     * @param $optionInfo
     * @return string
     */
    public static function getRefineOrderGoodsOption($optionInfo){
        $optionData = json_decode(gd_htmlspecialchars_stripslashes($optionInfo), true);
        $optionArray = array();
        $optionName = "";
        if( !empty( $optionData )) {
            foreach ($optionData as $val) {
                $optionArray[] = $val[1];
            }
            $optionName = implode('/', $optionArray);
        }
        return $optionName;
    }

    /**
     * 상품별 사유
     * @param $goodsNo
     * @return array
     */
    public static function getClaimReasonByGoodsNo($goodsNo, $isAdmin = false){
        $defaultReasonList = [];
        $addReasonList = [];

        if( $isAdmin ){
            $claimReasonList = SlCodeMap::ADMIN_CLAIM_REASON;
        }else{
            $claimReasonList = SlCodeMap::CLAIM_REASON;
        }

        foreach ( $claimReasonList as $eachKey => $eachValue){
            if( 10 >= $eachKey ){
                $defaultReasonList[] = $eachValue;
            }else{
                $goodsData = DBUtil2::getOne(DB_GOODS, 'goodsNo', $goodsNo);
                $goodsAddReason = $goodsData['addReason'];
                $goodsAddReasonList = explode(',',$goodsAddReason);
                if( in_array($eachKey, $goodsAddReasonList) ){
                    $addReasonList[] = $eachValue;
                }
            }
        }
        return array_merge($addReasonList, $defaultReasonList);
    }

    public static function getClaimCodeReasonByGoodsNo($goodsNo, $isAdmin = false){
        $defaultReasonList = [];
        $addReasonList = [];

        if( $isAdmin ){
            $claimReasonList = SlCodeMap::ADMIN_CLAIM_REASON;
        }else{
            $claimReasonList = SlCodeMap::CLAIM_REASON;
        }

        foreach ( $claimReasonList as $eachKey => $eachValue){
            if( 10 >= $eachKey ){
                $defaultReasonList[$eachKey] = $eachValue;
            }else{
                $goodsData = DBUtil2::getOne(DB_GOODS, 'goodsNo', $goodsNo);
                $goodsAddReason = $goodsData['addReason'];
                $goodsAddReasonList = explode(',',$goodsAddReason);
                if( in_array($eachKey, $goodsAddReasonList) ){
                    $addReasonList[$eachKey] = $eachValue;
                }
            }
        }
        //return array_merge($addReasonList, $defaultReasonList);
        return $addReasonList + $defaultReasonList;
    }

    /**
     * 배열값을 지정하여 배열의 키로 사용
     * arrayAppKey ==> arrayAppointedKey
     * @param array $array
     * @param $key
     * @param bool $unSetKeyValue
     * @return array
     */
    public static function arrayAppKey(array $array, $key, $unSetKeyValue = false){
        $refineArray = array();
        foreach($array as $arrayValue){
            $keyValue = $arrayValue[$key];
            if( $unSetKeyValue ){
                unset($arrayValue[$key]);
            }
            $refineArray[$keyValue] = $arrayValue;
        }
        return $refineArray;
    }

    /**
     * 2차원 배열에서 Key Value를 지정하여 1차원으로 만든다.
     * @param array $array
     * @param $key
     * @param $valueField
     * @return array
     */
    public static function arrayAppKeyValue(array $array, $key, $valueField ){
        $refineArray = array();
        foreach($array as $arrayValue){
            if(!empty($valueField)){
                $refineArray[$arrayValue[$key]] = $arrayValue[$valueField];
            }else{
                $refineArray[$arrayValue[$key]] = '';
            }
        }
        return $refineArray;
    }

    /**
     * 배열의 특정 value만 지정
     * @param array $array
     * @param $valueField
     * @return array
     */
    public static function arrayAppointedValue(array $array, $valueField ){
        $refineArray = array();
        foreach($array as $key => $arrayValue){
            $refineArray[$key] = $arrayValue[$valueField];
        }
        return $refineArray;
    }

    /**
     * 이미지 경로를 반환
     * @param array $value
     * @return string
     */
    public static function getImgSrc($value){
        $imgSrc = '';
        try {
            if( !empty($value['imageNm']) ){
                if( \Bundle\Component\Storage\Storage::disk(\Bundle\Component\Storage\Storage::PATH_CODE_ETC, $value['imageStorage'])->isFileExists($value['imagePath'].$value['imageNm']) ){
                    $imgSrc = \Bundle\Component\Storage\Storage::disk(\Bundle\Component\Storage\Storage::PATH_CODE_ETC,$value['imageStorage'])->getHttpPath($value['imagePath'].$value['imageNm']);
                }
            }
        }catch (\Exception $e){
            //gd_debug($e);
        }
        return $imgSrc;
    }

    /**
     * 주문번호 만드는 유틸 따온것
     * @return string
     */
    public static function generateTimeNo()
    {
        // 0 ~ 999 마이크로초 중 랜덤으로 sleep 처리 (동일 시간에 들어온 경우 중복을 막기 위해서.)
        usleep(mt_rand(0, 999));

        // 0 ~ 99 마이크로초 중 랜덤으로 sleep 처리 (첫번째 sleep 이 또 동일한 경우 중복을 막기 위해서.)
        usleep(mt_rand(0, 99));

        // microtime() 함수의 마이크로 초만 사용
        list($usec) = explode(' ', microtime());

        // 마이크로초을 4자리 정수로 만듬 (마이크로초 뒤 2자리는 거의 0이 나오므로 8자리가 아닌 4자리만 사용함 - 나머지 2자리도 짜름... 너무 길어서.)
        $tmpNo = sprintf('%04d', round($usec * 10000));

        // PREFIX_ORDER_NO (년월일시분초) 에 마이크로초 정수화 한 값을 붙여 주문번호로 사용함, 16자리 주문번호임
        return PREFIX_ORDER_NO . $tmpNo;
    }

    /**
     * 데이터 Trim
     * @param $targetData
     */
    public static function refineTrimData(&$targetData){
        if (isset($targetData)) {
            gd_trim($targetData);
        }
    }

    /**
     * 트랜잭션 처리
     * @param $service
     * @param $method
     * @param $param
     * @return mixed
     */
    public static function transactionMethod($service, $method, $param){
        $db = \App::getInstance('DB');
        $db->begin_tran();
        try{
            $result =  $service->$method($param);
            $db->commit();
            return $result;
        }catch(\Exception $e){
            SitelabLogger::error('트랜잭션 오류 발생');
            SitelabLogger::error($e);
            $db->rollback();
            return false;
        }
    }

    /**
     * 옵션명이 특수한 옵션인지 여부 확인
     * @param $str
     * @return bool
     */
    public static function isSpecialOption($str){
        $str = strtoupper($str);
        //정규식
        $pattern[] = '*XL*';
        $pattern[] = '*기모*';
        $pattern[] = '*긴팔*';
        $rslt = false;
        foreach($pattern as $p){
            if(preg_match($p,$str)){
                $rslt=true;
                break;
            }
        }
        return $rslt;
    }

    public static function jsonEncodeForJavaScript($data){
        return str_replace('\\','\\\\',json_encode($data, JSON_UNESCAPED_UNICODE));
    }


    /**
     * 필요한 데이터만 가져온다.
     * @param $data
     * @param array $availField
     * @param array|null $availJsonField
     * @return array
     */
    public static function getAvailData($data, array $availField, array $availJsonField = null): array
    {
        $result = array();
        foreach($availField as $each){
            $result[$each] = $data[$each];
        }
        if( !empty($availJsonField) ){
            foreach($availJsonField as $each){
                $result[$each] = json_decode($data[$each],true);
            }
        }
        return $result;
    }

    /**
     * 리스트에서 원하는 필드를 키와 밸유 설정하여 맵형태로 반환
     * @param array $list
     * @param $keyField
     * @param $valueField
     * @return array
     */
    public static function listToMap($list, $keyField, $valueField){
        $map=array();
        foreach($list as $each){
            $map[$each[$keyField]] = $each[$valueField];
        }
        return $map;
    }

    /**
     * 배송업체 맵
     * @return array
     */
    public static function getDeliveryCompanyMap(){
        $delivery = \App::load(\Component\Delivery\Delivery::class);
        $tmpDelivery = $delivery->getDeliveryCompany(null, true);
        return SlCommonUtil::arrayAppKeyValue($tmpDelivery, 'sno', 'companyName');
    }

    /**
     * 파일 내용을 변수로 가져온다.
     * @param $filePath
     * @return false|string
     */
    public static function getFileData($filePath){
        ob_start();
        include($filePath);
        $fileContents = ob_get_contents();
        ob_end_clean();
        return $fileContents;
    }

    /**
     * 파라미터로 입력된 값을 치환
     * @param $str
     * @param $param
     * @return array|mixed|string|string[]
     */
    public static function replaceData($str, $param){
        foreach(  $param as $replaceKey => $replaceData  ){
            $str = str_replace( '{%'.$replaceKey.'%}' , $replaceData, $str );
        }
        return $str;
    }

    /**
     * 파일을 가져와서 치환
     * @param $filePath
     * @param $param
     * @return array|mixed|string|string[]
     */
    public static function getReplaceFileData($filePath, $param){
        return SlCommonUtil::replaceData(SlCommonUtil::getFileData($filePath), $param);
    }

    /**
     * SQL 파일 가져오기
     * @param $class
     * @param $nameSpace
     * @return string
     */
    public static function getSqlFilePath($class, $nameSpace): string{
        $classPath = explode('\\',$class);
        return './module/'.str_ireplace('\\', '/' ,  $nameSpace ).'/Sql/'. lcfirst($classPath[count($classPath)-1]).'.sql';
    }

    /**
     * 컨트롤러에 JSON 결과 반환
     * @param array $resultData
     * @param int $code
     * @param string $message
     * @return array
     */
    public static function getJsonResult($resultData, $code=200, $message='정상 호출됨.'): array{
        $resultArray = [
            'code' => $code,
            'message' => $message,
        ];
        if(!empty($resultData)){
            $resultArray = array_merge($resultArray, $resultData);
        }
        return $resultArray;
    }

    /**
     * 배열에 들어있는 빈값 체크
     * @param $dataList
     * @param $checkKeyList
     * @return bool
     */
    public static function isArrayValueAllEmpty( $dataList, $checkKeyList ): bool
    {
        $result = true;
        foreach($checkKeyList as $checkKey){
            if( !empty( $dataList[$checkKey] ) ){
                $result = false;
                break;
            }
        }
        return $result;
    }

    /**
     * 배송 업체 반환
     * @return array
     */
    public static function getDeliveryList(): array{
        $delivery = \App::load(\Component\Delivery\Delivery::class);
        $tmpDelivery = $delivery->getDeliveryCompany(null, true);
        $deliveryCom[0] = '= ' . __('배송 업체') . ' =';
        if (empty($tmpDelivery) === false) {
            foreach ($tmpDelivery as $key => $val) {
                // 기본 배송업체 sno
                if ($key == 0) {
                    $deliverySno = $val['sno'];
                }
                $deliveryCom[$val['sno']] = $val['companyName'];
            }
            unset($tmpDelivery);
        }
        return $deliveryCom;
    }

    /**
     * 주문 승인 처리 여부 가져오기
     * @param $scmNo
     * @return bool
     */
    public static function getIsOrderAccept($scmNo){
        return 'y' == DBUtil2::getOne('sl_setScmConfig', 'scmNo' , $scmNo)['orderAcceptFl'];
    }

    /**
     * 회원 승인 처리 여부 가져오기
     * @param $scmNo
     * @return bool
     */
    public static function getIsMemberAccept($scmNo){
        return 'y' == DBUtil2::getOne('sl_setScmConfig', 'scmNo' , $scmNo)['memberAcceptFl'];
    }

    /**
     *  운영자 리스트
     * [02001001]    => 영업
     * [02001002]    => 디자인
     * [02001003]    => 생산관리
     * [02001004]    => 회계
     * [02001005]    => 기타운영지원
     * @param $departmentCd
     * @return mixed
     */
    public static function getManagerList( $departmentCd ){
        if( empty($departmentCd) ){
            $searchVo = new SearchVo('scmNo=?','1');
        }else{
            $searchVo = new SearchVo('departmentCd=?',  $departmentCd);
        }

        $searchVo->setWhere('isDelete=?');
        $searchVo->setWhereValue('n');

        $managerList = DBUtil2::getListBySearchVo(new TableVo(DB_MANAGER, 'tableManagerWithSno') , $searchVo);

        return SlCommonUtil::arrayAppKeyValue($managerList, 'sno', 'managerNm');
    }

    /**
     * 맵데이터 Key , Value 전환
     * @param $mapData
     * @return array
     */
    public static function getReverseMap($mapData): array{
        $resultMap = [];
        foreach($mapData as $key => $value){
            $resultMap[$value] = $key;
        }
        return $resultMap;
    }

    /**
     * 특정 키만 배열로 한다
     * @param $data
     * @param $key
     * @return array
     */
    public static function getArrayKeyData( $data, $key ){
        $result = [];
        foreach($data as $each){
            $result[] = $each[$key];
        }
        return $result;
    }

    /**
     * 부서 리스트 반환
     * @return array
     */
    public static function getDeptList(){
        $deptList = DBUtil2::getList(DB_CODE, 'groupCd', '02001');
        return SlCommonUtil::arrayAppKeyValue($deptList, 'itemCd','itemNm');
    }

    /**
     * 지점리스트 반환
     * @return array
     */
    public static function getBranchList(){
        $sql = "select distinct branch from sl_branchDept ";
        $list = DBUtil2::runSelect($sql);
        return SlCommonUtil::arrayAppKeyValue($list, 'branch', 'branch');
    }

    /**
     * 지점 부서 리스트 반환
     * @return array
     */
    public static function getBranchDeptList($branchName){
        $sql = "select sno,  branch , dept, address from sl_branchDept where branch='{$branchName}' ";
        $list = DBUtil2::runSelect($sql);

        $result['selectData'] = SlCommonUtil::arrayAppKeyValue($list, 'sno', 'dept');
        $result['allData'] = SlCommonUtil::arrayAppKey($list, 'sno') ;
        return $result;
    }

    /**
     * 색상 리스트 가져오기
     * @return array
     */
    public static function getColorList(){
        $colorList = gd_code('05001');
        $result = [];
        foreach($colorList as $key => $color){
            $colorArray = explode('^|^', $color);
            $result[$key]['name'] = $colorArray[0];
            $result[$key]['value'] = $colorArray[1];
        }
        return $result;
    }

    /**
     * 관리자 부서 가져오기
     * @param $managerSno
     * @return array
     */
    public static function getManagerInfo($managerSno=0){
        if(empty($managerSno)){
            $managerSno = \Session::get('manager.sno');
        }
        $infoData = DBUtil2::getOne(DB_MANAGER, 'sno', $managerSno ); //pw는 빼고 반환
        //해야할 필요가 있을 때 DB에서 조회
        //$infoData['dutyCdKr'] = '';
        //$infoData['positionCdKr'] = '';
        unset($infoData['memPw']);
        return $infoData;
    }

    /**
     * 로그인한 사람 정보
     * @return mixed
     */
    public static function getMyInfo(){
        return SlCommonUtil::getManagerInfo(SlCommonUtil::getManagerSno());
    }

    /**
     * 기본 시간 반환
     * @return false|string
     */
    public static function getDefaultDateTime(){
        return date('Y-m-d H:i:s');
    }

    /**
     * 휴대폰 문자열 포맷
     * @param $phone
     * @return array|string|string[]|null
     */
    public static function getCellPhoneFormat($phone){
        $phone = preg_replace("/[^0-9]/", "", $phone);
        $length = strlen($phone);
        switch($length){
            case 11 :
                return preg_replace("/([0-9]{3})([0-9]{4})([0-9]{4})/", "$1-$2-$3", $phone);
            case 10:
                return preg_replace("/([0-9]{3})([0-9]{3})([0-9]{4})/", "$1-$2-$3", $phone);
            default :
                return $phone;
        }
    }

    /**
     * 휴대전화 유효성 검사
     * @param string $phone
     * @return bool
     */
    public static function isValidCellPhone($phone='') {
        $phone = preg_replace('/[^0-9]/', '', $phone);
        if (preg_match("/^01[0-9]{8,9}$/", $phone)){
            return true;
        }else{
            return false;
        }
    }

    /**
     * 암호화 처리
     * @return string
     */
    public static function getAesIv(){
        return chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0);
    }
    public static function getAesKey(){
        return substr(hash('sha256', WorkCodeMap::SECRET_KEY, true), 0, 32);
    }
    public static function aesEncrypt($plainText){
        return SlCommonUtil::base64url_encode(openssl_encrypt($plainText, 'aes-256-cbc', SlCommonUtil::getAesKey(), OPENSSL_RAW_DATA, SlCommonUtil::getAesIv() ));
    }
    public static function aesDecrypt($encryptText){
        return openssl_decrypt(SlCommonUtil::base64url_decode($encryptText), 'aes-256-cbc', SlCommonUtil::getAesKey(), OPENSSL_RAW_DATA, SlCommonUtil::getAesIv());
    }
    public static function base64url_encode($data){
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }
    public static function base64url_decode($data) {
        return base64_decode(str_pad(strtr($data, '-_', '+/'), strlen($data) % 4, '=', STR_PAD_RIGHT));
    }

    /**
     * Short URL return
     * @param $url
     * @return mixed
     * @throws \Exception
     */
    public static function getShortUrl($url){
        $reqUrl = 'https://openapi.naver.com/v1/util/shorturl';
        $data = 'url=' . $url;
        $header = [];
        $header[] = 'Content-Type: application/x-www-form-urlencoded; charset=UTF-8';
        $header[] = 'X-Naver-Client-Id:ljiJRJ1yPFtL5GZxEdEb';
        $header[] = 'X-Naver-Client-Secret:NmxsWsZy6l';
        $output = json_decode((string)SlPostRequestUtil::request($reqUrl, $data, $header),true);
        return $output['result']['url'];
    }

    /**
     * 컨텐츠 치환
     * @param $replace
     * @param $prefix
     * @param $suffix
     * @param $contents
     * @return array|mixed|string|string[]
     */
    public static function replaceContents($replace, $prefix, $suffix, $contents){
        //$replace['serial']
        foreach($replace as $key => $value){
            $contents = str_replace($prefix.$key.$suffix,$value,$contents);
        }
        return $contents;
    }

    /**
     * 코드에 해당하는 값을 반환
     * @param $key
     * @param $codeMap
     * @param $value
     * @return array
     */
    public static function setCodeToName($key , $codeMap, &$value){
        $value[$key.'Kr'] = $codeMap[gd_isset($value[$key],0)];
    }

    /**
     * 개발모드 확인
     * @return bool
     */
    public static function isDev(){
        if( 'bcloud1478.godomall.com'  === \Request::getDefaultHost()){
            return true;
        }else{
            return false;
        }
    }

    public static function isDevId(){
        $managerId = \Session::get('manager.managerId');
        if( '_b1478'  === $managerId || 'djemalsrpwjd'  === $managerId || 'nkin' === $managerId ){
            return true;
        }else{
            return false;
        }
    }

    public static function isDevIp(){
        $devIpList = [
          '180.83.86.189',
          /*'1.243.196.131',*/
        ];
        if( in_array(\Request::getRemoteAddress(), $devIpList) ){
            return true;
        }else{
            return false;
        }
    }

    public static function getOrderDeliveryListMap(): array{
        $map = [];
        $deliveryList = DBUtil2::getList('sl_setScmDeliveryList');
        foreach($deliveryList as $key => $value){
            $subAddress = empty($value['receiverAddressSub'])?'-':$value['receiverAddressSub'];
            $fullAddressKey = md5($value['receiverAddress'].$subAddress);
            $map[$fullAddressKey] = $value['subject'];
        }
        //gd_debug($map);
        return $map;
    }

    /**
     * 빈 값 체크하고 있으면 설정한 값으로 반환
     * @param $value
     * @param $setValue
     * @return mixed|string
     */
    public static function setEmptyValue($value, $setValue){
        return empty($value)?'':$setValue;
    }

    /**
     * 결제 수단 반환
     * @param $settleKind
     * @return mixed
     */
    public static function getSettleKindName($settleKind){
        return gd_policy('order.settleKind', 1)[$settleKind]['name'];
    }
    public static function getSettleKindMap(){
        $settleKindPolicy =  gd_policy('order.settleKind', 1);
        $map = array();
        foreach($settleKindPolicy as $key => $each){
            $map[$key] = $each['name'];
        }
        return $map;
    }

    /**
     * Request 기본 날짜 설정
     * @param $dateArrStr
     * @param string $diffDay
     * @param int $searchPeriod
     */
    public static function setDefaultDate($dateArrStr, $diffDay = '-365 day', $searchPeriod=365){
        if(empty(\Request::get()->get($dateArrStr))){
            $arr[] = date('Y-m-d', strtotime($diffDay));
            $arr[] = date('Y-m-d');
            \Request::get()->set($dateArrStr, $arr);
            \Request::get()->set('searchPeriod', $searchPeriod);
        }
    }

    /**
     * 루프 데이터 셋팅
     * @param $loopList
     * @param $instant
     * @param $fncName
     * @param $mixData
     * @return mixed
     */
    public static function setEachData($loopList, $instant, $fncName, &$mixData=null){
        foreach($loopList as $key => $each){
            $loopList[$key] = $instant->$fncName($each, $key, $mixData); // ($each, $key, $data)
        }
        return $loopList;
    }

    public static function getHost(){
        return \Request::getScheme()."://".\Request::getDefaultHost();
    }

    public static function getAdminHost(){
        //return \Request::getScheme()."://gdadmin.".\Request::getDefaultHost();
        return \Request::getDomainUrl();
    }

    public static function devDebug($str){
        if( SlCommonUtil::isDevIp() ){
            gd_debug($str);
        }
    }

    /**
     * array에서 값을 찾고 있으면 해당 ArrayValue 반환
     * @param $str
     * @param $arr
     * @return false|mixed
     */
    public static function arrayInString($str, $arr){
        foreach($arr as $arr_value) {
            if (stripos($str,$arr_value) !== false) {
                return $arr_value;
            }
        }
        return false;
    }

    /**
     * 키와 값을 변경하여 요청한 값에대한 키 Return
     * @param $code
     * @param $key
     * @return mixed
     */
    public static function getFlipData($code, $key){
        return array_flip($code)[$key];
    }

    /**
     * 실시간 업데애트
     * @param $params
     * @return mixed
     * @throws \Exception
     */
    public static function updateRealtime($params){
        $tableName = SlCodeMap::REALTIME_UPDATE_DB_MAP[$params['div']];
        $content = str_replace("\r\n", "\r",urldecode($params['value']));

        $updateData = [$params['key'] => $content];

        return DBUtil2::update($tableName,$updateData, new SearchVo('sno=?', $params['sno']));
    }

    /**
     * 엑셀 데이터 가져오기
     * @param $data
     * @param $fieldName
     * @param int[] $code
     * @return string
     */
    public static function getExcelData($data, $fieldName, array $code){
        return trim($data[$code[$fieldName]]);
    }

    public static function getCurrentPageUrl($addParam){
        return basename(\Request::getPhpSelf()).'?'.$addParam. \Request::getQueryString();
    }

    public static function getOnlyNumber($str){
        return preg_replace("/[^0-9]*/s", "", $str.'');
    }

    public static function getOnlyNumberWithDot($str){
        return preg_replace("/[^0-9.]*/s", "", $str.'');
    }

    /**
     * 날짜 차이 계산
     * @param $startDate
     * @param $endDate
     * @return false|int
     * @throws \Exception
     */
    public static function getDateDiff($startDate, $endDate){
        if( strlen($startDate) >= 10 && strlen($endDate) >= 10 ){
            $sDate = new DateTime($startDate);
            $eDate = new DateTime($endDate);

            $dateDiff = date_diff($sDate, $eDate);
            return $dateDiff->days * ( empty($dateDiff->invert)?1:-1 ) ;
        }else{
            return 0;
        }
    }

    public static function getDateDiffMonth($startDate, $endDate){
        $sDate = new DateTime($startDate);
        $eDate = new DateTime($endDate);
        $dateDiff = date_diff($sDate, $eDate);
        if(!empty($dateDiff->y)) $str[] = $dateDiff->y . '년';
        if(!empty($dateDiff->m)) $str[] = $dateDiff->m . '개월';
        if(!empty($dateDiff->d)) $str[] = $dateDiff->d . '일';
        return [
            'str'=>$str,
            'invert'=>$dateDiff->invert,
        ];
    }


    /**
     * 날짜 계산
     * @param $startDate
     * @param $suffixCount
     * @param string $suffixCalcType
     * @return false|string
     */
    public static function getDateCalc($startDate, $suffixCount, $suffixCalcType='day'){
        return date('Y-m-d', strtotime($startDate . ' '.$suffixCount.' '.$suffixCalcType));
    }

    public static function dayOfWeek($date){
        $daysInKorean = ['일', '월', '화', '수', '목', '금', '토'];
        return $daysInKorean[date('w', strtotime($date))];
    }

    public static function getSimpleWeekDay($date, $isTag = false){
        if( '-' == $date || empty($date) || '0000-00-00' === $date ){

            if($isTag){
                return '<span class="text-muted">미정</span>';
            }else{
                return '미정';
            }
        }else{
            $daysInKorean = ['일', '월', '화', '수', '목', '금', '토'];
            $week = $daysInKorean[date('w', strtotime($date))];
            return gd_date_format('m/d',$date) . '('.$week.')';
        }
    }

    public static function getSimpleWeekDayWithYear($date){
        if( '-' == $date ||  empty($date) || '0000-00-00' === $date ){
            return '-';
        }else{
            $daysInKorean = ['일', '월', '화', '수', '목', '금', '토'];
            $week = $daysInKorean[date('w', strtotime($date))];

            if( '-' == gd_date_format('y/m/d',$date) ){
                $week = '';
            }else{
                $week = '('.$week.')';
            }

            return gd_date_format('y/m/d',$date) . $week;
        }
    }

    /**
     * 남은일자 반환
     * @param $date
     * @return string
     * @throws \Exception
     */
    public static function getRemainDt($date){
        if( !empty($date) && '-' !== $date && '0000-00-00' !== $date ){
            $sDate = date('Y-m-d');
            $eDate = gd_date_format('Y-m-d',$date);
            $dateDiff = SlCommonUtil::getDateDiff($sDate, $eDate);

            if( $dateDiff === 0 ) {
                $rslt = abs($dateDiff).'일';
            }else if( $dateDiff > 0 ){
                $rslt = '<span class="text-green">'.abs($dateDiff).'일 남음</span>';
            }else{
                $rslt = '<span class="text-danger">'.abs($dateDiff).'일 지남</span>';
            }
        }else{
            $rslt = '';
        }
        return $rslt;

    }

    public static function getRemainDt2($date){
        if( !empty($date) && '-' !== $date && '0000-00-00' !== $date ){
            $sDate = date('Y-m-d');
            $eDate = gd_date_format('Y-m-d',$date);
            $dateDiff = SlCommonUtil::getDateDiff($sDate, $eDate);

            if( $dateDiff === 0 ) {
                $rslt = abs($dateDiff).'일';
            }else if( $dateDiff > 0 ){
                $rslt = '<span class="text-green">'.abs($dateDiff).'일</span>';
            }else{
                $rslt = '<span class="text-danger">'.abs($dateDiff).'일 지남</span>';
            }
        }else{
            $rslt = '';
        }
        return $rslt;
    }


    public static function getRemainDtMonth($date){
        if( !empty($date) && '-' !== $date && '0000-00-00' !== $date ) {
            $sDate = date('Y-m-d');
            $eDate = gd_date_format('Y-m-d', $date);
            $dateDiff = SlCommonUtil::getDateDiffMonth($sDate, $eDate);

            if( empty($dateDiff['str']) ){
                $rslt = '-';
            }else if( $dateDiff['invert'] > 0 ){
                $rslt = '<span class="text-danger">'.implode(' ',$dateDiff['str']).' 지남</span>';
            }else{
                $rslt = '<span class="text-green">'.implode(' ',$dateDiff['str']).' 남음</span>';
            }
        }else{
            $rslt = '';
        }
        return $rslt;
    }

    /**
     * 날짜 데이터 공백으로 변경
     * @param $obj
     * @return mixed
     */
    public static function setDateBlank($obj){
        //SitelabLogger::logger2(__METHOD__, $obj);
        if( is_array($obj) ) {
            foreach($obj as $key => $value){
                if('0000-00-00' === $value){
                    $value = '';
                    $obj[$key] = $value;
                }
            }
        }
        return $obj;
    }

    /**
     * 한글 표기
     * @param $num
     * @return string
     */
    public static function numberToKorean($num) {
        $unit = array('', '만', '억', '조');
        $unit_pos = 0;
        $korean_num = '';

        while ($num > 0) {
            $section = $num % 10000;

            // 만 단위 이상만 표시하고, 1000~9999 (1천~9천)은 표시하지 않음
            if ($section != 0 && !($unit_pos == 0 && $section < 10000 && $section >= 1000)) {
                $formatted_section = $unit_pos > 0 ? number_format($section) : $section;
                $korean_num = $formatted_section . $unit[$unit_pos] . $korean_num;
            }

            $num = (int)($num / 10000);
            $unit_pos++;
        }
        return empty($korean_num)?'-':$korean_num;
    }

    /**
     * 숫자형 문자를 핸드폰 형식 반환
     * @param $phoneNumber
     * @return string
     */
    public static function formatPhoneNumber($phoneNumber) {
        $length = strlen($phoneNumber);

        if ($length === 11 || $length === 10) {
            $format = ($length === 11) ? [3, 4, 4] : [3, 3, 4];
            $start = 0;
            $formatted = [];

            foreach ($format as $len) {
                $formatted[] = substr($phoneNumber, $start, $len);
                $start += $len;
            }

            return implode('-', $formatted);
        }

        return "-";
    }

    /**
     * 모바일 기기 체크
     * @return bool
     */
    public static function isMobile(){
        $user_agent = \Request::getUserAgent();
        $mobile_agents = array(
            'Mobile', 'Android', 'Silk/', 'Kindle', 'BlackBerry', 'Opera Mini', 'Opera Mobi', "iphone","lgtelecom","skt","mobile","samsung","nokia","blackberry","android","android","sony","phone"
        );
        foreach ($mobile_agents as $agent) {
            if (stripos($user_agent, $agent) !== false) {
                return true; // 모바일 디바이스
            }
        }
        return false; // PC 혹은 기타 디바이스
    }

    /**
     * 관리자 번호 가져오기
     * @return false
     */
    public static function getManagerSno(){
        return \Session::get('manager.sno');
    }

    /**
     * 로그인 매니저 메일 반환
     * @return mixed
     */
    public static function getManagerMail(){
        return SlCommonUtil::getManagerInfo(\Session::get('manager.sno'))['email'];
    }

    /**
     * 생산처 아이디인지 여부 반환
     * @return bool
     */
    public static function isFactory(){
        $cnt = DBUtil2::getCount(DB_MANAGER, new SearchVo([
                'sno=?',
                'departmentCd=?',
            ]
            ,[
                \Session::get('manager.sno')
                ,'02001006' //생산처코드
            ]));
        return $cnt > 0;
    }

    /**
     * nl2br 강화
     * @param $str
     * @return string|string[]
     */
    public static function nl2br($str){
        return str_replace('\\n','<br>', $str);
    }

    /**
     * 중복 요소 제외하고 더하기
     * @param $list
     * @return float|int
     */
    public static function sumUnique($list) {
        // 배열 내의 중복 값을 제거
        $unique = array_unique($list);
        // 중복이 제거된 배열의 값들을 합산
        return array_sum($unique);
    }

    /**
     * List Rowspan 설정
     * @param $list
     * @param $rowspanKeyMap
     * @param $params
     */
    public static function setListRowSpan(&$list, $rowspanKeyMap, $params){

        foreach($rowspanKeyMap as $rowspanKey => $rowspanData){
            $rowspanData['spanData'] = [];
            $rowspanData['spanFirst'] = [];
        }

        foreach($list as $key => $value){
            //특정일자 기준일 경우 처리를 위한 데이터.
            $sortCondition = explode(',', $params['condition']['sort']);
            foreach($rowspanKeyMap as $rowspanKey => $rowspanData){
                //Key설정
                $rowspanKeyName = $rowspanKey . 'RowspanKey';

                if(is_array($rowspanData['valueKey'])){
                    $implodeList=[];
                    foreach($rowspanData['valueKey'] as $eachRowspanValueKey){
                        $implodeList[] = $value[$eachRowspanValueKey];
                    }
                    $spanKeyValue = implode('_',$implodeList);
                }else{
                    $spanKeyValue = $value[$rowspanData['valueKey']]; //기본 키 데이터
                }

                $value[$rowspanKeyName] = $spanKeyValue;

                //TODO : 효율적으로 변경하기.
                if ( 'D' == $sortCondition[0] ){ //등록 일기준
                    $value[$rowspanKeyName] .= gd_date_format('Y-m-d', $value['regDt']);
                }else if ( 'T1' == $sortCondition[0] ){
                    $value[$rowspanKeyName] .= $value['hopeDt'];
                }else if ( 'T2' == $sortCondition[0] ){
                    $value[$rowspanKeyName] .= $value['expectedDt'];
                }else if ( 'T3' == $sortCondition[0] ){
                    $value[$rowspanKeyName] .= $value['completeDt'];
                }else if ( 'COST1' == $sortCondition[0] ){
                    $value[$rowspanKeyName] .= $value['styleSno'].'_'.$value['estimateCost'];
                }else if ( 'C' == substr($sortCondition[0],0,1) ) { //납기일 기준
                    $value[$rowspanKeyName] .= $value['msDeliveryDt'];
                }

                /*else if( 'P3' == $sortCondition[0] ){
                    $value[$rowspanKeyName] .= $value['customerDeliveryDt'];
                }*/

                $rowspanData['spanData'][$value[$rowspanKeyName]]++; //Rowspan처리.
                $rowspanKeyMap[$rowspanKey] = $rowspanData;
            }
            $list[$key] = $value;
        }

        foreach($list as $key => $value){
            foreach($rowspanKeyMap as $rowspanKey => $rowspanData){
                $rowspanKeyName = $rowspanKey . 'RowspanKey';
                $rowspanName = $rowspanKey . 'Rowspan';
                if( empty($rowspanData['spanFirst'][$value[$rowspanKeyName]]) ){
                    $value[$rowspanName] = $rowspanData['spanData'][$value[$rowspanKeyName]]; //RowspanData입력.
                    $rowspanData['spanFirst'][$value[$rowspanKeyName]] = true;
                }else{
                    $value[$rowspanName] = 0;
                }
                $rowspanKeyMap[$rowspanKey] = $rowspanData;
            }
            $list[$key] = $value;
        }
    }

    /**
     * @param $template
     * @param $params
     * @return mixed|string|string[]
     */
    public static function setTemplateValue($template, $params){
        foreach($params as $replaceKey => $replaceWord){
            $template = str_replace('{'.$replaceKey.'}', $replaceWord, $template);
        }
        return $template;
    }

    /**
     * 기본적인 필드 리셋
     * @param $object
     */
    public static function setDefaultCopyObject(&$object){
        unset($object['sno']);
        unset($object['regDt']);
        unset($object['modDt']);
    }

    /**
     * 휴일 여부 체크
     * @param string $now
     * @return bool
     */
    public static function isHoliday($now=''){
        $now=empty($now) ? date('Ymd'):$now;
        $data = DBUtil2::getOne('sl_holiday', 'locdate', $now);
        if( 'Y' !== strtoupper($data['isHoliday']) ){
            return false;
        }else{
            return true;
        }
    }



    /**
     * 리스크 값 Value 빈값 만들기
     * (특이 구조에 적용하면 안됨. 반드시 1차원적 구조에 적용)
     * @param $list
     * @return mixed|string
     */
    public static function setListEmptyValue($list){
        foreach($list as $key => $each){
            $list[$key] = '';
        }
        return $list;
    }

    /**
     * 메소드 가져오기
     * @param $class
     * @return array
     * @throws \ReflectionException
     */
    public static function getMethodMap($class){
        //EX: '\Component\Ims\ImsApprovalService' or '\\'.__CLASS__
        $methodClass = new \ReflectionClass($class);
        $methods = $methodClass->getMethods();
        $methodMap = [];
        foreach( $methods as $method ){
            $methodMap[$method->name]=1;
        }
        return $methodMap;
    }

    /**
     * 클래스 상수 가져오기
     * @param $className
     * @return null
     */
    public static function getClassConstants($className) {
        try {
            $reflection = new ReflectionClass($className);
            return $reflection->getConstants(); // 클래스의 모든 상수 반환
        } catch (ReflectionException $e) {
            return null; // 클래스가 존재하지 않을 경우 null 반환
        }
    }

    /**
     * 엠티나 제로가 있으면 true
     * @param $array
     * @return bool
     */
    public static function  isAllEmptyOrZero($array) {
        // 배열의 모든 요소가 0이거나 비어있다면 true, 하나라도 아니면 false 반환
        return empty(array_filter($array, function($value) {
            return $value !== 0 && !empty($value);
        }));
    }

    /**
     * 문자열 초과 자르기
     * @param $text
     * @param int $limit
     * @return string
     */
    public static function truncateText($text, $limit = 60) {
        // 문자열 길이를 체크
        if (mb_strlen($text, 'UTF-8') > $limit) {
            // 문자열을 limit 길이만큼 자르고 '...'을 붙임
            return mb_substr($text, 0, $limit, 'UTF-8') . '...';
        }
        // 길이가 limit 이하인 경우 그대로 반환
        return $text;
    }


    /**
     * DB 데이터 정제
     * Strip : nl2br memoStrip
     * Json  : 그대로 decode
     * Scheme : 기본 스키마 셋팅 (default data set)
     * Code : 기본 스키마 셋팅 (...Kr)
     * File : File스키마 기본 설정
     * @param $tableName
     * @return array
     * @throws \Exception
     */
    public static function refineDbData($data, $tableName){
        $tableInfoList = DBTableField::callTableFunction($tableName); //sl_XXX
        foreach($tableInfoList as $tableData){
            if( true === $tableData['strip'] ){
                $data[$tableData['val']] = gd_htmlspecialchars_stripslashes($data[$tableData['val']]);
            }
            if( true === $tableData['json'] ){
                if( true === $tableData['strip'] ){
                    $data[$tableData['val']] = $data[$tableData['val'].'Strip'];
                }
                $data[$tableData['val']] = json_decode($data[$tableData['val']], true);
                if( isset($tableData['scheme']) ){
                    $data[$tableData['val']] = SlCommonUtil::setJsonField($data[$tableData['val']],$tableData['scheme']); //구조 변경 대비 기본값 설정
                }
            }
            if( true === $tableData['isFile'] && ( empty($data[$tableData['val']]) || 0 >= count($data[$tableData['val']])  ) ){
                $data[$tableData['val']] = ImsJsonSchema::getDefaultFileSchema();
            }
            if( isset($tableData['code']) ){
                //코드가 있다면.
                $data[$tableData['val'].'Kr'] = $tableData['code'][$data[$tableData['val']]];
            }
        }
        return SlCommonUtil::setDateBlank($data);
    }

    /**
     * 날짜 포맷
     * @param $date
     * @param string $format
     * @return string
     */
    public static function setDateFormat($date, $format = 'Y-m-d'){
        $dt = DateTime::createFromFormat('Ymd', $date);
        return $dt->format($format);
    }

    public static function unnamed($paramsList, &$targetList){
        foreach($paramsList as $key => $value){
            $targetList[$key]=$value;
        }
    }

    /**
     * IMS 관리자
     * @return bool
     */
    public static function isImsAdmin(){
        $managerId = \Session::get('manager.managerId');
        return in_array($managerId, ImsCodeMap::IMS_ADMIN);
    }

    /**
     * 달러정보
     * @return mixed
     */
    public static function getCurrentDollar(){
        return DBUtil2::getOneSortData('es_exchangeRateConfig', '1', '1', 'regDt desc')['exchangeRateConfigUSDManual'];
    }

    /**
     * 수동 스트립 하여 JSON디코드
     * @param $json
     * @return mixed
     */
    public static function stripJsonDecode($json){
        $json = str_replace('\\"', '"', $json); // \" -> "
        $json = str_replace('\\\\', '\\', $json); // \\ -> \
        return json_decode($json, true);
    }

    /**
     * 마진 계산
     * @param $price
     * @param $cost
     * @param $point
     * @return float
     */
    public static function getMargin($price, $cost, $point=0){
        return $price>0 ? round(100-($cost/$price*100), $point) : 0;
    }

    //---------- HTML 처리

    public static function setColWidth($maxWidth, &$fieldData){

        $colTotalWidth = 0;
        $emptyColCount = 0;

        foreach($fieldData as $each){
            if(!empty($each['col'])){
                $colTotalWidth+=$each['col'];
            }else{
                $emptyColCount++;
            }
        }

        $defaultColWidth = round(($maxWidth - $colTotalWidth) / $emptyColCount,0);

        foreach($fieldData as $key => $each){
            if( empty($each['col']) ){
                $each['col'] = $defaultColWidth;
                $fieldData[$key] = $each;
            }
        }

    }


    /***
     * HTML TITLE 생성
     * @param $titles
     * @return string
     */
    public static function createHtmlTableTitle($titles){
        $htmlTemplateColList = [];
        $htmlTemplateList = [];
        $colTotalWidth = 0;
        $emptyColCount = 0;
        foreach($titles as $title){
            if(!empty($title['col'])){
                $colTotalWidth+=$title['col'];
            }else{
                $emptyColCount++;
            }
        }

        $defaultColWidth = (100 - $colTotalWidth) / $emptyColCount;

        foreach($titles as $title){
            //Col Setting
            if(!empty($title['col'])) {
                $htmlTemplateColList[] = "<col style='width:{$title['col']}%'>";
            }else{
                $htmlTemplateColList[] = "<col style='width:{$defaultColWidth}%'>";
            }
            //Title Setting
            $htmlTemplateList[] = "<th class=''>{$title['name']}</th>";
        }

        //gd_debug($htmlTemplateColList);
        //gd_debug($htmlTemplateList);

        $htmlCol = implode('',$htmlTemplateColList);
        $htmlTitle = implode('',$htmlTemplateList);
        $colStr = "<colgroup>{$htmlCol}</colgroup>";
        $titleStr = "<tr>{$htmlTitle}</tr>";

        return $colStr.$titleStr;
    }

    /**
     * vue 사용 리스트 Body
     * @param $dpData
     * @return string
     */
    public static function createHtmlTableVueBody($dpData){
        $namespace = 'SiteLabUtil\SlCommonUtil';
        $htmlTemplateList = [];
        $htmlTemplateList[] = "<td>{% listTotal.idx - index %}</td>";
        foreach($dpData as $dpKey => $dpValue){
            if(true !== $dpValue['skip']){
                if(empty($dpValue['custom'])){
                    $fncName = 'createHtml'.ucfirst($dpKey);
                    if(!empty($dpValue['subData'])){
                        foreach($dpValue['subData'] as $subKey => $subValue){
                            $dpValue['subHtml'] = "<div class='{$subValue['class']}'>{% each.{$subKey} %}</div>";
                        }
                    }
                    if( method_exists($namespace, $fncName) ){
                        $htmlTemplateList[] = call_user_func([$namespace, $fncName], $dpKey, $dpValue);
                    }else{
                        if( 'number' === $dpValue['type'] ){
                            $htmlTemplateList[] = "<td class='{$dpValue['class']}'> {% $.setNumberFormat(each.{$dpKey}) %}{$dpValue['afterContents']}</td>";
                        }else{
                            $htmlTemplateList[] = "<td class='{$dpValue['class']}'> {% each.{$dpKey} %}{$dpValue['afterContents']}</td>";
                        }

                    }
                }else{
                    $htmlTemplateList[] = "<td class='{$dpValue['class']}'>**{$dpValue['custom']}**</td>";
                }
            }
        }
        //gd_debug($htmlTemplateList);
        return implode('', $htmlTemplateList);
    }

    /**
     * @param array $data
     * @param array $unsetList
     * @return array
     */
    public static function unsetByList(array $data, array $unsetList){
        foreach($unsetList as $unsetField){
            unset($data[$unsetField]);
        }
        return $data;
    }

    /**
     * 고객명 연결
     * @param $dpKey
     * @param $dpValue
     * @return string
     */
    public static function createHtmlCustomerName($dpKey, $dpValue){
        return "
        <td class='{$dpValue['class']}'>
            <span class='sl-blue tn-pop-customer-info hover-btn cursor-pointer' :data-sno='each.customerSno' @click='openCustomer2(each.customerSno)'>
                {% each.{$dpKey} %}
            </span>
            {$dpValue['afterContents']}
        </td>";
    }
    /**
     * 프로젝트 번호
     * @param $dpKey
     * @param $dpValue
     * @return string
     */
    public static function createHtmlProjectNo($dpKey, $dpValue){
        return "
        <td class='{$dpValue['class']}'>
            <a :href=\"'ims_view2.php?sno='+each.projectSno\" class='text-danger' target='_blank'>
                {% each.{$dpKey} %}
            </a>
            {$dpValue['afterContents']}
        </td>";
    }

    public static function createHtmlStyleFullName($dpKey, $dpValue){
        return "
        <td class='{$dpValue['class']}'>
            <span v-html='each.{$dpKey}' @click='openProductReg2(each.projectSno, each.sno, -1)' class='hover-btn cursor-pointer font-11'></span>
            {$dpValue['afterContents']}
            {$dpValue['subHtml']}
        </td>";
    }

    public static function createHtmlPrdCost($dpKey, $dpValue){
        return "
        <td class='{$dpValue['class']}'>
            <span v-html='$.setNumberFormat(each.{$dpKey})' @click='openFactoryEstimateView(each.projectSno, each.sno, each.estimateConfirmSno, \"cost\")' class='hover-btn cursor-pointer font-11 sl-blue bold' v-if='each.estimateConfirmSno > 0'></span>
            <span v-html='each.{$dpKey}' class='font-11 sl-blue' v-if='0>=each.estimateConfirmSno'></span>
            {$dpValue['afterContents']}
        </td>";
    }

}
