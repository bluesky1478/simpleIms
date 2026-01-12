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

use SiteLabUtil\SlCommonUtil;
use SlComponent\Util\SlCodeMap;
use SlComponent\Util\SlSkinUtil;
use UserFilePath;

/**
 * 고객 견적
 *
 * @author Jong-tae Ahn <qnibus@godo.co.kr>
 */
class CustomerEstimateController extends \Controller\Front\Controller
{
    /**
     * {@inheritdoc}
     */
    public function index()
    {
        //gd_debug( \Request::getReferer() );
        $basicData = gd_policy('basic.info');
        $this->setData('defaultInfo', gd_isset($basicData));
        if (empty($taxInvoice['taxStampImage']) === false) {
            $sealPath = UserFilePath::data('etc', $taxInvoice['taxStampIamge'])->www();
        } else if (empty($basicData['stampImage']) === false) {
            $sealPath = UserFilePath::data('etc', $basicData['stampImage'])->www();
        } else {
            $sealPath = '';
        }
        unset($taxInvoice, $basicData);
        $this->setData('sealPath', $sealPath);
        $this->getView()->setPageName('ics/customer_estimate2');
        //$this->getView()->setPageName('ics/main_template');

        $requestList = \Request::request()->toArray();
        $this->setData('requestParam' , $requestList);
        $this->setData('projectSno' , SlCommonUtil::aesDecrypt($requestList['key']));

        //gd_debug(SlCommonUtil::getHost().'/ims/ims_ps.php');
        $this->setData('imsAjaxUrl' , SlCommonUtil::getHost().'/ics/ics_ps.php');
        $this->setData('myHost' , \Request::getScheme()."://".\Request::getDefaultHost());

        $managerId = \Session::get('manager.managerId');
        $this->setData('managerId',$managerId);
        //gd_debug(SlCommonUtil::aesDecrypt(\Request::get()->get('key')));
    }
}
