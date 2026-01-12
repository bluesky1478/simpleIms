<?php
namespace SlComponent\Util;

use SlComponent\Database\DBUtil;
use SlComponent\Database\SearchVo;

/**
 * Class SlCode
 * @package SlComponent\Util
 */
class SlKaKaoMsg {

    const MSG01 = '[{mallNm}]
{orderName}님의 주문건이 출고 승인 처리되었습니다.
상품은 평일기준 3~4일 내로 출고됩니다.

▷ [{mallNm}] 바로가기
[{shopUrl}]';

    const MSG02 = '[{mallNm}]
{orderName}님의 주문건이 출고 불가 처리되었습니다. 본사에 문의하거나 재접수 바랍니다.

▷ [{mallNm}] 바로가기
[{shopUrl}]';

    const MSG03 = '[{mallNm}]
안녕하세요. [{orderName}]님
[주문자명]님의 주문이 주문이 접수 되었습니다.

▶주문번호 : [{orderNo}]
▶결제금액 : [{settlePrice}]원
감사합니다.


- 날짜 : [{regDt}]
- 주문번호 : [{orderNo}}]
- 주문자명 : [{orderName}]

▷ [{mallNm}] 바로가기
[{shopUrl}]';

    const MSG04 = '[{mallNm}]
안녕하세요. [{writerName}]님

작성하신 문의글이 정상적으로 등록되었습니다.
담당자가 확인 후 빠른 답변 드리겠습니다 :)

*업체쪽에 확인해야 할 사안이 있을 경우 답변이 다소 늦어질 수 있습니다.

▷ [{mallNm}] 바로가기
[{shopUrl}]';

    const MSG05 = '[{mallNm}]
안녕하세요. [{writerName}]님

작성하신 문의글에 답변이 등록되었습니다.
확인 부탁드립니다. 감사합니다:-)

▷ [{mallNm}] 바로가기
[{shopUrl}]';

    const MSG06 = '[{mallNm}]
안녕하세요. [{orderName}]님

이노버에서 구매해주셔서 감사합니다.

구매 만족도 평가에 참여 부탁드립니다.
고객님의 소중한 의견을 취합해 더욱 더 만족스러운 서비스를
드릴 수 있도록 노력하겠습니다.
▶응답해주신 고객분들께 추첨을 통하여 소정의 선물을 드리고 있습니다◀

참여하기
[{surveyUrl}]

▷ [{mallNm}] 바로가기
[{shopUrl}]';


    //일반 업체 만족도 평가
    const MSG07 = '[{mallNm}]
안녕하세요. [{orderName}]님

이노버에서 구매해주셔서 감사합니다.

구매 만족도 평가에 참여 부탁드립니다.
고객님의 소중한 의견을 취합해 더욱 더 만족스러운 서비스를
드릴 수 있도록 노력하겠습니다.
감사합니다 :)

참여하기
[{surveyUrl}]

▷ [{mallNm}] 바로가기
[{shopUrl}]
고객센터
(070-4239-4380)';

    //TKE 업체 만족도 평가
    const MSG08 = '[{mallNm}]
안녕하세요. TKE엘리베이터 [{orderName}]님

이노버에서 구매해주셔서 감사합니다.

구매 만족도 평가에 참여 부탁드립니다.
고객님의 소중한 의견을 취합해 더욱 더 만족스러운 서비스를
드릴 수 있도록 노력하겠습니다.
감사합니다 :)

참여하기
[{surveyUrl}]

▷ [{mallNm}] 바로가기
[{shopUrl}]
고객센터
(070-4239-4380)';

    //한국타이어 만족도 평가
    const MSG09 = '[{mallNm}]
안녕하세요. 한국타이어 [{orderName}]님

이노버에서 구매해주셔서 감사합니다.

구매 만족도 평가에 참여 부탁드립니다.
고객님의 소중한 의견을 취합해 더욱 더 만족스러운 서비스를
드릴 수 있도록 노력하겠습니다.
감사합니다 :)

참여하기
[{surveyUrl}]

▷ [한국타이어B2B] 바로가기
[hankookb2b.co.kr]
고객센터
(070-4239-4380)';

    //한국타이어 시즌 - 춘추/동계
    const MSG10 = '[{mallNm}]
안녕하세요. [{orderName}]님

★★ 경품 받으러 가기★★

간단한 설문조사하고
우리 매장 회식비 지원 받기!!

경품 받기 : {surveyUrl}

여러분의 목소리를 들려주세요!
더욱 만족스러운 서비스를 위해 "구매 만족도 평가"를 실시하고 있습니다.
향후 서비스 개선을 위해 활용될 예정이니 많은 참여 부탁드립니다 :)

▷ [한국타이어B2B] 바로가기
[hankookb2b.co.kr]
고객센터
(070-4239-4380)';

    //OTIS - 춘추/동계/하계
    const MSG11 = '[{mallNm}]
안녕하세요. [{orderName}]님

★★ 설문조사 이벤트★★

간단한 설문조사하고 선물 받아가세요!

경품 받기 : {surveyUrl}

여러분의 목소리를 들려주세요!
더욱 만족스러운 서비스를 위해 [구매 만족도 평가]를 실시하고 있습니다.
향후 서비스 개선을 위해 활용될 예정이니 많은 참여 부탁드립니다 :)

▷ [{mallNm}] 바로가기
[{shopUrl}]
고객센터
(070-4239-4380)';

    //TKE - 춘추/동계
    const MSG12 = '[{mallNm}]
안녕하세요. [{orderName}]님

★★ 설문조사 이벤트★★

간단한 설문조사하고 선물 받아가세요!

경품 받기 : {surveyUrl}

여러분의 목소리를 들려주세요!
더욱 만족스러운 서비스를 위해 [구매 만족도 평가]를 실시하고 있습니다.
향후 서비스 개선을 위해 활용될 예정이니 많은 참여 부탁드립니다 :)

▷ [{mallNm}] 바로가기
[{shopUrl}]
고객센터
(070-4239-4380)';

    //한국타이어 오픈 패키지 ID생성 알림
    const MSG13 = '[{mallNm}]
안녕하세요. {orderName}님

한국타이어 본사에서 고객님의 ID를 생성하여 아래와 같이 안내드립니다.

ID : {memId}
암호 : hankook + 핸드폰 뒷자리 (예 : hankook1111)
접속 URL : {hkUrl}

▷ {mallNm} 바로가기
{hkUrl}';

    //추가 결제 안내
    const MSG14 = '[{mallNm}]
안녕하세요. {orderName}님

추가 결제 요청드립니다.
아래와 같이 추가 결제 내용/금액을 확인하신 후 결제 바랍니다.

- 결제 요청 내용 : {subject}
- 금액 : {amount}

▷ {mallNm} 바로가기
[{shopUrl}]';

    const MSG15 = '[{mallNm}]
안녕하세요. {reqName}님

요청하신 상품 \'{goodsName}\'이(가) 신규 입고되어 알려드립니다.
구매를 원하시면 주문하기 버튼을 눌러주세요.
감사합니다.

▷ {mallNm} 바로가기
[{shopUrl}]';

    const MSG16 = '[{mallNm}]
안녕하세요. {reqName}님

요청하신 상품 \'{goodsName}\'을(를) 기성품으로 출고 예정입니다.
기성품 구매를 원하시면 주문하기 버튼을 눌러주세요.
감사합니다.

▷ {mallNm} 바로가기
[{shopUrl}]';


    const MSG17 = '[{mallNm}]
{orderName}님의 주문건이 출고 불가 처리되었습니다. 본사에 문의하거나 재접수 바랍니다.

사유 : {reason}

▷ {mallNm} 바로가기
{shopUrl}';

    const MSG18 = '[{mallNm}]
안녕하세요. {companyName} {orderName}님
유니폼의 힘을 믿는 기업 이노버 입니다.

두꺼운 겨울옷 대신 이제는 얇고 화사한 봄옷을 입어야할 만큼 날이 따뜻해졌습니다.
{year}년 겨울에 착용하였던 유니폼의 품질은 어떠하셨나요?
본 설문에 참여하셔서 소중한 의견을 들려주시기 바랍니다.

더욱더 좋은 제품으로 보답하겠습니다. 감사합니다.

참여하기
{surveyUrl}

▷ [{mallNm}] 바로가기
[{shopUrl}]
고객센터
(070-4239-4380)';

    const MSG19 = '[{mallNm}]
{memNm}님
{prdName} 신청기간은 {appPeriod}까지입니다.
신청기간 이후에는 신청이 불가하오니,
tkeb2b.co.kr 로 접속하시어 근무복 신청을 해주시기 바랍니다.

▷ [{mallNm}] 바로가기
[{targetShopUrl}]
고객센터
(070-4239-4380)';

    const MSG20 = '[{mallNm}]
{managerNm}님 IMS [{subject}] 결재 요청이 왔습니다.

고객명 : {customerName}
프로젝트번호 : {projectNo}
요청자 : {regManagerNm}

[{shopUrl}]';

    const MSG21 = '[{mallNm}]
안녕하세요. MS이노버 입니다. 
{customerName} 담당자님 제품 제작 사양서를 아래 메일로 발송해 드렸습니다.
수신Email : {email}

좋은 제품 제작을 위해 사양서 내용을 꼭 확인 부탁 드립니다.

감사합니다.';
    const MSG22 = '[{mallNm}]
안녕하세요. MS이노버 입니다. 
{customerName} 담당자님 제품 사이즈별 수량 입력 페이지를 아래 메일로 발송해 드렸습니다.
수신Email : {email}

발주하실 사이즈/수량을 입력해 주시는 대로 빠르게 제작 준비하도록 하겠습니다.   

감사합니다.';

    const MSG23 = '[{mallNm}]
안녕하세요. 한국타이어 {company} {orderName}님

유니폼 제작업체 엠에스이노버입니다.

구매하신 유니폼에 대한 만족도 평가와
신규 디자인 시안을 확인 후 선호도 투표에
참여 부탁드립니다.

고객님의 소중한 의견은 향후 디자인 개선과
서비스 품질 향상에 적극 반영될 예정입니다.

설문에 참여해주신 분들께는 감사의 마음으로
스타벅스 아메리카노 1잔 기프트콘을 증정드립니다.

감사합니다 :)

참여하기
☞ {surveyUrl}

☞ {shopUrl}

고객센터
☎ ({senderPhone})';



    const MSG = [
        1 => SlKaKaoMsg::MSG01 //출고승인 ID : 50145
        , 2 => SlKaKaoMsg::MSG02 //출고불가 ID : 50146
        , 3 => SlKaKaoMsg::MSG03 //주문접수 ID : 50147
        , 4 => SlKaKaoMsg::MSG04 //게시판 접수 ID : 50148
        , 5 => SlKaKaoMsg::MSG05 //게시판 답변 ID : 50149
        , 6 => SlKaKaoMsg::MSG06 //구매만족도 ID : 50150
        , 7 => SlKaKaoMsg::MSG07 //구매만족도-일반 ID : 50154
        , 8 => SlKaKaoMsg::MSG08 //구매만족도-일반 ID : 50155
        , 9 => SlKaKaoMsg::MSG09 //구매만족도-일반 ID : 50156
        , 10 => SlKaKaoMsg::MSG10 //구매만족도-일반 ID : 50157
        , 11 => SlKaKaoMsg::MSG11 //구매만족도-일반 ID : 50158
        , 12 => SlKaKaoMsg::MSG12 //구매만족도-일반 ID : 50158 (11번과 동일)
        , 13 => SlKaKaoMsg::MSG13 //ID생성 (한국타이어)
        , 14 => SlKaKaoMsg::MSG14 //추가 결제 안내 : 50168
        , 15 => SlKaKaoMsg::MSG15 //입고알림 : 50171
        , 16 => SlKaKaoMsg::MSG16 //입고알림(기성품) : 50172
        , 17 => SlKaKaoMsg::MSG17 //출고불가 ID (사유추가) : 50173
        , 18 => SlKaKaoMsg::MSG18 //동계만족도 : 50174
        , 19 => SlKaKaoMsg::MSG19 //제품 신청 기간 안내

        , 20 => SlKaKaoMsg::MSG20 //IMS 관리자결재요청
        , 21 => SlKaKaoMsg::MSG21 //IMS 사양서 요청
        , 22 => SlKaKaoMsg::MSG22 //IMS 아소트 입력 요청
        , 23 => SlKaKaoMsg::MSG23 //한국타이어 25년 리서치
    ];

    const MSG_CODE = [
        1 => '50145'
        , 2 => '50146'
        , 3 => '50147'
        , 4 => '50148'
        , 5 => '50149'
        , 6 => '50150'
        , 7 => '50154'
        , 8 => '50155'
        , 9 => '50156'
        , 10 => '50157'
        , 11 => '50158'
        , 12 => '50158'
        , 13 => '50167'
        , 14 => '50168'
        , 15 => '50171'
        , 16 => '50172'
        , 17 => '50173'
        , 18 => '50174'
        , 19 => '50178'

        , 20 => '50189'
        , 21 => '50199'
        , 22 => '50200'
        , 23 => '50201'
    ];


}


