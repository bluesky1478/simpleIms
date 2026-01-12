<?php
namespace Controller\Front;

use Component\Member\Util\MemberUtil;
use Component\Work\WorkCodeMap;
use SiteLabUtil\SlCommonUtil;
use SlComponent\Util\SlCodeMap;
use SlComponent\Util\SlLoader;
use SlComponent\Util\SlSkinUtil;

/**
 * Class pro 사용자들이 모든 컨트롤러에 공통으로 사용할 수 있는 컨트롤러
 * 컨트롤러에서 지원하는 메소드들을 사용할 수 있습니다. http://doc.godomall5.godomall.com/Godomall5_Pro_Guide/Controller
 */
class CommonController
{
    public function index($controller)
    {
        $controller->addScript([
            'sl_custom.js?ver=1',
        ]);

        $controller->setData('isDev', SlCommonUtil::isDevIp());

        //$controller->setData('remoteAddr',\Request::server()->get('REMOTE_ADDR'));  //아이피 추가.  스킨에서 {=remoteAddr} 치환코드로 사용가능
        //$controller->setData('userName', '사용자 이름');  //새 변수 추가하기. 스킨에서 {=userName} 치환코드로 사용가능

        //회원 한국타이어 본사아이디 여부
        $memberService = SlLoader::cLoad('godo','memberService','sl');
        $isHankookManager = $memberService->isHankookManager(\Session::get('member.memId'));
        $isTkeManager = $memberService->isTkeManager(\Session::get('member.memId'));
        $isOekManager = $memberService->isOekManager(\Session::get('member.memId'));
        $controller->setData('isHankookManager', $isHankookManager);
        $controller->setData('isTkeManager', $isTkeManager);
        $controller->setData('isOekManager', $isOekManager);
        $controller->setData('isDevIp', SlCommonUtil::isDevIp());

        $memberScmNo = MemberUtil::getMemberScmNo();
        $otherSkin = SlSkinUtil::getOtherSkinName();
        $controller->setData('otherSkin', $otherSkin);
        $controller->setData('otherSkinClass',  SlCodeMap::OTHER_SKIN_CLASS[$otherSkin]);
        $controller->setData('otherSkinCompName',  SlCodeMap::OTHER_SKIN_COMP_NAME[$otherSkin]);

        $controller->setData('memberScm', $memberScmNo);
        $controller->setData('loginMemberId', \Session::get('member')['memId']);

        $controller->setData('myDomain', \Request::getDomainUrl());
        $controller->setData('managerInfo', \Request::getDomainUrl());

        //로그인 전에 가격을 표기.
        $controller->setData('noLoginCateCd',SlCodeMap::NO_LOGIN_VIEW_SITE[URI_HOME]['cateCd']);

        //관리자 로그인시 추가 데이터 지정
        if( !empty(\Session::get('manager')) ){
            $controller->setData('COMP_TYPE', WorkCodeMap::COMP_TYPE);
            $controller->setData('COMP_DIV', WorkCodeMap::COMP_DIV);
            $controller->setData('PROPOSAL_TYPE', WorkCodeMap::PROPOSAL_TYPE);
            $controller->setData('MS_PROPOSAL_TYPE', WorkCodeMap::MS_PROPOSAL_TYPE);
            $managerList = SlCommonUtil::getManagerList();
            $controller->setData('managerList' , $managerList);
            $designManagerList = SlCommonUtil::getManagerList( WorkCodeMap::DEPT_CODE['DESIGN'] );
            $controller->setData('designManagerList' , $designManagerList);
            $controller->setData('managerInfo' , \Session::get('manager'));
            $controller->setData('deptList' , SlCommonUtil::getDeptList());
            $controller->setData('requestParam' , \Request::request()->toArray());
        }

        $agent = \Request::getUserAgent();
        if ( strpos($agent, 'Netscape') !== false || strpos($agent, 'Trident') !== false  || strpos($agent, 'msie') !== false  ) {
            $controller->setData('isIe',true);
        }
    }
}