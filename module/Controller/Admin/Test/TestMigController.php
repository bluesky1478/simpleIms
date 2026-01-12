<?php

namespace Controller\Admin\Test;

use Component\Ims\ImsCodeMap;
use Component\Ims\ImsDBName;
use Component\Ims\ImsJsonSchema;
use Framework\Utility\NumberUtils;
use Component\Database\DBTableField;
use Component\Sitelab\SiteLabSmsUtil;
use http\Encoding\Stream\Enbrotli;
use SiteLabUtil\SlCommonUtil;
use SlComponent\Database\DBUtil;
use SlComponent\Database\DBUtil2;
use SlComponent\Database\SearchVo;
use SlComponent\Database\TableVo;
use SlComponent\Util\SitelabLogger;
use Globals;
use Component\Deposit\Deposit;
use SlComponent\Util\SitelabUtil;
use SlComponent\Util\SlCode;
use SlComponent\Util\SlLoader;
use SlComponent\Util\SlSmsUtil;

/**
 * TEST 페이지
 */
class TestMigController extends \Controller\Admin\Controller{

    private $orderService;

    public function index(){
        gd_debug("== 데이터 마이그레이션 ==");
        //타타대우선불처리
        //$this->setLateToPre('2510211349274659');
        //$this->setLateToPre('2510210953507451');

        //$this->asianaHistorySet();

        //$this->set3plStockInputData(); //3pl 입고 이력 등록
        //$this->setDefaultGoodsOptionLink();

        //$this->migWorkFile();
        //$this->setEworkMigration();
        //$this->setReorderSchedule();
        //$this->setScheduleMig();

        // EXCLUDE 처리 ############################################

        /*$list = DBUtil2::runSelect("SELECT * FROM sl_3plProduct WHERE thirdpartyProductCode LIKE 'APTKEL%'");
        foreach($list as $each){
            $this->setExclude($each['thirdPartyProductCode']);
        }*/
        //'MSTKEL002'; '7';

        /*$this->setExclude('MSTBXC09');
        $this->setExclude('MSTBXC08');
        $this->setExclude('MSTBXC13');
        $this->setExclude('MSTBX008');
        $this->setExclude('MSTBX022');*/

        /*$this->setExclude('MSTBXB11');
        for($i=1; 7>=$i; $i++){
            $this->setExclude('MSTBXB0'.$i);
        }*/
        // EXCLUDE 처리 ############################################

        //신규 인터페이스 마이그레이션
        //$this->migInterface();

        //기존 마이그 펑션
        //$this->oldMig();

        //리오더 일괄 등록
        //$this->setReOrder();

        //교환 재고 잡아둔 부분 취소
        //$this->setChangeRollback('2412221946123762');


        gd_debug("완료");
        exit();
    }

    public function setChangeRollback($orderNo){
        $rslt1 = DBUtil2::update(DB_ORDER_GOODS, ['orderStatus'=>'s1', 'handleSno'=>0], new SearchVo(" orderStatus='e1' and orderNo=? ",$orderNo));
        gd_debug($orderNo. ' : ' .$rslt1);
        DBUtil2::delete(DB_ORDER_GOODS, new SearchVo(" orderStatus='p3' and orderNo=? ",$orderNo));
    }


    public function setLateToPre($orderNo){
        gd_debug("## 선불로 변경 {$orderNo}");
        $rslt1 = DBUtil2::runSql("update es_orderGoods set goodsDeliveryCollectFl='pre' where orderNo={$orderNo}");
        gd_debug($rslt1);
        $rslt2 = DBUtil2::runSql("update es_orderDelivery set deliveryCollectPrice=0 ,deliveryCollectFl='pre' where orderNo={$orderNo}");
        gd_debug($rslt2);
    }

    public function asianaHistorySet(){
        $service = SlLoader::cLoad('scm','ScmAsianaService');
        $refreshList = DBUtil2::runSelect("select distinct companyId from sl_asianaOrderHistory");
        foreach($refreshList as $data){
            $service->saveEmpAllHistory($data['companyId']);
        }
    }

    public function setReOrder(){
        $imsProjectService = SlLoader::cLoad('imsv2', 'ImsProjectService');
        //$imsProjectService->reOrder(529, 50, 26, '2026-04-24', '2026-08-14');
    }

    public function setReorderSchedule(){
        $map = [
            'custSample' => '고객샘플확보',
            'inspection' => '현장조사',
            'meeting' => '미팅',
            'plan' => '기획',
            'proposal' => '제안서',
            'sampleOrder' => '샘플지시서',
            'sampleComplete' => '샘플완료',
            'sampleReview' => '샘플리뷰',
            'sampleInform' => '샘플발송',
            'sampleConfirm' => '샘플확정',
        ];
        $map2 = [
            'ex','cp','tx'
        ];

        //1. 리오더기성복 프로젝트 가져오기 (상태가 발주까지 60 )
        //2. 걔들 EXT의 CP TX를 확인
        $sql = "select * from sl_imsProject where projectType in (1,3,7,4) ";
        $list = DBUtil2::runSelect($sql);
        foreach($list as $prj){
            $projectExt = SlCommonUtil::setDateBlank(DBUtil2::getOne(ImsDBName::PROJECT_EXT, "projectSno", $prj['sno']));

            foreach($map as $mapKey => $mapValue){
                $isEmpty = true;
                foreach ($map2 as $prefix){
                    //gd_debug($projectExt[$prefix.ucfirst($mapKey)]);
                    if(!empty($projectExt[$prefix.ucfirst($mapKey)])){
                        $isEmpty = false;
                    }
                }
                if($isEmpty){
                    DBUtil2::update(ImsDBName::PROJECT_EXT, ['tx'.ucfirst($mapKey) => '해당없음' ], new SearchVo('sno=?', $projectExt['sno']));
                }
            }
        }
    }

    public function setScheduleMig(){
        $projectList = DBUtil2::getList(ImsDBName::PROJECT, '1', '1');
        gd_debug('스케쥴 마이그레이션');
        gd_debug('없는 EXT 만들기');
        foreach($projectList as $project){
            $projectExtList = DBUtil2::getList(ImsDBName::PROJECT_EXT, 'projectSno', $project['sno']);
            if(empty($projectExtList)){
                DBUtil2::insert(ImsDBName::PROJECT_EXT, [
                    'projectSno' => $project['sno']
                ]);
            }
        }

        $sql = "SELECT * FROM sl_imsAddInfo where fieldDiv in (
         'plan',
         'proposal',
         'sampleOrder',
         'custSampleInform',
         'order'
        )";

        $list = DBUtil2::runSelect($sql);

        foreach($list as $each){
            $updateData = [];
            $updateData['projectSno'] = $each['projectSno'];
            foreach(ImsCodeMap::PROJECT_SCHEDULE_TYPE as $scTypeKey => $scType){
                $updateData[ $scTypeKey.ucfirst($each['fieldDiv']) ] = $each[$scType['type']];
                if( 'st' === $scTypeKey && !empty($each['completeDt']) && '0000-00-00' !== $each['completeDt'] ){
                    $updateData[ $scTypeKey.ucfirst($each['fieldDiv']) ] = 2; //완료일이 비어있지 않으면 완료상태이다.
                }
            }
            //gd_debug('========> ' . $each['projectSno']);
            //gd_debug($updateData);
            $rslt = DBUtil2::update(ImsDBName::PROJECT_EXT,$updateData, new SearchVo('projectSno=?', $each['projectSno']));
            gd_debug($rslt);
        }
        
        //gd_debug('코멘트 마이그레이션');
        /*DBUtil2::update(ImsDBName::PROJECT_COMMENT,[
            'commentDiv' => 'sampleInform'
        ],new SearchVo('commentDiv=?','custSampleInform'));*/

        gd_debug('발주DL 마이그레이션');
        foreach($projectList as $project){
            $updateData = [
                'exProductionOrder' => $project['customerOrderDeadLine']
                , 'txProductionOrder' => $project['customerOrderDeadLineText']
            ];

            //생산 정보가 있으면 발주일까지 등록한다.
            $productionRegDt = DBUtil2::runSelect("select regDt from sl_imsProduction where projectSno={$project['sno']} order by regDt desc")[0]['regDt'];
            if(!empty($productionRegDt)){
                //gd_debug( $project['sno'] . ':' . $productionRegDt );
                $updateData['cpProductionOrder'] = $productionRegDt;
            }
            $rslt = DBUtil2::update(ImsDBName::PROJECT_EXT,$updateData,new SearchVo('projectSno=?', $project['sno']));
            gd_debug($rslt);
        }
    }


    /**
     * 입찰 형태
     */
    public function migInterface() {
        $nowDate = date('Ymd');
        //gd_debug('입찰 타입 마이그레이션');
        //DBUtil2::runSql("update sl_imsProject set bidType2='single'");
        //DBUtil2::runSql("update sl_imsProject set bidType2='bid' where bidType='입찰' or projectType = '2'");

        //샘플 투입일 초기화. (사전에 백업하기)
        $backupRslt = DBUtil2::runSql("create table zzz_imsAddInfo{$nowDate} select * from sl_imsAddInfo");
        gd_debug( '프로젝트 일정관리 백업 : ' . $backupRslt);
        if(!empty($backupRslt)){
            $addInfoUpdate = DBUtil2::update(ImsDBName::PROJECT_ADD_INFO, [
                'expectedDt' => '0000-00-00',
                'completeDt' => '0000-00-00',
                'alterText' => '',
            ], new SearchVo('fieldDiv=?', 'sampleIn'));
            gd_debug('프로젝트 추가 정보 업데이트 : '. $addInfoUpdate);
        }

        //ext 생성 ==> 쿼리 문에 있음.
        /*$list = DBUtil2::runSelect("select sno from sl_imsProject where projectStatus in (10,15)");
        foreach($list as $each){
            DBUtil2::runSql();
        }*/


        //영업 준비 중인건
        //협상단계  15 => create SalseExt => wait
        //진행준비     => SalseExt ==> proc
        //고객사미팅   => SalseExt

        //기획 ~ 발주완료 => 영업완료

        //보류(확정)/보류(미확정)
        //=> 아예 안만든다.


        //DBUtil2::update();
        //10,15,16



        //비딩 타입 마이그 레이션 .
        //기본 전체는 '단독 : 3'
        //공개 입찰인경우 혹은 입찰인 경우  => 입찰 : 1
        //bidType2
    }


    public function oldMig(){
        //작지 납기일. 작성일 등 정제
        //$this->refineProductMsDeliveryDt();

        /*'' => '확인중',
        'bid' => '입찰',
        'costBid' => '비딩',
        'single'  => '단독',*/

        //DBUtil2::getOne(ImsDBName::PROJECT, '');

        //전산작지 writeDt 마이그
        /*$eworkList = DBUtil2::getList(ImsDBName::EWORK, '1','1');
        foreach($eworkList as $each){
            $saveData['writeDt'] = $each['regDt'];
            DBUtil2::update(ImsDBName::EWORK, $saveData, new SearchVo('sno=?', $each['sno']));
        }*/

        //$this->setRefineFactory(); //생산처 정보 정제
        //$this->setRefineSpecData();

        //$this->setHkOrderChange
        //$this->setMasterStyle();
        //$this->setExchange(); //생산가 견적 환율 정보 업데이트

        //$this->setNego();
        //$this->setCostMig(); //생산가 마이그

        //$this->setBtStatus();
        //$this->setBtStatus2();
        //$this->setSync();
        //$this->setFabricAdd();
        //$this->setFabric();
        //$this->setEstimate();
        //$this->setQb();

        //$this->setProjectStatus();
        //$this->setImsProduce();
        //$this->setImsFileCustomer();

        //$this->setProductSeason(); // 상품 시즌 마이그
        //$this->setProjectSeason(); // 프로젝트 시즌 마이그
        //setProduceShipDt (선적일자 마이그)

        //$this->migFabricRequest();
        //$this->set3plPrdMig(); //3PL 을 프로젝트 단위로 관리.
        //$this->setImsProjectStatusParam(); //3PL 을 프로젝트 단위로 관리.

        //프로젝트 필드 마이그레이션
        //$this->setProjectFieldMigration();
        //미팅데이터를 코멘트로 마이그레이션 -> 24/07/18
        //$this->setMeetingMigration();

        //3PL 속성부여
        //$erpService = SlLoader::cLoad('erp','erpService');
        //$erpService->set3PlAttribute();

        //정보 개선
        //$this->infoRefine();
    }


    /**
     * 정보 개선
     */
    public function infoRefine(){
        $sql = "SELECT sno, addedInfo, LENGTH(`addedInfo`) FROM `sl_imsProject` WHERE addedInfo is not null and LENGTH(`addedInfo`) > 1071 ORDER BY LENGTH(`addedInfo`) DESC";
        $list = DBUtil2::runSelect($sql,null,false);
        $rslt = [];
        foreach($list as $each){
            $infoList = json_decode($each['addedInfo'],true);
            foreach($infoList as $key => $info){
                if(!empty( $info )){
                    $rslt[$key][] = $info;
                }
            }
        }
        foreach( $rslt as $key => $data ){
            gd_debug($key.' '.ImsJsonSchema::ADD_INFO[$key]);
            gd_debug($data);
        }
    }
    /**
     * 정보 개선 (고객)
     */
    public function infoRefine2(){
        $sql = "SELECT sno, addedInfo, LENGTH(`addedInfo`) FROM `sl_imsCustomer` WHERE addedInfo is not null and LENGTH(`addedInfo`) > 92 ORDER BY LENGTH(`addedInfo`) DESC";
        $list = DBUtil2::runSelect($sql,null,false);
        $rslt = [];
        foreach($list as $each){
            $infoList = json_decode($each['addedInfo'],true);
            foreach($infoList as $key => $info){
                if(!empty( $info )){
                    $rslt[$key][] = $info;
                }
            }
        }

        foreach( $rslt as $key => $data ){
            gd_debug($key.' '.ImsJsonSchema::CUSTOMER_ADDINFO[$key]);
            gd_debug($data);
        }
    }

    /**
     * 미리 입력해둔 사이즈 스펙 마이그레이션
     */
    public function setRefineSpecData() {
        gd_debug( '사이즈 스펙 마이그레이션 ' );
        //$sql="update sl_imsEwork set specData = beforeSpecData";
        //$rslt = DBUtil2::runSql($sql);
        //gd_debug('복원:'.$rslt);

        $list = DBUtil2::runSelect("select * from sl_imsEwork where specData <> '' and specData is not null", null, false);
        foreach($list as $each){

            $beforeSpecData = $each['specData']; //원본
            $specList = json_decode($each['specData'],true);
            foreach($specList as $specInfo){

                $productBasicInfo = [
                    'specRange' => $specInfo['specRange'],
                    'standard' => $specInfo['standard'],
                    'specData' => [],
                ];

                $refineSpecData = [];
                foreach($specInfo['specData'] as $specEach){
                    $productBasicInfo['specData'][] = [
                      'title' => $specEach['title'],
                      'unit' => $specEach['unit'],
                    ];

                    $refineSpecData[] = $specEach;
                }

                DBUtil2::update(ImsDBName::PRODUCT, ['sizeSpec'=>json_encode($productBasicInfo)], new SearchVo('sno=?', $each['styleSno']));
                DBUtil2::update(ImsDBName::EWORK, [
                    'specData'=>json_encode($refineSpecData),
                    'beforeSpecData'=>$beforeSpecData //이전 스펙데이터
                ], new SearchVo('sno=?', $each['sno']));
            }

            /*DBUtil2::update(ImsDBName::EWORK,[
                'specData' => $each['specData'],
                'beforeSpecData' => $each['specData']
            ], new SearchVo('sno=?',$each['sno'])); //원본 저장 및 실제 작지 스펙데이터로 정제


            $specList = json_decode($each['specData'],true);

            $sizeSpec = '';
            DBUtil2::update(ImsDBName::PRODUCT,[
                'sizeSpec' => $sizeSpec,
                'beforeSpecData' => $each['specData']
            ], new SearchVo('sno=?',$each['sno']));

            gd_debug($spec);*/
        }
    }

    /**
     * 한국타이어 과년재고 빼기 위한 작업
     */
    public function setHkOrderChange(){

        //gd_debug($list);
        //하려면 이제 manual service로~~ $manualService = SlLoader::cLoad('godo','manualService','sl');
        $manualService = SlLoader::cLoad('godo','manualService','sl');
        $manualService->setHkOrderRefine(95250, 3310, 'MSTSTC32');
        $manualService->setHkOrderRefine(95196, 3310, 'MSTSTC32');

        //$manualService->setHkOrderRefine(94830, 6625, '24FWMHANPTTS30');
        //$manualService->setHkOrderRefine(94831, 3231, '24FWMHANPTTS28');

        //$manualService->setTkeOrderRefine(93058, 6710, '24FWMHANVEHK100', 100); //점퍼 실수 동점 100 조끼 100 (MSHKSC24, 6618)

        //$manualService->setTkeOrderRefine(94802, 6625, '24FWMHANPTTS30', 100); //점퍼 실수 동점 100 조끼 100 (MSHKSC24, 6618)

        //$manualService->setTkeOrderRefine(94078, 3302, '24FWMHANJPHK100', 100); //점퍼 실수 동점 100 조끼 100 (MSHKSC24, 6618)
        //$manualService->setTkeOrderRefine(93970, 6618, 'MSHKSC31', 100); //점퍼 실수 동점 100 조끼 100 (MSHKSC24, 6618)

        //$manualService->setTkeOrderRefine(93852, 6628, '24FWMHANJPHK110', 110); //점퍼 실수 동점 105
        //$manualService->setTkeOrderRefine(93880, 6619, 'MSHKSC32', 105); //점퍼 실수 동점 105
        //$manualService->setTkeOrderRefine(93859, 3318, 'MSHKSC26', 110); //점퍼 실수 동점 105


/*        $manualService->setTkeOrderRefine(94189, 3302, '24FWMHANJPHK100', 100); //점퍼 실수 동점 100 조끼 100 (MSHKSC24, 6618)
        $manualService->setTkeOrderRefine(94191, 3302, '24FWMHANJPHK95', 95); //점퍼 실수 동점 95 조끼 100 (MSHKSC16)
        $manualService->setTkeOrderRefine(94193, 3300, 'MSHKS038', 90); //점퍼 실수 동점 90 카라티 90 (MSHKSC15)

        $manualService->setTkeOrderRefine(94146, 3303, '24FWMHANJPHK105', 105); //점퍼 실수 동점 90 카라티 90 (MSHKSC15)
        $manualService->setTkeOrderRefine(94147, 3302, '24FWMHANJPHK100', 100); //점퍼 실수 동점 90 카라티 90 (MSHKSC15)
        $manualService->setTkeOrderRefine(94148, 6700, '24FWMHANJPHK95', 95); //점퍼 실수 동점 90 카라티 90 (MSHKSC15)*/

//동계 점퍼 90 :    MSHKS038  3300
//동계 점퍼 95 :    24FWMHANJPHK95   6700
//동계 점퍼 100 :   24FWMHANJPHK100  3302
//동계 점퍼 105 :   24FWMHANJPHK105  3303
//동계 점퍼 110 :   24FWMHANJPHK110  6628

        //$manualService->setTkeOrderRefine(92626, 6641, '24FWMHANTSHK100', 100); //카라티 실수 100 => 조끼 100 (MSHKSC24, 6618)
        //$manualService->setTkeOrderRefine(93997, 6637, '24FWMHANPTHK34', 34);  //바지 실수 동바지 34 춘추 34 (MSHKSB11, 1540)
        //$manualService->setTkeOrderRefine(94031, 6637, '24FWMHANPTHK34', 34);  //바지 실수 동바지 34 춘추 34 (MSHKSB11, 1540)


        //$manualService->setHkOrderRefine(94002, 3302, 'MSTSTC26');

        /*$this->setExclude('MSHKS052');
        $this->setExclude('MSHKS053');
        $this->setExclude('MSHKS040');
        $this->setExclude('MSHKS039');*/
        
    }
    
    
    public function setExclude($code){
        $rslt1 = DBUtil2::runSql("insert into sl_3plProductExclude values ('{$code}')");
        $rslt2 = DBUtil2::delete('sl_3plProduct',new SearchVo('thirdPartyProductCode=?', $code));
        gd_debug($code.' / 저장 : '. $rslt1. ' / 삭제 : '. $rslt2);
    }


    /**
     * 생산처 정보 업데이트
     */
    public function setRefineFactory(){
        $targetList = DBUtil2::getListBySearchVo(ImsDBName::PRODUCTION, new SearchVo('(produceStatus=? or produceStatus=99)','30')); //30, 99
        foreach($targetList as $each){
            $updateData = [
                'produceCompanySno' => $each['produceCompanySno']
            ];
            //DBUtil2::update(ImsDBName::PROJECT, $updateData, new SearchVo('sno=?',$each['projectSno'])); //프로젝트 (마지막 생산처)
            //수량 업데이트.
            if( !empty($each['totalQty'])  ){
                $updateData['prdExQty']  = $each['totalQty'];
                DBUtil2::update(ImsDBName::PRODUCT, $updateData, new SearchVo('sno=?',$each['styleSno']));   //스타일
            }
        }
    }

    /**
     * 처리 완료 일 기준으로 환율 없는 것 채워넣기.
     */
    public function setExchange(){

        // 8월 8일 이후 건 부터 진행.
        $list = DBUtil2::getListBySearchVo(ImsDBName::ESTIMATE, new SearchVo(" completeDt >= '2024-08-08 00:00:00' and (reqStatus=3 or reqStatus=5) ", 3), false);
        foreach($list as $each){

            $contents = json_decode($each['contents'], true);

            if(empty($contents['exchange'])){
                $exchange = DBUtil2::getOneSortData('es_exchangeRateConfig', 'left(regDt,10)=?', substr($each['completeDt'],0,10), 'regDt desc');
                $contents['exchange'] = $exchange['exchangeRateConfigUSDManual'];
                $contents['exchangeDt'] = substr($each['completeDt'],0,10);
                $encodeContents = json_encode($contents);
                $updateRslt = DBUtil2::update(ImsDBName::ESTIMATE, [
                    'contents'=>$encodeContents
                ], new SearchVo('sno=?',$each['sno']));
                gd_debug( $each['sno'].':'.$updateRslt);
            }
            ///gd_debug( $contents['exchange'].':'.$contents['exchangeDt'] );
        }
    }

    /**
     * 마스터 스타일 만들기
     */
    public function setMasterStyle(){
        $searchVo = new SearchVo('delFl=?','n');
        $searchVo->setWhere('masterStyleSno=0');
        $searchVo->setLimit(10);

        $list = DBUtil2::getListBySearchVo(ImsDBName::PRODUCT, $searchVo);

        foreach($list as $each){

        }

    }
    
    public function setNego(){
        //협상 단계 건.
        $list = DBUtil2::getList(ImsDBName::PROJECT, 'projectStatus', '15');

        foreach($list as $project){
            $saveData = SlCommonUtil::getAvailData($project,[
                'customerSno','projectSno'
            ]);
            $saveData['issueType'] = 'meeting';
            $saveData['subject'] = '협상 단계 내용';
            $saveData['contents'] = $project['workMemo'];
            DBUtil2::insert(ImsDBName::CUSTOMER_ISSUE,$saveData);
        }
        gd_debug('네고 완료.');
    }

    /**
     *
     * 생산가 통합 마이그레이션
     */
    public function setCostMig(){
        $backupDateTime = date('ymdhis');
        //기존 자료 백업
        DBUtil2::runSql("create table zzz_imsProjectProduct_{$backupDateTime} select * from sl_imsProjectProduct"); //상품 백업
        DBUtil2::runSql("create table zzz_imsEstimate_{$backupDateTime} select * from sl_imsEstimate"); //견적 백업

        $prdList = DBUtil2::getList(ImsDBName::PRODUCT, 'delFl', 'n');
        foreach($prdList as $prd){

            $estimateList = DBUtil2::getList(ImsDBName::ESTIMATE, ' styleSno', $prd['sno'], 'regDt asc');

            foreach($estimateList as $key => $estimate){
                //요청회차 변경
                //생산가가 확정 되어있으면 확정 번호 빼고는 모두 처리완료 상태로 변경. (견적서는 확정 상태이다)
                //prdCostConfirmSno , estimateConfirmSno
                if( 'estimate' === $estimate['estimateType'] && 5 == $estimate['reqStatus'] ){
                    $saveData['reqStatus'] = 3;
                }
                $saveData['reqCount']=$key+1;
                DBUtil2::update(ImsDBName::ESTIMATE, $saveData, new SearchVo('sno=?', $estimate['sno'])); //요청 회차 업데이트
            }

            if( !empty($prd['prdCostConfirmSno']) ){
                //확정생산가 = 견적 선택 (견적=확정 동일하게 맞춘다)
                $prdSave['estimateCost'] = $prd['prdCost'];
                $prdSave['estimateCount'] = $prd['prdCount'];
                $prdSave['estimateConfirmSno'] = $prd['prdCostConfirmSno'];
                $prdSave['estimateConfirmManagerSno'] = $prd['prdCostConfirmManagerSno'];
                $prdSave['estimateConfirmDt'] = $prd['prdCostConfirmDt'];
                $prdSave['estimateStatus'] = '2';
                DBUtil2::update(ImsDBName::PRODUCT, $prdSave, new SearchVo('sno=?', $prd['sno'])); //요청 회차 업데이트
            }

        }

        gd_debug('생산가 통합 완료.');

    }

    /**
     * 미팅데이터를 코멘트로 마이그레이션 -> 24/07/18
     */
    public function setMeetingMigration(){
        $list=DBUtil2::getList(ImsDBName::NEW_MEETING,'1','1');
        foreach($list as $key => $each){
            $saveData['issueType'] = 'meeting';
            $custInfo = DBUtil2::getOne(ImsDBName::CUSTOMER, 'sno', $each['customerSno']);
            $saveData['customerSno'] = $each['customerSno'];
            $saveData['meetingFiles'] = $each['meetingFiles'];
            $saveData['regManagerSno'] = $each['regManagerSno'];
            $saveData['subject'] = $each['purpose'].' '. $each['meetingDt'];
            $contents = [];
            $contents[] = $each['meetingDt'].' '.$each['meetingTime'];
            $contents[] = $each['attend'];
            $contents[] = $each['location'];
            $contents[] = $each['meetingContents'];
            $saveData['contents'] = implode(',',$contents);
            DBUtil2::insert(ImsDBName::CUSTOMER_ISSUE, $saveData);
        }
    }
    
    public function setProjectFieldMigration(){
        $list=DBUtil2::getList(ImsDBName::PROJECT,'1','1');
        foreach($list as $key => $each){
            DBUtil2::merge(ImsDBName::PROJECT_ADD_INFO, [
                'expectedDt' => $each['planDt'],
                'completeDt' => $each['planEndDt'],
            ], new SearchVo("projectSno={$each['sno']} AND fieldDiv=? ",'plan'));

            DBUtil2::merge(ImsDBName::PROJECT_ADD_INFO, [
                'expectedDt' => $each['proposalDt'],
                'completeDt' => $each['proposalEndDt'],
            ], new SearchVo("projectSno={$each['sno']} AND fieldDiv=? ",'proposal'));

/*            DBUtil2::merge(ImsDBName::PROJECT_ADD_INFO, [
                'expectedDt' => $each['customerOrderDeadLine'],
            ], new SearchVo("projectSno={$each['sno']} AND fieldDiv=? ",'orderComplete'));*/
        }
    }


    public function setImsProjectStatusParam(){
        $list = DBUtil2::getList(ImsDBName::STATUS_HISTORY, 'customerSno', '0');
        foreach($list as $each){
            $projectData = DBUtil2::getOne(ImsDBName::PROJECT, 'sno', $each['projectSno']);
            DBUtil2::update(ImsDBName::STATUS_HISTORY,['customerSno'=>$projectData['customerSno']],new SearchVo('sno=?',$each['sno']));
        }
        gd_debug('complete');
        //1. 일단 마이그 하고
        //2. Status 저장할 때 CustomerSno저장.

    }


    public function set3plPrdMig(){
        $customerList = DBUtil2::getList(ImsDBName::CUSTOMER, '1', '1');
        foreach($customerList as $customer){
            DBUtil2::update(ImsDBName::PROJECT,[
                'use3pl' => $customer['use3pl'],
                'useMall' => $customer['useMall'],
            ], new SearchVo('customerSno=?',$customer['sno']));
        }
        gd_debug('use3pl Mig OK');
    }

    public function migFabricRequest(){
        gd_debug("== 원단 요청 마이그. ==");

        DBUtil2::getListLoopAction(ImsDBName::FABRIC, new SearchVo('reqStatus > 0','1'),function($key, $each){
            /*$saveData = SlCommonUtil::getAvailData($each,[
                'reqStatus',
                'reqManagerSno',
                'reqCount',
                'reqFactory',
                'reqDeliveryInfo',
                'resDeliveryInfo',
                'resMemo',
                'completeDeadLineDt',
                'completeDt',
                'customerSno',
                'projectSno',
                'styleSno',
            ]);*/
            $saveData = $each;
            $saveData['fabricSno'] = $each['sno'];
            $saveData['reqType'] = 2;

            unset($saveData['sno']);

            $sno = DBUtil2::insert(ImsDBName::FABRIC_REQ, $saveData);
            //INSERT 후 reqDt 보고 regDt를 강제 업데이트!
            $rslt = DBUtil2::runSql("update sl_imsFabricRequest set regDt = '{$each['reqDt']}' where sno = {$sno}");
            gd_debug($sno . ' : ' . $rslt);
        });

        /*
        DBUtil2::getListLoopAction(ImsDBName::PREPARED, new SearchVo('preparedStatus in (0,1) and preparedType=?','bt'),function($key, $each){
            //DBUtil2::update(ImsDBName::PROJECT, ['projectStatus'=>90]  ,new SearchVo('sno=?',$each['sno']));
            gd_debug($each);
        });
        */
    }

    public function setBtStatus2(){
        //이전 버전 프로젝트 단위 관리 정보입력건
        $fabricList = DBUtil2::getList(ImsDBName::FABRIC, 'fabricMemo' , '이전 버전 프로젝트 단위 관리 정보입력건');
        foreach( $fabricList as $fabricInfo ){
            $prd = DBUtil2::getOne(ImsDBName::PRODUCT, 'productionStatus > 0 and sno', $fabricInfo['styleSno']);
            if(!empty($prd)){
                DBUtil2::update(ImsDBName::FABRIC,[
                    'btStatus' => 2
                ], new SearchVo('sno=?', $fabricInfo['sno']));
            }
        }
    }

    public function setBtStatus(){
        $imsService = SlLoader::cLoad('ims', 'imsService');
        //퀄리티가 없는 완료된 BT 는 다시 0 처리.
        $sql = "select a.* from sl_imsProjectProduct a where a.delFl='n' "; //전체 상품중.
        $prdList = DBUtil2::runSelect($sql);
        $projectList = [];
        foreach($prdList as $prd) {
            $fabricCnt = DBUtil2::getCount(ImsDBName::FABRIC, new SearchVo('styleSno=?', $prd['sno']));
            if( 0 >= $fabricCnt ){
                DBUtil2::update(ImsDBName::PRODUCT,[
                    'btStatus' => 0
                ] ,new SearchVo('sno=?', $prd['sno']));
                $projectList[$prd['projectSno']]=true;
            }
        }
        //프로젝트 정보 동기화
        foreach($projectList as $prjSno => $bool){
            $imsService->setSyncStatus($prjSno, __METHOD__);
        }
    }


    public function setFabricAdd(){
        //생산중 또는 생산완료 건 중 Fabric이 없는 건
        //$prdList = DBUtil2::getList(ImsDBName::PRODUCT, 'delFl' , 'n');

        $imsService = SlLoader::cLoad('ims', 'imsService');

        $sql = "select a.* from sl_imsProjectProduct a where a.delFl='n' and a.fabricStatus = 0  and a.productionStatus in (1,2) "; //생산중인것 중 아무것도 없는 것.
        $prdList = DBUtil2::runSelect($sql);
        $projectList = [];

        foreach($prdList as $prd){
            $fabricCnt = DBUtil2::getCount(ImsDBName::FABRIC, new SearchVo('styleSno=?', $prd['sno']));
            if( 0 >= $fabricCnt ){
                $saveData = SlCommonUtil::getAvailData($prd, [
                    'customerSno',
                    'projectSno',
                ]);
                $saveData['styleSno'] = $prd['sno'];
                $saveData['fabricMemo'] = '이전 버전 프로젝트 단위 관리 정보입력건.';
                $saveData['fabricStatus'] = 2; //원단 완료상태.
                $saveData['btStatus'] = 2; //BT 완료
                $saveData['fabricName'] = '원단확보';
                DBUtil2::insert(ImsDBName::FABRIC, $saveData);
                $projectList[$prd['projectSno']]=true;

                DBUtil2::update(ImsDBName::PRODUCT,[
                    'btStatus' => 2
                ] ,new SearchVo('sno=?', $prd['sno']));

            }
        }

        //프로젝트 정보 동기화
        foreach($projectList as $prjSno => $bool){
            $imsService->setSyncStatus($prjSno, __METHOD__);
        } 

    }


    public function setSync(){
        $imsService = SlLoader::cLoad('ims', 'imsService');
        $accPrj = DBUtil2::getList(ImsDBName::PROJECT, '1', '1');
        foreach($accPrj as $prjSno => $prj){
            $imsService->setSyncStatus($prj['sno'], __METHOD__);
        }
        gd_debug('프로젝트 싱크 완료');
    }

    public function setFabric(){
        $imsService = SlLoader::cLoad('ims', 'imsService');
        $tmpList = DBUtil2::runSelect("select * from zzz_imsProject");

        foreach( $tmpList as $each ){
            if( !empty($each['fabricStatus']) ){
                //fabricNational
                $prdList = DBUtil2::getList(ImsDBName::PRODUCT,"delFl='n' and projectSno", $each['sno']);
                foreach($prdList as $prd){
                    $saveData = SlCommonUtil::getAvailData($prd, [
                        'customerSno',
                        'projectSno',
                    ]);
                    $saveData['styleSno'] = $prd['sno'];
                    $saveData['fabricMemo'] = '이전 버전 프로젝트 단위 관리 정보입력건';
                    $saveData['fabricStatus'] = $each['fabricStatus'];
                    
                    //BT는 생산완료된것에 한하여 모두 확정 (80.90)
                    if( 80 == $each['projectStatus'] || 90 == $each['projectStatus'] ){
                        $saveData['btStatus'] = 2;
                    }

                    //제조국에 따라 모두 입력
                    if( 1 & $each['fabricNational'] ){
                        $saveData['fabricName'] = '한국원단';
                        $saveData['makeNational'] = 'kr';
                        DBUtil2::insert(ImsDBName::FABRIC, $saveData);
                    }
                    if( 2 & $each['fabricNational'] ){
                        $saveData['fabricName'] = '중국원단';
                        $saveData['makeNational'] = 'cn';
                        DBUtil2::insert(ImsDBName::FABRIC, $saveData);
                    }
                    if( 4 & $each['fabricNational'] ){
                        $saveData['fabricName'] = '시장원단';
                        $saveData['makeNational'] = 'market';
                        DBUtil2::insert(ImsDBName::FABRIC, $saveData);
                    }

                    if( empty($each['fabricNational']) ){
                        $saveData['fabricName'] = '원단확보';
                        unset($saveData['makeNational']);
                        DBUtil2::insert(ImsDBName::FABRIC, $saveData);
                    }
                }

                //update ? fabricNational
                $imsService->setSyncStatus($prjSno, __METHOD__);
            }
        }
    }


    public function setEstimate2(){
        gd_debug("== 가견적 요청2.  ==");

        $accPrj = [];
        $imsService = SlLoader::cLoad('ims', 'imsService');

        $searchVo = new SearchVo();
        $searchVo->setWhere("preparedType in ('estimate','cost')");
        $list = DBUtil2::getListBySearchVo(ImsDBName::PREPARED, $searchVo, false);

        foreach($list as $key => $each){
            $prj = DBUtil2::getOne(ImsDBName::PROJECT, 'sno', $each['projectSno']);
            //DBUtil2::update(ImsDBName::PROJECT, ['projectStatus'=>90]  ,new SearchVo('sno=?',$each['sno']));
            //gd_debug($each);
            $each['contents'] = json_decode($each['contents'],true);

            foreach( $each['contents']['productList'] as $prdKKey => $prd ){

                $saveData['estimateCost'] = $prd['prdCost'];//이게 가견적 / 생산가. 다른데 동일할수도 있다 ?
                $saveData['customerSno'] = $prj['customerSno'];
                $saveData['projectSno'] = $prj['sno'];
                $saveData['styleSno'] = $prd['sno'];
                $saveData['estimateType'] = $each['preparedType'];

                $accPrj[$saveData['projectSno']] = true;

                $contents = SlCommonUtil::getAvailData($prd, ['totalCost' ,
                    'fabricCost' ,
                    'subFabricCost' ,
                    'laborCost' ,
                    'marginCost' ,
                    'dutyCost',
                    'managementCost',
                    'prdMoq',
                    'priceMoq',
                    'addPrice',
                    'produceType',
                    'producePeriod',
                    'deliveryType',]);
                $contents['fabric'] = $prd['fabric'];
                $contents['subFabric'] = $prd['subFabric'];


                $saveData['contents'] = json_encode($contents);

                $migMap = [
                    0 => 1, //요청
                    1 => 1, //처리중
                    2 => 3, //처리완료
                    3 => 4, //처리불가
                    4 => 5, //승인(확정)
                    5 => 6, //반려
                ];
                $saveData['reqStatus'] = $migMap[$each['preparedStatus']];
                $saveData['reqManagerSno'] = $each['regManagerSno']; //요청자.

                $estimateCount = DBUtil2::getCount(ImsDBName::ESTIMATE, new SearchVo([
                    'styleSno=?',
                    'estimateType=?',
                ],[
                    $saveData['styleSno'],
                    $saveData['estimateType'],
                ]));
                $saveData['reqCount'] = $estimateCount + 1;
                $saveData['estimateCount'] = $prd['prdExQty']; //요청수량
                $saveData['reqFactory'] = $each['produceCompanySno']; //생산처.
                $saveData['reqMemo'] = $each['reqMemo']; //요청 메모.
                $saveData['resMemo'] = $each['procMemo']; //응답자 메모.
                $saveData['completeDeadLineDt'] = $each['deadLineDt']; //처리완료일.
                $saveData['regManagerSno'] = $each['regManagerSno']; //요청자.

                //승인되었는데 금액이 0인 값은
                $inserSno = DBUtil2::insert(ImsDBName::ESTIMATE, $saveData);

                //승인값 스타일에 저장
                if( 5 == $saveData['reqStatus'] && $saveData['estimateCost'] > 0  ){
                    if( 'cost' === $saveData['estimateType'] ){
                        $savePrdData['prdCostConfirmSno'] = $inserSno; //확정번호
                        $savePrdData['prdCostConfirmManagerSno'] = 1; //확정 작업자
                        $savePrdData['prdCost'] = $saveData['estimateCost'];//확정가격.
                        $savePrdData['prdCostStatus'] = 2;
                        $savePrdData['estimateCost'] = 0;
                    }else{
                        $savePrdData['estimateConfirmSno'] = $inserSno; //확정번호
                        $savePrdData['estimateConfirmManagerSno'] = 1; //확정 작업자
                        $savePrdData['estimateCost'] = $saveData['estimateCost'];//확정가격.
                        $savePrdData['estimateStatus'] = 2;
                    }
                    DBUtil2::update(ImsDBName::PRODUCT, $savePrdData, new SearchVo('sno=?', $saveData['styleSno'])); //확정되었을 때만.
                }else{
                    if( 'cost' === $saveData['estimateType'] ){
                        $savePrdData['prdCost'] = 0; //확정이 아니면 생산가 초기화
                        $savePrdData['prdCostConfirmSno'] = ''; //확정번호
                        $savePrdData['prdCostStatus'] = 0;
                    }else{
                        $savePrdData['estimateCost'] = 0; //확정이 아니면 가견적 초기화
                        $savePrdData['estimateConfirmSno'] = ''; //확정번호
                        $savePrdData['estimateStatus'] = 0;
                    }
                    DBUtil2::update(ImsDBName::PRODUCT, $savePrdData, new SearchVo('sno=?', $saveData['styleSno']));
                }
            }
        }


        //현재상태 싱크.
        foreach($accPrj as $prjSno => $prj){
            $imsService->setSyncStatus($prjSno, __METHOD__);
        }

        //$saveData['styleSno'] = $prj['sno'];

        /*0 => '요청',
        1 => '처리중',  <-- 여기 마이그 중!
        2 => '처리완료',
        3 => '처리불가',
        4 => '승인',   //승인 -> 승인
        5 => '반려', //반려,번복 -> 다시해.*/

    }

    public function setEstimate(){
        gd_debug("== 가견적 요청. ==");

        DBUtil2::getListLoopAction(ImsDBName::PREPARED, new SearchVo('preparedType=?','cost'),function($key, $each){
            $prj = DBUtil2::getOne(ImsDBName::PROJECT, 'sno', $each['projectSno']);
            //DBUtil2::update(ImsDBName::PROJECT, ['projectStatus'=>90]  ,new SearchVo('sno=?',$each['sno']));
            //gd_debug($each);
            $each['contents'] = json_decode($each['contents'],true);

            foreach( $each['contents']['productList'] as $prd ){
                $saveData['customerSno'] = $prj['customerSno'];
                $saveData['projectSno'] = $prj['sno'];
                $saveData['styleSno'] = $prd['sno'];
                $saveData['estimateType'] = $each['preparedType'];
                $contents = ImsJsonSchema::ESTIMATE;
                $contents['fabric'] = $prd['fabric'];
                $contents['subFabric'] = $prd['subFabric'];
                $saveData['contents'] = json_encode($contents);

                $migMap = [
                    0 => 1, //요청
                    1 => 1, //처리중
                    2 => 3, //처리완료
                    3 => 4, //처리불가
                    4 => 5, //승인
                    5 => 6, //반려
                ];
                $saveData['reqStatus'] = $migMap[$each['preparedStatus']];

                $saveData['reqManagerSno'] = $each['regManagerSno']; //요청자.
                $saveData['reqCount'] = $each['reqCnt']; //요청자.
                $saveData['reqCnt'] = $each['reqCnt']; //요청자.
                $saveData['reqFactory'] = $each['produceCompanySno']; //요청자.
                $saveData['reqMemo'] = $each['reqMemo']; //요청자.
                $saveData['resMemo'] = $each['procMemo']; //요청자.
                $saveData['completeDeadLineDt'] = $each['deadLineDt']; //요청자.
                $saveData['regManagerSno'] = $each['regManagerSno']; //요청자.
                DBUtil2::insert(ImsDBName::ESTIMATE, $saveData);
            }
            //$saveData['styleSno'] = $prj['sno'];

            /*0 => '요청',
            1 => '처리중',  <-- 여기 마이그 중!
            2 => '처리완료',
            3 => '처리불가',
            4 => '승인',   //승인 -> 승인
            5 => '반려', //반려,번복 -> 다시해.*/
        }, false);
    }
    



    public function setProjectStatus(){
        gd_debug("== 프로젝트 80(생산관리)를 발주완료 단계로 변경 ==");
        DBUtil2::getListLoopAction(ImsDBName::PROJECT, new SearchVo('projectStatus=?',80),function($key, $each){
            DBUtil2::update(ImsDBName::PROJECT, ['projectStatus'=>90]  ,new SearchVo('sno=?',$each['sno']));
        });
    }

    /**
     * 생산관리 단계의 프로젝트 데이터를 신규 생산관리로 변경 
     * @throws \Exception
     */
    public function setImsProduce(){
        gd_debug("== 생산 마이그레이션 ==");

        //존재하는 프로젝트의 생산단계는 그대로 가져간다.
        //스텝을 파싱해서 필드에 넣는다.
        //delFl = n 인 스타일의 모든 스케쥴 넣기.
        //DBUtil2::runSql("truncate table sl_imsProduction");

        $sql = "select 
     a.*
     , b.customerSno
     , c.msDeliveryDt
     , c.sno as styleSno 
     , c.prdExQty -- 수량
     , b.projectName
from sl_imsProduce a 
join sl_imsProject b 
  on a.projectSno = b.sno 
join sl_imsProjectProduct c 
  on a.projectSno = c.projectSno  
where c.delFl = 'n' 
";
        $list = DBUtil2::runSelect($sql, null, false);

        $insertCnt = 0;

        foreach($list as $src){
            $firstData = []; //승인자.
            $firstData['acceptData'] = [
                'managerNm' => '시스템',
                'acceptDt' => date('Y-m-d')
            ];
            $saveData = $src;
            $saveData['totalQty'] = $src['prdExQty'];
            $saveData['sizeOptionQty'] = json_encode([
                '별첨' => $src['prdExQty']
            ]);
            $saveData['assortConfirm'] = 'p';
            $saveData['workConfirm'] = 'p';
            $saveData['regManagerSno'] = '1';
            $saveData['label'] = $src['projectName'];
            $saveData['msDeliveryDt'] = $src['msDeliveryDt'];

            $parseData = json_decode($src['prdStep'],true);
            foreach( ImsCodeMap::PRODUCTION_STEP as $key => $step ){
                $srcParse = $parseData['prdStep'.(($key+1)*10)];
                $saveData[$step . 'ExpectedDt'] = $srcParse['expectedDt'];
                $saveData[$step . 'CompleteDt'] = $srcParse['completeDt'];
                $saveData[$step . 'Memo'] = $srcParse['memo'];
                $saveData[$step . 'Confirm'] = $srcParse['confirmYn'];

                //초기 기준 데이터
                $firstData['schedule'][$step]['ConfirmExpectedDt'] = gd_isset($srcParse['expectedDt'],'');
                $firstData['schedule'][$step]['CompleteDt'] = gd_isset($srcParse['completeDt'],'');
                $firstData['schedule'][$step]['Memo'] = gd_isset($srcParse['memo'],'');
                $firstData['schedule'][$step]['Confirm'] = gd_isset($srcParse['confirmYn'],'');
            }

            //생산 정보 등록.
            $saveData['firstData'] = json_encode($firstData);
            unset($saveData['sno']);
            DBUtil2::insert(ImsDBName::PRODUCTION, $saveData);
            //gd_debug('생산 마이그 데이터');
            //gd_debug($saveData);

            //스타일 정보 업데이트
            //gd_debug('스타일 마이그 데이터');
            //gd_debug($style);
            DBUtil2::update(ImsDBName::PRODUCT, ['sizeOption'=>json_encode(['별첨'])], new SearchVo('sno=?', $src['styleSno']));

            $insertCnt++;
        }

        gd_debug($insertCnt . ' 개 생성됨');

    }

    /**
     * New IMS
     * 파일 마이그.
     */
    public function setImsFileCustomer(){
        $list = DBUtil2::getList(ImsDBName::PROJECT_FILE, '1', '1');
        foreach($list as $fileInfo){
            if( !empty($fileInfo['projectSno']) ){
                $projectData = DBUtil2::getOne(ImsDBName::PROJECT, 'sno', $fileInfo['projectSno']);
                if( $fileInfo['customerSno'] != $projectData['customerSno'] ){
                    $rslt = DBUtil2::update(ImsDBName::PROJECT_FILE, [
                        'customerSno' => $projectData['customerSno']
                    ], new SearchVo('projectSno=?', $fileInfo['projectSno']));
                    //gd_debug($fileInfo['projectSno'] . ' 업데이트 : ' . $rslt);
                }
            }
        }
    }
    
    
    /**
     * 프로젝트 시즌 마이그레이션
     */
    public function setProjectSeason(){
        $checkMap = [
            'FW' => '동계',
            'SF' => '춘추',
            'SP' => '춘계',
            'SU' => '하계',
            'FA' => '추계',
            'WI' => '동계',
            'SS' => '춘계',
            'ALL' => '전체',
        ];
        $list = DBUtil2::getList(ImsDBName::PRODUCT,'1','1');
        foreach($list as $each){
            $updateData['projectYear'] = substr($each['prdYear'], -2);
            $updateData['projectSeason'] = $each['prdSeason'];
            gd_debug($updateData);
            DBUtil2::update(ImsDBName::PROJECT, $updateData, new SearchVo('sno=?',$each['sno']));
        }
    }

    public function setProductSeason(){
        $list = DBUtil2::getList(ImsDBName::PRODUCT,'1','1');

        $checkMap = [
            'FW' => '동계',
            'SF' => '춘추',
            'SP' => '춘계',
            'SU' => '하계',
            'FA' => '추계',
            'WI' => '동계',
            'SS' => '춘계',
            'ALL' => '전체',
        ];

/*FW 동계 (가을겨울)
SF 춘추  (봄,가을)
Sp 춘계  ( 봄 )
SU 하계  ( 여름 )
FA 추계
WI 동계
SS 춘계*/

        foreach($list as $each){
            //년도 체크
            if( strpos($each['styleCode'], "23") !== false ){
                $updateData['prdYear'] = 2023;
            }
            //시즌 체크
            foreach($checkMap as $checkSeason => $checkSeasonValue){
                //gd_debug($each['productName'] . ' : ' . $checkSeason);
                if( strpos($each['styleCode'], $checkSeason) !== false  ){
                    //if( empty($each['prdSeason']) ){
                        $updateData['prdSeason'] = $checkSeason;
                    //}
                }
            }
            if(!empty($updateData)){
                //gd_debug($updateData);
                DBUtil2::update(ImsDBName::PRODUCT, $updateData, new SearchVo('sno=?',$each['sno']));
            }
        }

    }

    /**
     * 선적일자 마이그레이션
     * @throws \Exception
     */
    public function setProduceShipDt(){

        $list = DBUtil2::getList(ImsDBName::PRODUCE,'1','1');
        foreach($list as $each){
            $prdStep = json_decode($each['prdStep'],true);
            //gd_debug($each['projectSno'] . ' : ' . $prdStep['prdStep60']['expectedDt'] . ' // ' . $prdStep['prdStep60']['completeDt']);
            //gd_debug($prdStep);
            DBUtil2::update(ImsDBName::PRODUCE, [
                'shipExpectedDt' => $prdStep['prdStep60']['expectedDt'],
                'shipCompleteDt' => $prdStep['prdStep60']['completeDt'],
            ], new SearchVo('sno=?',$each['sno']));

            //gd_debug($prdStep['prdStep60']['expectedDt']);
        }

    }


    /**
     * 상품(작지) 납기일 등록 (생산 정보 납기일 정보를 통해 공백 없게하기 )
     * @throws \Exception
     */
    public function refineProductMsDeliveryDt(){
        $list = DBUtil2::getList(ImsDBName::PRODUCTION, 'produceStatus', '30');

        foreach($list as $production){
            //납기일 정제
            $prd = DBUtil2::getOne(ImsDBName::PRODUCT, 'sno', $production['styleSno']);
            if(!empty($production['msDeliveryDt']) && '0000-00-00' != $production['msDeliveryDt'] && (empty($prd['msDeliveryDt']) || '0000-00-00' == $prd['msDeliveryDt'])){
                $rslt = DBUtil2::update(ImsDBName::PRODUCT, ['msDeliveryDt'=>$production['msDeliveryDt']], new SearchVo('sno=?', $production['styleSno']));
                //gd_debug($production['styleSno'] . ' : ' . $production['msDeliveryDt'] . ' ==> ' . $rslt);
            }
            //작지 정제
            $eworkData = DBUtil2::getOne(ImsDBName::EWORK, 'styleSno', $production['styleSno']);
            if(empty($eworkData['writeDt']) || '0000-00-00' == $eworkData['writeDt'] ) $eworkUpdate['writeDt']=$production['regDt']; //작성일 체크 => 없으면 발주일로
            if(empty($eworkData['requestDt']) || '0000-00-00' == $eworkData['requestDt'] ) $eworkUpdate['requestDt']=$production['regDt']; //의뢰일 체크 => 없으면 발주일로
            DBUtil2::update(ImsDBName::EWORK, $eworkUpdate, new SearchVo('sno=?', $eworkData['sno']));
        }
    }


    /**
     * 작업 지시서 마이그레이션
     * @throws \Exception
     */
    public function setEworkMigration(){
        gd_debug('작업지시서 마이그레이션');
        //현재의 스펙 데이터를 Prd SpecData로 넣는다.
        //이워크의 스펙 데이터를 스타일 자체에 넣는다.
        //이워크의 스펙데이터는 사용하지 않게한다. (변경점 기록은 ? 체크해봐야함)
        $prdList = DBUtil2::getList(ImsDBName::PRODUCT, "sizeSpec<>'' and 1", 1, false, false);
        foreach($prdList as $prd){
            $ework = DBUtil2::getOne(ImsDBName::EWORK, 'styleSno', $prd['sno'], true);
            $prdSpec = json_decode($prd['sizeSpec'],true);
            $eworkSpec = json_decode($ework['specData'],true);
            $prdSpec['specData'] = $eworkSpec;
            $rslt = DBUtil2::update(ImsDBName::PRODUCT, ['sizeSpec'=>json_encode($prdSpec)], new SearchVo('sno=?', $prd['sno']));
            gd_debug('#### ' . $prd['sno'] . ' #### RSLT ==> ' . $rslt);
        }

        gd_debug('####### 작지 파일 이력 등록');
        $sql = "select b.customerSno, b.projectSno, a.* from sl_imsEwork a join sl_imsProjectProduct b on a.styleSno = b.sno";
        $eworkList = DBUtil2::runSelect($sql);
        foreach($eworkList as $ework){
            foreach(ImsCodeMap::EWORK_FILE_LIST as $fileDiv){
                $fileInfo = json_decode($ework[$fileDiv]);
                if('fileAi' !== $fileDiv && !empty($fileInfo) ){
                    gd_debug($ework['customerSno'].'/'.$ework['projectSno'].'/'.$ework['sno'].':'.json_encode($fileInfo));
                    DBUtil2::insert(ImsDBName::PROJECT_FILE,[
                        'customerSno' => $ework['customerSno'],
                        'projectSno' => $ework['projectSno'],
                        'styleSno' => $ework['styleSno'],
                        'fileDiv' => $fileDiv,
                        'fileList' => json_encode($fileInfo),
                    ]);
                }
            }
        }
    }

    /**
     * 작지 파일 마이그레이션
     */
    public function migWorkFile(){
        $prjCnt = 0;
        $prdCnt = 0;
        $eworkFiles = DBUtil2::getList(ImsDBName::PROJECT_FILE, "styleSno is null and fileDiv", "fileWork", "regDt asc", false);
        //gd_debug($eworkFiles);
        foreach($eworkFiles as $file){
            DBUtil2::update(ImsDBName::PRODUCT, ['fileWork'=>$file['projectSno']] ,new SearchVo('projectSno=?', $file['projectSno']));
            $prjCnt++;
        }
        $eworkFiles = DBUtil2::getList(ImsDBName::PROJECT_FILE, "'' <> styleSno and styleSno is not null and fileDiv", "fileWork", "regDt asc");
        //gd_debug($eworkFiles);
        foreach($eworkFiles as $file){
            //gd_debug( json_decode($file['fileList'],true) );
            DBUtil2::update(ImsDBName::PRODUCT, ['fileWork'=>$file['fileList']] ,new SearchVo('sno=?', $file['styleSno']));
            $prdCnt++;
        }
        gd_debug('구 프로젝트 작지 : ' . $prjCnt);
        gd_debug('스타일 작지 : ' . $prdCnt);
        //Style File
        //전산 X , 개별 파일 , 프로젝트 파일은 이력으로  file
    }

    /**
     * 폐쇄몰 '상품 옵션'과 '창고 옵션' 연결
     */
    public function setDefaultGoodsOptionLink(){
        $searchVo = new SearchVo('b.delFl=?', 'n');
        $searchVo->setWhere("a.optionCode <> ''");
        $searchVo->setWhere("a.optionCode is not null");

        $goodsOptionList=DBUtil2::getJoinList(DB_GOODS_OPTION, [
            'b' => [DB_GOODS, 'a.goodsNo=b.goodsNo', 'b.goodsNm']
        ],$searchVo);

        $insertCnt = 0;
        foreach($goodsOptionList as $idx => $goodsOption){
            $saveValue['goodsNo'] =$goodsOption['goodsNo'];
            $saveValue['optionSno'] =$goodsOption['sno'];
            $saveValue['code'] =$goodsOption['optionCode'];
            DBUtil2::insert('sl_goodsOptionLink',$saveValue);
            $insertCnt++;
        }
        gd_debug('삽입수량 : ' . $insertCnt );
    }


    /**
     * 3pl 입고 이력 등록
     */
    public function set3plStockInputData(){
        $service = SlLoader::cLoad('godo','sopService','sl');

        //23
        $inDateList1 = [
            ['06-01','06-30'],
            ['07-01','07-31'],
            ['08-01','08-31'],
            ['09-01','09-30'],
            ['10-01','10-31'],
            ['11-01','11-30'],
            ['12-01','12-31'],
        ];
        /*foreach($inDateList1 as $each){
            $start = '2023-'.$each[0];
            $end = '2023-'.$each[1];
            $rslt = $service->reg3plInHistory($start, $end);
            gd_debug("시작일:{$start}  종료일:{$end} : {$rslt}");
        }*/

        //24-1
        $inDateList2 = [
            /*['01-01','01-31'],
            ['02-01','02-29'],
            ['03-01','03-31'],
            ['04-01','04-30'],
            ['05-01','05-31'],
            ['06-01','06-30'],*/
            ['07-01','07-31'],
            ['08-01','08-31'],
            ['09-01','09-30'],
            ['10-01','10-31'],
            ['11-01','11-30'],
            ['12-01','12-31'],
        ];
        /*foreach($inDateList2 as $each){
            $start = '2024-'.$each[0];
            $end = '2024-'.$each[1];
            $rslt = $service->reg3plInHistory($start, $end);
            gd_debug("시작일:{$start}  종료일:{$end} : {$rslt}");
        }*/

        //25
        $inDateList3 = [
            ['01-01','01-31'],
            ['02-01','02-28'],
            ['03-01','03-31'],
            ['04-01','04-30'],
            ['05-01','05-31'],
            ['06-01','06-30'],
            ['07-01','07-31'],
            ['08-01','08-31'],
        ];
        foreach($inDateList3 as $each){
            $start = '2025-'.$each[0];
            $end = '2025-'.$each[1];
            $rslt = $service->reg3plInHistory($start, $end);
            gd_debug("시작일:{$start}  종료일:{$end} : {$rslt}");
        }
    }

    public function tmp(){
        //$imsService = SlLoader::cLoad('ims', 'imsService');
        //$imsService->setSyncStatus(78, __METHOD__);
        //gd_debug('Sycn');

        /*$tr = DBUtil2::runSql("truncate table sl_imsEstimate");
        gd_debug('비우기 결과');
        gd_debug($tr);
        $this->setEstimate2();*/


        //estimate 확정인데 estimateConfirmSno 가 없는 건은 처리 완료 상태로 변경

        //prdCostConfirmSno 가 없는 건은 0원처리 및 그냥
        /*$prdList = DBUtil2::getList(ImsDBName::PRODUCT, '1', '1');
        foreach($prdList as $prd){

            $estimateList = DBUtil2::getList(ImsDBName::ESTIMATE, 'estimateType=\'estimate\' and styleSno', $prd['sno']);
            if( empty($prd['estimateConfirmSno']) ){
                DBUtil2::update(ImsDBName::PRODUCT,[
                    'estimateCost' => 0
                ], new SearchVo('sno=?', $prd['sno']));
                foreach($estimateList as $estiEach){
                    if( $estiEach['reqStatus'] == 5  || 0 == count($estimateList) ){ //확정이면 0 처리. 왜냐면 0이니깐.
                        DBUtil2::update(ImsDBName::ESTIMATE, ['reqStatus'=>3], new SearchVo('sno=?', $estiEach['sno']));
                    }
                }
            }

            $costList = DBUtil2::getList(ImsDBName::ESTIMATE, 'estimateType=\'cost\' and styleSno', $prd['sno']);
            if( empty($prd['prdCostConfirmSno']) || 0 == count($costList) ){
                DBUtil2::update(ImsDBName::PRODUCT,[
                    'prdCost' => 0
                ], new SearchVo('sno=?', $prd['sno']));
                foreach($costList as $estiEach){
                    if( $estiEach['reqStatus'] == 5 ){ //확정이면 0 처리. 왜냐면 0이니깐.
                        DBUtil2::update(ImsDBName::ESTIMATE, ['reqStatus'=>3], new SearchVo('sno=?', $estiEach['sno']));
                    }
                }
            }
        }*/
    }

}
