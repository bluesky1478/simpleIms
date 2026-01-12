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
namespace Controller\Admin\Provider\Order;

use App;
use Component\Sitelab\SiteLabDownloadUtil;
use Exception;
use Request;
use Framework\Debug\Exception\LayerNotReloadException;
use Framework\Debug\Exception\LayerException;
use SlComponent\Database\DBUtil;
use SlComponent\Database\SearchVo;

/**
 * 주문 첨부파일 다운로드
 *
 * @package Bundle\Controller\Admin\Order
 * @author  su
 */
class OrderDownloadController extends \Controller\Admin\Controller{
    public function index(){
        $sno = Request::get()->get('sno');
        $fileData = DBUtil::getOne('sl_orderAttFile','sno',$sno);
        \SlComponent\Download\SiteLabDownloadUtil::download('.' . $fileData['fileDirPath'] , $fileData['fileName']);
        exit();
    }
}
