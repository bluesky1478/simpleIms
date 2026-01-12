<?php
namespace Component\Goods;

use App;
use Component\Database\DBTableField;
use LogHandler;
use Request;
use SiteLabUtil\SlCommonUtil;
use SlComponent\Database\DBUtil;
use SlComponent\Database\DBUtil2;
use SlComponent\Database\SearchVo;
use SlComponent\Util\SitelabLogger;
use SlComponent\Util\SlCode;
use SlComponent\Util\SlCodeMap;
use SlComponent\Util\SlCommonTrait;
use SlComponent\Util\SlLoader;

class GoodsAdmin extends \Bundle\Component\Goods\GoodsAdmin{

    use SlCommonTrait;
    public $goodsStockService = null;

    /**
     * 생성자
     * GoodsAdmin constructor.
     */
    public function __construct(){
        parent::__construct();
        $this->goodsStockService = \App::load(\Component\Goods\GoodsStock::class);
    }

    /**
     * 상품정보 저장
     * @param $arrData 저장할 정보의 배열
     * @return mixed
     */
    public function saveInfoGoods($arrData){
        //신규가 아니면 (수정이라면 이전 재고 정보 가져오기)
        if ($arrData['mode'] === 'register') {
            $saveInfo = parent::saveInfoGoods($arrData);
            $this->goodsStockService->addStockInfoByGoodsNo($this->goodsNo);
        }else{
            //기존 재고 이력 가져오기
            $beforeGoodsOptionList = $this->goodsStockService->getGoodsOptionInfoAndStockCheck($arrData['goodsNo']);
            //상품정보 수정 처리
            $saveInfo = parent::saveInfoGoods($arrData);
            //재고 이력 저장
            $updateInfo['beforeGoodsOptionList'] = $beforeGoodsOptionList;
            $updateInfo['addReason'] = '2'; //관리자 수정 입고
            $updateInfo['minusReason'] = '5';  //관리자 수정 출고
            $this->goodsStockService->addUpdateStockInfoByGoodsNo($arrData['goodsNo'],$updateInfo);
        }
        return $saveInfo;
    }

    /**
     * 상품복사 및 재고 신규 등록
     * @param $goodsNo
     * @return mixed
     * @throws \Exception
     */
    public function setCopyGoods($goodsNo){
        $newGoodsNo = parent::setCopyGoods($goodsNo);
        //복사 상품 재고 초기화
        DBUtil2::update(DB_GOODS_OPTION, ['stockCnt'=>0], new SearchVo('goodsNo=?', $newGoodsNo));
        DBUtil2::update(DB_GOODS, ['totalStock'=>0], new SearchVo('goodsNo=?', $newGoodsNo));
        //$this->goodsStockService->addStockInfoByGoodsNo($newGoodsNo);
        return $newGoodsNo;
    }

    /**
     * 재고관리 기능을 이용한 재고 수정
     * @param $getData
     * @return mixed
     */
    public function setBatchStock($getData){
        $goodsNoList = array();
        $beforeGoodsOptionList = array();

        //상품번호 정제
        foreach($getData['arrGoodsNo'] as $key => $goodsNoAndOptionSno){
            $goodsNo = explode('_',$goodsNoAndOptionSno)[0];
            $goodsNoList[$goodsNo] = $goodsNo;
        }

        //기존 재고 이력 가져오기
        foreach($goodsNoList as $goodsNoListKey => $goodsNo){
            $beforeGoodsOptionList[$goodsNo] = $this->goodsStockService->getGoodsOptionInfoAndStockCheck($goodsNo);
        }

        //상품정보 수정 처리
        $batchStockResult = parent::setBatchStock($getData);

        //재고 이력 저장
        $updateInfo['addReason'] = '10'; //재고관리 입고
        $updateInfo['minusReason'] = '11';  //재고관리 출고
        foreach($goodsNoList as $goodsNoListKey => $goodsNo){
            $updateInfo['beforeGoodsOptionList'] = $beforeGoodsOptionList[$goodsNo];
            $this->goodsStockService->addUpdateStockInfoByGoodsNo($goodsNo,$updateInfo);
        }

        return $batchStockResult;
    }

    /**
     * 결제 상품 생성
     * @param $goodsNm
     * @param $goodsPrice
     * @return mixed
     * @throws \Exception
     */
    public function createPaymentsGoods($goodsNm, $goodsPrice){
        //SitelabLogger::logger('Create 01 : ' . $goodsNm . ' // ' . $goodsPrice);
        if ( SlCommonUtil::isDev() ){
            $goodsNo = SlCodeMap::PRIVATE_PAYMENT_GOODS_DEV;
        }else{
            $goodsNo = SlCodeMap::PRIVATE_PAYMENT_GOODS;
        }
        //SitelabLogger::logger('Create 02');
        $newGoodsNo = $this->setCopyGoods($goodsNo);
        //SitelabLogger::logger('Create 03 (newGoodsNo): ' . $newGoodsNo);
        $updateData['goodsNm'] = $goodsNm;
        $updateData['goodsPrice'] = $goodsPrice;
        $updateData['fixedPrice'] = $goodsPrice;
        $updateData['costPrice'] = $goodsPrice;

        //SitelabLogger::logger('Create 03 (newGoodsNo): ' . $newGoodsNo);
        //SitelabLogger::logger($updateData);

        $result1 = DBUtil2::update(DB_GOODS, $updateData, new SearchVo(['goodsNo=?'], [$newGoodsNo]));
        $result2 = DBUtil2::update(DB_GOODS_SEARCH, $updateData, new SearchVo(['goodsNo=?'], [$newGoodsNo]));
        /*SitelabLogger::logger('상품 업데이트 결과');
        SitelabLogger::logger($result1);
        SitelabLogger::logger($result2);*/
        return $newGoodsNo;
    }

    public function setSearchGoods($getValue = null, $list_type = null){

        if( is_array($getValue['key']) ){
            //다중검색
            $orgKey = $getValue['key'];
            $orgKeyword = $getValue['keyword'];
            unset($getValue['key']);
            unset($getValue['keyword']);

            parent::setSearchGoods($getValue, $list_type);
            $this->search['combineSearch']['goodsNoIn'] = '상품코드리스트';
            $orgGetValue = Request::get()->toArray();
            $fieldTypeGoods = DBTableField::getFieldTypes('tableGoods');

            $conditionList = [];

            $prefixMap = [
                'companyNm' => 's',
                'purchaseNm' => 'p',
                'factoryName' => 'sft',
            ];

            foreach( $orgGetValue['key'] as $searchIdx => $searchKey ) {
                if( 'goodsNoIn' == $searchKey ){
                    $goodsNoList = str_replace(' ',',',$orgGetValue['keyword'][$searchIdx]);
                    $conditionList[] = "g.goodsNo IN ( {$goodsNoList} )";
                }else if( !empty($orgGetValue['keyword'][$searchIdx])  ){
                    $prefix = 'g';
                    $fieldType = 's';
                    $mappingPrefix = $prefixMap[$searchKey];
                    if ( !empty( $mappingPrefix ) ) {
                        $prefix = $mappingPrefix;
                    }else{
                        $fieldType = $fieldTypeGoods[$searchKey];
                    }
                    $conditionList[] = $prefix . '.' . $searchKey . ' LIKE concat(\'%\',?,\'%\')';
                    $this->db->bind_param_push($this->arrBind, $fieldType, $orgGetValue['keyword'][$searchIdx]);
                }
            }

            if(!empty($conditionList)){
                $this->arrWhere[] = ' ( '  . implode(' AND ', $conditionList) . ' ) ';
            }
            $this->search['key'] = $orgKey;
            $this->search['keyword'] = $orgKeyword;
        }else{
            parent::setSearchGoods($getValue, $list_type);
            $this->search['combineSearch']['goodsNoIn'] = '상품코드리스트';
        }

    }

    public function getAdminListGoods($mode = null, $pageNum = 5){
        if ( SlCommonUtil::isDev() ){
            $cateCd = SlCodeMap::PRIVATE_PAYMENT_CATEGORY_DEV;
        }else{
            $cateCd = SlCodeMap::PRIVATE_PAYMENT_CATEGORY;
        }
        $this->arrWhere[] = " left(g.cateCd,3) <> '{$cateCd}' ";
        return parent::getAdminListGoods($mode, $pageNum);
    }

    public function getAdminListOptionBatch($mode = null){
        if ( SlCommonUtil::isDev() ){
            $cateCd = SlCodeMap::PRIVATE_PAYMENT_CATEGORY_DEV;
        }else{
            $cateCd = SlCodeMap::PRIVATE_PAYMENT_CATEGORY;
        }
        $this->arrWhere[] = " left(g.cateCd,3) <> '{$cateCd}' ";
        return parent::getAdminListOptionBatch($mode);
    }


}
