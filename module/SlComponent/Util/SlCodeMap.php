<?php
namespace SlComponent\Util;

use SlComponent\Database\DBUtil;
use SlComponent\Database\SearchVo;

/**
 * Sitelab Code Key List
 * Class SlCode
 * @package SlComponent\Util
 */
class SlCodeMap {

    const PRIVATE_PAYMENT_GOODS = '1000000111';
    const PRIVATE_PAYMENT_GOODS_DEV = '1000000000';
    const PRIVATE_PAYMENT_CATEGORY = '017';
    const PRIVATE_PAYMENT_CATEGORY_DEV = '021';

    const NAS_SERVER = '192.168.0.95';
    const NAS_SERVER_DEV = 'bnshop.net/test/';

    const TEST_MAIL_SEND = false; //테스트 서버 메일 발송 여부

    const PREORDER_CHECK_DT = '2025-09-15 11:39:00';//선 주문건 특정 일자 부터 체크하게 한다.

    //구매제한 2배 늘리는 상품 (OEK 하계티)
    CONST BUY_LIMIT_DOUBLE_GOODS = [
        1000000373, //23하계
        1000000477, //24하계
        1000000617, //26하계
    ];
    //구매 불가
    CONST BUY_LIMIT_ZERO_GOODS = [
        1000000370,
        1000000369,
        1000000368,
        1000000350,
        1000000349,
        //OEK 하계
        //1000000374,

        //FOD
        1000000409,
        1000000416,
    ];

    //고객사 관리자 페이지 주문 승인에서 제외할 아이디
    CONST SCM_ORDER_EXCLUDE_MEM_NO = [
        1, 15156 , 15157 , 15221
    ];

    //게시판 알림 메일 발송
    CONST BOARD_MAIL_LIST = [
        'jhsong@msinnover.com',
        'hjseo@msinnover.com',
    ];

    //출고지시 메일 발송.
    CONST ORDER_MAIL_LIST = [
        'jhsong@msinnover.com',
        'syhan@msinnover.com',
        /*'bluesky1478@hanmail.net',*/
        /*'nrlee@msinnover.com',*/
        'hjseo@msinnover.com',
        'jsjung@msinnover.com',
    ];

    CONST SAMYOUNG_MAIL_LIST = [
        'gshappyday@sylogis.co.kr', //방광식 파트장
        'kimjitae@sylogis.co.kr',   //김지태 팀장
        'jeongsiwon@sylogis.co.kr', //정시원 주임
    ];

    CONST GOLFZON_MAIL_LIST = [
        'solle33@golfzon.com',
    ];
    CONST GOLFZON_CC_MAIL_LIST = [
        'kypark@golfzon.com',
        'shimsj@golfzon.com',
    ];


    //------------ 설정 관련 코드 시작 --------------//
    const HANKOOK_MANAGER_ID = [
        'hkmaster', 'test_hk',
    ];

    const HYUNDAE_MANAGER_ID = [
        'hundai_ev'
        // , 'test_hdev' , 'b1478'
    ];

    const TKE_MANAGER_ID = [
        'TKE-MS',
        'b1478',
    ];

    const OEK_MANAGER_ID = [
        '601348', //황선우
        'test_oek1',
        'test_oek2',
        'b1478',
    ];

    /**
     * 제작상품 안내
     */
    const PRODUCE_GOODS_INFO = [
        //'1000002052' => 1000002052, //Test
        '1000000252' => 1000000254, //Real
    ];

    /**
     * 구매제한 셋트
     */
    const SET_GOODS_LIST = [
        'TKE_GOODS1' => [
            1000000254,
            1000000253,
            1000000252,
            1000000251,
        ],
    ];
    const SET_GOODS_MAP = [
        1000000254 => 'TKE_GOODS1',
        1000000253 => 'TKE_GOODS1',
        1000000252 => 'TKE_GOODS1',
        1000000251 => 'TKE_GOODS1',
    ];

    const NO_LOGIN_VIEW_SITE = [
        //'http://selcmall.com/' => [ 'cateCd' => '015' , 'scmNo' => '13' ],
        //'http://orangetee.shop/' => [ 'cateCd' => '035' , 'scmNo' => '35' ]
        /*, 'http://bcloud1478.godomall.com/' => [ 'cateCd' => '002' , 'scmNo' => '6' ]*/
    ];

    /**
     *  다른 스킨 사용하는 사이트
     */
    const OTHER_SKIN_SITE = [
        'hankookb2b.co.kr' => 'hankook'
        , 'otisb2b.co.kr' => 'otis'
        , 'oekb2b.kr' => 'oek'
        , 'selcmall.com' => 'selc'
        , 'tkeb2b.co.kr' => 'tke'
        //, 'bcloud1478.godomall.com' => 'hankook' //테스트용
        //, 'bcloud1478.godomall.com' => 'otis'
        //, 'bcloud1478.godomall.com' => 'selc'
        //, 'bcloud1478.godomall.com' => 'lotte'
        , 'bcloud1478.godomall.com' => 'asiana'
        //, 'orangetee.shop' => 'orange'
        , 'orangetee.shop' => 'lotte'
        , 'asianab2b.shop' => 'asiana'
        //, 'msinnover4.godomall.com' => 'asiana'
    ];
    const OTHER_SKIN_CLASS = [
        'hankook' => 'hankook',
        'otis' => 'hankook',
        'tke' => 'hankook',
        'oek' => 'hankook',
        'asiana' => 'hankook',
        'selc' => 'selc',
        'orange' => 'orange',
        'lotte' => 'lotte',
    ];
    const OTHER_SKIN_COMP_NAME = [
        'hankook' => '한국타이어',
        'otis' => '오티스(OSE)',
        'asiana' => '아시아나에어포트',
        'selc' => '삼성전자로지텍',
        'oek' => '오티스(OEK)',
        'orange' => '이준석캠프',
        'lotte' => '롯데칠성',
    ];

    const OTHER_SKIN_MAP = [
        '6' => 'hankookb2b.co.kr',  //한국타이어
        '11' => 'otisb2b.co.kr',  //오티스
        //'13' => 'selcmall.com',  //삼성전자로지텍
        //'21' => 'oek.kr',  //오티스 OEK
    ];

    const HANKOOK_TYPE = [
      1 => '티스테이션'
      , 2 => '한국타이어'
      //, 4 => '더타이어샵'
      //, 8 => 'TBX'
    ];

    const MEMBER_TYPE = [
        1 => '정규직원'
        , 2 => '파트너사'
    ];

    const HANKOOK_GOODS_OPTION_TYPE = [
        1 => '티스테이션(TS)'
        //, 4 => '더타이어샵(TTS)'
        //, 8 => 'TBX'
    ];

    /**
     * 승인처리 하는 공급사
     */
    /*const SCM_USE_ORDER_ACCEPT = [
        '4' => true   //제일건설 주식회사
        , '7' => true   //MS
    ];*/

    const SCM_MENU_ACCEPT_TOP = 'slab00016';
    const SCM_MENU_ACCEPT_MEMBER = 'slab00017';
    const SCM_MENU_ACCEPT_ORDER = 'slab00018';

    //초도 물량 있는 공급사
    const SCM_ORDER_INIT = [
        4 => '2011301048395703',
        /*2 => '2011301048395703'*/
    ];

    //------------ 설정 관련 코드 끝 --------------//
    /**
     *  공급사별 카테고리
     *  -> 추후에는 아예 카테고리에서 제외하는 방식 ?
     * ★ ERP 서비스 의 3PL 상품 SCM 분리 처리 하기 필수!
     */
/*    const SCM_CATEGORY_MAP = [
        '2' => '005'    //골프존
        , '3' => '006'  //혼다코리아
        , '4' => '008'  //제일건설 주식회사
        , '5' => '003'  //맥스
        , '6' => '002'  //한국타이어
        , '7' => '009'  //이노버
        , '8' => '010'  //TKE(티센크루프)
        , '9' => '011'  //린나이
        , '10' => '012'  //무영건설
        , '11' => '013'  //오티스
        , '12' => '014'  //영구크린
        , '13' => '015'  //삼성전자로지텍
        , '14' => '018'  //미쓰비시 (근무복주문)
        , '15' => '019'  //KTNG
        , '16' => '020'  //미쓰비시 (설치부)
        , '19' => '021'  //경동나비엔
        , '20' => '022'  //한전산업개발
        , '21' => '023'  //오티스(OEK)
        , '22' => '024'  //한국공항
        , '23' => '025'  //동양건설산업
        , '24' => '026'  //반도건설
        , '25' => '027'  //빙그레
        , '26' => '028'  //오티스(FOD)
        , '29' => '029'  //반도건설(총무)
        , '30' => '030'  //퍼시스
        , '31' => '031'  //타타대우
        , '32' => '032'  //현대엘베
        , '33' => '032'  //오티스 기성복
        , '34' => '034'  //아시아나
        , '35' => '035'  //이준석캠프
        , '36' => '036'  //교세라
        , '37' => ''  //승인대기
        , '48' => '037'  //파이널 체대 입시
        , '49' => '038'  //롯데칠성
    ];*/
    //ErpService :: SCM_DIVDE_CONTENTS 수정할것.
    //ErpService :: STOCK COMPARE 수정할것.

    const REFUND_TYPE = [
        'cash' => '현금',
        'card' => '카드',
        'deposit' => '예치금',
    ];

    /**
     * 재고 상품별 합쳐서 보여주는 사이트
     */
    const STATISTICS_MERGE = [
        6, 7
    ];

    /**
     *  재고 유형
     */
    const STOCK_TYPE = [
        '1'=>'입고'
        , '2'=>'출고'
    ];

    /**
     *  재고 사유
     */
    const STOCK_REASON = [
        '0'=>'기타'
        , '1'=>'신규 입고'
        , '2'=>'관리자수정 입고'
        , '3'=>'환불로 인한 입고'
        , '4'=>'교환으로 인한 입고'
        , '5'=>'관리자수정 출고'
        , '6'=>'상품구매 출고'
        , '7'=>'교환으로 인한 출고'
        , '8'=>'취소로 인한 입고'
        , '9'=>'반품으로 인한 입고'
        , '10'=>'재고관리 입고'
        , '11'=>'재고관리 출고'
        , '12'=>'출고불가 원복 입고'
        , '13'=>'공유 재고 입고'
        , '14'=>'공유 재고 출고'
    ];

    /**
     * 주문시 재고사유
     */
    const ORDER_STOCK_REASON = [
        'r'=>'3'    //환불로 인한 입고
        , 'e'=>'4'  //교환으로 인한 입고
        , 'c'=>'8'  //취소로 인한 입고
        , 'b'=>'9'  //반품으로 인한 입고
    ];

    /**
     * 주문시 취소사유
     */
    const ORDER_CANCEL_REASON = [
        'r'=>'2'    //환불로 인한 입고
        , 'e'=>'3'  //교환으로 인한 입고
        , 'c'=>'4'  //취소로 인한 입고
        , 'b'=>'5'  //반품으로 인한 입고
        , 'f'=>'6'  //반품으로 인한 입고
    ];

    /**
     * 취소 사유
     */
    const CANCEL_REASON = [
        '1' => '구매'
        , '2'=>'환불'
        , '3'=>'교환'
        , '4'=>'취소'
        , '5'=>'반품'
        , '6'=>'실패'
        , '7'=>'취소복원'
        , '8'=>'상품추가'
        , '9'=>'교환철회'
        , '10'=>'타상품교환'
        , '11'=>'맞교환'
     ];

    /**
     *  클레임 말머리명
     */
    /*const NEW_CLAIM_NAME = [
        'A/S' => '요청접수'
        ,'교환' => '접수완료'
        ,'반품/환불' => '처리중'
    ];*/

    /**
     * (신규) 클레임 처리 상태
     */
    const NEW_CLAIM_STATUS = [
        0 => '접수',
        1 => '처리중',
        2 => '처리완료',
        9 => '처리불가',
    ];
    const NEW_CLAIM_STATUS_COLOR = [
        0 => '',
        1 => '',
        2 => 'text-green',
        9 => 'text-danger',
    ];

    /**
     *  클레임 처리 상태
     */
    const CLAIM_STATUS = [
      '1' => '요청접수'
      ,'2' => '접수완료'
      ,'3' => '처리중'
      ,'4' => '처리완료'
    ];

    /**
     *  클레임 타입
     */
    const CLAIM_TYPE = [
      'exchange' => '교환'
      ,'as' => 'A/S'
      ,'back' => '반품/환불'
      /*,'refund' => '반품/환불' //FIXME 마이그 할 때만. 필요 나중에 지우기*/
    ];

    /**
     *  클레임 타입(게시판용)
     */
    const CLAIM_BOARD_TYPE = [
        'general' => '일반문의'
        ,'exchange' => '교환'
        ,'as' => 'A/S'
        ,'back' => '반품/환불'
        //,'refund' => '환불'
    ];
    const CLAIM_BOARD_TYPE_EXCLUDE_AS = [
      'exchange' => '교환'
      ,'back' => '반품/환불'
      /*,'refund' => '환불'*/
    ];

    /**
     * 주문 승인 상태
     */
    const ORDER_ACCT_STATUS = [
        '1' => '승인대기'
        ,'2' => '승인완료'
        ,'3' => '출고불가'
    ];
    /**
     * 주문 승인 상태 색상
     */
    const ORDER_ACCT_STATUS_COLOR = [
        '1' => ''
        ,'2' => 'sl-blue'
        ,'3' => 'text-danger'
    ];
    /**
     * 주문 승인 상태 라벨 색상
     */
    const ORDER_ACCT_STATUS_LABEL_COLOR = [
        '1' => 'label-white'
        ,'2' => 'label-success'
        ,'3' => 'label-danger'
    ];

    /**
     *  클레임 사유
     */
    const CLAIM_REASON = [
        '1' => '상품 옵션 선택을 잘못함'
        ,'2' => '사이즈가 작음'
        ,'3' => '사이즈가 큼'
        ,'4' => '상품의 원단 오염, 불량'
        ,'5' => '상품의 부자재 오염, 불량'
        ,'6' => '기타 상품 결함이 있음'
        ,'7' => '주문과 아예 다른 상품이 배송됨'
        ,'8' => '주문과 색상/사이즈가 다른 상품이 배송됨'
        ,'9' => '단순변심'
        ,'10' => '기타'
        ,'11' => '지퍼불량'
        ,'12' => '단추 불량'
        ,'13' => '밸크로(탈부착소재,찍찍이) 불량'
        ,'14' => '하단 스트링(하단사이즈 조절끈) 불량'
        ,'15' => '명찰고리 불량'
    ];

    //관리자용 클레임사유
    const ADMIN_CLAIM_REASON = [
        '1' => '상품 옵션 선택을 잘못함'
        ,'2' => '사이즈가 작음'
        ,'3' => '사이즈가 큼'
        ,'4' => '상품의 원단 오염, 불량'
        ,'5' => '상품의 부자재 오염, 불량'
        ,'6' => '기타 상품 결함이 있음'
        ,'7' => '주문과 아예 다른 상품이 배송됨'
        ,'8' => '주문과 색상/사이즈가 다른 상품이 배송됨'
        ,'9' => '단순변심'
        ,'10' => '기타'
        ,'11' => '지퍼불량'
        ,'12' => '단추 불량'
        ,'13' => '밸크로(탈부착소재,찍찍이) 불량'
        ,'14' => '하단 스트링(하단사이즈 조절끈) 불량'
        ,'15' => '명찰고리 불량'
    ];

    const ADMIN_CLAIM_REASON_COLOR = [
        '1' => 'rgb(255, 99, 132)'
        ,'2' => 'rgb(255, 159, 64)'
        ,'3' => 'rgb(255, 205, 86)'
        ,'4' => 'rgb(75, 192, 192)'
        ,'5' => 'rgb(54, 162, 235)'
        ,'6' => 'rgb(153, 102, 255)'
        ,'7' => 'rgb(201, 203, 207)'
        ,'8' => '#75c9f8'
        ,'9' => '#005099'
        ,'10' => '#002947'
        ,'11' => 'rgb(255, 99, 132)'
        ,'12' => 'rgb(255, 159, 64)'
        ,'13' => 'rgb(255, 205, 86)'
        ,'14' => 'rgb(75, 192, 192)'
        ,'15' => 'rgb(54, 162, 235)'
    ];

    /**
     * 클레임 게시판
     */
    const CLAIM_BOARD = [
        'qa' => true
    ];

    const SEND_MAIL_TYPE = [
      'safeCnt'
      , 'shareCnt'
    ];

    const GENERAL_GRADE = 1;
    const OPEN_PACKAGE_GRADE = 10; //TEST 4 , REAL 10

    /**
     * 영수형태
     */
    const RECEIPT_KIND  = [
        'n' => '신청안함'
        , 'r' => '현금영수증'
        , 't' => '세금계산서'
        , 'c' => '카드결제'
    ];

    /**
     * 품절상품 알림 상태
     */
    const SOLDOUT_REQ_SEND_TYPE  = [
        '0' => '미전송'
        , '1' => '자동발송'
        , '2' => '수동발송'
    ];

    const REALTIME_UPDATE_DB_MAP = [
        '3plReturnList' => 'sl_3plReturnList'
    ];

}


