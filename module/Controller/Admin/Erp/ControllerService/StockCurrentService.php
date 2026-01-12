<?php
namespace Controller\Admin\Erp\ControllerService;

use App;
use Component\Erp\ErpCodeMap;
use Component\Member\Manager;
use Component\Storage\Storage;
use Component\Work\WorkCodeMap;
use Framework\Debug\Exception\AlertBackException;
use Framework\Utility\DateTimeUtils;
use LogHandler;
use Request;
use Session;
use SiteLabUtil\SlCommonUtil;
use SlComponent\Database\DBConst;
use SlComponent\Database\DBUtil;
use SlComponent\Database\DBUtil2;
use SlComponent\Database\SearchVo;
use SlComponent\Godo\ListInterface;
use SlComponent\Util\ListUtil;
use SlComponent\Util\SitelabLogger;
use SlComponent\Util\SlCodeMap;
use SlComponent\Util\SlCommonTrait;
use SlComponent\Util\SlLoader;

/**
 * 재고현황
 * Class GoodsStock
 * @package Component\Goods
 */
class StockCurrentService implements ListInterface {

    use SlCommonTrait;

    private $sql;
    private $search;

    const LIST_TITLES = [
        '고객사명',
        '품목코드',
        '품목명',
        '옵션',
        '속성1(분류)',
        '속성2(시즌)',
        '속성3(타입)',
        '속성4(색상)',
        '속성5(년도)',
        '실제수량',
        '판매수량',
        '출고예약',
        '차이',
        '판매상품 보기',
        /*'사용여부'*/
    ];

    public function __construct(){
        $this->sql =  SlLoader::sqlLoad(__CLASS__);
    }
    public function getSearch()
    {
        return $this->search;
    }
    public function setSearch($search)
    {
        $this->search = $search;
    }

    /**
     * 리스트 타이틀 목록 반환
     * @param $searchData
     * @return string[]
     */
    public function getTitle($searchData): array
    {
        return self::LIST_TITLES;
    }

    /**
     * 검색 데이터 설정
     * @param $searchData
     */
    public function _setSearch($searchData){
        SlCommonUtil::refineTrimData($searchData);
        $setParam = [
            'combineSearch' => [
                'a.productName' => '품목명',
                'a.thirdPartyProductCode' => '품목코드',
            ],
        ];

        //변경.
        //$searchData['treatDateFl'] = 'a.inOutDate';

        ListUtil::setSearch($this, $setParam);

        // 기본 텍스트 검색 설정
        $this->setSearchData([
            'key'
            ,'keyword'
            ,'scmNo'
            ,'scmNoNm'
            ,'isErrorStockFl'
            ,'statTypeFl'
            ,'attr1'
            ,'attr2'
            ,'attr3'
            ,'attr4'
            ,'attr5'
            ,'optionName'
        ],$searchData);

        $this->setRadioSearch([
            'scmFl',
            'isErrorStockFl',
            'statTypeFl',
        ],$searchData,'all');
        $this->setRadioSearch([],$searchData,'');
        $this->setCheckSearch([]);

        if ($searchData['scmNo'] == 0 && $searchData['scmFl'] == 1) {
            $this->search['scmFl'] = 'all';
        }

        //날짜 기간 설정
        //$searchData = $this->setDefaultSearchDate($searchData, 364);
        //$this->setRangeDate($searchData['treatDateFl'],  $searchData['treatDate'][0], $searchData['treatDate'][1]);
    }

    /**
     * 재고 현황
     * @param string  $searchData   검색 데이타
     * @return array 주문 리스트 정보
     */
    public function getList($searchData): array {
        $data = $this->getTraitList($searchData, 'getList');
        $totalStockCnt = $totalCnt = 0;

        foreach($data['data'] as $eachKey => $each){
            $totalStockCnt += $each['stockCnt'];
            $totalCnt++;

            $goodsSql = "select b.sno as optionSno, a.goodsNo, a.goodsNm, optionValue1,optionValue2,optionValue3,optionValue4,optionValue5,b.stockCnt, a.goodsDisplayFl, a.goodsSellFl   from es_goods a join es_goodsOption b on a.goodsNo = b.goodsNo where a.delFl = 'n' and optionCode = '{$each['thirdPartyProductCode']}' ";
            if( 1 == $searchData['statTypeFl'] ){
                $goodsSql .= " AND a.goodsSellFl = 'y' ";
            }else if( 2 == $searchData['statTypeFl'] ){
                $goodsSql .= " AND a.goodsDisplayFl = 'y' ";
            }else if( 3 == $searchData['statTypeFl'] ){
                $goodsSql .= " AND a.goodsSellFl = 'y' ";
                $goodsSql .= " AND a.goodsDisplayFl = 'y' ";
            }

            $saleGoodsList = DBUtil2::runSelect($goodsSql);
            if(!empty($saleGoodsList)){
                foreach($saleGoodsList as $saleGoods){
                    $option = [];
                    for($i=1; 5>=$i; $i++){
                        if( !empty($saleGoods['optionValue'.$i]) ) $option[] = $saleGoods['optionValue'.$i];
                    }
                    $saleGoods['optionName'] = implode('/', $option).'<span class="text-muted">('.$saleGoods['optionSno'].')</span>';
                    $each['saleGoodsList'][] = $saleGoods;
                }
            }

            $each['isStockErrorClass'] = $each['saleCnt'] > $each['stockCnt'] ? 'text-danger':'';

            $data['data'][$eachKey] = $each; //추가 정보 넣어서 반환
        }

        $data['page']['totalStockCnt'] = $totalStockCnt;
        $data['page']['totalCnt'] = $totalCnt;

        return $data;
    }

    /**
     * 리스트 데이터 Refine.
     * @param $each
     * @param $key
     * @param $mixData
     * @return mixed
     */
    public function setRefineEachListData($each, $key, &$mixData){
        return $each;
    }

}
