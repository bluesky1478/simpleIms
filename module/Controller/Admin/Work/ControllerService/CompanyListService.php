<?php
namespace Controller\Admin\Work\ControllerService;

use App;
use Component\Member\Manager;
use Component\Storage\Storage;
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
 *  거래처 리스트
 * Class GoodsStock
 * @package Component\Goods
 */
class CompanyListService implements ListInterface {

    use SlCommonTrait;

    private $sql;
    private $search;

    const LIST_TITLES = [
        '번호'
        ,'구분'
        ,'업체명'
        /*,'진행상태'*/
        ,'대표전화'
        ,'영업 담당자'
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
                'a.companyName' => '거래처명',
            ],
            'combineTreatDate' => [
                'a.regDt' => '등록일자',
            ],
            'sortList' => [
                'a.regDt desc' => __('등록일자 ↑'),
                'a.regDt asc' => __('등록일자 ↓'),
            ],
            'sort' => gd_isset( $searchData['sort'] ,'a.regDt desc' ),
            'page' => gd_isset( $searchData['page'] ,1),
            'pageNum' => gd_isset( $searchData['pageNum'] ,30)
        ];
        ListUtil::setSearch($this, $setParam);

        // 기본 텍스트 검색 설정
        $this->setSearchData([
            'key'
            ,'keyword'
        ],$searchData);
        
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
        foreach($data['data'] as $key => $value){
            $value['latestDocData'] = $documentService->getLatestDocumentForCompany('SALES', 2, $value['sno']);
            //gd_debug($value['latestDocData']['docData']['currentStep']);
            $data['data'][$key] = $value;
        }
        return $data;
    }

}
