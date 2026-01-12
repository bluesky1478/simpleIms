<?php
namespace Component\Goods;

use App;
use Component\Database\DBTableField;
use LogHandler;
use Request;
use Exception;
use SlComponent\Database\DBUtil;
use SlComponent\Database\SearchVo;
use SlComponent\Database\TableVo;
use SlComponent\Util\SitelabLogger;
use SlComponent\Util\SlCodeMap;
use SlComponent\Util\SlCommonTrait;
use SlComponent\Util\SlLoader;

/**
 * 상품 재고 관리
 * Class GoodsStock
 * @package Component\Goods
 */
class GoodsStock {
    use SlCommonTrait;

    private $sql;
    private $goods;

    public function __construct(){
        $this->sql = \App::load(\Component\Goods\Sql\GoodsStockSql::class);
        $this->goods = SlLoader::cLoad('goods','goods');
    }

    /**
     * 재고 관리하는 상품 정보를 가져온다.
     * @param $goodsNo
     * @return mixed
     */
    public function getStockCheckGoodsInfo($goodsNo){
        return DBUtil::getOneBySearchVo(DB_GOODS,new SearchVo(['goodsNo=?','stockFl=?'],[$goodsNo,'y']));
    }

    /**
     * 상품 옵션 정보를 가져온다.
     * @param $goodsNo
     * @return mixed
     */
    public function getGoodsOptionInfoAndStockCheck($goodsNo){
        return $this->sql->getGoodsOptionInfoAndStockCheck($goodsNo);
    }

    /**
     * 재고 정보 추가
     * @param $arrData
     * @return mixed
     */
    public function addStockInfo($arrData){
        return DBUtil::insert('sl_goodsStock',$arrData);
    }

    /**
     * 재고 정보 신규 등록
     * @param $goodsNo
     */
    public function addStockInfoByGoodsNo($goodsNo){
        $goodsOptionList = $this->getGoodsOptionInfoAndStockCheck($goodsNo);
        foreach($goodsOptionList as $key => $each){
            $each['stockType']   = '1'; //입고
            $each['stockReason'] = '1';
            $each['beforeCnt'] = 0;
            $each['afterCnt'] = $each['stockCnt'];
            $result = $this->addStockInfo($each);
        }
    }

    /**
     * 재고 정보 추가 (상품번호 이용)
     * @param $goodsNo
     * @param $updateInfo
     */
    public function addUpdateStockInfoByGoodsNo($goodsNo,$updateInfo){
        $beforeGoodsOptionList = $updateInfo['beforeGoodsOptionList'];
        $afterGoodsOptionList = $this->getGoodsOptionInfoAndStockCheck($goodsNo);

        foreach($afterGoodsOptionList as $key => $each){
            if( isset($beforeGoodsOptionList[$key]['stockCnt']) && $beforeGoodsOptionList[$key]['stockCnt'] !== $each['stockCnt'] ){
                $updateInfo['beforeGoodsOption'] = $beforeGoodsOptionList[$key];
                $this->addUpdateStockInfo($each, $updateInfo);
            }
        }
    }

    /**
     * 재고 정보 추가 (개별)
     * @param $currentGoodsOption
     * @param $updateInfo
     */
    public function addUpdateStockInfo($currentGoodsOption, $updateInfo){
        $beforeGoodsOption = $updateInfo['beforeGoodsOption'];
        if( isset($beforeGoodsOption['stockCnt']) && $beforeGoodsOption['stockCnt'] !== $currentGoodsOption['stockCnt'] ){

            $saveData = $currentGoodsOption;

            $saveData['beforeCnt'] = $beforeGoodsOption['stockCnt'];
            $saveData['afterCnt']  = $currentGoodsOption['stockCnt'];
            $saveData['stockCnt']  = $saveData['afterCnt'] - $saveData['beforeCnt'];

            $saveData['memNo'] = $updateInfo['memNo'];
            $saveData['orderNo'] = $updateInfo['orderNo'];
            $saveData['orderGoodsSno'] = $updateInfo['orderGoodsSno'];

            if( $saveData['stockCnt'] > 0 ){
                //입고
                $saveData['stockType'] = '1';
                $saveData['stockReason'] = $updateInfo['addReason'];
            }else{
                //출고
                $saveData['stockType'] = '2';
                $saveData['stockReason'] = $updateInfo['minusReason'];

                /*SitelabLogger::logger('===> 출고시 안전재고 처리');
                SitelabLogger::logger($currentGoodsOption);
                $safeCnt = $this->goods->getOptionSafeCnt($currentGoodsOption['goodsNo'], $currentGoodsOption['optionSno']);
                SitelabLogger::logger('재고차이 : ' . $saveData['stockCnt']);
                SitelabLogger::logger('현재재고 : ' . $saveData['afterCnt']);
                SitelabLogger::logger('안전재고 : ' . $safeCnt);*/
                //$currentGoodsOption
            }

            $this->addStockInfo($saveData);
        }
    }

    /**
     *  안전재고 체크 및 메일 발송
     * @param $targetGoodsNoList
     * @param $goodsInfo
     * @param $mailData
     */
    public function checkAndMailSendGoodsStock($targetGoodsNoList, $goodsInfo, $mailData){
        /*
        SitelabLogger::logger(' 메일 전송 값 체크 ');
        SitelabLogger::logger($mailData);
        SitelabLogger::logger($targetGoodsNoList);
        SitelabLogger::logger($goodsInfo);
        */
        $contents = array();
        foreach( $targetGoodsNoList as $goodsNo ){
            $totalStockCnt = 0;
            $totalSafeCnt = 0;

            $goodsData = $goodsInfo[$goodsNo];
            $contents[$goodsNo]['goodsNo'] = $goodsNo;
            $contents[$goodsNo]['goodsNm'] = $goodsData['goodsNm'];
            $contents[$goodsNo]['goodsPrice'] = $goodsData['goodsPrice'];
            $contents[$goodsNo]['imageName'] = DBUtil::getOne(DB_GOODS_IMAGE, ['goodsNo','imageKind'] , [$goodsNo,'list'] )['imageName'];
            $contents[$goodsNo]['goodsImage'] = gd_html_goods_image($goodsNo, $contents[$goodsNo]['imageName'], $goodsData['imagePath'], $goodsData['imageStorage'], 40, $goodsData['goodsNm'], '_blank');
            if( 'local' == $goodsData['imageStorage'] ){
                $pathPrefix = \Request::getScheme()."://".\Request::getDefaultHost();
                $contents[$goodsNo]['goodsImage'] = str_replace('<img src="/data/goods/', "<img src=\"{$pathPrefix}/data/goods/", $contents[$goodsNo]['goodsImage'] );
                $contents[$goodsNo]['goodsImage'] = str_replace('/data/commonimg/', "{$pathPrefix}/data/commonimg/", $contents[$goodsNo]['goodsImage'] );
            }
            $optionList = DBUtil::getList(DB_GOODS_OPTION, 'goodsNo' , $goodsNo , 'optionNo' );
            $safeOptionList = DBUtil::getList('sl_goodsSafeStock', 'goodsNo' , $goodsNo , 'optionNo' );

            //Option
            foreach(  $optionList as $optionInfoKey => $optionInfo ){
                $optionTitle = array();
                for($i=1; $i<=5;$i++){
                    if(!empty($optionInfo['optionValue'.$i])){
                        $optionTitle[] = $optionInfo['optionValue'.$i];
                    }
                }

                $stockCnt = $optionInfo['stockCnt'];
                $safeCnt = $safeOptionList[$optionInfoKey]['safeCnt'];
                $contents[$goodsNo]['option'][$optionInfo['optionNo']]['title'] = implode('/',$optionTitle);
                $contents[$goodsNo]['option'][$optionInfo['optionNo']]['stockCnt'] = $stockCnt;
                $contents[$goodsNo]['option'][$optionInfo['optionNo']]['safeCnt'] = $safeOptionList[$optionInfoKey]['safeCnt'];
                $contents[$goodsNo]['option'][$optionInfo['optionNo']]['isDanger'] = ($safeOptionList[$optionInfoKey]['safeCnt'] > $optionInfo['stockCnt']) ? 'red':'black';
                $totalStockCnt += $stockCnt;
                $totalSafeCnt += $safeCnt;
            }
            //Total
            $contents[$goodsNo]['totalStockCnt'] = $totalStockCnt;
            $contents[$goodsNo]['totalSafeCnt'] = $totalSafeCnt;
        }

        $htmlContentsList = array();
        foreach( $contents as $goodsNo => $contentsValue ){

            $goodsImage = $contentsValue['goodsImage'];
            $goodsNm = $contentsValue['goodsNm'];
            $goodsPrice = number_format($contentsValue['goodsPrice']);

            $htmlContents = "
            <table style='margin: 10px auto 0px; padding: 0px; border: 1px solid rgb(187, 197, 206); width: 100%; line-height: 14px; font-size: 12px; border-collapse: collapse; table-layout: fixed; word-break: break-all;' border='0' cellspacing='0' cellpadding='0' align='center'>
            <tr>
            <th rowspan='2' style='padding: 3px 3px 3px 10px; color: rgb(0, 0, 0); border-bottom-color: rgb(187, 197, 206); border-bottom-width: 1px; border-bottom-style: solid; background-color: rgb(244, 244, 244);text-align: center'>상품코드</th>
            <th rowspan='2' style='padding: 3px 3px 3px 10px; color: rgb(0, 0, 0); border-bottom-color: rgb(187, 197, 206); border-bottom-width: 1px; border-bottom-style: solid; background-color: rgb(244, 244, 244);text-align: center'>상품명</th>
            <th rowspan='2' style='padding: 3px 3px 3px 10px; color: rgb(0, 0, 0); border-bottom-color: rgb(187, 197, 206); border-bottom-width: 1px; border-bottom-style: solid; background-color: rgb(244, 244, 244);text-align: center'>판매가</th>            
            ";
            foreach( $contentsValue['option'] as $optionKey => $optionValue ){
                $htmlContents .= "<th colspan='2' style='padding: 3px 3px 3px 10px; color: rgb(0, 0, 0); border-bottom-color: rgb(187, 197, 206); border-bottom-width: 1px; border-bottom-style: solid; background-color: rgb(244, 244, 244);text-align: center'>{$optionValue['title']}</th>";
            }
            $htmlContents .= "<th colspan='2' style='padding: 3px 3px 3px 10px; color: rgb(0, 0, 0); border-bottom-color: rgb(187, 197, 206); border-bottom-width: 1px; border-bottom-style: solid; background-color: rgb(244, 244, 244);text-align: center'>합계</th>";
            $htmlContents .= "</tr><tr>";

            //중간 Title
            foreach( $contentsValue['option'] as $optionKey => $optionValue ){
                $htmlContents .= "<th style='padding: 3px 3px 3px 10px; color: rgb(0, 0, 0); border-bottom-color: rgb(187, 197, 206); border-bottom-width: 1px; border-bottom-style: solid; background-color: rgb(244, 244, 244);text-align: center'>현재고</th>";
                $htmlContents .= "<th style='padding: 3px 3px 3px 10px; color: rgb(0, 0, 0); border-bottom-color: rgb(187, 197, 206); border-bottom-width: 1px; border-bottom-style: solid; background-color: rgb(244, 244, 244);text-align: center'>안전재고</th>";
            }
            //합계용 중간 Title
            $htmlContents .= "<th style='padding: 3px 3px 3px 10px; color: rgb(0, 0, 0); border-bottom-color: rgb(187, 197, 206); border-bottom-width: 1px; border-bottom-style: solid; background-color: rgb(244, 244, 244);text-align: center'>현재고</th>";
            $htmlContents .= "<th style='padding: 3px 3px 3px 10px; color: rgb(0, 0, 0); border-bottom-color: rgb(187, 197, 206); border-bottom-width: 1px; border-bottom-style: solid; background-color: rgb(244, 244, 244);text-align: center'>안전재고</th></tr>";

            //데이터
            $htmlContents .= "</tr><tr>
            <td style='width: 80%; height: 43px; color: rgb(51, 51, 51); padding-left: 20px; font-size: 13px; border-bottom-color: rgb(229, 229, 229); border-bottom-width: 1px; border-bottom-style: solid;text-align: center'>{$goodsNo}</td>
            <td style='width: 80%; height: 43px; color: rgb(51, 51, 51); padding-left: 20px; font-size: 13px; border-bottom-color: rgb(229, 229, 229); border-bottom-width: 1px; border-bottom-style: solid;text-align: center'>{$goodsNm}</td>
            <td style='width: 80%; height: 43px; color: rgb(51, 51, 51); padding-left: 20px; font-size: 13px; border-bottom-color: rgb(229, 229, 229); border-bottom-width: 1px; border-bottom-style: solid;text-align: center'>{$goodsPrice}원</td>";

            foreach( $contentsValue['option'] as $optionKey => $optionValue ){
                $htmlContents .= "<td style='width: 80%; height: 43px; color: rgb(51, 51, 51); padding-left: 20px; font-size: 13px; border-bottom-color: rgb(229, 229, 229); border-bottom-width: 1px; border-bottom-style: solid;text-align: center;color:{$optionValue['isDanger']}'>{$optionValue['stockCnt']}</td>";
                $htmlContents .= "<td style='width: 80%; height: 43px; color: rgb(51, 51, 51); padding-left: 20px; font-size: 13px; border-bottom-color: rgb(229, 229, 229); border-bottom-width: 1px; border-bottom-style: solid;text-align: center'>{$optionValue['safeCnt']}</td>";
            }
            $htmlContents .= "<td style='width: 80%; height: 43px; color: rgb(51, 51, 51); padding-left: 20px; font-size: 13px; border-bottom-color: rgb(229, 229, 229); border-bottom-width: 1px; border-bottom-style: solid;text-align: center;'>{$contentsValue['totalStockCnt']}</td>";
            $htmlContents .= "<td style='width: 80%; height: 43px; color: rgb(51, 51, 51); padding-left: 20px; font-size: 13px; border-bottom-color: rgb(229, 229, 229); border-bottom-width: 1px; border-bottom-style: solid;text-align: center'>{$contentsValue['totalSafeCnt']}</td>";
            $htmlContents .= "</tr></table>";
            $htmlContentsList[] = $htmlContents;
        }

        $mailData['tableHtml'] = implode('',$htmlContentsList);

        $this->sendSafeCntMail($mailData);

    }

    /**
     * 이메일 전송
     * @param $mailData
     */
    public function sendSafeCntMail($mailData){
        //SitelabLogger::logger($mailData);
        $defaultInfo = gd_policy('basic.info');
        $mailUtil = SlLoader::cLoad('mail','SiteLabMailUtil','sl');
        //발송 대상 가져오기.
        $sendTargetList = DBUtil::getList('sl_mailListOfSafeCnt', 'scmNo', $mailData['scmNo'] );
        $sendTargetListAdmin = DBUtil::getList('sl_mailListOfSafeCnt', 'scmNo', 0 );
        $sendTargetList = array_merge($sendTargetList, $sendTargetListAdmin);
        foreach( $sendTargetList as $key => $value  ){
            $mailData['email']  =  $value['email'];
            $mailData['receiverName']  =  $value['receiverName'];
            $mailData['mailSubject'] = $value['receiverName']. '님 상품 '.  $mailData['orderGoodsNm']   .'의 안전재고 수량이 부족합니다. 추가 생산여부 검토 바랍니다.';
            $replace['tableHtml'] = $mailData['tableHtml'];
            $replace['receiverName'] = $mailData['receiverName'];
            $replace['orderGoodsNm'] = $mailData['orderGoodsNm'];
            $replace['orderDt'] = $mailData['orderDt'];
            $body = $mailUtil->getMailTemplate($replace,'safe_stock_alarm.php');
            $from = $defaultInfo['email'];
            $to = $mailData['email'];
            $mailUtil->send($mailData['mailSubject'], $body, $from, $to);
        }
    }

}
