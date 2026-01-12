<?php
namespace SlComponent\Util;

use Component\Mail\MailUtil;
use App;
use Component\Member\Util\MemberUtil;
use SiteLabUtil\SlCommonUtil;
use SlComponent\Database\DBUtil;
use Globals;

class SlSkinUtil{

    public static function getOtherSkinName(){
        $otherSkinName = '';
        $getValue = \Request::get()->all();
        //Redirect 체크
        foreach( SlCodeMap::OTHER_SKIN_SITE as $domain => $skin ){
            if( $domain === \Request::getDefaultHost() && empty($getValue['tplFrontSkin']) ){
                //$this->redirect('http://'.$domain.'/main/index.php?tplFrontSkin='.$skin);
                $otherSkinName = $skin;
                break;
            }
        }

        $memberScmNo = MemberUtil::getMemberScmNo();
        if( !empty($memberScmNo)  ){
            if( 34 == $memberScmNo ){
                $otherSkinName = 'asiana';
            }
            if( 21 == $memberScmNo ){
                $otherSkinName = 'oek';
            }
            if( 6 == $memberScmNo ){
                $otherSkinName = 'hankook';
            }
        }
        return $otherSkinName;
    }

    public static function getOtherSkinClass(){
        $otherSkinName = '';
        $getValue = \Request::get()->all();
        //Redirect 체크
        foreach( SlCodeMap::OTHER_SKIN_SITE as $domain => $skin ){
            if( $domain === \Request::getDefaultHost() && empty($getValue['tplFrontSkin']) ){
                //$this->redirect('http://'.$domain.'/main/index.php?tplFrontSkin='.$skin);
                $otherSkinName = $skin;
                break;
            }
        }
        return $otherSkinName;
    }

}