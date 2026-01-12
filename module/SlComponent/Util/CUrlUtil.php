<?php
namespace SlComponent\Util;

use App;
use Component\Mail\MailUtil;
use Exception;
use Globals;
use SiteLabUtil\SlCommonUtil;
use SlComponent\Database\DBConst;
use SlComponent\Database\DBUtil;
use SlComponent\Database\SearchVo;
use UserFilePath;

class CUrlUtil{

    const IS_DEBUG = false;

    /**
     * CURL POST 호출
     * @param $requestUrl
     * @param $cookieFilePath
     * @return array
     */
    public static function request($requestUrl, $cookieFilePath=null){
        //$filename = UserFilePath::data('etc')->getRealPath() . '/cookie/cookie.txt';
        self::debug('====== POST 호출 시작 =====');
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $requestUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        if(!empty($cookieFilePath)){
            curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFilePath); // 저장한 쿠키값을 불러옴
        }
        $output = curl_exec($ch);
        curl_close($ch);
        return $output;
    }

    public static function requestWithBody($requestUrl, $body, $cookieFilePath=null){
        //$filename = UserFilePath::data('etc')->getRealPath() . '/cookie/cookie.txt';
        self::debug('====== POST 호출 시작(With BODY) =====');
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $requestUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
        if(!empty($cookieFilePath)){
            curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieFilePath);
            curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFilePath); // 저장한 쿠키값을 불러옴
        }
        ob_start();
        $output = curl_exec($ch);
        ob_end_clean();
        if( !empty($cookieFilePath) ){
            curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFilePath);
        }
        curl_close($ch);
        return $output;
    }

    public static function requestJson($requestUrl, $body, $cookieFilePath){
        //$filename = UserFilePath::data('etc')->getRealPath() . '/cookie/cookie.txt';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $requestUrl);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, $body);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        if( !empty($cookieFilePath) ){
            curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieFilePath);
            curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFilePath); // 저장한 쿠키값을 불러옴
        }
        ob_start();
        $output = curl_exec($ch);
        ob_end_clean();
        if( !empty($cookieFilePath) ){
            curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFilePath);
        }
        curl_close($ch);
        return $output;
    }

    function get_web_page( $url )
    {
        $cert_content = SlCommonUtil::getFileData('./module/Controller/Admin/Test/cacert.pem');
        $cert_file = tmpfile();
        $cert_path = stream_get_meta_data($cert_file)['uri'];
        fwrite($cert_file, $cert_content);

        gd_debug($cert_path);
        //gd_debug($cert_content);

        $options = array(
            CURLOPT_RETURNTRANSFER => true,     // return web page
            CURLOPT_HEADER         => false,    // don't return headers
            CURLOPT_FOLLOWLOCATION => true,     // follow redirects
            CURLOPT_ENCODING       => "",       // handle all encodings
            CURLOPT_USERAGENT      => "spider", // who am i
            CURLOPT_AUTOREFERER    => true,     // set referer on redirect
            CURLOPT_CONNECTTIMEOUT => 120,      // timeout on connect
            CURLOPT_TIMEOUT        => 120,      // timeout on response
            CURLOPT_MAXREDIRS      => 10,       // stop after 10 redirects
            CURLOPT_SSL_VERIFYPEER => false     // Disabled SSL Cert checks
        );

        $ch      = curl_init( $url );
        curl_setopt_array( $ch, $options );
        //curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true); // SSL 인증서 검증
        //curl_setopt($ch, CURLOPT_CAINFO, './module/Controller/Admin/Test/cacert.pem');
        //curl_setopt($ch, CURLOPT_CAINFO, 'cacert.pem');
        //curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // 응답 데이터를 문자열로 반환
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true); // SSL 인증서 검증 활성화
        curl_setopt($ch, CURLOPT_CAINFO, $cert_path); // CA 인증서 파일 경로 설정

        $content = curl_exec( $ch );
        $err     = curl_errno( $ch );
        $errmsg  = curl_error( $ch );
        $header  = curl_getinfo( $ch );
        curl_close( $ch );

        $header['errno']   = $err;
        $header['errmsg']  = $errmsg;
        $header['content'] = $content;
        return $header;
    }

    public static function debug($str){
        if( self::IS_DEBUG ){
            gd_debug($str);
        }
    }

}