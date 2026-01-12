<?php

namespace Controller\Admin\Ims;

use App;
use Component\Category\BrandAdmin;
use Component\Category\CategoryAdmin;
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
class ImsProjectRegController extends \Controller\Admin\Controller{

    use ImsControllerTrait;

    public function index(){
        $request=\Request::get()->toArray();
        $this->setProjectListRelatedController($request);

        $this->setDefault();

        if(!empty($this->getData('requestParam')['sno'])){
            $this->setData('title', '프로젝트 정보');    
            $this->setData('saveBtnTitle', '수정');
        }else{
            $this->setData('title', '신규 프로젝트 등록');
            $this->setData('saveBtnTitle', '저장');
        }

        $this->setData('designField',ImsCodeMap::PROJECT_DESIGN_FIELD);
        $this->setData('addedInfo', ImsJsonSchema::ADD_INFO);

        if(!empty(\Request::get()->get('popup'))){
            $this->getView()->setDefine('layout', 'layout_blank.php');
        }

    }

}