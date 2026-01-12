<?php

namespace Controller\Admin\Share;

use Request;

/**
 * 무상정책 화면
 * Class LayerFreePolicyController
 * @package Controller\Admin\Share
 */
class LayerAddPaymentsController extends \Controller\Admin\Controller{

	/**
	 * {@inheritdoc}
	 */
	public function index()
	{
        $getValue = Request::get()->toArray();
        $this->setData('orderNo', $getValue['orderNo']);
        $this->setData('autoPaymentSubject', $getValue['autoPaymentSubject']);
        $this->setData('autoReqPrice', $getValue['autoReqPrice']);

		$this->addScript([
			'bootstrap/bootstrap-table.js',
			'jquery/jquery.tablednd.js',
			'bootstrap/bootstrap-table-reorder-rows.js',
		]);
		// --- 관리자 디자인 템플릿
		$this->getView()->setDefine('layout', 'layout_layer.php');
	}
}
