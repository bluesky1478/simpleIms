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

namespace Component\Board;

use Component\Storage\Storage;
use Component\Member\Manager;
use Component\Member\Member;
use Component\Member\Util\MemberUtil;
use Component\Order\Order;
use Framework\Utility\DateTimeUtils;
use Framework\Utility\FileUtils;
use Framework\Utility\SkinUtils;
use Request;
use Session;
use SlComponent\Database\DBConst;
use SlComponent\Database\DBUtil;
use SlComponent\Database\SearchVo;
use SlComponent\Util\SitelabLogger;

class BoardFront extends \Bundle\Component\Board\BoardFront{

    public function canWrite($mode = 'w', $parentData = null){
       $result =  parent::canWrite($mode, $parentData);
        if( true == \Session::has('manager') ){
            $result = 'y';
        }
        return $result;
    }

    public function canRead($data){
        $result =  parent::canRead($data);
        if( true == \Session::has('manager') ){
            $result = 'y';
        }
        return $result;
    }

    public function canModify($data){
        $result =  parent::canModify($data);
        if( true == \Session::has('manager') ){
            $result = 'y';
        }
        return $result;
    }

}
