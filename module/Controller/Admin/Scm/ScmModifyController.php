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
namespace Controller\Admin\Scm;

use Component\Database\DBTableField;
use Exception;
use Framework\Debug\Exception\LayerException;
use Request;
use SlComponent\Util\SlLoader;

class ScmModifyController extends \Controller\Admin\Controller
{

    /**
     * 공급사 커스텀 정보 수정
     */
    public function index(){
        // --- 공급사 사용 설정 정보
        try {
            // --- 모듈 호출
            // 공급사 고유 번호
            $scmNo = Request::get()->get('scmno');

            // $scmNo 가 없으면 디비 디폴트 값 설정
            $scmAdmin = \App::load(\Component\Scm\ScmAdmin::class);
            $getData = $scmAdmin->getScm($scmNo);
            $this->callMenu('scm', 'scm', 'scmModify');

            $scmService=SlLoader::cLoad('godo','scmService','sl');
            $this->setData('scmCategory', $scmService->getScmCategory());
            $this->setData('data', $scmService->getScmConfigData($scmNo));
            $this->setData('addressList', $scmService->getScmAddressList($scmNo));

            $popupMode = Request::get()->get('popupMode');
            if (isset($popupMode) === true) {
                $this->getView()->setDefine('layout', 'layout_blank.php');
            }

        } catch (Exception $e) {
            throw new LayerException($e->getMessage());
        }

        $this->addScript(
            [
                'jquery/jquery.multi_select_box.js',
                'jquery/validation/additional/businessnoKR.js'
            ]
        );
        $this->setData('getData', gd_isset($getData));
        $this->setData('popupMode', gd_isset($popupMode));
    }
}
