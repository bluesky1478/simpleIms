<?php
namespace SlComponent\Util;

use Component\Mail\MailUtil;
use App;
use SlComponent\Database\DBUtil;
use Globals;
use SlComponent\Database\DBUtil2;

class SlSmsUtil{

    //Test서버에서만 적용..
    const sendContinue = false;

    public static function isTestMode(){
        if( URI_HOME === 'http://bcloud1478.godomall.com/' && !SlSmsUtil::sendContinue ){
            SitelabLogger::loggerAutoDebug('SMS TEST MODE');
            return true;
        }else{
            return false;
        }
    }

    public static function getSmsMsg($msgCode, $contentParam){
        $msg[0] = $contentParam['orderName']. '님의 주문건이 출고 승인 처리되었습니다. 상품은 평일기준 3~4일 내로 출고됩니다.';
        $msg[1] = $contentParam['orderName'] . '님의 주문건이 출고 불가 처리되었습니다. 본사에 문의하거나 재접수 바랍니다.';
        $msg[2] = "안녕하세요 {$contentParam['orderName']}님\r\n한국타이어 본사에서 고객님의 ID를 생성하여 아래와 같이 안내드립니다.\r\n\r\nID : {$contentParam['memId']} \r\n암호 : hankook + 핸드폰 뒷자리 (예 : hankook1111)\r\n\r\n접속URL : {$contentParam['shopUrl']} ";
        $msg[3] = "안녕하세요. {$contentParam['orderName']}님\r\n\r\n추가 결제 요청드립니다.\r\n아래와 같이 추가 결제 내용/금액을 확인하신 후 결제 바랍니다.\r\n\r\n- 결제 요청 내용 : {$contentParam['subject']}\r\n- 금액 : {$contentParam['amount']}\r\n\r\n결제하기 : {$contentParam['shopUrl']}";
        return $msg[$msgCode];
    }


    public static function sendSmsToMember($memNo, $content, $smsType = 'sms'){
        $result = 0;
        //$memberList[0]['memNo'] = empty(Session::get('member.memNo'))?0:Session::get('member.memNo');
        $memberList = DBUtil::getList(DB_MEMBER,'memNo',$memNo);
        if($content != null && !empty($memberList[0]['cellPhone']) ){
            $result = SlSmsUtil::sendSms($content,$memberList,$smsType);
        }
        return $result;
    }

    public static function sendSms($content, $memberList, $sendFl='sms', $tranType='send', $smsSendDate=''){

        $defaultInfo = gd_policy('basic.info');
        $content = '['.$defaultInfo['mallNm'].'] '.$content;

        $receiverData['sendFl'] = $sendFl;

        $receiverData['smsContents'] = $content;
        $smsSendDate = $smsSendDate==''?date('Y-m-d H:i:s'):$smsSendDate;
        $senderInfo = [
            'managerId' => 'msbaba',
            'managerNm' => '이노버',
            'recall'    => '1577-0327',
        ];

        $logData = [];
        $logData['receiver']['type'] = 'each';//$receiverData['receiverType'];
        $logData['receiver']['rejectSend'] = 0;
        $logData['receiver']['smsPoint'] = 1;
        $logData['receiver']['agreeCnt'] = 1;
        $logData['reserve']['mode'] = '';
        $logData['reserve']['date'] = $smsSendDate;
        $tranType = 'send';

        $sms = \App::load('\\Component\\Sms\\Sms');

        if( true === SlSmsUtil::isTestMode() ){
            $cnt = 0;
            SitelabLogger::loggerAutoDebug($memberList);
            SitelabLogger::loggerAutoDebug($content);
        }else{
            $cnt = $sms->send($receiverData['sendFl'], 'member', $receiverData['smsContents'], $memberList, $senderInfo, $smsSendDate, $tranType, 'limit', $logData);
        }

        return $cnt;

    }

    /**
     * 심플 SMS 발송하기
     * @param $content
     * @param $phone
     * @return int
     */
    public static function sendSmsSimple($content, $phone){
        $receiverData[0]['memNo'] = '0';
        $receiverData[0]['smsFl'] = 'y';
        $receiverData[0]['cellPhone'] = $phone;
        $receiverData[0]['memNm'] = '홍길동';
        return SlSmsUtil::sendSms($content, $receiverData, 'lms');
    }


}