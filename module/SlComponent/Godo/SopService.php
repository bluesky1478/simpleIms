<?php
namespace SlComponent\Godo;

use Component\Erp\ErpCodeMap;
use Component\Erp\ErpService;
use Component\Member\Util\MemberUtil;
use Component\Scm\AlterCodeMap;
use Component\Scm\ScmAsianaCodeMap;
use SiteLabUtil\SlCommonUtil;
use SlComponent\Database\DBUtil;
use SlComponent\Database\DBUtil2;
use SlComponent\Database\SearchVo;
use SlComponent\Excel\SimpleExcelComponent;
use SlComponent\Mail\SiteLabMailUtil;
use SlComponent\Util\CUrlUtil;
use SlComponent\Util\ExcelCsvUtil;
use SlComponent\Util\SitelabLogger;
use SlComponent\Util\SlCode;
use SlComponent\Util\SlCodeMap;
use Component\Storage\Storage;
use SlComponent\Util\SlLoader;
use Framework\StaticProxy\Proxy\FileHandler;
use UserFilePath;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Reader\Html;
use PhpOffice\PhpSpreadsheet\Writer\Xls;
use PhpOffice\PhpSpreadsheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Cell;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Component\Sms\Code;
use Component\Sms\SmsAuto;
use Component\Sms\SmsAutoCode;
use Component\Sms\SmsAutoObserver;


/**
 * 폐쇄몰 SOP 서비스
 * Class SlCode
 * @package SlComponent\Godo
 */
class SopService {
    /**
     * 아시아나 배송지 분리 ( 마스터 주문의 경우 )
     * @param $val
     */
    public function setAsianaReceiver(&$val){
        $refineMap = [
            '제주' => ScmAsianaCodeMap::JEJU,
            '여수' => ScmAsianaCodeMap::YEOSU,
            '부산' => ScmAsianaCodeMap::BUSAN,
            '무안' => ScmAsianaCodeMap::MUAN,
            '김포' => ScmAsianaCodeMap::GIMPO,
            '광주' => ScmAsianaCodeMap::GWANGJU,
        ];
        foreach($refineMap as $mapKey => $map){
            if(strpos($val['receiverName'],$mapKey) !== false){
                $val['receiverZoneCode'] = $map['zipCode'];
                $val['address'] = $map['address'].' '.$map['addressSub'];
                $val['receiverPhone'] = $map['phone'];
            }
        }
    }

    /**
     * 출고 리스트 타이틀 반환
     * @return string[]
     */
    public function getReleaseListTitle(){
        return [
            '운송회사',
            '송장번호',
            '주문번호',
            '고객명',
            '우편번호',
            '주소',
            '전화',
            '핸드폰',
            '제품코드',
            '제품명',
            '수량',
            '비고',
            '주문사이트',
            '불량유무',
            '출고예정일',
            '화주코드',
        ];
    }

    /**
     * 출고 리스트 가져오기
     * @return mixed
     */
    public function getReleaseList(){
        $startDate = date('Y-m-d', strtotime('-60 day'));
        $today = date('Y-m-d');
        $filePath = './module/SlComponent/Godo/Sql/releaseListV2.sql';
        $sql = SlCommonUtil::getFileData($filePath);
        $sql = str_replace('{$startDate}',$startDate, $sql);
        $sql = str_replace('{$today}',$today, $sql);
        //SitelabLogger::logger2(__METHOD__, $sql);
        return DBUtil2::runSelect($sql);
    }

    /**
     * 출고 리스트 엑셀로 만들기
     * @param $showStock
     * @param $today
     * @param false $isOrder
     * @return string
     * @throws \Exception
     */
    public function get3PlOrderExcel($showStock, $today, $isOrder=false){
        $title = $this->getReleaseListTitle();
        if( $showStock ){
            $title[] = '재고수량';
            $title[] = '재고체크';
        }
        $list = $this->getReleaseList();

        //==> 추가 코드 시작 (재고확인)
        $stockCheckMap = [];
        foreach($list as $each){
            $stockCheckMap[$each['optionCode']]['stockCnt'] = $each['stockCnt'];
            $stockCheckMap[$each['optionCode']]['goodsCnt'] += $each['goodsCnt'];
        }
        //==> 추가 코드 종료

        $refineList = [];
        $orderInfo = '';
        $packingNo = 0;
        $sumPackingOrderNo = '';
        $orderGoodsList = [];

        foreach($list as $key => $val){
            $orderGoodsList[] = $val;
            $currentOrderInfo = $val['receiverName'].$val['address'];

            if( $orderInfo != $currentOrderInfo ){
                $sumPackingOrderNo = $val['orderNo'];
                $orderInfo = $currentOrderInfo;
                $packingNo++;
                $memo = [];
                if( !empty($val['memo1']) ) $memo[] = $val['memo1']; //박스 스티커 체크.
                if( !empty($val['memo2']) ) $memo[] = $val['memo2']; //착불 체크.
                if( !empty($val['memo3']) ) $memo[] = $val['memo3']; //사이즈수량 표기 고객.
                $packingInfo = implode('/', $memo);
                $refineList[$packingNo]['packingInfo'] = $packingInfo; //박스 스티커 및 착불 판단.
                $refineList[$packingNo]['sumPackingCount'] = 0;
            }

            if( 'MSYGCL019' === $val['optionCode'] ){
                //영구 에코
                $orderGoodsCnt = $val['goodsCnt'];
                for( $forIdx=0; $orderGoodsCnt > $forIdx; $forIdx++){
                    $val['goodsCnt'] = 1;
                    $refineList[$packingNo]['data'][] = $val;
                    if($sumPackingOrderNo != $val['orderNo']){
                        $refineList[$packingNo]['sumPackingCount']++;
                    }
                }
                $val['orderMemo'] = '';
            }else{
                //일반
                $refineList[$packingNo]['data'][] = $val;

                if($sumPackingOrderNo != $val['orderNo']){
                    $refineList[$packingNo]['sumPackingCount']++;
                }
            }
        }

        $excelBody = '
        <style>
                .line { background-color:#fde9d9 }
                .fnt-red { color:red }
                .bold { font-weight: bold }
                .center { text-align:center }
                table { font-size:13px; font-family: "맑은 고딕"; }
                td { text-align: center }
        </style>
        ';
        $excelBody .= '<table class="table table-rows" border="1"><tr>';
        foreach ($title as $key => $val) {
            $excelBody .= ExcelCsvUtil::wrapTag($val, "th", '');
        }
        $excelBody .= '</tr>';

        foreach ($refineList as $packingNo => $packingData) {
            $lineColor = '';
            if( 1 !== $packingNo % 2 ){
                $lineColor = 'line';
            }

            $memo = $packingData['packingInfo'];
            if( $packingData['sumPackingCount'] > 0){
                if(empty($packingData['packingInfo'])){
                    $memo = '합포장';
                }else{
                    $memo .= '/합포장';
                }
            }
            foreach( $packingData['data'] as $val ){
                if(34 == $val['scmNo'] && 19964 == $val['memNo']){
                    $this->setAsianaReceiver($val);
                }
                if( 'MSYGCL019' === $val['optionCode'] ){
                    $memo = '';
                }
                $fieldData = array();
                $zoneCode = !empty($val['receiverZoneCode']) && '00000' != $val['receiverZoneCode'] ? $val['receiverZoneCode'] : '';

                $optionCode = $val['optionCode'];
                $productName = $val['productName'];
                $stockCnt = $val['stockCnt'];

                //재고 새로 체크 (대체 코드 있으면 대체 코드로 나간다)
                if( $stockCheckMap[$val['optionCode']]['stockCnt'] >= $val['goodsCnt'] ){
                    $stockCheckMap[$val['optionCode']]['stockCnt'] -= $val['goodsCnt'];
                    $val['notEnough'] = 'n';
                }else{
                    $stockSql = "select aa.code,bb.productName,bb.optionName,bb.stockCnt
                                   from sl_goodsOptionLink aa join sl_3plProduct bb on aa.code = bb.thirdPartyProductCode
                                  where aa.optionSno = '{$val['goodsOptionSno']}'
                                    and bb.stockCnt > 0
                                    and bb.thirdPartyProductCode <> '{$optionCode}'
                                order by aa.sort, aa.regDt limit 1";
                    $alterStockData = DBUtil2::runSelect($stockSql);
                    if(!empty($alterStockData) && count($alterStockData) > 0){
                        $optionCode = $alterStockData[0]['code'];
                        $productName = $alterStockData[0]['productName'];
                        if(!empty($alterStockData[0]['optionName'])){
                            $productName = $productName . '_' . $alterStockData[0]['optionName'];
                        }
                        $stockCnt = $alterStockData[0]['stockCnt'];
                        $val['notEnough'] = 'n';
                    }else{
                        $val['notEnough'] = 'y';
                    }
                }

                $fieldData[] = ExcelCsvUtil::wrapTd($memo,'fnt-red','width:200px'); //운송회사 (특이사항 기입)
                $fieldData[] = ExcelCsvUtil::wrapTd('','','width:200px'); //송장번호
                $fieldData[] = ExcelCsvUtil::wrapTd($val['orderNo'],'text','mso-number-format:\'\@\''); //주문번호
                $fieldData[] = ExcelCsvUtil::wrapTd($val['receiverName']); //고객명
                $fieldData[] = ExcelCsvUtil::wrapTd($zoneCode); //우편번호
                $fieldData[] = ExcelCsvUtil::wrapTd($val['address'],'','width:700px'); //주소
                $fieldData[] = ExcelCsvUtil::wrapTd($val['receiverPhone'],'text','mso-number-format:\'\@\''); //전화
                $fieldData[] = ExcelCsvUtil::wrapTd($val['receiverCellPhone'],'text','mso-number-format:\'\@\''); //핸드폰
                $fieldData[] = ExcelCsvUtil::wrapTd($optionCode); //제품코드
                $fieldData[] = ExcelCsvUtil::wrapTd($productName); //제품명
                $fieldData[] = ExcelCsvUtil::wrapTd(number_format($val['goodsCnt'])); //수량
                $fieldData[] = ExcelCsvUtil::wrapTd($val['orderMemo']); //비고
                $fieldData[] = ExcelCsvUtil::wrapTd($val['scmName']); //주문사이트
                $fieldData[] = ExcelCsvUtil::wrapTd(''); //불량유무
                $fieldData[] = ExcelCsvUtil::wrapTd($today,'text center','mso-number-format:\'\@\''); //출고예정일
                $fieldData[] = ExcelCsvUtil::wrapTd('A0157','center'); //화주코드

                if( $showStock ){
                    $fieldData[] = ExcelCsvUtil::wrapTd(number_format($stockCnt)); //실재고수량
                    if('y' === $val['notEnough']){
                        $fieldData[] = ExcelCsvUtil::wrapTd( '재고부족 ' . $stockCheckMap[$val['optionCode']]['stockCnt'] , 'fnt-red' ); //재고체크
                    }
                }

                $excelBody .=  "<tr class='{$lineColor}'>". implode('',$fieldData) . "</tr>";
            }
        }

        $excelBody .= '</table>';


        $isError = false;

        //주문상태 변경
        if( $isOrder ){
            //SitelabLogger::logger2(__METHOD__, '====> 자동 출고 주문상태 변경 시작');
            //SitelabLogger::logger2(__METHOD__, $orderGoodsList);
            foreach($orderGoodsList as $val){
                if(!empty($val['orderGoodsSno'])){
                    $rslt1 = DBUtil2::update(DB_ORDER_GOODS,['orderStatus' => 'g1'], new SearchVo('sno=?', $val['orderGoodsSno']) ); //주문상품 => 상품준비중 변경
                    $rslt2 = DBUtil2::runSql("update es_orderGoods set orderStatus = 'g1' where sno = {$val['orderGoodsSno']}");
                    SitelabLogger::logger2(__METHOD__, $val['orderNo'] . ':'. $val['orderGoodsSno']. ' => '. $rslt1 . ' // ' . $rslt2);
                    $orderInfo = DBUtil2::getOne(DB_ORDER, 'orderNo', $val['orderNo']);
                    if( 'p1' == $orderInfo['orderStatus'] ){
                        DBUtil2::update(DB_ORDER,['orderStatus' => 'g1'], new SearchVo('orderNo=?', $val['orderNo']) ); //주문 => 상품준비중 변경
                    }
                }else{
                    //골프존은 상관 없음
                    if( 2 <> $val['scmNo'] ){
                        $isError = true;
                        SitelabLogger::logger2(__METHOD__, $val);
                    }
                }
            }
            //SitelabLogger::logger2(__METHOD__, '====> 자동 출고 주문상태 변경 끝');
        }

        //에러 발생시 메일 발송 (골프존 아닌데 orderGoodsSno 가 없음 ?)
        if($isError){
            $todayKr = date('m월d일');
            $subject = '주문건 미변경 확인 _ ' . $todayKr;
            $contents = "<br>주문 오류 확인 <br>";
            $to = 'jhsong@msinnover.com';
            SiteLabMailUtil::sendSimpleMail($subject, $contents, $to);
        }

        return $excelBody;
    }

    /**
     * HTML 형식 아닌 진짜 엑셀 파일로 출력 데이터 전달.
     * @param $reqData
     * @return mixed
     * @throws \Exception
     */
    public function get3PlOrderExcelReal($reqData){
        $isOrder = gd_isset($reqData['isOrder'],false);
        $showStock = gd_isset($reqData['showStock'],false);
        $today = $reqData['today'];
        $subject = $reqData['subject'];
        $html = $this->get3PlOrderExcel($showStock, $today, $isOrder);

        //엑셀로 만들어 두었던 내용을 컨버팅.
        $dom = new \DOMDocument();
        $dom->loadHTML('<?xml encoding="UTF-8">' . $html);
        $tables = $dom->getElementsByTagName('table');
        $maxRowKey = 0;
        $maxColKey = 0;
        $styleMap = [];

        foreach($tables as $table){
            $rows = $table->getElementsByTagName('tr');
            foreach ($rows as $rowKey => $row) {
                $columns = $row->getElementsByTagName('td');
                $rowClass = $row->getAttribute('class');
                foreach ($columns as $colKey => $column) {
                    $cellKey = Cell::stringFromColumnIndex($colKey) . ($rowKey+1);
                    $value = $column->nodeValue;
                    $styleMap[$cellKey] = [
                        'style' => $column->getAttribute('style') ,
                        'class' => $column->getAttribute('class'),
                        'value' => $value,
                        'column' => $cellKey ,
                        'rowClass' => $rowClass ,
                    ];
                    if( $colKey > $maxColKey ) $maxColKey = $colKey;
                }
                if( $rowKey > $maxRowKey ) $maxRowKey = $rowKey;
            }
        }

        $defaultStyle = [
            'font' => [
                'bold' => false,
                'size' => 10,
                'name' => '맑은 고딕',
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER
            ],
            'fill' => [
                'fillType' => Fill::FILL_GRADIENT_LINEAR,
                'rotation' => 90,
                'startColor' => ['argb' => 'FFFF0000'],
                'endColor' => ['argb' => 'FFFF0000']
            ]
        ];

        //파일 가져오기.
        $tmpfile = '/tmp/'.uniqid().'.html';
        file_put_contents($tmpfile, $html);
        $spreadsheet = IOFactory::createReader('Html')->load($tmpfile);
        $spreadsheet->getDefaultStyle()->applyFromArray($defaultStyle);
        $spreadsheet->getProperties()->setCreator("MS INNOVER")
            ->setLastModifiedBy("MS INNOVER")
            ->setTitle($subject)
            ->setSubject($subject)
            ->setDescription($subject);

        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle($subject);

        $maxCol = Cell::stringFromColumnIndex($maxColKey);
        $maxRowKey += 1;

        for ($i = 1; $i <= $maxRowKey; $i++) {
            for ($j = 'A'; $j <= $maxCol; $j++) {
                $cell = $sheet->getStyle($j . $i);
                $cellKey = $j . $i;

                if( $i === 1 ){
                    $cell->getFont()->setBold(true);
                }
                $cell->getBorders()->getOutline()->setBorderStyle(Border::BORDER_THIN);
                $cell->getBorders()->getOutline()->getColor()->setARGB('FF000000');

                if( ( 'R' === $j || 'A' === $j) && 1 !== $i ){
                    $font = $cell->getFont();
                    $font->setColor(new Color('FF0000'));
                    $cell->setFont($font);
                    $cell->getFont()->setColor(new Color('FFFF0000'));
                }
                if('C' === $j && $i !== 1 ){
                    $sheet->setCellValueExplicit('C'.$i, ''.$styleMap['C'.$i]['value'].''  , \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                    $cell->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);
                }

                if( 'line' === $styleMap[$cellKey]['rowClass'] ){
                    $cell->getFill()->setFillType(Fill::FILL_SOLID);
                    $cell->getFill()->getStartColor()->setARGB('fffde9d9');
                }
            }
        }

        $sheet->getColumnDimension("A")->setWidth(20);
        $sheet->getColumnDimension("B")->setWidth(17);
        $sheet->getColumnDimension("C")->setWidth(20);//주문번호
        $sheet->getColumnDimension("D")->setWidth(21);
        $sheet->getColumnDimension("E")->setWidth(12);
        $sheet->getColumnDimension("F")->setWidth(100);
        $sheet->getColumnDimension("G")->setWidth(13);
        $sheet->getColumnDimension("H")->setWidth(14);
        $sheet->getColumnDimension("I")->setWidth(13);
        $sheet->getColumnDimension("J")->setWidth(35);
        $sheet->getColumnDimension("K")->setWidth(9);
        $sheet->getColumnDimension("L")->setWidth(34);
        $sheet->getColumnDimension("M")->setWidth(23);
        $sheet->getColumnDimension("N")->setWidth(13);
        $sheet->getColumnDimension("O")->setWidth(13);
        $sheet->getColumnDimension("P")->setWidth(13);

        unlink( $tmpfile );
        return $spreadsheet;
    }


    /**
     * 출고 데이터 엑셀 전환 (즉시 다운로드)
     * @param $reqData
     * @throws \Exception
     */
    public function makeFileRealExcel($reqData){
        $fileName = $reqData['fileName'];
        $spreadsheet = $this->get3PlOrderExcelReal($reqData);
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'.$fileName.'"');
        header('Cache-Control: max-age=0');
        $writer = new Xls($spreadsheet);
        $writer->save('php://output');
    }

    /**
     * 출고 데이터 엑셀 전환 (파일 패스 전달)
     * @param $reqData
     * @return string
     * @throws \Exception
     */
    public function makeFileRealExcel2($reqData){
        $fileName = $reqData['fileName'];
        $spreadsheet = $this->get3PlOrderExcelReal($reqData);
        $filePath = UserFilePath::data('etc')->getRealPath() . '/order/'.$fileName ;
        $writer = new Xls($spreadsheet);
        $writer->save($filePath);
        return $filePath;
    }

    /**
     * 출고 이력 등록
     * 삼영등록이력과 별개로 등록한다.
     * @param $start
     * @param $end
     * @param string $mode
     * @throws \Exception
     */
    public function reg3plOutHistory($start, $end, $mode='real'){

        if( 'real' === $mode){
            $outList = SamYoungService::requestOutHistoryData($start, $end);
            $fieldName = [
                'owner' => 'K09OwnerCd',
                'code' => 'K09NickNm1',
                'count' => 'K09JpmXQty',
                'custName' => 'CustName',
                'cellPhone' => 'K36CusHpNo',
                'outDate' => 'K09SlipDtx',
                'invoice' => 'K36INVOCENO',
                'orderNo' => 'K36MeMoxxx',
                'site' => 'K09OwnerCd',
            ];
        }else{
            $outList = SamYoungService::requestWaitOutHistoryData();
            $fieldName = [
                'owner' => 'K36OwnerCd',
                'code' => 'K36NickNm1',
                'count' => 'K36OutxQty',
                'custName' => 'K36CusGbNm',
                'cellPhone' => 'K36CusHpNo',
                'outDate' => 'K36SlipDtx',
                'invoice' => 'K36InvoceNo',
                'orderNo' => 'K36MeMoxxx',
                'site' => 'K36OrderSite',
            ];
        }


        $orderNoList = [];
        $isGolfOrder = false;

        //처리 횟수.
        $procCnt = 0;
        foreach($outList as $each){

            if( 'A0157' !== $each[$fieldName['owner']] ) continue;  //K36OwnerCd

            //시퀀스로 저장 여부 체크
            $existsData = DBUtil2::getOne('sl_3plStockInOut', 'seq', $each['SEQ']);

            $code = $each[$fieldName['code']]; //K36NickNm1
            $count = $each[$fieldName['count']]; //K36OutxQty
            $custName = $each[$fieldName['custName']]; //K36CusGbNm
            $cellPhone = $each[$fieldName['cellPhone']]; //K36CusHpNo
            $outDate = $each[$fieldName['outDate']]; //K36SlipDtx
            $invoice = $each[$fieldName['invoice']]; //K36InvoceNo
            $orderNo = $each[$fieldName['orderNo']]; //K36MeMoxxx

            //골프존 주문 있는지 여부
            if( false === $isGolfOrder && '골프존' === $each[$fieldName['site']] ) $isGolfOrder = true;

            //시퀀스에 해당하는 이력 없을 때 등록.
            if(empty($existsData)){
                $product = DBUtil2::getOne('sl_3plProduct', 'thirdPartyProductCode', $code);

                //기본 저장 데이터 셋팅.
                $outStockData = [
                    'productSno' => $product['sno'],
                    'payedFl' => $product['payedFl'],
                    'workPayedFl' => $product['workPayedFl'],
                    'thirdPartyProductCode' => $code,
                    'inOutType' => ErpCodeMap::ERP_STOCK_TYPE['출고'],
                    'inOutReason' => ErpCodeMap::ERP_STOCK_REASON['정기출고'],
                    'inOutDate' => $outDate,
                    'quantity' => $count,
                    'managerSno' => 1,
                    'orderNo' => $orderNo,
                    'identificationText' => $each['SEQ'],
                    'seq' => $each['SEQ'],
                    'memo' => $custName.' '.$cellPhone,
                    'invoiceNo' => $invoice,
                    'phone' => $each['K36CusTelNo'],
                    'cellPhone' => $cellPhone,
                    'address' => $each['K36CusAddress'],
                    'customerName' => $custName,
                ];

                //고도몰 주문일 경우 배송중 처리
                $this->checkGodoOrder([
                    'code' =>$each[$fieldName['code']],
                    'count' =>$each[$fieldName['count']],
                    'custName' =>$each[$fieldName['custName']],
                    'cellPhone' =>$each[$fieldName['cellPhone']],
                    'outDate' =>$each[$fieldName['outDate']],
                    'invoice' =>$each[$fieldName['invoice']],
                    'orderNo' =>$each[$fieldName['orderNo']],
                ], $outStockData);

                //----------------------------------------------

                //수기 주문 조회 (골프존).
                $manualOrderData = DBUtil2::getOneBySearchVo('sl_3plOrderTmp', new SearchVo([
                        'customerName=?',
                        'replace(mobile,\'-\',\'\')=?',
                        'productCode=?',
                        'qty=?',
                    ]
                    ,[
                        $custName,
                        str_replace('-','',$cellPhone),
                        $code,
                        $count,
                    ])
                );
                if(!empty($manualOrderData)){
                    //SitelabLogger::logger('Step2-Golf. 수기 주문 변경');
                    $deleteSno = $manualOrderData['sno'];
                    unset($manualOrderData['sno']);
                    $manualOrderData['orderDt'] = $outDate;
                    $manualOrderData['invoiceNo'] = $invoice;
                    DBUtil2::insert('sl_3plOrderHistory',$manualOrderData);
                    DBUtil2::delete('sl_3plOrderTmp',new SearchVo('sno=?', $deleteSno));
                }

                //출고 이력 등록
                //SitelabLogger::logger2(__METHOD__, $outStockData);
                DBUtil2::insert(ErpService::DB_3PL_IN_OUT, $outStockData);
                $procCnt++;
            }
        }

        //고도몰 변경대상 주문이 있다면 처리.
        foreach($orderNoList as $orderValue){
            $orderData = DBUtil2::getOne(DB_ORDER,'orderNo',$orderValue['orderNo']);
            if('g1' === $orderData['orderStatus'] || 'g3' === $orderData['orderStatus']){
                DBUtil2::update(DB_ORDER, ['orderStatus' => 'd1'], new SearchVo('orderNo=?', $orderValue['orderNo']));
                //아시아나는 배송알림 X
                if( 34 != $orderValue['orderNo'] ){
                    $orderComponent = SlLoader::cLoad('Order','Order');
                    $orderComponent->sendOrderInfo(Code::DELIVERY, 'all', $orderValue['orderNo']);
                    $orderComponent->sendOrderInfo(Code::INVOICE_CODE, 'sms', $orderValue['orderNo']);
                }
            }
        }

        //골프존 송장 등록 메일 발송 / 휴일은 실행하지 않음
        if($isGolfOrder && !SlCommonUtil::isHoliday() ){
            $this->sendInvoiceMail();
        }
     
        //송장 처리 후 재고 연동
        $this->update3plStock();

        //창고 재고 연동 후 집계 값 설정 (5분마다 도는 재고 집계 배치랑 겹쳐서 실행 안함)
        //$batchService = SlLoader::cLoad('batch','BatchService','sl');
        //$batchService->runRefineStockCnt();
    }

    /**
     * 출고 이력 등록 (심플버전, 송장 작업 안함 오직 출고 등록)
     * @param $start
     * @param $end
     * @param string $mode
     * @throws \Exception
     */
    public function regSimple3plOutHistory($start, $end){
        $outList = SamYoungService::requestOutHistoryData($start, $end);
        //gd_debug($start . ' / ' . $end);
        //gd_debug($outList);
        $fieldName = [
            'owner' => 'K09OwnerCd',
            'code' => 'K09NickNm1',
            'count' => 'K09JpmXQty',
            'custName' => 'CustName',
            'cellPhone' => 'K36CusHpNo',
            'outDate' => 'K09SlipDtx',
            'invoice' => 'K36INVOCENO',
            'orderNo' => 'K36MeMoxxx',
            'site' => 'K09OwnerCd',
        ];
        //처리 횟수.
        $procCnt = 0;
        foreach($outList as $each){
            if( 'A0157' !== $each[$fieldName['owner']] ) continue;  //K36OwnerCd
            //시퀀스로 저장 여부 체크
            $existsData = DBUtil2::getOne('sl_3plStockInOut', 'seq', $each['SEQ']);
            $code = $each[$fieldName['code']]; //K36NickNm1
            $count = $each[$fieldName['count']]; //K36OutxQty
            $custName = $each[$fieldName['custName']]; //K36CusGbNm
            $cellPhone = $each[$fieldName['cellPhone']]; //K36CusHpNo
            $outDate = $each[$fieldName['outDate']]; //K36SlipDtx
            $invoice = $each[$fieldName['invoice']]; //K36InvoceNo
            $orderNo = $each[$fieldName['orderNo']]; //K36MeMoxxx
            //시퀀스에 해당하는 이력 없을 때 등록.
            if(empty($existsData)){
                $product = DBUtil2::getOne('sl_3plProduct', 'thirdPartyProductCode', $code);
                //기본 저장 데이터 셋팅.
                $outStockData = [
                    'productSno' => $product['sno'],
                    'payedFl' => $product['payedFl'],
                    'workPayedFl' => $product['workPayedFl'],
                    'thirdPartyProductCode' => $code,
                    'inOutType' => ErpCodeMap::ERP_STOCK_TYPE['출고'],
                    'inOutReason' => ErpCodeMap::ERP_STOCK_REASON['정기출고'],
                    'inOutDate' => $outDate,
                    'quantity' => $count,
                    'managerSno' => 1,
                    'orderNo' => $orderNo,
                    'identificationText' => $each['SEQ'],
                    'seq' => $each['SEQ'],
                    'memo' => $custName.' '.$cellPhone,
                    'invoiceNo' => $invoice,
                    'phone' => $each['K36CusTelNo'],
                    'cellPhone' => $cellPhone,
                    'address' => $each['K36CusAddress'],
                    'customerName' => $custName,
                ];
                //----------------------------------------------

                //출고 이력 등록
                DBUtil2::insert(ErpService::DB_3PL_IN_OUT, $outStockData);
                $procCnt++;
            }
        }
    }

    /**
     * 입고 이력 등록
     * @param $start
     * @param $end
     * @return int
     * @throws \Exception
     */
    public function reg3plInHistory($start, $end){
        $procCnt = 0;
        $inList = SamYoungService::requestInHistoryData($start, $end);
        foreach($inList as $each){
            if( 'A0157' !== $each['K16OwnerCd'] ) continue;

            //시퀀스로 저장 여부 체크
            $existsData = DBUtil2::getOne('sl_3plStockInOut', 'seq', $each['SEQ']);
            $code = $each['K16NickNm1'];
            $count = $each['K16JpmsXQty'];
            $inDate = $each['K16SlipDtx'];
            $memo = $each['K16MeMoxxx'];

            //시퀀스에 해당하는 이력 없을 때 등록.
            if(empty($existsData)){
                $product = DBUtil2::getOne('sl_3plProduct', 'thirdPartyProductCode', $code);

                //기본 저장 데이터 셋팅.
                $inStockData = [
                    'productSno' => $product['sno'],
                    'payedFl' => $product['payedFl'],
                    'workPayedFl' => $product['workPayedFl'],
                    'thirdPartyProductCode' => $code,
                    'inOutType' => ErpCodeMap::ERP_STOCK_TYPE['입고'],
                    'inOutReason' => SamYoungService::getInputReason($memo)?ErpCodeMap::ERP_STOCK_REASON['교환입고']:ErpCodeMap::ERP_STOCK_REASON['정기입고'],
                    'inOutDate' => $inDate,
                    'quantity' => $count,
                    'memo' => $memo,
                    'managerSno' => 1,
                    'identificationText' => $each['SEQ'],
                    'seq' => $each['SEQ'],
                ];

                //입고 이력 등록
                DBUtil2::insert(ErpService::DB_3PL_IN_OUT, $inStockData);
                $procCnt++;
            }
        }
        return $procCnt;
    }

    /**
     * 고도몰 주문 체크 처리
     * @param $params
     * @param $outStockData
     * @throws \Exception
     */
    public function checkGodoOrder($params, $outStockData){
        //고도몰 주문 조회 ---------------------------
        $godoOrderList = $this->getGodoOrder([
            'receiverName'=>$params['custName'],
            'receiverCellPhone'=>$params['cellPhone'],
            'code'=>$params['code'],
            //'goodsCnt'=>$params['count'],  //수량은 분할해서 처리될 수 있음. (기존 로직 보다 완화)
        ]);

        //고도몰 주문 검색된다면 배송중 처리 및 송장 등록
        if(!empty($godoOrderList) && count($godoOrderList) > 0 ){
            foreach($godoOrderList as $godoOrder){
                //교환 대상 상품이라면 (정확하지는 않다, 교환프로세스 안타고 그냥 출고하는 경우도 있어서, 참고만한다.)
                if( !empty($godoOrder['handleSno']) ){
                    $outStockData['inOutReason'] = ErpCodeMap::ERP_STOCK_REASON['교환출고'];
                }
                $outStockData['memNo'] = $godoOrder['memNo'];
                $outStockData['orderDeliverySno'] = $godoOrder['orderDeliverySno'];
                $outStockData['orderGoodsSno'] = $godoOrder['orderGoodsSno'];
                $outStockData['orderNo'] = $godoOrder['orderNo'];

                //고도몰 주문 업데이트
                $godoUpdateData = [
                    'orderStatus'=>'d1',
                    'invoiceCompanySno'=>'8',
                    'invoiceNo'=>$params['invoice'],
                    'deliveryDt'=>'now()',
                ];
                DBUtil2::update(DB_ORDER_GOODS, $godoUpdateData, new SearchVo('sno=?', $godoOrder['orderGoodsSno'] ) );
                $orderNoList[] = [
                    'scmNo' => $godoOrder['scmNo'],
                    'orderNo' => $godoOrder['orderNo'],
                ];
            }
        }
        //고도몰 판매중인 상품이 있으면 판매수량 차감, 수기 주문 처리) => 판매수량은 주문할 때 빼고는 절대 건들지 않게한다.
    }

    /**
     * 고도몰 주문 조회. (아래 필드 필수 => 검색키)
     * receiverName
     * receiverCellPhone
     * code
     * goodsCnt
     *
     * @param $searchParam
     * @return mixed
     */
    public function getGodoOrder($searchParam){
        $phone = str_replace('-','', $searchParam['receiverCellPhone']);
        $sql = "
                select *, a.sno as orderGoodsSno from 
                es_orderGoods a 
                join es_orderInfo b on a.orderNo = b.orderNo 
                join es_goodsOption c on a.optionSno = c.sno
                join es_order d on a.orderNo = d.orderNo
                where b.receiverName = '{$searchParam['receiverName']}'
                and REPLACE(b.receiverCellPhone, '-', '') = '{$phone}'
                and EXISTS (
                    SELECT 1
                    FROM sl_goodsOptionLink l
                    WHERE l.optionSno = c.sno
                      AND l.code = '{$searchParam['code']}'
                )  
                -- and c.sno IN ( select optionSno from sl_goodsOptionLink where code = '{$searchParam['code']}' ) --기존코드
                -- and a.goodsCnt = {$searchParam['goodsCnt']}
                and a.orderStatus in ('g1','g3')  
        ";
        return DBUtil2::runSelect($sql); //여러개 나올 수 있음
    }

    /**
     * 골프존 송장 등록 알림
     * @return bool
     */
    public function sendInvoiceMail(){
        $rslt = false;
        //1. orderTmp 비어 있는지 확인.
        $count = DBUtil2::getCount('sl_3plGolfInvoiceHistory',new SearchVo('regDt>=?', date('Y-m-d').' 00:00:00'));
        $orderTmp = DBUtil2::getCount('sl_3plOrderTmp',new SearchVo('qty>=?','1'));

        if( empty($orderTmp) && empty($count) ){
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
            //메일 발송 이력 기록.
            DBUtil2::insert('sl_3plGolfInvoiceHistory',['orderCnt'=>0,'goodsCnt'=>0]);
            $rslt = true; //메일 발송.
        }
        return $rslt;
    }

    /**
     * 3PL 수량 업데이트
     */
    public function update3plStock(){
        $erpService = SlLoader::cLoad('erp','erpService');
        $prdList = SamYoungService::get3PlStock();
        foreach( $prdList['rows'] as $each ){
            $updateData = [
                5=>$each['K02PUMGBN1'],
                7=>$each['K02PUMGBN2'],
                20=>$each['SCurrentQty'],
            ];
            $erpService->saveEachProduct($updateData);
        }
        $erpService->set3PlAttribute();

        //수량 업데이트 후에는 집계 처리
        $this->summarizeStock();
    }


    /**
     * 상품에 연결된
     * 재고 현황 갱신
     */
    public function summarizeStock(){
        //집계 전 마이너스 처리된 건은 0으로 변경
        $sql = "update sl_3plProduct set stockCnt = 0 where 0 > stockCnt";
        DBUtil2::runSql($sql);

        $filePath = './module/SlComponent/Godo/Sql/sql_refine_in_out.sql';
        $preparedSql = SlCommonUtil::getFileData($filePath);
        DBUtil2::runSql($preparedSql);

        //Stock In , Out Cnt 등록
        DBUtil2::runSql("truncate table sl_goodsOptionExt");
        $filePath = './module/SlComponent/Godo/Sql/sql_refine_stock.sql';
        $sql = SlCommonUtil::getFileData($filePath);
        DBUtil2::runSql($sql);
    }



}
