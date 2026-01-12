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

namespace Controller\Front\Partner;

use App;
use Framework\Debug\Exception\AlertCloseException;
use Globals;
use Request;
use Session;
use SlComponent\Database\DBUtil2;
use UserFilePath;

/**
 * Index - 영업문서 등록 (추후 대쉬보드)
 */
class IndexController extends \Controller\Front\Partner\OrderReqController
{
    use PartnerTrait;
}

