<?php
namespace Component\Imsv2\Sql;

use App;
use Component\Database\DBIms;
use Component\Database\DBTableField;
use Component\Ims\ImsCodeMap;
use Component\Ims\ImsDBName;
use Component\Member\Manager;
use Component\Scm\ScmTkeService;
use Framework\Debug\Exception\AlertBackException;
use Framework\Utility\DateTimeUtils;
use LogHandler;
use Request;
use Session;
use SiteLabUtil\SlCommonUtil;
use SlComponent\Database\DBUtil;
use SlComponent\Database\DBUtil2;
use SlComponent\Database\SearchVo;
use SlComponent\Mail\SiteLabMailUtil;
use SlComponent\Util\ExcelCsvUtil;
use SlComponent\Util\SitelabLogger;
use SlComponent\Util\SlCodeMap;
use SlComponent\Util\SlLoader;
use Component\Sms\Code;

/**
 * IMS 프로젝트 리스트 쿼리 모음
 * Class GoodsStock
 * @package Component\Goods
 */
class ImsProductServiceSql {

    public function getProductList(){
        //getProductList

        return DBUtil2::setTableInfo([
            'prd' => //메인
                [
                    'data' => [ ImsDBName::PRODUCT ]
                    , 'field' => ['*']
                ]
            , 'prj' => //고객정보
                [
                    'data' => [ ImsDBName::PROJECT, 'LEFT OUTER JOIN', 'prj.sno = prd.projectSno' ]
                    , 'field' => ['projectStatus']
                ]
            , 'cust' => //고객정보
                [
                    'data' => [ ImsDBName::CUSTOMER, 'LEFT OUTER JOIN', 'prj.customerSno = cust.sno' ]
                    , 'field' => ['customerName']
                ]
        ]);
    }


}



