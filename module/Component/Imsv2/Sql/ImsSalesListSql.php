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
class ImsSalesListSql {

    /**
     * 프로젝트 리스트 스케쥴
     * @param SearchVo $searchVo
     * @return array
     * @throws \Exception
     */
    public function getSalesListSql(SearchVo $searchVo){
        //프로젝트 추가 정보 + 스케쥴 필드 설정
        $extField = array_flip(DBTableField::getTableKey(ImsDBName::PROJECT_EXT));
        $extField = array_flip(SlCommonUtil::unsetByList($extField,[
            'regDt','modDt', 'sno', 'projectSno'
        ]));
        $extFieldStr = 'ext.'.implode(',ext.',$extField);
        $tableInfo = [
            'prj' => //메인
                [
                    'data' => [ ImsDBName::PROJECT ]
                    , 'field' => ['prj.*', 'prj.sno as projectSno' ]
                ]
            , 'cust' => //고객정보
                [
                    'data' => [ ImsDBName::CUSTOMER, 'JOIN', 'prj.customerSno = cust.sno' ]
                    , 'field' => ['cust.customerName','cust.industry']
                ]
            , 'b' => //등록자
                [
                    'data' => [ DB_MANAGER, 'LEFT OUTER JOIN', 'prj.regManagerSno = b.sno' ]
                    , 'field' => ['b.managerNm as regManagerNm']
                ]
            , 'sales' => //영업 담당자
                [
                    'data' => [ DB_MANAGER, 'LEFT OUTER JOIN', 'prj.salesManagerSno = sales.sno' ]
                    , 'field' => ['sales.managerNm as salesManagerNm']
                ]
            , 'desg' => //디자인 담당자
                [
                    'data' => [ DB_MANAGER, 'LEFT OUTER JOIN', 'prj.designManagerSno = desg.sno' ]
                    , 'field' => ['desg.managerNm as designManagerNm']
                ]
            , 'ext' => //확장정보
                [
                    'data' => [ ImsDBName::PROJECT_EXT, 'LEFT OUTER JOIN', 'prj.sno = ext.projectSno' ]
                    , 'field' => [$extFieldStr]
                ]
        ];
        //TODO : 스타일명은 어떻게 표현 ? 외 건 or 전체 다 표현 추정매출
        //프로젝트에 스타일 금액 집계 시킨다. ( 저장할 때 + 매 분 정제 )
        return DBUtil2::setTableInfo($tableInfo, false);
    }
}



