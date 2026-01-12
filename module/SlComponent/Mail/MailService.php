<?php
namespace SlComponent\Mail;

use Component\Mail\MailUtil;
use App;
use SiteLabUtil\SlCommonUtil;
use SlComponent\Database\DBUtil;
use SlComponent\Util\SitelabLogger;
use SlComponent\Util\SlLoader;
use UserFilePath;

class MailService{

    /**
     * 재고 공유 메일 발송
     * @param $mailData
     */
    public function sendShareMail( $mailData ){

        //SitelabLogger::logger($mailData);

        $defaultInfo = gd_policy('basic.info');
        $mailUtil = SlLoader::cLoad('mail','SiteLabMailUtil','sl');
        //발송 대상 가져오기.
        $sendTargetList = DBUtil::getList('sl_mailList', 'scmNo', $mailData['scmNo'] );
        $sendTargetListAdmin = DBUtil::getList('sl_mailList', 'scmNo', 0 );
        $sendTargetList = array_merge($sendTargetList, $sendTargetListAdmin);
        foreach( $sendTargetList as $key => $value  ){
            $mailData['email']  =  $value['email'];
            $mailData['mailSubject'] = '상품 재고 공유건 알림';
            $replace['receiverName'] = $value['receiverName'];

            $replace['orderDt'] = $mailData['orderDt'];
            $replace['orderNo'] = $mailData['orderNo'];
            $replace['tableHtml'] = $this->getShareCntHtml($mailData['contents']);

            $body = $mailUtil->getMailTemplate($replace,'share_stock_alarm.php');
            //gd_debug( $body );

            $from = $defaultInfo['email'];
            $to = $mailData['email'];
            $mailUtil->send($mailData['mailSubject'], $body, $from, $to);
        }
    }

    /**
     * @param $contents
     * @return string
     */
    public function getShareCntHtml($contents){
        $htmlContents = "
            <table style='margin: 10px auto 0px; padding: 0px; border: 1px solid rgb(187, 197, 206); width: 100%; line-height: 14px; font-size: 12px; border-collapse: collapse; table-layout: fixed; word-break: break-all;' border='0' cellspacing='0' cellpadding='0' align='center'>
            <tr>
                <th style='padding: 3px 3px 3px 10px; color: rgb(0, 0, 0); border-bottom-color: rgb(187, 197, 206); border-bottom-width: 1px; border-bottom-style: solid; background-color: rgb(244, 244, 244);text-align: center'>상품코드</th>
                <th style='padding: 3px 3px 3px 10px; color: rgb(0, 0, 0); border-bottom-color: rgb(187, 197, 206); border-bottom-width: 1px; border-bottom-style: solid; background-color: rgb(244, 244, 244);text-align: center'>상품명</th>
                <th style='padding: 3px 3px 3px 10px; color: rgb(0, 0, 0); border-bottom-color: rgb(187, 197, 206); border-bottom-width: 1px; border-bottom-style: solid; background-color: rgb(244, 244, 244);text-align: center'>옵션명</th>
                <th style='padding: 3px 3px 3px 10px; color: rgb(0, 0, 0); border-bottom-color: rgb(187, 197, 206); border-bottom-width: 1px; border-bottom-style: solid; background-color: rgb(244, 244, 244);text-align: center'>공유재고</th>
                <th style='padding: 3px 3px 3px 10px; color: rgb(0, 0, 0); border-bottom-color: rgb(187, 197, 206); border-bottom-width: 1px; border-bottom-style: solid; background-color: rgb(244, 244, 244);text-align: center'>이전재고</th>
                <th style='padding: 3px 3px 3px 10px; color: rgb(0, 0, 0); border-bottom-color: rgb(187, 197, 206); border-bottom-width: 1px; border-bottom-style: solid; background-color: rgb(244, 244, 244);text-align: center'>현재재고</th>
            </tr>            
            ";
        foreach( $contents as $contentsValue ){
            $goodsNo = $contentsValue['goodsNo'];
            $goodsNm = $contentsValue['goodsNm'];
            $optionNm = $contentsValue['optionNm'];
            $shareCnt = number_format($contentsValue['shareCnt']);
            $beforeCnt = number_format($contentsValue['beforeCnt']);
            $afterCnt = number_format($contentsValue['afterCnt']);
            //데이터
            $htmlContents .= "<tr>
            <td style='width: 80%; height: 43px; color: rgb(51, 51, 51); padding-left: 20px; font-size: 13px; border-bottom-color: rgb(229, 229, 229); border-bottom-width: 1px; border-bottom-style: solid;text-align: center'>{$goodsNo}</td>
            <td style='width: 80%; height: 43px; color: rgb(51, 51, 51); padding-left: 20px; font-size: 13px; border-bottom-color: rgb(229, 229, 229); border-bottom-width: 1px; border-bottom-style: solid;text-align: center'>{$goodsNm}</td>
            <td style='width: 80%; height: 43px; color: rgb(51, 51, 51); padding-left: 20px; font-size: 13px; border-bottom-color: rgb(229, 229, 229); border-bottom-width: 1px; border-bottom-style: solid;text-align: center'>{$optionNm}</td>
            <td style='width: 80%; height: 43px; color: rgb(51, 51, 51); padding-left: 20px; font-size: 13px; border-bottom-color: rgb(229, 229, 229); border-bottom-width: 1px; border-bottom-style: solid;text-align: center'>{$shareCnt}</td>
            <td style='width: 80%; height: 43px; color: rgb(51, 51, 51); padding-left: 20px; font-size: 13px; border-bottom-color: rgb(229, 229, 229); border-bottom-width: 1px; border-bottom-style: solid;text-align: center'>{$beforeCnt}</td>
            <td style='width: 80%; height: 43px; color: rgb(51, 51, 51); padding-left: 20px; font-size: 13px; border-bottom-color: rgb(229, 229, 229); border-bottom-width: 1px; border-bottom-style: solid;text-align: center'>{$afterCnt}</td>
            ";
            $htmlContents .= "</tr>";
        }
        $htmlContents .= "</table>";

        return $htmlContents;
    }


}