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

namespace Controller\Front\Partner;

use App;
use Component\Work\WorkCodeMap;
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
 * 프로젝트 리스트
 * @author Lee Hakyoung <haky2@godo.co.kr>
 */
class OrderReqController extends \Controller\Front\Controller
{

    use PartnerTrait;

    public function workIndex() {
        $this->setMenu('PROJECT', 1);
        $this->setData('today', date('Y-m-d'));

        $orderList = DBUtil2::getList('sl_3plOrderTmp', 'scmNo', '2');
        $totalData = DBUtil2::runSelect("select sum(qty) as totalQty, count(1) as totalCnt from sl_3plOrderTmp where scmNo = 2")[0];

        $this->setData('orderList', $orderList);
        $this->setData('totalData', $totalData);

        $current = date('Hi');
        $standard = '1120';
        $this->setData('isOrderAble', $standard > $current);
        $this->setData('isDev', SlCommonUtil::isDevIp());

    }

}

