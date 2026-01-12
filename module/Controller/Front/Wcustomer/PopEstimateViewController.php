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

namespace Controller\Front\Wcustomer;
use Globals;
use Session;
use Request;
use Framework\Debug\Exception\AlertCloseException;
use Component\Member\Util\MemberUtil;
use SiteLabUtil\SlCommonUtil;
use SlComponent\Util\SitelabLogger;
use SlComponent\Util\SlLoader;
use UserFilePath;
use Framework\Utility\NumberUtils;

/**
 * 미팅보고서 팝업
 */
class PopEstimateViewController extends \Controller\Front\Controller
{
    /**
     * @inheritdoc
     */
    public function index(){
        $customerService = SlLoader::cLoad('workCustomer','workCustomerService','');
        $param = [
            'title' => '견적서',
        ];
        $customerService->setPopView($this, $param);
    }

}
