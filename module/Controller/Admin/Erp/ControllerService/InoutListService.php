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
class InoutListService implements ListInterface {

    use SlCommonTrait;

    private $sql;
    private $search;

    const LIST_TITLES = [
        '번호'
        ,'입/출고일자'   //inOutDate
        ,'구분'    //입고출고
        ,'사유'     //inOutReason Kr.
        ,'고객사'   //scmName
        ,'상품코드' //thirdPartyProductCode
        ,'상품명'  //productName
        ,'옵션'    //optionName
        ,'수량'    //quantity
        ,'메모'    //memo
        ,'주문번호(송장번호)' //orderNo
        ,'식별번호'   //managerNm Kr.
        ,'등록일'   //regDt
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
                'b.productName' => '상품명',
                'b.thirdPartyProductCode' => '상품코드',
                'a.orderNo' => '주문번호',
                'a.invoiceNo' => '송장번호',
            ],
            'combineTreatDate' => [
                'a.inOutDate' => '입/출고일자',
                'a.regDt' => '등록일자',
            ],
            'sortList' => [
                'a.inOutDate desc' => __('입/출고 일자 ↑'),
                'a.inOutDate asc' => __('입/출고 일자 ↓'),
            ],
            'sort' => gd_isset( $searchData['sort'] ,'a.inOutDate desc' ),
            'page' => gd_isset( $searchData['page'] ,1),
            'pageNum' => gd_isset( $searchData['pageNum'] ,20),
        ];

        //변경.
        $searchData['treatDateFl'] = 'a.inOutDate';

        ListUtil::setSearch($this, $setParam);

        // 기본 텍스트 검색 설정
        $this->setSearchData([
            'key'
            ,'keyword'
            ,'scmNo'
            ,'scmNoNm'
            ,'inOutType'
            ,'inOutReason'
            ,'memberType'
        ],$searchData);

        $this->setRadioSearch([
            'scmFl'
        ],$searchData,'all');
        $this->setRadioSearch([
            'inOutType'
            ,'memberType'
        ],$searchData,'');
        $this->setCheckSearch([
            'inOutReason'
        ]);

        if ($searchData['scmNo'] == 0 && $searchData['scmFl'] == 1) {
            $this->search['scmFl'] = 'all';
        }

        //날짜 기간 설정
        $searchData = $this->setDefaultSearchDate($searchData, 7);
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
        $data['data'] = SlCommonUtil::setEachData($data['data'], $this, 'setRefineEachListData');
        $data['totalInoutCount'] = $this->sql->getListSummaryData($searchData)[0];
        return $data;
    }

    /**
     * 리스트 데이터 Refine.
     * @param $each
     * @param $key
     * @param $data
     * @return mixed
     */
    public function setRefineEachListData($each, $key, $data){
        $each['inOutTypeKr'] = SlCommonUtil::getFlipData(ErpCodeMap::ERP_STOCK_TYPE,$each['inOutType']);
        $each['inOutReasonKr'] = SlCommonUtil::getFlipData(ErpCodeMap::ERP_STOCK_REASON,$each['inOutReason']);
        $each['quantityClass'] = empty($each['inOutType']) ? '' : ( $each['inOutType'] === '1' ? 'text-danger' : 'text-blue' ) ;
        return $each;
    }

}
