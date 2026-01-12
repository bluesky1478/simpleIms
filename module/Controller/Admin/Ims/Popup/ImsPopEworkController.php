<?php

namespace Controller\Admin\Ims\Popup;

use App;
use Component\Category\BrandAdmin;
use Component\Category\CategoryAdmin;
use Component\Database\DBTableField;
use Component\Ims\ImsCodeMap;
use Component\Ims\ImsCustomerEstimateService;
use Component\Ims\ImsDBName;
use Component\Stock\StockListService;
use Component\Work\WorkCodeMap;
use Controller\Admin\Erp\AdminErpControllerTrait;
use Controller\Admin\Erp\ControllerService\InoutListService;
use Controller\Admin\Ims\ImsControllerTrait;
use Globals;
use Request;
use SiteLabUtil\SlCommonUtil;
use SlComponent\Database\DBUtil2;
use SlComponent\Database\SearchVo;
use SlComponent\Godo\ControllerService;
use SlComponent\Util\ExcelCsvUtil;
use SlComponent\Util\SitelabLogger;
use SlComponent\Util\SlCodeMap;
use SlComponent\Util\SlLoader;
use function SlComponent\Util\SlLoader;

/**
 * 문서 리스트
 */
class ImsPopEworkController extends \Controller\Admin\Controller{

    use ImsControllerTrait;

    public function index(){
        $this->setDefault();
        $this->getView()->setDefine('layout', 'layout_blank.php');
        $this->setData('estimateDataScheme', json_encode(ImsCustomerEstimateService::getDefaultData()));
        ControllerService::setReloadData($this);

        $styleSno = \Request::get()->get('sno');
        if( 0 >= DBUtil2::getCount(ImsDBName::EWORK, new SearchVo('styleSno=?', $styleSno)) ){
            DBUtil2::insert(ImsDBName::EWORK, ['styleSno'=>$styleSno, 'writeDt'=>SlCommonUtil::getNowDate()]);
        }

        $imsService = SlLoader::cLoad('ims', 'imsService');
        $this->setData('revReasonList', $imsService->getCode('workRev','작지변경사유'));
        $this->setData('revTypeList', $imsService->getCode('workRevType','작지변경구분'));

    }

}