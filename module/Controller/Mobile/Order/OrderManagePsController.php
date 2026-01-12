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

namespace Controller\Mobile\Order;

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
use SiteLabUtil\SlCommonUtil;
use SlComponent\Database\DBUtil;
use SlComponent\Util\SitelabLogger;
use SlComponent\Util\SlCodeMap;
use SlComponent\Util\SlLoader;

class OrderManagePsController extends \Controller\Mobile\Controller{
    /**
     * {@inheritdoc}
     */
    public function index()
    {
        try {
            // 리퀘스트 처리
            $postValue = Request::post()->xss()->toArray();
            switch ($postValue['mode']) {
                case 'getBranchDept':
                    $this->json(
                        [
                            'code'    => 200,
                            'message' => '정상조회됨',
                            'data' => SlCommonUtil::getBranchDeptList($postValue['memberBranchName']),
                        ]
                    );

                    break;
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
