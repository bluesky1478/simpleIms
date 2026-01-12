<?php
namespace Controller\Admin\Ims\Popup;

use Component\Database\DBTableField;
use Component\Ims\ImsDBName;
use Controller\Admin\Ims\ImsControllerTrait;
use Request;

class ImsPopCustomerContactController extends \Controller\Admin\Controller
{
    use ImsControllerTrait;
    public function index() {
        $this->setDefault();

        $iCustomerSno = (int)Request::get()->get('sno');
        if ($iCustomerSno === 0) {
            echo "접근오류";
            exit;
        }
        $this->setData('iCustomerSno', $iCustomerSno);

        $aTmpFldList = DBTableField::callTableFunction(ImsDBName::CUSTOMER_CONTACT);
        $aFldNames = [];
        $aSkipUpsertFlds = ['regManagerSno','regDt','modDt']; //frontend-backend 파라메터에서 제외. upsert하는 메소드에서 따로 값 넣어야함
        foreach ($aTmpFldList as $val) {
            if (!in_array($val['val'], $aSkipUpsertFlds)) $aFldNames[] = $val['val'];
        }
        $this->setData('aFldNames', $aFldNames);

        //gnb, lnb 없애는 view단 구성
        $this->getView()->setDefine('layout', 'layout_blank.php');
    }
}