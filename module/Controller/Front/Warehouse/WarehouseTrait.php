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

namespace Controller\Front\Warehouse;

use SlComponent\Util\SlLoader;

Trait WarehouseTrait {

    public function index() {
        //로그인 여부 확인
        if( empty(\Session::get('manager'))){
            $linkUrl = \Request::getPhpSelf() . '?' . \Request::getQueryString();
            $this->redirect('login.php?linkUrl=' . $linkUrl);
            exit();
        }else{
            $workControllerService=SlLoader::cLoad('work','workControllerService','');
            $workControllerService->setControllerData($this);
            $this->workIndex();
        }
    }

    public function setMenu($docDept, $docType){
        $projectListMap = [
            1=>[
                'name' => '송장등록',
                'accept' => 'y',
                'href' => 'invoice_req.php',
            ],
            2=>[
                'name' => '반품관리',
                'accept' => 'y',
                'href' => 'return_list.php',
            ],
        ];
        if( 'PROJECT' === $docDept ){
            $projectListMap[$docType]['active']='active';
        }
        $menuList = [
            'PROJECT' => [
                'title' => '주문관리',
                'subMenuList' => $projectListMap,
            ],
        ];
        $this->setData('title' , $menuList[$docDept]['title']);
        $this->setData('titleSub', $menuList[$docDept]['subMenuList'][$docType]['name']);
        $this->setData('menuList', $menuList);
    }

}
