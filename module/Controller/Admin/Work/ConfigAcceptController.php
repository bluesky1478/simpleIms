<?php

namespace Controller\Admin\Work;

use App;
use Component\Category\BrandAdmin;
use Component\Category\CategoryAdmin;
use Component\Stock\StockListService;
use Globals;
use Request;
use SlComponent\Database\DBUtil2;
use SlComponent\Database\SearchVo;
use SlComponent\Util\ExcelCsvUtil;
use SlComponent\Util\SlCodeMap;
use SlComponent\Util\SlLoader;
use SlComponent\Util\SlProjectCodeMap;
use function SlComponent\Util\SlLoader;

class ConfigAcceptController extends \Controller\Admin\Controller{

    use AdminWorkControllerTrait;

    public function workIndex(){
        //http://gdadmin.bcloud1478.godomall.com/work/config_accept.php?docDept=ACCT
        $this->callMenu('work', 'config', 'accept');

        //$acceptList = DBUtil2::getList('sl_workAcceptLine');

        $documentService = SlLoader::cLoad('work','documentService','');

        $data = [];
        foreach(SlProjectCodeMap::PRJ_DOCUMENT as $deptKey => $dept){
            //$value['docData']
            foreach($dept['typeDoc'] as $docKey => $docData ){
                //$docData['acceptData'] = DBUtil2::getListBySearchVo('sl_workAcceptLine', new SearchVo(['docDept=?','docType=?'],[$deptKey, $docKey]));
                $docData['acceptData'] = $documentService->getAcceptLine(['docDept'=>$deptKey, 'docType'=>$docKey]);
                $dept['typeDoc'][$docKey] = $docData;
            }
            $data[$deptKey] = $dept;
        }

        $this->setData('acceptList', $data);

        //foreach($acceptList as $)

    }

}

