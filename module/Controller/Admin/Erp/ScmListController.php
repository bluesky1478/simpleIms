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

class ScmListController extends \Bundle\Controller\Admin\Scm\ScmListController
{

    /**
     * 공급사 커스텀 정보 수정
     */
    public function index(){
        $pageNum = \Request::get()->get('pageNum');
        if(empty($pageNum)) \Request::get()->set('pageNum',100);
        parent::index();
        $this->callMenu('erp', 'scm', 'scmList');
    }
}
