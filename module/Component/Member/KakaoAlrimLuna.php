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
use Component\Godo\GodoSmsServerApi;
use Component\Member\Group\Util;
use Component\Page\Page;
use Component\Sms\Code;
use Component\Sms\Sms;
use Component\Sms\SmsAutoCode;
use Component\Storage\Storage;
use Component\Validator\Validator;
use Framework\Database\DBTool;
use Framework\Debug\Exception\AlertBackException;
use Framework\Utility\ArrayUtils;
use Framework\Utility\ComponentUtils;
use Framework\Utility\DateTimeUtils;
use Framework\Utility\StringUtils;
use Logger;
use Request;
use SlComponent\Util\SitelabLogger;
use UserFilePath;

/**
 * Class KakaoAlrimLuna
 * @package Bundle\Component\Member
 * @author  cjb333
 */
class KakaoAlrimLuna extends \Bundle\Component\Member\KakaoAlrimLuna
{
    public function sendKakaoAlrimLuna($aSmsLog, $aSender, $aLogData, $receiverForSaveSmsSendList, $aReplaceArguments, $contents){
        if( !empty(strpos($contents, "상품의 입금 요청")) && '한국타이어GIFT' == $aReplaceArguments['depositNm'] ){
            //SitelabLogger::logger('한국타이어 GIFT 메세지 PASS');
            return ; //GIFT 상품은 입금요청 하지 않기.
        }else{
            return parent::sendKakaoAlrimLuna($aSmsLog, $aSender, $aLogData, $receiverForSaveSmsSendList, $aReplaceArguments, $contents);
        }
    }

}
