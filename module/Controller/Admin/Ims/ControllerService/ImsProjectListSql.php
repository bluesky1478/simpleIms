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
class ImsProjectListSql {

    use ListSqlTrait;

    const MAIN_TABLE = ImsDBName::PROJECT;

    public function getList($searchData){
        $request=\Request::get()->toArray();
        $isStyle = 'style'===$request['view']?true:false;

        $projectTableField = DBTableField::tableImsProject();
        $projectField = SlCommonUtil::arrayAppKeyValue($projectTableField,'val','val');
        $mainField = 'a.'.implode(',a.',$projectField);
        //implode(',a.',$projectField)

        $meetingTableField = DBTableField::tableImsMeeting();
        $meetingField = SlCommonUtil::arrayAppKeyValue($meetingTableField,'val','val');
        $meetingUnsetField=[
            'sno', 'projectSno', 'regManagerSno' , 'lastManagerSno', 'regDt', 'modDt'
        ];
        foreach($meetingUnsetField as $field){
            unset( $meetingField[$field] );
        }
        $meetingFieldStr = 'f.'.implode(',f.',$meetingField);

        $sqlList=[];
        $sqlList[] = "(select count(1) cnt from sl_imsFile where projectSno = a.sno and 'fileEtc2' = fileDiv ) as estimateCount";
        $sqlList[] = "(select count(1) cnt from sl_imsFile where projectSno = a.sno and 'fileEtc4' = fileDiv ) as salesConfirmedCount";
        $sqlList[] = "(select count(1) cnt from sl_imsFile where projectSno = a.sno and 'fileWork' = fileDiv ) as fileWorkCount";
        $sqlStr = implode(',', $sqlList);
        //gd_debug($sqlList);

        $tableList= [
            'a' => //프로젝트
                [
                    'data' => [ self::MAIN_TABLE ]
                    , 'field' => [$mainField, $sqlStr]
                ]
            , 'b' => //고객 정보
                [
                    'data' => [ ImsDBName::CUSTOMER, 'LEFT OUTER JOIN', 'a.customerSno = b.sno' ]
                    , 'field' => ['b.customerName']
                ]
            , 'c' => //영업 담당자명
                [
                    'data' => [ DB_MANAGER, 'LEFT OUTER JOIN', 'b.salesManagerSno = c.sno' ]
                    , 'field' => ['c.managerNm as salesManagerNm']
                ]
            , 'd' => //디자인 담당자명
                [
                    'data' => [ DB_MANAGER, 'LEFT OUTER JOIN', 'a.designManagerSno = d.sno' ]
                    , 'field' => ['d.managerNm as designManagerNm']
                ]
            , 'e' => //제품
                [
                    'data' => [ ImsDBName::PRODUCT, 'LEFT OUTER JOIN', ' a.sno = e.projectSno' ]
                    , 'field' => [
                        'sum(e.currentPrice) as currentPrice',
                        'sum(e.targetPrice) as targetPrice',
                        'sum(e.targetPrdCost) as targetPrdCost',
                        'sum(e.prdCost) as prdCost',
                        'sum(e.salePrice) as salePrice',
                        'sum(e.prdExQty) as prdExQty',
                        'max(e.productName) as style',
                        'sum(e.fabricCount) as fabricCount',
                        'sum(e.btCount) as btCount',
                        'sum(e.targetPrice * e.prdExQty) as calcSize',
                        'count(1) as styleCount',
                    ]
                ]
            , 'f' => //미팅
                [
                    'data' => [ ImsDBName::MEETING, 'LEFT OUTER JOIN', 'a.sno = f.projectSno' ]
                    , 'field' => [$meetingFieldStr]
                ]
        ];

        if( $isStyle ){
            //Style형태
            $tableList['e'] = [
                'data' => [ ImsDBName::PRODUCT, 'LEFT OUTER JOIN', 'a.sno = e.projectSno' ]
                , 'field' => [
                    'e.sno as productSno',
                    'e.productName',
                    'e.styleCode',
                    'e.currentPrice',
                    'e.targetPrice',
                    'e.targetPrdCost',
                    'e.prdCost',
                    'e.salePrice',
                    'e.prdExQty',
                    'e.fileThumbnail',
                    'e.fabricCount',
                    'e.btCount',
                ]
            ];
        }else{
            //일반
            $tableList['e'] = [
                'data' => [ ImsDBName::PRODUCT, 'LEFT OUTER JOIN', 'a.sno = e.projectSno and ( e.delFl is null OR e.delFl=\'n\' ) ' ]
                , 'field' => [
                    'sum(e.currentPrice) as currentPrice',
                    'sum(e.targetPrice) as targetPrice',
                    'sum(e.targetPrdCost) as targetPrdCost',
                    'sum(e.salePrice) as salePrice',
                    'sum(e.prdCost) as prdCost',
                    'sum(e.prdExQty) as prdExQty',
                    'max(e.productName) as style',
                    'sum(e.fabricCount) as fabricCount',
                    'sum(e.btCount) as btCount',
                    'sum((e.targetPrice * e.prdExQty)) as calcSize',
                    'count(1) as styleCount',
                ]
            ];
        }

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

        if( !empty($searchData['procStatusKey']) && !empty($searchData['procStatusValue']) ){
            $searchVo->setWhere( 'a.'.$searchData['procStatusKey']. '=?' );
            $searchVo->setWhereValue( -1 == $searchData['procStatusValue'] ? 0 : $searchData['procStatusValue'] );
        }

        if( 'e.styleCode' == $tmpKey ){
            $searchVo->setWhere( DBUtil::bind( "upper(replace({$tmpKey},' ',''))", DBUtil::BOTH_LIKE ) );
            $searchVo->setWhereValue( str_replace(' ','',strtoupper($tmpKeyword)) );
            $searchData['key'] = $tmpKey;
            $searchData['keyword'] = $tmpKeyword;
        }

        $searchVo = $this->setCondition($searchData, $searchVo); //기본 외 조건 추가 검색

        //정렬 설정
        $searchVo->setOrder($searchData['sort'].', a.regDt desc');

        $groupList[] = 'b.customerName';
        $groupList[] = 'c.managerNm';
        $groupList[] = 'd.managerNm';
        $groupList[] = $meetingFieldStr;

        if( !$isStyle ){
            $searchVo->setGroup($mainField.','.implode(',', $groupList));
        }else{
            $searchVo->setWhere('( e.delFl is null OR e.delFl=? )');
            $searchVo->setWhereValue('n');
        }

        return DBUtil2::getComplexListWithPaging($table ,$searchVo, $searchData, false);
    }

    /**
     * @param $searchData
     * @param $searchVo
     * @return mixed
     */
    public function setCondition($searchData, $searchVo){
        $request=\Request::get()->toArray();
        $step = SlCommonUtil::getOnlyNumber($request['status']);

        if(SlCommonUtil::isFactory()){
            $searchVo->setWhere('a.produceCompanySno = ?');
            $searchVo->setWhereValue(\Session::get('manager.sno'));
        }

        if(!empty($step)){
            $searchVo->setWhere('a.projectStatus = ?');
            $searchVo->setWhereValue($step);
        }

        if( !empty($searchData['orderProgressFl'])  && 'all' !== $searchData['orderProgressFl'][0]  ){
            $searchVo->setWhere(DBUtil::bind('a.projectStatus', DBUtil::IN, count($searchData['orderProgressFl']) ));
            $searchVo->setWhereValueArray( $searchData['orderProgressFl'] );
        }

        if( !empty($searchData['projectType'])  && 'all' !== $searchData['projectType'][0]  ){
            $searchVo->setWhere(DBUtil::bind('a.projectType', DBUtil::IN, count($searchData['projectType']) ));
            $searchVo->setWhereValueArray( $searchData['projectType'] );
        }

        if( 'y' === $searchData['isAccOnly'][0]  ){
            $searchVo->setWhere(" ( planConfirm = 'r' or proposalConfirm = 'r' or sampleConfirm = 'r'  )  ");     //제안
        }

        if( 'y' === $searchData['isExcludeRtw'][0]  ){
            $searchVo->setWhere(" a.projectType not in ( 3, 4, 5 ) ");     //기성복 제외
        }

        if( 'all' !== $searchData['isProduction'][0] ){
            $searchVo->setWhere(DBUtil::bind('a.productionStatus', DBUtil::IN, count($searchData['isProduction']) ));
            $searchVo->setWhereValueArray( $searchData['isProduction'] );
        }

        if( !empty($searchData['projectYear']) ){
            $searchVo->setWhere("a.projectYear=?");
            $searchVo->setWhereValue($searchData['projectYear']);
        }
        if( !empty($searchData['projectSeason']) ){
            $searchVo->setWhere("a.projectSeason=?");
            $searchVo->setWhereValue($searchData['projectSeason']);
        }

        //gd_debug($searchData);

        return $searchVo;
    }

}