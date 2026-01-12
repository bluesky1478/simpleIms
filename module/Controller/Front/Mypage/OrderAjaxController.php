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

namespace Controller\Front\Mypage;

use App;
use Component\Order\OrderAdmin;
use Component\Member\Manager;
use Component\Sms\Code;
use Component\Sms\SmsAutoOrder;
use Component\Database\DBTableField;
use Component\Goods\GoodsCate;
use Component\Page\Page;
use Exception;
use Framework\Debug\Exception\AlertOnlyException;
use Framework\Debug\Exception\AlertReloadException;
use Framework\Debug\Exception\AlertRedirectException;
use Message;
use Request;
use Session;
use SlComponent\Database\DBUtil;
use SlComponent\Util\SitelabLogger;
use SlComponent\Util\SlCodeMap;
use SlComponent\Util\SlControllerTrait;
use SlComponent\Util\SlLoader;

/**
 * Class MypageQnaController
 *
 * @package Bundle\Controller\Front\Mypage
 * @author  Jong-tae Ahn <qnibus@godo.co.kr>
 * @author  Shin Donggyu <artherot@godo.co.kr>
 */
class OrderAjaxController extends \Bundle\Controller\Front\Controller
{
    use SlControllerTrait;

    private $orderService;

    public function __construct(){
        parent::__construct();
        $this->orderService = SlLoader::cLoad('order','orderService');
    }

    public function index()
    {
        $this->runMethod(get_class_methods(__CLASS__));
    }

    public function payment($params){
        $this->orderService->paymentGoodsToCart($params['goodsNo'],$params['goodsPrice']);
        $this->setJson(200, '결제페이지로 이동합니다.');
    }

}
