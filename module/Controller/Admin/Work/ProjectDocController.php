<?php

namespace Controller\Admin\Work;

use App;
use Component\Category\BrandAdmin;
use Component\Category\CategoryAdmin;
use Component\Stock\StockListService;
use Globals;
use Request;
use SlComponent\Util\ExcelCsvUtil;
use SlComponent\Util\SlCodeMap;
use SlComponent\Util\SlLoader;
use SlComponent\Util\SlProjectCodeMap;
use function SlComponent\Util\SlLoader;

class ProjectDocController extends \Controller\Admin\Controller{
    public function index(){

        $this->addScript([
            '../../script/vue.js',
            '../../script/dropzone/min/dropzone.min.js'
        ]);
        $this->addCss(
            [
                '../../script/dropzone/min/dropzone.min.css'
            ]
        );

        $projectService=SlLoader::cLoad('work','projectService','');
        $projectService->setControllerData( $this );

        $this->getView()->setDefine('layout', 'layout_blank.php');

    }
}