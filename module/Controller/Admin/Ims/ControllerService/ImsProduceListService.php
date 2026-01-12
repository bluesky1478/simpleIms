<?php
namespace Controller\Admin\Ims\ControllerService;

use App;
use Component\Api\CustomApiSql;
use Component\Ims\ImsCodeMap;
use Component\Ims\ImsDBName;
use Component\Member\Manager;
use Component\Storage\Storage;
use Framework\Debug\Exception\AlertBackException;
use Framework\Utility\DateTimeUtils;
use LogHandler;
use Request;
use Session;
use SiteLabUtil\SlCommonUtil;
use SlComponent\Database\DBConst;
use SlComponent\Database\DBUtil2;
use SlComponent\Database\SearchVo;
use SlComponent\Godo\ListInterface;
use SlComponent\Util\ListUtil;
use SlComponent\Util\SlCommonTrait;
use SlComponent\Util\SlLoader;

/**
 * 입출고 리스트
 * Class GoodsStock
 * @package Component\Goods
 */
class ImsProduceListService implements ListInterface {
    use SlCommonTrait;
    use ImsListServiceTrait;

    private $imsProduceService;
    private $imsService;

    public function runConstructionAddMethod(){
        $this->imsProduceService = SlLoader::cLoad('ims','imsProduceService');
        $this->imsService = SlLoader::cLoad('ims','imsService');
    }

    const LIST_TITLES = [
        '생산처<br>납기 D-day',
        '연도/시즌',
        '고객사/프로젝트번호',
        '발주/납기/고객납기',
        '스타일',
        '수량',
        '구분',
        '세탁 및 <br>이화학검사ⓒ',
        '원부자재 확정ⓒ',
        '원부자재 선적',
        'QCⓒ',
        '인라인ⓒ',
        '선적',
        '도착',
        '입고 제품 검수ⓒ',
        '공장납기',
    ];

    /**
     * 검색 데이터 설정
     * @param $searchData
     */
    public function _setSearch($searchData){

        SlCommonUtil::refineTrimData($searchData);

        $searchKey = [
            'b.customerName' => '고객사명',
            'b.contactName' => '담당자명',
            'a.projectNo' => '프로젝트번호',
            'e.styleCode' => '스타일코드',
            'a.projectName' => '말머리(프로젝트별칭)',
        ];

        $sortList = [
            'a.msDeliveryDt asc' => __('이노버납기일 ↑'),
            'a.msDeliveryDt desc' => __('이노버납기일 ↓'),
            'f.shipExpectedDt' => __('선적 예정일 ↑'),
            'f.shipExpectedDt desc' => __('선적 예정일 ↓'),
            'f.shipCompleteDt' => __('선적 확정일 ↑'),
            'f.shipCompleteDt desc' => __('선적 확정일 ↓'),
            'f.regDt asc' => __('등록일 ↑'),
            'f.regDt desc' => __('등록일 ↓'),
            'f.modDt asc' => __('수정일 ↑'),
            'f.modDt desc' => __('수정일 ↓'),
            'b.customerName asc' => __('고객사명 ↑'),
            'b.customerName desc' => __('고객사명 ↓'),
        ];

        $setParam = [
            'combineSearch' => $searchKey,
            'sortList' => $sortList,
            'sort' => gd_isset( $searchData['sort'] ,'a.msDeliveryDt asc'),
            'page' => gd_isset( $searchData['page'] ,1),
            'pageNum' => gd_isset( $searchData['pageNum'] ,100),
            'combineTreatDate' => [
                'a.regDt' => '프로젝트 등록일',
                'f.shipExpectedDt' => '선적 예정일',
                'f.shipCompleteDt' => '선적 확정일',
            ],
        ];
        ListUtil::setSearch($this, $setParam);

        // 기본 텍스트 검색 설정
        $this->setSearchData([
            'key'
            ,'keyword'
            ,'produceCompanySno'
            ,'showMemo'
            ,'showReqAccept'
            ,'packingYn'
            ,'use3pl'
            ,'useMall'
            ,'projectYear'
            ,'projectSeason'
        ],$searchData);

        $this->setRadioSearch([
            'showMemo'
        ],$searchData,'y');

        $this->setRadioSearch([
            'showReqAccept',
            'packingYn',
            'use3pl',
            'useMall',
        ],$searchData,'all');

        //날짜 기간 설정
        if( empty($searchData['treatDateFl']) ) $searchData['treatDateFl'] = 'a.regDt';

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
        $data['data'] = SlCommonUtil::setEachData($data['data'], $this, 'setRefineEachListData');
        //$data['totalInoutCount'] = $this->sql->getListSummaryData($searchData)[0];
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

        $managerId = \Session::get('manager.managerId');

        $each = $this->imsService->decorationEachData($each); //가공.
        $each = $this->imsProduceService->decorationProduceEachData($each); //가공

        $completeDate = '';

        foreach( ImsCodeMap::PRODUCE_STEP_MAP as $stepKey => $stepTitle ){
            if( 90 === $stepKey ) $completeDate = $each['prdStep'.$stepKey]['expectedDt'];
            $each['prdStep'.$stepKey]['expectedDt'] = SlCommonUtil::getSimpleWeekDay($each['prdStep'.$stepKey]['expectedDt']);
            $each['prdStep'.$stepKey]['completeDt'] = SlCommonUtil::getSimpleWeekDay($each['prdStep'.$stepKey]['completeDt']);
        }

        $each['projectStatusKr'] = ImsCodeMap::PROJECT_STATUS[$each['projectStatus']];
        $each['projectTypeKr'] = ImsCodeMap::PROJECT_TYPE[$each['projectType']];
        $each['produceStatusKr'] = ImsCodeMap::PRODUCE_STATUS[$each['produceStatus']]; //생산상태.

        $each['confirmed'] = 'y' === $each['confirmed']? '예':'아니오';
        //$each['use3plKr'] = 'y' === $each['use3pl']? '<span class="sm-danger">(3PL)</span>':'';
        //gd_debug($each);

        //아이콘 추가
        $this->imsService->setProjectIcon($each, $each['sno']);

        $each['customerOrderDt'] = SlCommonUtil::getSimpleWeekDay($each['customerOrderDt']);
        $each['customerDeliveryDt'] = SlCommonUtil::getSimpleWeekDay($each['customerDeliveryDt']);

        $each['isWarn'] = ($completeDate > $each['msDeliveryDt']) ? '1':'';

        $each['msOrderDt'] = SlCommonUtil::getSimpleWeekDay($each['msOrderDt']);
        $each['msDeliveryDt'] = SlCommonUtil::getSimpleWeekDay($each['msDeliveryDt']);

        $styleCount = $each['styleCount']-1;
        $each['styleCountNm'] = $styleCount>0?'외 '.$styleCount.'건':'';

        $each['recommendDt'] = gd_date_format('m/d',$each['recommendDt']);
        $each['designEndDt'] = gd_date_format('m/d',$each['designEndDt']);
        $each['prdEndDt'] = gd_date_format('m/d',$each['prdEndDt']);
        $each['regDt'] = gd_date_format('Y-m-d',$each['regDt']);
        $each['modDt'] = gd_date_format('y/m/d H:i:s',$each['produceModDt']);

        $each['commentCnt'] = DBUtil2::getCount(ImsDBName::PROJECT_COMMENT, new SearchVo(['commentDiv=?','projectSno=?'], ['produce',$each['sno']]));
        $each['latestComment'] = $this->imsProduceService->getLatestComment('produce',$each['sno']);

        return $each;
    }

}
