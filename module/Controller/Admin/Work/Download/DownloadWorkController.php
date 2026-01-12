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

namespace Controller\Admin\Work\Download;


use Component\Work\Code\DocumentDesignCodeMap;
use Component\Work\DocumentCodeMap;
use Component\Work\WorkCodeMap;
use Controller\Admin\Work\AdminWorkControllerTrait;
use SlComponent\Database\DBUtil2;
use SlComponent\Util\SitelabLogger;
use SlComponent\Util\SlLoader;

class DownloadWorkController extends \Controller\Admin\Controller{
    public function index(){

        $this->getView()->setDefine('layout', 'layout_blank.php');
    }
}
