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
use Component\Board\BoardConfig;

/**
 * 사이트 접속 페이지
 *
 * @author Jong-tae Ahn <qnibus@godo.co.kr>
 */
class LayerOrderGoodsSelectController extends \Controller\Front\Controller
{
    public function index(){

        $locale = \Globals::get('gGlobal.locale') == 'en' ? 'en-gb' :  \Globals::get('gGlobal.locale') ;
        $bdId = \Request::post()->get('bdId');
        $boardConfig = new BoardConfig($bdId);
        $bdSno = \Request::post()->get('bdSno');
        $this->setData('orderDuplication',$boardConfig->cfg['orderDuplication']);
        $this->setData('bdId',$bdId);
        $this->setData('bdSno',$bdSno);
        $this->setData('locale',$locale);

        //클레임정보
        $this->setData('claimReason', SlCodeMap::CLAIM_REASON);

    }
}