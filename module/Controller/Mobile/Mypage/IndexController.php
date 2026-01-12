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

namespace Controller\Mobile\Mypage;

use Component\Database\DBTableField;
use Component\Goods\GoodsCate;
use Component\Page\Page;
use Cookie;
use Exception;
use Framework\Utility\DateTimeUtils;
use Request;
use Session;
use SlComponent\Util\SlLoader;
use SlComponent\Util\SlSkinUtil;

/**
 * Class MypageQnaController
 *
 * @package Bundle\Controller\Mobile\Mypage
 * @author  Jong-tae Ahn <qnibus@godo.co.kr>
 */
class IndexController extends \Bundle\Controller\Mobile\Mypage\IndexController
{
    /**
     * {@inheritdoc}
     */
    public function index()
    {
        try {
            parent::index();
            $this->setData('otherSkin', SlSkinUtil::getOtherSkinName());
            $goodsService = SlLoader::cLoad('goods','goodsService');
            $this->setData('payCateCd', $goodsService->getPaymentsCategory());
        } catch (Exception $e) {
            throw $e;
        }
    }
}
