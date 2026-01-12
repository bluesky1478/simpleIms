<?php

namespace Controller\Admin\Test;

use Framework\Utility\NumberUtils;
use Component\Database\DBTableField;
use Component\Sitelab\SiteLabSmsUtil;
use SiteLabUtil\SlCommonUtil;
use SlComponent\Database\DBUtil;
use SlComponent\Database\DBUtil2;
use SlComponent\Database\SearchVo;
use SlComponent\Database\TableVo;
use SlComponent\Util\SitelabLogger;
use Globals;
use Component\Deposit\Deposit;
use SlComponent\Util\SitelabUtil;
use SlComponent\Util\SlCode;
use SlComponent\Util\SlLoader;
use SlComponent\Util\SlSmsUtil;

/**
 * TKE 관리 페이지
 */
class TestTkeController extends \Controller\Admin\Controller{

    const MEMBER_BACKUP = 'stmp_member_';

    public function index(){
        gd_debug("== TKE 배송지 및 회원 처리 ==");
        $backupDate = date('ymd');
        gd_debug('처리일자 : '. $backupDate);

        //1. Data Backup
        //$this->setMemberAndDeliveryBackup($backupDate);

        //2. 배송지 정보 삭제. (이 후 수기 업로드할 것)
        //$rslt = DBUtil2::runSql("delete from sl_setScmDeliveryList where scmNo=8");
        //gd_debug("배송지 정보 삭제 (TKE만) : {$rslt}");

        //3. 회원 로우데이터 등록 -> Front Test에서 암호를 암호화  ( 이 후 로직 변경하기 addMemberTke )
        //'memId'=> 1, 'memNm'=> 2, 'nickNm'=> 3,'cellPhone'=> 4, 'zipcode'=> 5,
        //'address'=> 6, 'email'=> 7, 'groupName'=> 8, 'teamName'=> 9, 'teamRep'=> 10, 'buyLimitCount'=> 11,
        //회원아이디, 이름, 닉넴, 전화, 우편, 전화, 이멜, 정직원여부, 팀명, 팀장여부, 구매제한xxxx

        // ★ 회원 등급 잘 보기 !!! 1(정직)  5(NI), 6(SVC), 7(MFG)
        //4. 회원리스트에서 TKE 회원 로우 데이터 등록 (등록 후 암호 셋팅한 뒤 삭제 -> 최종 카피)
        //$rslt = DBUtil2::runSql("delete from es_member where ex1='TKE(티센크루프)' and groupSno IN (5,6,7) and memNo not in (4991,5469,4746,5639,5000,1)");
        //$rslt = DBUtil2::runSql("delete from es_member where ex1='TKE(티센크루프)' and groupSno IN (6) and memNo not in (4991,5469,4746,5639,5000,1)"); //SVC만 삭제
        //gd_debug("회원 정보 삭제 (파트너만 SVC) : {$rslt}");
        //$rslt = DBUtil2::runSql("delete from es_member where ex1='TKE(티센크루프)' and groupSno IN (5) and memNo not in (4991,5469,4746,5639,5000,1)"); //NI만 삭제
        //gd_debug("회원 정보 삭제 (파트너만 NI) : {$rslt}");
        //$rslt = DBUtil2::runSql("delete from es_member where ex1='TKE(티센크루프)' and groupSno IN (1,2) and memNo not in (4991,5469,4746,5639,5000,1)");
        ///gd_debug("회원 정보 삭제 (TKE만) : {$rslt}");

        $tkeService = SlLoader::cLoad('scm','scmTkeService');
        //$tkeService->saveMemberTke();

        gd_debug("완료");
        exit();
    }

    //데이터 백업
    public function setMemberAndDeliveryBackup($backupDate){
        $sql = "create table stmp_setScmDeliveryList_{$backupDate} select * from sl_setScmDeliveryList";
        $rslt = DBUtil2::runSql($sql);
        gd_debug("배송지 백업 : {$rslt}");

        $sql = "create table stmp_member_{$backupDate} select * from es_member";
        $rslt = DBUtil2::runSql($sql);
        gd_debug("회원 정보 백업 : {$rslt}");

        $sql = "create table stmp_setMemberConfig_{$backupDate} select * from sl_setMemberConfig";
        $rslt = DBUtil2::runSql($sql);
        gd_debug("구매제한 백업 : {$rslt}");
    }

    //회원 백업테이블 가져오기
    public function getMemberBackupTable($backupDate){
        return self::MEMBER_BACKUP.$backupDate;
    }

}