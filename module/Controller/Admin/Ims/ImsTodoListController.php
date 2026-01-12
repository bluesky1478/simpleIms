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
class ImsTodoListController extends \Controller\Admin\Controller{

    use ImsControllerTrait;

    public function index(){
        $request = \Request::request()->toArray();
        $this->callMenu('ims', 'board', gd_isset($request['tabMode'],'approval'));
        $this->setDefault();

        //이건 공통 되나 ? (결재, 나의, 받은)
        $search['combineSearch'] = [
            'a.subject' => '제목',
            'd.managerNm' => '대상자(받는사람)',
            'c.managerNm' => '요청자',
            'cust.customerName' => '고객명',
            'prj.sno' => '프로젝트번호',
        ];
        $this->setData('search', $search);
    }

}