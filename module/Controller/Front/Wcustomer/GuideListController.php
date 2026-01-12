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

namespace Controller\Front\Wcustomer;

use App;
use Framework\Debug\Exception\AlertCloseException;
use Globals;
use Request;
use Session;
use SiteLabUtil\SlCommonUtil;
use SlComponent\Util\SlLoader;
use UserFilePath;

/**
 * 고객 - 유니폼 디자인 가이드
 */
class GuideListController extends \Controller\Front\Controller
{
    public function index() {
        $workCustomerService = SlLoader::cLoad('workCustomer','workCustomerService','');
        $workCustomerService->setListData($this, 'ORDER2', 10);
    }

}

