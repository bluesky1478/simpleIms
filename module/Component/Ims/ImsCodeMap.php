<?php
namespace Component\Ims;

use SlComponent\Database\DBUtil;
use SlComponent\Database\SearchVo;

/**
 * Sitelab Code Key List
 * Class SlCode
 * @package SlComponent\Util
 */
class ImsCodeMap {

    const DEFAULT_THUMBNAIL_IMG = '/data/commonimg/ico_noimg_75.gif';

    //61.101.55.174 => imsftp.msinnover.com
    const NAS_URL = 'https://innoversftp.synology.me/ims';
    const NAS_DN_URL = 'https://innoversftp.synology.me/ims/download.php?';
    const NAS_ALL_DN_URL = 'https://innoversftp.synology.me/ims/download_all.php?';

    const CODE_DEPT_INFO = [ //부서
        '02001001'   => ['name'=>'영업','email'=>'dkhan@msinnover.com','phone'=>'010-3307-3001','managerNm'=>'한동경'] //한동경
        , '02001002' => ['name'=>'디자인','email'=>'skjeong@msinnover.com','phone'=>'010-2285-4817','managerNm'=>'정슬기'] //정슬기
        , '02001003' => ['name'=>'QC','email'=>'sbmoon@msinnover.com','phone'=>'010-8830-8307','managerNm'=>'문상범'] //문상범
        , '02001004' => ['name'=>'회계','email'=>'syhan@msinnover.com','phone'=>'010-2308-9327','managerNm'=>'한소윤'] //한소윤
        , '02001005' => ['name'=>'기타']
    ];

    const TEAM_SALES = '02001001'; //영업팀
    const TEAM_DESIGN = '02001002'; //디자인
    const TEAM_QC = '02001003'; //생산
    const TEAM_FINANCE = '02001004'; //회계

    const FACTORY_ESTIMATE_MODIFY_MANAGER = [
        59,20,32,35 //이보람, 송준호, 문상범, 유수희
    ];

    const PROJECT_SCHEDULE_ALARM_LIST = [
        'jhsong@msinnover.com',
        'skjeong@msinnover.com',
        'sbmoon@msinnover.com',
        'dkhan@msinnover.com',
        'jhseo@msinnover.com',
    ];

    const COST_APPROVAL_ALARM_LIST = [
        'innover_dev@msinnover.com',
        'syhan@msinnover.com',
    ];

    const CODE_WORK_RANK = [ //직급
        '02002001'   => ['name'=>'대표']
        , '02002007' => ['name'=>'이사']
        , '02002008' => ['name'=>'책임']
        , '02002010' => ['name'=>'선임']
        , '02002011' => ['name'=>'사원']
        , '02002012' => ['name'=>'수석']
    ];
    const CODE_WORK_POSITION = [ //직책
        '02003001'   => ['name'=>'일반']
        , '02003002' => ['name'=>'팀장']
        , '02003003' => ['name'=>'실장']
    ];

    //고객납기일 변경 안내 (FIXME : 추 후 생산처리스트에서 가져오기)
    const CUSTOMER_DELIVERY_SMS = [
        '010-8830-8307',//문상범
        '010-3307-3001',//한동경
        '010-7123-2904',//서현주
        //'010-8109-9599',//송준호

    ];
    const WORK_CHANGE_SMS = [
        '010-2316-2844',//정성희
        '010-3252-4652',//유수희
        '010-2285-4817', //정슬기
        //'010-3762-3928', //서재훈
        '010-8934-8431', //최해룡
        //'010-4427-8294', //최하나
        '010-7123-2904', //서현주
    ];

    const FACTORY_SMS = [
        43 => [
            '010-8934-8431', //최해룡
            //'010-4427-8294', //최하나
        ]
    ];

    //특수 관리자
    const IMS_ADMIN = [
        'sbmoon',
        'djemalsrpwjd',
        'b1478',
        's_hdk',
        'd_jsk',
    ];

    //Redirect만
    const IMS_MANAGER = [
        'c_sjh',
        'sbmoon',
        'djemalsrpwjd',
        'd_jsk',
    ];
    //생산처
    const PRODUCE_COMPANY_MANAGER = [
        'hanafnc',
        'daehasa',
        'nk',
    ];
    //영업
    const SALES_COMPANY_MANAGER = [
        'sales1'
    ];

    /**
     * 비축 원부자재 관리자
     */
    const STORE_MANAGER = [
        'sbmoon','b1478','nrlee'
    ];
    
    //승인관리자
    const AUTH_MANAGER = [
        'c_sjh',
        'sbmoon',
        'b1478', //20
        'djemalsrpwjd',
        'nrlee',
        's_hdk',
        'd_jsk',
        'shjung',
        'jhyoon',
        'hjseo',
    ];

    //폐쇄몰 담당자
    const PRIVATE_MALL_MANAGER = [
        ['sno'=>'76', 'name'=>'서현주', 'id'=>'hjseo']
    ];
    const PRIVATE_MALL_MANAGER_SIMPLE = [
        76, 20
    ];
    const PRIVATE_MALL_MANAGER_MAIL = [
        'jhsong@msinnover.com','hjseo@msinnover.com'
    ];

    const EWORK_MARK_CNT_LIMIT = 10;

    //전산작지 파일 리스트 ( fileMark1~6, markInfo1~6 ... )
    const EWORK_FILE_LIST = [
        'fileMain',
        'filePrd',
        'fileBatek',
        'fileAi', //AI 파일
        'fileMarkAi', //AI 파일
        'fileCareAi', //AI 파일
        'fileMark1',
        'fileMark2',
        'fileMark3',
        'fileMark4',
        'fileMark5',
        'fileMark6',
        'fileMark7',
        'fileMark8',
        'fileMark9',
        'fileMark10',
        'fileMarkPosition1',
        'fileMarkPosition2',
        'fileMarkPosition3',
        'fileMarkPosition4',
        'fileMarkPosition5',
        'fileMarkPosition6',
        'fileMarkPosition7',
        'fileMarkPosition8',
        'fileMarkPosition9',
        'fileMarkPosition10',
        'filePosition',
        'fileCare',
        'fileSpec',
        'filePacking1', //접는 방법
        'filePacking2', //포장 방법
        'filePacking3', //박스 패킹
    ];


    /**
     * 생산국가
     */
    const PRD_NATIONAL = [
        [
          'name' => '베트남',
          'initial' => 'vn',
        ],
        [
          'name' => '중국',
          'initial' => 'cn',
        ],
        [
          'name' => '한국',
          'initial' => 'kr',
        ],
        [
          'name' => '시장',
          'initial' => 'mk',
        ],
    ];
    const PRD_NATIONAL_CODE = [
        'kr' => '한국',
        'cn' => '중국',
        'vn' => '베트남',
        'mk' => '시장',
    ];

    /**
     * 고객 상태
     * 0 신규 : 신규(초도) - 새로운 프로젝트 등록시 신규(발주없음) or 재입찰(발주있음)로 셋팅
     * 1 재입찰 : 수기     - 새로운 프로젝트 등록시 신규(발주없음) or 재입찰(발주있음)로 셋팅
     * 2 계약중 : 자동 - 발주건 있음 + 현재 상태가 수기 상태가 아님 ( 재입찰, 보류, 이탈, 유찰 )
     * 10 보류 : 수기 - 초도는 마지막 프로젝트가 보류인 경우.
     * 11 이탈 : 수기 - 이탈 처리된 고객
     * 12 유찰 : 수기 - 유찰 처리된 고객
     */
    const CUSTOMER_STATUS = [
        0 =>'신규고객',
        1 =>'재입찰',
        2 =>'계약중',
        10 =>'보류고객',
        11 =>'이탈고객',
        12 =>'유찰고객',
    ];

    /**
     * 생산스케쥴 단계
     */
    const PRODUCE_STATUS = [
        0 =>'생산준비',
        10 =>'스케쥴입력',
        20 =>'스케쥴확정대기',
        30 =>'생산스케쥴관리',
        99 =>'생산완료',
    ];

    /**
     * 생산처
     */
    const PRODUCE_STEP_MAP = [
        10 =>'세탁/이화학검사', //파일 -> 요청 -> 승인(반려)
        20 =>'원부자재 확정',     //파일 -> 요청 -> 승인(반려) , 완료일 자동 등록 (파일 올리는 날, 완료일이 없으면 등록) , 반려 -> 완료일 리셋
        30 =>'원부자재 선적',     //파일 -> 요청 -> 승인(반려)
        40 =>'QC',              //파일 -> 요청 -> 승인(반려)
        25 =>'재단',            //완료일이 등록되면 자동 승인
        27 =>'봉제',            //완료일이 등록되면 자동 승인
        50 =>'인라인',           //파일 -> 요청 -> 승인(반려)
        60 =>'선적',            //파일 -> 요청 -> 승인(반려)
        70 =>'도착',            //완료일이 등록되면 자동 승인
        80 =>'입고제품 검수',   //완료일이 등록되면 자동 승인
        90 =>'공장납기',        //완료일이 등록되면 자동 승인
    ];
    const PRODUCTION_STEP = [
        'wash',          //1 세탁
        'fabricConfirm', //2 원부자재 확정
        'fabricShip',    //3 원부자재 선적
        'qc',            //4 QC
        'cutting',       //5 봉제
        'sew',           //6 봉제
        'inline',        //7 인라인
        'ship',          //8 선적
        'arrival',       //9 도착
        'check',         //10 검수
        'delivery',      //11 납기완료
    ];
    const PRODUCTION_STEP_ALL = [
        'wash' => '세탁및이화학검사' ,
        'fabricConfirm' => '원부자재확정',
        'fabricShip' => '원부자재선적',
        'qc' => 'QC',
        'cutting' => '재단',
        'sew' => '봉제',
        'inline' => '인라인',
        'ship' => '선적',
        'arrival' => '도착',
        'check' => '입고제품검수' ,
        'delivery' => '공장납기',
    ];
    const PRODUCTION_STEP_IDX = [
        10 => 'wash', //1 세탁
        20 => 'fabricConfirm', //2 원부자재 확정
        30 => 'fabricShip', //3 원부자재 선적
        40 => 'qc', //4 QC
        25 => 'cutting', //재단
        27 => 'sew', //봉제
        50 => 'inline', //5 인라인
        60 => 'ship', //6 선적
        70 => 'arrival', //7 도착
        80 => 'check', //8 검수
        90 => 'delivery', //9 납기완료
    ];

    const PROJECT_STATUS_COLOR = [
        15 =>'bg-light-gray',
        10 =>'bg-light-gray',
        20 =>'bg-light-gray',
        30 =>'bg-light-red',
        31 =>'bg-light-red',
        40 =>'bg-light-orange',
        41 =>'bg-light-orange',
        50 =>'bg-light-blue',
        60 =>'bg-light-green',
        90 =>'bg-light-green',
        91 =>'bg-light-green',
        92 =>'bg-light-green',
        //98 =>'bg-light-gray',
        //99 =>'bg-light-gray',
    ];

    const PROJECT_STATUS = [
        10 =>'영업대기',   // 대기 wait
        15 =>'사전영업',   // 대기 wait
        20 =>'기획',   // 영업완료~ complete 90 >= x >= 20 ==> 다 Complete...
        30 =>'제안',
        31 =>'제안서 확정대기',
        40 =>'샘플',
        41 =>'고객샘플 확정대기',
        50 =>'발주준비', //구 고객발주대기 <-- 작지 + 사양서도 여기서
        60 =>'발주', //
        90 =>'발주완료', //작지 정제. 납품수량 확인 ( 회계 반영 )
        91 =>'프로젝트 종결',
        97 =>'영업보류',   // 대기 wait
        98 =>'유찰',
        //99 =>'이탈', //이탈 프로젝트라는건 없다.
    ];

    /**
     * 프로젝트 중단 상태
     */
    const PROJECT_HOLD_STATUS = [
        97 =>'영업보류',   // 대기 wait
        98 =>'유찰',
        //99 =>'이탈', // 계약 종료 . 근데 프로젝트 자체에 이탈은 없다.
    ];


    const DESIGN_STATUS = [
        20 =>'기획',   // 영업완료~ complete
        30 =>'제안',
        31 =>'제안서확정대기',
        40 =>'샘플',
        41 =>'샘플확정대기',
        50 =>'발주준비',
        60 =>'발주',
        90 =>'발주완료',
        91 =>'프로젝트 종결',
    ];

    const ACCOUNTING_STATUS = [
        40 =>'샘플',
        41 =>'샘플확정대기',
        50 =>'발주준비',
        60 =>'발주',
        90 =>'발주완료',
        91 =>'프로젝트 종결',
    ];

    const PROJECT_STATUS_ALL_MAP = [
        10 =>'영업대기',
        11 =>'영업보류',   // 대기 wait
        15 =>'영업진행',
        20 =>'기획',
        30 =>'제안',
        31 =>'제안서확정대기',
        40 =>'샘플',
        41 =>'샘플확정대기',
        50 =>'발주준비',
        60 =>'발주',
        90 =>'발주완료',
        91 =>'프로젝트종결',
    ];

    const PROJECT_STATUS_PROC_MAP = [
        10 =>'영업대기',
        15 =>'사전영업',
        20 =>'기획',
        30 =>'제안',
        31 =>'제안서확정대기',
        40 =>'샘플',
        41 =>'샘플확정대기',
        50 =>'발주준비',
        60 =>'발주',
        90 =>'발주완료',
        91 =>'프로젝트종결',
    ];

    const PROJECT_CH_STATUS = [
        10 =>'영업대기',
        11 =>'영업보류',   // 대기 wait
        15 =>'영업진행',
        20 =>'기획',
        30 =>'제안',
        31 =>'제안서확정대기',
        40 =>'샘플',
        41 =>'샘플확정대기',
        50 =>'발주준비',
        60 =>'발주',
        //90 =>'발주완료',
        //91 =>'프로젝트종결',
        //98 =>'보류(미확정)',
        //99 =>'보류(확정)',
        //80 =>'단계이름없음',
    ];

/*    const PROJECT_STATUS = [
        10 =>'진행준비',
        15 =>'24FW진행준비',
        20 =>'디자인기획',
        30 =>'디자인제안',
        40 =>'샘플제작',
        50 =>'고객승인대기',
        60 =>'발주서(사양서)',
        //80 =>'생산관리',
        90 =>'발주/기획완료',
        98=>'보류(미확정)',
        99=>'보류(확정)',
    ];
    const PROJECT_STATUS_ALL_MAP = [
        10 =>'진행준비',
        15 =>'24FW진행준비',
        20 =>'디자인기획',
        30 =>'디자인제안',
        40 =>'샘플제작',
        50 =>'고객승인대기',
        60 =>'발주서(사양서)',
        80 =>'생산관리',
        90 =>'발주/기획완료',
        98=>'보류(미확정)',
        99=>'보류(확정)',
    ];
*/

    /**
     * 생산 납기 상태
     */
    const PROJECT_DELIVERY_STATUS = [
        0 => [
            'name' => '미설정',
            'color' => '#000000',
        ],
        1 => [
            'name' => '주시',
            'color' => '#ff7f00',
        ],
        2 => [
            'name' => '지연',
            'color' => '#ff0000',
        ],
        3 => [
            'name' => '양호',
            'color' => '#00a523',
        ],
    ];

    /**
     * 프로젝트 타입
     */
    const PROJECT_TYPE = [
        0 => '신규',             //신규
        2 => '공개입찰',          //신규
        6 => '리오더(개선)',      //신규
        8 => '리오더(신규)',      //신규
        5 => '샘플',             //신규

        1 => '리오더',        //리오더
        3 => '추가',          //리오더
        7 => '수정(A/S)',     //리오더
        4 => '기성복', //기성복
    ]; //신규/리오더/기성복
    //신규 타입
    const PROJECT_TYPE_N = [
        0 => '신규',             //신규
        2 => '공개입찰',          //신규
        6 => '리오더(개선)',        //신규
        8 => '리오더(신규)',        //신규
        5 => '샘플',             //신규
    ];
    const PROJECT_TYPE_R = [
        1 => '리오더',        //리오더
        3 => '추가',          //리오더
        7 => '수정(A/S)',     //리오더
        4 => '기성복', //기성복
    ];
    //0,2,5,6
    //1,3,7,4

    /**
     * 프로젝트 타입 중 신규 타입
     */
    const PROJECT_TYPE_NEW = [
        0 => '신규',
        2 => '공개입찰',
        4 => '기성복',
    ];

    //Defrecated...
    const PROJECT_TYPE_EN = [
        0 => 'N',
        1 => 'R',
        6 => 'R1',
        7 => 'C',
        8 => 'R2',
        2 => '입',
        3 => 'A',
        4 => '기',
        5 => 'S',
    ];

    const PROJECT_CONFIRM_TYPE = [
        'n' => [
            'class' => '',
            'name' => '준비',
        ],
        'r' => [
            'class' => 'text-blue',
            'name' => '승인요청',
        ],
        'p' => [
            'class' => 'text-green',
            'name' => '승인완료',
        ],
        'f' => [
            'class' => 'text-danger',
            'name' => '반려',
        ],
    ];
    const PROJECT_CONFIRM_TYPE_SIMPLE = [
        'n' => '준비',
        'r' => '승인요청',
        'p' => '승인',
        'f' => '반려',
    ];

    /**
     * 생산 스텝별 승인상태
     */
    const PRODUCE_CONFIRM_TYPE = [
        '' => [
            'class' => '',
            'name' => '준비',
        ],
        'r' => [
            'class' => 'text-blue',
            'name' => '승인요청',
        ],
        'y' => [
            'class' => 'text-green',
            'name' => '승인완료',
        ],
        'n' => [
            'class' => 'text-danger',
            'name' => '반려',
        ],
    ];

    const RECOMMEND_TYPE = [
        1 => '기획서',
        2 => '제안서',
        4 => '샘플',
        8 => '견적',
    ];
    const RECOMMEND_CONTENTS = [
        1 => '디자인',
        2 => '운영방안',
        4 => '품질관리',
        8 => '폐쇄몰',
        16 => '차별화',
    ];

    const FABRIC_BUY_TYPE = [
        1 => 'kr',
        2 => 'cn',
        4 => 'market',
    ];

    const PROJECT_DESIGN_FIELD = [
        ['title' => '기획서 예정일'   ,'name' => 'planDt'        ,'recommendType'=>1],
        ['title' => '기획서 완료일'   ,'name' => 'planEndDt'     ,'recommendType'=>1],
        ['title' => '제안서 예정일'   ,'name' => 'proposalDt'    ,'recommendType'=>2],
        ['title' => '제안서 완료일'   ,'name' => 'proposalEndDt' ,'recommendType'=>2],
        ['title' => '샘플 예정일'     ,'name' => 'sampleStartDt' ,'recommendType'=>4],
        ['title' => '샘플 완료일'     ,'name' => 'sampleEndDt'   ,'recommendType'=>4],
    ];

    //FILE CONSTANT
    const PROJECT_FILE = [
        ['fieldName' => 'filePlan'      , 'title' => '기획서', 'accept' => true],
        ['fieldName' => 'fileProposal'  , 'title' => '제안서', 'accept' => true],
        ['fieldName' => 'fileWork'      , 'title' => '작업지시서', 'accept' => true],
        ['fieldName' => 'fileConfirm'   , 'title' => '사양서', 'accept' => true],
    ];

    const PROJECT_ETC_FILE = [
        ['fieldName' => 'fileSalesStrategy', 'title' => '영업 전략'],
        ['fieldName' => 'fileMeetingReport', 'title' => '고객사 회의록'],
        ['fieldName' => 'fileSampleGuide', 'title' => '샘플 안내서'],
        ['fieldName' => 'fileMeeting', 'title' => '입찰 추가 정보', 'accept' => false],
        ['fieldName' => 'fileEtc1', 'title' => '미팅 추가 정보 업로드'],
        ['fieldName' => 'fileEtc2', 'title' => '견적서'],
        ['fieldName' => 'fileEtc3', 'title' => '계약서'],
        ['fieldName' => 'fileEtc4', 'title' => '영업확정서'],
        ['fieldName' => 'fileEtc5', 'title' => '근무환경조사자료'], //구 샘플웨어링 => 현 근무환경자료
        ['fieldName' => 'fileEtc6', 'title' => '원부자재내역'],
        ['fieldName' => 'fileEtc7', 'title' => '기타파일'],
        ['fieldName' => 'filePre1', 'title' => '디자인 제안서'],
        ['fieldName' => 'filePre2', 'title' => '개선 제안서'],
        ['fieldName' => 'filePre3', 'title' => '선호도 조사'],
        ['fieldName' => 'filePre4', 'title' => '샘플 테스트'],
        ['fieldName' => 'fileDeliveryReport', 'title' => '납품보고서'],
        ['fieldName' => 'fileDeliveryPlan', 'title' => '납품계획'],
        ['fieldName' => 'fileBarcode', 'title' => '바코드'],
    ];

    const PREPARED_FILE_ESTIMATE = [
        [
            'fieldName' => 'filePreparedEstimateMs1',
            'title' => 'MS요청 관련파일',
        ],
    ];
    const PREPARED_FILE_BT = [
        [
            'fieldName' => 'filePreparedBt',
            'title' => 'BT의뢰서',
        ],
        [
            'fieldName' => 'filePreparedBtPrdResult',
            'title' => 'BT결과서',
        ],
    ];
    const PREPARED_FILE_ORDER = [
        [
            'fieldName' => 'filePreparedOrder',
            'title' => '가발주',
        ],
    ];

    const PREPARED_FILE = [
        ['fieldName' => 'filePreparedBt', 'title' => 'BT의뢰서', 'noRev' => true], //리비젼 관리 안함
        ['fieldName' => 'filePreparedEstimateMs1', 'title' => '가견적(이노버-의뢰)', 'noRev' => true], //리비젼 관리 안함
        ['fieldName' => 'filePreparedEstimateMs2', 'title' => '가견적(이노버검토)', 'noRev' => true], //리비젼 관리 안함
        ['fieldName' => 'filePreparedEstimate', 'title' => '가견적(생산처)', 'noRev' => true], //리비젼 관리 안함
        ['fieldName' => 'filePreparedCost', 'title' => '생산확정견적', 'noRev' => true], //리비젼 관리 안함
        ['fieldName' => 'filePreparedOrder', 'title' => '가발주파일', 'noRev' => true], //기존 fake order / 리비젼 관리 안함
        ['fieldName' => 'filePreparedBtPrdResult', 'title' => 'BT결과(생산처)', 'noRev' => true], //리비젼 관리 안함
        ['fieldName' => 'filePreparedBtMsResult', 'title' => 'BT결과(MS)', 'noRev' => true], //리비젼 관리 안함
        //작업지시서는 fileWork 에 올라간다. (주요 파일)
    ];

    const PRODUCE_TYPE = [
        0 => '미정',
        1 => '완사입',
        2 => 'CMT',
        3 => '임가공',
    ];

    /**
     * 코멘트 단계
     */
    const PROJECT_COMMENT_DIV = [
        'meetingMemo' => '미팅준비',
        'planMemo' => '디자인기획',
        'proposalMemo' => '디자인제안',
        'sampleMemo' => '샘플제안',
        'customerWaitMemo' => '고객승인대기',
        'workMemo' => '발주서(사양서)',
        'produce' => '생산관리',
        'reserved' => '보류',
    ];

    const PROJECT_STEP_COMMENT_DIV = [
        '10' => 'meetingMemo',
        '20' => 'planMemo',
        '30' => 'proposalMemo',
        '40' => 'sampleMemo',
        '50' => 'customerWaitMemo',
        '60' => 'workMemo',
        '80' => 'produce',
        '90' => 'produce',
        '99' => 'reserved',
    ];

    const GLOBAL_DELIVERY_DIV = [
        'n' => '-',
        'ship' => '배',
        'air'  => '항공',
    ];

    const IMS_SEASON_ICON = [
        'FW' => '<i class="fa fa-2x fa-snowflake-o" aria-hidden="true" style="color:#00aded"></i>',
        'SF' => '<i class="fa fa-2x fa-leaf" aria-hidden="true" style="color:#ff6600"></i>',
        'SP' => '<i class="fa fa-2x fa-leaf" aria-hidden="true" style="color:#1b9609"></i>',
        'SU' => '<i class="fa fa-2x fa-sun-o text-danger" aria-hidden="true"></i>',
        'FA' => '<i class="fa fa-2x fa-leaf" aria-hidden="true" style="color:#ff6600"></i>',
        'WI' => '<i class="fa fa-2x fa-snowflake-o" aria-hidden="true" style="color:#00aded"></i>',
        'SS' => '<i class="fa fa-2x fa-sun-o text-danger" aria-hidden="true"></i>',
        '' => '<span><b>ALL</b></span>',
    ];

    const IMS_PRD_STATUS = [
        '0' => '미확정',
        '1' => '견적',
        '2' => '확정',
    ];

    /**
     * 원단상태(QB상태)
     */
    const IMS_FABRIC_STATUS = [
        '' => [
            'name' => '진행대기',
            'color' => 'back-point-muted'
        ],
        '0' => [
            'name' => '진행대기',
            'color' => 'back-point-muted'
        ],
        '1' => [
            'name' => '수배중',
            'color' => 'back-point-blue'
        ],
        '2' => [
            'name' => '수배완료',
            'color' => 'back-point-green'
        ],
        '3' => [
            'name' => '리오더',
            'color' => 'back-point-orange'
        ],
        '4' => [
            'name' => '반려',
            'color' => 'back-point-red'
        ],
        '5' => [
            'name' => '사용안함',
            'color' => 'back-point-red'
        ],
    ];

    /**
     * BT상태
     */
    const IMS_BT_STATUS = [
        '' => [
            'name' => '진행대기',
            'color' => 'back-point-muted'
        ],
        '0' => [
            'name' => '진행대기',
            'color' => 'back-point-muted'
        ],
        '1' => [
            'name' => '진행중',
            'color' => ''
        ],
        '2' => [
            'name' => '확정',
            'color' => 'back-point-blue'
        ],
        '3' => [
            'name' => '리오더',
            'color' => 'back-point-orange'
        ],
        '4' => [
            'name' => '반려',
            'color' => 'back-point-red'
        ],
    ];

    /**
     * BT요청 상태.
     */
    const IMS_BT_REQ_STATUS = [
        0 => '진행대기',
        1 => '요청', //사용
        2 => '처리중',
        3 => '처리완료', //사용
        4 => '처리불가',
        5 => '확정', //사용
        6 => '반려', //반려 (구 데이터)
    ];

    /**
     * 고객 납기 상태
     */
    const IMS_CUSTOMER_DELIVERY_STATUS = [
        0 => '-',
        1 => '일정 협의중',
        2 => '90일 확보',
        3 => '120일 확보',
        4 => '기간 협의 불가',
    ];

    /**
     * 상품 진행 상태
     */
    const IMS_PRD_PROC_STATUS = [
        0 => '대기',
        1 => '진행',
        2 => '완료',
    ];
    const IMS_PROC_STATUS = [
        0 => ['name'=>'대기', 'icon'=>'<span class="text-muted">-</span>'],
        1 => ['name'=>'진행', 'icon'=>'<i class="fa fa-play sl-blue" aria-hidden="true" style=""></i>'],
        2 => ['name'=>'완료', 'icon'=>'<i class="fa fa-lg fa-check-circle text-green" aria-hidden="true"></i>'],
    ];

    const IMS_MEETING_STATUS = [
        0 => '미팅준비',
        1 => '미팅완료',
    ];


    /**
     * 퀄리티/BT 요청 타입
     */
    const IMS_QB_REQ_TYPE = [
        0 => '없음',
        1 => '퀄리티',
        2 => 'BT',
        /*4 => 'BULK',*/
    ];

    /**
     * 관리 스케쥴 타입 (project_ext에 스케쥴 관련)
     */
    const PROJECT_SCHEDULE_TYPE = [
        'ex' => ['name'=>'예정일', 'type'=>'expectedDt'],
        'cp' => ['name'=>'완료일', 'type'=>'completeDt'],
        'tx' => ['name'=>'대체', 'type'=>'alterText'],
        'st' => ['name'=>'상태', 'type'=>'fieldStatus'],
    ];

    /**
     * 프로젝트 관리 스케쥴 타입
     */
    const PROJECT_MAIN_SCHEDULE_LIST = [
        //'meeting' => '미팅',
        'plan' => '기획', //완료자동
        'proposal' => '제안서', //완료자동
        'sampleInform' => '샘플발송',
        'order' => '작지/사양서', //완료자동
        'productionOrder' => '발주', //완료자동
    ];
    const PROJECT_DESIGN_SCHEDULE_LIST = [
        'plan' => '기획',
        'proposal' => '제안서',
        'sampleOrder' => '샘플지시서',
        'sampleComplete' => '샘플실완료',
        'sampleReview' => '샘플리뷰',
        'sampleInform' => '샘플발송',
        'sampleConfirm' => '샘플확정',
        'order' => '작지/사양서',
    ];
    const PROJECT_DETAIL_SCHEDULE_LIST = [
        //'custSample' => '고객샘플확보',
        //'inspection' => '현장조사',
        //'meeting' => '미팅',
        'plan' => '기획',
        'proposal' => '제안서',
        'sampleOrder' => '샘플지시서',
        'sampleComplete' => '샘플마감',
        'sampleReview' => '샘플리뷰',
        'sampleInform' => '샘플발송',
        'sampleConfirm' => '샘플확정',
        'order' => '작지/사양서',
        //'orderConfirm' => '사양서 확정',
        'productionOrder' => '발주',
    ];

    const PROJECT_SALES_SCHEDULE_LIST = [
        'contactManager' => '담당자 컨텍',
        'meetingReady' => '사전미팅',
        'sampleCust' => '샘플 확보',
        'researchField' => '현장 리서치',
        'sampleProduce' => '샘플 제작',
        'sampleTest' => '샘플 현장 테스트',
        'meetingProposal' => '제안미팅',
    ];

    /**
     * 프로젝트 관리 스케쥴 타입
     */
    const PROJECT_SCHEDULE_LIST = [
        'custSample' => '샘플확보',
        'inspection' => '현장조사',
        'meeting' => '미팅',
        'plan' => '기획',
        'proposal' => '제안서',
        'sampleOrder' => '샘플지시서',
        'sampleComplete' => '샘플실완료',
        'sampleReview' => '샘플리뷰',
        'sampleInform' => '샘플발송',
        'sampleConfirm' => '샘플확정',
        'order' => '작지/사양서',
        'orderConfirm' => '사양서 확정',
        'productionOrder' => '발주',
        'projectComplete' => '납품',
    ];

    /**
     * 프로젝트 추가 정보
     */
    const PROJECT_ADD_INFO =  [
        //제안
        'plan' => ['name' => '기획서일정', 'e'=>'영업', 'c'=>'디자인'],
        'proposal' => ['name' => '제안서일정', 'e'=>'영업', 'c'=>'디자인'],
        'custInform' => ['name' => '제안서 발송', 'e'=>'영업', 'c'=>'영업'],        // 제안서발송 단계 이동

        //샘플
        'sampleOrder' => ['name' => '샘플지시서 일정', 'e'=>'디자인', 'c'=>'디자인'],
        'sampleOut' => ['name' => '샘플완료일 일정', 'e'=>'디자인', 'c'=>'디자인'],
        'custSampleInform' => ['name' => '샘플발송', 'e'=>'영업', 'c'=>'영업'],     // 샘플발송 단계 이동
        
        //발주준비
        'custOrder' => ['name' => '고객발주(아소트)', 'e'=>'영업', 'c'=>'영업'],
        'order' => ['name' => '작지/사양서', 'e'=>'디자인', 'c'=>'디자인'], //작지/사양서   // 발주준비완료 단계 이동
        'custSpec' => ['name' => '사양서발송일', 'e'=>'영업', 'c'=>'영업'],

        'meetingInfo' => ['name' => '미팅정보','modifyType'=>'mix8'],
    ];

    const PROJECT_ADD_INFO_KEY = [
        'expectedDt',
        'completeDt',
        'memo',
        'etcMemo',
        'fieldStatus',
        'alterText',
    ];
    const PROJECT_ADD_INFO_KEY_SIMPLE = [
        'expectedDt',
        'completeDt',
        'alterText',
    ];

    const PROJECT_BID_TYPE = [
        'single' => '단독입찰',
        'public' => '공개입찰',
    ];

    const ISSUE_TYPE = [
        'issue' => '이슈',
        'order' => '발주 특이사항',
        'meeting' => '협상/미팅',
        'req' => '고객 요청',
        'delivery' => '납품정보',
        'work' => '작지수정',
        //'fabric' => '원단보유현황',
        'cost' => '비용',
    ];

    /**
     * 고객 코멘트 접수 타입
     */
    const INBOUND_TYPE = [
        '' => '미정',
        'mail' => '메일',
        'phone' => '전화',
        'meeting' => '미팅',
    ];

    /**
     * 작지/사양서 필요항목 (승인까지 필요로 한다 )
     */
    const EWORK_TYPE = [
        'main' => '메인',
        'batek' =>'바텍',
        'mark' =>'마크',
        'care' =>'케어',
        'spec' =>'스펙',
        'material' =>'자재',
        'packing' =>'포장',
        'warn' =>'주의',
    ];

    const PRICE_STATUS = [
        0 => '추정',
        1 => '타겟',
        2 => '견적',
        3 => '확정',
    ];

    //카테고리 타입 정의
    const CATE_TYPE_MATERIAL = 'material';

    //YN 타입
    const YES_OR_NO_TYPE  = ['y' => '예','n' => '아니오','x' => '해당없음' ,'' => '미확인'];
    const ABLE_TYPE  = ['y' => '가능','n' => '불가' ,'' => '미확인'];
    const EXIST_TYPE = ['y' => '유'  ,'n' => '무'  ,'' => '미확인'];
    const EXIST_TYPE2 = ['y' => '유상','n' => '무상','' => '미확인'];
    const EXIST_TYPE3 = ['y' => '있음','n' => '없음','' => '미확인'];
    const EXIST_TYPE4 = ['y' => '유'  ,'n' => '무','g' => '발주시협의','' => '미확인'];
    const EXIST_TYPE5 = ['y' => '유상'  ,'n' => '무상','g' => '발주시협의','' => '미확인'];
    const RATING_TYPE = ['top' => '상','mid' => '중','bottom' => '하','' => '미확인',];
    const RATING_TYPE2  = ['0' => '미확인','1' => '하(쉬움)' ,'2' => '중(보통)','4' => '상(어려움)']; //1,2,4,8.. (검색시 편함)
    const PAYMENT_TYPE = ['cash' => '현금','note' => '어음','' => '미확인',];
    const PAY_FABRIC_TYPE = ['x' => '결제X'  ,'y' => '완제품 납품 시 결제', 'n'=>'해당없음','' => '미확인'];
    const SCHEDULE_SHARE_TYPE = ['y' => '공유','' => '미공유',];
    const PROCESS_TYPE = ['y' => '진행'  ,'n' => '미진행'  ,'' => '미확인']; //
    const PACKING_TYPE = ['branch' => '지사'  ,'dept' => '지사/부서','nameplate' => '개인(명찰)','etc' => '기타'  ,'' => '미확인']; //□ 지사 □ 지사內 부서 □ 개인(명찰)
    const PAY_SHIPPING_TYPE = ['free' => '무상'  ,'pay' => '유상', 'nego' => '발주시협의' ,'' => '미확인'];
    const BATCH_TYPE = ['batch' => '일괄'  ,'always' => '상시', 'n'=>'해당없음' ,'' => '미확인'];
    const PAYMENT_TYPE2 = ['cust' => '고객사 결제'  ,'after' => '출고 후 정산', 'n'=>'해당없음' ,'' => '미확인'];
    const USED_TYPE = ['y' => '사용'  ,'n' => '미사용' ,'' => '미확인'];
    const AFTER_PAYMENT_TYPE = ['admin' => '본사일괄 정산'  ,'customer' => '지점별결제' ,'' => '미확인'];
    const AFTER_PAYMENT_PERIOD = ['month' => '월별', 'season' => '분기별'  ,'nothing' => '정산없음' ,'' => '미확인'];
    const THUMBNAIL_TYPE = ['pic' => '촬영', 'ai' => '일러스트' ,'' => '미확인'];
    const MEMBER_JOIN_TYPE = ['batch' => '엑셀일괄등록', 'customer' => '개별가입' ,'' => '미확인'];
    const CONTRACT_PAY_TYPE = ['contract' => '계약금', 'remain' => '잔금', 'nothing' => '없음' ,'' => '미확인'];
    const INCLUDE_TYPE  = ['y' => '포함','n' => '미포함' ,'' => '미확인'];
    const INCLUDE_TYPE_SIMPLE  = ['y' => '포함','n' => '미포함'];
    const SEX_TYPE  = ['male' => '남자','female' => '여자' ,'' => '미확인'];
    const SEX_CODE = ['m'=>'남자','f'=>'여자',''=>'공용'];
    const CUST_ESTIMATE_TYPE  = ['estimate' => '가견적','confirm' => '확정견적'];
    const CUST_ESTIMATE_STATUS  = ['n' => '','r' => '' ,'p' => '고객견적승인','f' => '재요청'];
    const SCHEDULE_TYPE  = ['0' => '선택','1' => '중요' ,'2' => '연차','3' => '미팅','5' => '출장','6' => '납품' ,'4' => '기타'];
    const MATERIALS_ON_HAND  = ['0' => '미확인','1' => '상시보유' ,'2' => '제작필요','4' => '유동적']; //1,2,4,8.. (검색시 편함)

    const STYLE_PROC_TYPE  = ['0' => '미확인','1' => '신규 기획' ,'2' => '소재 개선','3' => '기존 동일'];
    const CUST_SAMPLE_TYPE  = ['0' => '미확인','1' => '제공 불가' ,'2' => '추후 반납','3' => '훼손 가능'];
    const PERIOD_TYPE  = ['0' => '미확인','1' => '단기' ,'2' => '중기','3' => '장기'];

    const MALL_1 = ['' => '미확인','1' => '엑셀 일괄등록' ,'2' => '주문자 개별 가입' ,'etc' => '기타'];
    const MALL_2 = ['' => '미확인','1' => '본사 승인 후 출고' ,'2' => '본사 출고 관여 없음' ,'etc' => '기타'];
    const MALL_3 = ['' => '미확인','1' => '3PL 입고 완료 후 출고', '2' => '3PL 입고 전 선출고 요청','etc'=>'기타'];
    const MALL_4 = ['' => '미확인','1' => '주문자 무상 지급', '2' => '주문자 개별 구매','3' => '본사+구매자 분할 구매' ];
    const MALL_4_1 = ['' => '미확인','1' => '발주 총금액 본사 일괄 결제', '2' => '출고 수량만큼 본사 월말 결제' ];
    const MALL_5 = ['' => '미확인','1' => '무상','2' => '유상','etc' => '기타'];
    const MALL_6 = ['' => '미확인','1' => '무상(피복 가격에 포함)','2' => '유상(물류 입고 금액 7%)','etc' => '기타'];
    const MALL_7 = ['' => '미확인','1' => '월별 결제','2' => '년간 결제','3'=>'결재없음','etc'=>'기타'];
    const MALL_8 = ['' => '미확인','1' => '출고 건별 본사 월말 결제','2' => '주문시 주문자 결제 (선불,착불 선택)','3' => '주문자 착불만 가능','etc'=>'기타'];
    const MALL_9 = ['' => '미확인','1' => '생산 예정','2' => '생산 안함','3' => '논의 후 진행','etc' => '기타'];

    //프로젝트 정보 타입
    const PRJ_INFO_01 = ['' => '미확인','1' => '임직원 선호도 투표','2' => '제안 PT','3' => '의사 결정권자 결정','etc' => '기타'];
    const PRJ_INFO_02 = ['' => '미확인','1' => '최저가','2' => '디자인','3' => '배점기준','etc' => '기타'];
    const PRJ_INFO_03 = ['1' => '디자인 및 브랜드이미지 개선','2' => '내구성 부족','3' => '불편한 착용감','4' => '원가 절감']; //변경사유
    const PRJ_INFO_04 = ['' => '미확인', '1' => '개별세탁','2' => '공용세탁','3' => '세탁전문업체 이용']; //세탁구분
    const PRJ_INFO_05 = ['' => '미확인', '1' => '전체무상(벌수 제한 없음)','2' => '계약조건 스타일당 1벌 무상(1벌이후 스타일당 50만원 청구)']; //세탁구분

    const CUST_INFO_01 = ['' => '미확인','1' => '공통로고','2' => '협력사 별도 로고'];
    const CUST_INFO_02 = ['' => '미확인','1' => '사면봉제','2' => '벨크로','3' => '명찰걸이(핀셋타입)'];
    const CUST_INFO_03 = ['' => '미확인','1' => '상 (물건 상하차)','2' => '중 (단순포장)','3' => '하 (사무업무)'];


    //인터페이스 변경 신규 코드 (24/12/30 ~ )
    const SALES_STATUS  = [ // 0 : 코드명 , 1 : 색상 , 2 : icon
        'wait' => '대기',
        'proc' => '준비',
        'complete' => '영업완료',
        'imp'  => '기획불가',
        'hold' => '영업보류',
        'fail' => '유찰',
    ];

    //디자인 업무 타입
    const DESIGN_WORK_TYPE = [
        0 => '-',
        1 => '단가',
        2 => '디자인(신규)',
        3 => '디자인(개선)',
        4 => '리오더(개선)',
    ];

    //디자인 업무 타입
    const DESIGN_JOIN_TYPE = [
        0 => '-',
        1 => '디자인제안서',
        2 => '개선제안서',
        4 => '선호도조사',
        8 => '샘플테스트',
    ];

    //진행 타입
    const BID_TYPE = [
        //'' => '입찰타입미설정', //미팅 리스트
        'single'  => '단독', //미팅 리스트
        'bid' => '입찰',  //입찰 리스트
        'costBid' => '비딩',  //입찰 리스트
    ];
    const BID_TYPE_DP = [
        //'' => '미팅', //미팅 리스트
        'bid' => '입찰', //입찰 리스트 
        'costBid' => '비딩', //입찰 리스트
        'single'  => '미팅', //미팅 리스트
    ];

    const HTML_ICON = [
        'SUCCESS' => '<i aria-hidden="true" class="fa fa-check-circle text-green cursor-pointer"></i>',
        'PROC' => '<i aria-hidden="true" class="fa fa-play-circle sl-blue cursor-pointer"></i>',
        'STOP' => '<i aria-hidden="true" class="fa fa-stop-circle color-gray cursor-pointer"></i>',
        'REJECT' => '<i aria-hidden="true" class="fa fa-times-circle text-danger cursor-pointer"></i>'
    ];

}
