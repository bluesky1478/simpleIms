<?php

namespace Controller\Admin\Test;

use App;
use Component\Database\DBIms;
use Component\Database\DBTableField;
use Component\Deposit\Deposit;
use Component\Erp\ErpCodeMap;
use Component\Erp\ErpService;
use Component\Goods\GoodsPolicy;
use Component\Ims\EnumType\APPROVAL_STATUS;
use Component\Ims\EnumType\PREPARED_TYPE;
use Component\Ims\EnumType\TODO_STATUS;
use Component\Ims\EnumType\TODO_TARGET_TYPE;
use Component\Ims\EnumType\TODO_TYPE;
use Component\Ims\EnumType\TODO_TYPE2;
use Component\Ims\ImsApprovalService;
use Component\Ims\ImsCodeMap;
use Component\Ims\ImsDBName;
use Component\Ims\ImsJsonSchema;
use Component\Ims\ImsSendMessage;
use Component\Ims\ImsService;
use Component\Ims\StatusValidService;
use Component\Imsv2\ImsFieldUtil;
use Component\Imsv2\ImsProjectService;
use Component\Imsv2\ImsScheduleConfig;
use Component\Imsv2\ImsScheduleUtil;
use Component\Member\Util\MemberUtil;
use Component\Scm\AlterCodeMap;
use Component\Scm\ScmAsianaCodeMap;
use Component\Scm\ScmHyundaeService;
use Component\Scm\ScmTkeService;
use Component\Sitelab\SiteLabSmsUtil;
use Component\Work\Code\DocumentDesignCodeMap;
use Component\Work\DocumentCodeMap;
use Component\Work\WorkCodeMap;
use Controller\Admin\Sales\ControllerService\SalesListService;
use Encryptor;
use Framework\Utility\DateTimeUtils;
use Framework\Utility\NumberUtils;
use Globals;
use Request;
use SiteLabUtil\ImsUtil;
use SiteLabUtil\SlCommonUtil;
use SlComponent\Api\ExchangeRateService;
use SlComponent\Database\DBUtil;
use SlComponent\Database\DBUtil2;
use SlComponent\Database\SearchVo;
use SlComponent\Database\TableVo;
use SlComponent\Godo\SamYoungService;
use SlComponent\Mail\SiteLabMailMessage;
use SlComponent\Mail\SiteLabMailUtil;
use SlComponent\Util\ApiTrait;
use SlComponent\Util\CUrlUtil;
use SlComponent\Util\DocumentStruct;
use SlComponent\Util\ExcelCsvUtil;
use SlComponent\Util\SitelabLogger;
use SlComponent\Util\SitelabUtil;
use SlComponent\Util\SlCode;
use SlComponent\Util\SlCodeMap;
use SlComponent\Util\SlKakaoUtil;
use SlComponent\Util\SlLoader;
use SlComponent\Util\SlPostRequestUtil;
use SlComponent\Util\SlProjectCodeMap;
use SlComponent\Util\SlSmsUtil;
use UserFilePath;
use Framework\Utility\StringUtils;
use Component\Storage\Storage;
use Framework\Security\Digester;
use Framework\Utility\GodoUtils;
use DateTime;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Reader\Html;
use PhpOffice\PhpSpreadsheet\Writer\Xls;
use PhpOffice\PhpSpreadsheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Cell;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

use Bundle\Component\CurrencyExchangeRate\CurrencyExchangeRate;
use Bundle\Component\CurrencyExchangeRate\CurrencyExchangeRateAdmin;
use Framework\Debug\Exception\LayerException;

/**
 * TEST 페이지
 */
class TestController extends \Controller\Admin\Controller{

    use ApiTrait;

    private $orderService;

    /**
     * @throws \Exception
     */
    public function index(){

        ImsUtil::setSyncStatus(260,'');

        /*$map = ImsScheduleUtil::getAutoScheduleKey();
        gd_debug($map);*/

        /*$imsService = SlLoader::cLoad('ims', 'imsService');
        $list = $imsService->getListSendHistory([
            'sendType' => '제안서',
            'projectSno' => 172,
        ]);
        gd_debug($list);*/


        /*$managerList1 = SiteLabMailUtil::getTeamMail( ImsCodeMap::TEAM_SALES );
        $managerList2 = SiteLabMailUtil::getTeamMail( ImsCodeMap::TEAM_DESIGN );
        gd_debug(implode(',',array_merge($managerList1, $managerList2, ['jhseo@msinnover.com'])));*/
        //DBUtil2::getList(ImsDBName::SALES_CUSTOMER);

        /*$rslt = ImsScheduleUtil::getAutoScheduleKey();
        gd_debug($rslt);*/

        //ImsUtil::refreshSalesPrice();

        /*$data = ImsUtil::getTmListData([
            'projectSnoList' => [232,222,260]
        ]);
        gd_debug($data);*/


        /*$data = ImsUtil::getProjectCommentCount(232);*/
        /*$data = ImsScheduleUtil::getCommentListData([
            'projectSnoList' => [232,222]
        ]);
        gd_debug($data);*/

        //$now = SlCommonUtil::getNowDate();
        //$prevDay = SlCommonUtil::getDateCalc($now, -20);
        //gd_debug(date('Ymd'));
        //gd_debug(date('Ymd', strtotime('-20 day')));

        /*$start = date('Y-m-d', strtotime('-30 day'));
        $end = date('Y-m-d');
        $service = SlLoader::cLoad('godo','sopService','sl');
        $service->reg3plInHistory($start, $end);*/

        //DBUtil2::runSelect("select count(1) as cnt from sl_imsComment where commentDiv='{$scheduleKey}' and projectSno={$each['sno']} ")[0]['cnt'];

        //전체 갱신
        ImsUtil::refreshProject(260);

        /*$list = DBUtil2::getList(ImsDBName::PROJECT,'1','1');
        foreach($list as $each){
            ImsUtil::refreshProject($each['sno']);
        }
        ImsUtil::refreshSalesPrice(); //영업 집계 정보도 함께 갱신*/

        /*$prjExt = DBUtil2::getOne(ImsDBName::PROJECT_EXT, 'projectSno', 232);
        $target = ImsProjectService::getExpectedSchedule(232, $prjExt);
        gd_debug($target);*/

        /*$prjExt = DBUtil2::getOne(ImsDBName::PROJECT_EXT, 'projectSno', 232);
        $list = ImsProjectService::getProjectAddManagerList(232, $prjExt);
        gd_debug($list);*/


        //$projectAddManager = ImsProjectService::getProjectAddManagerList($projectSno);

        //$projectManager = DBUtil2::getList(ImsDBName::PROJECT_MANAGER, 'projectSno', 232);
        //gd_debug($projectManager);

        /*$list = ImsScheduleConfig::SCHEDULE_LIST;
        $list = ImsScheduleUtil::getScheduleMap();
        gd_debug($list);*/

        /*$prjExt = DBUtil2::getOne(ImsDBName::PROJECT_EXT, 'projectSno', 232);
        $projectAddManager = ImsProjectService::getProjectAddManagerList(232, $prjExt);
        $addManagerScheduleMap = [];
        foreach($projectAddManager as $managerList){
            foreach($managerList as $manager){
                $addManagerScheduleMap[$manager['managerSno']]['managerNm'] = $manager['managerNm'];
                $addManagerScheduleMap[$manager['managerSno']]['schedule'][] = [
                    'scheduleName' => ImsScheduleConfig::SCHEDULE_LIST[$manager['scheduleDiv']]['name'],
                    'scheduleDt' => $prjExt['ex'.ucfirst($manager['scheduleDiv'])],
                ];
            }
        }
        gd_debug( $addManagerScheduleMap );*/



        /*$prjExt = DBUtil2::getOne(ImsDBName::PROJECT_EXT, 'projectSno', 232);
        $addManagerlist = ImsProjectService::getProjectAddManagerList(232, $prjExt);
        $addManagerScheduleMap = [];
        foreach($addManagerlist as $managerList){
            foreach($managerList as $manager){
                $addManagerScheduleMap[$manager['managerSno']]['managerNm'] = $manager['managerNm'];
                $addManagerScheduleMap[$manager['managerSno']][$manager['scheduleDiv']] = [
                    'scheduleName' => ImsScheduleConfig::SCHEDULE_LIST[$manager['scheduleDiv']]['name'],
                    'scheduleDt' => $prjExt['ex'.ucfirst($manager['scheduleDiv'])],
                ];
            }
        }
        gd_debug($addManagerScheduleMap);*/



        /*$searchVo = new SearchVo('projectSno=?', 232);
        $searchVo->setDistinct();
        $manager = DBUtil2::getListBySearchVo(ImsDBName::PROJECT_MANAGER, $searchVo);*/
        /*$searchVo = new SearchVo('projectSno=?', 232);
        $searchVo->setDistinct();
        $manager=DBUtil2::getJoinList(ImsDBName::PROJECT_MANAGER, [
            'b' => [DB_MANAGER, 'a.managerSno=b.sno']
        ],$searchVo);
        gd_debug($manager);

        $tableInfo=DBUtil2::setTableInfo($tableInfo,false);
        DBUtil2::getComplexList($tableInfo,$searchVo, false, false, true);*/


        //$scheduleDivList = array_keys(ImsScheduleConfig::SCHEDULE_LIST_TYPE1);

        /*$prjExt = DBUtil2::getOne(ImsDBName::PROJECT_EXT, 'projectSno', 232);
        $scheduleKeyList = array_keys(ImsScheduleConfig::SCHEDULE_LIST);
        $scheduleTargetKeyList = [];
        foreach($scheduleKeyList as $key => $each){
            if( !empty($prjExt['ex'.ucfirst($each)])
                && '0000-00-00' !== $prjExt['ex'.ucfirst($each)]
                && ( empty($prjExt['cp'.ucfirst($each)]) || '0000-00-00' == $prjExt['cp'.ucfirst($each)] )
                && ( empty($prjExt['tx'.ucfirst($each)]) )
            ){
                $scheduleTargetKeyList[] = $each;
            }
        }

        $scheduleDivList = ['readyToDesign'];
        $searchVo = new SearchVo('projectSno=?', 232);
        $searchVo->setDistinct();
        $searchVo->setWhere(DBUtil2::bind('scheduleDiv', DBUtil2::IN, count($scheduleDivList) ));
        $searchVo->setWhereValueArray( $scheduleDivList );

        $projectAddManager = DBUtil2::getSimpleJoinList([
            'tableName' => ImsDBName::PROJECT_MANAGER,
            'field' => 'b.managerNm',
        ],['b' => [DB_MANAGER, 'a.managerSno=b.sno','b.sno as managerSno']], $searchVo);

        $addManagerNameList = [];
        foreach($projectAddManager as $managerInfo){
            $addManagerNameList[] = $managerInfo['managerNm'];
        }*/




        //TODO : 해야할 것 ==> JSON 으로 담당자 저장해서 현재 그가 어떤거에 걸려 있는지 알아야한다.
        //다음 일정도 알아야한다 . . . ..


        //$projectAddManager = ImsProjectService::getProjectAddManager(232);
        //gd_debug($projectAddManager);

        /*$rslt = ImsProjectService::getProjectAddManager(232);
        gd_debug( $rslt );*/

        /*$searchVo = new SearchVo('projectSno=?', 232);
        $addManagerList=DBUtil2::getJoinList(ImsDBName::PROJECT_MANAGER, [
            'b' => [DB_MANAGER, 'a.managerSno=b.sno', 'b.managerNm']
        ],$searchVo);
        $addManagerMap = [];
        foreach($addManagerList as $each){
            $addManagerMap[$each['scheduleDiv']]['managerDetail'][] = $each;
            $addManagerMap[$each['scheduleDiv']]['managerNameList'][] = $each['managerNm'];
        }
        foreach($addManagerMap as $scheduleDiv => $each){
            $addManagerMap[$scheduleDiv]['managerStr'] = implode(',',$each['managerNameList']);
        }
        //$addManagerList = SlCommonUtil::arrayAppKey($addManagerList, 'scheduleDiv');
        gd_debug($addManagerMap);*/

        /**/

        /*$service = SlLoader::cLoad('ims25','ims25ListService');
        $list = $service->getIms25List('all', $params);
        gd_debug($list);*/

        //$service->update3plStock(); //재고 갱신

        /*$imsService = SlLoader::cLoad('ims', 'imsService');
        $data = $imsService->getFactoryEstimate(['sno'=>198]);
        gd_debug($data);*/

        /*$imsService = SlLoader::cLoad('imsv2','ImsSalesService');
        $imsService->updateSalesCustomerStat();*/

        //가망고객 ( 낼 다시 체크 )
        /*$info = DBUtil2::getOne('sl_salesCustomerStats', 'sno', 10);
        $customer2 = json_decode($info['jsonCustomer2']);
        $customer3 = json_decode($info['jsonCustomer3']);
        //gd_debug($customer2);
        //gd_debug($customer3);
        foreach($customer2 as $custSno){
            DBUtil2::update('sl_salesCustomerInfo', ['customerType'=>20], new SearchVo('sno=?', $custSno));
        }
        foreach($customer3 as $custSno){
            DBUtil2::update('sl_salesCustomerInfo', ['customerType'=>30], new SearchVo('sno=?', $custSno));
        }*/


        //$stock = SamYoungService::get3PlStock();
        //$stock = SamYoungService::requestWaitOutHistoryData();
        /*$stock = SamYoungService::requestOutHistoryData('20251119','20251119');
        gd_debug('출고 예정 데이터');
        gd_debug($stock);*/

        /*$outData = SamYoungService::requestOutHistoryData('20250822','20250822');
        gd_debug('출고 데이터');
        gd_debug($outData);*/

        //송장 등록 12:-- 부터
        //출고 이력 등록 매달 1일 부터 말일까지.
        //12월 1일 => 이전 일자 체크 11월 30일 => 11월 1일 부터 11월 30일까지 출고 이력 재 등록 / 단 분류 패킹은 지우지 말 것 (데일리 배치 처리)
        //기타가 가망고객 ?

        /*$now = SlCommonUtil::getNowDate();
        $prevDay = SlCommonUtil::getDateCalc($now, -20);
        gd_debug( $prevDay );
        gd_debug( $now );
        DBUtil2::delete('sl_3plStockInOut', new SearchVo([" memo <> '분류배송' and inOutDate >= ?",'? >= inOutDate'],[$prevDay, $now]) );
        $service = SlLoader::cLoad('godo','sopService','sl');
        $service->regSimple3plOutHistory($prevDay, $now);*/

        //$outData = SamYoungService::requestOutHistoryData($now, $prevDay);


        /*$a = [1,2,3,4,5];
        gd_debug(gd_count($a));

        $k = gd_implode(',', $a);
        gd_debug($k);
        $seasonPrd['prdSeason'] = 'FW';
        if( gd_in_array($seasonPrd['prdSeason'],['FW','SS']) ){
            $each[strtolower($seasonPrd['prdSeason']).'Style'][] = $seasonPrd;
        }else{
            $each['etcStyle'][] = $seasonPrd;
        }
        */


        /*$service = SlLoader::cLoad('scm','ScmAsianaService');
        $service->saveEmpAllHistory('983183');
        $service->saveEmpAllHistory('983138');*/

        //아시아나 처리
        /*$service = SlLoader::cLoad('scm','ScmAsianaService');
        $refreshList = DBUtil2::runSelect("select distinct companyId from sl_asianaOrderHistory");
        $cnt = 0;
        foreach($refreshList as $data){
            $service->saveEmpAllHistory($data['companyId']);
            $cnt++;
        }
        gd_debug('총 : '. $cnt);*/

        //$hopeDt = SlCommonUtil::getDateCalc(date('Y-m-d'), -1);
        //gd_debug($hopeDt);

        /*$cateList = DBUtil2::getListBySearchVo('sl_imsBasicBusiCate', new SearchVo("parentBusiCateSno <> ?",'0'));
        $cateMap = [];
        foreach($cateList as $cateInfo){
            if( !empty($cateInfo['parentBusiCateSno']) ){
                $cateMap[$cateInfo['cateName']]=$cateInfo['sno'];
            }
        }
        gd_debug($cateMap);*/


        /*$imsService = SlLoader::cLoad('imsv2','ImsProjectListService');
        $list = $imsService->getSalesAnotherList([]);
        gd_debug($list);*/

        //$this->sendHkResearch();

        //Ework 복사
        /*$imsStyleService = SlLoader::cLoad('ims', 'imsStyleService');
        $imsStyleService->copyStyleBasicInfo([
            'srcSno' => 1512,
            'targetSno' => 2960,
        ]);
        $imsStyleService->copyStyleBasicInfo([
            'srcSno' => 2357,
            'targetSno' => 2961,
        ]);
        $imsStyleService->copyStyleBasicInfo([
            'srcSno' => 467,
            'targetSno' => 2959,
        ]);*/


        //gd_debug(ImsScheduleUtil::getScheduleList());

        /*$rslt = DBIms::tableImsProjectExt2();
        gd_debug($rslt);*/

        /*$manualService = SlLoader::cLoad('godo','manualService','sl');
        $orderNo = '2510291606071978'; //622,050 => 435,435원
        $manualService->refineOrderGoodsDc($orderNo, 30);*/

        /*$list = DBUtil2::getList(ImsDBName::UPDATE_HISTORY, 'tableSno', 2058, null, false);

        foreach($list as $each){
            $assort = json_decode($each['contents'],true)['assort'];
            gd_debug($each['regDt']);
            gd_debug($assort);
            gd_debug(json_decode($assort,true));
        }*/

        //아시아나 갱신
        /*$service = SlLoader::cLoad('scm','ScmAsianaService');
        $refreshList = DBUtil2::runSelect("select distinct companyId from sl_asianaOrderHistory");
        foreach($refreshList as $data){
            $service->saveEmpAllHistory($data['companyId']);
        }*/

        //$hyundaeService = SlLoader::cLoad('scm','scmHyundaeService');
        //$hyundaeService->getPackingList();
        //$hyundaeService->getPackingOrderList();

        //암호변경
        //$password = Digester::digest('a10392918');
        //gd_debug($password);


        //$imsProjectService = SlLoader::cLoad('imsv2','ImsProjectService');
        //$imsProjectService->copyStyle(688, 242); //
        //gd_debug(date('Y-m-d',strtotime('-1 month')));
        //if( empty($params['srcSno']) ) throw new \Exception('원본 스타일 번호가 없습니다. (개발팀 문의)');
        //if( empty($params['targetSno']) ) throw new \Exception('대상 스타일 번호가 없습니다. (개발팀 문의)');
        //$params['srcSno']
        //$params['targetSno']


        //$stockService = SlLoader::cLoad('imsv2','ImsStockService');
        //$list = $stockService->getReport();
        //gd_debug($list);

        /*$stockService = SlLoader::cLoad('imsv2','ImsStockService');
        //$list = $stockService->getGoodsStockTotalInfo($params);
        $params['goodsNo'] = '1000002255';
        $list = $stockService->getReservedList($params);

        gd_debug($list);*/


        //$list = DBTableField::getTableKey('sl_imsProjectExt');
        //gd_debug(array_combine($list, $list));

        //"\Component\Imsv2\Lst\StockInOutList";
        /*$service = SlLoader::cLoad('ims','ImsStyleServiceSql');
        $rslt = $service->getStyleTable();

        foreach($rslt as $each){
            $tableName = $each->getTableName();
            gd_debug($tableName);
            $tableInfoList = DBTableField::callTableFunction($tableName);
            gd_debug($tableInfoList);
        }*/

        /*$stock = SamYoungService::get3PlStock();
        gd_debug('재고 데이터');
        gd_debug($stock);*/

        //$inData = SamYoungService::requestInHistoryData('20230601','20230610');
        //gd_debug('입고 데이터');
        //gd_debug($inData);


        //ImsUtil::refreshSalesPrice();

        /*$godoOrder = $service->getGodoOrder([
            'receiverName'=>'송준호님',
            'receiverCellPhone'=>'010-8109-9599',
            'code'=>'MSGOLF002',
            //'goodsCnt'=>$params['count'],  //수량은 분할해서 처리될 수 있음.
        ]);*/
        //$stockService = SlLoader::cLoad('imsv2','ImsStockService');
        //$params=['cateCd' => '002'];
        //$rslt = $stockService->getGoodsCate($params);
        //$rslt = $stockService->get3PlPrdAttr(['scmSno'=>6]);


        /*$condition = [
            'attr' => [
                0 => [1=>'TS',2=>'하계',3=>'카라티',4=>'',5=>'',],
                1 => [1=>'TS',2=>'하계',3=>'바지',4=>'',5=>'',],
            ],
            'multiKey' => [
                0 => ['key'=>'productName','keyword'=>'점퍼'],
                1 => ['key'=>'productName','keyword'=>'카라티'],
            ],
            'multiCondition' => 'OR',
            'scmSno' => '6',
        ];
        $rslt = $stockService->getGoodsStockUnlink($condition);
        gd_debug($rslt);*/

        /*$list = $service->getReleaseList();*/
        //gd_debug($list);

        //$list = $stockService->getGoodsStockTotalInfoDetail(['goodsNo' => '1000000122',]);
        //gd_debug($list);
        //$list = $stockService->getGoodsStockTotalInfo(['scmSno' => '6',]);
        //gd_debug($list);

        /*$list = [ 1000000229, 1000000170, 1000000231, 1000000281, 1000000285, 1000000283];
        foreach($list as $goodsNo){
            DBUtil2::runSql("update es_goods set optionDisplayFl='s' , optionName='사이즈' where goodsNo = {$goodsNo}");
            DBUtil2::runSql("update es_goodsOption set optionValue1 = optionValue2 where goodsNo = {$goodsNo}");
            DBUtil2::runSql("update es_goodsOption set optionValue2 = '' where goodsNo = {$goodsNo}");
        }*/

        gd_debug("완료?");
        exit();
    }



    /**
     * 한국타이어 리서치
     * @throws \Exception
     */
    public function sendHkResearch(){
        //$list = DBUtil2::runSelect("select * from zzz_tmpResearch240807");

        $list = [
            ['name'=>'송준호', 'phone'=>'01081099599']
        ];
        foreach($list as $each){
            $param['mallNm'] = '한국타이어B2B';
            $param['shopUrl'] = 'http://hankookb2b.co.kr';
            $param['surveyUrl'] = 'https://naver.me/FINMQf7H';
            $param['btnUrl'] = 'https://naver.me/FINMQf7H';
            $param['orderName'] = $each['name'];
            $param['company'] = '티스테이션';
            //gd_debug($param['orderName'] .  ' ' . $each['phone'] );
            gd_debug($param);
            SlKakaoUtil::send(23 , $each['phone'] ,  $param);
        }
        /*foreach($list as $each){
            $param['mallNm'] = '한국타이어B2B';
            $param['shopUrl'] = 'http://hankookb2b.co.kr';
            $param['surveyUrl'] = ' https://forms.gle/cKXeKD9EvwMQLyZb6 ';
            $param['btnUrl'] = 'https://forms.gle/cKXeKD9EvwMQLyZb6';
            $param['orderName'] = $each['name'];
            gd_debug($param['orderName'] .  ' ' . $each['phone'] );
            SlKakaoUtil::send(9 , $each['phone'] ,  $param);
        }*/

        //$param['mallNm'] = '한국타이어B2B';
        //$param['shopUrl'] = 'http://hankookb2b.co.kr';
        /*        $param['surveyUrl'] = ' https://forms.gle/6bUUnmcs69V6Sap76 ';
                $param['btnUrl'] = 'https://forms.gle/6bUUnmcs69V6Sap76';
                $param['orderName'] = '송준호';
                //SlKakaoUtil::send(7 , '01033073001' ,  $param);
                SlKakaoUtil::send(7 , '01081099599' ,  $param);
                gd_debug($param);*/

    }

    public function getImsDBCode(){
        $each = ['sno' => 68];
        //기성 일단 제외.
        $sql = "select 
                    distinct concat(c.codeValueKr,' ',b.codeValueKr,' ',a.addStyleCode) as productName,
                    a.prdSeason  
                from sl_imsProjectProduct a 
                    left outer join sl_imsCode b on a.prdStyle = b.codeValueEn and '스타일' = b.codeType
                    left outer join sl_imsCode c on a.prdSeason = c.codeValueEn and '시즌' = c.codeType
                    join sl_imsProject prj on a.projectSno = prj.sno 
                where a.customerSno={$each['sno']} 
                  and a.delFl='n'
                  and prj.projectType in ( 0, 2, 6, 8, 5, 1  ) 
                order by productName ";

        //기성복 , 추가, AS 는 일단 제외
        gd_debug($sql);
        $prdInfo = DBUtil2::runSelect($sql);
        gd_debug($prdInfo);
    }

    public function setImsStatus(){
        gd_debug('상태 변경 확인');
        $imsService = SlLoader::cLoad('ims', 'imsService');
        $imsService->setSyncStatus(857, __METHOD__);
    }

    public function sendMailTest(){

        $mailData['subject'] = '테스트';
        $mailData['from'] = 'innover@msinnover.com';
        $mailData['to'] = 'bluesky1478@hanmail.net';
        $mailUtil = SlLoader::cLoad('mail','SiteLabMailUtil','sl');

        //$mailData['body'] = 'TEST';
        $replace['rc_companyName'] = 'MAX';
        $replace['rc_confirmFullUrl'] = 'http://bcloud1478.godomall.com/ics/customer_estimate.php?key=vEft2kidZcBW1qmndYLZ7A&receiver=%EC%86%A1%EC%A4%80%ED%98%B8%20%EC%B0%A8%EC%9E%A5';
        $replace['rc_confirmUrl'] = '견적서 확인';

        $mailData['body'] = $mailUtil->getMailTemplate($replace,'work_estimate.php');

        $rslt = $mailUtil->send($mailData['subject'] , $mailData['body'], $mailData['from'], $mailData['to']);
        gd_debug($rslt);
    }
    public function setTest(){
        gd_debug('테스트');
    }

    public function sendKakaoTest(){
        $param['phone'] = '01081099599';
        $param['managerName'] = '한동경';
        $param['requesterName'] = '송준호';
        $param['approvalManagerName'] = '문상범';
        $param['customerName'] = '홍길동';
        $param['procName'] = '아르민';
        $param['subject'] = '오티스 기획 결재요청건';
        $param['projectNo'] = '240101';
        $param['shopUrl'] = 'http://gdadmin.innoverb2b.com/';
        $param['deadLine'] = '10/21';
        SlKakaoUtil::send(50196 , $param['phone'] ,  $param);
    }

    public function fodModify(){
        $name = 'OTIS_FOD 기모바지(24 수량파악)';
        /*16076 => 20, //FOD 조정희(T05936)
        16077 => 20, //FOD 조정희(T02970)
        16044 => 5, //FOD 정숙희(T05978)
        16090 => 5, //FOD T05746	오지용
        16089 => 4, //FOD T06090	김선애
        16083 => 3, //FOD T06278	나현봉*/

        $optionMap = [
            5919 => 5912,
            5920 => 5913,
            5921 => 5914,
            5922 => 5915,
            5923 => 5916,
            5924 => 5917,
            5925 => 5918,
        ];

        $orderNoList = [];
        $memNoList = [];
        $sql = "select * from es_orderGoods a join es_order b on a.orderNo = b.orderNo where goodsNo = 1000000416 and b.memNo not in(16076,16077,16044,16090,16089,16083) and a.sno in (79694,79695,79696,79697) and a.orderNo =  2406141547410491";
        $list = DBUtil2::runSelect($sql);
        foreach($list as $each){
            $memNoList[]=$each['memNo'];
            $updateData['goodsNm'] = $name;
            $updateData['goodsNmStandard'] = $name;
            $updateData['optionSno'] = $optionMap[$each['optionSno']];
            $updateData['goodsNo'] = '1000000414';
            DBUtil2::update(DB_ORDER_GOODS, $updateData, new SearchVo('sno=?',$each['sno']));
            //gd_debug($each['sno']);
            $orderNoList[$each['orderNo']] = true;
        }

        gd_debug( '업데이트한 고객' );
        gd_debug( implode(',', $memNoList) );

        /*        //주문정보 업데이트
                foreach($orderNoList as $orderNo){
                    $orderSearch = new SearchVo('orderNo=?', $orderNo);
                    $orderData = DBUtil2::getOneBySearchVo(DB_ORDER, $orderSearch);
                    $goodsNm = str_replace('협력사_','', $orderData['orderGoodsNm']);
                    DBUtil2::getOne(DB_ORDER, [
                        'orderGoodsNm' => $goodsNm,
                        'orderGoodsNmStandard' => $goodsNm
                    ], $orderSearch);
                }*/

    }

    public function costCompleteCheck(){
        //대상. 확정이된 스타일의 견적 , 주테이블: imsEstimate and cost  styleSno
        //Loop : style단위로. estimateConfirmSno
        $cnt = 0 ;
        $prdList = DBUtil2::getListBySearchVo(ImsDBName::PRODUCT, new SearchVo('prdCostConfirmSno>?', '0'));
        foreach($prdList as $each){
            $rslt = DBUtil2::update(ImsDBName::ESTIMATE, ['reqStatus'=>'6'],new SearchVo(" reqStatus=3 and sno <> {$each['prdCostConfirmSno']} and styleSno=?", $each['sno']));
            if($rslt>0){
                gd_debug('업데이트 스타일 : ' . $each['sno']);
                $cnt++;
            }
        }
        gd_debug('처리결과 : ' . $cnt);
    }

    public function setBtData(){
        //방법이 ?
        $list = DBUtil2::getList(ImsDBName::PROJECT, ' projectType in (0,6,2,5)  and 1', '1');
        foreach($list as $each){
            if( !empty($each['customerOrderDeadLine']) && '0000-00-00' != $each['customerOrderDeadLine'] ){
                $addData = DBUtil2::getOne(ImsDBName::PROJECT_ADD_INFO, " fieldDiv = 'qb' and projectSno", $each['sno']);
                if(!empty($addData)) {
                    if ('0000-00-00' == $addData['expectedDt']) {
                        DBUtil2::update(ImsDBName::PROJECT_ADD_INFO , ['expectedDt'=>$each['customerOrderDeadLine']], new SearchVo('sno=?',$addData['sno']));
                    }
                }else{
                    DBUtil2::insert(ImsDBName::PROJECT_ADD_INFO, [
                        'fieldDiv' => 'qb',
                        'projectSno' => $each['sno'],
                        'expectedDt' => $each['customerOrderDeadLine'],
                    ]);
                }
            }
        }
    }


    public function insertApprovalLine1(){
        $approval = [
            [
            'sno' => 32,
            'name' => '문상범',
            ],
        ];
        $refManagers = [];

        $saveData = [
            'subject' => '(공통) 기획서 결재',
            'approvalType' => 'plan',
            'appManagers' => json_encode($approval),
            'refManagers' => json_encode($refManagers),
            'regManagerSno' => 0, //System. 공통.
        ];
        $sno = DBUtil2::insert(ImsDBName::APPROVAL_LINE, $saveData);
        gd_debug('저장번호 : ' . $sno);

    }
    public function insertApprovalLine2(){
        $approval = [
            [
                'sno' => 32,
                'name' => '문상범',
            ],
        ];
        $refManagers = [];

        $saveData = [
            'subject' => '(공통) 제안서 결재',
            'approvalType' => 'proposal',
            'appManagers' => json_encode($approval),
            'refManagers' => json_encode($refManagers),
            'regManagerSno' => 0, //System. 공통.
        ];
        $sno = DBUtil2::insert(ImsDBName::APPROVAL_LINE, $saveData);
        gd_debug('저장번호 : ' . $sno);

    }
    public function insertApprovalLine3(){
        $approval = [
            [
                'sno' => 32,
                'name' => '문상범',
            ],
        ];
        $refManagers = [];

        $saveData = [
            'subject' => '(공통) 판매가 결재',
            'approvalType' => 'salePrice',
            'appManagers' => json_encode($approval),
            'refManagers' => json_encode($refManagers),
            'regManagerSno' => 0, //System. 공통.
        ];
        $sno = DBUtil2::insert(ImsDBName::APPROVAL_LINE, $saveData);
        gd_debug('저장번호 : ' . $sno);
    }

    public function IMS상품순서정렬(){
        $list = DBUtil2::getList(ImsDBName::PROJECT, '1', '1');
        $cnt = 0;
        foreach( $list as $key => $each ){
            $prdList = DBUtil2::getList(ImsDBName::PRODUCT, " delFl = 'n' and projectSno", $each['sno'], 'regDt desc' );
            if( empty($prdList[0]['sort']) ){
                foreach( $prdList as $prdKey => $prd ){
                    DBUtil2::update(ImsDBName::PRODUCT, ['sort'=>$prdKey+1], new SearchVo('sno=?', $prd['sno']));
                    $cnt++;
                }
            }
        }

        gd_debug('처리완료 : ' . $cnt);
    }

    /**
     * 혼다 리서치
     * @throws \Exception
     */
    public function sendHondaResearch(){
        $list = DBUtil2::runSelect("select * from zzz_tmpResearch240807");
        foreach($list as $each){
            $param['surveyUrl'] = ' https://forms.gle/6bUUnmcs69V6Sap76 ';
            $param['btnUrl'] = 'https://forms.gle/6bUUnmcs69V6Sap76';
            $param['orderName'] = $each['name'];
            gd_debug($param['orderName'] .  ' ' . $each['phone'] );
            SlKakaoUtil::send(7 , $each['phone'] ,  $param);
        }
    }

    public function factoryMailTest(){
        $subject = '이노버 / 한전산업개발 당진 / 일반나염인쇄후바로출고';
        $replace['completeDt'] = date('Y-m-d');
        $replace['orderPrintUrl'] = URI_HOME.'download/order_print.php';
        $body = SiteLabMailUtil::getMailTemplateStatic($replace,'factory_order2.php');
        $from = 'innover@msinnover.com';
        //$to = 'bluesky1478@hanmail.net';
        $to = 'jhsong@msinnover.com';
        $rslt = SiteLabMailUtil::send($subject, $body, $from, $to);
        gd_debug($rslt);

    }

    public function setImsMerge($list){
        $imsService = SlLoader::cLoad('ims', 'imsService');
        $standard = 0;
        foreach($list as $key => $prjSno){
            if(0 == $key){
                $standard = $prjSno;
            }else{
                DBUtil2::update(ImsDBName::PRODUCT,['projectSno'=>$standard], new SearchVo('projectSno=?',$prjSno));
                DBUtil2::update(ImsDBName::PROJECT_COMMENT,['projectSno'=>$standard], new SearchVo('projectSno=?',$prjSno));
                DBUtil2::update(ImsDBName::PROJECT_FILE,['projectSno'=>$standard], new SearchVo('projectSno=?',$prjSno));
                DBUtil2::update(ImsDBName::PREPARED,['projectSno'=>$standard], new SearchVo('projectSno=?',$prjSno));
                $imsService->deleteProject([
                    'sno'=>$prjSno
                ]);
            }
        }
        gd_debug('병합종료');
    }


    public function setImsEsimateCost(){
        $list = DBUtil2::getListBySearchVo('sl_imsPrepared', new SearchVo([
            'preparedType=?',
            'preparedStatus=?',
        ],[
            'estimate',
            '4',
        ]), false);

        gd_debug(count($list));

        foreach($list as $each){
            //gd_debug($each['contents']);
            $obj = json_decode($each['contents'],true);
            if(!empty($obj)){
                foreach( $obj['productList'] as $product ){
                    if($product['prdCost'] > 0){
                        $styleCode = $product['styleCode'];
                        $orgPrd = DBUtil2::getOneBySearchVo(ImsDBName::PRODUCT, new SearchVo([
                            'projectSno=?',
                            'styleCode=?',
                        ],[
                            $product['projectSno'],
                            $styleCode,
                        ]));
                        //"styleCode='{$styleCode}' and projectSno=?", $product['projectSno']
                        //gd_debug($styleCode);
                        //gd_debug($product['projectSno']);
                        //gd_debug($orgPrd);

                        $updateRslt = DBUtil2::update(ImsDBName::PRODUCT, [
                            'fabric'=>json_encode($product['fabric']),
                            'subFabric'=>json_encode($product['subFabric']),
                            'prdCost'=>$product['prdCost'],
                            'prdCostStatus'=>1,
                        ], new SearchVo('sno=?',$orgPrd['sno']));

                        gd_debug($product['projectSno'] . ' ==> ' . $product['styleCode'] . ' : ' . $updateRslt);

                        /*gd_debug($product['styleCode']);
                        gd_debug($product['fabric']);
                        gd_debug($product['subFabric']);
                        gd_debug($product['projectSno']);*/
                    }
                }

            }
        }
    }


    //TKE 101-222... 전화번호 잘 못 등록되었을 때 정제 처리
    public function refineMemberCellPhone(){
        $orderService = SlLoader::cLoad('Order','OrderService');
        $sql = "select * from es_orderInfo where orderCellPhone like '10%' and regDt >'20230801'";
        gd_debug($sql);
        $list = DBUtil2::runSelect($sql);
        foreach($list as $each){
            $newPhone = SlCommonUtil::getCellPhoneFormat('0'.str_replace('-','',$each['orderCellPhone']));
            DBUtil2::update(DB_ORDER_INFO, ['orderCellPhone'=>$newPhone], new SearchVo('orderNo=?',$each['orderNo']));
        }

        $sql = "select * from es_orderInfo where receiverCellPhone like '10%' and regDt >'20230801'";
        gd_debug($sql);
        $list = DBUtil2::runSelect($sql);
        foreach($list as $each){
            $newPhone = SlCommonUtil::getCellPhoneFormat('0'.str_replace('-','',$each['receiverCellPhone']));
            DBUtil2::update(DB_ORDER_INFO, ['receiverCellPhone'=>$newPhone], new SearchVo('orderNo=?',$each['orderNo']));
        }
    }

    public function imsPreparedMig(){
        //Prepared List...
        DBUtil2::runSql("update sl_imsProject set btStatus = null, costStatus=null, estimateStatus=null, workStatus=null, orderStatus=null ");
        $list = DBUtil::getList(ImsDBName::PREPARED, '1', '1');
        foreach($list as $each){
            /*gd_debug($each['projectSno']);
            if( 79 == $each['projectSno'] || 63 == $each['projectSno'] ){
                gd_debug($each['projectSno']);
                gd_debug($each['preparedStatus']);
            }*/
            DBUtil2::update(ImsDBName::PROJECT,[
                $each['preparedType'].'Status' => $each['preparedStatus']
            ], new SearchVo('sno=?', $each['projectSno']));
        }

        $list2 = DBUtil2::getList(ImsDBName::PROJECT,'projectStatus','90');
        foreach($list2 as $each){

            DBUtil2::update(ImsDBName::PRODUCE, ['produceStatus'=>99], new SearchVo('projectSno=?', $each['sno']));
        }

    }

    public function test(){
        return ImsSendMessage::imsMessageReplacer(ImsSendMessage::PREPARED_COMMON, [
            'title' => 'BT/퀄리티',
            'company' => '린나이',
            'projectNo' => '1234',
            'productName' => '카라티 외 2건',
        ]);
    }


    public function setTkeMember(){
        //$rslt = DBUtil2::runSql("delete from es_member where ex1='TKE(티센크루프)' and groupSno=1 and memNo not in (4991,5469,4746,5639,5000,1)"); //정직원만 삭제.
        //gd_debug('회원 삭제 : ' . $rslt);
        $tkeService = SlLoader::cLoad('scm','scmTkeService');
        $tkeService->saveMemberTke();
        gd_debug('TEST  Complete...');
    }

    public function fileProcTest(){
        //$data = Storage::disk('', 'local')->getRealPath();
        //$data = Storage::disk(8, 'local')->getBasePath();
        $data = Storage::disk(8, 'local')->getPath()->getBasePath();
        //$delFilePath = GoodsPolicy::getSizeStorage()->getPath()->getBasePath().$goodsInfo['sizeFilePath'];
        //SitelabLogger::logger( $delFilePath );
        $delPath='goods/test.png';
        SitelabLogger::logger($delPath);

        $rslt = GoodsPolicy::getSizeStorage()->delete($delPath);
        gd_debug($rslt);
    }

    public function cUrlTest(){
        /*$cookieFilePath = UserFilePath::data('etc')->getRealPath() . '/cookie/cookie.txt';
        $requestUrl = "http://erp.msinnover.com:8082/common/onLogIn.do";
        $response = CUrlUtil::requestJson($requestUrl, '{"url":"/common/onLogIn.do","method":"POST","userId":"jhsong9599","userPw":"dndbwnsgh00","contactIp":""}', $cookieFilePath);
        gd_debug($response);

        $requestUrl = "http://erp.msinnover.com:8082/mdm/cust/PickupMst/pop/getMstCustInfo.do?custId=CZ83382";
        $response = CUrlUtil::request($requestUrl, $cookieFilePath);
        gd_debug($response);
        gd_debug(json_decode($response, true));
        */

        //$url = "http://wms.korea-soft.com/syl/contentManager/outContent/releasedayContent_data.php?skey=2&s_serial=&s_reg_dtm_start=20230418&s_reg_dtm_end=20230418&s_order_start=&s_order_end=&s_total1=&s_total2=&s_order=1&s_gubun=&_search=false&nd=1681860204714&rows=100&page=20&sidx=K09SlipNo2+desc%2C+K09SLIPDTX&sord=desc";
        $url = "http://wms.korea-soft.com/syl/contentManager/outContent/joborderContent2_data.php?skey=2&s_reg_dtm_start=20230721&s_reg_dtm_end=20230721&s_order_start=&s_order_end=&s_total1=&s_total2=&s_total3=&s_procmode=&_search=false&nd=1689912581160&rows=100&page=1&sidx=K36SlipXNo+desc%2C+K36SlipXNo&sord=desc";
        $response = CUrlUtil::request($url);
        gd_debug(json_decode($response, true));
    }

    public function sendTkeResearch(){
        $list = DBUtil2::runSelect("select * from zzz_tmpResearch");

        foreach($list as $each){
            $param['companyName'] = 'TKE엘리베이터';
            $param['orderName'] = $each['name'];
            $param['year'] = '2022';
            $param['surveyUrl'] = 'https://forms.gle/EiLsyDsjPGKvKLor7';
            $param['btnUrl'] = 'https://forms.gle/EiLsyDsjPGKvKLor7';
            SlKakaoUtil::send(18 , $each['phone'] ,  $param);
        }
    }

    public function refineHkStockOpenGoodsOption(){
        /*select * from es_goodsOption where goodsNo in (
            1000000222
            , 1000000126
        ) order by optionCode, goodsNo;*/

        //select * from es_goodsOption where goodsNo in ( 1000000234 , 1000000229 ) order by optionCode, goodsNo
        $openGoodsNo = '1000000222';
        $targetGoodsNo = '1000000126';
        $manualService = SlLoader::cLoad('godo','manualService','sl');
        //$manualService->refineHkStockOpenGoodsOption($openGoodsNo, $targetGoodsNo);
        $manualService->refineHkStockOpenGoodsOption2($openGoodsNo, $targetGoodsNo, 7);

    }

    public function excelStock(){
        //3PL 코드별 판매 재고 현황

        $erpService = SlLoader::cLoad('erp','erpService');
        $data = $erpService->getStockCompareData();

        gd_debug($data);

        $title = [
            '고객사',
            '3PL코드',
            '3PL품목명',
            '관리재고수량',
            '판매상품번호',
            '판매상품명',
            '판매옵션',
            '판매재고수량',
            '노출상태',
            '판매상태',
        ];
        /*$excelBody = '';
        foreach ($data as $key => $val) {
            $fieldData = array();
            $fieldData[] = ExcelCsvUtil::wrapTd($val['thirdPartyProductCode']);
            $fieldData[] = ExcelCsvUtil::wrapTd($val['productName']);
            $fieldData[] = ExcelCsvUtil::wrapTd($val['optionName']);
            $fieldData[] = ExcelCsvUtil::wrapTd(number_format($val['stockCnt']));
            $fieldData[] = ExcelCsvUtil::wrapTd($val['scmName']);
            $excelBody .=  "<tr>". implode('',$fieldData) . "</tr>";
        }
        $date = date('Y-m-d');
        $simpleExcelComponent = SlLoader::cLoad('Excel','SimpleExcelComponent','sl');
        $simpleExcelComponent->simpleDownload('이노버_전체재고_'.$date,$title,$excelBody);*/
    }

    public function sendSms1(){

        //춘추
        $memberList = DBUtil2::getList('zzz_tmp2', '1', '1');
        //gd_debug($memberList);

        $success = 0;
        $fail = 0;

        foreach($memberList as $key => $member){
            if( !empty($member['phone']) ){
                $receiverData[0]['memNo'] = '0';
                $receiverData[0]['smsFl'] = 'y';
                $receiverData[0]['cellPhone'] = $member['phone'];
                $receiverData[0]['memNm'] = $member['name'];

                $orderName = $member['name'];
                $content = "
22년 춘추 리오더상품 만족도조사
안녕하세요 [{$orderName}] 님
★★ 경품 받으러 가기★★

간단한 설문조사하고
우리 매장 회식비 지원 받기!!

경품 받기 : https://forms.gle/4qGKoQhtj5oH7DpY8

여러분의 목소리를 들려주세요!
더욱 만족스러운 서비스를 위해 '구매 만족도 평가'를 실시하고 있습니다.
향후 서비스 개선을 위해 활용될 예정이니 많은 참여 부탁드립니다 :)

▷ [한국타이어B2B] 바로가기
[hankookb2b.co.kr]
고객센터
(070-4239-4380)";

                //gd_debug( $content );
                //gd_debug($member['memNm']. '-' .$member['cellPhone']);
                //gd_debug($content);
                $result = SlSmsUtil::sendSms($content, $receiverData, 'lms');
                $success += $result['success'];
                $fail += $result['fail'];

            }
            //if( $key == 10 ) break;
        }

        gd_debug("성공 : {$success}");
        gd_debug("실패 : {$fail}");
    }

    //개별 처리
    public function batchUnitOrderTest($param){
        $writeSw = false;
        // Transaction 필

        gd_debug('BATCH ORDER START');
        //기본적으로 받은 정보 주문자별 1개로 묶는다.
        $order = \App::load(\Component\Order\Order::class);
        $orderStatus = 'p1';
        //자동 완성
        $param['orderNo'] = $order->generateOrderNo();
        $param['orderStatus'] = $orderStatus;
        $param['orderTypeFl'] = 'write';
        $param['scmNo'] = 8;
        $param['settleKind'] = 'gz';
        $param['paymentDt'] = date('Y-m-d H:i:s');
        if( SlCommonUtil::isDev() ){
            $param['deliverySno'] = 2050;
        }else{
            $param['deliverySno'] = 32;
        }

        //Validation Check ...
        $deliveryInfo = DBUtil2::getOne('sl_setScmDeliveryList','subject',$param['deliveryName']);
        $memberInfo = DBUtil2::getOne(DB_MEMBER,'memId',$param['memId']);
        $goodsInfo = DBUtil2::getOne(DB_GOODS,'goodsNo',$param['goodsNo']);
        $goodsOption = DBUtil2::getOneBySearchVo(DB_GOODS_OPTION, new SearchVo(['goodsNo=?','optionValue1=?'],[$param['goodsNo'],$param['optionName']]));

        //Delivery
        $param = array_merge($param,SlCommonUtil::getAvailData($deliveryInfo,[
            'receiverZipcode','receiverZonecode','receiverAddress','receiverAddressSub'
        ]));
        $param['orderGoodsNm'] = $goodsInfo['goodsNm'];
        $param['orderGoodsNmStandard'] = $goodsInfo['goodsNm'];
        $param['orderGoodsCnt'] = 1;
        $param['memNo'] = $memberInfo['memNo'];
        $param['memNm'] = $memberInfo['memNm'];
        $param['orderCellPhone'] = $memberInfo['cellPhone'];
        $param['receiverCellPhone'] = $memberInfo['cellPhone'];


        //Goods
        $param = array_merge($param, SlCommonUtil::getAvailData($goodsInfo,[
            'goodsCd','goodsNm','cateCd','brandCd'
        ]));
        $param['goodsNmStandard'] = $goodsInfo['goodsNm'];
        $param['goodsCd'] = $goodsInfo['goodsCd'];

        $param['orderName'] = $memberInfo['memNm'];

        $param['optionSno'] = $goodsOption['sno'];
        //$gVal['option'] = explode('^|^',$goodsInfo['optionName']);
        $tmp[] = [
            $goodsInfo['optionName'],
            $goodsOption['optionValue1'],
            $goodsOption['optionCode'],
            $goodsOption['optionPrice'],
            null,
        ];
        $param['optionInfo'] = json_encode($tmp, JSON_UNESCAPED_UNICODE);
        unset($tmp);


        //Insert Into Order Delivery
        if( $writeSw ) $orderDeliverySno = DBUtil2::insert(DB_ORDER_DELIVERY, $param);
        $param['orderDeliverySno'] = $orderDeliverySno;

        if( $writeSw ){
            DBUtil2::insert(DB_ORDER, $param);
            $orderGoodsSno = DBUtil2::insert(DB_ORDER_GOODS, $param);
            gd_debug("ES_ORDER_GOODS : {$orderGoodsSno}");
            $orderInfoSno = DBUtil2::insert(DB_ORDER_INFO, $param);
            gd_debug("ES_ORDER_INFO : {$orderInfoSno}");
            //승인여부
            $orderService = SlLoader::cLoad('Order','OrderService');
            $orderService->saveOrderAcct($param);
            //재고차감
            $order->setGoodsStockCutback($param['orderNo'], [$orderGoodsSno]);
        }

        gd_debug( $param );
    }

    /**
     * 514명 일괄 전송 - 21/11/26
     * @throws \Exception
     */
    public function sendKakaoVote(){
        $orderNoList = DBUtil2::getList('zzz_tmpOrder', '1' , '1');
        foreach($orderNoList as $orderNo){
            $orderInfo = DBUtil2::getOne('es_orderInfo', 'orderNo', $orderNo['orderNo']);
            $param['orderNo'] = $orderInfo['orderNo'];
            $param['orderName'] = $orderInfo['orderName'];
            $param['surveyUrl'] = 'https://forms.gle/J7kwcEwpkNossWtb7';
            SlKakaoUtil::send(10 , $orderInfo['orderCellPhone'] ,  $param);
            //gd_debug($orderNo['orderNo']. ' = ' . $orderInfo['orderName']);
        }
    }

    /**
     * 설문조사
     */
    public function sendSurveySms(){
        $memberList = DBUtil2::getList(DB_MEMBER, 'ex1', '한국타이어');

        $success = 0;
        $fail = 0;

        foreach($memberList as $key => $member){
            if( !empty($member['cellPhone']) ){
                $receiverData[0]['memNo'] = '0';
                $receiverData[0]['smsFl'] = 'y';
                $receiverData[0]['cellPhone'] = $member['cellPhone'];
                $receiverData[0]['memNm'] = $member['memNm'];

                $orderName = $member['memNm'];
                $content = "
안녕하세요. 한국타이어 [{$orderName}]님

이노버를 이용해주셔서 감사합니다.

고객님의 소중한 의견을 취합해 더욱 만족스러운 서비스를
드릴 수 있도록 선호도 평가에 참여 부탁드립니다. 
감사합니다 :)

참여하기
[http://s.godo.kr/14t5n]

▷ [한국타이어B2B] 바로가기
[hankookb2b.co.kr]
고객센터
(070-4239-4380)";
                //gd_debug($member['memNm']. '-' .$member['cellPhone']);
                //gd_debug($content);
                $result = SlSmsUtil::sendSms($content, $receiverData, 'lms');
                $success += $result['success'];
                $fail += $result['fail'];

            }
            //if( $key == 10 ) break;
        }

        gd_debug("성공 : {$success}");
        gd_debug("실패 : {$fail}");

        //gd_debug(count($memberList));
    }

    public function encryptTest(){
        $plainText = '테스트';
        $enc = SlCommonUtil::aesEncrypt($plainText);
        gd_debug($enc);
        $dec = SlCommonUtil::aesDecrypt($enc);
        gd_debug($dec);
    }

    public function getMailTemplate($replace, $templateFile='safe_stock_alarm.php'){
        $filePath = UserFilePath::data('mail', 'etc', $templateFile)->getRealPath();
        // 화일의 내용을 읽어오기 (화일 설명과 화일명 추출)
        $fd = fopen($filePath, 'r');
        $contents = fread($fd, 9999999);
        fclose($fd);
        foreach($replace as $key => $value){
            $contents = str_replace('{'.$key.'}',$value,$contents);
        }
        return $contents;
    }

    public function getOptionTitle(){
        $optionTitle = '
            <th colspan="2" style="padding: 3px 3px 3px 10px; color: rgb(0, 0, 0); border-bottom-color: rgb(187, 197, 206); border-bottom-width: 1px; border-bottom-style: solid; background-color: rgb(244, 244, 244);text-align: center">
                90
            </th>
            <th colspan="2" style="padding: 3px 3px 3px 10px; color: rgb(0, 0, 0); border-bottom-color: rgb(187, 197, 206); border-bottom-width: 1px; border-bottom-style: solid; background-color: rgb(244, 244, 244);text-align: center">
                95
            </th>
            <th colspan="2" style="padding: 3px 3px 3px 10px; color: rgb(0, 0, 0); border-bottom-color: rgb(187, 197, 206); border-bottom-width: 1px; border-bottom-style: solid; background-color: rgb(244, 244, 244);text-align: center">
                100
            </th>
            <th colspan="2" style="padding: 3px 3px 3px 10px; color: rgb(0, 0, 0); border-bottom-color: rgb(187, 197, 206); border-bottom-width: 1px; border-bottom-style: solid; background-color: rgb(244, 244, 244);text-align: center">
                105
            </th>
            <th colspan="2" style="padding: 3px 3px 3px 10px; color: rgb(0, 0, 0); border-bottom-color: rgb(187, 197, 206); border-bottom-width: 1px; border-bottom-style: solid; background-color: rgb(244, 244, 244);text-align: center">
                110
            </th>
            <th colspan="2" style="padding: 3px 3px 3px 10px; color: rgb(0, 0, 0); border-bottom-color: rgb(187, 197, 206); border-bottom-width: 1px; border-bottom-style: solid; background-color: rgb(244, 244, 244);text-align: center">
                115
            </th>        
        ';
        return $optionTitle;
    }

    public function getOptionData(){
        $optionData = '
        
        <td style="width: 80%; height: 43px; color: rgb(51, 51, 51); padding-left: 20px; font-size: 13px; border-bottom-color: rgb(229, 229, 229); border-bottom-width: 1px; border-bottom-style: solid;text-align: center">50</td>
        <td style="width: 80%; height: 43px; color: rgb(51, 51, 51); padding-left: 20px; font-size: 13px; border-bottom-color: rgb(229, 229, 229); border-bottom-width: 1px; border-bottom-style: solid;text-align: center"><span style="color:#CC1717">37</span></td>
        <td style="width: 80%; height: 43px; color: rgb(51, 51, 51); padding-left: 20px; font-size: 13px; border-bottom-color: rgb(229, 229, 229); border-bottom-width: 1px; border-bottom-style: solid;text-align: center">50</td>
        <td style="width: 80%; height: 43px; color: rgb(51, 51, 51); padding-left: 20px; font-size: 13px; border-bottom-color: rgb(229, 229, 229); border-bottom-width: 1px; border-bottom-style: solid;text-align: center">100</td>
        <td style="width: 80%; height: 43px; color: rgb(51, 51, 51); padding-left: 20px; font-size: 13px; border-bottom-color: rgb(229, 229, 229); border-bottom-width: 1px; border-bottom-style: solid;text-align: center">50</td>
        <td style="width: 80%; height: 43px; color: rgb(51, 51, 51); padding-left: 20px; font-size: 13px; border-bottom-color: rgb(229, 229, 229); border-bottom-width: 1px; border-bottom-style: solid;text-align: center">100</td>
        <td style="width: 80%; height: 43px; color: rgb(51, 51, 51); padding-left: 20px; font-size: 13px; border-bottom-color: rgb(229, 229, 229); border-bottom-width: 1px; border-bottom-style: solid;text-align: center">50</td>
        <td style="width: 80%; height: 43px; color: rgb(51, 51, 51); padding-left: 20px; font-size: 13px; border-bottom-color: rgb(229, 229, 229); border-bottom-width: 1px; border-bottom-style: solid;text-align: center">100</td>
        <td style="width: 80%; height: 43px; color: rgb(51, 51, 51); padding-left: 20px; font-size: 13px; border-bottom-color: rgb(229, 229, 229); border-bottom-width: 1px; border-bottom-style: solid;text-align: center">50</td>
        <td style="width: 80%; height: 43px; color: rgb(51, 51, 51); padding-left: 20px; font-size: 13px; border-bottom-color: rgb(229, 229, 229); border-bottom-width: 1px; border-bottom-style: solid;text-align: center">100</td>
        <td style="width: 80%; height: 43px; color: rgb(51, 51, 51); padding-left: 20px; font-size: 13px; border-bottom-color: rgb(229, 229, 229); border-bottom-width: 1px; border-bottom-style: solid;text-align: center">50</td>
        <td style="width: 80%; height: 43px; color: rgb(51, 51, 51); padding-left: 20px; font-size: 13px; border-bottom-color: rgb(229, 229, 229); border-bottom-width: 1px; border-bottom-style: solid;text-align: center">100</td>
        <td style="width: 80%; height: 43px; color: rgb(51, 51, 51); padding-left: 20px; font-size: 13px; border-bottom-color: rgb(229, 229, 229); border-bottom-width: 1px; border-bottom-style: solid;text-align: center">50</td>
        ';
        return $optionData;
    }

    public function mailSendTest(){

        gd_debug('메일 발송 테스트');
        $defaultInfo = gd_policy('basic.info');
        $mailUtil = SlLoader::cLoad('mail','SiteLabMailUtil','sl');
        //$mailData = $this->getMailData($orderFactoryNo);
        $mailData['mailSubject'] = 'OOOO님 상품 OOOO의 안전재고 수량이 부족합니다. 추가 생산여부 검토 바랍니다.(1)';
        $mailData['mailContent'] = '컨텐츠 테스트';

        $subject = $mailData['mailSubject'];

        $replace['mailContent'] = $mailData['mailContent'];
        $replace['optionTitle'] = $this->getOptionTitle();
        $replace['optionData'] = $this->getOptionData();

        $body = $this->getMailTemplate($replace,'safe_stock_alarm.php');

        $from = $defaultInfo['email'];
        //$to = 'bluesky1478@hanmail.net';
        $to = 'x1478@naver.com';
        $mailUtil->send($subject, $body, $from, $to);
        //$rslt = $mailUtil->send($subject, $body, $from, $to);
        //gd_debug($rslt);
    }

    /**
     * 설문 보내기
     */
    public function sendSurveyLink(){
        //1. 주문별 '배송중(d1)이면서' 가장 빠른 정책이 있다면 해당 정책으로 링크 발송
    }

    public function getRealTaxRate($supplyPrice, $vatPrice, $freePrice)
    {
        $realTaxRate = [
            'supply' => 0,
            'vat' => 0,
            'free' => 0,
        ];
        $totalPrice = $supplyPrice + $vatPrice + $freePrice;

        if((float)$totalPrice > 0) {
            $realTaxRate['supply'] = NumberUtils::getNumberFigure(($supplyPrice / $totalPrice) * 100, '0.001', 'round');
            $realTaxRate['vat'] = NumberUtils::getNumberFigure(($vatPrice / $totalPrice) * 100, '0.001', 'round');
            $realTaxRate['free'] = NumberUtils::getNumberFigure(($freePrice / $totalPrice) * 100, '0.001', 'round');

            list($realTaxRate['supply'], $realTaxRate['vat'], $realTaxRate['free']) = $this->getRealTaxBalance(100, $realTaxRate['supply'], $realTaxRate['vat'], $realTaxRate['free']);

            $realTaxRate['supply'] = $realTaxRate['supply'] / 100;
            $realTaxRate['vat'] = $realTaxRate['vat'] / 100;
            $realTaxRate['free'] = $realTaxRate['free'] / 100;
        }

        return $realTaxRate;
    }

    public function getRealTaxBalance($standardPrice, $supplyPrice, $vatPrice, $freePrice)
    {
        if($standardPrice !== ($supplyPrice+$vatPrice+$freePrice)){
            if($vatPrice > 0){
                $supplyPrice = $standardPrice - $vatPrice - $freePrice;
            }
            else if($freePrice > 0){
                $freePrice = $standardPrice - $supplyPrice - $vatPrice;
            }
            else if($supplyPrice > 0){
                $supplyPrice = $standardPrice - $vatPrice - $freePrice;
            }
        }

        return [$supplyPrice, $vatPrice, $freePrice];
    }

    public function taxTest(){
        $orderGoods['taxSupplyGoodsPrice'] = 0;
        $orderGoods['taxVatGoodsPrice'] = 0;
        $orderGoods['taxFreeGoodsPrice'] = 0;

        $refundComplexTax['taxSupply'] = 0;
        $refundComplexTax['taxVat'] = 0;
        $refundComplexTax['taxFree'] = 0;

        $goodsTaxablePrice = $orderGoods['taxSupplyGoodsPrice'] + $orderGoods['taxVatGoodsPrice'] + $orderGoods['taxFreeGoodsPrice'];
        // 전체 복합과세 = 상품 복합과세 금액 + 추가상품 복합과세 금액
        $totalTaxablePrice = $goodsTaxablePrice;
        $realTaxSupplyGoodsPrice = 0;
        $realTaxVatGoodsPrice = 0;
        $realTaxFreeGoodsPrice = 0;
        //$tmpGoodsTaxPrice = NumberUtils::taxAll($goodsTaxablePrice, $gVal['goodsTaxInfo'][1], $gVal['goodsTaxInfo'][0]);
        $gVal=[];
        $tmpGoodsTaxPrice=[];
        if ($gVal['goodsTaxInfo'][0] == 't') {
            $realTaxSupplyGoodsPrice = gd_isset($tmpGoodsTaxPrice['supply'], 0);
            $realTaxVatGoodsPrice = gd_isset($tmpGoodsTaxPrice['tax'], 0);
        } else {
            $realTaxFreeGoodsPrice = gd_isset($tmpGoodsTaxPrice['supply'], 0);
        }
        unset($tmpGoodsTaxPrice);

        // 실제 환불된 복합과세 금액 중 상품 계산 (환불수수수료 안분 후)
        $refundComplexTax['taxSupply'] += $realTaxSupplyGoodsPrice;
        $refundComplexTax['taxVat'] += $realTaxVatGoodsPrice;
        $refundComplexTax['taxFree'] += $realTaxFreeGoodsPrice;

        // 주문상품의 남아있는 복합과세금액 업데이트
        $tmpGoodsTaxablePrice = [];
        //$tmpGoodsTaxablePrice = NumberUtils::taxAll($tmpOrderGoodsRefundCharge['goods'], $gVal['goodsTaxInfo'][1], $gVal['goodsTaxInfo'][0]);
        if ($gVal['goodsTaxInfo'][0] == 't') {
            $orderGoodsData['realTaxSupplyGoodsPrice'] = gd_isset($tmpGoodsTaxablePrice['supply'], 0);
            $orderGoodsData['realTaxVatGoodsPrice'] = gd_isset($tmpGoodsTaxablePrice['tax'], 0);
            $orderGoodsData['realTaxFreeGoodsPrice'] = 0;
        } else {
            $orderGoodsData['realTaxSupplyGoodsPrice'] = 0;
            $orderGoodsData['realTaxVatGoodsPrice'] = 0;
            $orderGoodsData['realTaxFreeGoodsPrice'] = gd_isset($tmpGoodsTaxablePrice['supply'], 0);
        }
        $compareField = array_keys($orderGoodsData);
    }

    public function smsTest(){
        //문자보내기!
        //사이트랩 SMS

        /*
        $contentParam['writerNm'] = '테스터';
        $content = SlSmsUtil::getSmsMsg(0,$contentParam);
        $result = SlSmsUtil::sendSmsToMember(1,$content);
        gd_debug("SmsResult ▼ ");
        gd_debug($result);
        */

        $orderNo = '2012051053220665';
        //SMS 발송
        $orderInfo = DBUtil::getOne(DB_ORDER_INFO, 'orderNo', $orderNo);
        //$memberList = DBUtil::getList(DB_MEMBER,'memNo',1);

        $receiverData[0]['memNo'] = '0';
        $receiverData[0]['memNm'] = $orderInfo['orderName'];
        $receiverData[0]['smsFl'] = 'y';
        $receiverData[0]['cellPhone'] = $orderInfo['orderCellPhone'];
        $content = SlSmsUtil::getSmsMsg(1, $orderInfo);;

        //예약 문자 보내기
        //$smsSendDate = date('Y-m-d H:i:s', strtotime('2020-09-07 16:25:00') );
        //$result = SlSmsUtil::sendSms($content, $memberList, 'sms', 'res_send', $smsSendDate);

        $result = SlSmsUtil::sendSms($content, $receiverData, 'sms');

        gd_debug($result);
        gd_debug('SMS TEST 종료');
    }

    public function testStockList(){
        $stockListService = SlLoader::cLoad('Stock','StockListService');
        /*goodsNo: goodsNo,
                optionNo: optionNo,
                startDate : '<?=$search['searchDate'][0]?>',
                endDate : '<?=$search['searchDate'][1]?>'*/
        $searchData['goodsNo'] = '1000002215';
        $searchData['optionNo'] = '1';
        $searchData['startDate'] = '2020-09-05';
        $searchData['endDate'] = '2020-09-06';

        return $stockListService->getStatToList($searchData);
    }

    public function testFreePolicyCount(){
        $goodsPolicyService = SlLoader::cLoad('Goods','GoodsPolicy');
        return $goodsPolicyService->getFreeCount('1000002215','1');
    }

    //상품 재고 테스트
    public function testStock(){
        $service = \App::load(\Component\Goods\GoodsStock::class);
        $sql = \App::load(\Component\Goods\Sql\GoodsStockSql::class);
        //goodsNm ,
        $goodsNo = '1000002215';
        //$goodsNo = '1000000346';
        $result = $service->getGoodsOptionInfoAndStockCheck($goodsNo);

        gd_debug($result);
        //es_goodsOption
        //goodsNo,
        /*
        CREATE TABLE sl_goodsStock (
	 sno INT(10) NOT NULL AUTO_INCREMENT COMMENT '일련번호'
	,  goodsNo INT(10) NOT NULL COMMENT '상품번호'
	,  optionSno INT(10) NOT NULL COMMENT '상품옵션번호'
	,  memNo INT(10) NULL COMMENT '회원번호'
	,  orderNo VARCHAR(16) NULL COMMENT '주문번호'
	,  orderGoodsSno INT(10) NULL COMMENT '주문상품번호'
	,  stockType VARCHAR(20) NOT NULL COMMENT '재고유형'
	,  stockReason VARCHAR(20) NOT NULL COMMENT '재고사유'
	,  stockCnt INT(10) NOT NULL COMMENT '재고수량'
	,  regDt DATETIME NOT NULL COMMENT '등록시간'
	,  modDt DATETIME NOT NULL COMMENT '수정시간'
	, PRIMARY KEY (sno)
) ENGINE = InnoDB COMMENT = '주문상품 재고 내역 ';
         */
        //DBUtil::
    }

    //사이트랩 코드 사용방법
    public function getCodeUsed(){
        $result = SlCode::getCodeMap([SlCode::STOCK_REASON,SlCode::STOCK_TYPE,SlCode::USE_FL]);
        gd_debug($result);
        //gd_debug(SlCode::USE_FL);
        gd_debug(gd_code(Deposit::REASON_CODE_GROUP));
    }

    public function getFreePolicy(){
        $result = DBUtil::getList('sl_goodsPolicyMember',['goodsNo','memNo'],['1000002216','1']);
        return $result;
    }

    public function goodsPolicMemberCountTest(){
        $sql = \App::load(\Component\Goods\Sql\GoodsPolicySql::class);
        $searchGoodsNo = array();
        $searchGoodsNo[] = 1000002216;
        $searchGoodsNo[] = 1000002205;
        $searchGoodsNo[] = 1000002202;
        $testList = $sql->getGoodsPolicyMemberCount($searchGoodsNo);
        gd_debug($testList);
    }

    public function goodsPolicInfoTest(){
        $sql = \App::load(\Component\Goods\Sql\GoodsPolicySql::class);
        $searchGoodsNo = array();
        $searchGoodsNo[] = 1000002216;
        $searchGoodsNo[] = 1000002205;
        $searchGoodsNo[] = 1000002202; //원래는 value , type 형태로 들어가야함
        $testList = $sql->getGoodsPolicyInfo($searchGoodsNo);
        gd_debug($testList);
    }

    public function updateInsert(){
        //gd_debug(DBUtil::insert('sl_policyFreeSale',$saveData));

        //$data = DBUtil::getList('sl_policyFreeSale','1','1');
        //gd_debug($data);

        //gd_debug(DBUtil::getOne('es_member','memNo','1'));
        //gd_debug(DBUtil::getListBySearchVo('es_member',new SearchVo('memNo=?','1')));
        //gd_debug(DBUtil::getListBySearchVo('es_member',new SearchVo('memNo=?','11111')));

        //$mergeData['goodsNo'] = '1000002216';
        //$mergeData['policyFreeSaleSno'] = '2';
        //DBUtil::merge('sl_goodsPolicy', $mergeData,new SearchVo('goodsNo=?','1000002216') );
        //gd_debug(DBUtil::getOne('sl_goodsPolicy','goodsNo','1000002216'));
    }

    public function updateJoinItemEx1Value($inputUpdateData){
        $joinItem = gd_policy('member.joinitem', 1);
        //$ex1ValueItemArray[] = $updateData;
        $joinItem['ex1']['value']=$inputUpdateData;
        $searchVo = new SearchVo();
        $searchVo->setWhereArray(['groupCode=?','code=?']);
        $searchVo->setWhereValueArray(['member','joinItem']);
        $updateData = array();
        $updateData['data'] = json_encode($joinItem, JSON_UNESCAPED_SLASHES);
        DBUtil::update('es_config',$updateData,$searchVo);
    }
    /**
     * DB사용방법
     * 그냥 대충 이렇게 쓴다... 정도 확인
     */
    public function dbUsage(){
        //판매 데이터 생성
        gd_debug("== DB USAGE TEST ==");
        //gd_debug(gd_policy('member.joinitem', 1));
        //gd_debug(gd_policy('member.joinitem', 1)['ex1']);

        $joinItem = gd_policy('member.joinitem', 1);
        //$ex1ValueItemArray = explode(',',$joinItem['ex1']['value']);
        $joinItem['ex1']['value']='삼성,현대,코웨이,SK하이닉스';

        //TODO. config의 member.join ex1 data get and parse
        $searchVo = new SearchVo();
        $searchVo->setWhereArray(['groupCode=?','code=?']);
        $searchVo->setWhereValueArray(['member','joinItem']);
        //$searchVo->setWhereValue('member');   //각각 넣어서 사용할 수도 있다.
        //$searchVo->setWhereValue('joinItem');
        /*$searchVo->setWhereValueArray([
            ['value'=>'member','type'=>'s']
            ,['value'=>'joinItem','type'=>'s']
        ]); //Value 타입 지정이 필요하다면 지정해서 할 수 있다.*/

        //$memberConfig = DBUtil::getOne('es_config',['groupCode','code'],['member','joinItem']);

        $memberConfig = DBUtil::getOneBySearchVo('es_config',$searchVo);
        $updateData = array();
        $updateData['data'] = json_encode($joinItem, JSON_UNESCAPED_SLASHES);
        //gd_debug($memberConfig['data']);

        DBUtil::update('es_config',$updateData,$searchVo);
    }

    public function godoUtilTest() {
        gd_debug( Request::getHost() );
        gd_debug( Request::getInfoUri() );
        gd_debug( Request::getDirectoryUri() );
        gd_debug( Request::get()->all() );
        gd_debug( Request::get()->toArray() );
        gd_debug( Request::getReferer());
        gd_debug(DateTimeUtils::dateFormat('Y-m-d H:i:s', 'now'));
    }

    public function manualExcelData(){
        $sql = "
select 
    left(a.deliveryDt ,10)  as deliveryDt, 
    d.memId,   
    b.goodsNm, 
    max(a.goodsPrice) as goodsPrice, 
    sum(a.goodsCnt)   as goodsCnt, 
    max(a.goodsPrice) * sum(a.goodsCnt) as salePrice 
from es_orderGoods a 
join es_goods b 
  on a.goodsNo = b.goodsNo 
join es_order c 
  on a.orderNo = c.orderNo
join es_member d 
  on c.memNo = d.memNo  
where left(a.orderStatus,1) in ( 's', 'd' )
  and a.scmNo = 6
  and a.handleSno = 0
group by left(a.deliveryDt ,10), 
  d.memId,   
b.goodsNm
order by d.memId, left(a.deliveryDt ,10)";

        $list = DBUtil2::runSelect($sql);
        $title = [
            '날짜',
            '아이디',
            '상품명',
            '단가',
            '수량',
            '금액'
        ];
        $excelBody = '';
        foreach ($list as $key => $val) {
            $fieldData = array();
            $fieldData[] = ExcelCsvUtil::wrapTd($val['deliveryDt'],'text','mso-number-format:\'\@\''); //핸드폰
            $fieldData[] = ExcelCsvUtil::wrapTd($val['memId'],'text','mso-number-format:\'\@\''); //핸드폰
            $fieldData[] = ExcelCsvUtil::wrapTd($val['goodsNm']);
            $fieldData[] = ExcelCsvUtil::wrapTd(number_format($val['goodsPrice']));
            $fieldData[] = ExcelCsvUtil::wrapTd(number_format($val['goodsCnt']));
            $fieldData[] = ExcelCsvUtil::wrapTd(number_format($val['salePrice']));
            $excelBody .=  "<tr>". implode('',$fieldData) . "</tr>";
        }
        $date = date('Y-m-d');
        $simpleExcelComponent = SlLoader::cLoad('Excel','SimpleExcelComponent','sl');
        $simpleExcelComponent->simpleDownload('한국타이어출고분석Raw_'.$date,$title,$excelBody);
    }

    public function oldTest_240116() {
        $list = DBTableField::getTableKeyAndBlankValue('sl_imsSample');
        //$list = DBTableField::tableImsSample();
        gd_debug($list);

        /*$imsService = SlLoader::cLoad('ims', 'imsService');
        $tableProject = SlCommonUtil::arrayAppKeyValue(DBIms::tableImsProject(),'val','textarea') ;
        gd_debug($tableProject);*/


        /*$newYear = date('Y');
        //SitelabLogger::logger('====== ' . $newYear .'휴일을 셋팅 ');
        gd_debug('====== ' . $newYear .'휴일을 셋팅 ');
        $holiService = SlLoader::cLoad('api','HolidayService','sl');
        $holiService->setYearHoliday($newYear);*/

        //projectNo 작지의 승인을 가져온다.
        /*$searchVo = new SearchVo(
            [
                'historyDiv=?',
                'projectSno=?',
                'afterStatus=?',
            ]
            ,[
                'workConfirm',
                '133',
                '승인완료',
            ]
        );
        $searchVo->setOrder('regDt desc');
        $historyCnt = DBUtil2::getCount(ImsDBName::STATUS_HISTORY, $searchVo);

        if(!empty($historyCnt)){
            //이후 업로드 건은 알림
        }
        gd_debug($historyCnt);*/



        /*$manualService = SlLoader::cLoad('godo','manualService','sl');
        //한전 내피추가
        $orderNo = 2312212035268856;
        $hanList = [
            ['orderGoodsSno'=>65316,'size'=>'100'],
            ['orderGoodsSno'=>65315,'size'=>'105'],
            ['orderGoodsSno'=>65314,'size'=>'110'],
        ];
        $manualService->setHanJp($orderNo,$hanList);
        $orderService = SlLoader::cLoad('Order','OrderService');
        $orderService->reCalcOrderData($orderNo);*/


        /*$imsService = SlLoader::cLoad('ims', 'imsService');

        $searchVo = new SearchVo([
            'preparedType=?',
            'preparedStatus=?',
        ],[
            'estimate',
            '4',
        ]);

        $searchVo->setOrder('regDt');

        $refineList = DBUtil2::getListBySearchVo(ImsDBName::PREPARED, $searchVo);

        foreach($refineList as $each){
            gd_debug($each['projectSno']);

            $params = [
                'sno' => $each['sno'],
                'projectSno' => $each['projectSno'],
                'reqType' => 'estimate',
            ];
            $imsService->setEstimate($params);
        }*/


        //
        //gd_debug($params);


        /*
                $list = DBUtil2::getListBySearchVo(ImsDBName::PRODUCT, new SearchVo('prdCostStatus=?','1'));
                //gd_debug($list);
                gd_debug(empty(-9));

                foreach($list as $each){

                }
                */


        /*$today = date('Y-m-d');
        gd_debug( $today );
        $aaa = DBUtil2::getCount('sl_3plGolfInvoiceHistory',new SearchVo('regDt>=?', date('Y-m-d').' 00:00:00'));
        gd_debug($aaa);*/

        //$product = DBUtil2::getOne('sl_3plProduct', '1', '1');

        /*$service = SlLoader::cLoad('godo','sopService','sl');
        $data = $service->getGodoOrder([
            'receiverName'=>'주식회사강남타이어',
            'receiverCellPhone'=>'010-5064-8421',
            'code'=>'MSTSTC39',
            'goodsCnt'=>1,
        ]);

        gd_debug($data);*/

        /*$orderNo = '2311221714070410';
        $manualService = SlLoader::cLoad('godo','manualService','sl');
        //세금계산서 수정.
        $manualService->setTax($orderNo, 67650, '1000000337');
        $manualService->setTax($orderNo, 47025, '1000000338');
        $orderService = SlLoader::cLoad('Order','OrderService');
        $orderService->reCalcOrderData($orderNo);
        gd_debug('complete');*/

        /*$manualService = SlLoader::cLoad('godo','manualService','sl');
        $manualService->setImsRefinePrdCost(1);*/

        //수기 송장 등록.
        /*
                $manualService->setManualInvoice('2023-11-16');*/

        //딜러코스트 할인
        /*$manualService = SlLoader::cLoad('godo','manualService','sl');
        $orderNo = '2311201634360969';
        $manualService->refineOrderGoodsDc($orderNo, 30);*/
        /*
        $orderNo = '2311151044386287';
        $manualService->refineOrderGoodsDc($orderNo, 30);
        $orderNo = '2311151112365202';
        $manualService->refineOrderGoodsDc($orderNo, 30);*/
        //$orderDt = '2023-05-31 17:44:58';


        //주문 세금계산서 정제
        /*$orderNoList = [
            2309210845278114,
        ];
        foreach($orderNoList as $orderNo){
            gd_debug($orderNo);
            $manualService->setTax($orderNo, 67650, '1000000337');
            $manualService->setTax($orderNo, 47025, '1000000338');
            $orderService = SlLoader::cLoad('Order','OrderService');
            $orderService->reCalcOrderData($orderNo);
        }*/

        /*
        $orderNo = 2310171541380981;
        gd_debug($orderNo);
        $manualService->setTax($orderNo, 67650, '1000000337');
        $manualService->setTax($orderNo, 47025, '1000000338');
        $orderService = SlLoader::cLoad('Order','OrderService');
        $orderService->reCalcOrderData($orderNo);

        $orderNo = 2310230820084271;
        gd_debug($orderNo);
        $manualService->setTax($orderNo, 67650, '1000000337');
        $manualService->setTax($orderNo, 47025, '1000000338');
        $orderService = SlLoader::cLoad('Order','OrderService');
        $orderService->reCalcOrderData($orderNo);*/


        //한전 내피추가
        /*$orderNo = 2311160942545699;
        $hanList = [
            ['orderGoodsSno'=>64170,'size'=>'95'],
        ];
        $manualService->setHanJp($orderNo,$hanList);
        $orderService = SlLoader::cLoad('Order','OrderService');
        $orderService->reCalcOrderData($orderNo);*/

//      $manualService->setHan3plCode();

        //22 * 5 = 110
        //s,m,l,ll,lll
        //23FWKPDPT26S
        //23FWKPDPT36LLL

        /*$targetList = DBUtil2::getListBySearchVo(DB_ORDER_GOODS, new SearchVo(['goodsNo=?','orderStatus=?'],['1000000341','p3']));
        foreach($targetList as $each){
            DBUtil2::update(DB_ORDER, ['orderStatus'=>'p3'], new SearchVo('orderNo=?', $each['orderNo']) );
        }
        $targetList = DBUtil2::getListBySearchVo(DB_ORDER_GOODS, new SearchVo(['goodsNo=?','orderStatus=?'],['1000000340','p3']));
        foreach($targetList as $each){
            DBUtil2::update(DB_ORDER, ['orderStatus'=>'p3'], new SearchVo('orderNo=?', $each['orderNo']) );
        }*/

        //$orderInfo = $orderService->getOrder('2309251116086903');
        //gd_debug($orderInfo);
        //$orderService->reCalcOrderData('2309251116086903');

        /*$replaceData = ImsSendMessage::imsMessageReplacer(ImsSendMessage::PREPARED_COMMON,[
            'title'=>'테스트',
            'company'=>'테스트컴파니',
            'projectNo'=>'12345',
            'productName'=>'테스트상품 외 1건',
        ]);
        $imsService = SlLoader::cLoad('ims', 'imsService');
        $imsService->sendAlarm($replaceData['title'],$replaceData['msg'],20);*/

        /*$this->imsPreparedMig();

        $list = DBUtil2::getList(ImsDBName::PROJECT, '1', '1');
        foreach($list as $each){
            if(!empty($each['addedInfo'])){
                $addedInfo = json_decode($each['addedInfo'],true);
                if( !empty($addedInfo['etc2']) ){
                    gd_debug($each);
                    gd_debug($addedInfo['etc2']);
                }
            }
        }*/

        //IMS 생산상태 Sync...
        /*$list = DBUtil2::getList(ImsDBName::PROJECT,'projectStatus','90');

        foreach($list as $each){
            $produce = DBUtil2::getOne(ImsDBName::PRODUCE, 'projectSno',$each['sno']);
            gd_debug($produce);
        }*/

        //PREPARED_TYPE::['BT'];
        /*$preparedConst = constant('Component\Ims\EnumType\PREPARED_TYPE::'.strtoupper('bt'));
        gd_debug($preparedConst);*/

        /*$imsService = SlLoader::cLoad('ims', 'imsService');
        $projectData = $imsService->getProject(['sno'=>60]);
        gd_debug($projectData);*/

        //gd_debug( $this->test() );

        /*gd_debug((ord('a')));
        gd_debug((ord('z')));
        gd_debug((ord('A')));
        gd_debug((ord('Z')));*/
        //gd_debug((ord('ZA1123')));
        //90 >
        //gd_debug((ord('러러ㄷ')));
        //gd_debug(chr(ord('A')+1));
        //gd_debug(" prdStep like '%\\\"confirmYn\\\": \\\"r\\\"%' ");
        /*
                $list = DBUtil2::getList(ImsDBName::PRODUCE,'1','1');
                foreach($list as $each){

                    $saveData = [];
                    $saveData['projectSno'] = $each['projectSno'];
                    $saveData['commentDiv'] = 'produce';

                    if(!empty($each['memo'])){
                        $saveData['comment'] = $each['memo'];
                        $saveData['regManagerSno'] = 43;
                        DBUtil2::insert(ImsDBName::PROJECT_COMMENT, $saveData);
                    }

                    if(!empty($each['msMemo'])){
                        $saveData['comment'] = $each['msMemo'];
                        $saveData['regManagerSno'] = 32;
                        DBUtil2::insert(ImsDBName::PROJECT_COMMENT, $saveData);
                    }

                }*/

        //$this->setTkeMember();

        /*$date = date('Y-m-d', strtotime('4 day'));
        gd_debug($date);*/

        /*$imsService = SlLoader::cLoad('ims', 'imsService');
        $imsService->setEstimate([
            'sno' => '12',
            'projectSno' => '20',
        ]);*/
        //$list = $imsService->getPreparedList(5);
        //$list = DBUtil2::getList(ImsDBName::PREPARED,'projectSno', 5);
        //gd_debug($imsService->getPreparedCount());
        //gd_debug($imsService->getPreparedProduceCount());

        //gd_debug(\Session::get('manager.sno'));
        //gd_debug(SlCommonUtil::getFlipData(SalesListService::SALES_STATUS_MAP,'가망고객'));

        //$ll = SlCommonUtil::arrayAppKeyValue(  , 'sno', 'factoryName'  );
        /*const RECOMMEND_TYPE = [
            1 => '기획',
            2 => '제안',
            4 => '샘플',
        ];*/
        //gd_debug(ImsCodeMap::RECOMMEND_TYPE[1]);
        //gd_debug(substr(ImsCodeMap::RECOMMEND_TYPE[1],0,3));


        /*$meetingTableField = DBTableField::tableImsMeeting();
        $meetingField = SlCommonUtil::arrayAppKeyValue($meetingTableField,'val','val');
        $meetingUnsetField=[
            'sno', 'projectSno', 'regManagerSno' , 'lastManagerSno', 'regDt', 'modDt'
        ];
        foreach($meetingUnsetField as $field){
            unset( $meetingField[$field] );
        }
        gd_debug($meetingField);*/

        /*$table = 'sl_imsProject';
        $mod = SlCommonUtil::arrayAppKeyValue(DBTableField::callTableFunction($table), 'val','name');
        gd_debug($mod);*/
        //gd_debug(chr(ord('A')+3));

        /*$date1 = new DateTime('2023-07-29');
        $date2 = new DateTime('2023-08-15');
        $interval = $date1->diff($date2);
        echo $interval->format('%R%a days'); // Outputs: +17 days*/

        /*$sDate = '2023-08-01';
        $eDate = date('Y-m-d');
        $dateDiff = SlCommonUtil::getDateDiff($sDate, $eDate);
        gd_debug($dateDiff); //invert 0 => minus

        $dateDiff = SlCommonUtil::getDateDiff($eDate, $sDate);
        gd_debug($dateDiff);*/


        //gd_debug( ImsJsonSchema::PREPARED_BT );
        /*$fileConst = 'Component\Ims\ImsCodeMap::PREPARED_FILE_'.strtoupper('bt');
        //
        //$fileConst = 'Component\Ims\EnumType\PREPARED_TYPE::'.strtoupper('bt');
        $fileList = constant($fileConst);
        foreach($fileList as $key => $value){
            if( empty($totalResult['fileList'][$value['fieldName']]) ){
                $totalResult['fileList'][$value['fieldName']] = [
                    'title'=>'등록된 파일이 없습니다.',
                    'memo'=>'',
                    'files' => []
                ];
            }
        }
        gd_debug( $fileConst );
        gd_debug( $fileList );
        gd_debug( $totalResult );*/


        /*        $preparedTableField = DBTableField::tableImsPrepared();
                $preparedField = SlCommonUtil::arrayAppKeyValue($preparedTableField,'val','val');
                $refineNotAs = 'f.'.implode(',f.',$preparedField);
                gd_debug($refineNotAs);
                $refineField = [
                    'sno', 'regManagerSno', 'lastManagerSno', 'regDt', 'modDt'
                ];
                foreach($refineField as $fieldName){
                    $preparedField['prepared'.ucfirst($fieldName)] = $fieldName.' as prepared'.ucfirst($fieldName);
                    unset($preparedField[$fieldName]);
                }
                $refinePreparedField = 'f.'.implode(',f.',$preparedField);

                gd_debug($refinePreparedField);*/

        //$fileList = array_merge(ImsCodeMap::PROJECT_FILE, ImsCodeMap::PROJECT_ETC_FILE);
        //gd_debug($fileList);

        /*$imsService = SlLoader::cLoad('ims', 'imsService');
        $list = $imsService->getLatestProjectFiles(['projectSno'=>5]);
        gd_debug($list);*/

        //$data = $imsService->getProjectStepCount();
        //gd_debug($data);

        //$projectTableField = DBTableField::tableImsProject();
        //$projectField = SlCommonUtil::arrayAppKeyValue($projectTableField,'val','val');
        //gd_debug(implode(',',$list));

        //$projectTableField = DBTableField::tableImsProject();
        //$projectField = SlCommonUtil::arrayAppKeyValue($projectTableField,'val','val');
        //gd_debug('a.'.implode(',a.',$projectField));

        /*$ymd = date('y');
        gd_debug($ymd);

        $imsService = SlLoader::cLoad('ims', 'imsService');

        $year = 2023;
        $yearMap = [];
        for($i=0;15>=$i;$i++){
            $yearMap[$year+$i] = $year+$i;
        }
        $this->setData('codeYear', $yearMap);

        //시즌
        gd_debug($imsService->getCode('style','시즌'));*/

        /*$count = DBUtil2::getCount(ImsDBName::PROJECT, new SearchVo('regDt>=?', date('Y-m-d').' 00:00:00'));
        gd_debug($ymd.str_pad($count, 2, '0', STR_PAD_LEFT));
        foreach(ImsCodeMap::RECOMMEND_TYPE as $key=>$value){
            gd_debug( $key & 3 );
        }*/

        //$table = array_flip(SlCommonUtil::getArrayKeyData(DBTableField::callTableFunction(ImsDB::CUSTOMER), 'val'));
        //gd_debug(DBTableField::getTableKey(ImsDB::CUSTOMER));
        //$rslt = in_array('sn1o', DBTableField::getTableKey(ImsDBName::CUSTOMER));
        //$rslt = array_key_exists('sno',$table);
        //gd_debug($rslt);
        //$this->cUrlTest()();

        /*
        DBUtil2::insert('sl_imsCode', [
            'codeType' => '테스트',
            'codeDiv' => '테스트',
            'codeValueKr' => '테스트',
            'codeValueEn' => '테스트',
        ]);
        */

        //$list = DBUtil2::getList('sl_imsCode','1','1');
        //gd_debug($list);

        //$this->godoUtilTest();

        /*$atr = " 테 스 트 ";
        gd_debug($atr);
        gd_debug(trim($atr));

        $managerId = \SiteLabUtil\SlCommonUtil::isDevId();
        gd_debug( $managerId );*/

        /*$list = DBUtil2::runSelect("select * from sl_recap where estimateCost > 0");
        foreach($list as $each){
            DBUtil2::insert('sl_recapFakeEstimate', [
                'isAccept' => $each['estimateCost'],
                'projectSno' => $each['sno'],
            ]);
        }*/


        /*gd_debug( SlCommonUtil::getOnlyNumber('테스트 12345 , 56787') );

        gd_debug(gd_date_format('w', '2023-07-01'));
        gd_debug(gd_date_format('w', '2023-07-02'));
        gd_debug(gd_date_format('w', '2023-07-03'));
        gd_debug(gd_date_format('w', '2023-07-04'));
        gd_debug(gd_date_format('w', '2023-07-05'));
        gd_debug(gd_date_format('w', '2023-07-06'));
        gd_debug(gd_date_format('W', '2023-07-07'));*/


        //create table zzz_recap0709 SELECT * FROM `sl_recap` WHERE 1;
        //create table zzz_recapProduce0709 SELECT * FROM `sl_recapProduce` WHERE 1;

        //
        //$manualService = SlLoader::cLoad('godo','manualService','sl');
        //$manualService->refineRecapEndDt();

        //gd_debug($manualService->tkePartnerOrderChange('2305061233172406', '3190'));

        //$this->mailSendTest();

        //$searchVo = new SearchVo(['productCode=?','customerName=?'],['MSGOLF048','김재웅, 유원정']); //리스트로 나올수 있음.
        //$searchVo = new SearchVo(['productCode=?','customerName=?','mobile=?'],['MSGOLF048','김재웅, 유원정','010-3124-3835']); //리스트로 나올수 있음.
        //$tmpList = DBUtil2::getListBySearchVo('sl_3plOrderTmp', $searchVo);
        //gd_debug($tmpList);

        //gd_debug("시작");
        //$service = SlLoader::cLoad('Addition','AdditionService');
        //$service->downloadPackingList();

        //$manualService = SlLoader::cLoad('godo','manualService','sl');
        //gd_debug($manualService->tkePartnerOrderChange('2305061233173766', '5676'));

        //groupSno = 6
        /*        $list = DBUtil2::getList(DB_MEMBER, 'groupSno', '5');
                foreach($list as $each){
                    if( strpos($each['memId'], 'n') !== false ){
                    }else{
                        $list1 = DBUtil2::getList(DB_MEMBER, 'memId', 'n'.$each['memId']);
                        if( !empty($list1)){
                            gd_debug($list1);
                        }
                        DBUtil2::update(DB_MEMBER, ['memId'=>'n'.$each['memId']], new SearchVo('memNo=?',$each['memNo']));
                    }
                }*/

        //gd_debug($list);
    }

    public function oldTest_240507(){
        //$this->sendHkResearch();

        /*$param['orderName'] = '송준호';
        SlKakaoUtil::send(1 , '01081099599' ,  $param);
        gd_debug('일반 톡');*/

        /*$param['orderName'] = '송준호';
        $param['surveyUrl'] = 'URL은안되나';
        SlKakaoUtil::send(10 , '01081099599' ,  $param);
        gd_debug('daum 한타 톡');*/

        /*$param['companyName'] = 'TKE엘리베이터';
        $param['orderName'] = '송준호';
        $param['year'] = '2022';
        $param['surveyUrl'] = 'https://forms.gle/EiLsyDsjPGKvKLor7';
        $param['btnUrl'] = 'https://forms.gle/EiLsyDsjPGKvKLor7';
        SlKakaoUtil::send(18 , '01081099599' ,  $param);*/
        //SlKakaoUtil::send(13 , '01081099599' ,  $param);
        /*gd_debug('KAKAO TEST');
        gd_debug($param);*/

        //orderName
        /*        */

        //gd_debug(count($list));
        //gd_debug($list[0]['cnt']);

        /*$outStockData['memNo'] = $godoOrder['memNo'];
        $outStockData['orderDeliverySno'] = $godoOrder['orderDeliverySno'];
        $outStockData['orderGoodsSno'] = $godoOrder['orderGoodsSno'];
        $outStockData['orderNo'] = $godoOrder['orderNo'];

        //고도몰 주문 업데이트
        $godoUpdateData = [
            'orderStatus'=>'d1',
            'invoiceCompanySno'=>'8',
            'invoiceNo'=>$invoice,
            'deliveryDt'=>'now()',
        ];
        DBUtil2::update(DB_ORDER_GOODS, $godoUpdateData, new SearchVo('sno=?', $godoOrder['orderGoodsSno'] ) );
        $orderNoList[] = $godoOrder['orderNo'];*/


        /*$imsService = SlLoader::cLoad('ims', 'imsService');
        $rslt = $imsService->getProductionCount();
        gd_debug($rslt);

        $rslt2 = $imsService->getProductionRtwCount();
        gd_debug($rslt2);*/

        //$startDate = date('Y-m-d', strtotime('6 days'));
        //gd_debug( $startDate );

        /*gd_debug(SlCommonUtil::getDateDiff('2024-04-10','2024-04-06')); // -4 : 지연
        gd_debug(SlCommonUtil::getDateDiff('2024-04-10','2024-04-16')); // +6@ : 양호
        gd_debug(SlCommonUtil::getDateDiff('2024-04-10','2024-04-15')); // +0~5 : 촉박
        gd_debug(SlCommonUtil::getDateDiff('2024-04-10','2024-04-14')); // +0~5 : 촉박
        gd_debug(SlCommonUtil::getDateDiff('2024-04-10','2024-04-11')); // +0~5 : 촉박
        gd_debug(SlCommonUtil::getDateDiff('2024-04-10','2024-04-10')); // +0~5 : 촉박*/

        //$list = DBUtil2::runSelect("select msDeliveryDt, deliveryExpectedDt, datediff(msDeliveryDt, deliveryExpectedDt) as diffDt from sl_imsProduction order by regDt desc limit 10");
        //_debug($list);

        /*$projectSno = 12;
        $imsService = SlLoader::cLoad('ims', 'imsService');
        $imsService->setSyncStatus($projectSno);*/

        /*$list = DBUtil2::getList(DB_ORDER_GOODS, 'orderNo', '2403251712108149');
        foreach($list as $each){
            $each;
        }*/


        //$this->factoryMailTest();

        /*$orderNo = 2305161200522849;

        $erpService = SlLoader::cLoad('erp','erpService');
        $erpService->cancelOrder($orderNo);*/



        /*$order = \App::load('\\Component\\Order\\OrderAdmin');
        $orderData = $order->getOrderView($orderNo);
        gd_debug(substr($orderData['orderStatus'],0,1));*/

        /*$cancelMsg = [
            'orderStatus' => 'c4',
            'handleDetailReason' => __('고객요청에 의해 취소 처리'),
        ];

        $order = \App::load('\\Component\\Order\\OrderAdmin');
        $reOrderCalculation = \App::load('\\Component\\Order\\ReOrderCalculation');
        $orderData = $order->getOrderView($orderNo);
        $param = [];
        foreach ($orderData['goods'] as $value) {
            foreach ($value as $val) {
                foreach ($val as $goodsData) {
                    $param[$goodsData['sno']] = $goodsData['goodsCnt'];
                }
            }
        }
        $order->setAutoCancel($orderNo, $param, $reOrderCalculation, $cancelMsg);*/

        /*gd_debug($orderNo . ' 취소완료.');*/

        //$orderService = SlLoader::cLoad('Order','OrderService');
        //$orderService->syncOrderStatus('2403141335159327');

        /*$imsService = SlLoader::cLoad('ims', 'imsService');
        $params['condition']['sno'] = 13;
        $meetingData = $imsService->getListNewMeeting($params);
        gd_debug($meetingData['list'][0]);*/

        //과거 자료 가져오기 ( fabricStatus , fabricNational )
        /*$psno = 226;
        $projectInfo = DBUtil2::runSelect("select * from zzz_imsProject where sno = {$psno}");
        gd_debug($projectInfo); //2   , 1 ,2, 4*/

        //$styleSno = 373;
        //$fabricList = $imsService->setSyncProductFabricStatus($styleSno);
        //$imsService->setSyncProductBtStatus($styleSno, $fabricList);

        /*$projectSno = 136;
        gd_debug($projectSno);
        $imsService->setSyncStatus($projectSno, __METHOD__);*/

        //전체 싱크
        /**/

        //이후 업로드 건은 알림
        /*$imsService = SlLoader::cLoad('ims', 'imsService');
        $projectInfo = $imsService->getProject(['sno'=>284]);
        $commentList = [
            $projectInfo['customer']['customerName'],
            $projectInfo['project']['projectYear'],
            $projectInfo['project']['projectSeason'].'의',
            ' 작업지시서가 새로 등록되었습니다.'
        ];

        $comment = implode(' ', $commentList);
        $comment .= ' 테스트 메세지 (이 메세지가 보이면 송준호 팀장에 톡주세요)';

        $rslt = SlSmsUtil::sendSmsSimple($comment, '01081099599');
        gd_debug($rslt);
        $rslt = SlSmsUtil::sendSmsSimple($comment, '01022854817');
        gd_debug($rslt);*/


        /*
        $list = $imsService->getEstimateList([
            'styleSno' => 373
        ]);

        gd_debug($list);*/

        //departmentCd

        //$nasDownloadUrl = ImsCodeMap::NAS_DN_URL;
        //gd_debug("<a :href=\"'{$nasDownloadUrl}name='+encodeURIComponent(file.fileName)+'&path='+file.filePath\" class=\"text-blue\">{% fileIndex+1 %}. {% file.fileName %}</a>");

        // 이건 Project와 싱크를 위해 만듬
        /*$list = DBUtil2::getList(ImsDBName::PROJECT, 'projectStatus', '90');
        foreach($list as $each){
            $produce = DBUtil2::getOne(ImsDBName::PRODUCE, 'projectSno', $each['sno']);
            if( $produce['produceStatus'] <> '99' ){
                DBUtil2::update(ImsDBName::PRODUCE, [
                    'produceStatus' => 99
                ], new SearchVo('sno=?',$produce['sno']));
            }
        }*/


        //$list = SlCommonUtil::arrayAppointedValue(ImsCodeMap::IMS_FABRIC_STATUS,'name');
        //gd_debug($list);
    }


    public function test241220(){
        $params['styleSno'] = '446';
        $params['costSno'] = '150';
        //$imsService = SlLoader::cLoad('ims', 'imsService');
        //$imsService->setSyncStatus(50);

        //발주 제어 .
        //생산가 체크
        //판매가 체크
        /*$projectSno = 124;
        $imsService = SlLoader::cLoad('ims', 'imsService');
        $imsService->setSyncStatus($projectSno, __METHOD__);
        $projectData = $imsService->getProject(['sno'=>$projectSno]); //8
        $checkData = SlCommonUtil::getAvailData($projectData['project'], [
           'prdCostApproval',
           'prdPriceApproval',
           'assortApproval',
           'workConfirm',
           'customerOrderConfirm',
        ]);
        gd_debug($checkData);*/


        //상품할인하기
        $manualService = SlLoader::cLoad('godo','manualService','sl');
        $orderNo = '2412121022398157';
        //$manualService->refineOrderGoodsDc($orderNo, 50);

        //hd12345*

        //$depositService = SlLoader::cLoad('deposit','deposit');
        //gd_debug($depositService);

        //DBUtil2::getOne();
        /*$memNo = 1;
        $latest = DBUtil2::getOneSortData(DB_MEMBER_DEPOSIT, 'memNo=?', $memNo, 'regDt desc');
        //gd_debug($list);

        $addDeposit = 5000;
        if(!empty($latest)){
            $latest['beforeDeposit']=$latest['afterDeposit'];
            $latest['afterDeposit']=$latest['afterDeposit']+$addDeposit;
            $latest['deposit']=$addDeposit;
            $latest['contents']='매장평가 우수점 구매비용 지급';
            unset($latest['sno']);
            unset($latest['regDt']);
            unset($latest['modDt']);
            DBUtil2::insert(DB_MEMBER_DEPOSIT, $latest);
        }else{
            DBUtil2::insert(DB_MEMBER_DEPOSIT, [
                'memNo'
            ]);
        }*/


        //$prdList = DBUtil2::getList(ImsDBName::PRD_MATERIAL, 'styleSno', 421);
        //$convPrdList = SlCommonUtil::arrayAppKey($prdList, 'sno');

        $manualService = SlLoader::cLoad('godo','manualService','sl');
        /*$orderNo = '2411271528467913';
        $manualService->refineOrderGoodsDc($orderNo, 30);

        $orderNo = '2411271539035883';
        $manualService->refineOrderGoodsDc($orderNo, 30);*/


        //2411271528467913,2411271539035883

        /*$imsService = SlLoader::cLoad('ims', 'imsService');
        $rslt = $imsService->getProject(['sno'=>1]);
        gd_debug($rslt);*/

        //협의수량 업데이트.
        /*$prdList = DBUtil2::getList(ImsDBName::PRODUCT, "delFl='n' and projectSno", 8 );
        foreach($prdList as $prd){
            $totalQty = 0;
            $assortList = json_decode($prd['assort'],true);
            foreach( $assortList as $assort ){
                foreach( $assort['optionList'] as $optionCnt ){
                    $totalQty += $optionCnt;
                }
            }

            DBUtil2::update(ImsDBName::PRODUCT, ['prdExQty'=>$totalQty,'prdCount'=>$totalQty], new SearchVo('sno=?', $prd['sno']));
        }*/

        /*$sql = "select a.sno, concat(b.customerName,' ', a.productName, ' ', REPLACE(a.styleCode, ' ','')) as productName, a.sizeSpec from sl_imsProjectProduct a join sl_imsCustomer b on a.customerSno = b.sno join sl_imsProject c on a.projectSno = c.sno
                 where a.sno <> 370 and a.delFl = 'n' and c.projectStatus = 90 and a.sizeSpec <> ''"; //발주 완료 중.
        $defaultStyleSettingData =  DBUtil2::runSelect($sql,null,false);
        gd_debug( SlCommonUtil::arrayAppKeyValue($defaultStyleSettingData,'sno','productName') );
        gd_debug( SlCommonUtil::arrayAppKeyValue($defaultStyleSettingData,'sno','sizeSpec') );
        gd_debug($defaultStyleSettingData);*/


        /*$sql="update sl_imsEwork set specData = beforeSpecData";
        $rslt = DBUtil2::runSql($sql);
        gd_debug('복원:'.$rslt);*/


        /*gd_debug(date('Ymdhis'));
        $list = DBUtil2::runSelect("select * from sl_imsEwork where specData <> '' and specData is not null");
        foreach($list as $each){
            $parse = json_decode($each['specData'],true);
            if( count($parse) > 1 ){
                gd_debug($each);
                gd_debug($parse);
            }
            //gd_debug($each);
        }*/

        //$onHand = array_flip(ImsCodeMap::MATERIALS_ON_HAND)[$onHandValue];
        //gd_debug( array_flip(ImsCodeMap::MATERIALS_ON_HAND) );
        //$categoryService = SlLoader::cLoad('ims','imsCategoryService');
        /*$lastNo = DBUtil2::runSelect("select max(left(cateCd,3)) as lastNo from sl_imsCategory where cateType='material'")[0]['lastNo'];
        $lastNo = '000';
        gd_debug($lastNo);
        gd_debug((int)$lastNo);*/


        //gd_debug(Request::getDomainUrl());
        //gd_debug(SlCommonUtil::getAdminHost());

        //ImsJsonSchema::SPEC_DATA
        //array_keys(ImsJsonSchema::SPEC_DATA);

        /*$tableList= [
            'a' =>['data' => [ ImsDBName::CALENDAR ], 'field' => ['*']],
            'b' =>['data' => [ DB_MANAGER, 'JOIN', 'a.regManagerSno = b.sno' ], 'field' => ['managerNm as regManagerNm']]
        ];
        $table = DBUtil2::setTableInfo($tableList);
        $searchVo = new SearchVo('a.sno=?', 28);
        $data = DBUtil2::getComplexList($table,$searchVo, false, false, false);
        gd_debug($data);*/

        /*$imsService = SlLoader::cLoad('ims', 'imsService');
        $imsService->autoPackingTodo();*/

        /*$service = SlLoader::cLoad('godo','sopService','sl');
        $sql = "select * from sl_3plStockInOut where regDt >= '2024-10-11 00:00:00' and thirdPartyProductCode = '24SFMTKEJP110' and quantity=1";
        $list = DBUtil2::runSelect($sql);

        foreach($list as $each){
            $godoOrder = $service->getGodoOrder([
                'receiverName'=>$each['customerName'],
                //'receiverCellPhone'=>'',
                'code'=>$each['thirdPartyProductCode'],
                'goodsCnt'=>1,
            ]);

            gd_debug($each['customerName']);

            if(!empty($godoOrder['sno'])){
                //gd_debug($godoOrder['sno']);
                gd_debug($each['customerName'].'/'.$each['receiverCellPhone']. '/' . $each['thirdPartyProductCode'].'/'.$each['quantity']);
            }else{
                gd_debug('없음');
            }
        }*/


        /*        if(!empty($godoOrder)){
                    if( !empty($godoOrder['handleSno']) ){
                        $outStockData['inOutReason'] = ErpCodeMap::ERP_STOCK_REASON['교환출고'];
                    }
                    $outStockData['memNo'] = $godoOrder['memNo'];
                    $outStockData['orderDeliverySno'] = $godoOrder['orderDeliverySno'];
                    $outStockData['orderGoodsSno'] = $godoOrder['orderGoodsSno'];
                    $outStockData['orderNo'] = $godoOrder['orderNo'];

                    //고도몰 주문 업데이트
                    $godoUpdateData = [
                        'orderStatus'=>'d1',
                        'invoiceCompanySno'=>'8',
                        'invoiceNo'=>$invoice,
                        'deliveryDt'=>'now()',
                    ];
                    $rslt = DBUtil2::update(DB_ORDER_GOODS, $godoUpdateData, new SearchVo('sno=?', $godoOrder['orderGoodsSno'] ) );
                    $orderNoList[] = $godoOrder['orderNo'];
                    gd_debug($rslt);
                }*/


        /*$list = DBUtil2::getList(ImsDBName::PROJECT,1,1);
        foreach($list as $each){
            $addedInfo = json_decode($each['addedInfo'],true);
            if( !empty($addedInfo) ){
                //gd_debug( $addedInfo );
                if(!empty($addedInfo['info073']) ){
                    gd_debug('# 요청배경 :' . $each['sno']);
                    gd_debug($addedInfo['info73']);
                }
                if(!empty($addedInfo['info074'])){
                    gd_debug('# 영업의견 : ' . $each['sno']);
                    gd_debug($addedInfo['info74']);
                }
            }
        }*/

        //$manualService = SlLoader::cLoad('godo','manualService','sl');
        //$manualService->setFindAttributeHk();
        //gd_debug(ImsCodeMap::PRIVATE_MALL_MANAGER_SIMPLE);

        /*$targetOptionList = ScmTkeService::getPreOrderGoodsOption();
        if(empty($targetOptionList)){
            gd_debug('A');
        }else{
            gd_debug('B');
        }*/

        /*$targetList = DBUtil2::getList('sl_3plProduct', 'attr1=\'\' and scmNo', 6);

        foreach($targetList as $each){
            gd_debug($each);
        }*/


        /*$imsStyleService = SlLoader::cLoad('ims', 'imsStyleService');
        $list = $imsStyleService->getListStyle(['condition'=>['sno'=>3]]);
        gd_debug($list);*/

        //gd_debug(SlCommonUtil::getDateDiff('2024-06-14',SlCommonUtil::getDateCalc('2024-08-04', -3)));

        /*$rslt = SlCommonUtil::getDateCalc(date('Y-m-d'),5);
        gd_debug($rslt);*/

        //


        //gd_debug(SlCommonUtil::aesEncrypt(1));
        //$this->sendMailTest();

        //$projectSno = SlCommonUtil::aesDecrypt( $request['key'] ); //검증용.
        //SlCommonUtil::aesEncrypt();

        /*gd_debug( SlCommonUtil::isHoliday() );
        gd_debug( SlCommonUtil::isHoliday('20240815') );*/

        //runProjectSchedule
        //$cur = ExchangeRateService::getCurrentExchangeRate();
        //gd_debug($cur);

        //$date = date('Ymd');
        /*$date = '20240807';
        gd_debug($date);
        $holiService = SlLoader::cLoad('api','ExchangeRateService','sl');
        $holiService->setExchangeRate();*/

        /*$imsApprovalService = SlLoader::cLoad('ims', 'imsApprovalService');
        $methodMap = new \ReflectionClass('\Component\Ims\ImsApprovalService');
        $methods = $methodMap->getMethods();
        $rslt = [];
        foreach( $methods as $method ){
            $rslt[$method->name]=1;
        }
        //$me =SlCommonUtil::arrayAppKeyValue($methodMap->getMethods(),'name','name');
        gd_debug( $rslt);
        gd_debug( $rslt['acceptPro1posal'] );*/


        //환율관련
        /*        $currency = new CurrencyExchangeRate();
                $currencyAdmin = new CurrencyExchangeRateAdmin();
                gd_debug($currency->fetchPublicData(date('Ymd')));
                gd_debug($currency->fetch());
                gd_debug($currency->getConfigListFromDao());
                gd_debug($currencyAdmin->getGlobalCurrency());*/


        //gd_debug(date('ymdhis'));

        /*$reqManager = "02001002 ";
        gd_debug( (int)$reqManager );
        if( '0' == substr($reqManager,0,1) ){
            $reqManagerSno = substr($reqManager,1,99);
        }else{
            $reqManagerSno = $reqManager;
        }
        gd_debug( $reqManagerSno );
        //$this->sendKakaoTest();
        $imsService = SlLoader::cLoad('ims', 'imsService');*/

        /*        $params['target'] = 'customerIssue';
                $fncName = 'get'.ucfirst($params['target']);
                gd_debug($fncName);
                $rslt = $imsService->$fncName($params);
                gd_debug($rslt);*/

        /*$params['target'] = 'imsComment';
        $fncName = 'getList'.ucfirst($params['target']);
        $list = $imsService->$fncName($params);
        gd_debug($list);*/


        //1,4
        //DBUtil2::getList(ImsDBName::PROJECT, '');

        /*$addedField = [];
        foreach(ImsCodeMap::PROJECT_ADD_INFO as $key => $infoValue){
            //빈값 설정
            foreach(ImsCodeMap::PROJECT_ADD_INFO_KEY as $addInfoKey){
                $ucFirstAddInfoKey = ucfirst($addInfoKey);
                $addedField[] = "MAX(CASE WHEN added.fieldDiv = '{$key}' THEN added.{$addInfoKey} ELSE NULL END) AS {$key}{$ucFirstAddInfoKey}";
            }
        }
        gd_debug($addedField);*/

        /*$imsService = SlLoader::cLoad('ims', 'imsService');
        gd_debug($imsService->getProjectField());*/
        //gd_debug(array_key_exists('q1b',ImsCodeMap::PROJECT_ADD_INFO));

        /*$data = $imsService->getListProjectWithAddInfoTable();
        gd_debug($data);*/

        //gd_debug( DBTableField::tableProject() );
        /*$projectGroupField = DBTableField::getTableKey(ImsDBName::PROJECT);
        foreach($projectGroupField as $key => $each){
            $each = 'prj.' . $each;
            $projectGroupField[$key] = $each;
        }
        $projectGroupField[] = 'reg.managerNm';
        $projectGroupField[] = 'sales.managerNm';
        $projectGroupField[] = 'desg.managerNm';
        $projectGroupField[] = 'cust.customerName';
        gd_debug( $projectGroupField );
        gd_debug( implode(',',$projectGroupField) );*/

        //password 처리.
        /*        $pwdStr = 'bando20@$';
                $password = Digester::digest($pwdStr);
                gd_debug($password);*/

        //$this->setBtData();

        //$beforeData = DBUtil2::getOne(ImsDBName::PROJECT, 'sno', 138);
        //gd_debug($beforeData);
        //$beforeAddData = DBUtil2::getList(ImsDBName::PROJECT_ADD_INFO, 'projectSno', 138);
        //gd_debug(SlCommonUtil::arrayAppKey($beforeAddData, 'fieldDiv'));
        //$imsService = SlLoader::cLoad('ims', 'imsService');
        //$fileData = $this->getLatestProjectFiles(['sno'=>$params['projectSno']]);

        /*$imsService = SlLoader::cLoad('ims', 'imsService');
        $list = $imsService->getLatestProjectFiles(['projectSno'=>138]);
        gd_debug($list);
        gd_debug(count($list['fileEtc1']['files']));*/

        //키가져오기...
        //gd_debug(array_keys(ImsCodeMap::PROJECT_ADD_INFO));

        //$this->fodModify();

        //$managerInfo = SlCommonUtil::getManagerInfo();
        //gd_debug($managerInfo['departmentCd']);

        //$this->costCompleteCheck();

        /*$CodeMap = new \ReflectionClass('\Component\Admin\AdminMenu');
        $me = $CodeMap->getMethods();
        gd_debug($me);*/


        //$msno = SlCommonUtil::getManagerInfo()['departmentCd'];
        //gd_debug( $msno );

        $input = "<p>물류 비용 (생산기간 확보&nbsp; 필요)</p><p>&nbsp;- 400장 기준 110일 확보시 2,000원 정도로 다운 가능</p><p>물류 비용&nbsp;</p><p>&nbsp;- 1,000장 기준 120일 확보시 1,200원 정도로 다운 가능</p><p>&nbsp;- 現 IMS 등록 은 생산 기간 90일&nbsp;</p><p><br></p><p>원단은&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&nbsp;</p><p>&nbsp;- 1,000장 기준 소매 탕비 (600,000원) 장당 600원&nbsp;</p><p>&nbsp;- 제직 원단 4,600원 + 600원 = 5,200원&nbsp;</p><p>&nbsp;- 800 장 이하로는 탕비 2번 발생 (소매, 몸판)</p>";
// <p> 태그를 개행 문자로 치환
        //$output = preg_replace('/<p[^>]*>/', "\n", $input);
        $output = preg_replace('/\t+/', "", $input);
// 모든 태그 제거
        $output = strip_tags($output);
// &nbsp;와 같은 HTML 엔티티를 변환
        $output = html_entity_decode($output);
// 양쪽 공백 제거
        $output = trim($output);
// 여러 개의 개행 문자를 하나의 개행 문자로 치환
        $output = preg_replace('/\n+/', "\n", $output);
        //gd_debug($output);


        /*$imsService = SlLoader::cLoad('ims', 'imsService');
        $appData = $imsService->getApprovalData(['sno' => 52]);
        $a = ImsApprovalService::APPROVAL_TYPE[$appData['approvalType']]['name'];
        gd_debug($a);*/

        //$imsService = SlLoader::cLoad('ims', 'imsService');
        //$imsService->sendApprovalFirst(51);
        //SiteLabMailUtil::sendSystemMail('테스트 메일입니다.', '테스트', 'jhsong@msinnover.com');


        /*mode : 'getData',
        target : 'approvalData',
        projectSno : projectSno,
        styleSno : styleSno,
        eachSno : eachSno,
        approvalType : approvalType,*/


        /*        $service = SlLoader::cLoad('godo','sopService','sl');
                $godoOrder = $service->getGodoOrder([
                    'receiverName'=>'김동진차장',
                    'receiverCellPhone'=>'01062746784',
                    'code'=>'24MBANDO2VE115',
                    'goodsCnt'=>2,
                ]);*/

        //로그 남기기...
        //\Logger::channel('userLog')->debug(__METHOD__ . '[' . __LINE__ . '], ' . ' USER LOG : ', ['로그'=>'테스트']);
        //$imsApprovalService = SlLoader::cLoad('ims', 'imsApprovalService');

        /*$imsService = SlLoader::cLoad('ims', 'imsService');
        //$imsService->setProjectReOrder(565);

        gd_debug( SlCommonUtil::getMyInfo()['managerNm'] );
        gd_debug( SlCommonUtil::getMyInfo()['dutyCd'] );

        $code = gd_code('02002')['02002001'];
        gd_debug($code);*/

        /*$t = SlCommonUtil::arrayAppointedValue(ImsApprovalService::APPROVAL_TYPE,'name');
        gd_debug(json_encode($t));*/

        /*$imsService = SlLoader::cLoad('ims', 'imsService');
        $data = $imsService->getTodoRequestCount();
        gd_debug($data);*/
        //gd_debug(ImsService::getImsDBName('todo_comment'));
        //gd_debug(ImsService::getImsDBName('TODO_COMMENT'));
        /*$this->insertApprovalLine1();
        $this->insertApprovalLine2();
        $this->insertApprovalLine3();*/

        /*$manualService = SlLoader::cLoad('godo','manualService','sl');
        $orderNo = '2405281538146876';
        $manualService->refineOrderGoodsDc($orderNo, 30);*/
    }

    public function test250729(){
        /*$eworkService = SlLoader::cLoad('ims','ImsEworkService');
        $list = $eworkService->getEworkHistory([
            'styleSno' => '494'
            ,'historyDiv' => 'material'
        ]);
        gd_debug($list);*/

        //$eworkService
        //$list = DBUtil2::getList('es_exchangeRateConfig','1','1');
        //gd_debug($list);
        //$aa = SlCommonUtil::getCurrentDollar();
        //gd_debug($aa);

        /*$prdInfo = DBUtil2::getOne(ImsDBName::PRODUCT, 'sno',460,false);
        gd_debug( json_encode(json_decode($prdInfo['sizeSpec'],true)) );*/


        //$eworkList = DBUtil2::getList(ImsDBName::EWORK, '1', '1');

        //$list = DBUtil2::getList(ImsDBName::);
        //$eworkService = SlLoader::cLoad('ims','ImsEworkService');
        //$eworkData = $eworkService->getEworkData(464);
        //gd_debug($eworkData['product']);
        //gd_debug($eworkData['ework']['fileList']);
        //gd_debug($eworkData['ework']['data']);
        //$eworkDbInfo = SlCommonUtil::arrayAppKeyValue(DBIms::tableImsEwork(),'val','name');
        //gd_debug( isset($eworkDbInfo['markInfo5']) );
        //$imsService->setSyncStatus(50, __METHOD__);


        /*$params = [
            'warnMain'
        ];
        $eworkService->saveEworkWithPrdInfo($params);*/
        //$recordData['sno'] = '650'; //StyleSNo
        //$imsService->recordHistory('update', ImsDBName::EWORK, $recordData, ['완료 결재 임시 해제 : 어떤 어떤 사유로 인해 수정 진행']); //이력 기록

        //
        //$imsService->setSyncStatus(221, __METHOD__);
        //gd_debug(array_keys(ImsCodeMap::PROJECT_TYPE_N));


        /*$list = array_merge(ImsCodeMap::PROJECT_SCHEDULE_LIST, ImsCodeMap::PROJECT_SALES_SCHEDULE_LIST);
        gd_debug($list);*/


        //"SELECT * FROM es_order a join sl_orderAddedData b on a.orderNo = b.orderNo  join es_member c on a.memNo = c.memNo  where 0 >= a.settlePrice and c.sleepFl = 'n' and substr(a.orderStatus,0,1) in ('p','s','g','d') and b.giftAmount > 0 ";
        //$depositService->setMemberDeposit($memNo, $depositAmount, Deposit::REASON_CODE_GROUP . Deposit::REASON_CODE_ETC, 'o', $orderNo,  null, '오픈패키지 상품금액 차감 ('. $orderNo .')');

        //$memNo = '360';
        //$orderNo = '1';
        //$depositAmount = '-100000';


        /*$service = SlLoader::cLoad('scm','ScmAsianaService');
        $refreshList = DBUtil2::runSelect("select distinct companyId from sl_asianaOrderHistory");
        foreach($refreshList as $data){
            $service->saveEmpAllHistory($data['companyId']);
        }*/

        /*        $extTableInfoList = DBIms::tableImsProjectExt();
                $exclude = ['sno','projectSno','regDt','modDt'];
                $copyFieldList = [];
                foreach($extTableInfoList as $exTableInfo){
                    if(!in_array($exTableInfo['val'],$exclude)){
                        $copyFieldList[] = $exTableInfo['val'];
                    }
                }
                $copyFieldList[] = 'projectSno';
                $copyFieldStr = implode(',', $copyFieldList);

                $srcProjectSno = 607;
                $newProjectSno = 958;
                unset($copyFieldList[count($copyFieldList)-1]);
                $valueFieldStr = implode(',', $copyFieldList).' , '.$newProjectSno;
                DBUtil2::runSql("insert into sl_imsProjectExt ({$copyFieldStr}) select {$valueFieldStr} from sl_imsProjectExt where projectSno={$srcProjectSno}");

                gd_debug($rslt);*/

        //프로젝트 분할 처리 .
        //1. 프로젝트 복사 = 완전 동일하게



        //DBUtil2::

        /*$list = DBUtil2::runSelect("select styleSno, produceWarning from sl_imsEwork where produceWarning is not null and produceWarning <> ''");
        foreach($list as $each){
            $produceWarning = json_decode($each['produceWarning'], true);
            if( count($produceWarning['sampleSizeCnt']) > 0 ){
                foreach($produceWarning['sampleSizeCnt'] as $size => $sampleCnt){
                    if($sampleCnt>0){
                        gd_debug($each['styleSno'] . ' / ' .$size .' : '.$sampleCnt);
                    }
                }

            }
        }*/

        /*$aa = SlCommonUtil::setDateFormat('20250528','y/m/d');
        gd_debug($aa);*/

        //스타일 다른 프로젝트 복사시
        /*prdCost
        prdExQty
        estimateCost
        estimateCount
        prdCostConfirmSno */


        //"SELECT * FROM sl_imsProjectProduct WHERE 1";

        /**/

        //$imsProjectService = SlLoader::cLoad('imsv2','ImsProjectService');
        //$imsProjectService->setReOrderSchedule(931);

        /*$list = DBUtil2::getList(ImsDBName::PROJECT,"90 = projectStatus and isBookRegistered",'y');
        foreach($list as $project){
            DBUtil2::update(ImsDBName::PROJECT, ['projectStatus'=>91], new SearchVo('sno=?', $project['sno']));
        }*/
        //$prdList = "select distinct projectSno from sl_imsProduction where productionStatus = 30";
        //$now = date('y', strtotime('-1 year'));
        //gd_debug($now-1);

        /*gd_debug('업체선정 기준 확인');
        $list = DBUtil2::getList(ImsDBName::CUSTOMER, '1' , '1', null, false);
        $keyData = [];
        foreach($list as $each){
            $yesData = [];
            $addedInfo = json_decode($each['addedInfo'],true);
            foreach($addedInfo as $addInfoKey => $addInfo){
                if(!empty($addInfo)){
                    //$yesData[] = [$addInfoKey => $addInfo];
                    $keyData[$addInfoKey]++;
                }
            }
            if(count($yesData) > 0){
                //gd_debug($each['sno']);
                //gd_debug($yesData);
            }
        }
        gd_debug( $keyData );*/


        /*        gd_debug('업체선정 방법 확인');
                foreach($list as $each){
                    $addedInfo = json_decode($each['addedInfo'],true);
                    if(!empty($addedInfo['info109'])){
                        gd_debug($each['sno'] . ' : ' . $addedInfo['info109']);
                    }
                }*/


        //gd_debug('===> 아시아나 처리');
        //$this->get3PlOrderExcelTest(true, '2025-05-15', false);
        //ImsScheduleUtil::setProjectScheduleStatus(172);
        //ImsScheduleUtil::setScheduleCompleteDt(223,'order','now()');
        //ImsScheduleUtil::setProjectScheduleStatus(223);

        //$file = DBUtil2::getCount(ImsDBName::PROJECT_FILE, new SearchVo("fileDiv='filePlan' and projectSno=?",172));
        //gd_debug($file);
        //gd_debug('===> REFINE 판매가');
        /*$imsPrdService = SlLoader::cLoad('imsv2', 'imsProductService');
        $data = $imsPrdService->getProductList(['projectSno'=>223]);
        gd_debug($data);*/

        //778;
        //$imsService = SlLoader::cLoad('ims', 'imsService');
        //$imsService->setSyncStatus(221, __METHOD__);

        /*$params['projectSno'] = 778;
        $params['approvalType'] = 'salePrice';
        $approvalData = $imsService->getApprovalData($params);
        $completeDt = substr($approvalData['targetManagerList'][count($approvalData['targetManagerList'])-1]['completeDt'], 0, 10);
        gd_debug( $completeDt );*/

        /*
        $params['projectSno'] = 172;
        $params['approvalType'] = 'plan';
        $approvalData = $imsService->getApprovalData($params);
        gd_debug($approvalData['status']);*/

        //$manualService = SlLoader::cLoad('godo','manualService','sl');
        //$manualService->setTkeOrderRefine(132781, 8121, 'MSHKSC40', 110);
        /*$manualService->setHkOrderRefine(132775, 8133, '24SSMHANPOTS105');
        /*$manualService->setHkOrderRefine(131477, 8129, 'MSTSTC12');
        $manualService->setHkOrderRefine(130836, 8129, 'MSTSTC12');*/

        /*
        /*

        /*$imsProjectService->reOrder(	715	,50);*/
        //date('Y-m-d', strtotime('-7 days'));
        //$date = date('Ymd', strtotime('-3 days'));
        //gd_debug($date);
        //gd_debug( date('N') );
        /*$extField = array_flip(DBTableField::getTableKey(ImsDBName::PROJECT_EXT));
        $extField = array_flip(SlCommonUtil::unsetByList($extField,[
            'regDt','modDt', 'sno', 'projectSno'
        ]));*/


        //-15일 ?
        /*$eworkService = SlLoader::cLoad('ims','ImsEworkService');
        $eworkService->replaceMaterial(2240, 162);
        $eworkService->replaceMaterial(2241, 163);
        $eworkService->replaceMaterial(2242, 164);*/


        //$mail = SlCommonUtil::getManagerMail();
        //gd_debug($mail);

        /*$a = 'sh1208@naver.com';
        $a = (empty($a)?'':$a.',').\Session::get('manager')['email'];
        gd_debug($a);*/

        /*$list = DBUtil2::getList(ImsDBName::PRODUCTION, '1', '1');
        $cnt = 0;
        foreach($list as $each){
            $ework = DBUtil2::getOne(ImsDBName::EWORK, 'styleSno', $each['styleSno']);
            if(empty($ework['writeDt']) || '0000-00-00' == $ework['writeDt']){
                $rslt = DBUtil2::update(ImsDBName::EWORK, ['writeDt'=>$each['regDt']], new SearchVo('styleSno=?', $each['styleSno']));
                if(!empty($rslt)){
                    $cnt++;
                }
            }
        }
        gd_debug('update cnt :' . $cnt);*/

        //gd_debug(date('y'));
        ///$imsProjectService = SlLoader::cLoad('imsv2','ImsProjectService');

        /*$service = SlLoader::cLoad('godo','sopService','sl');
        $outList = $service->requestOutHistoryData('20250413', '20250415');
        gd_debug($outList);*/

        //$imsProjectService->reOrder(870	,50, 25); //리오더
        /*DBUtil2::update(DB_ORDER,['orderStatus'=>'c3'], new SearchVo('orderNo=?', 2504071623450142));
        DBUtil2::update(DB_ORDER_GOODS,['orderStatus'=>'c3'], new SearchVo('orderNo=?', 2504071623450142));
        DBUtil2::update(DB_ORDER,['orderStatus'=>'c3'], new SearchVo('orderNo=?', 2504071416114991));
        DBUtil2::update(DB_ORDER_GOODS,['orderStatus'=>'c3'], new SearchVo('orderNo=?', 2504071416114991));
        $list = DBUtil2::getList(DB_ORDER_GOODS, 'orderNo', '2504071623450142');
        foreach( $list as $orderGoods ){
            DBUtil2::delete('sl_asianaOrderHistory', new SearchVo('orderGoodsSno=?', $orderGoods['sno']));simple-download1
        }
        $list = DBUtil2::getList(DB_ORDER_GOODS, 'orderNo', '2504071416114991');
        foreach( $list as $orderGoods ){
            DBUtil2::delete('sl_asianaOrderHistory', new SearchVo('orderGoodsSno=?', $orderGoods['sno']));
        }
        //980036
        //아시아나 주문 이력 갱신

        $service->saveEmpAllHistory(980036);
        $service->saveEmpAllHistory(981946);*/

        /**/

        /*        $service = SlLoader::cLoad('scm','ScmAsianaService');
                $refreshList = DBUtil2::runSelect("select distinct companyId from sl_asianaOrderHistory");
                foreach($refreshList as $data){
                    $service->saveEmpAllHistory($data['companyId']);
                }*/


        //2503241009011648 : 이인호 제주지사 1049
        //2503241007061674 : 정희구 경기서비스지사(시흥)
        //ScmTkeService::manualDeliveryRefine();
        /*$sql = "select * from sl_orderScm where scmNo = 8 and regDt >= '2025-03-15 00:00:00' and scmDeliverySno > 0 ";
        $list = DBUtil2::runSelect($sql);
        foreach($list as $each){
            $deliveryData = DBUtil2::getOne('sl_setScmDeliveryList','sno',$each['scmDeliverySno']);
            if(empty($deliveryData)){
                $orderInfoData = DBUtil2::getOne(DB_ORDER_INFO,'orderNo',$each['orderNo']);
                $newDeliveryData = DBUtil2::getOne('sl_setScmDeliveryList','receiverAddress',$orderInfoData['receiverAddress']);
                if(!empty($newDeliveryData)) {
                    $rslt = DBUtil2::update('sl_orderScm', ['scmDeliverySno' => $newDeliveryData['sno']], new SearchVo('sno=?', $each['sno']));
                    gd_debug($rslt);
                }
            }
        }*/

        /*'1000000559' => [
            'option' => 85,90,95,100,105,110,115,120,125
        ],
        '1000000560' => [
            'option' => 24,26,28,30,32,34,36,38,40,42,44
        ],*/

        /*$service = SlLoader::cLoad('scm','ScmAsianaService');
        $refreshList = DBUtil2::runSelect("select distinct companyId from sl_asianaOrderHistory");
        foreach($refreshList as $data){
            $service->saveEmpAllHistory($data['companyId']);
        }*/

        //최근 데이터 찾음.
        //$manualService = SlLoader::cLoad('godo','manualService','sl');
        //$manualService->deleteOrder(2503180904345992);


        /*
                주문삭제
                $manualService->deleteOrder(2503281305027846);
        */
        //개별 Provide에 넣어야 한다.

        //이력 등록.
        //취소가 문제다.

        //사번 : 982000

        //DBUtil2::getList('sl_'  );

        /*$companyId = 982000;
        $service = SlLoader::cLoad('scm','ScmAsianaService');
        $service->saveEmpAllHistory($companyId);
        $empData = $service->getAsianaEmpData($companyId);
        gd_debug($empData);*/

        /*$service = SlLoader::cLoad('scm','ScmAsianaService');
        $refreshList = DBUtil2::runSelect("
        select distinct companyId from sl_asianaOrderHistory where regDt >= '2025-03-25 00:00:00'
        ");
        foreach($refreshList as $data){
            $service->saveEmpAllHistory($data['companyId']);
        }*/


        //일단 누적하자.

        //누적하는 대상.

        //승인

        //1. 주문시 바로 누적
        //2. 승인 취소시에만 orderGoodsSno 확인 후 취소 처리

        //DBUtil2::getList(DB_ORDER, 'orderNo', );
        //$empData['provideInfo']
        //gd_debug( $list );

        /*$orderService = SlLoader::cLoad('Order','OrderService');
        $orderList = [
            2501031012179997	,
        ];

        foreach($orderList as $orderNo){
            $orderService->reCalcOrderData($orderNo);
        }*/

        //


        //딜러 할인
        /*        $manualService = SlLoader::cLoad('godo','manualService','sl');
                $orderNo = '2503201152532719';
                $manualService->refineOrderGoodsDc($orderNo, 30);
                $orderNo = '2503201155259768';
                $manualService->refineOrderGoodsDc($orderNo, 30);*/




        //$hyundaeService->getPackingOrderList();


        //$service = SlLoader::cLoad('scm','ScmAsianaService');
        //$service->getCartList();
        //$service->createProduct();

        //IMS 프로젝트 이동
        //$imsProjectService = SlLoader::cLoad('imsv2','ImsProjectService');
        //$imsProjectService->movePrd(859, [3088]);
        //$imsProjectService->reOrder(214	,50); //리오더

        //주문삭제
        /*$manualService = SlLoader::cLoad('godo','manualService','sl');
        $manualService->deleteOrder(2503162041108569);
        $manualService->deleteOrder(2503162040399655);
        $manualService->deleteOrder(2503161911410172);
        $manualService->deleteOrder(2503160902313000);
        $manualService->deleteOrder(2503161439186577);*/

        //SELECT * FROM `sl_asianaOrderHistory` where companyId = '982348'
        //gd_debug($sql);
        /*
        $srcGoodsNo = '1000000000';
        $goodsAdminService = SlLoader::cLoad('goods','goodsAdmin');
        $newGoodsNo = $goodsAdminService->setCopyGoods($srcGoodsNo);
        DBUtil2::update('sl_imsProjectProduct',[
            'scmNo'=>'34',
            'goodsNm'=>'34',
        ],new SearchVo('goodsNo=?', $newGoodsNo));
        gd_debug($newGoodsNo);
        */

        //프로젝트 분기
        /*$imsProjectService = SlLoader::cLoad('imsv2','ImsProjectService');
        $imsProjectService->divideProject(778, [
            2773, 2764
        ]);*/



        //$str = '25';
        //gd_debug($str+2);

        //$imsProjectService = SlLoader::cLoad('imsv2','ImsProjectListService');
        //$imsProjectService->copyProjects();

        //gd_debug(\Session::get('manager.managerNm'));

        /*
        $subject = '(기획불가) 확인 후 정보 추가 바랍니다.';
        $contents = "<br> 기획서 정보 부족 (테스트).";
        $hopeDt = SlCommonUtil::getDateCalc(date('Y-m-d'), 3);
        $imsService->addTodoData($subject, $contents, $hopeDt, 174, ['02001001']);*/

        //+3

        /*$orderNo = '2502232105219001';
        $scmHankookService = SlLoader::cLoad('scm','scmHyundaeService');
        $scmHankookService->setHyundaeMasterOrder($orderNo);*/

        //$imsService = SlLoader::cLoad('ims', 'imsService');
        //$imsService->setSyncStatus(178);
        //$imsService->saveAddInfo(['sno'=>178]);

        //getDesignList
        /*$params = [
            'projectStatus' => [
                10,20,30,40,50,60
            ]
        ];
        $imsProjectListService = SlLoader::cLoad('imsv2','ImsProjectListService');
        $list = $imsProjectListService->getDesignList($params);
        gd_debug($list);*/


        /*$imsProjectService = SlLoader::cLoad('imsv2','ImsProjectListService');
        //$list = $imsProjectService->getSalesList();
        $params['condition']['sort']='P5,desc';
        $list = $imsProjectService->getQcList($params);
        gd_debug($list['list']);*/

        /*$imsProjectService = SlLoader::cLoad('imsv2','ImsProjectService');
        $aaa = $imsProjectService->getSimpleProject(172);*/

        /*$projectList = DBUtil2::getList(ImsDBName::PROJECT, 'projectType <> 4 and projectStatus', '50');

        $estimateSnoList = [];

        foreach($projectList as $prj){
            $productList = DBUtil2::getList(ImsDBName::PRODUCT, "delFl='n' and projectSno", $prj['sno']);
            foreach($productList as $product){

                $estimateData = DBUtil2::getOneSortData(ImsDBName::ESTIMATE," 'cost' = estimateType and 43 = reqFactory and styleSno=?",$product['sno'],'regDt desc');

                if( !empty($estimateData) ){
                    $rslt = DBUtil2::update(ImsDBName::ESTIMATE, [
                        'reqStatus' => '1',
                        'reqDt' => '2025-01-17',
                        'reqManagerSno' => '35',
                        'reqMemo' => '리오더 생산가 요청 드립니다.',
                        'resMemo' => '',
                        'completeDeadLineDt' => '2025-01-22',
                        'completeDt' => '0000-00-00',
                    ],new SearchVo('sno=?',$estimateData['sno']));
                    $estimateSnoList[] = $product['projectSno'] . ' - ' . $product['sno'] . ' : ' .$estimateData['sno'].' // ' .$estimateData['reqStatus'] . ' // ' . $rslt;
                }

            }
        }*/

        //프로젝트 돌려 . 스타일 돌려 . 이스티메이트에서 코스트에서 가장 최근desc 꺼를 요청으로 바꾸고 요청일자 바꾸고 처리일자 없앤다. / 코멘트도 없앤다.


        /*$styleSnoList = [
            2151,2154,2155,2726
        ];

        foreach($styleSnoList as $styleSn){o
            $eworkData = DBUtil2::getOne(ImsDBName::EWORK, 'styleSno', $styleSno);
            $eworkUpdate = [];
            //if(empty($eworkData['writeDt']) || '0000-00-00' == $eworkData['writeDt'] ) $eworkUpdate['writeDt']=date('Y-m-d'); //작성일 체크
            //if(empty($eworkData['requestDt']) || '0000-00-00' == $eworkData['requestDt'] ) $eworkUpdate['requestDt']=date('Y-m-d'); //의뢰일 체크
            if(empty($eworkData['writeDt']) || '0000-00-00' == $eworkData['writeDt'] ) $eworkUpdate['writeDt']='2025-12-23'; //작성일 체크
            if(empty($eworkData['requestDt']) || '0000-00-00' == $eworkData['requestDt'] ) $eworkUpdate['requestDt']='2025-12-27'; //의뢰일 체크

            DBUtil2::update(ImsDBName::PRODUCT, ['msDeliveryDt'=>'2025-04-22'], new SearchVo('sno=?', $styleSno));
            if( !empty($eworkUpdate) ){
                $updateRslt = DBUtil2::update(ImsDBName::EWORK, $eworkUpdate, new SearchVo('sno=?', $eworkData['sno']));
                gd_debug($updateRslt);
            }
        }*/

        //작성일 . 의뢰일 등록
        //일단 루프(생산). 없으면.regDt로

        /*$table = 'sl_ims'.ucfirst('projectExt');
        gd_debug($table);
        $dbFieldMap = SlCommonUtil::arrayAppKey(DBTableField::callTableFunction($table), 'val');
        gd_debug($dbFieldMap);*/

        //$method = SlCommonUtil::getMethodMap('Component\\Database\\DBTableField');
        //gd_debug( $method  );

        //확인 사항
        //1. 스트립하지 않은 데이터 가져오기 .
        //2. 저장할 때 그냥 저장
        //3. 비교

        //162 . sampleMemo
        //gd_debug($dbFieldMap);

        //$consts = SlCommonUtil::getClassConstants('Component\\Ims\\ImsDBName');
        //gd_debug($consts[strtoupper('code')]);


        /*
                $sql = "select * from sl_hyundaeZipcode";
                $list = DBUtil2::runSelect($sql);

                $rslt = [];
                foreach($list as $each){
                    $sql2 = "
        update es_orderInfo a
            join es_order b  on a.orderNo = b.orderNo
            join es_member c  on b.memNo = c.memNo
        set a.receiverZonecode = '{$each['receiverZonecode']}%'
        where a.receiverZonecode <> ''
            and a.receiverAddress like '{$each['receiverAddress']}%'
            and c.ex1 = '현대엘리베이터'";

                    gd_debug( $sql2 );

                    //$rslt[] = DBUtil2::runSql($sql2);
                }
                */

        //gd_debug($rslt);

        //TODO : 나중에 추가 하기 데일리 배치로.
        //$imsService->addProjectExtInfo();
        //gd_debug($rslt);

        //$manualService = SlLoader::cLoad('godo','manualService','sl');
        //$manualService->downloadImsComment();

        //$manualService = SlLoader::cLoad('godo','manualService','sl');
        //$manualService->recoverDeleteData(1420);

        //암호생성
        //$password = Digester::digest("a10240661");
        //gd_debug($password);

        //$password = Digester::digest("hdel123!");
        //gd_debug($password);
    }

}

