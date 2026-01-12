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


use Component\Excel\ExcelOrderCashReceiptConvert;
use Exception;
use Framework\Debug\Exception\AlertBackException;
use Logger;
use Request;
use SlComponent\Util\SitelabLogger;
use Component\Excel\ExcelFromHtmlStatisticsConvert;
use Component\Excel\ExcelVisitStatisticsConvert;
use Framework\Utility\ArrayUtils;
use Framework\Utility\StringUtils;

/**
 * Class 통계 엑셀 요청 처리 컨트롤러
 * @package Bundle\Controller\Admin\Order
 * @author  sueun
 */
class ExcelPsController extends \Bundle\Controller\Admin\Order\ExcelPsController
{
    public function index()
    {
        $requestPostParams = Request::post()->all();
        //SitelabLogger::logger('통계 확인');
        //SitelabLogger::logger($requestPostParams);

        /**
         * 요청 처리
         */
        switch ($requestPostParams['mode']) {
            case  'excel_download':
                if ($requestPostParams['excel_name'] == '') {
                    throw new Exception(__('요청을 찾을 수 없습니다.'));
                    break;
                }

                $this->streamedDownload($requestPostParams['excel_name'] . '.xls');
                $excel = new ExcelFromHtmlStatisticsConvert();
                $data = urldecode($requestPostParams['data']);
                $replaceData = $requestPostParams['replaceData'];
                // 특수문자 처리
                if (empty($replaceData) === false) {
                    $replaceData = ArrayUtils::removeEmpty(explode(STR_DIVISION, urldecode($replaceData)));
                    foreach ($replaceData as $value) {
                        if (StringUtils::contains($data, $value)) {
                            $data = str_replace($value, htmlentities($value), $data);
                        }
                    }
                }
                $excel->setExcelDownByJoinData($data);
                exit();
                break;
        }

        parent::index();
    }
}
