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
use Controller\Admin\Ims\Step\ImsStepTrait;
use Globals;
use Request;
use SiteLabUtil\SlCommonUtil;
use SlComponent\Database\DBUtil2;
use SlComponent\Util\ExcelCsvUtil;
use SlComponent\Util\SitelabLogger;
use SlComponent\Util\SlCodeMap;
use SlComponent\Util\SlLoader;
use function SlComponent\Util\SlLoader;

/**
 * 영업 리스트 25년 10월 Version
 */
class Ims25ListSalesController extends \Controller\Admin\Controller{

    use ImsControllerTrait;
    use ImsStepTrait;
    use ImsListControllerTrait;

    public function index(){
        $this->callMenu('ims', 'list', 'sales');
        $this->setData('current', 'sales');
        $this->setDefault();

        //검색 조건 설정 (기본)
        $search['combineSearch'] = [
            'cust.customerName' => '고객명',
            'prj.sno' => '프로젝트번호',
            'sales.managerNm' => '영업담당자',
            'desg.managerNm' => '디자인담당자',
            'prd.styleCode' => '스타일코드',
            'prd.productName' => '스타일명',
        ];
        $this->setData('search', $search);
        $this->setEmergencyTodoList();

        //집계 ( 나중에 다른곳에 필요하면 서비스로 넘기기 )
        $calcSaleStatus = DBUtil2::runSelect("
        SELECT
            SUM(CASE WHEN projectType in ( 0,2,5,6,8 ) AND projectStatus = '10' THEN 1 ELSE 0 END) AS wait,
            SUM(CASE WHEN projectType in ( 0,2,5,6,8 ) AND projectStatus = '15' THEN 1 ELSE 0 END) AS plan,
            SUM(CASE WHEN projectType in ( 0,2,5,6,8 ) AND projectStatus IN ('20','30','31','40','41','50','60') THEN 1 ELSE 0 END) AS proc
        FROM sl_imsProject a join sl_imsCustomer b on a.customerSno = b.sno
        WHERE projectStatus in (10,15,20,30,31,40,41,50,60) 
        ");
        $this->setData('calcSaleStatus', $calcSaleStatus[0]);


        //imsPageReload : 기본셋
        $request = Request::get()->toArray();
        if(  !empty($request['simple_excel_download']) &&  $request['simple_excel_download'] == 1 ){
            $request['page'] = 1;
            $request['pageNum'] = 15000;
            $request['multiKey'] = json_decode($request['multiKey'], true);
            $request['projectTypeChk'] = json_decode($request['projectTypeChk'], true);
            $request['salesStatusChk'] = json_decode($request['salesStatusChk'], true);
            $request['orderProgressChk'] = json_decode($request['orderProgressChk'], true);
            $request['delayStatus'] = json_decode($request['delayStatus'], true);
            $request['viewType'] = 'style';
            unset($request['simple_excel_download']);

            $this->simpleExcelDownload($request);
            exit();
        }
    }

    //다운로드
    public function simpleExcelDownload($request){
        //데이터 가져오기, default 작업
        $imsProjectListService = SlLoader::cLoad('imsv2','ImsProjectListService');
        $aResult = $imsProjectListService->getAllList($request);
        $aList = $aResult['list'];
        $aFlds = [
            'sno'=>'번호', 'customerName'=>'고객명', 'projectTypeKr'=>'프로젝트타입', 'assortApproval'=>'아소트확정', 'customerOrderConfirm'=>'사양서확정',
            'prdYear'=>'생산년도', 'prdSeason'=>'시즌', 'productName'=>'제품명', 'styleCode'=>'스타일 코드', 'styleSno'=>'스타일 넘버',
            'produceCompanyName'=>'생산처', 'prdFabricStatusKr'=>'원단처리상태','prdBtStatusKr'=>'BT처리상태', 'prdCustomerDeliveryDt'=>'고객납기','prdMsDeliveryDt'=>'MS납기',
            'exProductionOrder'=>'발주예정일', 'cpProductionOrder'=>'발주완료일', 'prdPeriod'=>'생산기간', 'prdExQty'=>'수량', 'repFabric'=>'대표원단',
            'prdMoq'=>'생산 MOQ','priceMoq'=>'단가 MOQ', 'priceConfirm'=>'판매가승인', 'salePrice'=>'판매가', 'totalCost'=>'생산가', 'margin'=>'마진',
        ];
        //$aFlds에서 필드 다 정리해서 아래 라인 실행할 필요없음
//        foreach ($aResult['fieldData'] as $val) {
//            $aFlds[$val['name']] = $val['title'];
//        }
        $title = [];
        foreach($aFlds as $key => $val) $title[] = $val;

        //리스트의 데이터 정제
        foreach($aList as $key => $val) {
            $aList[$key]['sno'] = $key+1;
            $aList[$key]['assortApproval'] = $val['assortApproval'] === 'p' ? '확정' : ''; //아소트확정
            $aList[$key]['customerOrderConfirm'] = $val['customerOrderConfirm'] === 'p' ? '확정' : ''; //사양서확정
            if ($val['isReorder'] === 'y') {
                $aList[$key]['prdFabricStatusKr'] = $aList[$key]['prdBtStatusKr'] = '해당 없음'; //원단처리상태 and BT처리상태
            }
            $aList[$key]['prdExQty'] = number_format($val['prdExQty']); //수량
            $aList[$key]['repFabric'] = !empty($val['repFabric']) ? $val['repFabric'] : '미정'; //대표원단
            if (!empty($val['estimateData'])) {
                $aList[$key]['prdPeriod'] = !empty($val['prdPeriod']) ? $val['prdPeriod'] : '생산처 미입력'; //생산기간
                $aList[$key]['prdMoq'] = number_format($val['estimateData']['prdMoq']); //생산MOQ
                $aList[$key]['priceMoq'] = number_format($val['estimateData']['priceMoq']); //단가MOQ
            } else {
                $aList[$key]['prdPeriod'] = $aList[$key]['prdMoq'] = $aList[$key]['priceMoq'] = '미정';
            }
            if ((int)$val['salePrice'] > 0) {
                $aList[$key]['priceConfirm'] = $val['priceConfirm'] === 'p' ? '확정' : ''; //판매가 승인
                $aList[$key]['salePrice'] = number_format($val['salePrice']).'원'; //판매가
            } else {
                $aList[$key]['priceConfirm'] = $aList[$key]['salePrice'] = '확인중';
            }
            if (!empty($val['estimateData']) && ($val['prdCostConfirmSno'] > 0 || $val['estimateConfirmSno'] > 0)) { //생산가
                if ($val['estimateConfirmSno'] > 0 && $val['prdCostConfirmSno'] <= 0) $aList[$key]['totalCost'] = '(가)';
                else $aList[$key]['totalCost'] = '';
                $aList[$key]['totalCost'] .= number_format($val['estimateData']['totalCost']).'원';
            } else {
                $aList[$key]['totalCost'] = '선택 견적 없음';
            }
            $aList[$key]['margin'] = number_format($val['margin']).'%'; //마진
        }
        //엑셀파일 만들기
        foreach($aList as $val) {
            $contentsRows = [];
            foreach($aFlds as $key2 => $val2) {
                $contentsRows[] = ExcelCsvUtil::wrapTd($val[$key2]);
            }
            $contents[] = '<tr>'.implode('',$contentsRows).'</tr>';
        }
        $simpleExcelComponent = SlLoader::cLoad('Excel','SimpleExcelComponent','sl');
        $simpleExcelComponent->simpleDownload('프로젝트리스트', $title, implode('',$contents));
    }

}