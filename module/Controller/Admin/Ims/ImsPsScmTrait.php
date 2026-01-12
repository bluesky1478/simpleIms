<?php


namespace Controller\Admin\Ims;

use Component\Ims\ImsDBName;
use Component\Ims\NkCodeMap;
use Component\Scm\ScmAsianaService;
use SiteLabUtil\SlCommonUtil;
use SlComponent\Database\DBUtil2;
use SlComponent\Database\SearchVo;
use SlComponent\Util\SlLoader;

trait ImsPsScmTrait
{

    /**
     * 재고 리스트 반환
     * @param $params
     * @return string[]
     */
    public function getGoodsStockTotalInfo($params) {
        $stockService = SlLoader::cLoad('imsv2','ImsStockService');
        return ['msg'=>'조회 완료','data'=>$stockService->getGoodsStockTotalInfo($params)];
    }

    /**
     * 미연결 재고 리스트 반환
     * @param $params
     * @return array
     */
    public function getGoodsStockUnlink($params){
        $stockService = SlLoader::cLoad('imsv2','ImsStockService');
        return ['msg'=>'조회 완료','data'=>$stockService->getGoodsStockUnlink($params)];
    }

    /**
     * 상품 재고 상세 정보
     * @param $params
     * @return array
     */
    public function getGoodsStockTotalInfoDetail($params) {
        $stockService = SlLoader::cLoad('imsv2','ImsStockService');
        return ['msg'=>'조회 완료','data'=>$stockService->getGoodsStockTotalInfoDetail($params)];
    }

    /**
     * 폐쇄몰 상품/3PL 코드 연결 끊기
     * @param $params
     * @return array
     */
    public function goods3plUnlink($params) {
        $stockService = SlLoader::cLoad('imsv2','ImsStockService');
        return ['msg'=>'처리 완료','data'=>$stockService->goods3plUnlink($params)];
    }

    /**
     * 상품 및 3pl 연결 정보 저장
     * @param $params
     * @return array
     */
    public function saveGoods3plProduct($params) {
        $stockService = SlLoader::cLoad('imsv2','ImsStockService');
        return ['msg'=>'처리 완료','data'=>$stockService->saveGoods3plProduct($params)];
    }

    /**
     * 창고 코드 연결
     * @param $params
     * @return array
     */
    public function link3plCode($params) {
        $stockService = SlLoader::cLoad('imsv2','ImsStockService');
        return ['msg'=>'처리 완료','data'=>$stockService->link3plCode($params)];
    }

    /**
     * 폐쇄몰 상품 카테고리 가져오기
     * @param $params
     * @return array
     */
    public function getGoodsCate($params) {
        $stockService = SlLoader::cLoad('imsv2','ImsStockService');
        return ['msg'=>'조회 완료','data'=>$stockService->getGoodsCate($params)];
    }

    /**
     * 입출고 이력 반환
     * @param $params
     * @return array
     */
    public function getStockInOutList($params) {
        $service = SlLoader::cLoad('imsv2\\Lst','StockInOutList');
        return ['msg'=>'조회 완료','data'=>$service->getList($params)];
    }

    /**
     * 예약 이력 반환
     * @param $params
     * @return array
     */
    public function getReservedList($params) {
        $stockService = SlLoader::cLoad('imsv2','ImsStockService');
        return ['msg'=>'조회 완료','data'=>$stockService->getReservedList($params)];
    }

    /**
     * 재고 업데이트 최신 데이터 가져오기
     * @param $params
     * @return array
     */
    public function getLatestUpdateDate($params) {
        $latestData = DBUtil2::runSelect("select max(modDt) as maxModDt from sl_3plProduct limit 1")[0]['maxModDt'];
        return ['msg'=>'조회 완료','data'=>$latestData];
    }

    /**
     * 재고 리포트 가져오기
     * @param $params
     * @return array
     */
    public function getStockReport($params) {
        $stockService = SlLoader::cLoad('imsv2','ImsStockService');
        $list['mainReport'] = $stockService->getReport();
        return ['msg'=>'조회 완료','data'=>$list];
    }

    /**
     * 재고 관리 코멘트 등록
     * @param $params
     * @return array
     */
    public function saveStockComment($params) {
        $stockService = SlLoader::cLoad('imsv2','ImsStockService');
        $stockService->saveStockComment($params);
        return ['msg'=>'처리 완료','data'=>$params];
    }

    /**
     * 업체별 재고 관리 코멘트 가져오기
     * @param $params
     * @return array
     */
    public function getStockComment($params) {
        $stockService = SlLoader::cLoad('imsv2','ImsStockService');
        return ['msg'=>' 완료','data'=>$stockService->getStockComment($params)];
    }

    /**
     *
     * @param $params
     * @return array
     * @throws \Exception
     */
    public function delStockComment($params) {
        DBUtil2::delete('sl_stockReportComment', new SearchVo('sno=?',$params['sno']));
        return ['msg'=>' 완료','data'=>$params];
    }

    /**
     * 입고이력 등록
     * @param $params
     * @return array
     * @throws \Exception
     */
    public function saveInputHistory($params) {
        foreach($params['addCode'] as $each){
            $insertData = [
                'memo' => $params['addContents'],
                'managerSno' => SlCommonUtil::getManagerSno(),
                'inOutDate' => $params['addDt'],
                'inOutType' => 1,
                'inOutReason' => 1,
            ];
            //상품 정보 가져오기
            $prdInfo = DBUtil2::getOne('sl_3plProduct','thirdPartyProductCode',$each['code']);
            $insertData['productSno'] = $prdInfo['sno'];
            $insertData['thirdPartyProductCode'] = $each['code'];
            $insertData['quantity'] = $each['qty'];
            DBUtil2::insert('sl_3plStockInOut', $insertData);
        }
        $service = SlLoader::cLoad('godo','sopService','sl');
        $service->summarizeStock();
        return ['msg'=>' 완료','data'=>$params];
    }

    /**
     * 아시아나 배송처리
     * @param $params
     * @return array
     */
    public function procAsianaDelivery($params){
        ScmAsianaService::procAsianaDelivery();
        return ['msg'=>'완료','data'=>$params];
    }

}