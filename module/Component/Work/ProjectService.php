<?php
namespace Component\Work;

use App;
use Session;
use Component\Database\DBTableField;
use Component\Member\Manager;
use Framework\Utility\DateTimeUtils;
use LogHandler;
use Request;
use Exception;
use Framework\Debug\Exception\AlertBackException;
use SiteLabUtil\SlCommonUtil;
use SlComponent\Database\DBUtil2;
use SlComponent\Database\SearchVo;
use SlComponent\Database\TableVo;
use SlComponent\Util\DocumentStruct;
use SlComponent\Util\SitelabLogger;
use SlComponent\Util\SlLoader;
use SlComponent\Util\SlProjectCodeMap;
use SlComponent\Util\SlSmsUtil;

/**
 * 재고 리스트 서비스
 * Class GoodsStock
 * @package Component\Goods
 */
class ProjectService {

    private $sql;

    public function __construct(){
        $this->sql = \App::load(\Component\Work\Sql\ProjectSql::class);
    }

    /**
     * 프로젝트 등록/수정
     * @param $param
     * @throws Exception
     */
    public function saveProject( $param ){
        //SitelabLogger::logger('=========> saveProject');
        //SitelabLogger::logger($param);

        if( empty($param['sno']) ){
            // PlanData 기본 구조 . ( 최초 등록시에는 PlanData가 없음 )
            //초기 커스텀 PlanDt
            $param['customerPlanDt'] = json_encode($this->getDefaultCustomerPlanDt());
            DBUtil2::insert('sl_project', $param);
        }else{
            $saveData = $param;
            $saveData['planData'] = json_encode($saveData['planData']);
            $saveData['productData'] = json_encode($saveData['productData']);

            //History가 없다면 자유롭게 저장 , 있으면 안내일정에서만 수정 가능하다.
            $planHistory = DBUtil2::getOne('sl_workPlanHistory', 'projectSno', $param['sno']);
            if( empty($planHistory) ){
                $saveData['customerPlanDt'] = json_encode($saveData['customerPlanDt']);
            }else{
                $projectData = $this->getProjectData($param['sno']);
                $saveData['customerPlanDt'] = json_encode($projectData['customerPlanDt']);
            }

            $sno = $saveData['sno'];
            unset($saveData['sno']);
            DBUtil2::update('sl_project', $saveData, new SearchVo('sno=?', $sno));
        }
    }

    /**
     * 기본 고객 안내 일정
     * @return \string[][]
     */
    public function getDefaultCustomerPlanDt(){
        return [
            0=>[
                'title' => '미팅',
                'planDt' => '',
                'confirmDt' => '',
            ],
            1=>[
                'title' => '포트폴리오',
                'planDt' => '',
                'confirmDt' => '',
            ],
            2=>[
                'title' => '실물샘플',
                'planDt' => '',
                'confirmDt' => '',
            ],
            3=>[
                'title' => '디자인가이드(발주)',
                'planDt' => '',
                'confirmDt' => '',
            ],
            4=>[
                'title' => '생산완료',
                'planDt' => '',
                'confirmDt' => '',
            ],
            5=>[
                'title' => '납품',
                'planDt' => '',
                'confirmDt' => '',
            ],
        ];
    }

    /**
     * 프로젝트의 문서를 반환
     * @param $param
     * @return mixed
     */
    public function getProjectDocument( $param ){
        $documentService = SlLoader::cLoad('work','documentService','');
        $documentData = $documentService->getDocument($param);
        $documentData['projectData'] = $this->getProjectData( gd_isset($documentData['projectSno'], $param['projectSno']) );

        $defaultDocumentService = SlLoader::cLoad('work','defaultDocumentService','');
        $documentData = $defaultDocumentService->setDefaultProjectDocument($documentData, $param);

        return $documentData;
    }

    /**
     * 프로젝트에 속한 문서 포함 정보 반환
     * @param $sno
     * @return mixed
     */
    public function getProjectDataWithDocument( $sno ){

        $projectData = $this->getProjectData($sno);

        $documentService = SlLoader::cLoad('work','documentService','');

        //set PlanData
        foreach( SlProjectCodeMap::PRJ_DOCUMENT as $docDept => $typeData ){

            $projectData['planData'][$docDept]['typeName'] = $typeData['typeName'];

            if( empty( $projectData['planData'][$docDept]['planDt'] ) ){
                $projectData['planData'][$docDept]['planDt'] = '';
            }
            if( empty( $projectData['planData'][$docDept]['confirmDt'] ) ){
                $projectData['planData'][$docDept]['confirmDt'] = '';
            }

            foreach( $typeData['typeDoc'] as $docType => $docData ){
                //최신 문서 넣기
                $loadCondition['latest'] = 'y';
                $loadCondition['docDept'] = $docDept;
                $loadCondition['docType'] = $docType;
                $loadCondition['projectSno'] = $sno;

                $document = $documentService->getDocument($loadCondition);

                if( 'SALES' === $document['docDept'] && 20 == $document['docType'] ){
                    //SitelabLogger::logger($document);
                }

                if( empty($document['docDept']) ){
                    $docData['document'] = [];
                }else{
                    //문서에서 필요한 부분만 넣는다.
                    $withDocumentData = SlCommonUtil::getAvailData($document, [
                        'applyManagers','regManagerSno','regManagerName','isApplyFl','tempFl','sno','version', 'isCustomerApplyFl', 'isCustomerApplyDt', 'regDt'
                    ]);
                    $withDocumentData['isCustomerApplyDt'] = gd_date_format('Y-m-d',$withDocumentData['isCustomerApplyDt']);
                    $withDocumentData['sendDt'] = gd_date_format('Y-m-d',$document['docData']['sendDt']);
                    $withDocumentData['feedbackDt'] = gd_date_format('Y-m-d',$document['docData']['feedbackDt']);

                    $docData['document'] = $withDocumentData;
                }
                $projectData['planData'][$docDept]['typeDoc'][$docType] = $docData;
            }
            $projectData['planData'][$docDept]['documentCount'] = count($typeData['typeDoc']);
        }

        return $projectData;
    }

    /**
     * 프로젝트 데이터 가져오기
     * @param $sno
     * @return mixed
     */
    public function getProjectData( $sno ){

        $projectData = $this->sql->getProjectData($sno);

        //고객 안내 일정 설정
        $projectData['customerPlanDt'] =json_decode(gd_htmlspecialchars_stripslashes($projectData['customerPlanDt']),true);

        if( empty($projectData['customerPlanDt']) ){
            $projectData['customerPlanDt'] = $this->getDefaultCustomerPlanDt();
        }

        //미팅 데이터
        $documentService = SlLoader::cLoad('work','documentService','');
        $defaultSetData = [
          1 => ['docDept'=>'DESIGN','docType'=>20],
          3 => ['docDept'=>'DESIGN','docType'=>20],
        ];
        foreach( $defaultSetData as $defaultKey => $defaultData){
            $projectData['customerPlanDt'][$defaultKey]['confirmDt'] = $documentService->getLatestDocumentData($defaultData['docDept'], $defaultData['docType'], $sno)['isCustomerApplyDt'];
        }

        //아직 미팅보고서 발송 전이라면...
        $history = DBUtil2::getList('sl_workPlanHistory','projectSno', $sno);
        if( empty($history) ){
            $projectData['isPlanDtModify'] = false;
        }else{
            $projectData['isPlanDtModify'] = true;
        }

        $projectData['description'] = gd_htmlspecialchars_stripslashes($projectData['description']);
        SlCommonUtil::setCodeToName('projectType', WorkCodeMap::MS_PROPOSAL_TYPE, $projectData );
        SlCommonUtil::setCodeToName('projectStatus', SlProjectCodeMap::PRJ_STATUS, $projectData );
        SlCommonUtil::setCodeToName('companyDiv', WorkCodeMap::COMP_DIV, $projectData );

        $companyData = DBUtil2::getOne('sl_workCompany','sno',$projectData['companySno']);
        $companyManagerList = json_decode($companyData['companyManager'],true);
        $companyData['companyManager'] = $companyManagerList;

        //담당자 리스트.
        $companyManagers = [];
        foreach($companyManagerList as $manager){
            $addedInfoList = [];
            $addedInfoList[] = gd_isset($manager['phone'],$manager['cellPhone']);
            $addedInfoList[] = $manager['email'];
            $addedInfo = empty( implode('',$addedInfoList) )?'':'<span class="text-muted"> ('. implode(' / ',$addedInfoList) .')</span>';
            $manager['html'] = $manager['name'].$addedInfo;
            $companyManagers[] = $manager;
        }

        $projectData['companyData'] = $companyData;
        $projectData['companyManagers'] = $companyManagers;

        $projectData['salesManagerCellPhone'] = SlCommonUtil::getCellPhoneFormat($projectData['salesManagerCellPhone']);
        $projectData['designManagerCellPhone'] = SlCommonUtil::getCellPhoneFormat($projectData['designManagerCellPhone']);

        $projectData['planData'] = json_decode($projectData['planData'],true);
        $projectData['productData'] = json_decode(gd_htmlspecialchars_stripslashes($projectData['productData']),true);

        //미팅보고서 정보
        $documentService = SlLoader::cLoad('work','documentService','');
        $projectData['meetingData'] = $documentService->getLatestDocumentData('SALES', '20', $sno);

        $this->refinePickerDate($projectData,['meetingDt','hopeDeliveryDt', 'deadlineDt']);

        return $projectData;
    }

    /**
     * Picker Date refine
     * @param $projectData
     * @param $fieldList
     */
    private function refinePickerDate(&$projectData, $fieldList){
        foreach($fieldList as $field){
            if( '0000-00-00' === $projectData[$field] ){
                $projectData[$field] = '';
            }
        }
    }

    /**
     * 프로젝트 상태 및 문서 상태 체크 및 업데이트
     * @param $projectSno
     * @throws Exception
     */
    public function setProjectStatus($projectSno){
        $documentService = SlLoader::cLoad('work','documentService','');
        $successDocumentList = [];

        $projectData = $this->getProjectData($projectSno);

        $stepAccept = [];

        //각 문서별 승인상태 체크
        //사양서 고객 체크.
        $isCustomerSuccess = false;

        //최소 프로젝트 고객안내일정 현재 단계
        $minProjectStep = 0;
        
        foreach($projectData['planData'] as $docDept => $dept){

            $successDocumentList[$docDept]['successCount'] = 0;
            foreach ($dept['typeDoc'] as $docType => $docData) {

                $latestDocument = $documentService->getDocument(['latest'=>'y','docDept'=>$docDept,'docType'=>$docType,'projectSno'=>$projectSno]);

                if(!empty($latestDocument['docDept']) && !empty($latestDocument['applyManagers']) ){
                    $acceptDocumentCount = 0;
                    $rejectStatus = 'n';
                    foreach( $latestDocument['applyManagers'] as $applyEach ){
                        if( 'y' === $applyEach['status'] || 'p' === $applyEach['status'] ){
                            $acceptDocumentCount++;
                        }else{
                            if( 'r' === $applyEach['status'] ){
                                $rejectStatus = 'r';
                            }
                        }
                    }

                    if( count($latestDocument['applyManagers']) === $acceptDocumentCount){
                        //문서 상태 변경
                        $documentService->setDocumentStatus($latestDocument['sno'], 'y');
                        $successDocumentList[$docDept]['successCount']++;

                        // 최소 고객안내 체크 : 포트폴리오
                        if( 'DESIGN' === $docDept && 20 == $docType ){
                            $minProjectStep = 1;
                        }
                        // 최소 고객안내 체크 : 디자인 가이드
                        if( 'ORDER2' === $docDept && 10 == $docType ){
                            $minProjectStep = 3;
                        }
                        //SitelabLogger::logger('문서승인처리');
                    }else{
                        $documentService->setDocumentStatus($latestDocument['sno'], $rejectStatus); //미승인 혹은 반려.
                    }
                    $successDocumentList[$docDept]['documentCount']++;
                }

                // 사양서(유니폼 디자인 가이드) 고객승인 여부 체크
                if( 'ORDER2' === $docDept && 10 == $docType ){
                    // 고객승인 체크
                    if( 'y' === $latestDocument['isCustomerApplyFl'] ){
                        $isCustomerSuccess = true;
                    }
                }

            }

            if( $successDocumentList[$docDept]['successCount'] === $successDocumentList[$docDept]['documentCount'] ){
                //승인
                $projectData['planData'][$docDept]['docAccept'] = 'y';
                $projectData['planData'][$docDept]['confirmDt'] = date('Y-m-d');
                $stepAccept[$docDept] = 'y';
            }else{
                //미승인
                $projectData['planData'][$docDept]['docAccept'] = 'n';
                $projectData['planData'][$docDept]['confirmDt'] = '0000-00-00';
                $stepAccept[$docDept] = 'n';
            }
        }

        if( empty($projectData['projectStatus']) && $this->checkAccept($stepAccept, ['SALES','DESIGN','QC']) ){
            //1. 견적단계라면 ---> 문서 확인 후 주문단계로 자동 변경
            $projectData['projectStatus'] = 1; //주문단계 상태로 변경
        }else if( 1 == $projectData['projectStatus'] && $this->checkAccept($stepAccept, ['ORDER1','ORDER2','ORDER3']) && $isCustomerSuccess ){
            //2. 주문단계라면 ---> 문서 확인 후 생산단계로 자동 변경
            $projectData['projectStatus'] = 2; //생산단계 상태로 변경
        }

        if( $minProjectStep > $projectData['customerPlanStatus'] ){
            $projectData['customerPlanStatus'] = $minProjectStep;
        }

        $this->saveProject($projectData);
    }

    /**
     * 문서 승인확인
     * @param $stepAccept
     * @param $checkList
     * @return bool
     */
    public function checkAccept($stepAccept, $checkList){
        $result = true;
        foreach($checkList as $each){
            if( 'y' === $stepAccept[$each]){
                $result &= true;
            }else{
                $result &= false;
            }
        }
        return $result;
    }


    /**
     * 고객 안내일정 변경
     * @param $projectSno
     * @param $step
     * @throws Exception
     */
    public function setCustomerPlanStep($projectSno, $step){
        DBUtil2::update('sl_project', ['customerPlanStatus'=>$step],new SearchVo('sno=?',$projectSno));
    }

    /**
     * 프로젝트 플랜 최초 기록
     * @param $projectSno
     */
    public function setFirstProjectPlan($projectSno){
        $planHistory = DBUtil2::getOne('sl_workPlanHistory', 'projectSno', $projectSno);
        if( empty($planHistory) ) {
            $projectService = SlLoader::cLoad('work','projectService','');
            $projectData = $projectService->getProjectData($projectSno);
            $firstPlanData['projectSno'] = $projectSno;
            $firstPlanData['reasonType'] = '1';
            $firstPlanData['reasonText'] = '미팅보고서 발송으로 최초 등록';
            $this->savePlanHistory($firstPlanData, '', $projectData['customerPlanDt']);
        }
    }

    /**
     * 진행 계획 수정 이력 저장
     * @param $param
     * @param $beforeHistory
     * @param $afterHistory
     */
    public function savePlanHistory($param, $beforeHistory, $afterHistory){
        $saveData['projectSno'] = $param['projectSno'];
        $saveData['beforeStepData'] = json_encode($beforeHistory);
        $saveData['afterStepData'] = json_encode($afterHistory);
        $saveData['reasonType'] = $param['reasonType'];
        $saveData['reasonText'] = $param['reasonText'];
        $saveData['managerSno'] = \Session::get('manager.sno');
        DBUtil2::insert('sl_workPlanHistory', $saveData);
    }

    /**
     * 계획 수정 이력
     * @param $projectSno
     * @return array
     */
    public function getPlanHistory($projectSno){
        $tableList= [
            'a' => //메인
                [
                    'data' => [ 'sl_workPlanHistory' ]
                    , 'field' => ['*']
                ]
            , 'b' => //승인자
                [
                    'data' => [ DB_MANAGER, 'JOIN', 'a.managerSno = b.sno' ]
                    , 'field' => ['managerNm']
                ]
        ];
        $table = DBUtil2::setTableInfo($tableList);
        $searchVo = new SearchVo(['projectSno=?'], [ $projectSno ] );
        $searchVo->setOrder('regDt desc');

        $refineList = [];
        $list = DBUtil2::getComplexList($table, $searchVo);
        foreach($list as $each){
            $each['beforeStepData2'] = json_decode( $each['beforeStepData'], true);
            $each['afterStepData2']  = json_decode( $each['afterStepData'], true);
            $refineList[] = $each;
        }
        return $refineList;
    }


    /**
     * 최근 작업지시서에서 상품 가져오기.
     * @param $projectSno
     * @return array
     */
    public function getLatestWorkProduct($projectSno){
        $documentService = SlLoader::cLoad('work','documentService','');
        $latestDocument = $documentService->getLatestDocumentData('ORDER3', 10, $projectSno);
        $defaultDataList = [];
        foreach($latestDocument['docData']['sampleData'] as $sampleData){
            $defaultData = $this->getDefaultProduct();
            $defaultData['prdName'] = $sampleData['productName'];
            $defaultData['prdStyleType'] = $sampleData['styleType'];
            $defaultData['prdStyleName'] = $sampleData['styleName'];
            $defaultData['factorySno'] = $sampleData['sampleFactorySno'];
            $defaultData['count'] = $sampleData['itemTotalCount'];
            $defaultDataList[] = $defaultData;
        }
        return $defaultDataList;
    }
    
    /**
     * 프로젝트 상품 기본 값 반환
     * @return array
     */
    public function getDefaultProduct(){
        $defaultData = DocumentStruct::PRJ_PRODUCT;
        $defaultData['producePlan'] = $this->getDefaultProductPlan();
        return $defaultData;
    }

    /**
     * 기본 고객
     * @return array
     */
    public function getDefaultProductPlan(){
        $defaultData = [];
        foreach(DocumentStruct::PRJ_PRODUCT_PLAN_LIST as $each){
            $defaultData[] = [
                'planDt' => '',
                'completeDt' => '',
            ];
        }
        return $defaultData;
    }


    /**
     * 관련 문서 보내기
     * @param $params
     * @return mixed|string
     * @throws Exception
     */
    public function sendEmail($params){

        $documentService = SlLoader::cLoad('work','documentService','');

        $sno=$params['sno'];
        $receiverName=$params['mailReceiverName'];
        $email=$params['sendEmail'];

        if( !empty($email)  ){

            $projectDocumentData = $this->getProjectDocument(['sno'=>$sno]);

            $isSendMailHistoryCount = DBUtil2::getCount('sl_sendMailHistory', new SearchVo('documentSno=?', $sno));

            //발송이력 업데이트
            $mailSendHistory['documentSno'] = $sno;
            $mailSendHistory['mailReceiverName'] = $receiverName;
            $mailSendHistory['sendEmail'] = $email;
            $mailSendHistory['managerSno'] = \Session::get('manager.sno');

            $sendNo = DBUtil2::insert('sl_sendMailHistory', $mailSendHistory);

            //문서열기
            $mailData = $documentService->getSendEmailData($sno, $email, $sendNo, $projectDocumentData);

            $data = $mailData['data'];

            $mailUtil = SlLoader::cLoad('mail','SiteLabMailUtil','sl');
            $mailUtil->send($mailData['subject'] , $mailData['body'], $mailData['from'], $mailData['to']);

            $data['docData']['sendDt'] = SlCommonUtil::getDefaultDateTime();
            $data['docData']['sendEmail'] = $email;
            $data['docData']['mailReceiverName'] = $receiverName;

            $documentService->updateDocData($data['docData'], $sno);

            //문서에 대한 메일을 처음 발송한다면.
            if( 0 >= $isSendMailHistoryCount ){
                $cpManagerList = $projectDocumentData['projectData']['companyData']['companyManager'];
                foreach( $cpManagerList as $cpManager){
                    $compName = $result['projectData']['companyData']['companyName'];
                    $documentName = $compName.' '.$projectDocumentData['docDefaultInfo']['name'];
                    $contentParam['cellPhone'] = $cpManager['cellPhone'];
                    $contentParam['link'] = $mailData['link'];
                    $contentParam['documentName'] = $documentName;
                    WorkService::sendWorkSmsToCustomerManager(2, $contentParam);
                }
            }

            return SlCommonUtil::getDefaultDateTime();
        }else{
            throw new Exception('메일주소를 확인해주세요.');
        }
    }


    //may be unused....
    public function setControllerData( $controller ){
        $getValue = \Request::request()->toArray();
        $dept = $getValue['docDept'];
        $type = $getValue['docType'];

        $fncName = 'set' . ucfirst($dept) . $type;
        $this->$fncName($controller, $dept, $type);
        $controller->setData('projectCodeMap',SlProjectCodeMap::PRJ_CODE_MAP);
    }


}
