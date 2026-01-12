<?php

namespace Controller\Admin\Test;

use Component\Database\DBTableField;
use SlComponent\Database\DBUtil;
use SlComponent\Database\DBUtil2;
use SlComponent\Database\SearchVo;
use SlComponent\Database\TableVo;
use SlComponent\Util\SitelabLogger;
use Globals;
use Component\Deposit\Deposit;
use SlComponent\Util\SitelabUtil;
use SlComponent\Util\SlCode;
use SlComponent\Util\SlLoader;

/**
 * TEST 페이지
 */
class StockRecoverController extends \Controller\Admin\Controller{

    public function index(){
        gd_debug("== 재고 이력 복원 ==");

        //HK LIST
/*        $list = [
            [ 'goodsNo' => '1000000281', 'regDt'  => '2022-10-04 11:50:28' ],
            [ 'goodsNo' => '1000000282', 'regDt'  => '2022-10-04 11:50:28' ],
            [ 'goodsNo' => '1000000283', 'regDt'  => '2022-10-04 11:50:28' ],
            [ 'goodsNo' => '1000000284', 'regDt'  => '2022-10-04 11:50:28' ],
            [ 'goodsNo' => '1000000285', 'regDt'  => '2022-10-04 11:50:28' ],
            [ 'goodsNo' => '1000000286', 'regDt'  => '2022-10-04 11:50:28' ],
            [ 'goodsNo' => '1000000287', 'regDt'  => '2022-10-04 11:50:28' ],
            [ 'goodsNo' => '1000000288', 'regDt'  => '2022-10-04 11:50:28' ],
            [ 'goodsNo' => '1000000289', 'regDt'  => '2022-10-13 10:41:30' ],
            [ 'goodsNo' => '1000000290', 'regDt'  => '2022-10-13 10:41:30' ],
            [ 'goodsNo' => '1000000291', 'regDt'  => '2022-10-13 10:41:30' ],
            [ 'goodsNo' => '1000000292', 'regDt'  => '2022-10-13 10:41:30' ],
            [ 'goodsNo' => '1000000293', 'regDt'  => '2022-10-13 10:41:30' ],
            [ 'goodsNo' => '1000000296', 'regDt'  => '2022-11-02 11:37:27' ],
            [ 'goodsNo' => '1000000297', 'regDt'  => '2022-11-02 11:37:27' ],
            [ 'goodsNo' => '1000000298', 'regDt'  => '2022-11-02 11:37:27' ],
            [ 'goodsNo' => '1000000299', 'regDt'  => '2022-11-02 11:37:27' ],
        ];*/

        //TKE, 영구크린.
        $list = [
            //[ 'goodsNo' => '1000000304', 'regDt'  => '2022-11-10 11:50:28' ],
            [ 'goodsNo' => '1000000303', 'regDt'  => '2022-11-10 11:50:28' ],
            [ 'goodsNo' => '1000000302', 'regDt'  => '2022-11-10 11:50:28' ],
            //[ 'goodsNo' => '1000000301', 'regDt'  => '2022-11-10 11:50:28' ],
            //[ 'goodsNo' => '1000000280', 'regDt'  => '2022-09-02 10:52:28' ],
            //[ 'goodsNo' => '1000000279', 'regDt'  => '2022-09-02 10:52:28' ],
        ];

        //한국 타이어 / 미쓰비시 를 제외한. 판매중인 상품 중

        //$goodsNo = [ 'goodsNo' => '1000000300'; //상품번호
        //$regDt = '2022-11-02 11:13:27'; //입고날짜는 일단 정한다.

        /*foreach($list as $each){
            gd_debug($each);
            $this->setFirstGoods($each['goodsNo'], $each['regDt']);
        }*/

        //$this->setFirstGoods($goodsNo, $regDt);

        gd_debug('동작안함...');

        gd_debug("== 완료 ==");
        exit();
    }

    public function setFirstGoods($goodsNo, $regDt){

        //아.. 안나갔으면 ? (출고가 없다면 = 현재수량)
        //최초의 beforeCnt
        $goodsOptionList = DBUtil::getList(DB_GOODS_OPTION, 'goodsNo', $goodsNo);
        foreach($goodsOptionList as $goodsOption){
            $searchVo = new SearchVo(['goodsNo=?','optionNo=?'],[$goodsNo,$goodsOption['optionNo']]);
            $searchVo->setOrder('regDt asc');
            $outData = DBUtil::getOneBySearchVo('sl_goodsStock', $searchVo);
            if(!empty($outData)){
                $stockCnt = $outData['beforeCnt'];
                $stockName = ( $goodsOption['optionValue1'] . ' 출고로');
            }else{
                $stockCnt = $goodsOption['stockCnt'];
                $stockName = ( $goodsOption['optionValue1'] . ' 현재로');
            }
            $insertData = [
                'stockName' => $stockName,
                'goodsNo' => $goodsNo,
                'optionNo' => $goodsOption['optionNo'],
                'memNo' => -1,
                'stockType' => 1,
                'stockReason' => 1,
                'stockCnt' => $stockCnt,
                'beforeCnt' => 0,
                'afterCnt' => $stockCnt,
                'regDt' => $regDt,
            ];

            //regDt 안되면 업데이트로....
            $id = DBUtil2::insert('sl_goodsStock', $insertData);
            $stockData = DBUtil2::getOne('sl_goodsStock', 'sno', $id);
            DBUtil2::runSql("UPDATE sl_goodsStock set regDt = '{$regDt}' WHERE sno = '{$id}' ");
        }

    }

}