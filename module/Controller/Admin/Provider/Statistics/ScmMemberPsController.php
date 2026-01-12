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

use Component\Member\HackOut\HackOutService;
use Component\Member\Manager;
use Component\Member\MemberVO;
use Component\Member\Util\MemberUtil;
use Component\Policy\JoinItemPolicy;
use Component\Policy\MileagePolicy;
use Component\Storage\Storage;
use Exception;
use Framework\Debug\Exception\AlertOnlyException;
use Framework\Debug\Exception\LayerException;
use SlComponent\Util\SlLoader;

/**
 * Class 회원 처리
 * @package Bundle\Controller\Admin\Member
 * @author  yjwee
 */
class ScmMemberPsController extends \Controller\Admin\Order\ScmMemberPsController
{
    public function index(){
        parent::index();
    }
}
