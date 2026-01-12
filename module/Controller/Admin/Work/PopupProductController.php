<?php

/**
 * This is commercial software, only users who have purchased a valid license
 * and accept to the terms of the License Agreement can install and use this
 * program.
 *
 * Do not edit or add to this file if you wish to upgrade Godomall5 to newer
 * versions in the future.
 *
 * @copyright ⓒ 2017, NHN godo: Corp.
 * @link      http://www.godo.co.kr
 */

namespace Controller\Admin\Work;

use App;
use Exception;
use Component\Naver\NaverPay;
use Framework\Debug\Exception\AlertCloseException;
use Session;
use Request;
use SlComponent\Util\SlLoader;

/**
 */
class PopupProductController extends \Controller\Admin\Controller
{
    /**
     * {@inheritdoc}
     */
    public function index(){
        $this->addScript([
            '../../script/vue.js',
        ]);
        $this->getView()->setDefine('layout', 'layout_blank.php');
    }
}
