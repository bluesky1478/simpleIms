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

namespace Component\Admin;

use Framework\Debug\Exception\AlertBackException;
use Framework\Debug\Exception\AlertRedirectException;
use Framework\Debug\Exception\AlertCloseException;
use Framework\Utility\GodoUtils;
use App;
use Globals;
use Request;
use SiteLabUtil\SlCommonUtil;

/**
 * 관리자 메뉴 class
 *
 * 관리자 좌측 메뉴 및 상단 위치 설정 관련 class
 * @author su
 */
class AdminMenu extends \Bundle\Component\Admin\AdminMenu
{
    public function callMenu($topMenu, $midMenu = '', $thisMenu = '', $adminMenuType = 'd'){
        if( 'x'  !== $thisMenu){
            return parent::callMenu($topMenu, $midMenu, $thisMenu, $adminMenuType);
        }
    }

    public function getTopMenu($adminMenuType = 'd')
    {
        $arrWhere['adminMenuType'] = $adminMenuType;
        $arrWhere['adminMenuEcKind'] = $this->ecKind;
        $this->setAdminMenuWhere($arrWhere);
        // 메뉴 Depth 구분
        $this->arrWhere[] = 'am.adminMenuDepth = ?';
        $this->db->bind_param_push($this->arrBind, 'i', 1);

        //버전별 메뉴 숨김
        $srcVersion = GodoUtils::getSrcVersion();
        $this->arrWhere[] = 'INSTR(am.adminMenuHideVersion, ?) < 1 ';
        $this->arrWhere[] = 'INSTR(am.adminMenuHideVersion, ?) < 1 ';
        $this->db->bind_param_push($this->arrBind, 's', $srcVersion);
        $this->db->bind_param_push($this->arrBind, 's', $srcVersion);

        $this->db->strOrder = 'adminMenuSort';

        $this->db->strWhere = implode(' AND ', $this->arrWhere);
        $query = $this->db->query_complete();
        $strSQL = 'SELECT ' . array_shift($query) . ' FROM ' . DB_ADMIN_MENU . ' as am ' . implode(' ', $query);
        //gd_debug($this->arrBind);
        //gd_debug($strSQL);
        $getData = $this->db->query_fetch($strSQL, $this->arrBind);
        unset($this->arrBind);
        unset($this->arrWhere);

        foreach ($getData as $topKey => $topVal) {
            if ($topVal['adminMenuSettingType'] === 'p') {
                if (GodoUtils::isPlusShop($topVal['adminMenuPlusCode']) === false) {
                    unset($getData[$topKey]);
                } else {
                    // 관리자 상단에 출력되는 전체 메뉴갯수 count - plusshop메뉴일 경우
                    if ($topVal['adminMenuDisplayType'] === 'y') {
                        $this->menuCnt++;
                    }
                }
            } else {
                // 관리자 상단에 출력되는 전체 메뉴갯수 count - 일반메뉴일 경우
                if ($topVal['adminMenuDisplayType'] === 'y') {
                    $this->menuCnt++;
                }
            }
        }

        $getTopData['data'] = $getData;
        if ($adminMenuType == 's') {
            $getTopData['link'] = URI_PROVIDER;
        } else {
            $getTopData['link'] = URI_ADMIN;
        }

        return gd_htmlspecialchars_stripslashes($getTopData);
    }

    /**
     * 메뉴 반환
     * @param string $adminMenuType
     * @param null $topMenu
     * @param null $plusShopType
     * @param bool $isStrip
     * @param false $showAllMenu
     * @return array
     *
     * slab01211 진행준비,  slab01217 고객사미팅,  slab01212 기획,     slab01213  제안,  slab01218  제안서확정
     * slab01214 샘플제안,  slab01215 샘플확정,    slab01216 발주대기, slab01220  발주,  slab01219  발주완료
     */
    public function getAdminMenuList($adminMenuType = 'd', $topMenu = null, $plusShopType = null, $isStrip = true, $showAllMenu = false)
    {
        $refineMenuList = [];
        $menuList = parent::getAdminMenuList($adminMenuType, $topMenu, $plusShopType, $isStrip, $showAllMenu);

        $managerInfo = SlCommonUtil::getManagerInfo();
        $exclude = [
            //'02001002' => ['slab01215'], //디자인
            //'02001002' => ['slab01310'], //디자인
            //'02001003' => ['slab01217','slab01212','slab01215','slab01216'], //QC
        ];

        if( !empty($exclude[$managerInfo['departmentCd']]) ){
            foreach($menuList as $menu){
                if( !in_array($menu['tNo'], $exclude[$managerInfo['departmentCd']])){
                    $refineMenuList[] = $menu;
                }
            }

        }else{
            $refineMenuList = $menuList;
        }

        return $refineMenuList;
    }

}
