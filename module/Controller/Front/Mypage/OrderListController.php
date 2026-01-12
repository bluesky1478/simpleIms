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

namespace Controller\Front\Mypage;

use Bundle\Component\PlusShop\PlusReview\PlusReviewArticleFront;
use Component\Board\BoardWrite;
use Component\Board\Board;
use Component\Database\DBTableField;
use Component\Goods\GoodsCate;
use Component\Member\Util\MemberUtil;
use Component\Page\Page;
use Controller\Admin\Ims\IcsControllerTrait;
use Cookie;
use Exception;
use Framework\Utility\GodoUtils;
use Request;
use Session;
use SiteLabUtil\SlCommonUtil;
use SlComponent\Util\SlLoader;
use SlComponent\Util\SlSkinUtil;

/**
 * Class MypageQnaController
 *
 * @package Bundle\Controller\Front\Mypage
 * @author  Jong-tae Ahn <qnibus@godo.co.kr>
 */
class OrderListController extends \Bundle\Controller\Front\Mypage\OrderListController
{

    use IcsControllerTrait;

    /**
     * {@inheritdoc}
     */
    public function index()
    {
        try {
            SlCommonUtil::setDefaultDate('wDate');
            parent::index();
            $this->setData('otherSkin', SlSkinUtil::getOtherSkinName());
            $goodsService = SlLoader::cLoad('goods','goodsService');
            $this->setData('payCateCd', $goodsService->getPaymentsCategory());
            $this->setData('memberScmNo', MemberUtil::getMemberScmNo());

            $this->setDefault();

        } catch (Exception $e) {
            throw $e;
        }
    }
}
