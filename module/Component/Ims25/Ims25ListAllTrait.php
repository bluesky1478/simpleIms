<?php

namespace Component\Ims25;

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
Trait Ims25ListAllTrait
{
    /**
     * 전체 리스트
     * @return array[]
     */
    public function getAllListField()
    {
        return [
            ['title' => '진행상태','type'=>'c','name'=>'projectStatusKr','col'=>4,'rowspan'=>true],
            ['title' => '프로젝트 타입', 'type' => 'c', 'name' => 'projectType', 'col' => 5,'rowspan'=>true],
            ['title' => '고객', 'type' => 'c', 'name' => 'customer', 'col' => 10,'rowspan'=>true],
            ['title' => '프로젝트/스타일', 'type' => 'c', 'name' => 'project', 'col' => 13,'rowspan'=>true],
            /*['title' => '담당/참여', 'type' => 'c', 'name' => 'managerName', 'col' => 10,'rowspan'=>true],*/

            ['title' => '매출정보', 'type' => 'c', 'name' => 'salesInfo', 'col' => 6,'rowspan'=>true],
            ['title' => '고객납기', 'type' => 'c', 'name' => 'customerDeliveryDt', 'col' => 5,'rowspan'=>true],
            ['title' => '발주D/L', 'type' => 'c', 'name' => 'productionOrder', 'col' => 5,'rowspan'=>true],

            ['title' => '구분', 'type' => 'c', 'name' => 'expectedTitle', 'col' => 3, 'class' => 'bg-light-gray2' ], //예정일 타이틀
            ['title' => '구분', 'type' => 'c', 'name' => 'completeTitle', 'col' => 3, 'subRow'=>true, 'class' => ''], //완료일 타이틀

            //예정 (첫번째 TR) #######################################################################################
            ['title' => '사전기획', 'type' => 'schedule'      , 'name' => 'salesReadyPlan', 'col' => 3, 'class' => 'bg-light-yellow'],
            ['title' => '미팅/입찰설명회', 'type' => 'schedule', 'name' => 'meeting', 'col' => 3, 'class' => 'bg-light-yellow'],
            ['title' => '제안', 'type' => 'schedule'          , 'name' => 'meetingProposal', 'col' => 3, 'class' => 'bg-light-yellow'],
            ['title' => '샘플 제안/발송', 'type' => 'schedule' , 'name' => 'sampleInform', 'col' => 3, 'class' => 'bg-light-yellow'],
            ['title' => '발주', 'type' => 'schedule'          , 'name' => 'productionOrder', 'col' => 3, 'class' => 'bg-light-yellow'],
            //######################################################################################################

            //완료 (두번째 TR) #######################################################################################
            ['title' => '기획', 'type' => 'schedule'          , 'name' => 'salesReadyPlan', 'col' => 3, 'subRow'=>true],
            ['title' => '미팅/입찰설명회', 'type' => 'schedule', 'name' => 'meeting', 'col' => 3, 'subRow'=>true],
            ['title' => '제안', 'type' => 'schedule'          , 'name' => 'meetingProposal', 'col' => 3, 'subRow'=>true],
            ['title' => '샘플 제안/발송', 'type' => 'schedule' , 'name' => 'sampleInform', 'col' => 3, 'subRow'=>true],
            ['title' => '발주', 'type' => 'schedule'          , 'name' => 'productionOrder', 'col' => 3, 'subRow'=>true],
            //######################################################################################################

            //['title' => '메모', 'type' => 'c', 'name' => 'projectMemo', 'col' => 2,'rowspan'=>true],
        ];
    }

    /**
     * 리스트 조건 설정
     * @param $condition
     * @param $searchVo
     * @return mixed
     */
    public function setAllCondition($condition, $searchVo){
        return $this->setIms25CommonCondition($condition, $searchVo);
    }

    /**
     * SearchVo 정렬 설정
     * @param $sortCondition
     * @param $searchVo
     */
    public function setAllSort($sortCondition, &$searchVo){
        //공통 정렬
        $this->setListSort(gd_isset($sortCondition,'P1,desc'), $searchVo);
    }

    /**
     * 전체 리스트
     * @param SearchVo $searchVo
     * @param $searchData
     * @return mixed
     */
    public function getIms25AllList(SearchVo $searchVo, $searchData){
        $tableInfo = $this->sql->getIms25ListSql();
        $list = DBUtil2::getComplexListWithPaging($tableInfo, $searchVo, $searchData, false, false);
        return [
            'pageEx' => $list['pageData']->getPage('#'),
            'page' => $list['pageData'],
            'list' => $list['listData'],
            'fieldData' => $this->getAllListField()
        ];
    }

    /**
     * 전체 리스트 (with Style)
     * @param SearchVo $searchVo
     * @param $searchData
     * @return mixed
     */
    public function getIms25StyleList(SearchVo $searchVo, $searchData){
        $tableInfo = $this->sql->getIms25ListSql(true);
        $list = DBUtil2::getComplexListWithPaging($tableInfo, $searchVo, $searchData, false, false);
        return [
            'pageEx' => $list['pageData']->getPage('#'),
            'page' => $list['pageData'],
            'list' => $list['listData'],
            'fieldData' => $this->getAllListField()
        ];
    }

}


