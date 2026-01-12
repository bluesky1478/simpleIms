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

use Component\Board\Board;
use Component\Board\BoardList;
use Component\Goods\GoodsCate;
use Component\Page\Page;
use Framework\Debug\Exception\AlertBackException;
use Framework\Debug\Exception\RedirectLoginException;
use Framework\StaticProxy\Proxy\Session;
use Request;
use SlComponent\Util\SlSkinUtil;
use View\Template;
use Framework\Utility\Strings;

class MypageQaExchangeController extends \Bundle\Controller\Front\Mypage\MypageQaController
{
    public function index()
    {
        $this->setData('memNo', Session::get('member.memNo'));
        $this->setData('otherSkin', SlSkinUtil::getOtherSkinName());
    }
}
