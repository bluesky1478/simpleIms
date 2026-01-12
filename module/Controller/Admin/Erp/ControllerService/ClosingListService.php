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
 * 입출고 리스트
 * Class GoodsStock
 * @package Component\Goods
 */
class ClosingListService implements ListInterface {

    use SlCommonTrait;

    private $sql;
    private $search;

    const LIST_TITLES = [
        '번호'
        ,'마감일자'
        ,'입고수량'
        ,'출고수량'
        ,'총 재고수량'
        ,'처리자'
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
            'combineSearch' => [],
            'combineTreatDate' => [
                'a.regDt' => '마감일자',
            ],
            'sortList' => [
                'a.regDt desc' => __('마감 일자 ↑'),
                'a.regDt asc' => __('마감 일자 ↓'),
            ],
            'sort' => gd_isset( $searchData['sort'] ,'a.regDt desc' ),
            'page' => gd_isset( $searchData['page'] ,1),
            'pageNum' => gd_isset( $searchData['pageNum'] ,100),
        ];

        //변경.
        $searchData['treatDateFl'] = 'a.regDt';
        ListUtil::setSearch($this, $setParam);
        // 기본 텍스트 검색 설정
        $this->setSearchData([],$searchData);
        $this->setRadioSearch([],$searchData,'all');
        $this->setRadioSearch([],$searchData,'');
        $this->setCheckSearch([]);

        //날짜 기간 설정
        $searchData = $this->setDefaultSearchDate($searchData, 364);
        $this->setRangeDate($searchData['treatDateFl'],  $searchData['treatDate'][0], $searchData['treatDate'][1]);
    }

    /**
     * 리스트
     * @param string  $searchData   검색 데이타
     * @return array 주문 리스트 정보
     */
    public function getList($searchData): array {
        //$data = $this->getTraitList($searchData, 'getList');
        //$data['data'] = SlCommonUtil::setEachData($data['data'], $this, 'setRefineEachListData');
        //return $data;
        return $this->getTraitList($searchData, 'getList');
    }

    /**
     * 리스트 데이터 Refine.
     * @param $each
     * @param $key
     * @param $data
     * @return mixed
     */
    /*public function setRefineEachListData($each, $key, $data){
        $each['inOutTypeKr'] = SlCommonUtil::getFlipData(ErpCodeMap::ERP_STOCK_TYPE,$each['inOutType']);
        $each['inOutReasonKr'] = SlCommonUtil::getFlipData(ErpCodeMap::ERP_STOCK_REASON,$each['inOutReason']);
        $each['quantityClass'] = empty($each['inOutType']) ? '' : ( $each['inOutType'] === '1' ? 'text-danger' : 'text-blue' ) ;
        return $each;
    }*/

}
