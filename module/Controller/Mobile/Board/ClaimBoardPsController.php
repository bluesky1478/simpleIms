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

namespace Controller\Mobile\Board;

use App;
use Component\Database\DBTableField;
use Component\Member\Manager;
use Component\Work\Code\DocumentDesignCodeMap;
use Exception;
use Framework\Debug\Exception\AlertCloseException;
use Framework\Utility\ComponentUtils;
use Globals;
use Request;
use Session;
use SiteLabUtil\SlCommonUtil;
use SlComponent\Database\DBUtil;
use SlComponent\Database\DBUtil2;
use SlComponent\Database\SearchVo;
use SlComponent\Download\SiteLabDownloadUtil;
use SlComponent\Util\DocumentStruct;
use SlComponent\Util\SitelabLogger;
use SlComponent\Util\SlControllerTrait;
use SlComponent\Util\SlLoader;
use UserFilePath;

/**
 * 게시판 클레임 처리
 */
class ClaimBoardPsController extends \Controller\Front\Board\ClaimBoardPsController
{
    public function index() {
        parent::index();
    }

}


