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

namespace Controller\Front\Warehouse;

use App;
use Component\Work\WorkCodeMap;
use Controller\Front\Warehouse\ControllerService\InvoiceRegListService;
use Controller\Front\Work\WorkControllerTrait;
use Framework\Debug\Exception\AlertCloseException;
use Globals;
use Request;
use Session;
use SiteLabUtil\SlCommonUtil;
use SlComponent\Database\DBUtil2;
use SlComponent\Util\SlLoader;
use SlComponent\Util\SlProjectCodeMap;
use UserFilePath;

/**
 * 송장 리스트
 * @author Lee Hakyoung <haky2@godo.co.kr>
 */
class InvoiceReqController extends \Controller\Front\Controller
{

    use WarehouseTrait;

    public function workIndex() {
        $this->setMenu('PROJECT', 1);
        $this->setData('today', date('Y-m-d'));
        $this->setData('listTitle', InvoiceRegListService::LIST_TITLES );

        $controllerListService = SlLoader::controllerServiceLoad(__NAMESPACE__,'invoiceRegList');
        $listService = SlLoader::cLoad('godo','listService','sl');
        $listService->setList($controllerListService, $this);


    }

}

