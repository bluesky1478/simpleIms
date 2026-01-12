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
namespace Controller\Admin\Provider\Statistics;

use Component\Scm\ScmOrderListService;
use Component\VisitStatistics\VisitStatistics;
use Component\Mall\Mall;
use DateTime;
use Framework\Debug\Exception\AlertBackException;
use Request;
use SiteLabUtil\SlCommonUtil;
use SlComponent\Util\ExcelCsvUtil;
use SlComponent\Util\SlCodeMap;
use SlComponent\Util\SlLoader;
use Component\Member\Manager;

/**
 * (공급사용) 출고통계
 * @author Seung-gak Kim <surlira@godo.co.kr>
 */
class ClaimReportTodayController extends \Controller\Admin\Order\ClaimReportTodayController
{
    /**
     * index
     *
     * @throws \Exception
     */
    public function index()
    {
        parent::index();
    }

}
