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
 * @link http://www.godo.co.kr
 */
namespace Controller\Admin\Member;
use App;
use Component\Storage\Storage;
use Framework\Debug\Exception\LayerException;
use Framework\Debug\Exception\LayerNotReloadException;
use Exception;
use Message;
use Request;
use Session;
use SlComponent\Database\DBUtil;
use SlComponent\Database\DBUtil2;
use SlComponent\Util\SitelabLogger;
use SlComponent\Util\SlCommonUtil;
use SlComponent\Util\SlLoader;

class CustomMemberSleepPsController extends \Controller\Admin\Controller
{
    public function index(){
        // --- 각 배열을 trim 처리
        $postValue = Request::post()->toArray();
        switch ($postValue['mode']) {
            case 'wake_member_force' :
                $memNo = DBUtil2::getOne(DB_MEMBER_SLEEP,'sleepNo',$postValue['sleepNo'])['memNo'];
                $sql = "update es_member a join es_memberSleep b on a.memNo = b.memNo
                    set a.memNm = b.memNm
                    , a.mileage = b.mileage
                    , a.deposit = b.deposit
                    , a.groupSno = b.groupSno
                    , a.email = b.email
                    , a.cellPhone = b.cellPhone
                    , a.phone = b.phone
                    , a.entryDt = b.entryDt
                    , a.sleepFl = 'n'
                    where a.memNo = {$memNo}";
                DBUtil2::runSql($sql);
                $sql = "delete from es_memberSleep where memNo = {$memNo}";
                DBUtil2::runSql($sql);
                $this->json("처리 하였습니다.");
                break;
        }
        exit();
    }

}