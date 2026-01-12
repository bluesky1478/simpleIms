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
use SlComponent\Recap\RecapService;
use SlComponent\Util\ExcelCsvUtil;
use SlComponent\Util\SlCodeMap;
use SlComponent\Util\SlLoader;
use function SlComponent\Util\SlLoader;

/**
 * Recap Customer
 */
class BoardWriteController extends \Bundle\Controller\Admin\Board\ArticleWriteController{

    public function index(){
        parent::index();
        $this->callMenu('ims', 'board', \Request::get()->get('bdId'));
        $this->getView()->setPageName('ims/board/article_write.php');
    }

}