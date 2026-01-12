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

namespace Controller\Admin\Base;

use Component\Ims\ImsCodeMap;
use Component\Member\Manager;
use Core\Base\View\Alert;
use Framework\Utility\StringUtils;
use SiteLabUtil\SlCommonUtil;
use SlComponent\Util\SlCodeMap;

/**
 * 관리자 메인 페이지
 *
 * @author yjwee <yeongjong.wee@godo.co.kr>
 * @author Jont-tae Ahn <qnibus@godo.co.kr>
 * @author Lee Namju <lnjts@godo.co.kr>
 * @author Shin Donggyu <artherot@godo.co.kr>
 */
class IndexController extends \Bundle\Controller\Admin\Base\IndexController
{
    public function index()
    {
        if( SlCommonUtil::isFactory() ){
            //생산처 리다이렉트
            $this->redirect('../ims/ims_production_list.php');
        }else{
            $managerInfo = SlCommonUtil::getManagerInfo();
            $dept = $managerInfo['departmentCd'];

            //현주씨 나는 그냥 메인
            //디자인실은 기획제작
            //나머지는 전체 리스트
            if( 1 != $managerInfo['sno'] && 20 != $managerInfo['sno'] && 76 != $managerInfo['sno']){
                if( '02001002' == $dept ){
                    $this->redirect('../ims/ims_list_design.php');
                }else{
                    $this->redirect('../ims/ims_list_all.php');
                }
            }
            //대표님 이사님 제외하고는 전체 리스트로
            /*if( ('02001005' == $dept || 'sbmoon' == $managerInfo['managerId'] || 'c_sjh' == $managerInfo['managerId'] ) && !SlCommonUtil::isDevId() ){
                $this->redirect('../ims/ims_list.php?status=15');
            }
            if( '02001005' != $dept && !SlCommonUtil::isDevId() ){
                $this->redirect('../ims/ims_list.php');
            }*/
        }
        parent::index();
    }
}
