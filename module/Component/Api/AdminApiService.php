<?php
namespace Component\Api;

use App;
use Component\Mall\Mall;
use Component\Mall\MallDAO;
use Component\Member\Util\MemberUtil;
use Component\Sitelab\MallConfig;
use Exception;
use Framework\Utility\NumberUtils;
use Request;
use Session;
use SlComponent\Database\DBConst;
use SlComponent\Database\DBUtil;
use SlComponent\Database\DBUtil2;
use SlComponent\Database\SearchVo;
use SlComponent\Database\TableVo;
use SlComponent\Util\CustomApiUtil;
use SlComponent\Util\LogTrait;
use SlComponent\Util\SitelabLogger;
use SlComponent\Util\SlCodeMap;
use SlComponent\Util\SlKakaoUtil;
use SlComponent\Util\SlLoader;
use Component\Coupon\Coupon;

/**
 * 관리자 AJAX 서비스 (필요한 부분 모아두었다가 종류별로 많아지면 분리)
 * Class SlCode
 * @package SlComponent\Util
 */
class AdminApiService {
    private $sql;

    public function __construct(){
        $this->sql = App::load(AdminApiSql::class);
    }

    // --- 재입고 알림 시작
    /**
     * 요청 응답 알림톡.
     * @param $params
     * @return array
     * @throws Exception
     */
    public function sendAlarm($params){
        return ['data'=>$params,'msg'=>'저장되었습니다.'];
    }

    public function getScmCategory($params){
        $scmData = DBUtil2::getOne('sl_setScmConfig', 'scmNo', $params['scmNo']);
        $cateCd = $scmData['cateCd'];
        $categoryList = DBUtil2::getListBySearchVo(DB_CATEGORY_GOODS, new SearchVo( ["cateCd like concat(?,'%')", 'cateCd != ?'] , [$cateCd, $cateCd] ));
        return ['data'=>$categoryList,'msg'=>'조회 완료'];
    }

    // --- 재입고 알림 끝


    /**
     * 상품 기준 알림 발송.
     * @param $params
     * @return array
     * @throws Exception
     */
    public function sendSoldOutRequestList($params){
        foreach($params['goodsNoList'] as $goodsNo){
            //미전송 리스트.
            $searchVo = new SearchVo(['a.goodsNo=?','a.sendType = ?'], [$goodsNo,0]);
            $this->sendSoldOutRequestListBySearchVo($searchVo, $params['category']);
        }
        return ['data'=>$params,'msg'=>'발송 되었습니다.'];
    }

    /**
     * 요청 기준 알림 발송
     * @param $params
     * @return array
     * @throws Exception
     */
    public function sendSoldOutRequestEach($params){
        $searchVo = new SearchVo('a.sendType = ?',0);
        $searchVo->setWhere(DBUtil::bind('sno', DBUtil::IN, count($params['reqSnoList']) ));
        $searchVo->setWhereValueArray( $params['reqSnoList'] );
        $this->sendSoldOutRequestListBySearchVo($searchVo, $params['category']);
        return ['data'=>$params,'msg'=>'발송 되었습니다.'];
    }

    /**
     * 알림 요청 삭제
     * @param $params
     * @return array
     * @throws Exception
     */
    public function delSoldOutRequest($params){
        $searchVo = new SearchVo('sendType = ?',0);
        $searchVo->setWhere(DBUtil2::bind('sno', DBUtil2::IN, count($params['reqSnoList']) ));
        $searchVo->setWhereValueArray( $params['reqSnoList'] );
        DBUtil2::delete('sl_soldOutReqList', $searchVo);
        return ['data'=>$params,'msg'=>'삭제 되었습니다.'];
    }

    /**
     * 검색 조건으로 찾아 재입고 알림 발송
     * @param $searchVo
     * @param $category
     * @throws Exception
     */
    public function sendSoldOutRequestListBySearchVo($searchVo, $category){
        $tableInfo = DBUtil2::setTableInfo([
            'a' => //요청리스트
                [
                    'data' => [ 'sl_soldOutReqList' ]
                    , 'field' => ['*']
                ],
            'b' => //상품
                [
                    'data' => [ DB_GOODS, 'JOIN', 'a.goodsNo = b.goodsNo' ]
                    , 'field' => ['goodsNm']
                ]
        ]);
        $list = DBUtil2::getComplexList($tableInfo,$searchVo);
        foreach( $list as $reqData ){
            $reqData['category'] = $category;
            $this->sendSoldOutRequest($reqData);
        }
    }

    /**
     * 재입고 알림 발송
     * @param $sendData
     * @throws Exception
     */
    public function sendSoldOutRequest($sendData){
        $defaultInfo = gd_policy('basic.info');
        if(!empty(SlCodeMap::OTHER_SKIN_MAP[$sendData['scmNo']])){
            $domain = 'http://'.SlCodeMap::OTHER_SKIN_MAP[$sendData['scmNo']];
        }else{
            $domain = 'http://'.$defaultInfo['mallDomain'];
        }
        if(!empty($sendData['category'])){
            $param['btnUrl'] = "{$domain}/goods/goods_list.php?cateCd={$sendData['category']}";
        }else{
            $param['btnUrl'] = "{$domain}";
        }

        $param['goodsName'] = $sendData['goodsNm'];
        $param['reqName'] = $sendData['reqName'];
        $param['shopUrl'] = $domain;

        SlKakaoUtil::send(15 , str_replace('-','',$sendData['cellPhone']),  $param);

        $reqSno = $sendData['sno'];
        $updateCondition = new SearchVo('sno=?', $reqSno);
        $updateData = [
            'sendType' => 2,
            'sendDt' => 'now()'
        ];

        DBUtil2::update('sl_soldOutReqList',$updateData, $updateCondition);
        $optionUpdateCondition = new SearchVo('reqSno=?',$reqSno);
        DBUtil2::update('sl_soldOutReqOptionList',$updateData, $optionUpdateCondition);
    }


    /**
     * 로그인 시도 초기화
     * @return array
     */
    public function resetLoginTry(){
        DBUtil2::runSql('truncate table es_logIpLoginTry');
        return ['data'=>$params,'msg'=>'처리 되었습니다.'];
    }

    public function setCashRefine($params){
        $orderData = DBUtil2::getOne(DB_ORDER, 'orderNo', $params['orderNo']);
        $updateData = [
            'supplyPrice' => $orderData['taxSupplyPrice'],
            'taxPrice' => $orderData['taxVatPrice'],
        ];
        $rslt = DBUtil2::update('es_orderCashReceipt', $updateData, new SearchVo('orderNo=?', $params['orderNo']));
    }

    /**
     * @param $params
     * @return array
     * @throws Exception
     */
    public function updateMemberBuyLimit($params){
        $searchVo = new SearchVo('memNo=?', $params['sno']);
        $beforeData = DBUtil2::getOneBySearchVo('sl_setMemberConfig', $searchVo);
        if($beforeData['buyLimitCount'] != $params['buyLimitCount']){
            DBUtil2::update('sl_setMemberConfig',[
                'buyLimitCount'=>$params['buyLimitCount']
            ], $searchVo);
        }
        return ['data'=>$params,'msg'=>'저장되었습니다.'];
    }

}
