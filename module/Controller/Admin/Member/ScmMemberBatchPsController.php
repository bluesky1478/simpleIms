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
 * @link      http://www.godo.co.kr
 */
namespace Controller\Admin\Member;

use App;
use Component\Member\Group\Util as GroupUtil;
use Exception;
use Framework\Debug\Exception\LayerException;
use Logger;
use Message;
use Request;
use SlComponent\Database\DBUtil;
use SlComponent\Database\SearchVo;
use SlComponent\Util\SitelabLogger;

/**
 * Class 회원일괄 처리
 * @package Bundle\Controller\Admin\Member
 * @author  yjwee
 */
class ScmMemberBatchPsController extends \Controller\Admin\Provider\Statistics\ScmMemberBatchPsController
{
    public function index()
    {
        parent::index();
    }
}
