<?php
namespace Component\Ims;

use App;
use Component\Database\DBIms;
use Component\Database\DBTableField;
use Component\Imsv2\ImsScheduleUtil;
use Component\Member\Manager;
use Component\Sms\Code;
use Framework\Debug\Exception\AlertBackException;
use Framework\Utility\DateTimeUtils;
use LogHandler;
use Request;
use Session;
use SiteLabUtil\ImsUtil;
use SiteLabUtil\SlCommonUtil;
use SlComponent\Database\DBUtil2;
use SlComponent\Database\SearchVo;
use SlComponent\Godo\ListService;
use SlComponent\Util\SitelabLogger;
use SlComponent\Util\SlLoader;
use SlComponent\Util\SlSmsUtil;

/**
 * IMS 스타일 서비스
 * Class GoodsStock
 * @package Component\Goods
 */
class ImsStyleService {

    private $sql;
    private $dpData;

    use ImsServiceTrait;
    use ImsListTrait;

    const VIEW_STYLE_FIELD = [
        'no' => ['name'=>'번호','col'=>'3','skip'=>true],
        'customerName' => ['name'=>'고객사','col'=>'10','class'=>"text-left pdl5"],
        'projectNo' => ['name'=>'프로젝트번호','col'=>'5'],
        'projectTypeKr' => ['name'=>'타입','col'=>'5'],
        'projectStatusKr' => ['name'=>'프로젝트상태','col'=>'7'
            , 'afterContents' => "<div class='btn btn-sm btn-white font-11' @click=\"openCommonPopup('ework', 1300, 850, {sno:each.sno, tabMode:'main'})\">전산작지정보</div>",],
        'sno' => ['name'=>'고유번호','col'=>'4'],
        'styleFullName' => [
            'name'=>'스타일명','col'=>'13','class'=>"text-left pdl5",
            'subData' => ['styleCode' => ['name'=>'스타일코드','class'=>'text-muted font-11']],
        ],
        'reqFactoryNm' => ['name'=>'생산처','col'=>'8'],
        'eworkMainFl' => ['name'=>'메인','col'=>'3','custom'=>'eworkMainFl'],
        'eworkMarkFl' => ['name'=>'마크','col'=>'3','custom'=>'eworkMarkFl'],
        'eworkPositionFl' => ['name'=>'케어','col'=>'3','custom'=>'eworkPositionFl'],
        'eworkSpecFl' => ['name'=>'스펙','col'=>'3','custom'=>'eworkSpecFl'],
        'eworkSpec2Fl' => ['name'=>'스펙D','col'=>'3','custom'=>'eworkSpec2Fl'],
        'eworkMaterialFl' => ['name'=>'자재','col'=>'3','custom'=>'eworkMaterialFl'],
        'eworkPackingFl' => ['name'=>'포장','col'=>'3','custom'=>'eworkPackingFl'],
        'eworkBatekFl' => ['name'=>'바텍','col'=>'3','custom'=>'eworkBatekFl'],

        'prdExQty' => ['name'=>'수량','col'=>'','type'=>'number'],
        'salePrice' => ['name'=>'판매가','col'=>'','type'=>'number','class'=>'text-danger'],
        'prdCost'   => ['name'=>'생산가','col'=>'','type'=>'number','class'=>'sl-blue'],
        'margin' => ['name'=>'마진','col'=>'5','type'=>'number','afterContents'=>'%'],
        'prdYearSeason' => ['name'=>'시즌','col'=>'3'],
    ];

    const CUSTOMER_STYLE_FIELD = [
        //['title' => '스타일'   , 'type' => 's', 'name' => 'productName', 'col' => 9, 'rowspan'=>true],   //FIXME 마스터 스타일로
        ['title' => '일련번호', 'type' => 's', 'name' => 'sno', 'col' => 1,'class'=>'ta-c'],
        ['title' => '프로젝트 스타일명', 'type' => 'c', 'name' => 'styleFullName', 'col' => 9,],
        ['title' => '코드'     , 'type' => 'c', 'name' => 'masterCode', 'col' => 5,  'rowspan'=>true],   //FIXME 마스터 코드로
        ['title' => '타입'     , 'type' => 's', 'name' => 'projectTypeKr', 'col' => 4,],
        ['title' => '연도/시즌' , 'type' => 's', 'name' => 'prdYearSeason', 'col' => 2,],
        ['title' => '생산처', 'type' => 's', 'name' => 'reqFactoryNm', 'col' => 5,],
        ['title' => '수량', 'type' => 'i', 'name' => 'prdExQty', 'col' => 4, 'class'=>'ta-r'],
        ['title' => '생산가', 'type' => 'i', 'name' => 'prdCost', 'col' => 4, 'class'=>'ta-r'],
        ['title' => '판매가', 'type' => 'i', 'name' => 'salePrice', 'col' => 4, 'class'=>'ta-r'],
        ['title' => '작지', 'type' => 'c', 'name' => 'work', 'col' => 4, 'class'=>'ta-c'],
        //['title' => '사양서', 'type' => 'c', 'name' => 'order', 'col' => 4,],
        //['title' => '메인원단', 'type' => 'c', 'name' => 'fabric', 'col' => 4,],
        //['title' => '이슈', 'type' => 's', 'name' => 'issueMemo', 'col' => 8,],
    ];

    public function __construct(){
        $this->sql = SlLoader::sqlLoad(__CLASS__, false);
        $this->dpData = ImsStyleService::VIEW_STYLE_FIELD;
    }

    public function getDisplayStyle($params){
        return $this->dpData;
    }

    public function getDisplayCustomerStyle(){
        $list = ImsStyleService::CUSTOMER_STYLE_FIELD;
        SlCommonUtil::setColWidth(95, $list);
        return $list;
    }

    /**
     * 스타일 리스트 불러오기
     * @param $params
     * @return array
     */
    public function getListStyle($params){
        $searchData['condition'] = $params;
        $searchVo = new SearchVo('a.delFl=?','n');
        //$this->setTodoCondition($params['condition'], $searchVo); //개별 조건 설정시
        //$params['rowSpanData'] = ['reqSno'  => ['valueKey' => 'sno'],]; //Rowspan필요시
        $searchData['instance'] = $this;
        $searchData['sqlInstance'] = $this->sql;
        $searchData['getTableFncName'] = 'getStyleTable';
        $searchData['decorationName'] = 'decorationStyle';

        //작지 등록 여부 체크
        $inData = $searchData['condition']['eworkDataChk'];
        //gd_debug($inData);

        //gd_debug($params);
        //SitelabLogger::logger2(__METHOD__, $inData);
        if( !is_array($inData) ){
            if( !empty($params['simple_excel_download']) ){
                $inData = json_decode($inData,true);
            }else{
                $inData = explode(',',$inData);
            }
        }
        //gd_debug($inData);

        if(!empty($inData) && 'all' !== $inData[0] && '' !== $inData[0] && $inData[0] != '[]' ){
            foreach( $inData as $eworkCondition ){
                $otherConditionTypeList = [
                    'fileBatek'=>'useBatek', 'filePacking1'=>'usePacking', 'fileMark1'=>'useMark'
                ];
                if( in_array($eworkCondition, array_keys($otherConditionTypeList))){
                    //스타일 중. 사용안함 여부 체크 시 별도 처리
                    $searchVo->setWhere("({$otherConditionTypeList[$eworkCondition]} = 'n' OR LENGTH({$eworkCondition})>35)");
                }else{
                    //'filePacking1';
                    if( 'material' === $eworkCondition ){
                        $searchVo->setWhere("(select count(1) from sl_imsPrdMaterial mat where mat.styleSno=a.sno)>0");
                    }else{
                        $searchVo->setWhere("LENGTH({$eworkCondition})>35");
                    }
                }
            }
        }

        //추가 검색
        if(!empty($params['prdYear'])){
            $searchVo->setWhere("a.prdYear like '%{$params['prdYear']}%'");
        }
        if(!empty($params['prdSeason'])){
            $searchVo->setWhere("a.prdSeason=?");
            $searchVo->setWhereValue($params['prdSeason']);
        }
        if(!empty($params['prdName'])){
            $searchVo->setWhere("a.productName like '%{$params['prdName']}%'");
        }

        return $this->getListCommonProc($searchData,$searchVo);
    }

    /**
     * 결재 리스트 추가 정보
     * @param $each
     * @param null $key
     * @param null $mixData
     * @return mixed
     * @throws \Exception
     */
    public function decorationStyle($each, $key=null, $mixData=null){

        //디코드를 위한 Strip
        $each['assort'] = stripslashes($each['assort']);

        //판매가 가림
        if( !empty($_COOKIE['setSaleCostDisplay']) &&  'n' === $_COOKIE['setSaleCostDisplay'] ){
            $each['salePrice'] = 0;
            $each['currentPrice'] = 0;
            $each['targetPrice'] = 0;
            $each['targetPrdCost'] = 0;
        }

        //기본 데이터 정제
        $each = SlCommonUtil::refineDbData($each, ImsDBName::PRODUCT);

        $salePrice = $each['salePrice'];
        $prdCost = $each['prdCost'];

        if(0 >= $salePrice && $each['targetPrice'] > 0){
            $salePrice = $each['targetPrice'];
        }
        if(0 >= $prdCost && $each['estimateCost'] > 0){
            $prdCost = $each['estimateCost'];
        }else if(0 >= $prdCost && $each['targetPrdCost'] > 0){
            $prdCost = $each['targetPrdCost'];
        }

        $each['dpPrice'] = $salePrice;
        $each['dpCost'] = $prdCost;

        //마진처리.
        if( $salePrice > 0 && $prdCost > 0  ){
            $each['margin'] = round(100-($prdCost/$salePrice*100));
        }else{
            //$salePrice =
            //생산 견적없음 + 판매가 없음
            //생산 견적없음 + 판매가 있음
            //생산 견적있음 + 판매가 없음
            //생산 견적있음 + 판매가 있음
            $each['margin'] = 0;
        }

        $prdYear = substr($each['prdYear'],2,2);
        $each['prdYearSeason'] = $prdYear.$each['prdSeason'];
        $each['projectStatusKr'] = ImsCodeMap::PROJECT_STATUS[$each['projectStatus']];
        $each['projectTypeKr'] = ImsCodeMap::PROJECT_TYPE[$each['projectType']];
        $each['styleFullName'] = "{$prdYear} {$each['prdSeason']} {$each['productName']}";
        $each['fabricStatusKr'] = ImsCodeMap::IMS_FABRIC_STATUS[$each['fabricStatus']]['name'];

        //사이즈스펙 재설정 ---------------------------------------------------------------------------
        $sizeSpecData = $each['sizeSpec'];
        if(empty($sizeSpecData) || !is_array($sizeSpecData) ){
            $sizeSpecData = SlCommonUtil::setJsonField([],ImsJsonSchema::SIZE_SPEC_DATA);
        }else{
            $sizeSpecData = SlCommonUtil::setJsonField($sizeSpecData,ImsJsonSchema::SIZE_SPEC_DATA);
        }
        $each['sizeSpec'] = $sizeSpecData;
        $each['sizeList'] = explode(',',$sizeSpecData['specRange']);
        //------------------------------------------------------------------------------------------
        //아소트 설정
        $each['assort'] = $this->getAssort($each);

        //퀄리티 데이터 삽입 ------------------------------------------------------------------------
        $prdMaterialList = DBUtil2::getList(ImsDBName::PRD_MATERIAL, 'styleSno', $each['sno'], 'sort, sno');
        $each['fabricDefault'] = DBTableField::getTableKeyAndBlankValue(ImsDBName::PRD_MATERIAL);
        $each['fabricDefault']['typeStr']='fabric';
        $each['subFabricDefault'] = DBTableField::getTableKeyAndBlankValue(ImsDBName::PRD_MATERIAL);
        $each['subFabricDefault']['typeStr']='subFabric';
        foreach($prdMaterialList as $prdMaterial){
            if('fabric' === $prdMaterial['typeStr']){
                $each['fabricList'][] = $prdMaterial;
            }else{
                $each['subFabricList'][] = $prdMaterial;
            }
        }
        //------------------------------------------------------------------------------------------

        //상태에 대한 아이콘 설정
        $each['fabricStatusKr'] = ImsCodeMap::IMS_FABRIC_STATUS[$each['fabricStatus']]['name'];
        $each['fabricStatusIcon'] = ImsCodeMap::IMS_PROC_STATUS[$each['fabricStatus']]['icon'];
        //$each['fabricStatusColor'] = ImsCodeMap::IMS_FABRIC_STATUS[$each['fabricStatus']]['color'];

        $each['btStatusKr'] = ImsCodeMap::IMS_BT_STATUS[$each['btStatus']]['name'];
        $each['btStatusIcon'] = ImsCodeMap::IMS_PROC_STATUS[$each['btStatus']]['icon'];
        $each['estimateStatusKr'] = ImsCodeMap::IMS_PROC_STATUS[$each['estimateStatus']]['name'];
        $each['estimateStatusIcon'] = ImsCodeMap::IMS_PROC_STATUS[$each['estimateStatus']]['icon'];
        $each['prdCostStatusKr'] = ImsCodeMap::IMS_PROC_STATUS[$each['prdCostStatus']]['name'];
        $each['prdCostStatusIcon'] = ImsCodeMap::IMS_PROC_STATUS[$each['prdCostStatus']]['icon'];
        $each['workStatusKr'] = ImsCodeMap::IMS_PROC_STATUS[$each['workStatus']]['name'];
        $each['workStatusIcon'] = ImsCodeMap::IMS_PROC_STATUS[$each['workStatus']]['icon'];

        //---
        $each['specOptionList'] = explode(',',$each['sizeSpec']['specRange']);
        $each['assort'] = $this->getAssort($each);
        $each['optionTotal'] = [];
        foreach( $each['specOptionList'] as $size ){
            $each['optionTotal'][$size]=0;
        }

        //확정 샘플 정보 저장 ( 승인되었으면 그냥 보여 준다 )
        $each['isWorkModify'] = 'y';
        $checkSampleFile = ['sampleFile1', 'sampleFile4', 'sampleFile6']; //샘플지시서, 샘플리뷰서, 샘플확정서

        if(in_array( $each['projectType'], ImsCodeMap::PROJECT_TYPE_N) && -1 != $each['sampleConfirmSno'] && 2 != $each['workStatus'] ){
            if($each['sampleConfirmSno'] > 0){
                foreach($checkSampleFile as $sampleFileName){
                    $each[$sampleFileName.'Exsists'] = DBUtil2::getCount(ImsDBName::PROJECT_FILE, new SearchVo(" fileDiv='{$sampleFileName}' and styleSno='{$each['sno']}' and eachSno=?", $each['sampleConfirmSno'])) > 0 ? 'y':'n';
                    if('n' === $each[$sampleFileName.'Exsists']) $each['isWorkModify'] = 'n';
                }
            }else{
                $each['isWorkModify'] = 'n';
                foreach($checkSampleFile as $sampleFileName){
                    $each[$sampleFileName.'Exsists'] = 'n';
                }
            }
        }else{
            $each['isWorkModify'] = 'y';
            foreach($checkSampleFile as $sampleFileName){
                $each[$sampleFileName.'Exsists'] = 'y';
            }
        }
        $each = $this->setCommonProductDecoration($each);
        $each['fileWork'] = SlCommonUtil::stripJsonDecode($each['fileWork']);

        return SlCommonUtil::setDateBlank($each);
    }

    /**
     * 고객 스타일을 모두 가져온다 ( 보여줄 필드 정보와 함께 )
     * @param $params
     */
    public function getListStyleWithCustomerField($params){
        $rslt['field'] = $this->getDisplayCustomerStyle();

        $params['prdSeason'] = 'SF';
        $rslt['list']['sf'] = $this->getListStyle($params);
        $params['prdSeason'] = 'SS';
        $rslt['list']['ss'] = $this->getListStyle($params);
        $params['prdSeason'] = 'FW';
        $rslt['list']['fw'] = $this->getListStyle($params);
        unset($params['prdSeason']);
        $params['multiKey'][] = ['key' =>'prdSeason' , 'keyword'=> 'ALL'];
        $params['multiKey'][] = ['key' =>'prdSeason' , 'keyword'=> '']; //<=== 이거 검색 안된다.

        $rslt['list']['all'] = $this->getListStyle($params);
    }

    /**
     * DEPRECATED
     * 아소트 URL 전달 (로직)
     * @param $params
     * @throws \Exception
     */
    public function sendAssortUrl($params){

        $mailUtil = SlLoader::cLoad('mail','SiteLabMailUtil','sl');
        $customerData = DBUtil2::getOne(ImsDBName::CUSTOMER, 'sno', $params['customerSno']);

        //우선 저장
        DBUtil2::update(ImsDBName::PROJECT, [
            'assortApproval' =>'r', //고객 입력요청상태로 변경
            'assortReceiver' =>$params['assortReceiver'],
            'assortEmail' =>$params['assortEmail'],
            'assortSendDt' =>'now()',
        ], new SearchVo('sno=?', $params['sno']));

        $key = SlCommonUtil::aesEncrypt($params['sno']);

        //견적 발송 (메일 발송)
        //$receiverName = urlencode($params['assortReceiver']);
        $mailData['subject'] = $customerData['customerName'].' 고객님 발주수량 입력 요청 드립니다.';
        $mailData['from'] = 'innover@msinnover.com';
        $mailData['to'] = $params['assortEmail'];

        $host = SlCommonUtil::getHost();
        $replace['rc_companyName'] = $customerData['customerName'];
        $replace['rc_confirmFullUrl'] = "{$host}/ics/ics_assort.php?key={$key}";
        $replace['rc_confirmUrl'] = '발주수량 입력하기';

        $mailData['body'] = $mailUtil->getMailTemplate($replace,'customer_assort.php');
        $mailUtil->send($mailData['subject'], $mailData['body'], $mailData['from'], $mailData['to'], null, SlCommonUtil::getManagerMail());
        //SitelabLogger::logger2(__METHOD__, '메일 발송 정보 확인'); //메일발송 내용.
        //SitelabLogger::logger2(__METHOD__, $mailData); //메일발송 내용.

        //발송이력 insert
        $aInsertHistory = [
            'sendType'=>'아소트',
            'projectSno'=>$params['sno'],
            'sendManagerSno'=>\Session::get('manager.sno'),
            'receiverName'=>$params['assortReceiver'],
            'receiverMail'=>$params['assortEmail'],
            'subject'=>$mailData['subject'],
            'contents'=>$mailData['body'],
            'regDt'=>date('Y-m-d H:i:s'),
        ];
        DBUtil2::insert(ImsDBName::SEND_HISTORY, $aInsertHistory);
    }


    /**
     * 아소트 고객 입력 완료 (로직)
     * @param $params
     * @return array
     * @throws \Exception
     */
    public function setAssortComplete($params){

        //우선 저장
        DBUtil2::update(ImsDBName::PROJECT, [
            'assortApproval' =>'f', //고객 입력완료상태로 변경
            'assortCustomerDt' =>'now()',
        ], new SearchVo('sno=?', $params['sno']));

        $imsService = SlLoader::cLoad('ims', 'imsService');
        $projectData = $imsService->getProject(['sno'=>$params['sno']]);
        $customerName = $projectData['customer']['customerName'];

        //TO-DO 등록 ( 영업(02001001). 생산(02001003). )
        $contents = "
{$customerName} 고객이 발주수량 입력을 완료 하였습니다. 
입력 수량 확인 후 이상 없을 경우 확인 처리 바랍니다.  
수량 변경이 필요할 경우 고객과 연락하여 직접 수정 바랍니다.  
프로젝트번호 : {$projectData['project']['projectNo']}  
입력URL 수신자 : {$projectData['project']['assortReceiver']}  
입력URL 수신메일 : {$projectData['project']['assortEmail']}  
입력일시 : {$projectData['project']['assortCustomerDt']}  
";

        $imsService = SlLoader::cLoad('ims', 'imsService');
        $hopeDt = SlCommonUtil::getDateCalc(date('Y-m-d'),5); //처리 완료일
        $saveTodoData = [
            'document' => [
                'todoType' => 'todo',
                'subject' => $customerName . '고객 발주수량 확인 (고객 발주수량 입력 완료)',
                'contents' => $contents,
                'hopeDt' => $hopeDt,
                'projectSno' => $params['sno'],
            ],
            'reqManagers'  => [
                '02001001', '02001003'
            ]
        ];
        $imsService->saveTodo($saveTodoData);

        $this->updateTotalQty($params);

    }

    /**
     * 사양서 고객 체크 완료 (로직)
     * @param $params
     * @return array
     * @throws \Exception
     */
    public function setOrderComplete($params){

        //우선 저장
        DBUtil2::update(ImsDBName::PROJECT, [
            'customerOrderConfirm' =>'p', //고객 입력완료상태로 변경
            'customerOrderConfirmDt' =>'now()',
        ], new SearchVo('sno=?', $params['sno']));

        DBUtil2::update(ImsDBName::PROJECT_EXT, [
            'stOrderConfirm' =>'10', //고객 입력완료상태로 변경
            'cpOrderConfirm' =>'now()', //고객 입력완료상태로 변경
        ], new SearchVo('projectSno=?', $params['sno']));

        $imsService = SlLoader::cLoad('ims', 'imsService');
        $projectData = $imsService->getProject(['sno'=>$params['sno']]);
        $customerName = $projectData['customer']['customerName'];

        //TO-DO 등록 ( 영업(02001001). 생산(02001003). )
        $contents = "
{$customerName} 고객이 사양서를 확정 하였습니다.
발주 처리 및 생산스케쥴 관리 바랍니다.
 
프로젝트번호 : {$projectData['project']['projectNo']}  
수신 : {$projectData['project']['customerOrderReceiver']}  
수신메일 : {$projectData['project']['customerOrderEmail']}  
확정 일시 : {$projectData['project']['customerOrderConfirmDt']}  
";

        $imsService = SlLoader::cLoad('ims', 'imsService');
        $hopeDt = SlCommonUtil::getDateCalc(date('Y-m-d'),5); //처리 완료일
        $saveTodoData = [
            'document' => [
                'todoType' => 'todo',
                'subject' => $customerName . '고객 사양서 확인 완료',
                'contents' => $contents,
                'hopeDt' => $hopeDt,
                'projectSno' => $params['sno'],
            ],
            'reqManagers'  => [
                '02001001', '02001003'
            ]
        ];

        //---------------------- 고객 사양서 체크 후 처리
        //날짜 입력
        //DBUtil2::update(ImsDBName::PROJECT_ADD_INFO,['completeDt'=>'now()'],new SearchVo("fieldDiv='custSpec' and projectSno=?",$params['sno']));
        //----------------------
        $imsService->saveTodo($saveTodoData);

        //ImsScheduleUtil::setScheduleCompleteDt($params['sno'],'order','now()');
        //ImsScheduleUtil::setProjectScheduleStatus($params['sno']);
    }


    /**
     * DEPRECATED
     * 사양서 URL 발송 (로직)
     * @param $params
     * @throws \Exception
     */
    public function sendOrderUrl($params){

        $mailUtil = SlLoader::cLoad('mail','SiteLabMailUtil','sl');
        $customerData = DBUtil2::getOne(ImsDBName::CUSTOMER, 'sno', $params['customerSno']);

        //우선 저장
        DBUtil2::update(ImsDBName::PROJECT, [
            'customerOrderConfirm' =>'r', //고객 입력요청상태로 변경
            'customerOrderReceiver' =>$params['receiver'],
            'customerOrderEmail' =>$params['email'],
            'customerOrderSendDt' =>'now()',
        ], new SearchVo('sno=?', $params['sno']));

        $key = SlCommonUtil::aesEncrypt($params['sno']);

        //견적 발송 (메일 발송)
        //$receiverName = urlencode($params['assortReceiver']);
        $mailData['subject'] = $customerData['customerName'].' 고객님 유니폼 디자인 가이드(사양서) 확인 요청 드립니다.';
        $mailData['from'] = 'innover@msinnover.com';
        $mailData['to'] = $params['email'];

        $host = SlCommonUtil::getHost();
        $replace['rc_companyName'] = $customerData['customerName'];
        $replace['rc_confirmFullUrl'] = "{$host}/ics/ics_guide.php?key={$key}";
        $replace['rc_confirmUrl'] = '사양서 확인 하기';

        $mailData['body'] = $mailUtil->getMailTemplate($replace,'customer_guide.php');
        $mailUtil->send($mailData['subject'], $mailData['body'], $mailData['from'], $mailData['to'], null, SlCommonUtil::getManagerMail());

        //사양서 발송 완료 처리
        //ImsUtil::updateSchedule('completeDt','now()','custSpec', $params['sno']);

        //sendOrderUrl
        ImsScheduleUtil::setProjectScheduleStatus($params['sno']);

        //발송이력 insert
        $aInsertHistory = [
            'sendType'=>'사양서',
            'projectSno'=>$params['sno'],
            'sendManagerSno'=>\Session::get('manager.sno'),
            'receiverName'=>$params['receiver'],
            'receiverMail'=>$params['email'],
            'subject'=>$mailData['subject'],
            'contents'=>$mailData['body'],
            'regDt'=>date('Y-m-d H:i:s'),
        ];
        DBUtil2::insert(ImsDBName::SEND_HISTORY, $aInsertHistory);

        //SitelabLogger::logger2(__METHOD__, '메일 발송 정보 확인'); //메일발송 내용.
        //SitelabLogger::logger2(__METHOD__, $mailData); //메일발송 내용.
    }

    /**
     * DEPRECATED
     * 구 제안서 발송
     * @param $params
     * @throws \Exception
     */
    public function sendProposalUrl($params){
        if(!isset($params['ccList'])){
            $params['ccList'] = [];
        }
        $params['ccList'][] = SlCommonUtil::getManagerMail();
        $ccList = implode(',',$params['ccList']);

        $mailUtil = SlLoader::cLoad('mail','SiteLabMailUtil','sl');
        $customerData = DBUtil2::getOne(ImsDBName::CUSTOMER, 'sno', $params['customerSno']);

        //제안서 발송 (메일 발송)
        $mailData['subject'] = $customerData['customerName'].' 고객님 제안서 전달 드립니다.';
        $mailData['from'] = 'innover@msinnover.com';
        $mailData['to'] = $params['email'];

        $replace['rc_companyName'] = $customerData['customerName'];
        $replace['rc_confirmFullUrl'] = $params['fileUrl'];
        $replace['rc_confirmUrl'] = '제안서 확인 하기';

        $mailData['body'] = $mailUtil->getMailTemplate($replace,'ims_proposal.php');

        $mailUtil->send($mailData['subject'], $mailData['body'], $mailData['from'], $mailData['to'], null, $ccList);

        //발송이력 insert
        $aInsertHistory = [
            'sendType'=>'제안서',
            'projectSno'=>$params['sno'],
            'sendManagerSno'=>\Session::get('manager.sno'),
            'receiverName'=>$params['receiver'],
            'receiverMail'=>$params['email'],
            'ccList'=>$ccList,
            'subject'=>$mailData['subject'],
            'contents'=>$mailData['body'],
            'regDt'=>date('Y-m-d H:i:s'),
        ];
        DBUtil2::insert(ImsDBName::SEND_HISTORY, $aInsertHistory);
        DBUtil2::update(ImsDBName::PROJECT, ['proposalDt'=>'now()'], new SearchVo('sno=?', $params['sno']));
    }

    /**
     * 업데이트 결과
     * @param $params
     * @return array
     * @throws \Exception
     */
    public function copyStyleBasicInfo($params){
        if( empty($params['srcSno']) ) throw new \Exception('원본 스타일 번호가 없습니다. (개발팀 문의)');
        if( empty($params['targetSno']) ) throw new \Exception('대상 스타일 번호가 없습니다. (개발팀 문의)');

        $updateRslt = [];

        $sql1 = "SET @new_sizeSpec = (SELECT sizeSpec FROM sl_imsProjectProduct WHERE sno={$params['srcSno']})";
        $updateRslt['srcSet'] = DBUtil2::runSql($sql1);
        $sql2 = "update sl_imsProjectProduct set sizeSpec = @new_sizeSpec where sno={$params['targetSno']}";
        $updateRslt['prdSet'] = DBUtil2::runSql($sql2);

        $eworkSrcData = DBUtil2::getOne(ImsDBName::EWORK,'styleSno',$params['srcSno']);
        $eworkSrcDataMark = DBUtil2::getOne(ImsDBName::EWORK,'styleSno',$params['srcSno'],false);
        $eworkData = DBUtil2::getOne(ImsDBName::EWORK,'styleSno',$params['targetSno']);

        $copyData = $eworkSrcData;
        $copyData['styleSno'] = $params['targetSno'];

        for($i=1;10>=$i;$i++){
            $copyData['markInfo'.$i] = $eworkSrcDataMark['markInfo'.$i];
        }

        $copyData['produceWarning'] = $eworkSrcDataMark['produceWarning'];
        unset($copyData['revision']);
        unset($copyData['sno']);
        unset($copyData['mainApproval']);
        unset($copyData['markApproval']);
        unset($copyData['careApproval']);
        unset($copyData['specApproval']);
        unset($copyData['materialApproval']);
        unset($copyData['packingApproval']);
        unset($copyData['batekApproval']);
        unset($copyData['writeDt']);
        unset($copyData['requestDt']);
        unset($copyData['regDt']);
        unset($copyData['modDt']);

        if( empty($eworkData) ){
            $iSrt = DBUtil2::insert(ImsDBName::EWORK, $copyData);
            $updateRslt['eworkSet'] = $iSrt;
        }else{
            //Update
            $uSrt = DBUtil2::update(ImsDBName::EWORK, $copyData, new SearchVo('styleSno=?', $params['targetSno']));
            $updateRslt['eworkSet'] = $uSrt;
        }
        //Revision Copy
        $revisionUpdateSql = "UPDATE sl_imsEwork AS target JOIN (
    SELECT revision
    FROM sl_imsEwork
    WHERE styleSno = {$params['srcSno']}
    LIMIT 1
) AS source SET target.revision = source.revision WHERE target.styleSno = {$params['targetSno']}";
        DBUtil2::runSql($revisionUpdateSql);

        //원부자재 업데이트
        $eworkService = SlLoader::cLoad('ims','ImsEworkService');

        $fabricList = DBUtil2::getList(ImsDBName::PRD_MATERIAL, 'typeStr = \'fabric\' and styleSno', $params['srcSno']);
        $subFabricList = DBUtil2::getList(ImsDBName::PRD_MATERIAL, 'typeStr = \'subFabric\' and styleSno', $params['srcSno']);
        $params['styleSno'] = $params['targetSno'];
        $eworkService->setMaterial($params, $fabricList, $subFabricList, $params['srcSno'].' 복사');

        //DBUtil2::delete(ImsDBName::PRD_MATERIAL, new SearchVo('styleSno=?', $params['targetSno']));
        /*$list = DBUtil2::getList(ImsDBName::PRD_MATERIAL, 'styleSno', $params['srcSno']);
        foreach($list as $each){
            $saveData = $each;
            $saveData['styleSno'] = $params['targetSno'];
            unset($saveData['regDt']);
            unset($saveData['modDt']);
            unset($saveData['sno']);
            DBUtil2::insert(ImsDBName::PRD_MATERIAL, $saveData);
        }*/

        return $updateRslt;
    }


    /**
     * 스타일 리스트 저장
     * @param $styleList
     * @return array
     * @throws \Exception
     */
    public function saveStyleList($styleList){
        $projectSno = 0;
        $saveSnoList = [];
        $sort=1;
        foreach($styleList as $each){
            $each['sort']=$sort;
            $sno = $this->saveStyle($each);
            $saveSnoList[] = $sno;
            $sort++;

            $styleInfo = DBUtil2::getOne(ImsDBName::PRODUCT, 'sno', $sno); //저장시 프로젝트 번호를 준다는 보장이 없어 불러온다.
            $projectSno = $styleInfo['projectSno'];

            //생산 정보가 있다면
            $productionInfo = DBUtil2::getOneSortData(ImsDBName::PRODUCTION, 'styleSno=?', $sno, 'regDt desc');
            if(!empty($productionInfo)){
                //스타일 정보와 연동.
                DBUtil2::update(ImsDBName::PRODUCTION,[
                    'msDeliveryDt' => $styleInfo['msDeliveryDt'],
                    'customerDeliveryDt' => $styleInfo['customerDeliveryDt'],
                ], new SearchVo('sno=?', $productionInfo['sno']));
            }
        }

        //스타일 명 저장. (legacy -> ImsUtil에서 처리 대체)
        /*if(!empty($saveSnoList)){
            $styleInfo = DBUtil2::getOne(ImsDBName::PRODUCT, 'sno', $saveSnoList[0]);
            $styleName = $styleInfo['productName'];
            if(count($saveSnoList)>1){
                $styleName .= ' 외 ' . ( count($saveSnoList)-1 ). '건' ;
            }
            DBUtil2::update(ImsDBName::PROJECT_EXT,[
                'salesStyleName' => $styleName
            ], new SearchVo('projectSno=?',$styleInfo['projectSno']));
        }*/

        return $saveSnoList;
    }

    /**
     * 스타일 저장 (FIXME : 더티체크 필수)
     * @param $style
     * @return mixed
     * @throws \Exception
     */
    public function saveStyle($style){
        unset($style['fabric']);
        unset($style['subFabric']);
        return $this->imsSave('projectProduct', $style, true);
    }


    /**
     * 스타일 퀄리티 사용 여부
     * @param $styleSno
     * @param string $isYn
     * @return mixed
     * @throws \Exception
     */
    public function setPassFabric($styleSno,$isYn='n'){
        return $this->imsSave('projectProduct', [
            'sno' => $styleSno,
            'fabricPass' => $isYn,
        ], true);
    }


    /**
     * 아소트 상태 변경
     * @param $params
     * @throws \Exception
     */
    public function setAssortStatus($params){
        $imsService = SlLoader::cLoad('ims', 'imsService');
        $updateData = ['assortApproval' => $params['status']];
        $extUpdateData = []; //스케쥴 업데이트
        if('r' === $params['status']){
            $extUpdateData['stAssortConfirm'] = '4';
            $extUpdateData['cpAssortConfirm'] = '0000-00-00';
            $updateData['assortCustomerDt'] = '0000-00-00 00:00:00'; //고객 입력 단계로 변경
        }else if('p' === $params['status']){ //확정
            $extUpdateData['stAssortConfirm'] = '10';
            $extUpdateData['cpAssortConfirm'] = 'now()';

            $updateData['assortManagerDt'] = 'now()';
            $updateData['assortApprovalManager'] = \Session::get('manager.managerNm');

            //협의수량 자동 업데이트
            $this->updateTotalQty($params);

            //아소트확정하면 분류패킹 insert -> packingSno(스타일테이블. 아소트구분들 중 하나라도 포함이 있는 스타일만) update.
            //packingSno != 0 인 스타일은 packing 묶음에서 빠짐. 그러나 일반적으로 생산/출고를 1개 프로젝트에서 여러번 나눠서 하는 경우는 없어서 1프로젝트 안의 스타일들의 packingSno는 동일함
            $aStyleList = DBUtil2::getList(ImsDBName::PRODUCT, "delFl='n' and projectSno", $params['sno'] );
            $aAllAssortByStyleSno = $aAssortByStyleSno = [];
            foreach($aStyleList as $val) {
                if ($val['packingSno'] == 0) {
                    $aAssort = json_decode($val['assort'], true);
                    foreach($aAssort as $val2) {
                        if ($val2['packingYn'] == 'Y') {
                            if (!isset($aAssortByStyleSno[$val['sno']])) $aAssortByStyleSno[$val['sno']] = [];
                            $aAssortByStyleSno[$val['sno']][] = $val2;
                        }
                        if (!isset($aAllAssortByStyleSno[$val['sno']])) $aAllAssortByStyleSno[$val['sno']] = [];
                        $aAllAssortByStyleSno[$val['sno']][] = $val2;
                    }
                }
            }
            $aStyleSnos = $aAllStyleSnos = [];
            foreach($aAssortByStyleSno as $key => $val) $aStyleSnos[] = $key;
            foreach($aAllAssortByStyleSno as $key => $val) $aAllStyleSnos[] = $key;

            //아소트구분 스타일별로 합치기(분류패킹용(고객노출), 모든생산)
            $aMergeAssortByStyleSno = [];
            foreach($aAssortByStyleSno as $key => $val) {
                if (!isset($aMergeAssortByStyleSno[$key])) $aMergeAssortByStyleSno[$key] = ['type'=>'일반','qtyType'=>'청구','optionList'=>[]];
                foreach($val as $key2 => $val2) {
                    foreach ($val2['optionList'] as $key3 => $val3) {
                        if (!isset($aMergeAssortByStyleSno[$key]['optionList'][$key3])) $aMergeAssortByStyleSno[$key]['optionList'][$key3] = 0;
                        $aMergeAssortByStyleSno[$key]['optionList'][$key3] += $val3;
                    }
                }
            }
            $aTmp = $aAssortByStyleSno;
            $aAssortByStyleSno = [];
            foreach($aTmp as $key => $val) {
                $aAssortByStyleSno[$key] = [$aMergeAssortByStyleSno[$key]];
            }
            $aMergeAssortByStyleSno = [];
            foreach($aAllAssortByStyleSno as $key => $val) {
                if (!isset($aMergeAssortByStyleSno[$key])) $aMergeAssortByStyleSno[$key] = ['type'=>'고객미노출','qtyType'=>'청구','optionList'=>[]];
                foreach($val as $key2 => $val2) {
                    foreach ($val2['optionList'] as $key3 => $val3) {
                        if (!isset($aMergeAssortByStyleSno[$key]['optionList'][$key3])) $aMergeAssortByStyleSno[$key]['optionList'][$key3] = 0;
                        $aMergeAssortByStyleSno[$key]['optionList'][$key3] += $val3;
                    }
                }
            }
            $aTmp = $aAllAssortByStyleSno;
            $aAllAssortByStyleSno = [];
            foreach($aTmp as $key => $val) {
                $aAllAssortByStyleSno[$key] = [$aMergeAssortByStyleSno[$key]];
            }

            //jsonCntSizeTotal, jsonCntSizeTotalims 구성
            $aJsonCntSizeTotal = $aJsonCntSizeTotalims = [];
            foreach ($aAssortByStyleSno as $key => $val) {
                foreach ($val as $val2) {
                    foreach ($val2['optionList'] as $key3 => $val3) {
                        //고객에게 발송할 아소트구분만 추려서 넣음(안전재고 등의 아소트구분은 고객에게 발송 안하므로 빠짐)
                        $aJsonCntSizeTotal[$key][$val2['type'].'___'.$val2['qtyType']][$key3] = ['customerQty'=>$val3, 'currQty'=>0, 'flagOverQty'=>false];
                    }
                }
            }
            foreach ($aAllAssortByStyleSno as $key => $val) {
                foreach ($val as $val2) {
                    foreach ($val2['optionList'] as $key3 => $val3) {
                        //실제 생산에 들어가는 모든 스타일, 아소트는 jsonCntSizeTotalims에 넣음 -> IMS에서 검수
                        if (!isset($aJsonCntSizeTotalims[$key][$key3])) $aJsonCntSizeTotalims[$key][$key3] = ['makeQty'=>0, 'currQty'=>0, 'storageQty'=>0]; //makeQty==제작수량(아소트에서 가져옴), currQty==발주수량(고객입력), storageQty==창고보관수량(IMS에서 입력), remainQty=제작수량-발주수량-창고보관수량(IMS에서 납품검수완료시)
                        $aJsonCntSizeTotalims[$key][$key3]['makeQty'] += $val3;
                    }
                }
            }

            //DB insert, update
            $aInsertPacking = ['customerSno'=>$aStyleList[0]['customerSno'], 'regManagerSno'=>\Session::get('manager.sno'), 'styleSnos'=>implode(',',$aStyleSnos), 'jsonCntSizeTotal'=>json_encode([$aJsonCntSizeTotal]), 'jsonCntSizeTotalims'=>json_encode([$aJsonCntSizeTotalims])];
            $iInsertPackingSno = DBUtil2::insert(ImsDBName::CUSTOMER_PACKING, $aInsertPacking);
            $oSVStyle = new SearchVo();
            $oSVStyle->setWhere("sno in (".implode(",",$aAllStyleSnos).")");
            DBUtil2::update(ImsDBName::PRODUCT, ['packingSno'=>$iInsertPackingSno], $oSVStyle);
        }else if('f' === $params['status']){ //취소
            $extUpdateData['stAssortConfirm'] = '4';
            $extUpdateData['cpAssortConfirm'] = '0000-00-00';

            $updateData['assortManagerDt'] = '0000-00-00 00:00:00';
            $updateData['assortApprovalManager'] = '';

            //분류패킹 삭제하기
            //namku(chk) N프로젝트==1분류패킹 감안한다면 다른 프로젝트 스타일의 packingSno도 0으로 해줘야함 and 다른 프로젝트 아소트확정취소도 해야함?
            $aStyleInfo = DBUtil2::getOne(ImsDBName::PRODUCT, 'projectSno', $params['sno']);
            $iPackingSno = $aStyleInfo['packingSno'];
            DBUtil2::update(ImsDBName::PRODUCT, ['packingSno'=>0], new SearchVo('projectSno=?', $params['sno']));
            DBUtil2::delete(ImsDBName::CUSTOMER_RECEIVER_DELIVERY, new SearchVo('packingSno=?', $iPackingSno));
            DBUtil2::delete(ImsDBName::CUSTOMER_PACKING, new SearchVo('sno=?', $iPackingSno));
        }

        DBUtil2::update(ImsDBName::PROJECT, $updateData ,new SearchVo('sno=?', $params['sno']));
        DBUtil2::update(ImsDBName::PROJECT_EXT, $extUpdateData ,new SearchVo('projectSno=?', $params['sno']));
    }

    /**
     * 총 수량 업데이트
     * @param $params
     */
    public function updateTotalQty($params){
        $imsService = SlLoader::cLoad('ims', 'imsService');
        $prdList = DBUtil2::getList(ImsDBName::PRODUCT, "delFl='n' and projectSno", $params['sno'] );
        foreach($prdList as $prd){
            $totalQty = 0;
            $assortList = json_decode($prd['assort'],true);
            foreach( $assortList as $assort ){
                foreach( $assort['optionList'] as $optionCnt ){
                    $totalQty += $optionCnt;
                }
            }
            $imsService->save(ImsDBName::PRODUCT,
                ['sno'=>$prd['sno'], 'prdExQty'=>$totalQty,'prdCount'=>$totalQty]
            );
        }
    }


}

