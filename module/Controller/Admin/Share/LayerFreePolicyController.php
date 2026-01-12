<?php

namespace Controller\Admin\Share;

use Request;

/**
 * 무상정책 화면
 * Class LayerFreePolicyController
 * @package Controller\Admin\Share
 */
class LayerFreePolicyController extends \Controller\Admin\Controller{

	/**
	 * {@inheritdoc}
	 */
	public function index()
	{
        $getValue = Request::get()->toArray();
        $this->setData('selectedGoodsCnt',$getValue['selectedGoodsCnt']);

		$this->addScript([
			'bootstrap/bootstrap-table.js',
			'jquery/jquery.tablednd.js',
			'bootstrap/bootstrap-table-reorder-rows.js',
		]);

		// --- 관리자 디자인 템플릿
		$this->getView()->setDefine('layout', 'layout_layer.php');

		// 공급사와 동일한 페이지 사용
		$this->getView()->setPageName('sitelab/layer/layer_free_policy.php');
	}
}
