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

namespace Controller\Front\Board;

use App;
use Component\Database\DBTableField;
use Component\Member\Manager;
use Component\Work\Code\DocumentDesignCodeMap;
use Exception;
use Framework\Debug\Exception\AlertCloseException;
use Framework\Utility\ComponentUtils;
use Globals;
use Request;
use Session;
use SiteLabUtil\SlCommonUtil;
use SlComponent\Database\DBUtil;
use SlComponent\Database\DBUtil2;
use SlComponent\Database\SearchVo;
use SlComponent\Download\SiteLabDownloadUtil;
use SlComponent\Util\DocumentStruct;
use SlComponent\Util\SitelabLogger;
use SlComponent\Util\SlControllerTrait;
use SlComponent\Util\SlLoader;
use UserFilePath;

/**
 * 게시판 클레임 처리
 */
class ClaimBoardPsController extends \Controller\Front\Controller
{
    use SlControllerTrait;

    public function index() {
        $this->runMethod(get_class_methods(__CLASS__));
    }

    /**
     * 주문의 상품 정보를 전달한다.
     * @param $params
     */
    public function getOrderGoods($params){
        $claimService = SlLoader::cLoad('Claim','ClaimBoardService');
        $orderGoodsList = $claimService->getOrderGoods($params['orderNo']);
        $this->setJson(200, __('조회 완료'), $orderGoodsList);
    }

    /**
     * 상품정보 반환
     * @param $params
     */
    public function getGoodsInfo($params){
        $claimService = SlLoader::cLoad('Claim','ClaimBoardService');
        $goodsInfo = $claimService->getGoodsInfo($params['goodsNo']);
        $this->setJson(200, __('조회 완료'), $goodsInfo);
    }

    /**
     * 주문의 상품 정보를 전달한다.
     * @param $params
     */
    public function getGoodsInfoWithoutOrder($params){
        $claimService = SlLoader::cLoad('Claim','ClaimBoardService');
        $goodsInfo = $claimService->getGoodsInfoWithoutOrder($params['goodsNo']);
        $this->setJson(200, __('조회 완료'), $goodsInfo);
    }


    /**
     * 클레임 데이터 불러오기
     * @param $params
     */
    public function getScmClaimData($params){
        $claimService = SlLoader::cLoad('Claim','ClaimBoardService');
        $claimData = $claimService->getScmClaimDataByBdSno($params['bdSno']);
        $this->setJson(200, __('조회 완료'), $claimData);
    }

}


