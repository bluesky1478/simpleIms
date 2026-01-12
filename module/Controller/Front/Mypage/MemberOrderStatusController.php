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

use Bundle\Component\PlusShop\PlusReview\PlusReviewArticleFront;
use Component\Board\BoardWrite;
use Component\Board\Board;
use Component\Database\DBTableField;
use Component\Goods\GoodsCate;
use Component\Page\Page;
use Cookie;
use Exception;
use Framework\Utility\GodoUtils;
use Request;
use Session;
use SiteLabUtil\SlCommonUtil;
use SlComponent\Database\DBUtil2;
use SlComponent\Util\ExcelCsvUtil;
use SlComponent\Util\SlLoader;
use SlComponent\Util\SlSkinUtil;
use Component\Board\BoardConfig;
use Framework\Debug\Exception\AlertBackException;
use Framework\Debug\Exception\AlertRedirectException;
use Framework\Debug\Exception\Except;
use App;
use Framework\Utility\DateTimeUtils;
use Framework\Utility\StringUtils;
use Message;
use Globals;

/**
 * Class MypageQnaController
 *
 * @package Bundle\Controller\Front\Mypage
 * @author  Jong-tae Ahn <qnibus@godo.co.kr>
 */
class MemberOrderStatusController extends \Bundle\Controller\Front\Mypage\OrderListController
{
    /**
     * {@inheritdoc}
     */
    public function index()
    {
        try {
            // 주문 리스트 정보
            $getParam = \Request::get()->toArray();
            $this->setData('key', gd_isset($getParam['key']));
            $this->setData('keyword', gd_isset($getParam['keyword']));
            $memberListService = SlLoader::cLoad('godo','memberListService','sl');

            $listData = $memberListService->getStatus($getParam);

            $this->setData('listData', $listData['list']);
            $this->setData('totalData', $listData['total']);
            $this->setData('requestUrl', basename(\Request::getPhpSelf()) .'?simple_excel_download=1&'.  \Request::getQueryString() );

            if(  !empty($getParam['simple_excel_download'])  ){
                $this->simpleExcelDownload($getParam);
                exit();
            }

        } catch (AlertBackException $e) {
            throw new AlertBackException($e->getMessage());
        } catch (Exception $e) {
            throw new AlertRedirectException($e->getMessage(), null, null, URI_HOME);
        }
    }

    public function simpleExcelDownload($getData){

        $title = 'TKE23년_하계근무복_주문현황_'.DateTimeUtils::dateFormat('Y-m-d', 'now');

        $data = $this->getData('listData');

        $excelBody = '';
        $excelBody .= "<tr><td colspan='6' style='font-size:20px;font-weight: bold;text-align: center; '>{$title}</td></tr>";
        $excelBody .= "<tr>";
        $excelBody .= ExcelCsvUtil::wrapTh('지점');
        $excelBody .= ExcelCsvUtil::wrapTh('회원');
        $excelBody .= ExcelCsvUtil::wrapTh('리뉴얼 하계 티셔츠');
        $excelBody .= ExcelCsvUtil::wrapTh('구매율');
        $excelBody .= ExcelCsvUtil::wrapTh('리뉴얼 하계 바지');
        $excelBody .= ExcelCsvUtil::wrapTh('구매율');
        $excelBody .= "</tr>";

        foreach ($data as $key => $val) {
            $excelBody .= "<tr>";
            $excelBody .= ExcelCsvUtil::wrapTd($val['deliveryName']);
            $excelBody .= ExcelCsvUtil::wrapTd(number_format($val['cnt']));
            $excelBody .= ExcelCsvUtil::wrapTd(number_format($val['teeCnt']));
            $excelBody .= ExcelCsvUtil::wrapTd(round($val['teeCnt']/$val['cnt']*100).'%');
            $excelBody .= ExcelCsvUtil::wrapTd(number_format($val['pantsCnt']));
            $excelBody .= ExcelCsvUtil::wrapTd(round($val['pantsCnt']/$val['cnt']*100).'%');
            $excelBody .= "</tr>";
        }

        $simpleExcelComponent = SlLoader::cLoad('Excel','SimpleExcelComponent','sl');
        $simpleExcelComponent->simpleDownload($title ,null,$excelBody, false);
    }

}
