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
namespace Controller\Admin\Erp;

use Component\Database\DBTableField;
use Exception;
use Framework\Debug\Exception\LayerException;
use Request;
use SlComponent\Util\SlLoader;

class ScmRegistController extends \Bundle\Controller\Admin\Scm\ScmRegistController
{

    /**
     * 공급사 커스텀 정보 수정
     */
    public function index(){
        parent::index();
        $this->callMenu('erp', 'scm', 'scmList');
        //$this->getView()->setPageName('scm/scm_regist.php');
    }
}
