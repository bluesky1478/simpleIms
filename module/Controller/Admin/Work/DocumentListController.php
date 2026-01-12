<?php

namespace Controller\Admin\Work;

use App;
use Component\Category\BrandAdmin;
use Component\Category\CategoryAdmin;
use Component\Stock\StockListService;
use Component\Work\WorkCodeMap;
use Globals;
use Request;
use SlComponent\Util\ExcelCsvUtil;
use SlComponent\Util\SlCodeMap;
use SlComponent\Util\SlLoader;
use function SlComponent\Util\SlLoader;

/**
 * 문서 리스트
 */
class DocumentListController extends \Controller\Admin\Controller{

    use AdminWorkControllerTrait;

    public function workIndex(){
        $reqParam = \Request::request()->toArray();

        if( !empty( $reqParam['allDocument'] ) ){
            $this->callMenu('work', 'manage', 'doclist');
        }else{

            $loadType = $reqParam['docDept'].$reqParam['docType'];

            $typeMap = [
                'ORDER110' => ['docType'=>'doclist110','docDept'=>'design'],
                'ORDER210' => ['docType'=>'doclist210','docDept'=>'design'],
                'ORDER310' => ['docType'=>'doclist310','docDept'=>'design'],
            ];
            $listDocDept = gd_isset($typeMap[$loadType]['docDept'], strtolower($reqParam['docDept']));
            $listDocType = gd_isset($typeMap[$loadType]['docType'], 'doclist' . $reqParam['docType']);
            //gd_debug( $loadType );gd_debug( $listDocDept );gd_debug( $listDocType );
            $this->callMenu('work', $listDocDept, $listDocType);
        }

        $companyListService = SlLoader::controllerServiceLoad(__NAMESPACE__,'documentList');
        $listService=SlLoader::cLoad('godo','listService','sl');
        $listService->setList($companyListService, $this);
    }

    /**
     * 엑셀 다운로드 처리
     * @param $getData
     */
    public function simpleExcelDownload($getData){
        //TODO
    }

}