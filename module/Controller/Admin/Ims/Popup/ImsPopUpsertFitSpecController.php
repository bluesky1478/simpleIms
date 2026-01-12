<?php
namespace Controller\Admin\Ims\Popup;

use Component\Ims\ImsDBName;
use Request;
use Controller\Admin\Ims\ImsControllerTrait;
use SlComponent\Database\DBUtil2;
use SlComponent\Database\SearchVo;
use SlComponent\Util\SlLoader;

class ImsPopUpsertFitSpecController extends \Controller\Admin\Controller
{
    use ImsControllerTrait;

    public function index() {
        $this->setDefault();

        $sPrdStyleGet = (string)Request::get()->get('prdStyleGet') == 'undefined' ? '' : (string)Request::get()->get('prdStyleGet');
        $sPrdSeasonGet = (string)Request::get()->get('prdSeasonGet') == 'undefined' ? '' : (string)Request::get()->get('prdSeasonGet');
        $this->setData('sPrdStyleGet', $sPrdStyleGet);
        $this->setData('sPrdSeasonGet', $sPrdSeasonGet);

        $this->getView()->setDefine('layout', 'layout_blank.php');
    }
}