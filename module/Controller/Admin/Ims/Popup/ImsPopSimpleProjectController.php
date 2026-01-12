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
use Controller\Admin\Ims\Step\ImsStepTrait;
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
class ImsPopSimpleProjectController extends \Controller\Admin\Ims\ImsProjectViewController{

    use ImsControllerTrait;
    use ImsStepTrait;

    public function index(){
        parent::index();
        $this->getView()->setPageName("ims/ims_pop_simple_project.php");
        $fieldMap = [
            'all'       => $this->setupStep10(),//전체
            'step10' => $this->setupStep10(),//진행준비
            'step16' => $this->setupStep16(),//고객사미팅
            'step20' => $this->setList20(), //기획
            'step40' => $this->setList40(),//샘플제안
            'step41' => $this->setList41(),//샘플제안확정
            'step50' => $this->setList50(),//발주대기
            'step60' => $this->setList60(),//발주
        ];
        $this->setData('fieldList', $fieldMap);
    }

}