<?php

namespace Controller\Admin\Sitelab;

use App;
use Request;

/**
 * @author  <tomi@godo.co.kr>
 */
class LayerGoodsListOptionSimpleController extends \Controller\Admin\Controller
{
	public function index()
	{
		$postValue = Request::post()->toArray();
		$goodsNo = $postValue['goodsNo'];
		$goodsAdmin = \App::load('\\Component\\Goods\\GoodsAdmin');
		// 옵션명 로드
		$getGoodsOptionName = $goodsAdmin->getGoodsInfo($goodsNo)['optionName'];
		// 옵션 정보 로드

        $request = \App::getInstance('request');
        $mallSno = $request->get()->get('mallSno', 1);
        $code = \App::load('\\Component\\Code\\Code',$mallSno);
        $stockReason = $code->getGroupItems('05002');
        $stockReasonNew['y'] = $stockReason['05002001']; //정상은 코드 변경
        $stockReasonNew['n'] = $stockReason['05002002']; //품절은 코드 변경
        unset($stockReason['05002001']);
        unset($stockReason['05002002']);
        $stockReason = array_merge($stockReasonNew, $stockReason);

		$this->setData('getGoodsOptionName', explode(STR_DIVISION, $getGoodsOptionName));
        $goodsOptionInfo = $goodsAdmin->getGoodsOptionWithSafeCnt($goodsNo);
		//gd_debug($goodsOptionInfo);
		$this->setData('goodsOptionInfo', $goodsOptionInfo);
		$this->setData('goodsNo', $goodsNo);
        $this->setData('stockReason', $stockReason);

		$this->getView()->setDefine('layout', 'layout_layer.php');

		//safeStockCnt


		// 공급사와 동일한 페이지 사용
		$this->getView()->setPageName('sitelab/layer_goods_list_option_simple.php');
	}
}
