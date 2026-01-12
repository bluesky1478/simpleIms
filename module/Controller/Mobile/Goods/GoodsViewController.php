<?php

namespace Controller\Mobile\Goods;

use Framework\Debug\Exception\AlertBackException;
use Framework\Debug\Exception\AlertOnlyException;
use Framework\Debug\Exception\AlertRedirectException;
use Framework\Debug\Exception\Framework\Debug\Exception;
use Message;
use Globals;
use Request;
use Cookie;
use Framework\Utility\StringUtils;
use Framework\Utility\SkinUtils;
use SlComponent\Util\SlLoader;
use SlComponent\Util\SlSkinUtil;

/**
 * 주문 상세 페이지
 * [관리자 모드] 주문 상세 페이지
 *
 * @package Bundle\Controller\Admin\Order
 * @author  Jong-tae Ahn <qnibus@godo.co.kr>
 */
class GoodsViewController extends \Bundle\Controller\Mobile\Goods\GoodsViewController{

    public function index(){
        try {
            parent::index();
            $this->setData('otherSkin', SlSkinUtil::getOtherSkinName());

            $scmService=SlLoader::cLoad('godo','scmService','sl');
            $scmService->setRefineGoodsOption($this);
            $scmService->setTkeFixedPrice($this);
            $scmService->setRestockReq($this);
            /*$goodsViewData = $this->getData('goodsView');
            $goodsViewData['maxOrderCnt'] = 1;
            $this->setData('goodsView',$goodsViewData);*/

        } catch (\Exception $e) {
            throw new AlertRedirectException($e->getMessage(),null,null,"/");
        }
    }

}