<?php
namespace Controller\Front\Partner\ControllerService;

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
 * 입출고 리스트
 * Class GoodsStock
 * @package Component\Goods
 */
class ThreePlOrderListService implements ListInterface {

    use SlCommonTrait;

    private $sql;
    private $search;

    const LIST_TITLES = [
        '#'
        ,'출고일자'
        ,'품목코드'
        ,'품목명'
        ,'수량'  
        ,'주문자'
        ,'주소'
        ,'주문번호'
        ,'송장번호'
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
                'a.productCode' => '상품코드',
                'a.orderNo' => '주문번호',
            ],
            'combineTreatDate' => [
                'a.orderDt' => '출고일자',
            ],
            'sortList' => [
                'a.orderDt desc' => __('출고 일자 ↑'),
                'a.orderDt asc' => __('출고 일자 ↓'),
                'a.customerName desc' => __('고객명 ↑'),
                'a.customerName asc' => __('고객명 ↓'),
                'a.productName desc' => __('제품명 ↑'),
                'a.productName asc' => __('제품명 ↓'),
                'a.productCode desc' => __('제품코드 ↑'),
                'a.productCode asc' => __('제품코드 ↓'),
            ],
            'sort' => gd_isset( $searchData['sort'] ,'a.orderNo desc' ),
            'page' => gd_isset( $searchData['page'] ,1),
            'pageNum' => gd_isset( $searchData['pageNum'] ,100),
        ];

        //변경.
        $searchData['treatDateFl'] = 'a.orderDt';

        ListUtil::setSearch($this, $setParam);

        // 기본 텍스트 검색 설정
        $this->setSearchData([
            'key'
            ,'keyword'
        ],$searchData);

        //날짜 기간 설정
        $searchData = $this->setDefaultSearchDate($searchData, 0);
        $this->setRangeDate($searchData['treatDateFl'],  $searchData['treatDate'][0], $searchData['treatDate'][1]);
    }

    /**
     * 거래처 리스트
     * @param string  $searchData   검색 데이타
     *
     * @return array 주문 리스트 정보
     */
    public function getList($searchData): array {
        $data = $this->getTraitList($searchData, 'getList');
        $data['data'] = SlCommonUtil::setEachData($data['data'], $this, 'setRefineEachListData', $data);
        $data['totalOutCount'] = $this->sql->getListSummaryData($data['search'])[0]['totalOutCount'];
        return $data;
    }

    public function setRefineEachListData($each, $key, &$mixData){
        $each['no'] = $mixData['page']->idx--;
        return $each;
    }


}
