<?php
namespace Component\Work;

/**
 * Sitelab Code Key List
 * Class SlCode
 * @package SlComponent\Util
 */
class WorkCodeMap {

    const SECRET_KEY = 'momotee_innover';

    /**
     * 시즌
     */
    const SEASON = [
        0 => '전체',
        1 => '춘추',
        2 => '하계',
        3 => '동계',
    ];
    
    /**
     *  거래처 구분
     */
    const COMP_TYPE = [
        0 => '미지정',
        1 => '중소기업',
        2 => '중견기업',
        3 => '대기업',
        99 => '기타',
    ];

    /**
     * 거래처 구분2
     */
    const COMP_DIV = [
        0 => '신규 업체',
        1 => '기존업체 미팅',
        2 => '진행중업체',
    ];
    /**
     * 제안 타입 (고객 희망 제안 방향)
     */
    const PROPOSAL_TYPE = [
        0 => '신규 기획',
        1 => '리오더',
        2 => '개선',
        3 => '기존동일',
        4 => '기성',
    ];
    const PROPOSAL_TYPE_CLASS = [
        0 => 'badge-success',
        1 => 'badge-danger',
        2 => 'badge-warning',
        3 => 'badge-primary',
        4 => 'badge-info',
    ];
    /**
     * 제안 타입 (MS)
     */
    const MS_PROPOSAL_TYPE = [
        0 => '신규 기획',
        1 => '리오더',
        2 => '개선',
        3 => '기존동일',
        4 => '기성',
    ];

    /**
     *
     */
    const PRJ_STATUS = [
        0 => '제안'
        , 1 => '사양서 확정 및 생산 발주'
        , 2=> '디자인 생산 크로스 검수'
        , 3 => '생산관리'
        , 4 => '출고관리'
    ];

    /**
     * 부서코드
     */
    const DEPT_STR = [
        '02001001' => 'SALES'
        , '02001002' => 'DESIGN'
        , '02001003' => 'QC'
        , '02001004' => 'ACCT'
    ];
    const DEPT_CODE = [
        'SALES' => '02001001'
        , 'DESIGN' => '02001002'
        , 'QC' => '02001003'
        , 'ACCT' => '02001004'
    ];

    const DEPT_KR = [
        'SALES' => '영업'
        , 'DESIGN' => '디자인'
        , 'QC' => '생산/QC'
        , 'ACCT' => '회계'
    ];

    //영업 문서
    const DOC_SALES = [
        1=>[
            name => 'salesMeetingReady',
            nameKr => '미팅준비보고서'
        ],
        2=>[
            name => 'salesMeeting',
            nameKr => '미팅보고서'
        ]
    ];

    const PRJ_DOC_SALES = [
        1=>'미팅보고서'
        ,2=>'근무환경 보고서'
        ,3=>'고객사 샘플리뷰서'
        ,4=>'생산견적요청서' // 승인없음
        ,5=>'견적서' // 승인없음
        ,6=>'발주 확정서'
        ,7=>'폐쇄몰 준비자료'// 승인없음
        ,8=>'계약서'
    ];

    const PRJ_DOC_DESIGN = [
        1=>'디자인컨셉'
        ,2=>'포트폴리오' //고객(승)
        ,3=>'샘플 의뢰서'
        ,4=>'피팅 체크 리스트'
        ,5=>'사양서' //고객(승)
        ,6=>'작업지시서'
        ,7=>'원단확인요청서'
    ];
    const PRJ_DOC_QC = [
        1=>'원단확인요청서'
        ,2=>'가견적 요청서'
        ,3=>'샘플검사&고객수정요청사항'
        ,4=>'발주서'
        ,5=>'원부자재표'
        ,6=>'판매구매확정서'
        ,7=>'세탁테스트 이화학검사'
        ,8=>'QA검품결과서'
        ,9=>'납품 평가서'
        ,10=>'생산 사고 보고서'
        ,11=>'그레이딩 및 마카요청서'
    ];

    const PRJ_DOC = [
        'SALES' => WorkCodeMap::PRJ_DOC_SALES    //영업
        , 'DESIGN' => WorkCodeMap::PRJ_DOC_DESIGN  //디자인
        , 'QC' => WorkCodeMap::PRJ_DOC_QC  //생산관리
    ];

    /**
     * 문서에서 사용하는 코드맵
     */
    const PRJ_CODE_MAP = [
        '구매형태' => ['단독'=>'단독', '경쟁'=>'경쟁', '단가입찰'=>'단가입찰'],
        '경쟁업체' => ['기존업체'=>'기존업체', '다수업체'=>'다수업체'],
        '업체선정요소' => ['디자인'=>'디자인', '품질'=>'품질', '단가'=>'단가'],
    ];

    /**
     * 점수 타입
     */
    const RATING_TYPE = [
        0 => '불량',
        1 => '미흡',
        2 => '보통',
        3 => '양호',
        4 => '우수',
    ];

    /**
     * 포트폴리오 스타일 검색 (일단은)
     * TODO : 추후 DB로 ?
     */
    const STYLE_TYPE = [
        0 => '춘추점퍼',
        1 => '동계점퍼',
        2 => '조끼',
    ];

    /**
     * 문서 승인타입
     */
    const DOC_ACCEPT_TYPE = [
        'n' => '대기',
        'q' => '요청',
        'r' => '반려',
        'y' => '승인',
    ];

    /**
     * 계획 수정 사유
     */
    const PLAN_MOD_REASON_TYPE = [
        '1' => '최초등록',
        '2' => '고객 귀책',
        '3'=> '이노버 귀책',
        '4'=> '생산처 귀책',
        '5'=> '배송 지연',
        '99'=> '기타',
    ];

    const COMPANY_STEP = [
        '미팅', '포트폴리오', '실물샘플', '디자인가이드(발주)', '생산완료', '납품'
    ];

}


