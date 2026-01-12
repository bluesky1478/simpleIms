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
namespace Controller\Admin\Order;

use App;
use Component\Claim\ClaimListService;
use Component\Sitelab\SiteLabDownloadUtil;
use Exception;
use Request;
use Framework\Debug\Exception\LayerNotReloadException;
use Framework\Debug\Exception\LayerException;
use SlComponent\Database\DBUtil;
use SlComponent\Database\DBUtil2;
use SlComponent\Database\SearchVo;
use SlComponent\Util\ExcelCsvUtil;
use SlComponent\Util\SlLoader;

/**
 * 주문 첨부파일 다운로드
 *
 * @package Bundle\Controller\Admin\Order
 * @author  su
 */
class DownloadTodayReleaseController extends \Controller\Admin\Controller{
    public function index(){
        if( !empty(\Request::get()->get('showStock')) ){
            $showStock = true;
        }else{
            $showStock = false;
        }

        $sopService = SlLoader::cLoad('godo','sopService','sl');
        $today = date('Ymd');
        $subject = $today.'_출고리스트';
        $sopService->makeFileRealExcel([
            'fileName'=>$subject.'.xls',
            'subject'=>$subject,
            'isOrder'=>false,
            'showStock'=>$showStock,
            'today'=>$today,
        ]);

        exit();
    }
}
