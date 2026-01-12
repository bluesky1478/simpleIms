<?php

namespace Controller\Front\Download;

use Request;
use SlComponent\Util\SitelabLogger;

/**
 * 무상정책 화면
 * Class LayerFreePolicyController
 * @package Controller\Admin\Share
 */
class DownloadController extends \Controller\Front\Controller{

	/**
	 * {@inheritdoc}
	 */
	public function index()
	{
        $name = urldecode(Request::get()->get('name'));
        $path = urldecode(Request::get()->get('path'));
        $path = str_replace($name, rawurlencode($name), $path);
        //SitelabLogger::logger($path);
        if( self::is_ie() ) $name = self::utf2euc($name);

        ob_start();
        header('Content-Type: application/octet-stream');
        header("Content-Transfer-Encoding: Binary");
        header("Content-disposition: attachment; filename=\"" . $name . "\"");
        ob_end_clean();
        readfile($path);
        exit();
	}

    public static function utf2euc($str) {
        return iconv("UTF-8","cp949//IGNORE", $str);
    }

    public  static function is_ie() {
        $userAgent = \Request::getUserAgent();
        preg_match('/MSIE (.*?);/', $userAgent, $matches);
        if (count($matches) < 2) {
            preg_match('/Trident\/\d{1,2}.\d{1,2}; rv:([0-9]*)/', $userAgent, $matches);
        }
        if (count($matches) > 1) {  //$matches변수값이 있으면 IE브라우저
            return true;
        }
        return false;
    }


}
