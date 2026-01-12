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
namespace Controller\Front\Ics;

use Component\Ims\ImsCodeMap;
use Component\Member\Util\MemberUtil;
use Controller\Admin\Ims\IcsControllerTrait;
use SiteLabUtil\SlCommonUtil;
use SlComponent\Util\SlCodeMap;
use SlComponent\Util\SlSkinUtil;
use UserFilePath;

/**
 * 데모 페이지
 *
 * @author Jong-tae Ahn <qnibus@godo.co.kr>
 */
class IcsGuideController extends \Controller\Front\Controller
{
    use IcsControllerTrait;

    /**
     * {@inheritdoc}
     */
    public function index()
    {
        $this->setDefault();
        $request = \Request::request()->toArray();
        $this->setData('projectSno' , SlCommonUtil::aesDecrypt($request['key']));
        $this->setData('managerScmNo' , SlCommonUtil::getManagerInfo()['scmNo']);
    }
}
