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
Trait ImsProjectListQcTrait
{
    /**
     * 발주
     * @return array[]
     */
    public function getQcField()
    {
        return [
            ['title' => '스타일', 'type' => 'prdStyle', 'name' => 'productName', 'col' => 10,'class' => 'ta-l'], //스타일
            ['title' => '생산처', 'type' => 's', 'name' => 'produceCompanyName', 'col' => 5,'class' => 'ta-l'], //스타일
            ['title' => 'Q/B','type'=>'c','name'=>'fabricStatusKr','col'=>5,],
            ['title' => '발주D/L', 'type' => 'c', 'name' => 'productionOrder', 'col' => 5,'rowspan'=>true ],
            ['title' => '생산기간', 'type' => 'c', 'name' => 'prdPeriod', 'col' => 3 ],
            ['title' => '수량', 'type' => 'i', 'name' => 'prdExQty', 'col' => 3 ],
            ['title' => '대표원단', 'type' => 'c', 'name' => 'repFabric', 'col' => 6 , 'class'=>'font-10 ta-l'],
            ['title' => '생산MOQ', 'type' => 'c', 'name' => 'prdMoq', 'col' => 3 , 'class'=>'ta-r'],
            ['title' => '단가MOQ', 'type' => 'c', 'name' => 'priceMoq', 'col' => 3, 'class'=>'ta-r' ],
            ['title' => '판매가', 'type' => 'c', 'name' => 'salePrice', 'col' => 4, 'class'=>'ta-r' ],
            ['title' => '생산가', 'type' => 'c', 'name' => 'prdCost', 'col' => 4, 'class'=>'ta-r' ],
            ['title' => '마진', 'type' => 's', 'name' => 'margin', 'col' => 2, 'valueSuffix'=>'%'],
            ['title' => '작지', 'type' => 'c', 'name' => 'workStatus', 'col' => 2, 'class'=>'' ],
        ];
    }

    /**
     * 생산처가 보는 발주 화면
     * @return array[]
     */
    public function getFactoryQcField()
    {
        return [
            ['title' => '스타일', 'type' => 'c', 'name' => 'factoryProductName', 'col' => 10,'class' => 'ta-l'], //스타일
            ['title' => '생산처', 'type' => 's', 'name' => 'produceCompanyName', 'col' => 5,'class' => 'ta-l'], //스타일
            ['title' => 'MS납기', 'type' => 'd1', 'name' => 'prdMsDeliveryDt', 'col' => 4],
            ['title' => '생산기간', 'type' => 'c', 'name' => 'prdPeriod', 'col' => 3 ],
            ['title' => '수량', 'type' => 'i', 'name' => 'prdExQty', 'col' => 3 ],
            ['title' => '대표원단', 'type' => 'c', 'name' => 'repFabric', 'col' => 6 , 'class'=>'font-10 ta-l'],
            ['title' => '생산MOQ', 'type' => 'c', 'name' => 'prdMoq', 'col' => 3 , 'class'=>'ta-r'],
            ['title' => '단가MOQ', 'type' => 'c', 'name' => 'priceMoq', 'col' => 3, 'class'=>'ta-r' ],
            ['title' => '생산가', 'type' => 'c', 'name' => 'prdCost', 'col' => 4, 'class'=>'ta-r' ],
            ['title' => '작지', 'type' => 'c', 'name' => 'factoryWorkStatus', 'col' => 2, 'class'=>'' ],
        ];
    }

    /**
     * 팝업에서 보는 발주 스타일 화면
     * @return array[]
     */
    public function getPopupQcField()
    {
        return [
            ['title' => '스타일명', 'type' => 'prdStyle', 'name' => 'productName', 'col' => 10,'class' => 'ta-l pdl10'], //스타일
            ['title' => '생산처', 'type' => 's', 'name' => 'produceCompanyName', 'col' => 5,'class' => 'ta-l'], //스타일
            ['title' => 'Q/B','type'=>'c','name'=>'fabricStatusKr','col'=>5,],
            ['title' => '생산기간', 'type' => 'c', 'name' => 'prdPeriod', 'col' => 3 ],
            ['title' => '수량', 'type' => 'i', 'name' => 'prdExQty', 'col' => 3 ],
            ['title' => '대표원단', 'type' => 'c', 'name' => 'repFabric', 'col' => 6 , 'class'=>'font-10 ta-l'],
            ['title' => '생산MOQ', 'type' => 'c', 'name' => 'prdMoq', 'col' => 3 , 'class'=>'ta-r'],
            ['title' => '단가MOQ', 'type' => 'c', 'name' => 'priceMoq', 'col' => 3, 'class'=>'ta-r' ],
            ['title' => '판매가', 'type' => 'c', 'name' => 'salePrice', 'col' => 4, 'class'=>'ta-r text-danger' ],
            ['title' => '생산가', 'type' => 'c', 'name' => 'prdCost', 'col' => 4, 'class'=>'ta-r sl-blue' ],
            ['title' => '마진', 'type' => 's', 'name' => 'margin', 'col' => 2, 'valueSuffix'=>'%'],
            ['title' => '작지', 'type' => 'c', 'name' => 'workStatus', 'col' => 2, 'class'=>'' ],
        ];
    }


    public function getQcList($params)
    {
        $isGroup=false;
        $isSchedule=false;
        $allData = $this->preparedProjectList('qc', 'P5,desc', $params, $isGroup, $isSchedule);

        //100 - 3 = 97 (check + 번호)

        if( SlCommonUtil::isFactory() ){
            $fieldData = $this->getFactoryQcField();
        }else{
            if('popup' == $params['viewType']){
                $fieldData = $this->getPopupQcField();
            }else{
                $fieldData = $this->getQcField();
            }
        }

        SlCommonUtil::setColWidth(95, $fieldData);


        $allData['pageData']->type1Cnt = $allData['totalData']['type1Cnt']; //신규
        $allData['pageData']->type2Cnt = $allData['totalData']['type2Cnt']; //리오더
        $allData['pageData']->type3Cnt = $allData['totalData']['type3Cnt']; //기성복

        $allData['pageData']->typeAllCnt = $allData['totalData']['typeAllCnt'];

        $list = SlCommonUtil::setEachData($allData['listData'], $this, 'decorationList');
        //--- Rowspan설정
        $this->setProductRowspan($list, $params);

        //리스트 일괄수정에 쓰일 arr 정리
        $enableFld = [
            'sno', 'styleSno', //update를 위해 무조건 넣어줘야 할 키값
            'exProductionOrder', 'prdCustomerDeliveryDt', 'prdMsDeliveryDt'
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
     * QC 발주리스트 검색 조건 설정
     * @param $condition
     * @param SearchVo $searchVo
     * @return mixed
     */
    public function setQcCondition($condition, SearchVo $searchVo){
        $totalQueryList = [];
        $totalQueryList[] = "count( distinct case when projectType IN (0,2,5,6) then projectSno else null end ) as type1Cnt"; //신규
        $totalQueryList[] = "count( distinct case when projectType IN (1,3,7) then projectSno else null end ) as type2Cnt"; //리오더
        $totalQueryList[] = "count( distinct case when projectType = 4 then projectSno else null end ) as type3Cnt"; //기성
        $totalQueryList[] = "count( distinct projectSno ) as typeAllCnt"; //전체
        $searchVo->setAddTotalField(' , '.implode(',', $totalQueryList));
        return $this->setCommonCondition($condition, $searchVo);
    }

}


