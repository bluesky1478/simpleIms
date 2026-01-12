<?php


namespace Component\Ims;


use SlComponent\Database\DBUtil2;

class ImsStoredServiceSql
{
    //비축 원부자재 간략리스트 table-info
    //사용소스 : 비축 원부자재 수정 페이지, 비축 원부자재 리스트 엑셀다운로드
    public function getStoredTableSimple($params=[]){
        $tableInfo=[
            'a' => //원부자재
                [
                    'data' => [ ImsDBName::STORED_FABRIC ]
                    , 'field' => ["sno,fabricName,fabricMix,color,customerUsageSno"],
                ],
        ];
        return DBUtil2::setTableInfo($tableInfo,false);
    }
    //비축 원부자재 리스트 table-info
    //사용소스 : 비축 원부자재 리스트 페이지
    public function getStoredTable($params=[]){
        $tableInfo=[
            'a' => //원부자재
                [
                    'data' => [ ImsDBName::STORED_FABRIC ]
                    , 'field' => ["a.customerUsageSno, a.sno, a.fabricName, a.fabricMix, a.color"]
                ],
            'b' => //입고(1:N)
                [
                    'data' => [ ImsDBName::STORED_FABRIC_INPUT, 'RIGHT OUTER JOIN', 'a.sno = b.fabricSno' ]
                    , 'field' => ['group_concat(b.sno order by b.inputDt separator "___namku___") as inputSno, group_concat(b.customerSno order by b.inputDt separator "___namku___") as customerSno, group_concat(b.unitPrice order by b.inputDt separator "___namku___") as unitPrice, group_concat(b.inputQty order by b.inputDt separator "___namku___") as inputQty, group_concat(b.inputUnit order by b.inputDt separator "___namku___") as inputUnit, group_concat(b.inputReason order by b.inputDt separator "___namku___") as inputReason, group_concat(b.inputOwn order by b.inputDt separator "___namku___") as inputOwn, group_concat(b.inputLocation order by b.inputDt separator "___namku___") as inputLocation, group_concat(b.inputMemo order by b.inputDt separator "___namku___") as inputMemo, group_concat(b.inputDt order by b.inputDt separator "___namku___") as inputDt, group_concat(b.expireDt order by b.inputDt separator "___namku___") as expireDt']
                ],
            'custUsage' => //고객(사용처)
                [
                    'data' => [ ImsDBName::CUSTOMER, 'LEFT OUTER JOIN', 'a.customerUsageSno = custUsage.sno' ]
                    , 'field' => ['custUsage.customerName as customerUsageName']
                ],
//            'cust' => //소유고객 sl_imsStoredFabricInput.customerSno : 안쓰는 컬럼
//                [
//                    'data' => [ ImsDBName::CUSTOMER, 'LEFT OUTER JOIN', 'b.customerSno = cust.sno' ]
//                    , 'field' => ['group_concat(cust.customerName order by b.inputDt separator "___namku___") as customerName']
//                ],
            'input_manager' => //등록 직원
                [
                    'data' => [ DB_MANAGER, 'LEFT OUTER JOIN', 'b.regManagerSno = input_manager.sno' ]
                    , 'field' => ['group_concat(input_manager.managerNm order by b.inputDt separator "___namku___") as reqInputNm']
                ]
        ];

        if( !empty($params['condition']['addJoinTable']) ){
            $tableInfo = array_merge($tableInfo, $params['condition']['addJoinTable']);
        }

        return DBUtil2::setTableInfo($tableInfo,false);
    }

    //원단정보, 입고정보, 출고수량 가져오는 table-info(입고건별 : gropu by b.sno)
    //사용소스 : 비축 원부자재 리스트 페이지(입고건별 출고수량 합산값 활용), 고객상세창-자재리스트 탭메뉴(리스트 활용), 입고수정 페이지(입고건정보 활용), 출고등록 페이지(입고건의 출고수량 합산값 활용), 출고건 수정 페이지(입고건의 출고수량 합산값 활용)
    public function getStoredInputTable(){
        $tableInfo=[
            'a' => //원부자재
                [
                    'data' => [ ImsDBName::STORED_FABRIC ]
                    , 'field' => ["a.customerUsageSno, a.fabricName, a.fabricMix, a.color"]
                ],
            'b' => //입고(1:N)
                [
                    'data' => [ ImsDBName::STORED_FABRIC_INPUT, 'LEFT OUTER JOIN', 'a.sno = b.fabricSno' ]
                    , 'field' => ['b.sno, b.fabricSno, b.customerSno, b.unitPrice, b.inputQty, b.inputUnit, b.inputReason, b.inputOwn, b.inputLocation, b.inputMemo, b.inputDt, b.expireDt']
                ],
            'c' => //출고(1:N)
                [
                    'data' => [ ImsDBName::STORED_FABRIC_OUT, 'LEFT OUTER JOIN', 'b.sno = c.inputSno and c.delFl = "n"' ]
                    , 'field' => ['sum(outQty) as outQty']
                ],
            'custUsage' => //고객(사용처)
                [
                    'data' => [ ImsDBName::CUSTOMER, 'LEFT OUTER JOIN', 'a.customerUsageSno = custUsage.sno' ]
                    , 'field' => ['custUsage.customerName as customerUsageName']
                ],
//            'cust' => //소유고객 sl_imsStoredFabricInput.customerSno : 안쓰는 컬럼
//                [
//                    'data' => [ ImsDBName::CUSTOMER, 'LEFT OUTER JOIN', 'b.customerSno = cust.sno' ]
//                    , 'field' => ['cust.customerName']
//                ],
            'input_manager' => //등록 직원
                [
                    'data' => [ DB_MANAGER, 'LEFT OUTER JOIN', 'b.regManagerSno = input_manager.sno' ]
                    , 'field' => ['input_manager.managerNm as reqInputNm']
                ]
        ];

        return DBUtil2::setTableInfo($tableInfo,false);
    }

    //출고리스트 table-info
    //사용소스 : 출고리스트 페이지
    public function getStoredOutputTable() {
        $tableInfo=[
            'a' => //원부자재
                [
                    'data' => [ ImsDBName::STORED_FABRIC ]
                    , 'field' => ["a.customerUsageSno, a.fabricName, a.fabricMix, a.color"]
                ],
            'b' => //입고(1:N)
                [
                    'data' => [ ImsDBName::STORED_FABRIC_INPUT, 'LEFT OUTER JOIN', 'a.sno = b.fabricSno' ]
                    , 'field' => ['b.sno, b.inputQty, b.inputOwn']
                ],
            'c' => //출고(1:N)
                [
                    'data' => [ ImsDBName::STORED_FABRIC_OUT, 'RIGHT OUTER JOIN', 'b.sno = c.inputSno and c.delFl = "n"' ]
                    , 'field' => ['c.sno as outputSno, outQty, substring(c.regDt,1,10) as outputDt, outReason']
                ],
//            'cust' => //소유고객 sl_imsStoredFabricInput.customerSno : 안쓰는 컬럼
//                [
//                    'data' => [ ImsDBName::CUSTOMER, 'LEFT OUTER JOIN', 'b.customerSno = cust.sno' ]
//                    , 'field' => ['cust.customerName']
//                ],
            'custUsage' => //고객(사용처)
                [
                    'data' => [ ImsDBName::CUSTOMER, 'LEFT OUTER JOIN', 'a.customerUsageSno = custUsage.sno' ]
                    , 'field' => ['custUsage.customerName as customerUsageName']
                ],
            'input_manager' => //출고 등록 직원
                [
                    'data' => [ DB_MANAGER, 'LEFT OUTER JOIN', 'c.regManagerSno = input_manager.sno' ]
                    , 'field' => ['input_manager.managerNm as reqInputNm']
                ]
        ];

        return DBUtil2::setTableInfo($tableInfo,false);
    }
}