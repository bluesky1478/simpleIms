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
use SlComponent\Util\STR;

/**
 * 회원 테이블 데이터 처리 클래스
 * @package Bundle\Component\Member
 * @author  yjwee
 * @method static MemberDAO getInstance
 */
class MemberDAO extends \Bundle\Component\Member\MemberDAO
{
    protected function setQuerySearch($params, &$arrBind){

        parent::setQuerySearch($params, $arrBind);

        if( !empty($params['connectedFl']) ){
            if( 'y' === $params['connectedFl'] ) {
                $bindStr = 'm.ex1 <> \'\'';
                $this->db->strWhere = $this->db->strWhere.' AND '.$bindStr;
            }else if( 'n' === $params['connectedFl'] ){
                $bindStr = 'm.ex1 = \'\'';
                $this->db->strWhere = $this->db->strWhere.' AND '.$bindStr;
            }
        }

        if( !isset($params['targetMemberFl']) ){
            $bindStr = '';
            if(!empty($params['scmNoNm'])){
                $refineScmNoNmArray = array();
                foreach($params['scmNoNm'] as $key => $value){
                    $refineScmNoNmArray[] = STR::APO.$value.STR::APO;
                }
                $bindStr = 'm.ex1 IN (' . implode(',',$refineScmNoNmArray) . ')' ;
                $this->db->strWhere = $this->db->strWhere.' AND '.$bindStr;
            }

            if(!empty($params['memberType']) && 'all' != $params['memberType'] ){
                $bindStr = 'smc.memberType = ' . $params['memberType'];
                $this->db->strWhere = $this->db->strWhere.' AND '.$bindStr;
            }

            $this->db->strField .= ', m.ex1, m.ex2, m.ex3, m.freeFl, m.hankookType, smc.memberType, smc.buyLimitCount , m.adultFl ';
            $this->db->strJoin .= ' LEFT OUTER JOIN sl_setMemberConfig smc ON m.memNo = smc.memNo ';
        }

    }

    /**
     * 회원 검색 조회 함수
     *
     * @param array $params 조회 조건 데이터 및 offset, limit 정보가 담긴 파라미터
     *                      offset, limit 에 값이 있으면 조회 조건에 추가된다.
     *
     * @return array
     */
    public function selectListBySearch(array $params){
        // -- _GET 값
        $getValue = \Request::get()->toArray();
        if(  !empty($getValue['simple_excel_download'])  ){
            \Request::get()->set('pageNum', '10000');
            \Request::get()->set('page', '1');
            $params['pageNum'] = '10000';
            $params['page'] = '1';
            $params['offset'] = '1';
            $params['limit'] = '10000';
        }

        /*if(  !empty($getValue['memberType'])  ){
            \Request::get()->set('memberType', '');
        }*/

        return parent::selectListBySearch($params);
    }


}
