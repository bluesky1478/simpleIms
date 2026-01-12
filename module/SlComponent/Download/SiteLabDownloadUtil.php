<?php
namespace SlComponent\Download;

class SiteLabDownloadUtil{

    public static function download($filepath, $filename = null){
        //$filepath = './data/sample/factory.xls';
        //$filename = '발주처양식.xls';
        //$path_parts = pathinfo($filepath);
        $filesize = filesize($filepath);

        if( null == $filename ) $filename = self::mb_basename($filepath);

        if( self::is_ie() ) $filename = self::utf2euc($filename);
        header("Pragma: public");
        header("Expires: 0");
        header("Content-Type: application/octet-stream");
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header("Content-Transfer-Encoding: binary");
        header("Content-Length: $filesize");

        ob_clean();
        flush();
        readfile($filepath);

    }

    public function mb_basename($path) {
        return end(explode('/',$path));
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