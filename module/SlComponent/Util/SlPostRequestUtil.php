<?php
namespace SlComponent\Util;

use App;
use Component\Mail\MailUtil;
use Globals;
use SlComponent\Database\DBConst;
use SlComponent\Database\DBUtil;
use SlComponent\Database\SearchVo;

class SlPostRequestUtil{

    const IS_DEBUG = false;

    /**
     * CURL POST 호출
     * @param $requestUrl
     * @param $data
     * @param string[] $header
     * @param string $reqDiv
     * @return array
     * @throws \Exception
     */
    public static function request($requestUrl, $data, $header = ['Content-Type:application/json'], $reqDiv = 'NAVER'){
        self::debug('====== POST 호출 시작 =====');
        self::debug($requestUrl);
        self::debug($header);
        self::debug($data);

        $historyParam['reqDiv'] = $reqDiv;
        $historyParam['reqUrl'] = $requestUrl;
        $historyParam['reqHeader'] = json_encode($header);
        $historyParam['reqData'] = $data;

        //히스토리 저장
        $seq = DBUtil::insert('sl_postReqHistory', $historyParam);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $requestUrl);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        $output = curl_exec($ch);
        self::debug($output);
        self::debug('====== POST 호출 완료 =====');

        DBUtil::update('sl_postReqHistory', ['resData' => $output, 'resData2' => json_encode(json_decode($output,true),JSON_UNESCAPED_UNICODE) ], new SearchVo('sno=?', $seq));

        curl_close($ch);

        return $output;
    }

    public static function debug($str){
        if( self::IS_DEBUG ){
            gd_debug($str);
        }
    }

}