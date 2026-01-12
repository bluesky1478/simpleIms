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
use SiteLabUtil\SlCommonUtil;
use SlComponent\Database\DBUtil2;
use SlComponent\Util\ExcelCsvUtil;
use SlComponent\Util\SlCodeMap;
use SlComponent\Util\SlLoader;
use Component\Member\Manager;
use Framework\Utility\SkinUtils;
use Component\Member\Member;

/**
 * scm_member_list
 * 회원관리
 * @author ssong
 */
class ScmAsianaEmpController extends \Controller\Admin\Controller
{
    /**
     * index
     *
     * @throws \Exception
     */
    public function index()
    {

        $this->addScript([
            '../../script/vue.js',
            '../../script/select2/js/select2.js',
            '../../script/datepicker/daterangepicker.js',
        ]);

        $this->addCss([
            '../../css/preloader.css',
            '../../css/font_awesome/css/font-awesome.css',
            '../../css/admin-ims.css?ver='.time(),
            '../../script/select2/css/select2.css',
            '../../script/datepicker/daterangepicker.css',
        ]);


        // 공급사 정보 설정
        $isProvider = Manager::isProvider();
        $this->callMenu('statistics', 'accept', 'emp');
        $this->setData('isProvider', $isProvider);
        $this->setData('scmNo', \Session::get('manager.scmNo'));
        $this->setData('companyNm', \Session::get('manager.companyNm'));

        $scmNo = \Session::get('manager.scmNo');
        $this->setData('scmConfig', DBUtil2::getOne('sl_setScmConfig','scmNo',$scmNo));
        $this->setData('requestUrl', basename(\Request::getPhpSelf()) .'?simple_excel_download=1&'.  \Request::getQueryString() );

        // 주문 리스트 정보
        $getParam = \Request::get()->toArray();
        $this->setData('reqParameter',$getParam);
        $controllerListService = SlLoader::controllerServiceLoad('Controller\Front\Mypage','AsianaEmployeeList');
        $listService = SlLoader::cLoad('godo','listService','sl');
        $listService->setList($controllerListService, $this);

        $this->setData('combineSearch',[
            'a.companyId' => '사번',
            'a.empName' => '이름',
            'a.empRank' => '직급',
            'a.empTeam' => '팀명',
            'a.empPart1' => '파트명',
            'a.empPart2' => '소부문명',
        ]);

        $this->setData('adminHost', SlCommonUtil::getAdminHost());

    }

}
