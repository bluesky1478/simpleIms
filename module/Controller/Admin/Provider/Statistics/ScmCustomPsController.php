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
namespace Controller\Admin\Provider\Statistics;

use Component\Member\Manager;
use Component\Naver\NaverPay;
use Component\Policy\Policy;
use Exception;
use Framework\Debug\Exception\LayerNotReloadException;
use Message;
use Request;
use SlComponent\Util\SitelabLogger;
use SlComponent\Util\SlLoader;

class ScmCustomPsController extends \Controller\Admin\Controller
{
    /**
     * 공급사 처리
     * [관리자 모드] 공급사 처리
     *
     * @author    su
     * @version   1.0
     * @since     1.0
     * @copyright ⓒ 2016, NHN godo: Corp.
     *
     * @param array $get
     * @param array $post
     * @param array $files
     *
     * @throws Except
     * @throws LayerNotReloadException
     */
    public function index()
    {
        try {
            // 모드 별 처리
            $postValue = Request::post()->toArray();
            $scmService=SlLoader::cLoad('godo','scmService','sl');

            switch ($postValue['mode']) {
                case 'delete_file':
                    $scmService->deleteScmFile($postValue);
                    $this->layer(__('삭제 완료'), null, null, null, 'top.location.href="/";');
                    break;
                case 'delete_address':
                    $scmService->deleteScmAddress($postValue);
                    //$this->json(['code' => 200]);
                    $this->layer(__('저장이 완료되었습니다.'), null, null, null, 'top.location.href="/";');
                    break;
                case 'add_address':
                    $scmService->saveScmAddress($postValue);
                    $this->json(['code' => 200]);
                    break;
                case 'customModify':
                    $filesValue = \Request::files()->toArray();
                    $scmService->saveScmCustomInfo($postValue, $filesValue);
                    $this->layer(__('저장이 완료되었습니다.'), null, null, null, 'top.location.href="scm_list.php";');
                    break;
                case 'add_address_batch':
                    $filesValue = Request::files()->toArray();
                    $scmService->addAddressBatch($filesValue, $postValue['scmNo']);
                    $this->layer(__('저장이 완료되었습니다.'), null, null, null, 'top.location.href="/";');
                    break;
                case 'link_selected':
                    $scmService = SlLoader::cLoad('godo','scmService','sl');
                    $scmService->setLinkScmPopup($postValue);
                    $this->layer(__('연결이 완료되었습니다.'), null, null, null, 'top.location.href="/";');
                    break;
                case 'invoice_excel_upload':
                    $filesValue = Request::files()->toArray();
                    $scmService->setOrderInvoiceByExcel($filesValue, $postValue);
                    $this->layer(__('송장 등록이 완료되었습니다.'), null, null, null, 'top.location.href="/";');
                    break;
                case 'invoice_excel_upload_order':
                    $filesValue = Request::files()->toArray();
                    $scmService->setOrderInvoiceByExcelOrder($filesValue, $postValue);
                    $this->layer(__('송장 등록이 완료되었습니다.'), null, null, null, 'top.location.href="/";');
                    break;
                default:
                    exit();
            }
        } catch (Exception $e) {
            throw new LayerNotReloadException($e->getMessage()); //새로고침안됨
        }
    }



}
