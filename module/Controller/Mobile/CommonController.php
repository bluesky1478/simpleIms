<?php
namespace Controller\Mobile;

use Component\Member\Util\MemberUtil;
use SlComponent\Util\SlCodeMap;
use SlComponent\Util\SlLoader;
use SlComponent\Util\SlSkinUtil;

/**
 * Class pro 사용자들이 모든 컨트롤러에 공통으로 사용할 수 있는 컨트롤러
 * 컨트롤러 메소드들을 사용할 수 있습니다. http://doc.godomall5.godomall.com/Godomall5_Pro_Guide/Controller
 */
class CommonController
{
    public function index($controller)
    {
        $controller->setData('otherSkin', SlSkinUtil::getOtherSkinName());
        $controller->setData('memberScm', MemberUtil::getMemberScmNo());

        $otherSkin = SlSkinUtil::getOtherSkinName();
        $controller->setData('otherSkinCompName',  SlCodeMap::OTHER_SKIN_COMP_NAME[$otherSkin]);

        //회원 한국타이어 본사아이디 여부
        $memberService = SlLoader::cLoad('godo','memberService','sl');
        $isHankookManager = $memberService->isHankookManager(\Session::get('member.memId'));
        $controller->setData('isHankookManager', $isHankookManager);

        $claimType = \Request::get()->get('claimType');
        if( SlCodeMap::CLAIM_TYPE[$claimType] ){
            $controller->setData('claimTitle', SlCodeMap::CLAIM_TYPE[$claimType]);
        }

    }
}