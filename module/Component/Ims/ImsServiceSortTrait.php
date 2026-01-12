<?php
namespace Component\Ims;

use App;
use Component\Database\DBTableField;
use Component\Member\Manager;
use Component\Sms\Code;
use Framework\Debug\Exception\AlertBackException;
use Framework\Utility\DateTimeUtils;
use LogHandler;
use Request;
use Session;
use SiteLabUtil\SlCommonUtil;
use SlComponent\Database\DBUtil2;
use SlComponent\Database\SearchVo;
use SlComponent\Util\SitelabLogger;
use SlComponent\Util\SlSmsUtil;

/**
 * IMS 생산가 견적 관련 서비스
 * Class GoodsStock
 * @package Component\Goods
 */
trait ImsServiceSortTrait {
    /**
     * 견적 기본 구조 정보
     * @param $sort
     * @param $searchVo
     * @return array
     */
    public function setListSort($sort, &$searchVo){
        $sortCondition = explode(',', $sort);
        $sortMap = [
            'A' => "a.completeDeadLineDt {$sortCondition[1]}, a.customerSno, a.projectSno, a.styleSno", //request
            'B' => "cust.customerName {$sortCondition[1]}, a.projectSno, a.regDt desc", //common
            //'B' => "cust.customerName {$sortCondition[1]}, a.projectSno, a.styleSno, a.completeDeadLineDt", //common
            'C' => "a.msDeliveryDt {$sortCondition[1]}, a.customerSno, a.projectSno, a.styleSno", //production
            'C1' => "prd.msDeliveryDt {$sortCondition[1]}, a.customerSno, a.projectSno, a.styleSno, a.reqCount desc", //request

            'D' => "a.regDt {$sortCondition[1]}", // all...
            'D1' => "cust.regDt {$sortCondition[1]}", // all...
            'D2' => "cust.customerName {$sortCondition[1]}", // all...
            'D3' => "cust.customerPrice {$sortCondition[1]}", // 고객 매출 순
            //['val' => 'customerCost', 'typ' => 'i', 'def' => null, 'name' => '고객 매입가'],
            //['val' => 'customerPrice', 'typ' => 'i', 'def' => null, 'name' => '고객 매출가'],

            //Sample
            'SD' => "a.sampleConfirm desc, a.regDt {$sortCondition[1]}", // all...
            'PV_SAMPLE' => "a.styleSno, a.regDt desc", // 프로젝트 뷰에서 보는 샘플 리스트

            'P1' => "prj.regDt {$sortCondition[1]}", // project 등록일
            'P2' => "prj.projectYear {$sortCondition[1]}, prj.regDt desc", // project prj.projectSeason ,
            'P3' => "prj.customerDeliveryDt {$sortCondition[1]}, prj.sno desc, prj.regDt desc, prd.sort", // 희망납기일
            'P4' => "customerSize {$sortCondition[1]}, case when prj.customerDeliveryDt is null or prj.customerDeliveryDt = '0000-00-00' then 1 else 0 end asc,prj.customerDeliveryDt asc, prj.regDt desc ", // 매출/희망납기일
            //등록일. 프로젝트 상태.
            'P5' => "prj.projectStatus {$sortCondition[1]}, prj.regDt desc, prj.sno desc",
            'P6' => "ext.salesDeliveryDt {$sortCondition[1]}, prj.regDt desc", //추정매출
            //'P7' => "prj.customerOrderDeadLine {$sortCondition[1]}, prj.regDt desc, prj.sno desc, prd.sort", //발주D/L
            'P7' => "ext.exProductionOrder {$sortCondition[1]}, prj.regDt desc, prj.sno desc, prd.sort", //발주D/L
            'P8' => "prj.customerDeliveryDt {$sortCondition[1]}, prj.sno desc, prj.regDt desc", // 고객납기일(프로젝트온리)
            'P9' => "ext.cpProductionOrder {$sortCondition[1]}, prj.sno desc, prj.regDt desc", // 고객발주일(프로젝트온리)

            'T1' => "a.hopeDt {$sortCondition[1]}, a.regDt desc", //희망일
            'T2' => "b.expectedDt {$sortCondition[1]}, a.regDt desc", //예정일
            'T3' => "b.completeDt {$sortCondition[1]}, a.regDt desc", //완료일

            'S1' => "a.sort {$sortCondition[1]}, a.regDt {$sortCondition[1]}", //스타일1
            'S2' => "a.prdStyle, a.addStyleCode, a.prdSeason", //스타일2

            //영업 소팅 (매출목표)
            'SA1'=> "ext.targetSalesYear {$sortCondition[1]}, prj.regDt desc", // project 등록일
            'SA2'=> "ext.contractDifficult {$sortCondition[1]}, ext.salesTarget {$sortCondition[1]}, ext.salesExDt asc", // project 등록일


            'COST1' => "a.estimateCost {$sortCondition[1]}, a.reqCount desc,a.regDt desc", //완료일
        ];
        $sort = $sortMap[$sortCondition[0]];
        $searchVo->setOrder(empty($sort)?'a.regDt desc':$sort);
    }
}

