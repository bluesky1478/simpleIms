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
namespace Controller\Admin\Erp\ControllerService;

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
use SlComponent\Database\DBConst;
use SlComponent\Database\DBUtil;
use SlComponent\Database\DBUtil2;
use SlComponent\Database\SearchVo;
use SlComponent\Database\TableVo;
use SlComponent\Util\ListSqlTrait;
use SlComponent\Util\SitelabLogger;
use SlComponent\Util\SlLoader;


/**
 * Inout 리스트 SQL
 */
class StockCurrentSql {

    use ListSqlTrait;

    const MAIN_TABLE = 'sl_3plProduct';

    public function getList($searchData){
        //gd_debug( $searchData['scmNo'] );
        DBUtil2::runSql("drop temporary table ztmp_tmp1");
        DBUtil2::runSql("drop temporary table ztmp_tmp2");
        $scmNoList = implode(',', $searchData['scmNo']);
        $subQuery1 = "select bb.optionCode, sum(stockCnt) as saleCnt from es_goods aa join es_goodsOption bb on aa.goodsNo = bb.goodsNo where aa.scmNo IN ( {$scmNoList}) and aa.delFl = 'n' and bb.optionCode is not null and bb.optionCode <> ''  ";
        //판매상품 집계 방법.
        if( 1 == $searchData['statTypeFl'] ){
            $subQuery1 .= " AND aa.goodsSellFl = 'y' ";
        }else if( 2 == $searchData['statTypeFl'] ){
            $subQuery1 .= " AND aa.goodsDisplayFl = 'y' ";
        }else if( 3 == $searchData['statTypeFl'] ){
            $subQuery1 .= " AND aa.goodsSellFl = 'y' ";
            $subQuery1 .= " AND aa.goodsDisplayFl = 'y' ";
        }
        $tmp1 = "create temporary table ztmp_tmp1 ( {$subQuery1} group by bb.optionCode ) ";
        //gd_debug($tmp1);
        $rslt = DBUtil2::runSql($tmp1);
        //gd_debug($rslt);
        $subQuery2 = "select bb.optionCode, sum(aa.goodsCnt) waitCnt from es_orderGoods aa  join es_goodsOption bb on aa.goodsNo = bb.goodsNo and aa.optionSno = bb.sno left outer join sl_orderAccept cc on aa.orderNo = cc.orderNo  join sl_setScmConfig dd on aa.scmNo = dd.scmNo   ";
        $subQuery2 .= " where  aa.scmNo IN ({$scmNoList}) and aa.orderStatus in ('o1','p1','g1','p2','p3') ";
        $subQuery2 .= " and ( 3 != cc.orderAcctStatus || 'y' != dd.orderAcceptFl ) and bb.optionCode is not null and bb.optionCode <> ''  ";
        $tmp2 = "create temporary table ztmp_tmp2 ( {$subQuery2} group by bb.optionCode ) ";
        //gd_debug($tmp2);
        $rslt = DBUtil2::runSql($tmp2);
        //gd_debug($rslt);

        $tableList= [
            'a' => //메인
                [
                    'data' => [ self::MAIN_TABLE ]
                    , 'field' => ["a.*, (a.stockCnt - IFNULL(b.saleCnt,0) - IFNULL(c.waitCnt,0) ) as totalCnt"]
                ],
            'b' => [
                'data' => [ 'ztmp_tmp1', 'LEFT OUTER JOIN', 'a.thirdPartyProductCode = b.optionCode' ]
                , 'field' => ['b.saleCnt']
            ],
            'c' => [
                'data' => [ 'ztmp_tmp2', 'LEFT OUTER JOIN', 'a.thirdPartyProductCode = c.optionCode' ]
                , 'field' => ['c.waitCnt']
            ]

        ];
        $table = DBUtil2::setTableInfo($tableList,false);

        //Search
        $searchVo = $this->createDefaultSearchVo($searchData);
        $searchVo = $this->setCondition($searchData, $searchVo); //기본 외 조건 추가 검색

        if( !empty($searchData['isErrorStockFl']) && 'all' !== $searchData['isErrorStockFl']  ){
            if( 1 == $searchData['isErrorStockFl'] ){
                $searchVo->setWhere("b.saleCnt > a.stockCnt");
            }else if( 2 == $searchData['isErrorStockFl'] ){
                $searchVo->setWhere("b.saleCnt = a.stockCnt");
            }else if( 3 == $searchData['isErrorStockFl'] ){
                $searchVo->setWhere("b.saleCnt < a.stockCnt");
            }else if( 4 == $searchData['isErrorStockFl'] ){
                $searchVo->setWhere("b.saleCnt <> a.stockCnt");
            }else if( 5 == $searchData['isErrorStockFl'] ){
                $searchVo->setWhere("(a.stockCnt - IFNULL(b.saleCnt,0) - IFNULL(c.waitCnt,0) ) <> 0");
            }
        }
        /*if( !empty($searchData['isErrorStockFl']) && 'all' !== $searchData['isErrorStockFl']  ){
            $subQuery1 = '('.$subQuery1.')';
            $subQuery2 = '('.$subQuery2.')';
            if( 1 == $searchData['isErrorStockFl'] ){
                $searchVo->setWhere("{$subQuery1} > a.stockCnt");
            }else if( 2 == $searchData['isErrorStockFl'] ){
                $searchVo->setWhere("{$subQuery1} = a.stockCnt");
            }else if( 3 == $searchData['isErrorStockFl'] ){
                $searchVo->setWhere("{$subQuery1} < a.stockCnt");
            }else if( 4 == $searchData['isErrorStockFl'] ){
                $searchVo->setWhere("{$subQuery1} <> a.stockCnt");
            }else if( 5 == $searchData['isErrorStockFl'] ){
                //$searchVo->setWhere("(stockCnt - {$subQuery1} - {$subQuery2}  ) <> 0");
                gd_debug('check..');
                $searchVo->setWhere("{$subQuery1} <> a.stockCnt");
            }
        }*/

        //정렬 설정
        $searchVo->setOrder("a.thirdPartyProductCode");

        return ['listData' => DBUtil2::getComplexList($table ,$searchVo)];
    }

    /**
     * @param $searchData
     * @param $searchVo
     * @return mixed
     */
    public function setCondition($searchData, $searchVo){
        //1. 공급사
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

        for($i=1; 5>=$i; $i++){
            if( !empty($searchData['attr'.$i]) ){
                $searchVo->setWhere( 'a.attr'.$i.'=?');
                $searchVo->setWhereValue( $searchData['attr'.$i] );
            }
        }

        if( !empty($searchData['optionName']) ){
            $searchVo->setWhere( DBUtil::bind( 'a.optionName', DBUtil::BOTH_LIKE ) );
            $searchVo->setWhereValue( $searchData['optionName'] );
        }

        return $searchVo;
    }

}