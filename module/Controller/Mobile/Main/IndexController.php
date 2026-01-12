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
namespace Controller\Mobile\Main;

use Component\Scm\ScmControllerTrait;
use SlComponent\Util\SlCodeMap;
use SlComponent\Util\SlSkinUtil;

/**
 * 사이트 접속 페이지
 *
 * @author Jong-tae Ahn <qnibus@godo.co.kr>
 */
class IndexController extends \Bundle\Controller\Mobile\Main\IndexController
{
    use ScmControllerTrait;

    /**
     * {@inheritdoc}
     */
    public function index()
    {
        $this->indexCommon();
    }
}
