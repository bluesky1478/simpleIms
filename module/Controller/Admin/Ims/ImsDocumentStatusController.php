<?php

namespace Controller\Admin\Ims;

use App;
use Component\Category\BrandAdmin;
use Component\Category\CategoryAdmin;
use Component\Ims\ImsDBName;
use Component\Stock\StockListService;
use Component\Work\WorkCodeMap;
use Controller\Admin\Erp\AdminErpControllerTrait;
use Controller\Admin\Erp\ControllerService\InoutListService;
use Globals;
use Request;
use SiteLabUtil\SlCommonUtil;
use SlComponent\Database\DBUtil2;
use SlComponent\Database\SearchVo;
use SlComponent\Util\ExcelCsvUtil;
use SlComponent\Util\SlCodeMap;
use SlComponent\Util\SlLoader;
use function SlComponent\Util\SlLoader;

/**
 * 프로젝트 리스트
 */
class ImsDocumentStatusController extends \Controller\Admin\Controller{

    use ImsControllerTrait;

    const MAIN_COUNT_SQL = "select 
concat(a.projectYear,a.projectSeason) as season,
count(distinct a.customerSno) as customerCount,
count(distinct a.sno) as projectCount,
count(distinct styleCode) as styleCount,
COUNT(DISTINCT CASE WHEN a.costStatus = '2' THEN a.sno END) as costCount,
count(distinct CASE WHEN b.inlineStatus = '1' THEN b.styleCode END) as inlineCount
from sl_imsProject a 
join sl_imsProjectProduct b on a.sno = b.projectSno
where b.delFl = 'n'
and a.projectStatus not in (98,99)
and a.projectType not in (3,4)
group by concat(a.projectYear,a.projectSeason)";

    const FILE_COUNT_SQL = "
SELECT 
  CONCAT(a.projectYear, a.projectSeason) AS season,
  COUNT(DISTINCT CASE WHEN b.fileDiv = 'fileEtc2' THEN b.projectSno END) as estimateCnt, --  특정 조건 b.fileDiv = 'fileEtc2' 인 값만 카운팅  
  COUNT(DISTINCT CASE WHEN b.fileDiv = 'fileEtc4' THEN b.projectSno END) as salesConfirmedCnt, --  특정 조건 b.fileDiv = 'fileEtc4' 인 값만 카운팅
  COUNT(DISTINCT CASE WHEN b.fileDiv = 'fileWork' THEN b.projectSno END) as workCnt --  특정 조건 b.fileDiv = 'fileWork' 인 값만 카운팅
FROM 
  sl_imsProject a 
JOIN 
  sl_imsFile b ON a.sno = b.projectSno 
WHERE 
  a.projectStatus NOT IN (98, 99)
  AND a.projectType NOT IN (3,4)
GROUP BY 
  CONCAT(a.projectYear, a.projectSeason);

";


    public function index(){
        $this->setDefault();
        $this->callMenu('ims', 'project', 'all');

        $refineList = [];

        $list = DBUtil2::runSelect(self::MAIN_COUNT_SQL);
        foreach( $list as $key => $each ){
            $list[$key] = $each;
            $refineList[$each['season']] = $each;
        }

        $estimateCountList = DBUtil2::runSelect(self::FILE_COUNT_SQL);
        foreach($estimateCountList as $each){
            $refineList[$each['season']]['estimate'] = $each['estimateCnt'];
            $refineList[$each['season']]['sales'] = $each['salesConfirmedCnt'];
            $refineList[$each['season']]['workCnt'] = $each['workCnt'];
        }

        $this->setData('list', $refineList);
    }

    /**
     * 공통
     */
    public function setStepCommon(){
        $this->setData('stepItem', [
            'customerDeliveryDtShort' => [
                'title' => '고객납기일',
                'type' => 'text',
                'col' => 6,
                'class' => 'font-13',
                'addKey' => 'customerDeliveryRemainDt',
            ],
            'styleWithCount' => [
                'title' => '스타일',
                'type' => 'text',
                'col' => 10,
            ],
            'prdExQty' => [
                'title' => '수량',
                'type' => 'number',
                'col' => 3,
            ],
            'recommendIcon' => [
                'title' => '제안형태',
                'type' => 'text',
                'col' => 3,
            ],
            'planConfirmKr' => [
                'title' => '기획서',
                'type' => 'text',
                'col' => 3,
            ],
            'proposalConfirmKr' => [
                'title' => '제안서',
                'type' => 'text',
                'col' => 3,
            ],
            'sampleConfirmKr' => [
                'title' => '샘플',
                'type' => 'text',
                'col' => 3,
            ],
            'customerOrderConfirmKr' => [
                'title' => '고객사양서확정',
                'type' => 'text',
                'col' => 3,
            ],
        ]);
    }
    public function setStepCommonStyle(){
        $this->setData('stepItem', [
            'designManagerNm' => [
                'title' => '디자인담당',
                'type' => 'text',
                'col' => 4,
            ],
            'fileThumbnail' => [
                'title' => '스타일이미지',
                'type' => 'img',
                'col' => 4.5,
            ],
            'productName' => [
                'title' => '스타일',
                'type' => 'text',
                'col' => 10,
                'addKey' => 'styleCode'
            ],
            'prdExQty' => [
                'title' => '수량',
                'type' => 'number',
                'col' => 3,
            ],
            'currentPrice' => [
                'title' => '현재단가',
                'type' => 'number',
                'col' => 4.5,
            ],
            'targetPrice' => [
                'title' => '타겟단가',
                'type' => 'number',
                'col' => 4.5,
            ],
            'reqCost' => [
                'title' => '타겟생산가',
                'type' => 'number',
                'col' => 4.5,
            ],
            'marginPercent' => [
                'title' => '타겟마진',
                'type' => 'percent',
                'col' => 3,
            ],
            'customerDeliveryDtShort' => [
                'title' => '고객납기일',
                'type' => 'text',
                'col' => 4.5,
                'class' => 'font-14',
            ],
            'customerDeliveryRemainDt' => [
                'title' => '납기 D-day',
                'type' => 'text',
                'col' => 4.5,
                'class' => 'font-14 text-danger'
            ],
            'planConfirmKr' => [
                'title' => '기획서',
                'type' => 'text',
                'col' => 4.5,
            ],
            'proposalConfirmKr' => [
                'title' => '제안서',
                'type' => 'text',
                'col' => 4.5,
            ],
            'sampleConfirmKr' => [
                'title' => '샘플',
                'type' => 'text',
                'col' => 4.5,
            ],
            'customerOrderConfirmKr' => [
                'title' => '고객사양서확정',
                'type' => 'text',
                'col' => 3.5,
            ],
        ]);
    }

    /**
     * 미팅준비
     */
    public function setStep10(){
        $this->setData('stepItem', [
            'meetingDt' => [
                'title' => '미팅 일자',
                'type' => 'text',
                'col' => 5,
                'addKey' => 'meetingRemainDt'
            ],
            'meetingTime' => [
                'title' => '시간',
                'type' => 'text',
                'col' => 3,
            ],
            'customerDeliveryDtShort' => [
                'title' => '고객납기일',
                'type' => 'text',
                'col' => 9,
                'class' => 'font-13',
                'addKey' => 'customerDeliveryRemainDt',
            ],
            'location' => [
                'title' => '미팅장소',
                'type' => 'text',
                'col' => 14,
                'class' => 'text-left'
            ],
            'readyItem' => [
                'title' => '제안방향',
                'type' => 'text',
                'col' => 14,
                'class' => 'text-left'
            ],
            'readyContents' => [
                'title' => '고객요청사항(준비물품)',
                'type' => 'text',
                'col' => 14,
                'class' => 'text-left'
            ],
        ]);
    }

    /**
     * 디자인 기획
     */
    public function setStep20(){
        $this->setData('stepItem', [
            /*'bid' => [
                'title' => '입찰',
                'type' => 'text',
                'col' => 4.5,
            ],*/
            'customerDeliveryDtShort' => [
                'title' => '고객납기일',
                'type' => 'text',
                'col' => 6,
                'class' => 'font-13',
                'addKey' => 'customerDeliveryRemainDt',
            ],
            'customerOrderDeadLineShort' => [
                'title' => '발주D/L',
                'type' => 'text',
                'col' => 4,
                'addKey' => 'customerOrderDeadLineRemain',
            ],
            'styleWithCount' => [
                'title' => '스타일',
                'type' => 'style',
                'col' => 10,
            ],
            'prdExQty' => [
                'title' => '수량',
                'type' => 'number',
                'col' => 3,
            ],
            'recommendIcon' => [
                'title' => '제안형태',
                'type' => 'text',
                'col' => 4.5,
            ],
            'planDtShort' => [
                'title' => '기획완료 예정',
                'type' => 'text',
                'col' => 4,
                'addKey' => 'planDtRemain',
                'class' => 'font-16',
            ],
            /*'planEndDtShort' => [
                'title' => '기획완료일',
                'type' => 'text',
                'col' => 4.5,
            ],*/
            'planConfirmKr' => [
                'title' => '기획승인',
                'type' => 'text',
                'col' => 2.5,
                'class' => 'font-13',
                'textClass' => 'planConfirmClass',
            ],
            'recommendDtShort' => [
                'title' => '고객제안마감일',
                'titleStyle' => 'background-color: #0a6aa1!important;',
                'type' => 'text',
                'col' => 4,
                'addKey' => 'recommendRemainDt',
                'class' => 'font-16',
            ],
            'planMemo' => [
                'title' => '비고',
                'type' => 'comment',
                'col' => 3,
            ],
        ]);
    }
    public function setStep20Style(){
        $this->setData('stepItem', [
            'fileThumbnail' => [
                'title' => '스타일이미지',
                'type' => 'img',
                'col' => 4.5,
            ],
            'productName' => [
                'title' => '스타일',
                'type' => 'style',
                'col' => 10,
                'addKey' => 'styleCode'
            ],
            'prdExQty' => [
                'title' => '수량',
                'type' => 'number',
                'col' => 3,
            ],
            'currentPrice' => [
                'title' => '현재단가',
                'type' => 'number',
                'col' => 4.5,
            ],
            'targetPrice' => [
                'title' => '타겟단가',
                'type' => 'number',
                'col' => 4.5,
            ],
            'reqCost' => [
                'title' => '타겟생산가',
                'type' => 'number',
                'col' => 4.5,
            ],
            'marginPercent' => [
                'title' => '타겟마진',
                'type' => 'percent',
                'col' => 3,
            ],
            'customerDeliveryDtShort' => [
                'title' => '고객납기일',
                'type' => 'text',
                'col' => 4.5,
                'class' => 'font-14',
            ],
            'customerDeliveryRemainDt' => [
                'title' => '납기 D-day',
                'type' => 'text',
                'col' => 4.5,
                'class' => 'font-14 text-danger'
            ],
            'planDtShort' => [
                'title' => '기획예정일',
                'type' => 'text',
                'col' => 4.5,
                'addKey' => 'planDtRemain'
            ],
            'planEndDtShort' => [
                'title' => '기획완료일',
                'type' => 'text',
                'col' => 4.5,
            ],
            'planConfirmKr' => [
                'title' => '기획승인',
                'type' => 'text',
                'col' => 4.5,
                'class' => 'font-16',
            ],
        ]);
    }

    /**
     * 디자인 제안
     */
    public function setStep30(){
        $this->setData('stepItem', [
            'customerDeliveryDtShort' => [
                'title' => '고객납기일',
                'type' => 'text',
                'col' => 5.5,
                'class' => 'font-13',
                'addKey' => 'customerDeliveryRemainDt',
            ],
            'customerOrderDeadLineShort' => [
                'title' => '발주D/L',
                'type' => 'text',
                'col' => 5.5,
                'addKey' => 'customerOrderDeadLineRemain',
            ],
            'styleWithCount' => [
                'title' => '스타일',
                'type' => 'style',
                'col' => 10,
            ],
            'prdExQty' => [
                'title' => '수량',
                'type' => 'number',
                'col' => 3,
            ],
            'recommendIcon' => [
                'title' => '제안형태',
                'type' => 'text',
                'col' => 4.5,
            ],
            'proposalDtShort' => [
                'title' => '제안완료 예정',
                'type' => 'text',
                'col' => 3,
                'addKey' => 'proposalDtRemain',
                'class' => 'font-16',
            ],
            'proposalConfirmKr' => [
                'title' => '승인',
                'type' => 'text',
                'col' => 3.5,
                'class' => 'font-13',
                'textClass' => 'proposalConfirmClass',
            ],
            'recommendDtShort' => [
                'title' => '고객제안마감일',
                'titleStyle' => 'background-color: #0a6aa1!important;',
                'type' => 'text',
                'col' => 4,
                'addKey' => 'recommendRemainDt',
                'class' => 'font-13',
            ],
            
            'proposalMemo' => [
                'title' => '비고',
                'type' => 'comment',
                'col' => 3,
            ],
        ]);
    }
    public function setStep30Style(){
        $this->setData('stepItem', [
            'fileThumbnail' => [
                'title' => '스타일이미지',
                'type' => 'img',
                'col' => 4.5,
            ],
            'productName' => [
                'title' => '스타일',
                'type' => 'style',
                'col' => 10,
                'addKey' => 'styleCode'
            ],
            'prdExQty' => [
                'title' => '수량',
                'type' => 'number',
                'col' => 3,
            ],
            'currentPrice' => [
                'title' => '현재단가',
                'type' => 'number',
                'col' => 4.5,
            ],
            'targetPrice' => [
                'title' => '타겟단가',
                'type' => 'number',
                'col' => 4.5,
            ],
            'reqCost' => [
                'title' => '타겟생산가',
                'type' => 'number',
                'col' => 4.5,
            ],
            'marginPercent' => [
                'title' => '타겟마진',
                'type' => 'percent',
                'col' => 3,
            ],
            'customerDeliveryDtShort' => [
                'title' => '고객납기일',
                'type' => 'text',
                'col' => 4.5,
                'class' => 'font-14',
            ],
            'customerDeliveryRemainDt' => [
                'title' => '납기 D-day',
                'type' => 'text',
                'col' => 4.5,
                'class' => 'font-14 text-danger'
            ],
            'proposalDtShort' => [
                'title' => '제안서 예정일',
                'type' => 'text',
                'col' => 4.5,
                'addKey' => 'proposalDtRemain'
            ],
            'proposalEndDtShort' => [
                'title' => '제안서 완료일',
                'type' => 'text',
                'col' => 4.5,
            ],
            'proposalConfirmKr' => [
                'title' => '제안서 승인',
                'type' => 'text',
                'col' => 4.5,
                'class' => 'font-16',
            ],
        ]);
    }

    /**
     * 디자인 샘플
     */
    public function setStep40(){
        $this->setData('stepItem', [
            'customerDeliveryDtShort' => [
                'title' => '고객납기일',
                'type' => 'text',
                'col' => 6,
                'class' => 'font-13',
                'addKey' => 'customerDeliveryRemainDt',
            ],
            'customerOrderDeadLineShort' => [
                'title' => '발주D/L',
                'type' => 'text',
                'col' => 3.5,
                'addKey' => 'customerOrderDeadLineRemain',
            ],
            'styleWithCount' => [
                'title' => '스타일',
                'type' => 'style',
                'col' => 10,
            ],
            'prdExQty' => [
                'title' => '수량',
                'type' => 'number',
                'col' => 3,
            ],
            'recommendIcon' => [
                'title' => '제안형태',
                'type' => 'text',
                'col' => 4.5,
            ],
            'sampleDtShort' => [
                'title' => '샘플 완료 예정일',
                'type' => 'text',
                'col' => 4.5,
                'addKey' => 'sampleDtRemain',
                'class' => 'font-16',
            ],
            'sampleConfirmKr' => [
                'title' => '승인',
                'type' => 'text',
                'col' => 4,
                'class' => 'font-16',
                'textClass' => 'sampleConfirmClass',
            ],
            'recommendDtShort' => [
                'title' => '고객제안마감일',
                'titleStyle' => 'background-color: #0a6aa1!important;',
                'type' => 'text',
                'col' => 4,
                'addKey' => 'recommendRemainDt',
                'class' => 'font-16',
            ],
            'sampleMemo' => [
                'title' => '비고',
                'type' => 'comment',
                'col' => 3,
            ],
        ]);
    }
    public function setStep40Style(){
        $this->setData('stepItem', [
            'fileThumbnail' => [
                'title' => '스타일이미지',
                'type' => 'img',
                'col' => 4.5,
            ],
            'productName' => [
                'title' => '스타일',
                'type' => 'style',
                'col' => 10,
                'addKey' => 'styleCode'
            ],
            'prdExQty' => [
                'title' => '수량',
                'type' => 'number',
                'col' => 3,
            ],
            'currentPrice' => [
                'title' => '현재단가',
                'type' => 'number',
                'col' => 4.5,
            ],
            'targetPrice' => [
                'title' => '타겟단가',
                'type' => 'number',
                'col' => 4.5,
            ],
            'reqCost' => [
                'title' => '타겟생산가',
                'type' => 'number',
                'col' => 4.5,
            ],
            'marginPercent' => [
                'title' => '타겟마진',
                'type' => 'percent',
                'col' => 3,
            ],
            'customerDeliveryDtShort' => [
                'title' => '고객납기일',
                'type' => 'text',
                'col' => 4.5,
                'class' => 'font-14',
            ],
            'customerDeliveryRemainDt' => [
                'title' => '납기 D-day',
                'type' => 'text',
                'col' => 4.5,
                'class' => 'font-14 text-danger'
            ],
            'sampleDtShort' => [
                'title' => '샘플 예정일',
                'type' => 'text',
                'col' => 4.5,
                'addKey' => 'sampleDtRemain'
            ],
            'sampleEndDtShort' => [
                'title' => '샘플 완료일',
                'type' => 'text',
                'col' => 4.5,
            ],
            'sampleConfirmKr' => [
                'title' => '샘플 승인',
                'type' => 'text',
                'col' => 4.5,
                'class' => 'font-16',
            ],
        ]);
    }

    /**
     * 고객승인대기
     */
    public function setStep50(){
        $this->setData('stepItem', [
            'customerDeliveryDtShort' => [
                'title' => '고객납기일',
                'type' => 'text',
                'col' => 6,
                'class' => 'font-13',
                'addKey' => 'customerDeliveryRemainDt',
            ],
            'customerOrderDeadLineShort' => [
                'title' => '발주D/L',
                'type' => 'text',
                'col' => 4,
                'addKey' => 'customerOrderDeadLineRemain',
            ],
            'customerDeliveryRemainDt' => [
                'title' => '납기 D-day',
                'type' => 'text',
                'col' => 6.5,
                'class' => 'font-13 text-danger'
            ],
            'styleWithCount' => [
                'title' => '스타일',
                'type' => 'style',
                'col' => 10,
            ],
            'prdExQty' => [
                'title' => '수량',
                'type' => 'number',
                'col' => 3,
            ],
            'recommendIcon' => [
                'title' => '제안형태',
                'type' => 'text',
                'col' => 4.5,
            ],
            'customerEstimateConfirmKr' => [
                'title' => '견적확정',
                'type' => 'text',
                'col' => 4,
                'class' => 'font-16',
            ],
            'customerWaitDtRemain' => [
                'title' => '대기일자',
                'type' => 'text',
                'col' => 4.5,
                'class' => 'font-15',
            ],
            'customerWaitMemo' => [
                'title' => '비고',
                'type' => 'comment',
                'col' => 3,
            ],
        ]);
    }
    public function setStep50Style(){
        $this->setData('stepItem', [
            'fileThumbnail' => [
                'title' => '스타일이미지',
                'type' => 'img',
                'col' => 4.5,
            ],
            'productName' => [
                'title' => '스타일',
                'type' => 'style',
                'col' => 10,
                'addKey' => 'styleCode'
            ],
            'prdExQty' => [
                'title' => '수량',
                'type' => 'number',
                'col' => 3,
            ],
            'currentPrice' => [
                'title' => '현재단가',
                'type' => 'number',
                'col' => 4.5,
            ],
            'targetPrice' => [
                'title' => '타겟단가',
                'type' => 'number',
                'col' => 4.5,
            ],
            'reqCost' => [
                'title' => '타겟생산가',
                'type' => 'number',
                'col' => 4.5,
            ],
            'marginPercent' => [
                'title' => '타겟마진',
                'type' => 'percent',
                'col' => 3,
            ],
            'customerDeliveryDtShort' => [
                'title' => '고객납기일',
                'type' => 'text',
                'col' => 4.5,
                'class' => 'font-14',
            ],
            'customerDeliveryRemainDt' => [
                'title' => '납기 D-day',
                'type' => 'text',
                'col' => 4.5,
                'class' => 'font-14 text-danger'
            ],
            'customerEstimateConfirmKr' => [
                'title' => '견적확정',
                'type' => 'text',
                'col' => 4,
                'class' => 'font-16',
            ],
            'customerWaitDtRemain' => [
                'title' => '대기일자',
                'type' => 'text',
                'col' => 4.5,
                'class' => 'font-16',
            ],
        ]);
    }

    /**
     * 발주 사양서
     */
    public function setStep60(){
        $this->setData('stepItem', [
            'customerDeliveryDtShort' => [
                'title' => '고객납기일',
                'type' => 'text',
                'col' => 6,
                'class' => 'font-13',
                'addKey' => 'customerDeliveryRemainDt',
            ],
            'customerOrderDeadLineShort' => [
                'title' => '발주D/L',
                'type' => 'text',
                'col' => 5,
                'addKey' => 'customerOrderDeadLineRemain',
            ],
            'styleWithCount' => [
                'title' => '스타일',
                'type' => 'style',
                'col' => 10,
            ],
            'prdExQty' => [
                'title' => '수량',
                'type' => 'number',
                'col' => 3,
            ],
            'recommendIcon' => [
                'title' => '제안형태',
                'type' => 'text',
                'col' => 4.5,
            ],
            'customerOrder2ConfirmKr' => [
                'title' => '고객발주확정',
                'type' => 'text',
                'col' => 3,
                'class' => 'font-13',
                'addKey' => 'customerOrder2ConfirmDtShort'
            ],
            'customerSaleConfirmKr' => [
                'title' => '판매구매',
                'type' => 'text',
                'col' => 3,
                'class' => 'font-13',
                'addKey' => 'customerSaleConfirmDtShort'
            ],
            'customerOrderConfirmKr' => [
                'title' => '사양서확정',
                'type' => 'text',
                'col' => 3,
                'class' => 'font-13',
                'addKey' => 'customerOrderConfirmDtShort'
            ],
            'workMemo' => [
                'title' => '비고',
                'type' => 'comment',
                'col' => 3,
            ],
        ]);
    }
    public function setStep60Style(){
        $this->setData('stepItem', [
            'fileThumbnail' => [
                'title' => '스타일이미지',
                'type' => 'img',
                'col' => 4.5,
            ],
            'productName' => [
                'title' => '스타일',
                'type' => 'style',
                'col' => 10,
                'addKey' => 'styleCode'
            ],
            'prdExQty' => [
                'title' => '수량',
                'type' => 'number',
                'col' => 3,
            ],
            'currentPrice' => [
                'title' => '현재단가',
                'type' => 'number',
                'col' => 4.5,
            ],
            'targetPrice' => [
                'title' => '타겟단가',
                'type' => 'number',
                'col' => 4.5,
            ],
            'reqCost' => [
                'title' => '타겟생산가',
                'type' => 'number',
                'col' => 4.5,
            ],
            'marginPercent' => [
                'title' => '타겟마진',
                'type' => 'percent',
                'col' => 3,
            ],
            'customerDeliveryDtShort' => [
                'title' => '고객납기일',
                'type' => 'text',
                'col' => 4.5,
                'class' => 'font-14',
            ],
            'customerDeliveryRemainDt' => [
                'title' => '납기 D-day',
                'type' => 'text',
                'col' => 4.5,
                'class' => 'font-14 text-danger'
            ],
            'customerEstimateConfirmKr' => [
                'title' => '견적확정',
                'type' => 'text',
                'col' => 4,
                'class' => 'font-16',
            ],
            'customerWaitDtRemain' => [
                'title' => '대기일자',
                'type' => 'text',
                'col' => 4.5,
                'class' => 'font-16',
            ],
        ]);
    }

    public function setStep98(){
        $this->setStepCommon();
    }
    public function setStep98Style(){
        $this->setStepCommonStyle();
    }
    public function setStep99(){
        $this->setStepCommon();
    }
    public function setStep99Style(){
        $this->setStepCommonStyle();
    }

}