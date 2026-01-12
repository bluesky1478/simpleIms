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
use Component\Board\Board;
use Component\Database\DBTableField;
use Component\Mail\MailMimeAuto;
use Framework\Database\DBTool;
use Framework\Object\SingletonTrait;
use Framework\Utility\DateTimeUtils;
use Framework\Utility\StringUtils;
use Framework\Security\Digester;
use Framework\Utility\GodoUtils;
use Exception;
use SlComponent\Database\DBUtil;
use SlComponent\Database\SearchVo;
use SlComponent\Util\SitelabLogger;
use SlComponent\Util\STR;

/**
 * 회원 테이블 데이터 처리 클래스
 * @package Bundle\Component\Member
 * @author  yjwee
 * @method static MemberDAO getInstance
 */
class Member extends \Bundle\Component\Member\Member{

    public function join($params){
        $parentResult = parent::join($params);
        if( '한국타이어'  === $params['ex1'] ){
            DBUtil::update( DB_MEMBER,  ['appFl' =>'y' ] , new SearchVo('memNo=?',  $parentResult->getMemNo() )  );
        }
        if( '오티스'  === $params['ex1'] ){
            DBUtil::update( DB_MEMBER,  ['appFl' =>'y' ] , new SearchVo('memNo=?',  $parentResult->getMemNo() )  );
        }
        if( '이준석캠프'  === $params['ex1'] ){
            DBUtil::update( DB_MEMBER,  ['appFl' =>'y' ] , new SearchVo('memNo=?',  $parentResult->getMemNo() )  );
        }
        return $parentResult;
    }

    /**
     * 관리자 회원검색 리스트 셀렉트 박스 목록
     *
     * @static
     * @return array
     */
    public static function getCombineSearchSelectBox()
    {
        $parentList = parent::getCombineSearchSelectBox();
        $parentList['address'] = '주소검색';
        $parentList['addressSub'] = '주소 상세 검색';
        return $parentList;
    }

}
