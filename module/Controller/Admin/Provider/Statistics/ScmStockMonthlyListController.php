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

use Component\VisitStatistics\VisitStatistics;
use DateTime;
use Framework\Debug\Exception\AlertBackException;
use Request;
use SlComponent\Util\SlLoader;
use Component\Member\Manager;

/**
 * 일별 리포트
 * @author ssong
 */
class ScmStockMonthlyListController extends \Controller\Admin\Order\ScmStockMonthlyListController
{
    /**
     * index
     *
     * @throws \Exception
     */
    public function index()
    {
        // 공급사 정보 설정
        $isProvider = Manager::isProvider();
        $this->setData('isProvider', $isProvider);
        $this->setData('scmNo', \Session::get('manager.scmNo'));
        $this->setData('companyNm', \Session::get('manager.companyNm'));
        parent::index();
    }
}
