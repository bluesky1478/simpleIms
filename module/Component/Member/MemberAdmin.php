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

namespace Component\Member;

use App;
use Component\Database\DBTableField;
use Component\Mail\MailUtil;
use Component\Member\Group\Util as GroupUtil;
use Component\Member\Util\MemberUtil;
use Component\Mileage\Mileage;
use Component\Mileage\MileageDAO;
use Component\Sms\Code;
use Component\Sms\SmsAuto;
use Component\Sms\SmsAutoCode;
use Component\Validator\Validator;
use Exception;
use Framework\Security\Digester;
use Framework\Utility\ArrayUtils;
use Framework\Utility\ComponentUtils;
use Framework\Utility\DateTimeUtils;
use Framework\Utility\StringUtils;
use Framework\Utility\GodoUtils;
use Globals;
use Logger;
use Request;
use Session;
use SlComponent\Database\DBUtil2;
use SlComponent\Database\SearchVo;
use SlComponent\Util\SitelabLogger;

/**
 * Class 관리자에서 사용하는 회원 관리
 * @package Bundle\Component\Member
 * @author  yjwee
 */
class MemberAdmin extends \Bundle\Component\Member\MemberAdmin{

    public function modifyMemberData($arrData, $from = null)
    {
        $parentResult = parent::modifyMemberData($arrData, $from);
        DBUtil2::update(DB_MEMBER, ['hankookType' => $arrData['hankookType'] ] , new SearchVo('memNo=?' ,  $arrData['memNo'] )  );
        $setMemberConfig = DBUtil2::getOne('sl_setMemberConfig', 'memNo', $arrData['memNo']);
        $saveData = ['memberType' => $arrData['memberType'] , 'buyLimitCount' => $arrData['buyLimitCount']];

        if(!empty($setMemberConfig)){
            DBUtil2::update('sl_setMemberConfig', $saveData , new SearchVo('memNo=?' ,  $arrData['memNo'] )  );
        }else{
            if( !empty($arrData['memberType'])  && !empty($arrData['buyLimitCount']) ){
                $saveData['memNo'] = $arrData['memNo'];
                DBUtil2::insert('sl_setMemberConfig', $saveData  );
            }
        }
        return $parentResult;
    }

}
