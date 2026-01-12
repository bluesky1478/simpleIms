<?php
namespace Component\Ims;

/**
 * Sitelab Code Key List - Namkyu
 * Class SlCode
 * @package SlComponent\Util
 */
class NkCodeMap {
    //게시물 수정이력 DB TABLE 타입(향후 반영시킬 게시물 늘어나면 추가할것)
    const UPDATE_HISTORY_TABLE_TYPE = [
        1 => '프로젝트/스타일 이슈',
    ];

    //원부자재(원단) 관련 DB Code
    const STORED_INPUT_UNIT = [
        'YD' => 'YD', 'EA' => 'EA', 'SET' => 'SET',
    ];
    const STORED_INPUT_OWN = [
        1 => '하나어패럴', 2 => '이노버', 3 => '고객',
    ];
    const MATERIAL_TYPE = [
        1 => '원단', 2 => '충전재', 3 => '부자재', 4 => '마크', 5 => '기능',
    ];
    const MATERIAL_ST = [
        1 => '정상', 2 => '품절', 3 => '단종',
    ];

    const MATERIAL_USED_STYLE = [
        1 => '점퍼',
        2 => '조끼',
        4 => '티셔츠',
        8 => '바지',
    ];
    const MATERIAL_UNIT = [
        'YD' => 'YD', 'KG' => 'KG', 'EA' => 'EA', 'SET' => 'SET',
    ];
    const CURRENCY_UNIT = [
        1 => '원화', 2 => '달러',
    ];
    const MATERIAL_BT_YN = [
        'y' => '완료', 'n' => '미완료',
    ];
    const MATERIAL_ON_HAND = [
        'y' => '생지있음', 'n' => '생지없음', 'c' => '확인중',
    ];
    const ADDED_BS_TYPE = [
        1 => '판매', 2 => '구매',
    ];
    //프로젝트 최초기획 유형
    const PROJECT_PLAN_SCHE_TYPE = [
        1 => '예정일', 2 => '의사결정요청일',
    ];
    //프로젝트 최초기획 단계
    const PROJECT_PLAN_SCHE_STEP = [
        1 => '기획', 2 => '제안서',
        3 => '샘플 제작 요청', 4 => 'Q/B',
        5 => '샘플 제안', 6 => '샘플/발주 확정',
        7 => '발주', 8 => '발주 D/L',
        9 => '이노버 납기', 10 => '납품',
    ];

    //스타일기획 발주수량변동
    const PRODUCT_PLAN_CHANGE_QTY = [
        1 => '미확인', 2 => '있음', 3 => '없음'
    ];
    const PRODUCT_PLAN_GENDER = [
        '' => '공용', 'M' => '남자', 'F' => '여자'
    ];
    //스타일기획 상태
    const PRODUCT_PLAN_ST = [
        1 => '일반', 2 => '확정',
    ];

    //프로젝트/스타일 이슈 유형
    const PROJECT_ISSUE_TYPE = [
        1 => '품질', 2 => '생산', 3 => '물류', 4 => '발주', 5 => '기타',
    ];
    //프로젝트/스타일 이슈 상태
    const PROJECT_ISSUE_ST = [
        1 => '접수', 2 => '조치중', 3 => '종결',
    ];

    //샘플구분
    const SAMPLE_TYPE = [
        1 => '제안', 2 => 'MS 제안', 3 => '고객 추가', 4 => '고객 수정', 5 => '불량 수정(S)', 6 => '불량수정 (D)',
    ];

    //스타일기획,샘플 기타비용의 유형
    const SAMPLE_ETC_COST_TYPE = [
        1 => '공임비용', 2 => '기타비용',
    ];

    //차량관리 구분값들
    const ETC_CAR_TYPE = [
        '리스(업무용승용차)' => '리스(업무용승용차)', '직원소유' => '직원소유', '타인소유' => '타인소유', '회사소유' => '회사소유',
    ];
    const ETC_CAR_MAINTAIN_TYPE = [
        '세차'=>'세차', '엔진오일교체'=>'엔진오일교체', '타이어교체'=>'타이어교체', '파손수리'=>'파손수리', '종합검사'=>'종합검사',
    ];
    const ETC_CAR_ADDR_TYPE = [
        '회사' => '회사', '거래처' => '거래처',
    ];
    const ETC_CAR_DRIVE_TYPE = [
        '거래처방문' => '거래처방문', '제조시설등 사업자방문' => '제조시설등 사업자방문',
    ];

    //영업관리(고객발굴) sl_salesCustomerInfo
    const SALES_CUST_TYPE = [
//        0 => '미선택',
        10 => '잠재고객',
        20 => '관심고객',
        30 => '가망고객',
//        40 => '기타고객',
        50 => '발굴완료',
//        80 => '미팅고객(진행)',
//        90 => '미팅고객(계약)',
//        99 => '미팅고객(이탈)',
    ];
    const SALES_CUST_BUY_METHOD = [
        '입찰' => '입찰', '비딩' => '비딩', '단독' => '단독'
    ];
    const SALES_CUST_BUY_DIV = [
        '제작복' => '제작복', '기성복' => '기성복'
    ];
    //영업관리(고객발굴)확장 sl_salesCustomerInfoExt
    const SALES_CUST_CONTACT_TYPE = [
        '대표번호' => '대표번호', '우회 연결' => '우회 연결'
    ];
    const SALES_CUST_CUSTOMER_NEEDS = [
        1 => '미확인', 2 => '신규 디자인', 3 => '원가 절감', 4 => '기능 개선', 5 => '소재 개선'
    ];
    const SALES_CUST_MALL_INTEREST = [
        1 => '미확인', 2 => '높음', 3 => '보통', 4 => '낮음'
    ];
    //영업관리(고객발굴) -> 영업이력 관련 sl_salesCustomerContents
    const SALES_CUST_CONTENTS_TYPE = [
        1 => 'TM', 2 => 'EM'
    ];
    const SALES_CUST_CONTENTS_AFTER_CALL_REASON = [
        1 => '없음', 2 => '담당자 부재중', 3 => '재연락 요청', 4 => '입찰 계획 확인'
    ];



    //세부스케쥴 작업용. 피드백에 따라 바뀔수 있음
    const PROJECT_SCHE_DETAIL_ST = [
        1 => '정상', 2 => '완료', 3 => '예정일 초과', 4 => 'D/L 초과', 9 => '미사용'
    ];

    const FACTORY_TYPE = [
        1 => '샘플실',
        2 => '패턴실',
        /*4 => '매입처',*/
    ];

    //분류패킹 - 담당자별 상태
    const RECEIVER_DELIVERY_ST = [
        1 => '입력요청전',
        2 => '입력요청',
        3 => '입력완료',
        4 => '입력확인완료',
    ];

    //분류패킹===납품===발주 상태
    const CUSTOMER_PACKING_ST = [
        1 => '고객입력',
        2 => '고객입력완료',
        3 => '이노버확인완료',
        4 => '입고완료',
    ];

    //스타일기획 레퍼런스 부가정보 - 유형
    const REF_PRODUCT_PLAN_INFO_TYPE = [
        1 => '참고 브랜드',
        2 => '컨셉',
        3 => '디자인',
        4 => '부가기능',
    ];

    //스타일기획 레퍼런스 - 원부자재 유형
    const REF_PRODUCT_PLAN_MATERIAL_TYPE = [
        'fabric' => '원단/충전재',
        'subFabric' => '부자재',
        'jsonUtil' => '기능',
        'jsonMark' => '마크',
        'jsonLaborCost' => '공임',
        'jsonEtc' => '기타',
    ];
    //스타일기획 레퍼런스 - 타입(다중선택)
    const REF_PRODUCT_PLAN_TYPE = [
        0 => '없음',
        1 => '납품스타일',
        2 => '개발샘플',
    ];




}