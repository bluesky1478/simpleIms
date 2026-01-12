<?php
namespace Controller\Admin\Ims\Popup;

use Component\Ims\ImsDBName;
use Controller\Admin\Ims\ImsControllerTrait;
use Request;
use SlComponent\Database\DBUtil2;
use SlComponent\Database\SearchVo;

class ImsPopProductPrdCostController extends \Controller\Admin\Controller
{
    use ImsControllerTrait;
    public function index() {
        $this->setDefault();

        $request = \Request::request()->toArray();
        $iSno = (int)$request['sno'];
        if ($iSno === 0) {
            echo "접근오류";
            exit;
        }
        $aList = DBUtil2::getListBySearchVo(ImsDBName::PRODUCT, new SearchVo('sno=?', $iSno));
        if (!isset($aList[0]['sno']) || (int)$aList[0]['sno'] === 0) {
            echo "접근오류";
            exit;
        }
        $bFlagUpsert = $aList[0]['prdCostConfirmSno'] == -1 ? false : true;

        $this->setData('iSno', $iSno);
        $this->setData('bFlagUpsert', $bFlagUpsert);

        //gnb, lnb 없애는 view단 구성
        $this->getView()->setDefine('layout', 'layout_blank.php');
    }
}