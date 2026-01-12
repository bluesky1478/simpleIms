<?php
namespace Component\Erp;

use App;
use Component\Database\DBTableField;
use Component\Member\Manager;
use Component\Scm\ScmTkeService;
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
use SlComponent\Util\SitelabLogger;
use SlComponent\Util\SlCodeMap;
use SlComponent\Util\SlLoader;
use Component\Sms\Code;

/**
 * ERP 서비스
 * Class GoodsStock
 * @package Component\Goods
 */
class ErpService {

    const DB_3PL_PRODUCT = 'sl_3plProduct';
    const DB_3PL_IN_OUT = 'sl_3plStockInOut';

    const INIT_PRD_INPUT_FIELD = [
        'thirdPartyProductCode'=>5,
        'productName'=>7,
        'stockCnt'=>20,
    ];

    const INPUT_FIELD = [
        'inDate'=>1,
        'thirdPartyProductCode'=>2,
        'productName'=>3,
        'quantity'=>6,
        'memo'=>13,
        'regDate'=>14,
        'regTime'=>15,
    ];

    const OUTPUT_FIELD = [
        'outDate' => 1,
        'invoice' => 12,
        'orderNo' => 14,
        'thirdPartyProductCode' => 8,
        'customerName' => 3,
        'address' => 5,
        'phone' => 6,
        'cellPhone' => 7,
        'quantity' => 13,
        'regDate'=>19,
        'regTime'=>20,
    ];

    const SCM_DIVIDE_CONTENTS = [
        'TS' => ['scmNo'=>6,'scmName'=>'한국타이어'],
        'HK' => ['scmNo'=>6,'scmName'=>'한국타이어'],
        '티스테이션' => ['scmNo'=>6,'scmName'=>'한국타이어'],
        'TBX' => ['scmNo'=>6,'scmName'=>'한국타이어'],
        'TTS' => ['scmNo'=>6,'scmName'=>'한국타이어'],
        'TKE' => ['scmNo'=>8,'scmName'=>'TKE(티센크루프)'],
        '한전' => ['scmNo'=>20,'scmName'=>'한전산업개발'],
        '티케이엘리베이터' => ['scmNo'=>8,'scmName'=>'TKE(티센크루프)'],
        '골프존' => ['scmNo'=>2,'scmName'=>'골프존'],
        '무영' => ['scmNo'=>10,'scmName'=>'(주)무영씨엠건축사무소'],
        'KTNG' => ['scmNo'=>15,'scmName'=>'KTNG'],
        'OTIS(FOD)' => ['scmNo'=>26,'scmName'=>'오티스(FOD)'],
        '오티스(FOD)' => ['scmNo'=>26,'scmName'=>'오티스(FOD)'],
        'OTIS(OEK)' => ['scmNo'=>21,'scmName'=>'오티스(OEK)'],
        '오티스(OEK)' => ['scmNo'=>21,'scmName'=>'오티스(OEK)'],
        'OTIS(OSE)' => ['scmNo'=>11,'scmName'=>'오티스(OSE)'],
        '오티스(OSE)' => ['scmNo'=>11,'scmName'=>'오티스(OSE)'],
        'OTIS' => ['scmNo'=>11,'scmName'=>'오티스(OSE)'],
        '설치' => ['scmNo'=>16,'scmName'=>'미쓰비시엘레베이터(설치)'],
        '미쓰비시' => ['scmNo'=>14,'scmName'=>'미쓰비시엘레베이터'],
        '영구크린' => ['scmNo'=>12,'scmName'=>'영구크린'],
        '영구이사' => ['scmNo'=>12,'scmName'=>'영구크린'],
        '제일건설' => ['scmNo'=>4,'scmName'=>'제일건설 주식회사'],
        '혼다' => ['scmNo'=>3,'scmName'=>'혼다코리아'],
        '맥스' => ['scmNo'=>5,'scmName'=>'맥스'],
        '린나이' => ['scmNo'=>9,'scmName'=>'린나이'],
        '한국공항' => ['scmNo'=>22,'scmName'=>'한국공항'],
        '동양건설' => ['scmNo'=>23,'scmName'=>'동양건설'],
        '라인건설' => ['scmNo'=>23,'scmName'=>'동양건설'],
        '반도건설(총무팀)' => ['scmNo'=>29,'scmName'=>'반도건설(총무팀)'],
        '반도건설' => ['scmNo'=>24,'scmName'=>'반도건설'],
        '빙그레' => ['scmNo'=>25,'scmName'=>'빙그레'],
        '퍼시스' => ['scmNo'=>30,'scmName'=>'퍼시스'],
        '타타대우' => ['scmNo'=>31,'scmName'=>'타타대우'],
        '현대' => ['scmNo'=>32,'scmName'=>'현대엘리베이터'],
        '00300' => ['scmNo'=>35,'scmName'=>'이준석캠프'],
        '아시아나' => ['scmNo'=>34,'scmName'=>'아시아나'],
        '파이널' => ['scmNo'=>48,'scmName'=>'파이널체대입시'],
    ];

    private $sql;

    public function __construct(){
        $this->sql =  SlLoader::sqlLoad(__CLASS__, false);
    }

    /**
     * @deprecated : 재고 수량은 무조건 삼영에 맞춘다 ( 9시 , 15시 부터 30분 송장 등록 로직에 갱신 프로세스 이용 )
     * 재고 업데이트
     * @param $prdSno
     * @param $stockCnt
     * @throws \Exception
     */
    public function updateStock($prdSno, $stockCnt){
        //$searchVo = new SearchVo('sno=?', $prdSno);
        //$beforeProductData = DBUtil2::getOneBySearchVo(ErpService::DB_3PL_PRODUCT, $searchVo);
        //DBUtil2::update(ErpService::DB_3PL_PRODUCT, ['stockCnt'=> ( $beforeProductData['stockCnt']+$stockCnt) ] , $searchVo);
    }

    /**
     * 출고 등록
     * @param $each
     * @param $key
     * @param $mixData
     * @throws \Exception
     */
    public function saveEachOutStock($each, $key, &$mixData){
        $excelField = $mixData['excelField'];
        $saveData = [
            'goodsCnt' => $this->getExcelData($each,'quantity', $excelField),
            'memo' => $this->getExcelData($each,'invoice', $excelField),
            'orderNo' => $this->getExcelData($each,'orderNo', $excelField),
            'outDate' => $this->getExcelData($each,'outDate', $excelField),
            'thirdPartyProductCode' => $this->getExcelData($each,'thirdPartyProductCode', $excelField),
        ];
        $this->insertOutStock($saveData, $key, $mixData);
    }

    /**
     * 입고 정보 입력 ( 파일로 등록 -> 추후 매크로 트리거 강구 )
     * @param $each
     * @param $key
     * @param $mixData
     * @throws \Exception
     */
    public function saveEachInputStock($each, $key, &$mixData){
        $productData = $this->getDivide3PlProductName($this->getExcelData($each,'productName'));
        $productData['thirdPartyCode'] = 1;
        $productData['thirdPartyProductCode'] = $this->getExcelData($each,'thirdPartyProductCode');
        //Step1 : 상품을 검색한다. 없으면 삽입.
        $prdSno = $this->insertProduct($productData);

        if( empty($prdSno) ) return;

        //$each
        $quantity = $this->getExcelData($each,'quantity');

        $memo = $this->getExcelData($each,'memo');
        $managerSno = \Session::get('manager.sno');
        $inDate = $this->getExcelData($each,'inDate');

        $inStockData = [
            'productSno' => $prdSno,
            'thirdPartyProductCode' => $productData['thirdPartyProductCode'],
            'inOutType' => ErpCodeMap::ERP_STOCK_TYPE['입고'],
            'inOutReason' => ErpService::getDivide3PlInputType($memo),
            'inOutDate' => $inDate,
            'quantity' => $quantity,
            'memo' => $memo,
            'managerSno' => $managerSno,
        ];

        //third code , inoutdate , qty , memo
        $searchInOut = DBUtil2::getCount( ErpService::DB_3PL_IN_OUT , new SearchVo(
            [
                'thirdPartyProductCode=?',
                'inOutDate=?',
                'quantity=?',
                'memo=?'
            ],[
            $inStockData['thirdPartyProductCode'],
            $inStockData['inOutDate'],
            $inStockData['quantity'],
            $inStockData['memo'],
        ]) );

        if( empty($searchInOut) ){
            //이력 삽입.
            DBUtil2::insert(ErpService::DB_3PL_IN_OUT, $inStockData);
            //Step1-1 : 수량 업데이트
            //$this->updateStock($prdSno, $quantity); //입고이력이 입력되었다고 해서 수량을 갱신하지는 않는다.
        }
    }

    /**
     * 3PL 상품 등록
     * @param $productData
     * @return mixed
     * @throws \Exception
     */
    public function insertProduct($productData){
        $excludeData = DBUtil2::getOne('sl_3plProductExclude', 'thirdPartyProductCode', $productData['thirdPartyProductCode']);
        if( empty($productData['stockCnt']) && !empty($excludeData)) {
            return false; //재고 들어온건 다시 집어 넣는다.
        }else{
            //상관 없는건 지운다. (다음엔 다시 들어오게)
            if( !empty($productData['stockCnt']) && !empty($excludeData)){
                DBUtil2::runSql("delete from sl_3plProductExclude where thirdPartyProductCode = '{$productData['thirdPartyProductCode']}'");
            }
        }
        $searchVo = new SearchVo('thirdPartyProductCode=?',$productData['thirdPartyProductCode']);
        $searchProductData = DBUtil2::getOneBySearchVo(ErpService::DB_3PL_PRODUCT, $searchVo);
        if(empty($searchProductData)){
            $scmInfo = $this->getScmInfoBy3PlProductName($productData['productName']);
            $productData['scmNo'] = $scmInfo['scmNo'];
            $productData['scmName'] = $scmInfo['scmName'];
            $productData['attr2'] = $scmInfo['season'];
            $productData['attr5'] = date('y'); //생산년도
            //상품 등록
            $prdSno = DBUtil2::insert(ErpService::DB_3PL_PRODUCT, $productData);
        }else{
            $prdSno = $searchProductData['sno'];
            DBUtil2::update(ErpService::DB_3PL_PRODUCT, ['stockCnt'=>$productData['stockCnt']], $searchVo);
        }
        return $prdSno;
    }


    /**
     * 3PL 상품 정보 등록
     * @param $each
     * @param $key
     * @param $mixData
     * @throws \Exception
     */
    public function saveEachProduct($each, $key, &$mixData){
        $excelCode = ErpService::INIT_PRD_INPUT_FIELD;
        $productData = $this->getDivide3PlProductName($this->getExcelData($each,'productName', $excelCode));
        $productData['thirdPartyCode'] = 1;
        $productData['thirdPartyProductCode'] = $this->getExcelData($each,'thirdPartyProductCode', $excelCode);
        $productData['stockCnt'] = $this->getExcelData($each,'stockCnt', $excelCode);
        if( !empty($productData['thirdPartyProductCode']) ){
            $this->insertProduct($productData);
        }
    }


    /**
     * 엑셀 데이터 가져오기
     * @param $data
     * @param $fieldName
     * @param int[] $code
     * @return string
     */
    public function getExcelData($data, $fieldName, $code = ErpService::INPUT_FIELD){
        return trim($data[$code[$fieldName]]);
    }


    /**
     * 3PL 상품명 분기
     * @param $productName
     * @return array
     */
    public function getDivide3PlProductName($productName){
        $productNameArray = explode('_',$productName);
        if(count($productNameArray) > 1){
            //옵션 있음
            $productOption = array_pop($productNameArray);
            $refineProductName = implode('_', $productNameArray);
        }else{
            //단일 상품(옵션 없음)
            $refineProductName = $productName;
            $productOption = '';
        }
        return [
            'productName' => $refineProductName,
            'optionName' => $productOption,
        ];
    }


    /**
     * 상품명으로 고객사명/번호 반환
     * @param $productName
     * @return array
     */
    public function getScmInfoBy3PlProductName($productName){
        $season = '';
        $seasonStrList = ['하계','춘추','동계'];
        foreach ($seasonStrList as $each) {
            if (strpos($productName, $each) !== false) {
                $season = $each;
                break;
            }
        }

        $scmName = explode(' ',explode('-',explode('_',$productName)[0])[0])[0];
        $scmNo = 0;
        foreach( ErpService::SCM_DIVIDE_CONTENTS as $scmCompareName => $scmContents ){
            if (strpos($scmName, $scmCompareName) !== false) {
                $scmName = $scmContents['scmName'];
                $scmNo = $scmContents['scmNo'];
                break;
            }
        }
        return [
            'scmNo' => $scmNo,
            'scmName' => $scmName,
            'season' => $season,
        ];
    }

    /**
     * 메모를 통한 입고 타입 반환
     * @param $memo
     * @return int
     */
    public static function getDivide3PlInputType($memo){
        $compareList = [
            '교환' => '교환입고',
            '반품' => '반품입고',
            '샘플' => '샘플입고',
            '회송' => '반품입고',
            '반송' => '반품입고',
        ];
        $inputType = 1;
        foreach( $compareList as $compareKey => $inputTypeKr ){
            if (strpos($memo, $compareKey) !== false) {
                $inputType = ErpCodeMap::ERP_STOCK_REASON[$inputTypeKr];
                break;
            }
        }
        return $inputType;
    }


    /**
     * 주문번호로 출고 등록
     * @param $orderNo
     * @return mixed
     */
    public function insertOutStockByOrderGoods($arrData, $orderNo){
        $orderData = DBUtil2::getOne(DB_ORDER, 'orderNo', $orderNo);
        $orderGoodsDataList = [];
        foreach( $arrData['sno'] as $orderGoodsSno ){
            $orderGoodsData = DBUtil2::getOne(DB_ORDER_GOODS, 'sno', $orderGoodsSno);
            $orderGoodsData['memo'] = $orderGoodsData['invoiceNo'];
            $orderGoodsDataList[] = $orderGoodsData;
        }
        SlCommonUtil::setEachData($orderGoodsDataList, $this, 'insertOutStock', $orderData); //setEachData($loopList, $instant, $fncName, &$mixData=null)
        return $each;
    }

    /**
     * 출고 정보 등록
     * @param $each
     * @param $key
     * @param $mixData
     * @return mixed
     * @throws \Exception
     */
    public function insertOutStock($each, $key, &$mixData){

        $reason = '정기출고';
        if( !empty($each['handleSno']) ){
            $reason = '교환출고';
        }
        //정확성.
        if( empty($each['thirdPartyProductCode']) ){
            $goodsOption = DBUtil2::getOne(DB_GOODS_OPTION, 'sno', $each['optionSno']);
            $code = $goodsOption['optionCode'];
        }else{
            $code = $each['thirdPartyProductCode'];
        }

        $outDate = gd_isset($each['outDate'], date('Y-m-d')); // 출고일자 등록 안되어 있으면 오늘로.

        if( !empty($code) ){
            $product = DBUtil2::getOne('sl_3plProduct', 'thirdPartyProductCode', $code);

            $outStockData = [
                'productSno' => $product['sno'],
                'thirdPartyProductCode' => $code,
                'inOutType' => ErpCodeMap::ERP_STOCK_TYPE['출고'],
                'inOutReason' => ErpCodeMap::ERP_STOCK_REASON[$reason],
                'inOutDate' => $outDate,
                'quantity' => $each['goodsCnt'],
                'memNo' => $mixData['memNo'],
                'managerSno' => \Session::get('manager.sno'),
                'orderNo' => $each['orderNo'],
                'orderDeliverySno' => $each['orderDeliverySno'],
                'identificationText' => $each['identificationText'],
                'memo' => $each['memo'],
                'invoiceNo' => $each['invoice'],
            ];

            //출고 정보 삽입.
            if( !empty($product['sno']) ){
                $searchInOut = $this->getRealOutStock($code, $each['orderDeliverySno']);
                if( empty($searchInOut) ){
                    DBUtil2::insert(ErpService::DB_3PL_IN_OUT, $outStockData);
                    //$this->updateStock($outStockData['productSno'], ($outStockData['quantity']*-1) );
                }
            }else{
                DBUtil2::insert('sl_3plOrderNotProduct', $outStockData);
                $mixData['isNotProductCode'] = true;
            }
        }

        return $each;
    }

    public function checkRealOutStockByOrderNo($orderNo){
        $orderData = DBUtil2::getOne(DB_ORDER, 'orderNo', $orderNo);
        $orderGoodsDataList = DBUtil2::getList(DB_ORDER_GOODS, 'orderNo', $orderNo);
        SlCommonUtil::setEachData($orderGoodsDataList, $this, 'checkRealOutStock', $orderData);
    }
    public function checkRealOutStock($each, $key, &$mixData){
        $goodsOption = DBUtil2::getOne(DB_GOODS_OPTION, 'sno', $each['optionSno']);
        $code = $goodsOption['optionCode'];

        $searchInOut = $this->getRealOutStock($code, $each['orderDeliverySno']);
        if(!empty($searchInOut)){
            $searchInOut['deleteSno'] = $searchInOut['sno'];
            DBUtil2::insert('sl_3plStockInOutDelete', $searchInOut);
            DBUtil2::delete(self::DB_3PL_IN_OUT,new SearchVo('sno=?', $searchInOut['sno']));
        }
    }

    /**
     * 출고이력 확인 (코드와 배송 번호로)
     * @param $code
     * @param $orderDeliverySno
     * @return mixed
     */
    public function getRealOutStock($code, $orderDeliverySno){
        return DBUtil2::getOneBySearchVo( ErpService::DB_3PL_IN_OUT , new SearchVo(
            [
                'thirdPartyProductCode=?',
                'orderDeliverySno=?',
                'orderDeliverySno!=0',
            ],[
            $code,
            $orderDeliverySno,
        ]) );
    }


    /**
     * 재고 이력 Summary
     * 입/출고일자 | 구분 | 사유 | 고객사 | 상품코드 | 상품명 | 옵션 | 수량
     * @param $searchData
     * @return mixed
     */
    public function getSummaryInOutList($searchData){
        $list = $this->sql->selectSummaryInOutList($searchData);
        $list['listData'] = SlCommonUtil::setEachData($list['listData'], $this, 'refineEachSummaryStockList');
        return $list;
    }

    /**
     * 재고 이력
     * @param $searchData
     * @return mixed
     */
    public function getInOutList($searchData){
        $list = $this->sql->selectInOutList($searchData);
        $list['listData'] = SlCommonUtil::setEachData($list['listData'], $this, 'refineEachSummaryStockList');
        return $list;
    }

    /**
     * 재고 입출고 수량 반환
     * @param $searchData
     * @return mixed
     */
    public function getInOutStockCount($searchData){
        return $this->sql->selectInOutStockCount($searchData);
    }

    public function getSummaryInOutStockCount($searchData){
        return $this->sql->selectInOutStockCount($searchData);
    }

    /**
     * @param $each
     * @param $key
     * @param $mixData
     * @return mixed
     */
    public function refineEachSummaryStockList($each, $key, &$mixData){
        $each['inOutTypeKr'] = SlCommonUtil::getFlipData(ErpCodeMap::ERP_STOCK_TYPE,$each['inOutType']);
        $each['inOutReasonKr'] = SlCommonUtil::getFlipData(ErpCodeMap::ERP_STOCK_REASON,$each['inOutReason']);
        $each['inOutTypeClass'] = '1' === $each['inOutType'] ? 'text-danger' : 'text-blue';
        return $each;
    }

    /**
     * 마지막 마감 조회
     * @return mixed
     */
    public function getLastClosingDate(){
        $searchVo = new SearchVo();
        $searchVo->setOrder('regDt desc');
        $searchVo->setLimit(1);
        return DBUtil2::getOneBySearchVo('sl_3plStockClosing', $searchVo)['regDt'];
    }

    /**
     * 총 재고 수량 반환
     * @return mixed
     */
    public function getStockCount(){
        return $this->sql->selectStockCount();
    }

    /**
     * 상품 리스트 반환
     * @return mixed
     */
    public function getProductList(){
        return $this->sql->selectProductList();
    }

    /**
     * 마지막 마감 데이터 가져오기.
     * @return array
     */
    public function getClosingInoutListSearchOption() {
        $lastClosingDate = gd_date_format('Y-m-d',  gd_isset($this->getLastClosingDate(), '2019-01-01') );
        return [
            'treatDateFl' => 'a.regDt',
            'treatDate' => [$lastClosingDate, date('Y-m-d')],
            'closingSno' => '',
            'lastClosingDate' => $lastClosingDate,
        ];
    }

    /**
     * 마감처리
     * @throws \Exception
     */
    public function setClosing(){
        $closingTargetData = $this->getClosingInoutListSearchOption();
        $closingTargetData['page'] = 1;
        $closingTargetData['pageNum'] = 100000;
        $countData = $this->getInOutStockCount($closingTargetData);
        //gd_debug($countData);

        if( empty($countData['totalStockCnt']) ){
            throw new \Exception('마감할 데이터가 없습니다!');
        }

        $totalStockCnt = DBUtil2::runSelect("select sum(stockCnt) as totalStockCnt from sl_3plProduct ")[0]['totalStockCnt'];

        //1. 마감 기록.
        $closingSno = DBUtil2::insert('sl_3plStockClosing', [
            'stockInQty' => $countData['inStockCnt'],
            'stockOutQty' => $countData['outStockCnt'],
            'totalQty' => $totalStockCnt,
            'managerSno' => \Session::get('manager.sno'),
        ]);
        //gd_debug($closingSno);

        //2. 입출고 리스트 마감일 기록
        $list = $this->getInOutList($closingTargetData);
        foreach($list['listData'] as $each){
            $updateData['closingDate'] = date('Y-m-d');
            $updateData['closingSno'] = $closingSno;
            DBUtil2::update('sl_3plStockClosing',$updateData, new SearchVo('sno=?',$each['sno']));
        }
        //3. 총 마감 재고 기록
        $encodeAllListStr = $this->getEncodeAllProductList();
        DBUtil2::update('sl_3plStockClosing',['totalMemo'=>$encodeAllListStr], new SearchVo('sno=?',$closingSno));
    }

    /**
     * 인코딩한 전체 상품 재고 리스트 반환
     * @return string
     */
    public function getEncodeAllProductList(){
        $list = $this->getProductList();
        $refineList = SlCommonUtil::setEachData($list, $this, 'prdStockToStr');
        $refineListStr = implode(',',$refineList);
        return base64_encode(gzencode($refineListStr));
    }

    /**
     * 저장된 전상품 리스트를 가져온다.
     * @param $encodeData //TODO : SNO 로 교체할 수 있음.
     * @return string
     */
    public function getDecodeAllProductList($encodeData){
        $decodeStr = gzdecode(base64_decode($encodeData));
        $pastProductStockList = explode(',',$decodeStr);
        $managerSno = \Session::get('manager.sno');
        $tableName = 'tmpAllProductStock'.$managerSno;
        $sql = "CREATE TEMPORARY TABLE {$tableName}( sno INT(10), stockCnt INT(10) )";
        DBUtil2::runSql($sql);
        $refineList = SlCommonUtil::setEachData($pastProductStockList, $this, 'strToPrdStock');
        $insertValueList = SlCommonUtil::setEachData($refineList, $this, 'saveTmpPrdStock');
        $insertValueStr = implode(',',$insertValueList);
        DBUtil2::runSql("insert into {$tableName} values {$insertValueStr}");
        $tmpList = DBUtil2::runSelect("select b.*, a.stockCnt as pastStockCnt from {$tableName} a join sl_3plProduct b on a.sno = b.sno ");
        $sql = "DROP TEMPORARY TABLE {$tableName}";
        DBUtil2::runSql($sql); //명시적 삭제.
        return $tmpList;
    }

    /**
     * 상품 정보를 문자열로
     * @param $each
     * @param $key
     * @param $mixData
     * @return string
     */
    public function prdStockToStr($each, $key, &$mixData){
        return $each['sno'].':'.$each['stockCnt'];
    }
    /**
     * 문자열을 상품 정보로
     * @param $each
     * @param $key
     * @param $mixData
     * @return false|string[]
     */
    public function strToPrdStock($each, $key, &$mixData){
        return explode(':',$each);
    }
    /**
     * 임시 테이블에 저장을 위한 변환
     * @param $each
     * @param $key
     * @param $mixData
     * @return string
     */
    public function saveTmpPrdStock($each, $key, &$mixData){
        return "({$each[0]},{$each[1]})";
    }

    /**
     * 최근 등록일자 저장
     * @return array
     */
    public function getLatestInOutDate(){
        $defaultDate = date('Y-m-d', strtotime('-3 month'));
        $result = DBUtil2::runSelect("SELECT max(inOutDate) as inDate, max(regDt) as inRegDate   FROM sl_3plStockInOut where regDt >= '{$defaultDate} 00:00:00' and  inOutType = 1")[0]; //입고
        $result = array_merge($result, DBUtil2::runSelect("SELECT max(inOutDate) as outDate, max(regDt) as outRegDate FROM sl_3plStockInOut where regDt >= '{$defaultDate} 00:00:00' and  inOutType = 2")[0]); //출고
        return $result;
    }

    /**
     * 전체 재고 다운로드
     */
    public function getTotalStockDownload(){

        $data = $this->getProductList();

        $title = [
            '품목코드',
            '품목명',
            '옵션',
            '현재재고',
            '고객사',
        ];
        $excelBody = '';
        foreach ($data as $key => $val) {
            $fieldData = array();
            $fieldData[] = ExcelCsvUtil::wrapTd($val['thirdPartyProductCode']);
            $fieldData[] = ExcelCsvUtil::wrapTd($val['productName']);
            $fieldData[] = ExcelCsvUtil::wrapTd($val['optionName']);
            $fieldData[] = ExcelCsvUtil::wrapTd(number_format($val['stockCnt']));
            $fieldData[] = ExcelCsvUtil::wrapTd($val['scmName']);
            $excelBody .=  "<tr>". implode('',$fieldData) . "</tr>";
        }
        $date = date('Y-m-d');
        $simpleExcelComponent = SlLoader::cLoad('Excel','SimpleExcelComponent','sl');
        $simpleExcelComponent->simpleDownload('이노버_전체재고_'.$date,$title,$excelBody);
    }

    /**
     * 3PL 주문 임시 저장.
     * @param $each
     * @param $key
     * @param $mixData
     */
    public function set3plOrderTemp($each, $key, &$mixData){
        $excelField = $mixData['excelField'];

        $fieldList = DBTableField::table3plOrderTmp();
        $saveData = [];
        foreach($fieldList as $field){
            $fieldName = $field['val'];
            $saveData[$fieldName] = $this->getExcelData($each,$fieldName, $excelField);
        }
        unset($saveData['sno']);

        $product = DBUtil2::getOne('sl_3plProduct', 'thirdPartyProductCode', $saveData['productCode']);
        $saveData['scmName'] = $product['scmName'];
        $saveData['scmNo'] = $product['scmNo'];

        DBUtil2::insert('sl_3plOrderTmp', $saveData);
    }


    /**
     * 영구크린 약품주문
     * @param $each
     * @param $key
     * @param $mixData
     * @return bool
     */
    public function setYounguOrderTemp($each, $key, &$mixData){
        $excelField = $mixData['excelField'];
        //$fieldList = DBTableField::table3plOrderTmp(); //zipcode.
        $saveData = [];
        $customerName = trim($this->getExcelData($each,'customerName', $excelField));

        if( empty($customerName) ) return false;

        $addressData = DBUtil2::getOneBySearchVo('sl_setScmDeliveryList', new SearchVo('scmNo=12 AND subject=?', $customerName));

        if( !empty($addressData) ) {

            $inputAddress = $this->getExcelData($each,'address', $excelField);
            if( 5 > strlen($inputAddress) ){
                $address = $addressData['receiverAddress'];
                if( !empty($addressData['receiverAddressSub']) && '-' != $addressData['receiverAddressSub'] ){
                    $address .= $addressData['receiverAddressSub'];
                }
                $saveData['zipcode'] = $addressData['receiverZonecode'];
                $saveData['address'] = $address;
                $saveData['phone'] = $addressData['receiverCellPhone'];
                $saveData['mobile'] = $addressData['receiverCellPhone'];
            }else{
                $saveData['zipcode'] = $this->getExcelData($each,'zipcode', $excelField);
                $saveData['address'] = $this->getExcelData($each,'address', $excelField);
                $saveData['phone'] = $this->getExcelData($each,'phone', $excelField);
                $saveData['mobile'] = $this->getExcelData($each,'mobile', $excelField);;
            }
            $saveData['customerName'] = $addressData['receiverName'];

            $qtyStr = $this->getExcelData($each,'qtyStr', $excelField);
            $qty = preg_replace("/[^0-9]*/s", "", $qtyStr);
            for($i=0; $qty>$i; $i++){
                $saveData['orderNo'] = 'MS2'.$i.date('mds').str_pad(($key+1),4,"0",STR_PAD_LEFT);
                $saveData['qty'] = 1;
                $saveData['scmName'] = '영구크린';
                $saveData['scmNo'] = 12;
                $saveData['productCode'] = 'MSYGCL019';
                $saveData['productName'] = '영구크린 에코메이커 1박스';

                $saveData['remark'] = $customerName;

                DBUtil2::insert('sl_3plOrderTmp', $saveData);
            }
            return true;
        }else{
            return false;
        }
    }


    public function setHkOrderTemp($each, $key, &$mixData){
        $excelField = $mixData['excelField'];
        //$fieldList = DBTableField::table3plOrderTmp(); //zipcode.
        $saveData = [];
        $customerName = $this->getExcelData($each,'customerName', $excelField);
        if( empty($customerName) ) return false;

        $addressData = DBUtil2::getOneBySearchVo('sl_setScmDeliveryList', new SearchVo('scmNo=12 AND subject=?', $customerName));
        if( !empty($addressData) ) {

            $inputAddress = $this->getExcelData($each,'address', $excelField);
            if( 5 > strlen($inputAddress) ){
                $address = $addressData['receiverAddress'];
                if( !empty($addressData['receiverAddressSub']) && '-' != $addressData['receiverAddressSub'] ){
                    $address .= $addressData['receiverAddressSub'];
                }
                $saveData['zipcode'] = $addressData['receiverZonecode'];
                $saveData['address'] = $address;
                $saveData['phone'] = $addressData['receiverCellPhone'];
                $saveData['mobile'] = $addressData['receiverCellPhone'];
            }else{
                $saveData['zipcode'] = $this->getExcelData($each,'zipcode', $excelField);
                $saveData['address'] = $this->getExcelData($each,'address', $excelField);
                $saveData['phone'] = $this->getExcelData($each,'phone', $excelField);
                $saveData['mobile'] = $this->getExcelData($each,'mobile', $excelField);;
            }
            $saveData['customerName'] = $addressData['receiverName'];

            $qtyStr = $this->getExcelData($each,'qtyStr', $excelField);
            $qty = preg_replace("/[^0-9]*/s", "", $qtyStr);
            for($i=0; $qty>$i; $i++){
                $saveData['orderNo'] = 'MS2'.$i.date('mds').str_pad(($key+1),4,"0",STR_PAD_LEFT);
                $saveData['qty'] = 1;
                $saveData['scmName'] = '영구크린';
                $saveData['scmNo'] = 12;
                $saveData['productCode'] = 'MSYGCL019';
                $saveData['productName'] = '영구크린 에코메이커 1박스';
                DBUtil2::insert('sl_3plOrderTmp', $saveData);
            }
            return true;
        }else{
            return false;
        }
    }


    /**
     * 송장등록
     * @param $each
     * @param $key
     * @param $mixData
     * @throws \Exception
     */
    public function saveInvoice($each, $key, &$mixData){

        //FIXME : 송장 등록을 여러번 하는데에 따른 재고 처리 방안 모색.
        /*$todayRegOutHistorySearchVo = new SearchVo("inOutType = 2 AND regDt >= ?", $today.' 00:00:00');
        $outHistoryList = DBUtil2::getListBySearchVo('sl_3plStockInOut', $todayRegOutHistorySearchVo);
        foreach($outHistoryList as $outHistory){
            $this->erpService->updateStock($outHistory['productSno'],$outHistory['quantity']); //복원.
        }
        DBUtil2::delete('sl_3plStockInOut', $todayRegOutHistorySearchVo);*/


        $excelField = $mixData['excelField'];

        $fieldList = DBTableField::table3plStockInOut();
        $saveData = [];
        foreach($fieldList as $field){
            $fieldName = $field['val'];
            $saveData[$fieldName] = $this->getExcelData($each,$fieldName,$excelField);
        }
        unset($saveData['sno']);
        unset($saveData['regDt']);
        unset($saveData['modDt']);

        $outDate = $this->getExcelData($each,'inOutDate',$excelField);

        $identification[] = $this->getExcelData($each,'orderNo',$excelField);
        $identification[] = $this->getExcelData($each,'thirdPartyProductCode',$excelField);
        $identification[] = $this->getExcelData($each,'quantity',$excelField);
        $identification[] = $this->getExcelData($each,'inputDate',$excelField);
        $identification[] = $this->getExcelData($each,'inputTime',$excelField);

        $product = DBUtil2::getOne('sl_3plProduct', 'thirdPartyProductCode', $saveData['thirdPartyProductCode']);
        $saveData['productSno'] = $product['sno'];

        $saveData['identificationText'] = implode('_',$identification);
        $saveData['managerSno'] = \Session::get('manager.sno');

        $saveData['inOutType'] = 2;
        $saveData['inOutReason'] = 2;//정기출고

        //고도몰주문 처리. (교환/반품)
        /*$orderData = DBUtil2::getOne(DB_ORDER, 'orderNo', $saveData['orderNo']);
        if(!empty($orderData)){

            //고도몰 등록 주문인가? (고도몰 수량 변동X, 주문 상태 변경 g1 -> d1)
            $saveData['memNo'] = $orderData['memNo'];
            $godoGoodsInfo = $this->getGodoOrderGoodsInfo($saveData);

            if(!empty($godoGoodsInfo)){
                $saveData['optionSno'] = $godoGoodsInfo['optionSno'];
                $saveData['orderDeliverySno'] = $godoGoodsInfo['orderDeliverySno'];
                if( !empty($godoGoodsInfo['handleSno']) ){
                    $saveData['inOutReason'] = 4;//교환출고
                }
                //송장 업데이트

                $godoUpdateData = [
                    'orderStatus'=>'d1',
                    'invoiceCompanySno'=>'8',
                    'invoiceNo'=>$saveData['invoiceNo'],
                    'deliveryDt'=>'now()',
                ];

                DBUtil2::update(DB_ORDER_GOODS, $godoUpdateData, new SearchVo('sno=?', $godoGoodsInfo['orderGoodsSno'] ) );

                if('g1' === $orderData['orderStatus']){
                    DBUtil2::update(DB_ORDER, ['orderStatus' => 'd1'], new SearchVo('orderNo=?', $orderData['orderNo']));
                    $orderComponent = SlLoader::cLoad('Order','Order');
                    $orderComponent->sendOrderInfo(Code::DELIVERY, 'all', $orderNo);
                    $orderComponent->sendOrderInfo(Code::INVOICE_CODE, 'sms', $orderNo);
                }
            }
        }else{
            //고도몰 판매중인 상품이 있으면 판매수량 차감, 수기 주문 처리)
            $this->setOutStockSaleGoods($saveData);
        }*/

        //OrderTemp 정리.
        //개별 출고 히스토리 기록
        $searchVo = new SearchVo(['productCode=?','orderNo=?'],[$saveData['thirdPartyProductCode'],$saveData['orderNo']]); //리스트로 나올수 있음.
        $tmpList = DBUtil2::getListBySearchVo('sl_3plOrderTmp', $searchVo);
        if( empty($tmpList) ){
            //다른조건 검색
            $searchVo = new SearchVo(['productCode=?','customerName=?','mobile=?'],[$saveData['thirdPartyProductCode'],$saveData['customerName'],$saveData['cellphone']]); //리스트로 나올수 있음.
            $tmpList = DBUtil2::getListBySearchVo('sl_3plOrderTmp', $searchVo);
        }

        foreach($tmpList as $tmpData){
            $deleteSno = $tmpData['sno'];
            unset($tmpData['sno']);
            $tmpData['orderDt'] = $outDate;
            $tmpData['invoiceNo'] = $saveData['invoiceNo'];
            DBUtil2::insert('sl_3plOrderHistory',$tmpData);
            DBUtil2::delete('sl_3plOrderTmp',new SearchVo('sno=?', $deleteSno));
        }

        //출고 재고 차감.
        $this->updateStock($saveData['productSno'],$saveData['quantity']*-1);
        DBUtil2::insert('sl_3plStockInOut', $saveData); //출고이력 등록되었는지 확인.
    }

    /**
     * 고도몰 등록 주문인가? (고도몰 수량 변동X, 주문 상태 변경 g1 -> d1)
     * @param $saveData
     * @return mixed
     */
    public function getGodoOrderGoodsInfo($saveData){
        $sql = "
            select a.optionSno
                 , a.orderDeliverySno
                 , a.handleSno
                 , a.sno as orderGoodsSno 
              from es_orderGoods a 
              join es_goodsOption b 
                on a.goodsNo = b.goodsNo 
             where b.optionCode = '{$saveData['thirdPartyProductCode']}'
               and a.orderNo = '{$saveData['orderNo']}'
               and a.orderStatus = 'g1'
        ";
        //SitelabLogger::logger($sql);

        $rsltList = DBUtil2::runSelect($sql)[0];
        return $rsltList;
    }

    /**
     * 고도몰 판매중인 상품이 있는가? ( 고도몰 판매수량 - )
     * @param $orderData
     * @param $saveData
     * @throws \Exception
     */
    public function setOutStockSaleGoods($saveData){
        //판매+비삭제 상품
        $code = $saveData['thirdPartyProductCode'];
        $sql = "
            select b.sno
                 , b.stockCnt
              from es_goods a  
              join es_goodsOption b 
                on a.goodsNo = b.goodsNo 
             where b.optionCode = '{$code}'
               and a.delFl = 'n'
               and a.goodsSellFl = 'y'
             order by b.stockCnt desc
             limit 1
        ";
        $list = DBUtil2::runSelect($sql);
        if(!empty($list)){
            $stockCnt = $list[0]['stockCnt'] - $saveData['quantity'];
            DBUtil2::update(DB_GOODS_OPTION, [ 'stockCnt' => $stockCnt ], new SearchVo('sno=?', $list[0]['sno']));
        }
    }

    /**
     * 상품정보 반환.
     * @param $params
     * @return mixed
     */
    public function getPrdInfo($params){
        return DBUtil2::getOne('sl_3plProduct', 'thirdPartyProductCode', $params['prdCode']);
    }

    /**
     * 창고에 반품 정보 등록
     * @param $params
     * @return mixed
     * @throws \Exception
     */
    public function save3plReturn($params){

        $excludeField = ['sno','regDt','modDt','returnStatus','prdStatus','partnerMemo'];
        $fieldList = DBTableField::table3plReturnList();
        $fieldArray = [];
        foreach($fieldList as $field){
            if( !in_array($field['val'], $excludeField) ){
                $fieldArray[] = $field['val'];
            }
        }
        //기본 저장 데이터
        $saveData = SlCommonUtil::getAvailData($params, $fieldArray);

        $totalQty = 0;
        foreach( $params['items'] as $key => $value){
            $totalQty += $value['prdCnt'];
            //$params['items'][$key] = $value;
        }
        //반품제품
        $saveData['prdInfo'] = json_encode($params['items']);
        $saveData['totalQty'] = $totalQty;

        //SitelabLogger::logger("저장 체크....");
        //SitelabLogger::logger($saveData);

        if( !empty($params['sno']) ){
            $sno = DBUtil2::update('sl_3plReturnList', $saveData, new SearchVo('sno=?',$params['sno']));
        }else{

            //$saveData['innoverMemo'] = addslashes($saveData['innoverMemo']);
            $sno = DBUtil2::insert('sl_3plReturnList', $saveData);
            DBUtil2::update('sl_3plReturnList', $saveData, new SearchVo('sno=?',$sno));
            $returnListData = DBUtil2::getOne('sl_3plReturnList','sno',$sno);
            //SitelabLogger::logger('Return Debug....');
            //SitelabLogger::logger($sno);


            //신규 등록 알림.
            $subject = "(엠에스이노버) 반품회수요청 건";
            $contents = "<br>{$returnListData['scmName']} {$saveData['customerName']}님 반품회수 요청이 있습니다.<br>해당 내용은 주문관리 시스템에서 확인하실 수 있습니다. <br>";
            $contents .= "<a href='http://innoverb2b.com/warehouse/return_list.php'>http://innoverb2b.com/warehouse/return_list.php</a>";
            if( SlCommonUtil::isDev() ){
                $to = 'bluesky1478@hanmail.net';
                $cc = 'jhsong@msinnover.com, nbluesky1478@gmail.com';
            }else{
                $to = implode(',',SlCodeMap::SAMYOUNG_MAIL_LIST);
                $cc = implode(',',SlCodeMap::ORDER_MAIL_LIST);
                SiteLabMailUtil::sendSimpleMail($subject, $contents, $to, $cc);
            }
        }

        return $sno;
    }

    /**
     * 송장 저장 이력
     * @param $today
     * @throws \Exception
     */
    public function saveInvoiceRegHistory($today){

        //이력 추가 등록은 가능. 차라리. 이력 자체를 삭제하게 처리. (중복 등록시)
        $searchVo = new SearchVo('regDt >= ?', $today.' 00:00:00');
        DBUtil2::delete('sl_3plInoviceRegHistory', $searchVo);
        $sql = "select b.scmName, count(1) as outCnt, sum(a.quantity) as outPrdQty from sl_3plStockInOut a left outer join sl_3plProduct b on a.productSno = b.sno where a.regDt >= '{$today} 00:00:00' and a.inOutType = 2 group by scmName order by scmName";
        $list = DBUtil2::runSelect($sql);
        $totalOutCnt = 0;
        $totalOutQty = 0;
        foreach($list as $val){
            $totalOutCnt += $val['outCnt'];
            $totalOutQty += $val['outPrdQty'];
        }
        DBUtil2::insert('sl_3plInoviceRegHistory', [
            'outDate' => $today,
            'outCnt' => $totalOutCnt,
            'outPrdQty' => $totalOutQty,
            'scmOutHistory' => json_encode($list),
        ]);
    }

    public function getWherehouseReturnData($getValue){
        $claimBoardService = SlLoader::cLoad('claim','claimBoardService');
        $erpService = SlLoader::cLoad('erp','erpService');

        //고도몰 등록
        if( !empty($getValue['sno']) ){
            //수정
            $returnData = DBUtil2::getOne('sl_3plReturnList', 'sno', $getValue['sno']);
            $returnData['innoverMemo'] = rawurlencode($returnData['innoverMemo']);
            $returnData['partnerMemo'] = rawurlencode($returnData['partnerMemo']);
        }else if( !empty($getValue['claimSno']) ){
            //클레임 연동
            $godoClaimData = DBUtil2::getOne('sl_scmClaimData','sno',$getValue['claimSno']);
            $orderInfo = DBUtil2::getOne(DB_ORDER_INFO, 'orderNo', $godoClaimData['orderNo']);
            $orderGoodsSearch = new SearchVo('orderNo=?',$godoClaimData['orderNo']);
            $orderGoodsSearch->setWhere("'' <> invoiceNo");
            $orderGoodsSearch->setOrder('regDt desc');
            $orderGoodsSearch->setLimit(1);
            $orderGoods = DBUtil2::getOneBySearchVo(DB_ORDER_GOODS,$orderGoodsSearch);
            $returnData = [
                'scmNo' => '',
                'customerName' => $orderInfo['orderName'],
                'address' => $orderInfo['receiverAddress'].' '.$orderInfo['receiverAddressSub'],
                'phone' => $orderInfo['orderCellPhone'],
                'mobile' => $orderInfo['receiverCellPhone'],
                'claimSno' => $getValue['claimSno'],
                'innoverMemo' => '',
                'invoiceNo' => $orderGoods['invoiceNo'],
                'prdInfo' => '[{"prdCode":"", "prdName":"","prdCnt":"","stockCnt":""}]',
            ];

            //gd_debug(json_decode($godoClaimData['claimGoods'],true));
            $claimDataPlus = $claimBoardService->getScmClaimData($godoClaimData);
            //gd_debug($claimDataPlus['claimGoods']);

            $refineScmNo = '';
            $prdInfoList = [];
            foreach($claimDataPlus['claimGoods'] as $claimGoods){
                //gd_debug($claimGoods);
                foreach( $claimGoods['option'] as $option ){
                    if( !empty($option['optionCnt']) ){
                        $prdCode = $option['optionCode'];
                        $prdData = $erpService->getPrdInfo(['prdCode'=>$prdCode]);
                        if( !empty($prdData['scmNo']) ){
                            $refineScmNo = $prdData['scmNo'];
                        }
                        $prdInfoList[] = [
                            'prdCode' => $prdCode,
                            'prdName' => $prdData['productName'],
                            'optionName' => $prdData['optionName'],
                            'prdCnt' => $option['optionCnt'],
                            'stockCnt' => $prdData['stockCnt'],
                        ];
                    }
                }
            }
            if(!empty($prdInfoList)){
                $returnData['scmNo'] = $refineScmNo;
                $returnData['prdInfo'] = json_encode($prdInfoList);
            }
        }else{
            $returnData = [
                'sno' => '',
                'scmNo' => '',
                'customerName' => '',
                'address' => '',
                'phone' => '',
                'mobile' => '',
                'claimSno' => '',
                'innoverMemo' => '',
                'invoiceNo' => '',
                'prdInfo' => '[{"prdCode":"", "prdName":"", "optionName":"","prdCnt":"","stockCnt":""}]',
            ];
        }

        return $returnData;
    }

    public function runTkeOrderRefine(){
        $goodsNoList = implode(',',ScmTkeService::getPreOrderGoods());
        $checkDt = SlCodeMap::PREORDER_CHECK_DT;
        $sql = "select * from  es_orderGoods where regDt >= '{$checkDt}' and orderStatus = 'p1' AND goodsNo in ( {$goodsNoList} ) ";
        /*$sql .= " AND orderNo not in (
2310051001447312,
2310051639589903,
2310060842079665,
2310061313085373,	
2310061327413094,
2310061633227973,	
2310061645083858,
2310071024591761,
2310071437179542,
2310091001304235,
2310091150370325,
2310091538024140,
2310091641036693,
2310100856366074,
2310100935316115,
2310101149560182,
2310101344282612,
2310101347391466,
2310111608429268,
2310120847340651,
2310120916554416,
2310121103592364,
2310121243351536,
2310121334395203,
2310121600180694,
2310121628135766,
	) ";*/

        $list = DBUtil2::runSelect($sql);
        $orderNoList = [];
        foreach($list as $each){
            $orderNoList[$each['orderNo']] = $each['scmNo'];
            DBUtil2::update(DB_ORDER_GOODS, ['orderStatus'=>'p3'], new SearchVo('sno=?',$each['sno'])); //es_order_goods
        }

        //모든 주문 출고대기 처리.
        foreach( $orderNoList as $orderNo => $orderValue ){
            DBUtil2::update(DB_ORDER, ['orderStatus'=>'p3'], new SearchVo('orderNo=?',$orderNo)); //es_order_goods
            if( 6 == $orderValue ){//한타면 묶어서 출고대기
                DBUtil2::update(DB_ORDER_GOODS, ['orderStatus'=>'p3'], new SearchVo('orderNo=?',$orderNo)); //es_order_goods
            }
        }

        //옵션에 대한 출고 대기 처리. (TYPE1)
        /*$targetOptionList = ScmTkeService::getPreOrderGoodsOption();
        if(!empty($targetOptionList)){
            $targetOption = implode(',',$targetOptionList);
            $sql = "select * from  es_orderGoods where orderStatus = 'p1' AND optionSno IN ( {$targetOption} )";
            $optionList = DBUtil2::runSelect($sql);
            foreach($optionList as $each){
                DBUtil2::update(DB_ORDER_GOODS, ['orderStatus'=>'p3'], new SearchVo('sno=?',$each['sno'])); //es_order_goods
            }
        }*/

        //옵션에 대한 출고 대기 처리. (TYPE2 : 전체 주문 HOLD)
        $optionOrderNoList = [];
        $targetOptionList = ScmTkeService::getPreOrderGoodsOption();
        if(!empty($targetOptionList)){
            $targetOption = implode(',',$targetOptionList);
            $sql = "select * from  es_orderGoods where orderStatus = 'p1' AND optionSno IN ( {$targetOption} )";
            $optionList = DBUtil2::runSelect($sql);
            foreach($optionList as $each){
                DBUtil2::update(DB_ORDER_GOODS, ['orderStatus'=>'p3'], new SearchVo('sno=?',$each['sno'])); //es_order_goods
                $optionOrderNoList[$each['orderNo']]=1;
            }
        }

        //주문HOLD
        /*foreach($optionOrderNoList as $optionOrderNoKey => $optionOrderNo){
            DBUtil2::update(DB_ORDER, ['orderStatus'=>'p3'], new SearchVo('orderNo=?',$optionOrderNoKey)); //es_order_goods
            DBUtil2::update(DB_ORDER_GOODS, ['orderStatus'=>'p3'], new SearchVo('orderNo=?',$optionOrderNoKey)); //es_order_goods
        }*/

        //$manualService = SlLoader::cLoad('godo','manualService','sl');
        //$manualService->setHankookOrderRefine();

        //이상한 product customer 변경.
        $sql = "update sl_imsProjectProduct a join sl_imsProject b on a.projectSno = b.sno set a.customerSno = b.customerSno WHERE a.customerSno = 0";
        DBUtil2::runSql($sql);

    }


    /**
     * 3PL 속성 넣기
     * @throws \Exception
     */
    public function set3PlAttribute(){

        $attr1List = [
            '파트너사','TKE','HK','TS'
        ];
        $attr3List = [
            '점퍼','바지','카라티','조끼','티셔츠'
        ];

        $updateData = [];

        $list = DBUtil2::getList('sl_3plProduct', '1', '1');
        foreach($list as $each){
            //gd_debug( $each['sno'] );
            foreach ($attr1List as $attr1) {
                if (strpos($each['productName'], $attr1) !== false && empty($each['attr1']) ) {
                    $updateData[$each['sno']]['attr1']=$attr1;
                    break;
                }
            }
            foreach ($attr3List as $attr3) {
                if (strpos($each['productName'], $attr3) !== false && empty($each['attr3']) ) {
                    if('티셔츠'==$attr3){
                        $attr3 = '카라티';
                    }
                    $updateData[$each['sno']]['attr3']=$attr3;
                    break;
                }
            }
        }

        foreach( $updateData as $sno => $update ){
            DBUtil2::update('sl_3plProduct',$update,new SearchVo('sno=?', $sno));
        }
    }

}

