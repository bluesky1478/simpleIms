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
namespace Component\Scm\Sql;

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
use SlComponent\Database\DBUtil;
use SlComponent\Database\SearchVo;
use SlComponent\Database\TableVo;
use SlComponent\Util\SitelabLogger;
use SlComponent\Util\SlCodeMap;


/**
 * 주문 class
 * @author Shin Donggyu <artherot@godo.co.kr>
 */
class ScmStockMonthlyListSql {

    public function getList($searchData){

        if(in_array($searchData['scmNo'][0], SlCodeMap::STATISTICS_MERGE)){
            $mergeSw = true;
        }else{
            $mergeSw = false;
        }

        $table['a'] = new TableVo(DB_GOODS,'tableGoods','a');

        //Search
        $searchVo = new SearchVo();

        if( $mergeSw ){
            $table['a']->setField('a.goodsNm');
            $searchVo->setGroup('a.goodsNm');
            //그룹 설정
        }else{
            $table['a']->setField('a.goodsNo, a.goodsNm');
        }

        //정렬 설정
        $searchVo->setOrder($searchData['sort']);

        //1. 공급사
        //scmNo
        if( !empty($searchData['scmFl']) && 'all' !== $searchData['scmFl']  ){
            if( 'n' === $searchData['scmFl']){
                //본사
                $searchVo->setWhere('a.scmNo = ?');
                $searchVo->setWhereValue('1');
            }else{
                //공급사 검색
                $searchVo->setWhere(DBUtil::bind('a.scmNo', DBUtil::IN, count($searchData['scmNo']) ));
                $searchVo->setWhereValueArray( $searchData['scmNo']  );
            }
        }

        //2. 검색어
        if( !empty($searchData['keyword']) ){
            $searchVo->setWhere( DBUtil::bind( $searchData['key'], DBUtil::BOTH_LIKE ) );
            $searchVo->setWhereValue( $searchData['keyword'] );
        }

        //3. 상품번호
        if( !empty($searchData['goodsNo']) && 'all' != $searchData['goodsNo'] ){
            $searchVo->setWhere( 'goodsNo=?' );
            $searchVo->setWhereValue( $searchData['goodsNo'] );
        }

        $searchVo->setWhere('a.stockFl = \'y\'');
        $searchVo->setWhere('a.delFl = \'n\'');

        //gd_debug($searchData);
        return DBUtil::getComplexListWithPaging($table ,$searchVo, $searchData);
    }

}