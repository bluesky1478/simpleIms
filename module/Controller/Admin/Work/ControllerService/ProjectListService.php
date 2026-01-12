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
use SlComponent\Database\DBUtil2;
use SlComponent\Database\SearchVo;
use SlComponent\Godo\ListInterface;
use SlComponent\Util\SitelabLogger;
use SlComponent\Util\SlCodeMap;
use SlComponent\Util\SlCommonTrait;
use SlComponent\Util\SlLoader;
use SlComponent\Util\SlProjectCodeMap;

/**
 *  프로젝트 리스트
 * Class GoodsStock
 * @package Component\Goods
 */
class ProjectListService implements ListInterface {

    use SlCommonTrait;

    private $sql;
    private $search;

    const LIST_TITLES = [
        'total' => [
            '프로젝트 번호'
            ,'프로젝트 타입'
            ,'프로젝트 명'
            ,'진행단계'
            ,'고객사'
            ,'미팅일'
            ,'희망납기'
            ,'발주데드라인'
            ,'일정관리'
            ,'영업'
            ,'디자인'
            ,'QC'
            ,'최종확정'
            ,'작업지시서'
            ,'사양서'
        ],
        'estimate' => [
            '프로젝트 번호'
            ,'프로젝트 타입'
            ,'프로젝트 명'
            ,'고객사'
            ,'미팅일'
            ,'희망납기'
            ,'발주데드라인'
            ,'일정관리'
            ,'영업'
            ,'디자인'
            ,'QC'
        ],
        'order' => [
            '프로젝트 번호'
            ,'프로젝트 타입'
            ,'프로젝트 명'
            ,'고객사'
            ,'미팅일'
            ,'희망납기'
            ,'발주데드라인'
            ,'일정관리'
            ,'최종확정'
            ,'작업지시서'
            ,'사양서'
        ],
        'produce' => [
            '프로젝트 정보<br>고객사'
            ,'상품명/수량'
            ,'생산처'
        ],
    ];

    public function __construct(){
        $this->sql =  SlLoader::sqlLoad(__CLASS__);
    }
    public function getSearch()
    {
        return $this->search();
    }
    public function setSearch($search)
    {
        $this->search($search);
    }

    /**
     * 리스트 타이틀 목록 반환
     * @param $searchData
     * @return string[]
     */
    public function getTitle($searchData): array
    {
        $type = Request::request()->get('type');
        if( empty($type) ){
            $type = 'total';
        }
        return ProjectListService::LIST_TITLES[$type];
    }

    /**
     * 검색 데이터 설정
     * @param $searchData
     */
    public function _setSearch($searchData){
        //키워드 검색 키 정의
        $this->search['combineSearch'] = [
            'a.projectName' => '프로젝트명',
            'b.companyName' => '고객사명',
            'a.sno' => '프로젝트번호',
        ];
        // --- $searchData trim 처리
        SlCommonUtil::refineTrimData($searchData);

        //기간 검색 조건
        $this->search['combineTreatDate'] = [
            'a.regDt' => '등록일자',
        ];

        // --- 정렬
        $this->search['sortList'] = [
            'a.regDt desc' => __('등록일자 ↑'),
            'a.regDt asc' => __('등록일자 ↓'),
        ];
        $this->search['sort'] = gd_isset( $searchData['sort'] ,'a.regDt desc' );

        // -- 페이징 기본 설정
        $this->search['page'] = gd_isset( $searchData['page'] ,1);
        $this->search['pageNum'] = gd_isset( $searchData['pageNum'] ,30);

        // 기본 검색 설정
        $this->setSearchData([
            'key'
            ,'keyword'
            ,'projectType'
            ,'projectStatus'
        ],$searchData);

        $this->setRadioSearch([
            'projectType',
            'projectStatus',
        ],$searchData,'all');

        //날짜 기간 설정
        $searchData = $this->setDefaultSearchDate($searchData, 364);
        $this->setRangeDate($searchData['treatDateFl'],  $searchData['treatDate'][0], $searchData['treatDate'][1]);

    }

    /**
     * 프로젝트 리스트
     * @param mixed  $searchData   검색 데이타
     *
     * @return array 주문 리스트 정보
     */
    public function getList($searchData): array
    {
        $request = \Request::request()->toArray();
        if( 'estimate' === $request['type'] ){
            $searchData['projectStatus']=0;
        }else if( 'order' === $request['type'] ){
            $searchData['projectStatus']=1;
        }else if( 'produce' === $request['type'] ){
            $searchData['projectStatus']=2;
        }

        $data = $this->getTraitList($searchData, 'getList');

        foreach($data['data'] as $key => $value){
            $value['planData'] = json_decode( ($value['planData']),true);
            $value['customerPlanDt'] = json_decode( gd_htmlspecialchars_stripslashes($value['customerPlanDt']),true);
            $value['productData'] = json_decode( gd_htmlspecialchars_stripslashes($value['productData']),true);

            foreach( $value['productData'] as $prdKey => $prd ){
                $prd['factoryName'] = DBUtil2::getOne('sl_workSampleFactory','sno',$prd['factorySno'])['factoryName'];
                $value['productData'][$prdKey] = $prd;
            }

            $value['projectTypeKr'] = WorkCodeMap::MS_PROPOSAL_TYPE[$value['projectType']];
            $value['projectStatusKr'] = SlProjectCodeMap::PRJ_STATUS[$value['projectStatus']];
            $data['data'][$key] = $value;
        }

        //gd_debug($data['data']);

        return $data;
    }
}
