<?php
/**
 * ScmAdmin Class
 *
 * @author    su
 * @version   1.0
 * @since     1.0
 * @copyright ⓒ 2016, NHN godo: Corp.
 */

namespace Component\Scm;

use App;
use Component\Member\Manager;
use Component\Storage\Storage;
use Component\Category\Category;
use Component\Database\DBTableField;
use Component\Validator\Validator;
use Framework\Utility\ArrayUtils;
use Request;
use Session;
use SiteLabUtil\SlCommonUtil;
use SlComponent\Database\DBUtil;
use SlComponent\Database\DBUtil2;
use SlComponent\Database\SearchVo;
use SlComponent\Util\SitelabLogger;

class ScmAdmin extends \Bundle\Component\Scm\ScmAdmin{
    /**
     * Config의 member.joinItem을 업데이트.
     * @param $updateData
     * @throws \Exception
     */
    public function updateJoinItemEx1Value($inputUpdateData){
        $joinItem = gd_policy('member.joinitem', 1);
        $joinItem['ex1']['value']=$inputUpdateData;
        $searchVo = new SearchVo();
        $searchVo->setWhereArray(['groupCode=?','code=?']);
        $searchVo->setWhereValueArray(['member','joinItem']);
        $updateData = array();
        $updateData['data'] = json_encode($joinItem, JSON_UNESCAPED_SLASHES);
        DBUtil::update('es_config',$updateData,$searchVo);
    }

    /**
     * 공급사 등록
     *
     * @param array &$req $_POST
     * @param array &$files $_FILES
     *
     * @return int scmNo
     * @throws \Exception
     * @author su
     *
     */
    public function saveScm(&$arrData, &$files){
        //공급사 변경사항 저장 전 정보
        $updateScmNo = $arrData['scmNo']=='1'?'':$arrData['scmNo'];
        $beforeScmInfo = DBUtil::getOne(DB_SCM_MANAGE,'scmNo',$updateScmNo);

        //공급사 정보 저장
        //$scmNo = parent::saveScm($arrData, $files);
        $scmNo = $this->saveScmOrg($arrData, $files);

        //공급사 저장 후처리
        $joinItem = gd_policy('member.joinitem', 1);

        if(empty($beforeScmInfo)){
            //신규 입력시
            //member.joinItem 추가 한다.
            $ex1ValueItemArray = explode(',',$joinItem['ex1']['value']);
            $ex1ValueItemArray[] = $arrData['companyNm'];
            if( !empty($arrData['companyNm']) ){
                $this->updateJoinItemEx1Value(implode(',',$ex1ValueItemArray));
            }
        }else{
            //수정시
            $afterScmInfo = DBUtil::getOne(DB_SCM_MANAGE,'scmNo',$scmNo);
            
            if( $beforeScmInfo['companyNm'] !== $afterScmInfo['companyNm'] ){
                //member.joinItem 변경
                $ex1ValueItemArray = explode(',',$joinItem['ex1']['value']);
                $newEx1ValueItemArray = array();
                foreach($ex1ValueItemArray as $key => $value){
                    if($value === $beforeScmInfo['companyNm']){
                        $newEx1ValueItemArray[] = $afterScmInfo['companyNm']; //변경된 항목으로 수정
                    }else{
                        $newEx1ValueItemArray[] = $value;
                    }
                }
                $this->updateJoinItemEx1Value(implode(',',$newEx1ValueItemArray));

                //회원정보의 ex1도 업데이트
                $searchVo = new SearchVo('ex1=?',$beforeScmInfo['companyNm']);
                DBUtil::update(DB_MEMBER,['ex1'=>$afterScmInfo['companyNm']],$searchVo);
            }
        }

        return $scmNo;
    }

    //공급사 리스트 가져오기
    public function getSelectScmList(){
        $selectList = array();
        $list = DBUtil::getListBySearchVo('es_scmManage',new SearchVo('scmNo <> ? ','1'));
        foreach($list as $key => $value){
            $selectList[$value['scmNo']] = $value['companyNm'];
        }
        return $selectList;
    }

    /**
     * 공급사 리스트 가져오기
     *
     * @param null $mode
     * @return
     * @author su
     */
    public function getScmAdminList($mode = null){

        if( !SlCommonUtil::isDev() ){
            $this->arrWhere[] = " sm.scmNo NOT IN (1,5,13)"; //본사,맥스,삼성로지텍
        }

        $parentList = parent::getScmAdminList($mode);

        foreach($parentList['data'] as $key => $value ){
            $scmData = DBUtil2::getOne('sl_setScmConfig', 'scmNo', $value['scmNo']);
            $category = DBUtil2::getOne(DB_CATEGORY_GOODS, 'cateCd', $scmData['cateCd']);
            $value['category'] = $category['cateNm'];
            $value['stockManage'] = 'y' == $scmData['stockManageFl'] ? '<b>예</b>' : '아니오' ;
            $value['orderAccept'] = 'y' == $scmData['orderAcceptFl'] ? '<b>예</b>' : '아니오' ;
            $value['memberAccept'] = 'y' == $scmData['memberAcceptFl'] ? '<b>예</b>' : '아니오' ;
            $value['deliverySelect'] = 'y' == $scmData['deliverySelectFl'] ? '<b>예</b>' : '아니오' ;
            $value['memo'] = $scmData['memo'];
            $value['files'] = json_decode($scmData['files'],true);
            //gd_debug($value['files']);
            $parentList['data'][$key] = $value;

        }
        return $parentList;
    }



    //공급사 정산 오류로 저장 안되서 처리

    public function saveScmOrg(&$arrData, &$files)
    {
        // 삭제 여부 ( 삭제 안함 )
        $arrData['delFl'] = 'n';

        // 출고지 주소
        if ($arrData['chkSameUnstoringAddr'] == 'y') {
            $arrData['unstoringZonecode'] = $arrData['zonecode'];
            $arrData['unstoringZipcode'] = $arrData['zipcode'];
            $arrData['unstoringAddress'] = $arrData['address'];
            $arrData['unstoringAddressSub'] = $arrData['addressSub'];
        } else {

        }
        // 반품/교환 주소
        if ($arrData['chkSameReturnAddr'] == 'y') {
            $arrData['returnZonecode'] = $arrData['zonecode'];
            $arrData['returnZipcode'] = $arrData['zipcode'];
            $arrData['returnAddress'] = $arrData['address'];
            $arrData['returnAddressSub'] = $arrData['addressSub'];
        } else if ($arrData['chkSameReturnAddr'] == 'x') {
            $arrData['returnZonecode'] = $arrData['unstoringZonecode'];
            $arrData['returnZipcode'] = $arrData['unstoringZipcode'];
            $arrData['returnAddress'] = $arrData['unstoringAddress'];
            $arrData['returnAddressSub'] = $arrData['unstoringAddressSub'];
        } else {

        }
        // 담당자 정보
        $staff = [];
        $staffNum = count($arrData['staffType']);
        for ($i = 0; $i < $staffNum; $i++) {
            $staff[$i]['staffType'] = $arrData['staffType'][$i];
            $staff[$i]['staffName'] = $arrData['staffName'][$i];
            $staff[$i]['staffTel'] = $arrData['staffTel'][$i];
            $staff[$i]['staffPhone'] = $arrData['staffPhone'][$i];
            $staff[$i]['staffEmail'] = $arrData['staffEmail'][$i];
        }
        $staff = gd_htmlspecialchars_addslashes($staff);
        $arrData['staff'] = json_encode($staff, JSON_UNESCAPED_UNICODE);

        if($arrData['isProvider'] == 'n') { // 본사에서 저장 시에만 계좌 정보 값 저장 ( 공급사 > 기본정보설정에서 저장시 제외 )
            $specialChar = ['<', '>', '\\', '"', '\'', '`']; // 특수문자 제거
            // 계좌 정보
            $account = [];
            $accountNum = count($arrData['accountType']);
            for ($i = 0; $i < $accountNum; $i++) {
                $account[$i]['accountType'] = $arrData['accountType'][$i];
                $account[$i]['accountNum'] = $arrData['accountNum'][$i];
                $account[$i]['accountName'] = str_replace($specialChar, '', $arrData['accountName'][$i]);
                $account[$i]['accountMemo'] = str_replace($specialChar, '', $arrData['accountMemo'][$i]);
            }
            $account = gd_htmlspecialchars_addslashes($account);
            $arrData['account'] = json_encode($account, JSON_UNESCAPED_UNICODE);
        }

        if (gd_isset($arrData['businessNm'])) {
            $specialChar = ['&', ',', ';', '\\n', '\\', '"', '\'', '|']; // 특수문자 제거
            $arrData['businessNm'] = str_replace($specialChar,'', $arrData['businessNm']);
            $arrData['businessNm'] = mb_substr($arrData['businessNm'], 0, 50);
        }

        // 사업자 등록증 이미지
        if ($arrData['isBusinessImageDelete'] == 'y' && $arrData['oldBusinessLicenseImage']) {
            $this->storage->delete(basename($arrData['oldBusinessLicenseImage']));
            $arrData['businessLicenseImage'] = '';
        }
        if (ArrayUtils::isEmpty($files['businessLicenseImage']) === false) {
            $file = $files['businessLicenseImage'];
            if ($file['error'] == 0 && $file['size']) {
                $saveFileName = $arrData['businessNo'] . '_bl_' . substr(md5(microtime()), 0, 8);
                $arrData['businessLicenseImage'] = $this->storage->upload($file['tmp_name'], $saveFileName);
            }
        }
        // Validation
        $validator = new Validator();
        if (substr($arrData['mode'], 0, 6) == 'modify') {
            $validator->add('scmNo', 'number', true); // 공급사 고유번호
        } else {
            if ($this->getDuplicateScmCompanyNm($arrData['companyNm'])) {
                throw new Exception(__('이미 존재하는 공급사명입니다.'));
            }

            $arrData['scmInsertAdminId'] = Session::get('manager.managerId');
            $arrData['managerNo'] = Session::get('manager.sno');
            // 기존 아이디 중복 확인 같으면 정상, 같이 않으면 오류
            if ($arrData['managerId'] != $arrData['managerDuplicateId']) {
                throw new Exception(__('아이디 중복확인이 되지 않았습니다.'));
            }
            $validator->add('managerId', 'userid', true, null, true, false); // 공급사 아이디
            $validator->add('managerId', 'minlen', true, null, 4); // 아이디 최소길이
            $validator->add('managerId', 'maxlen', true, null, 50); // 아이디 최대길이
            $validator->add('managerDuplicateId', 'userid', true); // 공급사 아이디 중복확인 아이디
            $validator->add('managerPw', 'password', true); // 공급사 비밀번호
            $validator->add('managerPw', 'minlen', true, null, 10); // 비밀번호 최소길이
            $validator->add('managerPw', 'maxlen', true, null, 16); // 비밀번호 최대길이
            $validator->add('scmInsertAdminId', 'userid', true); // 공급사 등록하는 관리자 아이디
            $validator->add('managerNo', 'number', true); // 공급사 관리자 키
        }


        $scmCommission = \App::load(\Component\Scm\ScmCommission::class);
        //기본 판매 수수료 범위 체크
        if ( $arrData['scmCommission'] > 100 ||  $arrData['scmCommission'] < 0) {
            throw new Exception(__('수수료는') . ' 0 ~ 100 % ' . __('입니다.'));
        }
        //저장된 추가 판매수수료
        if (gd_isset($arrData['scmCommissionInDB'])) {
            $scmCommission->checkScmCommissionValue($arrData['scmCommissionInDB']);
            $addCommissionArrData['scmCommissionInDB'] = $arrData['scmCommissionInDB'];
            unset($arrData['scmCommissionInDB']);
        }
        //추가된 추가 판매수수료
        if (gd_isset($arrData['scmCommissionNew'])) {
            $scmCommission->checkScmCommissionValue($arrData['scmCommissionNew']);
            $addCommissionArrData['scmCommissionNew'] = $arrData['scmCommissionNew'];
            unset($arrData['scmCommissionNew']);
        }
        //판매수수료 동일적용
        if ($arrData['scmSameCommission'] == 'Y') {
            $arrData['scmCommissionDelivery'] = $arrData['scmCommission'];
            $addCommissionArrData['scmSameCommission'] = $arrData['scmSameCommission'];
            unset($arrData['scmSameCommission']);
        } else {
            //기본 배송비 수수료 범위 체크
            if ( $arrData['scmCommissionDelivery'] > 100 ||  $arrData['scmCommissionDelivery'] < 0) {
                throw new Exception(__('수수료는') . ' 0 ~ 100 % ' . __('입니다.'));
            }
            //저장된 추가 판매수수료
            if (gd_isset($arrData['scmCommissionDeliveryInDB'])) {
                $scmCommission->checkScmCommissionValue($arrData['scmCommissionDeliveryInDB']);
                $addCommissionArrData['scmCommissionDeliveryInDB'] = $arrData['scmCommissionDeliveryInDB'];
                unset($arrData['scmCommissionDeliveryInDB']);
            }
            //추가된 추가 판매수수료
            if (gd_isset($arrData['scmCommissionDeliveryNew'])) {
                $scmCommission->checkScmCommissionValue($arrData['scmCommissionDeliveryNew']);
                $addCommissionArrData['scmCommissionDeliveryNew'] = $arrData['scmCommissionDeliveryNew'];
                unset($arrData['scmCommissionDeliveryNew']);
            }
        }

        $validator->add('mode', 'alpha', true); // 모드
        $validator->add('companyNm', '', true); // 공급사명
        $validator->add('scmType', '', true); // 공급사상태-운영('y'), 일시정지('n'), 탈퇴('x')
        $validator->add('managerNickNm', '', ''); // 닉네임
        $validator->add('scmCommission', '', true); // 판매수수료-%로 소수점 2자리
        $validator->add('scmCommissionDelivery', '', true); // 배송비수수료-%로 소수점 2자리
        $validator->add('scmKind', '', true); // 공급사종류 - 공급사('p'),본사('c')
        $validator->add('scmCode', '', ''); // 공급사코드
        $validator->add('imageStorage', '', ''); // 이미지 저장소 위치
        $validator->add('scmPermissionInsert', '', true); // 상품등록권한-자동승인('a'),관리자승인('c')
        $validator->add('scmPermissionModify', '', true); // 상품수정권한-자동승인('a'),관리자승인('c')
        $validator->add('scmPermissionDelete', '', true); // 상품삭제권한-자동승인('a'),관리자승인('c')
        $validator->add('ceoNm', '', true); // 대표자
        $validator->add('businessNo', '', true); // 사업자 번호
        $validator->add('businessNm', '', true); // 상호명
        $validator->add('businessLicenseImage', '', ''); // 사업자 등록증 이미지
        $validator->add('service', '', true); // 업태
        $validator->add('item', '', true); // 종목
        $validator->add('phone', '', true); // 대표전화
        $validator->add('centerPhone', '', ''); // 고객센터
        $validator->add('zipcode', '', ''); // 구 우편번호
        $validator->add('zonecode', '', true); // 우편번호
        $validator->add('address', '', true); // 주소
        $validator->add('addressSub', '', true); // 상세주소
        $validator->add('unstoringZipcode', '', ''); // 구 우편번호
        $validator->add('unstoringZonecode', '', ''); // 우편번호
        $validator->add('unstoringAddress', '', ''); // 주소
        $validator->add('unstoringAddressSub', '', ''); // 상세주소
        $validator->add('returnZipcode', '', ''); // 구 우편번호
        $validator->add('returnZonecode', '', ''); // 우편번호
        $validator->add('returnAddress', '', ''); // 주소
        $validator->add('returnAddressSub', '', ''); // 상세주소
        if (substr($arrData['mode'], 0, 6) == 'modify' && $arrData['scmNo'] == DEFAULT_CODE_SCMNO) { // 본사 수정시 기능권한 저장 패스
            // empty statement
        } else if (!gd_is_provider()) { // 공급사 등록/수정시 기능권한 저장
            // 공급사 기능 권한 설정
            if (count($arrData['functionAuth']) > 0) {
                $functionAuth = [
                    'functionAuth' => $arrData['functionAuth'],
                ];
            } else {
                $functionAuth = null;
            }
            $arrData['functionAuth'] = json_encode($functionAuth, JSON_UNESCAPED_UNICODE); // 운영자 기능 권한 설정
            $validator->add('functionAuth', ''); // 공급사 기능권한
        }
        $validator->add('staff', '', ''); // 담당자 정보
        $validator->add('account', '', ''); // 계좌 정보
        $validator->add('delFl', 'yn', true); // 삭제여부
        if ($validator->act($arrData, true) === false) {
            throw new Exception(implode("<br/>", $validator->errors));
        }
        //        $arrData = ArrayUtils::removeEmpty($arrData);
        switch (substr($arrData['mode'], 0, 6)) {
            case 'insert':
                try {
                    $this->db->begin_tran();

                    // 저장
                    $arrBind = $this->db->get_binding(DBTableField::tableScmManage(), $arrData, 'insert', array_keys($arrData), ['scmNo']);
                    $this->db->set_insert_db(DB_SCM_MANAGE, $arrBind['param'], $arrBind['bind'], 'y');

                    // 등록된 공급사고유번호
                    $scmNo = $this->db->insert_id();

                    //추가 판매수수료, 배송비수수료
                    if (gd_isset($addCommissionArrData)) {
                        $addCommissionArrData['scmNo'] = $scmNo;
                        $scmCommission->saveScmCommission($addCommissionArrData, 'insert');
                    }
                    //로그
                    $scmLog = $scmCommission->getScmLogData($scmNo);
                    $scmCommission->setScmLog('scm', 'insert', $scmNo, '', $scmLog);

                    // 공급사 관리자 등록 / es_manage 에 등록
                    $manager = \App::load(Manager::class);
                    $arrManager = [];
                    $arrManager['scmNo'] = $scmNo;
                    $arrManager['mode'] = 'register';
                    $arrManager['isSuper'] = 'y';
                    $arrManager['permissionFl'] = 's';
                    $arrManager['managerId'] = $arrData['managerId'];
                    $arrManager['managerNm'] = $arrData['ceoNm'];
                    $arrManager['managerPw'] = $arrData['managerPw'];
                    $arrManager['managerNickNm'] = $arrData['managerNickNm'];
                    // 대표운영자 상품재고 권한 부여
                    $arrManager['functionAuth']['goodsStockModify'] = 'y';
                    $arrManagerFiles = [];
                    $arrManagerFiles['dispImage'] = $files['scmImage'];
                    $manager->setIsRequireAuthentication(false);
                    $manager->saveManagerData($arrManager, $arrManagerFiles);

                    // 배송기본 정보 추가
                    $arrDelivery['scmNo'] = $scmNo;
                    $arrDelivery['unstoringZipcode'] = $arrData['unstoringZipcode'];
                    $arrDelivery['unstoringZonecode'] = $arrData['unstoringZonecode'];
                    $arrDelivery['unstoringAddress'] = $arrData['unstoringAddress'];
                    $arrDelivery['unstoringAddressSub'] = $arrData['unstoringAddressSub'];
                    $arrDelivery['returnZipcode'] = $arrData['returnZipcode'];
                    $arrDelivery['returnZonecode'] = $arrData['returnZonecode'];
                    $arrDelivery['returnAddress'] = $arrData['returnAddress'];
                    $arrDelivery['returnAddressSub'] = $arrData['returnAddressSub'];
                    $delivery = \App::load(\Component\Delivery\Delivery::class);
                    $delivery->saveDeliveryDefaultData($arrDelivery);

                    // 엑셀양식기본양식추가
                    $arrExcelForm['scmNo'] = $scmNo;
                    $excelForm = \App::load('\\Component\\Excel\\ExcelForm');
                    $excelForm->saveExcelFormDefaultData($arrExcelForm);

                    $this->db->commit();

                } catch (Exception $e) {
                    $this->db->rollback();
                    throw new Exception(__('정산 요청 중 오류가 발생하였습니다. 다시 시도해 주세요.?'));
                }
                break;
            case 'modify' :
                try {
                    $this->db->begin_tran();
                    //로그
                    $scmPrevData = $scmCommission->getScmLogData($arrData['scmNo']);

                    // 공급사 수정
                    $arrBind = $this->db->get_binding(DBTableField::tableScmManage(), $arrData, 'update', array_keys($arrData), ['scmNo']);
                    $this->db->bind_param_push($arrBind['bind'], 'i', $arrData['scmNo']);
                    $this->db->set_update_db(DB_SCM_MANAGE, $arrBind['param'], 'scmNo = ?', $arrBind['bind'], false);

                    //추가 판매수수료, 배송비수수료
                    if (gd_isset($addCommissionArrData)) {
                        $addCommissionArrData['scmNo'] = $arrData['scmNo'];
                        $scmCommission->saveScmCommission($addCommissionArrData, 'update');
                    } else {
                        $addCommissionArrData['scmNo'] = $arrData['scmNo'];
                        $scmCommission->saveScmCommission($addCommissionArrData, 'update');
                    }

                    //로그
                    $scmUpdateData = $scmCommission->getScmLogData($arrData['scmNo']);
                    $scmCommission->setScmLog('scm', 'update', $arrData['scmNo'], $scmPrevData, $scmUpdateData);

                    // 공급사 관련 정보 수정
                    if ($arrData['scmType'] == 'x') {
                        $arrGoodsData['scmNo'] = $arrData['scmNo'];
                        $arrGoodsData['goodsDisplayFl'] = 'n';
                        $arrGoodsData['goodsDisplayMobileFl'] = 'n';
                        $arrGoodsData['goodsSellFl'] = 'n';
                        $arrGoodsData['goodsSellMobileFl'] = 'n';
                        $arrGoodsBind = $this->db->get_binding(DBTableField::tableGoods(), $arrGoodsData, 'update', array_keys($arrGoodsData), ['sno']);
                        $this->db->bind_param_push($arrGoodsBind['bind'], 'i', $arrGoodsData['scmNo']);
                        $this->db->set_update_db(DB_GOODS, $arrGoodsBind['param'], 'scmNo = ?', $arrGoodsBind['bind'], false);
                        unset($arrGoodsBind);

                        $goodsDivisionFl = gd_policy('goods.config')['divisionFl'] ;
                        if($goodsDivisionFl =='y') {
                            //상품검색테이블 업데이트
                            $arrGoodsBind = $this->db->get_binding(DBTableField::tableGoodsSearch(), $arrGoodsData, 'update', array_keys($arrGoodsData), ['sno']);
                            $this->db->bind_param_push($arrGoodsBind['bind'], 'i', $arrGoodsData['scmNo']);
                            $this->db->set_update_db(DB_GOODS_SEARCH, $arrGoodsBind['param'], 'scmNo = ?', $arrGoodsBind['bind'], false);
                            unset($arrGoodsBind);
                        }
                        // 공급사 수수료 일정 삭제(탈퇴)
                        $scmCommission = App::load(\Component\Scm\ScmCommission::class);
                        $scmCommission->deleteScmScheduleCommissionBatch($arrData['scmNo'], 'once');
                    }

                    $this->db->commit();
                } catch (Exception $e) {
                    $this->db->rollback();
                    throw new Exception(__('정산 요청 중 오류가 발생하였습니다. 다시 시도해 주세요.'));
                }
                break;
        }

        if (substr($arrData['mode'], 0, 6) == 'insert') {
            return $scmNo;
        } else {
            return $arrData['scmNo'];
        }
    }

}