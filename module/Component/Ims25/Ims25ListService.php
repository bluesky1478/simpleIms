<?php

namespace Component\Ims25;

use App;
use Component\Ims\ImsCodeMap;
use Component\Ims\ImsDBName;
use Component\Ims\ImsServiceConditionTrait;
use Component\Ims\ImsServiceSortTrait;
use Component\Imsv2\ImsProjectService;
use Component\Member\Manager;
use Framework\Debug\Exception\AlertBackException;
use Framework\Utility\DateTimeUtils;
use LogHandler;
use Request;
use Session;
use SiteLabUtil\ImsUtil;
use SiteLabUtil\SlCommonUtil;
use SlComponent\Database\DBUtil2;
use SlComponent\Database\SearchVo;
use SlComponent\Util\SitelabLogger;

/**
 * IMS25Ver 프로젝트 리스트 서비스
 * Class GoodsStock
 * @package Component\Goods
 */
class Ims25ListService
{
    use ImsServiceConditionTrait;
    use ImsServiceSortTrait;
    use Ims25ListAllTrait; //전체 리스트

    private $sql;

    public function __construct(){
        $this->sql = \App::load('\\Component\\Ims25\\Sql\\Ims25ListSql');
    }

    /**
     * 리스트 반환 - 타입에 따른 분기
     * getIms25AllList
     * @param $listType
     * @param $params
     * @return mixed
     */
    public function getIms25List($listType, $params){
        //$conditionFncName = 'set'.ucfirst($listType).'Condition'; //검색
        $conditionFncName = 'setAllCondition'; //검색
        //$sortFncName = 'set'.ucfirst($listType).'Sort'; //정렬
        $sortFncName = 'setAllSort'; //정렬
        $listFncName = 'getIms25'.ucfirst($listType).'List'; //리스트 getIms25AllList , getIms25StyleList
        $searchData = [
            'page' => gd_isset($params['page'], 1),
            'pageNum' => gd_isset($params['pageNum'], 100),
            'condition' => $params,
        ];
        $searchVo = new SearchVo();
        $searchVo = $this->$conditionFncName($searchData['condition'], $searchVo); //조건설정
        $this->$sortFncName($searchData['condition']['sort'], $searchVo); //정렬
        $rslt = $this->$listFncName($searchVo, $searchData);
        $rslt['list'] = SlCommonUtil::setEachData($rslt['list'], $this, 'decorationList');
        return $rslt;
    }

    /**
     * IMS 25 Version
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
     * @param $mixedData
     * @return mixed
     * @throws \Exception
     */
    public function decorationList($each, $key, $mixedData)
    {
        $each = SlCommonUtil::setDateBlank($each);

        $refineTables = [
            ImsDBName::CUSTOMER,ImsDBName::PROJECT,ImsDBName::PROJECT_EXT,
        ];
        foreach($refineTables as $tableName){
            $each = SlCommonUtil::refineDbData($each, $tableName);
        }

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

        $estimateData = null;
        if(!empty($each['prdCostConfirmSno']) ){
            $estimateData = json_decode($each['costContents'],true);
        }else if(!empty($each['estimateConfirmSno'])){
            $estimateData = json_decode($each['estimateContents'],true);
        }

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
        //SitelabLogger::logger2(__METHOD__, $each['extDesigner']);
        //SitelabLogger::logger2(__METHOD__, addslashes($each['addManager']));
        $each['extDesignerList'] = json_decode(stripcslashes($each['extDesigner']),true);
        $each['addManagerList'] = json_decode(stripcslashes(addslashes($each['addManager'])),true);

        //매출목표
        $each['salesTargetKr'] = ImsCodeMap::PERIOD_TYPE[$each['salesTarget']];
        //계약난이도
        $each['contractDifficultKr'] = ImsCodeMap::RATING_TYPE2[$each['contractDifficult']];

        //FIXME : 코멘트 가져오기 (프로세스 새롭게 하기)
        /*if($isGroup){
            $scheduleList = array_merge(ImsCodeMap::PROJECT_SCHEDULE_LIST, ImsCodeMap::PROJECT_SALES_SCHEDULE_LIST);
            foreach($scheduleList as $scheduleKey => $scheduleName){
                $each[$scheduleKey.'CommentCnt'] = DBUtil2::runSelect("select count(1) as cnt from sl_imsComment where commentDiv='{$scheduleKey}' and projectSno={$each['sno']} ")[0]['cnt'];
            }
        }*/
        $each['commentInfo'] = json_decode($each['commentCount'],true);

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

}


