<?php
namespace Component\Ims;

use App;
use Component\Database\DBIms;
use Component\Database\DBTableField;
use Component\Member\Manager;
use Component\Scm\ScmTkeService;
use Framework\Debug\Exception\AlertBackException;
use Framework\Utility\DateTimeUtils;
use LogHandler;
use Request;
use Session;
use SiteLabUtil\SlCommonUtil;
use SlComponent\Database\DBUtil;
use SlComponent\Database\DBUtil2;
use SlComponent\Database\SearchVo;
use SlComponent\Mail\SiteLabMailUtil;
use SlComponent\Util\ExcelCsvUtil;
use SlComponent\Util\SitelabLogger;
use SlComponent\Util\SlCodeMap;
use SlComponent\Util\SlLoader;
use Component\Sms\Code;

/**
 * IMS 서비스
 * Class GoodsStock
 * @package Component\Goods
 */
class ImsServiceSql {

    use ImsServiceTodoSqlTrait;
    use ImsServiceConditionTrait;

    /**
     * 코멘트
     * @return array
     */    
    public function getTableImsComment(){
        return DBUtil2::setTableInfo([
            'a' => //메인
                [
                    'data' => [ ImsDBName::PROJECT_COMMENT ]
                    , 'field' => ["*"]
                ]
            , 'reg' => //최초 등록
                [
                    'data' => [ DB_MANAGER, 'LEFT OUTER JOIN', 'a.regManagerSno = reg.sno' ]
                    , 'field' => ['managerNm as regManagerNm']
                ]
            , 'prd' => //상품정보.
                [
                    'data' => [ ImsDBName::PRODUCT, 'LEFT OUTER JOIN', 'a.styleSno = prd.sno' ]
                    , 'field' => ['productName', 'prdYear', 'prdSeason', 'styleCode']
                ]
            , 'prj' => //프로젝트정보
                [
                    'data' => [ ImsDBName::PROJECT, 'LEFT OUTER JOIN', 'a.projectSno = prj.sno' ]
                    , 'field' => ['projectNo', 'projectType', 'projectStatus']
                ]
            , 'cust' => //고객정보
                [
                    'data' => [ ImsDBName::CUSTOMER, 'LEFT OUTER JOIN', 'a.customerSno = cust.sno' ]
                    , 'field' => ['customerName']
                ]
        ]);
    }

    /**
     * 프로젝트
     * @return array
     * @throws \Exception
     */
    public function getProjectNewTable(){

        $extFieldList = DBTableField::getDefaultFieldList(ImsDBName::PROJECT_EXT, ['projectSno']);

        return DBUtil2::setTableInfo([
            'prj' => //메인
                [
                    'data' => [ ImsDBName::PROJECT ]
                    , 'field' => ['*, prj.sno as projectSno']
                ]
            , 'b' => //등록자
                [
                    'data' => [ DB_MANAGER, 'LEFT OUTER JOIN', 'prj.regManagerSno = b.sno' ]
                    , 'field' => ['managerNm as regManagerNm']
                ]
            , 'sales' => //영업 담당자
                [
                    'data' => [ DB_MANAGER, 'LEFT OUTER JOIN', 'prj.salesManagerSno = sales.sno' ]
                    , 'field' => ['managerNm as salesManagerNm']
                ]
            , 'desg' => //디자인 담당자
                [
                    'data' => [ DB_MANAGER, 'LEFT OUTER JOIN', 'prj.designManagerSno = desg.sno' ]
                    , 'field' => ['managerNm as designManagerNm']
                ]
            , 'cust' => //고객정보
                [
                    'data' => [ ImsDBName::CUSTOMER, 'JOIN', 'prj.customerSno = cust.sno' ]
                    , 'field' => ['customerName']
                ]
            , 'ext' => //프로젝트 확장 정보
                [
                    'data' => [ ImsDBName::PROJECT_EXT, 'LEFT OUTER JOIN', 'prj.sno = ext.projectSno' ]
                    , 'field' => $extFieldList
                ]
        ]);
    }


    /**
     * 파일
     * projectSno, styleSno , eachSno
     * @return array
     */
    public function getFileTable(){
        return DBUtil2::setTableInfo([
            'a' => //메인
                [
                    'data' => [ ImsDBName::PROJECT_FILE ]
                    , 'field' => ['*']
                ]
            , 'b' => //등록자
                [
                    'data' => [ DB_MANAGER, 'LEFT OUTER JOIN', 'a.regManagerSno = b.sno' ]
                    , 'field' => ['managerNm']
                ]
            , 'c' => //고객 정보
                [
                    'data' => [ ImsDBName::CUSTOMER, 'LEFT OUTER JOIN', 'a.customerSno = c.sno' ]
                    , 'field' => ['customerName']
                ]
            , 'd' => //프로젝트 정보
                [
                    'data' => [ ImsDBName::PROJECT, 'LEFT OUTER JOIN', 'a.projectSno = d.sno' ]
                    , 'field' => ['projectNo']
                ]
            , 'e' => //스타일 정보
                [
                    'data' => [ ImsDBName::PRODUCT, 'LEFT OUTER JOIN', 'a.styleSno = e.sno' ]
                    , 'field' => ['productName', 'styleCode']
                ]
            , 'f' => //샘플 정보
                [
                    'data' => [ ImsDBName::SAMPLE, 'LEFT OUTER JOIN', 'a.eachSno = f.sno' ]
                    , 'field' => ['sampleName, f.sno as sampleSno']
                ]
        ]);
    }

    /**
     * 준비.
     * @return array
     */
    public function getPreparedTable(){
        return DBUtil2::setTableInfo([
            'a' => //메인
                [
                    'data' => [ ImsDBName::PREPARED ]
                    , 'field' => ['*, a.sno as preparedSno']
                ]
            , 'b' => //등록자
                [
                    'data' => [ DB_MANAGER, 'LEFT OUTER JOIN', 'a.regManagerSno = b.sno' ]
                    , 'field' => ['managerNm as regManagerNm']
                ]
            , 'c' => //생산처
                [
                    'data' => [ DB_MANAGER, 'LEFT OUTER JOIN', 'a.produceCompanySno = c.sno' ]
                    , 'field' => ['managerNm as produceCompany']
                ]
        ]);
    }

    /**
     * 샘플 구조
     * @return array
     */
    public function getSampleListTable(){
        return DBUtil2::setTableInfo([
            'a' => //메인
                [
                    'data' => [ ImsDBName::SAMPLE ]
                    , 'field' => ['*, a.sno as sampleSno, a.sno as eachSno']
                ]
            , 'b' => //등록자
                [
                    'data' => [ DB_MANAGER, 'LEFT OUTER JOIN', 'a.sampleManagerSno = b.sno' ]
                    , 'field' => ['managerNm as sampleManagerNm']
                ]
            , 'c' => //샘플실
                [
                    'data' => [ ImsDBName::SAMPLE_FACTORY, 'LEFT OUTER JOIN', 'a.sampleFactorySno = c.sno' ]
                    , 'field' => ['factoryName']
                ]
            , 'pattern' => //패턴실
                [
                    'data' => [ ImsDBName::SAMPLE_FACTORY, 'LEFT OUTER JOIN', 'a.patternFactorySno = pattern.sno' ]
                    , 'field' => ['factoryName as patternFactoryName']
                ]
            , 'prd' => //상품정보.
                [
                    'data' => [ ImsDBName::PRODUCT, 'JOIN', 'a.styleSno = prd.sno' ]
                    , 'field' => ['productName', 'prdYear', 'prdSeason', 'styleCode', 'sizeOption', 'typeOption', 'msDeliveryDt as productMsDeliveryDt', 'customerDeliveryDt as productCustomerDeliveryDt', 'fabricStatus as prdFabricStatus' , 'prdCostConfirmSno', 'prdExQty']
                ]
            , 'prj' => //프로젝트정보
                [
                    'data' => [ ImsDBName::PROJECT, 'JOIN', 'a.projectSno = prj.sno' ]
                    , 'field' => ['projectNo', 'projectType', 'projectStatus', 'packingYn', 'projectSeason', 'projectYear' , 'msOrderDt', 'use3pl', 'useMall']
                ]
            , 'plan' =>
                [
                    'data' => [ ImsDBName::PRODUCT_PLAN, 'LEFT OUTER JOIN', 'a.productPlanSno = plan.sno' ]
                    , 'field' => ['planConcept']
                ]
        ]);
    }
    /**
     * 원단 구조
     * @return array
     */
    public function getFabricListTable(){
        return DBUtil2::setTableInfo([
            'a' => //메인
                [
                    'data' => [ ImsDBName::FABRIC ]
                    , 'field' => ['*, a.sno as fabricSno, a.sno as eachSno']
                ]
            , 'b' => //등록자
                [
                    'data' => [ DB_MANAGER, 'LEFT OUTER JOIN', 'a.regManagerSno = b.sno' ]
                    , 'field' => ['managerNm as regManagerNm']
                ]
            , 'c' => //수정자
                [
                    'data' => [ DB_MANAGER, 'LEFT OUTER JOIN', 'a.lastManagerSno = c.sno' ]
                    , 'field' => ['managerNm as lastManagerNm']
                ]
            , 'd' => //생산처
                [
                    'data' => [ DB_MANAGER, 'LEFT OUTER JOIN', 'a.reqFactory = d.sno' ]
                    , 'field' => ['managerNm as reqFactoryNm']
                ]
            , 'e' => //생산처 요청자
                [
                    'data' => [ DB_MANAGER, 'LEFT OUTER JOIN', 'a.reqManagerSno = e.sno' ]
                    , 'field' => ['managerNm as reqManagerNm']
                ]
            , 'prd' => //상품정보.
                [
                    'data' => [ ImsDBName::PRODUCT, 'JOIN', 'a.styleSno = prd.sno' ]
                    , 'field' => ['productName', 'prdYear', 'prdSeason', 'styleCode', 'sizeOption', 'typeOption', 'msDeliveryDt as productMsDeliveryDt', 'customerDeliveryDt as productCustomerDeliveryDt', 'fabricStatus as prdFabricStatus' , 'prdCostConfirmSno', 'prdExQty']
                ]
            , 'prj' => //프로젝트정보
                [
                    'data' => [ ImsDBName::PROJECT, 'JOIN', 'prd.projectSno = prj.sno' ]
                    , 'field' => ['projectNo', 'projectType', 'projectStatus', 'packingYn', 'projectSeason', 'projectYear' , 'msOrderDt', 'use3pl', 'useMall']
                ]
            , 'cust' => //고객정보
                [
                    'data' => [ ImsDBName::CUSTOMER, 'JOIN', 'a.customerSno = cust.sno' ]
                    , 'field' => ['customerName']
                ]
        ]);
    }

    /**
     * 원단요청 리스트
     * @return array
     */
    public function getFabricReqListTable(){
        return DBUtil2::setTableInfo([
            'a' => //메인
                [
                    'data' => [ ImsDBName::FABRIC_REQ ]
                    , 'field' => ['*, a.sno as eachSno']
                ]
            , 'fabric' => //메인
                [
                    'data' => [ ImsDBName::FABRIC, 'JOIN', 'fabric.sno = a.fabricSno' ]
                    , 'field' => [
                        'position',
                        'attached',
                        'fabricName',
                        'fabricMix',
                        'color',
                        'spec',
                        'meas',
                        'weight',
                        'fabricWidth',
                        'afterMake',
                        'makeNational',
                        'fabricConfirmInfo',
                        'btConfirmInfo',
                        'bulkConfirmInfo',
                    ]
                ]
            , 'b' => //등록자
                [
                    'data' => [ DB_MANAGER, 'LEFT OUTER JOIN', 'a.reqManagerSno = b.sno' ]
                    , 'field' => ['managerNm as reqManagerNm']
                ]
            , 'd' => //생산처
                [
                    'data' => [ DB_MANAGER, 'LEFT OUTER JOIN', 'a.reqFactory = d.sno' ]
                    , 'field' => ['managerNm as reqFactoryNm']
                ]
            , 'prd' => //상품정보.
                [
                    'data' => [ ImsDBName::PRODUCT, 'JOIN', 'fabric.styleSno = prd.sno' ]
                    , 'field' => ['productName', 'prdYear', 'prdSeason', 'styleCode', 'sizeOption', 'typeOption', 'msDeliveryDt as productMsDeliveryDt', 'customerDeliveryDt as productCustomerDeliveryDt', 'fabricStatus' , 'prdCostConfirmSno', 'prdExQty']
                ]
            , 'prj' => //프로젝트정보
                [
                    'data' => [ ImsDBName::PROJECT, 'JOIN', 'fabric.projectSno = prj.sno' ]
                    , 'field' => ['projectNo', 'projectType', 'projectStatus']
                ]
            , 'cust' => //고객정보
                [
                    'data' => [ ImsDBName::CUSTOMER, 'JOIN', 'fabric.customerSno = cust.sno' ]
                    , 'field' => ['customerName']
                ]
            , 'sales' => //영업
                [
                    'data' => [ DB_MANAGER, 'LEFT OUTER JOIN', 'prj.salesManagerSno = sales.sno' ]
                    , 'field' => ['managerNm as salesManagerName']
                ]
            , 'design' => //디자인
                [
                    'data' => [ DB_MANAGER, 'LEFT OUTER JOIN', 'prj.designManagerSno = design.sno' ]
                    , 'field' => ['managerNm as designManagerName']
                ]
        ]);
    }

    /**
     * 가견적 생산가 생산견적
     * @return array
     */
    public function getEstimateListTable(){
        return DBUtil2::setTableInfo([
            'a' => //메인
                [
                    'data' => [ ImsDBName::ESTIMATE ]
                    , 'field' => ['*, a.sno as estimateSno, a.sno as eachSno']
                ]
            , 'd' => //생산처
                [
                    'data' => [ DB_MANAGER, 'LEFT OUTER JOIN', 'a.reqFactory = d.sno' ]
                    , 'field' => ['managerNm as reqFactoryNm']
                ]
            , 'e' => //요청자
                [
                    'data' => [ DB_MANAGER, 'LEFT OUTER JOIN', 'a.reqManagerSno = e.sno' ]
                    , 'field' => ['managerNm as reqManagerNm']
                ]
            , 'f' => //마지막 수정
                [
                    'data' => [ DB_MANAGER, 'LEFT OUTER JOIN', 'a.lastManagerSno = f.sno' ]
                    , 'field' => ['managerNm as lastManagerNm']
                ]
            , 'prd' => //상품정보.
                [
                    'data' => [ ImsDBName::PRODUCT, 'JOIN', 'a.styleSno = prd.sno' ]
                    , 'field' => ['productName', 'prdYear', 'prdSeason', 'styleCode']
                ]
            , 'prj' => //프로젝트정보
                [
                    'data' => [ ImsDBName::PROJECT, 'JOIN', 'a.projectSno = prj.sno' ]
                    , 'field' => ['projectNo', 'projectType', 'projectStatus']
                ]
            , 'cust' => //고객정보
                [
                    'data' => [ ImsDBName::CUSTOMER, 'JOIN', 'a.customerSno = cust.sno' ]
                    , 'field' => ['customerName']
                ]
        ]);
    }


    /**
     * 구 프로젝트 
     * @return array
     */
    public function getProjectTable(){

        $sqlList = [];
        foreach( ImsCodeMap::PROJECT_STEP_COMMENT_DIV as $div){
            $sqlList[] = "(select count(1) cnt from sl_imsComment where projectSno = a.sno and commentDiv = '{$div}' ) as {$div}Count";
        }
        $commentListSql = implode(',', $sqlList);

        return DBUtil2::setTableInfo([
            'a' => //메인
                [
                    'data' => [ ImsDBName::PROJECT ]
                    , 'field' => ["*, {$commentListSql}"]
                ]
            , 'b' => //영업 담당자
                [
                    'data' => [ DB_MANAGER, 'LEFT OUTER JOIN', 'a.salesManagerSno = b.sno' ]
                    , 'field' => ['managerNm as salesManagerNm']
                ]
            , 'c' => //디자인 담당자
                [
                    'data' => [ DB_MANAGER, 'LEFT OUTER JOIN', 'a.designManagerSno = c.sno' ]
                    , 'field' => ['managerNm as designManagerNm']
                ]
            , 'd' => //생산처
                [
                    'data' => [ DB_MANAGER, 'LEFT OUTER JOIN', 'a.produceCompanySno = d.sno' ]
                    , 'field' => ['managerNm as produceCompany']
                ]
            , 'e' => //확장 정보
                [
                    'data' => [ ImsDBName::PROJECT_EXT, 'LEFT OUTER JOIN', 'a.sno = e.projectSno' ]
                    , 'field' => [
                        'salesStatus',
                        'salesDeliveryDt',
                        'extAmount',
                        'extMargin',
                        'designWorkType',
                    ]
                ]
        ]);
    }

    /**
     * 상품
     * @return array
     */
    public function getProductTable(){
        return DBUtil2::setTableInfo([
            'a' => //메인
                [
                    'data' => [ ImsDBName::PRODUCT ]
                    , 'field' => ["*"]
                ]
            , 'b' => //가견적 확정자
                [
                    'data' => [ DB_MANAGER, 'LEFT OUTER JOIN', 'a.estimateConfirmManagerSno = b.sno' ]
                    , 'field' => ['managerNm as estimateConfirmManagerNm']
                ]
            , 'c' => //확정가 확정자
                [
                    'data' => [ DB_MANAGER, 'LEFT OUTER JOIN', 'a.prdCostConfirmManagerSno = c.sno' ]
                    , 'field' => ['managerNm as prdCostConfirmManagerNm']
                ]
            , 'prj' => //확정가 확정자
                [
                    'data' => [ ImsDBName::PROJECT, 'JOIN', 'a.projectSno = prj.sno' ]
                    , 'field' => ['projectType']
                ]
        ]);
    }


    /**
     * (신) 미팅 리스트
     * @return array
     */
    public function getMeetingTable(){
        return DBUtil2::setTableInfo([
            'a' => //메인
                [
                    'data' => [ ImsDBName::NEW_MEETING ]
                    , 'field' => ["*"]
                ]
            , 'b' => //최초 등록
                [
                    'data' => [ DB_MANAGER, 'LEFT OUTER JOIN', 'a.regManagerSno = b.sno' ]
                    , 'field' => ['managerNm as regManagerNm']
                ]
            , 'c' => //마지막 수정
                [
                    'data' => [ DB_MANAGER, 'LEFT OUTER JOIN', 'a.lastManagerSno = c.sno' ]
                    , 'field' => ['managerNm as lastManagerNm']
                ]
            , 'cust' => //고객정보
                [
                    'data' => [ ImsDBName::CUSTOMER, 'LEFT OUTER JOIN', 'a.customerSno = cust.sno' ]
                    , 'field' => ['customerName']
                ]
        ]);
    }

    /**
     * 생산 테이블
     * @return array
     */
    public function getProductionListTable(){
        return DBUtil2::setTableInfo([
            'a' => //메인
                [
                    'data' => [ ImsDBName::PRODUCTION ]
                    , 'field' => ["*"]
                ]
            , 'b' => //최초 등록
                [
                    'data' => [ DB_MANAGER, 'LEFT OUTER JOIN', 'a.regManagerSno = b.sno' ]
                    , 'field' => ['managerNm as regManagerNm']
                ]
            , 'c' => //마지막 수정
                [
                    'data' => [ DB_MANAGER, 'LEFT OUTER JOIN', 'a.lastManagerSno = c.sno' ]
                    , 'field' => ['managerNm as lastManagerNm']
                ]
            , 'fact' => //생산처 정보
                [
                    'data' => [ DB_MANAGER, 'LEFT OUTER JOIN', 'a.produceCompanySno = fact.sno' ]
                    , 'field' => ['managerNm as reqFactoryNm']
                ]
            , 'prd' => //상품정보.
                [
                    'data' => [ ImsDBName::PRODUCT, 'JOIN', 'a.styleSno = prd.sno' ]
                    , 'field' => ['salePrice','prdCost','productName', 'prdYear', 'prdSeason', 'styleCode', 'sizeOption', 'typeOption', 'msDeliveryDt as productMsDeliveryDt', 'customerDeliveryDt as productCustomerDeliveryDt', 'fabricStatus' , 'prdCostConfirmSno', 'workStatus']
                ]
            , 'prj' => //프로젝트정보
                [
                    'data' => [ ImsDBName::PROJECT, 'JOIN', 'a.projectSno = prj.sno' ]
                    , 'field' => ['projectNo', 'projectType', 'projectStatus', 'packingYn', 'projectSeason', 'projectYear' , 'msOrderDt', 'use3pl', 'useMall']
                ]
            , 'cust' => //고객정보
                [
                    'data' => [ ImsDBName::CUSTOMER, 'JOIN', 'a.customerSno = cust.sno' ]
                    , 'field' => ['customerName']
                ]
            /*, 'ework' => //작업지시서
                [
                    'data' => [ ImsDBName::EWORK, 'LEFT OUTER JOIN', 'a.styleSno = ework.styleSno' ]
                    , 'field' => ['productionWarning']
                ]*/
        ]);
    }


    /**
     * (신) 미팅 리스트
     * @return array
     */
    public function getStatusTable(){
        return DBUtil2::setTableInfo([
            'a' => //메인
                [
                    'data' => [ ImsDBName::STATUS_HISTORY ]
                    , 'field' => ["*"]
                ]
            , 'b' => //최초 등록
                [
                    'data' => [ DB_MANAGER, 'LEFT OUTER JOIN', 'a.regManagerSno = b.sno' ]
                    , 'field' => ['managerNm as regManagerNm']
                ]
            , 'prd' => //상품정보.
                [
                    'data' => [ ImsDBName::PRODUCT, 'LEFT OUTER JOIN', 'a.styleSno = prd.sno' ]
                    , 'field' => ['productName', 'prdYear', 'prdSeason', 'styleCode']
                ]
            , 'prj' => //프로젝트정보
                [
                    'data' => [ ImsDBName::PROJECT, 'LEFT OUTER JOIN', 'a.projectSno = prj.sno' ]
                    , 'field' => ['projectNo', 'projectType', 'projectStatus']
                ]
            , 'cust' => //고객정보
                [
                    'data' => [ ImsDBName::CUSTOMER, 'LEFT OUTER JOIN', 'a.customerSno = cust.sno' ]
                    , 'field' => ['customerName']
                ]
        ]);
    }


    /**
     * 고객 이슈
     * @return array
     */
    public function getTableImsCustomerIssue(){
        return DBUtil2::setTableInfo([
            'a' => //메인
                [
                    'data' => [ ImsDBName::CUSTOMER_ISSUE ]
                    , 'field' => ["*"]
                ]
            , 'reg' => //최초 등록
                [
                    'data' => [ DB_MANAGER, 'LEFT OUTER JOIN', 'a.regManagerSno = reg.sno' ]
                    , 'field' => ['managerNm as regManagerNm']
                ]
            , 'prj' => //프로젝트정보
                [
                    'data' => [ ImsDBName::PROJECT, 'LEFT OUTER JOIN', 'a.projectSno = prj.sno' ]
                    , 'field' => ['projectNo', 'projectType', 'projectStatus']
                ]
            , 'cust' => //고객정보
                [
                    'data' => [ ImsDBName::CUSTOMER, 'LEFT OUTER JOIN', 'a.customerSno = cust.sno' ]
                    , 'field' => ['customerName']
                ]
        ]);
    }


    /**
     * 미팅 리스트
     * @param $condition
     * @param $searchVo
     * @return mixed
     */
    public function setMeetingListCondition($condition, $searchVo){
        $searchVo = $this->setCommonCondition($condition, $searchVo);
        if( '' != $condition['meetingStatus'] ){
            $searchVo->setWhere('meetingStatus=?');
            $searchVo->setWhereValue($condition['meetingStatus']);
        }
        return $searchVo;
    }

    /**
     * 생산 리스트 (TODO)
     * @param $condition
     * @param $searchVo
     * @return mixed
     */
    public function setProductionListCondition($condition, $searchVo){
        $searchVo = $this->setCommonCondition($condition, $searchVo);
        return $searchVo;
    }

}

