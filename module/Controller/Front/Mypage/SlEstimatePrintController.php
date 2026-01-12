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

namespace Controller\Front\Mypage;
use Globals;
use Session;
use Request;
use Framework\Debug\Exception\AlertCloseException;
use SlComponent\Util\SlLoader;
use UserFilePath;
use Framework\Utility\NumberUtils;

/**
 * 견적서 인쇄
 */
class SlEstimatePrintController extends \Controller\Front\Controller
{
    /**
     * @inheritdoc
     */
    public function index(){
        try {
            $this->setData( 'uriHome' , URI_HOME );

            $popupTitle = __('견적서');
            // --- 모듈 호출
            $getValue = Request::get()->toArray();

            if( empty(Session::get('manager.sno')) ){
                $this->setData('isProtect', true);
            }

            $orderService=SlLoader::cLoad('order','orderService');
            $recommendData = $orderService->getOrderDownloadData($getValue['orderNo']);

            //SitelabLogger::logger($recommendData);
            $this->setData( 'recommendData' , $recommendData );

            // 인감 이미지는 기존은 세금계산서 정보에, 현재는 기본 정보에 등록됨
            $taxInvoice = gd_policy('order.taxInvoice');
            $basicData = gd_policy('basic.info');
            if (empty($taxInvoice['taxStampIamge']) === false) {
                $sealPath = UserFilePath::data('etc', $taxInvoice['taxStampIamge'])->www();
            } else if (empty($basicData['stampImage']) === false) {
                $sealPath = UserFilePath::data('etc', $basicData['stampImage'])->www();
            } else {
                $sealPath = '';
            }
            unset($taxInvoice, $basicData);

            $beforeFillCnt =  count($recommendData['viewData']);
            for ($i = 1; $i < 25-$beforeFillCnt; $i++) $fillSpace[] = '';

            $this->setData('fillSpace', $fillSpace);
            $this->setData('popupTitle', gd_isset($popupTitle));
            $this->setData('gMall', gd_htmlspecialchars((Globals::get('gMall'))));
            $this->setData('sealPath', $sealPath);
        }
        catch (\Exception $e) {
            throw $e;
        }
    }

}
