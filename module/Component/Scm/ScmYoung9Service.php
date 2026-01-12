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
use SiteLabUtil\FileUtil;
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
class ScmYoung9Service {

    //const PRD_CODE = ['24FWM09CPO90', '24FWM09CPO95', '24FWM09CPO100', '24FWM09CPO105', '24FWM09CPO110', '24FWM09CPO115', '24FWM09CPO120', '24FWM09CPO130', '24FWM09CVE90', '24FWM09CVE95', '24FWM09CVE100', '24FWM09CVE105', '24FWM09CVE110', '24FWM09CVE115', '24FWM09CVE120', '24FWM09CVE130'];
    const PRD_CODE = ['25SS09CPO90', '25SS09CPO95', '25SS09CPO100', '25SS09CPO105', '25SS09CPO110', '25SS09CPO115', '25SS09CPO120', '25SS09CTS90', '25SS09CTS95', '25SS09CTS100', '25SS09CTS105', '25SS09CTS110', '25SS09CTS115', '25SS09CTS120', '25SS09CVE90', '25SS09CVE95', '25SS09CVE100', '25SS09CVE105', '25SS09CVE110', '25SS09CVE115', '25SS09CVE120', '25SS09CVE135'];
    const PRD_NAME = [
        '25SS09CPO' => '영구크린_하계카라티_',
        '25SS09CTS' => '영구크린_하계차이나카라티_',
        '25SS09CVE' => '영구크린_하계조끼_',
    ];

    /**
     * 영구크린 약품주문
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
        foreach(ScmYoung9Service::PRD_CODE as $prdKey => $prdCode){
            $idx = 5 + $prdKey;
            $stock = SlCommonUtil::getOnlyNumber(trim($each[$idx]));
            $prdInfo = DBUtil2::getOne('sl_3plProduct', 'thirdPartyProductCode', $prdCode);
            $prdName = $prdInfo['productName'].'_'.$prdInfo['optionName'];
            if('blank' !== strtolower($prdCode) && !empty($stock)){
                $saveData = [
                    'orderNo' => $orderNo,
                    'scmName' => '영구크린',
                    'scmNo' => '12',
                    'qty' => $stock,
                    'remark' => $data['remark'],
                    'productCode' => $prdCode,
                    'customerName' => $data['receiverName'],
                    'productName' => $prdName,
                    'zipcode' => $data['zipcode'],
                    'address' => $data['address'],
                    'phone' => $data['receiverPhone'],
                    'mobile' => $data['receiverPhone'],
                ];
                /*'excelField' => [
                    'remark' => 1,
                    'receiverName' => 2,
                    'zipcode' => 3,
                    'address' => 4,
                    'receiverPhone' => 5,
                ],*/
                DBUtil2::insert('sl_3plOrderTmp', $saveData);
            }
        }
    }

    public function setManualOrder($files){
        $dataMap = [
            1 => 'receiverName',
            2 => 'remark',
            3 => 'cellPhone',
            4 => 'zipcode',
            5 => 'address',
        ];

        $result = PhpExcelUtil::readToArray($files, 1);
        foreach($result as $index => $val){

            $data = [];

            //기본정보 가져오기
            foreach($dataMap as $dataKey => $dataName){
                $data[$dataName] = $val[$dataKey];
            }

            $orderNo = 'MS5'.date('mds').str_pad(($index+1),4,"0",STR_PAD_LEFT);
            //$deliveryInfo = DBUtil2::getOne('sl_setScmDeliveryList','subject',$data['remark']);
            //$phone = empty($data['cellPhone'])?$deliveryInfo['receiverCellPhone']:$data['cellPhone'];

            $insertData = [
                'orderNo' => $orderNo,
                'customerName' => $data['receiverName'],
                'zipCode' => $data['zipcode'],
                'address' => $data['address'],
                'phone' => $data['cellPhone'],
                'mobile' => $data['cellPhone'],
                'scmName' => '영구크린',
                'scmNo' => 12,
                'remark' => $data['remark'],
            ];

            foreach(ScmYoung9Service::PRD_CODE as $prdIdx => $prdCode){
                if(!empty($val[$prdIdx+6])){
                    $productInfo = DBUtil2::getOne('sl_3plProduct', 'thirdPartyProductCode', $prdCode);
                    $insertData['productCode'] = $prdCode;
                    $insertData['productName'] = $productInfo['productName'].'_'.$productInfo['optionName'];
                    $insertData['qty'] = $val[$prdIdx+6];
                    DBUtil2::insert('sl_3plOrderTmp', $insertData);
                }
            }
        }
    }



}
