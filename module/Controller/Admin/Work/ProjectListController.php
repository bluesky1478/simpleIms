<?php

namespace Controller\Admin\Work;

use App;
use Component\Category\BrandAdmin;
use Component\Category\CategoryAdmin;
use Component\Stock\StockListService;
use Component\Work\WorkCodeMap;
use Globals;
use Request;
use SlComponent\Util\DocumentStruct;
use SlComponent\Util\ExcelCsvUtil;
use SlComponent\Util\SlCodeMap;
use SlComponent\Util\SlLoader;
use SlComponent\Util\SlProjectCodeMap;
use function SlComponent\Util\SlLoader;

class ProjectListController extends \Controller\Admin\Controller{
    public function index(){
        $this->addScript([
            'jquery/jquery.multi_select_box.js',
        ]);

        $request = \Request::request()->toArray();
        $type = gd_isset($request['type'], 'total');
        $this->callMenu('work', 'project', $type);

        $typeDisplayMap = [
            'total' =>[
                'isEstimate'=>true,'isOrder'=>true
            ],
            'estimate' =>[
                'isEstimate'=>true,'isOrder'=>false
            ],
            'order' =>[
                'isEstimate'=>false,'isOrder'=>true
            ],
            'produce' =>[
                'isEstimate'=>false,'isOrder'=>false,'listTemplate'=>'work/project_list_product.php'
            ],
        ];

        $projectListService = SlLoader::controllerServiceLoad(__NAMESPACE__,'projectList');
        $listService=SlLoader::cLoad('godo','listService','sl');
        $listService->setList($projectListService, $this);

        $this->setData('PROJECT_TYPE', WorkCodeMap::MS_PROPOSAL_TYPE);
        $this->setData('PRJ_STATUS', SlProjectCodeMap::PRJ_STATUS);
        $this->setData('listType', $type);

        $this->setData('isEstimate',$typeDisplayMap[$type]['isEstimate']);
        $this->setData('isOrder', $typeDisplayMap[$type]['isOrder']);

        $this->setData('PRD_PLAN_LIST', DocumentStruct::PRJ_PRODUCT_PLAN_LIST);

        if( !empty($typeDisplayMap[$type]['listTemplate']) ){
            $this->getView()->setPageName($typeDisplayMap[$type]['listTemplate']);
        }
    }

    /**
     * 엑셀 다운로드 처리
     * @param $getData
     */
    public function simpleExcelDownload($getData){

    }

}