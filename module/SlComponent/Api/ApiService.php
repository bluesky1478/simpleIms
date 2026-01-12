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
 *
 * Class SlCode
 * @package SlComponent\Util
 */
class ApiService {

    public function test($params){

        return ['data'=>'TEST','msg'=>'테사트 완료'];

    }


}
