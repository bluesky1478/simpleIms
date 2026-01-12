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
class ImsPreparedListSql {

    use ListSqlTrait;

    const MAIN_TABLE = ImsDBName::PROJECT;

    public function getList($searchData){
        $projectTableField = DBTableField::tableImsProject();
        $projectField = SlCommonUtil::arrayAppKeyValue($projectTableField,'val','val');
        $mainField = 'a.'.implode(',a.',$projectField);
        //implode(',a.',$projectField)

        $preparedTableField = DBTableField::tableImsPrepared();
        $preparedField = SlCommonUtil::arrayAppKeyValue($preparedTableField,'val','val');
        //gd_debug($preparedField);

        $refineNotAs = 'f.'.implode(',f.',$preparedField); //group 용
        $refineField = [
            'sno', 'regManagerSno', 'lastManagerSno', 'regDt', 'modDt', 'produceCompanySno'
        ];
        foreach($refineField as $fieldName){
            $preparedField['prepared'.ucfirst($fieldName)] = $fieldName.' as prepared'.ucfirst($fieldName);
            unset($preparedField[$fieldName]);
        }
        $refinePreparedField = 'f.'.implode(',f.',$preparedField);

        $tableList= [
            'a' => //프로젝트
                [
                    'data' => [ self::MAIN_TABLE ]
                    , 'field' => [$mainField]
                ]
            , 'b' => //고객 정보
                [
                    'data' => [ ImsDBName::CUSTOMER, 'LEFT OUTER JOIN', 'a.customerSno = b.sno' ]
                    , 'field' => ['b.customerName']
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
                        'sum(e.fabricCount) as fabricCount',
                        'sum(e.btCount) as btCount',
                        'max(e.productName) as style',
                        'count(1) as styleCount',
                    ]
                ]
            , 'f' => //사실 얘가 메인
                [
                    'data' => [ ImsDBName::PREPARED, 'JOIN', 'a.sno = f.projectSno' ]
                    , 'field' => [$refinePreparedField]
                ]
            , 'c' => //등록자
                [
                    'data' => [ DB_MANAGER, 'LEFT OUTER JOIN', 'f.regManagerSno = c.sno' ]
                    , 'field' => ['c.managerNm as preparedRegManagerNm']
                ]
            , 'd' => //생산처
                [
                    'data' => [ DB_MANAGER, 'LEFT OUTER JOIN', 'f.produceCompanySno = d.sno' ]
                    , 'field' => ['d.managerNm as produceCompany']
                ]
        ];

        $table = DBUtil2::setTableInfo($tableList, false);

        //Search
        $searchVo = $this->createDefaultSearchVo($searchData);
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
        $step = $request['preparedType'];
        if(!empty($step)){
            $searchVo->setWhere('f.preparedType = ?');
            $searchVo->setWhereValue($step);
        }

        //생산처일 경우에는 본인 리스트만 보이게
        $mId = \Session::get('manager.managerId');
        if( in_array($mId, ImsCodeMap::PRODUCE_COMPANY_MANAGER) ){
            $searchVo->setWhere('f.produceCompanySno = ?');
            $searchVo->setWhereValue(\Session::get('manager.sno'));
        }

        if( 'all' !== $searchData['workStep'] && !empty($searchData['workStep'])){
//n,r,p,f 작대, 승대, 승완, 반려
/*0 => '요청', ==> 작업대기
1 => '처리중', ===> 작업대기
2 => '처리완료', ==>> 승인대기
3 => '처리불가', ==> 없음
4 => '승인',   //승인 -> 승인  ==> 승인완료
5 => '재요청', //반려,번복 -> 다시해.  반려 */
            if( 'p' === $searchData['workStep'] ){
                $searchVo->setWhere('f.preparedStatus = 4 ');
            }else if( 'r' === $searchData['workStep'] ){
                $searchVo->setWhere('f.preparedStatus = 2 ');
            }else if( 'f' === $searchData['workStep'] ){
                $searchVo->setWhere('f.preparedStatus = 5 ');
            }else {
                if('work' !== $step){
                    $searchVo->setWhere('f.preparedStatus in ( 0, 1 ) ');
                }else{
                    $searchVo->setWhere('f.preparedStatus in ( 0, 1, 5 ) ');
                }
            }
        }

        return $searchVo;
    }

}