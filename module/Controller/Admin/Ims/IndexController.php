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

namespace Controller\Admin\Ims;


use Component\Ims\ImsCodeMap;
use SiteLabUtil\SlCommonUtil;

class IndexController extends \Controller\Admin\Controller
{
    public function index()
    {
        //아이디에 따라 다르게 보이기.
        if( slcommonutil::isfactory() ){
            $this->redirect('ims_production_list.php');
        }else{
            /*$dept = SlCommonUtil::getManagerInfo()['departmentCd'];
            if( '02001002' == $dept ){
                //디자인실 리다이렉트
                $this->redirect('../ims/ims_list_design.php');
            }else if( '02001003' == $dept ){
                //QC 리다이렉트
                $this->redirect('../ims/ims_list_qc.php');
            }else{
                //기타
                $this->redirect('../ims/ims_list_sales.php');
            }*/

            $this->redirect('../ims/ims_list_all.php');

        }
        exit;
    }
}
