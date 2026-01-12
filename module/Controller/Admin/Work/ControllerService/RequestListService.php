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
 * 업무요청 리스트
 * Class GoodsStock
 * @package Component\Goods
 */
class RequestListService implements ListInterface {

    use SlCommonTrait;

    private $sql;
    private $search;

    const LIST_TITLES = [
        '번호'
        ,'프로젝트번호'
        ,'고객사/문서명'
        ,'요청자/요청일'
        ,'대상부서'
        ,'요청내용'
        ,'완료요청일'
        ,'답변내용'
        ,'처리여부'
        ,'처리자'
        ,'처리일자'
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
        $setParam = [
            'combineSearch' => [
                'b.managerNm' => '요청자',
                'c.managerNm' => '처리자',
                'a.reqContents' => '요청내용',
                'a.resContents' => '답변내용',
            ],
            'combineTreatDate' => [
                'a.regDt' => '요청일자',
                'a.procDt' => '답변일자',
            ],
            'sortList' => [
                'a.regDt desc' => __('요청일자 ↑'),
                'a.regDt asc' => __('요청일자 ↓'),
            ],
            'sort' => gd_isset( $searchData['sort'] ,'a.regDt desc' ),
            'page' => gd_isset( $searchData['page'] ,1),
            'pageNum' => gd_isset( $searchData['pageNum'] ,30),
            'docDept' => gd_isset( $searchData['docDept'] , WorkCodeMap::DEPT_STR[SlCommonUtil::getManagerInfo(\Session::get('manager.sno'))['departmentCd']]),
        ];
        ListUtil::setSearch($this, $setParam);

        // 라디오 검색 설정
        $this->setRadioSearch([
            'isProcFl'
        ],$searchData,'all');

        // 기본 텍스트 검색 설정
        $this->setSearchData([
            'key'
            ,'keyword'
        ],$searchData);

        $searchData = $this->setDefaultSearchDate($searchData, 364);
        //날짜 기간 설정
        $this->setRangeDate($searchData['treatDateFl'],  $searchData['treatDate'][0], $searchData['treatDate'][1]);
    }

    /**
     * 거래처 리스트
     * @param string  $searchData   검색 데이타
     *
     * @return array 주문 리스트 정보
     */
    public function getList($searchData): array
    {
        $documentService = SlLoader::cLoad('work','documentService','');

        $data = $this->getTraitList($searchData, 'getList');
        foreach($data['data'] as $key => $each){
            $docBasicData = $documentService->getDocumentBasicData($each['docDept'], $each['docType'], $each['tempFl'], $each['version']);
            $each['docLink'] = $docBasicData['docLink'];
            $each['documentName'] = $docBasicData['docNameExtend'];
            $data['data'][$key] = $each;
        }
        return $data;
    }

}
