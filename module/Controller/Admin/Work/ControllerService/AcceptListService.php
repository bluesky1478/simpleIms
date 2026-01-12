<?php
namespace Controller\Admin\Work\ControllerService;

use App;
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
 * 문서 리스트
 * Class GoodsStock
 * @package Component\Goods
 */
class AcceptListService implements ListInterface {

    use SlCommonTrait;

    private $sql;
    private $search;

    const LIST_TITLES = [
        '번호'
        ,'프로젝트번호/이름'
        ,'고객사'
        ,'문서명'
        ,'버전'
        ,'승인정보'
        ,'승인상태'
        ,'작성자'
        ,'영업'
        ,'디자인'
        ,'등록일'
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
                'd.managerNm' => '작성자',
                'c.companyName' => '고객사명',
                'b.projectName' => '프로젝트명',
                'e.managerNm' => '영업 담당자',
                'f.managerName' => '디자인 담당자',
            ],
            'combineTreatDate' => [
                'b.regDt' => '프로젝트 등록일자',
            ],
            'sortList' => [
                'b.regDt desc' => __('프로젝트 등록일자 ↑'),
                'b.regDt asc' => __('프로젝트 등록일자 ↓'),
            ],
            'sort' => gd_isset( $searchData['sort'] ,'a.regDt desc' ),
            'page' => gd_isset( $searchData['page'] ,1),
            'pageNum' => gd_isset( $searchData['pageNum'] ,30),
            'docDept' => gd_isset( $searchData['docDept'] , ''),
            'docType' => gd_isset( $searchData['docType'] , ''),
            'isApplyFl' => gd_isset( $searchData['isApplyFl'] , 'n')
        ];

        ListUtil::setSearch($this, $setParam);

        // 기본 텍스트 검색 설정
        $this->setSearchData([
            'key'
            ,'keyword'
            ,'regManagerName'
            ,'isApplyFl'
        ],$searchData);

        $this->setRadioSearch([
            'isApplyFl'
        ],$searchData,'all');

        //날짜 기간 설정
        $searchData = $this->setDefaultSearchDate($searchData, 364);
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
        return $data;
    }

}
