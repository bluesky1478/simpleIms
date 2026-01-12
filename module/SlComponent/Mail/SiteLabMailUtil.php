<?php
namespace SlComponent\Mail;

use Component\Ims\ImsCodeMap;
use Component\Mail\MailUtil;
use App;
use SiteLabUtil\SlCommonUtil;
use SlComponent\Database\DBUtil2;
use SlComponent\Database\SearchVo;
use SlComponent\Database\TableVo;
use SlComponent\Util\SitelabLogger;
use SlComponent\Util\SlLoader;
use UserFilePath;

class SiteLabMailUtil{

    const REP_FILE_NAME = '_file_replace_name_.tmp';

    /**
     * 템플릿 가져오기
     * @param $replace
     * @param string $templateFile
     * @return false|string|string[]
     */
    public function getMailTemplate($replace, $templateFile='safe_stock_alarm.php'){
        $filePath = UserFilePath::data('mail', 'etc', $templateFile)->getRealPath();
        $fd = fopen($filePath, 'r');
        $contents = fread($fd, 9999999);
        fclose($fd);
        foreach($replace as $key => $value){
            $contents = str_replace('{'.$key.'}',$value,$contents);
        }
        return $contents;
    }

    public static function getMailTemplateStatic($replace, $templateFile='safe_stock_alarm.php'){
        $filePath = UserFilePath::data('mail', 'etc', $templateFile)->getRealPath();
        $fd = fopen($filePath, 'r');
        $contents = fread($fd, 9999999);
        fclose($fd);
        foreach($replace as $key => $value){
            $contents = str_replace('{'.$key.'}',$value,$contents);
        }
        return $contents;
    }

    /**
     * 메일 발송
     * @param $subject
     * @param $htmlBody
     * @param $from
     * @param $to
     * @param null $file
     * @param null $cc
     * @return mixed
     */
    public static function send($subject, $htmlBody, $from, $to, $file = null, $cc = null){

        //테스트는 무조건 내 메일로 오게한다.
        if(SlCommonUtil::isDev()){
            $to = 'innover_dev@msinnover.com';
            //$to = 'gameandani2@gmail.com';
        }

        if (!MailUtil::hasMallDomain()) {
            throw new Exception(__('대표도메인이 설정되지 않았습니다.'), 200);
        }
        set_time_limit(0);
        $defaultDomain = gd_policy('basic.info');
        $defaultDomain = 'http://' . $defaultDomain['mallDomain'];

        $mime = new \Mail_mime();

        $mime->setHTMLBody($htmlBody, false);
        $body = $mime->get();

        // 헤더 설정
        $mime->setContentType('text/html', ['charset' => SET_CHARSET]);
        $header['From'] = $from;
        $header['To'] = $to;
        if(null !== $cc){
            $header['Cc'] = $cc;
        }
        $header['Subject'] = '=?' . SET_CHARSET . '?B?' . base64_encode($subject) . '?=';
        $headers = $mime->headers($header);

        /*if (@is_file($this->sendmail_path)) {
            $params['sendmail_path'] = '/usr/sbin/sendmail';
            $params['sendmail_args'] = '-t -i';
            $mail = &\Mail::factory('sendmail', $params);
        } else {*/
        $mail = &\Mail::factory('mail');
        //}

        if ($mail instanceof \PEAR_Error) {
            throw new Exception($mail->getMessage(), $mail->getBacktrace());
        }

        try {
            $result = $mail->send($to, $headers, $body);
        } catch (Exception $e) {
            throw $e;
        }
        return $result;
    }

    /**
     * 메일 발송
     * @param $subject
     * @param $htmlBody
     * @param $from
     * @param $to
     * @param null $file
     * @return mixed
     */
    public function sendWithExcelFile($subject, $htmlBody, $from, $to, $file = null, $cc = null){
        if (!MailUtil::hasMallDomain()) {
            throw new Exception(__('대표도메인이 설정되지 않았습니다.'), 200);
        }
        set_time_limit(0);

        $mime = new \Mail_mime(['eol' => $this->crlf]);
        $mime->setContentType('multipart/mixed', ['charset' => 'UTF-8']);
        $mime->setHTMLBody(mb_convert_encoding($htmlBody, "UTF-8", "auto"));

        if( null !== $file ){
            $filePath = $file['filePath'];
            $mime->addAttachment($filePath, 'application/vnd.ms-excel', SiteLabMailUtil::REP_FILE_NAME);
        }

        // 헤더 설정
        $header['From'] = $from;
        $header['To'] = $to;
        if(null !== $cc){
            $header['Cc'] = $cc;
        }
        $header['Subject'] = '=?' . SET_CHARSET . '?B?' . base64_encode($subject) . '?=';
        $body = $mime->get();
        $headers = $mime->headers($header);

        if (@is_file($this->sendmail_path)) {
            $params['sendmail_path'] = '/usr/sbin/sendmail';
            $params['sendmail_args'] = '-t -i';
            $mail = &\Mail::factory('sendmail', $params);
        } else {
            $mail = &\Mail::factory('mail');
        }
        if ($mail instanceof \PEAR_Error) {
            throw new Exception($mail->getMessage(), $mail->getBacktrace());
        }
        try {
            $body = str_replace('ISO-8859-1','UTF-8',$body);
            $body = str_replace('US-ASCII','UTF-8',$body);
            if( null !== $file ){
                $encodeFileName = mb_encode_mimeheader($file['fileName'], 'UTF-8', 'B');
                $body = str_replace(SiteLabMailUtil::REP_FILE_NAME,'"'.$encodeFileName.'"',$body);
            }
            $result = $mail->send($to, $headers, $body);
        } catch (Exception $e) {
            SitelabLogger::logger($e);
            throw $e;
        }
        return $result;
    }


    /**
     * 심플 메일 발송
     * @param $subject
     * @param $contents
     * @param $to
     * @param null $cc
     */
    public static function sendSimpleMail($subject, $contents, $to, $cc = null){
        $defaultInfo = gd_policy('basic.info');
        $from = $defaultInfo['email'];
        $body = "<!doctype html><html><head><meta http-equiv='Content-Type' content='text/html; charset=utf-8' /></head><body style='font-size:12px;font-family:\"맑은 고딕\"'>안녕하십니까?<br>엠에스이노버입니다.<br><br>{$contents}<br><br>감사합니다.</body></html>";
        SiteLabMailUtil::send($subject, $body, $from, $to, null, $cc);
    }
    public static function sendSystemMail($subject, $contents, $to, $cc = null){
        $defaultInfo = gd_policy('basic.info');
        $from = $defaultInfo['email'];
        $body = "<!doctype html><html><head><meta http-equiv='Content-Type' content='text/html; charset=utf-8' /></head><body style='font-size:12px;font-family:\"맑은 고딕\"'>이노버 시스템 메일 발송<br><br>{$contents}<br><br></body></html>";
        SiteLabMailUtil::send($subject, $body, $from, $to, null, $cc);
    }

    /**
     * 심플 메일 발송시 보낸이 자동 참조
     * @param $subject
     * @param $contents
     * @param $to
     * @param null $cc
     */
    public static function sendSimpleMailWithCc($subject, $contents, $to, $cc = null){
        $cc = (empty($cc)?null:$cc.',').\Session::get('manager')['email'];
        SiteLabMailUtil::sendSimpleMail($subject, $contents, $to, $cc);
    }

    /**
     * 키로 발송 메일 타이틀 / 메세지 전달
     * @param $key
     * @param $parmas
     */
    public static function sendSystemKeyMail($key, $parmas){
        $mailData = SiteLabMailMessage::MAP[$key];
        $mailData['subject'] = SlCommonUtil::replaceContents($parmas, '{% ', ' %}', $mailData['subject']);
        $mailData['contents'] = SlCommonUtil::replaceContents($parmas, '{% ', ' %}', $mailData['contents']);
        SiteLabMailUtil::sendSystemMail($mailData['subject'], $mailData['contents'], $parmas['to'], $parmas['cc']);
    }

    /**
     * 팀에 속하는 메일 목록 반환
     * @param $departmentCd
     * @return array
     */
    public static function getTeamMail($departmentCd){
        if( empty($departmentCd) ){
            $searchVo = new SearchVo('scmNo=?','1');
        }else{
            $searchVo = new SearchVo('departmentCd=?',  $departmentCd);
        }
        $searchVo->setWhere('isDelete=?');
        $searchVo->setWhereValue('n');
        $searchVo->setWhere("email<>''");
        $managerList = DBUtil2::getListBySearchVo(new TableVo(DB_MANAGER, 'tableManagerWithSno') , $searchVo);
        return SlCommonUtil::arrayAppKeyValue($managerList, 'sno', 'email');
    }

}