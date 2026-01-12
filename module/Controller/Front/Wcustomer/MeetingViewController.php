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
 * 견적서 인쇄
 */
class MeetingViewController extends \Controller\Front\Controller
{
    /**
     * @inheritdoc
     */
    public function index(){
        $request = \Request::request()->toArray();
        $workCustomerService = SlLoader::cLoad('workCustomer','workCustomerService','');
        $workCustomerService->setViewData($this, $request['sno']);
        $documentData = $this->getData('documentData');
        $projectSno = SlCommonUtil::aesDecrypt( $request['key'] ); //검증용.
        //get으로 가져온 키가 다를 경우 대비
        if( $projectSno != $documentData['projectSno']){
            gd_debug('오류 : 관리자에 문의 바랍니다.');
            exit();
        }
    }

}
