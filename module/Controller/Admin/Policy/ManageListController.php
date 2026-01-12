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
 * @link      http://www.godo.co.kr
 */

namespace Controller\Admin\Policy;

use Component\Member\Manager;
use Component\Member\ManagerCs;
use Component\Page\Page;
use Component\Scm\Scm;
use Framework\Debug\Exception\LayerException;
use Framework\Utility\GodoUtils;
use Framework\Utility\StringUtils;
use Globals;
use Request;

/**
 * 운영자 관리 리스트
 *
 * @author Lee Namju <lnjts@godo.co.kr>
 * @author Shin Donggyu <artherot@godo.co.kr>
 */
class ManageListController extends \Bundle\Controller\Admin\Policy\ManageListController
{
    public function index()
    {
        if(empty(\Request::get()->get('scmFl'))){
            \Request::get()->set('scmFl','n');
        }
        if(empty(\Request::get()->get('pageNum'))){
            \Request::get()->set('pageNum',100);
        }
        parent::index();
    }
}
