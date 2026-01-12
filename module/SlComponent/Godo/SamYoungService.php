<?php
namespace SlComponent\Godo;

use Component\Erp\ErpCodeMap;
use Component\Erp\ErpService;
use Component\Member\Util\MemberUtil;
use Component\Scm\AlterCodeMap;
use Component\Scm\ScmAsianaCodeMap;
use SiteLabUtil\SlCommonUtil;
use SlComponent\Database\DBUtil;
use SlComponent\Database\DBUtil2;
use SlComponent\Database\SearchVo;
use SlComponent\Excel\SimpleExcelComponent;
use SlComponent\Mail\SiteLabMailUtil;
use SlComponent\Util\CUrlUtil;
use SlComponent\Util\ExcelCsvUtil;
use SlComponent\Util\SitelabLogger;
use SlComponent\Util\SlCode;
use SlComponent\Util\SlCodeMap;
use Component\Storage\Storage;
use SlComponent\Util\SlLoader;
use Framework\StaticProxy\Proxy\FileHandler;
use UserFilePath;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Reader\Html;
use PhpOffice\PhpSpreadsheet\Writer\Xls;
use PhpOffice\PhpSpreadsheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Cell;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Component\Sms\Code;
use Component\Sms\SmsAuto;
use Component\Sms\SmsAutoCode;
use Component\Sms\SmsAutoObserver;


/**
 * 폐쇄몰 SOP 서비스
 * Class SlCode
 * @package SlComponent\Godo
 */
class SamYoungService {

    /**
     * 로그인
     */
    public static function login(){
        $cookieFilePath = UserFilePath::data('etc')->getRealPath() . '/cookie/cookie.txt';
        $requestUrl = "http://wms.korea-soft.com/syl/loginCheck.php";
        CUrlUtil::requestWithBody($requestUrl, 'user_id=msinnover&user_pw=3928', $cookieFilePath);
        return $cookieFilePath;
    }

    /**
     * 삼영에서 현재 재고 가져오기
     * @return mixed
     */
    public static function get3PlStock(){
        $url = "http://wms.korea-soft.com/syl/contentManager/stockContent/stockjpmContent_data.php?skey=2&s_ownercode=A0157&s_total1=&s_total2=&s_procmode=N&s_order=0&s_sort=2&s_stock=1&_search=false&nd=1728602636761&rows=20000&page=1&sidx=K02PUMGBN1&sord=desc";
        $cookieFilePath = SamYoungService::login();
        $response = CUrlUtil::request($url, $cookieFilePath);
        return json_decode($response, true);
    }

    /**
     * 삼영 API를 통한 출고 데이터 가져오기.
     * @param $start
     * @param $end
     * @return mixed
     */
    public static function requestOutHistoryData($start, $end){
        $url = "http://wms.korea-soft.com/syl/contentManager/outContent/releasedayContent_data.php?skey=2&s_serial=&s_reg_dtm_start={$start}&s_reg_dtm_end={$end}&s_order_start=&s_order_end=&s_total1=&s_total2=&s_order=1&s_gubun=&_search=false&nd=1701151634188&rows=10000&page=1&sidx=K09SlipNo2+desc%2C+K09SLIPDTX&sord=desc";
        $cookieFilePath = SamYoungService::login();
        $response = CUrlUtil::request($url, $cookieFilePath);
        $parseRslt = json_decode($response, true);
        return $parseRslt['rows'];
    }

    /**
     * 삼영 API를 통한 출고 대기 데이터 가져오기.
     * @return mixed
     */
    public static function requestWaitOutHistoryData(){
        $url = "http://wms.korea-soft.com/syl/contentManager/outContent/joborderContent2_data.php?&_search=false&nd=1763539722469&rows=10000&page=1&sidx=K36SlipXNo+desc%2C+K36SlipXNo&sord=desc";
        $cookieFilePath = SamYoungService::login();
        $response = CUrlUtil::request($url, $cookieFilePath);
        $parseRslt = json_decode($response, true);
        return $parseRslt['rows'];
    }

    /**
     * 삼영 API를 통한 입고 데이터 가져오기.
     * @param $start
     * @param $end
     * @return mixed
     */
    public static function requestInHistoryData($start, $end){
        $url = "http://wms.korea-soft.com/syl/contentManager/inContent/storeddayContent_data.php?skey=2&s_reg_dtm_start={$start}&s_reg_dtm_end={$end}&s_total1=&s_total2=&s_gubun=&s_order=0&s_sort=2&_search=false&nd=1756089462358&rows=10000&page=1&sidx=K16SlipDtx&sord=desc";
        $cookieFilePath = SamYoungService::login();
        $response = CUrlUtil::request($url, $cookieFilePath);
        $parseRslt = json_decode($response, true);
        return $parseRslt['rows'];
    }

    /**
     * 입고 사유 체크
     * @param $memo
     * @return bool
     */
    public static function getInputReason($memo){
        $needles = ['교환','반품'];
        $found = array_filter($needles, function($n) use ($memo) {
            return strpos($memo, $n) !== false;
        });

        return $found?true:false;
    }

}
