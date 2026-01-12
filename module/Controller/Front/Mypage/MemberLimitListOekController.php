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
class MemberLimitListOekController extends \Bundle\Controller\Front\Mypage\OrderListController
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
            $this->setData('buyFl', gd_isset($getParam['buyFl']));

            $memberListService = SlLoader::cLoad('godo','memberListService','sl');
            $listData = $memberListService->getList($getParam);
            $this->setData('listData', gd_isset($listData));
            $this->setData('total', count($listData));
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

        $title = '주문현황_'.DateTimeUtils::dateFormat('Y-m-d', 'now');

        $data = $this->getData('listData');

        $excelBody = '';
        $excelBody .= "<tr><td colspan='6' style='font-size:20px;font-weight: bold;text-align: center; '>{$title}</td></tr>";
        $excelBody .= "<tr>";
        $excelBody .= ExcelCsvUtil::wrapTh('아이디','title',null);
        $excelBody .= ExcelCsvUtil::wrapTh('이름','title',null);
        $excelBody .= ExcelCsvUtil::wrapTh('닉네임','title',null);
        $excelBody .= ExcelCsvUtil::wrapTh('동계점퍼(24 수량파악)','title',null);
        $excelBody .= ExcelCsvUtil::wrapTh('동계바지(24 수량파악)','title',null);
        $excelBody .= ExcelCsvUtil::wrapTh('구매 제한수량','title',null);
        $excelBody .= "</tr>";

        foreach ($data as $key => $val) {
            $excelBody .= "<tr>";
            $excelBody .= ExcelCsvUtil::wrapTd($val['memId']);
            $excelBody .= ExcelCsvUtil::wrapTd($val['memNm']);
            $excelBody .= ExcelCsvUtil::wrapTd($val['nickNm']);
            $excelBody .= ExcelCsvUtil::wrapTd(number_format($val['usedCount1']));
            $excelBody .= ExcelCsvUtil::wrapTd(number_format($val['usedCount2']));
            $excelBody .= ExcelCsvUtil::wrapTd($val['buyLimitCount']);
            $excelBody .= "</tr>";
        }

        $simpleExcelComponent = SlLoader::cLoad('Excel','SimpleExcelComponent','sl');
        $simpleExcelComponent->simpleDownload($title ,null,$excelBody, false);
    }

}
