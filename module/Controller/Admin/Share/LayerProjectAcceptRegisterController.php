<?php

namespace Controller\Admin\Share;

use Component\Work\WorkCodeMap;
use Request;
use SiteLabUtil\SlCommonUtil;
use SlComponent\Database\DBUtil2;
use SlComponent\Util\SlLoader;
use SlComponent\Util\SlProjectCodeMap;

/**
 * 프로젝트 승인권자 등록 레이어
 * @package Controller\Admin\Share
 */
class LayerProjectAcceptRegisterController extends \Controller\Admin\Controller{

	/**
	 * {@inheritdoc}
	 */
	public function index()
	{
	    $workControllerService = SlLoader::cLoad('work','workControllerService','');
        $workControllerService->setControllerData($this);

        // --- 관리자 디자인 템플릿
        $this->getView()->setDefine('layout', 'layout_layer.php');

        $this->setData('managerList', SlCommonUtil::getManagerList());
        $sno = Request::request()->get('sno');
        $this->setData('sno', $sno);

        if( !empty($sno) ){
            $this->setData('acceptData', DBUtil2::getOne('sl_workAcceptLine', 'sno', $sno) );
        }
	}
}
