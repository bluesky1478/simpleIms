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
namespace Controller\Admin\Board;

use App;
use Component\Board\BoardTemplate;
use Component\Board\ArticleViewAdmin;
use Component\Board\BoardUtil;
use Component\Board\Board;
use Component\Board\ArticleWriteAdmin;
use Component\Board\BoardAdmin;
use Framework\Debug\Exception\AlertBackException;
use Framework\Utility\UrlUtils;
use Request;
use Globals;
use SlComponent\Util\SlCodeMap;
use SlComponent\Util\SlLoader;

class ArticleWriteController extends \Bundle\Controller\Admin\Board\ArticleWriteController
{
    /**
     * Description
     */
    public function index()
    {
        parent::index();
        /*$getValue = \Request::get()->toArray();
        $bdId = $getValue['bdId'];
        $bdSno = $getValue['sno'];

        $isClaimBoard = !empty(SlCodeMap::CLAIM_BOARD[$getValue['bdId']]);
        $this->setData('isClaimBoard' , $isClaimBoard);

        if( !empty(SlCodeMap::CLAIM_BOARD[$getValue['bdId']]) ){
            $claimBoardService = SlLoader::cLoad('claim','claimBoardService');
            $claimBoardService->setClaimData($this, $bdId, $bdSno);
        }*/

    }
}
