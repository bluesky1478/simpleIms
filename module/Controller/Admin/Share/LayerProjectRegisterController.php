<?php

namespace Controller\Admin\Share;

use Component\Work\WorkCodeMap;
use Request;
use SlComponent\Util\SlLoader;
use SlComponent\Util\SlProjectCodeMap;

/**
 * 프로젝트 등록 레이어
 * @package Controller\Admin\Share
 */
class LayerProjectRegisterController extends \Controller\Admin\Controller{

	/**
	 * {@inheritdoc}
	 */
	public function index()
	{
        $param = Request::request()->toArray();

        $this->setData('prjStatus', SlProjectCodeMap::PRJ_STATUS);

		// --- 관리자 디자인 템플릿
		$this->getView()->setDefine('layout', 'layout_layer.php');


		$workService = SlLoader::cLoad('work','workService','');
        $this->setData('companyListMap', $workService->getCompanyMap());
        $this->setData('typeListMap', WorkCodeMap::MS_PROPOSAL_TYPE);

		// 공급사와 동일한 페이지 사용
		//$this->getView()->setPageName('sitelab/layer/layer_sale_policy.php');
	}
}
