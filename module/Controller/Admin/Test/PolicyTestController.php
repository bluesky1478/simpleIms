<?php

namespace Controller\Admin\Test;

use Bundle\Component\Apple\AppleLogin;
use Bundle\Component\Member\Member;
use Component\Database\DBTableField;
use SlComponent\Database\DBUtil;
use SlComponent\Database\SearchVo;
use SlComponent\Database\TableVo;
use SlComponent\Util\SitelabLogger;
use Globals;
use Component\Deposit\Deposit;
use SlComponent\Util\SitelabUtil;
use SlComponent\Util\SlCode;
use SlComponent\Util\SlLoader;

/**
 * TEST 페이지
 */
class PolicyTestController extends \Controller\Admin\Controller{

    public function index(){
        gd_debug("== POLICY TEST ==");

        $orderNo = '2009051854434507';

        gd_debug( 'orderGoods' );
        $orderGoodsInfo = DBUtil::getList(DB_ORDER_GOODS, 'orderNo' , $orderNo);

        gd_debug($orderGoodsInfo);

        gd_debug("== 완료 ==");
        exit();
    }


    public function join($params)
    {
        $session = \App::getInstance('session');
        $globals = \App::getInstance('globals');
        $logger = \App::getInstance('logger');

        if (isset($params['birthYear']) === true && isset($params['birthMonth']) === true && isset($params['birthDay']) === true) {
            $params['birthDt'] = $params['birthYear'].'-'.$params['birthMonth'].'-'.$params['birthDay'];
        }
        if (isset($params['marriYear']) === true && isset($params['marriMonth']) === true && isset($params['marriDay']) === true) {
            $params['marriDate'] = $params['marriYear'].'-'.$params['marriMonth'].'-'.$params['marriDay'];
        }

        // xss 보안 취약점 개선
        if ($params['memberFl'] == 'business') {
            if (Validator::required($params['company']) === false) {
                throw new Exception(__('회사명을 입력하세요.'));
            }

            if (Validator::required($params['busiNo']) === false) {
                throw new Exception(__('사업자번호를 입력하세요.'));
            }
        }

        $vo = $params;
        if (is_array($params)) {
            DBTableField::setDefaultData($this->tableFunctionName, $params);
            $vo = new \Component\Member\MemberVO($params);
        }

        $vo->databaseFormat();
        $vo->setEntryDt(date('Y-m-d H:i:s'));
        $vo->setGroupSno(GroupUtil::getDefaultGroupSno());

        $v = new Validator();
        $v->init();
        $v->add('agreementInfoFl', 'yn', true, '{' . __('이용약관') . '}'); // 이용약관
        $v->add('privateApprovalFl', 'yn', true, '{' . __('개인정보 수집.이용 동의 필수사항') . '}'); // 개인정보동의 이용자 동의사항
        $v->add('privateApprovalOptionFl', '', false, '{' . __('개인정보 수집.이용 동의 선택사항') . '}'); // 개인정보동의 이용자 동의사항
        $v->add('privateOfferFl', '', false, '{' . __('개인정보동의 제3자 제공') . '}'); // 개인정보동의 제3자 제공
        $v->add('privateConsignFl', '', false, '{' . __('개인정보동의 취급업무 위탁') . '}'); // 개인정보동의 취급업무 위탁
        $v->add('foreigner', '', false, '{' . __('내외국인구분') . '}'); // 내외국인구분
        $v->add('dupeinfo', '', false, '{' . __('본인확인 중복가입확인정보') . '}'); // 본인확인 중복가입확인정보
        $v->add('pakey', '', false, '{' . __('본인확인 번호') . '}'); // 본인확인 번호
        $v->add('rncheck', '', false, '{' . __('본인확인방법') . '}'); // 본인확인방법
        $v->add('under14ConsentFl', 'yn', true, '{' . __('만 14세 이상 동의') . '}'); // 만 14세 이상 동의

        $joinSession = new SimpleStorage($session->get(Member::SESSION_JOIN_INFO));
        $session->del(Member::SESSION_JOIN_INFO);
        $vo->setPrivateApprovalFl($joinSession->get('privateApprovalFl'));
        $vo->setPrivateApprovalOptionFl(json_encode($joinSession->get('privateApprovalOptionFl'), JSON_UNESCAPED_SLASHES));
        $vo->setPrivateOfferFl(json_encode($joinSession->get('privateOfferFl'), JSON_UNESCAPED_SLASHES));
        $vo->setPrivateConsignFl(json_encode($joinSession->get('privateConsignFl'), JSON_UNESCAPED_SLASHES));
        $vo->setForeigner($joinSession->get('foreigner'));
        $vo->setDupeinfo($joinSession->get('dupeinfo'));
        $vo->setPakey($joinSession->get('pakey'));
        $vo->setRncheck($joinSession->get('rncheck'));
        $vo->setUnder14ConsentFl($joinSession->get('under14ConsentFl'));
        $toArray = $vo->toArray();
        if ($v->act($toArray) === false) {
            $logger->warning(implode("\n", $v->errors));
            throw new Exception(implode("\n", $v->errors));
        }

        $hasPaycoUserProfile = $session->has(GodoPaycoServerApi::SESSION_USER_PROFILE);
        $hasNaverUserProfile = $session->has(GodoNaverServerApi::SESSION_USER_PROFILE);
        $hasThirdPartyProfile = $session->has(Facebook::SESSION_USER_PROFILE);
        $hasKakaoUserProfile = $session->has(GodoKakaoServerApi::SESSION_USER_PROFILE);
        $hasWonderUserProfile = $session->has(GodoWonderServerApi::SESSION_USER_PROFILE);
        $hasAppleUserProfile = $session->has(AppleLogin::SESSION_USER_PROFILE);
        $passValidation = $hasPaycoUserProfile || $hasNaverUserProfile || $hasThirdPartyProfile || $hasKakaoUserProfile
            || $hasWonderUserProfile || $hasAppleUserProfile;
        \Component\Member\MemberValidation::validateMemberByInsert($vo, null, $passValidation);

        $authCellPhonePolicy = new SimpleStorage(gd_get_auth_cellphone_info());
        $ipinPolicy = new SimpleStorage(ComponentUtils::getPolicy('member.ipin'));

        //SNS 회원 가입을 진행중이고
        //본인 인증을 노출하지 않을 경우 아이핀/휴대폰 본인인증의 상태값을 미사용(n)으로 변경함.
        if ($passValidation === true && \Component\Member\MemberValidation::checkSNSMemberAuth() === 'n') {
            $ipinPolicy->set('useFl', 'n');
            $authCellPhonePolicy->set('useFl', 'n');
        }

        // 휴대폰인증시 저장된 세션정보와 실제 넘어온 파라미터 검증 (생년월일) - XSS 취약점 개선요청
        if ($authCellPhonePolicy->get('useFl', 'n') === 'y' && $session->has(Member::SESSION_DREAM_SECURITY)) {
            $dreamSession = new SimpleStorage($session->get(Member::SESSION_DREAM_SECURITY));

            $joinItem = gd_policy('member.joinitem');
            if ($joinItem['birthDt']['use'] === 'y' && $dreamSession->get('ibirth') != str_replace('-','', $vo->getBirthDt())) {
                throw new Exception(__("휴대폰 인증시 입력한 생년월일과 동일하지 않습니다."));
            }

            if ($joinItem['cellPhone']['use'] === 'y' && $dreamSession->get('phone') != str_replace('-','', $vo->getCellPhone())) {
                throw new Exception(__("휴대폰 인증시 입력한 번호와 동일하지 않습니다."));
            }

            if ($dreamSession->get('name') != $vo->getMemNm()) {
                throw new Exception(__("휴대폰 인증시 입력한 이름과 동일하지 않습니다."));
            }
        }

        if ($hasWonderUserProfile === false && $authCellPhonePolicy->get('useFl', 'n') === 'y' && $ipinPolicy->get('useFl', 'n') === 'n'&& !$session->has('simpleJoin')) {
            if (!$session->has(Member::SESSION_DREAM_SECURITY)) {
                $logger->info('Cellphone need identity verification.');
                throw new Exception(__('휴대폰 본인인증이 필요합니다.'));
            }
            $dreamSession = new SimpleStorage($session->get(Member::SESSION_DREAM_SECURITY));
            $session->del(Member::SESSION_DREAM_SECURITY);
            if (!Validator::required($dreamSession->get('DI'))) {
                $logger->info('Duplicate identification entry information does not exist.');
                throw new Exception(__('본인확인 중복가입정보가 없습니다.'));
            }
            if (!$vo->isset($vo->getDupeinfo())) {
                $vo->setDupeinfo($dreamSession->get('DI'));
            }
            if (!$vo->isset($vo->getBirthDt())) {
                $vo->setBirthDt($dreamSession->get('ibirth'));
            }
        }

        $member = $vo->toArray();
        if (empty($member['dupeinfo']) === false && MemberUtil::overlapDupeinfo($member['memId'], $member['dupeinfo'])) {
            $logger->info('Already members registered customers.');
            throw new Exception(__('이미 회원등록한 고객입니다.'));
        }
        if ($member['appFl'] == 'y') {
            $member['approvalDt'] = date('Y-m-d H:i:s');
        }

        $hasSessionGlobalMall = $session->has(SESSION_GLOBAL_MALL);
        $isUseGlobal = $globals->get('gGlobal.isUse', false);
        $logger->info(sprintf('has session global mall[%s], global use[%s]', $hasSessionGlobalMall, $isUseGlobal));
        if ($hasSessionGlobalMall && $isUseGlobal) {
            $mallSnoBySession = \Component\Mall\Mall::getSession('sno');
            $logger->info('has global mall session and has globals isUse. join member mallSno=' . $mallSnoBySession);
            $member['mallSno'] = $mallSnoBySession;
        } else {
            $logger->info('join member default mallSno');
            $member['mallSno'] = DEFAULT_MALL_NUMBER;
        }
        if ($hasPaycoUserProfile || $hasNaverUserProfile || $hasThirdPartyProfile || $hasKakaoUserProfile || $hasWonderUserProfile) {
            $memNo = $this->memberDAO->insertMemberByThirdParty($member);
            $member['memNo'] = $memNo;
        } else {
            $memNo = $this->memberDAO->insertMember($member);
            $member['memNo'] = $memNo;
        }
        if ($member['mallSno'] == DEFAULT_MALL_NUMBER) {
            $this->benefitJoin(new \Component\Member\MemberVO($member));
        } else {
            $logger->info(sprintf('can\'t benefit. your mall number is %d', $member['mallSno']));
        }
        $session->set(Member::SESSION_NEW_MEMBER, $member['memNo']);

        if ($vo->isset($member['cellPhone'])) {
            /** @var \Bundle\Component\Sms\SmsAuto $smsAuto */
            $aBasicInfo = gd_policy('basic.info');
            $aMemInfo = $this->getMemberId($memNo);
            $smsAuto = \App::load('\\Component\\Sms\\SmsAuto');
            $observer = new SmsAutoObserver();
            $observer->setSmsType(SmsAutoCode::MEMBER);
            $observer->setSmsAutoCodeType(Code::JOIN);
            $observer->setReceiver($member);
            $observer->setReplaceArguments(
                [
                    'name'      => $member['memNm'],
                    'memNm'     => $member['memNm'],
                    'memId'     => $member['memId'],
                    'appFl'     => $member['appFl'],
                    'groupNm'   => $aMemInfo['groupNm'],
                    'mileage'   => 0,
                    'deposit'   => 0,
                    'rc_mallNm' => Globals::get('gMall.mallNm'),
                    'shopUrl'   => $aBasicInfo['mallDomain'],
                ]
            );
            $smsAuto->attach($observer);
        }

        return new \Component\Member\MemberVO($member);
    }

}