<?php
namespace Component\Ims;

use App;
use Component\Database\DBIms;
use Component\Database\DBTableField;
use Component\Member\Manager;
use Component\Scm\ScmTkeService;
use Framework\Debug\Exception\AlertBackException;
use Framework\Utility\DateTimeUtils;
use LogHandler;
use Request;
use Session;
use SiteLabUtil\SlCommonUtil;
use SlComponent\Database\DBUtil2;
use SlComponent\Database\SearchVo;
use SlComponent\Mail\SiteLabMailUtil;
use SlComponent\Util\ExcelCsvUtil;
use SlComponent\Util\SitelabLogger;
use SlComponent\Util\SlCodeMap;
use SlComponent\Util\SlLoader;
use Component\Sms\Code;

/**
 * IMS 서비스
 * Class GoodsStock
 * @package Component\Goods
 */
class ImsJsonSchema {

    /**
     * 기본 스키마 설정
     * @param $srcData
     * @param $jsonSchema
     * @return array
     */
    public static function setDefaultSchema($srcData, $jsonSchema){
        $refineData = [];
        foreach($jsonSchema as $key => $schema){
            if( $srcData[$key] ){
                $refineData[$key] = $srcData[$key];
            }else{
                $refineData[$key] = '';
            }
        }
        return $refineData;
    }

    /**
     * 고객 견적 수신자 정보
     */
    const EWORK_MARK_INFO = [
        'position' => '위치',
        'kind' => '종류',
        'color' => '색상',
        'size' => '크기',
    ];

    const EWORK_WARNING = [
        'customerConfirm' => 'n', //고객 컨펌
        'customerConfirmMemo' => '', //고객 컨펌 비고
        'sampleSizeCnt' => null, //사이즈 수량
        'contents1' => [], //스타일 변경내용
        'contents2' => [], //고객사 요청사항 / 확정되지 않은 사양
        'contents3' => [], //비고
        'storedFabric' => [], //원부자재 비축 요청
    ];

    /**
     * 입력 리비전 히스토리
     */
    const EWORK_REVISION = [
        'regDt' => '', //등록일
        'regManagerName' => '', //등록자
        'revReason' => 0, //변경 사유
        'revType' => 0,   //변경 구분
        'revDetail' => '', //변경 세부
        'revBefore' => '', //변경 전
        'revAfter' => '',  //변경 후
        'revSt' => '대기',  //상태(대기,변경완료)
        'revRoute' => '작업지시서',  //등록경로(작업지시서,납품검수)
        'chgManagerName' => '', //변경자
        'chgDt' => '', //변경일시
    ];

    const EWORK_WARNING_CONTENTS = [
        'changeDt' => '', //변경일
        'div' => '', //구분
        'memo1' => '', //내용 or 비축 자재명 or 변경전 요청사항
        'memo2' => '', //변경후 요청사항 확정 예정일
    ];
    const EWORK_WARNING_FABRIC = [
        'attached' => '', //부착위치
        'fabricName' => '', //변경일
        'fabricMix' => '', //혼용율
        'fabricColor' => '', //색상
        'fabricCnt' => '', //수량
        'fabricMemo' => '', //사용 예정
    ];

    /**
     * 사이즈 정보
     */
    const SIZE_SPEC_DATA = [
        'specRange' => '', //제작사이즈
        'standard' => '', //기준사이즈
        'specData' => [
            [
                'title' => '', //측정항목
                'unit' => 'CM', //측정단위
                'share' => 'y', //공개범위
            ],
        ], //측정항목 : 이를만. "only title"
    ];

    /**
     * 작업지시서 사이즈 스펙
     */
    const SPEC_DATA = [
        'title' => '', //스펙명
        'share' => '', //공유
        'deviation' => '', //편차
        'spec' => '', //스펙
        'unit' => '', //단위
        'memo' => '', //비고
        'correction' => '', //보정데이터
    ];

    /**
     * 고객 견적 수신자 정보
     */
    const CUSTOMER_ESTIMATE_RECEIVER = [
        'name' => '이름',
        'position' => '직급/직책',
        'mail' => '메일',
        'phone' => '연락처',
        'etc' => '기타',
    ];
    /**
     * 고객 견적 품목 정보
     */
    const CUSTOMER_ESTIMATE_PRD = [
        'styleSno' => '스타일번호',
        'styleCode' => '코드',
        'name' => '품명',
        'qty' => '수량',
        'estimateCost' => '견적생산가',
        'estimateConfirmSno' => 0,
        'unitPrice' => '단가',
        'supply' => '공급가',
        'tax' => '세액',
        'total' => '합계',
        'etc' => '비고',
    ];

    /**
     * 고객 추가 정보
     */
    const CUSTOMER_ADDINFO = [
        'etc1' => '근무환경',
        'etc2' => '직원수',
        'etc3' => '착용연령',
        'etc4' => '고객Needs',
        'etc5' => '발주물량변동사항',
        'etc6' => '현재유니폼제작업체',
        'etc7' => '지급주기',
        'etc8' => '현재업체계약종료',

        'info003' => '샘플 확보 여부',
        'info004' => '샘플 반납 여부',

        'info009' => '색상 민감도',
        'info010' => '품질 민감도',
        'info011' => '단가 민감도',
        'info012' => '납기 민감도',

        'info015' => '폐쇄몰 관심도',

        'info016' => '샘플비 청구 유무',
        'info018' => '샘플비 청구 방법',

        'info026' => '비축 원단 수량',
        'info027' => '안전재고 비율',
        'info028' => '고객사 원단 결제 유무',
        'info052' => '안전 재고 정산 방법',
        'info053' => '안전 재고 출고 방법',
        'info054' => '안전 재고 보관처',
        'info055' => '비축 원단 생산',
        'info100' => '원단 재고 보관처',

        'info039' => '원단 비축 유무',
        'info040' => '비율',
        'info044' => '안전재고 생산유무',

        'info057' => '결제 형태',

        'info060' => '계약금 유무',
        'info061' => '금액',
        'info062' => '계산서 발행 방법',
        'info063' => '계약서 체결',
        'info064' => '계산서 발행 형태',
        'info065' => '계약금 입금일',
        'info066' => '결제 담당자 정보',
        'info067' => '계산서 발행 주기',

        'info072' => '현장 조사',

        'info088' => '노사 합의 여부',
        'info089' => '의사 결정 라인',

        'info101' => '계약 주기',
        'info102' => '경쟁 업체',
        'info103' => '리서치 가능 유무',
        
        'info104' => '생산기간 안내',
        'info105' => '제작 샘플비 안내',
        'info106' => '제안서 형태',
        'info107' => '포트폴리오 제안일',

        'info108' => '이해관계',

        'info109' => '업체 선정 방법',
        'info110' => '업체 선정 기준',

        'info111' => '진행 가능성',
        'info112' => '제안서 필수 항목',
        'info113' => '로고 구분', //Check.
        'info114' => '명찰 구분', //Check.
        'info115' => '제안 컨셉 수',
        'info116' => '업체 변경 사유',
        'info117' => '기존 업체',
        'info118' => '명찰 정보',

        'info119' => '입고비',
        'info120' => '보관비',
        'info121' => '출고비',
        'info122' => '남녀 비율',
        'info123' => '근무환경(선택)',
        'info124' => '계약 종료 재고 처리',

        'info125' => '업체선정 기준',
        'info126' => '업체선정 기준 기타내용',
        'info127' => '업체선정 방법',
        'info128' => '업체선정 방법 기타내용',

        'info129' => '담당자 유대감',
        'info130' => '담당자 니즈',

        //폐쇄몰
        'mall001' => '회원가입',
        'mall002' => '회원가입 비고',

        'mall003' => '본사관리자 출고 승인',
        'mall004' => '본사관리자 출고 승인 비고',

        'mall005' => '출고 방법',
        'mall006' => '출고 방법 기타',

        'mall007' => '상품 결제',
        'mall008' => '결제 타입1:주문자 무상 지급 타입 선택', //발주 총금액 본사 일괄 결제 / 출고 수량 만큼 본사 월말 결제
        'mall009' => '', //발주 총금액 본사 일괄 결제 / 출고 수량 만큼 본사 월말 결제
        'mall010' => '', //발주 총금액 본사 일괄 결제 / 출고 수량 만큼 본사 월말 결제
        'mall011' => '상품 결제 비고',
        
        'mall012' => '상품 노출 금액',
        'mall013' => '타입1',
        'mall014' => '타입2',
        'mall015' => '',
        'mall016' => '상품 노출 금액 비고',
        
        'mall017' => '폐쇄몰 개설 비용',
        'mall018' => '폐쇄몰 개설 비용 비고',
        
        'mall019' => '물류 운영 관리 비용',
        'mall020' => '물류 운영 관리 비용 비고',
        
        'mall021' => '물류 운영 관리 비용 결제 방법',
        'mall022' => '물류 운영 관리 비용 결제 방법 비고',

        'mall023' => '',
        'mall024' => '',

        'mall025' => '배송비 결제',
        'mall026' => '배송비 결제 비고',

        'mall027' => '재고 소진시 (안전재고 미달)',
        'mall028' => '재고 소진시 비고',
        
        'mall029' => '맞춤형 기능',
        'mall030' => '어드민ID',
        'mall031' => '몰ID',

        'etc99' => '기타',
    ];


    /**
     * 원단 정보
     */
    const FABRIC_INFO = [
        'no'=>'',
        'attached'=>'', //부착위치
        'fabricName'=>'', //Code.
        'fabricCompany'=>'',
        'fabricMix'=>'',
        'color'=>'',
        'spec'=>'',
        'meas'=>'',
        'unitPrice'=>'',
        'price'=>'',
        'memo'=>'',
        'btConfirm'=>'',
        'btConfirmDt'=>'',
        'btMemo'=>'',
    ];

    /**
     * 부자재 정보
     */
    const SUB_FABRIC_INFO = [
        'no'=>'',
        'subFabricName'=>'', //Code.
        'subFabricMix'=>'',
        'company'=>'',
        'color'=>'',//색상
        'spec'=>'',
        'meas'=>'', //요척
        'unitPrice'=>'',
        'price'=>'',
        'memo'=>'',
    ];


    /**
     * 견적금액
     */
    const ESTIMATE = [
        'totalCost' =>  0,
        'fabricCost' =>  0,
        'subFabricCost' =>  0,
        'utilCost' =>  0,
        'markCost' =>  0,
        'laborCost' =>  0,
        'etcCost' =>  0,
        'customerSno' =>  0,
        'dollerRatio' =>  0, //환율 === exchange
        'exchange' =>  0, //환율
        'exchangeDt' =>  '', //환율일자
        'marginCost' =>  0,
        'dutyCost'=>  0,
        'managementCost'=>  0,
        'prdMoq'=>  0,
        'priceMoq'=>  0,
        'addPrice'=>  0,
        'produceType'=>  0,
        'producePeriod'=>  '',
        'deliveryType'=>  '',
        'fabric' =>  [],
        'subFabric' =>  [],
        'jsonUtil' =>  [],
        'jsonMark' =>  [],
        'jsonLaborCost' =>  [],
        'jsonEtc' =>  [],
    ];

    /**
     * 샘플 원단 정보
     */
    const SAMPLE_FABRIC_INFO = [
        'no'=>'',
        'fabricName'=>'', //Code.
        'fabricCompany'=>'',
        'fabricMix'=>'',
        'color'=>'',
        'spec'=>'',
        'meas'=>'',
        'unitPrice'=>'',
        'price'=>'',
        'memo'=>'',
        'makeNational'=>'', //제조국
    ];

    /**
     * 부자재 정보
     */
    const SAMPLE_SUB_FABRIC_INFO = [
        'no'=>'',
        'subFabricName'=>'', //Code.
        'subFabricMix'=>'',
        'company'=>'',
        'spec'=>'',
        'meas'=>'', //요척
        'unit'=>'', //단위
        'unitPrice'=>'',
        'price'=>'',
        'memo'=>'',
    ];

    const PREPARED_BT = [
        'sendDt' => '', //발송예정일
        'sendType' => '', //발송형태
        'sendInfo' => '', //발송정보
        'filePreparedBt' => '',
        'filePreparedBtResult' => '',
    ];
    const PREPARED_WORK = [

    ];
    const PREPARED_ESTIMATE = [
        'produceDeliveryDt' => '', //생산처 납기
        'produceNational' => '',
        'produceType' => '0',
        'productList' => []
    ];
    const PREPARED_COST = [
        'produceDeliveryDt' => '', //생산처 납기
        'produceNational' => '',
        'produceType' => '0',
        'productList' => []
    ];
    const PREPARED_ORDER = [
        'workSendDt' => '',
        'filePreparedOrder'  => '',
    ];

    const PRODUCE_STEP = [
        'expectedDt' => '',
        'completeDt' => '',
        'confirmYn' => '',
        'memo' => '',
    ];

    const PRODUCE_STEP_CONFIRM = [
        'confirmDt' => '',
        'confirmStatus' => '',
        'confirmManagerName' => '',
    ];

    const PRODUCE_ESTIMATE_CONTENTS = [
        'respDeliveryDt' => '', //생산처 납기일자
        'respPrdNational' => '', //생산국가
        'respPrdType' => '', //생산형태
    ];


    /**
     * 미팅체크리스트
     */
    const MEETING_CHECKLIST = [
        'item' => '',  //항목
        'ready' => '', //준비
        'confirm' => '',  //확인
        'etc' => '',  //비고
    ];
    
    /**
     * 스타일 리스트
     */
    const MEETING_STYLELIST = [
        'style' => '',  //스타일
        'count' => '', //예상수량
        'currentPrice' => '',  //현재단가
        'targetPrice' => '',  //타겟단가
        'discomfort' => '',  //불편사항
    ];

    /**
     * 생산 최초 데이터
     */
    const PRODUCTION_FIRST_DATA = [
        'schedule' => [
            'wash' => [
                'ConfirmExpectedDt',
                'CompleteDt',
                'Memo',
                'Confirm',
            ], //1 세탁
            'fabricConfirm'=> [
                'ConfirmExpectedDt',
                'CompleteDt',
                'Memo',
                'Confirm',
            ], //2 원부자재 확정
            'fabricShip'=> [
                'ConfirmExpectedDt',
                'CompleteDt',
                'Memo',
                'Confirm',
            ], //3 원부자재 선적
            'qc'=> [
                'ConfirmExpectedDt',
                'CompleteDt',
                'Memo',
                'Confirm',
            ], //4 QC
            'inline'=> [
                'ConfirmExpectedDt',
                'CompleteDt',
                'Memo',
                'Confirm',
            ], //5 인라인
            'ship'=> [
                'ConfirmExpectedDt',
                'CompleteDt',
                'Memo',
                'Confirm',
            ], //6 선적
            'arrival'=> [
                'ConfirmExpectedDt',
                'CompleteDt',
                'Memo',
                'Confirm',
            ], //7 도착
            'confirm'=> [
                'ConfirmExpectedDt',
                'CompleteDt',
                'Memo',
                'Confirm',
            ], //8 검수
            'delivery'=> [
                'ConfirmExpectedDt',
                'CompleteDt',
                'Memo',
                'Confirm',
            ], //9 납기완료
        ],
        'acceptData' => [
            'managerNm' => '',
            'acceptDt' => '',
        ],
    ];


    const APPROVAL_MANAGER_DATA = [
        'sno' => '',  //매니저번호
        'name' => '', //이름
        'status' => '',  //승인상태
        'approvalDt' => '',  //처리일자 (승인, 반려 일자)
        'memo' => '',  //첨언 (댓글에 등록하지만 마지막 내용도 등록)
    ];

    /**
     * 파일 기본 구조
     * @return array
     */
    public static function getDefaultFileSchema(){
        return [
            'title' => '등록된 파일이 없습니다.',
            'memo' => '',
            'files' => [],
            'noRev' => 0
        ];
    }

    /**
     * 아소트
     */
    const ASSORT = [
        'type' => '',  //구분
        'optionList' => [], //이름
    ];

    /**
     * 프로젝트 추가 정보
     */
    const PROJECT_ADDINFO = [
        'etc1' => '' //생산 기간 안내
        ,'etc2' => '' //분류 패킹
        ,'etc3' => '' //이노버 제안 샘플 선호도
        ,'etc4' => '' //분류 패킹 자료 회신 일자
        ,'etc5' => '' //오리지널 샘플 확보 유무
        ,'etc6' => '' //폐쇄몰 진행 여부
        ,'etc7' => '' //오리지널 샘플 훼손 유무
        ,'etc8' => '' //폐쇄몰 비용 안내
        ,'etc9' => '' //오리지널 샘플 반납 유무
        ,'etc10' => '' //폐쇄몰 정보 전달 예정일
        ,'etc11' => ''//샘플 제작 관련 비용 안내
        ,'etc12' => ''//유니폼 색상&품질 민감도
        ,'etc13' => ''//생산 MOQ 전달 유무
        ,'etc14' => ''//추가 수량 매입 여부
        ,'etc15' => ''//결재 형태
        ,'etc16' => ''//원단 비축 가능 유무
        ,'etc18' => ''//결재 방법
        ,'etc19' => ''//세탁 방법
        ,'etc20' => ''//단가 인상 가능 여부
        
        ,'etc21' => []//제안내용 (디자인 운영방안...)
        ,'etc22' => ''//미사용
        ,'etc23' => ''//미사용
        ,'etc24' => ''//미사용
        ,'etc25' => ''//미사용
        ,'etc26' => ''//경쟁업체
        ,'etc27' => ''//계약 기간 / 계약 금액
        ,'etc28' => ''//영업 전략
        ,'etc29' => ''//실행 계획
        ,'etc30' => ''//기대 효과
        ,'etc31' => []//변경사유
        ,'etc32' => ''//변경사유 기타
        ,'etc33' => ''//세탁 구분
        ,'etc34' => ''//샘플 비용

        ,'etc35' => ''//분류패킹 유상 여부
        ,'etc36' => ''//분류패킹 추가 정보
    ];


    /**
     * 영업 스타일
     */
    const SALES_STYLE = [
        'season'=>'',
        'phone'=>'',
        'email'=>'',
    ];


    const SHARE_CUST_INFO = [
        'name'=>'',
        'phone'=>'',
        'email'=>'',
    ];

    const ADD_INFO = [
        //Special
        'shareCustomerInfo' => '공유 받을 고객 정보',

        //근무환경정보
        'info001' => '고객사 근무 환경',
        'info002' => '착용자 연령/성별',
        //고객사 샘플 정보
        'info003' => '샘플 확보',
        'info004' => '샘플 반납 유무',
        //기타사항
        'info005' => '발주 물량 변동',
        'info006' => '계약기간',
        'info007' => '선호컨셉',
        'info008' => '선호컬러',
        //고객성향
        'info009' => '색상',
        'info010' => '품질',
        'info011' => '단가',
        'info012' => '납기',
        'info013' => '이노버 제공 샘플 선호도',
        'info014' => '고객 희망 기능',
        'info015' => '폐쇄몰 관심도',
        //샘플 제작 정보
        'info016' => '샘플 유상/무상',
        'info017' => '샘플 제작비',
        'info018' => '샘플 결제 방법', //어음,현금
        'info019' => '샘플 제출일시',
        'info020' => '제출시간',
        'info021' => '샘플 제출 장소 및 접수자',
        'info022' => '접수자 정보(샘플 받는분 정보)',
        //대기사유
        'info023' => '고객 의사 결정',
        'info024' => '생산 기간 협의',
        'info025' => '대기사유', // s가 붙으면 textarea strip
        //임시
        'info026' => '',
        'info027' => '',
        'info028' => '',
        //마크사양 스케쥴 공유
        'info029' => '마크 유무',
        'info030' => '갯수 및 기타 정보',
        'info031' => '생산 스케쥴 고객 공유',
        'info032' => '', //임시
        //분류패킹
        'info033' => '분류 패킹 유무',
        'info034' => '회신D/L',
        'info035' => '패킹 방법',
        'info036' => '패킹 방법 기타',
        'info037' => '배송비 부담',
        'info038' => '배송비 부담 기타',
        //원부자재 비축 및 안전재고
        'info039' => '원부자재 보유 협의',
        'info040' => '원부자재 보유 협의 기타',
        'info041' => '시즌별 추가 발송 횟수',
        'info042' => '시즌별 추가 발송 횟수 기타',
        'info043' => '원부자재 결제 유무',
        'info044' => '안전재고 생산 유무',
        'info045' => '안전재고 출고 방법',
        'info046' => '안전재고 결제 방법',
        //3PL 폐쇄몰
        'info047' => '배송비 지급',
        'info048' => '교환/반품',
        'info049' => '배송비 정산 주기',
        'info050' => '출고 승인 사용',
        'info051' => '썸네일 타입',
        //'info052' => '',
        //'info053' => '',
        //'info054' => '',
        //'info055' => '',
        //결제내용
        'info056' => '결제방법',
        'info057' => '결제형태',
        'info058' => '계산서 발행 업체 수',
        'info059' => '결제일자',
        'info060' => '계약금여부',
        'info061' => '금액',
        //'info062' => '',
        //'info063' => '',
        //'info064' => '',
        //'info065' => '',
        //사전영업
        'info070' => '현 유니폼 확보 가능여부',
        'info071' => '현 유니폼 확보 추가 정보',
        'info072' => '현장조사',
        //미팅배경
        'info073' => '프로젝트개요',
        'info074' => '영업의견',
        //공개 입찰 영업희망일
        'info075' => '입찰 설명회',
        'info076' => '기획서',
        'info077' => '제안서',
        'info078' => '가견적',
        'info079' => '제안서 확정',
        'info080' => '샘플',   //샘플제안
        'info081' => '생산가', //확정견적
        //고객사미팅 . 영업 희망일
        'info082' => '샘플확정',
        'info083' => '고객발주',
        'info084' => '납품일',
        //고객사 정보
        'info085' => '고객사 근무 환경',
        'info086' => '착용자 연령',
        'info087' => '착용자 성별',
        'info088' => '노조개입',
        'info089' => '의사 결정 라인',
        'info090' => '의사 결정 기간',
        //'info003' => '샘플 확보',
        //'info004' => '샘플 반납 유무',
        'info100' => '제안내용',
        'info101' => '분류 패킹 비용',

    ];

    const PRD_ADD_INFO = [
        'prd001' => '', //기타/비고
        'prd002' => '0', //고객사 샘플
        'prd003' => '', //스타일 선호도
        'prd004' => '', //원단 선호도
        'prd005' => '', //부자재 선호도
        'prd006' => '', //인쇄 형태 선호도
        'prd007' => '', //기능 선호도
        'prd008' => '', //불편사항
        'prd009' => '', //발주수량변동
        'prd010' => '', //컨셉 => 컨셉수
        'prd011' => '', //컬러 => 선호 컬러
        'prd012' => '', //기능
        'prd013' => '', //원단 => 선호 원단
        'prd014' => '', //추가옵션=>선호 디자인
        'prd015' => '', //로고사양
    ];

}



