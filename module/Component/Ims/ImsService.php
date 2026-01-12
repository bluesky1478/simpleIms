<?php

namespace Component\Ims;

use App;
use Component\Database\DBIms;
use Component\Database\DBTableField;
use Component\Ims\EnumType\PREPARED_TYPE;
use Component\Imsv2\ImsScheduleUtil;
use Component\Member\Manager;
use Component\Sms\Code;
use Controller\Admin\Ims\Step\ImsStepTrait;
use Framework\Debug\Exception\AlertBackException;
use Framework\Utility\DateTimeUtils;
use LogHandler;
use Request;
use Component\Scm\ScmAsianaTrait;
use Session;
use SiteLabUtil\ImsUtil;
use SiteLabUtil\SlCommonUtil;
use SlComponent\Database\DBUtil2;
use SlComponent\Database\SearchVo;
use SlComponent\Database\TableVo;
use SlComponent\Mail\MailService;
use SlComponent\Mail\SiteLabMailUtil;
use SlComponent\Util\SitelabLogger;
use SlComponent\Util\SlLoader;
use SlComponent\Util\SlSmsUtil;

/**
 * IMS 서비스
 * Class GoodsStock
 * @package Component\Goods
 */
class ImsService
{

    use ImsStepTrait;
    use ImsServiceTrait;
    use ImsServiceSampleTrait;
    use ImsServiceFabricTrait;
    use ImsServiceEstimateTrait;
    use ImsServiceRequestTrait;
    use ImsServiceProductionTrait;
    use ImsServiceSortTrait;
    use ImsServiceCheckTrait;
    use ImsServiceExcelTrait;
    use ImsServiceTodoTrait;
    use ImsReorderServiceTrait;
    use ImsSendTrait;
    use ImsProjectServiceTrait;
    use ImsServiceListTrait;

    use ScmAsianaTrait; //아시아나 서비스

    private $sql;

    //신규, 공개입찰, 샘플 ?
    const PROJECT_CHECK_STEP = [
        0, 2, 5
    ];

    public function __construct()
    {
        $this->sql = SlLoader::sqlLoad(__CLASS__, false);
    }
    public function setProjectCondition($condition, $searchVo)
    {
        $this->sql->setProjectCondition($condition, $searchVo);
    }
    public function setCommonCondition($condition, $searchVo)
    {
        $this->sql->setCommonCondition($condition, $searchVo);
    }
    public function getData($params)
    {
        $this->$params['target'];
    }

    /**
     * ImsDB명 반환
     * @param $constName
     * @return false|mixed
     */
    public static function getImsDBName($constName){
        $reflect = new \ReflectionClass('\Component\Ims\ImsDBName');
        return $reflect->getConstant(strtoupper($constName));
    }

    /**
     * 간단 저장
     * @param $params
     * @throws \Exception
     */
    public function simpleSave($params){
        if(empty($params['sno'])){
            throw new \Exception('업데이트 번호 sno 없음(개발팀문의)');
        }else{
            $tableName = ImsService::getImsDBName($params['target']);
            DBUtil2::update($tableName,$params['data'],new SearchVo('sno=?', $params['sno']));
        }
    }

    /**
     * 파일 히스토리
     * @param $params
     * @return mixed
     */
    public function getFileHistory($params)
    {
        $tableInfo = $this->sql->getFileTable();
        $revisionCheckList = [
            'customerSno',
            'projectSno',
            'styleSno',
            'eachSno',
        ];
        $searchVo = new SearchVo('fileDiv=?', $params['fileDiv']);
        foreach ($revisionCheckList as $field) {
            if (!empty($params[$field])) {
                $searchVo->setWhere('a.' . $field . '=?');
                $searchVo->setWhereValue($params[$field]);
            }
        }

        $searchVo->setOrder('a.regDt desc');

        $fileInfoList = DBUtil2::getComplexList($tableInfo, $searchVo);

        foreach ($fileInfoList as $key => $value) {
            $value['fileList'] = json_decode($value['fileList'], true);
            $fileInfoList[$key] = $value;
        }
        return $fileInfoList;
    }


    /**
     * 고객정보 가져오기
     * @param $params
     * @return array[]
     */
    public function getCustomer($params)
    {

        $customerData = DBUtil2::getOne(ImsDBName::CUSTOMER, 'sno', $params['sno'], false);

        if (!empty($customerData)) {
            //가져오기
            $result = $customerData;
            $addedList = json_decode($result['addedInfo'], true);
            $refineAddInfo = [];
            foreach (ImsJsonSchema::CUSTOMER_ADDINFO as $addInfoKey => $addInfo) {
                $refineAddInfo[$addInfoKey] = empty($addedList[$addInfoKey]) ? '' : $addedList[$addInfoKey];
            }
            $result['addedInfo'] = $refineAddInfo;
            $result['contactMemo'] = gd_htmlspecialchars_stripslashes($result['contactMemo']);
            //업종정보 가져오기(text, 1차name)
            $result['busiCateText'] = '미지정';
            $result['parentBusiCateName'] = '상위업종 선택';
            if ((int)$result['busiCateSno'] > 0) {
                $aTableInfo = [
                    'a' => ['data' => [ ImsDBName::BUSI_CATE ], 'field' => ["a.sno, a.cateName"]],
                    'b' => ['data' => [ ImsDBName::BUSI_CATE, 'LEFT OUTER JOIN', 'a.parentBusiCateSno = b.sno' ], 'field' => ["b.cateName as parentBusiCateName"]],
                ];
                $aBusiCateList = DBUtil2::getComplexList(DBUtil2::setTableInfo($aTableInfo,false), new SearchVo('a.sno=?', $result['busiCateSno']), false, false, true);
                if (isset($aBusiCateList[0]['sno'])) {
                    $result['busiCateText'] = $aBusiCateList[0]['parentBusiCateName'].' > '.$aBusiCateList[0]['cateName'];
                    $result['parentBusiCateName'] = $aBusiCateList[0]['parentBusiCateName'];
                }
            }
        } else {
            //신규 스키마 전달
            $schemaList = DBIms::tableImsCustomer();
            foreach ($schemaList as $key => $schema) {
                $result[$schema['val']] = '';
            }
            $result['addedInfo'] = []; //재할당.
            foreach (ImsJsonSchema::CUSTOMER_ADDINFO as $addInfoKey => $addInfo) {
                $result['addedInfo'][$addInfoKey] = '';
            }
            //$result['contactGender'] = 'F'; //재할당. (테스트)
            unset($result['sno']);
        }

        if (!empty($result['salesManagerSno'])) {
            $result['salesManagerNm'] = DBUtil2::getOne(DB_MANAGER, 'sno', $result['salesManagerSno'])['managerNm'];
        }

        return $this->decorationCustomerEachData(SlCommonUtil::setDateBlank($result));
    }

    /**
     * 고객 데이터 꾸미기
     * @param $customerData
     * @return mixed
     */
    public function decorationCustomerEachData($customerData)
    {
        $customerData['useMallKr'] = 'y' === $customerData['useMall'] ? '예' : '아니오';
        $customerData['use3plKr'] = 'y' === $customerData['use3pl'] ? '예' : '아니오';
        $customerData['customerDiv'] = empty($customerData['customerDiv']) ? 0 : $customerData['customerDiv'];
        //$customerData['customerDivKr'] = ImsCodeMap::CUSTOMER_STATUS[$customerData['customerDiv']];
        $each['fullAddress'] = implode(' ', [$customerData['contactZipcode'], $customerData['contactAddress'], $customerData['contactAddressSub']]);
        $each['contactGenderKr'] = 'M' === $each['contactGender'] ? '남자' : '여자';
        return $customerData;
    }


    /**
     * 미팅 정보 가져오기
     * @param $params
     * @return array|mixed
     */
    public function getMeeting($params)
    {
        $meetingData = DBUtil2::getOne(ImsDBName::MEETING, 'projectSno', $params['projectSno'], true);
        if (!empty($meetingData)) {
            //가져오기
            $result = $meetingData;
        } else {
            //신규 스키마 전달
            $schemaList = DBIms::tableImsMeeting();
            foreach ($schemaList as $key => $schema) {
                $result[$schema['val']] = '';
            }
            unset($result['sno']);
        }
        return SlCommonUtil::setDateBlank($result);
    }

    /**
     * 생산 정보 가져오기
     * @param $params
     * @return mixed
     * @throws \Exception
     */
    public function getProduce($params)
    {
        $result = $this->getProject($params); //sno는 프로젝트 sno로 가져온다.
        $imsProduceService = SlLoader::cLoad('ims', 'imsProduceService');
        $result['produce'] = SlCommonUtil::setDateBlank($imsProduceService->getProduceData($params['sno']));//projectSno
        //생산 전용 코멘트 가져오기
        $result['prdCommentList'] = $this->getProjectComment($params['sno'], 'produce'); //생산 코멘트리스트
        return $result;
    }

    /**
     * 프로젝트 정보 가져오기
     * @param $params
     * @return array|mixed
     * @throws \Exception
     */
    public function getProject($params)
    {
        $projectData = [];
        if (empty($params['sno'])) {
            //신규~
            $schemaList = DBIms::tableImsProject();

            foreach ($schemaList as $key => $schema) {
                $projectData[$schema['val']] = $schema['def'];
            }
            $projectData['addedInfo'] = []; //재할당.
            foreach (ImsJsonSchema::ADD_INFO as $addInfoKey => $addInfo) {
                $projectData['addedInfo'][$addInfoKey] = '';
            }

            //Default Data
            $projectData['projectType'] = '0';
            $projectData['salesManagerSno'] = \Session::get('manager.sno');

            //$projectData['salesStartDt'] = date('Y-m-d');
            $projectData['use3pl'] = 'n';
            $projectData['useMall'] = 'n';
            $projectData['recommend'] = [];
            $projectData['fabricNational'] = [];

            $projectData['projectStatus'] = 10; //Project 상태에 따라 다름
            $projectData['customerSno'] = -1; //Project 상태에 따라 다름

            $projectData['projectNo'] = '';

            $projectData['projectYear'] = date('y'); //현재 년도.
            $projectData['projectSeason'] = 'ALL'; //현재 시즌.

            //확장정보
            $schemaList = DBIms::tableImsProjectExt();
            foreach ($schemaList as $key => $schema) {
                $result['projectExt'][$schema['val']] = $schema['def'];
            }
            $result['projectExt']['targetSalesYear'] = date('Y'); //현재 년도.

            unset($projectData['sno']);
        } else {
            //기존 수정
            //$projectData = DBUtil2::getOne(ImsDBName::PROJECT, 'sno', $params['sno'], false);
            $searchVo = new SearchVo('a.sno=?', $params['sno']);
            $tableInfo = $this->sql->getProjectTable();
            $prjOrgData = DBUtil2::getComplexList($tableInfo, $searchVo, false, false, false)[0];
            $projectData = $this->decorationEachData($prjOrgData); //Just One.

            //확장정보 가져오기
            $result['projectExt'] =  SlCommonUtil::setDateBlank( DBUtil2::getOne(ImsDBName::PROJECT_EXT, 'projectSno', $projectData['sno']) );
        }


        //With CustomerData
        $projectData['projectSno'] = $projectData['sno'];
        $result['project'] = SlCommonUtil::setDateBlank($projectData); //프로젝트 기본정보
        $result['customer'] = SlCommonUtil::setDateBlank($this->getCustomer(['sno' => $projectData['customerSno']])); //고객사 정보
        //$result['meeting'] = SlCommonUtil::setDateBlank($this->getMeeting(['projectSno' => $projectData['sno']])); //미팅 정보

        $result['productList'] = SlCommonUtil::setDateBlank($this->getProductList(['projectSno' => $projectData['sno']])); //스타일 정보

        if( empty($result['project']['styleWithCount']) && !empty($result['productList']) ){
            $styleCount = count($result['productList'])-1;
            $result['project']['styleWithCount'] = $result['productList'][0]['productName'] . ($styleCount > 0 ? ' 외 ' . $styleCount . '건' : '');
        }

        $result['removeProductList'] = SlCommonUtil::setDateBlank($this->getRemoveProductList(['projectSno' => $projectData['sno']])); //스타일 정보
        //$result['preparedList'] = SlCommonUtil::setDateBlank($this->getPreparedList($projectData['sno'])); //선행작업 정보
        $result['commentList'] = SlCommonUtil::setDateBlank($this->getProjectComment($projectData['sno'])); //코멘트리스트

        foreach( $result['productList'] as $prdKey => $prdData ){
            if(!empty($prdData['estimateConfirmSno'])){
                $prdData['confirmCostData'] = $this->getFactoryEstimate(['sno'=>$prdData['estimateConfirmSno']]);
            }
            $result['project']['totalSalePrice'] += ( $prdData['salePrice'] * $prdData['prdExQty'] ) ;
            $result['project']['totalCost'] += ( $prdData['prdCost'] * $prdData['prdExQty'] );
            $result['project']['totalDutyCost'] += $prdData['confirmCostData']['contents']['dutyCost'];
            $result['project']['totalDutyCostSum'] += ( $prdData['confirmCostData']['contents']['dutyCost'] * $prdData['prdExQty'] ) ;
            $result['project']['totalFactoryCost'] += ( $prdData['prdCost'] * $prdData['prdExQty'] ) + $prdData['confirmCostData']['contents']['dutyCost'];
            $result['project']['totalCount'] += $prdData['prdExQty'] ;
            $result['productList'][$prdKey] = $prdData;
        }

        //생산 선행 작업 정보 가져오기 (Type별 최신 데이터)
        foreach (PREPARED_TYPE::ALL_TYPE_NAME as $typeName) {
            $result['prepared'][$typeName] = $this->getPreparedReq([ //최근께 겹쳐지는 형태.
                'projectSno' => $projectData['sno'],
                'reqType' => $typeName,
            ]);
        }

        //파일 정보 가져오기
        $result['fileList'] = $this->getProjectFiles($projectData['sno'], $params['styleSno']);

        //아소트 입력 URL 전달 정보
        if( empty($result['project']['assortReceiver']) ){
            $result['project']['assortReceiver'] = $result['customer']['contactName'];
            $result['project']['assortEmail'] = $result['customer']['contactEmail'];
        }
        //사양서 체크 URL 전달 정보
        if( empty($result['project']['customerOrderReceiver']) ){
            $result['project']['customerOrderReceiver'] = $result['customer']['contactName'];
            $result['project']['customerOrderEmail'] = $result['customer']['contactEmail'];
        }

        return $result;
    }

    /**
     * 프로젝트단에서 설정한 데이터를 스타일 데이터에 넣는다.
     * @param $projectSno
     * @param $projectData
     * @throws \Exception
     */
    public function syncProduct($projectSno, $projectData)
    {
        if ('y' == $projectData['syncProduct']) {
            $syncFieldList = [
                'produceCompanySno', //생산처
                'produceType', //생산타입
                'produceNational', //생산국가
                'customerDeliveryDt', //고객납기
                'msDeliveryDt', //이노버납기
            ];
            $updateData = [];
            foreach ($syncFieldList as $syncField) {
                $updateData[$syncField] = $projectData[$syncField];
            }
            DBUtil2::update(ImsDBName::PRODUCT, $updateData, new SearchVo('projectSno=?', $projectSno));
        }
    }


    /**
     * 선행작업 데이터 가져오기
     * @param $params
     * @return array|mixed
     * @throws \Exception
     */
    public function getPrepared($params)
    {
        $projectData = $this->getProject(['sno' => $params['projectSno']]);
        //SitelabLogger::logger($projectData);

        $searchVo = new SearchVo('a.sno=?', $params['sno']);
        $tableInfo = $this->sql->getPreparedTable();
        $data = DBUtil2::getComplexList($tableInfo, $searchVo, false, false, false)[0];

        if (empty($data)) {
            $preparedConst = 'Component\Ims\ImsJsonSchema::PREPARED_' . strtoupper($params['reqType']);
            //신규 스키마 전달
            $schemaList = DBIms::tableImsPrepared();
            foreach ($schemaList as $key => $schema) {
                $result[$schema['val']] = '';
            }
            $result['contents'] = []; //재할당.
            //각 요청 추가 정보
            foreach (constant($preparedConst) as $addInfoKey => $addInfo) {
                $result['contents'][$addInfoKey] = $addInfo;
            }
            if ('estimate' === $params['reqType'] || 'cost' === $params['reqType']) {
                $result['contents']['productList'] = $projectData['productList'];
            }

            //Default
            $result['preparedType'] = $params['reqType'];
            $result['projectSno'] = $params['projectSno'];
        } else {
            $result = $this->decorationPreparedEachData($data);
        }

        $projectData['prepared'] = SlCommonUtil::setDateBlank($result);

        return $projectData;
    }

    /**
     * 요청 정보 가져오기
     * @param $params
     * @return mixed
     */
    public function getPreparedReq($params)
    {
        $searchVo = new SearchVo([
            'a.projectSno=?',
            'a.preparedType=?',
        ], [
            $params['projectSno'],
            $params['reqType'],
        ]);

        $tableInfo = $this->sql->getPreparedTable();
        $preparedData = DBUtil2::getComplexList($tableInfo, $searchVo, false, false, true)[0]; //Just One.

        $contents = '';

        if (!empty($preparedData)) {
            //가져오기
            $result = $preparedData;
            $result['preparedStatusKr'] = PREPARED_TYPE::STATUS[$result['preparedStatus']];
            $result['contents'] = json_decode($result['contents'], true);
        } else {
            $preparedConst = 'Component\Ims\ImsJsonSchema::PREPARED_' . strtoupper($params['reqType']);
            //신규 스키마 전달
            $schemaList = DBIms::tableImsPrepared();
            foreach ($schemaList as $key => $schema) {
                $result[$schema['val']] = '';
            }
            $result['contents'] = []; //재할당.
            //각 요청 추가 정보
            foreach (constant($preparedConst) as $addInfoKey => $addInfo) {
                $result['contents'][$addInfoKey] = '';
            }

            //Default
            $preparedTypeConst = 'Component\Ims\EnumType\PREPARED_TYPE::' . strtoupper($params['reqType']);
            $result['preparedType'] = constant($preparedTypeConst)['typeName'];
            $result['projectSno'] = $params['projectSno'];
            $result['preparedSno'] = '';

            unset($result['sno']);
        }

        return SlCommonUtil::setDateBlank($result);
    }

    /**
     * 프로젝트 선행 리스트
     * @param $projectSno
     * @return array
     * @throws \Exception
     */
    public function getPreparedList($projectSno)
    {
        $searchVo = new SearchVo('projectSno=?', $projectSno);
        $searchVo->setOrder('regDt desc');
        $tableInfo = $this->sql->getPreparedTable();
        $list = DBUtil2::getComplexList($tableInfo, $searchVo, false, false, false);
        $typeList = [];
        foreach ($list as $key => $value) {
            $typeList[$value['preparedType']][] = $this->decorationPreparedEachData($value);
        }
        foreach (PREPARED_TYPE::ALL_TYPE_NAME as $typeName) {
            if (!isset($typeList[$typeName])) {
                $typeList[$typeName] = [];
            }
        }
        return $typeList;
    }

    /**
     * 선행작업 deco
     * @param $value
     * @return mixed
     * @throws \Exception
     */
    public function decorationPreparedEachData($value)
    {

        $value['reqMemo'] = gd_htmlspecialchars_stripslashes($value['reqMemo']);
        $value['procMemo'] = gd_htmlspecialchars_stripslashes($value['procMemo']);

        $value['preparedStatusKr'] = PREPARED_TYPE::STATUS_COLOR[$value['preparedStatus']];
        $value['contents'] = json_decode($value['contents'], true);
        $value['deadLineDtRemain'] = SlCommonUtil::getRemainDt($value['deadLineDt']);
        $value['deadLineDtShort'] = SlCommonUtil::getSimpleWeekDay($value['deadLineDt']);
        $value['regDtShort'] = SlCommonUtil::getSimpleWeekDay($value['regDt']);

        //$value['regDtShort'] = SlCommonUtil::getSimpleWeekDay($value['regDt']);

        $value['reqMemoBr'] = nl2br($value['reqMemo']);
        $value['reqMemo1Br'] = nl2br($value['reqMemo1']);
        $value['reqMemo2Br'] = nl2br($value['reqMemo2']);
        $value['reqMemo3Br'] = nl2br($value['reqMemo3']);
        $value['procMemoBr'] = nl2br($value['procMemo']);

        $preparedConst = constant('Component\Ims\ImsJsonSchema::PREPARED_' . strtoupper($value['preparedType']));
        foreach ($preparedConst as $contentsKey => $contentsValue) {
            if (!isset($value['contents'][$contentsKey])) {
                $value['contents'][$contentsKey] = $contentsValue;
            }
        }

        $contentsPrdList = $value['contents']['productList'];
        if (!empty($contentsPrdList)) {
            $refinePrdContents = [];
            foreach ($contentsPrdList as $contentsPrd) {
                $cnt = DBUtil2::getCount(ImsDBName::PRODUCT, new SearchVo(" delFl='n' and sno = ? ", $contentsPrd['sno']));
                if (!empty($cnt)) {
                    $refinePrdContents[] = $contentsPrd;
                }
            }
            $value['contents']['productList'] = $refinePrdContents;
        }

        return $value;
    }

    /**
     * 프로젝트 추가 정보 정제
     * @param $addedList
     * @return mixed
     */
    public function refineProjectAddedInfo($addedList){
        //shareCustomerInfo
        $refineAddInfo = [];
        foreach (ImsJsonSchema::ADD_INFO as $addInfoKey => $addInfo) {
            $refineAddInfo[$addInfoKey] = empty($addedList[$addInfoKey]) ? '' : $addedList[$addInfoKey];
            //공유 받을 고객 정보
            if( 'shareCustomerInfo' === $addInfoKey ) {
                if(empty($refineAddInfo[$addInfoKey])){
                    $refineAddInfo[$addInfoKey] = [
                        ImsJsonSchema::SHARE_CUST_INFO
                    ];
                }
            }
        }
        return $refineAddInfo;
    }

    /**
     * 프로젝트 개별 데이터 꾸며서 반환
     * @param $projectData
     * @return mixed
     * @throws \Exception
     */
    public function decorationEachData($projectData)
    {
        //gd_debug($projectData);
        //gd_debug( $projectData['sno'].':'.$projectData['prdCost'] );
        //SitelabLogger::logger($projectData['addedInfo']);
        $projectData['addedInfo'] = json_decode($projectData['addedInfo'], true);
        $projectData['addedInfo'] = $this->refineProjectAddedInfo($projectData['addedInfo']);

        $recommend = $projectData['recommend'];
        $projectData['recommend'] = [];
        foreach (ImsCodeMap::RECOMMEND_TYPE as $recommendKey => $recommendValue) {
            if (($recommendKey & $recommend) > 0) {
                $projectData['recommend'][] = $recommendKey . '';
            }
        }

        $fabricNational = $projectData['fabricNational'];
        $projectData['fabricNational'] = [];
        foreach (ImsCodeMap::FABRIC_BUY_TYPE as $recommendKey => $recommendValue) {
            if (($recommendKey & $fabricNational) > 0) {
                $projectData['fabricNational'][] = $recommendKey . '';
            }
        }

        $projectData['msOrderDtShort'] = SlCommonUtil::getSimpleWeekDay($projectData['msOrderDt']);
        $projectData['msOrderDtRemain'] = SlCommonUtil::getRemainDt($projectData['msOrderDt']);

        //고객 발주/납기
        if (2 == $projectData['productionStatus']) {
            //생산중인건 납기일 표기
            $projectData['customerDeliveryDtShort'] = '완료';
            //$projectData['customerDeliveryRemainDt'] = '<span class="text-muted">complete</span>';
            $projectData['customerDeliveryRemainDt'] = 'complete';
        } else {
            if (in_array(\Session::get('manager.managerId'), ImsCodeMap::PRODUCE_COMPANY_MANAGER)) { //TODO : 생산처 그룹으로 체크하도록 변경
                //하나일경우 Hide.
                $projectData['customerDeliveryDtShort'] = '비공개';
            } else {
                $projectData['customerDeliveryDtShort'] = SlCommonUtil::getSimpleWeekDayWithYear($projectData['customerDeliveryDt']);
                $projectData['customerDeliveryRemainDt'] = SlCommonUtil::getRemainDt(gd_date_format('Y-m-d', $projectData['customerDeliveryDt']));
            }
        }
        $projectData['customerOrderDtShort'] = SlCommonUtil::getSimpleWeekDay($projectData['customerOrderDt']);


        $projectData['projectStatusKr'] = ImsCodeMap::PROJECT_STATUS[$projectData['projectStatus']];

        $projectData['projectTypeKr'] = ImsCodeMap::PROJECT_TYPE[$projectData['projectType']];
        $projectData['projectTypeEn'] = ImsCodeMap::PROJECT_TYPE_EN[$projectData['projectType']];

        //$projectData['use3plAndMall'] = $result = implode(",", array_filter($use3plAndMall, 'trim'));

        //미팅 남은기간
        $projectData['meetingRemainDt'] = SlCommonUtil::getRemainDt(gd_date_format('Y-m-d', $projectData['meetingDt']));
        //준비 남은기간
        $projectData['readyRemainDt'] = SlCommonUtil::getRemainDt($projectData['readyDeadLineDt']);

        //고객발주
        $projectData['customerOrderShort'] = SlCommonUtil::getSimpleWeekDayWithYear(gd_date_format('Y-m-d', $projectData['customerOrderShort']));
        $projectData['customerOrderShort2'] = SlCommonUtil::getSimpleWeekDay(gd_date_format('Y-m-d', $projectData['customerOrderShort']),true);

        //이노버 납기 남은기간
        $projectData['msDeliveryRemainDt'] = SlCommonUtil::getRemainDtMonth(gd_date_format('Y-m-d', $projectData['msDeliveryDt']));
        $projectData['msDeliveryDtShort'] = SlCommonUtil::getSimpleWeekDayWithYear(gd_date_format('Y-m-d', $projectData['msDeliveryDt']));

        //제안 마감 남은기간
        $projectData['recommendDtShort'] = SlCommonUtil::getSimpleWeekDay($projectData['recommendDt']);
        $projectData['recommendRemainDt'] = SlCommonUtil::getRemainDt($projectData['recommendDt']);

        //기획예정일
        $projectData['planDtRemain'] = SlCommonUtil::getRemainDt($projectData['planDt']);
        $projectData['planDtShort'] = SlCommonUtil::getSimpleWeekDay($projectData['planDt']);
        $projectData['planDtShort2'] = SlCommonUtil::getSimpleWeekDay($projectData['planDt'],true);

        $projectData['planEndDtShort'] = SlCommonUtil::getSimpleWeekDay($projectData['planEndDt']);
        $projectData['planEndDtShort2'] = SlCommonUtil::getSimpleWeekDay($projectData['planEndDt'],true);

        //제안예정일
        $projectData['proposalDtRemain'] = SlCommonUtil::getRemainDt($projectData['proposalDt']);
        $projectData['proposalDtShort'] = SlCommonUtil::getSimpleWeekDay($projectData['proposalDt']);
        $projectData['proposalDtShort2'] = SlCommonUtil::getSimpleWeekDay($projectData['proposalDt'],true);
        $projectData['proposalEndDtShort'] = SlCommonUtil::getSimpleWeekDay($projectData['proposalEndDt']);
        $projectData['proposalEndDtShort2'] = SlCommonUtil::getSimpleWeekDay($projectData['proposalEndDt'],true);
        //샘플예정일
        $projectData['sampleDtRemain'] = SlCommonUtil::getRemainDt($projectData['sampleStartDt']);
        $projectData['sampleDtShort'] = SlCommonUtil::getSimpleWeekDay($projectData['sampleStartDt']);
        $projectData['sampleDtShort2'] = SlCommonUtil::getSimpleWeekDay($projectData['sampleStartDt'],true);
        $projectData['sampleEndDtShort'] = SlCommonUtil::getSimpleWeekDay($projectData['sampleEndDt']);

        $projectData['customerWaitDtRemain'] = str_replace(' 지남', '', SlCommonUtil::getRemainDt($projectData['customerWaitDt']));

        $statusClass = ImsCodeMap::PROJECT_CONFIRM_TYPE[$projectData['planConfirm']]['class'];
        $projectData['planConfirmKr'] = "<span class='{$statusClass}'>" . ImsCodeMap::PROJECT_CONFIRM_TYPE[$projectData['planConfirm']]['name'] . "</span>";
        $statusClass = ImsCodeMap::PROJECT_CONFIRM_TYPE[$projectData['proposalConfirm']]['class'];
        $projectData['proposalConfirmKr'] = "<span class='{$statusClass}'>" . ImsCodeMap::PROJECT_CONFIRM_TYPE[$projectData['proposalConfirm']]['name'] . "</span>";
        $statusClass = ImsCodeMap::PROJECT_CONFIRM_TYPE[$projectData['sampleConfirm']]['class'];
        $projectData['sampleConfirmKr'] = "<span class='{$statusClass}'>" . ImsCodeMap::PROJECT_CONFIRM_TYPE[$projectData['sampleConfirm']]['name'] . "</span>";
        $projectData['workConfirmKr'] = ImsCodeMap::PROJECT_CONFIRM_TYPE[$projectData['workConfirm']]['name'];

        $projectData['customerEstimateConfirmKr'] = 'y' === $projectData['customerEstimateConfirm'] ? '확정' : '<span class="text-muted">미확정</span>';
        $projectData['customerSaleConfirmKr'] = 'y' === $projectData['customerSaleConfirm'] ? '확정' : '<span class="text-muted">미확정</span>';

        $projectData['planConfirmClass'] = ImsCodeMap::PROJECT_CONFIRM_TYPE[$projectData['planConfirm']]['class'];
        $projectData['proposalConfirmClass'] = ImsCodeMap::PROJECT_CONFIRM_TYPE[$projectData['proposalConfirm']]['class'];
        $projectData['sampleConfirmClass'] = ImsCodeMap::PROJECT_CONFIRM_TYPE[$projectData['sampleConfirm']]['class'];
        $projectData['workConfirmClass'] = ImsCodeMap::PROJECT_CONFIRM_TYPE[$projectData['workConfirm']]['class'];

        $projectData['produceTypeKr'] = ImsCodeMap::PRODUCE_TYPE[$projectData['produceType']];
        $projectData['customerOrderConfirmKr'] = 'y' === $projectData['customerOrderConfirm'] ? '확정' : '<span class="text-muted">미확정</span>';
        $projectData['customerOrder2ConfirmKr'] = 'y' === $projectData['customerOrder2Confirm'] ? '확정' : '<span class="text-muted">미확정</span>';

        $projectData['customerOrderConfirmDtShort'] = SlCommonUtil::getSimpleWeekDay($projectData['customerOrderConfirmDt']);
        $projectData['customerOrder2ConfirmDtShort'] = SlCommonUtil::getSimpleWeekDay($projectData['customerOrderConfirmDt']);
        $projectData['customerEstimateConfirmDtShort'] = SlCommonUtil::getSimpleWeekDay($projectData['customerEstimateConfirmDt']);
        $projectData['customerSaleConfirmDtShort'] = SlCommonUtil::getSimpleWeekDay($projectData['customerSaleConfirmDt']);

        $styleCount = $projectData['styleCount'] - 1;
        $projectData['styleWithCount'] = $projectData['style'] . ($styleCount > 0 ? ' 외 ' . $styleCount . '건' : '');

        $projectData['btStatusIcon'] = ImsCodeMap::IMS_PROC_STATUS[$projectData['btStatus']]['icon'];
        $projectData['estimateStatusIcon'] = ImsCodeMap::IMS_PROC_STATUS[$projectData['estimateStatus']]['icon'];
        $projectData['costStatusIcon'] = ImsCodeMap::IMS_PROC_STATUS[$projectData['costStatus']]['icon'];
        $projectData['priceStatusIcon'] = ImsCodeMap::IMS_PROC_STATUS[$projectData['priceStatus']]['icon'];
        $projectData['workStatusIcon'] = ImsCodeMap::IMS_PROC_STATUS[$projectData['workStatus']]['icon'];
        $projectData['orderStatusIcon'] = PREPARED_TYPE::STATUS_ICON[$projectData['orderStatus']];

        $projectData['productionStatusIcon'] = ImsCodeMap::IMS_PROC_STATUS[$projectData['productionStatus']]['icon'];


        $estimateIcon = $projectData['estimateCount'] > 0 ? 'fa-circle sl-green' : 'fa-times text-danger';
        $orderAcceptIcon = $projectData['salesConfirmedCount'] > 0 ? 'fa-circle sl-green' : 'fa-times text-danger';
        $workIcon = $projectData['fileWorkCount'] > 0 ? 'fa-circle sl-green' : 'fa-times text-danger';
        $projectData['estimateIcon'] = '<i class="fa fa-lg ' . $estimateIcon . ' " aria-hidden="true"></i>';
        $projectData['orderAcceptIcon'] = '<i class="fa fa-lg ' . $orderAcceptIcon . ' sl-green" aria-hidden="true"></i>';
        $projectData['workIcon'] = '<i class="fa fa-lg ' . $workIcon . ' sl-green" aria-hidden="true"></i>';

        //gd_debug($projectData['addedInfo']);
        //Textarea Strip Data
        ///$totalCost = 0;
        //$totalPrice = 0;
        $tableProject = SlCommonUtil::arrayAppKeyValue(DBIms::tableImsProject(), 'val', 'strip');
        foreach ($projectData as $productDataKey => $productEach) {
            if (!empty($tableProject[$productDataKey])) {
                $projectData[$productDataKey] = gd_htmlspecialchars_stripslashes($productEach);
            }
        }
        //$projectData['totalCost'] = $totalCost;
        //$projectData['totalPrice'] = $totalPrice;

        //긴급도 넣기.(발주전까지)
        //customerDeliveryRemainDt
        $projectData['urgency'] = '';
        $sDate = date('Y-m-d'); //Today
        $eDate = gd_date_format('Y-m-d',$projectData['customerDeliveryDt']);
        $dateDiff = SlCommonUtil::getDateDiff($sDate, $eDate);
        if( 90 > $projectData['projectStatus'] && 100 > $dateDiff ){
            $projectData['urgency'] = '긴급';
        }else if( 90 > $projectData['projectStatus'] &&  120 >= $dateDiff ) {
            $projectData['urgency'] = '보통';
        }else if( 90 > $projectData['projectStatus'] &&  $dateDiff > 120 ){
            $projectData['urgency'] = '여유';
        }

        $this->setProjectIcon($projectData, $projectData['sno']);
        $projectData['regDtOrg']=$projectData['regDt'];

        $projectData = array_merge($projectData, $this->getProjectAddInfo($projectData['sno']));


        //신규/리오더 구분
        if(in_array($projectData['projectType'], [0,2,5,6])){
            $projectData['isReorder'] = 'n';
        }else{
            $projectData['isReorder'] = 'y';
        }

        $this->refineProjectIgnoreItem($projectData);

        return $projectData;
    }


    /**
     * 프로젝트 해당 없음 데이터 처린
     *
     * 해당없음 처리의 두가지 형태
     * 1. 음영을 주지 않는 해당없음 -> 언제라도 일정이 들어갈 수 있음
     * 2. 음영이 있는 해당없음 -> 타입에 따라 거의 하지 않음
     * @param $projectData
     */
    public function refineProjectIgnoreItem(&$projectData){
        $ignoreWord = '해당없음';

        //기획 해당 없음 처리
        if(empty($projectData['planDt']) || '0000-00-00' == $projectData['planDt'] ){
            $projectData['planDtShort2'] = $ignoreWord;
        }
        if( (empty($projectData['planDt']) || '0000-00-00' == $projectData['planDt'])
            && (empty($projectData['planEndDt']) || '0000-00-00' == $projectData['planEndDt']) ){
            $projectData['planEndDtShort2'] = $ignoreWord;
        }

        //가발주(원부자재선행) 해당 없음 처리
        if(empty($projectData['fakeOrderExpectedDt']) || '0000-00-00' == $projectData['fakeOrderExpectedDt'] ){
            $projectData['fakeOrderExpectedDtShort'] = $ignoreWord;
        }
        if( (empty($projectData['fakeOrderExpectedDt']) || '0000-00-00' == $projectData['fakeOrderExpectedDt'])
            && (empty($projectData['fakeOrderCompleteDt']) || '0000-00-00' == $projectData['fakeOrderCompleteDt']) ){
            $projectData['fakeOrderCompleteDtShort'] = $ignoreWord;
        }

        $ignoreProjectType = [1,3,4,7]; //리오더, 추가, 기성복 FIXME 기성복이라도 샘플제안이 있다면 해당없음 취소
        $ignoreField = ['qb', 'estimate', 'sampleOrder', 'sampleIn', 'sampleOut', 'sampleReview', 'custSampleInform'];

        if( 4 == $projectData['projectType'] ){
            unset($ignoreField['custSampleInform']);
        }

        if(in_array($projectData['projectType'], $ignoreProjectType)){
            foreach($ignoreField as $field){
                //$projectData[$field.'ExpectedDtShort'] = $ignoreWord;
                //$projectData[$field.'CompleteDtShort'] = $ignoreWord;
                if( ( empty($projectData[$field.'ExpectedDt']) || '0000-00-00' == $projectData[$field.'ExpectedDt'] )
                    && empty($projectData[$field.'AlterText']) ){
                    $projectData[$field.'AlterText']=$ignoreWord;
                }
            }
        }
    }

    /**
     * 프로젝트 파일 정보를 반환
     * @param $projectSno
     * @param int $styleSno
     * @return array
     */
    public function getProjectFiles($projectSno, $styleSno=0 )
    {
        //고객 단위 파일 리스트
        $customerFileDivList = [
            'fileSalesStrategy',
            'fileMeetingReport',
            'fileSampleGuide',
        ];

        $projectData = DBUtil2::getOne(ImsDBName::PROJECT, 'sno', $projectSno);

        $fileMapList = [];

        //IMS고도화 추가 파일
        $fileMapList[] = [
            ['fieldName' => 'sampleFile1', 'title' => '샘플의뢰서(개별)'],
            ['fieldName' => 'sampleFile2', 'title' => '실물사진(개별)'],
            ['fieldName' => 'sampleFile3', 'title' => '패턴(개별)'],
            ['fieldName' => 'sampleFile4', 'title' => '기타파일(개별)'],
            ['fieldName' => 'btFile1', 'title' => 'BT의뢰서'],
            ['fieldName' => 'btFile2', 'title' => 'BT결과'],
            ['fieldName' => 'bulkFile', 'title' => 'BULK결과'],
        ];

        $fileMapList[] = [
            ['fieldName' => 'fileSample', 'title' => '샘플의뢰서',],
            ['fieldName' => 'fileSampleConfirm', 'title' => '샘플검토',],
            ['fieldName' => 'filePattern', 'title' => '샘플패턴',],
            ['fieldName' => 'fileSampleEtc', 'title' => '샘플기타',],
            ['fieldName' => 'fileCareMark', 'title' => '케어라벨',],
            ['fieldName' => 'filePrdMark', 'title' => '마크',],
            ['fieldName' => 'filePrdEtc', 'title' => '생산기타파일',],
        ];
        $fileMapList[] = ImsCodeMap::PROJECT_FILE;      //주요파일
        $fileMapList[] = ImsCodeMap::PROJECT_ETC_FILE;  //기타파일
        $fileMapList[] = ImsCodeMap::PREPARED_FILE;     //생산선행파일

        $fileMapList[] = [
            ['fieldName' => 'filePacking','title' => '분류패킹',],
        ];
        $fileMapList[] = [
            ['fieldName' => 'fileBarcode','title' => '3PL바코드',],
        ];

        //생산파일
        foreach (ImsCodeMap::PRODUCE_STEP_MAP as $stepKey => $stepTitle) {
            $produceFileList[] = [
                'title' => $stepTitle,
                'fieldName' => 'prdStep' . $stepKey,
            ];
        }

        $fileList = $produceFileList;
        foreach ($fileMapList as $fileMapKey => $fileMapData) {
            $fileList = array_merge($fileList, $fileMapData);
        }

        $resultFileList = $this->getLatestProjectFiles([
            'projectSno' => $projectSno,
            'styleSno' => $styleSno,
        ]);
        //Customer 단위 파일 가져오기.
        $customerFileList = $this->getLatestProjectFiles([
            'customerSno' => $projectData['customerSno'],
            'fileDivList' => $customerFileDivList,  //
        ]);
        $resultFileList = array_merge($resultFileList, $customerFileList);
        if (empty($resultFileList)) {
            $resultFileList = [];
        }
        foreach ($fileList as $key => $value) { //구조셋팅.
            //SitelabLogger::logger($value['fieldName']);
            if (empty($resultFileList[$value['fieldName']]) || $value['noRev']) {
                $resultFileList[$value['fieldName']] = [
                    'title' => '등록된 파일이 없습니다.',
                    'memo' => '',
                    'files' => [],
                    'noRev' => $value['noRev']
                ];
            }
        }

        return $resultFileList;
    }

    /**
     * 프로젝트 스타일 리스트
     * @param $params
     * @param string $delFl
     * @return mixed
     * @throws \Exception
     */
    public function getProductList($params, $delFl = 'n')
    {
        $addWhere = '';
        $condition = $params['searchCondition'];

        if( !empty($condition['multiKey']) ){

            $prdCondition = [];
            foreach( $condition['multiKey'] as $keyIndex => $keyCondition ){
                if( 'prd.styleCode' == $keyCondition['key'] ){
                    $key = "REPLACE(".$keyCondition['key'].",' ','')";
                    $keyword = str_replace(' ','',$keyCondition['keyword']);
                    $prdCondition[] = " ( {$key} like '%{$keyword}%' ) ";
                }
                if( 'prd.productName' == $keyCondition['key'] ){
                    $key = "REPLACE(".$keyCondition['key'].",' ','')";
                    $keyword = str_replace(' ','',$keyCondition['keyword']);
                    $prdCondition[] = " ( {$key} like '%{$keyword}%' ) ";
                } //duplicate 3개 이상 검색 시 수정.
            }
            if(count($prdCondition) > 0){
                $addWhere = ' and (' . implode(' '.$condition['multiCondition'].' ', $prdCondition) . ')';
            }
        }

       $sql = "select *
         , ( select count(1) from sl_imsSample where styleSno = prd.sno  ) as sampleCnt         
         -- , ( select count(1) from sl_imsFabric where styleSno = prd.sno  ) as newFabricCnt
         -- , ( select count(1) from sl_imsFabric where styleSno = prd.sno and btStatus = 2  ) as newBtCnt
         -- , ( select count(1) from sl_imsFabric where styleSno = prd.sno and fabricStatus = 2 ) as fabricCompleteCnt
         , ( select count(1) from sl_imsProduction where styleSno = prd.sno ) as productionCnt
        from sl_imsProjectProduct prd 
        where delFl = '{$delFl}'
          and projectSno = {$params['projectSno']}
          {$addWhere}
        order by sort, regDt desc
        ";
        $list = DBUtil2::runSelect($sql);
        foreach ($list as $key => $each) {
            $each['usedFabricList'] = [];
            //Fabric Data 추가
            $each['usedFabricListShow'] = 'off';
            $each['usedEworkListShow'] = 'off';
            $fabricList = $this->getFabricList([
                'projectSno' => $each['projectSno'],
                'styleSno' => $each['sno'],
                'ignoreStatus' => '5',
            ]);
            if(!empty($fabricList)){
                $each['usedFabricList'] = $fabricList;
            }
            $list[$key] = $this->decorationProductData($each);
        }
        return SlCommonUtil::setDateBlank($list);
    }

    /**
     * 휴지통 스타일 반환
     * @param $params
     * @return mixed
     */
    public function getRemoveProductList($params)
    {
        return $this->getProductList($params, 'y');
    }


    /**
     * 상품의 추가 정보 추가
     * @param $each
     * @return mixed
     */
    public function decorationProductData($each)
    {
        $each['addedInfo'] = json_decode($each['addedInfo'],true);
        $each['addedInfo'] = SlCommonUtil::setJsonField($each['addedInfo'], ImsJsonSchema::PRD_ADD_INFO);

        $each['isDetail'] = 'n';

        $each['ework'] = [
            'data' => null,
            'fileList' => null,
        ];

        $each['guide'] = [
            'filePrd' => '', //썸네일
            'fileSpec' => '', //사이즈스펙 그림
            'prdFabricInfo' => '', //원단설명
            'specData' => '',
            'markInfo' => '', //해야하는데 일단 제외
        ];

        $each['fabricList'] = [];
        $each['subFabricList'] = [];

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

        if( !empty($each['prdCost']) && !empty($each['salePrice']) ){
            $each['msMargin'] = round( 100 - ($each['prdCost']/$each['salePrice']*100) );
        }else{
            $each['msMargin'] = 0;
        }

        //사이즈스펙 재설정 ---------------------------------------------------------------------------
        $sizeSpecData = json_decode($each['sizeSpec'], true);
        if(empty($sizeSpecData) || !is_array($sizeSpecData) ){
            $sizeSpecData = SlCommonUtil::setJsonField([],ImsJsonSchema::SIZE_SPEC_DATA);
        }else{
            $sizeSpecData = SlCommonUtil::setJsonField($sizeSpecData,ImsJsonSchema::SIZE_SPEC_DATA);
        }
        $each['sizeSpec'] = $sizeSpecData;

        //$sizeSpecPrdData = DBUtil2::getOne(ImsDBName::PRODUCT, 'sno', $each['sno'], false);
        //$each['sizeSpec2'] = json_decode($sizeSpecPrdData['sizeSpec'],true);
        //$each['sizeSpec2'] = json_decode($prdData,true);

        $each['sizeList'] = explode(',',$sizeSpecData['specRange']);
        //------------------------------------------------------------------------------------------

        $each['sizeOption'] = json_decode(gd_htmlspecialchars_stripslashes($each['sizeOption']), true);
        if (empty($each['sizeOption'])) $each['sizeOption'] = [''];

        if (!empty($each['typeOption'])) {
            $each['typeOption'] = json_decode(gd_htmlspecialchars_stripslashes($each['typeOption']), true);
        } else {
            $each['typeOption'] = [];
        }

        $each['typeOptionStr'] = $each['typeOption'];

        $each['memo'] = gd_htmlspecialchars_stripslashes($each['memo']);

        $prdYear = substr($each['prdYear'], 2, 2);
        $each['styleFullName'] = "{$prdYear} {$each['prdSeason']} {$each['productName']}";
        $each['produceTypeKr'] = ImsCodeMap::PRODUCE_TYPE[$each['produceType']];

        $each['produceCompanyKr'] = '';
        if( !empty($each['produceCompanySno']) ){
            $each['produceCompanyKr'] = DBUtil2::getOne(DB_MANAGER, 'sno', $each['produceCompanySno'])['managerNm'];
        }

        $each['prdGenderKr'] = empty(ImsCodeMap::SEX_CODE[strtolower($each['prdGender'])])?'공용':ImsCodeMap::SEX_CODE[strtolower($each['prdGender'])];
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

        $each['priceConfirmKr'] = ImsCodeMap::PROJECT_CONFIRM_TYPE[$each['priceConfirm']]['name'];
        $each['styleSno'] = $each['sno'];

        $each['inlineStatusKr'] = empty($each['inlineStatus'])?'없음':'보관';

        //$each['fabric'] = json_decode(gd_htmlspecialchars_stripslashes($each['fabric']), true);
        //$each['subFabric'] = json_decode(gd_htmlspecialchars_stripslashes($each['subFabric']), true);
        $each['fabric'] = [
            ImsJsonSchema::FABRIC_INFO
        ];
        $each['subFabric'] = [
            ImsJsonSchema::SUB_FABRIC_INFO
        ];

        $productChangeList = DBUtil2::runSelect("select * from sl_imsProjectProductTmp where sno = {$each['sno']} order by modDt desc", null, false);
        foreach($productChangeList as $prdHis){
            $prdHis['fabric'] = json_decode(gd_htmlspecialchars_stripslashes($prdHis['fabric']), true);
            $prdHis['subFabric'] = json_decode(gd_htmlspecialchars_stripslashes($prdHis['subFabric']), true);
            $each['fabricHistory'][] = $prdHis;
        }

        $searchVo = new SearchVo('styleSno=?', $each['sno']);
        $searchVo->setOrder('a.regDt desc');
        $each['latestProduction'] = DBUtil2::getOneBySearchVo(ImsDBName::PRODUCTION, $searchVo);

        //파일가져오기
        //$loadFileList = ['fileWork'];

        $prdList = DBUtil2::getList(ImsDBName::PRODUCTION, 'styleSno', $each['sno']);
        $each['file']['fileWork'] = $this->getLatestFileList([
            'styleSno'=>$each['sno']
            ,'fileDiv'=>'fileWork'
            ,'eachSno'=>$prdList[count($prdList)-1]['sno']
        ]
        ); //작지


        //판매가 가림
        if( !empty($_COOKIE['setSaleCostDisplay']) &&  'n' === $_COOKIE['setSaleCostDisplay'] ){
            $each['salePrice'] = 0;
            $each['currentPrice'] = 0;
            $each['targetPrice'] = 0;
            $each['targetPrdCost'] = 0;
        }

        $each['specOptionList'] = explode(',',$each['sizeSpec']['specRange']);

        //SitelabLogger::logger2(__METHOD__, 'decorationProductData .... ASSORT SET');
        $each['assort'] = $this->getAssort($each);

        //FIXME 함수화 하기
        $each['assortTotal'] = 0;

        $each['optionTotal'] = [];
        foreach( $each['specOptionList'] as $size ){
            $each['optionTotal'][$size]=0;
        }

        $each = $this->setCommonProductDecoration($each);

        return $each;
    }

    /**
     * 스키마 리스트 반환
     * @param $listData
     * @param $schema
     * @return mixed
     */
    public function setSchemaList($listData, $schema)
    {
        foreach ($listData as $key => $each) {
            foreach ($schema as $schemaKey => $schemaValue) {
                if (!isset($each[$schemaKey])) {
                    $each[$schemaKey] = $schemaValue;
                }
            }
            $listData[$key] = $each;
        }
        return $listData;
    }

    /**
     * 상품정보만 담백하게 전달.
     * @param $sno
     * @return mixed
     */
    public function getSimpleProductData($sno)
    {
        $searchVo = new SearchVo('a.sno=?', $sno);
        $prdList = DBUtil2::getComplexList($this->sql->getProductTable(), $searchVo, false, false, false);
        $prd = $prdList[0];
        unset($prd['fabric']);
        unset($prd['subFabric']);
        $prd['assort'] = stripslashes($prd['assort']);

        if (empty($prd['sno'])) {
            $prd = $this->getProductDefaultScheme($prd);
        }
        //SitelabLogger::logger2(__METHOD__, 'getProductData .... ASSORT SET 1');
        //SitelabLogger::logger2(__METHOD__, $prd['assort']);
        return $this->decorationProductData($prd);
    }

    /**
     * 스타일 기본 구조 반환
     * @param $productData
     * @return mixed
     */
    public function getProductDefaultScheme($productData){
        /*신규 등록*/
        $schemaList = DBIms::tableImsProjectProduct();
        foreach ($schemaList as $key => $schema) {
            $productData[$schema['val']] = '';
        }
        foreach (ImsJsonSchema::PRD_ADD_INFO as $addInfoKey => $addInfo) {
            $productData['addedInfo'][$addInfoKey] = '';
        }

        $productData['fabric'] = []; //원자재
        $productData['subFabric'] = []; //부자재

        //Default Data
        $productData['prdYear'] = date('Y');
        $productData['projectSno'] = $projectSno;
        $productData['customerSno'] = $result['customer']['sno'];

        //최초는 고객납기 / 이노버 납기 연동
        $productData['customerDeliveryDt'] = $result['project']['customerDeliveryDt'];
        $productData['msDeliveryDt'] = $result['project']['msDeliveryDt'];

        //사이즈 스펙으로 대체 할 예정 ( 생산때문에 우선 별첨 기본으로 들어가야 한다, 당분간 삭제하지 말 것 )
        $productData['sizeOption'] = ['별첨'];
        unset($productData['sno']);
        return $productData;
    }


    /**
     * 스타일 데이터
     * @param $params
     * @return mixed
     * @throws \Exception
     */
    public function getProductData($params)
    {
        $productData = $this->getSimpleProductData($params['sno']); //기본 스타일 데이터.
        $projectSno = empty($params['projectSno']) ? $productData['projectSno'] : $params['projectSno'];

        //프로젝트 정보는 기본 로딩.
        $result = $this->getProject(['sno' => $projectSno, 'styleSno'=>$params['sno']]);

        //상품
        //판매가 가림
        if( !empty($_COOKIE['setSaleCostDisplay']) &&  'n' === $_COOKIE['setSaleCostDisplay'] ){
            $productData['salePrice'] = 0;
        }

        $result['product'] = SlCommonUtil::setDateBlank($productData);

        if (empty($result['product']['sizeOption'])) {
            $result['product']['sizeOption'] = [];
        }
        if (empty($result['product']['typeOption'])) {
            $result['product']['typeOption'] = [];
        }

        //SampleView 기본 구조 설정 (sno가 있을 경우 실 데이터는 스크립트로 로딩)
        $result['viewDefaultSample'] = $this->getSampleViewDefaultData($result);
        //FabricView 기본 구조 설정 (sno가 있을 경우 실 데이터는 스크립트로 로딩)
        $result['viewDefaultFabric'] = $this->getFabricViewDefaultData($result);
        //생산처 견적 기본 구조
        $result['viewDefaultEstimate'] = $this->getEstimateViewDefaultData($result);
        //생산처 확정가 기본 구조
        $result['viewDefaultCost'] = $this->getCostViewDefaultData($result);
        //생산 기본 구조
        $result['viewDefaultProduction'] = $this->getSchemaProduction($result['product']);

        //생산처 여부
        $result['isFactory'] = SlCommonUtil::isFactory();

        $eworkService = SlLoader::cLoad('ims','ImsEworkService');
        $eworkService->setEworkData($result);

        //SitelabLogger::logger2(__METHOD__, 'getProductData .... ASSORT SET 2');
        //SitelabLogger::logger2(__METHOD__, $result['product']['assort']);
        //$result['product']['assort'] = stripslashes($result['product']['assort']);
        $result['product']['assort'] = $this->getAssort($result['product']);

        $result['product']['optionTotal'] = [];
        $result['product']['divTotal'] = [];

        //mainData.product.assort

        foreach( explode(',',$result['product']['sizeSpec']['specRange']) as $size ){
            $result['product']['optionTotal'][$size]=0;
        }

        $result['product']['productionInfo'] = DBUtil2::getOne(ImsDBName::PRODUCTION, 'styleSno', $params['sno']);

        return $result;
    }

    public function getProduct($params){
        return $this->getProductData($params);
    }

    /**
     * 원단. 번호 설정
     * @param $noSetTargetList
     * @param string $type
     * @return mixed
     */
    public function setFabricNo(&$noSetTargetList, $type = 'fabric')
    {
        foreach ($noSetTargetList as $eachKey => $eachValue) {
            if ('fabric' === $type) {
                $eachValue['no'] = 0 == $eachKey ? 'G' : chr(ord('A') + ($eachKey - 1));
            } else {
                $eachValue['no'] = chr(ord('A') + ($eachKey));
            }
            $noSetTargetList[$eachKey] = $eachValue;
        }
        return $noSetTargetList;
    }


    /**
     * 프로젝트 파일 저장
     * @param $params
     * @return array
     * @throws \Exception
     */
    public function saveProjectFiles($params)
    {
        $saveData = [];
        //최근 Revision 가져오기
        $revisionCheckList = [
            'customerSno',
            'projectSno',
            'styleSno',
            'eachSno',
        ];
        $searchVo = new SearchVo('fileDiv=?', $params['fileDiv']);
        foreach ($revisionCheckList as $checkKey) {
            if (!empty($params[$checkKey])) { //값이 있어야함.
                $searchVo->setWhere($checkKey . '=?');
                $searchVo->setWhereValue($params[$checkKey]);
                $saveData[$checkKey] = $params[$checkKey]; //update된다.
            }
        }
        $searchVo->setOrder('regDt desc');

        $latestData = DBUtil2::getOneBySearchVo(ImsDBName::PROJECT_FILE, $searchVo);

        $saveData['rev'] = gd_isset($latestData['rev'], 0) + 1;
        $saveData['fileDiv'] = $params['fileDiv'];
        $saveData['fileList'] = json_encode($params['fileList']);
        $saveData['memo'] = $params['memo'];

        $prjData = DBUtil2::getOne(ImsDBName::PROJECT, 'sno', $saveData['projectSno']);
        if (empty($saveData['customerSno']) && !empty($saveData['projectSno'])) {
            $saveData['customerSno'] = $prjData['customerSno']; //고객 정보가 없으면 고객정보 넣기
        }

        //완료일이 필요한
        $workCompleteCheckMap = [
            'filePlan' => 'planEndDt',
            'fileProposal' => 'proposalEndDt',
        ];
        $checkField = $workCompleteCheckMap[$params['fileDiv']];
        if (!empty($checkField)) {
            if (empty($prjData[$checkField]) || '0000-00-00' === $prjData[$checkField]) {
                $this->save(ImsDBName::PROJECT, [
                    'sno' => $prjData['sno'],
                    $checkField => 'now()',
                ]);
            }
        }

        $sno = $this->save(ImsDBName::PROJECT_FILE, $saveData, null, true);//insert only
        $this->saveProjectFileAfter($params);

        ImsScheduleUtil::setProjectScheduleStatus($saveData['projectSno']);

        return $this->getLatestProjectFiles(['sno' => $sno]);
    }

    /**
     * 저장 후 처리.
     * @param $params
     * @throws \Exception
     */
    public function saveProjectFileAfter($params)
    {
        //원부자재 선적시 완료일 처리.
        if ('fileFabricShip' === $params['fileDiv']) {
            $saveData['sno'] = $params['eachSno'];
            $saveData['fabricShipCompleteDt'] = 'now()';
            $this->save(ImsDBName::PRODUCTION, $saveData);
        }

        //선적파일 등록시 처리
        if ('fileShip' === $params['fileDiv']) {
            $customer = DBUtil2::getOne(ImsDBName::CUSTOMER, 'sno', $params['customerSno']);
            $style = DBUtil2::getOne(ImsDBName::PRODUCT, 'sno', $params['styleSno']);
            $subject = "선적 파일 등록 ({$customer['customerName']} {$style['productName']})";
            SiteLabMailUtil::sendSystemMail($subject, $subject.'<br><br>확인해보시기 바랍니다.', implode(',',ImsCodeMap::PRIVATE_MALL_MANAGER_MAIL));
        }

        //작업지시서 파일 등록시 (초기부터 모두 기록)
        if( !empty($params['styleSno']) ){
            $imsService = SlLoader::cLoad('ims', 'imsService');
            $workFileKr = [
                'fileMain'=>'메인도안',
                'filePrd'=>'사양서썸네일',
                'fileBatek'=>'바텍',
                'fileAi'=>'메인AI', //AI 파일
                'fileMarkAi'=>'마크AI', //AI 파일
                'fileCareAi'=>'캐어AI', //AI 파일
                'fileMark'=>'마크',
                'fileMarkPosition'=>'마크위치',
                'filePosition'=>'캐어라벨 위치',
                'fileCare'=>'캐어라벨',
                'fileSpec'=>'스펙',
                'filePacking'=>'포장관련 ', //접는 방법
            ];
            foreach(ImsCodeMap::EWORK_FILE_LIST as $eworkFileDiv){
                if($eworkFileDiv === $params['fileDiv']) {
                    $eworkFileDivKrSource = preg_replace('/[0-9]/', '', $eworkFileDiv);
                    $recordData['sno'] = $params['styleSno'];
                    $imsService->recordHistory('update', ImsDBName::EWORK, $recordData, [$workFileKr[$eworkFileDivKrSource]. ' 파일 등록 함']);
                }
            }
        }

    }


    /**
     * 프로젝트 파일 가져오기
     * @param $projectSno
     * @param $params
     * @return array
     */
    public function getLatestProjectFiles($params)
    {

        $tableInfo = $this->sql->getFileTable();
        if (!empty($params['sno'])) {
            //개별적으로 가져올경우. ( insert 후 )
            $fileInfoList = DBUtil2::getComplexList($tableInfo, new SearchVo('a.sno=?', $params['sno']));
        } else {
            $searchVo = new SearchVo();
            $revisionCheckList = [
                'customerSno',
                'projectSno',
                'styleSno',
                'eachSno',
            ];
            foreach ($revisionCheckList as $checkKey) {
                if (!empty($params[$checkKey])) { //값이 있어야함.
                    $searchVo->setWhere('a.' . $checkKey . '=?');
                    $searchVo->setWhereValue($params[$checkKey]);
                    $saveData[$checkKey] = $params[$checkKey]; //update된다.
                }
            }
            // $searchVo = new SearchVo('projectSno=?', $params['projectSno']); //변경 : FIXME : 확인 후 지우기.
            $searchVo->setOrder('a.rev');

            if (!empty($params['fileDiv'])) {
                $searchVo->setWhere('fileDiv=?');
                $searchVo->setWhereValue($params['fileDiv']);
            }else if(!empty($params['fileDivList'])){
                $searchVo->setWhere(DBUtil2::bind('fileDiv', DBUtil2::IN, count($params['fileDivList']) ));
                $searchVo->setWhereValueArray( $params['fileDivList'] );
            }

            $fileInfoList = DBUtil2::getComplexList($tableInfo, $searchVo);

        }

        $resultList = [];

        foreach ($fileInfoList as $key => $value) {
            if (empty($resultList[$value['fileDiv']]) || $value['rev'] > $resultList[$value['fileDiv']]['rev']) {
                $value['title'] = 'Rev' . $value['rev'] . ' ' . $value['managerNm'] . '등록' . '(' . gd_date_format('y/m/d', $value['regDt']) . ')';
                //Rev1. 홍길동 등록(23/07/21)
                $value['fileList'] = json_decode($value['fileList'], true);

                $resultList[$value['fileDiv']]['title'] = $value['title'];
                $resultList[$value['fileDiv']]['memo'] = $value['memo'];
                $resultList[$value['fileDiv']]['files'] = $value['fileList'];
                $resultList[$value['fileDiv']]['sno'] = $value['sno'];
            }
        }

        return $resultList;
    }

    /**
     * 최근 파일 가져오기 (키는 직접 => 위험 TODO :추후 변경 )
     * @param $params
     * @return array
     */
    public function getLatestFileList($params){
        //있는거 그대로 검색.
        unset($params['mode']);
        $searchVo = new SearchVo();
        foreach($params as $key => $value){
            $searchVo->setWhere('a.'.$key . '=?');
            $searchVo->setWhereValue($value);
        }
        $searchVo->setOrder('a.regDt desc');

        $fileInfoList = DBUtil2::getComplexList($this->sql->getFileTable(), $searchVo); //strip ?
        $fileInfo = $fileInfoList[0]; //최신파일 정보

        $result['title'] = 'Rev' . $fileInfo['rev'] . ' ' . $fileInfo['managerNm'] . '등록' . '(' . gd_date_format('y/m/d', $fileInfo['regDt']) . ')'; //Rev1. 홍길동 등록(23/07/21)
        $result['memo'] = $fileInfo['memo'];
        $result['files'] = json_decode($fileInfo['fileList'], true);
        $result['sno'] = $fileInfo['sno'];
        $result['customerSno'] = $fileInfo['customerSno'];
        $result['projectSno'] = $fileInfo['projectSno'];
        $result['styleSno'] = $fileInfo['styleSno'];
        $result['fileDiv'] = $fileInfo['fileDiv'];
        $result['regManagerSno'] = $fileInfo['regManagerSno'];

        return $result;
    }

    /**
     * 고객 저장.
     * @param $saveData
     * @return mixed
     * @throws \Exception
     */
    public function saveCustomer($saveData)
    {
        if( empty($saveData['customerName']) ){
            throw new \Exception('고객명을 입력하세요.');
        }
        if( empty($saveData['styleCode']) ){
            throw new \Exception('고객 기본 스타일코드를 입력하세요.');
        }

        $saveData['addedInfo'] = json_encode($saveData['addedInfo']);
        $customerSno = $this->save(ImsDBName::CUSTOMER, $saveData);

        $loadCustomer = DBUtil2::getOne(ImsDBName::CUSTOMER, 'sno', $customerSno);
        //3PL 상태 프로젝트와 동기화
        $updateSql = "update sl_imsProject a join sl_imsCustomer b on a.customerSno = b.sno set a.use3pl = '{$loadCustomer['use3pl']}', a.useMall = '{$loadCustomer['useMall']}' where a.customerSno = {$customerSno}";
        DBUtil2::runSql($updateSql);

        return $customerSno;
    }

    /**
     * 미팅정보 저장.
     * @param $saveData
     * @return mixed
     * @throws \Exception
     */
    public function saveMeeting($saveData)
    {
        return $this->save(ImsDBName::MEETING, $saveData);
    }

    /**
     * 선행 요청 작업 저장
     * @param $saveData
     * @return mixed
     * @throws \Exception
     */
    public function savePreparedReq($saveData)
    {

        if (isset($saveData['contents'])) {
            $saveData['contents'] = json_encode($saveData['contents']);
        }
        if ('work' === $saveData['preparedType']) {
            //작지 요청일 경우 처리 요청일 = 예정일
            $this->save(ImsDBName::PROJECT, ['sno' => $saveData['projectSno'], 'workDt' => $saveData['deadLineDt']]);
        }
        if (isset($saveData['preparedStatus'])) {
            $prepared = DBUtil2::getOne(ImsDBName::PREPARED, 'sno', $saveData['sno']);
            if (4 == $saveData['preparedStatus'] || 5 == $saveData['preparedStatus'] && $prepared['preparedStatus'] != $saveData['preparedStatus']) {
                $preparedConst = constant('Component\Ims\EnumType\PREPARED_TYPE::' . strtoupper($prepared['preparedType']));
                $saveData['acceptManager'] = \Session::get('manager.managerNm');
                $saveData['acceptDt'] = 'now()';
                //승인/반려시 알림보내기.
                $projectData = $this->getProject(['sno' => $prepared['projectSno']]);
                $replaceData = ImsSendMessage::imsMessageReplacer(ImsSendMessage::PREPARED_ACCEPT_COMMON, [
                    'title' => $preparedConst['title'],
                    'company' => $projectData['customer']['customerName'],
                    'projectNo' => $projectData['project']['projectNo'],
                    'productName' => $projectData['project']['styleWithCount'],
                    'acceptType' => (4 == $saveData['preparedStatus'] ? '승인완료' : '반려처리'),
                ]);
                $this->sendAlarm($replaceData['title'], $replaceData['msg'], $prepared['produceCompanySno']);
            }
        }

        if (empty($saveData['sno']) && !empty($saveData['produceCompanySno'])) {
            //신규 등록시. 요청.
            $preparedConst = constant('Component\Ims\EnumType\PREPARED_TYPE::' . strtoupper($saveData['preparedType']));
            $projectData = $this->getProject(['sno' => $saveData['projectSno']]);

            $replaceData = ImsSendMessage::imsMessageReplacer(ImsSendMessage::PREPARED_COMMON, [
                'title' => $preparedConst['title'],
                'company' => $projectData['customer']['customerName'],
                'projectNo' => $projectData['project']['projectNo'],
                'productName' => $projectData['project']['styleWithCount'],
            ]);
            $this->sendAlarm($replaceData['title'], $replaceData['msg'], $saveData['produceCompanySno']);
        }

        //SitelabLogger::logger('저장 데이터 확인.');
        //SitelabLogger::logger($saveData);

        return $this->save(ImsDBName::PREPARED, $saveData);
    }

    /**
     * 고객사 삭제
     * @param $params
     * @return mixed
     * @throws \Exception
     */
    public function deleteCustomer($params)
    {
        $this->delete(ImsDBName::CUSTOMER, $params['sno']); //프로젝트 등록된 고객이라면 삭제 불가.
    }

    /**
     * 스타일 삭제
     * @param $params
     * @throws \Exception
     */
    public function deleteProjectProduct($params)
    {
        $this->delete(ImsDBName::PRODUCT, $params['sno']); //프로젝트 등록된 고객이라면 삭제 불가.
    }

    /**
     * 프로젝트 코멘트 삭제
     * @param $params
     * @throws \Exception
     */
    public function deleteProjectComment($params)
    {
        $this->delete(ImsDBName::PROJECT_COMMENT, $params['sno']);
    }

    /**
     * 프로젝트 파일 삭제
     * @param $params
     * @throws \Exception
     */
    public function deleteImsFile($params)
    {
        $this->delete(ImsDBName::PROJECT_FILE, $params['sno']);
    }

    /**
     * 프로젝트 삭제
     * @param $params
     * @throws \Exception
     */
    public function deleteProject($params)
    {
        $this->delete(ImsDBName::PROJECT, $params['sno']);
    }

    /**
     * 고객 코멘트 삭제
     * @param $params
     * @throws \Exception
     */
    public function deleteCustomerIssue($params)
    {
        $this->delete(ImsDBName::CUSTOMER_ISSUE, $params['sno']); //프로젝트 등록된 고객이라면 삭제 불가.
    }

    /**
     * 고객 프로젝트 저장
     * @param $saveCustomer
     * @param $saveProject
     * @param $saveMeeting
     * @return mixed
     * @throws \Exception
     */
    public function saveProject($saveCustomer, $saveProject, $saveMeeting)
    {
        //고객 정보 저장
        $customerSno = $this->saveCustomer($saveCustomer);

        //$saveProject['workMemo']

        $saveProject['customerSno'] = $customerSno;
        $saveProject['addedInfo'] = json_encode($saveProject['addedInfo']);
        //기획.제안.샘플일자 해당없음 처리.
        /*foreach( ImsCodeMap::PROJECT_DESIGN_FIELD as $value ){
            if(!in_array($value['recommendType'], $saveProject['recommend'])){
                $saveProject[$value['name']] = '';
            }
        }*/
        $saveProject['recommend'] = array_sum($saveProject['recommend']);
        $saveProject['fabricNational'] = array_sum($saveProject['fabricNational']);
        //생산이 있다면 생산처 싱크

        //SitelabLogger::logger($saveProject);
        $this->syncProduceCompanySno($saveProject);
        $saveProject = $params['saveData'] = $this->setConfirmDt($saveProject);

        //프로젝트 저장.
        $projectSno = $this->save(ImsDBName::PROJECT, $saveProject, [
            'projectNo' => $this->createProjectNo()
        ]);

        if (empty($saveProject['sno'])) {
            $cnt = DBUtil2::getCount(ImsDBName::STATUS_HISTORY, new SearchVo('projectSno=?', $projectSno));
            if (0 >= $cnt) {
                $this->saveStatusHistory([
                    'projectSno' => $projectSno,
                    'beforeStatus' => '-',
                    'afterStatus' => $saveProject['projectStatus'],
                    'reason' => '초기 등록',
                ]);
            }
            //진행 상태값 초기화.
            DBUtil2::runSql("update sl_imsProject set btStatus = null, costStatus=null, estimateStatus=null, workStatus=null, orderStatus=null where sno = '{$projectSno}' ");
        }

        //미팅정보 저장. FIXME : 미팅정보 별도 분리.
        $saveMeeting['projectSno'] = $projectSno;
        $meeting = DBUtil2::getOne(ImsDBName::MEETING, 'projectSno', $projectSno);
        if (!empty($meeting)) {
            $saveMeeting['sno'] = $meeting['sno']; //없으면 공백 입력.
        }
        $this->saveMeeting($saveMeeting);
        $this->setSyncStatus($projectSno, __METHOD__);

        return $projectSno;
    }



    /**
     * 프로젝트 추가 정보 가져오기
     * @param $projectSno
     * @return array
     */
    public function getProjectAddInfo($projectSno){
        //기본 값 만들기
        $rslt = [];
        $addDataList = DBUtil2::getList(ImsDBName::PROJECT_ADD_INFO, "projectSno", $projectSno);

        foreach(ImsCodeMap::PROJECT_ADD_INFO as $key => $infoValue){
            foreach(ImsCodeMap::PROJECT_ADD_INFO_KEY as $addInfoKey){
                $rslt[$key.ucfirst($addInfoKey)] = '';
                $rslt[$key.ucfirst($addInfoKey).'Short'] = '<span class="text-muted">미정</span>';
                /*if( 'fakeOrder'  && ('expectedDt' == $addInfoKey || 'completeDt' == $addInfoKey )  ){
                    $rslt[$key.ucfirst($addInfoKey).'Short'] = '<span class="text-muted2"><b>해당없음</b></span>';
                }*/
            }

            foreach($addDataList as $addData){
                foreach(ImsCodeMap::PROJECT_ADD_INFO_KEY as $addInfoKey){
                    $rslt[$addData['fieldDiv'].ucfirst($addInfoKey)] = $addData[$addInfoKey]; //데이터 채워 넣기
                    $rslt[$addData['fieldDiv'].ucfirst($addInfoKey).'Short']  = SlCommonUtil::getSimpleWeekDay($addData[$addInfoKey], true);
                    $rslt[$addData['fieldDiv'].ucfirst($addInfoKey).'Remain'] = SlCommonUtil::getRemainDt2($addData[$addInfoKey]);
                }
            }
        }
        return $rslt;
    }

    /**
     * 디자인 정보만 수정. => 들어온 데이터 전체 수정.
     * @param $params
     * @return mixed
     * @throws \Exception
     */
    public function saveDesignData($params)
    {
        $this->syncProduceCompanySno($params['saveData']);
        $params['saveData'] = $this->setConfirmDt($params['saveData']);

        //자식 스타일 데이터 연동.
        $this->syncProduct($params['saveData']['sno'], $params['saveData']);

        $saveData = $params['saveData'];
        $saveData['recommend'] = array_sum($saveData['recommend']);
        $saveData['addedInfo'] = json_encode($saveData['addedInfo']); //addedInfo 저장.

        foreach (ImsJsonSchema::ADD_INFO as $addInfoKey => $addInfo) {
            $refineAddInfo[$addInfoKey] = empty($addedList[$addInfoKey]) ? '' : $addedList[$addInfoKey];
        }

        $saveData = $this->save(ImsDBName::PROJECT, $saveData);
        $this->setSyncStatus($params['projectSno'], __METHOD__);

        $this->saveAddInfo($params['saveData']);

        return $saveData;
    }


    /**
     * 생산처 변경 연동.
     * @param $saveProject
     * @throws \Exception
     */
    public function syncProduceCompanySno($saveProject)
    {
        if (isset($saveProject['produceCompanySno'])) {
            $prdData = DBUtil2::getOne(ImsDBName::PRODUCE, 'projectSno', $saveProject['sno']);
            if (!empty($prdData)) {
                $saveProduceComapany['sno'] = $prdData['sno'];
                $saveProduceComapany['produceCompanySno'] = $saveProject['produceCompanySno'];
                $this->save(ImsDBName::PRODUCE, $saveProduceComapany);
            }
        }
    }

    /**
     * 확정날짜 설정 (확정되면 기록)
     * @param $params
     * @return mixed
     */
    public function setConfirmDt($params)
    {

        $checkList = [
            'customerOrderConfirm', 'customerOrder2Confirm', 'customerEstimateConfirm', 'customerSaleConfirm'
        ];
        foreach ($checkList as $field) {
            $dtKey = $field . 'Dt';
            if ('y' === $params[$field] && empty($params[$dtKey])) {
                $project = DBUtil2::getOne(ImsDBName::PROJECT, 'sno', $params['sno']);
                if ('n' === $project[$field]) {
                    $params[$dtKey] = date('Y-m-d');
                }
            }
            if ('n' === $params[$field]) {
                $params[$dtKey] = '';
            }
        }
        return $params;
    }


    /**
     * 디자인 정보만 수정.
     * @param $params
     * @return mixed
     * @throws \Exception
     */
    public function saveProjectEachData($params)
    {
        $saveData = $params;
        //사양서 확정 + 저장할 정보가 y , 이전 정보는 n
        if ('y' === $params['customerOrderConfirm'] && empty($params['customerOrderConfirmDt'])) {
            $project = DBUtil2::getOne(ImsDBName::PROJECT, 'sno', $params['sno']);
            if ('n' === $project['customerOrderConfirm']) {
                $saveData['customerOrderConfirmDt'] = date('Y-m-d');
            }
        }
        if ('n' === $params['customerOrderConfirm']) {
            $saveData['customerOrderConfirmDt'] = '';
        }
        unset($saveData['mode']);

        $this->setSyncStatus($saveData['sno'], __METHOD__);

        return $this->save(ImsDBName::PROJECT, $saveData);
    }

    /**
     * 상품 정보 등록
     * @param $saveReqData
     * @return mixed
     * @throws \Exception
     */
    public function saveProduct($saveReqData)
    {
        unset($saveReqData['assort']);
        $saveData = $saveReqData;
        unset($saveData['fabric']);
        unset($saveData['subFabric']);

        $saveData['sizeOption'] = json_encode($saveReqData['sizeOption']);
        if (!empty($saveData['sizeOption'])) {
            $saveData['typeOption'] = json_encode($saveReqData['typeOption']);
        } else {
            $saveData['typeOption'] = json_encode([]);
        }

        //사이즈 스펙 기초 정보 저장
        $saveData['sizeSpec'] = json_encode($saveReqData['sizeSpec']);
        //SitelabLogger::logger2(__METHOD__, '사이즈 스펙 저장 테스트');
        //SitelabLogger::logger2(__METHOD__, $saveData['sizeSpec']);
        //판매가격이 0 이상, 판매확정상태가 승인 및 요청 이외일 경우 자동 승인 요청 등록.
        /*if(!empty($saveData['sno']) && $saveData['salePrice'] > 0 ){
            $orgPrd = DBUtil2::getOne(ImsDBName::PRODUCT, 'sno',$saveData['sno']);
            if( 'p' != $orgPrd['priceConfirm'] && 'r' != $orgPrd['priceConfirm'] ){
                $saveData[''];
            }
        }*/

        unset($saveData['projectSno']);
        $finalSaveData = $this->save(ImsDBName::PRODUCT, $saveData);

        $this->setSyncStatus($saveData['projectSno'], __METHOD__);

        return $finalSaveData;
    }


    /**
     * 코드 값 반환
     * @param $codeDiv
     * @param $codeType
     * @return array
     */
    public function getCode($codeDiv, $codeType)
    {
        $searchVo = new SearchVo();
        $searchVo->setOrder('codeSort');
        $searchVo->setWhere('codeDiv=?');
        $searchVo->setWhereValue($codeDiv);
        $searchVo->setWhere('codeType=?');
        $searchVo->setWhereValue($codeType);
        //codeValueKr+codeDescription , codeValueEn
        $codeList = DBUtil2::getListBySearchVo(ImsDBName::CODE, $searchVo);
        //gd_debug($codeList);
        return SlCommonUtil::arrayAppKeyValue($codeList, 'codeValueEn', 'codeValueKr');
    }

    /**
     * 프로젝트 번호 생성
     * @return string
     */
    public function createProjectNo()
    {
        $count = DBUtil2::getCount(ImsDBName::PROJECT, new SearchVo('regDt>=?', date('Y-m-d') . ' 00:00:00'));
        return (date('ymd')) . str_pad($count, 2, '0', STR_PAD_LEFT);
    }

    /**
     * 고객사 리스트
     * @return array
     */
    public function getCustomerListMap()
    {
        return SlCommonUtil::arrayAppKeyValue(DBUtil2::getList(ImsDBName::CUSTOMER, '1', '1'), 'sno', 'customerName');
    }

    /**
     * 샘플실 리스트
     * @return array
     */
    public function getSampleFactoryMap()
    {
        return SlCommonUtil::arrayAppKeyValue(DBUtil2::getList(ImsDBName::SAMPLE_FACTORY, 'factoryType', '1'), 'sno', 'factoryName');
    }

    /**
     * Step 별 카운트3pl
     * @return mixed
     */
    public function getProjectStepCount()
    {
        //getProjectStepCount
        //searchDefault.prjListCompanySno = '<?=!empty($imsProduceCompany)? $managerSno :''';
        $addedSql = '';
        if( SlCommonUtil::isFactory() ){
            $addedSql = 'and produceCompanySno='.\Session::get('manager.sno');
        }
        $sql = " select projectStatus, count(1) as cnt from sl_imsProject where 1=1 {$addedSql} group by projectStatus ";
        $projectStatus = SlCommonUtil::arrayAppKeyValue(DBUtil2::runSelect($sql), 'projectStatus', 'cnt');
        $result = [];
        foreach (ImsCodeMap::PROJECT_STATUS as $status => $statusName) {
            $result[$status] = empty($projectStatus[$status]) ? 0 : $projectStatus[$status];
        }
        return $result;
    }

    /**
     * 신규 생산 카운팅
     * @return array
     */
    public function getProductionCount()
    {
        $mSno = \Session::get('manager.sno');
        $addWhere = SlCommonUtil::isFactory() ?" and a.produceCompanySno={$mSno} ":'';

        $sql = " 
select a.produceStatus, count(1) as cnt 
  from sl_imsProduction a 
  join sl_imsProject b 
    on a.projectSno = b.sno 
 where a.produceStatus <> 0 {$addWhere} and a.produceStatus <> 99 
group by a.produceStatus ";
        $rsltMap = SlCommonUtil::arrayAppKeyValue(DBUtil2::runSelect($sql), 'produceStatus', 'cnt');
        $rtw = $this->getProductionRtwCount();
        $rsltMap[30] = $rsltMap[30] - $rtw[30];
        $rsltMap[40] = $rtw[30];
        return $rsltMap;
    }

    /**
     * 기성복 카운팅
     * @return array
     */
    public function getProductionRtwCount()
    {
        $mSno = \Session::get('manager.sno');
        $addWhere = SlCommonUtil::isFactory() ?" and a.produceCompanySno={$mSno} ":'';
        $sql = " select a.produceStatus, count(1) as cnt from sl_imsProduction a join sl_imsProject b on a.projectSno = b.sno where b.projectType = 4 and a.produceStatus = 30 {$addWhere} group by a.produceStatus ";
        $runResult = DBUtil2::runSelect($sql);
        if(empty($runResult)){
            return [30=>0];
        }else{
            return SlCommonUtil::arrayAppKeyValue($runResult, 'produceStatus', 'cnt');
        }
    }

    /**
     * 신규 요청관리 카운팅
     * @return array
     */
    public function getRequestCount()
    {
        $mSno = \Session::get('manager.sno');
        $addWhere = SlCommonUtil::isFactory() ?" and a.reqFactory={$mSno} ":'';

        $sql = " select count(1) as cnt from sl_imsEstimate a join sl_imsProject b on a.projectSno = b.sno join sl_imsProjectProduct c on a.styleSno = c.sno  where a.reqStatus = 1 {$addWhere}";
        $result['cost'] = DBUtil2::runSelect($sql)[0]['cnt'];

        $sql = " select count(1) as cnt from sl_imsFabricRequest a join sl_imsProject b on a.projectSno = b.sno  join sl_imsFabric c on a.fabricSno = c.sno where a.reqStatus = 1 {$addWhere}";
        $result['qb'] = DBUtil2::runSelect($sql)[0]['cnt'];

        return $result;
    }

    /**
     * 생산처 Step별 카운트
     * @return array
     */
    public function getPreparedProduceCount()
    {
        $mSno = \Session::get('manager.sno');
        $sql = " select preparedType, count(1) as cnt from sl_imsPrepared a join sl_imsProject b on a.projectSno = b.sno where preparedStatus IN (0,1) and a.produceCompanySno={$mSno} group by preparedType "; //요청,확인

        $projectStatus = SlCommonUtil::arrayAppKeyValue(DBUtil2::runSelect($sql), 'preparedType', 'cnt');
        $result = [];
        foreach (PREPARED_TYPE::ALL_TYPE_NAME as $statusName) {
            $result[$statusName] = empty($projectStatus[$statusName]) ? 0 : $projectStatus[$statusName];
        }
        return $result;
    }

    public function getPreparedCount()
    {
        $sql = " select preparedType, count(1) as cnt from sl_imsPrepared where preparedStatus = 2 group by preparedType "; //요청,확인
        $projectStatus = SlCommonUtil::arrayAppKeyValue(DBUtil2::runSelect($sql), 'preparedType', 'cnt');
        $result = [];
        foreach (PREPARED_TYPE::ALL_TYPE_NAME as $statusName) {
            if ('work' === $statusName) {
                //&& !in_array(\Session::get('manager.managerId'),ImsCodeMap::AUTH_MANAGER)
                //$sql = " select count(1) as cnt from sl_imsPrepared where preparedStatus IN (0,1,5) and preparedType = 'work' group by preparedType "; //요청,확인
                $sql = " select count(1) as cnt from sl_imsPrepared a join sl_imsProject b on a.projectSno = b.sno where preparedStatus IN (0,1,2) and preparedType = 'work' group by preparedType "; //요청,확인
                $workCnt = DBUtil2::runSelect($sql)[0]['cnt'];
                $result['work'] = empty($workCnt) ? 0 : $workCnt;
            } else {
                $result[$statusName] = empty($projectStatus[$statusName]) ? 0 : $projectStatus[$statusName];
            }
        }
        return $result;
    }

    /**
     * @param $params
     * @param $projectData
     * @return array
     * @throws \Exception
     * @deprecated
     * 생산등록
     */
    public function addProduce($params, $projectData)
    {
        //더이상 자동 등록하지 않음
        /*$this->save(ImsDBName::PRODUCE, [
            'projectSno' => $params['projectSno'],
            'produceCompanySno' => $projectData['produceCompanySno'],
        ]);
        $projectData = $this->getProject(['sno'=>$projectData['sno']]);
        $replaceData = ImsSendMessage::imsMessageReplacer(ImsSendMessage::PRODUCE_REQ,[
            'company'=>$projectData['customer']['customerName'],
            'projectNo'=>$projectData['project']['projectNo'],
            'productName'=>$projectData['project']['styleWithCount'],
        ]);
        $this->sendAlarm($replaceData['title'],$replaceData['msg'],$projectData['project']['produceCompanySno']);*/

        return $params;
    }

    /**
     * 승인/반려 처리
     * @param $params
     * @return array|mixed
     * @throws \Exception
     */
    public function setAccept($params)
    {
        //TODO 승인 before data 확인.
        $projectData = $this->getProject(['sno' => $params['projectSno']]); //Before
        $this->saveStatusHistory([
            'historyDiv' => $params['acceptDiv'],
            'projectSno' => $params['projectSno'],
            'beforeStatus' => ImsCodeMap::PROJECT_CONFIRM_TYPE[$projectData['project'][$params['acceptDiv']]]['name'],
            'afterStatus' => ImsCodeMap::PROJECT_CONFIRM_TYPE[$params['confirmStatus']]['name'],
            'reason' => $params['memo'],
        ]);
        $produceData[$params['prdStep']]['confirmYn'] = $params['confirmStatus'];
        unset($produceData[$params['prdStep']]['confirmYnKr']);

        $updateSaveData = [
            'sno' => $params['projectSno'],
            $params['acceptDiv'] => $params['confirmStatus']
        ];

        //샘플 승인일 경우 완료일 넣기.
        if( 'sampleConfirm' === $params['acceptDiv'] ){
            if( 'p' === $params['confirmStatus'] ){
                $updateSaveData['sampleEndDt'] = 'now()';
            }else{
                $updateSaveData['sampleEndDt'] = '';
            }
        }
        //연계작업 ( 작지 승인시 요청 승인완료 처리, n 준비 , r 요청 , p 승인 , f 반려 )
        /*if( 'workConfirm' === $params['acceptDiv'] ){
            if( 'p' === $params['confirmStatus'] ){
                DBUtil2::update(ImsDBName::PREPARED, [
                    'preparedStatus' => 4
                ], new SearchVo('projectSno=?', $params['projectSno']));
                $updateSaveData['workStatus'] = 4;
            }else if( 'f' === $params['confirmStatus'] ){
                DBUtil2::update(ImsDBName::PREPARED, [
                    'preparedStatus' => 5
                ], new SearchVo('projectSno=?', $params['projectSno']));
                $updateSaveData['workStatus'] = 5;
            }
        }*/
        $this->save(ImsDBName::PROJECT, $updateSaveData);

        return $this->getProject(['sno' => $params['projectSno']]);
    }

    /**
     * 승인처리 (24신규)
     * @param $params
     * @return bool
     * @throws \Exception
     */
    public function setNewAccept($params)
    {
        $targetMap = [
            //프로젝트
            'planConfirm' => ['db' => ImsDBName::PROJECT, 'name' => '기획', 'setCompleteDt'=>'planEndDt'],
            'proposalConfirm' => ['db' => ImsDBName::PROJECT, 'name' => '제안', 'setCompleteDt'=>'proposalEndDt'],
            'sampleConfirm' => ['db' => ImsDBName::PROJECT, 'name' => '샘플', 'setCompleteDt'=>'sampleCompleteDt'],
            //스타일
            'priceConfirm' => ['db' => ImsDBName::PRODUCT, 'name' => '판매가', 'setCompleteDt'=>'priceConfirmDt'],
            //생산
            'assortConfirm' => ['db' => ImsDBName::PRODUCTION, 'name' => '아소트', 'setCompleteDt'=>false],
            'workConfirm' => ['db' => ImsDBName::PRODUCTION, 'name' => '작업지시서', 'setCompleteDt'=>'workEndDt'],
            //생산파일
            'washConfirm' => ['db' => ImsDBName::PRODUCTION, 'name' => '세탁', 'setCompleteDt'=>'washCompleteDt'],
            'fabricConfirmConfirm' => ['db' => ImsDBName::PRODUCTION, 'name' => '원부자재확정', 'setCompleteDt'=>'fabricConfirmCompleteDt'],
            'fabricShipConfirm' => ['db' => ImsDBName::PRODUCTION, 'name' => '원부자재선적', 'setCompleteDt'=>''],
            'qcConfirm' => ['db' => ImsDBName::PRODUCTION, 'name' => 'QC', 'setCompleteDt'=>'qcCompleteDt'],
            'inlineConfirm' => ['db' => ImsDBName::PRODUCTION, 'name' => '인라인', 'setCompleteDt'=>'inlineCompleteDt'],
            'shipConfirm' => ['db' => ImsDBName::PRODUCTION, 'name' => '선적', 'setCompleteDt'=>'shipCompleteDt'],
            'productionComplete' => ['db' => 'NOT_UPDATE', 'name' => '납품완료', '1'=>'1'],
            'fileProductionPacking' => ['db' => 'NOT_UPDATE', 'name' => '패킹', '1'=>'1'],
            'fileProductionInvoice' => ['db' => 'NOT_UPDATE', 'name' => '배송운송장', '1'=>'1'],
        ];

        $sno = $params['condition']['sno'];
        $target = $params['target'];
        $targetDBName = $targetMap[$target]['db'];
        $targetName = $targetMap[$target]['name'];

        if('NOT_UPDATE' === $targetDBName){
            return true;
        }

        $beforeData = DBUtil2::getOne($targetDBName, 'sno', $sno);
        $beforeStatus = ImsCodeMap::PROJECT_CONFIRM_TYPE_SIMPLE[$beforeData[$target]];
        $afterStatus = ImsCodeMap::PROJECT_CONFIRM_TYPE_SIMPLE[$params['acceptValue']];

        //승인 히스토리 저장
        $this->saveStatusHistory([
            'historyDiv' => $target,
            'historyDivName' => $targetName,
            'customerSno' => $params['condition']['customerSno'],
            'projectSno' => $params['condition']['projectSno'],
            'styleSno' => $params['condition']['styleSno'],
            'eachSno' => $params['condition']['eachSno'],
            'beforeStatus' => $beforeStatus,
            'afterStatus' => $afterStatus,
            'reason' => $params['condition']['memo']
        ]);

        $saveData = [
            'sno' => $sno,
            $target => $params['acceptValue'],
        ];

        //승인 혹은 요청일 경우 자동으로 완료일 지정
        $setCompleteDtFiled = $targetMap[$target]['setCompleteDt'];
        if( in_array($params['acceptValue'],['r','p']) && !empty($setCompleteDtFiled) && empty($saveData[$setCompleteDtFiled]) ){
            $saveData[$setCompleteDtFiled] = 'now()';
        }

        //승인 처리.
        $this->save($targetDBName, $saveData);

        //결과 연동
        $this->setSyncStatus($params['condition']['projectSno'], __METHOD__);
    }

    /**
     * 파일 업로드 후 완료일자 업데이트
     * @param $params
     * @return array
     * @throws \Exception
     */
    public function setCompleteDt($params)
    {
        $saveData['sno'] = $params['sno'];
        $saveData[$params['field']] = 'now()';

        //작지 완료 시 요청 상태 변경
        if ('workEndDt' === $params['field']) {
            $preparedData = $this->getPreparedProjectWork($params['sno']);
            $this->save(ImsDBName::PREPARED, [
                'sno' => $preparedData['sno'],
                'preparedStatus' => 2, //작업완료
            ]);
        }

        return $this->save(ImsDBName::PROJECT, $saveData);
    }

    /**
     * 프로젝트 작지 정보
     * @param $projectSno
     * @return mixed
     */
    public function getPreparedProjectWork($projectSno)
    {
        return DBUtil2::getOne(ImsDBName::PREPARED, "preparedType = 'work' AND  projectSno", $projectSno);
    }

    /**
     * 파일 정보 가져오기
     * @param $params
     * @return array
     */
    public function loadFile($params)
    {
        return $this->getLatestProjectFiles(['sno' => $params['sno']]);
    }

    /**
     * 가견적 설정 (승인설정)
     * @param $params
     * @throws \Exception
     */
    public function setEstimate($params)
    {
        $preparedWithProject = $this->getPrepared($params);

        $updatePrdList = SlCommonUtil::arrayAppKey($preparedWithProject['prepared']['contents']['productList'], 'sno');

        if (SlCommonUtil::isDevIp()) {
            //gd_debug($updatePrdList);
        }

        //1. 국가/타입 업데이트
        $prjUpdate['sno'] = $preparedWithProject['project']['sno'];
        $prjUpdate['projectSno'] = $preparedWithProject['project']['sno'];
        $prjUpdate['produceNational'] = $preparedWithProject['prepared']['contents']['produceNational'];
        $prjUpdate['produceType'] = $preparedWithProject['prepared']['contents']['produceType'];
        $prjUpdate['produceCompanySno'] = $preparedWithProject['prepared']['produceCompanySno']; //생산처

        $prjUpdate['produceDeliveryDt'] = $preparedWithProject['prepared']['contents']['produceDeliveryDt'];
        $this->save(ImsDBName::PROJECT, $prjUpdate);

        //2. PrdList. 원본을 업데이트.
        foreach ($preparedWithProject['productList'] as $prdList) {

            if (!empty($updatePrdList[$prdList['sno']])) {

                $saveReqData = SlCommonUtil::getAvailData($updatePrdList[$prdList['sno']], [
                    'prdCost', 'fabricCost', 'subFabricCost', 'laborCost', 'marginCost', 'dutyCost', 'managementCost', 'prdMoq', 'priceMoq', 'addPrice', 'produceType'
                ]);
                $saveReqData['sno'] = $prdList['sno'];

                //MERGE.... 생산처 그대로 덮으면 BT가 사라지는 버그 있음.

                /*'memo'=>'',
                'btConfirm'=>'',
                'btConfirmDt'=>'',
                'btMemo'=>'',*/

                $updatePrdList[$prdList['sno']]['fabric'];

                //SitelabLogger::logger('저장 체크....');
                //SitelabLogger::logger($saveReqData['fabric']);

                $saveReqData['fabric'] = $updatePrdList[$prdList['sno']]['fabric'];
                $saveReqData['subFabric'] = $updatePrdList[$prdList['sno']]['subFabric'];

                //원단 번호와 자재명이 동일할 때 BT정보엎기.
                //$saveReqData['fabric']

                //기본적으로 덮는다. (대신 BT는 살린다?)
                //원본
                DBUtil2::runSql("insert into sl_imsProjectProductTmp select * from sl_imsProjectProduct where sno = {$prdList['sno']}");

                //이거 연결되면 BT정보가 날아감.....

                /*if( 4 == $params['status'] ){
                    if( 'estimate' == $params['reqType'] ) $saveReqData['prdCostStatus'] = 1;
                    if( 'cost' == $params['reqType'] ) $saveReqData['prdCostStatus'] = 2;
                }*/

                $this->saveProduct($saveReqData);
            }

        }
    }

    /**
     * 프로젝트 복사 및 스타일 이동
     * @param $params
     * @return mixed
     * @throws \Exception
     */
    public function copyProject($params)
    {
        $orgData = DBUtil2::getOne(ImsDBName::PROJECT, 'sno', $params['projectSno'], false);
        $projectData = SlCommonUtil::getAvailData($orgData, [
            'customerSno',
            'produceCompanySno',
            'produceType',
            'salesManagerSno',
            'designManagerSno',
            'projectYear',
            'projectSeason',
            'use3pl',
            'useMall',
            'deliveryCostMemo',
            'bizPlanYn',
            'projectType',
            'projectStatus',
            'produceCompanySno',
            'produceType',
            'customerOrderDt',
            'customerOrderDeadLine',
            'customerOrderDeadLineText',
            'msOrderDt',
            'msDeliveryDt',
            'customerDeliveryDt',
            'customerDeliveryDtConfirmed',
            'customerDeliveryDtStatus',
            'customerDeliveryDtStatus2',
            'bidType',
            'bidType2',
            'produceDeliveryDt',
            'produceNational',
            'confirmed',
            'bid',
            'recommend',
            'addedInfo',
            'packingYn',
            'directDeliveryYn',
            'deliveryMethod',
            'deliveryCostMemo',
            'syncProduct',
            'use3pl',
            'useMall',
            'bizPlanYn',
            'bizPlanYear',
        ]);
        $projectData['projectMemo'] .= ' ' . $orgData['projectNo'] . '로 부터 복사함 ' . \Session::get('manager.managerNm') . ' ' . date('y/m/d');
        $projectData['srcProjectSno'] = $params['projectSno'];

        $newProjectSno = DBUtil2::insert(ImsDBName::PROJECT, $projectData);

        $this->copyProjectExt($params['projectSno'], $newProjectSno);

        foreach ($params['prdSnoList'] as $prdSno) {
            if ('y' === $params['prdCopy']) {
                $prd = DBUtil2::getOne(ImsDBName::PRODUCT, 'sno', $prdSno, false);
                $prd['projectSno'] = $newProjectSno;
                unset($prd['sno']);
                unset($prd['regDt']);
                unset($prd['modDt']);
                DBUtil2::insert(ImsDBName::PRODUCT, $prd);
            } else {
                $this->movePrd($prdSno, $newProjectSno, 'n');
            }
        }

        //$this->setProjectReOrder($newProjectSno);
        $this->setRefinePreparedStatus($newProjectSno);
        $this->setSyncStatus($newProjectSno, __METHOD__);

        return $newProjectSno;
    }

    /**
     * 상품 옮기기.
     * @param $prdSno
     * @param $moveProjectSno
     * @param string $priceConfirm
     * @throws \Exception
     */
    public function movePrd($prdSno, $moveProjectSno, $priceConfirm=null){
        $updateTableList = [
            ImsDBName::SAMPLE,
            ImsDBName::FABRIC,
            ImsDBName::FABRIC_REQ,
            'sl_imsFabricReqHistory',
            ImsDBName::PROJECT_FILE,
            ImsDBName::ESTIMATE,
            ImsDBName::PRODUCTION,
        ];
        foreach($updateTableList as $updateTable){
            DBUtil2::update($updateTable, ['projectSno'=>$moveProjectSno], new SearchVo('styleSno=?',$prdSno));
        }

        $updateData = [];
        $updateData['projectSno'] = $moveProjectSno;
        if(!empty($priceConfirm)){
            $updateData['priceConfirm'] = $priceConfirm;
        }

        DBUtil2::update(ImsDBName::PRODUCT, $updateData, new SearchVo('sno=?',$prdSno));

    }

    /**
     * 프로젝트 일정 복사
     * @param $srcProjectSno
     * @param $newProjectSno
     */
    public function copyProjectExt($srcProjectSno, $newProjectSno){
        $extTableInfoList = DBIms::tableImsProjectExt();
        $exclude = [
            'sno','projectSno','regDt','modDt',
        ];
        $copyFieldList = [];
        foreach($extTableInfoList as $exTableInfo){
            if(!in_array($exTableInfo['val'],$exclude)){
                $copyFieldList[] = $exTableInfo['val'];
            }
        }
        $copyFieldList[] = 'projectSno';
        $copyFieldStr = implode(',', $copyFieldList);

        unset($copyFieldList[count($copyFieldList)-1]);
        $valueFieldStr = implode(',', $copyFieldList).' , '.$newProjectSno;
        DBUtil2::runSql("insert into sl_imsProjectExt ({$copyFieldStr}) select {$valueFieldStr} from sl_imsProjectExt where projectSno={$srcProjectSno}");
    }

    /**
     * 상품 삭제 (영구)
     * @param $params
     * @throws \Exception
     */
    public function deleteProduct($params)
    {
        foreach ($params['prdSnoList'] as $prdSno) {
            $this->delete(ImsDBName::PRODUCT, $prdSno);
        }
    }

    /**
     * 상품 휴지통
     * @param $params
     * @throws \Exception
     */
    public function goTrashProduct($params)
    {
        foreach ($params['prdSnoList'] as $prdSno) {
            $this->save(ImsDBName::PRODUCT, ['sno' => $prdSno, 'delFl' => 'y']);
        }
    }
    
    /**
     * 일괄 견적
     * @param $params
     * @throws \Exception
     */
    public function goBatchEstimate($params)
    {
        $date = new \DateTime();
        //SitelabLogger::logger2(__METHOD__, $params);
        foreach ($params['prdSnoList'] as $prdData) {
            //$this->save(ImsDBName::PRODUCT, ['sno' => $prdSno, 'delFl' => 'y']);
            $this->saveEstimateReq([
                'projectSno'=>$params['projectSno'],
                'customerSno'=>$params['customerSno'],
                'styleSno'=>$prdData['sno'],
                'estimateType'=>$params['estimateType'],
                'reqStatus'=>'1',
                'estimateCount'=>$prdData['cnt'],
                'reqFactory'=>$params['reqFactory'],
                'fabric'=>ImsJsonSchema::FABRIC_INFO,
                'subFabric'=>ImsJsonSchema::SUB_FABRIC_INFO,
                'completeDeadLineDt'=> $date->modify('+1 day')
            ]);
        }
    }

    /**
     * 상품 복원
     * @param $params
     * @throws \Exception
     */
    public function recoveryProduct($params)
    {
        foreach ($params['prdSnoList'] as $prdSno) {
            $this->save(ImsDBName::PRODUCT, ['sno' => $prdSno, 'delFl' => 'n']);
        }
    }

    /**
     * 상품 복사
     * @param $params
     * @throws \Exception
     */
    public function copyProduct($params)
    {
        foreach ($params['prdSnoList'] as $prdSno) {
            $copyData = DBUtil2::getOne(ImsDBName::PRODUCT, 'sno', $prdSno, false);
            $saveData = SlCommonUtil::getAvailData($copyData, [
                'projectSno',
                'customerSno',
                'styleCode',
                'addStyleCode',
                'productName',
                'prdYear',
                'prdSeason',
                'prdGender',
                'prdStyle',
                'prdColor',
                'produceType',
                'produceCompanySno',
                'produceNational',
                'currentPrice',
                'targetPrice',
                'targetPrdCost',
                'salePrice',
                'fileThumbnail',
                'fileThumbnailReal',
            ]);
            $saveData['productName'] = $copyData['productName'].' (복사)';
            $this->save(ImsDBName::PRODUCT, $saveData);
            $this->setSyncStatus($copyData['projectSno'], __METHOD__);
        }
    }

    /**
     * 프로젝트 아이콘 설정
     * @param $each
     * @param $projectSno
     */
    public function setProjectIcon(&$each, $projectSno)
    {
        $useInfo = [];

        if ('y' === $each['packingYn']) {
            $files = DBUtil2::getList(ImsDBName::PROJECT_FILE, 'fileDiv = \'filePacking\' and projectSno', $projectSno);
            if (!empty($files)) {
                $useInfo[] = '<i class="fa fa-gift fa-lg" aria-hidden="true" ></i> 분류';
                $each['packingFile'] = 'y';
            } else {
                $useInfo[] = '<i class="fa fa-gift fa-lg" aria-hidden="true" style="color:red !important; font-size:20px" ></i> <span style="color:red !important;">분류</span>';
                $each['packingFile'] = 'n';
            }
        }
        if ('y' === $each['use3pl']) {
            $files = DBUtil2::getList(ImsDBName::PROJECT_FILE, 'fileDiv = \'fileBarcode\' and projectSno', $projectSno);
            if (!empty($files)) {
                $useInfo[] = '<i class="fa fa-university fa-lg" aria-hidden="true"></i> 3PL';
            } else {
                $useInfo[] = '<i class="fa fa-university fa-lg text-danger" aria-hidden="true"></i> <span class="text-danger">3PL</span>';
            }
        }
        if ('y' === $each['useMall']) {
            if (empty($each['privateMallDeliveryDt']) || '0000-00-00' == $each['privateMallDeliveryDt']) {
                $useInfo[] = '<i class="fa fa-internet-explorer fa-lg text-danger" aria-hidden="true"></i> <span class="text-danger">폐쇄몰</span>';
            } else {
                $useInfo[] = '<i class="fa fa-internet-explorer fa-lg" aria-hidden="true"></i> 폐쇄몰';
            }
        }

        //$useInfo[] = '<i class="fa fa-ship fa-lg" aria-hidden="true"></i> 선적';

        //if('y' === $each['useMall']) $useInfo[] = '폐쇄몰';
        $useInfoStr = '';
        if (!empty($useInfo)) {
            /*if( empty($each['eachSno']) ){
                $useInfoStr = implode(' &nbsp;&nbsp; ', $useInfo);
            }else{
                $useInfoStr = implode('<p></p>', $useInfo);
            }*/
            $useInfoStr = implode(' &nbsp;&nbsp; ', $useInfo);
        }

        $each['useInfo'] = "{$useInfoStr}";

        $season = ImsCodeMap::IMS_SEASON_ICON[$each['projectSeason']];
        $each['seasonIcon'] = empty($season) ? ImsCodeMap::IMS_SEASON_ICON[''] : $season;  /*'<i class="fa fa-2x fa-snowflake-o" aria-hidden="true"></i>'*/;

        $each['fabricStatusKr'] = ImsCodeMap::IMS_FABRIC_STATUS[$each['fabricStatus']]['name'];
        $each['fabricStatusColor'] = ImsCodeMap::IMS_FABRIC_STATUS[$each['fabricStatus']]['color'];

    }


    /**
     * 기본 파일 셋팅
     * @param $checkFileList
     * @param $each
     * @return mixed
     */
    public function setDefaultFile($checkFileList, $each)
    {
        $fileSearchVo = [
            'customerSno' => $each['customerSno'],
            'projectSno' => $each['projectSno'],
            'styleSno' => $each['styleSno'],
            'eachSno' => $each['sno'],
            'fileDiv' => $each['fileDiv'],
        ];
        //SitelabLogger::logger2(__METHOD__, '검색하기...');
        //SitelabLogger::logger2(__METHOD__, $fileSearchVo);
        $each['fileList'] = $this->getLatestProjectFiles($fileSearchVo);
        //SitelabLogger::logger2(__METHOD__, $checkFileList);
        //SitelabLogger::logger2(__METHOD__, $each);

        foreach ($checkFileList as $checkSampleFile) {
            if(empty($each['fileList'][$checkSampleFile])){
                $each['fileList'][$checkSampleFile] = [
                    'title' => '등록된 파일이 없습니다.',
                    'memo' => '',
                    'files' => [],
                    'noRev' => $value['noRev']
                ];
            }
        }

        //SitelabLogger::logger('기본 파일 리스트 확인.');
        //SitelabLogger::logger($each);

        return $each;
    }


    /**
     * 요청 리스트 (전체 가져오기)
     * @param $params
     * @return array
     */
    public function getListRequest($params)
    {
        /*$qbList = $this->getQbList($params);
        $params['isComplete'] = true;
        $rslt = [
            'produce' => [],
            'qbList' => $qbList,
            'fabricList' => [],
            'produceComplete' => [],
            'qbListComplete' => $qbListComplete,
        ];
        return $rslt;*/
    }

    /**
     * 프로젝트 진행 상태 전체 체크
     * @param $projectSno
     * @param $reqClass
     * @return false
     * @throws \Exception
     */
    public function setSyncStatus($projectSno, $reqClass)
    {
        ImsUtil::setSyncStatus($projectSno, $reqClass);
    }


    /**
     * 신규 미팅 데이터
     * @param $params
     * @return array|mixed
     */
    public function getNewMeeting($params)
    {
        if (empty($params['sno'])) {
            $data = DBTableField::getTableBlankData('tableImsNewMeeting');
            $data['meetingStatus'] = 0;
        } else {
            $data = $this->getListNewMeeting([
                'condition' => ['sno' => $params['sno']]
            ])['list'][0];
        }

        //SitelabLogger::logger2(__METHOD__, '저장 데이터 체크');
        //SitelabLogger::logger2(__METHOD__, $data);
        
        if (empty($data['checkList'])) {
            $data['checkList'] = [
                ImsJsonSchema::MEETING_CHECKLIST
            ];
        }
        if (empty($data['style'])) {
            $data['style'] = [
                ImsJsonSchema::MEETING_STYLELIST
            ];
        }

        return $data;
    }


    /**
     * 신규 미팅 등록
     * @param $saveData
     * @return mixed
     * @throws \Exception
     */
    public function saveNewMeeting($saveData)
    {
        $saveData = DBTableField::checkAndRefineSaveData(ImsDBName::NEW_MEETING, $saveData);
        return $this->save(ImsDBName::NEW_MEETING, $saveData);
    }

    /**
     * 신규 미팅 리스트
     * @param $params
     * @return mixed
     */
    public function getListNewMeeting($params)
    {
        //(요청, 처리중 , 처리불가 , 반려 ===> 진행중 ) 와 완료 ( limit 50 )
        $searchVo = new SearchVo();
        $this->setCommonCondition($params['condition'], $searchVo); //Request쪽에 있음.
        $this->sql->setMeetingListCondition($params['condition'], $searchVo);
        $this->setListSort($params['condition']['sort'], $searchVo);

        //$list = DBUtil2::getComplexList($this->sql->getMeetingTable(), $searchVo);
        $searchData = [
            'page' => gd_isset($params['condition']['page'], 1),
            'pageNum' => gd_isset($params['condition']['pageNum'], 100),
        ];
        $allData = DBUtil2::getComplexListWithPaging($this->sql->getMeetingTable(), $searchVo, $searchData, false, false);
        //SitelabLogger::logger2(__METHOD__, $allData); //미팅리스트확인
        //$list = SlCommonUtil::setEachData($list, $this, 'decorationNewMeeting');

        return [
            'pageEx' => $allData['pageData']->getPage('#'),
            'page' => $allData['pageData'],
            'list' => SlCommonUtil::setEachData($allData['listData'], $this, 'decorationNewMeeting')
        ];
    }

    public function decorationNewMeeting($each)
    {
        $each['checkList'] = stripslashes($each['checkList']);
        $each['style'] = stripslashes($each['style']);
        $each = DBTableField::parseJsonField(ImsDBName::NEW_MEETING, $each);

        $each = DBTableField::fieldStrip(ImsDBName::NEW_MEETING, $each);
        $each['meetingStatusKr'] = ImsCodeMap::IMS_MEETING_STATUS[$each['meetingStatus']];
        //$each['meetingStatusKr'] = ImsCodeMap::IMS_MEETING_STATUS[$each['meetingStatus']];

        return $each;
    }

    /**
     * 프로젝트 리스트.
     * @param $params
     * @return array
     */
    public function getListProject($params)
    {
        //(요청, 처리중 , 처리불가 , 반려 ===> 진행중 ) 와 완료 ( limit 50 )
        $searchVo = new SearchVo();
        //$this->setProjectCondition($params['condition'], $searchVo); //Request쪽에 있음.
        $this->setCommonCondition($params['condition'], $searchVo); //Request쪽에 있음.
        $this->setListSort($params['condition']['sort'], $searchVo);

        $searchData = [
            'page' => gd_isset($params['condition']['page'], 1),
            'pageNum' => gd_isset($params['condition']['pageNum'], 200),
        ];

        $allData = DBUtil2::getComplexListWithPaging($this->sql->getProjectNewTable(), $searchVo, $searchData, false, false);

        return [
            'pageEx' => $allData['pageData']->getPage('#'),
            'page' => $allData['pageData'],
            'list' => SlCommonUtil::setEachData($allData['listData'], $this, 'decorationProject')
        ];
    }

    /**
     * 프로젝트 리스트 추가 데이터
     * @param $each
     * @return array
     * @throws \Exception
     */
    public function decorationProject($each)
    {
        $each = DBTableField::parseJsonField(ImsDBName::PROJECT, $each);
        $searchVo = new SearchVo("delFl='n' and projectSno=?", $each['sno']);
        $searchVo->setOrder('sort, regDt desc');
        $productList = DBUtil2::getListBySearchVo(ImsDBName::PRODUCT, $searchVo);

        $each['prdInfo'] = $productList; //상품 리스트
        $each['prdExQty'] = 0;
        $styleName = $productList[0]['productName'];
        if (count($productList) > 1) {
            $styleName .= ' 외 ' . (count($productList) - 1) . '건';
        }
        foreach($productList as $prd){
            $each['prdExQty'] += $prd['prdExQty'];
        }

        $each['styleName'] = $styleName;
        $each['saleAmount'] = 0;

        //스타일별 값 취합 ( 규모, 판매가 , 생산가 등)
        foreach ($productList as $prdEach) {
            //판매가가 입력되었을 경우
            if( $prdEach['salePrice'] > 0 ){
                $each['customerSize'] += ($prdEach['salePrice'] * $prdEach['prdExQty']); //판매가 매출규모
            }else{
                $each['customerSize'] += ($prdEach['targetPrice'] * $prdEach['prdExQty']); //가 매출규모
            }
        }
        $each['customerSize'] = SlCommonUtil::numberToKorean($each['customerSize']);

        $each['addedInfo'] = json_decode($each['addedInfo'], true);
        $each['projectTypeKr'] = ImsCodeMap::PROJECT_TYPE[$each['projectType']];
        $each['projectStatusKr'] = ImsCodeMap::PROJECT_STATUS[$each['projectStatus']];

        //긴급도
        $each['urgency'] = '';
        $sDate = date('Y-m-d'); //Today
        $eDate = gd_date_format('Y-m-d',$each['customerDeliveryDt']);
        
        //FIXME : 고치기
        $dateDiff = SlCommonUtil::getDateDiff($sDate, $eDate);
        if( 90 > $each['projectStatus'] && 100 > $dateDiff ){
            $each['urgency'] = '긴급';
        }else if( 90 > $each['projectStatus'] &&  120 >= $dateDiff ) {
            $each['urgency'] = '보통';
        }else if( 90 > $each['projectStatus'] &&  $dateDiff > 120 ){
            $each['urgency'] = '여유';
        }

        $each = array_merge($each, $this->getProjectAddInfo($each['sno']));

        return $each;
    }

    /**
     * 상품 정보 등록
     * @param $params
     * @return mixed
     * @throws \Exception
     */
    public function saveInline($params){
        $saveList = [];
        foreach( $params['saveData'] as $each ){
            $prdData = DBUtil2::getOne(ImsDBName::PRODUCT, 'sno', $each['sno']);
            //if($prdData['inlineStatus'] != $each['inlineStatus'] ){
                $saveList[] = $this->save(ImsDBName::PRODUCT, [
                    'sno' => $each['sno'],
                    'inlineStatus' => $each['inlineStatus'],
                    'inlineMemo' => $each['inlineMemo'],
                    'sort' => $each['sort'],
                ]);
            //}
        }
        return $this->save(ImsDBName::PRODUCT, $saveList);
    }

    /**
     * 신규 미팅 삭제
     * @param $params
     * @throws \Exception
     */
    public function deleteNewMeeting($params){
        $this->delete(ImsDBName::NEW_MEETING, $params['sno']); //TODO : 프로젝트 등록된 고객이라면 삭제 불가 처리해야함.
    }

    /**
     * 견적 생산가 지우기.
     * @param $params
     * @throws \Exception
     */
    public function deleteEstimate($params){
        $this->delete(ImsDBName::ESTIMATE, $params['sno']);
    }

    /**
     * 프로젝트 저장 (필요한 정보만)
     * @param $saveProject
     * @return mixed
     * @throws \Exception
     */
    public function saveSimpleProject($saveProject){
        //스케쥴 저장
        $this->saveAddInfo($saveProject);
        return $saveProject['sno'];
    }

    /**
     * 프로젝트 확장 정보 저장
     * @param $saveData
     * @return mixed
     * @throws \Exception
     */
    public function saveProjectExt($saveData){
        $projectSno = $saveData['projectSno'];
        unset($saveData['projectSno']);

        //스케쥴 저장
        $updateRslt = DBUtil2::update(ImsDBName::PROJECT_EXT, $saveData, new SearchVo('projectSno=?',$projectSno));
        //상태 최신화
        //$this->setProjectExtStatus($projectSno);
        ImsScheduleUtil::setProjectScheduleStatus($projectSno);

        return $updateRslt;
    }


    /**
     * 데이터 저장
     * @param $params
     * @return mixed
     * @throws \Exception
     */
    public function saveData($params){
        if( empty($params['target']) ) throw new \Exception('저장 테이블 없음');
        $saveData = $params['saveData'];
        $tableName = 'sl_'.$params['target'];
        DBTableField::checkRequired($tableName, $saveData);
        //FileData가 있을 경우. ? JSON 일 경우 ?
        $saveData = DBTableField::parseJsonField($tableName, $saveData, 'encode');
        //SitelabLogger::logger2(__METHOD__, 'Save Data 확인...');
        //SitelabLogger::logger2(__METHOD__, $saveData);
        return $this->save($tableName, $saveData);
    }

    /**
     * 협상 단계 메모
     * @param $params
     * @return mixed
     */
    public function getNegoData($params){
        $searchVo = new SearchVo('customerSno=?', $params['customerSno']);
        $searchVo->setWhere('issueType=?');
        $searchVo->setWhereValue('meeting');
        $searchVo->setOrder('regDt desc');

        $data = DBUtil2::getOneBySearchVo(ImsDBName::CUSTOMER_ISSUE, $searchVo );
        return html_entity_decode(strip_tags($data['contents']));
    }

    /**
     * 고객 견적 상세
     * @param $params
     * @return mixed
     */
    public function getCustomerEstimate($params){
        $imsCustomerEstimateService = SlLoader::cLoad('ims', 'imsCustomerEstimateService');
        return $imsCustomerEstimateService->getCustomerEstimate($params);
    }

    /**
     * 고객 견적 리스트
     * @param $params
     * @return mixed
     */
    public function getListCustomerEstimate($params){
        $imsCustomerEstimateService = SlLoader::cLoad('ims', 'imsCustomerEstimateService');
        return $imsCustomerEstimateService->getListCustomerEstimate($params);
    }

    /**
     * TEST
     * @param $params
     * @return array
     */
    public function getTest($params){
        return ['test' => '테스트데이터', $params];
    }


    /**
     * 스케쥴 지우기
     * @param $params
     * @throws \Exception
     */
    public function deleteSchedule($params){
        $this->delete(ImsDBName::CALENDAR, $params['sno']); //프로젝트 등록된 고객이라면 삭제 불가.
    }

    /**
     * 스케쥴 전달
     * @param $params
     * @return mixed
     */
    public function getImsSchedule($params){
        //SitelabLogger::logger2(__METHOD__, $params);
        //$data = DBUtil2::getOne(ImsDBName::CALENDAR, 'sno', $params['sno']);
        $tableList= [
            'a' =>['data' => [ ImsDBName::CALENDAR ], 'field' => ['*']],
            'b' =>['data' => [ DB_MANAGER, 'JOIN', 'a.regManagerSno = b.sno' ], 'field' => ['managerNm as regManagerNm']]
        ];
        $table = DBUtil2::setTableInfo($tableList);
        $searchVo = new SearchVo('a.sno=?', $params['sno']);
        $data = DBUtil2::getComplexList($table,$searchVo, false, false, true)[0];

        return SlCommonUtil::setDateBlank($data);
    }

    /**
     * @param $params
     * @return array
     */
    public function getImsScheduleAll($params){
        //연휴
        $holidayList = DBUtil2::getList('sl_holiday', 'isHoliday', 'Y');
        $scheduleList = [];
        foreach($holidayList as $holiday){
            $scheduleList[] = [
                'title' => $holiday['dateName'],
                'start' => $holiday['locdate'],
                'classNames' => ['holiday-event'],
            ];
        }

        $calendarEventList = DBUtil2::runSelect("select a.*, b.managerNm from sl_imsCalendar a join es_manager b on a.regManagerSno = b.sno");

        foreach($calendarEventList as $calendarEvent){
            $dpData = [
                'sno' => $calendarEvent['sno'],
                'start' => $calendarEvent['start'],
                'description' => $calendarEvent['contents'],
            ];
            if( !empty($calendarEvent['end']) && '0000-00-00' !== $calendarEvent['end'] ){
                $dpData['end'] = SlCommonUtil::getDateCalc($calendarEvent['end'],1);
            }

            if(1 == $calendarEvent['type']){ //중요 (빨강)
                $prefix='(중요)';
                $dpData['backgroundColor'] = '#bf3f33';
                $dpData['borderColor'] = '#D9534F';
            }else if(2 == $calendarEvent['type']){ //연차 (초록
                $prefix='(연차)';
                $dpData['backgroundColor'] = '#49664c';
                $dpData['borderColor'] = '#6B8E23';
            }else if(3 == $calendarEvent['type']){ //미팅 (파랑)
                $prefix='(미팅)';
                $dpData['backgroundColor'] = '#4A90E2';
                $dpData['borderColor'] = '#357ABD';
            }else if(5 == $calendarEvent['type']){ //출장 (파랑)
                $prefix='(출장)';
                $dpData['backgroundColor'] = '#4A90E2';
                $dpData['borderColor'] = '#357ABD';
            }else if(6 == $calendarEvent['type']){ //납품 (파랑)
                $prefix='(납품)';
                $dpData['backgroundColor'] = '#bf3f33';
                $dpData['borderColor'] = '#D9534F';
            }else{ //기타
                $prefix='(기타)';
                $dpData['backgroundColor'] = '#a9a9a9';
                $dpData['borderColor'] = '#b3b3b3';
            }
            //$dpData['title'] = "{$prefix} {$calendarEvent['title']} ({$calendarEvent['managerNm']})";
            $dpData['title'] = "{$prefix} {$calendarEvent['title']}";

            $scheduleList[] = $dpData;
        }

        return $scheduleList;
    }

    /**
     * 기획 불가 처리
     * @param $projectSno
     * @param $reason
     * @throws \Exception
     */
    public function setPlanNotPossible($projectSno, $reason){
        $project = DBUtil2::getOne(ImsDBName::PROJECT, 'sno', $projectSno);
        $customer = DBUtil2::getOne(ImsDBName::CUSTOMER, 'sno', $project['customerSno']);

        DBUtil2::update(ImsDBName::PROJECT_EXT, [
            'salesStatus' => 'imp'
        ], new SearchVo('projectSno=?',$projectSno));
        DBUtil2::update(ImsDBName::PROJECT, [
            'projectStatus' => '10'
        ], new SearchVo('sno=?',$projectSno));

        $subject = $customer['customerName'] . ' 기획 불가';
        $contents = '('.$projectSno.') ' .$customer['customerName'] . ' 고객사 프로젝트 기획불가<br><br>불가 사유<br>';
        $contents .= $reason;

        $hopeDt = SlCommonUtil::getDateCalc(date('Y-m-d'), 3);
        $this->addTodoData($subject, $contents, $hopeDt, $projectSno, ['02001001']);
    }


    /**
     * 프로젝트별 메일 발송 이력 반환
     * @param $params
     * @return mixed
     */
    public function getListSendHistory($params){
        $searchVo = new SearchVo('projectSno=?', $params['condition']['projectSno']);
        $searchVo->setOrder('regDt desc');
        if(!empty($params['condition']['sendType'])){
            $searchVo->setWhere('sendType=?');
            $searchVo->setWhereValue($params['condition']['sendType']);
        }
        return DBUtil2::getListBySearchVo(ImsDBName::SEND_HISTORY, $searchVo);
    }


}

