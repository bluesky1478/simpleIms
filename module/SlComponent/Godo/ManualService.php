<?php
namespace SlComponent\Godo;

use Component\Deposit\Deposit;
use Component\Erp\ErpCodeMap;
use Component\Erp\ErpService;
use Component\Ims\ImsDBName;
use Component\Member\Util\MemberUtil;
use Controller\Admin\Order\DownloadTkeReleaseController0510;
use SiteLabUtil\SlCommonUtil;
use SlComponent\Database\DBUtil;
use SlComponent\Database\DBUtil2;
use SlComponent\Database\SearchVo;
use SlComponent\Excel\SimpleExcelComponent;
use SlComponent\Util\ExcelCsvUtil;
use SlComponent\Util\PhpExcelUtil;
use SlComponent\Util\SitelabLogger;
use SlComponent\Util\SlCode;
use SlComponent\Util\SlCodeMap;
use Component\Storage\Storage;
use SlComponent\Util\SlLoader;
use SlComponent\Util\SlSmsUtil;
use Globals;
use Framework\Utility\NumberUtils;
use Component\Sms\Code;
use Component\Sms\SmsAuto;
use Component\Sms\SmsAutoCode;
use Component\Sms\SmsAutoObserver;
/**
 * joinStatus() : 회원 가입 현황을 다운로드.
 * checkTkeBuy() : TKE 회원 특정 상품 구매 현황을 다운로드.
 * setDeposit($files) : 한국타이어 예치금 지급(정비복 포인트).
 *
 */
class ManualService {

    /**
     * 수기 원부자재자료
     * @param $files
     */
    public function setManualMaterial($files){
        $result = PhpExcelUtil::readToArray($files, 1);
        foreach($result as $index => $val){
            //원단
            $typeStr = 'fabric';
            if( '부자재' === $val[1] || 'subFabric' === $val[1]  ){
                $typeStr = 'subFabric';
            }
            $deliveryInfo = [
                'typeStr' => $typeStr,
                'styleSno' => $val[2],
                'position' => $val[3],
                'attached' => $val[4],
                'fabricName' => $val[5],
                'fabricMix' => $val[6],
                'color' => $val[7],
                'spec' => $val[8],
                'weight' => $val[9],
                'afterMake' => $val[10],
                'meas' => $val[11],
                'unit' => $val[12],
                'unitPrice' => $val[13],
                'makeNational' => $val[14],
                'makeCompany' => $val[15],
                'memo' => $val[16],
                'cate1' => $val[17],
                'cate2' => $val[18],
            ];
            DBUtil2::insert('sl_imsPrdMaterial', $deliveryInfo);
        }
    }
    
    /**
     * IMS 사이즈 스펙 입력
     */
    public function insertSizeSpec($files){

        //작업전 백업
        $currentDateTime = date('Ymdhis');
        $backupQuery = "create table zzz_imsEwork_{$currentDateTime} select * from sl_imsEwork";
        $backupRslt = DBUtil2::runSql($backupQuery);
        gd_debug($backupQuery . ' : ' . $backupRslt);

        $result = PhpExcelUtil::readToArray($files, 1);
        $idxStyleSno = 1;
        $idxRange = 8;
        $idxGender = 5;
        $idxStandard = 9;
        $idxShare = 10;
        $idxTitle = 11;
        $idxDeviation = 12;
        $idxSpec = 13;
        $idxUnit = 14;
        $idxMemo = 15;
        $saveDataList = [];
        foreach($result as $index => $val){
            $styleSno = $val[$idxStyleSno];
            $key= md5($styleSno.$val[$idxRange].$val[$idxGender]);

            if(empty($saveDataList[$styleSno])){
                $saveDataList[$styleSno] = [];
            }

            if( empty($saveDataList[$styleSno][$key]) ){
                $saveDataList[$styleSno][$key] = [
                    'styleSno' => $styleSno,
                    'specRange' => $val[$idxRange],
                    'standard' => $val[$idxStandard],
                    'gender' => $val[$idxGender],
                    'specData' => [],
                ];
            }

            $saveDataList[$styleSno][$key]['specData'][] = [
                'title' => $val[$idxTitle],
                'share' => 'X' === strtoupper($val[$idxShare])?'n':'y',
                'deviation' => $val[$idxDeviation],
                'spec' => $val[$idxSpec],
                'unit' => $val[$idxUnit],
                'memo' => $val[$idxMemo],
            ];
        }

        $refineSaveDataList = [];
        foreach($saveDataList as $saveData){
            $refineSaveDataList[] = $saveData;
        }

        foreach($saveDataList as $styleSno => $saveData){
            //gd_debug($styleSno);
            $specData = json_encode($saveData);

            $updateData = [
                'specData' => $specData,
                'styleSno' => $styleSno,
            ];
            gd_debug($updateData);

            //DBUtil2::delete(ImsDBName::EWORK, new SearchVo('styleSno=?',$styleSno));

            DBUtil2::merge(ImsDBName::EWORK, [
                'specData' => $specData,
                'styleSno' => $styleSno,
            ], new SearchVo("styleSno=?",$styleSno));
        }
    }

    public function setHanJp($orderNo, $setGoodsList){
        gd_debug($orderNo);
        $manualService = SlLoader::cLoad('godo','manualService','sl');
        foreach($setGoodsList as $each){
            gd_debug($each['orderGoodsSno'].' / '.$each['size']);
            $manualService->hanRefine($orderNo, $each['orderGoodsSno'], $each['size']);
        }
        $orderService = SlLoader::cLoad('Order','OrderService');
        $orderService->reCalcOrderData($orderNo);
    }


//TKE 수동 배송완료 처리 
/*update es_orderGoods set orderStatus = 'd2' , deliveryCompleteDt = now()
where orderNo in ( select orderNo from  zzz_tmp2 )
-- SELECT distinct a.orderNo FROM `es_order` a join es_orderGoods b on a.orderNo = b.orderNo WHERE b.scmNo = 8 and b.orderStatus = 'p3' and b.goodsNo in (1000000341,1000000340 )*/

    public function setP3Invoice(  ){
        //invoiceCompanySno = 8
        $sql = "select  a.sno,  c.invoiceNo, a.orderNo 
from es_orderGoods a 
 join es_goodsOption b on a.optionSno = b.sno 
 join sl_3plStockInOut c on a.orderNo = c.orderNo and b.optionCode = c.thirdPartyProductCode
where a.scmNo = 8 
   and a.orderStatus = 'p3'";

        $list = DBUtil2::runSelect($sql);

        $orderNoList = [];
        foreach($list as $each){
            DBUtil2::update('es_orderGoods', [
                'invoiceCompanySno'=>8,
                'invoiceNo'=>$each['invoiceNo'],
                'orderStatus' => 'd1',
                'deliveryDt' => 'd1',
            ], new SearchVo('sno=?', $each['sno']) );
            $orderNoList[$each['orderNo']]=$each['orderNo'];
        }

        foreach( $orderNoList as $orderNo ){
            DBUtil2::update('es_order',['orderStatus'=>'d1'], new SearchVo('orderNo=?', $orderNo) );
        }

    }



    public function setTaxList(){
        //setTax($orderNo, $price, $goodsNo)
    }

    public function setTax($orderNo, $price, $goodsNo){
        $sql = "UPDATE `es_orderGoods` SET 
goodsPrice = {$price} 
, taxSupplyGoodsPrice = ({$price}*goodsCnt)/1.1
, taxVatGoodsPrice = ({$price}*goodsCnt)-(({$price}*goodsCnt)/1.1)
, realTaxSupplyGoodsPrice = ({$price}*goodsCnt)/1.1
, realTaxVatGoodsPrice = ({$price}*goodsCnt)-(({$price}*goodsCnt)/1.1)
WHERE orderNo = {$orderNo} AND goodsNo = '{$goodsNo}' AND goodsPrice = 0";

        DBUtil2::runSql($sql);
    }


    public function setHan3plCode(){
        
        $sql = "SELECT a.* FROM `es_goodsOption` a join es_goods b on a.goodsNo = b.goodsNo WHERE b.scmNo = 20";
        $list = DBUtil2::runSelect($sql);

        foreach($list as $each){

            $prdMap = [
                '점퍼' => '한전 23동계 점퍼',
                '내피(조끼)' => '한전 23동계 내피 조끼',
                '상의' => '한전 23동계 작업복상의',
                '바지(기장:S)' => '한전 23동계 바지',
                '바지(기장:M)' => '한전 23동계 바지',
                '바지(기장:L)' => '한전 23동계 바지',
                '바지(기장:LL)' => '한전 23동계 바지',
                '바지(기장:LLL)' => '한전 23동계 바지',
            ];
            $optionMap = [
                '점퍼' => '?',
                '내피(조끼)' => '?',
                '상의' => '?',
                '바지(기장:S)' => '?_S',
                '바지(기장:M)' => '?_M',
                '바지(기장:L)' => '?_L',
                '바지(기장:LL)' => '?_LL',
                '바지(기장:LLL)' => '?_LLL',
            ];

            $productName = $prdMap[$each['optionValue1']];
            $optionName = str_replace('?',$each['optionValue2'], $optionMap[$each['optionValue1']]);

            DBUtil2::insert('sl_3plProduct',[
                'scmNo' => 20,
                'scmName' => '한전산업개발',
                'thirdPartyCode' => '1',
                'thirdPartyProductCode' => $each['optionCode'],
                'productName' => $productName,
                'optionName' => $optionName,
                'stockCnt' => 0,
                'attr2' => '동계',
            ]);
        }

    }


    public function hanRefine($orderNo, $orderGoodsSno, $name){
        $copyOrderGoods = DBUtil2::getOneBySearchVo('es_orderGoods', new SearchVo('goodsNo=1000000337 AND sno=?', $orderGoodsSno));
        unset($copyOrderGoods['sno']);
        unset($copyOrderGoods['regDt']);
        unset($copyOrderGoods['modDt']);
        //gd_debug($copyOrderGoods);
        $sno = DBUtil2::insert('es_orderGoods', $copyOrderGoods);
        gd_debug($name . ' : ' . $sno);
        $map = [
            '85' => 4064,
            '90' => 4065,
            '95' => 4066,
            '100' => 4067,
            '105' => 4068,
            '110' => 4069,
            '115' => 4070,
            '120' => 4071,
            '125' => 4072,
            '130' => 4073,
        ];
        $optionSno = $map[$name];
        $copyOrderGoods['optionSno'] = $optionSno;
        $sql = "UPDATE es_orderGoods SET optionSno = {$optionSno}  , optionInfo = '[[\\\\\"상품구분\\\\\",\\\\\"내피(조끼)\\\\\",null,0,null],[\\\\\"사이즈\\\\\",\\\\\"{$name}\\\\\",\\\\\"\\\\\",0,null]]' WHERE sno = {$sno}";
        //gd_debug($sql);
        DBUtil2::runSql($sql);

        //재고빼기
        DBUtil2::runSql("update es_goodsOption set stockCnt = stockCnt -{$copyOrderGoods['goodsCnt']} where sno = {$optionSno}");
        //$optionSno

        $orderService = SlLoader::cLoad('Order','OrderService');
        $orderService->reCalcOrderData($orderNo);
    }


    public function refineHkStockOpenGoodsOption2($openGoodsNo, $targetGoodsNo, $maxOptionNo = 7){
        //Copy .
        $list = DBUtil2::getList(DB_GOODS_OPTION, 'goodsNo', $openGoodsNo);
        foreach($list as $each){
            if( $each['optionNo'] > $maxOptionNo ){
                unset($each['sno']);
                $each['goodsNo'] = $targetGoodsNo;
                $each['stockCnt'] = 0;
                $insertResult = DBUtil2::insert(DB_GOODS_OPTION, $each);
                $msg = "옵션 삽입 이력 : {$each['optionCode']} -> {$insertResult}";
                gd_debug($msg);
                SitelabLogger::logger($msg);
            }
        }

        //Copy 후 재고 처리.
        $this->refineHkStockOpenGoodsOption($openGoodsNo, $targetGoodsNo, $maxOptionNo);
    }


    /**
     * 오픈 패키지, 원상품 50:50 수량 재 편성 작업 23-03-16
     * @param $openGoodsNo
     * @param $targetGoodsNo
     */
    public function refineHkStockOpenGoodsOption($openGoodsNo, $targetGoodsNo, $ignoreMaxOptionNo = 0){

        //확인 쿼리
        /*select * from es_goodsOption where goodsNo in (
            1000000236
            , 1000000235
        ) order by optionCode, goodsNo;*/

        //재고 0 처리
        if( 0 === $ignoreMaxOptionNo){
            $stock0 = DBUtil2::update(DB_GOODS_OPTION, [ 'stockCnt' => 0 ], new SearchVo('goodsNo=?',$targetGoodsNo));
            gd_debug("Stock 0 처리 : {$stock0}");
            SitelabLogger::logger("Stock 0 처리 : {$stock0}");
        }

        $openGoodsList = DBUtil2::getList(DB_GOODS_OPTION, 'goodsNo', $openGoodsNo);
        $totalOpenStockCnt = 0;
        $totalStockCnt = 0;

        foreach( $openGoodsList as $openGoods ){

            $code = $openGoods['optionCode'];
            $targetGoodsOption = DBUtil2::getOneBySearchVo(DB_GOODS_OPTION, new SearchVo(['goodsNo=?','optionCode=?'],[$targetGoodsNo, $code]));

            $stockCnt = $openGoods['stockCnt'];

            //업데이트 하기 전. 무시할 옵션번호    7 >= 8 false ,  7 >= 7 true, 7 >= 6 true , 0 = 1 false
            if( $ignoreMaxOptionNo >= $openGoods['optionNo'] ) {
                $totalOpenStockCnt += $stockCnt;
                $totalStockCnt += $targetGoodsOption['stockCnt'];
                continue;
            }

            $eventStockCnt = floor($stockCnt/2); //버림.
            $eventModeStockCnt = $stockCnt%2;    //나머지.
            $eventCeilStockCnt = $eventStockCnt+$eventModeStockCnt;    //나머지.

            $totalOpenStockCnt += $eventStockCnt;
            $totalStockCnt += $eventCeilStockCnt;


            //오픈 패키지 업데이트
            $rslt1 = DBUtil2::update(DB_GOODS_OPTION, [ 'stockCnt' => $eventStockCnt ], new SearchVo('sno=?',$openGoods['sno']));
            //원 상품 업데이트
            $rslt2 = DBUtil2::update(DB_GOODS_OPTION, [ 'stockCnt' => $eventCeilStockCnt ], new SearchVo('sno=?',$targetGoodsOption['sno']));

            $updateLogMsg = "{$rslt1},{$rslt2} // {$code} =>  open:{$openGoods['sno']}[{$eventStockCnt}] / target:{$targetGoodsOption['sno']}[{$eventCeilStockCnt}]  ===> stockCnt:{$stockCnt} / eventStockCnt:{$eventStockCnt} / eventModeStockCnt:{$eventModeStockCnt} /  eventCeilStockCnt:{$eventCeilStockCnt}";
            gd_debug($updateLogMsg);
            SitelabLogger::logger($updateLogMsg);
        }

        //노출함 처리.
        $viewOk = DBUtil2::update(DB_GOODS, ['goodsDisplayFl'=>'y', 'goodsDisplayMobileFl'=>'y','goodsSellFl'=>'y','goodsSellMobileFl'=>'y', 'totalStock'=>$totalStockCnt], new SearchVo('goodsNo=?',$targetGoodsNo));
        gd_debug("노출 처리 : {$viewOk}");
        SitelabLogger::logger("노출 처리 : {$viewOk}");

        $openTotalStock = DBUtil2::update(DB_GOODS, ['totalStock'=>$totalOpenStockCnt], new SearchVo('goodsNo=?',$openGoodsNo));
        gd_debug("수량정제 : {$openTotalStock}");
        SitelabLogger::logger("수량정제 : {$openTotalStock}");
    }


    public function inputPrdCode($files){
        $params['instance'] = $this;
        $params['fnc'] = 'inputPrdCodeEach';
        $sheetData = ExcelCsvUtil::runExcelReadAndProcess($files, $params);

    }
    public function inputPrdCodeEach($each, $key, &$mixData){
        if( 1 == $key ) return false;
    }

    public function inputAndHkResearch($files){
        $params['instance'] = $this;
        $params['fnc'] = 'sendHkResearch';
        $params['mixData']['success'] = 0;
        $params['mixData']['fail'] = 0;

        $result = ExcelCsvUtil::runExcelReadAndProcess($files, $params);

        gd_debug($result);

    }
    public function sendHkResearch($each, $key, &$mixData){
        if( 1 == $key ) return false;

        $member['memNm'] = $each[1];
        $member['cellPhone'] = $each[2];

        if( !empty($member['cellPhone']) ){
            $receiverData[0]['memNo'] = '0';
            $receiverData[0]['smsFl'] = 'y';
            $receiverData[0]['cellPhone'] = $member['cellPhone'];
            $receiverData[0]['memNm'] = $member['memNm'];

            $orderName = $member['memNm'];
            $content = "
안녕하세요. 한국타이어 [{$orderName}]님

이노버에서 구매해주셔서 감사합니다.

구매 만족도 평가에 참여 부탁드립니다.
고객님의 소중한 의견을 취합해 더욱 더 만족스러운 서비스를
드릴 수 있도록 노력하겠습니다.
감사합니다 :)

참여하기
[https://forms.gle/GP1559J8vVcHTUxA9]

▷ [한국타이어B2B] 바로가기
[hankookb2b.co.kr]
고객센터
(070-4239-4380)";
            //gd_debug($member['memNm']. '-' .$member['cellPhone']);
            //gd_debug($content);
            $result = SlSmsUtil::sendSms($content, $receiverData, 'lms');
            $mixData['success'] += $result['success'];
            $mixData['fail'] += $result['fail'];
        }
        //gd_debug( $mixData['result'] );
    }

    /**
     * TKE 회원 특정 상품 구매 현황을 다운로드.
     */
    public function checkTkeBuy(){

        $goodsList = [
            1000000301,
            1000000302,
            1000000303,
            1000000304,
        ];

        $memberList = DBUtil2::runSelect(
            "
SELECT a.memNo,
       a.memId,
       a.nickNm,
       a.memNm,       
       if(1=b.memberType,'정규직','파트너사') as memberType, 
       'X' as goods1,
       'X' as goods2,
       'X' as goods3,
       'X' as goods4
  FROM es_member a
  JOIN sl_setMemberConfig b
    ON a.memNo = b.memNo
 WHERE a.ex1 = 'TKE(티센크루프)'
 ORDER BY b.memberType, a.memNm 
"
        );

        foreach($memberList as $memberKey => $member){
            foreach($goodsList as $goodsKey => $goodsNo){
                $sql = "SELECT COUNT(1) cnt FROM es_orderGoods a JOIN es_order b ON a.orderNo = b.orderNo WHERE a.goodsNo = '{$goodsNo}' AND b.memNo = {$member['memNo']}";
                $orderGoods = DBUtil2::runSelect($sql);
                if( !empty($orderGoods[0]['cnt']) ){
                    $member['goods'.($goodsKey+1)] = 'O';
                }
            }
            $memberList[$memberKey] = $member;
        }


        $excelBody = '';

        foreach( $memberList as $val ){
            $fieldData = [];
            $fieldData[] = ExcelCsvUtil::wrapTd($val['memNo']);
            $fieldData[] = ExcelCsvUtil::wrapTd($val['memId'],'text','mso-number-format:\'\@\'');
            $fieldData[] = ExcelCsvUtil::wrapTd($val['nickNm'],'text','mso-number-format:\'\@\'');
            $fieldData[] = ExcelCsvUtil::wrapTd($val['memNm'],'text','mso-number-format:\'\@\'');
            $fieldData[] = ExcelCsvUtil::wrapTd($val['memberType']);
            $fieldData[] = ExcelCsvUtil::wrapTd($val['goods1']);
            $fieldData[] = ExcelCsvUtil::wrapTd($val['goods2']);
            $fieldData[] = ExcelCsvUtil::wrapTd($val['goods3']);
            $fieldData[] = ExcelCsvUtil::wrapTd($val['goods4']);
            $excelBody .=  "<tr>". implode('',$fieldData) . "</tr>";
        }
        $simpleExcelComponent = SlLoader::cLoad('Excel','SimpleExcelComponent','sl');
        $simpleExcelComponent->simpleDownload('TKE상품구매여부_221124' , ['회원번호', '아이디', '닉네임', '회원명', '정규/파트너구분','TKE 동계점퍼(TKEK)','TKE 동계점퍼(파트너사)','TKE 동계바지(TKEK)','TKE 동계바지(파트너사)'],$excelBody);

    }

    /**
     * joinStatus($files) : 엑셀에 등록된 폰번호와 이메일로 회원 가입 현황을 다운로드.
     */
    public function joinStatus($files){
        if( !empty($files) ){
            $startRowCnt = 1;
            $result = ExcelCsvUtil::checkAndRead($files,$startRowCnt);

            if( empty($result['isOk'])  ){
                throw new \Exception($result['failMsg']);
            }

            $sheetData = $result['data']->sheets[0];
            $sheetData = $sheetData['cells'];
            $fieldDataList = array();

            $fieldDataList[1] = [ '폰번호' , 'cellPhone' ] ;
            $fieldDataList[2] = [ '이메일' , 'email' ] ;

            $result = [];
            foreach( $sheetData as $idx => $data ){
                if( ($startRowCnt+1) > $idx ) continue;

                $memberDefaultData = [];
                foreach( $fieldDataList as $key => $value ){
                    if(isset( $data[$key] )){
                        $memberDefaultData[$value[1]] = $data[$key];
                    }
                }

                $memberInfo = DBUtil2::getOne(DB_MEMBER, "replace(cellPhone,'-','')" , str_replace('-','',$memberDefaultData['cellPhone']));
                if(!empty($memberInfo)){
                    $memberDefaultData['memId'] = $memberInfo['memId'];
                    $memberDefaultData['ex1'] = $memberInfo['ex1'];
                }
                $memberInfo2 = DBUtil2::getOne(DB_MEMBER, "LOWER(email)" , strtolower($memberDefaultData['email']));
                if(!empty($memberInfo2) && empty($memberDefaultData['memId']) ){
                    $memberDefaultData['memId'] = $memberInfo2['memId'];
                    $memberDefaultData['ex1'] = $memberInfo2['ex1'];
                }
                $result[] = $memberDefaultData;
            }

            $excelBody = '';

            foreach( $result as $val ){
                $fieldData = [];
                $fieldData[] = ExcelCsvUtil::wrapTd($val['cellPhone']);
                $fieldData[] = ExcelCsvUtil::wrapTd($val['email']);
                $fieldData[] = ExcelCsvUtil::wrapTd($val['memId'],'text','mso-number-format:\'\@\'');
                $fieldData[] = ExcelCsvUtil::wrapTd($val['ex1']);
                $excelBody .=  "<tr>". implode('',$fieldData) . "</tr>";
            }
            $simpleExcelComponent = SlLoader::cLoad('Excel','SimpleExcelComponent','sl');
            $simpleExcelComponent->simpleDownload('회원가입현황' , ['검색전화번호', '검색이메일', '가입회원아이디', '업체'],$excelBody);
        }
    }

    /**
     * 한국타이어 예치금 지급(정비복 포인트).
     */
    public function setDeposit($files){
        $depositService = SlLoader::cLoad('deposit','deposit');

        $startRowCnt = 1;
        $result = ExcelCsvUtil::checkAndRead($files,$startRowCnt);

        if( empty($result['isOk'])  ){
            throw new \Exception($result['failMsg']);
        }

        $sheetData = $result['data']->sheets[0];
        $sheetData = $sheetData['cells'];
        $fieldDataList = array();

        $fieldDataList[1] = [ '아이디' , 'memId' ] ;
        $fieldDataList[2] = [ '포인트' , 'deposit' ] ;

        $result = [];
        foreach( $sheetData as $idx => $data ){
            if( ($startRowCnt+1) > $idx ) continue;

            $memberDefaultData = [];
            foreach( $fieldDataList as $key => $value ){
                if(isset( $data[$key] )){
                    $memberDefaultData[$value[1]] = $data[$key];
                }
            }
            $memberInfo = DBUtil2::getOne(DB_MEMBER, 'memId',$memberDefaultData['memId']);
            if(!empty($memberInfo)){
                $memberDefaultData ['before'] = $memberInfo['deposit'];

                //TODO : 지급하려면 주석 해제
                $rslt = $depositService->setMemberDeposit($memberInfo['memNo'], $memberDefaultData['deposit'], Deposit::REASON_CODE_GROUP . Deposit::REASON_CODE_ETC, 'o', null,  null, '매장평가 우수점 구매비용 지급');

                if( !empty($rslt) ){
                    $memberInfo2 = DBUtil2::getOne(DB_MEMBER, 'memId',$memberDefaultData['memId']);
                    $memberDefaultData['after'] = $memberInfo2['deposit'];
                    $memberDefaultData['msg'] = '지급성공';
                }else{
                    $memberDefaultData['msg'] = '지급실패1';
                }
            }else{
                $memberDefaultData['msg'] = '지급실패2';
            }

            $result[] = $memberDefaultData;
        }

        $excelBody = '';

        foreach( $result as $val ){
            $fieldData = [];
            $fieldData[] = ExcelCsvUtil::wrapTd($val['memId'],'text','mso-number-format:\'\@\'');
            $fieldData[] = ExcelCsvUtil::wrapTd(number_format($val['deposit']));
            $fieldData[] = ExcelCsvUtil::wrapTd(number_format($val['before']));
            $fieldData[] = ExcelCsvUtil::wrapTd(number_format($val['after']));
            $fieldData[] = ExcelCsvUtil::wrapTd($val['msg']);
            $excelBody .=  "<tr>". implode('',$fieldData) . "</tr>";
        }
        $simpleExcelComponent = SlLoader::cLoad('Excel','SimpleExcelComponent','sl');
        $simpleExcelComponent->simpleDownload('포인트 지급 현황' , ['회원ID', '지급포인트', '이전예치금', '이후예치금', '처리메세지'],$excelBody);
    }

    public function getTkeMember(){
        $scmNo = 4;
        $sql = "select distinct 
                c.receiverName as orderName
              , c.receiverCellPhone as orderCellPhone
              , a.memNm
              , a.nickNm 
                from es_order b 
                join es_orderInfo c on b.orderNo = c.orderNo 
                join es_orderGoods d on b.orderNo = d.orderNo 
                join es_goods e on d.goodsNo = e.goodsNo
                left outer join es_member a on b.memNo = a.memNo
                where b.regDt >= '2022-10-01 00:00:00' and left(b.orderStatus,1) in ('d','s') and d.scmNo = {$scmNo}";

        $result = DBUtil2::runSelect($sql);

        foreach( $result as $val ){
            $fieldData = [];
            $fieldData[] = ExcelCsvUtil::wrapTd($val['orderName']);
            $fieldData[] = ExcelCsvUtil::wrapTd($val['orderCellPhone'],'text','mso-number-format:\'\@\'');
            $fieldData[] = ExcelCsvUtil::wrapTd($val['memNm']);
            $fieldData[] = ExcelCsvUtil::wrapTd($val['nickNm']);
            $excelBody .=  "<tr>". implode('',$fieldData) . "</tr>";
        }
        $simpleExcelComponent = SlLoader::cLoad('Excel','SimpleExcelComponent','sl');
        $simpleExcelComponent->simpleDownload('제일건설_2022-10-현재까지_주문자' , ['주문자명', '주문자연락처','1','2'],$excelBody);
    }


    public function insertResearchTarget($targetList){
        DBUtil2::runSql("truncate table zzz_tmpResearch");
        foreach( $targetList as $each ){
            $insertSql = "insert into zzz_tmpResearch(name,phone) values('{$each[1]}','{$each[2]}')";
            gd_debug($insertSql);
            DBUtil2::runSql("insert into zzz_tmpResearch(name,phone) values('{$each[1]}','{$each[2]}')");
        }
    }

    /**
     * 입출고 이력 초기화
     * @param $inOutType
     * @param $regDt
     */
    public function rollbackInOutStock($inOutType, $regDt){
        //ex : $manualService = SlLoader::cLoad('godo','manualService','sl');
        //ex : $manualService->rollbackInOutStock(1,'2023-03-23 00:00:00');
        $searchVo = new SearchVo(
            ['inOutType=?','regDt>=?']
            ,[$inOutType,$regDt]
        );
        $list = DBUtil2::getListBySearchVo('sl_3plStockInOut',  $searchVo);
        foreach( $list as $each ){
            $result = DBUtil2::runSql("UPDATE sl_3plProduct SET stockCnt = stockCnt - {$each['quantity']} WHERE sno='{$each['productSno']}'");
            DBUtil2::delete('sl_3plStockInOut', new SearchVo('sno=?',$each['sno']));
            gd_debug("{$each['productSno']}({$each['quantity']}개) : {$result}");
        }
        //gd_debug($list);
    }


    public function manualOrderYounggu($fileData){
        $prdColMap = [
            [2,  'MSYGCL007', '영구크린_하계카라티_90'],
            [3,  'MSYGCL008', '영구크린_하계카라티_95'],
            [4,  'MSYGCL009', '영구크린_하계카라티_100'],
            [5,  'MSYGCL010', '영구크린_하계카라티_105'],
            [6,  'MSYGCL011', '영구크린_하계카라티_110'],
            [7,  'MSYGCL012', '영구크린_하계카라티_115'],
            [9,  'MSYGCL013', '영구크린_하계티(차이나)_90'],
            [10, 'MSYGCL014', '영구크린_하계티(차이나)_95'],
            [11, 'MSYGCL015', '영구크린_하계티(차이나)_100'],
            [12, 'MSYGCL016', '영구크린_하계티(차이나)_105'],
            [13, 'MSYGCL017', '영구크린_하계티(차이나)_110'],
            [14, 'MSYGCL018', '영구크린_하계티(차이나)_115'],
            [16, 'MSYGCL001', '영구크린_하계조끼_90'],
            [17, 'MSYGCL002', '영구크린_하계조끼_95'],
            [18, 'MSYGCL003', '영구크린_하계조끼_100'],
            [19, 'MSYGCL004', '영구크린_하계조끼_105'],
            [20, 'MSYGCL005', '영구크린_하계조끼_110'],
            [21, 'MSYGCL006', '영구크린_하계조끼_115'],
        ];

        //$prdColMap;
        $scmName = '영구크린';
        $scmNo = '12';
        $totalCnt = 0; //총구매수량.
        $insertCnt = 0; //총구매수량.
        foreach($fileData as $index => $value){
            if( 1 >= $index) continue;

            $manOrderNo = 'MS02'.date('mds').str_pad($index,4,"0",STR_PAD_LEFT);;

            foreach($prdColMap as $prdColData){
                $buyDataValue = $value[$prdColData[0]];
                if( !empty($buyDataValue) && is_numeric($buyDataValue) && !empty($value[24]) ){
                    $saveData = [
                        'orderNo' => $manOrderNo,
                        'customerName' => $value[1],
                        'address' => $value[24],
                        'phone' => $value[25],
                        'mobile' => $value[25],
                        'productCode' => $prdColData[1],
                        'productName' => $prdColData[2],
                        'qty' => $buyDataValue,
                        'scmName' => $scmName,
                        'scmNo' => $scmNo,
                    ];
                    DBUtil2::insert('sl_3plOrderTmp', $saveData);
                    $insertCnt++;
                    $totalCnt+=$buyDataValue;
                }
            }
        }
        gd_debug($insertCnt);
        gd_debug($totalCnt);
    }


    public function getHkStock2(){
        $list = DBUtil2::runSelect( "select * from sl_3plProduct where scmNo = 6 order by productName" );

        $htmlList = [];
        $htmlList[] = '<table class="table table-rows" border="1">';


        foreach( $list as $each ){
            $htmlList[] = '<tr>';
            $htmlList[] = ExcelCsvUtil::wrapTd($each['attr1']);
            $htmlList[] = ExcelCsvUtil::wrapTd($each['attr2']);
            $htmlList[] = ExcelCsvUtil::wrapTd($each['attr3']);
            $htmlList[] = ExcelCsvUtil::wrapTd($each['attr4']);
            $htmlList[] = ExcelCsvUtil::wrapTd($each['optionName']);
            $htmlList[] = ExcelCsvUtil::wrapTd($each['stockCnt']);
            $htmlList[] = '</tr>';
        }

        $htmlList[] = '</table>';
        $excelBody =  implode('',$htmlList);
        $simpleExcelComponent = SlLoader::cLoad('Excel','SimpleExcelComponent','sl');
        $simpleExcelComponent->downloadCommon($excelBody, '한국타이어재고표');

    }

    public function getHkStock(){

        $refineList = [];
        $list = DBUtil2::runSelect( "select * from sl_3plProduct where scmNo = 6 order by productName" );
        foreach( $list as $each ){

        }
        //attr

        $OPTION_LIST1 = [
            90,95,100,105,110,115,120,
        ];
        $OPTION_LIST2 = [
            28,30,32,34,36,38,40
        ];

        $fileTitle = "한국타이어재고표_".date('Y-m-d');

        $optionList = DownloadTkeReleaseController0510::OPTION_LIST;

        $htmlList = [];
        $htmlList[] = '<table class="table table-rows" border="1">';

        $htmlList[] = '<tr>';
        $htmlList[] = ExcelCsvUtil::wrapTh($fileTitle,'title','background-color:#ffffff; font-weight:bold; font-size:20px','colspan=9');
        $htmlList[] = '</tr>';

        $htmlList[] = '<tr>';
        $htmlList[] = ExcelCsvUtil::wrapTh('구분','title','background-color:#f0f0f0; font-weight:bold');
        foreach( $OPTION_LIST1 as $option ){
            $htmlList[] = ExcelCsvUtil::wrapTh($option,'title','background-color:#d9e1f2; font-weight:bold');
        }
        $htmlList[] = '</tr>';

        SlCommonUtil::setEachData($list, $this, 'setHkExcelEach', $htmlList);

        //SlCommonUtil::setEachData($refineList, $this, 'setExcelEach', $htmlList);
        //$totalData = [];
        //SlCommonUtil::setEachData($refineList, $this, 'setExcelTotalEach', $totalData);
        //gd_debug($totalData);

        /*$htmlList[] = ExcelCsvUtil::wrapTh('TOTAL','title','background-color:#f0f0f0; font-weight:bold');
        foreach( $optionList as $option ){
            if($option > 80){
                $htmlList[] = ExcelCsvUtil::wrapTh($totalData[$option],'title','background-color:#d9e1f2; font-weight:bold');
            }else{
                $htmlList[] = ExcelCsvUtil::wrapTh($totalData[$option],'title','background-color:#fff2cc; font-weight:bold');
            }
        }*/
        //$htmlList[] = ExcelCsvUtil::wrapTh('티:'.number_format($totalData['tee']).'개','title','background-color:#f0f0f0; font-weight:bold');
        //$htmlList[] = ExcelCsvUtil::wrapTh('바지:'.number_format($totalData['pants']).'개','title','background-color:#f0f0f0; font-weight:bold');
        //$htmlList[] = ExcelCsvUtil::wrapTh('총 수량:'.number_format($totalData['total']).'개','title','background-color:#f0f0f0; font-weight:bold');

        $htmlList[] = '</table>';

        $excelBody =  implode('',$htmlList);

        $simpleExcelComponent = SlLoader::cLoad('Excel','SimpleExcelComponent','sl');
        $simpleExcelComponent->downloadCommon($excelBody, $fileTitle);

    }
    public function setHkExcelEach($val, $key, &$htmlList){
        $htmlList[] = '<tr>';
        //$optionList = DownloadTkeReleaseController::OPTION_LIST;

        foreach( $optionList as $option ){
            $htmlList[] = ExcelCsvUtil::wrapTd(number_format($val['option'][$option]), '', 'text-align:center');
        }

        $htmlList[] = '</tr>';
    }

    public function setFindAttributeHk(){
        //실재고다.
        $goodsList = DBUtil2::runSelect( "select * from sl_3plProduct where attr1='' and scmNo = 6 order by productName" );
        /*foreach($goodsList as $each){
            gd_debug( $each['productName'] );
        }*/

        $contentsList = [
            'attr1' => [
                'HK' => 'HK',
                'TTS' => 'TTS',
                'TBX' => 'TBX',
                'TS' => 'TS',
            ],
            'attr2' => [
                '춘추' => '춘추',
                '하계' => '하계',
                '동계' => '동계',
                '반팔' => '하계',
            ],
            'attr3' => [
                '조끼' => '조끼',
                '점퍼' => '점퍼',
                '바지' => '바지',
                '카라티' => '카라티',
            ],
            /*'attr4' => [
                '차콜' => '차콜',
                '블랙' => '블랙',
            ]*/
        ];

        foreach($goodsList as $goods){
            foreach($contentsList as $compareKey => $contents){
                foreach($contents as $compareData => $value){
                    if (strpos($goods['productName'] , $compareData) !== false) {
                        $rslt = DBUtil2::update('sl_3plProduct',[$compareKey=>$value], new SearchVo('sno=?', $goods['sno']));
                        gd_debug($goods['sno'] . ' : ' . $compareKey .' => ' . $value . ' ==> ' . $rslt);
                        break;
                    }
                }
            }
        }

    }


    //생산년도(입고년도) 등록.
    public function setAttributeYear($each, $key, &$mixData){
        $paramData = PhpExcelUtil::getExcelDataList($each, $mixData['excelField']);
        DBUtil2::update('sl_3plProduct',['attr5'=>'2023'],new SearchVo('(attr5 = \'\' or attr5 is null) and thirdPartyProductCode=?', $paramData['prdCode']));
    }
    //시즌 등록
    public function setAttributeSeason(){
        $compareList = [
            '춘추' => '춘추',
            '하계' => '하계',
            '동계' => '동계',
            '반팔' => '하계',
            '추계' => '춘추',
            '방한' => '동계',
        ];

        /*'goodsType' => [
            '카라티'=>'상의',
            '바지'=>'하의',
            '점퍼'=>'상의',
        ],*/

        $list = DBUtil2::getList('sl_3plProduct',"( attr2 = '' or attr2 is null )",'1');
        $rslt = 0;
        foreach($list as $goods){
            foreach($compareList as $compareData => $value){
                if (strpos($goods['productName'] , $compareData) !== false) {
                    $rslt += DBUtil2::update('sl_3plProduct',['attr2'=>$value], new SearchVo('sno=?', $goods['sno']));
                    break;
                }
            }
        }
        gd_debug('updateCount : ' . $rslt);
    }
    //타입 등록
    public function setAttributeTypeTke(){

        $compareList = [
            '카라티'=>'상의',
            '바지'=>'하의',
            '점퍼'=>'상의',
            '티셔츠'=>'상의',
        ];

        $list = DBUtil2::getList('sl_3plProduct',"( attr3 = '' or attr3 is null ) and scmNo",'8'); //TKE 전용

        $rslt = 0;
        foreach($list as $goods){
            foreach($compareList as $compareData => $value){
                if (strpos($goods['productName'] , $compareData) !== false) {
                    $rslt += DBUtil2::update('sl_3plProduct',['attr3'=>$value], new SearchVo('sno=?', $goods['sno']));
                    break;
                }
            }
        }
        gd_debug('updateCount : ' . $rslt);
    }
    //파트너구분 등록
    public function setAttributeTypeAttr1(){
        $list = DBUtil2::getList('sl_3plProduct',"( attr1 = '' or attr1 is null ) and scmNo",'8'); //TKE 전용
        $rslt1 = $rslt2 = 0;
        foreach($list as $goods){
            if (strpos($goods['productName'] , '파트너') !== false) {
                $rslt1 += DBUtil2::update('sl_3plProduct',['attr1'=>'파트너사'], new SearchVo('sno=?', $goods['sno']));
            }else{
                $rslt2 += DBUtil2::update('sl_3plProduct',['attr1'=>'TKE'], new SearchVo('sno=?', $goods['sno']));
            }
        }
        gd_debug('updateCount(정규) : ' . $rslt1);
        gd_debug('updateCount(파트너) : ' . $rslt2);
    }

    public function refineOrderGoodsDc($orderNo, $dcPercent, $orderDt = null){

        gd_debug($orderNo);

        $tmp['trunc'] = Globals::get('gTrunc.goods');
        $orderGoodsList = DBUtil2::getList(DB_ORDER_GOODS, 'orderNo', $orderNo);
        $totalGoodsPrice = 0;
        $totalDcPrice = 0;
        $purePrice = 0;
        foreach( $orderGoodsList as $orderGoods ){

            //동계점퍼만 할인 ( 특정상품만 할인하고 싶을 때 여기 수정 )
            /*if( '1000000453' !== $orderGoods['goodsNo']  ) {
                $dcPercent = 0;
            }*/

            $tmp['discountByPrice'] = $orderGoods['goodsPrice'];
            // 상품할인금액
            $discountPercent = $dcPercent / 100;

            $purePrice += ($orderGoods['goodsPrice'] * $orderGoods['goodsCnt']);

            $goodsDcPrice = gd_number_figure($tmp['discountByPrice'] * $discountPercent, $tmp['trunc']['unitPrecision'], $tmp['trunc']['unitRound']);
            $goodsDcPrice = $goodsDcPrice * $orderGoods['goodsCnt'];
            $orderGoodsPrice = ($orderGoods['goodsCnt'] * $orderGoods['goodsPrice']);
            $orderGoodsPriceWithDc = $orderGoodsPrice - $goodsDcPrice;
            $refineGoodsTaxPrice = NumberUtils::taxAll($orderGoodsPriceWithDc, 10, 't');
            $totalGoodsPrice += $orderGoodsPriceWithDc;
            $totalDcPrice += $goodsDcPrice;
            $updateData = [
                'taxSupplyGoodsPrice' => $refineGoodsTaxPrice['supply'],
                'taxVatGoodsPrice' => $refineGoodsTaxPrice['tax'],
                'realTaxSupplyGoodsPrice' => $refineGoodsTaxPrice['supply'],
                'realTaxVatGoodsPrice' => $refineGoodsTaxPrice['tax'],
                'goodsDcPrice' => $goodsDcPrice,
            ];
            $rslt = DBUtil2::update('es_orderGoods', $updateData, new SearchVo('sno=?', $orderGoods['sno']));
            gd_debug('상품 할인 업데이트 : '.$rslt);
        }
        //TotalGoodsPrice
        //배송비용
        $orderInfo = DBUtil2::getOne(DB_ORDER, 'orderNo', $orderNo);
        $totalGoodsPrice += $orderInfo['totalDeliveryCharge'];

        $refineTaxPrice = NumberUtils::taxAll($totalGoodsPrice, 10, 't');
        $rslt = DBUtil2::update(DB_ORDER, [
            'settlePrice' => $totalGoodsPrice,
            'totalGoodsPrice' => $purePrice,
            'totalGoodsDcPrice' => $totalDcPrice,
            'taxSupplyPrice' => $refineTaxPrice['supply'],
            'taxVatPrice' => $refineTaxPrice['tax'],
            'realTaxSupplyPrice' => $refineTaxPrice['supply'],
            'realTaxVatPrice' => $refineTaxPrice['tax'],
        ],new SearchVo('orderNo=?', $orderNo));
        gd_debug('주문가격 업데이트 : '.$rslt);

        /*if( null != $orderDt ){
            $rslt1 = DBUtil2::runSql("update es_order set regDt = '{$orderDt}'  where orderNo = {$orderNo}");
            $rslt2 = DBUtil2::runSql("update es_orderGoods set regDt = '{$orderDt}'  where orderNo = {$orderNo}");
            gd_debug('주문일자 업데이트 : '.$rslt1.'/'.$rslt2);
        }*/

    }


    /**
     * 교환 체크
     * @throws \Exception
     */
    public function setRefineExchange(){
        $targetSql = "select distinct a.orderNo from es_orderGoods a join es_orderHandle b on a.handleSno = b.sno where a.handleSno > 0 and left(a.orderStatus,1) in ( 's' , 'd', 'g' ) and left(beforeStatus ,1) not in ('g', 'p')";
        $list = DBUtil2::runSelect($targetSql);
        foreach( $list as $each ){
            $inOutReason = ErpCodeMap::ERP_STOCK_REASON['교환출고'];
            $subSql = "select * from sl_3plStockInOut where orderNo = '{$each['orderNo']}' and inOutType=2 and inOutReason=2 order by inOutDate";
            $inOutList = DBUtil2::runSelect($subSql);
            $guideDate = $inOutList[0]['inOutDate'];
            foreach($inOutList as $inOutDate){
                if( $guideDate != $inOutDate['inOutDate'] ){
                    $rslt = DBUtil2::update('sl_3plStockInOut',[
                        'inOutReason' => $inOutReason
                    ], new SearchVo('sno=?', $inOutDate['sno']));
                    gd_debug($inOutDate['orderNo']. ' / ' . $inOutDate['sno'] .' / '.$guideDate.' / '.$inOutDate['inOutDate'] . ' / ' . $rslt);
                }
            }
        }
    }

    /**
     * 송장 등록
     * @throws \Exception
     */
    public function setIdentificationToInvoice(){
        $list = DBUtil2::getList('sl_3plStockInOut','inOutType','2');
        foreach($list as $each){
            if(empty($each['invoiceNo'])){
                $idList = explode('_',$each['identificationText']);
                DBUtil2::update('sl_3plStockInOut', [
                    'invoiceNo' => $idList[count($idList)-1]
                ], new SearchVo('sno=?',$each['sno']));
            }
        }
    }


    /**
     * 주문번호를 통한 사이즈 교체 ( optionSno로 하는게 아니라 묶음 주문 있을 때는 새로 로직 짜기 )
     * @param $orderNoList
     * @param $optionSno
     * @param $optionInfo
     * @throws \Exception
     */
    public function changeOrderGoodsOption($orderNoList, $optionSno, $optionInfo){
        foreach($orderNoList as $orderNo){
            $rslt = DBUtil2::update(DB_ORDER_GOODS, [
                'optionSno' => $optionSno,
                'optionInfo' => $optionInfo,
            ], new SearchVo('orderNo=?', $orderNo));

            gd_debug($orderNo . ' : ' . $rslt);
        }
    }
    public function changeOrderGoodsOptionControl(){
        $sizeInfo = "[[\"사이즈\",\"110\",\"APTKEL005\",0,null]]";
        $orderNoList = [
            2306081222148450	,
            2306081820424034	,
            2306081941010439	,
            2306081206517666	,
            2306081205134652	,
            2306081803595833	,
            2306081637394904	,
            2306081524367911	,
            2306081454266097	,
            2306081447234045	,
        ];
        $this->changeOrderGoodsOption($orderNoList, 2851, $sizeInfo);

        $sizeInfo = "[[\"사이즈\",\"105\",\"APTKEL004\",0,null]]";
        $orderNoList = [
            2306081243215988	,
            2306081235574412	,
            2306081215297812	,
        ];
        $this->changeOrderGoodsOption($orderNoList, 2850, $sizeInfo);
    }

    /**
     * 파트너사 관리자에서 파트너사로 주문 인계
     * @param $orderNo
     * @param $memNo
     * @return mixed
     * @throws \Exception
     */
    public function tkePartnerOrderChange($orderNo, $memNo){
        return DBUtil2::update(DB_ORDER, ['memNo'=>$memNo], new SearchVo('orderNo=?', $orderNo));
    }

    public function refineRecapEndDt(){
        $list = DBUtil2::getList('sl_recap','1','1');
        foreach($list as $each){
            $save = [];
            if( empty($each['designEndDt']) ){
                $designList = [];
                $designList[] = $each['planDt'];
                $designList[] = $each['proposalDt'];
                $designList[] = $each['sampleStartDt'];
                $designList[] = $each['bluePrintStartDt'];
                $save['designEndDt'] = max($designList);
            }
            if( empty($each['prdEndDt']) ){
                //QC 마감일.
                $qcList = [];
                $qcList[] = $each['btStartDt'];
                $qcList[] = $each['similarStartDt'];
                $save['prdEndDt'] = max($qcList);
            }

            if(!empty($save)){
                DBUtil2::update('sl_recap', $save, new SearchVo('sno=?', $each['sno']));
            }
        }
    }


    public function manualOrderHk(){

        DBUtil2::runSql("Truncate table sl_3plOrderTmp");

        $files = \Request::files()->toArray();
        $result = PhpExcelUtil::readToArray($files, 1);

        $prdColMap = [
            [2,  'MSHKS003', '티스테이션(HK)_카라티(반팔) 95'],
            [3,  'MSHKSC37', 'HK-하계카라티(차콜) 95'],
            [4,  'MSHKSC38', 'HK-하계카라티(차콜) 100'],
            [5,  'MSHKSC39', 'HK-하계카라티(차콜) 105'],
            [6,  'MSHKSC40', 'HK-하계카라티(차콜) 110'],
            [7,  'MSHKSB01', 'HK_하계바지(블랙) 28'],
            [8,  'MSHKSB02', 'HK_하계바지(블랙) 30'],
            [9,  'MSHKSB03', 'HK_하계바지(블랙) 32'],
            [10,  'MSHKSB04', 'HK_하계바지(블랙) 34'],
            [11,  'MSHKSB05', 'HK_하계바지(블랙) 36'],
            [12,  'MSHKSB06', 'HK_하계바지(블랙) 38'],
        ];

        //$prdColMap;
        $scmName = '한국타이어';
        $scmNo = '6';
        $totalCnt = 0; //총구매수량.
        $insertCnt = 0; //총구매수량.
        foreach($result as $index => $value){
            $manOrderNo = 'MS06'.date('mds').str_pad($index,4,"0",STR_PAD_LEFT);;

            foreach($prdColMap as $prdColData){

                $buyDataValue = $value[$prdColData[0]];

                if( !empty($buyDataValue) && is_numeric($buyDataValue) && !empty($value[13]) ){
                    $saveData = [
                        'orderNo' => $manOrderNo,
                        'customerName' => $value[1],
                        'address' => $value[13],
                        'phone' => $value[14],
                        'mobile' => $value[15],
                        'productCode' => $prdColData[1],
                        'productName' => $prdColData[2],
                        'qty' => $buyDataValue,
                        'scmName' => $scmName,
                        'scmNo' => $scmNo,
                    ];
                    DBUtil2::insert('sl_3plOrderTmp', $saveData);
                    $insertCnt++;
                    $totalCnt+=$buyDataValue;
                }
            }
        }
        gd_debug($insertCnt);
        gd_debug($totalCnt);
    }

    /**
     * 한전 옵션 처리
     * @throws \Exception
     */
    public function hanOption(){
        //상품 옵션 코드 변경.
        $option = DBUtil2::getList(DB_GOODS_OPTION, "optionNo >= 21 and 170 >= optionNo and goodsNo", '1000000338');
        //gd_debug($option);

        $idx = 0;
        $number = 24;
        $size = [
            0 => 'S',
            1 => 'M',
            2 => 'L',
            3 => 'LL',
            4 => 'LLL',
        ];
        $sizeIdx = 0;

        foreach($option as $each){
            $code = '23FWKPDPT' . $number . $size[$sizeIdx] ;
            $number++;
            if(45 == $number){
                $number=52;
            }

            //gd_debug($each['optionValue1'] . ' '. $each['optionValue2'] . ' : ' . $code);
            DBUtil2::update(DB_GOODS_OPTION, ['optionCode'=>$code], new SearchVo('sno=?',$each['sno']) );

            if(21 == $idx){
                $idx = 0;
                $number = 24;
                $sizeIdx++;
            }else{
                $idx++;
            }
        }
    }

    public function setManualInvoice($inOutDate){
        $erpService = SlLoader::cLoad('erp','erpService');
        $list = DBUtil2::getList('sl_3plStockInOut', 'inOutDate', $inOutDate);
        $orderNoList = [];
        foreach($list as $each){
            $code = $each['thirdPartyProductCode'];
            $invoice = $each['invoiceNo'];
            $cellPhone = $each['cellphone'];
            $customerName = $each['customerName'];
            $sql = "select a.sno, a.orderNo, a.handleSno 
                      from es_orderGoods a 
                      join es_goodsOption b on a.goodsNo = b.goodsNo 
                      join es_orderInfo c on a.orderNo = c.orderNo   
                     where b.optionCode = '{$code}'
                       and c.receiverCellPhone = '{$cellPhone}'
                       and c.receiverName = '{$customerName}'
                       and a.orderStatus = 'g1' ";

            $orderGoodsInfo = DBUtil2::runSelect($sql)[0];
            $updateSno = $orderGoodsInfo['sno'];
            if(!empty($updateSno)){
                $godoUpdateData = [
                    'orderStatus'=>'d1',
                    'invoiceCompanySno'=>'8',
                    'invoiceNo'=>$invoice,
                    'deliveryDt'=>'now()',
                ];
                DBUtil2::update(DB_ORDER_GOODS, $godoUpdateData, new SearchVo('sno=?', $updateSno ) );

                if( !empty($orderGoodsInfo['handleSno']) ){
                    DBUtil2::update('sl_3plStockInOut', [
                        'inOutReason' => 4 //교환출고.
                    ], new SearchVo('sno=?', $each['sno']));
                }
                $orderNoList[] = $orderGoodsInfo['orderNo'];
            }else{
                //고도몰 판매중인 상품이 있으면 판매수량 차감, 수기 주문 처리)
                $erpService->setOutStockSaleGoods([
                    'thirdPartyProductCode' => $code,
                    'quantity' => $each['quantity']
                ]);
            }

            foreach($orderNoList as $orderNo){
                $orderData = DBUtil2::getOne(DB_ORDER,'orderNo',$orderNo);
                if('g1' === $orderData['orderStatus']){
                    DBUtil2::update(DB_ORDER, ['orderStatus' => 'd1'], new SearchVo('orderNo=?', $orderNo));
                    $orderComponent = SlLoader::cLoad('Order','Order');
                    $orderComponent->sendOrderInfo(Code::DELIVERY, 'all', $orderNo);
                    $orderComponent->sendOrderInfo(Code::INVOICE_CODE, 'sms', $orderNo);
                }
            }

        }

    }

    public function setImsRefinePrdCost($projectSno){
        $list = DBUtil2::getList(ImsDBName::PRODUCT,1,1);
        foreach($list as $each){
            gd_debug('===================================');
            gd_debug(json_decode($each['fabric']));
            gd_debug(json_decode($each['subFabric']));
        }
    }

    /**
     * TS 같이 optionValue1, 2 사용하는 것 리파인
     * @param $orderGoodsSno
     * @param $change
     * @param $code
     * @throws \Exception
     */
    public function setHkOrderRefine($orderGoodsSno, $change, $code){
        //1. Backup
        $sql = "insert into sl_orderGoodsRefineHistory SELECT * FROM es_orderGoods WHERE sno = {$orderGoodsSno}";
        gd_debug('1. 백업처리.');
        gd_debug(DBUtil2::runSql($sql));

        //변경할 것 : optionSno , optionInfo
        $orderGoods = DBUtil2::getOne(DB_ORDER_GOODS, 'sno', $orderGoodsSno);
        $optionInfo = json_decode($orderGoods['optionInfo'],true);

        //이건 옵션에 따라 다를 것임.
        $optionInfo[1][2] = $code;
        $orderGoods['optionInfo'] = json_encode($optionInfo, JSON_UNESCAPED_UNICODE);

        $updateRslt = DBUtil2::update(DB_ORDER_GOODS, ['optionSno'=>$change, 'optionInfo'=>$orderGoods['optionInfo'] ], new SearchVo('sno=?', $orderGoodsSno));

        gd_debug('2. 옵션변경.');
        gd_debug($change);
        gd_debug($orderGoods['optionInfo']);
        gd_debug($updateRslt);

        //6253 (원본, 변경)
        //2555
    }

    /**
     * TKE 같이 optionValue1 만 사용하는 것 리파인
     * @param $orderGoodsSno
     * @param $change
     * @param $code
     * @param $size
     * @throws \Exception
     */
    public function setTkeOrderRefine($orderGoodsSno, $change, $code, $size){
        //1. Backup
        $sql = "insert into sl_orderGoodsRefineHistory SELECT * FROM es_orderGoods WHERE sno = {$orderGoodsSno}";
        gd_debug('1. 백업처리.');
        gd_debug(DBUtil2::runSql($sql));

        //변경할 것 : optionSno , optionInfo
        $orderGoods = DBUtil2::getOne(DB_ORDER_GOODS, 'sno', $orderGoodsSno);
        $optionInfo = json_decode($orderGoods['optionInfo'],true);

        //이건 옵션에 따라 다를 것임.
        $optionInfo[0][1] = $size;
        $optionInfo[0][2] = $code;
        unset($optionInfo[1]);

        gd_debug( $optionInfo );

        $orderGoods['optionInfo'] = json_encode($optionInfo, JSON_UNESCAPED_UNICODE);

        $updateRslt = DBUtil2::update(DB_ORDER_GOODS, ['optionSno'=>$change, 'optionInfo'=>$orderGoods['optionInfo'] ], new SearchVo('sno=?', $orderGoodsSno));

        gd_debug('2. 옵션변경.');
        gd_debug($change);
        gd_debug($orderGoods['optionInfo']);
        gd_debug($updateRslt);

        //6253 (원본, 변경)
        //2555
    }

    /*
        1. esOrderGoods 'p1' 상품 중  상품코드 위와 같을 때
        2. optionSno 로 option value1 값에 (과년) 찾고 stock 가져오기
        3. 해당 stockCnt 가 구매 수량 goodsCnt 보다 같거나 크면
        아래를 실행
        $this->setTkeOrderRefine(92750, 6618, 'MSHKSC31', 100); //주문번호, 변경상품옵션 HK 30
        $this->setTkeOrderRefine( orderGoods.sno  ,  '찾은 esGoodsOption.sno' ,  '찾은 optionCode' ,  검색 option value1  ); //주문번호, 변경상품옵션 HK 30
        변경 후 찾은 esGoodsOption stockCnt -goodsCnt
    */
    /**
     * 한타 과년재고 자동 빼기 (일단 HK 동계 한정)
     * @throws \Exception
     */
    public function setHankookOrderRefine(){
        /*$list = DBUtil2::runSelect("select * from es_orderGoods where orderStatus='p1' and goodsNo in ( 1000000282, 1000000288, 1000000286, 1000000284 )"); //조끼랑 카라티.
        foreach( $list as $each ){
            $srcOption = DBUtil2::getOne(DB_GOODS_OPTION, 'sno', $each['optionSno']);
            if ( strpos($srcOption['optionValue1'], '과년') === false) {
                $targetOptionName = $srcOption['optionValue1'].'(과년)';
                $targetOption = DBUtil2::getOne(DB_GOODS_OPTION, "goodsNo={$each['goodsNo']} and stockCnt >= {$each['goodsCnt']}  and optionValue1", $targetOptionName);
                if(!empty($targetOption)){
                    SitelabLogger::logger2(__METHOD__, "원주문:{$targetOption['sno']} / 변경옵션코드:{$targetOption['optionCode']} / 옵션명:{$srcOption['optionValue1']}  ");
                    $this->setTkeOrderRefine($each['sno'], $targetOption['sno'], $targetOption['optionCode'], $srcOption['optionValue1']); //주문번호, 변경상품옵션 HK 30
                    $updateRslt = DBUtil2::update(DB_GOODS_OPTION, ['stockCnt'=>$targetOption['stockCnt']-$each['goodsCnt']], new SearchVo('sno=?', $targetOption['sno']));
                }
            }
        }*/
    }

    /**
     * 삭제 데이터 복원
     * @param $sno
     */
    public function recoverDeleteData($sno){
        $inputData = DBUtil2::getOne('sl_imsDeleteHistory', 'sno', $sno, false);

        //gd_debug($data);
        //gd_debug(json_decode($inputData['contents'],true));
        $data =  json_decode($inputData['contents'],true);

        $sql = "insert into sl_imsProjectProduct (
                sno, projectSno, customerSno, styleCode, addStyleCode, productName, prdYear, prdSeason, prdGender, prdStyle, prdColor, produceType, produceCompanySno, produceNational, customerDeliveryDt, msDeliveryDt, prdExQty, prdMoq, priceMoq, addPrice, salePrice, currentPrice, targetPrice, targetPriceMax, targetPrdCost, prdCost, prdCount, estimateCost, estimateCount, prdCostStatus, fabricCost, subFabricCost, laborCost, marginCost, dutyCost, managementCost, memo, sizeOption, typeOption, fabric, subFabric, sizeSpec, fabricStatus, fabricNational, btStatus, msMargin, fabricCount, btCount, sampleConfirmSno, prdCostConfirmSno, prdCostConfirmManagerSno, prdCostConfirmDt, estimateConfirmSno, estimateConfirmManagerSno, estimateConfirmDt, estimateStatus, inlineStatus, inlineMemo, assortStatus, workStatus, productionStatus, priceConfirm, priceConfirmDt, priceApprovalName, priceCustConfirm, priceCustConfirmDt, fileThumbnail, fileWork, fileThumbnailReal, regManagerSno, lastManagerSno, masterStyleSno, assortMemo, moq, addedInfo, styleProcType, assort, assortConfirm, sort, parentSno, delFl, regDt, modDt                                  
) values (
'{$data['sno']}', '{$data['projectSno']}', '{$data['customerSno']}', '{$data['styleCode']}', '{$data['addStyleCode']}', '{$data['productName']}', '{$data['prdYear']}', '{$data['prdSeason']}', '{$data['prdGender']}', '{$data['prdStyle']}', '{$data['prdColor']}', '{$data['produceType']}', '{$data['produceCompanySno']}', '{$data['produceNational']}', '{$data['customerDeliveryDt']}', '{$data['msDeliveryDt']}', '{$data['prdExQty']}', '{$data['prdMoq']}', '{$data['priceMoq']}', '{$data['addPrice']}', '{$data['salePrice']}', '{$data['currentPrice']}', '{$data['targetPrice']}', '{$data['targetPriceMax']}', '{$data['targetPrdCost']}', '{$data['prdCost']}', '{$data['prdCount']}', '{$data['estimateCost']}', '{$data['estimateCount']}', '{$data['prdCostStatus']}', '{$data['fabricCost']}', '{$data['subFabricCost']}', '{$data['laborCost']}', '{$data['marginCost']}', '{$data['dutyCost']}', '{$data['managementCost']}', '{$data['memo']}', '{$data['sizeOption']}', '{$data['typeOption']}', '{$data['fabric']}', '{$data['subFabric']}', '{$data['sizeSpec']}', '{$data['fabricStatus']}', '{$data['fabricNational']}', '{$data['btStatus']}', '{$data['msMargin']}', '{$data['fabricCount']}', '{$data['btCount']}', '{$data['sampleConfirmSno']}', '{$data['prdCostConfirmSno']}', '{$data['prdCostConfirmManagerSno']}', '{$data['prdCostConfirmDt']}', '{$data['estimateConfirmSno']}', '{$data['estimateConfirmManagerSno']}', '{$data['estimateConfirmDt']}', '{$data['estimateStatus']}', '{$data['inlineStatus']}', '{$data['inlineMemo']}', '{$data['assortStatus']}', '{$data['workStatus']}', '{$data['productionStatus']}', '{$data['priceConfirm']}', '{$data['priceConfirmDt']}', '{$data['priceApprovalName']}', '{$data['priceCustConfirm']}', '{$data['priceCustConfirmDt']}', '{$data['fileThumbnail']}', '{$data['fileWork']}', '{$data['fileThumbnailReal']}', '{$data['regManagerSno']}', '{$data['lastManagerSno']}', '{$data['masterStyleSno']}', '{$data['assortMemo']}', '{$data['moq']}', '{$data['addedInfo']}', '{$data['styleProcType']}', '{$data['assort']}', '{$data['assortConfirm']}', '{$data['sort']}', '{$data['parentSno']}', '{$data['delFl']}', '{$data['regDt']}', '{$data['modDt']}'
) ";
        //gd_debug($sql);
        gd_debug('Rslt : '. DBUtil2::runSql($sql));
    }


    /**
     * 처리완료된 생산가 자동 선택.
     * 스타일에 estimateConfirmSno / prdCostConfirmSno 모두 없다면
     *
     */
    public function refineCost(){
        //우선 기획/제작
        $projectList = DBUtil2::runSelect("select * from sl_imsProject where projectStatus >= 20 and 90 >= projectStatus");
        foreach($projectList as $prj){
            //프로젝트가 기획이하 단계일 경우에는 패스
            if( 20 >= $prj['projectStatus'] ){
                continue;
            }
            $prdList = DBUtil2::getList(ImsDBName::PRODUCT, " projectSno={$prj['sno']} and 0 >= estimateConfirmSno and 0 >= prdCostConfirmSno and delFl", 'n'); //선택 안된 것
            foreach($prdList as $prd){
                //처리 완료된 견적에서 가장 최근 것 가져오기
                $estimateData = DBUtil2::getOneSortData(ImsDBName::ESTIMATE, ' ( 5 = reqStatus or 3 = reqStatus ) and styleSno=?', $prd['sno'] , 'regDt desc');
                if(!empty($estimateData)){
                    $rslt = DBUtil2::update(ImsDBName::PRODUCT, [
                        'estimateCost' => $estimateData['estimateCost'],
                        'estimateConfirmSno' => $estimateData['sno'],
                        'estimateConfirmManagerSno' => $estimateData['regManagerSno'],
                    ], new SearchVo('sno=?', $prd['sno']));
                    gd_debug( $prd['sno'] . ' : ' . $rslt);
                }
            }
        }

    }



    /**
     * 다운로드 - IMS 코멘트
     */
    public function downloadImsComment(){

        $sql = " 
select
    d.customerName, c.projectNo, c.projectYear, c.projectSeason 
    , b.managerNm
    , a.comment
    , a.regDt
from sl_imsComment a 
    join es_manager b on a.regManagerSno = b.sno 
    join sl_imsProject c on a.projectSno = c.sno
    join sl_imsCustomer d on c.customerSno = d.sno
order by c.regDt desc";

        $list = DBUtil2::runSelect($sql, null, false);

        $titles = [
            '번호',
            '고객명',
            '프로젝트번호',
            '연도',
            '시즌',
            '등록자',
            '내용',
            '등록일',
        ];

        $excelBody = '';
        foreach ($list as $key => $val) {
            $noPhpTags = preg_replace('/<\?php.*?\?>/s', '', $val['comment']);
            $plainText = strip_tags($noPhpTags);
            trim($plainText);

            $fieldData = array();
            $fieldData[] = ExcelCsvUtil::wrapTd($key+1);
            $fieldData[] = ExcelCsvUtil::wrapTd($val['customerName']);
            $fieldData[] = ExcelCsvUtil::wrapTd($val['projectNo']);
            $fieldData[] = ExcelCsvUtil::wrapTd($val['projectYear']);
            $fieldData[] = ExcelCsvUtil::wrapTd($val['projectSeason']);
            $fieldData[] = ExcelCsvUtil::wrapTd($val['managerNm']);
            $fieldData[] = ExcelCsvUtil::wrapTd($plainText);
            $fieldData[] = ExcelCsvUtil::wrapTd($val['regDt']);
            $excelBody .=  "<tr>". implode('',$fieldData) . "</tr>";
        }

        $simpleExcelComponent = SlLoader::cLoad('Excel','SimpleExcelComponent','sl');
        $simpleExcelComponent->simpleDownload('코멘트리스트',$titles,$excelBody);
    }


    /**
     * 폐쇄몰 주문 삭제
     * @param $orderNo
     * @throws \Exception
     */
    public function deleteOrder($orderNo){
        /*$list = DBUtil2::getList(DB_ORDER_GOODS, 'orderNo',$orderNo);
        foreach($list as $orderGoods){
            DBUtil2::delete('sl_asianaOrderHistory',new SearchVo('sno=?', $orderGoods['sno']));
        }*/
        $tables = [
            DB_ORDER,
            DB_ORDER_GOODS,
            DB_ORDER_INFO,
            DB_ORDER_DELIVERY,
        ];
        foreach($tables as $each){
            DBUtil2::delete($each, new SearchVo('orderNo=?', $orderNo));
        }
    }

}
