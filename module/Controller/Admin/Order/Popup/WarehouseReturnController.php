<?php

namespace Controller\Admin\Order\Popup;

use App;
use Globals;
use Request;
use SlComponent\Database\DBUtil2;
use SlComponent\Database\SearchVo;
use SlComponent\Util\SlLoader;

class WarehouseReturnController extends \Controller\Admin\Controller{

    public function index(){
        $this->addScript([
            '../../script/vue.js',
        ]);
        $this->addCss([
            '../../css/preloader.css',
        ]);


        $getValue = Request::get()->toArray();

        //공급사 리스트
        $scmAdmin = \App::load(\Component\Scm\ScmAdmin::class);
        $this->setData('scmList',$scmAdmin->getSelectScmList());


        $erpService = SlLoader::cLoad('erp','erpService');
        $claimData = $erpService->getWherehouseReturnData($getValue);
        $this->setData('claimData', $claimData);

        $this->getView()->setDefine('layout', 'layout_blank.php');
    }


}