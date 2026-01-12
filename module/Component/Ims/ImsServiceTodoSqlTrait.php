<?php
namespace Component\Ims;

use App;
use Component\Member\Manager;
use Component\Sms\Code;
use Framework\Debug\Exception\AlertBackException;
use Framework\Utility\DateTimeUtils;
use LogHandler;
use Request;
use Session;
use SlComponent\Database\DBUtil2;

/**
 * IMS TO-DO 서비스 SQL
 * Class GoodsStock
 * @package Component\Goods
 */
trait ImsServiceTodoSqlTrait {

    /**
     * 결재라인
     * @return array
     */
    public function getApprovalLineListTable(){
        return DBUtil2::setTableInfo([
            'a' => //메인
                [
                    'data' => [ ImsDBName::APPROVAL_LINE ]
                    , 'field' => ["*"]
                ]
        ]);
    }

    /**
     * 결재 리스트
     * @return array
     */
    public function getApprovalListTable(){
        return DBUtil2::setTableInfo([
            'a' => //메인
                [
                    'data' => [ ImsDBName::TODO_REQUEST ]
                    , 'field' => ["*"]
                ]
            ,'b' => //결재자
                [
                    'data' => [ ImsDBName::TODO_RESPONSE, 'JOIN', 'a.sno = b.reqSno' ]
                    , 'field' => ['targetType']
                ]
            , 'c' => //등록자
                [
                    'data' => [ DB_MANAGER, 'LEFT OUTER JOIN', 'a.regManagerSno = c.sno' ]
                    , 'field' => ['managerNm as regManagerNm']
                ]
            , 'prd' => //상품정보.
                [
                    'data' => [ ImsDBName::PRODUCT, 'LEFT OUTER JOIN', 'a.styleSno = prd.sno' ]
                    , 'field' => ['productName', 'prdYear', 'prdSeason', 'styleCode', 'sizeOption', 'typeOption', 'msDeliveryDt as productMsDeliveryDt', 'customerDeliveryDt as productCustomerDeliveryDt', 'fabricStatus' , 'prdCostConfirmSno']
                ]
            , 'prj' => //프로젝트정보
                [
                    'data' => [ ImsDBName::PROJECT, 'LEFT OUTER JOIN', 'a.projectSno = prj.sno' ]
                    , 'field' => ['projectNo', 'projectType', 'projectStatus', 'packingYn', 'projectSeason', 'projectYear' , 'msOrderDt', 'use3pl', 'useMall', 'productionStatus']
                ]
            , 'cust' => //고객정보
                [
                    'data' => [ ImsDBName::CUSTOMER, 'LEFT OUTER JOIN', 'a.customerSno = cust.sno' ]
                    , 'field' => ['customerName']
                ]
            ]);
    }


    /**
     * 요청 리스트
     * @return array
     */
    public function getTodoRequestListTable(){
        return DBUtil2::setTableInfo([
            'a' => //메인
                [
                    'data' => [ ImsDBName::TODO_REQUEST ]
                    , 'field' => ["a.*"]
                ]
            ,'b' => //요청 받은 정보
                [
                    'data' => [ ImsDBName::TODO_RESPONSE, 'JOIN', 'a.sno = b.reqSno' ]
                    , 'field' => ['b.targetType', 'b.status', 'b.reqRead', 'b.expectedDt', 'b.completeDt', 'b.managerSno as targetManagerSno', 'b.sno as resSno', 'b.reason']
                ]
            , 'c' => //등록자
                [
                    'data' => [ DB_MANAGER, 'LEFT OUTER JOIN', 'a.regManagerSno = c.sno' ]
                    , 'field' => ['c.managerNm as regManagerNm']
                ]
            , 'd' => //받은사람
                [
                    'data' => [ DB_MANAGER, 'LEFT OUTER JOIN', 'b.managerSno = d.sno' ]
                    , 'field' => ['d.managerNm as targetManagerNm', 'd.departmentCd as targetTeamSno']
                ]
            , 'e' => //처리자
                [
                    'data' => [ DB_MANAGER, 'LEFT OUTER JOIN', 'b.completeManagerSno = e.sno' ]
                    , 'field' => ['e.managerNm as completeManagerNm']
                ]
            , 'prd' => //상품정보.
                [
                    'data' => [ ImsDBName::PRODUCT, 'LEFT OUTER JOIN', 'a.styleSno = prd.sno' ]
                    , 'field' => ['prd.productName', 'prd.prdYear', 'prd.prdSeason', 'prd.styleCode', 'prd.sizeOption', 'prd.typeOption', 'prd.msDeliveryDt as productMsDeliveryDt', 'prd.customerDeliveryDt as productCustomerDeliveryDt', 'prd.fabricStatus' , 'prd.prdCostConfirmSno']
                ]
            , 'prj' => //프로젝트정보
                [
                    'data' => [ ImsDBName::PROJECT, 'LEFT OUTER JOIN', 'a.projectSno = prj.sno' ]
                    , 'field' => ['prj.sno as projectNo', 'prj.projectType', 'prj.projectStatus', 'prj.packingYn', 'prj.projectSeason', 'prj.projectYear' , 'prj.msOrderDt', 'prj.use3pl', 'prj.useMall', 'prj.productionStatus']
                ]
            , 'cust' => //고객정보
                [
                    'data' => [ ImsDBName::CUSTOMER, 'LEFT OUTER JOIN', 'a.customerSno = cust.sno' ]
                    , 'field' => ['cust.customerName']
                ]
            , 'code' => //받은사람 (팀별)
                [
                    'data' => [ DB_CODE, 'LEFT OUTER JOIN', 'concat(\'0\',b.managerSno) = code.itemCd' ]
                    , 'field' => ['code.itemNm as teamNm']
                ]
        ], false);
    }


    /**
     * TO-DO 코멘트 테이블
     * @return array
     */
    public function getTodoCommentListTable(){
        return DBUtil2::setTableInfo([
            'a' => //메인
                [
                    'data' => [ ImsDBName::TODO_COMMENT ]
                    , 'field' => ["*"]
                ]
            , 'c' => //등록자
                [
                    'data' => [ DB_MANAGER, 'LEFT OUTER JOIN', 'a.regManagerSno = c.sno' ]
                    , 'field' => ['managerNm as regManagerNm','managerId as regManagerId']
                ]
        ]);
    }

}


