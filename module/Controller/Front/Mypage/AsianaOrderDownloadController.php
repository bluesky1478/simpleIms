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
use Component\Scm\ScmAsianaCodeMap;
use Cookie;
use Exception;
use Framework\Utility\GodoUtils;
use Request;
use Session;
use SiteLabUtil\SlCommonUtil;
use SlComponent\Database\DBUtil2;
use SlComponent\Util\ExcelCsvUtil;
use SlComponent\Util\SlCodeMap;
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
class AsianaOrderDownloadController extends \Bundle\Controller\Front\Mypage\IndexController
{
    /**
     * {@inheritdoc}
     */
    public function index()
    {
        $request = \Request::request()->toArray();
        $orderNo = $request['orderNo'];

        $title = '주문내역';
        $data = $this->getList($orderNo);

        $excelBody = '';
        $excelBody .= ExcelCsvUtil::wrapTh('주문일','title',null);
        $excelBody .= ExcelCsvUtil::wrapTh('승인일','title',null);
        $excelBody .= ExcelCsvUtil::wrapTh('사번','title',null);
        $excelBody .= ExcelCsvUtil::wrapTh('성명','title',null);
        $excelBody .= ExcelCsvUtil::wrapTh('직급','title',null);
        $excelBody .= ExcelCsvUtil::wrapTh('팀','title',null);
        $excelBody .= ExcelCsvUtil::wrapTh('파트','title',null);
        $excelBody .= ExcelCsvUtil::wrapTh('소부문','title',null);
        $excelBody .= ExcelCsvUtil::wrapTh('상품명','title',null);
        $excelBody .= ExcelCsvUtil::wrapTh('옵션','title',null);
        $excelBody .= ExcelCsvUtil::wrapTh('수량','title',null);
        $excelBody .= "</tr>";

        foreach ($data as $key => $val) {
            $excelBody .= "<tr>";
            $excelBody .= ExcelCsvUtil::wrapTd(substr($val['regDt'],0,10));
            $excelBody .= ExcelCsvUtil::wrapTd(substr($val['acctDt'],0,10));
            $excelBody .= ExcelCsvUtil::wrapTd($val['companyId']);
            $excelBody .= ExcelCsvUtil::wrapTd($val['empName']);
            $excelBody .= ExcelCsvUtil::wrapTd($val['empRank']);
            $excelBody .= ExcelCsvUtil::wrapTd($val['empTeam']);
            $excelBody .= ExcelCsvUtil::wrapTd($val['empPart1']);
            $excelBody .= ExcelCsvUtil::wrapTd($val['empPart2']);
            $excelBody .= ExcelCsvUtil::wrapTd($val['prdName']);
            $excelBody .= ExcelCsvUtil::wrapTd($val['prdOption'],'text', 'text-align:left');
            $excelBody .= ExcelCsvUtil::wrapTd($val['orderCnt'],'text', 'text-align:center');
            $excelBody .= "</tr>";
        }
        $simpleExcelComponent = SlLoader::cLoad('Excel','SimpleExcelComponent','sl');
        $simpleExcelComponent->simpleDownload($title ,null,$excelBody, false);

        /*$fileName = $reqData['fileName'];
        $spreadsheet = $this->get3PlOrderExcelReal($reqData);
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'.$fileName.'"');
        header('Cache-Control: max-age=0');
        $writer = new Xls($spreadsheet);
        $writer->save('php://output');*/

        exit();
    }

    public function refineZero($cnt){
        return 0 == $cnt? '':$cnt;
    }

    public function getList($orderNo){
        $sql = "
select 
    b.companyId,
    c.empName,
    c.empRank,
    c.empTeam,
    c.empPart1,
    c.empPart2,
    b.prdName,
    b.prdOption,
    b.orderCnt,
    d.regDt,
    d.acctDt   
from es_orderGoods a 
left outer join sl_asianaOrderHistory b on a.sno = b.orderGoodsSno
left outer join sl_asianaEmployee c on b.companyId = c.companyId
join sl_orderAccept d on a.orderNo = d.orderNo
where a.scmNo = 34
  and a.orderNo in ( {$orderNo} )
order by a.orderNo desc, c.empTeam, c.empPart1, c.empPart2, c.empRank, c.empName, b.prdName, b.prdOption 
";
        return DBUtil2::runSelect($sql);
    }


}
