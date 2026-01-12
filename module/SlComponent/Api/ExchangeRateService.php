<?php
namespace SlComponent\Api;

use App;
use Component\Mall\Mall;
use Component\Mall\MallDAO;
use Component\Sitelab\MallConfig;
use Exception;
use Framework\Utility\NumberUtils;
use Request;
use Session;
use SiteLabUtil\SlCommonUtil;
use SlComponent\Database\DBConst;
use SlComponent\Database\DBUtil;
use SlComponent\Database\DBUtil2;
use SlComponent\Database\SearchVo;
use SlComponent\Util\CUrlUtil;
use SlComponent\Util\CustomApiUtil;
use SlComponent\Util\LogTrait;
use SlComponent\Util\SitelabLogger;
use SlComponent\Util\SlCodeMap;
use SlComponent\Util\SlKakaoUtil;
use SlComponent\Util\SlLoader;
use Component\Coupon\Coupon;

/**
 * 환율 처리 서비스
 * Class SlCode
 * @package SlComponent\Util
 */
class ExchangeRateService {
    /**
     * 휴일 데이터를 가져와 저장한다.
     * @param $year
     * @param $month
     * @return int|string
     * @throws Exception
     */
    public static function getCurrentExchangeRate(){

        $rslt = 0;

        try {

            // 크롤링할 URL 설정 (네이버 금융 환율 페이지)
            $url = 'https://finance.naver.com/marketindex/exchangeList.nhn';

            // cURL 초기화
            $ch = curl_init();

            // cURL 옵션 설정
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0');

            // URL에서 HTML 가져오기
            $html = curl_exec($ch);
            curl_close($ch);

            // HTML을 DOMDocument로 로드
            $doc = new \DOMDocument();
            libxml_use_internal_errors(true); // HTML 파싱 오류 무시 설정
            $doc->loadHTML($html);
            libxml_clear_errors();

            // XPath로 필요한 데이터 추출
            $xpath = new \DOMXPath($doc);

            // 미국 USD에 해당하는 행만 선택
            $usdNodes = $xpath->query("//table[@class='tbl_exchange']/tbody/tr");

            if ($usdNodes->length > 0) {
                foreach ($usdNodes as $node) {
                    $countryNode = $xpath->query(".//td[@class='tit']/a", $node);

                    if ($countryNode->length > 0 && strpos($countryNode->item(0)->nodeValue, '미국 USD') !== false) {
                        // '송금 보낼 때' 환율 추출 (하나은행 환율)
                        $remitSendNode = $xpath->query(".//td[5]", $node); // '송금 보낼 때' 환율이 4번째 td에 위치
                        if ($remitSendNode->length > 0) {
                            $rslt = trim($remitSendNode->item(0)->nodeValue);
                        }
                        break; // 미국 USD 정보를 찾았으므로 루프 종료
                    }
                }
            }

        }catch (\Exception $e){}

        return SlCommonUtil::getOnlyNumberWithDot($rslt);
    }


    public function updateCurrentExchange(){
        $exRate = self::getCurrentExchangeRate();
        if( $exRate > 0 ){
            //1. 백업
            DBUtil2::runSql("insert into sl_exchangeRateConfigBackup (exchangeRateConfigNo, exchangeRateConfigUSDType, exchangeRateConfigUSDManual, exchangeRateConfigUSDAdjustment, exchangeRateConfigCNYType, exchangeRateConfigCNYManual, exchangeRateConfigCNYAdjustment, exchangeRateConfigJPYType, exchangeRateConfigJPYManual, exchangeRateConfigJPYAdjustment, exchangeRateConfigEURType, exchangeRateConfigEURManual, exchangeRateConfigEURAdjustment, regDt, modDt) select * from es_exchangeRateConfig");
            //2. 지우기
            DBUtil2::runSql("delete from es_exchangeRateConfig where 1=1");
            //3. 새로 넣기
            $searchVo = new SearchVo();
            $searchVo->setLimit(1);
            $searchVo->setOrder('regDt desc');
            $before = DBUtil::getOneBySearchVo('es_exchangeRate', $searchVo);
            unset($before['exchangeRateNo']);
            unset($before['regDt']);
            unset($before['modDt']);
            $before['exchangeRateUSD'] = $exRate;
            DBUtil::insert('es_exchangeRate', $before);

            $searchVo = new SearchVo();
            $searchVo->setLimit(1);
            $searchVo->setOrder('regDt desc');
            $before = DBUtil::getOneBySearchVo('es_exchangeRateConfig', $searchVo);
            unset($before['exchangeRateConfigNo']);
            unset($before['regDt']);
            unset($before['modDt']);
            $before['exchangeRateConfigUSDType'] = 'manual';
            $before['exchangeRateConfigUSDManual'] = $exRate;
            DBUtil::insert('es_exchangeRateConfig', $before);
        }
    }

    public static function getCurrentExchange(){

    }


}
