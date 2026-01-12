<?php

namespace Controller\Admin\Test;

use App;
use Component\Database\DBTableField;
use Component\Deposit\Deposit;
use Component\Erp\ErpCodeMap;
use Component\Erp\ErpService;
use Component\Goods\GoodsPolicy;
use Component\Ims\EnumType\PREPARED_TYPE;
use Component\Ims\ImsCodeMap;
use Component\Ims\ImsDBName;
use Component\Ims\ImsJsonSchema;
use Component\Ims\ImsSendMessage;
use Component\Scm\ScmTkeService;
use Component\Sitelab\SiteLabSmsUtil;
use Component\Work\Code\DocumentDesignCodeMap;
use Component\Work\DocumentCodeMap;
use Component\Work\WorkCodeMap;
use Controller\Admin\Sales\ControllerService\SalesListService;
use Encryptor;
use Framework\Utility\DateTimeUtils;
use Framework\Utility\NumberUtils;
use Globals;
use Request;
use SiteLabUtil\SlCommonUtil;
use SlComponent\Database\DBUtil;
use SlComponent\Database\DBUtil2;
use SlComponent\Database\SearchVo;
use SlComponent\Mail\SiteLabMailUtil;
use SlComponent\Util\ApiTrait;
use SlComponent\Util\CUrlUtil;
use SlComponent\Util\DocumentStruct;
use SlComponent\Util\ExcelCsvUtil;
use SlComponent\Util\SitelabLogger;
use SlComponent\Util\SitelabUtil;
use SlComponent\Util\SlCode;
use SlComponent\Util\SlCodeMap;
use SlComponent\Util\SlKakaoUtil;
use SlComponent\Util\SlLoader;
use SlComponent\Util\SlPostRequestUtil;
use SlComponent\Util\SlProjectCodeMap;
use SlComponent\Util\SlSmsUtil;
use UserFilePath;
use Framework\Utility\StringUtils;
use Component\Storage\Storage;
use Framework\Security\Digester;
use Framework\Utility\GodoUtils;
use DateTime;

/**
 * TEST 페이지
 */
class TestCurlController extends \Controller\Admin\Controller{

    use ApiTrait;

    private $orderService;

    /**
     * @throws \Exception
     */
    public function index(){
        gd_debug("시작");

        $this->cUrlTest2();
        //$sopService = SlLoader::cLoad('godo','sopService');
        //$sopService->addOutHistory('20231128', '20231128');

        gd_debug("완료");
        exit();
    }

    public function cUrlTest2(){
        //$today = date('Ymd');
        $today = '20241010';

        $start = $today;
        $end = $today;
        $cookieFilePath = UserFilePath::data('etc')->getRealPath() . '/cookie/cookie.txt';
        gd_debug($cookieFilePath);

        $requestUrl = "http://wms.korea-soft.com/syl/loginCheck.php";
        $response = CUrlUtil::requestWithBody($requestUrl, 'user_id=msinnover&user_pw=3928', $cookieFilePath);
        gd_debug(json_decode($response,true));
        $url = "http://wms.korea-soft.com/syl/contentManager/outContent/joborderContent2_data.php?&_search=false&nd=1701322033057&rows=100&page=1&sidx=K36SlipXNo+desc%2C+K36SlipXNo&sord=desc";
        $response = CUrlUtil::request($url, $cookieFilePath);
        $parseData = json_decode($response, true);
        gd_debug( $parseData['records'] );
        //$url = "http://wms.korea-soft.com/syl/contentManager/outContent/releasedayContent_data.php?skey=2&s_serial=&s_reg_dtm_start={$start}&s_reg_dtm_end={$end}&s_order_start=&s_order_end=&s_total1=&s_total2=&s_order=1&s_gubun=&_search=false&nd=1701151634188&rows=10000&page=1&sidx=K09SlipNo2+desc%2C+K09SLIPDTX&sord=desc";
        $url = "http://wms.korea-soft.com/syl/contentManager/stockContent/stockjpmContent_data.php?skey=2&s_ownercode=A0157&s_total1=&s_total2=&s_procmode=N&s_order=0&s_sort=2&s_stock=1&_search=false&nd=1728602636761&rows=10000&page=1&sidx=K02PUMGBN1&sord=desc";
        $response = CUrlUtil::request($url, $cookieFilePath);
        $parseData = json_decode($response, true);
        gd_debug( $parseData['records'] );

        gd_debug( json_decode($response, true) );
        SitelabLogger::logger(json_decode($response, true));
    }


    public function cUrlTest(){
        /*$cookieFilePath = UserFilePath::data('etc')->getRealPath() . '/cookie/cookie.txt';
        $requestUrl = "http://erp.msinnover.com:8082/common/onLogIn.do";
        $response = CUrlUtil::requestJson($requestUrl, '{"url":"/common/onLogIn.do","method":"POST","userId":"jhsong9599","userPw":"dndbwnsgh00","contactIp":""}', $cookieFilePath);
        gd_debug($response);

        $requestUrl = "http://erp.msinnover.com:8082/mdm/cust/PickupMst/pop/getMstCustInfo.do?custId=CZ83382";
        $response = CUrlUtil::request($requestUrl, $cookieFilePath);
        gd_debug($response);
        gd_debug(json_decode($response, true));
        */

        //$url = "http://wms.korea-soft.com/syl/contentManager/outContent/releasedayContent_data.php?skey=2&s_serial=&s_reg_dtm_start=20230418&s_reg_dtm_end=20230418&s_order_start=&s_order_end=&s_total1=&s_total2=&s_order=1&s_gubun=&_search=false&nd=1681860204714&rows=100&page=20&sidx=K09SlipNo2+desc%2C+K09SLIPDTX&sord=desc";
        //$url = "http://wms.korea-soft.com/syl/contentManager/outContent/joborderContent2_data.php?skey=2&s_reg_dtm_start=20231128&s_reg_dtm_end=20231128&s_order_start=&s_order_end=&s_total1=&s_total2=&s_total3=&s_procmode=&_search=false&nd=1689912581160&rows=100&page=1&sidx=K36SlipXNo+desc%2C+K36SlipXNo&sord=desc";

        $today = date('Ymd');
        $start = $today;
        $end = $today;

        //$cookieFilePath = UserFilePath::data('etc')->getRealPath() . '/cookie/cookie.txt';
        //$response = CUrlUtil::requestJson($requestUrl, '{"url":"/common/onLogIn.do","method":"POST","userId":"jhsong9599","userPw":"dndbwnsgh00","contactIp":""}', $cookieFilePath);

        $cookieFilePath = UserFilePath::data('etc')->getRealPath() . '/cookie/cookie.txt';
        //$cookieFilePath = "/www/msinnover4_godomall_com/data/cookie/cookie.txt";
        //$cookieFilePath = UserFilePath::data()->getPathName()."/log/sitelab/cookie.log";

        gd_debug($cookieFilePath);

        $requestUrl = "http://wms.korea-soft.com/syl/loginCheck.php";
        //$response = CUrlUtil::requestJson($requestUrl, '{"url":"/syl/loginCheck.php","method":"POST","userId":"msinnover","userPw":"3928"}', $cookieFilePath);
        $response = CUrlUtil::requestWithBody($requestUrl, 'user_id=msinnover&user_pw=3928', $cookieFilePath);
        gd_debug(json_decode($response,true));

        $url = "http://wms.korea-soft.com/syl/contentManager/outContent/joborderContent2_data.php?&_search=false&nd=1701322033057&rows=100&page=1&sidx=K36SlipXNo+desc%2C+K36SlipXNo&sord=desc";
        $response = CUrlUtil::request($url, $cookieFilePath);
        $parseData = json_decode($response, true);
        gd_debug( $parseData['records'] );

        $url = "http://wms.korea-soft.com/syl/contentManager/outContent/releasedayContent_data.php?skey=2&s_serial=&s_reg_dtm_start={$start}&s_reg_dtm_end={$end}&s_order_start=&s_order_end=&s_total1=&s_total2=&s_order=1&s_gubun=&_search=false&nd=1701151634188&rows=10000&page=1&sidx=K09SlipNo2+desc%2C+K09SLIPDTX&sord=desc";
        $response = CUrlUtil::request($url, $cookieFilePath);
        $parseData = json_decode($response, true);
        gd_debug( $parseData['records'] );

        //gd_debug(json_decode($response, true));

        //s_reg_dtm_start
        //s_reg_dtm_end
        //681411401854
        //MSYGCL019

        //1101 , 1109
        SitelabLogger::logger(json_decode($response, true));
    }

}