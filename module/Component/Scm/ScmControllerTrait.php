<?php

namespace Component\Scm;

use Component\Member\Util\MemberUtil;
use SlComponent\Util\SlCodeMap;
use SlComponent\Util\SlSkinUtil;

trait ScmControllerTrait
{

    public function indexCommon(){
        if( empty(\Session::get('member.memNo')) && !( SlCodeMap::NO_LOGIN_VIEW_SITE[URI_HOME]['cateCd'] )   ){
            $this->redirect('./member/login.php');
        }else{
            $scmNo = MemberUtil::getMemberScmNo(\Session::get('member.memNo'));
            $this->setData('scmNo',$scmNo);

            if( 'y' !== \Session::get('member.adultFl')  &&  32 == $scmNo && 18203 != \Session::get('member.memNo') ){
                //현대 엘리베이터 개인정보 인증.
                $this->redirect('../member/hd_join_agreement.php?memberFl=personal');
            }else{
                $this->setData('otherSkin', SlSkinUtil::getOtherSkinName());
                parent::index();
            }
        }
    }

}