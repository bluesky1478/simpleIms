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
 * OEK 서비스
 * Class GoodsStock
 * @package Component\Goods
 */
class ScmOekService {

    //춘추점퍼 25
    const PRD_CODE = [
        90 => '25SFMOEKJP90',
        95 => '25SFMOEKJP95',
        100 => '25SFMOEKJP100',
        105 => '25SFMOEKJP105',
        110 => '25SFMOEKJP110',
        115 => '25SFMOEKJP115',
        120 => '25SFMOEKJP120',
        130 => '25SFMOEKJP130'
    ];

    //춘추점퍼 24
    /*const PRD_CODE = [
        100 => '24SFMOEKJP100',
        105 => '24SFMOEKJP105',
        110 => '24SFMOEKJP110',
        115 => '24SFMOEKJP115',
        130 => '24SFMOEKJP130'
    ];*/

    /**
     * OEK 수기주문
     * @param $each
     * @param $key
     * @param $mixData
     */
    public function setOrderTemp($each, $key, &$mixData){

        $orderNo = 'MS5'.date('mds').str_pad(($key+1),4,"0",STR_PAD_LEFT);

        $data = [];
        $excelField = $mixData['excelField'];
        foreach($excelField as $excelKey => $excelData){
            $data[$excelKey] = SlCommonUtil::getExcelData($each,$excelKey, $excelField);
        }

        $prdCode = ScmOekService::PRD_CODE[$data['prd']];
        $prdInfo = DBUtil2::getOne('sl_3plProduct', 'thirdPartyProductCode', $prdCode);
        $prdName = $prdInfo['productName'].'_'.$prdInfo['optionName'];
        if('blank' !== strtolower($prdCode) && !empty($prdCode) ){
            $saveData = [
                'orderNo' => $orderNo,
                'scmName' => '오티스(OEK)',
                'scmNo' => '21',
                'qty' => 1,
                'remark' => $data['remark'],
                'productCode' => $prdCode,
                'customerName' => $data['receiverName'],
                'productName' => $prdName,
                'zipcode' => $data['zipcode'],
                'address' => $data['address'],
                'phone' => $data['receiverPhone'],
                'mobile' => $data['receiverPhone'],
            ];
            DBUtil2::insert('sl_3plOrderTmp', $saveData);
        }
    }

}
