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

namespace Component\Mileage;

use Bundle\Component\Mileage\Mileage;
use Component\Database\DBTableField;
use Framework\Object\SingletonTrait;
use Framework\Utility\StringUtils;
use Component\Member\HackOut\HackOutDAO;

/**
 * 마일리지 데이터베이스 담당 클래스
 * @package Bundle\Component\Mileage
 * @author  yjwee <yeongjong.wee@godo.co.kr>
 */
class MileageDAO extends \Bundle\Component\Mileage\MileageDAO
{
    public function selectMemberBatchMileageList(array $params, $start, $limit)
    {
        $this->bindParams = $arrWhere = [];

        if(!empty($params['scmNo'])){
            $arrWhere[] = "ma.sno = '{$params['scmNo']}' ";
        }

        if ($params['listType'] === 'hackout') {
            // 탈퇴 회원 조회 컬럼
            $searchField = 'mh.memNo, mh.memId, mh.managerNo, mh.managerId, mh.mileage, mh.regDt';
            unset($params['sort']);

            // 탈퇴 회원 아이디 검색시 암호화 조회
            if ($params['key'] === 'memId') {
                $encryptor = \App::getInstance('encryptor');
                $params['keyword'] = $encryptor->mysqlAesEncrypt($params['keyword']);
            }
            $data = $this->hackOutDao->getMemberHackOutInfo($params, $searchField, $start, $limit);
        } else {
            //@formatter:off
            if ($params['searchKind'] == 'equalSearch') {
                $this->db->bindEqualKeywordByTables(Mileage::COMBINE_SEARCH, $params, $this->bindParams, $arrWhere, ['tableMemberMileage', 'tableMember'], ['mm', 'mb']);
            } else {
                $this->db->bindKeywordByTables(Mileage::COMBINE_SEARCH, $params, $this->bindParams, $arrWhere, ['tableMemberMileage', 'tableMember'], ['mm', 'mb']);
            }
            //@formatter:on
            $this->db->bindParameter('handleMode', $params, $this->bindParams, $arrWhere, 'tableMemberMileage', 'mm');
            $this->db->bindParameter('handleCd', $params, $this->bindParams, $arrWhere, 'tableMemberMileage', 'mm');
            $this->db->bindParameter('handleNo', $params, $this->bindParams, $arrWhere, 'tableMemberMileage', 'mm');
            $this->db->bindParameter('memNo', $params, $this->bindParams, $arrWhere, 'tableMemberMileage', 'mm');
            $this->db->bindParameter('deleteFl', $params, $this->bindParams, $arrWhere, 'tableMemberMileage', 'mm');
            $this->db->bindParameterByRange('mileage', $params, $this->bindParams, $arrWhere, 'tableMemberMileage', 'mm');
            $this->db->bindParameterByDateTimeRange('regDt', $params, $this->bindParams, $arrWhere, 'tableMemberMileage', 'mm');
            if ($params['reasonCd'] == Mileage::REASON_CODE_GROUP . Mileage::REASON_CODE_ETC && $params['contents'] != '') {
                $this->db->bindParameterByLike('contents', $params, $this->bindParams, $arrWhere, 'tableMemberMileage', 'mm');
            }
            $this->db->bindParameter('reasonCd', $params, $this->bindParams, $arrWhere, 'tableMemberMileage', 'mm');
            $this->db->bindParameter('groupSno', $params, $this->bindParams, $arrWhere, 'tableMember', 'mb');

            //@formatter:off
            $memberFields = DBTableField::setTableField(
                'tableMember', ['memNo', 'memId', 'groupSno', 'memNm', 'nickNm', ], null, 'mb'
            );
            $mileageFields = DBTableField::setTableField(
                'tableMemberMileage', ['memNo', 'managerId', 'managerNo', 'mileage', 'afterMileage', 'contents', 'handleMode', 'handleCd', 'handleNo', 'reasonCd', 'deleteFl', 'deleteScheduleDt', 'deleteDt', ], null, 'mm'
            );
            //@formatter:on
            $memberField = implode(', ', $memberFields);
            $mileageField = implode(', ', $mileageFields);

            $this->db->strField = $memberField . ', ' . $mileageField . ', mm.sno, mm.regDt,ma.isDelete';
            $this->db->strJoin = ' LEFT JOIN ' . DB_MEMBER . ' as mb ON mm.memNo = mb.memNo';
            $this->db->strJoin .= ' LEFT JOIN ' . DB_MANAGER . ' as ma ON ma.sno = mm.managerNo';

            //튜닝
            $this->db->strJoin .= ' LEFT JOIN es_scmManage as scm ON mb.ex1 = scm.companyNm';

            if ($params['mode'] == 'add') {
                $arrWhere[] = 'mm.mileage >= 0';
            } else if ($params['mode'] == 'remove') {
                $arrWhere[] = 'mm.mileage <= 0';
            }
            $this->db->strWhere = implode(' AND ', $arrWhere);
            $this->db->strOrder = gd_isset($params['sort'], 'entryDt desc');
            $this->db->strLimit = '?,?';
            $this->db->bind_param_push($this->bindParams, 'i', $start);
            $this->db->bind_param_push($this->bindParams, 'i', $limit);

            $query = $this->db->query_complete(true, true);
            $strSQL = 'SELECT ' . array_shift($query) . ' FROM ' . DB_MEMBER_MILEAGE . ' as mm ' . implode(' ', $query);
            //gd_debug($strSQL);
            $data = $this->db->query_fetch($strSQL, $this->bindParams);
        }

        return $data;
    }


}
