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

namespace Controller\Api;

use SlComponent\Database\DBUtil2;

/**
 *
 * @author Lee Seungjoo <slowj@godo.co.kr>
 */
class AppApiController extends \Bundle\Controller\Api\Controller
{
    /**
     * {@inheritDoc}
     */
    public function index()
    {
        //$memberData = DBUtil2::getOne('es_member', 'memNo', 1);
        //gd_debug($memberData);
        //echo "{subject:'어서오세요2'}";
        //$this->getView()->setDefine('layout', 'layout_blank.php');

        $this->json(
            [
                'code' => 200,
                'message' => '저장완료.',
                'data' => [],
            ]
        );
    }
}
