<?php
namespace Controller\Admin\Ims\Cron;

use Component\Database\DBTableField;
use Component\Ims\ImsDBName;
use Request;

use Controller\Admin\Ims\ImsPsNkTrait;
use SiteLabUtil\SlCommonUtil;
use SlComponent\Database\DBUtil2;
use SlComponent\Database\SearchVo;
use SlComponent\Util\SlLoader;

class ImsCronSalesCustomerStatsController extends \Controller\Admin\Controller
{
    use ImsPsNkTrait;

    public function index() {
        //일일 배치 처리
        $imsService = SlLoader::cLoad('imsv2','ImsSalesService');
        $imsService->updateSalesCustomerStat();
        exit;
    }
}