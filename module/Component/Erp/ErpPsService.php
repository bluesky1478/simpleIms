<?php
namespace Component\Erp;

use App;
use Component\Database\DBTableField;
use Component\Member\Manager;
use Component\Scm\ScmTkeService;
use Exception;
use Framework\Debug\Exception\AlertBackException;
use Framework\Utility\DateTimeUtils;
use LogHandler;
use Request;
use Session;
use SiteLabUtil\SlCommonUtil;
use SlComponent\Database\DBUtil2;
use SlComponent\Database\SearchVo;
use SlComponent\Mail\SiteLabMailUtil;
use SlComponent\Util\ExcelCsvUtil;
use SlComponent\Util\PhpExcelUtil;
use SlComponent\Util\SitelabLogger;
use SlComponent\Util\SlCodeMap;
use SlComponent\Util\SlLoader;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Framework\StaticProxy\Proxy\FileHandler;
use UserFilePath;

/**
 * ERP 처리 서비스
 * Class GoodsStock
 * @package Component\Goods
 */
class ErpPsService {

    private $erpService;

    public function __construct(){
        $this->erpService = SlLoader::cLoad('erp','erpService');
    }

    /**
     * (신) 파일 입고 등록
     * @param $params
     * @return array
     * @throws Exception
     */
    public function saveInputHistory($params){
        $historyService = SlLoader::cLoad('erp','erpHistoryService');
        $files = \Request::files()->toArray();
        $params['instance'] = $historyService;
        $params['fnc'] = 'saveInputHistory';
        PhpExcelUtil::runExcelReadAndProcess($files, $params, 1);
        return ['data'=>$params,'msg'=>'저장되었습니다.'];
    }

    /**
     * (신) 파일 출고 등록
     * @param $params
     * @return array
     * @throws Exception
     */
    public function saveOutputHistory($params){
        $historyService = SlLoader::cLoad('erp','erpHistoryService');
        $files = \Request::files()->toArray();
        $params['instance'] = $historyService;
        $params['fnc'] = 'saveOutputHistory';
        PhpExcelUtil::runExcelReadAndProcess($files, $params, 1);
        return ['data'=>$params,'msg'=>'저장되었습니다.'];
    }



    /**
     * 재고 등록
     * @param $params
     * @return array
     * @throws Exception
     */
    public function setInputStock($params){
        $files = \Request::files()->toArray();
        $params['instance'] = $this->erpService;
        $params['fnc'] = 'saveEachInputStock';
        PhpExcelUtil::runExcelReadAndProcess($files, $params, 1);
        return ['data'=>$params,'msg'=>'저장되었습니다.'];
    }

    /**
     * 마감 등록
     * @param $params
     * @return array
     * @throws Exception
     */
    public function saveClosing($params){


        return ['data'=>$params,'msg'=>'저장되었습니다.'];
    }


    /**
     * 상품 등록 (초기)
     * @param $params
     * @return array
     * @throws Exception
     */
    public function setProduct($params){
        //DBUtil2::runSql("Truncate table sl_3plProduct");
        $files = \Request::files()->toArray();
        $params['instance'] = $this->erpService;
        $params['fnc'] = 'saveEachProduct';

        //ExcelCsvUtil::runExcelReadAndProcess($files, $params, 2);
        PhpExcelUtil::runExcelReadAndProcess($files, $params, 1);

        return ['data'=>$params,'msg'=>'저장되었습니다.'];
    }

    public function setManualOutStock($params){
        $files = \Request::files()->toArray();
        $params['instance'] = $this->erpService;
        $params['fnc'] = 'saveEachOutStock';
        $params['mixData'] = [
            'excelField' => [
                'outDate' => 1,
                'invoice' => 2,
                'orderNo' => 14,
                'thirdPartyProductCode' => 8,
                'quantity' => 13,
            ]
        ];

        $result = PhpExcelUtil::runExcelReadAndProcess($files, $params, 1);

        if( !empty($result['isNotProductCode']) ){
            throw new \Exception('저장 하였으나 일부 저장 되지 않은 이력이 있습니다.');
        }
        //SitelabLogger::logger($params['mixData']['isNotProductCode']);
        //SitelabLogger::logger($params['mixData']['isNotProductCode']);

        return ['data'=>$params,'msg'=>'저장되었습니다.'];
    }

    public function setManualOutStockGolf($params){
        $files = \Request::files()->toArray();
        $params['instance'] = $this->erpService;
        $params['fnc'] = 'saveEachOutStock';
        $params['mixData'] = [
            'excelField' => [
                'invoice' => 2,
                'orderNo' => 3,
                'thirdPartyProductCode' => 9,
                'quantity' => 11,
            ]
        ];
        //$params['mixData']['outDate'] = [date('Y-m-d')];

        PhpExcelUtil::runExcelReadAndProcess($files, $params, 1);

        if( !empty($result['isNotProductCode']) ){
            throw new \Exception('저장 하였으나 일부 저장 되지 않은 이력이 있습니다.');
        }

        return ['data'=>$params,'msg'=>'저장되었습니다.'];
    }

    public function setClosing($params){
        $erpService = SlLoader::cLoad('erp','erpService');
        $erpService->setClosing();
        return ['data'=>$params,'msg'=>'마감 처리 되었습니다.'];
    }

    /**
     * 재고 수정.
     * @param $params
     * @return array
     * @throws Exception
     */
    public function modifySaleStock($params){
        $searchVo = new SearchVo('sno=?', $params['sno']);
        $optionInfo = DBUtil2::getOneBySearchVo(DB_GOODS_OPTION, $searchVo);
        $optionInfo['optionSno'] = $optionInfo['sno'];
        $optionInfo['beforeStockCnt'] = $optionInfo['stockCnt'];
        $optionInfo['afterStockCnt'] = $params['stockCnt'];
        $optionInfo['managerSno'] = \Session::get('manager.sno');
        unset($optionInfo['sno']);
        if( $optionInfo['stockCnt'] != $params['stockCnt'] ){
            DBUtil2::insert('sl_3plSaleGoodsModifyHistory',$optionInfo);
            DBUtil2::update(DB_GOODS_OPTION, ['stockCnt'=>$params['stockCnt']], $searchVo);
        }
        return ['data'=>$params,'msg'=>'재고 수정 완료.'];
    }

    /**
     * 재고수정 일괄
     * @param $params
     * @return array
     * @throws Exception
     */
    public function modifySaleStockBatch($params){
        foreach( $params['updateList'] as $updateData ){
            $saveData['sno'] = $updateData['sno'];
            $saveData['stockCnt'] = $updateData['stock'];
            $this->modifySaleStock($saveData);
        }
        return ['data'=>$params,'msg'=>'재고 수정 완료.'];
    }

    /**
     * 상품 옵션 삭제 (고객사 재고 관리에서 수행)
     * @param $params
     * @return array
     * @throws Exception
     */
    public function removeGoodsOption($params){
        /*$saveData['sno'] = $params['sno'];
        $saveData['stockCnt'] = $updateData['stock'];
        $this->modifySaleStock($saveData);
        return ['data'=>$params,'msg'=>'재고 정 완료.'];*/
    }


    /**
     * KTNG 수기 주문
     * @param $params
     * @return array
     * @throws Exception
     */
    public function setKtngOrderTemp($params)
    {
        SitelabLogger::logger("setKtngOrderTemp");

        DBUtil2::runSql("TRUNCATE TABLE sl_3plOrderTmp");

        $files = \Request::files()->toArray();
        $params['instance'] = SlLoader::cLoad('scm','scmKtngService');
        $params['fnc'] = 'setKtngOrderTemp';
        $params['mixData'] = [
            'excelField' => [
                'deliveryName' => 1,
                'prdName' => 2,
                'sex' => 3,
            ],
            'code' => []
        ];

        $result = PhpExcelUtil::runExcelReadAndProcess($files, $params, 1);

        SitelabLogger::logger("Code....");
        SitelabLogger::logger($params['mixData']['code']);

        if( !empty($result['isNotEnoughStock']) ){
            //throw new \Exception('재고가 부족한 제품이 있습니다. 확인해주세요.');
        }
        return ['data'=>$params,'msg'=>'저장되었습니다.'];
    }

    /**
     * 영구크린 수기 주문
     * @param $params
     * @return array
     * @throws Exception
     */
    public function setYoung9OrderTemp($params){
        SitelabLogger::logger("수기 주문 시작");
        //DBUtil2::runSql("TRUNCATE TABLE sl_3plOrderTmp");
        $files = \Request::files()->toArray();

        //$params['instance'] = SlLoader::cLoad('scm','ScmYoung9Service');
        $params['instance'] = SlLoader::cLoad('scm','ScmOekService');

        $params['fnc'] = 'setOrderTemp';
        $params['mixData'] = [
            'excelField' => [
                'receiverName' => 1,
                'prd' => 2,
                'zipcode' => 3,
                'address' => 4,
                'receiverPhone' => 5,
                'remark' => 6,
            ],
        ];
        PhpExcelUtil::runExcelReadAndProcess($files, $params, 1);
        SitelabLogger::logger("수기 주문 종료");
        return ['data'=>$params,'msg'=>'저장되었습니다.'];
    }
    

    /**
     * 영구크린 약품 주문
     * @param $params
     * @return array
     * @throws Exception
     */
    public function setYounguOrderTemp($params){

        //DBUtil2::runSql("TRUNCATE TABLE sl_3plOrderTmp");

        $files = \Request::files()->toArray();
        $params['instance'] = $this->erpService;
        $params['fnc'] = 'setYounguOrderTemp';
        $params['mixData'] = [
            'excelField' => [
                'customerName' => 1,
                'address' => 2,
                'zipcode' => 3,
                'phone' => 4,
                'mobile' => 4,
                'qtyStr' => 5,
            ]
        ];

        $result = PhpExcelUtil::runExcelReadAndProcess($files, $params, 4);
        if( !empty($result['isNotEnoughStock']) ){
            //throw new \Exception('재고가 부족한 제품이 있습니다. 확인해주세요.');
        }
        return ['data'=>$params,'msg'=>'저장되었습니다.'];
    }

    /**
     * 수기 주문.
     * @param $params
     * @return array
     * @throws Exception
     */
    public function set3plOrderTemp($params){
        //DBUtil2::runSql("TRUNCATE TABLE set3plOrderTemp");
        $files = \Request::files()->toArray();
        $params['instance'] = $this->erpService;
        $params['fnc'] = 'set3plOrderTemp';
        $params['mixData'] = [
            'excelField' => [
                'memo' => 1,
                'invoiceNo' => 2,
                'orderNo' => 3,
                'customerName' => 4,
                'zipcode' => 5,
                'address' => 6,
                'phone' => 7,
                'mobile' => 8,
                'productCode' => 9,
                'productName' => 10,
                'qty' => 11,
                'remark' => 12,
            ]
        ];

        $result = PhpExcelUtil::runExcelReadAndProcess($files, $params, 1);

        if( !empty($result['isNotEnoughStock']) ){
            //throw new \Exception('재고가 부족한 제품이 있습니다. 확인해주세요.');
        }

        return ['data'=>$params,'msg'=>'저장되었습니다.'];
    }

    public function runTodayConfirm($params){

        $nowDate = date('Ymd');
        $data = DBUtil2::getOne('sl_holiday', 'locdate', $nowDate);

        if( 'Y' !== strtoupper($data['isHoliday']) ){
            //FIXME TKE 주문은 대기 처리 : 추후 제거
            $erpService = SlLoader::cLoad('erp','erpService');
            $erpService->runTkeOrderRefine();

            $todayFull = date('m월 d일');
            $today = date('Ymd');
            $subject = "엠에스이노버출고리스트확인_{$today}";

            //만들어진 파일을. 메일로 전송한다.
            $sopService = SlLoader::cLoad('godo','sopService','sl');
            $excelFileName = "order_confirm_{$today}.xls";

            $filePath = $sopService->makeFileRealExcel2([
                'fileName'=>$excelFileName,
                'subject'=>$subject,
                'isOrder'=>false,
                'showStock'=>true,
                'today'=>$today,
            ]);

            $mailFile['filePath'] = $filePath;
            $mailFile['fileName'] = "{$subject}.xls";
            $mailFile['excelFileName'] = $excelFileName;

            $mailData['subject'] = $subject;
            $mailData['from'] = 'innover@msinnover.com';
            if( SlCommonUtil::isDev() ){
                $mailData['to'] = 'nbluesky1478@gmail.com';
                $mailData['cc'] = 'jhsong@msinnover.com';
            }else{
                $mailData['to'] = implode(',',SlCodeMap::ORDER_MAIL_LIST);
            }
            $mailData['body'] = "<!doctype html><html><head><meta http-equiv='Content-Type' content='text/html; charset=utf-8' /></head><body style='font-size:12px;font-family:\"맑은 고딕\"'>안녕하십니까?<br>엠에스이노버 자동 출고 시스템입니다.<br><br>{$todayFull} 출고 지시 20분전 리스트를 전달 드립니다.<br>";
            $mailData['body'] .= "첨부파일을 확인하여 출고지시 파일의 이상유무를 확인해주세요.<br>(재고확인 부분은 실제 출고시 보이지 않습니다.) <br>이상이 있다면 개발팀에 즉시 확인 요청 바랍니다.<br><br>감사합니다.</body></html>";
            $mailUtil = SlLoader::cLoad('mail','SiteLabMailUtil','sl');
            $mailUtil->sendWithExcelFile($mailData['subject'] , $mailData['body'], $mailData['from'], $mailData['to'], $mailFile, $mailData['cc']);
        }

        exit();
    }

    public function runTodayRelease($params){

        $nowDate = date('Ymd');
        $data = DBUtil2::getOne('sl_holiday', 'locdate', $nowDate);

        if( 'Y' !== strtoupper($data['isHoliday']) ){
            //FIXME TKE 주문은 대기 처리 : 추후 제거
            $erpService = SlLoader::cLoad('erp','erpService');
            $erpService->runTkeOrderRefine();
            DBUtil2::runSql("update `es_orderInfo` set receiverCellPhone = concat('0',REPLACE(receiverCellPhone, '-', '')) where receiverCellPhone like '1%'");//이상한 주문 휴대번호 변경

            $todayFull = date('m월 d일');
            $today = date('Ymd');
            $subject = "엠에스이노버 출고 리스트_{$today}";

            //만들어진 파일을. 메일로 전송한다.
            $sopService = SlLoader::cLoad('godo','sopService','sl');
            $excelFileName = "order_{$today}.xls";

            $filePath = $sopService->makeFileRealExcel2([
                'fileName'=>$excelFileName,
                'subject'=>$subject,
                'isOrder'=>true,
                'showStock'=>false,
                'today'=>$today,
            ]);

            $mailFile['filePath'] = $filePath;
            $mailFile['fileName'] = "{$subject}.xls";
            $mailData['subject'] = $subject;
            $mailData['from'] = 'innover@msinnover.com';
            if( SlCommonUtil::isDev() ){
                $mailData['to'] = 'jhsong@msinnover.com';
                $mailData['cc'] = 'nbluesky1478@gmail.com';
            }else{
                $mailData['to'] = implode(',',SlCodeMap::SAMYOUNG_MAIL_LIST);
                $mailData['cc'] = implode(',',SlCodeMap::ORDER_MAIL_LIST);
            }
            $mailData['body'] = "<!doctype html><html><head><meta http-equiv='Content-Type' content='text/html; charset=utf-8' /></head><body style='font-size:12px;font-family:\"맑은 고딕\"'>안녕하십니까?<br>엠에스이노버 입니다.<br><br>{$todayFull} 출고 리스트를 전달 드립니다.<br>";
            $mailData['body'] .= "<br><br>감사합니다.</body></html>";

            $mailUtil = SlLoader::cLoad('mail','SiteLabMailUtil','sl');
            $mailUtil->sendWithExcelFile($mailData['subject'] , $mailData['body'], $mailData['from'], $mailData['to'], $mailFile, $mailData['cc']);
        }

        exit();
    }

    public function get3plOrderHistory($params){
        $list = DBUtil2::getList('sl_3plOrderHistory','1','1');
        return ['data'=>$list,'msg'=>'조회 되었습니다.'];
    }


    /**
     * 송장 등록
     * @param $params
     * @return array
     * @throws Exception
     */
    public function regInvoice($params){
        //송장 등록 전, 재고 원복 후 기존 내역 삭제.
        $today = date('Y-m-d');

        $files = \Request::files()->toArray();
        $params['instance'] = $this->erpService;
        $params['fnc'] = 'saveInvoice';

        $params['mixData'] = [
            'excelField' => [
                'thirdPartyProductCode'=> 8,
                'inOutDate'=> 1,
                'memo'=> 12,
                'orderNo'=> 14,
                'invoiceNo'=> 12,
                'customerName'=> 3,
                'address'=> 5,
                'phone'=> 6,
                'cellphone'=> 7,
                'inputDate'=> 19,
                'inputTime'=> 20,
                'quantity'=> 13,
            ]
        ];

        //1. 송장 등록.
        PhpExcelUtil::runExcelReadAndProcess($files, $params, 1);

        //1-0. 상품준비중 -> 배송중 처리
        $manualService = SlLoader::cLoad('godo','manualService','sl');
        $manualService->setManualInvoice($today);

        //1-1. 송장 등록후 나머주 orderTmp정리.
        //남은것도 그냥 Tmp에 넣는다. (송장 입력만 안된 상태)
        $tmpList = DBUtil2::getList('sl_3plOrderTmp', '1', '1');
        foreach($tmpList as $tmpData){
            $deleteSno = $tmpData['sno'];
            unset($tmpData['sno']);
            $tmpData['orderDt'] = $today;
            $tmpData['invoiceNo'] = $saveData['invoiceNo'];
            DBUtil2::insert('sl_3plOrderHistory',$tmpData);
            DBUtil2::delete('sl_3plOrderTmp',new SearchVo('sno=?', $deleteSno));
        }

        //2. 송장 등록 이력 저장.
        $this->erpService->saveInvoiceRegHistory($today);
        //2-1. 송장 등록이 안된 경우 조치.
        DBUtil2::runSql("update sl_3plOrderHistory a join sl_3plStockInOut b on a.orderNo = b.orderNo set a.invoiceNo = b.invoiceNo  where a.regDt >= '{$today} 00:00:00' and a.invoiceNo = ''");

        //3. 송장 골프존 메일 발송.
        $sql = "select * from sl_3plStockInOut a join sl_3plProduct b on a.productSno = b.sno where a.regDt >= '{$today} 00:00:00' and b.scmNo = 2  ";
        $golfList = DBUtil2::runSelect($sql);
        if( !empty($golfList) ){
            $todayKr = date('m월d일');
            $subject = "(엠에스이노버) {$todayKr} 골프존 송장등록 완료 건";
            $contents = "<br>금일 출고리스트 운송장 등록이 완료되었습니다.<br>해당 내용은 주문관리 시스템에서 확인하실 수 있습니다. <br>";
            $contents .= "<a href='http://innoverb2b.com/partner/order_list.php'>http://innoverb2b.com/partner/order_list.php</a>";
            if( SlCommonUtil::isDev() ){
                $to = 'bluesky1478@hanmail.net';
                $cc = 'jhsong@msinnover.com';
            }else{
                $to = implode(',',SlCodeMap::GOLFZON_MAIL_LIST);
                $cc = implode(',',SlCodeMap::GOLFZON_CC_MAIL_LIST).','.implode(',',SlCodeMap::ORDER_MAIL_LIST);
            }
            SiteLabMailUtil::sendSimpleMail($subject, $contents, $to, $cc);
            //4. TODO 송장 메일 발송 여부 업데이트. ( 1회성으로 나가도록 처리 )
        }

        return ['data'=>$params,'msg'=>'등록 되었습니다.'];
    }

    public function getPrdInfo($params){
        $prdData = $this->erpService->getPrdInfo($params);
        return ['data'=>['prdData'=>$prdData],'msg'=>'조회 되었습니다.'];
    }

    public function save3plReturn($params){
        $params['sno']= $this->erpService->save3plReturn($params);
        return ['data'=>$params,'msg'=>'저장 되었습니다.'];
    }

    /**
     * 실시간 저장.
     * @param $params
     * @return array
     * @throws Exception
     */
    public function updateRealtime($params){

        $result = SlCommonUtil::updateRealtime($params);

        if( 'returnStatus' == $params['key'] ){

            $searchVo = new SearchVo('sno=?', $params['sno']);
            if( 3 == $params['value'] ){
                //회수 완료 메일 발송.
                $info = DBUtil2::getOneBySearchVo('sl_3plReturnList', $searchVo);
                if( empty($info['returnDt']) || $info['returnDt'] == '0000-00-00' ){

                    //SitelabLogger::logger('회수완료 CHECK (erpPsService) ');
                    //SitelabLogger::logger($info['sno'] . ' : ' . $info['returnDt']);

                    $subject = "제품 회수완료 안내 ({$info['scmName']} {$info['customerName']})";
                    $contents = "
                        <br>클레임 번호 : {$info['claimSno']}
                        <br>고객사 : {$info['scmName']}
                        <br>고객명 : {$info['customerName']}
                        <br><br>
                        {$info['partnerMemo']}
                    ";

                    if( SlCommonUtil::isDev() ){
                        $to = 'bluesky1478@hanmail.net';
                        $cc = 'jhsong@msinnover.com';
                    }else{
                        $to = implode(',',SlCodeMap::ORDER_MAIL_LIST);
                    }
                    SiteLabMailUtil::sendSimpleMail($subject, $contents, $to, $cc);
                }
                DBUtil2::update('sl_3plReturnList',['returnDt'=>'now()'],$searchVo);
            }else{
                DBUtil2::update('sl_3plReturnList',['returnDt'=>'0000-00-00 00:00:00'],$searchVo);
            }
        }

        return ['data'=>['updateResult' => $result],'msg'=>'Complete'];
    }

    public function downloadPackingList($params){
        $files = \Request::files()->toArray();
        $additionService = SlLoader::cLoad('addition','additionService');
        $additionService->downloadPackingList($files);
    }

    public function runTkeOrderRefine($params){
        $erpService = SlLoader::cLoad('erp','erpService');
        $erpService->runTkeOrderRefine();
    }

    public function runTest($params){
        SitelabLogger::logger('테스트...');
        return ['data'=>$params,'msg'=>'조회 되었습니다.'];
    }


    /**
     * 송장 등록 수기
     * @param $params
     * @return array
     * @throws Exception
     */
    public function regInvoiceManual($params){
        //송장 등록 전, 재고 원복 후 기존 내역 삭제.
        $today = '2023-10-06';

        //공통
        $files = \Request::files()->toArray();
        $params['instance'] = $this->erpService;
        $params['fnc'] = 'saveInvoice';
        $params['mixData'] = [
            'excelField' => [
                'thirdPartyProductCode'=> 8,
                'inOutDate'=> 1,
                'memo'=> 12,
                'orderNo'=> 14,
                'invoiceNo'=> 12,
                'customerName'=> 3,
                'address'=> 5,
                'phone'=> 6,
                'cellphone'=> 7,
                'inputDate'=> 19,
                'inputTime'=> 20,
                'quantity'=> 13,
            ]
        ];
        //1. 송장 등록.
        PhpExcelUtil::runExcelReadAndProcess($files, $params, 1);

        //1-1. 송장 등록후 나머주 orderTmp정리.
        //남은것도 그냥 Tmp에 넣는다. (송장 입력만 안된 상태)
        /*$tmpList = DBUtil2::getList('sl_3plOrderTmp', '1', '1');
        foreach($tmpList as $tmpData){
            $deleteSno = $tmpData['sno'];
            unset($tmpData['sno']);
            $tmpData['orderDt'] = $today;
            $tmpData['invoiceNo'] = $saveData['invoiceNo'];
            DBUtil2::insert('sl_3plOrderHistory',$tmpData);
            DBUtil2::delete('sl_3plOrderTmp',new SearchVo('sno=?', $deleteSno));
        }*/

        //2. 송장 등록 이력 저장.
        $this->erpService->saveInvoiceRegHistory($today);
        //2-1. 송장 등록이 안된 경우 조치.
        DBUtil2::runSql("update sl_3plOrderHistory a join sl_3plStockInOut b on a.orderNo = b.orderNo set a.invoiceNo = b.invoiceNo  where a.regDt >= '{$today} 00:00:00' and a.invoiceNo = ''");

        //3. 송장 골프존 메일 발송.
        $sql = "select * from sl_3plStockInOut a join sl_3plProduct b on a.productSno = b.sno where a.regDt >= '{$today} 00:00:00' and b.scmNo = 2  ";
        $golfList = DBUtil2::runSelect($sql);
        if( !empty($golfList) ){
            $todayKr = $today;
            $subject = "(엠에스이노버) {$todayKr} 골프존 송장등록 완료 건";
            $contents = "<br>금일 출고리스트 운송장 등록이 완료되었습니다.<br>해당 내용은 주문관리 시스템에서 확인하실 수 있습니다. <br>";
            $contents .= "<a href='http://innoverb2b.com/partner/order_list.php'>http://innoverb2b.com/partner/order_list.php</a>";
            if( SlCommonUtil::isDev() ){
                $to = 'bluesky1478@hanmail.net';
                $cc = 'jhsong@msinnover.com';
            }else{
                $to = implode(',',SlCodeMap::GOLFZON_MAIL_LIST);
                $cc = implode(',',SlCodeMap::GOLFZON_CC_MAIL_LIST).','.implode(',',SlCodeMap::ORDER_MAIL_LIST);
            }
            SiteLabMailUtil::sendSimpleMail($subject, $contents, $to, $cc);
            //4. TODO 송장 메일 발송 여부 업데이트. ( 1회성으로 나가도록 처리 )
        }

        return ['data'=>$params,'msg'=>'등록 되었습니다.'];
    }

    /**
     * 주문취소
     * @param $params
     * @return string[]
     * @throws \Exception
     */
    public function cancelOrder($params){
        $orderNo = $params['orderNo'];
        $order = \App::load('\\Component\\Order\\OrderAdmin');
        $orderData = $order->getOrderView($orderNo);

        $availStatus = [
            'o', 'p'
        ];
        if( in_array(substr($orderData['orderStatus'],0,1 ), $availStatus)  ){
            $cancelMsg = [
                'orderStatus' => 'c4',
                'handleDetailReason' => __('고객요청에 의해 취소 처리'),
            ];
            $reOrderCalculation = \App::load('\\Component\\Order\\ReOrderCalculation');
            $param = [];
            foreach ($orderData['goods'] as $value) {
                foreach ($value as $val) {
                    foreach ($val as $goodsData) {
                        $param[$goodsData['sno']] = $goodsData['goodsCnt'];
                    }
                }
            }
            $order->setAutoCancel($orderNo, $param, $reOrderCalculation, $cancelMsg);

            //아시아나 주문일 경우 처리.
            $asianaService = SlLoader::cLoad('scm','ScmAsianaService');
            $asianaService->cancelOrderRefine($orderNo);

        }else{
            throw new \Exception('취소 불가 상태');
        }

        return ['msg'=>'취소완료'];
    }

    public function cancelOrderEach($params){

        $orderNo = $params['orderNo'];
        $order = \App::load('\\Component\\Order\\OrderAdmin');
        $orderData = $order->getOrderView($orderNo);
        $availStatus = ['o', 'p'];

        $beforeOrderStatus = $orderData['orderStatus'];

        if(in_array(substr($orderData['orderStatus'],0,1),$availStatus)){
            $companyIdList = [];
            foreach($params['cancelOrderList'] as $cancelOrder){
                $companyId = explode(' ',$cancelOrder)[0];
                //숫자만
                if(is_numeric($companyId)){
                    $companyIdList[] = $companyId;
                }
            }
            $companyIds = implode(',', $companyIdList);

            $cancelMsg = [
                'orderStatus' => 'c4',
                'handleDetailReason' => __('고객요청에 의해 취소 처리'),
            ];
            $reOrderCalculation = \App::load('\\Component\\Order\\ReOrderCalculation');
            $param = [];
            foreach ($orderData['goods'] as $value) {
                foreach ($value as $val) {
                    foreach ($val as $goodsData) {
                        //이 주문이 선택한 해당 직원의 주문인가 체크
                        $getCntSql = "select count(1) as cnt from sl_asianaOrderHistory where orderGoodsSno = '{$goodsData['sno']}' and companyId in ({$companyIds})";
                        $selectData = DBUtil2::runSelect($getCntSql);
                        if($selectData[0]['cnt'] > 0){
                            $param[$goodsData['sno']] = $goodsData['goodsCnt'];
                            DBUtil2::delete('sl_asianaOrderHistory', new SearchVo('orderGoodsSno=?',$goodsData['sno'])); //아시아나 주문이력 삭제
                        }
                    }
                }
            }
            $order->setAutoCancel($orderNo, $param, $reOrderCalculation, $cancelMsg);
            DBUtil2::update(DB_ORDER, ['orderStatus'=>$beforeOrderStatus], new SearchVo('orderNo=?', $orderNo));

            //주문 이력 재 설정
            $asianaService = SlLoader::cLoad('scm','ScmAsianaService');
            foreach($companyIdList as $eachCompanyId){
                $asianaService->saveEmpAllHistory($eachCompanyId);
            }
        }else{
            throw new \Exception('취소 불가 상태');
        }

        return ['msg'=>'취소완료'];
    }


    /**
     * 3PL 상품 재고 연동
     * @param $params
     * @return array
     */
    public function syncProduct($params){
        $service = SlLoader::cLoad('godo','sopService','sl');
        $service->update3plStock();
        return ['data'=>$params,'msg'=>'업데이트 완료.'];
    }

}
