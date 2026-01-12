<?php

namespace Controller\Admin\Share;

use Component\Ims\ImsCodeMap;
use SiteLabUtil\SlCommonUtil;
use SlComponent\Util\SlLoader;

/**
 * 회원연결 화면
 * Class LayerPolicyLinkMemberController
 * @package Controller\Admin\Share
 */
class LayerScheduleUpdateController extends \Controller\Admin\Controller{

	/**
	 * {@inheritdoc}
	 */
	public function index()
	{
	    $request = \Request::get()->toArray();
	    $this->setData('request', $request);

		$this->addScript([
			'bootstrap/bootstrap-table.js',
			'jquery/jquery.tablednd.js',
			'bootstrap/bootstrap-table-reorder-rows.js',
		]);

		// --- 관리자 디자인 템플릿
		$this->getView()->setDefine('layout', 'layout_layer.php');
        $this->setData('PRODUCE_STEP_MAP',ImsCodeMap::PRODUCE_STEP_MAP);

        $imsProduceService = SlLoader::cLoad('ims', 'imsProduceService');
        $data = $imsProduceService->getProduceData($request['sno']);

        $data['commentCnt'] = $imsProduceService->getCommentCount('produce',$request['sno']);

        $managerId = \Session::get('manager.managerId');
        $isProduce = in_array($managerId,ImsCodeMap::PRODUCE_COMPANY_MANAGER);
        //생산처 파우치
        $this->setData('imsProduceCompany',$isProduce);

        $this->setData('data', $data);

	}
}
