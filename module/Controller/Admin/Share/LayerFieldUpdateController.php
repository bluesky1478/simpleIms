<?php

namespace Controller\Admin\Share;

/**
 * 회원연결 화면
 * Class LayerPolicyLinkMemberController
 * @package Controller\Admin\Share
 */
class LayerFieldUpdateController extends \Controller\Admin\Controller{

	/**
	 * {@inheritdoc}
	 */
	public function index()
	{

	    $request = \Request::get()->toArray();

        if( 'margin' == $request['key'] ){
            $request['dataValue'] = preg_replace("/[^0-9]*/s", "", $request['dataValue']);
        }

	    $this->setData('request', $request);

		$this->addScript([
			'bootstrap/bootstrap-table.js',
			'jquery/jquery.tablednd.js',
			'bootstrap/bootstrap-table-reorder-rows.js',
		]);

		// --- 관리자 디자인 템플릿
		$this->getView()->setDefine('layout', 'layout_layer.php');

	}
}
