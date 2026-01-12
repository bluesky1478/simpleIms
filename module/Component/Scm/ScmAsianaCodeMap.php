<?php
namespace Component\Scm;

use App;
use Component\Ims\ImsCodeMap;
use Component\Ims\ImsDBName;
use Session;
use Component\Database\DBTableField;
use Component\Member\Manager;
use Framework\Utility\DateTimeUtils;
use LogHandler;
use Request;
use Exception;
use Framework\Debug\Exception\AlertBackException;
use SiteLabUtil\FileUtil;
use SiteLabUtil\SlCommonUtil;
use SlComponent\Database\DBUtil;
use SlComponent\Database\DBUtil2;
use SlComponent\Database\SearchVo;
use SlComponent\Database\TableVo;
use SlComponent\Util\ExcelCsvUtil;
use SlComponent\Util\PhpExcelUtil;
use SlComponent\Util\SitelabLogger;
use SlComponent\Util\SlCodeMap;
use SlComponent\Util\SlCommonTrait;
use SlComponent\Util\SlKakaoUtil;
use SlComponent\Util\SlLoader;
use Component\Mail\MailAutoObserver;
use Component\Godo\NaverPayAPI;
use Component\Member\Member;
use Component\Naver\NaverPay;
use Component\Delivery\OverseasDelivery;
use Component\Deposit\Deposit;
use Component\ExchangeRate\ExchangeRate;
use Component\Mail\MailMimeAuto;
use Component\Mall\Mall;
use Component\Mall\MallDAO;
use Component\Member\Util\MemberUtil;
use Component\Mileage\Mileage;
use Component\Policy\Policy;
use Component\Sms\Code;
use Component\Sms\SmsAuto;
use Component\Sms\SmsAutoCode;
use Component\Sms\SmsAutoObserver;
use Component\Validator\Validator;
use Component\Goods\SmsStock;
use Component\Goods\KakaoAlimStock;
use Component\Goods\MailStock;
use Encryptor;
use Framework\Application\Bootstrap\Log;
use Framework\Debug\Exception\AlertOnlyException;
use Framework\Debug\Exception\AlertRedirectException;
use Framework\Helper\MallHelper;
use Framework\Utility\ArrayUtils;
use Framework\Utility\ComponentUtils;
use Framework\Utility\NumberUtils;
use Framework\Utility\StringUtils;
use Framework\Utility\UrlUtils;
use Globals;
use Logger;
use Framework\Utility\KafkaUtils;
use SlComponent\Util\SlSmsUtil;
use Framework\Security\Digester;
use Framework\Utility\GodoUtils;

/**
 * 아시아나 서비스
 * Class GoodsStock
 * @package Component\Goods
 */
class ScmAsianaCodeMap {

    const MASTER_NO = [
        19964
    ];

    //상품별 설정 24개상품
    const GOODS_PROVIDE_INFO =  [ //운영 설정
        1000000548 => ['yearCnt' => 3, 'provideCnt' => 1], //동복상의 3년 1개
        1000000537 => ['yearCnt' => 3, 'provideCnt' => 1], //동복점퍼상의 3년 1개
        1000000546 => ['yearCnt' => 3, 'provideCnt' => 1], //동복조끼 3년 1개
        1000000545 => ['yearCnt' => 3, 'provideCnt' => 1], //동복하의(홑) 3년 1개
        1000000544 => ['yearCnt' => 3, 'provideCnt' => 1], //동복하의(기모) 3년 1개
        1000000543 => ['yearCnt' => 1, 'provideCnt' => 1], //동정비복 1년 1개
        1000000542 => ['yearCnt' => 1, 'provideCnt' => 1], //하복상의 1년 1개
        1000000541 => ['yearCnt' => 1, 'provideCnt' => 1], //하복하의 1년 1개
        1000000539 => ['yearCnt' => 0, 'provideCnt' => 2], //면티(반팔) 1년 2개
        1000000538 => ['yearCnt' => 0, 'provideCnt' => 2], //면티(긴팔) 1년 2개 (연도 제한이 있어야하는데 없는건가?)
        1000000570 => ['yearCnt' => 1, 'provideCnt' => 1], //하정비복
        1000000549 => ['yearCnt' => 0, 'provideCnt' => 0], //안전조끼(일반) 제한 없음
        1000000552 => ['yearCnt' => 0, 'provideCnt' => 0], //안전조끼(AM), 제한 없음
        1000000553 => ['yearCnt' => 0, 'provideCnt' => 0], //안전조끼(F/S), 제한 없음
        1000000551 => ['yearCnt' => 0, 'provideCnt' => 0], //안전조끼(검수), 제한 없음
        1000000554 => ['yearCnt' => 0, 'provideCnt' => 0], //안전조끼(안전), 제한 없음
        1000000550 => ['yearCnt' => 0, 'provideCnt' => 0], //안전조끼(신입), 제한 없음
        1000000563 => ['yearCnt' => 0, 'provideCnt' => 4], //안전화(4인치) , 한번살 때 4개 제한 (최근 3개월 전 방환화 주문 이력 있을 경우 신청 불가)
        1000000564 => ['yearCnt' => 0, 'provideCnt' => 4], //안전화(6인치) , 한번살 때 4개 제한
        1000000572 => ['yearCnt' => 0, 'provideCnt' => 0], //방한화(우선 제한 없음)
        1000000567 => ['yearCnt' => 0, 'provideCnt' => 0], //우화 (230,235,295,300)
        1000000566 => ['yearCnt' => 0, 'provideCnt' => 2], //우의 1년 2개 (이나 제한없게 함)
        1000000556 => ['yearCnt' => 1, 'provideCnt' => 1, 'isYearCheck' => 'y'], //1년 1개에서 변경 (하작업모) , 택1
        1000000582 => ['yearCnt' => 1, 'provideCnt' => 1, 'isYearCheck' => 'y'], //1년 1개에서 변경 (버킷햇) , 택1
        1000000555 => ['yearCnt' => 1, 'provideCnt' => 1], //동작업모
        1000000557 => ['yearCnt' => 1, 'provideCnt' => 1], //방한모
    ];

    const FW_BOOTS_ALLOW_TEAM = [
        'team' => ['지상서비스1팀','지상서비스2팀','김포지점','화물서비스1팀'],
        'part1' => ['장비지원파트','캐빈파트','급유지원파트','여객지원파트','화물탑재파트'],
        'part2' => ['A/C세척','LOADER','FreshAir','인천급유','급유지원','장비지원','화물탑재'],
    ];

    const GOODS_PROVIDE_INFO_KR =  [  //24개상품
        '동복상의'      => 1000000548,
        '동복점퍼상의'  => 1000000537,
        '동복조끼'      => 1000000546,
        '동복하의(홑)'  => 1000000545,
        '동복하의(기모)' => 1000000544,
        '동정비복'      => 1000000543,
        '하복상의'      => 1000000542,
        '하복하의'      => 1000000541,
        '면티(반팔)'    => 1000000539,
        '면티(긴팔)'    => 1000000538,
        '하정비복'      => 1000000570,
        '안전조끼(일반)' => 1000000549,
        '안전조끼(AM)'   => 1000000552,
        '안전조끼(F/S)' => 1000000553,
        '안전조끼(검수)' => 1000000551,
        '안전조끼(안전)' => 1000000554,
        '안전조끼(신입)' => 1000000550,
        '안전화(4인치)'  => 1000000563,
        '안전화(6인치)'  => 1000000564,
        '우화'          => 1000000567,
        '우의'          => 1000000566,
        '하작업모'      => 1000000556,
        '동작업모'      => 1000000555,
        '방한모'        => 1000000557,
        '버킷햇'        => 1000000582,
    ];

    const GOODS_PROVIDE_INFO_KR_DEV = [
        '동복상의'      => 1000002613,
        '하작업모'      => 1000002621,
        '동복점퍼상의'  => 1000002602,
        '동복조끼'      => 1000002611,
        '동복하의(홑)'  => 1000002610,
        '동복하의(기모)' => 1000002609,
        '동정비복'      => 1000002608,
        '하복상의'      => 1000002607,
        '하복하의'      => 1000002606,
        '면티(반팔)'    => 1000002604,
        '면티(긴팔)'    => 1000002603,
        '안전조끼(일반)' => 1000002614,
        '안전조끼(AM)'   => 1000002617,
        '안전조끼(F/S)' => 1000002618,
        '(N)안전조끼(검수)' => 1000002616,
        '(N)안전조끼(안전)' => 1000002619,
        '(N)안전조끼(신입)' => 1000002615,
        '(N)하작업모'      => 1000002621,
        '(N)동작업모'      => 1000002620,
        '(N)방한모'        => 1000002622,
    ];

    const GOODS_PROVIDE_INFO_DEV =  [ //개발용
        1000002613 => ['yearCnt' => 1, 'provideCnt' => 1, 'isYearCheck' => 'y'], //묶임 테스트 (동복상의)
        1000002602 => ['yearCnt' => 3, 'provideCnt' => 1],
        1000002611 => ['yearCnt' => 3, 'provideCnt' => 1],
        1000002610 => ['yearCnt' => 3, 'provideCnt' => 1],
        1000002609 => ['yearCnt' => 1, 'provideCnt' => 1, 'isYearCheck' => 'y'],
        1000002608 => ['yearCnt' => 1, 'provideCnt' => 1],
        1000002607 => ['yearCnt' => 1, 'provideCnt' => 1],
        1000002606 => ['yearCnt' => 1, 'provideCnt' => 1],
        1000002604 => ['yearCnt' => 1, 'provideCnt' => 2],
        1000002603 => ['yearCnt' => 0, 'provideCnt' => 0],
        1000002614 => ['yearCnt' => 1, 'provideCnt' => 0],
        1000002617 => ['yearCnt' => 0, 'provideCnt' => 0],
        1000002618 => ['yearCnt' => 0, 'provideCnt' => 0],
        1000002616 => ['yearCnt' => 0, 'provideCnt' => 0],
        1000002619 => ['yearCnt' => 0, 'provideCnt' => 0],
        1000002615 => ['yearCnt' => 0, 'provideCnt' => 0],
        1000002621 => ['yearCnt' => 1, 'provideCnt' => 1, 'isYearCheck' => 'y'], //묶임 테스트 (하작업모)
        1000002620 => ['yearCnt' => 1, 'provideCnt' => 1],
        1000002622 => ['yearCnt' => 1, 'provideCnt' => 1],
    ];

    const CHOICE_GOODS = [
        ['버킷햇','하작업모'],
        ['동복하의(홑)','동복하의(기모)'],
    ];

    /**
     * 무한정 재고
     */
    const UNLIMIT_GOODS = [
        1000000564, //안전화 6인치
        1000000563, //안전화 4인치
        1000000574, //우화 240-290
        1000000567, //우화
        1000000568, //깔창
        1000000572, //방한화
    ];


    const GIMPO = [
        'name'=>'오서현',
        'phone'=>'01052073396',
        'zipCode'=>'07505',
        'address'=>'서울특별시강서구하늘길170-1',
        'addressSub'=>'아시아나에어포트 김포지점',
    ];
    const INCHEON = [
        'name'=>'차진우',
        'phone'=>'01026108627',
        'zipCode'=>'22381',
        'address'=>'인천중구공항동로295번길77-6',
        'addressSub'=>'아시아나항공 운송대리점 B동',
    ];
    const BUSAN = [
        'name'=>'배준범',
        'phone'=>'01045305810',
        'zipCode'=>'46718',
        'address'=>'부산광역시강서구공항진입로108(대저2동)',
        'addressSub'=>'아시아나에어포트 부산지점',
    ];
    const JEJU = [
        'name'=>'고택환',
        'phone'=>'01098482245',
        'zipCode'=>'63115',
        'address'=>'제주특별자치도제주시용문로71',
        'addressSub'=>'아시아나에어포트 제주지점',
    ];
    const GWANGJU = [
        'name'=>'안광식',
        'phone'=>'01046355538',
        'zipCode'=>'62425',
        'address'=>'광주광역시광산구상무대로420-25',
        'addressSub'=>'아시아나에어포트 광주지점',
    ];
    const MUAN = [
        'name'=>'범원규',
        'phone'=>'01087734773',
        'zipCode'=>'58533',
        'address'=>'전남 무안군 망운면 공할로 970-260',
        'addressSub'=>'아시아나에어포트 무안영업소',
    ];
    const YEOSU = [
        'name'=>'강대윤',
        'phone'=>'01020825923',
        'zipCode'=>'59606',
        'address'=>'전라남도여수시율촌면여순로386',
        'addressSub'=>'아시아나에어포트 여수영업소',
    ];


    const ADDRESS = [
        'AAPGMP1' => ScmAsianaCodeMap::GIMPO, //김포,
        'AAPGS1' => ScmAsianaCodeMap::INCHEON, //인천,,
        'AAPGE1' => ScmAsianaCodeMap::INCHEON, //인천,,
        'AAPAM1' => ScmAsianaCodeMap::INCHEON, //인천,,
        'AAPGH1' => ScmAsianaCodeMap::INCHEON, //인천,,
        'AAPGH2' => ScmAsianaCodeMap::INCHEON, //인천,,
        'AAPGB1' => ScmAsianaCodeMap::INCHEON, //인천,,
        'AAPGB2' => ScmAsianaCodeMap::INCHEON, //인천,,
        'AAPGC1' => ScmAsianaCodeMap::INCHEON, //인천,
        'AAPGC2' => ScmAsianaCodeMap::INCHEON, //인천,
        'AAPGF1' => ScmAsianaCodeMap::INCHEON, //인천,
        'AAPPUS1' => ScmAsianaCodeMap::BUSAN, //부산
        'AAPCJU1' => ScmAsianaCodeMap::JEJU, //제주
        'AAPKWJ1' => ScmAsianaCodeMap::GWANGJU, //광주
        'AAPMWX' => ScmAsianaCodeMap::MUAN, //무안
        'AAPRSU1'=> ScmAsianaCodeMap::YEOSU, //여수 ,
    ];

}
