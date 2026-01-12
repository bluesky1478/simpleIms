<?php
namespace SlComponent\Mail;

use Component\Mail\MailUtil;
use App;
use SiteLabUtil\SlCommonUtil;
use SlComponent\Util\SitelabLogger;
use SlComponent\Util\SlLoader;
use UserFilePath;

class SiteLabMailMessage{

    const STATUS15 = 'STATUS15';

    const MAP = [
        'STATUS15' => [
            'title' => '사전 영업 진행 발송 메일',
            'subject' => '(IMS) {% customerName %}社 사전영업을 진행 합니다.',
            'contents' => "
                고객사 : {% customerName %}<br>
                업종 : {% industry %}<br>
                담당자 : {% contact %}<br>
                디자인실 참여 여부 : {% designJoin %}<br>
                입찰/미팅일자 :  {% exMeeting %}<br>
                메모 : {% salesMemo %}<br>
                영업 담당자 : {% salesManagerNm %}<br>
                <br>
                자세한 내용은 프로젝트 <span style='color:red'>#{% projectSno %}</span>를 참고 바랍니다.            
            ",
        ]
    ];

}