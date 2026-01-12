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

namespace Controller\Api;

use SiteLabUtil\SlCommonUtil;
use SlComponent\Database\DBUtil2;
use SlComponent\Database\SearchVo;
use SlComponent\Util\SitelabLogger;
use SlComponent\Util\SlControllerTrait;
use SlComponent\Util\SlLoader;

/**
 *
 * @author Lee Seungjoo <slowj@godo.co.kr>
 */
class ClaimServiceController extends \Bundle\Controller\Api\Controller
{
    use SlControllerTrait;
    private $claimBoardService;

    public function __construct(){
        parent::__construct();
        $this->claimBoardService = SlLoader::cLoad('claim','claimBoardService');
    }

    public function index()
    {
        $this->runMethod(get_class_methods(__CLASS__));
    }

    /**
     * 클레임 데이터 불러오기
     * @param $params
     */
    public function getScmClaimData($params){
        $claimData = $this->claimBoardService->getScmClaimDataByBdSno($params['bdSno']);
        $this->setJson(200, __('조회 완료'),$claimData);
    }

    /**
     * 클레임 등록
     * @param $params
     */
    public function regClaim($params){
        $claimData = SlCommonUtil::transactionMethod($this->claimBoardService, 'regClaim', $params['sno']);
        //$claimData = $this->claimBoardService->getScmClaimDataBySno($params['bdSno']);
        //$claimData = SlCommonUtil::transactionMethod($this->claimBoardService, 'regClaim', $params['bdSno']);
        $this->setJson(200, __('처리 완료'),$claimData);
    }

    /**
     * 클레임 정보 업데이트
     * @param $params
     */
    public function updateClaim($params){
        $claimData = SlCommonUtil::transactionMethod($this->claimBoardService, 'updateClaim', $params);
        $this->setJson(200, __('처리 완료'), $claimData);
    }

    /**
     * 배송전 단순 완료 처리 상태변경
     * @param $params
     */
    public function setComplete($params){
        $claimData = SlCommonUtil::transactionMethod($this->claimBoardService, 'setComplete', $params['sno']);
        $this->setJson(200, __('처리 완료'),$claimData);
    }

    /**
     * 클레임 처리불가
     * @param $params
     */
    public function setReject($params){
        $claimData = SlCommonUtil::transactionMethod($this->claimBoardService, 'setReject', $params['sno']);
        $this->setJson(200, __('처리 완료'),$claimData);
    }


}
