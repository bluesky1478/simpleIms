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
Trait ImsProjectListDesignTrait
{
    /**
     * 기획/제작 : 디자인 ( 가망 고객 )
     * @return array[]
     */
    public function getDesignField()
    {
        return [
            ['title' => '타입', 'type' => 'c', 'name' => 'projectType', 'col' => 4,'rowspan'=>true],
            ['title' => '프로젝트', 'type' => 'c', 'name' => 'projectNo', 'col' => 14,'rowspan'=>true],
            ['title' => '고객납기', 'type' => 'd3', 'name' => 'customerDeliveryDt', 'col' => 5,'rowspan'=>true],
            ['title' => '발주D/L', 'type' => 'c', 'name' => 'productionOrder', 'col' => 5,'rowspan'=>true],

            ['title' => '구분', 'type' => 'c', 'name' => 'subTitle', 'col' => 3, 'class' => 'bg-light-gray' ], //예정일 타이틀
            ['title' => '구분', 'type' => 'c', 'name' => 'subTitle', 'col' => 3, 'subRow'=>true, 'class' => ''], //완료일 타이틀

            //예정
            ['title' => '기획', 'type' => 'expected2', 'name' => 'plan', 'col' => 4, 'class' => 'bg-light-yellow'],
            ['title' => '제안', 'type' => 'expected2', 'name' => 'proposal', 'col' => 4, 'class' => 'bg-light-yellow'],
            ['title' => '샘플지시서', 'type' => 'expected2', 'name' => 'sampleOrder', 'col' => 4, 'class' => 'bg-light-yellow'],
            ['title' => '샘플실완료', 'type' => 'expected2', 'name' => 'sampleComplete', 'col' => 4, 'class' => 'bg-light-yellow'],
            ['title' => '샘플리뷰', 'type' => 'expected2', 'name' => 'sampleReview', 'col' => 4, 'class' => 'bg-light-yellow'],
            ['title' => '샘플발송', 'type' => 'expected2', 'name' => 'sampleInform', 'col' => 4, 'class' => 'bg-light-yellow'],
            ['title' => '샘플확정', 'type' => 'expected2', 'name' => 'sampleConfirm', 'col' => 4, 'class' => 'bg-light-yellow'],
            ['title' => '작지/사양서', 'type' => 'expected2', 'name' => 'order', 'col' => 4, 'class' => 'bg-light-yellow'],

            //완료
            ['title' => '기획', 'type' => 'complete', 'name' => 'plan', 'col' => 4, 'subRow'=>true],
            ['title' => '제안', 'type' => 'complete', 'name' => 'proposal', 'col' => 4, 'subRow'=>true],
            ['title' => '샘플지시서', 'type' => 'complete', 'name' => 'sampleOrder', 'col' => 4, 'subRow'=>true],
            ['title' => '샘플실완료', 'type' => 'complete', 'name' => 'sampleComplete', 'col' => 4, 'subRow'=>true],
            ['title' => '샘플리뷰', 'type' => 'complete', 'name' => 'sampleReview', 'col' => 4, 'subRow'=>true],
            ['title' => '샘플발송', 'type' => 'complete', 'name' => 'sampleInform', 'col' => 4, 'subRow'=>true],
            ['title' => '샘플확정', 'type' => 'complete', 'name' => 'sampleConfirm', 'col' => 4, 'subRow'=>true],
            ['title' => '작지/사양서', 'type' => 'complete', 'name' => 'order', 'col' => 4, 'subRow'=>true],

            ['title' => '퀄리티', 'type' => 'c', 'name' => 'fabricStatus', 'col' => 2,'rowspan'=>true],
            ['title' => 'BT', 'type' => 'c', 'name' => 'btStatus', 'col' => 2,'rowspan'=>true],
            ['title' => '담당', 'type' => 'c', 'name' => 'managerInfo', 'col' => 7,'class'=>'ta-l font-11' ,'rowspan'=>true],

            /*['title' => '아소트', 'type' => 'c', 'name' => 'assort', 'col' => 2,'rowspan'=>true],
            ['title' => '판매가', 'type' => 'c', 'name' => 'prdPriceApproval', 'col' => 2,'rowspan'=>true],
            ['title' => '생산가', 'type' => 'c', 'name' => 'prdCostApproval', 'col' => 2,'rowspan'=>true],*/

            ['title'=>'상태','type'=>'c','name'=>'projectStatusKr','col'=>5,'rowspan'=>true],
        ];
    }

    /**
     * 디자인 프로젝트 리스트
     * @param $params
     * @return array
     */
    public function getDesignList($params)
    {
        $allData = $this->preparedProjectList('design', 'P5,desc', $params);

        $fieldData = $this->getDesignField();
        SlCommonUtil::setColWidth(95, $fieldData);

        $allData['pageData']->type1Cnt = $allData['totalData']['type1Cnt']; //기획
        $allData['pageData']->type2Cnt = $allData['totalData']['type2Cnt']; //제안
        $allData['pageData']->type3Cnt = $allData['totalData']['type3Cnt']; //샘플
        $allData['pageData']->type4Cnt = $allData['totalData']['type4Cnt']; //발주준비

        $allData['pageData']->typeAllCnt = $allData['totalData']['typeAllCnt'];

        //리스트 일괄수정에 쓰일 arr 정리
        $enableFld = [
            'sno', //update를 위해 무조건 넣어줘야 할 키값
            'exProductionOrder','exPlan','exProposal','exSampleOrder','exSampleComplete','exSampleReview','exSampleInform','exSampleConfirm','exOrder',
            'cpPlan','cpProposal','cpSampleOrder','cpSampleComplete','cpSampleReview','cpSampleInform','cpSampleConfirm','cpOrder',
            'customerDeliveryDt',
        ];
        $listData2 = []; //일괄수정에 쓰이는 리스트
        foreach ($allData['listData'] as $key => $val) {
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
            'list' => $allData['listData'],
            'listUpdateMulti' => $listData2,
            'listUpdateMultiOrigin' => $listData2,
            'fieldData' => $fieldData
        ];
    }

    /**
     * 디자인 검색 조건 설정
     * @param $condition
     * @param SearchVo $searchVo
     * @return mixed
     */
    public function setDesignCondition($condition, SearchVo $searchVo){
        $searchVo->setWhere('prj.projectStatus in (20,30,31,40,41,50,60,90)');
        //$searchVo->setWhere('prj.projectType in (0,2,5,6)');

        $totalQueryList = [];
        $totalQueryList[] = "count( case when projectStatus IN (20) then 1 else null end ) as type1Cnt"; //기획
        $totalQueryList[] = "count( case when projectStatus IN (30,31) then 1 else null end ) as type2Cnt"; //제안
        $totalQueryList[] = "count( case when projectStatus IN (40,41) then 1 else null end ) as type3Cnt"; //샘플
        $totalQueryList[] = "count( case when projectStatus IN (50) then 1 else null end ) as type4Cnt"; //발주준비
        $totalQueryList[] = "count( distinct projectSno ) as typeAllCnt"; //발주준비
        $searchVo->setAddTotalField(' , '.implode(',', $totalQueryList));
        return $this->setCommonCondition($condition, $searchVo);
    }

}


