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
namespace Controller\Admin\Provider\Statistics;

use App;
use Component\Member\Group\Util as GroupUtil;
use Exception;
use Framework\Debug\Exception\LayerException;
use Logger;
use Message;
use Request;
use SlComponent\Database\DBUtil;
use SlComponent\Database\DBUtil2;
use SlComponent\Database\SearchVo;
use SlComponent\Util\SitelabLogger;
use SlComponent\Util\SlLoader;

/**
 * Class 회원일괄 처리
 * @package Bundle\Controller\Admin\Member
 * @author  yjwee
 */
class ScmMemberBatchPsController extends \Controller\Admin\Controller
{
    public function index()
    {

        /**
         * @var  \Bundle\Component\Member\MemberAdmin $admin
         * @var  \Bundle\Component\Mileage\Mileage    $mileage
         * @var  \Bundle\Component\Deposit\Deposit    $deposit
         */
        $admin = App::load('\\Component\\Member\\MemberAdmin');
        $mileage = App::load('\\Component\\Mileage\\Mileage');
        $deposit = App::load('\\Component\\Deposit\\Deposit');
        try {
            $postData = Request::request()->toArray();
            $mode = Request::request()->get('mode');
            $post = Request::request()->toArray();
            $searchJson = Request::request()->get('searchJson');
            $memberNo = Request::request()->get("chk");
            $groupSno = Request::request()->get('newGroupSno');

            switch ($mode) {
                case 'scm_modify_batch_member' :
                    $filesValue = Request::files()->toArray();
                    $memberService=SlLoader::cLoad('godo','memberService','sl');
                    $memberService->scmModifyBatchMember($filesValue, 8); //TKE ONLY
                    $this->layer(__('수정 완료!'), null, 2000);
                    break;
                case 'tke_change' :
                    $updateData['memberType']  = $postData['memberType'];
                    $updateData['buyLimitCount']  = $postData['buyLimitCount'];
                    $memberSetData = DBUtil2::getOne('sl_setMemberConfig', 'memNo', $postData['memNo']);
                    if( empty($memberSetData) ){
                        $updateData['memNo']  = $postData['memNo'];
                        //SitelabLogger::logger('저장');
                        //SitelabLogger::logger($updateData);
                        DBUtil2::insert('sl_setMemberConfig', $updateData );
                    }else{
                        //SitelabLogger::logger('저장-업데이트');
                        //SitelabLogger::logger($updateData);
                        DBUtil2::update('sl_setMemberConfig', $updateData , new SearchVo('memNo=?' ,  $postData['memNo'] ));
                    }
                    $this->layer(__('수정 완료!'), null, 2000);
                    break;
                case 'hankookChange':
                    $updateData['hankookType']  = $postData['hankookType'];
                    DBUtil::update(DB_MEMBER,$updateData, new SearchVo('memNo=?', $postData['memNo']) );
                    $this->json('처리완료');
                    break;
                case 'member_not_free' :
                    foreach( $postData['chk'] as $memNo  ){
                        DBUtil::update(DB_MEMBER,['freeFl' => 'n'], new SearchVo('memNo=?', $memNo) );
                    }
                    $this->json('처리완료');
                    break;
                case 'member_free' :
                    foreach( $postData['chk'] as $memNo  ){
                        DBUtil::update(DB_MEMBER,['freeFl' => 'y'], new SearchVo('memNo=?', $memNo) );
                    }
                    $this->json('처리완료');
                    break;
                case 'nickChange':
                    $updateData['nickNm']  = $postData['nick'];
                    DBUtil::update(DB_MEMBER,$updateData, new SearchVo('memNo=?', $postData['memNo']) );
                    $this->json('처리완료');
                    break;
                case 'pw_reset':
                    //TODO : 추후 DB저장 설정 (암호 초기화)
                    if( 4 == $postData['scmNo']  ){
                        $pw = '$2y$06$TqF3bw7GB2L6d4JQILfSyurQ9ce2dpIKMWJsCjyeQQEIHpn9kgMGC'; //jeil123456
                    }else{
                        $pw = '$2y$06$mubNAInRIAFuzJCJxtA9t.Bmav6JRx5SyYpiTR9CDn/8BKTmz.JH6'; //inno15770327
                    }
                    $updateData['memPw']  = $pw;
                    DBUtil::update(DB_MEMBER,$updateData, new SearchVo('memNo=?', $postData['memNo']) );
                    $this->json('처리완료');
                    break;
                case 'add_deposit':
                    $result = $deposit->addDeposit($post);
                    $this->json(
                        [
                            $result,
                        ]
                    );
                    break;
                case 'add_deposit_all':
                    $result = $deposit->addDepositAll($post, $searchJson);
                    $this->json(
                        [
                            $result,
                        ]
                    );
                    break;
                case 'remove_deposit':
                    $result = $deposit->removeDeposit($post);
                    $resultStorage = $deposit->getResultStorage();
                    $this->json(
                        [
                            $result,
                            $resultStorage->toArray(),
                        ]
                    );
                    break;
                case 'remove_deposit_all':
                    $result = $deposit->removeDepositAll($post, $searchJson);
                    $resultStorage = $deposit->getResultStorage();
                    $this->json(
                        [
                            $result,
                            $resultStorage->toArray(),
                        ]
                    );
                    break;
                case 'add_mileage':
                    $result = $mileage->addMileage($post);
                    $this->json(
                        [
                            $result,
                        ]
                    );
                    break;
                case 'all_add_mileage':
                    $result = $mileage->allAddMileage($post, $searchJson);
                    $this->json(
                        [
                            $result,
                        ]
                    );
                    break;
                case 'remove_mileage':
                    $result = $mileage->removeMileage($post);
                    $resultStorage = $mileage->getResultStorage();
                    \Logger::debug(__METHOD__, $resultStorage);
                    $this->json(
                        [
                            $result,
                            $resultStorage->toArray(),
                        ]
                    );
                    break;
                case 'all_remove_mileage':
                    $result = $mileage->allRemoveMileage($post, $searchJson);
                    $resultStorage = $mileage->getResultStorage();
                    $this->json(
                        [
                            $result,
                            $resultStorage->toArray(),
                        ]
                    );
                    break;

                case 'reasonCd_modify_mileage':
                    $result = $mileage->reasonCdModifyMileage($post);
                    $this->json(
                        [
                            $result,
                        ]
                    );
                    break;
                case 'reasonCd_modify_deposit':
                    $result = $deposit->reasonCdModifyDeposit($post);
                    $this->json(
                        [
                            $result,
                        ]
                    );
                    break;
                case 'apply_group_grade':
                    $passwordCheckFl = gd_isset(Request::post()->get('passwordCheckFl'), 'y');
                    if($passwordCheckFl != 'n') {
                        $smsSender = \App::load(\Component\Sms\SmsSender::class);
                        $smsSender->validPassword(Request::post()->get('password'));
                    }
                    $result = $admin->applyGroupGradeByMemberNo($groupSno, $memberNo);
                    $beforeMembers = $admin->getBeforeMembersByGroupBatch();
                    $members = $admin->getAfterMembersByGroupBatch();
                    $admin->writeGroupChangeHistory($beforeMembers, $members);
                    $admin->sendGroupChangeEmail($members);
                    $admin->sendGroupChangeSms($members, $passwordCheckFl);
                    $groups = GroupUtil::getGroupName();
                    $this->json(sprintf(__('%s명 중 %s명이 %s 등급으로 변경되었습니다.'), $result['total'], $result['success'], $groups[$result['groupSno']]));
                    break;
                case 'all_apply_group_grade':
                    $passwordCheckFl = gd_isset(Request::post()->get('passwordCheckFl'), 'y');
                    if($passwordCheckFl != 'n') {
                        $smsSender = \App::load(\Component\Sms\SmsSender::class);
                        $smsSender->validPassword(Request::post()->get('password'));
                    }
                    $result = $admin->allApplyGroupGradeByMemberNo($groupSno, $searchJson);
                    $beforeMembers = $admin->getBeforeMembersByGroupBatch();
                    $members = $admin->getAfterMembersByGroupBatch();
                    $admin->writeGroupChangeHistory($beforeMembers, $members);
                    $admin->sendGroupChangeEmail($members);
                    $admin->sendGroupChangeSms($members, $passwordCheckFl);
                    $groups = GroupUtil::getGroupName();
                    $this->json(sprintf(__('%s명 중 %s명이 %s 등급으로 변경되었습니다.'), $result['total'], $result['success'], $groups[$result['groupSno']]));
                    break;
                case 'approval_join':
                    $result = $admin->approvalJoinByMemberNo($memberNo);
                    $this->json(sprintf(__('%s명 중 %s명이 승인 상태로 변경되었습니다.'), $result['total'], $result['success']));
                    break;
                case 'disapproval_join':
                    $result = $admin->disapprovalJoinByMemberNo($memberNo);
                    $this->json(sprintf(__('%s명 중 %s명이 미승인 상태로 변경되었습니다.'), $result['total'], $result['success']));
                    break;
                case 'all_approval_join':
                    $result = $admin->allApprovalJoinByMemberNo($searchJson);
                    $this->json(sprintf(__('%s명 중 %s명이 승인 상태로 변경되었습니다.'), $result['total'], $result['success']));
                    break;
                case 'all_disapproval_join':
                    $result = $admin->allDisapprovalJoinByMemberNo($searchJson);
                    $this->json(sprintf(__('%s명 중 %s명이 미승인 상태로 변경되었습니다.'), $result['total'], $result['success']));
                    break;
                default:
                    throw new Exception(__('요청을 처리할 페이지를 찾을 수 없습니다.') . ', ' . $mode, 404);
                    break;
            }
        } catch (\Throwable $e) {
            \Logger::error(__METHOD__ . ', ' . $e->getFile() . '[' . $e->getLine() . '], ' . $e->getMessage(), $e->getTrace());
            if (Request::isAjax()) {
                $this->json($this->exceptionToArray($e));
            } else {
                throw new LayerException($e->getMessage(), $e->getCode(), $e);
            }
        }
    }
}
