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
namespace Controller\Front\Member;

use SiteLabUtil\SlCommonUtil;
use SlComponent\Util\SitelabLogger;
use SlComponent\Util\SlCodeMap;
use SlComponent\Util\SlSkinUtil;
use Component\Member\Util\MemberUtil;

/**
 * 사이트 접속 페이지
 *
 * @author Jong-tae Ahn <qnibus@godo.co.kr>
 */
class AutoLoginController extends \Controller\Front\Controller
{
    /**
     * {@inheritdoc}
     */
    public function index()
    {
        $memId = \Request::get()->get('loginMemId');
        if( !empty($memId) ){
            $member = \App::load('\\Component\\Member\\Member');
            $memberWithGroup = $this->selectMemberWithGroup($memId, 'memId');
            $loginLimit = json_decode($memberWithGroup['loginLimit'], true);
            $memberWithGroup['loginLimit'] = $loginLimit;

            $encryptMember = MemberUtil::encryptMember($memberWithGroup);
            $member->saveLoginLog($encryptMember['memNo']);
            $member->refreshMemberByLogin($encryptMember['memNo'], $encryptMember['loginCnt']);
            $member->refreshBasket($encryptMember['memNo']);
            // 모듈 설정
            $cart = \App::load('Component\\Cart\\Cart');
            $cart->setMergeCart($encryptMember['memNo']);
            $member->setSessionByLogin($encryptMember);
            //exit();
            //$member->login($memId, $memPw);
        }
        $this->redirect('/');
    }

    public function selectMemberWithGroup($value, $column)
    {
        $db = \App::getInstance('DB');
        $arrBind = [];
        $db->strField = 'm.memNo, m.memId, m.memPw, m.groupSno, m.memNm, m.nickNm, m.appFl, m.sleepFl, m.maillingFl, m.smsFl, m.saleCnt, m.saleAmt, m.mallSno';
        $db->strField .= ', m.cellPhone, m.email, m.adultConfirmDt, m.adultFl, m.loginCnt, m.changePasswordDt, m.guidePasswordDt, m.loginLimit, m.zonecode, m.mileage, m.memo as mMemo, m.birthDt as mBirthDt';
        $db->strField .= ', m.modDt AS mModDt, m.regDt AS mRegDt, m.lastSaleDt as mLastSaleDt, m.lastLoginDt as mLastLoginDt, ms.snsJoinFl, IF(ms.connectFl=\'y\', ms.snsTypeFl, \'\') AS snsTypeFl, ms.connectFl, ms.accessToken';
        $db->strField .= ', mg.groupNm, mg.groupSort, m.modDt AS memberModDt, m.regDt AS memberRegDt';
        $db->strJoin = ' LEFT JOIN ' . DB_MEMBER_GROUP . ' AS mg ON m.groupSno = mg.sno';
        $db->strJoin .= ' LEFT JOIN ' . DB_MEMBER_SNS . ' AS ms ON ms.memNo = m.memNo';
        $db->strWhere = 'm.' . $column . " = '{$value}'";
        //$db->bind_param_push($arrBind, $this->fields[$column], $value);
        $query = $db->query_complete();
        $strSQL = 'SELECT ' . array_shift($query) . ' FROM ' . DB_MEMBER . ' AS m ' . implode(' ', $query);
        return $db->query_fetch($strSQL, $arrBind, false);
    }

}
