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

namespace Component\Design;

use Component\Member\Util\MemberUtil;
use Component\Validator\Validator;
use Component\Database\DBTableField;
use Component\Page\Page;
use Globals;
use League\Flysystem\Exception;
use Request;
use Message;
use SiteLabUtil\SlCommonUtil;
use SlComponent\Database\DBUtil;
use SlComponent\Database\SearchVo;
use SlComponent\Util\SitelabLogger;
use UserFilePath;
use FileHandler;
use Cookie;

/**
 * 팝업 관리 클래스
 * @author Shin Donggyu <artherot@godo.co.kr>
 */
class DesignPopup extends \Bundle\Component\Design\DesignPopup
{
    public function getUsePopupData($currentUrl){
        $scmNo = MemberUtil::getMemberScmNo();
        $parentData = parent::getUsePopupData($currentUrl);
        $returnData = array();
        foreach($parentData as $key => $value){
            if(  !empty( DBUtil::getOneBySearchVo('sl_scmPopup', new SearchVo(['scmNo=?','popupSno=?'],[$scmNo,$value['sno']])) )  ){
                $returnData[] = $value;
            }else{
                //전체용
                if( 35 != MemberUtil::getMemberScmNo() ){
                    if(  !empty( DBUtil::getOneBySearchVo('sl_scmPopup', new SearchVo(['scmNo=?','popupSno=?'],[0,$value['sno']])) )  ){
                        $returnData[] = $value;
                    }
                }
            }
        }
        return $returnData;
    }
}
