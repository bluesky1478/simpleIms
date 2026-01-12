<?php
namespace SlComponent\Godo;

use App;
use SlComponent\Database\DBUtil;
use SlComponent\Database\DBUtil2;
use SlComponent\Database\SearchVo;
use SlComponent\Util\ExcelCsvUtil;
use SlComponent\Util\PhpExcelUtil;
use SlComponent\Util\SitelabLogger;
use SlComponent\Util\SlCodeMap;
use Framework\Security\Digester;
use Framework\Utility\GodoUtils;

/**
 * 회원(비회원)관련 서비스
 * ★ 순환 참조 가능성 조심! ★
 * Class SlCode
 * @package SlComponent\Godo
 */
class MemberService {
    private $sql;

    public function __construct(){
        //$this->sql = \App::load(\SlComponent\Godo\Sql\MemberSql::class);
    }

    public function addMember($files, $scm){

        $FIELD_NAME = 1;

        $startRowCnt = 1;
        $result = ExcelCsvUtil::checkAndRead($files,$startRowCnt);

        if( empty($result['isOk'])  ){
            throw new \Exception($result['failMsg']);
        }

        $sheetData = $result['data']->sheets[0];
        $sheetData = $sheetData['cells'];
        $fieldDataList = array();

        $fieldDataList[1] = [ '아이디' , 'memId' ] ;
        $fieldDataList[2] = [ '이름' , 'memNm' ] ;
        $fieldDataList[3] = [ '닉네임' , 'nickNm' ] ;
        $fieldDataList[4] = [ '핸드폰' , 'cellPhone' ] ;
        $fieldDataList[5] = [ '우편번호' , 'zonecode' ] ;
        $fieldDataList[6] = [ '주소' , 'address' ] ;
        $fieldDataList[7] = [ '주소상세' , 'addressSub' ] ;
        $fieldDataList[8] = [ '이메일' , 'email' ] ;
        $fieldDataList[9] = [ '그룹번호' , 'groupSno' ] ;
        $fieldDataList[10] = [ '직원타입' , 'memberType' ] ;
        $fieldDataList[11] = [ '팀이름' , 'teamName' ] ;
        $fieldDataList[12] = [ '팀대표' , 'repFl' ] ;
        $fieldDataList[13] = [ '구매수량제한' , 'buyLimitCount' ];

        foreach( $sheetData as $idx => $data ){
            if( ($startRowCnt+1) > $idx ) continue;

            $memberData = DBUtil2::getOne(DB_MEMBER, 'memId', $data[1]);
            if( !empty($memberData) ){
                continue;
            }

            //저장 데이터 설정
            //if( 'TKE(티센크루프)' == $scm ){
            if( '한국타이어' == $scm ){
                $memberDefaultData['memPw'] = '$2y$06$MgTcC2H6gw6MotqYrA0HbO9mxcqQscl4cPDVCMRERKl5T0UiIbWee'; //hk12341234!!
                //$memberDefaultData['memPw'] = '$2y$06$dS1UwzD9xm6yZLObRCZbRuaHHhJ1KMJ6yvqPXCxhSh7ujjLg5oTxu'; //tkek1234!!
            }else {

                $memberDefaultData['memPw'] = 'NzJkMmM1OGY5OTlmMGM3NSQSrwWXfHqUcDBu/ycQN+mPZHaQasUYQF5FUdH8zyKe'; //현대엘리베이터 hdel123!

                //$memberDefaultData['memPw'] = 'ZjQ2MTFkOWVkMWNiZWMxMokvtp7zjN7OWsQ295EM/ybdLUnnA/UoRZfrBqKbI/a6'; //asiana8500
                //$memberDefaultData['memPw'] = 'MzQ3NzJjNGQ3NDczMzUxMNn6+79TE19w6tfiUhfnMhbGfehYXVpNppYeo15dVMne'; //bando1234
                //$memberDefaultData['memPw'] = 'ODI2NGJkYjA4MTYwOTI0NNw3uq21m72aSo3EYCZAo1+h9YKkpJJD1OH0VhTy7Ixc'; //fod1234!@#$
                //$memberDefaultData['memPw'] = 'N2NlMTA3OWE3ZThhMmY3Y3Vajo+xeBSmvypIvdXWAQGXrMDTIw+uXrUNZw9bHk7V'; //fursys1234!@#$ (fursys1234!@#$)
                //$memberDefaultData['memPw'] = 'ODY5YzRiZjNhMzFlZDlhMIEximN0lVbsGP4n4seNeLnJvUFg5515MYRrJkohfUzj'; ////tata123456* ( ODY5YzRiZjNhMzFlZDlhMIEximN0lVbsGP4n4seNeLnJvUFg5515MYRrJkohfUzj )
                //$memberDefaultData['memPw'] = 'MTk3MTVlZjI4ODc4M2NlZKz+E3lGMxzonvK0FHfA3LaVov1jLkuZN3JMpZOD+L3V'; //현대 hd12345* => MTk3MTVlZjI4ODc4M2NlZKz+E3lGMxzonvK0FHfA3LaVov1jLkuZN3JMpZOD+L3V

                //$memberDefaultData['memPw'] = 'ODI2NGJkYjA4MTYwOTI0NNw3uq21m72aSo3EYCZAo1+h9YKkpJJD1OH0VhTy7Ixc'; //fod1234!@#$
                //$memberDefaultData['memPw'] = '$2y$06$mubNAInRIAFuzJCJxtA9t.Bmav6JRx5SyYpiTR9CDn/8BKTmz.JH6'; //inno15770327
                //$memberDefaultData['memPw'] = 'YmY5MDQ4ODVmNzlkYWM1ZJnYZjNN2BF/TvL+d0lVRupQoj6ZamDqlQE7ZvMAXyZq';
                //otis1234!@#$
                //NWNkOWUxMzAwZGNjNDgzZpbK6N6Woy8KcQ1oZGQP3eqQC+z2f9b/6nMc/9gg8P2M
                //fod1234!@#$
                //ODI2NGJkYjA4MTYwOTI0NNw3uq21m72aSo3EYCZAo1+h9YKkpJJD1OH0VhTy7Ixc
            }

            $memberDefaultData['adminMemo'] = date('YmdHis');
            //$memberDefaultData['zipCode'] = '0';
            //$memberDefaultData['zoneCode'] = '0';
            //$memberDefaultData['addressSub'] = '-';

            $memberDefaultData['appFl'] = 'y';
            $memberDefaultData['approvalDt'] = date('Y-m-d H:i:s');
            $memberDefaultData['entryDt'] = date('Y-m-d H:i:s');
            //$memberDefaultData['groupSno'] = 7;
            $memberDefaultData['ex1'] = $scm;
            //$memberDefaultData['hankookType'] = '10';

            $setConfig = [];

            foreach( $fieldDataList as $key => $value ){

                //6주소
                if( 7 !== $key ) $data[$key] = str_replace(' ','',$data[$key]); //주소지는 치환하지 않음

                //직원타입 , 구매수량 제한
                if( $key >= 10 ){
                    if(isset( $data[$key] )){
                        $setConfig[$value[$FIELD_NAME]] = $data[$key];
                    }
                }else{
                    if(isset( $data[$key] )){
                        $memberDefaultData[$value[$FIELD_NAME]] = $data[$key];
                    }
                }
            }
            //SitelabLogger::logger('저장 데이터 ');
            //SitelabLogger::logger($memberDefaultData);
            if( empty(  DBUtil2::getOne(DB_MEMBER, 'memId', $memberDefaultData['memId']  )  )  ){
                $memNo = DBUtil2::insert(DB_MEMBER, $memberDefaultData);
            }

            //SitelabLogger::logger($memNo);

            if( count($setConfig) > 0 ){
                $setConfig['memNo'] = $memNo;
                DBUtil2::insert('sl_setMemberConfig', $setConfig);
            }

        }

    }


    /**
     * TKE 공급사에서 회원 유형 (정규/파트너) 지정 및 구매수량 수정
     * @param $files
     * @param $scm
     * @throws \Exception
     */
    public function scmModifyBatchMember($files, $scm){
        $startRowCnt = 2;
        $result = ExcelCsvUtil::checkAndRead($files,$startRowCnt);

        if( empty($result['isOk'])  ){
            throw new \Exception($result['failMsg']);
        }

        $sheetData = $result['data']->sheets[0];
        $sheetData = $sheetData['cells'];

        /*$fieldDataList[2] = [ '아이디' , 'memId' ] ;
        $fieldDataList[8] = [ '직원타입' , 'memberType' ] ;
        $fieldDataList[9] = [ '구매수량제한' , 'buyLimitCount' ];*/

        foreach( $sheetData as $idx => $data ){
            if( ($startRowCnt+1) > $idx ) continue;

            $memberData = DBUtil2::getOne(DB_MEMBER, 'memId', $data[2]);
            $memberSetData = DBUtil2::getOne('sl_setMemberConfig', 'memNo', $memberData['memNo']);
            $saveData['memNo'] = $memberData['memNo'];
            $saveData['memberType'] = ( '파트너사' == $data[8] )?2:1;
            $saveData['buyLimitCount'] = $data[9];

            if( empty($memberSetData) ){
                DBUtil2::insert('sl_setMemberConfig', $saveData );
            }else{
                DBUtil2::update('sl_setMemberConfig', $saveData , new SearchVo('sno=?' ,  $memberSetData['sno'] ));
            }
        }

    }

    /**
     * 한국타이어 관리자 여부
     * @param $id
     * @return bool
     */
    public function isHankookManager($id){
        if( in_array($id, SlCodeMap::HANKOOK_MANAGER_ID) ){
            return true;    
        }else{
            return false;       
        }
    }

    public function isHyundaeManager($id){
        if( in_array($id, SlCodeMap::HYUNDAE_MANAGER_ID) ){
            return true;
        }else{
            return false;
        }
    }

    /**
     * TKE 관리자 여부
     * @param $id
     * @return bool
     */
    public function isTkeManager($id){
        if( in_array($id, SlCodeMap::TKE_MANAGER_ID) ){
            return true;
        }else{
            return false;
        }
    }

    /**
     * OEK 관리자 여부
     * @param $id
     * @return bool
     */
    public function isOekManager($id){
        if( in_array($id, SlCodeMap::OEK_MANAGER_ID) ){
            return true;
        }else{
            return false;
        }
    }




}
