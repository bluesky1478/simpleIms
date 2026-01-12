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


use Component\Member\Member;
use Component\Member\Util\MemberUtil;
use Component\Sitelab\SitelabLogger;
use Component\Teestory\Config;
use SiteLabUtil\SlCommonUtil;
use SlComponent\Util\SlLoader;

class WriteController extends \Bundle\Controller\Front\Board\WriteController
{
    public function index(){
        parent::index();

        $memNo = \Session::get('member.memNo');
        $this->setData('memberScmNo', MemberUtil::getMemberScmNo($memNo));

        $claimBoardService = SlLoader::cLoad('claim','claimBoardService');
        $claimBoardService->setClaimBoardController($this);
    }
}