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
class ImsProjectViewNewController extends \Controller\Admin\Controller{

    use ImsControllerTrait;
    use ImsStepTrait;

    public function index(){

        $request = \Request::request()->toArray();
        $this->setProjectListRelatedController($request);
        //$this->callMenu('ims', 'project', 'all');

        $this->setDefault();
        $this->setData('designField',ImsCodeMap::PROJECT_DESIGN_FIELD);
        $this->setData('addedInfo', ImsJsonSchema::ADD_INFO);

        if( !empty($request['popup']) ){
            $this->getView()->setDefine('layout', 'layout_blank.php');
        }

        if( $this->getData('isProduceCompany') ){
            $this->getView()->setDefine('layout', 'layout_blank.php');
            $this->getView()->setPageName("ims/ims_project_view_produce.php");
        }

        $this->setData($request['status'], 'text-danger');

        $imsService = SlLoader::cLoad('ims', 'imsService');
        $imsService->setSyncStatus($request['sno']);

        $this->setData('prdSetupData', $this->setupProductList());
    }

    public function setupProductList(){
        return [
            'list' => [
                ['이미지',5],
                ['프로젝트타입',3],
                ['시즌년도',4],
                ['상품명',18],
                ['제작수량',5],
                ['고객 희망 납기일',5],
                ['예가',5],
                ['타겟 생산가',5],
                ['마진',5],
                ['예상발주',5],
                ['희망납기',5],
                ['지급기준',10],
                ['현 유니폼 불편 사항',15],
            ]
        ];
    }

}