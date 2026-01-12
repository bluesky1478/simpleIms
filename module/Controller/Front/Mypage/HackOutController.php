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
use Component\Page\Page;
use Cookie;
use Exception;
use Framework\Utility\GodoUtils;
use Request;
use Session;
use SlComponent\Util\SlSkinUtil;

/**
 * Class MypageQnaController
 *
 * @package Bundle\Controller\Front\Mypage
 * @author  Jong-tae Ahn <qnibus@godo.co.kr>
 */
class HackOutController extends \Bundle\Controller\Front\Mypage\HackOutController
{
    /**
     * {@inheritdoc}
     */
    public function index()
    {
        try {
            parent::index();
            $this->setData('otherSkin', SlSkinUtil::getOtherSkinName());
            //OrderData refine
            //gd_debug( $this->getData('orderData')[0]['goods'] );
        } catch (Exception $e) {
            throw $e;
        }
    }
}
