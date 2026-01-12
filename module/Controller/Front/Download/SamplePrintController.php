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

namespace Controller\Front\Download;

use Exception;
use Framework\Debug\Exception\AlertCloseException;
use Globals;
use Request;
use Session;
use App;
use SlComponent\Database\DBUtil;
use SlComponent\Util\SitelabLogger;
use SlComponent\Util\SlLoader;
use UserFilePath;

/**
 * 샘플의뢰서 프린트
 * @author Lee Hakyoung <haky2@godo.co.kr>
 */
class SamplePrintController extends \Controller\Front\Controller
{
    public function index() {
        try {
            // 인감 이미지는 기존은 세금계산서 정보에, 현재는 기본 정보에 등록됨
            $basicData = gd_policy('basic.info');
            $this->setData('defaultInfo', gd_isset($basicData));
            if (empty($taxInvoice['taxStampIamge']) === false) {
                $sealPath = UserFilePath::data('etc', $taxInvoice['taxStampIamge'])->www();
            } else if (empty($basicData['stampImage']) === false) {
                $sealPath = UserFilePath::data('etc', $basicData['stampImage'])->www();
            } else {
                $sealPath = '';
            }
            unset($taxInvoice, $basicData);
            $this->setData('sealPath', gd_isset($sealPath));
            $this->setData('gMall', gd_htmlspecialchars(Globals::get('gMall')));

            include UserFilePath::adminSkin('head.php');

            $html = $this->createGoodsPreviewBody();
            $this->setData('previewGoodsBody', $html);

            $cols = [];
            for($i=0; 21>$i; $i++){
                $cols[] = '';
            }
            $this->setData('cols', $cols);
            $rows = [];
            for($i=0; 21>$i; $i++){
                $rows[] = '';
            }
            $this->setData('rows', $rows);

        }
        catch (Exception $e) {
            throw $e;
        }
    }

    public function createGoodsPreviewBody(){
        return "";
    }

}

