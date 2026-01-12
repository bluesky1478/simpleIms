<?php

namespace Controller\Admin\Share;

use Request;

/**
 * 할인정책 화면
 * Class LayerSalePolicyController
 * @package Controller\Admin\Share
 */
class LayerSalePolicyController extends \Controller\Admin\Controller{

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
		$this->getView()->setPageName('sitelab/layer/layer_sale_policy.php');
	}
}
