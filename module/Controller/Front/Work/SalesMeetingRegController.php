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
use Component\Work\DocumentCodeMap;
use Component\Work\WorkCodeMap;
use Framework\Debug\Exception\AlertCloseException;
use Globals;
use Request;
use Session;
use SlComponent\Database\DBUtil2;
use SlComponent\Util\SlLoader;
use UserFilePath;

/**
 * 미팅 보고서 등록
 */
class SalesMeetingRegController extends \Controller\Front\Controller
{
    use WorkControllerTrait;

    public function workIndex() {
        $this->setMenu('영업관리', '미팅보고서', 'sales4Active');
    }
}

