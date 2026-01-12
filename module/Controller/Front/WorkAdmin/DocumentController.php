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

namespace Controller\Front\WorkAdmin;

use App;
use Component\Ims\ImsCodeMap;
use Component\Member\Util\MemberUtil;
use Component\Work\DocumentCodeMap;
use Component\Work\WorkCodeMap;
use Controller\Front\Work\WorkControllerTrait;
use Framework\Debug\Exception\AlertCloseException;
use Globals;
use Request;
use Session;
use SlComponent\Database\DBUtil2;
use SlComponent\Util\SlLoader;
use SlComponent\Util\SlProjectCodeMap;
use UserFilePath;

/**
 * 문서
 */
class DocumentController extends \Controller\Front\Controller
{
    //use WorkControllerTrait;

    public function index() {
        //TODO : 자동 로그인
        $memId = 'b1478';
        $member = \App::load('\\Component\\Member\\Member');
        $memberWithGroup = $this->selectMemberWithGroup($memId, 'memId');
        $loginLimit = json_decode($memberWithGroup['loginLimit'], true);
        $memberWithGroup['loginLimit'] = $loginLimit;
        $encryptMember = MemberUtil::encryptMember($memberWithGroup);
        $member->refreshMemberByLogin($encryptMember['memNo'], $encryptMember['loginCnt']);
        $member->refreshBasket($encryptMember['memNo']);
        // 모듈 설정
        $cart = \App::load('Component\\Cart\\Cart');
        $cart->setMergeCart($encryptMember['memNo']);
        $member->setSessionByLogin($encryptMember);

        //로그인 여부 확인
        $workControllerService=SlLoader::cLoad('work','workControllerService','');
        $workControllerService->setControllerData($this);
        $this->workIndex();
    }

    public function workIndex() {

        $docDept = 'SALES';
        $docType = 10;
        $this->setMenu('SALES', 10);
        $this->setData('docDept', $docDept);
        $this->setData('docType', $docType);
        $this->setData('includeFileName', 'work_admin/include/'.$docDept.$docType.'.html');

        $this->getView()->setPageName("work_admin/document_ims");

        //고객사
        $imsService = SlLoader::cLoad('ims','imsService');
        $this->setData('customerListMap', $imsService->getCustomerListMap());
        $this->setData('nasDownloadUrl', ImsCodeMap::NAS_DN_URL);
        $this->setData('nasUrl', ImsCodeMap::NAS_URL);

    }

    public function selectMemberWithGroup($value, $column)
    {
        $db = \App::getInstance('DB');
        $arrBind = [];
        $db->strField = 'm.memNo, m.memId, m.memPw, m.groupSno, m.memNm, m.nickNm, m.appFl, m.sleepFl, m.maillingFl, m.smsFl, m.saleCnt, m.saleAmt, m.mallSno';
        $db->strField .= ', m.cellPhone, m.email, m.adultConfirmDt, m.adultFl, m.loginCnt, m.changePasswordDt, m.guidePasswordDt, m.loginLimit, m.zonecode, m.mileage, m.memo as mMemo, m.birthDt as mBirthDt';
        $db->strField .= ', m.modDt AS mModDt, m.regDt AS mRegDt, m.lastSaleDt as mLastSaleDt, m.lastLoginDt as mLastLoginDt, ms.snsJoinFl, IF(ms.connectFl=\'y\', ms.snsTypeFl, \'\') AS snsTypeFl, ms.connectFl, ms.accessToken';
        $db->strField .= ', mg.groupNm, mg.groupSort, m.modDt AS memberModDt, m.regDt AS memberRegDt';
        $db->strJoin = ' LEFT JOIN ' . DB_MEMBER_GROUP . ' AS mg ON m.groupSno = mg.sno';
        $db->strJoin .= ' LEFT JOIN ' . DB_MEMBER_SNS . ' AS ms ON ms.memNo = m.memNo';
        $db->strWhere = 'm.' . $column . " = '{$value}'";
        //$db->bind_param_push($arrBind, $this->fields[$column], $value);
        $query = $db->query_complete();
        $strSQL = 'SELECT ' . array_shift($query) . ' FROM ' . DB_MEMBER . ' AS m ' . implode(' ', $query);
        return $db->query_fetch($strSQL, $arrBind, false);
    }

    public function setMenu($docDept, $docType){

        $projectListMap = [
            1=>[
                'name' => '고객사 리스트',
                'accept' => 'y',
                'href' => 'company_list.php',
            ],
            2=>[
                'name' => '프로젝트 리스트',
                'accept' => 'y',
                'href' => 'project_list.php',
            ]
        ];
        $salesDocumentListMap = SlProjectCodeMap::PRJ_DOCUMENT['SALES']['typeDoc'];
        foreach($salesDocumentListMap as $docKey => $docData){
            $docData['href'] = "document_list.php?docDept=SALES&docType={$docKey}";
            $salesDocumentListMap[$docKey] = $docData;
        }

        if( 'PROJECT' === $docDept ){
            $projectListMap[$docType]['active']='active';
        }else{
            $salesDocumentListMap[$docType]['active']='active';
        }
        /*gd_debug( $docDept );
        gd_debug($projectListMap);
        gd_debug($salesDocumentListMap);*/

        $menuList = [
            'PROJECT' => [
                'title' => '프로젝트 관리',
                'subMenuList' => $projectListMap,
            ],
            'SALES' => [
                'title' => '영업관리 문서',
                'subMenuList' => $salesDocumentListMap,
            ]
        ];
        //gd_debug( $menuList  );

        //gd_debug( $menuList );
        $this->setData('title' , $menuList[$docDept]['title']);
        $this->setData('titleSub', $menuList[$docDept]['subMenuList'][$docType]['name']);

        $this->setData('menuList', $menuList);
    }

}

