<?php
namespace Component\Scm;

use App;
use Component\Ims\ImsCodeMap;
use Component\Ims\ImsDBName;
use Session;
use Component\Database\DBTableField;
use Component\Member\Manager;
use Framework\Utility\DateTimeUtils;
use LogHandler;
use Request;
use Exception;
use Framework\Debug\Exception\AlertBackException;
use SiteLabUtil\SlCommonUtil;
use SlComponent\Database\DBUtil;
use SlComponent\Database\DBUtil2;
use SlComponent\Database\SearchVo;
use SlComponent\Database\TableVo;
use SlComponent\Util\ExcelCsvUtil;
use SlComponent\Util\PhpExcelUtil;
use SlComponent\Util\SitelabLogger;
use SlComponent\Util\SlCodeMap;
use SlComponent\Util\SlCommonTrait;
use SlComponent\Util\SlKakaoUtil;
use SlComponent\Util\SlLoader;
use Component\Mail\MailAutoObserver;
use Component\Godo\NaverPayAPI;
use Component\Member\Member;
use Component\Naver\NaverPay;
use Component\Delivery\OverseasDelivery;
use Component\Deposit\Deposit;
use Component\ExchangeRate\ExchangeRate;
use Component\Mail\MailMimeAuto;
use Component\Mall\Mall;
use Component\Mall\MallDAO;
use Component\Member\Util\MemberUtil;
use Component\Mileage\Mileage;
use Component\Policy\Policy;
use Component\Sms\Code;
use Component\Sms\SmsAuto;
use Component\Sms\SmsAutoCode;
use Component\Sms\SmsAutoObserver;
use Component\Validator\Validator;
use Component\Goods\SmsStock;
use Component\Goods\KakaoAlimStock;
use Component\Goods\MailStock;
use Encryptor;
use Framework\Application\Bootstrap\Log;
use Framework\Debug\Exception\AlertOnlyException;
use Framework\Debug\Exception\AlertRedirectException;
use Framework\Helper\MallHelper;
use Framework\Utility\ArrayUtils;
use Framework\Utility\ComponentUtils;
use Framework\Utility\NumberUtils;
use Framework\Utility\StringUtils;
use Framework\Utility\UrlUtils;
use Globals;
use Logger;
use Framework\Utility\KafkaUtils;
use SlComponent\Util\SlSmsUtil;
use Framework\Security\Digester;
use Framework\Utility\GodoUtils;

/*
-- sl_goodsStock 등록 .. HOW ?
insert into sl_goodsStock
*/

/**
 * 현대EL 서비스
 * Class GoodsStock
 * @package Component\Goods
 */
class ScmHyundaeService {
    //hdel123! ( NzJkMmM1OGY5OTlmMGM3NSQSrwWXfHqUcDBu/ycQN+mPZHaQasUYQF5FUdH8zyKe )
    //2(test id), 19839(hundai_ev, 문정은 매니저)
    const HD_MASTER = [
        2, 19839
    ];

    const REFINE_ADDR_MAP = [
        'Major지사 ',
        'Major지사 신분당선직영 ',
        '인천지사(서비스)-',
        '강서지사(구로직영)-',
        '강남지사(서비스)-',
        '울산지사(영업,설치)-',
        '강북지사(서비스)-',
        '대구지사(영업,설치)-',
        '중랑직영 (강북지사서비스)-',
        '안동직영 (서비스) - ',
        '속초직영(강원지사 서비스) - ',
        '경남지사(서비스,리모델링)-',
        '판교직영(분당지사 서비스)-',
        '수원지사(서비스)-',
        '부산지사(서비스)-',
        '전주지사(서비스)-',
        '서대구직영 (서비스) - ',
        '부산지사(영업,설치)-',
        '인천지사(남동직영)-',
        '강원지사(서비스)-',
        '영등포직영(강서지사서비스)-',
        '구미직영 (서비스) - ',
        '전주지사(영업,설치)-',
        '광교직영(수원서비스) - ',
        '송도직영(인천지사 서비스) ',
        '강북지사(서비스-동대문직영) - ',
        '강서지사(서비스)-',
        '분당지사(서비스)-',
        '강릉직영-',
        '연지동-',
        '대전지사(영업,설치,리모델링)-',
        'MP설치팀/강북리모델링팀-',
        '강남서비스직영-',
        '강남설치/강남리모델링팀-',
        '강북지사(설치)-',
        '강원서비스 (제천분소)-',
        '강원지사(설치)-',
        '경남지사(영업,설치)-',
        '광교직영(수원서비스) -',
        '광주지사(서비스)-',
        '광주지사(영업,설치)-',
        '구미직영 (서비스) -',
        '대구지사(서비스)-',
        '대전지사(서비스)-',
        '대전충청서비스 (청주직영)-',
        '상암직영(강서지사 서비스) -',
        '서대구직영 (서비스) -',
        '세종직영(서비스) -',
        '속초직영(강원지사 서비스) -',
        '안동직영 (서비스) -',
        '울산지사(서비스)-',
        '인천지사(영업,설치,리모델링)-',
        '제주지사(영업,설치,서비스)-',
        '중부지사 (설치) -',
        '춘천직영(서비스) -',
        '출하팀(천안)-',
        '출하팀(충주) -',
        '충주신공장 -',
        '충청지사(서비스)-',
    ];

    //주문 생성
    public function createOrders(){

        $order = \App::load(\Component\Order\Order::class);
        $goodsMap = [
            //'1' => DBUtil2::getOne(DB_GOODS, 'goodsNo', '1000000471'), //점퍼
            //'2' => DBUtil2::getOne(DB_GOODS, 'goodsNo', '1000000472'), //바지
            //'1' => DBUtil2::getOne(DB_GOODS, 'goodsNo', '1000000486'), //점퍼 (방한)
            //'2' => DBUtil2::getOne(DB_GOODS, 'goodsNo', '1000000487'), //바지 (방한)
            //'1' => DBUtil2::getOne(DB_GOODS, 'goodsNo', '1000000492'), //점퍼 (춘추)
            //'2' => DBUtil2::getOne(DB_GOODS, 'goodsNo', '1000000493'), //바지 (춘추)
            /*'1' => DBUtil2::getOne(DB_GOODS, 'goodsNo', '1000000558'), //상의 (25하계)
            '2' => DBUtil2::getOne(DB_GOODS, 'goodsNo', '1000000500'), //조끼 (25하계)
            '3' => DBUtil2::getOne(DB_GOODS, 'goodsNo', '1000000501'), //하의 (25하계)*/
            //'1' => DBUtil2::getOne(DB_GOODS, 'goodsNo', '1000000605'), //상의 (25동계)
            //'1' => DBUtil2::getOne(DB_GOODS, 'goodsNo', '1000000607'), //(25동계) //방한상의
            //'2' => DBUtil2::getOne(DB_GOODS, 'goodsNo', '1000000608'), //(25동계) //방한하의
            '1' => DBUtil2::getOne(DB_GOODS, 'goodsNo', '1000000605'), //(25동계) //상의
            '2' => DBUtil2::getOne(DB_GOODS, 'goodsNo', '1000000606'), //(25동계) //하의
        ];
        $desliverySno = 118;

        $deliverySearch = new SearchVo('deliverySno=?',$desliverySno);
        $deliverySearch->setLimit(1);
        $deliverySearch->setOrder("regDt desc");
        $deliveryInfo = DBUtil2::getOneBySearchVo(DB_ORDER_DELIVERY, $deliverySearch);
        unset($deliveryInfo['sno']);
        unset($deliveryInfo['orderNo']);

        //$list = DBUtil2::getList('zzz_sl_hyundaeOrder_250319_1','1','1');
        $list = DBUtil2::getList('sl_hyundaeOrder','1','1');
        gd_debug('생성할 주문:' . count($list));
        //gd_debug($list);

        $excelBody = '';
        $orderList = [];
        $orderCnt = [];
        foreach($list as $key => $value){
            //gd_debug( $value );
            $orderNo = $order->generateOrderNo();
            if(empty($orderList[$orderNo])){
                $orderList[$orderNo] = $value;
                $orderCnt[$orderNo]++;
            }else{
                $orderNo = $order->generateOrderNo();
                if(empty($orderList[$orderNo])){
                    $orderList[$orderNo] = $value;
                    $orderCnt[$orderNo]++;
                }else{
                    $orderNo = $order->generateOrderNo();
                    if(empty($orderList[$orderNo])){
                        $orderList[$orderNo] = $value;
                        $orderCnt[$orderNo]++;
                    }else{
                        $orderNo = $order->generateOrderNo();
                        if(empty($orderList[$orderNo])){
                            $orderList[$orderNo] = $value;
                            $orderCnt[$orderNo]++;
                        }else{
                            $orderNo = $order->generateOrderNo();
                            if(empty($orderList[$orderNo])){
                                $orderList[$orderNo] = $value;
                                $orderCnt[$orderNo]++;
                            }else{
                                $orderNo = $order->generateOrderNo();
                                if(empty($orderList[$orderNo])){
                                    $orderList[$orderNo] = $value;
                                    $orderCnt[$orderNo]++;
                                }else{
                                    $orderNo = $order->generateOrderNo();
                                    if(empty($orderList[$orderNo])){
                                        $orderList[$orderNo] = $value;
                                        $orderCnt[$orderNo]++;
                                    }else{
                                        $orderNo = $order->generateOrderNo();
                                        $orderList[$orderNo] = $value;
                                        $orderCnt[$orderNo]++;
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        gd_debug( count($orderList) );
        foreach( $orderList as $orderNo => $value){
            if($orderCnt[$orderNo] > 1){
                gd_debug($orderNo); //만들지 않고 표시만
            }else{
                $this->createOrder($value, $order, $goodsMap, $deliveryInfo, $orderNo);
            }
        }
    }

    /**
     * 주문 생성 (3품목)
     * @param $value
     * @param $order
     * @param $goodsMap
     * @param $deliveryInfo
     * @param $orderNo
     */
    public function createOrder($value, $order, $goodsMap, $deliveryInfo, $orderNo){
        $scmNo = 32;
        
        //$orderStatus = 's1'; //구매확정
        $orderStatus = 'p2'; //결제완료

        $settleKind = 'gz';
        //$now = '2025-05-29 08:30:00';
        //$now = '2025-10-26 14:40:00';
        //$now = '2025-10-19 13:40:00'; //방한복
        $now = '2025-10-12 13:40:00'; //동계복
        $memberData = DBUtil2::getOne(DB_MEMBER, 'memId', $value['memId']);

        $param['orderNo'] = $orderNo;
        $param['memNo'] = $memberData['memNo'];
        $param['orderStatus'] = $orderStatus;
        $param['settleKind'] = $settleKind;
        $param['paymentDt'] = $now;
        $param['regDt'] = $now;

        //$deliveryInfo = $orderParam['deliveryInfo'];
        $deliveryInfo['orderNo']=$orderNo;
        $deliveryInfo['regDt']=$now;
        $newOrderDeliverySno = DBUtil2::insert(DB_ORDER_DELIVERY, $deliveryInfo);
        DBUtil2::runSql("update es_orderDelivery set regDt='{$now}' where sno = {$newOrderDeliverySno}");//RegDt Update

        $orderParam = [
            'orderNo' => $orderNo,
            'orderStatus' => $orderStatus,
            'scmNo' => $scmNo,
            'now' => $now,
            'orderService' => $order,
            'deliveryNo' => $newOrderDeliverySno,
        ];

        //상품 저장하기
        //기본 상의

        $mainGoodsData = $goodsMap['1'];
        $goodsNm = $mainGoodsData['goodsNm'];
        $goodsKindCnt = 1;
        $goodsTotalCnt = $value['goods1Cnt'];
        $this->saveOrderGoods(1,$mainGoodsData, $value, $orderParam, $value['goods1Cnt']);

        if(!empty($value['goods2'])){
            //하의
            $goodsTotalCnt += $value['goods2Cnt'];
            $this->saveOrderGoods(2, $goodsMap['2'], $value, $orderParam, $value['goods2Cnt']);
            $goodsKindCnt++;
        }
        if(!empty($value['goods3'])){
            $goodsTotalCnt += $value['goods3Cnt'];
            $this->saveOrderGoods(3, $goodsMap['3'], $value, $orderParam, $value['goods3Cnt']);
            $goodsKindCnt++;
        }

        if( $goodsKindCnt > 1 ){
            $goodsNm .= ' 외 '. ($goodsKindCnt-1) .'건';
        }

        $param['orderGoodsNm'] = $goodsNm;
        $param['orderGoodsNmStandard'] = $goodsNm;
        $param['orderGoodsCnt'] = $goodsTotalCnt;
        $param['statusPolicy'] = '{"mplus":["s1"],"cplus":["s1"],"mminus":["o1"],"cminus":["o1"],"sminus":["o1"],"mrestore":["c1"],"crestore":["c1"],"srestore":["c1","b4","e5","r3"]}';

        //gd_debug('주문 데이터');
        //gd_debug($param);
        DBUtil2::insert(DB_ORDER, $param);
        SlCommonUtil::setRegDtUpdate(DB_ORDER, $now, "orderNo={$orderNo}");
        //OrderInfo
        $info['orderNo'] = $orderNo;
        $info['orderName'] = $value['memNm']; //주문자
        $info['receiverName'] = $value['receiver']; //수령자
        $info['receiverPhone'] = $value['cellPhone']; //연락처
        $info['receiverCellPhone'] = $value['cellPhone'];
        $info['receiverAddress'] = $value['address'];
        $info['receiverAddressSub'] = $value['receiver'];
        DBUtil2::insert(DB_ORDER_INFO, $info);
        SlCommonUtil::setRegDtUpdate(DB_ORDER_INFO, $now, "orderNo={$orderNo}");
    }

    /**
     * 상품등록
     * @param $idx
     * @param $goodsData
     * @param $value
     * @param $orderParam
     * @param $cnt
     */
    public function saveOrderGoods($idx, $goodsData, $value, $orderParam, $cnt){

        $goodsNo = $goodsData['goodsNo'];

        $saveGoods['mallSno']=1;
        $saveGoods['orderNo']=$orderParam['orderNo'];
        $saveGoods['orderCd']=$idx;
        $saveGoods['orderStatus']=$orderParam['orderStatus'];
        $saveGoods['orderDeliverySno']=$orderParam['deliveryNo'];
        $saveGoods['scmNo']=$orderParam['scmNo'];
        $saveGoods['goodsNo']=$goodsNo;
        $saveGoods['goodsNm']=$goodsData['goodsNm'];
        $saveGoods['goodsNmStandard']=$goodsData['goodsNm'];
        $saveGoods['goodsCnt']=$cnt;

        $size = trim($value['goods'.$idx]); //ex) 'goods1'
        $optionData = DBUtil2::getOneBySearchVo(DB_GOODS_OPTION, new SearchVo([
            'goodsNo=?',
            'optionValue1=?',
        ],[
            $goodsNo,
            $size,
        ]));
        $saveGoods['optionSno']=$optionData['sno'];
        $saveGoods['optionInfo']='[["사이즈","'.$size.'","'.$optionData['optionCode'].'",0,null]]';
        $saveGoods['paymentDt']=$orderParam['now'];
        $saveGoods['deliveryDt']=$orderParam['now'];
        $saveGoods['deliveryCompleteDt']=$orderParam['now'];
        //$saveGoods['regDt']=$orderParam['now'];
        //gd_debug('상품 데이터');
        //gd_debug($saveGoods);

        $orderGoodsSno = DBUtil2::insert(DB_ORDER_GOODS, $saveGoods); //재고 차감 필요시 수정. (TKE saveOrderGoods 참고)
        DBUtil2::runSql("update es_orderGoods set regDt='{$orderParam['now']}' where sno = {$orderGoodsSno}");//RegDt Update
    }

    //25 하계
    public function getPackingList(){ //상의, 조끼, 하의
        $options[1] = [90,95,100,105,110,115,120,125,130,];
        $options[2] = [28,30,32,34,36,38,40,42];
        $optionList = array_merge($options[1], $options[2]);
        $goodsKindCnt = count($options);

        $list = DBUtil2::getList('sl_hyundaeOrder','1','1');

        $deliveryInfo = [];
        $deliveryNo = 0;

        $beforeReceiver = '';
        foreach($list as $each){
            $receiver = $each['dept1'].'_'.$each['receiver'];
            if( $beforeReceiver !== $receiver ){
                $beforeReceiver = $receiver;
                $deliveryNo++;
            }
            if(empty($deliveryInfo[$deliveryNo])){
                $deliveryInfo[$deliveryNo]['deliveryInfo'] = $each; //대표.
            }
            for($idx=1; $goodsKindCnt>=$idx; $idx++){
                if(!empty($each['goods'.$idx.'Cnt'])){
                    $deliveryInfo[$deliveryNo]['goods'.$idx][$each['goods'.$idx]]+=$each['goods'.$idx.'Cnt'];
                }
            }
        }
        //gd_debug($deliveryInfo);

        $title = ['번호'];
        foreach($optionList as $option){
            $title[] = $option;
        }
        $title[] = '합계';
        $title[] = '부서';
        $title[] = '수령자';
        $title[] = '연락처';
        $title[] = '배송지주소';

        $excelBody = '';
        foreach($deliveryInfo as $deliveryNo => $delivery){

            $excelBody .= "<tr>";

            $excelBody .= ExcelCsvUtil::wrapTd($deliveryNo);
            $cnt = 0;
            for($idx=1; $goodsKindCnt>=$idx; $idx++){
                foreach($options[$idx] as $option){
                    $value = $delivery['goods'.$idx][$option];
                    $excelBody .= ExcelCsvUtil::wrapTd($value);
                    if(!empty($value)){
                        $cnt += $value;
                    }
                }
            }

            $cellPhone = SlCommonUtil::getCellPhoneFormat($delivery['deliveryInfo']['cellPhone']);
            $excelBody .= ExcelCsvUtil::wrapTd($cnt);
            $excelBody .= ExcelCsvUtil::wrapTd($delivery['deliveryInfo']['dept1']);
            $excelBody .= ExcelCsvUtil::wrapTd($delivery['deliveryInfo']['receiver']);
            $excelBody .= ExcelCsvUtil::wrapTd($cellPhone);

            $address = $delivery['deliveryInfo']['address'] . ' ' . $delivery['deliveryInfo']['dept1'];

            $excelBody .= ExcelCsvUtil::wrapTd($address);

            $excelBody .= "</tr>";

        }

        $simpleExcelComponent = SlLoader::cLoad('Excel','SimpleExcelComponent','sl');
        $simpleExcelComponent->simpleDownload('현대EL 분류패킹_25동계', $title, $excelBody);
    }

    //25 하계
    public function setManualPackingListV3($files){
        $refineList = ScmHyundaeService::REFINE_ADDR_MAP;
        DBUtil2::runSql("truncate table sl_hyundaeOrder");
        $result = PhpExcelUtil::readToArray($files, 1);
        foreach($result as $index => $val){
            if(empty($val[2]) || 7 > strlen($val[2])) continue;
            $deliveryInfo = [
                'memId' => $val[2],
                'memNm' => $val[3],
                'receiver' => $val[9],
                'address' => str_replace($refineList, '', $val[8]),
                'cellPhone' => $val[10],
                'goods1' => $val[4], //조끼
                'goods2' => $val[6], //상의
                'goods3' => $val[6], //하의
            ];
            DBUtil2::insert('sl_hyundaeOrder', $deliveryInfo);
        }
    }

    //25 춘추
    public function setManualPackingListV2($files){

        $refineList = ScmHyundaeService::REFINE_ADDR_MAP;

        DBUtil2::runSql("truncate table sl_hyundaeOrder");
        $result = PhpExcelUtil::readToArray($files, 1);
        foreach($result as $index => $val){
            if(empty($val[2]) || 7 > strlen($val[2])) continue;
            $deliveryInfo = [
                'memId' => $val[2],
                'memNm' => $val[3],
                'receiver' => $val[9],
                'address' => str_replace($refineList, '', $val[8]),
                'cellPhone' => $val[10],
                'goods1' => $val[4],
                'goods2' => $val[6],
            ];
            DBUtil2::insert('sl_hyundaeOrder', $deliveryInfo);
        }
    }

    /**
     * 수기 주문 넣기
     * @param $files
     */
    public function setManualPackingListV1($files){
        //DBUtil2::runSql("drop table zzz_sl_hyundaeOrder_tmp");
        //DBUtil2::runSql("create table zzz_sl_hyundaeOrder_tmp select * from sl_hyundaeOrder");
        DBUtil2::runSql("truncate table sl_hyundaeOrder");

        $result = PhpExcelUtil::readToArray($files, 1);
        $deliveryNo = 1;
        foreach($result as $index => $val){

            $dept = $val[1];
            if( '구분' ===  $dept) {
                $deliveryNo++;
                continue;
            }
            if( empty($dept) ) continue;

            $deliveryInfo = [
                'deliveryNo' => $deliveryNo,
                'dept1' => $val[1], //구분
                'memId' => $val[2], //사번
                'memNm' => $val[3], //이름
                'receiver' => $val[11], //수령자
                'address' => str_replace(ScmHyundaeService::REFINE_ADDR_MAP, '', $val[4]), //주소
                'cellPhone' => $val[12],
                'goods1' => $val[5],  //상의
                'goods1Cnt' => $val[6],
                'goods2' => $val[7], //조끼
                'goods2Cnt' => $val[8],
                'goods3' => $val[9], //하의
                'goods3Cnt' => $val[10],
            ];
            $rslt = DBUtil2::insert('sl_hyundaeOrder', $deliveryInfo);
            gd_debug($deliveryInfo['memNm'] . ' : ' . $rslt);
        }
    }

    /**
     * 수기 주문 넣기
     * @param $files
     */
    public function setManualPackingListV4($files){
        DBUtil2::runSql("truncate table sl_hyundaeOrder");

        $result = PhpExcelUtil::readToArray($files, 1);
        $deliveryNo = 1;
        foreach($result as $index => $val){

            $dept = $val[1];
            if( '구분' ===  $dept) {
                $deliveryNo++;
                continue;
            }
            if( empty($dept) ) continue;

            $deliveryInfo = [
                'deliveryNo' => $deliveryNo,
                'dept1' => $val[1], //구분
                'dept2' => $val[2], //구분
                'memId' => $val[3], //사번
                'memNm' => $val[4], //이름
                'receiver' => $val[5], //수령자
                'address' => str_replace(ScmHyundaeService::REFINE_ADDR_MAP, '', $val[6]), //주소
                'cellPhone' => $val[7],
                'goods1' => $val[8],  //상의
                'goods1Cnt' => $val[9],
                'goods2' => $val[10], //조끼
                'goods2Cnt' => $val[11],
                'goods3' => $val[12], //하의
                'goods3Cnt' => $val[13],
            ];
            $rslt = DBUtil2::insert('sl_hyundaeOrder', $deliveryInfo);
            gd_debug($deliveryInfo['memNm'] . ' : ' . $rslt);
        }
    }

    /**
     * 수기 주문 (25 FW)
     * @param $files
     */
    public function setManualPackingListV5($files){
        DBUtil2::runSql("truncate table sl_hyundaeOrder");

        $result = PhpExcelUtil::readToArray($files, 1);
        $deliveryNo = 0;
        $beforeReceiver = '';
        foreach($result as $index => $val){

            $receiver = $val[1].'_'.$val[5];

            if( empty($receiver) ) continue;

            if($beforeReceiver !== $receiver) {
                $deliveryNo++;
                $beforeReceiver = $receiver;
            }

            $deliveryInfo = [
                'deliveryNo' => $deliveryNo,
                'dept1' => $val[1], //구분
                'dept2' => $val[2], //구분
                'memId' => $val[3], //사번
                'memNm' => $val[4], //이름
                'receiver' => $val[5], //수령자
                'address' => str_replace(ScmHyundaeService::REFINE_ADDR_MAP, '', $val[6]), //주소
                'cellPhone' => $val[7],
                'goods1' => $val[8],  //
                'goods1Cnt' => $val[9],
                'goods2' => $val[10], //
                'goods2Cnt' => $val[11],
                'goods3' => $val[12], //
                'goods3Cnt' => $val[13],
            ];
            $rslt = DBUtil2::insert('sl_hyundaeOrder', $deliveryInfo);
            //gd_debug($deliveryInfo['memNm'] . ' : ' . $rslt);
        }
    }


    /**
     * 주문리스트 25 하계
     */
    public function getPackingOrderList(){

        $exclude = [];

        $title = [
            '배송번호',
            '구분',
            '사번',
            '성명',
            '배송지',
            '연락처',
            '상의',
            '상의수량',
            //'조끼',
            //'조끼수량',
            '하의',
            '하의수량',
        ];

        $list = DBUtil2::getList('sl_hyundaeOrder','1','1');
        foreach($list as $each){
            if( in_array($each['deliveryNo'], $exclude) ) continue;
            $excelBody .= "<tr>";
            $excelBody .= ExcelCsvUtil::wrapTd($each['deliveryNo']); //배송번호
            $excelBody .= ExcelCsvUtil::wrapTd($each['dept1']); //구분
            //$excelBody .= ExcelCsvUtil::wrapTd($each['dept2']);
            //$excelBody .= ExcelCsvUtil::wrapTd($each['pos']);
            $excelBody .= ExcelCsvUtil::wrapTd($each['memId']); //사번
            $excelBody .= ExcelCsvUtil::wrapTd($each['memNm']); //성명
            $excelBody .= ExcelCsvUtil::wrapTd($each['address']); //배송지
            $excelBody .= ExcelCsvUtil::wrapTd($each['cellPhone'],'text','mso-number-format:\'\@\''); //전화
            $excelBody .= ExcelCsvUtil::wrapTd($each['goods1']);
            $excelBody .= ExcelCsvUtil::wrapTd($each['goods1Cnt']);
            $excelBody .= ExcelCsvUtil::wrapTd($each['goods2']);
            $excelBody .= ExcelCsvUtil::wrapTd($each['goods2Cnt']);
            //$excelBody .= ExcelCsvUtil::wrapTd($each['goods3']);
            //$excelBody .= ExcelCsvUtil::wrapTd($each['goods3Cnt']);
            $excelBody .= "</tr>";
        }

        $simpleExcelComponent = SlLoader::cLoad('Excel','SimpleExcelComponent','sl');
        $simpleExcelComponent->simpleDownload('현대EL 주문리스트', $title, $excelBody);
    }


    /**
     * 입력한 아이디로 주문 변경
     * @param $orderNo
     * @throws Exception
     */
    public function setHyundaeMasterOrder($orderNo){
        $postValue = Request::post()->toArray();
        //사번이 들어왔는지 확인

        //ID 생성-------------------------------------------------------
        if( !empty($postValue['companyNo']) ){
            //회원 찾기
            $memberData = DBUtil2::getOne(DB_MEMBER, 'memId', $postValue['companyNo']);
            if( empty($memberData) ){
                $param['appFl'] = 'y';

                $createId = trim($postValue['companyNo']);
                //회원 생성
                $param['memId'] = $createId;
                $param['agreementInfoFl'] = 'y';
                $param['privateApprovalFl'] = 'y';
                $param['memPw'] = 'hdel123!@123!@';
                //$param['memNm'] = $postValue['receiverName'];
                $param['memNm'] = $postValue['orderName'];
                $param['ex1'] = '현대엘리베이터';
                $memberService = SlLoader::cLoad('member','member');
                $rt = $memberService->join($param);
                $memNo = $rt->getMemNo();
            }else{
                $memNo = $memberData['memNo'];
            }
            //주문을 변경한다. (결제 완료 처리 및 생성 또는 재계약 회원으로 변경)
            $updateData['memNo'] = $memNo;
            DBUtil2::update(DB_ORDER, $updateData, new SearchVo('orderNo=?', $orderNo));
            DBUtil2::update(DB_ORDER_INFO, [
                //'orderName' => $postValue['receiverName']
                'orderName' => $postValue['orderName']
            ], new SearchVo('orderNo=?', $orderNo));
            //암호 변경
            DBUtil2::update(DB_MEMBER,['memPw'=>'NzJkMmM1OGY5OTlmMGM3NSQSrwWXfHqUcDBu/ycQN+mPZHaQasUYQF5FUdH8zyKe'], new SearchVo('memNo=?',$memNo));
        }
    }

}
