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

namespace Controller\Front\Work;

use App;
use Framework\Debug\Exception\AlertCloseException;
use Globals;
use Request;
use Session;
use UserFilePath;

/**
 * 주문 프린트
 * @author Lee Hakyoung <haky2@godo.co.kr>
 */
class LoginController extends \Controller\Front\Controller
{
    public function index() {
        $this->setData('linkUrl', \Request::get()->get('linkUrl'));
    }
}

