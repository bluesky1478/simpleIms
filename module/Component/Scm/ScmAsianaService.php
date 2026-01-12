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
use SiteLabUtil\FileUtil;
use SiteLabUtil\SlCommonUtil;
use SlComponent\Database\DBUtil;
use SlComponent\Database\DBUtil2;
use SlComponent\Database\SearchVo;
use SlComponent\Database\TableVo;
use SlComponent\Mail\SiteLabMailUtil;
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
use DateTime;

/**
 * 아시아나 서비스
 * Class GoodsStock
 * @package Component\Goods
 */
class ScmAsianaService {

    private $sql;

    public function __construct(){
        $this->sql = \App::load(\Component\Scm\Sql\ScmAsianaSql::class);
    }

    /***
     * 사번 변경
     * @param $after
     * @param $before
     * @param $position
     * @throws Exception
     */
    public function refineCompanyId($after, $before, $position=null){
        DBUtil2::update('sl_asianaOrderHistory',['companyId'=>$after],new SearchVo('companyId=?', $before));
        $empUpdateData = [
            'companyId'=>$after,
        ];
        if(!empty($position)){
            $empUpdateData['empRank'] = $position;
        }
        DBUtil2::update('sl_asianaEmployee',$empUpdateData,new SearchVo('companyId=?', $before));
    }

    public function migrationMethod(){
        //제공내역 등록최근3년 지급내역 시작
        /*$empList = DBUtil2::getList('sl_asianaEmployee','1','1');
        //TODO : 주문 이력 사원에 저장하기
        $checkCnt = 0;
        $completeCnt = 0;
        foreach($empList as $each){
            $companyId = $each['companyId'];
            $checkCnt += $this->saveEmpAllHistory($companyId);
            $completeCnt++;
        }
        gd_debug($checkCnt);
        gd_debug($completeCnt);*/


        //제공내역 등록최근3년 지급내역 종료
        $service = SlLoader::cLoad('scm','ScmAsianaService');


        //$service->asianaOrder(); //?뭐였더라
        //$list = $service->getCartList();
        //gd_debug($list);

        //$list = $service->getAsianaEmpData(941088);
        //gd_debug($list);


        //상품 생성 ( TODO  이거 카테고리 및 기성품 보고 다시 생성 하기 )
        //$service->createProduct();
    }

    /**
     * 기존 상품 자료 등록
     * @param $files
     */
    public function insertGoods($files){
        $dataMap = [
            2 => 'prdName',
            3 => 'prdOption',
            4 => 'initStock',
            5 => 'prdCode',
            6 => 'cate1',
            7 => 'cate2',
        ];
        FileUtil::loadAndInsert($files, $dataMap, 'sl_asianaItem');
    }

    /**
     * 기존 주문 이력 등록
     * @param $files
     */
    public function insertOrderHistory($files){
        $dataMap = [
            1 => 'empTeam',
            2 => 'empPart1',
            3 => 'empPart2',
            4 => 'companyId',
            5 => 'name',
            6 => 'requestDt',
            7 => 'prdName',
            8 => 'prdOption',
            9 => 'orderCnt',
        ];
        FileUtil::loadAndInsert($files, $dataMap, 'sl_asianaOrderHistory');
    }

    public function insertAddOrderHistory($files){
        $goodsMap = [
            4 => '동복상의',
            5 => '동복조끼',
            6 => '동복하의(홑)',
            7 => '동복하의(기모)',
        ];

        $result = PhpExcelUtil::readToArray($files, 1);
        foreach($result as $index => $val){
            $data = [
                'companyId' => $val[2],
                'name' => $val[3],
                'requestDt' => $val[1],
            ];
            for($i=4; 8>=$i; $i++){
                if( !empty($val[$i])  ){
                    $data['prdName'] = $goodsMap[$i];
                    $data['prdOption'] = $val[$i];
                    DBUtil2::insert('sl_asianaOrderHistory', $data);
                }
            }
        }
        
        //완료 후 이력 갱신
        $service = SlLoader::cLoad('scm','ScmAsianaService');
        $refreshList = DBUtil2::runSelect("select distinct companyId from sl_asianaOrderHistory");
        foreach($refreshList as $data){
            $service->saveEmpAllHistory($data['companyId']);
        }
    }

    /**
     * 기존 전달해준 자료 기준으로 폐쇄몰 상품 생성
     * @throws Exception
     */
    public function createProduct(){
        $asianaGoodslist = DBUtil2::runSelect("select * from sl_asianaItem");

        $goodsMap = [];
        foreach($asianaGoodslist as $asianaGoods){
            $goodsMap[$asianaGoods['prdName']][] = $asianaGoods;
        }
        //gd_debug($goodsMap);

        $deliveryInfo = DBUtil2::getOne(DB_SCM_DELIVERY_BASIC, "fixFl='free' and scmNo", '34');

        $saveDataList = [];
        foreach($goodsMap as $key => $goodsList){

            if( !empty($key) ){
                $createGoodsData = [
                    'scmNo' => 34,
                    'totalStock' => 0,
                    'stockFl' => 'y',
                    'goodsDisplayFl' => 'y',
                    'goodsDisplayMobileFl' => 'y',
                    'optionFl' => 'y',
                    'addGoodsFl' => 'n',
                    'deliverSno' => 'n',
                    'optionName' => '사이즈',
                    'deliverySno' => $deliveryInfo['sno'],
                    'goodsNm' => $key,
                    'option' => [],
                ];

                foreach($goodsList as $goods){
                    //카테고리 가져오기.
                    $category = DBUtil2::getOne(DB_CATEGORY_GOODS, "cateCd like '034%' and cateNm", trim($goods['cate2']));
                    if( !empty($category) ){
                        $createGoodsData['cateCd'] = $category['cateCd'];
                    }

                    if( !empty($goods['prdOption']) || !empty($goods['prdCode']) ){
                        $createGoodsData['option'][] = [
                            'optionValue1' => $goods['prdOption'],
                            'optionCode' => $goods['prdCode'], //출고시 맵핑.
                            'stockCnt' => $goods['initStock'], //초기 수량
                            'confirmRequestStock' => 1, //초기 수량
                        ];
                        $createGoodsData['totalStock'] += $goods['initStock'];
                    }

                }
                //gd_debug('=====> ' . $key);
                //gd_debug($createGoodsData);
                $saveDataList[] = $createGoodsData;
            }else{
                gd_debug('키 없음 데이터 확인');
                gd_debug($goodsList);
            }

        }
        //gd_debug($saveData);
        $srcGoodsNo = '1000000108';
        $goodsAdminService = SlLoader::cLoad('goods','goodsAdmin');
        $newGoodsList = [];

        foreach($saveDataList as $saveData){
            $newGoodsNo=$goodsAdminService->setCopyGoods($srcGoodsNo);
            DBUtil2::delete('es_goodsOption',new SearchVo('goodsNo=?', $newGoodsNo));
            DBUtil2::delete('es_goodsLinkCategory',new SearchVo('goodsNo=?', $newGoodsNo));
            DBUtil2::update('es_goods',$saveData,new SearchVo('goodsNo=?', $newGoodsNo));
            foreach($saveData['option'] as $optionDatKey => $optionData){
                $optionData['optionNo'] = $optionDatKey+1;
                $optionData['goodsNo'] = $newGoodsNo;
                $optionData['optionViewFl'] = 'y';
                $optionData['optionSellFl'] = 'y';
                $optionSno = DBUtil2::insert('es_goodsOption', $optionData);
                //Update
                DBUtil2::update('sl_asianaItem',[
                    'goodsNo' => $newGoodsNo,
                    'optionSno' => $optionSno,
                    'cateCd' => $saveData['cateCd'],
                ], new SearchVo('prdCode=?', $optionData['optionCode']));
            }

            DBUtil2::insert('es_goodsLinkCategory',['goodsNo' => $newGoodsNo,'cateCd' => '034',]);
            if(!empty($saveData['cateCd'])){
                DBUtil2::insert('es_goodsLinkCategory',['goodsNo' => $newGoodsNo,'cateCd' => substr($saveData['cateCd'], 0, 6)]);
                DBUtil2::insert('es_goodsLinkCategory',['goodsNo' => $newGoodsNo,'cateCd' => $saveData['cateCd'],]);
            }
            $newGoodsList[] = $newGoodsNo;
        }

        gd_debug(count($newGoodsList).'개의 상품이 생성');
        gd_debug($newGoodsList);

    }

    /**
     *
     * 제약 조건은 DB에 저장된 오류 + 실시간 체크된 것 (재고 같은) 것 체크해서 내뱉는다.
     *
     * 현재 카트리스트를 보여준다 (반드시 로그인한 자의 카트만 보여준다)
     * @throws Exception
     */
    public function getCartList(){
        $memNo = \Session::get('member.memNo');

        if(empty($memNo)){
            throw new Exception('로그인이 필요한 서비스 입니다.');
        }

        $list = $this->sql->getCartList(['memNo' => $memNo]);

        $goodsOptionOrderList = [];
        $goodsOptionList = [];

        //택1상품 처리 맵
        $choiceGoodsCheckMap = [];

        $allowTeam = ScmAsianaCodeMap::FW_BOOTS_ALLOW_TEAM['team'];
        $allowPart1 = ScmAsianaCodeMap::FW_BOOTS_ALLOW_TEAM['part1'];
        $allowPart2 = ScmAsianaCodeMap::FW_BOOTS_ALLOW_TEAM['part2'];

        //제약 처리.
        foreach($list as $key => $each){
            $goodsOptionList[] = $each['optionSno'];
            $goodsOptionOrderList[$each['optionSno']] += $each['orderCnt']; //신청수량
            $orderHistory = array_reverse(json_decode($each['provideInfo'], true));

            //$orderHistory = json_decode($each['provideInfo'], true);
            $each['orderHistory'] = $orderHistory;

            foreach($orderHistory as $order){
                $year = substr($order['requestDt'],2,2).'년';
                $each['orderYear'][$year][$order['prdName']]['prdName'] = $order['prdName'];
                $each['orderYear'][$year][$order['prdName']]['orderCnt'] += $order['orderCnt'];
                $order['prdName'];
            }

            //이노버랑(테스트를 위한 제약) . 마스터는 안한다.
            //if( 19963 == $memNo && 19964 )
            if( 19964 != $memNo ){
                $this->checkProvideInfo($each);
                //택1상품 체크 : TODO 추후 택1상품 더 생기면 체크해봐야함
                foreach( ScmAsianaCodeMap::CHOICE_GOODS as $choiceGoods ){
                    if( in_array($each['goodsNm'],$choiceGoods)){
                        $choiceGoodsCheckMap[$each['companyId']]++;
                        if( $choiceGoodsCheckMap[$each['companyId']] > 1 ){
                            $each['isValid']=true;
                            $each['isValidMsg']=implode(',', $choiceGoods).' 중 택1 처리';
                        }
                    }
                }

                //동계 상품 주문불가
                if( 'group' === $each['goodsAccess'] && '2' == $each['goodsAccessGroup']){
                    $each['isValid']=true;
                    $each['isValidMsg']='현재 주문 불가';
                }

                //방한화 특정팀만 주문 가능. 테스트 바꾸기 FIXME : 면티(긴팔)
                if( '방한화' == $each['goodsNm'] ){
                    if( !(in_array($each['empTeam'],$allowTeam)
                        && in_array($each['empPart1'],$allowPart1)
                        && in_array($each['empPart2'],$allowPart2)) ){
                        $each['isValid']=true;
                        $each['isValidMsg']='주문 허용 부서 아님';
                    }
                }

                //SitelabLogger::logger2(__METHOD__, $each['optionSno']);
                /*SitelabLogger::logger2(__METHOD__, $each['companyId']);
                SitelabLogger::logger2(__METHOD__, str_replace(' ', '',$each['empTeam']) );
                SitelabLogger::logger2(__METHOD__, str_replace(' ', '',$each['empPart1']) );
                SitelabLogger::logger2(__METHOD__, str_replace(' ', '',$each['empPart2']) );*/

            }

            //퇴직자 주문 불가 처리
            if('y' === $each['retiredFl']){
                $each['isValid']=true;
                $each['isValidMsg']='퇴직자/주문불가';
            }
            $list[$key] = $each;
        }

        SlCommonUtil::setListRowSpan($list, [
            'user'  => ['valueKey' => ['companyId','name']] //projectRowspan (each , field)
        ], [
            'condition' => ['sort' => '']
        ]);

        $stockMap = $this->getStockCheckData($goodsOptionList, $goodsOptionOrderList);

        //재고 Validation
        $isStockValid = true;
        foreach( $stockMap as $stockDatas ){
            foreach( $stockDatas as $stockData ){
                if($stockData['orderCnt'] > $stockData['stockCnt']){
                    $isStockValid = false;
                    break;
                }
            }
        }

        return [
            'stockValid' => $isStockValid,
            'stockMap' => $stockMap,
            'list' => $list
        ];
    }

    /**
     * 지급 기준 체크
     * @param $each
     */
    public function checkProvideInfo(&$each){
        //이력에서 있었던 상품 찾기
        foreach($each['orderHistory'] as $orderHistoryKey => $orderHistory){
            //과거의 이력 상품명 다른경우 여기서 맵핑
            if (strpos($orderHistory['onlyPrdName'], '장화') !== false) {
                $orderHistory['onlyPrdName'] = '우화';
            }
            $orderHistory['onlyPrdName'] = str_replace('/신','',$orderHistory['onlyPrdName']);

            $checkPrd = [];
            $checkPrd[] = $orderHistory['onlyPrdName'];
            foreach( ScmAsianaCodeMap::CHOICE_GOODS as $choiceGoods ){
                if( in_array($orderHistory['onlyPrdName'],$choiceGoods)){
                    $checkPrd = $choiceGoods;
                    break;
                }
            }

            //히스토리 상품명 , 주문한 상품 .
            if (in_array($each['goodsNm'], $checkPrd)){
                //최근 데이터 찾음.
                if(SlCommonUtil::isDev()){
                    $standard = ScmAsianaCodeMap::GOODS_PROVIDE_INFO_DEV[ScmAsianaCodeMap::GOODS_PROVIDE_INFO_KR_DEV[$each['goodsNm']]]; //TODO 추후에는 DB에서 한번에 빼오기
                }else{
                    $standard = ScmAsianaCodeMap::GOODS_PROVIDE_INFO[ScmAsianaCodeMap::GOODS_PROVIDE_INFO_KR[$each['goodsNm']]];
                }

                if($standard['yearCnt'] > 0){
                    if('y' === $standard['isYearCheck']){
                        //날짜로 체크
                        $d1 = intval(substr($orderHistory['requestDt'], 0, 4));
                        $d2 = intval(substr(date('Ymd'), 0, 4));
                        $diff = $d2 - $d1;

                        //Or 년도로 체크
                        if( $standard['yearCnt'] > $diff ){
                            $each['isValid']=true;
                            $each['isValidMsg']='지급연한 안됨';
                        }
                    }else{
                        $standardDays = $standard['yearCnt']*365;
                        //날짜로 체크
                        $d1 = DateTime::createFromFormat('Ymd', $orderHistory['requestDt']);
                        $d2 = DateTime::createFromFormat('Ymd', date('Ymd'));
                        $diff = $d1->diff($d2);

                        //Or 년도로 체크
                        if( $standardDays > $diff->days ){
                            $each['isValid']=true;
                            $each['isValidMsg']='지급연한 안됨';
                        }
                    }
                }

                break;
            }
        }

        //연도가 OK면 지급 수량 체크 지급수량 이상 주문
        if(SlCommonUtil::isDev()){
            $provideCntStandard = ScmAsianaCodeMap::GOODS_PROVIDE_INFO_DEV[ScmAsianaCodeMap::GOODS_PROVIDE_INFO_KR_DEV[$each['goodsNm']]];
        }else{
            $provideCntStandard = ScmAsianaCodeMap::GOODS_PROVIDE_INFO[ScmAsianaCodeMap::GOODS_PROVIDE_INFO_KR[$each['goodsNm']]];
        }

        if($provideCntStandard['provideCnt'] > 0 && $each['orderCnt'] > $provideCntStandard['provideCnt']) {
            $each['isValid']=true;
            $each['isValidMsg'].=" 지급수량({$provideCntStandard['provideCnt']}ea) 초과";
        }
        //TODO : 나눠서 주문하는 경우 체크 (현재는 그저 버그로 둔다)
        //SitelabLogger::logger2(__METHOD__, $goodsNoList);
        //$each['isValid']=true;
        //$each['isValidMsg']='지급기준 안됨';
    }

    /**
     * 신청한 상품 옵션에 따른 재고 가져오기
     * @param $goodsOptionList
     * @param $goodsOptionOrderMap
     * @return array
     */
    public function getStockCheckData($goodsOptionList, $goodsOptionOrderMap){
        $goodsOptionStr = implode(',', $goodsOptionList);
        $sql = "select a.goodsNm, b.optionValue1, b.stockCnt, b.sno as optionSno, a.optionFl, a.stockFl  from es_goods a join es_goodsOption b on a.goodsNo = b.goodsno where b.sno in ( {$goodsOptionStr} ) order by b.sno";
        $stockList = DBUtil2::runSelect($sql);

        $stockMap = [];
        foreach($stockList as $stockData){
            if( 'y' === $stockData['stockFl'] ){
                $stockMap[$stockData['goodsNm']][$stockData['optionValue1']]['stockCnt'] += $stockData['stockCnt'];
            }else{
                $stockMap[$stockData['goodsNm']][$stockData['optionValue1']]['stockCnt'] = 999;
            }
            $stockMap[$stockData['goodsNm']][$stockData['optionValue1']]['orderCnt'] += $goodsOptionOrderMap[$stockData['optionSno']];
        }

        return $stockMap;
    }

    /**
     * 사원별 전체 주문 집계 이력 업데이트
     * @param $companyId
     * @return mixed
     * @throws Exception
     */
    public function saveEmpAllHistory($companyId){
        /*$sql = "
            select 
            LEFT(requestDt,4) as reqYear, prdName, sum(orderCnt) as orderCnt 
            from sl_asianaOrderHistory 
            where companyId = '{$companyId}' group by LEFT(requestDt,4), prdName";*/

        //완전히 새롭게 할 수는 있네 (중간에 추가하는게 아니라).

        $sql = "
            select 
            requestDt, concat(prdName,prdOption) as prdName, prdName as onlyPrdName, orderCnt 
            from sl_asianaOrderHistory 
            where companyId = '{$companyId}' and delFl='n' ";

        $orderList = DBUtil2::runSelect($sql);

        foreach($orderList as $key => $order){
            $order['prdName'] = str_replace('/신','',$order['prdName']);
            $order['prdName'] = str_replace('장화','우화',$order['prdName']);
            $orderList[$key] = $order;
        }

        //$orderList = [];

        return DBUtil2::update('sl_asianaEmployee',[
            'provideInfo' => json_encode($orderList)
        ],new SearchVo('companyId=?', $companyId));
    }

    /**
     * 일괄 주문 신청
     * @param $params
     * @throws Exception
     */
    public function asianaOrder($params){
        $scmNo = 34;
        $order = \App::load(\Component\Order\Order::class);
        $orderNo = $order->generateOrderNo();
        $memNo = \Session::get('member.memNo');

        $orderStatus = 'p1'; //결제완료
        $settleKind = 'gz';
        $now = date('Y-m-d H:i:s');
        $memberData = DBUtil2::getOne(DB_MEMBER, 'memNo', $memNo);

        $param['orderNo'] = $orderNo;
        $param['memNo'] = $memNo;
        $param['orderStatus'] = $orderStatus;
        $param['settleKind'] = $settleKind;
        $param['paymentDt'] = $now;
        $param['regDt'] = $now;

        if( SlCommonUtil::isDev() ){
            $desliverySno = 66143; //테스트 서버 배송번호
        }else{
            $desliverySno = 51038; //운영 서버 배송번호
        }
        $deliveryInfo = DBUtil2::getOneBySearchVo(DB_ORDER_DELIVERY, new SearchVo('sno=?',$desliverySno));
        $deliveryInfo['orderNo']=$orderNo;
        $deliveryInfo['regDt']=$now; //RegDt 자동으로 들어간다.
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

        //$cartList

        //--
        $orderGoodsCalcData = $this->setAsianaOrderGoods($memNo, $orderNo, $orderParam);
        $param['orderGoodsNm'] = $orderGoodsCalcData['orderGoodsNm'];
        $param['orderGoodsNmStandard'] = $orderGoodsCalcData['orderGoodsNmStandard'];
        $param['orderGoodsCnt'] = $orderGoodsCalcData['orderGoodsCnt'];
        //--

        $param['statusPolicy'] = '{"mplus":["s1"],"cplus":["s1"],"mminus":["o1"],"cminus":["o1"],"sminus":["o1"],"mrestore":["c1"],"crestore":["c1"],"srestore":["c1","b4","e5","r3"]}';

        //gd_debug('주문 데이터');
        //gd_debug($param);
        DBUtil2::insert(DB_ORDER, $param);

        //OrderInfo
        $info['orderNo'] = $orderNo;

        $info['orderName'] = $params['orderName']; //주문자
        $info['orderCellPhone'] = $params['orderCellPhone']; //주문자
        //==> 변경사항 있을 경우 업데이트

        if( $params['orderName'] != $memberData['memNm'] || $params['orderCellPhone'] != $memberData['cellPhone'] ){
            $rslt = DBUtil2::update(DB_MEMBER,[
                'memNm' => $params['orderName'],
                'cellPhone' => $params['orderCellPhone'],
            ],new SearchVo('memNo=?', $memberData['memNo']));
        }

        if( empty( ScmAsianaCodeMap::ADDRESS[$memberData['memId']] ) ){
            $addressInfo = ScmAsianaCodeMap::INCHEON; //마스터 OR 이노버
        }else{
            $addressInfo = ScmAsianaCodeMap::ADDRESS[strtoupper(\Session::get('member.memId'))];
        }

        $info['receiverName'] = $addressInfo['name']; //수령자
        $info['receiverPhone'] = $addressInfo['phone']; //연락처
        $info['receiverCellPhone'] = $addressInfo['phone'];
        $info['receiverZonecode'] = $addressInfo['zipCode'];
        $info['receiverAddress'] = $addressInfo['address'];
        $info['receiverAddressSub'] = $addressInfo['addressSub'];

        $info['regDt'] = $now;
        DBUtil2::insert(DB_ORDER_INFO, $info);
        DBUtil2::runSql("update es_order set regDt='{$now}' where orderNo = {$orderNo}");//RegDt Update
        DBUtil2::runSql("update es_orderInfo set regDt='{$now}' where orderNo = {$orderNo}");//RegDt Update

        //승인 대기 처리
        $orderService = SlLoader::cLoad('Order','OrderService');
        $orderService->saveOrderScm($orderNo, []); //주문별 공급사 저장
        $orderService->saveOrderAcct($orderNo,[]);

        //TODO : 주문 Validation 처리 ( 제한 수량 이상 주문 금지 , 마스터 아이디 제외 )

        //아시아나 장바구니 비우기.
        DBUtil2::delete('sl_asianaCart', new SearchVo('memNo=?',$memNo));

        $godoOrder = \App::load(\Component\Order\Order::class);
        $godoOrder->sendOrderInfo('ORDER', 'sms', $orderNo);

    }

    /**
     * 주문 상품 등록
     * @param $memNo
     * @param $orderNo
     * @param $orderParam
     * @return array
     * @throws Exception
     */
    public function setAsianaOrderGoods($memNo, $orderNo, $orderParam){
        $cartList = $this->sql->getCartList([
            'memNo' => $memNo
        ]);

        $mainGoodsData = '';
        $goodsKindMap = [];
        $companyIdList = [];
        $snoList = [];
        foreach($cartList as $cartKey => $cartData){

            if(0 == $cartKey){
                $mainGoodsData = $cartData['goodsNm'];
            }

            $optionData = DBUtil2::getOne(DB_GOODS_OPTION, 'sno', $cartData['optionSno']);
            $goodsNo = $optionData['goodsNo'];

            $saveGoods['mallSno']=1;
            $saveGoods['orderNo']=$orderNo;
            $saveGoods['orderCd']=$cartKey+1;
            $saveGoods['orderStatus']=$orderParam['orderStatus'];
            $saveGoods['orderDeliverySno']=$orderParam['deliveryNo'];
            $saveGoods['scmNo']=$orderParam['scmNo'];
            $saveGoods['goodsNo']=$goodsNo;
            $saveGoods['goodsNm']=$cartData['goodsNm'];
            $saveGoods['goodsNmStandard']=$cartData['goodsNm'];
            $saveGoods['goodsCnt']=$cartData['orderCnt'];

            $saveGoods['optionSno']=$optionData['sno'];
            $saveGoods['optionInfo']='[["사이즈","'.$optionData['optionValue1'].'","'.$optionData['optionCode'].'",0,null]]';
            $saveGoods['paymentDt']=$orderParam['now'];
            $saveGoods['deliveryDt']=$orderParam['now'];
            $saveGoods['deliveryCompleteDt']=$orderParam['now'];
            //$saveGoods['regDt']=$orderParam['now'];
            //gd_debug('상품 데이터');
            //gd_debug($saveGoods);
            $orderGoodsSno = DBUtil2::insert(DB_ORDER_GOODS, $saveGoods); //재고 차감 필요시 수정. (TKE saveOrderGoods 참고)
            $snoList[] = $orderGoodsSno;

            DBUtil2::runSql("update es_orderGoods set regDt='{$orderParam['now']}' where sno = {$orderGoodsSno}");//RegDt Update
            $goodsKindMap[$cartData['goodsNm']] += $cartData['orderCnt'];

            //아시아나 주문 기록  (연결성 확보)
            $asianaOrder = $cartData;
            unset($asianaOrder['sno']);
            $asianaOrder['requestDt'] = date('Ymd');
            $asianaOrder['prdName'] = $cartData['goodsNm'];
            $asianaOrder['prdOption'] = $optionData['optionValue1'];
            $asianaOrder['orderGoodsSno'] = $orderGoodsSno;
            $asianaOrder['optionInfo'] = $saveGoods['optionInfo'];

            if( !empty($cartData['companyId']) ){
                if( empty($companyIdList[$cartData['companyId']]) ){
                    $empInfo = DBUtil2::getOne('sl_asianaEmployee', 'companyId', $cartData['companyId']);
                    $companyIdList[$cartData['companyId']] = $empInfo;
                }else{
                    $empInfo = $companyIdList[$cartData['companyId']];
                }
                $asianaOrder['empTeam'] = $empInfo['empTeam'];
                $asianaOrder['empPart1'] = $empInfo['empPart1'];
                $asianaOrder['empPart2'] = $empInfo['empPart2'];
            }

            //아시아나 주문 이력 등록
            DBUtil2::insert('sl_asianaOrderHistory', $asianaOrder);
        }

        //재고 차감
        $order = SlLoader::cLoad('order','order');
        $order->setGoodsStockCutback($orderNo, $snoList);

        foreach($companyIdList as $companyId => $empInfo){
            $this->saveEmpAllHistory($companyId); //일단 전체 누적?
        }

        $goodsNm = $mainGoodsData;
        if( count($goodsKindMap) > 1 ){
            //바지
            $kindCnt = count($goodsKindMap)-1;
            $goodsNm .= $goodsNm . " 외 {$kindCnt}건";
        }

        $goodsCnt = 0;
        foreach($goodsKindMap as $goodsOrderCnt){
            $goodsCnt += $goodsOrderCnt;
        }

        return [
            'orderGoodsNm' => $goodsNm,
            'orderGoodsNmStandard' => $goodsNm,
            'orderGoodsCnt' => $goodsCnt,
        ];
    }

    public function getAsianaEmpData($companyId){
        $empData = DBUtil2::getOne('sl_asianaEmployee', 'companyId', $companyId);
        $empData['provideInfo'] = array_reverse(json_decode($empData['provideInfo'], true));
        //SitelabLogger::logger2(__METHOD__, $empData['provideInfo']);
        foreach($empData['provideInfo'] as $key => $provideInfo){
            foreach( ScmAsianaCodeMap::CHOICE_GOODS as $choiceGoods){
                if( in_array($provideInfo['onlyPrdName'],$choiceGoods)){
                    $provideInfo['onlyPrdName'] = implode('|', $choiceGoods);
                }
            }
            $empData['provideInfo'][$key] = $provideInfo;
        }
        //SitelabLogger::logger2(__METHOD__, $empData['provideInfo']);
        return $empData;
    }

    public function setUploadController($controller){
        $params = \Request::request()->toArray();
        $files = \Request::files()->toArray();
        if( 'asiana_emp_upload' === $params['mode'] ){
            $fileResult = PhpExcelUtil::readToArray($files, 1);
            $this->updateEmployee($fileResult);
        }
        if( 'asiana_order_upload' === $params['mode'] ){
            //비우고 저장.
            DBUtil2::delete('sl_asianaCart', new SearchVo('memNo=?',\Session::get('member.memNo')));
            $fileResult = PhpExcelUtil::readToArray($files, 2);
            $this->saveCart($fileResult);
        }
    }

    public function saveCart($fileResult){
        $tableName = 'sl_asianaCart';
        $dataMap = [
            1 => 'companyId',
            //2 => 'name',
            3 => 'prdName',
            4 => 'prdOption',
            5 => 'orderCnt',
        ];

        //직원 정보 Map
        $empList = DBUtil2::getList('sl_asianaEmployee','1','1');
        $optionList = DBUtil2::runSelect("select a.goodsNm, b.optionValue1, b.sno as optionSno, b.stockCnt  from es_goods a join es_goodsOption b on a.goodsNo = b.goodsNo where a.scmNo = 34 and a.delFl = 'n'");
        $optionMap = [];
        foreach($optionList as $option){
            $optionMap[$option['goodsNm'].$option['optionValue1']] = $option;
        }
        $empMap = SlCommonUtil::arrayAppKey($empList, 'companyId');

        $cartDatas = [];
        foreach($fileResult as $index => $val){
            if( 0 >= $index ) continue;
            $data = [];
            foreach($dataMap as $dataKey => $dataName){
                $data[$dataName] = trim($val[$dataKey]);
            }

            if( empty($data['companyId']) || empty($data['prdName']) ) continue;

            $data['memNo'] = \Session::get('member.memNo');
            if(empty($data['orderCnt'])) $data['orderCnt']=1;

            //이름 찾기
            $data['name'] = $empMap[$data['companyId']]['empName'];

            //옵션찾기
            $prdName = $data['prdName'];
            $prdOption = strtoupper($data['prdOption']);
            $prdKey = $prdName.$prdOption;
            $optionSno = $optionMap[$prdKey]['optionSno'];
            $data['optionSno'] = $optionSno;
            $cartDatas[] = $data;
        }

        //최종 Validation 후 저장
        foreach($cartDatas as $each){
            $prdKey = $each['prdName'].strtoupper($each['prdOption']);
            $this->cartValidation($each, $optionMap[$prdKey]);
            DBUtil2::insert($tableName, $each);
        }

    }

    /**
     * 카드 데이터 Validation .
     * @param $data
     * @param $prdInfo (재고 체크를 위함)
     */
    public function cartValidation(&$data, $prdInfo){
        $errMsg = [];
        //이름
        if(empty($data['name'])){
            $errMsg[] = '사원정보없음';
        }
        //상품
        if(empty($data['optionSno'])){
            $errMsg[] = '상품정보입력오류';
        }

        if(count($errMsg)>0){
            $data['isValid']=1;
            $data['isValidMsg']=implode(',', $errMsg);
        }
    }


    public function updateEmployee($fileResult){
        $tableName = 'sl_asianaEmployee';
        $dataMap = [
            2 => 'companyId',
            3 => 'empName',
            4 => 'empRank',
            5 => 'empTeam',
            6 => 'empPart1',
            7 => 'empPart2',
        ];
        //직원등록 (누적)
        $cnt = 0;
        foreach($fileResult as $index => $val){
            $data = [];
            foreach($dataMap as $dataKey => $dataName){
                $data[$dataName] = trim($val[$dataKey]);
            }

            if(empty($data['companyId']) && !is_numeric($data['companyId']) ) continue;

            $empInfo = DBUtil2::getOne($tableName, 'companyId', $data['companyId']);
            if( empty($empInfo) ){
                DBUtil2::insert($tableName, $data);
            }else{
                $id = $data['companyId'];
                unset($data['companyId']);
                DBUtil2::update($tableName, $data, new SearchVo('companyId=?',$id)); //나머지 데이터는 업데이트
            }
            $cnt++;
        }

        //간단히 메일 보내기.
        SiteLabMailUtil::sendSystemMail('아시아나 회원 정보 업데이트('.$cnt.')', '확인해보시기 바랍니다.', implode(',',ImsCodeMap::PRIVATE_MALL_MANAGER_MAIL));

    }

    /**
     * 아시아나 취소 주문 이력 갱신
     * @param $orderNo
     * @throws Exception
     */
    public function cancelOrderRefine($orderNo){
        $sql = "
select b.companyId, b.orderGoodsSno 
from es_orderGoods a join sl_asianaOrderHistory b on a.sno = b.orderGoodsSno where orderNo='{$orderNo}' and a.scmNo=34"; //아시아나에만 적용
        $refreshCompanyIdList = [];
        $list = DBUtil2::runSelect($sql);
        foreach( $list as $orderGoods ){
            $refreshCompanyIdList[] = $orderGoods['companyId'];
            DBUtil2::delete('sl_asianaOrderHistory', new SearchVo('orderGoodsSno=?', $orderGoods['orderGoodsSno']));
        }
        foreach( $refreshCompanyIdList as $companyId ){
            $this->saveEmpAllHistory($companyId);
        }
    }

    /**
     * 아시아나 배송 처리
     */
    public static function procAsianaDelivery(){
        DBUtil2::runSql("update es_order set orderStatus = 'd2' where orderNo in (select distinct orderNo from es_orderGoods where orderStatus = 'g1' and scmNo = 34)");
        DBUtil2::runSql("update es_orderGoods  set orderStatus = 'd2' , deliveryDt = now(), deliveryCompleteDt = now() where orderStatus = 'g1' and scmNo = 34");
    }

    /**
     * 아시아나 출고 대기 수량
     * @return mixed
     */
    public static function getDeliveryWaitCount(){
        return DBUtil2::runSelect("select sum(goodsCnt) as cnt from es_orderGoods  where orderStatus = 'g1' and scmNo = 34")[0]['cnt'];
    }


}
