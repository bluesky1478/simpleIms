<?php

/**
 * This is commercial software, only users who have purchased a valid license
 * and accept to the terms of the License Agreement can install and use this
 * program.
 *
 * Do not edit or add to this file if you wish to upgrade Godomall5 to newer
 * versions in the future.
 *
 * @copyright ⓒ 2016, NHN godo: Corp.
 * @link      http://www.godo.co.kr
 */
namespace Controller\Admin\Work\ControllerService;

use App;
use Component\Mail\MailAutoObserver;
use Component\Godo\NaverPayAPI;
use Component\Member\Member;
use Component\Naver\NaverPay;
use Component\Database\DBTableField;
use Component\Delivery\OverseasDelivery;
use Component\Deposit\Deposit;
use Component\ExchangeRate\ExchangeRate;
use Component\Mail\MailMimeAuto;
use Component\Mall\Mall;
use Component\Mall\MallDAO;
use Component\Member\Manager;
use Component\Member\Util\MemberUtil;
use Component\Mileage\Mileage;
use Component\Policy\Policy;
use Component\Sms\Code;
use Component\Sms\SmsAuto;
use Component\Sms\SmsAutoCode;
use Component\Sms\SmsAutoObserver;
use Component\Validator\Validator;
use Component\Goods\SmsStock;
use Component\Goods\KakaoAlimStock;
use Component\Goods\MailStock;
use Component\Work\WorkCodeMap;
use Encryptor;
use Exception;
use Framework\Application\Bootstrap\Log;
use Framework\Debug\Exception\AlertOnlyException;
use Framework\Debug\Exception\AlertRedirectException;
use Framework\Helper\MallHelper;
use Framework\Utility\ArrayUtils;
use Framework\Utility\ComponentUtils;
use Framework\Utility\NumberUtils;
use Framework\Utility\StringUtils;
use Framework\Utility\UrlUtils;
use Globals;
use Logger;
use LogHandler;
use Request;
use Session;
use Framework\Utility\DateTimeUtils;
use SlComponent\Database\DBConst;
use SlComponent\Database\DBUtil;
use SlComponent\Database\DBUtil2;
use SlComponent\Database\SearchVo;
use SlComponent\Database\TableVo;
use SlComponent\Util\ListSqlTrait;
use SlComponent\Util\SitelabLogger;


/**
 * 업무요청 리스트 SQL
 */
class RequestListSql {

    use ListSqlTrait;

    const MAIN_TABLE = 'sl_workRequest';

    public function getList($searchData){
        $tableList= [
            'a' => //메인
                [
                    'data' => [ self::MAIN_TABLE ]
                    , 'field' => ['*']
                ]
            , 'b' => //요청자
                [
                    'data' => [ DB_MANAGER, 'LEFT OUTER JOIN', 'a.writeManagerSno = b.sno' ]
                    , 'field' => ['managerNm as writeManagerName', 'departmentCd']
                ]
            , 'c' => //처리자
                [
                    'data' => [ DB_MANAGER, 'LEFT OUTER JOIN', 'a.procManagerSno = c.sno' ]
                    , 'field' => ['managerNm as procManagerName']
                ]
            , 'd' => //관련문서 문서 중 tempFl 제외.
                [
                    'data' => [ 'sl_workDocument', 'LEFT OUTER JOIN', 'a.documentSno = d.sno and d.tempFl = \'n\' and d.delFl = \'n\'  ' ]
                    , 'field' => ['docDept', 'docType', 'version', 'tempFl', 'projectSno']
                ]
            , 'e' => //부서
                [
                    'data' => [ 'es_code', 'LEFT OUTER JOIN', 'a.targetDeptNo = e.itemCd' ]
                    , 'field' => ['itemNm as targetDeptName']
                ]
            , 'f' => //작성자부서
                [
                    'data' => [ 'es_code', 'LEFT OUTER JOIN', 'b.departmentCd = f.itemCd' ]
                    , 'field' => ['itemNm as writerDeptName', 'itemCd' ,  ]
                ]
            , 'g' => //프로젝트
                [
                    'data' => [ 'sl_project', 'LEFT OUTER JOIN', 'd.projectSno = g.sno' ]
                    , 'field' => ['projectName' ]
                ]
            , 'h' => //고객사
                [
                    'data' => [ 'sl_workCompany', 'LEFT OUTER JOIN', 'g.companySno = h.sno' ]
                    , 'field' => ['companyName' ]
                ]

        ];

        $table = DBUtil2::setTableInfo($tableList);

        //Search
        $searchVo = $this->createDefaultSearchVo($searchData);
        //$searchVo->setWhere('d.tempFl=?');
        //$searchVo->setWhereValue('n');
        //$searchVo->setWhere('d.delFl=?');
        //$searchVo->setWhereValue('n');

        if( !empty($searchData['docDept']) ){
            $searchVo->setWhere('a.targetDeptNo = ? ');
            $searchVo->setWhereValue( WorkCodeMap::DEPT_CODE[strtoupper($searchData['docDept'])] );
        }

        if( 'all' !== $searchData['isProcFl'] && !empty($searchData['isProcFl']) ) {
            $searchVo->setWhere('a.isProcFl = ? ');
            $searchVo->setWhereValue($searchData['isProcFl']);
        }

        //정렬 설정
        $searchVo->setOrder($searchData['sort']);

        return DBUtil2::getComplexListWithPaging($table ,$searchVo, $searchData, false);

    }

}