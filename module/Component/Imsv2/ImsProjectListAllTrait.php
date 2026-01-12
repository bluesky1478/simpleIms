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
Trait ImsProjectListAllTrait
{
    /**
     * 전체 리스트
     * @return array[]
     */
    public function getAllListField()
    {
        return [
            ['title' => '타입', 'type' => 'c', 'name' => 'projectType', 'col' => 5,'rowspan'=>true],
            ['title' => '프로젝트', 'type' => 'c', 'name' => 'projectNo', 'col' => 13,'rowspan'=>true],

            ['title' => '매출정보', 'type' => 'c', 'name' => 'salesInfo', 'col' => 6,'rowspan'=>true],
            ['title' => '고객납기', 'type' => 'c', 'name' => 'customerDeliveryDt', 'col' => 6,'rowspan'=>true],
            ['title' => '발주D/L', 'type' => 'c', 'name' => 'productionOrder', 'col' => 6,'rowspan'=>true],

            ['title' => '구분', 'type' => 'c', 'name' => 'subTitle', 'col' => 3, 'class' => 'bg-light-gray2' ], //예정일 타이틀
            ['title' => '구분', 'type' => 'c', 'name' => 'subTitle', 'col' => 3, 'subRow'=>true, 'class' => ''], //완료일 타이틀

            //예정
            //['title' => '미팅', 'type' => 'expected2', 'name' => 'meeting', 'col' => 4, 'class' => 'bg-light-yellow'],
            ['title' => '기획', 'type' => 'expected2', 'name' => 'plan', 'col' => 4, 'class' => 'bg-light-yellow'],
            ['title' => '제안', 'type' => 'expected2', 'name' => 'proposal', 'col' => 4, 'class' => 'bg-light-yellow'],
            ['title' => '샘플발송', 'type' => 'expected2', 'name' => 'sampleInform', 'col' => 4, 'class' => 'bg-light-yellow'],
            ['title' => '작지/사양서', 'type' => 'expected2', 'name' => 'order', 'col' => 4, 'class' => 'bg-light-yellow'],
            ['title' => '발주', 'type' => 'expected2', 'name' => 'productionOrder', 'col' => 4, 'class' => 'bg-light-yellow'],

            //완료
            //['title' => '미팅', 'type' => 'complete', 'name' => 'meeting', 'col' => 4, 'subRow'=>true],
            ['title' => '기획', 'type' => 'complete', 'name' => 'plan', 'col' => 4, 'subRow'=>true],
            ['title' => '제안', 'type' => 'complete', 'name' => 'proposal', 'col' => 4, 'subRow'=>true],
            ['title' => '샘플', 'type' => 'complete', 'name' => 'sampleInform', 'col' => 4, 'subRow'=>true],
            ['title' => '작지/사양서', 'type' => 'complete', 'name' => 'order', 'col' => 4, 'subRow'=>true],
            ['title' => '발주', 'type' => 'complete', 'name' => 'productionOrder', 'col' => 4, 'subRow'=>true],

            ['title' => '필수상태', 'type' => 'c', 'name' => 'requiredStatus', 'col' => 4,'rowspan'=>true],
            ['title' => '부가서비스', 'type' => 'c', 'name' => 'addService', 'col' => 6,'rowspan'=>true],
            ['title' => '진행상태','type'=>'c','name'=>'projectStatusKr','col'=>5,'rowspan'=>true],
            ['title' => '담당자', 'type' => 'c', 'name' => 'managerName', 'col' => 4,'rowspan'=>true],
            //['title' => '메모', 'type' => 'c', 'name' => 'projectMemo', 'col' => 2,'rowspan'=>true],
        ];
    }

    /**
     * 디자인 프로젝트 리스트
     * @param $params
     * @return array
     */
    public function getAllList($params)
    {
        if( 'project' === $params['viewType'] ){
            $allData = $this->preparedProjectList('all', 'P5,desc', $params);
            $fieldData = $this->getAllListField();
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

        //리스트 일괄수정에 쓰일 arr 정리
        //완료일을 수정할 수 없는 스케쥴 == ['plan','proposal','order','productionOrder'] == ['기획','제안','작지/사양서','발주']
        $enableFld = [
            'sno', //update를 위해 무조건 넣어줘야 할 키값
            'customerDeliveryDt', 'exProductionOrder', 'exPlan', 'exProposal', 'exSampleInform', 'exOrder', 'cpSampleInform',
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
     * 검색 조건 설정
     * @param $condition
     * @param SearchVo $searchVo
     * @return mixed
     */
    public function setAllCondition($condition, SearchVo $searchVo){
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


