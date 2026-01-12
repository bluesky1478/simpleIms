<?php

namespace Controller\Admin\Ims;

use App;
use Component\Category\BrandAdmin;
use Component\Category\CategoryAdmin;
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
class ImsCustomerRegController extends \Controller\Admin\Controller{

    use ImsControllerTrait;

    public function index(){
        $this->callMenu('ims', 'customer', 'list');
        $this->setDefault();

        if(!empty($this->getData('requestParam')['sno'])){
            $this->setData('title', '고객사 정보');    
            $this->setData('saveBtnTitle', '수정');
        }else{
            $this->setData('title', '고객사 등록');
            $this->setData('saveBtnTitle', '저장');
        }

        if(!empty(\Request::get()->get('popup'))){
            $this->getView()->setDefine('layout', 'layout_blank.php');
        }
    }

}