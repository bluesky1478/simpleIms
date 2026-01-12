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
 * 영구크린 서비스
 * Class GoodsStock
 * @package Component\Goods
 */
class ScmKtngService {
    //23년 후반기 배송
    const PRD_CODE = [
        //하복남자
        1 => ['남상의', 'MSKTNG001', 'MSKTNG002', 'MSKTNG003', 'MSKTNG004', 'MSKTNG005', 'MSKTNG006', 'MSKTNG007', 'MSKTNG008', '남하의', 'MSKTNG015', 'MSKTNG016', 'MSKTNG017', 'MSKTNG018', 'MSKTNG019', 'MSKTNG020', 'MSKTNG021', 'MSKTNG022', 'MSKTNG023', 'MSKTNG024'],
        //하복여자
        2 => ['MSKTNG009', 'MSKTNG010', 'MSKTNG011', 'MSKTNG012', 'MSKTNG013', 'MSKTNG014', 'x', 'x', 'x', 'MSKTNG025', 'MSKTNG026', 'MSKTNG027', 'MSKTNG028', 'MSKTNG029', 'MSKTNG030', 'MSKTNG031', 'MSKTNG032', 'x', 'x', 'x'],
        //춘추남자
        3 => ['x', 'MSKTNG033', 'MSKTNG034', 'MSKTNG035', 'MSKTNG036', 'MSKTNG037', 'MSKTNG038', 'MSKTNG039', 'MSKTNG040', 'MSKTNG048', 'MSKTNG049', 'MSKTNG050', 'MSKTNG051', 'MSKTNG052', 'MSKTNG053', 'MSKTNG054', 'x', 'MSKTNG056', 'MSKTNG057', 'MSKTNG058'],
        //춘추 여자
        4 => ['MSKTNG041', 'MSKTNG042', 'MSKTNG043', 'MSKTNG044', 'MSKTNG045', 'MSKTNG046', 'MSKTNG047', 'x', 'x', 'MSKTNG059', 'MSKTNG060', 'MSKTNG061', 'MSKTNG062', 'MSKTNG063', 'MSKTNG064', 'MSKTNG065', 'x', 'x', 'x', 'x'],
    ];


    //23년 전반기 배송
    const ADDR = 0;
    const RECEIVER = 1;
    const PHONE = 2;
    const ZIPCODE = 3;
    const DELIVERY_INFO = [
        '신탄진'	=> ['대전광역시 대덕구 벚꽃길 71 신탄진공장 지원실','윤선희','010-9420-0030','34337'],
        '신탄진2'=> ['대전광역시 대덕구 벚꽃길 71 신탄진2공장','추민상','010-3564-4648','34337'],
        '영주'	=> ['경상북도 영주시 적서공단로 179 KT&G 지원실','조금옥','010-9359-2902','36148'],
        '광주'	=> ['광주광역시 북구 하서로 300 KT&G 지원실','전정은','010-4400-7899','61068'],
        '천안'	=> ['충청남도 천안시 서북구 봉정로 270 KT&G 지원실','김선희','010-2439-3570','31104']
    ];

    /**
     * 영구크린 약품주문
     * @param $each
     * @param $key
     * @param $mixData
     */
    public function setKtngOrderTemp($each, $key, &$mixData){
        $excelField = $mixData['excelField'];
        $optionBegin = 4;
        $optionEnd = 25;
        $optionRows = 4;

        $deliveryName = SlCommonUtil::getExcelData($each,'deliveryName', $excelField);
        //$sex = SlCommonUtil::getExcelData($each,'sex', $excelField);

        if('코드' == $deliveryName){
            $rowCodeList = [];
            for($i=$optionBegin; $optionEnd >= $i; $i++){
                $rowCodeList[$i] = $each[$i];
            }
            $mixData['code'][] = $rowCodeList;
        }else{
            $idx = $key % $optionRows;
            for($i=$optionBegin; $optionEnd >= $i; $i++){
                $orderNo = 'MS5'.$i.date('mds').str_pad(($key+1),4,"0",STR_PAD_LEFT);
                $code = $mixData['code'][$idx][$i];
                $prdInfo = DBUtil2::getOne('sl_3plProduct', 'thirdPartyProductCode', $code);
                $prdName = $prdInfo['productName'].'_'.$prdInfo['optionName'];
                $stock = $each[$i];
                if('blank' !== strtolower($code) && !empty($stock)){
                    $saveData = [
                        'orderNo' => $orderNo,
                        'scmName' => 'KTNG',
                        'scmNo' => '15',
                        'qty' => $stock,
                        'deliveryName' => $deliveryName,
                        'productCode' => $code,
                        'customerName' => self::DELIVERY_INFO[$deliveryName][self::RECEIVER],
                        'productName' => $prdName,
                        'zipcode' => self::DELIVERY_INFO[$deliveryName][self::ZIPCODE],
                        'address' => self::DELIVERY_INFO[$deliveryName][self::ADDR],
                        'phone' => self::DELIVERY_INFO[$deliveryName][self::PHONE],
                        'mobile' => self::DELIVERY_INFO[$deliveryName][self::PHONE],
                    ];

                    DBUtil2::insert('sl_3plOrderTmp', $saveData);

                }
            }
            //SitelabLogger::logger($stockList);
        }
    }




}
