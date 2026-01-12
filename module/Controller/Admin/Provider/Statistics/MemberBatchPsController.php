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

namespace Controller\Admin\Provider\Statistics;

use Component\Member\Member;
use Component\Page\Page;
use Framework\Utility\ArrayUtils;
use Framework\Utility\StringUtils;
use SlComponent\Godo\ScmService;
use SlComponent\Util\SitelabLogger;

/**
 * Class 마일리지 일괄 지급/차감 관리
 * @package Bundle\Controller\Admin\Member
 * @author  yjwee
 */
class MemberBatchPsController extends \Bundle\Controller\Admin\Member\MemberBatchPsController
{
    public function index()
    {
        parent::index();
    }
}
