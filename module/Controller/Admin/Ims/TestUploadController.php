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
use SlComponent\Util\SitelabLogger;
use SlComponent\Util\SlCodeMap;
use SlComponent\Util\SlLoader;
use function SlComponent\Util\SlLoader;

/**
 * 문서 리스트
 */
class TestUploadController extends \Controller\Admin\Controller{

    use ImsControllerTrait;

    public function index(){
        SitelabLogger::logger('파일 업로드 테스트');
        SitelabLogger::logger(\Request::request()->toArray());
        SitelabLogger::logger(\Request::files()->toArray());
        $files  = \Request::files()->toArray();
        $this->json(['code' => 200, 'filePath' => $files['file']['tmp_name'], 'fileName' => $files['file']['name'] ]);
        exit();
    }

}