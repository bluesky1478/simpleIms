<?php
namespace Controller\Admin\Ims\Popup;

use Controller\Admin\Ims\ImsControllerTrait;
use Request;
use SlComponent\Database\DBUtil2;
use SlComponent\Database\SearchVo;
use SlComponent\Util\SlLoader;

class ImsPopStoredModController extends \Controller\Admin\Controller
{
    use ImsControllerTrait;
    private $imsService;

    public function __construct() {
        parent::__construct();
        $this->imsService = SlLoader::cLoad('ims','imsService');
    }

    public function index() {
        $this->setDefault();
        $iStoredSno = (int)Request::get()->get('sno');

        $this->setData('customerListMap', $this->imsService->getCustomerListMap());
        $searchVo = new SearchVo(['delFl=?', 'sno=?'], ['n', $iStoredSno]);
        $tableList = SlLoader::sqlLoad('Component\Ims\ImsStoredService', false)->getStoredTableSimple();
        $aData = DBUtil2::getComplexListWithQuery($tableList, $searchVo, false, false, false)['list'];
        if (!isset($aData[0]['sno'])) {
            echo "접근오류";
            exit;
        }
        $this->setData('StoredInfo', $aData[0]);

        //gnb, lnb 없애는 view단 구성
        $this->getView()->setDefine('layout', 'layout_blank.php');
    }
}