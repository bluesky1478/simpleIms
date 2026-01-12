<?php

namespace Controller\Admin\Provider\Statistics;

use App;
use Component\Category\BrandAdmin;
use Component\Category\CategoryAdmin;
use Component\Ims\ImsDBName;
use Component\Stock\StockListService;
use Component\Work\WorkCodeMap;
use Controller\Admin\Erp\AdminErpControllerTrait;
use Controller\Admin\Erp\ControllerService\InoutListService;
use Controller\Admin\Ims\ImsControllerTrait;
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
class IcsOrderPackingController extends \Controller\Admin\Controller{

    use ImsControllerTrait;

    public function index(){
        $iCustomerSno = 6; //namkuuu 후순위 작업. 향후 DB or 세션에서 가져와야함

        $iPackingSno = (int)Request::get()->get('sno');
        if ($iPackingSno === 0) {
            //가장 최근에 등록된 분류패킹master를 불러온다
            $oSV = new SearchVo('customerSno=?', $iCustomerSno);
            $oSV->setOrder('sno desc');
            $aInfo = DBUtil2::getOneBySearchVo(ImsDBName::CUSTOMER_PACKING, $oSV);
            if (!isset($aInfo['sno'])) {
                echo "접근오류";
                exit;
            }
            $iPackingSno = (int)$aInfo['sno'];
        }
        $this->setData('iPackingSno', $iPackingSno);

        $this->callMenu('statistics', 'b2b', 'packing');
        $this->setDefault();
    }
}