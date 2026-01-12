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
 * @link      http://www.godo.co.kr
 */

namespace Controller\Admin\Ims;


use Component\Erp\ErpCodeMap;
use Component\Ims\EnumType\PREPARED_TYPE;
use Component\Ims\ImsApprovalService;
use Component\Ims\ImsCodeMap;
use Component\Ims\ImsDBName;
use Component\Ims\ImsEworkService;
use Component\Work\DocumentCodeMap;
use Component\Work\WorkCodeMap;
use SiteLabUtil\SlCommonUtil;
use SlComponent\Database\DBUtil2;
use SlComponent\Database\SearchVo;
use SlComponent\Database\TableVo;
use SlComponent\Util\SlCodeMap;
use SlComponent\Util\SlLoader;
use Request;
use SlComponent\Util\SlProjectCodeMap;
use Bundle\Component\CurrencyExchangeRate\CurrencyExchangeRate;
use Bundle\Component\CurrencyExchangeRate\CurrencyExchangeRateAdmin;
use Framework\Debug\Exception\LayerException;
use UserFilePath;

trait IcsControllerTrait {
    public function setDefault(){
        $this->setData('nasUrl','https://innoversftp.synology.me/ims');
        $this->setData('nasDownloadUrl','https://innoversftp.synology.me/ims/download.php?');
        $this->setData('nasAllDownloadUrl','https://innoversftp.synology.me/ims/download.php?');
        $this->setData('adminHost' , "http://gdadmin.".\Request::getDefaultHost());

        $this->setData('eworkType', ImsEworkService::getEworkTypeList());
        $this->setData('eworkTypeKr', ImsEworkService::getEworkTypeList(1));

        $this->setData('isDevId', SlCommonUtil::isDevId());

        $isProduce = SlCommonUtil::isFactory();
        $this->setData('isProduce', $isProduce);

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
        $this->setData('requestParam' , $requestList);

        $this->setData('imsAjaxUrl' , SlCommonUtil::getHost().'/ics/ics_ps.php');
        $this->setData('myHost' , \Request::getScheme()."://".\Request::getDefaultHost());

        $managerId = \Session::get('manager.managerId');
        $this->setData('managerId',$managerId);
    }

}
