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

namespace Component\Excel;

use Component\Database\DBTableField;
use Component\Member\Manager;
use Component\Validator\Validator;
use Framework\Debug\Exception\AlertBackException;
use Framework\Utility\ArrayUtils;
use Framework\Utility\GodoUtils;
use Session;

/**
 * Class ExcelForm
 * @package Bundle\Component\Excel
 * @author  yjwee <yeongjong.wee@godo.co.kr>
 *          atomyang
 */
class ExcelForm extends \Bundle\Component\Excel\ExcelForm
{
    public function setExcelFormOrder($location){
        $setData = parent::setExcelFormOrder($location);

        $setData['orderAcctStatus'] = ['name'=>__('승인여부'),'orderFl'=>'y'];
        $setData['giftAmount'] = ['name'=>__('선물금액'),'orderFl'=>'y'];
        $setData['addDeposit'] = ['name'=>__('적립예치금'),'orderFl'=>'y'];
        $setData['reqDeliveryDt'] = ['name'=>__('배송요청일'),'orderFl'=>'y'];
        $setData['memberType'] = ['name'=>__('회원유형'),'orderFl'=>'y'];
        $setData['deliverySubject'] = ['name'=>__('배송지점'),'orderFl'=>'y'];
        $setData['customDeliveryCost'] = ['name'=>__('업체별배송비'),'orderFl'=>'y'];
        $setData['customWorkCost'] = ['name'=>__('업체별작업비'),'orderFl'=>'y'];
        $setData['customPrdName'] = ['name'=>__('삼영제품명'),'orderFl'=>'n'];
        $setData['customScmName'] = ['name'=>__('주문사이트'),'orderFl'=>'n'];

        return $setData;
    }


}
