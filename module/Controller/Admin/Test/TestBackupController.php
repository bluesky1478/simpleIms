<?php

namespace Controller\Admin\Test;

use App;
use Component\Database\DBTableField;
use Component\Deposit\Deposit;
use Component\Erp\ErpCodeMap;
use Component\Erp\ErpService;
use Component\Goods\GoodsPolicy;
use Component\Scm\ScmTkeService;
use Component\Sitelab\SiteLabSmsUtil;
use Component\Work\Code\DocumentDesignCodeMap;
use Component\Work\DocumentCodeMap;
use Component\Work\WorkCodeMap;
use Encryptor;
use Framework\Utility\DateTimeUtils;
use Framework\Utility\NumberUtils;
use Globals;
use Request;
use SiteLabUtil\SlCommonUtil;
use SlComponent\Database\DBUtil;
use SlComponent\Database\DBUtil2;
use SlComponent\Database\SearchVo;
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

/**
 * TEST 페이지
 */
class TestBackupController extends \Controller\Admin\Controller{

    use ApiTrait;

    private $orderService;

    /**
     * @throws \Exception
     */
    public function index(){

        gd_debug("완료");

        exit();
    }

    public function backup1(){

        $manualService = SlLoader::cLoad('godo','manualService','sl');
        gd_debug('TEST');
        //$this->manualExcelData();
        //$manualService->setRefineExchange();
        //$manualService->setIdentificationToInvoice();

        //수기 할인
        // 절사 내용
        /*$orderNo = '2306071151213532';
        $orderDt = '2023-05-31 11:51:00';
        $manualService->refineOrderGoodsDc($orderNo, 30, $orderDt);
        gd_debug('주문일자 업데이트 : '.$rslt1.'/'.$rslt2);
        gd_debug('TEST Complete');*/

        //$manualService->setAttributeSeason();
        //$manualService->setAttributeTypeTke();
        //$manualService->setAttributeTypeAttr1();

        //733425
        //$tax = NumberUtils::taxAll(733425, 10, 't');
        //gd_debug($tax);

        //12
        //$list = DBUtil2::getList('sl_setScmDeliveryList', 'scmNo', '12');
        //$list = DBUtil2::getList('sl_setScmDeliveryList', '1', '1');
        //ExcelCsvUtil::downloadList($list, '영구크린_배송지리스트');


        //$trunc = Globals::get('gTrunc.goods');
        //gd_debug($trunc);

        //$orderService = SlLoader::cLoad('Order','OrderService');
        //$orderService->setOrderTax('2305161040392803'); //Tax 이상점 변경.
        /*$orderList = DBUtil2::runSelect("select distinct orderNo from es_orderGoods where regDt >= '2023-05-12 00:00:00 and scmNo=6'");
        foreach($orderList as $order){
            $orderService->setOrderTax($order['orderNo']); //Tax 이상점 변경.
        }
        gd_debug('complete...');*/


        //$manualService = SlLoader::cLoad('godo','manualService','sl');
        //$manualService->setFindAttribute();
        //$manualService->setFindAttributeHk();
        //$manualService->getHkStock2();

        //gd_debug('TKE NI회원 업데이트');
        /*'NI' => 5,
        'SVC' => 6,
        'MFG' => 7,*/
        /*$list = DBUtil2::runSelect("select * from es_member where entryDt >= '2023-05-15 00:00:00' and ex1='TKE(티센크루프)' and groupSno = 5 ");
        gd_debug(count($list));
        foreach( $list as $each ){
            //$rslt = DBUtil2::update(DB_MEMBER, ['memId'=> 'n'.$each['memId'] ], new SearchVo('memNo=?', $each['memNo']) );
            gd_debug($rslt);
        }*/
        /*$service = SlLoader::cLoad('batch','BatchService','sl');
        $service->runTkeOrderAdvice('');*/

        //gd_debug( implode(',',ScmTkeService::getPreOrderGoods()));

        /*$tkeMemberGroup = [
            '정직원',
            '파견직',
            '컨설턴트',
        ];

        $searchVo = new SearchVo('memPw=?', '');
        //$searchVo->setLimit(1);
        $list = DBUtil2::getListBySearchVo('sl_tkeMember', $searchVo);
        gd_debug(count($list));
        $tkeService = SlLoader::cLoad('scm','scmTkeService');*/
        //$tkeService->saveMemberTke();

        /*$tkeMemberList = DBUtil2::getList(DB_MEMBER, 'ex1','TKE(티센크루프)');
        $cnt = 0;
        foreach($tkeMemberList as $each){
            $memCfg = DBUtil2::getOne('sl_setMemberConfig', 'memNo', $each['memNo']);
            if( 1 == $memCfg['memberType'] ){
                $cnt += DBUtil2::delete(DB_MEMBER, new SearchVo('memNo=?', $each['memNo']));
            }
        }
        gd_debug($cnt);*/

        /*gd_debug(date('Y-m-d H:i:00'));

        gd_debug(ScmTkeService::getPreOrderGoods());
        gd_debug(ScmTkeService::getPreOrderGoods(ScmTkeService::TEE));

        $current = date('Ymd');
        $standard = '20230425';
        gd_debug( $standard > $current );

        $standard = '20230426';
        gd_debug( $standard > $current );

        $standard = '20230511';
        gd_debug( $standard > $current );*/

        /*$password = 'dndbwnsgh00';
        if(GodoUtils::sha256Fl()) {
            $member['memPw'] = Digester::digest($password);
        } else {
            $member['memPw'] = App::getInstance('password')->hash($password);
        }

        gd_debug($member);*/
        //gd_debug( SlCommonUtil::getOrderStatusAllMap() );

        //$this->cUrlTest();

        //gd_debug( Storage::diskgetDiskPath );
        //gd_debug( $data );

        //$str = '에코메이커 2박스';
        //$qty = preg_replace("/[^0-9]*/s", "", $str);
        //gd_debug($qty);

        /*$today = date('Y-m-d');
        $sql = "select * from sl_3plStockInOut a join sl_3plProduct b on a.productSno = b.sno where a.regDt >= '{$today} 00:00:00' and b.scmNo = 2  ";
        $list = DBUtil2::runSelect($sql);

        gd_debug( $sql );
        gd_debug( !empty($list) );*/

        //$this->refineHkStockOpenGoodsOption();
        //$manualService = SlLoader::cLoad('godo','manualService','sl');
        //$manualService->getTkeMember();
        //$manualService->rollbackInOutStock(1,'2023-03-23 00:00:00');

        //주문번호
        //고객명 (수령자)
        //우편번호 - 주소(+상세)
        //전화 / 휴대폰
        //제품코드
        //제품명
        //수량
        //배송비고
        //비고(운송회사)

        //결제일 1달 + 한국타이어 배송요청일 + p1 + 승인완료(or미승인)
        //1. 기본 리스트 뽑기

    }



    /**
     * 참고용 소스
     */
    public function oldTest(){

        //searchDate

        /*$orderService = SlLoader::cLoad('order','orderService');
        $orderGoodsList = $orderService->getOrderGoodsAndGoodsOption('2205171033388869');
        gd_debug( $orderGoodsList );*/

        //DBUtil2::getOne('bd_exchange', 'sno', 17); //

        //게시판 내용 복사
        //DBUtil2::runSql("INSERT INTO (  )  ");

        //gd_debug(gd_date_format('Y년m월','2022-01-03'));
        /*$option = DBUtil2::getList(DB_GOODS_OPTION, 'goodsNo', '1000000228');
        foreach($option as $value){
            gd_debug($value['sno'] . ' : ' . $value['optionNo'] . ' : ' . $value['optionValue1'] . ' ' . $value['optionValue2']);
        }*/
        //1000000228
        //,1000000227
        //,1000000226
        //,1000000225
        //,1000000224
        //,1000000223
        /*$goodsNoList = [
            ,1000000222
            ,1000000221
        ];

        foreach($goodsNoList as $goodsNo){
            $stockData = DBUtil2::runSelect("SELECT regDt, count(1) cnt FROM `sl_goodsStock` WHERE goodsNo = {$goodsNo} group by regDt order by regDt limit 2");
            if( $stockData[0]['cnt'] == $stockData[1]['cnt'] ){
                gd_debug($goodsNo . ' : 처리 완료');
                //gd_debug($stockData[0]['regDt']);
                //gd_debug($stockData[1]['regDt']);
                DBUtil2::runSql("DELETE FROM `sl_goodsStock` WHERE goodsNo = {$goodsNo} AND regDt =  '{$stockData[0]['regDt']}'");
                DBUtil2::runSql("update `sl_goodsStock` set stockType = 1, stockReason = 1 , stockCnt = afterCnt , beforeCnt = 0   WHERE goodsNo = {$goodsNo} and regDt = '{$stockData[1]['regDt']}'");
            }else{
                gd_debug($goodsNo . ' : 실패');
                gd_debug($stockData[0]['cnt'] . ' // ' . $stockData[1]['cnt'] );
            }
        }*/

        //배송지 사용
        /*$list = SlCommonUtil::getOrderDeliveryListMap();
        gd_debug($list);*/

        /*$sql = "select a.orderNo, b.memNm, b.address, d.receiverName, d.receiverAddress from es_order a join es_member b on a.memNo = b.memNo join sl_setMemberConfig c on b.memNo = c.memNo join es_orderInfo d on a.orderNo = d.orderNo where a.regDt >= '2022-05-13 00:00:00' and b.ex1 = 'TKE(티센크루프)' and c.memberType = 1";
        $result = DBUtil2::runSelect($sql);

        foreach($result as $each){
            $each['orderNo']='_'.$each['orderNo'];
            gd_debug(implode('▶',$each));
        }*/

        /*$str = 'TKEK_HR';
        gd_debug($str);

        $encryptor = \App::getInstance('encryptor');
        $enc = $encryptor->mysqlAesEncrypt($str);
        gd_debug( $enc );*/

        //gd_debug(\Request::getDefaultHost());

        /*$param['orderNo'] = '123456789';
        $param['orderName'] = '송준호';
        $param['writerName'] = '송준호';
        $param['settlePrice'] = '95,000';
        $param['regDt'] = '2021-05-28';
        $param['surveyUrl'] = 'https://forms.gle/mfp3jKbh7P7xtnoY7';
        SlKakaoUtil::send(11, '01081099599' ,  $param); //리서치-한국*/

        /*
        $memberList[] = [
            'memNo' => 0,
            'memName' => '김한국',
            'smsFl' => 'y',
            'cellPhone' => '010-8109-9599',
        ];
        $result = SlSmsUtil::sendSms($content, $memberList, 'lms');*/

        /*$orderService = SlLoader::cLoad('order','orderAdmin');
        $orderService->manualStatusChange('2204251720556636', 'p1');*/

        /*$depositService = SlLoader::cLoad('deposit','deposit');
        $rslt = $depositService->setMemberDeposit(1, 1000, Deposit::REASON_CODE_GROUP . Deposit::REASON_CODE_ETC, 'o', '2204251456068806',  null, '본사 선물금 잔액 예치금 적립');
        gd_debug($rslt);*/

        /*$result = DBUtil2::insert('sl_scmPopup',[
            'scmNo' => 6,
            'popupSno' => 22,
        ]);
        gd_debug($result);*/


        /*$contentParam['documentName'] = '삼성전자 미팅보고서';
        $contentParam['targetUrl'] = SlCommonUtil::getShortUrl('http://bcloud1478.godomall.com/wcustomer/meeting_list.php?key=tLfxkd4mf5QZsyevKJ3ZxQ&sno=4');
        $content = SlSmsUtil::getWorkSmsMsg(2,$contentParam);
        $memberList[] = [
            'memNo' => 0,
            'memName' => '(주)코웨이',
            'smsFl' => 'y',
            'cellPhone' => '010-8109-9599',
        ];
        $result = SlSmsUtil::sendSms($content, $memberList, 'lms');*/
        //$projectData = DBUtil2::getOne('sl_project','sno',$documentData['projectSno']);
        //gd_debug( SlCommonUtil::getShortUrl(urlencode('http://bcloud1478.godomall.com/wcustomer/meeting_list.php?key=tLfxkd4mf5QZsyevKJ3ZxQ&sno=4')));

        /*$documentService = SlLoader::cLoad('work','documentService','');
        $documentData = $documentService->getDocumentDataBySno(71);
        $projectData = DBUtil2::getOne('sl_project','sno',$documentData['projectSno']);
        gd_debug($projectData);
        gd_debug($documentData);*/


        /*$projectService = SlLoader::cLoad('work','projectService','');
        $result = $projectService->getProjectDocument(['sno'=>4]);

        gd_debug($result['projectData']['companyData']['companyName']);
        gd_debug($result);*/

        /*$documentService = SlLoader::cLoad('work','documentService','');
        $data = $documentService->setCustomerApply(10);
        gd_debug($data);*/

        /*$history = DBUtil2::getList('sl_workPlanHistory','projectSno', 1000);
        gd_debug($history);*/

        //$projectService = SlLoader::cLoad('work','projectService','');
        //$data = $projectService->getProjectData(1000);
        //$data = $projectService->getProjectDocument(['sno'=>'66']);
        //$projectService->setProjectStatus(1000);
        //gd_debug($data);

        /*$planHistory = $projectService->getPlanHistory(1000);
        gd_debug( $planHistory );*/


        //$workService = SlLoader::cLoad('work','workService','');
        //$guideSpec = $workService->getGuideSpec(1);
        //$guideSpec = $workService->getStyle(1);
        //gd_debug( $guideSpec );

        /*
        $result = [];
        foreach( $guideSpec as  $eachValue){
            $result[] = $eachValue[1];
        }
        gd_debug($result);*/

        //gd_debug($workService->getCheckList(1));
        //gd_debug($workService->getStyle(2));


        /*gd_debug($workService->getSpecData(1));
        */

        //DIFF DATE
        /*$firstDate  = new \DateTime();
        $secondDate = new \DateTime("2022-03-27");
        $intvl = $firstDate->diff($secondDate);
        gd_debug($intvl);
        gd_debug($intvl->days);*/


        /*$documentService = SlLoader::cLoad('work','documentService','');
        $data = $documentService->getDocumentDataBySno();
        gd_debug($data);*/


        /*        $documentService = SlLoader::cLoad('work','documentService','');
                $loadCondition['latest'] = 'y';
                $loadCondition['docDept'] = 'SALES';
                $loadCondition['docType'] = '101';
                $loadCondition['projectSno'] = '1000';
                $docData['docData'] = $documentService->getDocument($loadCondition);
                gd_debug( $docData );*/

        /*$documentService = SlLoader::cLoad('work','documentService','');
        gd_debug($documentService->getDocumentBySno(1));*/

        /*$projectService = SlLoader::cLoad('work','projectService','');
        $data = $projectService->getProjectDataWithDocument(1000);
        gd_debug($data);*/

        /*$insert['scmNo'] = '6';
        $insert['popupSno'] = '21';
        $rslt = DBUtil2::insert('sl_scmPopup', $insert);
        gd_debug($rslt);*/

        //gd_debug( SET_CHARSET );

        /*gd_debug( substr('fileTEST-OF-TEST',0,4) );

        //$contentParam['targetUrl'] = 'https://momotee.co.kr/board/board_list.php?sno=121';
        $contentParam['targetUrl'] = 'https://www.momotee.co.kr/member/join_method.php';
        $result = SlCommonUtil::getShortUrl($contentParam['targetUrl']);
        gd_debug( $result );*/


        /*$docData = [];
        $docData['afield']['bfield']['cfiled'] = 2;

        gd_debug($docData);

        $k = '';
        $str = "\$docData['afield']['bfield']['cfiled'];";
        $k = eval($str);
        gd_debug('TEST : ' . $k);*/

        /*$workService = SlLoader::cLoad('work','workService','');
        $data = $workService->getStyleNameListMap();
        gd_debug($data);*/

        /*        const MSG11 = '[{mallNm}]
        안녕하세요. [{orderName}]님

        ★★ 설문조사 이벤트★★

        간단한 설문조사하고 선물 받아가세요!

        경품 받기 : {surveyUrl}

        여러분의 목소리를 들려주세요!
        더욱 만족스러운 서비스를 위해 [구매 만족도 평가]를 실시하고 있습니다.
        향후 서비스 개선을 위해 활용될 예정이니 많은 참여 부탁드립니다 :)

        ▷ [{mallNm}] 바로가기
        [{shopUrl}]
        고객센터
        (070-4239-4380)';*/

        /**/

        /*$workService = SlLoader::cLoad('work','workService','');
        $result = $workService->getCheckList(0);
        gd_debug($result);*/


        //$map = json_encode(DocumentDesignCodeMap::DESIGN_DEFAULT_SPEC_DATA[0], JSON_UNESCAPED_UNICODE);
        //gd_debug($map);

        //$map = DBUtil2::getOne('sl_workStyleData', 'styleType', '0' );
        //gd_debug(json_decode($map['spec'],true));
        //gd_debug(DocumentDesignCodeMap::DESIGN_DEFAULT_SPEC_DATA[0]);


        /*gd_debug("== TEST ==");
        $reqUrl = 'https://openapi.naver.com/v1/util/shorturl';
        $data = 'url=http://bcloud1478.godomall.com/work/sales_meeting_ready_reg.php?sno=168';
        $header = [];
        $header[] = 'Content-Type: application/x-www-form-urlencoded; charset=UTF-8';
        $header[] = 'X-Naver-Client-Id:ljiJRJ1yPFtL5GZxEdEb';
        $header[] = 'X-Naver-Client-Secret:NmxsWsZy6l';
        $output = SlPostRequestUtil::request($reqUrl, $data, $header);
        gd_debug( json_decode($output,true) );*/

        //$result = SlCommonUtil::getShortUrl( 'http://bcloud1478.godomall.com/work/sales_meeting_ready_reg.php?sno=168' );
        //gd_debug( $result );

        /*$docService=SlLoader::cLoad('work','documentService','');
        $contentParam = [];
        $contentParam['targetUrl'] = \Request::getScheme()."://gdadmin.".\Request::getDefaultHost().'/work/document_reg.php?sno=1';

        $data = $docService->getDocumentDataBySno(168);
        gd_debug($data['docData']['companyName']);*/

        //Short URL
        //gd_debug( $contentParam['targetUrl'] );


        //$test = SlCommonUtil::getArrayKeyData( DBTableField::tableWorkCompany() , 'val'  );
        //gd_debug($test);

        //1000002237

        /*$str = '{"permission_1": ["godo00416", "godo00445"], "permission_2": {"godo00416": ["godo00417"], "godo00445": ["slab00016", "slab00013"]}, "permission_3": {"godo00417": ["godo00418", "godo00419", "godo00420", "godo00421", "godo00422", "godo00423", "godo00424", "godo00425", "godo00805"], "slab00013": ["slab00022", "slab00023"], "slab00016": ["slab00017", "slab00018"]}}';
        $json = json_decode($str,true);
        //$json['permission_3']['slab00016'] = [];
        $json['permission_3']['slab00016'] = ['slab00018'];
        gd_debug($json);*/

        /*        $managerData = DBUtil2::getOne(DB_MANAGER, 'sno', 3);
                $permitMenu = json_decode($managerData['permissionMenu'],true);
                $authList = [];
                $authList[] = 'slab00017';
                $authList[] = 'slab00018';
                $permitMenu['permission_3']['slab00016'] = $authList;
                DBUtil2::update(DB_MANAGER,  ['permissionMenu' => json_encode($permitMenu, JSON_UNESCAPED_UNICODE) ] , new SearchVo('sno=?' , 3) );

                $managerDataResult = DBUtil2::getOne(DB_MANAGER, 'sno', 3);
                gd_debug($managerDataResult);*/

        /*$scmService=SlLoader::cLoad('godo','scmService','sl');
        $scmService->getScmCategory();*/

        /*$scmStockListService = SlLoader::cLoad('Scm','ScmStockListService');;
        $data = $scmStockListService->getChartData(1000002237,'2020-01-01' , '2022-01-01');
        gd_debug($data);*/

        $param['orderNo'] = '123456789';
        $param['orderName'] = '송준호';
        $param['writerName'] = '송준호';
        $param['settlePrice'] = '95,000';
        $param['regDt'] = '2021-05-28';
        //SlKakaoUtil::send(9 , '01081099599' ,  $param); //리서치-한국

        //$param['surveyUrl'] = 'https://forms.gle/NmzsDDyU8xduYzwk9';
        //$param['btnUrl']  = 'https://forms.gle/NmzsDDyU8xduYzwk9';

        //$param['surveyUrl'] = 'https://forms.gle/Fxb4wBURrcCea2eF9';
        //$param['btnUrl']  = 'https://forms.gle/Fxb4wBURrcCea2eF9';

        //$param['surveyUrl'] = 'https://forms.gle/swyXx8U4Z6sMLLjm7';
        //$param['btnUrl']  = 'https://forms.gle/swyXx8U4Z6sMLLjm7';

        //SlKakaoUtil::send(4 , '01081099599' ,  $param); //문의 등록
        //SlKakaoUtil::send(5 , '01081099599' ,  $param); //문의 답변
        //SlKakaoUtil::send(6 , '01081099599' ,  $param); //리서치
        //SlKakaoUtil::send(8 , '01081099599' ,  $param); //리서치-TKE
        //SlKakaoUtil::send(9 , '01081099599' ,  $param); //리서치-한국

        //$sendCnt = DBUtil2::getCount('sl_orderMsgHistory',new SearchVo(['orderNo=?','templateId=?'], [ '2105061327128405' , '6' ] ));
        //gd_debug($sendCnt);

        //$orderService = SlLoader::cLoad('Order','OrderService');
        //$orderService->sendOrderMsg(6, '2105061327128405', false);

        /*주문안한사람 메세지 전달 .. */
        /*        $sql = "select a.memId , a.cellPhone, if( b.memberType = 1 , '정규직', '파트너사' ) as memberType
        from es_member a
        join sl_setMemberConfig b
          on a.memNo = b.memNo
        where a.memNo not in ( select distinct memNo from es_order ) and ex1 = 'TKE(티센크루프)'";
                $list = DBUtil2::runSelect($sql);
                foreach(  $list as $data  ){
                    gd_debug($data['memId']. ',' . '\''.$data['cellPhone']. ',' . $data['memberType']  );
                }*/


        /*        $a = (int)'1';
                $b = (int)'14';
                gd_debug($a & $b);*/


        //자동 입력 필드
        //mallSno = 1
        //groupSno = 1
        //memPw = '$2y$06$mubNAInRIAFuzJCJxtA9t.Bmav6JRx5SyYpiTR9CDn/8BKTmz.JH6'
        // memberFl = 'personal'
        // cellPhoneCountryCode = 'kr'
        //adminMemo = 'date('YmdHis')'

        //입력필드
        //memId
        //memName
        //zoneCode
        //address
        //addressSub
        //cellPhone
        // entryDt = 'now()'
        //ex1~2 = 선택한 공급사


        /*$memberData = DBUtil2::getOne(DB_MEMBER, 'memId', 'b147806');

        if( empty($memberData) ){
            $memberDefaultData['memPw'] = '$2y$06$mubNAInRIAFuzJCJxtA9t.Bmav6JRx5SyYpiTR9CDn/8BKTmz.JH6'; //inno15770327
            $memberDefaultData['adminMemo'] = date('YmdHis');
            $memberDefaultData['appFl'] = 'y';
            $memberDefaultData['approvalDt'] = date('Y-m-d H:i:s');

            $memberDefaultData['memId'] = 'b147806';
            $memberDefaultData['memName'] = '테스트06';
            $memberDefaultData['address'] = '경기도 고양시 덕양구 향기로 185 (디엠씨해링턴플레이스엔에이치에프)';
            $memberDefaultData['cellPhone'] = '010-8109-9599';
            $memberDefaultData['entryDt'] = date('Y-m-d H:i:s');
            $memberDefaultData['ex1'] = '현대(주)형대';

            DBUtil2::insert(DB_MEMBER, $memberDefaultData);
        }*/

        /*$rslt = Encryptor::encrypt('dndbwnsgh00');
        gd_debug($rslt);
        gd_debug('NTQyZTBmNDVjOTQ1NDJiMstUgQoKQhsg3Ihm3+8mUHUvJ9+LdlSNBa+vx9wR5vzl');

        $encryptor = \App::getInstance('encryptor');
        gd_debug( $encryptor->mysqlAesEncrypt('dndbwnsgh00') );*/

        /*$goodsPolicy = \App::load(\Component\Goods\GoodsPolicy::class);
        $searchData['memNo'] = '10';
        $searchData['goodsNo'] = '1000002237';
        $result = $goodsPolicy->getPolicyByGoodsMember($searchData);
        gd_debug($result);*/

    }

    public function oldTest2(){
        //http://bcloud1478.godomall.com/work/sales_meeting_ready_reg.php?sno=168

        //$excludeStatus = ['r','e','f'];
        //gd_debug( !in_array(substr('e3',0,1), $excludeStatus) );

        //gd_debug( URI_HOME.'wcustomer/index.php' );
        /*$docService=SlLoader::cLoad('work','documentService','');
        $comp = 45;
        $docDept = 'SALES';
        $docType = '2';
        $result = $docService->getLatestDocumentForCompany($docDept, $docType, $comp);*/

        /*$docDept = 'DESIGN';
        $docType = 1;

        $setFncName = 'setDefaultData' . ucfirst(strtolower($docDept)).$docType;
        $methodMap = SlCommonUtil::getReverseMap(get_class_methods(__CLASS__));
        if( isset($methodMap[$setFncName]) ){
            self::$setFncName();
        }else{
            gd_debug( $setFncName . '는 없네용...' );
        }*/
        //gd_debug($param);
        //gd_debug($orderInfo);
        //$param['writerName'] = $orderInfo['orderName'];
        //$param['settlePrice'] = '0';
        //$param['regDt'] = '2021-05-28';
        //SlKakaoUtil::send(10 , '01081099599' ,  $param);


        /*$param['orderNo'] = '123456789';
        $param['orderName'] = '송준호';
        $param['writerName'] = '송준호';
        $param['settlePrice'] = '95,000';
        $param['regDt'] = '2021-05-28';
        $param['surveyUrl'] = 'https://forms.gle/vS2CDNL3BssPyPby5';
        SlKakaoUtil::send(10 , '01081099599' ,  $param); //리서치-한국*/

        /*$docService=SlLoader::cLoad('work','documentService','');
        $result = $docService->getDefaultSampleData(1);
        gd_debug( $result );*/
        /*$mailData['orderDt'] = '2021-11-08 13:02';
        $mailData['orderNo'] = '123456';
        $mailData['scmNo'] = 6;
        $mailDataContents['goodsNo'] = 100000;
        $mailDataContents['goodsNm'] = '테스트상품';
        $mailDataContents['optionNm'] = '옵션명';
        $mailDataContents['shareCnt'] = 3;
        $mailDataContents['beforeCnt'] = 5;
        $mailDataContents['afterCnt'] = 2;
        $mailData['contents'][] = $mailDataContents;
        $mailService=SlLoader::cLoad('mail','mailService','sl');
        $mailService->sendShareMail( $mailData );*/
        //gd_debug( WorkCodeMap::DOC_SALES );
        //$list = SlCommonUtil::getColorList();
        //gd_debug($list);
        //05003
        //$depositReasons = gd_code('05100');
        //gd_debug($depositReasons);

        /*$workService = SlLoader::cLoad('work','workService','');
        $result = $workService->getCompany();
        gd_debug($result);*/

        /*$r = new \ReflectionClass('\Component\Work\WorkCodeMap');
        $obj = $r->newInstance();
        //gd_debug($obj);
        $v = $r->getConstant('DOC_SALES');
        gd_debug($v);*/


        //$docDept = 'DOC_SALES';
        //gd_debug( WorkCodeMap::$docDept );

        //gd_debug( DocumentCodeMap::DOC_SALES );
//        gd_debug( SlCommonUtil::getBranchList() );
//        gd_debug( SlCommonUtil::getBranchDeptList('본사') );
    }

    public function oldTest3(){
        $erpService = SlLoader::cLoad('erp','erpService');
        //gd_debug( date('Y-m-d', strtotime('-3 month')) );

        /*$list = [
            'closingSno' => '1',
            'lastClosingDate' => $lastClosingDate,
        ];*/

        /*$list = DBUtil2::getList('sl_3plStockInOut','closingSno','0');
        foreach($list as $each){
            if( $each['inOutType'] == 2 ){
                $erpService->updateStock($each['productSno'], ($each['quantity']*-1));
            }else{
                $erpService->updateStock($each['productSno'], ($each['quantity']));
            }
        }*/

        /*$closingTargetData = $erpService->getClosingInoutListSearchOption();
        $closingTargetData['page'] = 1;
        $closingTargetData['pageNum'] = 100000;
        $countData = $erpService->getInOutStockCount($closingTargetData);*/

        //$totalStockCnt = DBUtil2::runSelect("select sum(stockCnt) as totalStockCnt from sl_3plProduct ")[0]['totalStockCnt'];
        //gd_debug($totalStockCnt);

        /*
        $encodeAllListStr = $erpService->getEncodeAllProductList();
        $rslt = DBUtil2::update('sl_3plStockClosing',['totalMemo'=>$encodeAllListStr], new SearchVo('sno=?',$closingSno));
        gd_debug( $rslt );*/

        /*$closingSno = 15;
        $encodeData = DBUtil2::getOne('sl_3plStockClosing', 'sno'  ,$closingSno)['totalMemo'];
        $rslt = $erpService->getDecodeAllProductList($encodeData);
        gd_debug($rslt);*/

        $orderNo = '2302202153049430';
        /*
        $list = $erpService->getProductList();
        $refineList = SlCommonUtil::setEachData($list, $this, 'prdStockToStr');
        $str = implode(',',$refineList);
        $compressed_str = base64_encode(gzencode($str));*/
        //여기서 부터 디코드.
        $searchOption = [
            'page' => 1,
            'pageNum' => 150,
            'treatDateFl' => 'a.regDt',
            'treatDate' => [$lastClosingDate, date('Y-m-d')],
            'sort' => 'a.inOutDate, a.inOutType, a.thirdPartyProductCode',
            /*'closingDate' => '0000-00-00',*/
            'closingSno' => '',
        ];

        //$count = $erpService->getInOutStockCount($searchOption);
        //$list = $erpService->getSummaryStockList($searchOption);
        //gd_debug($count);
        //gd_debug($list);
        gd_debug('Complete....');

        //inOutType INT(2) NOT NULL COMMENT '입출고구분',

        //$erpService->insertOutStockByOrder($orderNo);
        /*$orderGoodsDataList = DBUtil2::getList(DB_ORDER_GOODS, 'orderNo', $orderNo);
        foreach($orderGoodsDataList as $each => $value){
            $optionInfo = DBUtil2::getOne(DB_GOODS_OPTION, 'sno', $value['optionSno']);
            $optionInfo['optionCode'];
            gd_debug($optionInfo['optionCode']);
        }*/


        //gd_debug(array_splice($list, 0, 4 )  );//1,4

        //gd_debug( SlSmsUtil::isTestMode() );

        //$timestamp = strtotime("+1 day");
        //$year = date('Y',$timestamp);

        /*$timestamp = strtotime("2001-01-01 +1 day");
        gd_debug( date("Y-m-d H:i:s", $timestamp) );*/

        /*$defaultInfo = gd_policy('basic.info');
        $param['mallNm'] = $defaultInfo['mallDomain'];
        gd_debug($param);*/

        //scm에 맞는 도메인
        /*$domain = 'http://'.SlCodeMap::OTHER_SKIN_MAP[6];
        gd_debug($domain);

        //카카오 알림 테스트
        $param['goodsName'] = 'TS TKE 동계바지(파트너사)';
        $param['reqName'] = '송준호';
        $param['btnUrl'] = "{$domain}/goods/goods_list.php?cateCd=002";
        $param['shopUrl'] = $domain;
        SlKakaoUtil::send(15 , '01081099599' ,  $param);
        gd_debug($param);

        $param['goodsName'] = '미쓰비시 동계점퍼';
        $param['reqName'] = '김태영';
        SlKakaoUtil::send(16 , '01081099599' ,  $param);
        gd_debug($param);*/

        /*$depositService = SlLoader::cLoad('deposit','deposit');
        $memNo = 1;
        $depositAmount = 5000;
        $rslt = $depositService->setMemberDeposit($memNo, $depositAmount, Deposit::REASON_CODE_GROUP . Deposit::REASON_CODE_ETC, 'o', null,  null, '정비복 포인트');
        gd_debug($rslt);*/

        /*$reqTable = 'sl_soldOutReqList';
        $memNo = 2;
        $goodsNo = '1000002252';

        $reqList = DBUtil2::getListBySearchVo($reqTable, new SearchVo(['memNo=?','goodsNo=? AND 0 = sendType'],[$menNo,$goodsNo]));
        gd_debug($reqList);*/


        /*        $member = DBUtil2::runSelect("SELECT * FROM es_member where memId Like 'hk%' ");
                gd_debug($member);*/

        //$this->setClaimApiUrl($this);

        //$orderService = SlLoader::cLoad('Order','OrderService');
        //$orderGoodsList = $orderService->getOrderGoods('2206291500005134');
        //gd_debug( json_encode($orderGoodsList, JSON_UNESCAPED_UNICODE) );
        //gd_debug( $refineGoodsList );
        //gd_debug( SlCommonUtil::getClaimCodeReasonByGoodsNo(1000002249) );
        //$goodsInfo = DBUtil2::getOne(DB_GOODS, 'goodsNo', 1000002244); //단일

        /*$claimBoardService = SlLoader::cLoad('claim','claimBoardService');
        gd_debug( $claimBoardService->getGoodsInfo(1000002245) );*/

        /*        $goodsNo = 1000002179;

                $goodsInfo = DBUtil2::getOne(DB_GOODS, 'goodsNo', $goodsNo);

                $optionValueList = explode('^|^', $goodsInfo['optionName']);

                $goodsOptionSelectList = [];
                $goodsOptionSelectFirstList = [];

                if(count($optionValueList)>1){
                    //$optionValueList
                    $lastIdx = count($optionValueList)-1;

                    $selectList = [];
                    foreach($optionValueList as $key => $optionName){
                        if( $key == $lastIdx ) {
                            break;
                        }
                        $optionSubject = '=== ' . $optionName . ' 선택(필수) ===';
                        $selectList[] = $optionSubject;
                        $goodsOptionSelectFirstList[] = $optionSubject;
                        $optionValueIdx = $key + 1;
                        $goodsDistinctSelectOption = DBUtil2::runSelect("SELECT DISTINCT optionValue{$optionValueIdx} as optionValue FROM es_goodsOption WHERE goodsNo={$goodsNo}");
                        foreach($goodsDistinctSelectOption as $optionValue){
                            $selectList[] = $optionValue['optionValue'];
                        }

                        $goodsOptionSelectList[] = $selectList;
                        $selectList = [];
                    }
                }else{
                    $lastIdx = 0;
                }
                $lastOptionValue = $lastIdx + 1;
                $goodsDistinctOption = DBUtil2::runSelect("SELECT DISTINCT optionValue{$lastOptionValue} as optionValue  FROM es_goodsOption WHERE goodsNo={$goodsNo}");

                gd_debug($goodsOptionSelectFirstList);
                gd_debug($goodsOptionSelectList);
                $optionList = [];
                foreach($goodsDistinctOption as $optionValue){
                    $optionList[] = $optionValue['optionValue'];
                }
                gd_debug($optionList);*/



        //goodsOptionList
        //gd_debug($goodsInfo);


        //주문 밀어 넣기

        //일괄 처리 전 상품들의 Stock 확인
        //엑셀로 받는 데이터
        /*        $param[0]['memId'] = 'b1478';
                $param[0]['goodsNo'] = 1000002244;
                $param[0]['receiverName'] = '신현섭';
                $param[0]['receiverCellPhone'] = '010-8109-9599';
                $param[0]['optionName'] = '90';
                $param[0]['deliveryName'] = '본사_8F';
                $param[0]['stockCnt'] = 1;

                $param[1]['memId'] = 'b1478';
                $param[1]['goodsNo'] = 1000002244;
                $param[1]['receiverName'] = '김준호';
                $param[1]['receiverCellPhone'] = '010-8109-9599';
                $param[1]['optionName'] = '100';
                $param[1]['deliveryName'] = '본사_8F';
                $param[1]['stockCnt'] = 2;

                $param[2]['memId'] = 'b1478';
                $param[2]['goodsNo'] = 1000002244;
                $param[2]['receiverName'] = '이아름';
                $param[2]['receiverCellPhone'] = '010-8109-9599';
                $param[2]['optionName'] = '100';
                $param[2]['deliveryName'] = '본사_8F';
                $param[2]['stockCnt'] = 2;

                $param[3]['memId'] = 'b1478';
                $param[3]['goodsNo'] = 1000002244;
                $param[3]['receiverName'] = '김준호';
                $param[3]['receiverCellPhone'] = '010-8109-9599';
                $param[3]['optionName'] = '105';
                $param[3]['deliveryName'] = '본사_8F';
                $param[3]['stockCnt'] = 1;

                $param[4]['memId'] = 'b1478';
                $param[4]['goodsNo'] = 1000002249;
                $param[4]['receiverName'] = '이아름';
                $param[4]['receiverCellPhone'] = '010-8109-9599';
                $param[4]['optionName'] = '100';
                $param[4]['deliveryName'] = '강남2_보라매지점';
                $param[4]['stockCnt'] = 5;

                $orderBatchRegService = SlLoader::cLoad('order','orderBatchRegService');
                $orderBatchRegService->batchOrder( $param , false);*/
        //gd_debug( SlCommonUtil::getOrderStatusAllMap() );
        //gd_debug(method_exists($this, 'batchUnitOrderTest1' ));
    }

    public function tempTest(){

        $order = SlLoader::cLoad('order','orderService');
        //$orderNo = '2012011348253245';
        $orderNo = '2012062032263669';
        $afterOrderGoodsList = $order->getOrderGoodsAndGoodsOption($orderNo);


        $sendTargetList = DBUtil::getList('sl_mailListOfSafeCnt', 'scmNo', '4' );
        $sendTargetListAdmin = DBUtil::getList('sl_mailListOfSafeCnt', 'scmNo', 0 );

        $sendTargetList = array_merge($sendTargetList, $sendTargetListAdmin);

        gd_debug($sendTargetList);

        exit();

        $stockCheckGoodsOption = array();
        $goodsInfo = array();

        $targetGoodsNoList = array();
        foreach($afterOrderGoodsList as $key => $orderGoods){
            //안전재고 체크 대상
            //$stockCheckGoodsOption[$orderGoods['goodsNo']][] = $orderGoods['optionNo'];
            $goodsNo = $orderGoods['goodsNo'];
            $optionNo = $orderGoods['optionNo'];

            if( empty($goodsInfo[$goodsNo]) ){
                $goodsInfo[$goodsNo] = DBUtil::getOne(DB_GOODS,'goodsNo',$goodsNo);
            }

            if(  'y' ===  $goodsInfo[$goodsNo]['stockFl']  ){
                //상품+옵션 번호로 안전재고 및 재고 가져오기
                $stockCnt = DBUtil::getOne(DB_GOODS_OPTION,['goodsNo','optionNo'],[$goodsNo,$optionNo])['stockCnt'];
                $safeCnt = DBUtil::getOne('sl_goodsSafeStock',['goodsNo','optionNo'],[$goodsNo,$optionNo])['safeCnt'];
                if(  empty($targetGoodsNoList[$goodsNo]) &&  $safeCnt > $stockCnt ){
                    $targetGoodsNoList[$goodsNo] = $goodsNo;
                    //gd_debug($stockCnt);
                    //gd_debug($safeCnt);
                }
            }
        }

        //gd_debug($goodsInfo);
        //gd_debug($targetGoodsNoList);

        $contents = array();
        foreach( $targetGoodsNoList as $goodsNo ){
            $totalStockCnt = 0;
            $totalSafeCnt = 0;

            $goodsData = $goodsInfo[$goodsNo];
            $contents[$goodsNo]['goodsNo'] = $goodsNo;
            $contents[$goodsNo]['goodsNm'] = $goodsData['goodsNm'];
            $contents[$goodsNo]['goodsPrice'] = $goodsData['goodsPrice'];
            $contents[$goodsNo]['imageName'] = DBUtil::getOne(DB_GOODS_IMAGE, ['goodsNo','imageKind'] , [$goodsNo,'list'] )['imageName'];
            $contents[$goodsNo]['goodsImage'] = gd_html_goods_image($goodsNo, $contents[$goodsNo]['imageName'], $goodsData['imagePath'], $goodsData['imageStorage'], 40, $goodsData['goodsNm'], '_blank');
            if( 'local' == $goodsData['imageStorage'] ){
                $pathPrefix = \Request::getScheme()."://".\Request::getDefaultHost();
                $contents[$goodsNo]['goodsImage'] = str_replace('<img src="/data/goods/', "<img src=\"{$pathPrefix}/data/goods/", $contents[$goodsNo]['goodsImage'] );
                $contents[$goodsNo]['goodsImage'] = str_replace('/data/commonimg/', "{$pathPrefix}/data/commonimg/", $contents[$goodsNo]['goodsImage'] );
            }
            $optionList = DBUtil::getList(DB_GOODS_OPTION, 'goodsNo' , $goodsNo , 'optionNo' );
            $safeOptionList = DBUtil::getList('sl_goodsSafeStock', 'goodsNo' , $goodsNo , 'optionNo' );

            //Option
            foreach(  $optionList as $optionInfoKey => $optionInfo ){
                $optionTitle = array();
                for($i=1; $i<=5;$i++){
                    if(!empty($optionInfo['optionValue'.$i])){
                        $optionTitle[] = $optionInfo['optionValue'.$i];
                    }
                }

                $stockCnt = $optionInfo['stockCnt'];
                $safeCnt = $safeOptionList[$optionInfoKey]['safeCnt'];
                $contents[$goodsNo]['option'][$optionInfo['optionNo']]['title'] = implode('/',$optionTitle);
                $contents[$goodsNo]['option'][$optionInfo['optionNo']]['stockCnt'] = $stockCnt;
                $contents[$goodsNo]['option'][$optionInfo['optionNo']]['safeCnt'] = $safeOptionList[$optionInfoKey]['safeCnt'];
                $contents[$goodsNo]['option'][$optionInfo['optionNo']]['isDanger'] = ($safeOptionList[$optionInfoKey]['safeCnt'] > $optionInfo['stockCnt']) ? 'red':'black';
                $totalStockCnt += $stockCnt;
                $totalSafeCnt += $safeCnt;
            }
            //Total
            $contents[$goodsNo]['totalStockCnt'] = $totalStockCnt;
            $contents[$goodsNo]['totalSafeCnt'] = $totalSafeCnt;
        }

        gd_debug($contents);

        $htmlContentsList = array();
        foreach( $contents as $goodsNo => $contentsValue ){

            $goodsImage = $contentsValue['goodsImage'];
            $goodsNm = $contentsValue['goodsNm'];
            $goodsPrice = number_format($contentsValue['goodsPrice']);

            $htmlContents = "
            <table style='margin: 10px auto 0px; padding: 0px; border: 1px solid rgb(187, 197, 206); width: 100%; line-height: 14px; font-size: 12px; border-collapse: collapse; table-layout: fixed; word-break: break-all;' border='0' cellspacing='0' cellpadding='0' align='center'>
            <tr>
            <th rowspan='2' style='padding: 3px 3px 3px 10px; color: rgb(0, 0, 0); border-bottom-color: rgb(187, 197, 206); border-bottom-width: 1px; border-bottom-style: solid; background-color: rgb(244, 244, 244);text-align: center'>상품코드</th>
            <th rowspan='2' style='padding: 3px 3px 3px 10px; color: rgb(0, 0, 0); border-bottom-color: rgb(187, 197, 206); border-bottom-width: 1px; border-bottom-style: solid; background-color: rgb(244, 244, 244);text-align: center'>이미지</th>
            <th rowspan='2' style='padding: 3px 3px 3px 10px; color: rgb(0, 0, 0); border-bottom-color: rgb(187, 197, 206); border-bottom-width: 1px; border-bottom-style: solid; background-color: rgb(244, 244, 244);text-align: center'>상품명</th>
            <th rowspan='2' style='padding: 3px 3px 3px 10px; color: rgb(0, 0, 0); border-bottom-color: rgb(187, 197, 206); border-bottom-width: 1px; border-bottom-style: solid; background-color: rgb(244, 244, 244);text-align: center'>판매가</th>            
            ";
            foreach( $contentsValue['option'] as $optionKey => $optionValue ){
                $htmlContents .= "<th colspan='2' style='padding: 3px 3px 3px 10px; color: rgb(0, 0, 0); border-bottom-color: rgb(187, 197, 206); border-bottom-width: 1px; border-bottom-style: solid; background-color: rgb(244, 244, 244);text-align: center'>{$optionValue['title']}</th>";
            }
            $htmlContents .= "<th colspan='2' style='padding: 3px 3px 3px 10px; color: rgb(0, 0, 0); border-bottom-color: rgb(187, 197, 206); border-bottom-width: 1px; border-bottom-style: solid; background-color: rgb(244, 244, 244);text-align: center'>합계</th>";
            $htmlContents .= "</tr><tr>";

            //중간 Title
            foreach( $contentsValue['option'] as $optionKey => $optionValue ){
                $htmlContents .= "<th style='padding: 3px 3px 3px 10px; color: rgb(0, 0, 0); border-bottom-color: rgb(187, 197, 206); border-bottom-width: 1px; border-bottom-style: solid; background-color: rgb(244, 244, 244);text-align: center'>현재고</th>";
                $htmlContents .= "<th style='padding: 3px 3px 3px 10px; color: rgb(0, 0, 0); border-bottom-color: rgb(187, 197, 206); border-bottom-width: 1px; border-bottom-style: solid; background-color: rgb(244, 244, 244);text-align: center'>안전재고</th>";
            }
            //합계용 중간 Title
            $htmlContents .= "<th style='padding: 3px 3px 3px 10px; color: rgb(0, 0, 0); border-bottom-color: rgb(187, 197, 206); border-bottom-width: 1px; border-bottom-style: solid; background-color: rgb(244, 244, 244);text-align: center'>현재고</th>";
            $htmlContents .= "<th style='padding: 3px 3px 3px 10px; color: rgb(0, 0, 0); border-bottom-color: rgb(187, 197, 206); border-bottom-width: 1px; border-bottom-style: solid; background-color: rgb(244, 244, 244);text-align: center'>안전재고</th></tr>";

            //데이터
            $htmlContents .= "</tr><tr>
            <td style='width: 80%; height: 43px; color: rgb(51, 51, 51); padding-left: 20px; font-size: 13px; border-bottom-color: rgb(229, 229, 229); border-bottom-width: 1px; border-bottom-style: solid;text-align: center'>{$goodsNo}</td>
            <td style='width: 80%; height: 43px; color: rgb(51, 51, 51); padding-left: 20px; font-size: 13px; border-bottom-color: rgb(229, 229, 229); border-bottom-width: 1px; border-bottom-style: solid;text-align: center'>{$goodsImage}</td>
            <td style='width: 80%; height: 43px; color: rgb(51, 51, 51); padding-left: 20px; font-size: 13px; border-bottom-color: rgb(229, 229, 229); border-bottom-width: 1px; border-bottom-style: solid;text-align: center'>{$goodsNm}</td>
            <td style='width: 80%; height: 43px; color: rgb(51, 51, 51); padding-left: 20px; font-size: 13px; border-bottom-color: rgb(229, 229, 229); border-bottom-width: 1px; border-bottom-style: solid;text-align: center'>{$goodsPrice}원</td>";

            foreach( $contentsValue['option'] as $optionKey => $optionValue ){
                $htmlContents .= "<td style='width: 80%; height: 43px; color: rgb(51, 51, 51); padding-left: 20px; font-size: 13px; border-bottom-color: rgb(229, 229, 229); border-bottom-width: 1px; border-bottom-style: solid;text-align: center;color:{$optionValue['isDanger']}'>{$optionValue['stockCnt']}</td>";
                $htmlContents .= "<td style='width: 80%; height: 43px; color: rgb(51, 51, 51); padding-left: 20px; font-size: 13px; border-bottom-color: rgb(229, 229, 229); border-bottom-width: 1px; border-bottom-style: solid;text-align: center'>{$optionValue['safeCnt']}</td>";
            }
            $htmlContents .= "<td style='width: 80%; height: 43px; color: rgb(51, 51, 51); padding-left: 20px; font-size: 13px; border-bottom-color: rgb(229, 229, 229); border-bottom-width: 1px; border-bottom-style: solid;text-align: center;'>{$contentsValue['totalStockCnt']}</td>";
            $htmlContents .= "<td style='width: 80%; height: 43px; color: rgb(51, 51, 51); padding-left: 20px; font-size: 13px; border-bottom-color: rgb(229, 229, 229); border-bottom-width: 1px; border-bottom-style: solid;text-align: center'>{$contentsValue['totalSafeCnt']}</td>";
            $htmlContents .= "</tr></table>";
            $htmlContentsList[] = $htmlContents;
        }

        gd_debug(implode('<br>',$htmlContentsList));
        gd_debug('=====================================================');

        //$this->smsTest();
        //$this->mailSendTest();


        $order = SlLoader::cLoad('order','orderService');
        $orderGoodsList = $order->getOrderGoodsAndGoodsOption('2012011348253245');
        gd_debug($orderGoodsList);
        //optionSno - 옵션의 재고 가져오기
        gd_debug(DBUtil::getOne(DB_GOODS_OPTION, 'sno', '132462'));

        //체크

        //승인 처리 후 재고 복원
        //$result = $order->setGoodsStockRestore(2012011348253245,263);
        //gd_debug($result);

        gd_debug("* 결제상태");
        $orderAdmin = \App::load('\\Component\\Order\\OrderAdmin');
        gd_debug($orderAdmin->getSettleKind('gb'));

        gd_debug("* 주문상태");
        $result = SlCommonUtil::getOrderStatusAllMap();
        gd_debug($result);

        //Mail Test
        //$this->mailSendTest();

    }

    public function downloadPackingList(){

        //공란
        //타이틀
        //품목명 + 수량
        //타이틀
        //품목명 + 수량
        //주소

        $data = $this->getDummyData();
        $option = $this->getDummyData();
        $prdCount = count($option['prdOptionList']);
        $prdRowSpanCount = $prdCount * 2;
        $totalRowSpanCount = $prdRowSpanCount + 3;

        //공란
        $excelBody = '<tr style="height:5px;"></tr>';
        //$fieldData[] = "<tr><td colspan='{$colSpanOptionCount}' style='height:5px;' ></td></tr>";

        $colSpanOptionCount = $option['maxOptionCount'];
        $colSpanTotalCount = $colSpanOptionCount + 5;

        foreach($data as $idx => $each){
            $fieldData[] = "<tr>";
            $fieldData[] = ExcelCsvUtil::wrapTd('');
            $fieldData[] = ExcelCsvUtil::wrapTd($idx+1, 'text', 'vertical-align:middle;text-align:center;fot-weight:bold',"rowspan={$totalRowSpanCount}");
            $fieldData[] = ExcelCsvUtil::wrapTh("고객사 / 부서명");
            $fieldData[] = ExcelCsvUtil::wrapTh("품목 / 사이즈");
            foreach($option['prdOptionList'][0] as $optionEach){
                $fieldData[] = ExcelCsvUtil::wrapTh($optionEach);
            }
            $fieldData[] = ExcelCsvUtil::wrapTd('합계');
            $fieldData[] = "</tr>";

            $fieldData[] = "<tr>";
            $fieldData[] = ExcelCsvUtil::wrapTh($each['compName']);
            //PRD만큼..
            foreach( $each['prd'] as $prdEach ){
                $fieldData[] = ExcelCsvUtil::wrapTd($prdEach['name']);
                foreach($option['prdOptionList'][0] as $optionEach){
                    $fieldData[] = ExcelCsvUtil::wrapTd($prdEach['option'][$optionEach]);
                }
            }
            $fieldData[] = ExcelCsvUtil::wrapTd($prdEach['option'][$optionEach]); //행 합계

            $fieldData[] = "</tr>";

            //타이틀
            //품목명 + 수량
            //타이틀
            //품목명 + 수량
            //주소
            //public static function wrapTd($str , $class=null, $style=null, $etcTag=null){
            $fieldData[] = ExcelCsvUtil::wrapTd($val['memo'],'text','');
        }

    }


}