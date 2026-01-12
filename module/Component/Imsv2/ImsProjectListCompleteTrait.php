<?php

namespace Component\Imsv2;

use App;
use Component\Ims\ImsCodeMap;
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
Trait ImsProjectListCompleteTrait
{
    /**
     * 전체 리스트
     * @return array[]
     */
    public function getCompleteListField()
    {
        return [
            ['title' => '타입', 'type' => 'c', 'name' => 'projectType', 'col' => 5,],
            ['title' => '프로젝트', 'type' => 'c', 'name' => 'projectNo', 'col' => 13,],

            ['title' => '매출정보', 'type' => 'c', 'name' => 'salesInfo', 'col' => 6,],
            ['title' => '고객납기', 'type' => 'd3', 'name' => 'customerDeliveryDt', 'col' => 6,],
            ['title' => '발주D/L', 'type' => 'c', 'name' => 'productionOrder', 'col' => 6,],

            ['title' => '회계반영', 'type' => 'c', 'name' => 'isBookRegistered', 'col' => 6,],
            ['title' => '작지정제', 'type' => 'c', 'name' => 'refineOrder', 'col' => 6,],
            ['title' => '납품 수량확인', 'type' => 'c', 'name' => 'confirmStock', 'col' => 6,],

            ['title' => '3PL', 'type' => 'html', 'name' => 'use3plKr', 'col' => 4,],
            ['title' => '폐쇄몰', 'type' => 'html', 'name' => 'useMallKr', 'col' => 4,],
            ['title' => '분류패킹', 'type' => 'html', 'name' => 'packingYnKr', 'col' => 4,],
            ['title' => '직접납품', 'type' => 'html', 'name' => 'directDeliveryYnKr', 'col' => 4,],

            ['title'=>'진행상태','type'=>'c','name'=>'projectStatusKr','col'=>5,],
            ['title' => '담당자', 'type' => 'c', 'name' => 'managerName', 'col' => 4,],
            //['title' => '메모', 'type' => 'c', 'name' => 'projectMemo', 'col' => 2,],
        ];
    }

    /**
     * 디자인 프로젝트 리스트
     * @param $params
     * @return array
     */
    public function getCompleteList($params)
    {
        if( 'project' === $params['viewType'] ){
            $allData = $this->preparedProjectList('all', 'P5,desc', $params);
            $fieldData = $this->getCompleteListField();
        }else{
            $isGroup=false;
            $isSchedule=false;
            $allData = $this->preparedProjectList('all', 'P5,desc', $params, $isGroup, $isSchedule);
            SlCommonUtil::setListRowSpan($allData['listData'], [
                'project'  => ['valueKey' => 'sno'], //projectRowspan (each , field)
            ], $params);
            $fieldData = $this->getQcField();
        }

        //판매가 가림
        if( !empty($_COOKIE['setSaleCostDisplay']) &&  'n' === $_COOKIE['setSaleCostDisplay'] ){
            $allData['totalData']['type1Cnt'] = 0;
            $allData['totalData']['type2Cnt'] = 0;
            $allData['totalData']['type3Cnt'] = 0;
            $allData['totalData']['type4Cnt'] = 0;
        }
        $allData['pageData']->type1Cnt = SlCommonUtil::numberToKorean($allData['totalData']['type1Cnt']); //매출
        $allData['pageData']->type2Cnt = SlCommonUtil::numberToKorean($allData['totalData']['type2Cnt']); //생산가
        $allData['pageData']->type3Cnt = SlCommonUtil::numberToKorean($allData['totalData']['type3Cnt']); //마진가격
        $allData['pageData']->type4Cnt = SlCommonUtil::numberToKorean($allData['totalData']['type4Cnt']); //마진율
        $allData['pageData']->typeAllCnt = $allData['totalData']['typeAllCnt'];

        SlCommonUtil::setColWidth(95, $fieldData);


        //--- Rowspan설정
        //$this->setProjectListRowspan($list);

        return [
            'pageEx' => $allData['pageData']->getPage('#'),
            'page' => $allData['pageData'],
            'list' => $allData['listData'],
            'fieldData' => $fieldData
        ];
    }

    /**
     * 검색 조건 설정
     * @param $condition
     * @param SearchVo $searchVo
     * @return mixed
     */
    public function setCompleteCondition($condition, SearchVo $searchVo){
        $searchVo->setWhere('prj.projectStatus not in (98,99)');

        $totalQueryList = [];

        if( 'project' === $condition['viewType'] ){
            $totalQueryList[] = "count( distinct projectSno ) as typeAllCnt"; //전체
            $totalQueryList[] = "sum(totalPrdPrice) as type1Cnt";
            $totalQueryList[] = "sum(totalPrdCost) as type2Cnt";
            $totalQueryList[] = "sum(totalPrdPrice) - sum(totalPrdCost) as type3Cnt";
            $totalQueryList[] = "round(100-(sum(totalPrdCost)/sum(totalPrdPrice)*100),0) as type4Cnt";
        }else{
            $totalQueryList[] = "count( 1 ) as typeAllCnt"; //전체
            $totalQueryList[] = "sum(salePrice*prdExQty) as type1Cnt";
            $totalQueryList[] = "sum(prdCost*prdExQty) as type2Cnt";
            $totalQueryList[] = "sum(salePrice*prdExQty) - sum(prdCost*prdExQty) as type3Cnt";
            $totalQueryList[] = "round(100-(sum(prdCost*prdExQty)/sum(salePrice*prdExQty)*100),0) as type4Cnt";
        }

        $searchVo->setAddTotalField(' , '.implode(',', $totalQueryList));
        return $this->setCommonCondition($condition, $searchVo);
    }

}


