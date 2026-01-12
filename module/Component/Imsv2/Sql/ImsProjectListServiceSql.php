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
class ImsProjectListServiceSql {

    public function getProjectListSql(){
        //프로젝트 추가 정보 + 스케쥴 필드 설정
        $extField = array_flip(DBTableField::getTableKey(ImsDBName::PROJECT_EXT));
        $extField = array_flip(SlCommonUtil::unsetByList($extField,[
            'regDt','modDt', 'sno', 'projectSno'
        ]));
        //$extFieldStr = 'ext.'.implode(',ext.',$extField);

        return DBUtil2::setTableInfo([
            'prj' => //메인
                [
                    'data' => [ ImsDBName::PROJECT ]
                    , 'field' => ['*, prj.sno as projectSno']
                ]
            , 'cust' => //고객정보
                [
                    'data' => [ ImsDBName::CUSTOMER, 'JOIN', 'prj.customerSno = cust.sno' ]
                    , 'field' => ['customerName', 'busiCateSno', 'contactName', 'contactEmail', 'contactMobile']
                ]
            , 'biz_cate2' => //업종정보
                [
                    'data' => [ ImsDBName::BUSI_CATE, 'LEFT JOIN', 'cust.busiCateSno = biz_cate2.sno' ]
                    , 'field' => ['cateName as bizCate2']
                ]
            , 'biz_cate1' => //업종정보 (부모)
                [
                    'data' => [ ImsDBName::BUSI_CATE, 'LEFT JOIN', 'biz_cate2.parentBusiCateSno = biz_cate1.sno' ]
                    , 'field' => ['cateName as bizCate1']
                ]
            , 'b' => //등록자
                [
                    'data' => [ DB_MANAGER, 'LEFT OUTER JOIN', 'prj.regManagerSno = b.sno' ]
                    , 'field' => ['managerNm as regManagerNm']
                ]
            , 'sales' => //영업 담당자
                [
                    'data' => [ DB_MANAGER, 'LEFT OUTER JOIN', 'prj.salesManagerSno = sales.sno' ]
                    , 'field' => ['managerNm as salesManagerNm']
                ]
            , 'desg' => //디자인 담당자
                [
                    'data' => [ DB_MANAGER, 'LEFT OUTER JOIN', 'prj.designManagerSno = desg.sno' ]
                    , 'field' => ['managerNm as designManagerNm']
                ]
            , 'factory' => //대표 생산처
                [
                    'data' => [ DB_MANAGER, 'LEFT OUTER JOIN', 'prj.produceCompanySno = factory.sno' ]
                    , 'field' => ['managerNm as mainFactoryName']
                ]
            , 'ext' => //확장정보
                [
                    'data' => [ ImsDBName::PROJECT_EXT, 'LEFT OUTER JOIN', 'prj.sno = ext.projectSno' ]
                    , 'field' => $extField
                ]
        ]);
    }

    /**
     * 프로젝트 리스트 스케쥴
     * @param SearchVo $searchVo
     * @param false $isGroup
     * @param false $isSchedule
     * @return array
     * @throws \Exception
     */
    public function getProjectListWithStyleSql(SearchVo $searchVo, $isGroup=false, $isSchedule=false){
        //프로젝트 추가 정보 + 스케쥴 필드 설정
        $extField = array_flip(DBTableField::getTableKey(ImsDBName::PROJECT_EXT));
        $extField = array_flip(SlCommonUtil::unsetByList($extField,[
            'regDt','modDt', 'sno', 'projectSno'
        ]));
        $extFieldStr = 'ext.'.implode(',ext.',$extField);

        // Group by 설정 시작
        if($isGroup){
            $projectGroupField = array_flip(array_flip(DBTableField::getTableKey(ImsDBName::PROJECT)));
            foreach($projectGroupField as $key => $each){
                $each = 'prj.' . $each;
                $projectGroupField[$key] = $each;
            }
            $groupField = $projectGroupField;
            $groupField[] = 'cust.customerName';
            $groupField[] = 'cust.industry';
            $groupField[] = 'b.managerNm';
            $groupField[] = 'sales.managerNm';
            $groupField[] = 'desg.managerNm';
            $groupField[] = 'ext.salesStatus';
            $groupField[] = 'ext.salesDeliveryDt';
            $groupField[] = 'ext.salesStyleName';
            $groupField[] = 'ext.extAmount';
            $groupField[] = 'ext.extMargin';
            $groupField[] = 'ext.designWorkType';
            $groupField[] = 'ext.salesExDt';
            $groupField[] = 'ext.extDesigner';
            $searchVo->setGroup(implode(',',$groupField));
            $projectField = implode(',',$projectGroupField);
            $styleField = " 
            CASE WHEN COUNT(DISTINCT prd.productName) = 0 THEN NULL 
                 WHEN COUNT(DISTINCT prd.productName) = 1 THEN MAX(DISTINCT prd.productName) 
            ELSE CONCAT( SUBSTRING_INDEX(GROUP_CONCAT(DISTINCT prd.productName ORDER BY prd.sort ASC),',', 1), ' 외 ', COUNT(DISTINCT prd.productName) - 1,'건') 
            END AS productName ";
        }else{
            $projectField = 'prj.*';

            $styleFieldList = [
                'prd.sno as styleSno',
                'prd.productName',
                'right(prd.prdYear,2) as prdYear',
                'prd.prdSeason',
                'prd.styleCode',
                'prd.prdCost',
                'prd.salePrice',
                'prd.prdExQty',
                'prd.prdCostConfirmSno',
                'prd.estimateConfirmSno',
                'prd.workStatus as prdWorkStatus',

                'prd.priceConfirm',
                'prd.prdCostStatus',
                'prd.msDeliveryDt as prdMsDeliveryDt',
                'prd.customerDeliveryDt as prdCustomerDeliveryDt',
                'prd.fabricStatus as prdFabricStatus',
                'prd.btStatus as prdBtStatus',
                //'prd.produceCompanyName as prdBtStatus',

            ];
            $styleField = implode(',', $styleFieldList);
        }

        // Group by 설정 끝
        $tableInfo = [
            'prj' => //메인
                [
                    'data' => [ ImsDBName::PROJECT ]
                    , 'field' => [$projectField, 'prj.sno as projectSno' ]
                ]
            , 'prd' => //스타일
            [
                'data' => [ ImsDBName::PRODUCT, 'LEFT OUTER JOIN', 'prj.sno = prd.projectSno and prd.delFl = \'n\'' ]
                , 'field' => [ $styleField ]
            ]
            , 'cust' => //고객정보
            [
                'data' => [ ImsDBName::CUSTOMER, 'JOIN', 'prj.customerSno = cust.sno' ]
                , 'field' => ['cust.customerName','cust.industry']
            ]
            , 'biz_cate2' => //업종정보
            [
                'data' => [ ImsDBName::BUSI_CATE, 'LEFT JOIN', 'cust.busiCateSno = biz_cate2.sno' ]
                , 'field' => ['biz_cate2.cateName as bizCate2']
            ]
            , 'biz_cate1' => //업종정보 (부모)
            [
                'data' => [ ImsDBName::BUSI_CATE, 'LEFT JOIN', 'biz_cate2.parentBusiCateSno = biz_cate1.sno' ]
                , 'field' => ['biz_cate1.cateName as bizCate1']
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
            , 'factory' => //디자인 담당자
            [
                'data' => [ DB_MANAGER, 'LEFT OUTER JOIN', 'prd.produceCompanySno = factory.sno' ]
                , 'field' => ['factory.managerNm as produceCompanyName']
            ]
            , 'ext' => //확장정보
            [
                'data' => [ ImsDBName::PROJECT_EXT, 'LEFT OUTER JOIN', 'prj.sno = ext.projectSno' ]
                , 'field' => [$extFieldStr]
            ]
        ];

        //그룹이 아닐때는 견적 정보 조인
        if(!$isGroup){
            $tableInfo['estimate'] = [
                'data' => [ ImsDBName::ESTIMATE, 'LEFT OUTER JOIN', 'prd.estimateConfirmSno = estimate.sno' ]
                , 'field' => ['estimate.contents as estimateContents, estimate.estimateCost']
            ];
            $tableInfo['cost'] = [
                'data' => [ ImsDBName::ESTIMATE, 'LEFT OUTER JOIN', 'prd.prdCostConfirmSno = cost.sno' ]
                , 'field' => ['cost.contents as costContents, cost.estimateCost as prdEstimateCost']
            ];
        }else{
            //그룹일 때
            $tableInfo['prd']['field'][] = "( select sum(salePrice * prdExQty) as price from sl_imsProjectProduct where delFl='n' and projectSno = prj.sno ) as totalPrdPrice";
            $tableInfo['prd']['field'][] = "( select sum(prdCost * prdExQty) as price from sl_imsProjectProduct where delFl='n' and projectSno = prj.sno ) as totalPrdCost";
            $tableInfo['prd']['field'][] = "( select sum(prdExQty) as price from sl_imsProjectProduct where delFl='n' and projectSno = prj.sno ) as totalQty";

            $tableInfo['prd']['field'][] = "( select sum(targetPrice * prdExQty) as price from sl_imsProjectProduct where delFl='n' and projectSno = prj.sno ) as totalTargetPrice";
            $tableInfo['prd']['field'][] = "( select sum(targetPrdCost * prdExQty) as price from sl_imsProjectProduct where delFl='n' and projectSno = prj.sno ) as totalTargetCost";
        }

        return DBUtil2::setTableInfo($tableInfo, false);
    }


    /**
     * 스케쥴 조인
     * @return array
     */
    public function getScheduleField(){
        $scheduleField = [
            'plan',
            'proposal',
            'custInform',
            'sampleOrder',
            'sampleOut',
            'custSampleInform',
            'custOrder',
            'order',
            'custSpec',
        ];
        $addFieldList = [];

        foreach($scheduleField as $fieldName){
            $addFieldList[$fieldName] = ImsCodeMap::PROJECT_ADD_INFO[$fieldName];
        }

        foreach($addFieldList as $key => $infoValue){
            //빈값 설정
            foreach(ImsCodeMap::PROJECT_ADD_INFO_KEY_SIMPLE as $addInfoKey){
                $ucFirstAddInfoKey = ucfirst($addInfoKey);
                $addedField[] = "MAX(CASE WHEN added.fieldDiv = '{$key}' THEN added.{$addInfoKey} ELSE NULL END) AS {$key}{$ucFirstAddInfoKey}";
            }
            //코멘트 수
            $addedField[] = "MAX(CASE WHEN added.fieldDiv = '{$key}' THEN added.commentCnt ELSE NULL END) AS {$key}CommentCnt";
            //지연-오늘보다 앞의 날짜.
            $addedField[] = "MAX(CASE WHEN added.fieldDiv = '{$key}' THEN  ( ( '' = added.alterText or alterText is null )  and  '0000-00-00' = added.completeDt and  added.expectedDt != '0000-00-00' and CURDATE() > added.expectedDt ) ELSE NULL END) AS {$key}Delay";
        }
        return $addedField;
    }


    /**
     * 프로젝트 상세 정보 조인시 참고
     * @throws \Exception
     */
    public function oldSource(){

        $addFieldList = [
            'custInform' => ImsCodeMap::PROJECT_ADD_INFO['custInform'],  //제안 DL
            'custSampleInform' => ImsCodeMap::PROJECT_ADD_INFO['custSampleInform'],  //샘플 DL
            'order' => ImsCodeMap::PROJECT_ADD_INFO['order'],  //작지/사양서 DL
        ];
        foreach($addFieldList as $key => $infoValue){
            //빈값 설정
            foreach(ImsCodeMap::PROJECT_ADD_INFO_KEY as $addInfoKey){
                $ucFirstAddInfoKey = ucfirst($addInfoKey);
                $addedField[] = "MAX(CASE WHEN added.fieldDiv = '{$key}' THEN added.{$addInfoKey} ELSE NULL END) AS {$key}{$ucFirstAddInfoKey}";
            }
            $addedField[] = "MAX(CASE WHEN added.fieldDiv = '{$key}' THEN added.commentCnt ELSE NULL END) AS {$key}CommentCnt";
        }

        $projectGroupField = array_flip(DBTableField::getTableKey(ImsDBName::PROJECT));
        unset($projectGroupField['customerSize']);
        unset($projectGroupField['planMemo']);
        unset($projectGroupField['proposalMemo']);
        unset($projectGroupField['addedInfo']);
        $projectGroupField = array_flip($projectGroupField);
        foreach($projectGroupField as $key => $each){
            $each = 'prj.' . $each;
            $projectGroupField[$key] = $each;
        }
        $groupField = $projectGroupField;
        //$groupField[] = 'prd.productName';
        $groupField[] = 'cust.customerName';
        $groupField[] = 'b.managerNm';
        $groupField[] = 'sales.managerNm';
        $groupField[] = 'desg.managerNm';
        $groupField[] = 'ext.salesStatus';
        $groupField[] = 'ext.salesDeliveryDt';
        $groupField[] = 'ext.salesStyleName';
        $groupField[] = 'ext.extAmount';
        $groupField[] = 'ext.extMargin';
        $groupField[] = 'ext.designWorkType';
        $groupField[] = 'ext.salesExDt';
        $searchVo->setGroup(implode(',',$groupField));

        /* 프로젝트 스케쥴

        'prj' => //메인
                [
                    'data' => [ ImsDBName::PROJECT ]
                    , 'field' => [implode(',',$projectGroupField), 'prj.sno as projectSno']
                ]

        , 'added' =>
                [
                    'data' => [ ImsDBName::PROJECT_ADD_INFO, 'LEFT OUTER JOIN', 'prj.sno = added.projectSno' ]
                    , 'field' => $addedField
                ]
        ,
        */

    }

}



