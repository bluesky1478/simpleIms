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
namespace Controller\Admin\Ims\ControllerService;

use App;
use Component\Ims\ImsCodeMap;
use Component\Ims\ImsDBName;
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
use SiteLabUtil\SlCommonUtil;
use SlComponent\Database\DBConst;
use SlComponent\Database\DBUtil;
use SlComponent\Database\DBUtil2;
use SlComponent\Database\SearchVo;
use SlComponent\Database\TableVo;
use SlComponent\Util\ListSqlTrait;
use SlComponent\Util\SitelabLogger;
use SlComponent\Util\SlCodeMap;
use SlComponent\Util\SlLoader;


/**
 * Inout 리스트 SQL
 */
class ImsProduceListSql {

    use ListSqlTrait;

    const MAIN_TABLE = ImsDBName::PROJECT;

    public function getList($searchData){
        $projectTableField = DBTableField::tableImsProject();
        $projectField = SlCommonUtil::arrayAppKeyValue($projectTableField,'val','val');
        $mainField = 'a.'.implode(',a.',$projectField);
        //implode(',a.',$projectField)

        $produceTableField = DBTableField::tableImsProduce();
        $produceField = SlCommonUtil::arrayAppKeyValue($produceTableField,'val','val');
        $refineNotAs = 'f.'.implode(',f.',$produceField); //group 용
        $refineField = [
            'sno', 'regManagerSno', 'lastManagerSno', 'regDt', 'modDt' ,'projectSno', 'memo' , 'produceCompanySno'
        ];
        foreach($refineField as $fieldName){
            $produceField['produce'.ucfirst($fieldName)] = $fieldName.' as produce'.ucfirst($fieldName);
            unset($produceField[$fieldName]);
        }
        $refineProduceField = 'f.'.implode(',f.',$produceField);

        $tableList= [
            'a' => //프로젝트
                [
                    'data' => [ self::MAIN_TABLE ]
                    , 'field' => [$mainField]
                ]
            , 'b' => //고객 정보
                [
                    'data' => [ ImsDBName::CUSTOMER, 'LEFT OUTER JOIN', 'a.customerSno = b.sno' ]
                    , 'field' => ['b.customerName','b.use3pl','b.useMall']
                ]
            , 'c' => //영업 담당자명
                [
                    'data' => [ DB_MANAGER, 'LEFT OUTER JOIN', 'a.salesManagerSno = c.sno' ]
                    , 'field' => ['c.managerNm as salesManagerNm']
                ]
            , 'd' => //디자인 담당자명
                [
                    'data' => [ DB_MANAGER, 'LEFT OUTER JOIN', 'a.designManagerSno = d.sno' ]
                    , 'field' => ['d.managerNm as designManagerNm']
                ]
            , 'e' => //제품
                [
                    'data' => [ ImsDBName::PRODUCT, 'LEFT OUTER JOIN', 'a.sno = e.projectSno' ]
                    , 'field' => [
                        'sum(e.currentPrice) as currentPrice',
                        'sum(e.targetPrice) as targetPrice',
                        'sum(e.targetPrdCost) as targetPrdCost',
                        'sum(e.prdCost) as prdCost',
                        'sum(e.prdExQty) as prdExQty',
                        'max(e.productName) as style',
                        'count(1) as styleCount',
                    ]
                ]
            , 'f' => //사실 얘가 메인
                [
                    'data' => [ ImsDBName::PRODUCE, 'LEFT OUTER JOIN', 'a.sno = f.projectSno' ]
                    , 'field' => [$refineProduceField]
                ]
            , 'g' => //생산처
                [
                    'data' => [ DB_MANAGER, 'LEFT OUTER JOIN', 'f.produceCompanySno = g.sno' ]
                    , 'field' => ['g.managerNm as produceCompanyName']
                ]
        ];

        $table = DBUtil2::setTableInfo($tableList, false);

        //검색어
        $tmpKeyword = $tmpKey = '';
        if( 'e.styleCode' == $searchData['key'] ){
            $tmpKey = $searchData['key'];
            $tmpKeyword = $searchData['keyword'];
            unset($searchData['key']);
            unset($searchData['keyword']);
        }
        //Search
        $searchVo = $this->createDefaultSearchVo($searchData);

        $searchVo->setWhere('( e.delFl is null OR e.delFl=? )');
        $searchVo->setWhereValue('n');

        if( 'e.styleCode' == $tmpKey ){
            $searchVo->setWhere( DBUtil::bind( "upper(replace({$tmpKey},' ',''))", DBUtil::BOTH_LIKE ) );
            $searchVo->setWhereValue( str_replace(' ','',strtoupper($tmpKeyword)) );
            $searchData['key'] = $tmpKey;
            $searchData['keyword'] = $tmpKeyword;
        }

        //Search
        //$searchVo = $this->createDefaultSearchVo($searchData);
        $searchVo = $this->setCondition($searchData, $searchVo); //기본 외 조건 추가 검색

        //정렬 설정
        $searchVo->setOrder($searchData['sort']);

        $groupList[] = 'b.customerName';
        $groupList[] = 'c.managerNm';
        $groupList[] = 'd.managerNm';

        //$groupList = array_merge($groupList, $refinePreparedField);
        $searchVo->setGroup($mainField.','.implode(',', $groupList).','.$refineNotAs);

        return DBUtil2::getComplexListWithPaging($table ,$searchVo, $searchData, false, false);
    }

    /**
     * @param $searchData
     * @param $searchVo
     * @return mixed
     */
    public function setCondition($searchData, $searchVo){
        $request=\Request::get()->toArray();
        $step = SlCommonUtil::getOnlyNumber($request['status']);

        //생산처일 경우에는 본인 리스트만 보이게
        $mId = \Session::get('manager.managerId');
        if( in_array($mId, ImsCodeMap::PRODUCE_COMPANY_MANAGER) ){
            $searchVo->setWhere('a.produceCompanySno = ?');
            $searchVo->setWhereValue(\Session::get('manager.sno'));
            $searchVo->setWhere('f.produceCompanySno = ?');
            $searchVo->setWhereValue(\Session::get('manager.sno'));
        }

        if( !empty($searchData['produceCompanySno']) && 'all' !== $searchData['produceCompanySno'] ){
            $searchVo->setWhere('a.produceCompanySno = ?');
            $searchVo->setWhereValue($searchData['produceCompanySno']);
            $searchVo->setWhere('f.produceCompanySno = ?');
            $searchVo->setWhereValue($searchData['produceCompanySno']);
        }

        if(!empty($step)){
            if( 90 == $step || 99 == $step ){
                $searchVo->setWhere('f.produceStatus = ?');
                $searchVo->setWhereValue('99');
                $searchVo->setWhere('a.projectStatus = ?');
                $searchVo->setWhereValue('90');
            }else{

                $searchVo->setWhere('f.produceStatus = ?');
                $searchVo->setWhereValue($step);

                $searchVo->setWhere('a.projectStatus = ?');
                $searchVo->setWhereValue('80');
            }

        }else{
            $searchVo->setWhere('a.projectStatus = ?');
            $searchVo->setWhereValue('80');
        }

        if( 'y' === $searchData['showReqAccept']  ){
            $searchVo->setWhere(" prdStep like '%\\\"confirmYn\\\":\\\"r\"%' ");
        }

        if( 'all' !== $searchData['packingYn']  ){
            $searchVo->setWhere(" a.packingYn = '{$searchData['packingYn']}' ");
        }
        if( 'all' !== $searchData['use3pl']  ){
            $searchVo->setWhere(" b.use3pl = '{$searchData['use3pl']}' ");
        }
        if( 'all' !== $searchData['useMall']  ){
            $searchVo->setWhere(" b.useMall = '{$searchData['useMall']}' ");
        }

        if( !empty($searchData['projectYear']) ){
            $searchVo->setWhere("a.projectYear=?");
            $searchVo->setWhereValue($searchData['projectYear']);
        }
        if( !empty($searchData['projectSeason']) ){
            $searchVo->setWhere("a.projectSeason=?");
            $searchVo->setWhereValue($searchData['projectSeason']);
        }

        return $searchVo;
    }

}