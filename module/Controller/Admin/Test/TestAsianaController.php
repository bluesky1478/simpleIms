<?php

namespace Controller\Admin\Test;

use Controller\Admin\Order\DownloadTkeReleaseController;
use Framework\Utility\NumberUtils;
use Component\Database\DBTableField;
use Component\Sitelab\SiteLabSmsUtil;
use SiteLabUtil\SlCommonUtil;
use SlComponent\Database\DBUtil;
use SlComponent\Database\DBUtil2;
use SlComponent\Database\SearchVo;
use SlComponent\Database\TableVo;
use SlComponent\Util\ExcelCsvUtil;
use SlComponent\Util\SitelabLogger;
use Globals;
use Component\Deposit\Deposit;
use SlComponent\Util\SitelabUtil;
use SlComponent\Util\SlCode;
use SlComponent\Util\SlLoader;
use SlComponent\Util\SlSmsUtil;

/**
 * TKE 관리 페이지
 */
class TestAsianaController extends \Controller\Admin\Controller{

    public function index(){
        // -- 아시아나 배송 처리 시작
        //$this->getOrderDeliveryCnt();
        //$this->setOrderDelivery();
        // -- 아시아나 배송 처리 끝
        //$this->excelCurrentEmpList();

        //아시아나 사번 변경
        $service = SlLoader::cLoad('scm','ScmAsianaService');
/*        $service->refineCompanyId('983173', '513833');
        $service->refineCompanyId('983262', '514055'); // 김재율
        $service->refineCompanyId('983263', '514056'); // 최무성
        $service->refineCompanyId('983264', '514057'); // 김지민
        $service->refineCompanyId('983265', '514061'); // 곽필성
        $service->refineCompanyId('983266', '514058'); // 김광우
        $service->refineCompanyId('983267', '514063'); // 김현민
        $service->refineCompanyId('983268', '514066'); // 주재현
        $service->refineCompanyId('983269', '514074'); // 정세진
        $service->refineCompanyId('983270', '514076'); // 전호윤
        $service->refineCompanyId('983271', '514069'); // 홍인표*/

        //아시아나 사번 변경 + 직급 변경
        /*
        $service->refineCompanyId('514308', '901240','촉탁');
        $service->refineCompanyId('514309', '891086','촉탁');
        $service->refineCompanyId('514310', '901047','촉탁');*/

        //퇴직자 처리
        /*$this->setRetired('911257');
        $this->setRetired('901119');
        $this->setRetired('941173');*/

        gd_debug("완료");
        exit();
    }


    /**
     * 퇴직자 처리
     * @param $id
     */
    public function setRetired($id){
        gd_debug(DBUtil2::runSql("update sl_asianaEmployee set retiredFl='y' where companyId='{$id}'"));
    }
    

    /**
     * 아시아나 배송 처리
     */
    public function getOrderDeliveryCnt(){
        //수량 체크
        gd_debug(DBUtil2::runSelect("select sum(goodsCnt) cnt from es_orderGoods  where orderStatus = 'g1' and scmNo = 34;"));
    }
    public function setOrderDelivery(){
        //주문 변경
        $rslt1 = DBUtil2::runSql("update es_order set orderStatus = 'd2' where orderNo in (select distinct orderNo from es_orderGoods where orderStatus = 'g1' and scmNo = 34)");
        //주문 상품 변경
        $rslt2 = DBUtil2::runSql("update es_orderGoods  set orderStatus = 'd2' , deliveryDt = now(), deliveryCompleteDt = now() where orderStatus = 'g1' and scmNo = 34");
        gd_debug('주문변경:'.$rslt1);
        gd_debug('주문상품변경:'.$rslt2);
    }

    /**
     * 현재 사원리스트 다운로드
     */
    public function excelCurrentEmpList(){

        $empList = DBUtil2::getList('sl_asianaEmployee','1','1');

        $fileTitle = "아시아나사원리스트_".date('Y-m-d');
        $htmlList = [];
        $htmlList[] = '<table class="table table-rows" border="1">';
        $htmlList[] = '<tr>';
        $htmlList[] = ExcelCsvUtil::wrapTh('사번','title','background-color:#f0f0f0; font-weight:bold','');
        $htmlList[] = ExcelCsvUtil::wrapTh('이름','title','background-color:#f0f0f0; font-weight:bold','');
        $htmlList[] = '</tr>';
        foreach( $empList as $emp ){
            $htmlList[] = '<tr>';
            $htmlList[] = ExcelCsvUtil::wrapTd($emp['companyId']);
            $htmlList[] = ExcelCsvUtil::wrapTd($emp['empName']);
            $htmlList[] = '</tr>';
        }
        $htmlList[] = '</table>';
        $excelBody =  implode('',$htmlList);
        $simpleExcelComponent = SlLoader::cLoad('Excel','SimpleExcelComponent','sl');
        $simpleExcelComponent->downloadCommon($excelBody, $fileTitle);
    }


}