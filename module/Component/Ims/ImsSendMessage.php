<?php
namespace Component\Ims;

use SlComponent\Database\DBUtil;
use SlComponent\Database\SearchVo;

/**
 * Sitelab Code Key List
 * Class SlCode
 * @package SlComponent\Util
 */
class ImsSendMessage {

    const PRODUCE_REQ = [
        'title' => '[이노버 IMS]생산스케쥴입력 요청({company}/{projectNo})',
        'msg' => '{company}(프로젝트번호 {projectNo} {productName})의 스케쥴 입력 요청 드립니다.',
    ];
    const PREPARED_COMMON = [
        'title' => '[이노버 IMS]{title} 요청({company}/{projectNo})',
        'msg' => '{company}(프로젝트번호 {projectNo} {productName})의 {title} 요청 드립니다.',
    ];
    const PREPARED_ACCEPT_COMMON = [
        'title' => '[이노버 IMS]{title} {acceptType} ({company}/{projectNo})',
        'msg' => '{company}(프로젝트번호 {projectNo} {productName})의 {title}(이)가 {acceptType} 되었습니다.',
    ];//BT퀄리티가, 가견적(이), 생산가확정이(이), 가발주는 자동 승인 처리

    //...

    public static function imsMessageReplacer($replaceContents, $replaceData){
        //010-8934-8431 최해룡
        //010-4427-8294 최하나
        $result = [];
        foreach($replaceContents as $replaceContentKey => $replaceContent){
            foreach ($replaceData as $key => $value) {
                $replaceContent = str_replace("{" . $key . "}", $value, $replaceContent);
            }
            $result[$replaceContentKey] = $replaceContent;
        }
        return $result;
    }
}

