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
use SiteLabUtil\SlCommonUtil;
use SlComponent\Util\SlCodeMap;
use SlComponent\Util\SlSkinUtil;
use UserFilePath;

/**
 * 데모 페이지
 *
 * @author Jong-tae Ahn <qnibus@godo.co.kr>
 */
class IcsPackingController extends \Controller\Front\Controller
{
    /**
     * {@inheritdoc}
     */
    public function index()
    {
        $this->setData('nasUrl', ImsCodeMap::NAS_URL);
        $this->setData('nasDownloadUrl',ImsCodeMap::NAS_DN_URL);
        $this->setData('nasAllDownloadUrl',ImsCodeMap::NAS_ALL_DN_URL);
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

        $requestList = \Request::request()->toArray();
        $iDeliverySno = (int)$requestList['no'];
        if ($iDeliverySno === 0) {
            echo "접근오류";
            exit;
        }
        $this->setData('iDeliverySno' , $iDeliverySno);
        $this->setData('requestParam' , $requestList);
        $this->setData('imsAjaxUrl' , SlCommonUtil::getHost().'/ics/ics_ps.php');
        $this->setData('myHost' , \Request::getScheme()."://".\Request::getDefaultHost());
        $managerId = \Session::get('manager.managerId');
        $this->setData('managerId',$managerId);
    }
}
