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

/**
 *
 * @author Lee Seungjoo <slowj@godo.co.kr>
 */
class IndexController extends \Bundle\Controller\Api\Controller
{
    /**
     * {@inheritDoc}
     */
    public function index()
    {
        $tmp = ['하이','그럼','어서','와라'];
        //echo '{"subject":"어서오세요"}';
        echo json_encode($tmp);
        exit;
    }
}
