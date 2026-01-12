<?php

namespace Controller\Admin\Share;

/**
 * 회원연결 화면
 * Class LayerPolicyLinkMemberController
 * @package Controller\Admin\Share
 */
class LayerPolicyLinkMemberController extends \Controller\Admin\Controller{

	/**
	 * {@inheritdoc}
	 */
	public function index()
	{
	    $scmAdmin = \App::load(\Component\Scm\ScmAdmin::class);

		$this->addScript([
			'bootstrap/bootstrap-table.js',
			'jquery/jquery.tablednd.js',
			'bootstrap/bootstrap-table-reorder-rows.js',
		]);

		// --- 관리자 디자인 템플릿
		$this->getView()->setDefine('layout', 'layout_layer.php');

		// 공급사와 동일한 페이지 사용
		$this->getView()->setPageName('sitelab/layer/layer_policy_link_member.php');

		//공급사 리스트
        $this->setData('scmList',$scmAdmin->getSelectScmList());

	}
}
