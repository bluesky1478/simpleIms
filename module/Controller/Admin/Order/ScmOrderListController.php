<?php

namespace Controller\Admin\Order;

use App;
use Component\Order\OrderPolicyListService;
use Component\Scm\ScmOrderListService;
use Controller\Admin\Ims\ImsControllerTrait;
use Exception;
use Component\Category\CategoryAdmin;
use Component\Category\BrandAdmin;
use Globals;
use Request;
use SiteLabUtil\SlCommonUtil;
use SlComponent\Database\DBUtil;
use SlComponent\Database\DBUtil2;
use SlComponent\Util\ExcelCsvUtil;
use SlComponent\Util\SitelabLogger;
use SlComponent\Util\SlCodeMap;
use SlComponent\Util\SlLoader;
use function SlComponent\Util\SlLoader;
use Component\Member\Manager;
use Framework\Utility\DateTimeUtils;
use Component\Storage\Storage;
use Framework\StaticProxy\Proxy\FileHandler;
use UserFilePath;
use Framework\Debug\Exception\AlertOnlyException;

class ScmOrderListController extends \Controller\Admin\Controller{

    public function index(){

        $this->addScript([
            '../../script/vue.js',
            '../../script/select2/js/select2.js',
            '../../script/datepicker/daterangepicker.js',
        ]);

        $this->addCss([
            '../../css/preloader.css',
            '../../css/font_awesome/css/font-awesome.css',
            '../../css/admin-ims.css?ver='.time(),
            '../../script/select2/css/select2.css',
            '../../script/datepicker/daterangepicker.css',
        ]);


        $getValue = Request::get()->toArray();

        //공급사 리스트
        $scmAdmin = \App::load(\Component\Scm\ScmAdmin::class);
        $scmList = $scmAdmin->getSelectScmList();
        $refineScmList = array();
        $firstScmNo = 0;
        $firstScmName = '';
        $isFirst = true;
        foreach( $scmList as $key => $val ){
            //if( true === SlCodeMap::SCM_USE_ORDER_ACCEPT_ [$key] ){
            //if( true === SlCommonUtil::getIsOrderAccept($key) ){
                $refineScmList[$key] = $val;
                if(true === $isFirst){
                    $firstScmNo = $key;
                    $firstScmName = $val;
                    $isFirst = false;
                }
            //}
        }

        $isProvider = Manager::isProvider();
        if( $isProvider ){
            $this->callMenu('statistics', 'accept', 'order');
            $scmNo = \Session::get('manager.scmNo');
            $companyNm = \Session::get('manager.companyNm');
            $this->setData('scmConfig', DBUtil2::getOne('sl_setScmConfig','scmNo',$scmNo));
        }else{
            $this->callMenu('order', 'order', 'sorder');
            $scmNo = empty($getValue['scmNo'][0]) ? $firstScmNo : $getValue['scmNo'][0];
            $companyNm = empty($getValue['scmNo'][0]) ?  $firstScmName : $scmList[$getValue['scmNo'][0]];
        }
        $this->setData('scmList', $refineScmList);
        $this->setData('isProvider', $isProvider);
        $this->setData('scmNo', $scmNo);
        $this->setData('companyNm', $companyNm);

        $this->addScript([
            'jquery/jquery.multi_select_box.js',
        ]);

        $scmOrderListService = SlLoader::cLoad('Scm','ScmOrderListService');

        $getValue = SlCommonUtil::getRefineValueAndExcelDownCheck($getValue); //미사용
        //gd_debug($getValue);
        $getValue['scmFl'] =  1;
        $getValue['scmNo'][] =  $scmNo;
        $getValue['scmNoNm'][] =  $companyNm;
        //gd_debug($getValue);

        $getData = $scmOrderListService->getList($getValue);
        //gd_debug($getData);
        if(  !empty($getValue['simple_excel_download'])  ){
            if( 3 == $getValue['downType'] ){
                $this->simpleExcelDownload3($getData);
            }else if( 4 == $getValue['downType'] ){
                $this->simpleExcelDownload4($getData);
            }else if( 5 == $getValue['downType'] ){
                $this->simpleExcelDownload5($getData);
            }else{
                $this->simpleExcelDownload($getData);
            }
            exit();
        }

        //라디오 체크
        $this->setData('checked', $getData['checked']);
        //검색정보
        $this->setData('search', $getData['search']);
        //페이지
        $this->setData('page', $getData['page']);
        //타이틀

        $titles = ScmOrderListService::LIST_TITLES;
        $titlesAsiana = ScmOrderListService::LIST_TITLES_ASIANA;


        if( 'y' === $this->getData('scmConfig')['orderAcceptFl'] || empty($this->getData('isProvider')) ) {
            $titles[] = '출고승인여부';
            $titles[] = '승인일자';
        }

        $this->setData('listTitles',$titles);
        $this->setData('listTitlesAsiana',$titlesAsiana);


        //리스트 데이터
        foreach( $getData['data'] as $key => $value ){
            $orderFileList = DBUtil::getList('sl_orderAttFile','orderNo',$value['orderNo']);
            $value['orderFileList'] = $orderFileList;
            $getData['data'][$key] = $value;
        }

        $this->setData('data',$getData['data']);
        $this->setData('scmNo',$scmNo);
        $this->setData('requestUrl', basename(\Request::getPhpSelf()) .'?simple_excel_download=1&'.  \Request::getQueryString() );
        $this->setData('orderStatusMap',SlCommonUtil::getOrderStatusAllMap());
        $this->getView()->setPageName('order/scm_order_list.php');
    }

    /**
     * 엑셀 다운로드 체크하고 get 값 정제하여 반환
     * @param $getValue
     * @return mixed
     */
    public function getRefineValueAndExcelDownCheck($getValue){
        if(  !empty($getValue['simple_excel_download'])  ){
            $getValue['pageNum'] = 10000;
            $getValue['page'] = 1;
        }
        return $getValue;
    }


    public function simpleExcelDownload($getData){

        $excelTitles = [
            '주문번호' //a.orderNo
            ,'회원명'    //e.memNm
            ,'회원ID'    //e.memId
            ,'닉네임'    //e.nickNm
            ,'주문상품' //goodsNm
            ,'옵션' //- optionStr
            ,'수량' //- goodsCnt
            ,'총 결제금액' //goodsSettlePrice
            ,'총 품목금액' // goodsTotalPrice
            ,'총 배송금액' // goodsDeliveryPrice
            ,'결제방법' // settleKindStr
            ,'주문일자' // a.regDt
            ,'주문자 이름' // b.orderName
            ,'주문자 이메일' // b.orderEmail
            ,'주문자 전화번호' // b.orderPhone
            ,'주문자 핸드폰번호' // b.orderCellPhone
            ,'수취인 이름'// b.receiverName
            ,'수취인 전화번호'// b.receiverPhone
            ,'수취인 핸드폰번호'// b.receiverCellPhone
            ,'수취인 전체주소'// b.receiverAddress + receiverAddressSub
            ,'관리자 전달 메세지' // requestToAdmin
            ,'주문상태'
            ,'배송정보'
        ];

        $scmConfig = $this->getData('scmConfig');
        if( 'y' === $scmConfig['orderAcceptFl'] || empty($this->getData('isProvider')) ){
            $excelTitles[] = '주문승인상태';
        }

        $memberMasking = \App::load('Component\\Member\\MemberMasking');

        $data = $getData['data'];
        //gd_debug($data);
        $excelBody = '';
        $orderStatusMap = SlCommonUtil::getOrderStatusAllMap();

        //데이터 정제
        $excelList = array();
        foreach ($data as $key => $val) {
            $idx = 1;
            foreach(  $val['goodsInfo'] as $key2 => $val2  ){
                /*gd_debug('=======>');
                gd_debug($val['orderNo']);
                gd_debug($val2['goodsNm']);*/
                $val2['goodsIdx'] = $idx;
                $val2['optionStr'] = SlCommonUtil::getRefineOrderGoodsOption($val2['optionInfo']);
                $val2['goodsSettlePrice'] = $val2['taxSupplyGoodsPrice'] + $val2['taxVatGoodsPrice'] + $val2['taxFreeGoodsPrice'];
                $val2['goodsTotalPrice'] = ($val2['goodsCnt'] * $val2['goodsPrice']) + $val2['optionPrice'];
                $val2['invoiceInfo'] = $val2['invoiceCompanyName'] . ' ' . $val2['invoiceNo'];

                if( 1 === $idx ){
                    $val2['goodsDeliveryPrice'] = $val['totalDeliveryCharge'];
                }else{
                    $val2['goodsDeliveryPrice'] = 0;
                }
                $excelList[] = array_merge($val, $val2);
                $idx++;
            }
        }
        //배송비는 첫째 항목에 합친다.
        foreach ($excelList as $key => $val) {
            $dataList[] = $val['goodsPrice'];
            $fieldData = array();
            //주문번호
            $fieldData[] = ExcelCsvUtil::wrapTd($val['orderNo'],'text','mso-number-format:\'\@\'');
            $listKey = [
                'memNm', 'memId', 'nickNm',  'goodsNm' , 'optionStr' , 'goodsCnt', 'goodsSettlePrice', 'goodsTotalPrice', 'goodsDeliveryPrice'
                , 'settleKindStr' , 'regDt' , 'orderName', 'orderEmail','orderPhone','orderCellPhone', 'receiverName', 'receiverPhone', 'receiverCellPhone','receiverFullAddress','requestToAdmin'
                , 'orderStatusStr' , 'invoiceInfo'
            ];
            if( 'y' === $scmConfig['orderAcceptFl'] || empty($this->getData('isProvider'))  ){
                $listKey[] = 'orderAcctStatusStr';
            }

            $numberFormatKey = [
                'goodsCnt', 'goodsSettlePrice','goodsTotalPrice', 'goodsDeliveryPrice'
            ];
            foreach($listKey as $listKeyValue){
                $listValue = $val[$listKeyValue];
                //Number_format
                if( !empty($numberFormatKey[$listKeyValue])){
                    $listValue = number_format($listValue);
                }
                $fieldData[] = ExcelCsvUtil::wrapTd($listValue);
            }
            $excelBody .=  "<tr>". implode('',$fieldData) . "</tr>";
        }
        $simpleExcelComponent = SlLoader::cLoad('Excel','SimpleExcelComponent','sl');
        $simpleExcelComponent->simpleDownload('주문리스트_'.DateTimeUtils::dateFormat('Y-m-d', 'now') ,$excelTitles,$excelBody);
    }

    /**
     * 엑셀 다운로드
     * @param $getData
     */
    public function simpleExcelDownload2($getData){

        $memberMasking = \App::load('Component\\Member\\MemberMasking');

        $data = $getData['data'];
        $page = $getData['page'];
        $excelBody = '';
        $orderStatusMap = SlCommonUtil::getOrderStatusAllMap();

        foreach ($data as $key => $val) {
            $dataList = array();
            //주문번호(밑에)
            //주문자
            $dataList[] = $val['memNm'].'('. $memberMasking->masking('order','id',$val['memId']).'/'.$val['nickNm'].')';
            //주문상품 + 관리자 요청 메세지
            $requestMessage = !empty($val['requestToAdmin']) ? ' <br><b>관리자 요청 메세지 : ' .  $val['requestToAdmin'] . '</b>' : ''   ;
            $dataList[] = $val['goodsHtml'] . $requestMessage;
            //결제금액
            $dataList[] = number_format($val['settlePrice']) . '원' ;
            //주문상태
            $dataList[] = $val['orderStatusStr'];
            //주문일자
            $dataList[] = str_replace(' ', '<br>', gd_date_format('Y-m-d H:i', $val['regDt']));
            //출고승인여부
            $dataList[] = $val['orderAcctStatusStr'];
            //승인일자
            $dataList[] = str_replace(' ', '<br>', gd_date_format('Y-m-d H:i', $val['acctDt']));

            $fieldData = array();
            $fieldData[] = ExcelCsvUtil::wrapTd($page->idx--);
            //주문번호
            $fieldData[] = ExcelCsvUtil::wrapTd($val['orderNo'],'text','mso-number-format:\'\@\'');

            foreach( $dataList as $key => $value){
                $fieldData[] = ExcelCsvUtil::wrapTd($value);
                //필드별 별도처리 IF 사용
            }
            $excelBody .=  "<tr>". implode('',$fieldData) . "</tr>";
        }

        $isProvider = Manager::isProvider();
        if( $isProvider ){
            $scmNo = \Session::get('manager.scmNo');
            DBUtil2::getOne('sl_setScmConfig','scmNo',$scmNo);

        }

        $simpleExcelComponent = SlLoader::cLoad('Excel','SimpleExcelComponent','sl');
        $simpleExcelComponent->simpleDownload('주문리스트_'.DateTimeUtils::dateFormat('Y-m-d', 'now') ,ScmOrderListService::LIST_TITLES,$excelBody);
    }


    public function simpleExcelDownload3($getData){

        $excelTitles = [
            '배송지', //-- DBUTIl sl_setScmDeliveryList
            '수령자명',
            '팀명',
            '그룹번호', //b.orderName
            '주문자명', //b.orderName
            '닉네임',   //e.nickNm
            '상품명',   //goodsNm
            '옵션명',   //optionStr
            '수량',     //- goodsCnt
            '주문번호', //a.orderNo
            '배송정보' ,
            '주문일자' ,
            '주문상태'
        ];
        $data = $getData['data'];

        $goodsTotalCnt = 0;

        //데이터 정제
        $excelDataList = [];
        foreach ($data as $key => $val) {
            foreach($val['goodsInfo'] as $key2 => $val2){
                $fieldData = [];
                $fieldData[] = '<tr>';

                $val2['optionStr'] = SlCommonUtil::getRefineOrderGoodsOption($val2['optionInfo']);
                $val2['invoiceInfo'] = $val2['invoiceCompanyName'] . ' ' . $val2['invoiceNo'];
                $excelMergeData = array_merge($val, $val2);

                $deliverySql = "select subject from sl_setScmDeliveryList a join sl_orderScm b on a.sno = b.scmDeliverySno where a.scmNo = {$val['scmNo']} and b.orderNo = '{$val['orderNo']}' ";
                $deliveryData = DBUtil2::runSelect($deliverySql)[0];
                $excelMergeData['deliverySubject'] = $deliveryData['subject'];

                $excelRefineData = SlCommonUtil::getAvailData($excelMergeData,[
                    'deliverySubject',
                    'receiverName',
                    'teamName',
                    'groupSno',
                    'orderName',
                    'nickNm',
                    'goodsNm',
                    'optionStr',
                    'goodsCnt',
                    'orderNo',
                    'invoiceInfo',
                    'regDt',
                    'orderStatus',
                ]);

                foreach($excelRefineData as $refineKey => $refineEach){
                    if('orderNo' == $refineKey){
                        $fieldData[] = ExcelCsvUtil::wrapTd($refineEach, 'text', 'mso-number-format:\'\@\'');
                    }else{
                        $fieldData[] = ExcelCsvUtil::wrapTd($refineEach);
                    }
                }

                $fieldData[] = '</tr>';
                $excelDataList[$excelRefineData['deliverySubject']][] = $fieldData;
                $goodsTotalCnt += $excelRefineData['goodsCnt'];
            }
        }

        $excelBody = '';
        ksort($excelDataList);
        foreach ($excelDataList as $excelData){
            foreach ($excelData as $excelEachData){
                $excelBody .= implode('',$excelEachData);
            }
        }

        $simpleExcelComponent = SlLoader::cLoad('Excel','SimpleExcelComponent','sl');
        $simpleExcelComponent->simpleDownload('배송지별주문리스트_'.DateTimeUtils::dateFormat('Y-m-d', 'now') ,$excelTitles,$excelBody);
    }

    public function simpleExcelDownload5($getData){

        $excelTitles = [
            '수령이름',
            '팀명',
            '파트명',
            '소부문명',
            '사번',
            '이름',
            '상품명',   //goodsNm
            '옵션명',   //optionStr
            '수량',     //- goodsCnt
            '주문번호', //a.orderNo
        ];
        $data = $getData['data'];

        $goodsTotalCnt = 0;

        //데이터 정제
        $excelDataList = [];
        foreach ($data as $key => $val) {
            foreach($val['goodsInfo'] as $goodsInfo){
                $team = $goodsInfo['empTeam'];
                $part1 = $goodsInfo['empPart1'];
                $part2 = $goodsInfo['empPart2'];

                $key = $team.$part1.$part2;

                $excelDataList[$key][] = [
                    'orderNo' => $goodsInfo['orderNo'],
                    'goodsNm' => $goodsInfo['goodsNm'],
                    'optionValue1' => $goodsInfo['optionValue1'],
                    'goodsCnt' => $goodsInfo['goodsCnt'],
                    'team' => $team,
                    'part1' => $part1,
                    'part2' => $part2,
                    'companyId' => $goodsInfo['companyId'],
                    'name' => $goodsInfo['name'],
                ];
            }
        }
        //gd_debug($excelDataList);

        ksort($excelDataList);

        $excelBody = '';
        foreach ($excelDataList as $excelDataKey => $excelList){
            foreach($excelList as $excelData){
                $fieldData = [];
                $fieldData[] = '<tr>';
                $fieldData[] = ExcelCsvUtil::wrapTd($excelDataKey);
                $fieldData[] = ExcelCsvUtil::wrapTd($excelData['team']);
                $fieldData[] = ExcelCsvUtil::wrapTd($excelData['part1']);
                $fieldData[] = ExcelCsvUtil::wrapTd($excelData['part2']);
                $fieldData[] = ExcelCsvUtil::wrapTd($excelData['companyId']);
                $fieldData[] = ExcelCsvUtil::wrapTd($excelData['name']);
                $fieldData[] = ExcelCsvUtil::wrapTd($excelData['goodsNm']);
                $fieldData[] = ExcelCsvUtil::wrapTd($excelData['optionValue1'], 'text', 'text-align:center');
                $fieldData[] = ExcelCsvUtil::wrapTd($excelData['goodsCnt']);
                $fieldData[] = ExcelCsvUtil::wrapTd($excelData['orderNo'], 'text', 'mso-number-format:\'\@\'');

                $fieldData[] = '</tr>';

                $excelBody .= implode('',$fieldData);
            }
        }




        $simpleExcelComponent = SlLoader::cLoad('Excel','SimpleExcelComponent','sl');
        $simpleExcelComponent->simpleDownload('아시아나_주문리스트_'.DateTimeUtils::dateFormat('Y-m-d', 'now') ,$excelTitles,$excelBody);
    }


    public function simpleExcelDownload4($getData){
        $subject = '(엠에스이노버) TKE 23 리뉴얼 하계근무복 배송지별 주문 리스트 전달 건';
        $todayFull = date('m월 d일');
        $today = date('Ymd');
        $excelFileName = "tke_order_confirm_{$today}.xls";

        $this->makeFile($excelFileName, $getData);

        $mailFile['filePath'] = $filePath;
        $mailFile['fileName'] = "{$subject}.xls";
        $mailFile['excelFileName'] = $excelFileName;

        $mailData['subject'] = $subject;
        $mailData['from'] = 'innover@msinnover.com';

        $mailData['to'] = 'nbluesky1478@gmail.com';
        $mailData['cc'] = 'jhsong@msinnover.com';

        /*if( SlCommonUtil::isDev() ){
            $mailData['to'] = 'nbluesky1478@gmail.com';
            $mailData['cc'] = 'jhsong@msinnover.com';
        }else{
            $mailData['to'] = implode(',',SlCodeMap::ORDER_MAIL_LIST);
        }*/
        $mailData['body'] = "<!doctype html><html><head><meta http-equiv='Content-Type' content='text/html; charset=utf-8' /></head><body style='font-size:12px;font-family:\"맑은 고딕\"'>안녕하십니까?<br>엠에스이노버 자동 출고 시스템입니다.<br><br>{$todayFull} 출고 지시 20분전 리스트를 전달 드립니다.<br>";
        $mailData['body'] .= "첨부파일을 확인하여 출고지시 파일의 이상유무를 확인해주세요.<br>(재고확인 부분은 실제 출고시 보이지 않습니다.) <br>이상이 있다면 개발팀에 즉시 확인 요청 바랍니다.<br><br>감사합니다.</body></html>";
        $mailUtil = SlLoader::cLoad('mail','SiteLabMailUtil','sl');

        SitelabLogger::logger($mailData);
        SitelabLogger::logger($mailFile);

        $mailUtil->sendWithExcelFile($mailData['subject'] , $mailData['body'], $mailData['from'], $mailData['to'], $mailFile, $mailData['cc']);

        new AlertOnlyException('완료');
    }

    public function makeFile($fileName, $getData){

        SitelabLogger::logger('MakeFile FileName.');
        SitelabLogger::logger($fileName);

        $excelTitles = [
            '배송지', //-- DBUTIl sl_setScmDeliveryList
            '수령자명',
            '팀명',
            '주문자명', //b.orderName
            '닉네임',   //e.nickNm
            '상품명',   //goodsNm
            '옵션명',   //optionStr
            '수량',     //- goodsCnt
            '주문번호', //a.orderNo
            '배송정보'
        ];
        $data = $getData['data'];

        $goodsTotalCnt = 0;

        //데이터 정제
        $excelDataList = [];
        foreach ($data as $key => $val) {
            foreach(  $val['goodsInfo'] as $key2 => $val2  ){
                $fieldData = [];
                $fieldData[] = '<tr>';

                $val2['optionStr'] = SlCommonUtil::getRefineOrderGoodsOption($val2['optionInfo']);
                $val2['invoiceInfo'] = $val2['invoiceCompanyName'] . ' ' . $val2['invoiceNo'];
                $excelMergeData = array_merge($val, $val2);

                $deliverySql = "select subject from sl_setScmDeliveryList a join sl_orderScm b on a.sno = b.scmDeliverySno where a.scmNo = {$val['scmNo']} and b.orderNo = '{$val['orderNo']}' ";
                $deliveryData = DBUtil2::runSelect($deliverySql)[0];
                $excelMergeData['deliverySubject'] = $deliveryData['subject'];

                $excelRefineData = SlCommonUtil::getAvailData($excelMergeData,[
                    'deliverySubject',
                    'receiverName',
                    'teamName',
                    'orderName',
                    'nickNm',
                    'goodsNm',
                    'optionStr',
                    'goodsCnt',
                    'orderNo',
                    'invoiceInfo',
                ]);

                foreach($excelRefineData as $refineKey => $refineEach){
                    if('orderNo' == $refineKey){
                        $fieldData[] = ExcelCsvUtil::wrapTd($refineEach, 'text', 'mso-number-format:\'\@\'');
                    }else{
                        $fieldData[] = ExcelCsvUtil::wrapTd($refineEach);
                    }
                }

                $fieldData[] = '</tr>';

                $excelDataList[$excelRefineData['deliverySubject']][] = $fieldData;

                $goodsTotalCnt += $excelRefineData['goodsCnt'];

            }
        }

        $excelBody = "<table border='1'>";
        ksort($excelDataList);
        foreach ($excelDataList as $excelData){
            foreach ($excelData as $excelEachData){
                $excelBody .= implode('',$excelEachData);
            }
        }
        $excelBody .= "</table>";

        //$simpleExcelComponent = SlLoader::cLoad('Excel','SimpleExcelComponent','sl');
        //$simpleExcelComponent->simpleDownload('배송지별주문리스트_'.DateTimeUtils::dateFormat('Y-m-d', 'now') ,$excelTitles,$excelBody);

        //메일을 어떻게 보내지 ?

        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'.$fileName.'"');
        header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header('Pragma: public'); // HTTP/1.0
        ob_start();
        echo $excelBody;
        $inputData = ob_get_contents();
        ob_end_flush();
        $filePath = UserFilePath::data('etc')->getRealPath() . '/order/'.$fileName ;
        FileHandler::write($filePath, $inputData, 0707);

        SitelabLogger::logger($filePath);

        return $filePath;
    }

}