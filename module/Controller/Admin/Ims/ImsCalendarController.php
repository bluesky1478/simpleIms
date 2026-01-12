<?php

namespace Controller\Admin\Ims;

use App;
use Component\Category\BrandAdmin;
use Component\Category\CategoryAdmin;
use Component\Ims\EnumType\PREPARED_TYPE;
use Component\Ims\ImsCodeMap;
use Component\Ims\ImsDBName;
use Component\Ims\ImsJsonSchema;
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
use SlComponent\Util\SlCodeMap;
use SlComponent\Util\SlLoader;
use function SlComponent\Util\SlLoader;

/**
 * 문서 리스트
 */
class ImsCalendarController extends \Controller\Admin\Controller{

    use ImsControllerTrait;

    public function index(){

        $this->callMenu('ims', 'board', 'calendar');
        $this->setDefault();

        $searchVo = new SearchVo();
        $searchVo->setWhere('a.projectType=2'); //공개 입찰일 경우.
        $searchVo->setWhere("b.exMeeting != '0000-00-00' AND b.exMeeting is NOT NULL");
        //$searchVo->setWhere('b.exMeeting >= ?');
        //$searchVo->setWhereValue(SlCommonUtil::getNowDate());

        $bidProjectList = DBUtil2::getSimpleJoinList(
            ['tableName' => ImsDBName::PROJECT,'field' => 'a.sno as projectSno'],
            [
                'b' => [ImsDBName::PROJECT_EXT, 'a.sno=b.projectSno','b.exMeeting'],
                'c' => [ImsDBName::CUSTOMER, 'a.customerSno=c.sno','c.customerName'],
            ]
        , $searchVo);

        $this->setData('bidProjectList', json_encode($bidProjectList));
        //gd_debug($bidProjectList);
    }


}