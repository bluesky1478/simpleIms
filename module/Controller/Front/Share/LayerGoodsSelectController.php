<?php

/**
 * This is commercial software, only users who have purchased a valid license
 * and accept to the terms of the License Agreement can install and use this
 * program.
 *
 * Do not edit or add to this file if you wish to upgrade Godomall5 to newer
 * versions in the future.
 *
 * @copyright ⓒ 2016, NHN godo: Corp.
 * @link http://www.godo.co.kr
 */
namespace Controller\Front\Share;

use Component\Member\Util\MemberUtil;
use Request;
use SlComponent\Util\SlCodeMap;
use SlComponent\Util\SlLoader;

/**
 * 사이트 접속 페이지
 *
 * @author Jong-tae Ahn <qnibus@godo.co.kr>
 */
class LayerGoodsSelectController extends \Controller\Front\Controller
{
    public function index(){
        $bdId = \Request::post()->get('bdId');
        $bdSno = \Request::post()->get('bdSno');
        $isPlusReview = \Request::post()->get('isPlusReview');
        $target = \Request::post()->get('target');
        $this->setData('bdId',$bdId);
        $this->setData('bdSno',$bdSno);
        $this->setData('isPlusReview', $isPlusReview);
        $cateId = \Request::post()->get('selectId', null);
        $cate = \App::load('\\Component\\Category\\Category');
        $cateDisplay = $cate->getMultiCategoryBox($cateId,null,null,true);
        $this->setData('cateDisplay', gd_isset($cateDisplay));
        $this->setData('target', gd_isset($target));
        //$this->setData('selectedCategoryCode', SlCodeMap::SCM_CATEGORY__MAP[MemberUtil::getMemberScmNo()]);
        $scmService=SlLoader::cLoad('godo','scmService','sl');
        $this->setData('selectedCategoryCode', $scmService->getScmCategoryCode(MemberUtil::getMemberScmNo()));

        //클레임정보
        $this->setData('claimReason', SlCodeMap::CLAIM_REASON);
        $this->setData('orderNo', SlCodeMap::SCM_ORDER_INIT[MemberUtil::getMemberScmNo()]);
        
    }
}