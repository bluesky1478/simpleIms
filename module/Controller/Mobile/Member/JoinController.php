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
namespace Controller\Mobile\Member;

use SlComponent\Util\SlCodeMap;
use SlComponent\Util\SlSkinUtil;

/**
 * 사이트 접속 페이지
 *
 * @author Jong-tae Ahn <qnibus@godo.co.kr>
 */
class JoinController extends \Bundle\Controller\Mobile\Member\JoinController
{
    /**
     * {@inheritdoc}
     */
    public function index()
    {
        $this->setData('otherSkin', SlSkinUtil::getOtherSkinName());
        parent::index();
        if(  'hankook' === SlSkinUtil::getOtherSkinName()  ){
            $this->setData('hankookType' , SlCodeMap::HANKOOK_TYPE);
        }
    }
}
