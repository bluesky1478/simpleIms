<?php
namespace Component\Ims;

use App;
use Component\Database\DBIms;
use Component\Database\DBTableField;
use Component\Ims\EnumType\PREPARED_TYPE;
use Component\Imsv2\ImsScheduleUtil;
use Component\Member\Manager;
use Component\Scm\ScmTkeService;
use Framework\Debug\Exception\AlertBackException;
use Framework\Utility\DateTimeUtils;
use LogHandler;
use Request;
use Session;
use SiteLabUtil\SlCommonUtil;
use SlComponent\Database\DBUtil2;
use SlComponent\Database\SearchVo;
use SlComponent\Mail\SiteLabMailUtil;
use SlComponent\Util\ExcelCsvUtil;
use SlComponent\Util\SitelabLogger;
use SlComponent\Util\SlCodeMap;
use SlComponent\Util\SlLoader;
use Component\Sms\Code;

/**
 * IMS 결재 관련 서비스
 * Class GoodsStock
 * @package Component\Goods
 */
class ImsApprovalService {

    private $sql;

    const APPROVAL_STATUS = [
        'ready' => '기안',
        'request' => '요청',
        'proc' => '진행',
        'complete' => 'PASS',//미사용
        'accept' => '결재완료',
        'reject' => '반려',
    ];

    /**
     * 결재 타입
     */
    const APPROVAL_TYPE = [ //n,r,p,f

        //$targetTableSearchVo = new SearchVo('sno=?', $beforeApprovalData[ImsApprovalService::APPROVAL_TYPE[$approvalType]['updateValueField']] );
        //작지 승인
        'eworkMain'     => [
            'name' => '작업지시서 메인 정보', 'fileDiv' => '' , 'dbTable' => ImsDBName::EWORK
            ,'statusField'=>'mainApproval', 'completeDt'=>'', 'updateValueField' => 'styleSno' , 'updateKey' => 'styleSno'
        ],
        'eworkMark'     => [
            'name' => '마크 정보', 'fileDiv' => '' , 'dbTable' => ImsDBName::EWORK
            ,'statusField'=>'markApproval', 'completeDt'=>'', 'updateValueField' => 'styleSno' , 'updateKey' => 'styleSno'
        ],
        'eworkCare'     => [
            'name' => '캐어라벨 정보', 'fileDiv' => '' , 'dbTable' => ImsDBName::EWORK
            ,'statusField'=>'careApproval', 'completeDt'=>'', 'updateValueField' => 'styleSno' , 'updateKey' => 'styleSno'
        ],
        'eworkSpec'     => [
            'name' => '사이즈스펙 정보', 'fileDiv' => '' , 'dbTable' => ImsDBName::EWORK
            ,'statusField'=>'specApproval', 'completeDt'=>'', 'updateValueField' => 'styleSno' , 'updateKey' => 'styleSno'
        ],
        'eworkMaterial'     => [
            'name' => '자재 정보', 'fileDiv' => '' , 'dbTable' => ImsDBName::EWORK
            ,'statusField'=>'materialApproval', 'completeDt'=>'', 'updateValueField' => 'styleSno' , 'updateKey' => 'styleSno'
        ],
        'eworkPacking'     => [
            'name' => '패킹 정보', 'fileDiv' => '' , 'dbTable' => ImsDBName::EWORK
            ,'statusField'=>'packingApproval', 'completeDt'=>'', 'updateValueField' => 'styleSno' , 'updateKey' => 'styleSno'
        ],
        'eworkBatek'     => [
            'name' => '바텍 정보', 'fileDiv' => '' , 'dbTable' => ImsDBName::EWORK
            ,'statusField'=>'batekApproval', 'completeDt'=>'', 'updateValueField' => 'styleSno' , 'updateKey' => 'styleSno'
        ],


        'salesPlan'     => [
            'name' => '영업 기획서', 'fileDiv' => '' , 'dbTable' => ImsDBName::PROJECT
            ,'statusField'=>'salesPlanApproval', 'completeDt'=>'', 'updateValueField' => 'projectSno'
        ]
        ,
        'plan'     => [
            'name' => '기획', 'fileDiv' => 'filePlan' , 'dbTable' => ImsDBName::PROJECT
            ,'statusField'=>'planConfirm', 'completeDt'=>'planEndDt', 'updateValueField' => 'projectSno'
            ]
        ,
        'proposal' => [
            'name' => '제안', 'fileDiv' => 'fileProposal', 'dbTable' => ImsDBName::PROJECT
            ,'statusField'=>'proposalConfirm' , 'completeDt'=>'proposalEndDt', 'updateValueField' => 'projectSno'
            ],
        'salePrice' => [
            'name' => '판매가', 'fileDiv' => '', 'dbTable' => ImsDBName::PROJECT
            ,'statusField'=>'prdPriceApproval' , 'completeDt'=>'salePriceCompleteDt', 'updateValueField' => 'projectSno'
            ],
        'cost' => [
            'name' => '생산가', 'fileDiv' => '', 'dbTable' => ImsDBName::PROJECT
            ,'statusField'=>'prdCostApproval' , 'completeDt'=>'costCompleteDt', 'updateValueField' => 'projectSno'
            ],
        'sampleFile1' => [
            'name' => '샘플의뢰서(지시서)', 'fileDiv' => '', 'dbTable' => ImsDBName::SAMPLE
            ,'statusField'=>'sampleFile1Approval', 'completeDt'=>'sampleOrderCompleteDt', 'updateValueField' => 'eachSno'
            ],
        'order' => [
            'name' => '사양서', 'fileDiv' => 'fileConfirm', 'dbTable' => ImsDBName::PROJECT
            ,'statusField'=>'prdConfirmApproval', 'completeDt'=>'orderCompleteDt', 'updateValueField' => 'projectSno'
            ],
    ];

    const READY     = ['val'=>'ready','name'=>'기안',];
    const REQ       = ['val'=>'request','name'=>'요청',];
    const PROC      = ['val'=>'proc','name'=>'진행',];
    const COMPLETE  = ['val'=>'complete','name'=>'PASS',];
    const ACCEPT    = ['val'=>'accept','name'=>'결재완료',];
    const REJECT    = ['val'=>'reject','name'=>'반려',];

    const APP_LINE_TITLE_CHK = [
        'positionCd' => [
            '02002001','02002007'
        ], //직급 : 대표, 이사
        'dutyCd' => [
            '02003002','02003003'
        ], //직급 : 팀장, 실장
    ];

    public function __construct(){
        //$this->sql =  SlLoader::sqlLoad(__CLASS__, false);
    }

    /**
     * 승인 맵 반환
     * @return mixed
     */
    public static function getApprovalTypeMap(){
        $map=[];
        foreach(ImsApprovalService::APPROVAL_TYPE as $key => $val){
            $map[$key] = $val['name'];
        }
        return $map;
    }

    public function saveHistory($params){
        /*$this->saveStatusHistory([
            'historyDiv' => $params['acceptDiv'],
            'projectSno' => $params['projectSno'],
            'beforeStatus' => ImsCodeMap::PROJECT_CONFIRM_TYPE[$projectData['project'][$params['acceptDiv']]]['name'],
            'afterStatus' => ImsCodeMap::PROJECT_CONFIRM_TYPE[$params['confirmStatus']]['name'],
            'reason' => $params['memo'],
        ]);*/
    }

    /**
     * 상태변경
     * @param $beforeApprovalData
     * @param $changeApprovalStatus
     * @param $changeRelationStatus
     * @throws \Exception
     */
    public function statusChangeCommon($beforeApprovalData, $changeApprovalStatus, $changeRelationStatus){
        //SitelabLogger::log($beforeApprovalData);

        $approvalType = $beforeApprovalData['approvalType'];
        $approvalSetupInfo = ImsApprovalService::APPROVAL_TYPE[$approvalType];

        DBUtil2::update(ImsDBName::TODO_REQUEST,[
            'approvalStatus' => $changeApprovalStatus
        ], new SearchVo('sno=?', $beforeApprovalData['sno']));

        //승인과 연계된 객체의 상태 처리
        $updateKey = ImsApprovalService::APPROVAL_TYPE[$approvalType]['updateKey'];
        $updateKey = empty($updateKey) ? 'sno=?':$updateKey.'=?';
        $targetTableSearchVo = new SearchVo($updateKey, $beforeApprovalData[ImsApprovalService::APPROVAL_TYPE[$approvalType]['updateValueField']] );

        DBUtil2::update($approvalSetupInfo['dbTable'],[
            $approvalSetupInfo['statusField'] => $changeRelationStatus
        ], $targetTableSearchVo); //상태값 변경

        //SitelabLogger::log('상태값 변경 조건 : ' . $changeApprovalStatus . ' / ' . $changeRelationStatus);
        //SitelabLogger::log($approvalSetupInfo);

        $updateSno = $beforeApprovalData[ImsApprovalService::APPROVAL_TYPE[$approvalType]['updateValueField']];

        //자동 처리 완료 대상!
        //ready 기안, proc 진행, reject 반려, accept 결재완료
        $autoCompleteField = ['salesPlan','plan','proposal','salePrice','cost','order','eworkMain'];

        //자동 처리 완료 대상 처리($autoCompleteField)
        if( in_array($approvalType, $autoCompleteField) ){
            //승인 처리
            if('accept' === $changeApprovalStatus){
                $imsService = SlLoader::cLoad('ims', 'imsService');
                $fncMap = [
                    //영업 기획 승인
                    'salesPlan' => (function() use($imsService, $updateSno, $approvalType) {
                        ImsScheduleUtil::setScheduleCompleteDt($updateSno,$approvalType,'now()');
                        $project = DBUtil2::getOne(ImsDBName::PROJECT, 'sno', $updateSno);
                        if(20 > $project['projectStatus']){
                            $imsService->setStatus(['projectSno'=>$updateSno,'projectStatus'=>20, 'reason'=>'영업 기획 승인 완료로 디자인 기획 단계로 변경'], true);
                        }
                    }),
                    //기획 승인
                    'plan' => (function() use($imsService, $updateSno, $approvalType) {
                        ImsScheduleUtil::setScheduleCompleteDt($updateSno,$approvalType,'now()');
                        $project = DBUtil2::getOne(ImsDBName::PROJECT, 'sno', $updateSno);
                        if(20 == $project['projectStatus']){ //이전단계가 제안일 경우
                            $imsService->setStatus(['projectSno'=>$updateSno,'projectStatus'=>30, 'reason'=>'기획 승인 완료로 제안 단계로 변경'], true);
                        }
                    }),
                    //제안 승인
                    'proposal' => (function() use($imsService, $updateSno, $approvalType) {
                        ImsScheduleUtil::setScheduleCompleteDt($updateSno,$approvalType,'now()');
                        $project = DBUtil2::getOne(ImsDBName::PROJECT, 'sno', $updateSno);
                        if(30 == $project['projectStatus']){ //이전 상태가 제안일 경우
                            $imsService->setStatus(['projectSno'=>$updateSno,'projectStatus'=>31, 'reason'=>'제안 승인 완료로 단계로 변경'], true);
                        }
                    }),
                    //판매가 승인 (별도 스케쥴 없음)
                    'salePrice' => (function() use($imsService, $updateSno, $approvalType) {
                        DBUtil2::update(ImsDBName::PRODUCT,['priceConfirm' => 'p', 'priceConfirmDt' => 'now()'],new SearchVo('projectSno=?',$updateSno));
                    }),
                    //생산가 최종 승인 (스케쥴있고, 상태 변경 없음)
                    'cost' => (function() use($imsService, $updateSno, $approvalType) {
                        ImsScheduleUtil::setScheduleCompleteDt($updateSno,'costConfirm','now()');
                    }),
                    //생산가 최종 승인 (스케쥴있고, 상태 변경 없음)
                    'eworkMain' => (function() use($imsService, $updateSno, $approvalType) {
                        SitelabLogger::log('작지 후처리 시작');
                        $prdInfo = DBUtil2::getOne(ImsDBName::PRODUCT, 'sno', $updateSno);
                        $project = DBUtil2::getOne(ImsDBName::PROJECT, 'sno', $prdInfo['projectSno']);
                        $projectSno = $project['sno'];
                        $sql = "select 
                            count(1) as allCnt,  sum(if(a.mainApproval = 'p',1,0)) as acceptCnt
                            from sl_imsEwork a 
                            join sl_imsProjectProduct b 
                              on a.styleSno = b.sno
                            join sl_imsProject c 
                              on b.projectSno = c.sno
                            where b.delFl = 'n'
                              and c.sno = {$projectSno}";
                        $workStatusInfo = DBUtil2::runSelect($sql)[0];
                        if(!empty($workStatusInfo) && $workStatusInfo['allCnt']==$workStatusInfo['acceptCnt']  ) { //이전 상태가 발주준비일 경우 + 모든 상품 완료시
                            //ImsScheduleUtil::setScheduleCompleteDt($projectSno,'order','now()');
                            $sql = "update sl_imsProjectExt set cpOrder = now() where projectSno='{$projectSno}'";
                            $updateRslt = DBUtil2::runSql($sql);
                            SitelabLogger::log($sql);
                            SitelabLogger::log('스케쥴 강제 업데이트 : '. $updateRslt);
                        }
                        if(50 == $project['projectStatus'] && !empty($workStatusInfo) && $workStatusInfo['allCnt']==$workStatusInfo['acceptCnt']  ){ //이전 상태가 발주준비일 경우 + 모든 상품 완료시
                            //SitelabLogger::log('조건에 맞아 단계 변경함.');
                            $imsService->setStatus(['projectSno'=>$projectSno,'projectStatus'=>60, 'reason'=>'모든 스타일 작업지시서 승인 완료로 단계로 변경'], true);
                        }
                    }),
                ];

                //후처리 실행 , 승인 완료시 완료일자 등록 등
                if (array_key_exists($approvalType, $fncMap)) {
                    $fncMap[$approvalType]();
                }
            }else{
                //반려, 취소
                if( 'cost' === $approvalType ){
                    ImsScheduleUtil::setScheduleCompleteDt($updateSno,'costConfirm');
                }else if( 'salePrice' === $approvalType ){
                    DBUtil2::update(ImsDBName::PRODUCT,['priceConfirm' => 'r', 'priceConfirmDt' => ''],new SearchVo('projectSno=?',$updateSno));
                }else if( 'eworkMain' === $approvalType ){ //작지 취소 시
                    if( !empty($beforeApprovalData['styleSno']) ){
                        DBUtil2::update(ImsDBName::EWORK, ['mainApproval'=>'n','materialApproval'=>'n'], new SearchVo('styleSno=?', $beforeApprovalData['styleSno']));
                        DBUtil2::update(ImsDBName::PRODUCT, ['workStatus'=>1], new SearchVo('sno=?', $beforeApprovalData['styleSno']));
                    }
                    ImsScheduleUtil::setScheduleCompleteDt($updateSno,'order'); //완료일자 복구
                }else{
                    ImsScheduleUtil::setScheduleCompleteDt($updateSno,$approvalType); //완료일자 복구
                }
            }
        }

        SitelabLogger::log('상태 변경 완료');

    }

    /**
     * 판매가 승인 완료시 처리
     * @param $beforeApprovalData
     * @throws \Exception
     */
    public function acceptSalePrice($beforeApprovalData){
        //판매가 승인 후처리.
        //프로젝트 스타일 선택된 견적을 모두 확정 처리 한다.
        $projectSno = $beforeApprovalData['projectSno'];
        $prdList = DBUtil2::getList(ImsDBName::PRODUCT,"delFl='n' and projectSno",$projectSno);
        //Style별 확정 처리
        foreach($prdList as $prd){
            DBUtil2::update(ImsDBName::PRODUCT, [
                'priceConfirm' => 'p',
                'priceConfirmDt' => 'now()',
            ], new SearchVo('sno=?', $prd['sno']));
        }
        $this->statusChangeCommon($beforeApprovalData, 'accept', 'p');
    }

    /**
     * 생산가 처리 완료시 처리
     * @param $beforeApprovalData
     * @throws \Exception
     */
    public function acceptCost($beforeApprovalData){

        $eworkService = SlLoader::cLoad('ims','ImsEworkService');

        //생산가 승인 후처리.
        //프로젝트 스타일 선택된 견적을 모두 확정 처리 한다.
        $projectSno = $beforeApprovalData['projectSno'];
        $prdList = DBUtil2::getList(ImsDBName::PRODUCT,"delFl='n' and projectSno",$projectSno);

        //Style별 확정 처리
        $projectInfo = DBUtil2::getOne(ImsDBName::PROJECT, 'sno', $projectSno);

        //Validation (기성복은 필요 없음)
        if( 4 != $projectInfo['projectType'] ){
            foreach($prdList as $prd){
                if( empty($prd['estimateConfirmSno']) && 4 != $beforeApprovalData['projectType'] ) throw new \Exception($prd['productName'] . '은(는) 선택된 견적이 없습니다.');
            }
        }

        foreach($prdList as $prd){
            //기성복은 견적 없음
            if(4 != $projectInfo['projectType']){

                //estimate처리 상태 변경
                $estimateData = DBUtil2::getOne(ImsDBName::ESTIMATE, 'sno', $prd['estimateConfirmSno']);
                $estimateSaveData['reqStatus'] = '5'; //확정처리
                //SitelabLogger::logger2(__METHOD__, '견적 저장 정보');
                //SitelabLogger::logger2(__METHOD__, $estimateData);
                DBUtil2::update(ImsDBName::ESTIMATE, $estimateSaveData, new SearchVo('sno=?', $estimateData['sno']));

                //견적(estimate) 내용을 그대로.
                $copyFieldList = ['Count','ConfirmSno','ConfirmManagerSno','ConfirmDt','Status',];
                foreach($copyFieldList as $copyField){
                    $prdSaveData['prdCost'.$copyField] = $prd['estimate'.$copyField]; //견적을 확정 처리
                }
            }else{
                //기성복 처리
                $prdSaveData['estimateStatus']=2;
                $prdSaveData['prdCostStatus']=2;
                $prdSaveData['estimateConfirmSno'] = -1;
                $prdSaveData['prdCostConfirmSno'] = -1;
                $prdSaveData['estimateConfirmManagerSno'] = SlCommonUtil::getManagerSno();
                $prdSaveData['prdCostConfirmManagerSno'] = SlCommonUtil::getManagerSno();
                $prdSaveData['prdCostConfirmDt'] = 'now()';
                $prdSaveData['estimateConfirmDt'] = 'now()';
            }
            $prdSaveData['prdCost'] = $prd['estimateCost']; //견적가로 업데이트.

            DBUtil2::update(ImsDBName::PRODUCT, $prdSaveData, new SearchVo('sno=?', $prd['sno']));

            //원부자재 복사한다.
            $params['styleSno'] = $prd['sno'];
            $params['costSno'] = $estimateData['sno'];
        }
        
        //SitelabLogger::logger2(__METHOD__, '승인처리~~~~~~~');
        $this->statusChangeCommon($beforeApprovalData, 'accept', 'p');

        //TODO : 기성복 300만원 이상 TO-DO LIST 등록
        //$this->addSimpleTodoData($subject, $hopeDt, $contents, [15]);

        //생산가 확정시 메일 발송
        $customer = DBUtil2::getOne(ImsDBName::CUSTOMER, 'sno', $projectInfo['customerSno']);
        $subject = "생산가 확정 ({$customer['customerName']} / {$projectSno})";
        SiteLabMailUtil::sendSystemMail($subject, $subject.'<br><br>확인해보시기 바랍니다.', implode(',',ImsCodeMap::COST_APPROVAL_ALARM_LIST));
    }

    /**
     * 생산가 반려
     * @param $beforeApprovalData
     * @throws \Exception
     */
    public function rejectCost($beforeApprovalData){
        $this->rollbackConfirmCost($beforeApprovalData);
        $this->statusChangeCommon($beforeApprovalData, 'reject', 'f');
    }

    /**
     * 생산가 결재 요청
     * @param $beforeApprovalData
     * @throws \Exception
     */
    public function procCost($beforeApprovalData){
        $this->rollbackConfirmCost($beforeApprovalData);
        $this->statusChangeCommon($beforeApprovalData, 'proc', 'n');
    }

    /**
     * 메인 승인
     * @param $beforeApprovalData
     * @throws \Exception
     */
    public function acceptEworkMain($beforeApprovalData){
        //그냥 메인만 승인
        $updatePrdApproval = [];
        foreach(ImsCodeMap::EWORK_TYPE as $eworkType => $eworkValue){
            $updatePrdApproval[$eworkType.'Approval'] = 'p';
        }
        DBUtil2::update(ImsDBName::EWORK, $updatePrdApproval, new SearchVo('styleSno=?',$beforeApprovalData['styleSno']));
        $this->statusChangeCommon($beforeApprovalData, 'accept', 'p');
    }
    //메인 반려
    public function rejectEworkMain($beforeApprovalData){
        if( 1 == $beforeApprovalData['projectType'] ){ //리오더라면 자동 승인 처리
            $updatePrdApproval = [];
            foreach(ImsCodeMap::EWORK_TYPE as $eworkType => $eworkValue){
                $updatePrdApproval[$eworkType.'Approval'] = 'f';
            }
            DBUtil2::update(ImsDBName::EWORK, $updatePrdApproval, new SearchVo('styleSno=?',$beforeApprovalData['styleSno']));
        }
        $this->statusChangeCommon($beforeApprovalData, 'reject', 'f');
    }
    //메인 진행
    public function procEworkMain($beforeApprovalData){
        if( 1 == $beforeApprovalData['projectType'] ){ //리오더라면 자동 승인 처리
            $updatePrdApproval = [];
            foreach(ImsCodeMap::EWORK_TYPE as $eworkType => $eworkValue){
                $updatePrdApproval[$eworkType.'Approval'] = 'r';
            }
            DBUtil2::update(ImsDBName::EWORK, $updatePrdApproval, new SearchVo('styleSno=?',$beforeApprovalData['styleSno']));
        }
        $this->statusChangeCommon($beforeApprovalData, 'proc', 'r');
    }

    /**
     * 생산가 승인 롤백
     * @param $beforeApprovalData
     * @throws \Exception
     */
    public function rollbackConfirmCost($beforeApprovalData){
        $projectSno = $beforeApprovalData['projectSno'];
        $prdList = DBUtil2::getList(ImsDBName::PRODUCT,"delFl='n' and projectSno",$projectSno);
        //Style별 확정 처리
        foreach($prdList as $prd){
            //estimate처리 상태 변경
            $estimateData = DBUtil2::getOne(ImsDBName::ESTIMATE, 'sno', $prd['estimateConfirmSno']);
            $estimateSaveData['reqStatus'] = '3'; //처리완료로

            DBUtil2::update(ImsDBName::ESTIMATE, $estimateSaveData, new SearchVo('sno=?', $estimateData['sno']));

            $copyFieldList = ['Count','ConfirmSno','ConfirmManagerSno','ConfirmDt'];
            foreach($copyFieldList as $copyField){
                $prdSaveData['prdCost'.$copyField] = ''; //견적을 확정 처리
            }
            $prdSaveData['prdCost'] = 0; //견적을 확정 처리
            $prdSaveData['prdCostStatus'] = 0;
            DBUtil2::update(ImsDBName::PRODUCT, $prdSaveData, new SearchVo('sno=?', $prd['sno']));
        }
    }

}

