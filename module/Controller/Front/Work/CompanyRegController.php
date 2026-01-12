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
use Component\Work\WorkCodeMap;
use Framework\Debug\Exception\AlertCloseException;
use Globals;
use Request;
use Session;
use UserFilePath;

/**
 * 거래처 등록
 * @author Lee Hakyoung <haky2@godo.co.kr>
 */
class CompanyRegController extends \Controller\Front\Controller
{
    public function index() {
        $this->setData('workCodeMap' , WorkCodeMap::COMP_TYPE);
    }
}

