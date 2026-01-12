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
namespace Controller\Admin\Sitelab;

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

class GoodsAjaxController extends \Controller\Admin\Controller{

    use SlControllerTrait;

    private $goodsService;

    public function __construct(){
        parent::__construct();
        $this->goodsService = SlLoader::cLoad('goods','goodsService');
    }

    public function index()
    {
        $this->runMethod(get_class_methods(__CLASS__));
    }

    /**
     * 공동구매 설정 
     * @param $params
     */
    public function saveGroupBuy($params){
        $this->goodsService->saveGroupBuy($params);
        $this->setJson(200, '처리완료');
    }

    /**
     * 공동구매 리셋
     * @param $params
     */
    public function resetGroupBuy($params){
        $this->goodsService->resetGroupBuy($params);
        $this->setJson(200, '처리완료');
    }

    /**
     * 재고 수정
     * @param $params
     */
    public function setBatchStock($params){
        $data = $this->goodsService->setBatchStock($params);
        $this->setJson(200, '처리완료', $data);
    }

}
