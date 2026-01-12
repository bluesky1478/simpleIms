<?php
namespace Component\Ims;

use Component\Ims\EnumType\PREPARED_TYPE;
use SlComponent\Util\SitelabLogger;

/**
 * IMS 정렬처리
 * Class GoodsStock
 * @package Component\Goods
 */
trait ImsServiceSortNkTrait {
    /**
     * 견적 기본 구조 정보
     * @param $sort
     * @param $searchVo
     * @param null $params
     * @return array
     */
    public function setListSortNk($sort, &$searchVo, $params=null){
        $sortCondition = explode(',', $sort);
        $sortMap = [
            'A' => "binary custUsage.customerName {$sortCondition[1]}, a.fabricName {$sortCondition[1]}, a.fabricMix {$sortCondition[1]}, a.color {$sortCondition[1]}, b.inputDt asc", //자재정보(엑셀다운로드시 b.inputDt asc 쓰임(group by a.sno할때는 안쓰임))
            'B' => "isBookRegisteredDt {$sortCondition[1]}", //회계반영일(sl_imsProject)
            'C' => "c.regDt {$sortCondition[1]}", //3번째 테이블(출고)
            'D' => "a.regDt {$sortCondition[1]}, a.sno {$sortCondition[1]}", //main table 등록일시
            'CD' => "a.driveDt {$sortCondition[1]}, a.driveStartTime {$sortCondition[1]}", //차량 운행일자
            'CM' => "a.maintainDt {$sortCondition[1]}, a.sno {$sortCondition[1]}", //차량 정비일자
            'CN' => "cust.customerName {$sortCondition[1]}", //고객명
            'SN' => "productName {$sortCondition[1]}", //스타일명
            'BCN' => "parent.cateName {$sortCondition[1]}, a.cateName {$sortCondition[1]}", //업종명
            'SCCAD' => "max(contents.afterCallDt) {$sortCondition[1]}, a.sno {$sortCondition[1]}", //발굴고객 영업이력의 후속영업일자(후속연락일자)
        ];
        
        //파라미터로 가변적 정렬 조건 받게 처리
        if(!empty($params['extSortMap'])){
            $sortMap += $params['extSortMap'];
        }

        if (count($sortCondition) % 2 != 0) {
            $searchVo->setOrder('a.regDt desc');
        } else {
            $aSort = '';
            foreach ($sortCondition as $key => $val) {
                if ($key % 2 == 0) {
                    if (isset($sortMap[$val])) {
                        $aSort[] = $sortMap[$val];
                    } else {
                        $aSort[] = $val.' '.$sortCondition[$key+1];
                    }
                }
            }
            $searchVo->setOrder(implode(', ', $aSort));
        }
    }
}

