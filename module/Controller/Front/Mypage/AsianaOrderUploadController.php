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
use SlComponent\Util\PhpExcelUtil;
use SlComponent\Util\SitelabLogger;
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
class AsianaOrderUploadController extends \Controller\Front\Controller
{
    /**
     * {@inheritdoc}
     */
    public function index()
    {
        $params = \Request::request()->toArray();
        $service = SlLoader::cLoad('scm','ScmAsianaService');
        $service->setUploadController($this);
        $this->json(
            [
                'code' => 200,
                'message' => '업로드 완료',
                'data' => $params,
            ]
        );
    }

}
