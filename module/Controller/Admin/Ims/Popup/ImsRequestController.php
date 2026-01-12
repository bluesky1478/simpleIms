<?php

namespace Controller\Admin\Ims\Popup;

use App;
use Component\Category\BrandAdmin;
use Component\Category\CategoryAdmin;
use Component\Stock\StockListService;
use Component\Work\WorkCodeMap;
use Controller\Admin\Erp\AdminErpControllerTrait;
use Controller\Admin\Erp\ControllerService\InoutListService;
use Controller\Admin\Ims\ImsControllerTrait;
use Globals;
use Request;
use SiteLabUtil\SlCommonUtil;
use SlComponent\Util\ExcelCsvUtil;
use SlComponent\Util\SlCodeMap;
use SlComponent\Util\SlLoader;
use function SlComponent\Util\SlLoader;

/**
 * 선행작업 요청
 */
class ImsRequestController extends \Controller\Admin\Controller{

    use ImsControllerTrait;

    public function index(){
        $this->setDefault();
        $this->getView()->setDefine('layout', 'layout_blank.php');
        //$imsService = SlLoader::cLoad('ims', 'imsService');
        $requestParam = $this->getData('requestParam');

        $fileConst = 'Component\Ims\ImsCodeMap::PREPARED_FILE_'.strtoupper($requestParam['reqType']);
        $this->setData('PREPARED_FILE', constant($fileConst));

        $typeConst = 'Component\Ims\EnumType\PREPARED_TYPE::'.strtoupper($requestParam['reqType']);
        $this->setData('title', constant($typeConst)['title']);

        if( 'work' === $requestParam['reqType'] ){
            $this->setData('includeContents',  'ims_request_'.$requestParam['reqType'].'.php' );
            $this->getView()->setPageName("ims/popup/ims_request_ms.php");
        }else{
            if( $this->getData('isProduceCompany') ){
                $this->setData('includeContents',  'ims_request_'.$requestParam['reqType'].'_prd.php' );
                $this->getView()->setPageName("ims/popup/ims_request_produce.php");
            }else{
                $this->setData('includeContents',  'ims_request_'.$requestParam['reqType'].'.php' );
                $this->getView()->setPageName("ims/popup/ims_request_ms.php");
            }
        }

    }

}