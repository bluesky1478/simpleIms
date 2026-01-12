<?php

namespace Controller\Admin\Ims;

use App;
use Component\Category\BrandAdmin;
use Component\Category\CategoryAdmin;
use Component\Ims\ImsCodeMap;
use Component\Stock\StockListService;
use Component\Work\WorkCodeMap;
use Controller\Admin\Erp\AdminErpControllerTrait;
use Controller\Admin\Erp\ControllerService\InoutListService;
use Controller\Admin\Ims\Step\ImsStepTrait;
use Globals;
use Request;
use SiteLabUtil\ImsUtil;
use SiteLabUtil\SlCommonUtil;
use SlComponent\Database\DBUtil2;
use SlComponent\Util\ExcelCsvUtil;
use SlComponent\Util\SitelabLogger;
use SlComponent\Util\SlCodeMap;
use SlComponent\Util\SlLoader;
use function SlComponent\Util\SlLoader;

/**
 * 프로젝트 리스트 25년 10월 Version (생산팀)
 */
class Ims25ListQcController extends \Controller\Admin\Controller{

    use ImsControllerTrait;
    use ImsStepTrait;
    use ImsListControllerTrait;

    public function index(){
        $this->callMenu('ims', 'list', 'qc');
        $this->setData('current', 'qc');
        $this->setDefault();

        //검색 조건 설정 (기본)
        $search['combineSearch'] = [
            'cust.customerName' => '고객명',
            'prj.sno' => '프로젝트번호',
            'sales.managerNm' => '영업담당자',
            'desg.managerNm' => '디자인담당자',
            'prd.styleCode' => '스타일코드',
            'prd.productName' => '스타일명',
        ];
        $this->setData('search', $search);
        $this->setEmergencyTodoList();
        $this->listDownload();
    }
}