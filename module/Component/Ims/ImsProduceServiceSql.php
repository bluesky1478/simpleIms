<?php
namespace Component\Ims;

use App;
use Component\Database\DBIms;
use Component\Database\DBTableField;
use Component\Member\Manager;
use Component\Scm\ScmTkeService;
use Framework\Debug\Exception\AlertBackException;
use Framework\Utility\DateTimeUtils;
use LogHandler;
use Request;
use Session;
use SiteLabUtil\SlCommonUtil;
use SlComponent\Database\DBUtil2;
use SlComponent\Database\SearchVo;
use SlComponent\Mail\SiteLabMailUtil;
use SlComponent\Util\ExcelCsvUtil;
use SlComponent\Util\SitelabLogger;
use SlComponent\Util\SlCodeMap;
use SlComponent\Util\SlLoader;
use Component\Sms\Code;

/**
 * IMS 생산관련 서비스 SQL
 * Class GoodsStock
 * @package Component\Goods
 */
class ImsProduceServiceSql {

    public function getFileTable(){
        return DBUtil2::setTableInfo([
            'a' => //메인
                [
                    'data' => [ ImsDBName::PROJECT_FILE ]
                    , 'field' => ['*']
                ]
            , 'b' => //등록자
                [
                    'data' => [ DB_MANAGER, 'LEFT OUTER JOIN', 'a.regManagerSno = b.sno' ]
                    , 'field' => ['managerNm']
                ]
        ]);
    }

}

