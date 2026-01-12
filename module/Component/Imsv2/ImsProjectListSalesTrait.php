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
use SlComponent\Database\TableVo;
use SlComponent\Util\SitelabLogger;

/**
 * IMS 프로젝트 리스트 서비스
 * Class GoodsStock
 * @package Component\Goods
 */
Trait ImsProjectListSalesTrait
{
    /**
     * 영업 ( 전체 )
     * @return array[]
     */
    public function getSalesField(){
        return [
            ['title'=>'업종','type'=>'html','name'=>'bizCateName','col'=>0,'rowspan'=>true],
            ['title'=>'고객사 프로젝트','type'=>'complexProject','name'=>'projectNo','col'=>13,'rowspan'=>true],
            ['title'=>'매출목표<br/>계약난이도','type'=>'c','name'=>'targetSalesYear','col'=>0,'rowspan'=>true],
            ['title'=>'추정매출','type'=>'s','name'=>'totalTargetPriceKr','col'=>0,'rowspan'=>true],
            ['title'=>'예상마진','type'=>'c','name'=>'totalTargetMarginKr','col'=>0,'rowspan'=>true],
            ['title'=>'프로젝트 타입','type'=>'c','name'=>'projectTypeKr','col'=>5,'rowspan'=>true],
            ['title'=>'업무 구분','type'=>'c','name'=>'designWorkTypeKr','col'=>5,'rowspan'=>true],
            ['title'=>'담당자','type'=>'c','name'=>'salesManagerNm','col'=>8,'default'=>'미지정','rowspan'=>true],

            ['title'=>'구분','type'=>'fldText','name'=>'예정일','col'=>0, 'class' => 'bg-light-gray'],
            ['title'=>'구분','type'=>'fldText','name'=>'상태','col'=>0, 'subRow'=>true],
            ['title'=>'담당자 컨텍','type'=>'expected2','name'=>'contactManager','col'=>5, 'class' => 'bg-light-yellow'],
            ['title'=>'담당자 컨텍','type'=>'complete','name'=>'contactManager','col'=>5, 'subRow'=>true],
            ['title'=>'사전미팅','type'=>'expected2','name'=>'meetingReady','col'=>5, 'class' => 'bg-light-yellow'],
            ['title'=>'사전미팅','type'=>'complete','name'=>'meetingReady','col'=>5, 'subRow'=>true],
            ['title'=>'샘플 확보','type'=>'expected2','name'=>'sampleCust','col'=>5, 'class' => 'bg-light-yellow'],
            ['title'=>'샘플 확보','type'=>'complete','name'=>'sampleCust','col'=>5, 'subRow'=>true],
            ['title'=>'현장 리서치','type'=>'expected2','name'=>'researchField','col'=>5, 'class' => 'bg-light-yellow'],
            ['title'=>'현장 리서치','type'=>'complete','name'=>'researchField','col'=>5, 'subRow'=>true],
            ['title'=>'샘플 제작','type'=>'expected2','name'=>'sampleProduce','col'=>5, 'class' => 'bg-light-yellow'],
            ['title'=>'샘플 제작','type'=>'complete','name'=>'sampleProduce','col'=>5, 'subRow'=>true],
            ['title'=>'샘플 현장<br/>테스트','type'=>'expected2','name'=>'sampleTest','col'=>5, 'class' => 'bg-light-yellow'],
            ['title'=>'샘플 현장<br/>테스트','type'=>'complete','name'=>'sampleTest','col'=>5, 'subRow'=>true],
            ['title'=>'제안미팅','type'=>'expected2','name'=>'meetingProposal','col'=>5, 'class' => 'bg-light-yellow'],
            ['title'=>'제안미팅','type'=>'schedule','name'=>'meetingProposal','col'=>5, 'subRow'=>true],
            ['title'=>'진행상태','type'=>'c','name'=>'salesStatusKr','col'=>0,'rowspan'=>true], //,'colspan'=>2
            ['title'=>'고객<br>코멘트','type'=>'c','name'=>'salesView','col'=>2,'rowspan'=>true],
        ];
    }

    /**
     * <찍어낼 수 있어야 함>
     * 영업 프로젝트 리스트
     * @param $params
     * @return array
     */
    public function getSalesList($params)
    {
        $allData = $this->preparedProjectList('sales', 'P5,desc', $params);

        //100 - 3 = 97 (check + 번호)
        $fieldData = $this->getSalesField(); //전체 리스트 필드
        SlCommonUtil::setColWidth(95, $fieldData);

        $fieldTab1 = ImsFieldUtil::getSalesTab2(); //탭1:영업대기 필드
        SlCommonUtil::setColWidth(95, $fieldTab1);

        $allData['pageData']->type1Cnt = $allData['totalData']['type1Cnt'];
        $allData['pageData']->type1WaitCnt = $allData['totalData']['type1WaitCnt'];
        $allData['pageData']->type1ReadyCnt = $allData['totalData']['type1ReadyCnt'];
        $allData['pageData']->type2Cnt = $allData['totalData']['type2Cnt'];
        $allData['pageData']->type2WaitCnt = $allData['totalData']['type2WaitCnt'];
        $allData['pageData']->type2ReadyCnt = $allData['totalData']['type2ReadyCnt'];
        $allData['pageData']->type3Cnt = $allData['totalData']['type3Cnt'];
        $allData['pageData']->type3WaitCnt = $allData['totalData']['type3WaitCnt'];
        $allData['pageData']->type3ReadyCnt = $allData['totalData']['type3ReadyCnt'];

        $allData['pageData']->typeImpCnt = $allData['totalData']['typeImpCnt']; //기획불가

        $listData = SlCommonUtil::setEachData($allData['listData'], $this, 'decorationList');
        $listData2 = []; //영업 리스트 -> 일괄수정에 쓰이는 프로젝트 리스트
        //현재 유효한 영업사원, 디자인사원 가져오기(프로젝트 등록시 정리한 내용 활용(setDefault()))
        $searchVo = new SearchVo('scmNo=?','1');
        $searchVo->setWhere('isDelete=?');
        $searchVo->setWhereValue('n');
        $managerList = DBUtil2::getListBySearchVo(new TableVo(DB_MANAGER, 'tableManagerWithSno') , $searchVo);
        $deptManager = [];
        foreach( $managerList as $manager ){
            if(!empty($manager['departmentCd'])){
                $deptManager[$manager['departmentCd']][$manager['sno']] = $manager['managerNm'];
            }
        }
        $salesManagerList = $deptManager['02001001'];
        $designManagerList = $deptManager['02001002'];
        //listUpdateMulti 구성
        $enableFld = [
            'sno', 'targetSalesYear', 'contractDifficult', 'projectType', 'designWorkType',
            'exContactManager', 'cpContactManager', 'exMeetingReady', 'cpMeetingReady', 'exSampleCust', 'cpSampleCust', 'exResearchField', 'cpResearchField', 'exSampleProduce', 'cpSampleProduce', 'exSampleTest', 'cpSampleTest', 'exMeetingProposal', 'cpMeetingProposal',
            'salesManagerSno', 'designManagerSno', 'customerSno'
        ];
        foreach ($listData as $key => $val) {
            $aTmp = [];
            foreach ($val as $key2 => $val2) {
                if (in_array($key2, $enableFld)) {
                    $tmpVal = $val2 === null ? '' : $val2;
                    if ($key2 == 'projectType' && !in_array($val2, \Component\Ims\ImsCodeMap::PROJECT_TYPE_N)) { //프로젝트 타입 예전값이면
                        $tmpVal = '';
                    } elseif ($key2 == 'salesManagerSno' && !isset($salesManagerList[$val2])) { //선택된 영업사원이 유효한 직원이 아니라면
                        $tmpVal = '0';
                    } elseif ($key2 == 'designManagerSno' && !isset($designManagerList[$val2])) { //선택된 영업사원이 유효한 직원이 아니라면
                        $tmpVal = '0';
                    }
                    $aTmp[$key2] = $tmpVal;
                }
            }
            $listData2[$key] = $aTmp;
        }
        return [
            'pageEx' => $allData['pageData']->getPage('#'),
            'page' => $allData['pageData'],
            'list' => $listData,
            'listUpdateMulti' => $listData2,
            'listUpdateMultiOrigin' => $listData2,
            'fieldData' => $fieldData,
            'fieldAnother' => [
                'tab1' => $fieldTab1,
                'tab2' => $fieldTab1,
            ],
        ];
    }

    /**
     * 영업 검색 조건 설정
     * @param $condition
     * @param SearchVo $searchVo
     * @return mixed
     */
    public function setSalesCondition($condition, SearchVo $searchVo){
        //영업완료가 생성된 데이터만 취급
        $searchVo->setWhere('ext.salesStatus is not null');
        if( 'all' !== $condition['bidType2'] && !empty($condition['bidType2']) ){
            $searchVo->setWhere('prj.bidType2=?');
            $searchVo->setWhereValue($condition['bidType2']);
        }

        //TOTAL 데이터
        $totalQueryList = [];
        $totalQueryList[] = "count( case when 'bid' = bidType2 then 1 else null end ) as type1Cnt";
        $totalQueryList[] = "count( case when 'bid' = bidType2 and 'wait'  = salesStatus then 1 else null end )  as type1WaitCnt";
        $totalQueryList[] = "count( case when 'bid' = bidType2 and 'ready' = salesStatus then 1 else null end )  as type1ReadyCnt";
        $totalQueryList[] = "count( case when 'costBid' = bidType2 then 1 else null end ) as type2Cnt";
        $totalQueryList[] = "count( case when 'costBid' = bidType2 and 'wait'  = salesStatus then 1 else null end )  as type2WaitCnt";
        $totalQueryList[] = "count( case when 'costBid' = bidType2 and 'ready' = salesStatus then 1 else null end )  as type2ReadyCnt";
        $totalQueryList[] = "count( case when 'single' = bidType2 then 1 else null end ) as type3Cnt";
        $totalQueryList[] = "count( case when 'single' = bidType2 and 'wait'  = salesStatus then 1 else null end )  as type3WaitCnt";
        $totalQueryList[] = "count( case when 'single' = bidType2 and 'ready' = salesStatus then 1 else null end )  as type3ReadyCnt";

        $totalQueryList[] = "count( case when 'imp' = salesStatus then 1 else null end )  as typeImpCnt"; //기획불가
        $searchVo->setAddTotalField(' , '.implode(',', $totalQueryList));

        return $this->setCommonCondition($condition, $searchVo);
    }


    /**
     * 영업 관련 다른 리스트
     * @param $params
     * @return mixed
     */
    public function getSalesAnotherList($params){
        $fieldTab2 = ImsFieldUtil::getSalesTab2(); //탭1:영업대기 필드 TODO : tab타입에 따른 각자의 필드 필요
        SlCommonUtil::setColWidth(95, $fieldTab1);
        $projectSearchVo = $this->setCommonCondition($params, new SearchVo()); //공통 검색 조건
        $this->setListSort(gd_isset($params['sort'],'P1,desc'), $projectSearchVo); //정렬
        //추가 검색 필요시 지정
        
        //리스트 테이블
        $tableInfo = $this->salesSql->getSalesListSql($projectSearchVo);
        $list = DBUtil2::getComplexList($tableInfo, $projectSearchVo);
        $list = SlCommonUtil::setEachData($list, $this, 'decorationList');

        return [
            'field' => $fieldTab2,
            'list' => $list,
        ];
    }


}