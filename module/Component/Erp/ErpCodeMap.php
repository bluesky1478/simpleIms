<?php
namespace Component\Erp;

/**
 * Sitelab Code Key List
 * Class SlCode
 * @package SlComponent\Util
 */
class ErpCodeMap {

    const ERP_STOCK_TYPE = [
        '입고' => 1,
        '출고' => 2
    ];
    
    const ERP_STOCK_REASON = [
        '정기입고'=>1,
        '정기출고'=>2,
        '교환입고'=>3,
        '교환출고'=>4,
        '샘플입고'=>5,
        '샘플출고'=>6,
        '반품입고'=>7,
        '기타입고'=>91,
        '기타출고'=>92,
    ];

    const WAREHOUSE_RETURN = [
        1 => '접수',
        2 => '접수확인',
        3 => '회수완료',
    ];

    const WAREHOUSE_RETURN_PRD = [
        1 => '확인대기',
        2 => '최상',
        3 => '양호',
        4 => '상태불량',
    ];

}


