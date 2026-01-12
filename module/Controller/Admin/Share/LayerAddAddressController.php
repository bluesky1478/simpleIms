<?php

namespace Controller\Admin\Share;

use Request;
use SlComponent\Database\DBUtil2;

/**
 * 무상정책 화면
 * Class LayerFreePolicyController
 * @package Controller\Admin\Share
 */
class LayerAddAddressController extends \Controller\Admin\Controller{

	/**
	 * {@inheritdoc}
	 */
	public function index()
	{
        $getValue = Request::get()->toArray();
        $this->setData('scmNo', $getValue['scmNo']);
        $this->setData('sno', $getValue['sno']);

        if( !empty($getValue['sno']) ){
            $addressData = DBUtil2::getOne('sl_setScmDeliveryList', 'sno', $getValue['sno']);
            $this->setData('addressData', $addressData);
        }

		// --- 관리자 디자인 템플릿
		$this->getView()->setDefine('layout', 'layout_layer.php');
	}
}
