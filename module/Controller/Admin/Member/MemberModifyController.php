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

use Framework\Debug\Exception\AlertBackException;
use Framework\Utility\DateTimeUtils;
use Framework\Utility\StringUtils;
use SlComponent\Database\DBUtil2;

/**
 * Class MemberModifyController
 * @package Bundle\Controller\Admin\Member
 * @author  yjwee
 */
class MemberModifyController extends \Bundle\Controller\Admin\Member\MemberModifyController
{
    public function index()
    {
        parent::index();
        $request = \App::getInstance('request');
        $setMemberConfig = DBUtil2::getOne('sl_setMemberConfig', 'memNo', $request->get()->get('memNo'));
        $this->setData( 'memberType', $setMemberConfig['memberType'] );
        $this->setData( 'buyLimitCount', $setMemberConfig['buyLimitCount'] );
    }
}
