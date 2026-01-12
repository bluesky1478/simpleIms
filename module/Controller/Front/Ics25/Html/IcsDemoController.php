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
namespace Controller\Front\Ics25\Html;

use Component\Ims\ImsCodeMap;
use Controller\Admin\Ims\IcsControllerTrait;
use Controller\Admin\Ims\ImsControllerTrait;
use SiteLabUtil\SlCommonUtil;
use SlComponent\Util\SlCodeMap;
use SlComponent\Util\SlSkinUtil;
use UserFilePath;

/**
 * 데모 페이지
 *
 * @author Jong-tae Ahn <qnibus@godo.co.kr>
 */
class IcsDemoController extends \Controller\Front\Controller
{
    use IcsControllerTrait;

    /**
     * {@inheritdoc}
     */
    public function index()
    {
        $this->setDefault();
    }
}
