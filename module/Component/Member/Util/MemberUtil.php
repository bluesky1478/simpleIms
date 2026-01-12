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

namespace Component\Member\Util;

use App;
use Bundle\Component\Payment\Payco\Payco;
use Bundle\Component\Policy\KakaoLoginPolicy;
use Bundle\Component\Policy\PaycoLoginPolicy;
use Bundle\Component\Policy\SnsLoginPolicy;
use Bundle\Component\Policy\NaverLoginPolicy;
use Bundle\Component\Policy\WonderLoginPolicy;
use Component\Database\DBTableField;
use Component\Facebook\Facebook;
use Component\Godo\GodoPaycoServerApi;
use Component\Godo\GodoNaverServerApi;
use Component\Godo\GodoKakaoServerApi;
use Component\Godo\GodoWonderServerApi;
use Component\Member\Manager;
use Component\Member\Member;
use Component\Member\MemberVO;
use Component\Member\MyPage;
use Component\Validator\Validator;
use Component\Mall\Mall;
use Cookie;
use Encryptor;
use Exception;
use Framework\Debug\Exception\AlertRedirectException;
use Framework\Object\SimpleStorage;
use Framework\Object\SingletonTrait;
use Framework\Utility\ArrayUtils;
use Framework\Utility\ComponentUtils;
use Framework\Utility\SkinUtils;
use Framework\Utility\StringUtils;
use Framework\Utility\DateTimeUtils;
use Logger;
use Message;
use Request;
use Session;
use SlComponent\Database\DBUtil;
use SlComponent\Database\DBUtil2;
use SlComponent\Util\SitelabLogger;

/**
 * Class MemberUtil
 * @package Bundle\Component\Member\Util
 * @author  yjwee
 * @method static MemberUtil getInstance
 */
class MemberUtil extends \Bundle\Component\Member\Util\MemberUtil
{

    /**
     * 로그인 되었는 지를 체크 (회원/비회원)
     *
     * @author artherot
     * @return bool => false, 회원 => member, 비회원 => guest)
     */
    public static function checkLogin()
    {
        if (Session::has('member')) {
            $result = 'member';
        }else if( 'y'  === \Request::get()->get('isAdmin') && !empty(\Session::get('manager')) ){
            $result = 'member';
        } else {
            if (Session::has('guest')) {
                $result = 'guest';
            }
        }
        return $result;
    }

    /**
     * 회원 로그인여부 (프론트 전용)
     *
     * @static
     * @return mixed
     */
    public static function isLogin()
    {
        $session = App::getInstance('session');

        if( Session::has('manager') && 'y'  === \Request::get()->get('isAdmin') ){
            $result = 1;
        }else{
            $result = $session->has(Member::SESSION_MEMBER_LOGIN);
        }

        return $result;
    }


    /**
     * 관리자-회원정보 추가필드 html 생성
     *
     * @static
     *
     * @param array $fieldValue 추가필드 값
     *
     * @param null  $mallSno
     *
     * @return string
     */
    public static function makeExtraField(array $fieldValue = [], $mallSno = null)
    {
        if ($mallSno === null && $fieldValue['mallSno'] > DEFAULT_MALL_NUMBER) {
            $mallSno = $fieldValue['mallSno'];
        }
        $joinField = \Component\Policy\JoinItemPolicy::getInstance()->getPolicy($mallSno);

        $html[] = '<tr>';
        for ($i = 1; $i < 7; $i++) {
            $key = 'ex' . $i;
            $value = $joinField[$key];
            $title = '추가' . $i;

            //ex1은 관리자에서 무조건 사용!
            if ($value['use'] != 'y' && $i != 1 ) {
                continue;
            }
            if ($value['name']) {
                $title .= '<br /><span class="nobold">(' . $value['name'] . ')</span>';
            }
            if ($i != 1 && $i % 2 != 0) {
                $html[] = '</tr><tr>';
            }
            $html[] = '<th class="input_title r_space ex">' . $title . '</th>';
            $html[] = '<td>';
            $tmpArray = explode(',', $value['value']);
            switch ($value['type']) {
                default :
                    $html[] = '<input type="text" name="' . $key . '" class="form-control" value="' . $fieldValue[$key] . '"/>';
                    break;
                case 'SELECT':
                    $html[] = gd_select_box($key, $key, gd_array_change_key_value($tmpArray), null, $fieldValue[$key], '=선택=');
                    break;
                case 'RADIO':
                    $html[] = gd_radio_box($key, array_combine(array_values($tmpArray), array_values($tmpArray)), $fieldValue[$key]);
                    break;
                case 'CHECKBOX':
                    $html[] = gd_check_box($key . '[]', array_combine(array_values($tmpArray), array_values($tmpArray)), $fieldValue[$key]);
                    break;
            }
            $html[] = '</td>';
        }

        return implode('', $html);
    }

    public static function checkedByMemberListSearch(array $params){
        $result = parent::checkedByMemberListSearch($params);

        if ($result['scmNo'] == 0 && $result['scmFl'] == 1) {
            $result['scmFl']['all'] = 'checked="checked"';
        }else if(empty($params['scmFl'])){
            $result['scmFl']['all'] = 'checked="checked"';
        }else{
            $result['scmFl'][$params['scmFl']] = 'checked="checked"';
        }

        if ( empty($params['memberType']) )  {
            $result['memberType']['all'] = 'checked="checked"';
        }else{
            $result['memberType'][$params['memberType']] = 'checked="checked"';
        }

        if ( empty($params['connectedFl']) )  {
            $result['connectedFl'][''] = 'checked="checked"';
        }else{
            $result['connectedFl'][$params['connectedFl']] = 'checked="checked"';
        }

        return $result;
    }

    //회원에 속한 SCM 정보를 확인
    public static function getMemberScmNo($memNo = null){
        if(empty($memNo)){
            $memNo = \Session::get('member')['memNo'];
        }
        $memInfo = DBUtil::getOne('es_member','memNo',$memNo);
        $scmInfo = DBUtil::getOne('es_scmManage','companyNm',$memInfo['ex1']);
        return $scmInfo['scmNo'];
    }

    public static function getMemberScmData($memNo){
        return DBUtil2::getOne('sl_setScmConfig', 'scmNo', MemberUtil::getMemberScmNo($memNo) );
    }

    public static function getMemberConfig($memNo){
        return DBUtil2::getOne('sl_setMemberConfig', 'memNo', $memNo);
    }

    public static function getMemberData($memNo){
        $memberData = DBUtil2::getOne(DB_MEMBER, 'memNo', $memNo);
        unset($memberData['memPw']);
        return $memberData;
    }

}
