<?php

namespace Controller\Admin\Share;

use Request;
use SlComponent\Util\SlCode;
use SlComponent\Util\SlCodeMap;
use SlComponent\Util\SlLoader;

/**
 * 클레임 처리 화면
 * Class LayerFreePolicyController
 * @package Controller\Admin\Share
 */
class LayerOrderClaimController extends \Controller\Admin\Controller{

	/**
	 * {@inheritdoc}
	 */
	public function index()
	{
        $getValue = Request::get()->toArray();
        $this->setData('sno',$getValue['sno']);
        $this->setData('orderNo',$getValue['orderNo']);
        $this->setData('claimType',$getValue['claimType']);
        $this->setData('claimTypeStr',$getValue['claimTypeStr']);

		$this->addScript([
			'bootstrap/bootstrap-table.js',
			'jquery/jquery.tablednd.js',
			'bootstrap/bootstrap-table-reorder-rows.js',
		]);

		// --- 관리자 디자인 템플릿
		$this->getView()->setDefine('layout', 'layout_layer.php');

        $claimService = SlLoader::cLoad('claim','claimService');;
        $data = $claimService->getClaimContents($getValue);
        $this->setData('data',$data);
        $this->setData('reqTypeContents',$claimService->getReqTypeMap($getValue));
        $this->setData('procStatusMap',SlCodeMap::CLAIM_STATUS);

		// 공급사와 동일한 페이지 사용
		//$this->getView()->setPageName('sitelab/layer/layer_free_policy.php');
	}
}
