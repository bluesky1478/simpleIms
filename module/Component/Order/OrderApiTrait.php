<?php

/**
 * This is commercial software, only users who have purchased a valid license
 * and accept to the terms of the License Agreement can install and use this
 * program.
 *
 * Do not edit or add to this file if you wish to upgrade Godomall5 to newer
 * versions in the future.
 *
 * @copyright ⓒ 2016, NHN godo: Corp.
 * @link      http://www.godo.co.kr
 */
namespace Component\Order;

use App;
use Component\Mail\MailAutoObserver;
use Component\Godo\NaverPayAPI;
use Component\Member\Member;
use Component\Naver\NaverPay;
use Component\Database\DBTableField;
use Component\Delivery\OverseasDelivery;
use Component\Deposit\Deposit;
use Component\ExchangeRate\ExchangeRate;
use Component\Mail\MailMimeAuto;
use Component\Mall\Mall;
use Component\Mall\MallDAO;
use Component\Member\Manager;
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
use Exception;
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
use LogHandler;
use Request;
use Session;
use Framework\Utility\DateTimeUtils;
use SiteLabUtil\SlCommonUtil;
use SlComponent\Database\DBConst;
use SlComponent\Database\DBUtil;
use SlComponent\Database\DBUtil2;
use SlComponent\Database\SearchVo;
use SlComponent\Database\TableVo;
use SlComponent\Util\SitelabLogger;
use SlComponent\Util\SlCodeMap;
use Component\Storage\Storage;
use SlComponent\Util\SlKakaoUtil;
use SlComponent\Util\SlLoader;
use SlComponent\Util\SlSmsUtil;


/**
 * 주문 서비스
 */
trait OrderApiTrait{

    /**
     * 재계약 고객여부 확인
     * @param $param
     */
    public function isHankookCustomerType($param){
        //한국타이어(6) 고객사 중
        $result = [
            'type' => -1
        ];
        $cellPhone = $param['receiverCellPhone'];

        $searchVo = new SearchVo(
            ["REPLACE(cellPhone,'-','')=?","ex1=?"],
            [str_replace('-','',$cellPhone),'한국타이어']
        );
        $searchVo->setOrder('lastLoginDt desc');

        if(strlen($cellPhone) >= 10){
            $memberData = DBUtil2::getOneBySearchVo(DB_MEMBER, $searchVo);
            if(!empty($memberData)){
                $result = [
                    'type' => 2,
                    'memNm' => $memberData['memNm'],
                    'memId' => $memberData['memId'],
                    'memNo' => $memberData['memNo'],
                ]; //재계약 고객
            }else{
                $result = [
                    'type' => 1
                ]; //신규
            }
        }
        $this->setJson(200, __('조회 완료'),['hankookType'=>$result]);
    }

}