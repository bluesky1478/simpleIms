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
 * 문서 리스트
 */
class ImsRequestListController extends \Controller\Admin\Controller{

    use ImsControllerTrait;

    public function index(){
        $request = \Request::request()->toArray();
        $this->callMenu('ims', 'prepared', gd_isset($request['tabMode'],'qb'));
        $this->setDefault();

        $search['combineSearch'] = [
            'cust.customerName' => '고객명',
            'prj.sno' => '프로젝트번호',
            'prd.styleCode' => '스타일코드',
            'prd.productName' => '상품명',
        ];
        $this->setData('search', $search);

        /*if( !empty($request['popup']) ){
            $this->getView()->setDefine('layout', 'layout_blank.php');
        }*/
        /*if( $this->getData('isProduceCompany') ){
            $this->getView()->setDefine('layout', 'layout_blank.php');
            $this->getView()->setPageName("ims/ims_project_view_produce.php");
        }*/
        //$this->getView()->setPageName("ims/ims_project_view.php");
        //$this->getView()->setPageName("ims/ims_project_view2.php");
    }

}