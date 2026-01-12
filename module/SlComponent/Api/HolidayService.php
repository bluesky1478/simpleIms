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
 * 클레임 처리 서비스
 * Class SlCode
 * @package SlComponent\Util
 */
class HolidayService {

    private $holiTable = 'sl_holiday';

    /**
     * 휴일 데이터를 가져와 저장한다.
     * @param $year
     * @param $month
     * @throws Exception
     */
    public function setHoliday($year, $month){
        $yearMonth = $year.$month;
        DBUtil::delete($this->holiTable, new SearchVo(" locdate like concat(?,'%')" , $yearMonth));
        $requestUrl = "http://apis.data.go.kr/B090041/openapi/service/SpcdeInfoService/getHoliDeInfo?solYear={$year}&solMonth={$month}&ServiceKey=GbIYfasDmXrCHbVjH2IqusNqOw3lI52vAK5uLF0kLdA4zt2Tg8QxGz7GnJnencgYjTN%2FigP%2BmJQ2GWhISqWVYg%3D%3D&numOfRows=30";
        $response = CUrlUtil::request($requestUrl);
        $object = simplexml_load_string($response);
        foreach( $object->body->items->item as $value ){
            $arrayValue = (array)$value;
            $saveParam = SlCommonUtil::getAvailData($arrayValue, [
                'dateKind',
                'dateName',
                'isHoliday',
                'locdate',
                'seq',
            ]);
            if(!empty($saveParam['locdate'])){
                DBUtil::insert($this->holiTable, $saveParam);
            }else{
                gd_debug($object);
            }
        }
    }

    /**
     * 연별 휴일을 저장
     * @param $year
     * @throws Exception
     */
    public function setYearHoliday($year){
        for($i=1; 12>=$i; $i++){
            $month = str_pad($i,2,'0', STR_PAD_LEFT);
            $this->setHoliday($year, $month);
        }
    }


}
