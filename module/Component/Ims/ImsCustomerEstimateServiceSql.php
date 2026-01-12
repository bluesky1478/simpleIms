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
 * IMS 서비스
 * Class GoodsStock
 * @package Component\Goods
 */
class ImsCustomerEstimateServiceSql {

    use ImsServiceConditionTrait;

    /**
     * 고객 견적 리스트
     * @return array
     */
    public function getCustomerEstimateTable(){
        return DBUtil2::setTableInfo([
            'a' => //메인
                [
                    'data' => [ ImsDBName::CUSTOMER_ESTIMATE ]
                    , 'field' => ["*"]
                ]
            , 'b' => //등록자
                [
                    'data' => [ DB_MANAGER, 'LEFT OUTER JOIN', 'a.regManagerSno = b.sno' ]
                    , 'field' => ['managerNm as regManagerNm']
                ]
            , 'c' => //담당자
                [
                    'data' => [ DB_MANAGER, 'LEFT OUTER JOIN', 'a.estimateManagerSno = c.sno' ]
                    , 'field' => ['managerNm as estimateManagerNm', 'cellPhone as estimateManagerCellPhone']
                ]
            , 'prj' => //프로젝트정보
                [
                    'data' => [ ImsDBName::PROJECT, 'LEFT OUTER JOIN', 'a.projectSno = prj.sno' ]
                    , 'field' => ['projectNo', 'projectType', 'projectStatus']
                ]
            , 'cust' => //고객정보
                [
                    'data' => [ ImsDBName::CUSTOMER, 'LEFT OUTER JOIN', 'a.customerSno = cust.sno' ]
                    , 'field' => ['customerName']
                ]
        ]);
    }


}

