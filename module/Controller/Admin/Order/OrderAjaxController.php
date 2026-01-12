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
namespace Controller\Admin\Order;

use Component\Storage\Storage;
use Framework\Debug\Exception\LayerException;
use Framework\Debug\Exception\LayerNotReloadException;
use Exception;
use Message;
use Request;
use Session;
use SiteLabUtil\SlCommonUtil;
use SlComponent\Database\DBUtil;
use SlComponent\Database\SearchVo;
use SlComponent\Util\SitelabLogger;
use SlComponent\Util\SlControllerTrait;
use SlComponent\Util\SlLoader;

class OrderAjaxController extends \Controller\Admin\Controller{

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

    /**
     * 결제 추가 
     * @param $params
     */
    public function addPayments($params){
        $msg = '결제요청 추가 실패';
        if (!empty($this->orderService->addPayments($params))) {
            $msg = '결제요청 추가 완료';
        }
        $this->setJson(200, $msg);
    }

    /**
     * 배송 박스 타입 설정
     * @param $params
     */
    public function setDeliveryBoxType($params){
        foreach ( $params['orderNoList'] as $each) {
            $orderNo = explode('||', $each)[0];
            if(!empty($orderNo)){
                $this->orderService->setDeliveryBoxType($orderNo,$params['type']);
            }
        }
        $this->setJson(200, '처리 완료');
    }


    public function orderModifyGoodsCnt($params){
        $this->orderService->modifyGoodsCnt($params);
        $this->setJson(200, '처리 완료');
    }

}
