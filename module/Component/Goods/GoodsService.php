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
namespace Component\Goods;

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
 * 상품 서비스
 */
class GoodsService{

    /**
     * 공동구매 저장
     * @param $params
     * @throws Exception
     */
    public function saveGroupBuy($params){
        foreach($params['selectedGoods'] as $goodsNo) {
            $updateData = SlCommonUtil::getAvailData($params, [
                'groupBuyStart',
                'groupBuyEnd',
                'groupBuyCount',
                'groupBuyPrice',
                'groupBuyComment',
            ] );
            //SitelabLogger::logger('테스트...');
            //SitelabLogger::logger($updateData);
            //SitelabLogger::logger($goodsNo);
            DBUtil2::update(DB_GOODS, $updateData, new SearchVo('goodsNo=?', $goodsNo));
        }
    }

    /**
     * 공동구매 초기화
     * @param $params
     * @throws Exception
     */
    public function resetGroupBuy($params){
        foreach($params['selectedGoods'] as $goodsNo) {
            $updateData = [
                'groupBuyStart' => '',
                'groupBuyEnd' => '',
                'groupBuyCount' => '',
                'groupBuyPrice' => '',
                'groupBuyComment' => '',
            ];
            DBUtil2::update(DB_GOODS, $updateData, new SearchVo('goodsNo=?', $goodsNo));
        }
    }

    /**
     * 개인결제 카테고리
     * @return string
     */
    public function getPaymentsCategory(){
        if( SlCommonUtil::isDev() ){
            return SlCodeMap::PRIVATE_PAYMENT_CATEGORY_DEV;
        }else{
            return SlCodeMap::PRIVATE_PAYMENT_CATEGORY;
        }
    }


    /**
     * 일괄 재고 수정
     * @param $params
     * @return mixed
     */
    public function setBatchStock($params){

        $stockFlMap = [
            'add'=>'p',
            'subtract'=>'m',
            'modify'=>'c',
        ];

        $goodsNo = $params['goodsNo'];
        foreach($params['stockOptionSnoList'] as $optionKey => $optionSno){
            if( '' != $params['stockCnt'][$optionKey] ){
                $saveData['arrGoodsNo'][] = $goodsNo.'_'.$optionSno;
                $saveData['option']['stockFl'][$goodsNo][$optionSno] = $stockFlMap[$params['stockFl']];
                $saveData['option']['stockCntFix'][$goodsNo][$optionSno] = $params['stockCurrentCnt'][$optionKey];
                $saveData['option']['stockCnt'][$goodsNo][$optionSno] = $params['stockCnt'][$optionKey];
            }
        }
        $saveData['termsFl'] = 'n';

        $goodsAdmin = SlLoader::cLoad('goods','goodsAdmin');
        $goodsAdmin->setBatchStock($saveData);

        return DBUtil::getList(DB_GOODS_OPTION, 'goodsNo', $goodsNo, 'sno');

    }

}