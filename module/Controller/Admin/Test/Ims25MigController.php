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
class Ims25MigController extends \Controller\Admin\Controller{

    public function index(){
        gd_debug("== IMS 25 마이그레이션 ==");
        gd_debug('1. 영업보류 프로젝트 상태 변경 (11 -> 97) : ' . ( DBUtil2::runSql("update sl_imsProject set projectStatus=97 where projectStatus=11") ) );
        gd_debug("2. 고객 상태 변경 : " . ( $this->setCustomerStatus() ));

        //TODO : 영업대기, 사전영업 단계 제외    기획~종결 모두 영업기획 승인 처리.

        gd_debug("완료");
        exit();
    }

    /**
     * 고객 상태 마이그레이션
     * 0 신규 : 신규(초도) - 새로운 프로젝트 등록시 신규(발주없음) or 재입찰(발주있음)로 셋팅
     * 1 재입찰 : 수기     - 새로운 프로젝트 등록시 신규(발주없음) or 재입찰(발주있음)로 셋팅
     * 2 계약중 : 자동 - 발주건 있음 + 현재 상태가 수기 상태가 아님 ( 재입찰, 보류, 이탈, 유찰 )
     * 10 보류 : 수기 - 초도는 마지막 프로젝트가 보류인 경우.
     * 11 이탈 : 수기 - 이탈 처리된 고객
     * 12 유찰 : 수기 - 유찰 처리된 고객
     */
    public function setCustomerStatus(){
        //Legacy
        /*$rslt1 = DBUtil2::runSql("update sl_imsCustomer set customerDiv = 0  where 1=1");
        $custDiv2Sql = "select distinct customerSno from sl_imsProject where 50 >= projectStatus and projectStatus >= 15";
        $rslt2 = DBUtil2::runSql("update sl_imsCustomer set customerDiv = 1  where sno in ({$custDiv2Sql})");
        $custDiv3Sql = "select distinct customerSno from sl_imsProject where projectStatus >= 60 &&  91 >= projectStatus";
        $rslt3 = DBUtil2::runSql("update sl_imsCustomer set customerDiv = 2  where sno in ({$custDiv3Sql})");*/

        //초도는 아래 처럼 셋팅
        //* 0 신규 : 신규
        //* 1 계약 중 : 발주건 있음 + 마지막 프로젝트 보류 아님 + 현재 상태가 보류상태가 아닐 것
        //* 10 보류 : 마지막 프로젝트가 보류

        $rslt = DBUtil2::runSql("
        UPDATE sl_imsCustomer c
        LEFT JOIN (
            SELECT t.customerSno,
                   t.has_order,
                   p_last.projectStatus AS last_status
            FROM (
                SELECT p.customerSno,
                       MAX(CASE WHEN p.projectStatus BETWEEN 60 AND 91 THEN 1 ELSE 0 END) AS has_order,
                       MAX(p.sno) AS last_projectSno
                FROM sl_imsProject p
                GROUP BY p.customerSno
            ) t
            LEFT JOIN sl_imsProject p_last
                   ON p_last.customerSno = t.customerSno
                  AND p_last.sno         = t.last_projectSno
        ) s
        ON s.customerSno = c.sno
        SET c.customerStatus = CASE
            WHEN s.last_status = 11 THEN 10
            WHEN s.has_order = 1
                 AND (s.last_status IS NULL OR s.last_status <> 11)
                 AND c.customerStatus <> 10 THEN 1
            ELSE 0
        END");
        return $rslt;
    }

}
