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

class ClaimPsController extends \Controller\Admin\Controller{
    public function index(){
        try {
            // --- 각 배열을 trim 처리
            $postValue = Request::post()->toArray();
            //SitelabLogger::logger($postValue);
            $claimService = \App::load(\Component\Claim\ClaimService::class);
            switch ($postValue['mode']) {
                case 'setExchangeDelivery':
                    $claimService->setExchangeDelivery($postValue);
                    exit();
                // 요청분류 저장
                case 'add_req_contents':
                    echo json_encode($claimService->saveClaimRequestType($postValue));
                    exit();
                case 'save_claim_proc':
                    $claimService->saveClaimProcData($postValue);
                    echo '1';
                    exit();
                case 'getBranchDept':
                    $this->json(
                        [
                            'code'    => 200,
                            'message' => '정상조회됨',
                            'data' => SlCommonUtil::getBranchDeptList($postValue['memberBranchName']),
                        ]
                    );
                    break;
                    exit();
            }
        } catch (Exception $e) {
            throw new LayerException($e->getMessage());
        }
    }
}
