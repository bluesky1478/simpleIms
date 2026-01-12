<?php
namespace Component\Scm;

use App;
use Session;
use Component\Database\DBTableField;
use Component\Member\Manager;
use Framework\Utility\DateTimeUtils;
use LogHandler;
use Request;
use Exception;
use Framework\Debug\Exception\AlertBackException;
use SiteLabUtil\SlCommonUtil;
use SlComponent\Database\DBUtil;
use SlComponent\Database\DBUtil2;
use SlComponent\Database\SearchVo;
use SlComponent\Database\TableVo;
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
 * TKE 서비스
 * Class GoodsStock
 * @package Component\Goods
 */
class ScmConst {

    const SCM_CALC_COMMON = [ //정산금액
        'workAmount' => 1000, //작업비용
        'packageAmount' => 130, //포장비용
        'packageBegin' => 4, //합포장
        'polyAmount' => 4000, //폴리백
        'polyBoxGuide' => 2,
        'boxAmount' => 6500,  //박스비
        'exchangeAmount' => 10000,
    ];

    const SCM_ITEM = [
        //한국타이어
        6 => [
            'items' => [],
            'option' => [
                'topBegin' => 90,
                'bottomBegin' => 28,
                'topAcc' => 5,
                'bottomAcc' => 2,
                'accCount' => 7
            ],
            'attr' => [1,5,2,3,4],
            'sort' => [
                1 => [
                    'TS',//1        ,   1
                    'HK',//4 , ch.  ,   2
                    'TTS',        //,   3
                    'TBX',
                ],
                2 => [
                    '춘추',//2
                    '하계',//5
                    '동계',
                ],
                3 => [
                    '카라티',//3
                    '조끼',
                    '점퍼',
                    '바지',
                ],
                4 => [
                    '차콜',
                    '블랙',
                ],
            ],
        ],
        //TKE
        8 => [
            'items' => [],
            'option' => [
                'topBegin' => 85,
                'bottomBegin' => 24,
                'topAcc' => 5,
                'bottomAcc' => 2,
                'accCount' => 11
            ],
            'attr' => [
                1,5,2,3
            ],
            'sort' => [
                1 => [
                    'TKE',
                    '파트너사',
                ],
                2 => [
                    '춘추',
                    '하계',
                    '동계',
                ],
                3 => [
                    '상의',
                    '하의',
                ],
            ],
            'calc' => [ //정산금액
                'workAmount' => 1000,
                'packageAmount' => 130,
                'packageBegin' => 4,
                'polyAmount' => 4000,
                'polyBoxGuide' => 2,
                'boxAmount' => 6500,
                'exchangeAmount' => 10000,
            ],

        ],

        //미쓰비시 (서비스)
        14 => ['calc' => ScmConst::SCM_CALC_COMMON],
        //미쓰비시 (설치)
        16 => ['calc' => ScmConst::SCM_CALC_COMMON,],
        //반도
        24 => ['calc' => ScmConst::SCM_CALC_COMMON,],
        //동양
        23 => ['calc' => [
            'workAmount' => 1000, //작업비용
            'packageAmount' => 0, //포장비용
            'packageBegin' => 0, //합포장
            'polyAmount' => 0, //폴리백
            'polyBoxGuide' => 0,
            'boxAmount' => 0,  //박스비
            'exchangeAmount' => 0,
        ]],
        //OEK
        21 => ['calc' => [
            'workAmount' => 0, //작업비용
            'packageAmount' => 0, //포장비용
            'packageBegin' => 0, //합포장
            'polyAmount' => 0, //폴리백
            'polyBoxGuide' => 0,
            'boxAmount' => 0,  //박스비
            'exchangeAmount' => 0,
        ]],
        //아시아나
        34 => ['calc' => [
            'workAmount' => 0, //작업비용
            'packageAmount' => 0, //포장비용
            'packageBegin' => 0, //합포장
            'polyAmount' => 0, //폴리백
            'polyBoxGuide' => 0,
            'boxAmount' => 0,  //박스비
            'exchangeAmount' => 0,
        ]],

    ];
}
