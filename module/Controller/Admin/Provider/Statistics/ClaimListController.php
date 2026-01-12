<?php

namespace Controller\Admin\Provider\Statistics;

use App;
use Component\Claim\ClaimListService;
use Component\Claim\ClaimService;
use Component\Stock\StockListService;
use Exception;
use Component\Category\CategoryAdmin;
use Component\Category\BrandAdmin;
use Globals;
use Request;
use SiteLabUtil\SlCommonUtil;
use SlComponent\Util\ApiTrait;
use SlComponent\Util\ExcelCsvUtil;
use SlComponent\Util\SitelabLogger;
use SlComponent\Util\SlCodeMap;
use SlComponent\Util\SlLoader;
use function SlComponent\Util\SlLoader;

class ClaimListController extends \Controller\Admin\Order\ClaimListController{

    public function index(){
        $this->setData('scmNo', \Session::get('manager.scmNo'));

        $refineScmList = array();
        $scmAdmin = \App::load(\Component\Scm\ScmAdmin::class);
        $scmList = $scmAdmin->getSelectScmList();
        foreach( $scmList as $key => $val ){
            //if( true === SlCodeMap::SCM_USE_ORDER_ACCEPT_ [$key] ){
            //if( true === SlCommonUtil::getIsOrderAccept($key) ){
            $refineScmList[$key] = $val;
            if(true === $isFirst){
                $firstScmNo = $key;
                $firstScmName = $val;
                $isFirst = false;
            }
            //}
        }

        $this->setData('scmList', $refineScmList);

        parent::index();
    }

}