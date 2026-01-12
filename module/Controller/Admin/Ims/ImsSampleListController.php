<?php

namespace Controller\Admin\Ims;

use App;
use Component\Category\BrandAdmin;
use Component\Category\CategoryAdmin;
use Component\Database\DBTableField;
use Component\Ims\EnumType\PREPARED_TYPE;
use Component\Ims\ImsCodeMap;
use Component\Ims\ImsDBName;
use Component\Ims\ImsJsonSchema;
use Component\Ims\NkCodeMap;
use Component\Stock\StockListService;
use Component\Work\WorkCodeMap;
use Controller\Admin\Erp\AdminErpControllerTrait;
use Controller\Admin\Erp\ControllerService\InoutListService;
use Globals;
use Request;
use SiteLabUtil\SlCommonUtil;
use SlComponent\Database\DBUtil2;
use SlComponent\Database\SearchVo;
use SlComponent\Util\ExcelCsvUtil;
use SlComponent\Util\PhpExcelUtil;
use SlComponent\Util\SlCodeMap;
use SlComponent\Util\SlLoader;
use function SlComponent\Util\SlLoader;

//자재정보 리스트
class ImsSampleListController extends \Controller\Admin\Controller{

    use ImsPsNkTrait;
    use ImsControllerTrait;

    private $imsNkService;

    public function __construct() {
        parent::__construct();
        $this->imsNkService = SlLoader::cLoad('imsv2', 'imsNkService');
    }

    public function index() {
        $this->callMenu('ims', 'customer', 'sampleList');
        $this->setDefault();

        //검색항목
        $search['combineSearch'] = [
            'cust.customerName' => '고객사명',
            'a.projectSno' => '프로젝트번호',
            'a.sampleName' => '샘플명',
            'b.styleCode' => '스타일코드',
            'b.productName' => '스타일명',
        ];
        $this->setData('search', $search);
    }
}