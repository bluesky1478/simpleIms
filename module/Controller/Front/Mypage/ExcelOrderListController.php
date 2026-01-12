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
class ExcelOrderListController extends \Bundle\Controller\Front\Mypage\OrderListController
{
    /**
     * {@inheritdoc}
     */
    public function index()
    {
        try {

            $this->addCss(
                [
                    '../css/order/order.css'
                ]
            );

            // 주문 리스트 정보
            $getParam = \Request::get()->toArray();
            $this->setData('key', gd_isset($getParam['key']));
            $this->setData('keyword', gd_isset($getParam['keyword']));

            $sql = "select goodsNo, sum(goodsCnt) as goodsCnt from sl_tkeOrderGoods group by goodsNo order by goodsNo";
            $totalGoods = DBUtil2::runSelect($sql);
            $memCnt = DBUtil2::getCount('sl_tkeOrder', new SearchVo('1=?','1'));
            $teeCnt = $totalGoods[0]['goodsCnt'];
            $pantsCnt = $totalGoods[1]['goodsCnt'];

            $this->setData('memCnt', $memCnt);
            $this->setData('totalGoodsCnt', $teeCnt + $pantsCnt);
            $this->setData('teeCnt', $teeCnt);
            $this->setData('pantsCnt', $pantsCnt);

            $this->setData('updateDate', DBUtil2::getOne('sl_tkeOrder','1','1')['regDt']);

        } catch (AlertBackException $e) {
            throw new AlertBackException($e->getMessage());
        } catch (Exception $e) {
            throw new AlertRedirectException($e->getMessage(), null, null, URI_HOME);
        }
    }


}
