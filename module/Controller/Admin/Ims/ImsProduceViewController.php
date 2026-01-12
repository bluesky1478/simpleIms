<?php

namespace Controller\Admin\Ims;

use App;
use Component\Category\BrandAdmin;
use Component\Category\CategoryAdmin;
use Component\Ims\EnumType\PREPARED_TYPE;
use Component\Ims\ImsCodeMap;
use Component\Ims\ImsJsonSchema;
use Component\Stock\StockListService;
use Component\Work\WorkCodeMap;
use Controller\Admin\Erp\AdminErpControllerTrait;
use Controller\Admin\Erp\ControllerService\InoutListService;
use Globals;
use Request;
use SiteLabUtil\SlCommonUtil;
use SlComponent\Util\ExcelCsvUtil;
use SlComponent\Util\SlCodeMap;
use SlComponent\Util\SlLoader;
use function SlComponent\Util\SlLoader;

/**
 * 생산 상세
 */
class ImsProduceViewController extends \Controller\Admin\Controller{

    use ImsControllerTrait;

    public function index(){
        $request=\Request::get()->toArray();

        if( !empty($request['popup']) ){
            $this->getView()->setDefine('layout', 'layout_blank.php');
        }

        $request=\Request::get()->toArray();
        $this->setProduceListRelatedController($request);

        $this->setDefault();
        $this->setData('designField',ImsCodeMap::PROJECT_DESIGN_FIELD);
        $this->setData('addedInfo', ImsJsonSchema::ADD_INFO);
    }

}