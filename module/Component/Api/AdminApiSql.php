<?php
namespace Component\Api;

use Component\Mall\Mall;
use Component\Mall\MallDAO;
use Component\Sitelab\MallConfig;
use Framework\Utility\NumberUtils;
use Request;
use SlComponent\Database\DBConst;
use SlComponent\Database\DBUtil;
use SlComponent\Database\SearchVo;
use SlComponent\Util\ListSqlTrait;
use SlComponent\Util\LogTrait;
use SlComponent\Util\SitelabLogger;
use SlComponent\Util\SlCodeMap;
use SlComponent\Util\SlKakaoUtil;
use SlComponent\Util\SlLoader;

/**
 * 관리자 API 서비스
 * Class SlCode
 * @package SlComponent\Util
 */
class AdminApiSql {

    use ListSqlTrait;

}
