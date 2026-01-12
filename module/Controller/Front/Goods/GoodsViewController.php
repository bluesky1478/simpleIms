<?php

namespace Controller\Front\Goods;

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
use SiteLabUtil\SlCommonUtil;
use SlComponent\Database\DBUtil2;
use SlComponent\Util\SlCode;
use SlComponent\Util\SlCodeMap;
use SlComponent\Util\SlLoader;
use SlComponent\Util\SlSkinUtil;

/**
 * 주문 상세 페이지
 * [관리자 모드] 주문 상세 페이지
 *
 * @package Bundle\Controller\Admin\Order
 * @author  Jong-tae Ahn <qnibus@godo.co.kr>
 */
class GoodsViewController extends \Bundle\Controller\Front\Goods\GoodsViewController{

    public function index(){
        try {

            if( MemberUtil::isLogin() ){
                parent::index();
                $this->setData('otherSkin', SlSkinUtil::getOtherSkinName());
                $this->addScript([  'gd_goods_view.js' ]);

                $scmService=SlLoader::cLoad('godo','scmService','sl');
                $scmService->setRefineGoodsOption($this);
                $scmService->setTkeFixedPrice($this);
                $scmService->setRestockReq($this);

                if( 21 == MemberUtil::getMemberScmNo(\Session::get('member.memNo')) ){
                    $this->getView()->setPageName('goods/goods_view_oek');
                }

                $this->setData('memberId', \Session::get('member.memId'));

            }else{
                if( empty(SlCodeMap::NO_LOGIN_VIEW_SITE[URI_HOME])){
                    throw new AlertRedirectException('로그인을 하셔야 구매 가능합니다.',null,null,'../member/login.php');
                }else{
                    parent::index();
                }
            }
        } catch (AlertRedirectException $ae) {
            if( empty(SlCodeMap::NO_LOGIN_VIEW_SITE[URI_HOME])){
                throw new AlertRedirectException('로그인을 하셔야 구매 가능합니다.',null,null,'../member/login.php');
            }
        } catch (\Exception $e) {
            throw new AlertRedirectException($e->getMessage(),null,null,"/");
        }
    }

}