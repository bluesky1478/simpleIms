<?php
namespace Controller\Admin\Sales\ControllerService;

use App;
use Component\Database\DBTableField;
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
use SlComponent\Recap\RecapService;
use SlComponent\Util\ListUtil;
use SlComponent\Util\SitelabLogger;
use SlComponent\Util\SlCodeMap;
use SlComponent\Util\SlCommonTrait;
use SlComponent\Util\SlLoader;

/**
 * Recap 생산 리스트
 * Class GoodsStock
 * @package Component\Goods
 */
class SalesListService implements ListInterface {

    use SlCommonTrait;

    private $sql;
    private $search;
    private $filesFieldInfo = [];

    const LIST_TITLE = [
        '출처',
        '등급',
        '고객 구분',
        '고객사명',
        '업종 구분',
        '사원수',
        '대표번호',
        '부서',
        '담당자',
        '직통번호',
        '이메일',
        '비고',
        '구매방식',
        '의류 구분',
        '구매 예정일',
        '구매 품목',
        '구매 수량',
        '최근통화일',
        '통화내용',
    ];
    const LIST_TITLE_EXCEL = [
        '출처',
        '등급',
        '고객 구분',
        '고객사명',
        '업종 구분',
        '사원수',
        '대표번호',
        '부서',
        '담당자',
        '직통번호',
        '이메일',
        '비고',
        '구매방식',
        '의류 구분',
        '구매 예정일',
        '구매 품목',
        '구매 수량',
        '최근통화일',
    ];
    const LIST_TITLE_DETAIL = [
        ['top' => ['출처', 'targetSource']],
        ['top' => ['등급', 'level']],
        ['top' => ['고객 구분', 'customerType']],
        ['top' => ['고객사명', 'customerName']],
        ['top' => ['업종 구분', 'industry']],
        ['top' => ['사원수', 'employeeCnt']],
        ['top' => ['대표번호', 'phone']],
        ['top' => ['부서', 'dept']],
        ['top' => ['담당자', 'contactName']],
        ['top' => ['직통번호', 'contactPhone']],
        ['top' => ['이메일', 'contactEmail']],
        ['top' => ['비고', 'memo', '', '1']],
        ['top' => ['구매방식', 'buyMethod']],
        ['top' => ['의류 구분', 'buyDiv']],
        ['top' => ['구매 예정일', 'buyExt']],
        ['top' => ['구매 품목', 'buyItem']],
        ['top' => ['구매 수량', 'buyCnt']],
        ['top' => ['통화일자', 'contactDt']],
        ['top' => ['통화내용', 'contactContents']],
    ];

    public function __construct(){
        $this->sql =  SlLoader::sqlLoad(__CLASS__);
        $recapFileField = DBTableField::tableRecapPrdFile();
        foreach($recapFileField as $fileKey => $fieldValue) {
            if (strpos($fieldValue['val'], 'file') !== false) {
                $this->filesFieldInfo[] = $fieldValue['val'];
            }
        }
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
        return SalesListService::LIST_TITLE;
    }

    /**
     * 검색 데이터 설정
     * @param $searchData
     */
    public function _setSearch($searchData){
        SlCommonUtil::refineTrimData($searchData);
        $setParam = [
            'combineSearch' => [
                'a.customerName' => '고객사명',
                'a.level' => '고객등급',
                'a.targetSource' => '출처',
                'a.customerType' => '고객구분',
                'a.industry' => '업종',
                'a.contactName' => '담당자',
                'a.contactEmail' => '이메일',
            ],
            'combineTreatDate' => [
                'a.contactDt' => '최근통화일자',
                'a.regDt' => '등록일자',
            ],
            'sortList' => [
                'a.regDt asc, a.sno desc' => __('등록일 ↑'),
                'a.regDt desc, a.sno desc' => __('등록일 ↓'),
                'a.contactDt	 asc, a.sno desc' => __('최근통화일 ↑'),
                'a.contactDt	 desc, a.sno desc' => __('최근통화일 ↓'),
                'a.customerName asc, a.sno desc' => __('고객사 ↑'),
                'a.customerName desc, a.sno desc' => __('고객사 ↓'),
                'a.level asc, a.sno desc' => __('등급 ↑'),
                'a.level desc, a.sno desc' => __('등급 ↓'),
            ],
            'sort' => gd_isset( $searchData['sort'] ,'a.regDt desc' ),
            'page' => gd_isset( $searchData['page'] ,1),
            'pageNum' => gd_isset( $searchData['pageNum'] ,30),
        ];

        ListUtil::setSearch($this, $setParam);

        if(empty(\Request::get()->get('searchDate'))){
            //$searchData[0] = date('Y-m-d', strtotime('-365 day')); //1년전
            //$searchData[1] = date('Y-m-d');
            //$this->search['searchDate'][0] = date('Y-m-d', strtotime('-364 day')); //1년전
            //$this->search['searchDate'][1] = date('Y-m-d');
        }else{
            $this->search['searchDate'][0] = \Request::get()->get('searchDate')[0];
            $this->search['searchDate'][1] = \Request::get()->get('searchDate')[1];
        }
        //SlCommonUtil::setDefaultDate('searchDate','-181 day');

        // 기본 텍스트 검색 설정
        $this->setSearchData([
            'key'
            ,'keyword'
            ,'searchDateFl'
        ],$searchData);

        /*if(empty(\Request::get()->get('searchDate'))){
            $arr[] = date('Y-m-d', strtotime('-365 day')); //1년전
            $arr[] = date('Y-m-d', strtotime('-182 day')); //6개월전.
            \Request::get()->set('searchDate', $arr);
        }*/

        //$this->search['searchDate'] = \Request::get()->get('searchDate');
        //$this->search['searchDate2'] = \Request::get()->get('searchDate2');

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
        //$data['pureData'] = $data['data'];
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

        $each['customerType'] = SalesListService::SALES_STATUS_MAP[$each['customerType']];

        $each['contactCnt'] = DBUtil2::getCount('sl_salesCustomerContents', new SearchVo('salesSno=?',$each['sno']));

        return $each;
    }

    const SALES_STATUS_MAP = [
        10 => '잠재고객',
        20 => '관심고객',
        30 => '가망고객',
        40 => '기타고객',
        50 => '발굴완료',
        80 => '미팅고객(진행)',
        90 => '미팅고객(계약)',
        99 => '미팅고객(이탈)',
    ];

}
