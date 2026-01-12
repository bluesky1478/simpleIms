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
use SlComponent\Database\SearchVo;
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
class AsianaEmployeeController extends \Bundle\Controller\Front\Mypage\IndexController
{
    /**
     * {@inheritdoc}
     */
    public function index()
    {
        try {
            // 주문 리스트 정보
            $getParam = \Request::get()->toArray();

            foreach($getParam as $paramKey => $param){
                $this->setData($paramKey,$param);
            }

            $controllerListService = SlLoader::controllerServiceLoad(__NAMESPACE__,'AsianaEmployeeList');
            $listService = SlLoader::cLoad('godo','listService','sl');
            $listService->setList($controllerListService, $this);

            $teamList3 = DBUtil2::getDistinctList('sl_asianaEmployee','concat(empTeam,\',\',empPart1,\',\',empPart2) as empPart2', new SearchVo('empTeam<>?','팀명'));
            $teamList3 = SlCommonUtil::arrayAppointedValue($teamList3, 'empPart2');
            $teamPartList = [];
            foreach( $teamList3 as $team ){
                $tpData = explode(',', $team);
                $teamPartList[$tpData[0]][$tpData[1]][] = $tpData[2];
            }
            $this->setData('teamPartList',json_encode($teamPartList));

        } catch (AlertBackException $e) {
            throw new AlertBackException($e->getMessage());
        } catch (Exception $e) {
            throw new AlertRedirectException($e->getMessage(), null, null, URI_HOME);
        }
    }


    /**
     * 엑셀 다운로드
     * @param $getData
     */
    public function simpleExcelDownload($getData){

        $data = $getData['data'];
        $page = $getData['page'];

        $title = '지급현황_'.DateTimeUtils::dateFormat('Y-m-d', 'now');

        $excelBody = '';
        $excelBody .= "<tr><td colspan='8' style='font-size:20px;font-weight: bold;text-align: center; '>{$title}</td></tr>";
        $excelBody .= "<tr>";
        $excelBody .= ExcelCsvUtil::wrapTh('번호','title',null);
        $excelBody .= ExcelCsvUtil::wrapTh('사번','title',null);
        $excelBody .= ExcelCsvUtil::wrapTh('이름','title',null);
        $excelBody .= ExcelCsvUtil::wrapTh('직급','title',null);
        $excelBody .= ExcelCsvUtil::wrapTh('팀명','title',null);
        $excelBody .= ExcelCsvUtil::wrapTh('파트명','title',null);
        $excelBody .= ExcelCsvUtil::wrapTh('소부문명','title',null);
        $excelBody .= ExcelCsvUtil::wrapTh('최근 지급내역','title','width:500px');
        $excelBody .= "</tr>";

        $cnt = 0;
        foreach ($data as $key => $val) {
            if('y' === $val['retiredFl']){
                $excelBody .= "<tr style='color:#ff0000'>";
            }else{
                $excelBody .= "<tr>";
            }
            $excelBody .= ExcelCsvUtil::wrapTd($page->idx--);
            $excelBody .= ExcelCsvUtil::wrapTd($val['companyId']);
            if('y' === $val['retiredFl']){
                $excelBody .= ExcelCsvUtil::wrapTd($val['empName'].' (퇴사)'); //,'text-danger', 'color:#ff0000'
            }else{
                $excelBody .= ExcelCsvUtil::wrapTd($val['empName']);
            }
            $excelBody .= ExcelCsvUtil::wrapTd($val['empRank']);
            $excelBody .= ExcelCsvUtil::wrapTd($val['empTeam']);
            $excelBody .= ExcelCsvUtil::wrapTd($val['empPart1']);
            $excelBody .= ExcelCsvUtil::wrapTd($val['empPart2']);
            $orderHistory = json_decode($val['orderHistory'],true);
            if( !empty($orderHistory) ){
                $historyArray = [];
                foreach($orderHistory as $orderHistoryData){
                    $provideDate = SlCommonUtil::setDateFormat($orderHistoryData['requestDt'],'y/m/d');
                    $historyArray[] = $provideDate . ' : ' . $orderHistoryData['prdName'].' x '.$orderHistoryData['orderCnt'].'ea';
                }
                $excelBody .= ExcelCsvUtil::wrapTd(implode('<br>',$historyArray));
            }else{
                $excelBody .= ExcelCsvUtil::wrapTd("");
            }
            $excelBody .= "</tr>";
            $cnt++;
        }

        $simpleExcelComponent = SlLoader::cLoad('Excel','SimpleExcelComponent','sl');
        $simpleExcelComponent->simpleDownload($title ,null,$excelBody, false);
    }

}
