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
class AsianaGoodsDownloadController extends \Bundle\Controller\Front\Mypage\IndexController
{
    /**
     * {@inheritdoc}
     */
    public function index()
    {
        $title = '아시아나에어포트상품_'.DateTimeUtils::dateFormat('Y-m-d', 'now');
        $data = $this->getList();

        $excelBody = '';
        $excelBody .= ExcelCsvUtil::wrapTh('상품코드','title',null);
        $excelBody .= ExcelCsvUtil::wrapTh('상품명','title',null);
        $excelBody .= ExcelCsvUtil::wrapTh('옵션','title',null);
        $excelBody .= ExcelCsvUtil::wrapTh('현재고량','title',null);
        $excelBody .= ExcelCsvUtil::wrapTh('지급연한','title',null);
        $excelBody .= ExcelCsvUtil::wrapTh('지급수량','title',null);
        $excelBody .= ExcelCsvUtil::wrapTh('분류1','title',null);
        $excelBody .= ExcelCsvUtil::wrapTh('분류2','title',null);
        $excelBody .= "</tr>";

        foreach ($data as $key => $val) {
            $excelBody .= "<tr>";
            $excelBody .= ExcelCsvUtil::wrapTd($val['optionSno']);
            $excelBody .= ExcelCsvUtil::wrapTd($val['goodsNm']);
            $excelBody .= ExcelCsvUtil::wrapTd($val['optionValue1']);
            $excelBody .= ExcelCsvUtil::wrapTd(number_format($val['stockCnt']));

            if ( SlCommonUtil::isDev() ){
                $excelBody .= ExcelCsvUtil::wrapTd($this->refineZero(ScmAsianaCodeMap::GOODS_PROVIDE_INFO_DEV[$val['goodsNo']]['yearCnt']));
                $excelBody .= ExcelCsvUtil::wrapTd($this->refineZero(ScmAsianaCodeMap::GOODS_PROVIDE_INFO_DEV[$val['goodsNo']]['provideCnt']));
            }else{
                $excelBody .= ExcelCsvUtil::wrapTd($this->refineZero(ScmAsianaCodeMap::GOODS_PROVIDE_INFO[$val['goodsNo']]['yearCnt']));
                $excelBody .= ExcelCsvUtil::wrapTd($this->refineZero(ScmAsianaCodeMap::GOODS_PROVIDE_INFO[$val['goodsNo']]['provideCnt']));
            }

            $excelBody .= ExcelCsvUtil::wrapTd($val['cate1']);
            $excelBody .= ExcelCsvUtil::wrapTd($val['cate2']);
            $excelBody .= "</tr>";
        }

        $simpleExcelComponent = SlLoader::cLoad('Excel','SimpleExcelComponent','sl');
        $simpleExcelComponent->simpleDownload($title ,null,$excelBody, false);
    }

    public function refineZero($cnt){
        return 0 == $cnt? '':$cnt;
    }

    public function getList(){
        $sql = "
select 
       goodsNm, 
       b.sno as optionSno, 
       optionValue1, 
       stockCnt, 
       confirmRequestStock as prvCnt, 
       c.cateNm as cate1,
       d.cateNm as cate2, 
       a.goodsNo
from es_goods a 
join es_goodsOption b on a.goodsNo = b.goodsNo 
left outer join es_categoryGoods c on left(a.cateCd,6) = c.cateCd
left outer join es_categoryGoods d on a.cateCd = d.cateCd 
where a.scmNo = 34 and a.delFl = 'n'
";

        return DBUtil2::runSelect($sql);
    }


}
