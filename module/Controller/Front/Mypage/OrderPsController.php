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
use SlComponent\Util\SlLoader;

/**
 * Class MypageQnaController
 *
 * @package Bundle\Controller\Front\Mypage
 * @author  Jong-tae Ahn <qnibus@godo.co.kr>
 * @author  Shin Donggyu <artherot@godo.co.kr>
 */
class OrderPsController extends \Bundle\Controller\Front\Mypage\OrderPsController
{
    /**
     * {@inheritdoc}
     */
    public function index()
    {
        try {
            // 리퀘스트 처리
            $postValue = Request::post()->xss()->toArray();
            switch ($postValue['mode']) {
                // AS/교환/반품/환불 신청
                case 'sl_as':
                case 'sl_back':
                case 'sl_refund':

                case 'sl_exchange':
                    $claimHistoryService = SlLoader::cLoad('Claim','ClaimService');
                    $claimHistoryService->saveRequest($postValue);
                    $claimStr = SlCodeMap::CLAIM_TYPE[$postValue['claimType']];
                    throw new AlertRedirectException(__($claimStr.' 신청이 완료되었습니다.'), null, null, $postValue['returnUrl'], 'parent');
                    break;
                default :
                    parent::index();
            }
        } catch (Exception $e) {
            if (Request::isAjax()) {
                $this->json(
                    [
                        'code'    => 0,
                        'message' => $e->getMessage(),
                    ]
                );
            } else {
                throw $e;
            }
        }
    }

}
