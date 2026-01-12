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

namespace Controller\Mobile\Goods;

use App;
use Framework\Utility\StringUtils;
use Session;
use Request;
use Exception;
use Framework\Utility\ArrayUtils;
use Framework\Utility\SkinUtils;
use Globals;
use SlComponent\Util\SlLoader;

/**
 * Class LayerDeliveryAddress
 *
 * @package Bundle\Controller\Front\Order
 * @author  su
 */
class LayerOptionController extends \Bundle\Controller\Mobile\Goods\LayerOptionController
{
    /**
     * @inheritdoc
     */
    public function index(){

        parent::index();
        $scmService=SlLoader::cLoad('godo','scmService','sl');
        $scmService->setRefineGoodsOption($this);
        $scmService->setTkeFixedPrice($this);
        $scmService->setRestockReq($this);

    }
}
