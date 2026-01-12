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

namespace Controller\Front\Work;

use App;
use Component\Database\DBTableField;
use Component\Member\Manager;
use Component\Work\Code\DocumentDesignCodeMap;
use Exception;
use Framework\Debug\Exception\AlertCloseException;
use Framework\Utility\ComponentUtils;
use Globals;
use Request;
use Session;
use SiteLabUtil\SlCommonUtil;
use SlComponent\Database\DBUtil;
use SlComponent\Database\DBUtil2;
use SlComponent\Database\SearchVo;
use SlComponent\Download\SiteLabDownloadUtil;
use SlComponent\Util\DocumentStruct;
use SlComponent\Util\SitelabLogger;
use SlComponent\Util\SlControllerTrait;
use SlComponent\Util\SlLoader;
use UserFilePath;

/**
 * 업무 처리
 */
class WorkPsController extends \Controller\Front\Controller
{
    use SlControllerTrait;

    private $workService;
    //private $documentService;

    public function __construct()
    {
        $this->workService = SlLoader::cLoad('work','workService','');
        //$this->documentService = SlLoader::cLoad('work','documentService','');
        parent::__construct();
    }

    public function index() {
        $this->runMethod(get_class_methods(__CLASS__));
    }

    /**
     * 로그인
     * @param $param
     */
    public function managerLogin($param){
        $manager = \App::load(Manager::class);
        $managerId = $param['managerId'];
        $managerPw = $param['managerPw'];
        $diskData = $manager->adminDiskCheck();
        $managerInfo = $manager->validateManagerLogin(['managerId' => $managerId,'managerPw' => $managerPw,], $diskData['adminAccess']);
        $manager->afterManagerLogin($managerInfo);
        $this->setJson(200, __('정상로그인'));
    }

    /**
     * 로그아웃
     */
    public function managerLogout(){
        $message = '로그아웃 처리 완료';
        $session = \App::getInstance('session');
        if (!$session->has('manager')) {
            $message = __('로그인 정보가 없거나 잘못된 접속입니다.');
        }
        $manager = \App::load(Manager::class);
        $manager->managerLogout();
        $this->setJson(200, $message);
    }


    /**
     * 거래처 정보 가져오기
     * @param $param
     */
    public function getCompany($param) {
        $companyList = $this->workService->getCompany($param['sno']);
        $this->setJson(200, '정상조회 하였습니다.',   $companyList);
    }

    /**
     * 거래처 정보 저장
     * @param $param
     * @throws Exception
     */
    public function saveCompany($param) {
        $data = $this->workService->saveCompany($param);
        $this->setJson(200, '거래처 저장 완료', $data);
    }

    /**
     * 문서 저장
     * @param $param
     */
    public function saveDocument($param) {
        //$data = SlCommonUtil::transactionMethod($this->documentService, 'saveDocument', $param);
        //$this->setJson(200, $data['saveMsg'], $data );
    }

    /**
     * 문서 파일 저장
     * @param $param
     */
    public function uploadDocumentFile($param){
        $filesValue = \Request::files()->toArray();
        //$this->documentService->uploadAndSave($param, $filesValue);
    }
    public function uploadDocumentFileGeneral($param){
        $filesValue = \Request::files()->toArray();
        //$this->documentService->uploadAndSaveGeneral($param, $filesValue);
    }
    public function uploadDocumentFileDropzone($param){
        $filesValue = \Request::files()->toArray();
        //$data = $this->documentService->uploadAndSaveDropzone($param, $filesValue);
        $this->setJson(200, '파일 업로드 완료.', $data);
    }

    public function uploadPortDataFile($param){
        $filesValue = \Request::files()->toArray();
        //$data = $this->documentService->uploadPortDataFile($param, $filesValue);
        $this->setJson(200, '파일 업로드 완료.', $data);
    }
    public function uploadFileNew($param){
        $filesValue = \Request::files()->toArray();
        //$data = $this->documentService->uploadFileNew($param, $filesValue);
        $this->setJson(200, '파일 업로드 완료.', $data);
    }

    /**
     * 문서 가져오기
     * @param $param
     */
    public function getDocument($param) {
        $projectService = SlLoader::cLoad('work','projectService','');
        $data = $projectService->getProjectDocument($param);
        $this->setJson(200, '정상조회 하였습니다.', $data);
    }

    /**
     * 거래처 번호로 문서 리스트 가져오기
     * @param $param
     */
    public function getDocumentListByCompanySno($param) {
        //$data = $this->documentService->getDocumentList( $param['docDept'], $param['docType'], [ 'companySno' => $param['companySno'] ] );
        $this->setJson(200, '정상조회 하였습니다.', $data);
    }

    /**
     * 담당자 리스트 가져오기
     * @param $param
     */
    public function getManagerListByCompanySno($param) {
        $data = $this->workService->getCompany( $param['companySno'] )['companyManager'];
        $this->setJson(200, '정상조회 하였습니다.', $data);
    }

    /**
     * 담당자 가져오기
     * @param $param
     */
    public function getCompanyManager($param){
        //TODO : 나중에 담당 관리자 선택 할 수 있게 수정 할 수 있음 (우선 첫번째 담당자 가져오기)
        $companyData = $this->workService->getCompany($param['sno']);
    }


    /**
     * 평가항목 저장
     * @param $param
     * @throws Exception
     */
    public function saveRatingItem($param) {
        $saveData = $param;
        $saveData['ratingItem'] = json_encode( $saveData['ratingItem'] , JSON_UNESCAPED_UNICODE );
        unset($saveData['sno']);
        if( empty($param['sno']) ){
            //insert
            $data['sno'] = DBUtil2::insert( 'sl_workRatingItem' , $saveData );
        }else{
            //update
            DBUtil2::update( 'sl_workRatingItem' , $saveData , new SearchVo('sno=?' ,$param['sno']) );
            $data['sno'] = $param['sno'];
        }
        $this->setJson(200, '평가항목 저장 완료', $data);
    }

    /**
     * 평가항목 가져오기
     * @param $param
     */
    public function getRatingItem($param){
        $item = DBUtil2::getOne('sl_workRatingItem', 'sno' , $param['sno']);
        if(empty($item)){
            $data = [
                'sno' => '0',
                'ratingSubject' => '',
                'ratingItem' => [
                    0 => [ 'contents' => ''],
                ],
            ];
        }else{
            $data = [
                'sno' => $item['sno'],
                'ratingSubject' => $item['ratingSubject'],
                'ratingItem' => json_decode($item['ratingItem'], true),
            ];
        }
        $this->setJson(200, '정상조회 하였습니다.', $data);
    }

    /**
     * 평가항목 리스트 가져오기
     * @param $param
     */
    public function getRatingItemList($param){
        $data['itemList'] = DBUtil2::getList('sl_workRatingItem', '1' , '1');
        $this->setJson(200, '정상조회 하였습니다.', $data);
    }

    /**
     * 샘플 데이터 반환
     * @param $param
     */
    public function getDefaultSampleData($param){
        //$returnData = $this->documentService->getDefaultSampleData($param['styleSno'], $param['docPart'] );
        $this->setJson(200, __('정상 조회 되었습니다.'), $returnData);
    }

    /**
     * 요청사항 저장
     * @param $param
     * @throws Exception
     */
    public function saveWorkRequest($param) {
        unset($param['mode']);
        $data = $this->workService->saveWorkRequestUnit($param,$param['docSno']);
        $this->setJson(200, '요청사항 저장 완료', $data);
    }

    /**
     * 포트폴리오 코멘트 저장
     * @param $param
     */
    public function savePortfolioComment($param) {
        unset($param['mode']);
        //$data = $this->documentService->savePortfolioComment($param,$param['docSno']);
        $this->setJson(200, '코멘트 저장 완료', $data);
    }

    /**
     * 디자인가이드 코멘트 저장
     * @param $param
     */
    public function saveOrderComment($param) {
        unset($param['mode']);
        $data = $this->documentService->saveOrderComment($param,$param['docSno']);
        $this->setJson(200, '코멘트 저장 완료', $data);
    }

    /**
     * 메일 발송
     * @param $param
     */
    public function sendEmail($param){
        $projectService = SlLoader::cLoad('work','projectService','');
        $returnDt = SlCommonUtil::transactionMethod($projectService, 'sendEmail', $param);

        // 미팅보고서라면 최초 프로젝트 고객안내 이력 기록 하기.
        //$data = $this->documentService->getDocumentDataBySno($param['sno']);
        if( 'SALES' === $data['docDept'] && 20 == $data['docType']){
            $projectService->setFirstProjectPlan($data['projectSno']);
        }

        if( !empty($returnDt) ){
            $this->setJson(200, '메일 발송 완료.', $returnDt);
        }else{
            $this->setJson(500, '메일 발송 오류 (개발자에 문의).', []);
        }
    }

    /**
     * 진행상태 변경
     * @param $param
     */
    public function updateStep($param){
        //$data = $this->documentService->getDocumentDataBySno($param['sno']);
        //$data['docData']['currentStep'] = $param['step'];
        //$this->documentService->updateDocData($data['docData'], $param['sno']);

        $projectService = SlLoader::cLoad('work','projectService','');
        $projectService->setCustomerPlanStep($param['sno'], $param['step']);

        $this->setJson(200, '진행 상태가 변경되었습니다.', $param['step']);
    }

    /**
     * 진행 계획 변경
     * @param $param
     * @throws Exception
     */
    public function updateStepPlan($param){
        $projectService = SlLoader::cLoad('work','projectService','');
        $data = $projectService->getProjectData($param['sno']);
        $beforeStepData = $data['customerPlanDt'];

        $updateData['customerPlanDt'] = json_encode($param['planDt']);
        DBUtil2::update('sl_project', $updateData, new SearchVo('sno=?' , $param['sno'] ));
        $afterStepData = $param['planDt'];

        //History 저장
        $param['projectSno'] = $param['sno'];
        $projectService->savePlanHistory($param, $beforeStepData, $afterStepData);
        $this->setJson(200, '진행 계획이 변경되었습니다.');
    }

    /**
     * 샘플진행
     * @param $param
     */
    public function goSample($param){
        //$data = $this->documentService->getDocumentDataBySno($param['documentSno']);
        $data['docData']['portData'][$param['styleNo']][$param['styleType']]['status'] = $param['status'];
        $this->documentService->updateDocData($data['docData'], $param['documentSno']);
        $this->documentService->setCustomerApply( $param['documentSno'] );

        $projectService = SlLoader::cLoad('work','projectService','');
        $data = $projectService->getProjectDocument(['sno'=>$param['documentSno']]);

        $this->setJson(200, '처리 되었습니다.', $data);
    }

    /**
     * 기준 스펙을 반환
     * @param $param
     */
    public function getGuideSpec($param){
        $guideSpec = $this->workService->getGuideSpec($param['type']);

        if( isset($param['fitNo']) ){
            $result = [];
            foreach( $guideSpec as  $eachValue){
                $result[] = $eachValue[$param['fitNo']];
            }
        }else{
            $result = $guideSpec;
        }
        $this->setJson(200, '기준 스펙 조회 완료', $result);
    }

    /**
     * 문서 승인/반려 처리
     * @param $param
     */
    public function acceptDoc($param){

        //$result = $this->documentService->saveDocumentAccept($param);

        //프로젝트 단위로 승인 상태 체크.
        $projectService = SlLoader::cLoad('work','projectService','');
        $projectService->setProjectStatus($result['projectData']['sno']);

        $this->setJson(200, '처리 완료', $result);
    }

    /**
     * 스타일별 체크리스트 반환
     * @param $param
     */
    public function getStyleCheckList($param){
        $result = $this->workService->getCheckList($param['type']);
        $this->setJson(200, '처리 완료', $result);
    }

    /**
     * 유니폼 디자인 가이드 상품 기본 정보 전달
     * @param $param
     */
    public function getDefaultProductData($param){
        $result = DocumentDesignCodeMap::DESIGN_PRODUCT;;
        $this->setJson(200, '조회 완료', $result);
    }

    /**
     * 유니폼 디자인 가이드 상품 기본 옵션 정보 전달
     * @param $param
     */
    public function getDefaultProductOption($param){
        $result = DocumentDesignCodeMap::DESIGN_PRODUCT_OPTION;;
        $checkList = $this->workService->getSpecData($param['style']);
        foreach( $checkList as $value ){
            $value['guideSpec'] = 0;
            $value['completeSpec'] = 0;
            $value['specUnit'] = 'cm';
            $result['checkList'][] = $value;
        }
        $this->setJson(200, '조회 완료', $result);
    }

    /**
     * 업로드 데이터를 바로 JSON으로 내려 준다.
     * @param $param
     */
    public function getUploadData($param){
        $filesValue = \Request::files()->toArray();
        $fncName = 'getUpload'.ucfirst($param['div']); //partData , subPartData.
        //SitelabLogger::logger($fncName);
        $formUploadService = SlLoader::cLoad('work','formUploadService','');
        $result = $formUploadService->$fncName($filesValue);
        $this->setJson(200, '조회 완료', $result);
    }

    /**
     * 양식 다운로드
     * @param $param
     */
    public function getExcelUploadForm($param){
        $fileRealName = $param['div'];
        $fileName = $param['fileName'];
        SiteLabDownloadUtil::download("./data/form/{$fileRealName}.xls','{$fileName}.xls");
    }

    /**
     * 미팅보고서 번호 기준으로 견적일자 가져오기.
     * @param $param
     */
    public function getEstimateDate($param){
        $searchVo = new SearchVo();
        $searchVo->setWhereArray([
            'standardSno=?',
            'UPPER(docDept)=?',
            'docType=?',
        ]);
        $searchVo->setWhereValueArray([
            $param['sno'],
            'SALES',
            '6',
        ]);
        $estimateData = DBUtil2::getListBySearchVo('sl_workDocument', $searchVo, false);
        $this->setJson(200, '조회 완료', json_decode($estimateData[0]['docData'],true)['estimateDt']);
    }

    /**
     * 발주 - 디자인 가이드 확정 처리
     * @param $param
     */
    public function setOrderStatus($param){
        //$this->documentService->setCommonCustomerApply($param);
        $resultData = $this->documentService->getDocumentDataBySno($param['documentSno']);
        $this->setJson(200, '처리 되었습니다.', $resultData);
    }

    /**
     * 스타일 저장
     * @param $param
     * @throws Exception
     */
    public function saveStyle($param){
        $param['data']['specCheckInfo'] = json_encode($param['data']['specCheckInfo']);
        $param['data']['checkList'] = json_encode($param['data']['checkList']);
        //SitelabLogger::logger($param['data']);
        if( !empty($param['data']['sno']) ){
            DBUtil2::update('sl_workStyle', $param['data'], new SearchVo('sno=?' , $param['data']['sno'] ));
            $this->setJson(200, '수정 되었습니다.');
        }else{
            DBUtil2::insert('sl_workStyle', $param['data']);
            $this->setJson(200, '등록 되었습니다.');
        }
    }

    /**
     * 스타일 반환
     * @param $param
     */
    public function getStyle($param){
        $listData = $this->workService->getStyle($param);
        $this->setJson(200, '조회 완료.', $listData);
    }

    /**
     * 스타일 반환 (개별)
     * @param $param
     */
    public function getStyleData($param){
        $listData = $this->workService->getStyle($param);
        $this->setJson(200, '조회 완료.', $listData[0]);
    }

    /**
     * 포트폴리오 기본 데이터 가져오기.
     * @param $param
     */
    public function getDefaultPortData($param){
        $defaultData = DocumentStruct::DOC_PART['portData'];
        $defaultData['styleName'] = $param['styleName'];
        $this->setJson(200, '조회 완료.', $defaultData);
    }

    /**
     * 고객 승인 상태 처리
     * @param $param
     */
    public function setCustomerApply($param){
        //$this->documentService->setCustomerApply($param['sno']);
        $projectService = SlLoader::cLoad('work','projectService','');
        $data = $projectService->getProjectDocument($param);
        $this->setJson(200, '처리 완료.', $data);
    }

    /**
     * 최근 작업지시서의 상품 가져오기. (프로젝트 상품용)
     * @param $param
     */
    public function getLatestWorkProduct($param){
        $projectService = SlLoader::cLoad('work','projectService','');
        $this->setJson(200, '처리 완료.', $projectService->getLatestWorkProduct($param['projectSno']));
    }

    /**
     * 프로젝트 상품용 기본 구조 반환
     * @param $param
     */
    public function getDefaultProduct($param){
        $projectService = SlLoader::cLoad('work','projectService','');
        $this->setJson(200, '처리 완료.', $projectService->getDefaultProduct());
    }

    public function getTestData($param){
        $this->setJson(200, '처리 완료.', [
            [
                'name' => '김태영',
                'job' => '치기공사',
                'company' => '성남기공',
                'location' => '고양',
                'lastLogin' => '2022-05-04 18:03',
                'favoriteColor' => '검정',
            ],
            [
                'name' => '송준호',
                'job' => '개발자',
                'company' => '모모티/이노버',
                'location' => '인천/서울',
                'lastLogin' => '2022-05-04 15:03',
                'favoriteColor' => '블루',
            ],
        ]);
    }

}


