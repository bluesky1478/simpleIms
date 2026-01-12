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

use Bundle\Component\PlusShop\PlusReview\PlusReviewArticleFront;
use Component\Board\BoardWrite;
use Component\Board\Board;
use Component\Database\DBTableField;
use Component\Goods\GoodsCate;
use Component\Member\Util\MemberUtil;
use Component\Page\Page;
use Cookie;
use Exception;
use Framework\Utility\GodoUtils;
use Request;
use Session;
use SlComponent\Util\SitelabLogger;
use SlComponent\Util\SlLoader;
use SlComponent\Util\SlSkinUtil;
use Framework\Debug\Exception\AlertRedirectException;

/**
 */
class OrderViewController extends \Bundle\Controller\Mobile\Mypage\OrderViewController
{
    /**
     * {@inheritdoc}
     */
    public function index()
    {
        try {
            parent::index();
        } catch (Exception $e) {
            if( MemberUtil::isLogin() ){
                throw $e;
            }else{
                throw new AlertRedirectException('로그인이 필요합니다.', null, null, '../member/login.php');
            }
        }
    }
}
