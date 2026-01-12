<?php

namespace Controller\Admin\Work;

use App;
use Component\Category\BrandAdmin;
use Component\Category\CategoryAdmin;
use Component\Stock\StockListService;
use Couchbase\Document;
use Globals;
use Request;
use SiteLabUtil\SlCommonUtil;
use SlComponent\Util\DocumentStruct;
use SlComponent\Util\ExcelCsvUtil;
use SlComponent\Util\SlCodeMap;
use SlComponent\Util\SlLoader;
use SlComponent\Util\SlProjectCodeMap;
use function SlComponent\Util\SlLoader;

class ProjectViewController extends \Controller\Admin\Controller{

    use AdminWorkControllerTrait;


    public function workIndex(){

        $getValue = \Request::get()->toArray();
        $type = gd_isset($getValue['type'], 'total');

        $this->addScript([
            'jquery/jquery.multi_select_box.js',
        ]);

        $this->callMenu('work', 'project', $type);

        $workService = SlLoader::cLoad('work','workService','');
        $this->setData('companyListMap', $workService->getCompanyMap());


        $projectStepData = [
          'estimate' => [
              'name' => '견적처리',
              'firstTitle' => '처리부서',
              'dept' => ['SALES' , 'DESIGN', 'QC']
          ],
          'order' => [
              'name' => '주문처리',
              'firstTitle' => '단계',
              'dept' => ['ORDER1' , 'ORDER3', 'ORDER2']
          ]
        ];

        $this->setData('projectStepData', $projectStepData );

        $linkUrl = 'wcustomer/index.php';
        $previewUrl = URI_HOME.$linkUrl.'?key='.SlCommonUtil::aesEncrypt($getValue['sno']);
        $this->setData('previewUrl', $previewUrl);
        $this->setData('productPlanList', DocumentStruct::PRJ_PRODUCT_PLAN_LIST);
        $this->setData('productPlanListJson', json_encode(DocumentStruct::PRJ_PRODUCT_PLAN_LIST));

    }
}