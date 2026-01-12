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
 * @link      http://www.godo.co.kr
 */

namespace Controller\Admin\Ims;


use Component\Erp\ErpCodeMap;
use Component\Ims\EnumType\PREPARED_TYPE;
use Component\Ims\ImsApprovalService;
use Component\Ims\ImsCodeMap;
use Component\Ims\ImsDBName;
use Component\Imsv2\ImsScheduleConfig;
use Component\Work\DocumentCodeMap;
use Component\Work\WorkCodeMap;
use SiteLabUtil\ImsUtil;
use SiteLabUtil\SlCommonUtil;
use SlComponent\Database\DBUtil2;
use SlComponent\Database\SearchVo;
use SlComponent\Database\TableVo;
use SlComponent\Util\SlCodeMap;
use SlComponent\Util\SlLoader;
use Request;
use SlComponent\Util\SlProjectCodeMap;
use Bundle\Component\CurrencyExchangeRate\CurrencyExchangeRate;
use Bundle\Component\CurrencyExchangeRate\CurrencyExchangeRateAdmin;
use Framework\Debug\Exception\LayerException;
use Component\Member\Manager;

trait ImsControllerTrait {

    public function getCodeSeasonList(){
        $list = DBUtil2::getList(ImsDBName::CODE, 'codeType', '시즌');
        return SlCommonUtil::arrayAppKeyValue($list,'codeValueEn','codeValueKr');
    }

    public function setDefault(){
        $current_page = Request::getRequestUri();
        $sessionPageInfo = \Session::get('ims_last_page');
        $deptList = SlCommonUtil::getDeptList();

        //팝업에든 리로드 적용하지 않음
        if( !(strpos($current_page,'/popup/')!==false) && !(strpos($current_page,'/ims/customer_view.php')!==false) ) {
            if (!empty( $sessionPageInfo ) && $sessionPageInfo === $current_page) {
                $this->setData('imsPageReload', 'y');
            }else{
                \Session::set('ims_last_page',$current_page);
                $this->setData('imsPageReload', 'n');
            }
            /*gd_debug('Before : ' . $sessionPageInfo);
            gd_debug('After  : '. $current_page);
            gd_debug('Reload  : '. $this->getData('imsPageReload'));*/
        }

        $this->setCssAndScript();

        $imsService = SlLoader::cLoad('ims','imsService');

        //연도 리스트.
        $yearList = [];
        $firstYear = 19;
        $lastYearCount = (substr(date('Y'),2,2) + 5) - $firstYear;

        for($i=0;$lastYearCount>=$i;$i++){
            $yearList[] = $firstYear+$i;
        }
        $this->setData('yearList',$yearList);
        //연도 리스트(4자리).
        $yearFullList = [];
        $firstYearFull = 2024;
        $lastYearCount = ((int)date('Y') + 5) - $firstYearFull;
        for($i=0;$lastYearCount>=$i;$i++){
            $yearFullList[$firstYearFull+$i.'년'] = $firstYearFull+$i;
        }
        $this->setData('yearFullList',$yearFullList);
        //시즌 리스트.
        $this->setData('seasonList', $this->getCodeSeasonList());
        //성별
        $this->setData('codeGender', $imsService->getCode('style','성별'));
        //스타일
        $this->setData('codeStyle', $imsService->getCode('style','스타일'));
        //색상
        $this->setData('codeColor', $imsService->getCode('style','색상'));
        //국가
        $prdNational = SlCommonUtil::arrayAppKeyValue(ImsCodeMap::PRD_NATIONAL, 'initial', 'name');
        $this->setData('prdNational', $prdNational);
        //생산형태
        $this->setData('prdType', ImsCodeMap::PRODUCE_TYPE);

        //신규 추가.
        $fabricStatus = ImsCodeMap::IMS_FABRIC_STATUS;
        unset($fabricStatus['']);
        $this->setData('fabricStatusMap', SlCommonUtil::arrayAppointedValue($fabricStatus,'name'));
        $btStatus = ImsCodeMap::IMS_BT_STATUS;
        unset($btStatus['']);
        $this->setData('btStatusMap', SlCommonUtil::arrayAppointedValue($btStatus,'name'));

        $this->setData('isMobile', SlCommonUtil::isMobile());
        $this->setData('nowYear', date('y'));

        $managerId = \Session::get('manager.managerId');
        $this->setData('managerId', $managerId);

        $this->addScript([
            '../../script/vue.js',
            '../../script/select2/js/select2.js',
            '../../script/datepicker/daterangepicker.js',
        ]);
        $this->addCss([
            '../../css/preloader.css',
            '../../css/font_awesome/css/font-awesome.css',
            '../../css/admin-ims.css?ver='.time(),
            '../../css/admin-addon.css?ver='.time(),
            '../../css/admin-nk.css?ver='.time(),
            '../../script/select2/css/select2.css',
            '../../script/datepicker/daterangepicker.css',
        ]);

        $this->setData('nasUrl',ImsCodeMap::NAS_URL);
        $this->setData('nasDownloadUrl',ImsCodeMap::NAS_DN_URL);
        $this->setData('nasAllDownloadUrl',ImsCodeMap::NAS_ALL_DN_URL);

        $nasDownloadUrl = ImsCodeMap::NAS_DN_URL;
        $this->setData('nasDownloadTag',"<a :href=\"'{$nasDownloadUrl}name='+encodeURIComponent(file.fileName)+'&path='+file.filePath\" class=\"text-blue\">{% fileIndex+1 %}. {% file.fileName %}</a>");
        $this->setData('meetingRegUrl', SlCommonUtil::getHost() . '/workAdmin/document.php?docDept=SALES&docType=10');

        $this->setData('guideUrl', SlCommonUtil::getHost() . '/ics/ics_guide.php'); //사양서
        $this->setData('assortUrl', SlCommonUtil::getHost() . '/ics/ics_assort.php'); //아소트
        $this->setData('eworkUrl', SlCommonUtil::getHost() . '/ics/ics_work.php'); //작지
        
        $this->setData('customerEstimateUrl', SlCommonUtil::getHost() . '/ics/customer_estimate.php');
        $this->setData('deptList' , $deptList);

        $requestList = \Request::request()->toArray();
        $this->setData('requestParam' , $requestList);
        $this->setData('myHost' , SlCommonUtil::getAdminHost());


        $isProvider = Manager::isProvider();
        if($isProvider){
            //파우치용
            $this->setData('imsAjaxUrl' , SlCommonUtil::getAdminHost().'/provider/statistics/ics_ps.php');
        }else{
            $this->setData('imsAjaxUrl' , SlCommonUtil::getAdminHost().'/ims/ims_ps.php');
        }

        $imsProduceService = SlLoader::cLoad('ims','imsProduceService');

        //고객사
        $this->setData('customerListMap', $imsService->getCustomerListMap());

        //샘플실
        $this->setData('sampleFactoryMap', $imsService->getSampleFactoryMap());

        //프로젝트 상태
        $this->setData('projectListMap', ImsCodeMap::PROJECT_STATUS);
        $this->setData('projectTypeMap', ImsCodeMap::PROJECT_TYPE);

        //권한자 설정. (삭제/승인 등)
        $authManager = ImsCodeMap::AUTH_MANAGER;
        $this->setData('authManager',$authManager);
        $this->setData('isAuth', in_array($managerId,$authManager));

        //삭제나 작지 리비전 권한자 설정.
        $imsManager = ImsCodeMap::IMS_ADMIN;
        $this->setData('imsManager',$imsManager);
        $this->setData('isImsManager', in_array($managerId,$imsManager));


/*
1	02001001 영업
2	02001002 디자인 teamSno
3	02001003 생산관리
4	02001004 회계
5	02001005 기타운영지원
6	02001006 생산처
7	02001007 영업외주
*/
        //gd_debug(SlCommonUtil::getManagerList());
        $searchVo = new SearchVo('scmNo=?','1');
        $searchVo->setWhere('isDelete=?');
        $searchVo->setWhereValue('n');
        $deptManager = [];
        $managerList = DBUtil2::getListBySearchVo(new TableVo(DB_MANAGER, 'tableManagerWithSno') , $searchVo);
        $salseManagerList = [];
        foreach( $managerList as $manager ){
            if(!empty($manager['departmentCd'])){
                $deptManager[$manager['departmentCd']][$manager['sno']] = $manager['managerNm'];

                //영업담당자
                if( '02001001' == $manager['departmentCd'] || '02001005' == $manager['departmentCd'] ){
                    $salseManagerList[$manager['sno']] = $manager['managerNm']. ' ('.SlCommonUtil::getCellPhoneFormat($manager['cellPhone']).')';
                }
            }
        }
        $this->setData('salseManagerList' , $salseManagerList);
        $this->setData('produceCompanyList' , $deptManager['02001006']);

        //$deptManager['02001005'][43] = '하나어패럴';
        unset($deptManager['02001006']);
        $deptManager['02001006'][43] = '하나어패럴';

        $managerList = $deptManager['02001001'] + $deptManager['02001002']  +  gd_isset($deptManager['02001003'],[])  +  gd_isset($deptManager['02001004'],[])  +  gd_isset($deptManager['02001005'],[]);

        $this->setData('managerList' , $managerList);

        $salesManagerList = [];
        if( SlCommonUtil::isDev() ){
            unset($deptManager['02001001'][17]);
            unset($deptManager['02001001'][21]);
            unset($deptManager['02001001'][23]);
            $salesManagerList[17] = '서재훈';
            $salesManagerList[21] = '문상범';
            $salesManagerList[23] = '한동경';
        } else {
            unset($deptManager['02001001'][18]);
            unset($deptManager['02001001'][32]);
            unset($deptManager['02001001'][14]);
            $salesManagerList[18] = '서재훈';
            $salesManagerList[32] = '문상범';
            $salesManagerList[14] = '한동경';
        }
        foreach($deptManager['02001001'] as $key => $each){
            $salesManagerList[$key]=$each;
        }

        $this->setData('salesManagerList' , $salesManagerList);//영업
        $this->setData('designManagerList' , $deptManager['02001002']);//디자인
        $this->setData('productionManagerList' , $deptManager['02001003']);//생산관리
        $this->setData('financeManagerList' , $deptManager['02001004']);//회계
        $this->setData('etcManagerList' , $deptManager['02001005']);//운영

        $this->setData('salesEtcManagerList' , $salesManagerList+$deptManager['02001005']+$deptManager['02001003']);//영업

        $this->setData('teamManagerList' , [
            'sales' => ['teamCode'=>'02001001','teamName'=>'영업', 'managers'=>$deptManager['02001001']],
            'design' => ['teamCode'=>'02001002','teamName'=>'디자인', 'managers'=>$deptManager['02001002']],
            'production' => ['teamCode'=>'02001003','teamName'=>'생산', 'managers'=>$deptManager['02001003']],
            'finance' => ['teamCode'=>'02001004','teamName'=>'회계', 'managers'=>$deptManager['02001004']],
            'etc' => ['teamCode'=>'02001005','teamName'=>'기타', 'managers'=>$deptManager['02001005']],
            'factory' => ['teamCode'=>'02001006','teamName'=>'하나어패럴', 'managers'=>$deptManager['02001006']],
        ]);

        //각 프로젝트 갯수 현황.
        //$this->setData('imsProjectStatusCount' , json_encode($imsService->getProjectStepCount()));
        //$this->setData('imsProduceStatusCount' , json_encode($imsProduceService->getProduceStepCount()));

        //$isProduce = in_array($managerId,ImsCodeMap::PRODUCE_COMPANY_MANAGER);
        $isProduce = SlCommonUtil::isFactory();
        $this->setData('imsPreparedCount', json_encode($imsService->getPreparedProduceCount()));

        //신규 생산 작업 카운팅
        $this->setData('imsProductionCount', json_encode($imsService->getProductionCount()));

        //estimate , qb 카운팅
        $this->setData('imsRequestCount', json_encode($imsService->getRequestCount()));

        //To-do 카운팅
        $this->setData('imsTodoRequestCount', json_encode($imsService->getTodoRequestCount()));

        $managerInfo = SlCommonUtil::getManagerInfo(\Session::get('manager.sno'));
        $managerInfo['deptName'] = $deptList[$managerInfo['departmentCd']];
        unset($managerInfo['managerPw']);

        $this->setData('loginManagerInfo',$managerInfo);
        $this->setData('managerInfo' , $managerInfo);

        $isSales = '02001001' === $managerInfo['departmentCd'];
        $this->setData('isSales', $isSales);
        $this->setData('deptName', $deptList[$managerInfo['departmentCd']] );
        $this->setData('teamSno', $managerInfo['departmentCd']);

        //생산처 파우치
        $this->setData('imsProduceCompany',$isProduce);

        //프로젝트 주요 파일 (제외할 부분 제외)

        $dpList = [];
        $unsetList = ['filePlan', 'fileProposal', 'fileWork', 'fileConfirm'];
        foreach( ImsCodeMap::PROJECT_FILE as $key => $value ){
            if(!in_array($value['fieldName'],$unsetList)){
                $dpList[] = $value;
            }
        }
        $this->setData('PROJECT_FILE_DP_LIST',$dpList);
        $this->setData('PROJECT_FILE_LIST',ImsCodeMap::PROJECT_FILE);
        //프로젝트 기타 파일
        $this->setData('PROJECT_ETC_FILE_LIST',ImsCodeMap::PROJECT_ETC_FILE);

        $this->setData('isDev',SlCommonUtil::isDevId());
        $this->setData('isDevSite',SlCommonUtil::isDev());
        $this->setData('PRODUCE_STEP_MAP',ImsCodeMap::PRODUCE_STEP_MAP);
        $this->setData('PREPARED_STATUS_MAP',PREPARED_TYPE::STATUS);

        $mId = \Session::get('manager.managerId');
        if( in_array($mId, ImsCodeMap::PRODUCE_COMPANY_MANAGER) ){
            $this->setData('isProduceCompany',true);
        }

        $this->setData('managerName',\Session::get('manager.managerNm'));
        $this->setData('managerSno',\Session::get('manager.sno'));

        //리스트별 담당자 지정
        $stepManager = [
            'step10' => 14, //미준 : 14 한동경
            'step20' => 34, //기획 : 34 정성희
            'step30' => 34, //제안 : 34 정성희
            'step40' => 35, //35 : 샘플 유수희
            'step50' => 14, //14 : 고객 한동경
            'step60' => 14, //14 : 발주 한동경
            'step80' => 32, //32 : 생산 문상범
            'step90' => 32, //32 : 생산 문상범
        ];


        $step = $requestList['status'];
        if( '/ims/imsProduceList.php' === Request::getPhpSelf() || '/ims/ims_produce_list.php'  === Request::getPhpSelf() ){
            $step = 'step80';
        }

        $stepManagerSno = $stepManager[$step];
        if( !empty($stepManagerSno) ){
            $stepManagerInfo = DBUtil2::getOne(DB_MANAGER,'sno',$stepManagerSno);
            //gd_debug($stepManagerInfo);

            $dutyCd = DBUtil2::getOne(DB_CODE, 'itemCd', $stepManagerInfo['dutyCd']);
            $positionCd = DBUtil2::getOne(DB_CODE, 'itemCd', $stepManagerInfo['positionCd']);

            $stepManagerInfo['positionName'] = '일반'===$dutyCd['itemNm']?$positionCd['itemNm']:$dutyCd['itemNm'];

            if( 11 >= strlen($stepManagerInfo['cellPhone']) ){
                $stepManagerInfo['cellPhone'] = SlCommonUtil::formatPhoneNumber($stepManagerInfo['cellPhone']);
            }
            $stepManagerInfo['dispImage'] = empty($stepManagerInfo['dispImage']) ? '/data/commonimg/ico_noimg_75.gif' : $stepManagerInfo['dispImage'];
            //$this->setData('stepManagerInfo', $stepManagerInfo);
        }


        //생산 스텝
        $this->setData('stepTitleList', ImsCodeMap::PRODUCE_STEP_MAP);
        $this->setData('stepList', ImsCodeMap::PRODUCTION_STEP);
        $this->setData('stepAllList', ImsCodeMap::PRODUCTION_STEP_ALL);

        $this->setData('projectListItemList', ImsCodeMap::PROJECT_ADD_INFO);

        //고객납기일 상태
        $this->setData('customerDeliveryStatus', ImsCodeMap::IMS_CUSTOMER_DELIVERY_STATUS);

        //QB요청 타입
        $this->setData('qbReqTypeList', ImsCodeMap::IMS_QB_REQ_TYPE);

        //결재 타입
        //$this->setData('approvalType', json_encode(SlCommonUtil::arrayAppointedValue(ImsApprovalService::APPROVAL_TYPE,'name')));
        $this->setData('approvalType', json_encode(ImsApprovalService::APPROVAL_TYPE));

        $currency = new CurrencyExchangeRate();
        $currencyUsd = $currency->getConfigListFromDao()->USD->manual;

        $this->setData('currencyUsd', $currencyUsd);
        $this->setData('currencyDate', date('Y-m-d') );

        $this->setData('managerName',\Session::get('manager.managerNm'));
        $this->setData('isDevId',SlCommonUtil::isDevId());

        $this->setData('allScheduleMap', json_encode(ImsScheduleConfig::SCHEDULE_LIST));
        $this->setData('industryMap', json_encode(ImsUtil::getIndustryMap()));
        $this->setData('industrySplitMap', json_encode(ImsUtil::getIndustrySplitMap()));

        //$this->setData('isFactory', $this->getData('isProduceCompany') );
        //$config->{$currencyVal['globalCurrencyString']}->adjustment
    }

    public function setProjectListRelatedController($request){
        $status = empty($request['status']) ? 'all' : $request['status'];
        $status = SlCommonUtil::extractNumbers($status);

        if('99' === $status || '98' === $status){
            $midType = 'reserved';
            $status = 'step'.$status;
        }else{
            $midType = 'project';
            if( is_numeric($status) ){
                //$midType = 'prj';
                $status = 'step'.$status;
            }
        }

        if(!empty($request['preparedType'])){
            $midType = 'prepared';
            $status = 'work';
        }

        if( !empty($request['popup']) || !empty($request['modify']) ){

        }else{
            $status = empty($status) ? 'step' : $status;
            $this->callMenu('ims', $midType, $status);
        }

        $this->setData('currentProjectStatus', gd_isset(SlCommonUtil::getOnlyNumber($status),10));
        // 정규식 패턴 view 파라미터 제거
        //$pattern = '/view=[^&]+$|searchFl=[^&]+$|view=[^&]+&|searchFl=[^&]+&/';//'/[?&]view=[^&]+$|([?&])view=[^&]+&/';
        $pattern = '/status=[^&]+$|view=[^&]+$|searchFl=[^&]+$|status=[^&]+&|view=[^&]+&|searchFl=[^&]+&/';
        // view 제거된 쿼리 스트링
        $queryString = preg_replace($pattern, '', Request::getQueryString());

        //gd_debug($queryString);
        $this->setData('queryString', substr('view=&','',$queryString));

    }

    public function setProduceListRelatedController($request){
        $status = empty($request['status']) ? 'step80' : $request['status'];
        if('step80' === $status || 'step90' === $status ){
            $midMenu = 'project';
        }else{
            $midMenu = 'prdManage';
        }
        $this->callMenu('ims', $midMenu, $status);
        //gd_debug(Request::getQueryString());
        // 정규식 패턴 view 파라미터 제거
        $pattern = '/status=[^&]*$|view=[^&]*$|searchFl=[^&]*$|status=[^&]*&|view=[^&]*&|searchFl=[^&]*&/';
        // view 제거된 쿼리 스트링
        $queryString = preg_replace($pattern, '', Request::getQueryString());
        //gd_debug($queryString);
        $this->setData('queryString', $queryString);

    }

    public function setCssAndScript(){
        $this->addScript([
            '../../script/vue.js',
            '../../script/select2/js/select2.js',
            '../../script/datepicker/daterangepicker.js',
            '../../script/sweetalert2.min.js',
            '../../script/vue2-datepicker.min.js',
            '../../script/vue2-datepicker-ko.js',
            '../../script/sortable.min.1.8.4.js',
            '../../script/vuedraggable.min.2.2.0.js',
            '../../gd_share/script/sms.js',
        ]);
        $this->addCss([
            '../../css/preloader.css',
            '../../css/font_awesome/css/font-awesome.css',
            '../../gd_share/css/bootstrap-datetimepicker.css',
            '../../gd_share/css/bootstrap-datetimepicker-standalone.css',
            '../../script/select2/css/select2.css',
            '../../script/datepicker/daterangepicker.css',
            '../../script/vue2-datepicker.css',
            '../../script/sweetalert2.min.css',
            '../../css/admin-ims.css?ver='.time(),
        ]);
    }

}
