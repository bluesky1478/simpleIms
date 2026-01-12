<?php

namespace Controller\Admin\Ims;

use App;
use Component\Category\BrandAdmin;
use Component\Category\CategoryAdmin;
use Component\Ims\ImsCodeMap;
use Component\Stock\StockListService;
use Component\Work\WorkCodeMap;
use Controller\Admin\Erp\AdminErpControllerTrait;
use Controller\Admin\Erp\ControllerService\InoutListService;
use Globals;
use Request;
use SiteLabUtil\SlCommonUtil;
use SlComponent\Database\DBUtil2;
use SlComponent\Util\ExcelCsvUtil;
use SlComponent\Util\SlCodeMap;
use SlComponent\Util\SlLoader;
use function SlComponent\Util\SlLoader;

/**
 * 프로젝트 리스트
 */
class ImsSpecialListController extends \Controller\Admin\Controller{

    use ImsControllerTrait;

    public function index(){
        $request=\Request::get()->toArray();
        //$this->setProjectListRelatedController($request);

        $this->callMenu('ims', 'customer', '24fw');


        $this->setDefault();

        $requestParam = $this->getData('requestParam');

        $this->setData('targetPage','ims_project_view.php');
        $this->setData('regBtnName','프로젝트 등록');
        
        if(empty($request['status'])){
            $stepFncName = 'setStepCommon';
        }else{
            $stepFncName = 'set'.ucfirst($request['status']);
        }

        $controllerListService = SlLoader::controllerServiceLoad(__NAMESPACE__,'imsProjectList');
        if( 'style' === $requestParam['view'] ){
            $stepFncName .= 'Style';
        }

        $this->$stepFncName();


        $listService = SlLoader::cLoad('godo','listService','sl');
        $listService->setList($controllerListService, $this);

        if(  !empty($getValue['simple_excel_download'])  ){
            $this->simpleExcelDownload($this->getData('data'));
            exit();
        }

        $this->getView()->setPageName("ims/list/ims_project_list.php");

    }

    public function simpleExcelDownload($getData){
        $stepItem = $this->getData('stepItem');
        $titles = [
            '번호',
            '프로젝트타입',
            '프로젝트번호',
            '연도',
            '시즌',
            '고객사',
            '특이사항',
            '고객납기일',
        ];

        foreach ($stepItem as $stepKey => $stepValue) {
            $titles[] = $stepValue['title'];
        }

        $titles[] = '프로젝트상태';
        $titles[] = '생산상태';
        $titles[] = '퀄리티수배';
        $titles[] = 'BT';
        $titles[] = '가견적';
        $titles[] = '생산가';
        $titles[] = '판매가';
        $titles[] = '작지작업';
        $titles[] = '매출규모';
        $titles[] = '영업담당자';
        $titles[] = '디자인담당자';
        $titles[] = '등록일';
        $titles[] = '수정일';

        $data = $getData['data'];
        $page = $getData['page'];
        //gd_debug($page);
        //gd_debug($data);
        $excelBody = '';
        foreach ($data as $key => $val) {

            $fieldData = array();
            $fieldData[] = ExcelCsvUtil::wrapTd($page->idx--);
            $fieldData[] = ExcelCsvUtil::wrapTd($val['projectTypeKr']);
            $fieldData[] = ExcelCsvUtil::wrapTd($val['projectNo']);
            $fieldData[] = ExcelCsvUtil::wrapTd($val['projectYear']);
            $fieldData[] = ExcelCsvUtil::wrapTd($val['projectSeason']);
            $fieldData[] = ExcelCsvUtil::wrapTd($val['customerName']);
            $fieldData[] = ExcelCsvUtil::wrapTd($val['projectName']);
            $fieldData[] = ExcelCsvUtil::wrapTd($val['customerDeliveryDtShort']);

            $iCnt = 0;
            foreach ($stepItem as $stepKey => $stepValue) {
                //스타일
                if('style' === $stepValue['type']) {
                    $iCnt++;
                    if( empty($val[$stepKey])) {
                        $fieldData[] = ExcelCsvUtil::wrapTd('스타일 미등록');
                    }else{
                        $fieldData[] = ExcelCsvUtil::wrapTd($val[$stepKey]);
                    }
                }
                
                //일반 텍스트
                if('text' === $stepValue['type']) {
                    $iCnt++;
                    if( empty($val[$stepKey]) && 'styleWithCount' === $stepKey ) {
                        $fieldData[] = ExcelCsvUtil::wrapTd('스타일 미등록');
                    }else{
                        $textData = str_replace('\\','',nl2br($val[$stepKey]));
                        if( !empty($val[$stepValue['addKey']]) ){
                            $textData .= '<br>' . $val[$stepValue['addKey']];
                        }
                        $fieldData[] = ExcelCsvUtil::wrapTd($textData);
                    }
                }

                //숫자
                if('number' === $stepValue['type']) {
                    $iCnt++;
                    $fieldData[] = ExcelCsvUtil::wrapTd(number_format($val[$stepKey]));
                }

                //퍼센트
                if('percent' === $stepValue['type']) {
                    $iCnt++;
                    $fieldData[] = ExcelCsvUtil::wrapTd(round($val[$stepKey]).'%');
                }

                //이미지
                if('img' === $stepValue['type']) {
                    $iCnt++;
                    $fieldData[] = ExcelCsvUtil::wrapTd('[이미지]');
                }
                //gd_debug($iCnt);
            }

            $fieldData[] = ExcelCsvUtil::wrapTd($val['projectStatusKr']);
            $fieldData[] = ExcelCsvUtil::wrapTd($val['productionStatusIcon']);
            $fieldData[] = ExcelCsvUtil::wrapTd(ImsCodeMap::IMS_PRD_PROC_STATUS[$val['fabricStatus']]);
            $fieldData[] = ExcelCsvUtil::wrapTd(ImsCodeMap::IMS_PRD_PROC_STATUS[$val['btStatus']]);
            $fieldData[] = ExcelCsvUtil::wrapTd(ImsCodeMap::IMS_PRD_PROC_STATUS[$val['estimateStatus']]);
            $fieldData[] = ExcelCsvUtil::wrapTd(ImsCodeMap::IMS_PRD_PROC_STATUS[$val['costStatus']]);
            $fieldData[] = ExcelCsvUtil::wrapTd(ImsCodeMap::IMS_PRD_PROC_STATUS[$val['priceStatus']]);
            $fieldData[] = ExcelCsvUtil::wrapTd(ImsCodeMap::IMS_PRD_PROC_STATUS[$val['workStatus']]);

            $fieldData[] = ExcelCsvUtil::wrapTd($val['customerSize']);
            $fieldData[] = ExcelCsvUtil::wrapTd($val['salesManagerNm']);
            $fieldData[] = ExcelCsvUtil::wrapTd($val['designManagerNm']);

            $fieldData[] = ExcelCsvUtil::wrapTd(gd_date_format('y/m/d',$val['regDt']));
            $fieldData[] = ExcelCsvUtil::wrapTd(gd_date_format('y/m/d',$val['modDt']));

            //$fieldData[] = ExcelCsvUtil::wrapTd($val['memo'],'text','mso-number-format:\'\@\'');
            $excelBody .=  "<tr>". implode('',$fieldData) . "</tr>";
        }

        $simpleExcelComponent = SlLoader::cLoad('Excel','SimpleExcelComponent','sl');
        $simpleExcelComponent->simpleDownload('프로젝트리스트',$titles,$excelBody);
    }


    /**
     * 공통
     */
    public function setStepCommon(){
        $this->setData('stepItem', [
/*            'customerDeliveryDtShort' => [
                'title' => '고객납기일',
                'type' => 'text',
                'col' => 6,
                'class' => 'font-13',
                'addKey' => 'customerDeliveryRemainDt',
            ],*/
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
            /*'workMemo' => [
                'title' => '코멘트',
                'type' => 'comment',
                'col' => 3,
            ],*/
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
            /*'customerDeliveryDtShort' => [
                'title' => '고객납기일',
                'type' => 'text',
                'col' => 4.5,
                'class' => 'font-14',
            ],*/
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

    public function setStep90(){
        $this->setStepCommon();
    }
    public function setStep90Style(){
        $this->setStepCommonStyle();
    }


    /**
     * 구버전 리스트
     */
    public function setOld(){
        $this->setStepCommon();
    }
    public function setOldStyle(){
        $this->setStepCommonStyle();
    }
    
    /**
     * 미팅준비
     */
    public function setStep10(){
        $this->setStepCommon();
    }
    public function setStep10Style(){
        $this->setStepCommonStyle();
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
            /*'customerDeliveryDtShort' => [
                'title' => '고객납기일',
                'type' => 'text',
                'col' => 6,
                'class' => 'font-13',
                'addKey' => 'customerDeliveryRemainDt',
            ],*/
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
            /*'planMemo' => [
                'title' => '코멘트',
                'type' => 'comment',
                'col' => 3,
            ],*/
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
            /*'customerDeliveryDtShort' => [
                'title' => '고객납기일',
                'type' => 'text',
                'col' => 4.5,
                'class' => 'font-14',
            ],*/
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
            /*'customerDeliveryDtShort' => [
                'title' => '고객납기일',
                'type' => 'text',
                'col' => 5.5,
                'class' => 'font-13',
                'addKey' => 'customerDeliveryRemainDt',
            ],*/
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
            /*'proposalMemo' => [
                'title' => '코멘트',
                'type' => 'comment',
                'col' => 3,
            ],*/
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
            /*'customerDeliveryDtShort' => [
                'title' => '고객납기일',
                'type' => 'text',
                'col' => 4.5,
                'class' => 'font-14',
            ],*/
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
            /*'customerDeliveryDtShort' => [
                'title' => '고객납기일',
                'type' => 'text',
                'col' => 6,
                'class' => 'font-13',
                'addKey' => 'customerDeliveryRemainDt',
            ],*/
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
            /*'sampleMemo' => [
                'title' => '코멘트',
                'type' => 'comment',
                'col' => 3,
            ],*/
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
            /*'customerDeliveryDtShort' => [
                'title' => '고객납기일',
                'type' => 'text',
                'col' => 4.5,
                'class' => 'font-14',
            ],*/
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
            /*'customerDeliveryDtShort' => [
                'title' => '고객납기일',
                'type' => 'text',
                'col' => 6,
                'class' => 'font-13',
                'addKey' => 'customerDeliveryRemainDt',
            ],*/
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
            /*'customerWaitMemo' => [
                'title' => '코멘트',
                'type' => 'comment',
                'col' => 3,
            ],*/
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
            /*'customerDeliveryDtShort' => [
                'title' => '고객납기일',
                'type' => 'text',
                'col' => 4.5,
                'class' => 'font-14',
            ],*/
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
            /*'customerDeliveryDtShort' => [
                'title' => '고객납기일',
                'type' => 'text',
                'col' => 6,
                'class' => 'font-13',
                'addKey' => 'customerDeliveryRemainDt',
            ],*/
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
            /*'workMemo' => [
                'title' => '코멘트',
                'type' => 'comment',
                'col' => 3,
            ],*/
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
            /*'customerDeliveryDtShort' => [
                'title' => '고객납기일',
                'type' => 'text',
                'col' => 4.5,
                'class' => 'font-14',
            ],*/
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