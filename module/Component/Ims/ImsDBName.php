<?php
namespace Component\Ims;

/**
 * IMS DB정의
 * Class GoodsStock
 * @package Component\Goods
 */
class ImsDBName {

    const CODE = 'sl_imsCode';
    const MEETING = 'sl_imsMeeting';
    const CUSTOMER = 'sl_imsCustomer';
    const PROJECT = 'sl_imsProject';
    const PROJECT_EXT = 'sl_imsProjectExt';
    const PROJECT_MANAGER = 'sl_imsProjectManager';
    const PROJECT_FILE = 'sl_imsFile';
    const PRODUCT = 'sl_imsProjectProduct';
    const PREPARED = 'sl_imsPrepared';
    const PRODUCE = 'sl_imsProduce'; //생산
    const PRODUCTION = 'sl_imsProduction'; //생산 (신규)
    const PRODUCTION_COMMENT = 'sl_imsProductionComment'; //생산코멘트
    const SAMPLE_FACTORY = 'sl_imsSampleFactory'; //생산
    const STATUS_HISTORY = 'sl_imsStatusHistory';
    const UPDATE_HISTORY = 'sl_imsUpdateHistory';
    const UPDATE_COMMENT = 'sl_imsUpdateComment';
    const PROJECT_COMMENT = 'sl_imsComment';
    const SAMPLE = 'sl_imsSample';
    const FABRIC = 'sl_imsFabric';
    const FABRIC_REQ = 'sl_imsFabricRequest';
    const ESTIMATE = 'sl_imsEstimate';
    const NEW_MEETING = 'sl_imsNewMeeting';
    const TODO_REQUEST = 'sl_imsTodoRequest';
    const TODO_RESPONSE = 'sl_imsTodoResponse';
    const TODO_COMMENT = 'sl_imsTodoComment';
    const APPROVAL_LINE = 'sl_imsApprovalLine';
    const PROJECT_ADD_INFO = 'sl_imsAddInfo'; //DEFRECATED
    const CUSTOMER_ISSUE = 'sl_imsCustomerIssue';
    const CUSTOMER_ESTIMATE = 'sl_imsCustomerEstimate';
    const CUSTOMER_CONTACT = 'sl_imsCustomerContact';

    const EWORK = 'sl_imsEwork';
    const EWORK_HISTORY = 'sl_imsEworkHistory';
    const STYLE_SPEC = 'sl_imsStyleSpec';
    const CALENDAR = 'sl_imsCalendar';

    const MATERIAL = 'sl_imsMaterial';
    const MATERIAL_TYPE_DETAIL = 'sl_imsMaterialTypeDetail';
    const MATERIAL_UPDATE_LOG = 'sl_imsMaterialUpdateLog';
    const MATERIAL_GROUP = 'sl_imsMaterialGroup';

    //프로젝트별 부가판매/매입
    const ADDED_B_S = 'sl_imsAddedBuySale';

    const CATEGORY = 'sl_imsCategory';

    const PRD_MATERIAL = 'sl_imsPrdMaterial';

    const SEND_HISTORY = 'sl_imsSendHistory';
    //const CATEGORY = 'sl_imsCategory';

    const STORED_FABRIC = 'sl_imsStoredFabric'; //원부자재
    const STORED_FABRIC_INPUT = 'sl_imsStoredFabricInput'; //원부자재 입고
    const STORED_FABRIC_OUT = 'sl_imsStoredFabricOut'; //원부자재 출고

    const PRODUCT_PRD_COST = 'sl_imsProjectProductPrdCost'; //원부자재 출고

    const PROJECT_PLAN_SCHE = 'sl_imsProjectPlanSchedule'; //프로젝트 최초기획스케쥴
    const PRODUCT_PLAN = 'sl_imsProjectProductPlan'; //스타일기획

    const PROJECT_ISSUE = 'sl_imsProjectIssue'; //프로젝트/스타일 이슈리스트
    const PROJECT_ISSUE_ACTION = 'sl_imsProjectIssueAction'; //프로젝트/스타일 이슈 조치리스트
    const UPDATE_HISTORY_NK = 'sl_imsUpdateHistoryNk'; //게시물 업데이트 이력(1 record = 1 field update)


    const BASIC_SIZE_SPEC = 'sl_imsBasicSizeSpec'; //사이즈스펙양식

    const CUSTOMER_FIT_SPEC_OPTION = 'sl_imsCustomerFitSpecOption'; //고객 제공 샘플 측정항목

    const BASIC_ETC_COST = 'sl_imsBasicSampleEtcCost'; //기초정보-공임비용/기타비용 항목리스트(스타일기획, 샘플 등록/수정시 사용)

    const BASIC_FITTING_CHECK = 'sl_imsBasicFittingCheck'; //기초정보-피팅체크양식
    const BASIC_PROPOSAL_GUIDE = 'sl_imsBasicProposalGuide'; //기초정보-제안서가이드양식
    const BASIC_SALES_PLAN = 'sl_imsBasicSalesPlan'; //기초정보-영업기획서양식

    const ETC_CAR = 'sl_imsEtcCar';
    const ETC_CAR_MAINTAIN = 'sl_imsEtcCarMaintain';
    const ETC_CAR_ADDR = 'sl_imsEtcCarAddr';
    const ETC_CAR_DRIVE = 'sl_imsEtcCarDrive';
    //업종
    const BUSI_CATE = 'sl_imsBasicBusiCate';
    //영업고객(==고객발굴)
    const SALES_CUSTOMER = 'sl_salesCustomerInfo';
    const SALES_CUSTOMER_CONTENTS = 'sl_salesCustomerContents';
    const SALES_CUSTOMER_STATS = 'sl_salesCustomerStats';

    const PROJECT_SALES_PLAN_FILL = 'sl_imsProjectSalesPlanFill'; //프로젝트상세 -> 영업기획서 작성
    const PROJECT_SALES_PLAN_FILL_DETAIL = 'sl_imsProjectSalesPlanFillDetail'; //프로젝트상세 -> 영업기획서 작성 - 필드(cell)별
    const PROJECT_SALES_PLAN_FILL_JSON = 'sl_imsProjectSalesPlanFillJson'; //프로젝트상세 -> 영업기획서 작성 - json문항

    //시험성적서 작성
    const TEST_REPORT_FILL = 'sl_imsTestReportFill';

    //스타일 QC/인라인 검수
    const PRODUCT_INSPECT = 'sl_imsProjectProductInspect';

    //분류패킹 발주건(분류패킹master)
    const CUSTOMER_PACKING = 'sl_imsCustomerPacking';

    //분류패킹 고객담당자
    const CUSTOMER_RECEIVER = 'sl_imsCustomerReceiver';
    //분류패킹 고객담당자 패킹현황
    const CUSTOMER_RECEIVER_DELIVERY = 'sl_imsCustomerReceiverDelivery';

    //작업용. 세부스케쥴. 피드백에 따라 바뀔수 있음
    const PROJECT_SCHE_DETAIL = 'sl_nmsProjectScheDetail';

    //납품검수(납품보고서)
    const PRODUCT_INSPECT_DELIVERY = 'sl_imsProjectProductInspectDelivery';

    //스타일기획 레퍼런스
    const REF_PRODUCT_PLAN = 'sl_imsRefStylePlan';
    //스타일기획 레퍼런스 부가정보(브랜드, 컨셉, 디자인, 부가기능)
    const REF_PRODUCT_PLAN_APPEND = 'sl_imsRefStylePlanAppendInfo';
    //스타일기획 레퍼런스 부가정보 릴레이션
    const REF_PRODUCT_PLAN_APPEND_RELATION = 'sl_imsRefStylePlanAppendInfoRelation';
    //스타일기획 레퍼런스 고객사 릴레이션
    const REF_PRODUCT_PLAN_CUSTOMER_RELATION = 'sl_imsRefStylePlanCustomerRelation';
    //스타일기획 레퍼런스 원부자재/공임비용/기타비용
    const REF_PRODUCT_PLAN_MATERIAL = 'sl_imsRefStylePlanMaterial';



}

