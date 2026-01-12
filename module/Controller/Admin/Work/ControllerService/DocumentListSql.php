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
use SlComponent\Util\SlLoader;


/**
 * 문서 리스트 SQL
 */
class DocumentListSql {

    use ListSqlTrait;

    const MAIN_TABLE = 'sl_workDocument';

    /*'번호' ,'고객사' ,'문서명' ,'작성부서' ,'담당자' ,'등록일'*/

    public function getList($searchData){
        //gd_debug( $searchData );

        $documentService = SlLoader::cLoad('work','documentService','');

        $tableList= [
            'a' => //메인
                [
                    'data' => [ 'sl_workDocument' ]
                    , 'field' => ['*']
                ]
            , 'b' => //프로젝트
                [
                    'data' => [ 'sl_project', 'JOIN', 'a.projectSno = b.sno' ]
                    , 'field' => ['projectName']
                ]
            , 'c' => //거래처
                [
                    'data' => [ 'sl_workCompany', 'LEFT OUTER JOIN', 'b.companySno = c.sno' ]
                    , 'field' => ['companyName']
                ]
            , 'd' => //등록자
                [
                    'data' => [ DB_MANAGER, 'LEFT OUTER JOIN', 'a.regManagerSno = d.sno' ]
                    , 'field' => ['managerNm as regManagerName']
                ]
            , 'e' => //영업담당
                [
                    'data' => [ DB_MANAGER, 'LEFT OUTER JOIN', 'b.salesManagerSno = e.sno' ]
                    , 'field' => ['managerNm as salesManagerName']
                ]
            , 'f' => //디자인담당
                [
                    'data' => [ DB_MANAGER, 'LEFT OUTER JOIN', 'b.designManagerSno = f.sno' ]
                    , 'field' => ['managerNm as designManagerName']
                ]
        ];

        $table = DBUtil2::setTableInfo($tableList);

        //Search
        $searchVo = $this->createDefaultSearchVo($searchData);

        if( !empty($searchData['docDept']) ){
            $searchVo->setWhere('UPPER(a.docDept) = ? ');
            $searchVo->setWhereValue($searchData['docDept']);
        }
        if( !empty($searchData['docType']) ){
            $searchVo->setWhere('a.docType = ? ');
            $searchVo->setWhereValue($searchData['docType']);
        }

        //정렬 설정
        $searchVo->setOrder($searchData['sort']);

        $list = DBUtil2::getComplexListWithPaging($table ,$searchVo, $searchData, false);

        $list['listData'] = $documentService->decorationDocumentList($list['listData']);

        return $list;

    }

}