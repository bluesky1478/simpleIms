<?php

/**
 * This is commercial software, only users who have purchased a valid license
 * and accept to the terms of the License Agreement can install and use this
 * program.
 *
 * Do not edit or add to this file if you wish to upgrade Godomall5 to newer
 * versions in the future.
 *
 * @copyright ⓒ 2016, NHN godo: Corp.
 * @link http://www.godo.co.kr
 */
namespace Controller\Admin\Design;

use Component\Database\DBTableField;
use Exception;
use Framework\Debug\Exception\LayerException;
use Request;
use SlComponent\Database\DBUtil2;
use SlComponent\Util\SlLoader;

class PopupListController extends \Bundle\Controller\Admin\Design\PopupListController
{
    public function index(){
        parent::index();
        $scmAdmin = \App::load(\Component\Scm\ScmAdmin::class);
        $scmList = $scmAdmin->getSelectScmList();
        $this->setData('scmList', $scmList);

        $data = $this->getData('data');

        foreach ($data as $key => $val) {
            $popupInfo = DBUtil2::getOne('sl_scmPopup', 'popupSno', $val['sno']);
            $val['scmNo'] = $popupInfo['scmNo'];
            $data[$key] = $val;
        }

        $this->setData('data',$data);

    }
}
