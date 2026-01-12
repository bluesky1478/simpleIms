<?php
namespace SlComponent\Util;

use Component\Mail\MailUtil;
use App;
use SiteLabUtil\SlCommonUtil;
use SlComponent\Database\DBUtil;
use Globals;
use SlComponent\Database\DBUtil2;
use SlComponent\Database\SearchVo;

class SlKakaoUtil{

    const KAKAO_SEND_SWITCH = 'y'; // 알림톡 발송 제어  y, n

    const KAKAO_TEMPLATE_TABLE = 'sl_kakaoMsg';
    const KAKAO_HISTORY_TABLE = 'sl_kakaoMsgHistory';
    const KAKAO_ID = 'momotee';
    const KAKAO_KEY = 'yme7vuzgkcvt8jljq8ijfztopk86q5wogq3wfa1u';

    /**
     * 카카오 메세지 전송
     * @param $templateId
     * @param $receiverPhone
     * @param $param
     * @throws \Exception
     */
    public static function send($templateId, $receiverPhone, $param){
        $defaultInfo = gd_policy('basic.info');
        $param['mallNm'] = $defaultInfo['mallNm'];
        $param['shopUrl'] = gd_isset($param['shopUrl'],$defaultInfo['mallDomain']);
        $param['senderPhone'] = '070-4239-4380';

        //없으면 DB에서 찾는다.
        if(empty(SlKaKaoMsg::MSG[$templateId])){
            $templateContents = DBUtil2::getOne('sl_kakaoMsg','templateId',$templateId)['contents'];
        }else{
            $templateContents = SlKaKaoMsg::MSG[$templateId];//message
        }

        foreach($param as $key => $value){
            $templateContents = str_replace('{'.$key.'}',$value,$templateContents);
        }

        if( !empty($param['testContents']) ){
            $templateContents = $param['testContents'];
        }

        if( 'y' === SlKakaoUtil::KAKAO_SEND_SWITCH && !SlCommonUtil::isDev() ){

            $sendData['templateId'] = SlKaKaoMsg::MSG_CODE[$templateId]; //messageCode ...
            if(empty($sendData['templateId'])){
                $sendData['templateId'] = $templateId; //DB사용하는 것은 TemplateID 그대로 사용.
            }

            $sendNo = DBUtil::insert(SlKakaoUtil::KAKAO_HISTORY_TABLE, $sendData);

            if(!empty($param['btnUrl'])){
                $btnUrl = [0 => [ 'url_pc' => "{$param['btnUrl']}", 'url_mobile' => "{$param['btnUrl']}" ]];
            }else{
                $btnUrl = [0 => [ 'url_pc' => "{$param['shopUrl']}", 'url_mobile' => "{$param['shopUrl']}" ]];
            }

            $messages[] = [
                'no' => $sendNo, // 메시지 인덱스입니다.
                'tel_num' => $receiverPhone,
                'msg_content' => $templateContents,
                'sms_content' => $templateContents,
                'use_sms' => '1',
                'btn_url' => $btnUrl
            ];
            if(!empty($param['reserveTime'])) {
                $messages[0]['reserve_time'] = $param['reserveTime'];
            }

            $data = [];
            $data['userid'] = SlKakaoUtil::KAKAO_ID;
            $data['api_key'] = SlKakaoUtil::KAKAO_KEY;
            $data['template_id'] = $sendData['templateId'];

            foreach ($messages as $message) {
                $data['messages'][] = $message;
            }

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "https://api.lunasoft.co.kr/lunatalk/api/message/send");
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
            $output = curl_exec($ch);
            curl_close($ch);
            $result = json_decode($output, true);

            //결과 저장
            $resultData['contents'] = json_encode($data, JSON_UNESCAPED_UNICODE);
            $resultData['resultCode'] = $result['code'];
            $resultData['resultData'] = json_encode(json_decode($output,true), JSON_UNESCAPED_UNICODE);

            DBUtil::update(SlKakaoUtil::KAKAO_HISTORY_TABLE, $resultData, new SearchVo('sno=?', $sendNo)  );
        }else{
            SitelabLogger::logger2(__METHOD__, $receiverPhone.' ('. $templateId .')==> '.$templateContents);
        }

    }
}