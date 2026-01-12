<?php
namespace Component\Work;

use App;
use Session;
use Component\Database\DBTableField;
use Component\Member\Manager;
use Framework\Utility\DateTimeUtils;
use LogHandler;
use Request;
use Exception;
use Framework\Debug\Exception\AlertBackException;
use SiteLabUtil\SlCommonUtil;
use SlComponent\Database\DBUtil;
use SlComponent\Database\DBUtil2;
use SlComponent\Database\SearchVo;
use SlComponent\Database\TableVo;
use SlComponent\Util\SitelabLogger;
use SlComponent\Util\SlCodeMap;
use SlComponent\Util\SlCommonTrait;
use SlComponent\Util\SlSmsUtil;

/**
 * 이노버 시스템 서비스 - ( 규모 커지면 카테고리별 서비스 나누기 )
 */
class WorkService {

    private $workCodeMap;
    private $documentCodeMap;

    public function __construct(){
        $this->workCodeMap = new \ReflectionClass('\Component\Work\WorkCodeMap');
        $this->documentCodeMap = new \ReflectionClass('\Component\Work\DocumentCodeMap');
    }

    /**
     * 업체 리스트 반환
     * @return mixed
     */
    public function getCompanyList(){
        $companyList = DBUtil::getList('sl_workCompany', '1' , '1', 'regDt desc');
        foreach( $companyList as $key => $value ){
            $managerList = json_decode( $value['companyManager'] ,true);
            $value['mainManagerName'] = $managerList[0]['name'];
            $value['mainManagerPosition'] = $managerList[0]['position'];
            $value['mainManagerPhone'] = $managerList[0]['phone'];
            $value['mainManagerCellPhone'] = $managerList[0]['cellPhone'];
            $value['mainManagerEmail'] = $managerList[0]['email'];
            $companyList[$key] = $value;
        }
        return $companyList;
    }

    /**
     * 업체 리스트 반환(Select용 코드 맵)
     * @return array
     */
    public function getCompanyMap(){
        return SlCommonUtil::arrayAppKeyValue( $this->getCompanyList() , 'sno', 'companyName');
    }

    public function saveCompany($param){
        $saveData = $param;
        $saveData['companyManager'] = json_encode( $saveData['companyManager'] , JSON_UNESCAPED_UNICODE );
        unset($saveData['sno']);
        if( empty($param['sno']) ){
            //insert
            $sno = DBUtil2::insert( 'sl_workCompany' , $saveData );
        }else{
            //update
            DBUtil2::update( 'sl_workCompany' , $saveData , new SearchVo('sno=?' ,$param['sno']) );
            $sno = $param['sno'];
        }
        return $this->getCompany($sno);;
    }

    /**
     * 업체 정보 반환
     * @param $sno
     * @return array
     */
    public function getCompany($sno){
        $dbResult = DBUtil2::getOne('sl_workCompany' , 'sno' , $sno);
        if( empty($dbResult) ){
            $result = DBTableField::getTableBlankData('tableWorkCompany');
            $result['companyType'] = 0;
            $result['salesManagerSno'] = \Session::get('manager.sno');
            $result['companyManager'] = [
                [   'name' => '',
                    'position' => '',
                    'phone' => '',
                    'cellPhone' => '',
                    'email' => '',
                    'etc' => '' ]
            ];
        }else{
            $result = SlCommonUtil::getAvailData($dbResult, SlCommonUtil::getArrayKeyData( DBTableField::tableWorkCompany() , 'val'  ) , ['companyManager']  ) ;

            $result['mainManagerName'] = $result['companyManager'][0]['name'];
            $result['mainManagerPhone'] = $result['companyManager'][0]['phone'];
            $result['mainManagerCellPhone'] = $result['companyManager'][0]['cellPhone'];
            $result['mainManagerEmail'] = $result['companyManager'][0]['email'];
            $result['mainManagerAddress'] = $result['companyManager'][0]['address'];
            $result['mainManagerPosition'] = $result['companyManager'][0]['position'];
        }
        return $result;
    }

    /**
     * 요청 저장(다건)
     * @param $param
     * @param $docSno
     * @throws Exception
     */
    function saveWorkRequest($param, $docSno){
        foreach($param as $requestData){
            $this->saveWorkRequestUnit($requestData, $docSno);
        }
    }
    /**
     * 요청 저장 (단건)
     * @throws Exception
     */
    function saveWorkRequestUnit($requestData, $docSno){
        if( !empty($requestData['reqContents']) ){
            if( empty($requestData['sno']) ){
                $requestData['documentSno'] = $docSno;
                $requestData['writeManagerSno'] = \Session::get('manager.sno');
                DBUtil2::insert( 'sl_workRequest' , $requestData );
            }else{
                DBUtil2::update( 'sl_workRequest' , $requestData, new SearchVo('sno=?', $requestData['sno']) );
            }
        }
    }



    /**
     * 요청 리스트 가져오기
     * @param $docSno
     * @return mixed
     */
    function getWorkRequestByDocSno($docSno){
        $docSno = empty($docSno) ? -9999:$docSno;
        $workRequestList = DBUtil2::getList( 'sl_workRequest' , 'documentSno' , $docSno , 'sno' );

        if( empty($workRequestList) ){
            $workRequestList = DocumentCodeMap::WORK_REQ_DEFAULT;
        }else{

            foreach( $workRequestList as $key => $requestData ){

                if( '0000-00-00' == $requestData['completeRequestDt']){
                    $requestData['completeRequestDt'] = '';
                }

                if(!empty($requestData['procManagerSno'])){
                    $managerInfo = DBUtil2::getOne(DB_MANAGER, 'sno', $requestData['procManagerSno']);
                    $requestData['procManagerName'] = $managerInfo['managerNm'];
                }

                $workRequestList[$key] = $requestData;
            }
        }

        return $workRequestList;
    }

    /**
     * 샘플실 맵
     */
    function getSampleFactoryMap(){
        $list = DBUtil2::getList('sl_workSampleFactory', '1', '1');
        return SlCommonUtil::arrayAppKey($list, 'sno');
    }

    /**
     * 샘플실 정보
     * @param $sno
     * @return mixed
     */
    function getSampleFactoryDataBySno($sno){
        return DBUtil2::getOne('sl_workSampleFactory', 'sno', $sno);
    }

    /**
     * 스타일 데이터
     * @param $styleType
     * @return mixed
     */
    function getSpecDBData($styleType){
        return DBUtil2::getOne('sl_workStyleData', 'styleType', $styleType);
    }

    /**
     * 스타일 별 스펙 데이터 반환
     * @param $styleType
     * @return mixed
     */
    function getSpecData($styleType){
        $styleData = $this->getSpecDBData($styleType);
        return json_decode($styleData['specCheck'],true);
    }

    function splitStyleData($type, $key){
        $data = $this->getSpecDBData($type);
        $result = [];
        $divStep1 = explode(',', preg_replace('/\r\n|\r|\n/',',',$data[$key]));
        foreach($divStep1 as $eachKey => $eachValue){
            if( !empty($eachValue) ){
                $result[$eachKey] = explode(',', preg_replace('/\t/',',',$eachValue));
            }
        }
        return $result;
    }

    /**
     * 스타일 별 기준 스펙을 불러온다.
     * 엑셀표(사이즈스펙)에서 복붙하기
     * @param $type
     * @return array
     */
    public function getGuideSpec($type) {
        $result = [];
        $styleData = $this->getStyle(['sno'=>$type])[0];
        foreach( $styleData['specCheckInfo'] as $eachKey => $eachValue){

            $result[] = $eachValue['specGuide'];
        }
        return $result;
    }

    /**
     * 스타일 별 체크리스트 반환
     * @param $type
     * @return array
     */
    public function getCheckList($type) {
        $refineCheckData = [];
        $checkData = $this->getStyle(['sno'=>$type])[0];
        foreach( $checkData['checkList'] as $value ){
            $refineData = [];
            $refineData['check1'] = $value['checkItem'];
            $refineData['check2'] = '';
            $refineData['check3'] = $value['checkEtc'];
            $refineCheckData[] = $refineData;
        }
        return $refineCheckData;
    }

    /**
     * 스타일명 반환
     * @return array
     */
    public function getStyleNameListMap(){
        $styleList = $this->getStyle();
        return SlCommonUtil::arrayAppKeyValue($styleList, 'sno', 'styleName');
    }

    /**
     * 스타일 데이터 가져오기
     * @param $param
     * @return array
     */
    public function getStyle($param=null){

        $searchVo = new SearchVo();
        if( !empty($param['styleName']) ){
            $searchVo->setWhere(DBUtil2::bind('styleName', DBUtil2::BOTH_LIKE));
            $searchVo->setWhereValue($param['styleName']);
        }
        if( !empty($param['season']) ){
            $searchVo->setWhere('season=?');
            $searchVo->setWhereValue($param['season']);
        }
        if( !empty($param['sno']) ){
            $searchVo->setWhere('sno=?');
            $searchVo->setWhereValue($param['sno']);
        }

        $list = DBUtil2::getListBySearchVo('sl_workStyle', $searchVo);

        foreach($list as $key => $value){
            $value['specCheckInfo'] = json_decode($value['specCheckInfo'], true);
            $value['checkList'] = json_decode($value['checkList'], true);
            $specCheckList = [];
            foreach( $value['specCheckInfo'] as $specValue){
                $specCheckList[] = $specValue['specItemName'];
            }
            $value['specCheckList'] = implode(', ', $specCheckList);
            $value['seasonKr'] = WorkCodeMap::SEASON[$value['season']];
            $list[$key] = $value;
        }
        return $list;
    }


    /**
     * SMS 발송
     * @param $msgCode
     * @param $contentParam
     * @return mixed|string
     */
    public static function getWorkSmsMsg($msgCode, $contentParam){
        $customerMsgPrefix = "안녕하세요. 엠에스이노버입니다.\r\n".$contentParam['documentName'];
        $customerMsgSuffix = "\r\n바로가기 : {$contentParam['targetUrl']} \r\n감사합니다.";
        $msg[0] = $contentParam['documentName'] . ' 승인 요청 - (요청자: '. $contentParam['writerName'] . ') 바로가기 : ' . $contentParam['targetUrl'] ;
        $msg[1] = $contentParam['documentName']. ' ' . $contentParam['statusKr'] . '됨 -  바로가기 : ' . $contentParam['targetUrl'] ;
        $msg[2] = "{$customerMsgPrefix} 문서를 담당자 메일로 발송하였습니다. 아래 바로가기 URL을 통해서도 확인 가능합니다. {$customerMsgSuffix}"; //문서 메일 발송
        $msg[3] = "{$contentParam['documentName']}에 고객 요청이 있습니다."; //관리자에게 고객 코멘트 알림
        $msg[4] = "{$customerMsgPrefix} 문서에 담당자가 코멘트를 남겼습니다.{$customerMsgSuffix}"; //고객에게 관리자 코멘트 알림
        return $msg[$msgCode];
    }

    /**
     * 관리자에게 SMS발송
     * @param $msgCode
     * @param $contentParam
     * @param $targetManagerSno
     */
    public static function sendWorkSmsToManager($msgCode, $contentParam, $targetManagerSno){
        $managerInfo = DBUtil2::getOne(DB_MANAGER, 'sno', $targetManagerSno);
        //SMS 전달.
        $receiverData = [];
        $receiverData[0]['memNo'] = '0';
        $receiverData[0]['memNm'] = $managerInfo['managerNm'];
        $receiverData[0]['smsFl'] = 'y';
        $receiverData[0]['cellPhone'] = $managerInfo['cellPhone'];
        if(!empty($receiverData[0]['cellPhone'])){
            $content = WorkService::getWorkSmsMsg($msgCode, $contentParam);
            SlSmsUtil::sendSms($content, $receiverData, 'sms');
        }
    }

    /**
     * 고객에게 메세지 발송
     * @param $msgCode
     * @param $contentParam
     * @throws Exception
     */
    public static function sendWorkSmsToCustomer($msgCode, $contentParam){
        $cellPhone = $contentParam['cellPhone'];
        if( SlCommonUtil::isValidCellPhone($cellPhone) ){
            $contentParam['targetUrl'] = SlCommonUtil::getShortUrl(urlencode($contentParam['link']));
            $content = WorkService::getWorkSmsMsg($msgCode,$contentParam);
            $memberList[] = [
                'memNo' => 0,
                'memName' => 'customer',
                'smsFl' => 'y',
                'cellPhone' => $cellPhone,
            ];
            SlSmsUtil::sendSms($content, $memberList, 'lms');
        }
    }

}
