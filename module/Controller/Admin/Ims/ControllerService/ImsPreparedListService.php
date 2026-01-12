<?php
namespace Controller\Admin\Ims\ControllerService;

use App;
use Component\Ims\EnumType\PREPARED_TYPE;
use Component\Ims\ImsCodeMap;
use Component\Member\Manager;
use Component\Storage\Storage;
use Framework\Debug\Exception\AlertBackException;
use Framework\Utility\DateTimeUtils;
use LogHandler;
use Request;
use Session;
use SiteLabUtil\SlCommonUtil;
use SlComponent\Database\DBConst;
use SlComponent\Godo\ListInterface;
use SlComponent\Util\ListUtil;
use SlComponent\Util\SlCommonTrait;
use SlComponent\Util\SlLoader;

/**
 * 입출고 리스트
 * Class GoodsStock
 * @package Component\Goods
 */
class ImsPreparedListService implements ListInterface {
    use SlCommonTrait;
    use ImsListServiceTrait;

    private $imsService;

    const LIST_TITLES = [
        '프로젝트번호',
        '고객사/스타일수',
        '처리상태',
        '의뢰일자',
        '완료D/L(완료요청일)',
    ];

    public function runConstructionAddMethod(){
        $this->imsService = SlLoader::cLoad('ims','imsService');
    }

    /**
     * 검색 데이터 설정
     * @param $searchData
     */
    public function _setSearch($searchData){
        $searchKey = [
            'b.customerName' => '고객사명',
            'b.contactName' => '담당자명',
            'b.styleCode' => '고객명(Style code)',
        ];
        $sortList = [
            'f.deadlineDt asc' => __('완료D/L ↑'),
            'f.deadlineDt desc' => __('완료D/L ↓'),
            'f.preparedStatus asc' => __('상태 ↑'),
            'f.preparedStatus desc' => __('상태 ↓'),
            'f.regDt asc' => __('의뢰일 ↑'),
            'f.regDt desc' => __('의뢰일 ↓'),
            'a.regDt asc' => __('등록일 ↑'),
            'a.regDt desc' => __('등록일 ↓'),
            'b.customerName asc' => __('고객사명 ↑'),
            'b.customerName desc' => __('고객사명 ↓'),
        ];

        $setParam = [
            'combineSearch' => $searchKey,
            'sortList' => $sortList,
            'sort' => gd_isset( $searchData['sort'] ,'f.deadlineDt asc'),
            'page' => gd_isset( $searchData['page'] ,1),
            'pageNum' => gd_isset( $searchData['pageNum'] ,100),
        ];
        ListUtil::setSearch($this, $setParam);

        if(!isset($searchData['workStep'])){
            if( $this->isProduceCompany() ){
                //생산처
                $searchData['workStep'] = 'n';
            }else{
                if( 'work' === $searchData['preparedType'] ){
                    //이노버
                    $managerId = \Session::get('manager.managerId');
                    $searchData['workStep'] = 'n';

                    //승인권자는 승인 대기 중인것을 보여줌. 그 외는 작업할 것을 보여줌.
                    /*if( in_array($managerId,ImsCodeMap::AUTH_MANAGER) ){
                        $searchData['workStep'] = 'r';
                    }else{
                        $searchData['workStep'] = 'n';
                    }*/
                }else{
                    $searchData['workStep'] = 'r';
                }
            }
        }

        // 기본 텍스트 검색 설정
        $this->setSearchData([
            'key'
            ,'keyword'
        ],$searchData);

        $this->setRadioSearch([
            'workStep'
        ],$searchData,'all');


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
     * @throws \Exception
     */
    public function setRefineEachListData($each, $key, $data){

        $each = $this->imsService->decorationPreparedEachData($each);
        $each = $this->imsService->decorationEachData($each);

        $each['deadLineRemainDt'] =  SlCommonUtil::getRemainDtMonth($each['deadLineDt']);
        $each['deadLineDtShort'] = SlCommonUtil::getSimpleWeekDay($each['deadLineDt']);
        $each['preparedRegDtShort'] = SlCommonUtil::getSimpleWeekDay($each['preparedRegDt']);
        $each['preparedStatusKr'] = PREPARED_TYPE::STATUS_COLOR[$each['preparedStatus']];
        $each['sendType'] = $each['contents']['sendType'];
        $each['sendInfo'] = $each['contents']['sendInfo'];
        $each['workSendDt'] = $each['contents']['workSendDt'];
        $each['sendDt'] = SlCommonUtil::getSimpleWeekDay($each['contents']['sendDt']);

        $season = ImsCodeMap::IMS_SEASON_ICON[$each['projectSeason']];
        $each['seasonIcon'] = empty($season)?ImsCodeMap::IMS_SEASON_ICON['']:$season;  /*'<i class="fa fa-2x fa-snowflake-o" aria-hidden="true"></i>'*/;

        return $each;
    }

}
