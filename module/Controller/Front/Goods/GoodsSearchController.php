<?php

namespace Controller\Front\Goods;

use Component\Member\Member;
use Component\Member\Util\MemberUtil;
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
use SlComponent\Database\DBUtil;
use SlComponent\Util\SlLoader;
use SlComponent\Util\SlSkinUtil;

/**
 * 주문 상세 페이지
 * [관리자 모드] 주문 상세 페이지
 *
 * @package Bundle\Controller\Admin\Order
 * @author  Jong-tae Ahn <qnibus@godo.co.kr>
 */
class GoodsSearchController extends \Bundle\Controller\Front\Goods\GoodsSearchController{

    public function index(){
        try {
            //카테고리 고정.
            \Request::get()->set('cateGoods',[MemberUtil::getMemberScmData(\Session::get('member.memNo'))['cateCd']]);

            parent::index();
            $this->setData('otherSkin', SlSkinUtil::getOtherSkinName());
            $scmService=SlLoader::cLoad('godo','scmService','sl');
            $scmService->setListTkeFixedPrice($this);
        } catch (\Exception $e) {
            throw new AlertRedirectException($e->getMessage(),null,null,"/");
        }
    }

}