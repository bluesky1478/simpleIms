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
class StockStatusService implements ListInterface {

    use SlCommonTrait;

    private $sql;
    private $search;

    const LIST_TITLES = [
        '고객사'   //scmName
        ,'품목명'  //productName
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
                'a.productName' => '상품명',
                'b.scmName' => '고객사',
            ],
            //'combineTreatDate' => [],
            //'sortList' => [],
            //'sort' => gd_isset( $searchData['sort'] ,'a.inOutDate desc' ),
            //'page' => gd_isset( $searchData['page'] ,1),
            //'pageNum' => gd_isset( $searchData['pageNum'] ,100),
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
        ],$searchData);

        $this->setRadioSearch([
            'scmFl'
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

        $masterList = [];

        foreach($data['data'] as $each){
            $masterList[md5($each['productName'])][$each['optionName']] = $each;
        }
        foreach( $masterList as $key => $each ){
            usort($each, function($a, $b) {
                return strcmp($a['thirdPartyProductCode'], $b['thirdPartyProductCode']);
            });
            $each = SlCommonUtil::arrayAppKey($each, 'optionName');
            $masterList[$key] = $each;
        }

        $productList = [];
        foreach($masterList as $key => $value){
            $keyList = array_keys($value);
            $productData = SlCommonUtil::getAvailData($value[$keyList[0]],['scmName','productName']);

            $optionList = array_flip(array_keys($value));
            foreach($optionList as $optionKey => $optionValue){
                $optionList[$optionKey] = 0; //초기화.
            }

            $productData['optionList'] = $optionList;

            $productData['optionKey'] = md5(implode('', $keyList));
            $productList[] = $productData;
        }

        $totalCnt = 0;
        $totalStockCnt = 0;
        $optionMaxCount = 0;
        $productListOptionType = [];
        foreach($productList as $key => $value){
            $productKey = md5($value['productName']);
            $productStockCnt = 0;
            foreach($value['optionList'] as $optionKey => $optionValue){
                //gd_debug($optionValue);
                $stockCnt = $masterList[$productKey][$optionKey]['stockCnt'];
                $value['optionList'][$optionKey] += $stockCnt; //상품+옵션 별
                $productStockCnt += $stockCnt;
            }

            $value['productStockCnt'] = $productStockCnt;

            //$productList[$key] = $value;
            $productListOptionType[$value['optionKey']]['optionList'] = array_keys($value['optionList']);
            $productListOptionType[$value['optionKey']]['data'][] = $value;
            $productListOptionType[$value['optionKey']]['optionTotalStockCnt'] += $productStockCnt;

            if( count($value['optionList']) > $optionMaxCount ){
                $optionMaxCount = count($value['optionList']);
            }

            $totalStockCnt+=$productStockCnt;
            $totalCnt++;
        }

        //gd_debug($productListOptionType);

        $data['data'] = $productListOptionType;
        $data['page']['optionMaxCount'] = $optionMaxCount;
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
