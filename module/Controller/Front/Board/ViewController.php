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

use Component\Goods\GoodsCate;
use Component\Page\Page;
use Framework\Debug\Exception\AlertBackException;
use Framework\Debug\Exception\AlertRedirectException;
use Framework\Debug\Exception\RedirectLoginException;
use Framework\Debug\Exception\RequiredLoginException;
use Request;
use SlComponent\Util\SlCodeMap;
use SlComponent\Util\SlLoader;
use SlComponent\Util\SlSkinUtil;
use View\Template;
use Component\Validator\Validator;
use Globals;
use Component\Board\BoardView;
use Component\Board\BoardList;

class ViewController extends \Bundle\Controller\Front\Board\ViewController
{
    public function index()
    {
        parent::index();
        $claimBoardService = SlLoader::cLoad('claim','claimBoardService');
        $claimBoardService->setClaimBoardViewController($this);
    }


}
