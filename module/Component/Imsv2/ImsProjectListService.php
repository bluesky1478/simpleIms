<?php

namespace Component\Imsv2;

use App;
use Component\Ims\ImsCodeMap;
use Component\Ims\ImsDBName;
use Component\Ims\ImsServiceConditionTrait;
use Component\Ims\ImsServiceSortTrait;
use Component\Imsv2\Util\ImsProjectListServiceUtil;
use Component\Member\Manager;
use Component\Sms\Code;
use Framework\Debug\Exception\AlertBackException;
use Framework\Utility\DateTimeUtils;
use LogHandler;
use Request;
use Session;
use SiteLabUtil\SlCommonUtil;
use SlComponent\Database\DBUtil2;
use SlComponent\Database\SearchVo;
use SlComponent\Util\SitelabLogger;

/**
 * IMS 프로젝트 리스트 서비스
 * Class GoodsStock
 * @package Component\Goods
 */
class ImsProjectListService
{
    use ImsServiceConditionTrait;
    use ImsServiceSortTrait;

    use ImsProjectListCompleteTrait; //전체 리스트
    use ImsProjectListAllTrait; //전체 리스트
    use ImsProjectListSalesTrait;
    use ImsProjectListDesignTrait;
    use ImsProjectListQcTrait;

    private $sql;
    private $salesSql;

    public function __construct(){
        $this->sql = \App::load('\\Component\\Imsv2\\Sql\\ImsProjectListServiceSql');
        $this->salesSql = \App::load('\\Component\\Imsv2\\Sql\\ImsSalesListSql');
    }

    /**
     * 프로젝트 리스트 준비
     * @param $listType
     * @param $sortDefault
     * @param $params
     * @param $isGroup
     * @param $isSchedule
     * @return array
     */
    public function preparedProjectList($listType, $sortDefault, $params, $isGroup=true, $isSchedule=true){
        $setConditionFnc = 'set'.ucfirst($listType).'Condition';
        $searchData = [
            'page' => gd_isset($params['page'], 1),
            'pageNum' => gd_isset($params['pageNum'], 200),
        ];
        $searchData['condition'] = $params;

        $searchVo = new SearchVo();
        $searchVo->setExcludeTableAlias(['added']);
        $searchVo = $this->$setConditionFnc($searchData['condition'], $searchVo);
        $this->setListSort(gd_isset($searchData['condition']['sort'],$sortDefault), $searchVo);

        //세부스케쥴 - 담당자 검색시 조건추가
        /*$isScheDetailSchManager = false;
        if (isset($params['iSchManagerSno']) && $params['iSchManagerSno'] > 0) {
            $isScheDetailSchManager = true;
            $searchVo->setWhere('sche_detail.ownerManagerSno = '.$params['iSchManagerSno']);
        }*/

        $tableInfo = $this->sql->getProjectListWithStyleSql($searchVo, $isGroup, $isSchedule); //$isScheDetailSchManager

        $allData = DBUtil2::getComplexListWithPaging($tableInfo, $searchVo, $searchData, false, false);
        $allData['listData'] = SlCommonUtil::setEachData($allData['listData'], $this, 'decorationList', $isGroup);
        return $allData;
    }


    /**
     * 프로젝트 Rowspan
     * @param $list
     */
    public function setProjectListRowspan($list){
        //MODE에 따른 변경
        /*
        SlCommonUtil::setListRowSpan($list, [
            'project'  => ['valueKey' => 'sno'] //projectRowspan (each , field)
        ], $params);
        */
        foreach($list as $key => $each){
            $each['projectRowspan'] = 2;
            $list[$key] = $each;
        }
    }

    /**
     * 스타일별 Rowspan
     * @param $list
     * @param $params
     */
    public function setProductRowspan(&$list, $params){
        SlCommonUtil::setListRowSpan($list, [
            'project'  => ['valueKey' => 'sno'] //projectRowspan (each , field)
        ], $params);
    }

    /**
     * 리스트 꾸미기 (공통)
     * 0 => '신규',             //신규
     * 2 => '공개입찰',          //신규
     * 6 => '리오더개선',        //신규
     * 5 => '샘플',             //신규
     *
     * 1 => '리오더',        //리오더
     * 3 => '추가',          //리오더
     * 7 => '수정(A/S)',     //리오더
     * 4 => '기성복', //기성복
     * @param $each
     * @param $key
     * @param $isGroup ==> $mixData
     * @return mixed
     */
    public function decorationList($each, $key, $isGroup)
    {
        $each = SlCommonUtil::setDateBlank($each);

        //판매가 가림
        if( !empty($_COOKIE['setSaleCostDisplay']) &&  'n' === $_COOKIE['setSaleCostDisplay'] ){
            $each['salePrice'] = 0;
            $each['totalPrdPrice'] = 0;
            $each['targetPrice'] = 0;
            $each['targetPriceMax'] = 0;
        }

        //신규/리오더 구분
        if(in_array ($each['projectType'],array_keys(ImsCodeMap::PROJECT_TYPE_N))){
            $each['isReorder'] = 'n';
        }else{
            $each['isReorder'] = 'y';
        }

        $each['projectYearSeason'] = $each['projectYear'] . ' ' . $each['projectSeason'];
        $each['bizPlanYnKr'] = 'y' === $each['bizPlanYn'] ? '포함' : '아니오';
        $each['designWorkTypeKr'] = ImsCodeMap::DESIGN_WORK_TYPE[$each['designWorkType']];

        $statusPrefix = '';
        if (in_array($each['salesStatus'], ['wait', 'proc'])) {
            $statusPrefix = ImsCodeMap::BID_TYPE_DP[$each['bidType2']];
        }
        $each['salesStatusKr'] = $statusPrefix . ImsCodeMap::SALES_STATUS[$each['salesStatus']];
        $each['projectTypeKr'] = ImsCodeMap::PROJECT_TYPE[$each['projectType']];
        $each['bidType2Kr'] = ImsCodeMap::BID_TYPE[$each['bidType2']];

        $iconMap = [
            '0' => ImsCodeMap::HTML_ICON['STOP'],
            '1' => ImsCodeMap::HTML_ICON['PROC'],
            '2' => ImsCodeMap::HTML_ICON['SUCCESS'],
            '3' => ImsCodeMap::HTML_ICON['SUCCESS'],
            '4' => ImsCodeMap::HTML_ICON['REJECT'],
            '5' => ImsCodeMap::HTML_ICON['STOP'],
        ];

        $each['fabricStatusIcon'] = $iconMap[$each['fabricStatus']];
        $each['btStatusIcon'] = $iconMap[$each['btStatus']];
        $each['fabricStatusKr'] = ImsCodeMap::IMS_FABRIC_STATUS[$each['fabricStatus']]['name'];
        $each['btStatusKr'] = ImsCodeMap::IMS_BT_STATUS[$each['btStatus']]['name'];

        $each['projectStatusKr'] = ImsCodeMap::PROJECT_STATUS[$each['projectStatus']];

        $each['workStatusIcon'] = $iconMap[$each['workStatus']];
        $each['workStatusKr'] = ImsCodeMap::IMS_PROC_STATUS[$each['workStatus']]['name'];

        //$each['estimateContents'] = json_decode(stripslashes($each['estimateContents']),true);
        //$each['estimateContents'] = $each['estimateContents'];

        $estimateData = null;
        if(!empty($each['prdCostConfirmSno']) ){
            $estimateData = json_decode($each['costContents'],true);
        }else if(!empty($each['estimateConfirmSno'])){
            $estimateData = json_decode($each['estimateContents'],true);
        }
        //SitelabLogger::logger2(__METHOD__, $estimateData);

        $margin=0;
        if(!empty($estimateData)){
            if(!empty($each['salePrice'])){
                $margin = 100 - round($estimateData['totalCost']/$each['salePrice'] * 100);
            }
            $each['estimateData'] = SlCommonUtil::getAvailData($estimateData, [
                'totalCost', 'producePeriod','deliveryType','prdMoq', 'priceMoq'
            ]);
            $each['estimateData']['fabric'] = $estimateData['fabric'];
            $each['repFabric'] = implode(' ',[$estimateData['fabric'][0]['fabricName'],$estimateData['fabric'][0]['fabricMix'],$estimateData['fabric'][0]['color']]);
            $each['prdPeriod'] = $estimateData['producePeriod'];
        }
        $each['margin'] = $margin;

        //상품별
        $each['prdFabricStatusKr'] = ImsCodeMap::IMS_FABRIC_STATUS[$each['prdFabricStatus']]['name'];
        $each['prdBtStatusKr'] = ImsCodeMap::IMS_FABRIC_STATUS[$each['prdBtStatus']]['name'];

        $each['useMallKr'] = ImsCodeMap::YES_OR_NO_TYPE[$each['useMall']];
        $each['use3plKr'] = ImsCodeMap::YES_OR_NO_TYPE[$each['use3pl']];
        $each['packingYnKr'] = ImsCodeMap::YES_OR_NO_TYPE[$each['packingYn']];
        $each['directDeliveryYnKr'] = ImsCodeMap::YES_OR_NO_TYPE[$each['directDeliveryYn']];

        $each['totalPrdPriceKr'] = SlCommonUtil::numberToKorean($each['totalPrdPrice']);
        $each['totalPrdCostKr'] = SlCommonUtil::numberToKorean($each['totalPrdCost']);
        $each['totalMarginKr'] = SlCommonUtil::numberToKorean($each['totalPrdPrice']-$each['totalPrdCost']);

        $each['totalMarginPercent'] = 0;
        if( $each['totalPrdPrice'] > 0 ){
            $each['totalMarginPercent'] = 100 - round($each['totalPrdCost']/$each['totalPrdPrice']*100);
        }

        //지연체크
        ImsProjectService::decorationProjectCommon($each);
        
        //디자이너
        $each['extDesignerList'] = json_decode(stripcslashes($each['extDesigner']),true);
        $each['addManagerList'] = json_decode(stripcslashes($each['addManager']),true);

        //매출목표
        $each['salesTargetKr'] = ImsCodeMap::PERIOD_TYPE[$each['salesTarget']];
        //계약난이도
        $each['contractDifficultKr'] = ImsCodeMap::RATING_TYPE2[$each['contractDifficult']];

        //FIXME : 코멘트 가져오기 (프로세스 새롭게 하기)
        if($isGroup){
            $scheduleList = array_merge(ImsCodeMap::PROJECT_SCHEDULE_LIST, ImsCodeMap::PROJECT_SALES_SCHEDULE_LIST);
            foreach($scheduleList as $scheduleKey => $scheduleName){
                $each[$scheduleKey.'CommentCnt'] = DBUtil2::runSelect("select count(1) as cnt from sl_imsComment where commentDiv='{$scheduleKey}' and projectSno={$each['sno']} ")[0]['cnt'];
            }
        }

        //추정매출
        $each['totalTargetPriceKr'] = SlCommonUtil::numberToKorean($each['totalTargetPrice']);
        $each['totalTargetMarginKr'] = SlCommonUtil::numberToKorean($each['totalTargetPrice']-$each['totalTargetCost']);

        if( $each['totalTargetCost'] > 0 && $each['totalTargetPrice'] ){
            $each['totalTargetMargin'] = round($each['totalTargetCost']/$each['totalTargetPrice']*100,0);
        }else{
            $each['totalTargetMargin'] = 0;
        }

        //프로젝트 ( 업종 )
        $bizCateName = [$each['bizCate1'] , $each['bizCate2']];
        $each['bizCateName']=implode('<br>',$bizCateName);

        $each['productionStatusKr'] = 'TEST';

        return $each;
    }

    /**
     * 리오더 준비
     * @return array[]
     */
    public function getReorderField(){
        return [
            ['title'=>'등록일','type'=>'d1','name'=>'regDt','col'=>4,],
            ['title'=>'프로젝트','type'=>'c','name'=>'reorderProject','col'=>16,],
            ['title'=>'연도/시즌','type'=>'s','name'=>'projectYearSeason','col'=>4,],
            ['title'=>'스타일','type'=>'s','name'=>'productName','col'=>15,'class'=>'ta-l pdl5'],
            ['title'=>'아소트', 'type' => 'c', 'name' => 'assort', 'col' => 0,],
            ['title'=>'작지상태', 'type' => 'c', 'name' => 'workStatus', 'col' => 0,],
            ['title'=>'발주D/L','type'=>'c','name'=>'productionOrder','col'=>0,],
            ['title'=>'희망납기(고객납기)','type'=>'d3','name'=>'customerDeliveryDt','col'=>0,],
            ['title'=>'영업 담당자','type'=>'s','name'=>'salesManagerNm','col'=>0,],
        ];
    }

    /**
     * 리오더 프로젝트 리스트
     * @param $params
     * @return array
     */
    public function getReorderList($params)
    {
        if( 'project' === $params['viewType'] ){
            $allData = $this->preparedProjectList('reorder', 'P3,asc', $params);
            $fieldData = $this->getReorderField();
            $list = $allData['listData'];
        }else{
            $isGroup=false;
            $isSchedule=false;
            $allData = $this->preparedProjectList('reorder', 'P3,asc', $params, $isGroup, $isSchedule);

            $list = SlCommonUtil::setEachData($allData['listData'], $this, 'decorationList');
            //--- Rowspan설정
            //$this->setProductRowspan($list, $params);

            SlCommonUtil::setListRowSpan($list, [
                'project'  => ['valueKey' => 'sno'], //projectRowspan (each , field)
            ], $params);

            $fieldData = $this->getQcField();
        }

        SlCommonUtil::setColWidth(95, $fieldData);
        //리스트 일괄수정에 쓰일 arr 정리
        $enableFld = [
            'sno', //update를 위해 무조건 넣어줘야 할 키값
            'exProductionOrder', 'customerDeliveryDt'
        ];
        $listData2 = []; //일괄수정에 쓰이는 리스트
        foreach ($list as $key => $val) {
            $aTmp = [];
            foreach ($val as $key2 => $val2) {
                if (in_array($key2, $enableFld)) {
                    $aTmp[$key2] = $val2; //$allData['listData'][$key]의 값을 그대로 계승함
                }
            }
            $listData2[$key] = $aTmp;
        }

        //--- Rowspan설정
        //$this->setProjectListRowspan($list);
        return [
            'pageEx' => $allData['pageData']->getPage('#'),
            'page' => $allData['pageData'],
            'list' => $list,
            'listUpdateMulti' => $listData2,
            'listUpdateMultiOrigin' => $listData2,
            'fieldData' => $fieldData
        ];
    }


    /**
     * 리오더 검색 조건 설정
     * @param $condition
     * @param SearchVo $searchVo
     * @return mixed
     */
    public function setReorderCondition($condition, SearchVo $searchVo){
        //IN NOT IN
        $searchVo->setWhere(' prj.projectType in (1,3,7,4)');
        $searchVo->setWhere(' prj.projectStatus not in (60,90,99,98)');

        return $this->setCommonCondition($condition, $searchVo);
    }

}


