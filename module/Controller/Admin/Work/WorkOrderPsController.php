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

namespace Controller\Admin\Work;

use Component\Work\Code\DocumentDesignCodeMap;
use Component\Work\DocumentCodeMap;
use SiteLabUtil\SlCommonUtil;
use SlComponent\Database\DBUtil2;
use SlComponent\Database\SearchVo;
use SlComponent\Util\SitelabLogger;
use SlComponent\Util\SlControllerTrait;
use SlComponent\Util\SlLoader;

class WorkOrderPsController extends \Controller\Admin\Controller{

    use SlControllerTrait;

    public function index() {
        $this->runMethod(get_class_methods(__CLASS__));
    }

    public function test(){
        $message = '로그아웃 처리 완료';
        $this->setJson(200, $message);
    }



}
