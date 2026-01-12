<?php
namespace Component\Category;

use App;
use Component\Database\DBTableField;
use Component\Member\Util\MemberUtil;
use LogHandler;
use Request;
use Exception;
use SiteLabUtil\SlCommonUtil;
use SlComponent\Database\DBUtil2;
use SlComponent\Util\SitelabLogger;
use SlComponent\Util\SlCodeMap;
use SlComponent\Util\SlLoader;

use Component\Storage\Storage;
use Framework\Utility\ArrayUtils;
use Framework\Utility\StringUtils;
use Framework\Debug\Exception\AlertRedirectException;
use Globals;
use Session;


class Category extends \Bundle\Component\Category\Category{

    public function getMultiCategoryBox($selectID = null, $selectValue = null, $strStyle = null, $userMode = false, $isMobile = false)
    {
        if($userMode) {
            $defaultUrl = '../share/category_select_json.php';
        } else {
            $defaultUrl = '/share/category_select_json.php';
        }


        // 상품 카테고리
        if ($userMode === true) {
            if(Request::isMobile())  $cateDisplayMode = "cateDisplayMobileFl";
            else $cateDisplayMode = "cateDisplayFl";
            $userWhere = ' AND '.$cateDisplayMode.' = \'y\'';
            $jsonParam = 'userMode=y';
        }
        $whereStr = 'length(cateCd) = \'' . $this->cateLength . '\' AND divisionFl = \'n\'' . gd_isset($userWhere);

        $tmpData[] = $this->getCategoryData(null, null, 'mallDisplay,cateCd,cateNm,catePermission,catePermissionGroup,cateOnlyAdultFl', $whereStr, 'cateSort asc');
        // selectValue 값이 배열일 경우 마지막 값으로 설정
        if(is_array($selectValue)){
            $selectValue = ArrayUtils::last($selectValue);
        }

        if (gd_isset($selectValue)) {
            $depth = strlen($selectValue) / $this->cateLength;
            for ($i = 0; $i <= $depth; $i++) {
                $tmpLength = (($this->cateLength * $i) + $this->cateLength);
                $tmpValue[$i] = substr($selectValue, 0, $tmpLength);
                if ($i == 0) {
                    continue;
                }
                $whereStr = 'cateCd LIKE \'' . substr($selectValue, 0, ($tmpLength - $this->cateLength)) . '%\' AND length(cateCd) = \'' . $tmpLength . '\' AND divisionFl = \'n\'' . gd_isset($userWhere);
                $tmpData[] = $this->getCategoryData(null, null, 'cateCd,cateNm', $whereStr, 'cateSort asc');
            }
        }

        //--- 카테고리 타입에 따른 설정 (상품,브랜드)
        if ($this->cateType == 'goods') {
            $tmpTitle = __('카테고리');
            $tmpName = 'cateGoods';
            $tmpUrl = $defaultUrl . (isset($jsonParam) === true ? '?' . $jsonParam : '');
        } else {
            $tmpTitle = __('브랜드');
            $tmpName = $this->cateType;
            $tmpUrl = $defaultUrl . '?cateType=' . $this->cateType . (isset($jsonParam) === true ? '&' . $jsonParam : '');
        }

        //--- select box ID 설정
        if (is_null($selectID) === false) {
            $tmpName = $selectID;
        }

        return $this->setMultiSelectBox2($tmpName, $tmpData, gd_isset($tmpValue), $this->cateDepth, $tmpUrl, '=' . $tmpTitle . __('선택').'=', $strStyle,$isMobile);
    }

    public function setMultiSelectBox2($inputID, $arrData, $arrValue = null, $selectCnt, $ajexUrl, $strTitle = '---', $addStyle = null,$isMobile= false)
    {
        $useMallList = array_combine(array_column($this->gGlobal['useMallList'], 'sno'), $this->gGlobal['useMallList']);

        $useModeFl = false;
        //관리자가 아닌경우 실행
        if (Request::getSubdomainDirectory() !== 'admin') {
            $useModeFl = true;
        }

        //현재 그룹 정보
        $myGroup = Session::get('member.groupSno');


        $tmp = '';
        $tmpValue = [];
        for ($i = 0; $i < $selectCnt; $i++) {
            $inputNo = $i + 1;
            if($isMobile) {
                $tmp.='<div class="inp_sel" style="margin-top:10px">'.chr(10);
            }
            if(gd_is_skin_division()) {
                $tmp.='<div class="select_box">'.chr(10);
                $selectClass = "chosen-select";
            } else {
                $selectClass = "form-control multiple-select";
            }
            if (!gd_is_skin_division() && $addStyle == 'addDiv'){
                $tmp .= '<div>'.chr(10);
            }
            $tmp .= '<select id="' . $inputID . $inputNo . '" name="'.$inputID.'[]" ' . $addStyle . ' class="'.$selectClass.'">' . chr(10);
            $tmp .= '<option value="">' . $strTitle . '</option>' . chr(10);
            if (gd_isset($arrData[$i])) {
                foreach ($arrData[$i] as $key => $val) {

                    $disabledFl = false;

                    if ($val['cateOnlyAdultFl'] =='y' && gd_check_adult() === false) {
                        $disabledFl = true;
                    }

                    // 현재 카테고리 권한 체크
                    if ($val['catePermission'] > 0) {
                        // 현재 카테고리 권한에 따른 정보 카테고리 체크
                        if (gd_is_login() === false) {
                            $disabledFl = true;
                        }

                        if($val['catePermission'] =='2' && $val['catePermissionGroup'] && !in_array( $myGroup,explode(INT_DIVISION,$val['catePermissionGroup']))) {
                            $disabledFl = true;
                        }
                    }
                    $disabledStr = "";
                    if($useModeFl && $disabledFl) {
                        $disabledStr = "disabled='disabled'";
                    }

                    foreach(explode(",",$val['mallDisplay']) as $k1 => $v1) {
                        if($useMallList[$v1]) {
                            $mallSno[$k1] = $useMallList[$v1]['domainFl'];
                            $mallName[$k1] = $useMallList[$v1]['mallName'];
                        }
                    }

                    if(!empty(\Session::get('manager.sno'))){
                        $disabledStr = '';
                    }

                    $tmp .= '<option value="' . $val['cateCd'] . '" '.$disabledStr.' data-flag="'.implode(",",$mallSno).'" data-mall-name="'.implode(",",$mallName).'">' . StringUtils::htmlSpecialChars($val['cateNm']) . '</option>' . chr(10);
                    unset($mallSno);
                    unset($mallName);
                }
            }
            $tmp .= '</select>' . chr(10);
            if (!gd_is_skin_division() && $addStyle == 'addDiv'){
                $tmp .= '</div>'.chr(10);
            }
            if(gd_is_skin_division()) {
                $tmp.='</div>'.chr(10);
            }
            if($isMobile) {
                $tmp.='</div>'.chr(10);
            }
            $tmpBox[] = '$(\'#' . $inputID . $inputNo . '\').multi_select_box(\'#' . $inputID . '\',' . $selectCnt . ',\'' . $ajexUrl . '\',\'' . $strTitle . '\');';
            if (gd_isset($arrValue[$i])) {
                $tmpValue[] = "$('#" . $inputID . $inputNo . " option[value=\'" . $arrValue[$i] . "\']').attr('selected','selected');";
            }
        }

        $tmp .= '<script type="text/javascript">' . chr(10);
        $tmp .= '$(function() {' . chr(10);
        $tmp .= '	' . implode(chr(10) . '	', $tmpBox) . chr(10);
        $tmp .= '});' . chr(10);
        $tmp .= implode(chr(10), $tmpValue) . chr(10);
        $tmp .= '</script>' . chr(10);

        return $tmp;
    }

    public function getCategoryGoodsList($cateCd,$mobileFl = 'n') {
        $categoryGoodsList = parent::getCategoryGoodsList($cateCd,$mobileFl);
        //gd_debug($categoryGoodsList);
        return $categoryGoodsList;
    }

    public function getCategoryCodeInfo($cateCd = null, $depth = null, $division = true, $goodsCntFl = false, $userMode = null, $displayFl = false, $selectboxImgFl = true){
        $result = parent::getCategoryCodeInfo($cateCd, $depth , $division , $goodsCntFl , $userMode , $displayFl , $selectboxImgFl);
        $refineCategory = [];
        /*
        SCM  우선 하드코딩 - 추후 관리 페이지 만들기
        */
        $memberScm = MemberUtil::getMemberScmNo();
        //$scmCategoryMap = SlCodeMap::SCM_CATEGORY__MAP;
        $scmService=SlLoader::cLoad('godo','scmService','sl');

        if( MemberUtil::isLogin() ){
            foreach($result as $key => $value){
                if( $scmService->getScmCategoryCode($memberScm) === $value['cateCd'] ){
                    $value['memNo'] = \Session::get('member.memNo');
                    $refineCategory[] = $value;
                }
            }
        }else{
            foreach($result as $key => $value){
                if( $value['cateCd'] == SlCodeMap::NO_LOGIN_VIEW_SITE[URI_HOME]['cateCd'] ){
                    $refineCategory[] = $value;
                }
            }
        }

        /*if( 16 == \Session::get('member')['groupSno'] &&  26 == $memberScm ){
            $refineCategory = $refineCategory[0]['children'];
            foreach($refineCategory as $key => $value){
                $value['memNo'] = \Session::get('member.memNo');
                $refineCategory[$key] = $value;
            }
        }*/

        return $refineCategory;
    }

}
