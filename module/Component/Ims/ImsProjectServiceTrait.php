<?php
namespace Component\Ims;

use App;
use Component\Database\DBIms;
use Component\Database\DBTableField;
use Component\Ims\EnumType\PREPARED_TYPE;
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
use SlComponent\Util\SlSmsUtil;

/**
 * IMS 프로젝트 서비스
 * Class GoodsStock
 * @package Component\Goods
 */
trait ImsProjectServiceTrait {

    public function getProjectField(){
        $projectGroupField = array_flip(DBTableField::getTableKey(ImsDBName::PROJECT));
        unset($projectGroupField['customerSize']);
        unset($projectGroupField['planMemo']);
        unset($projectGroupField['proposalMemo']);

        $projectGroupField = array_flip($projectGroupField);

        foreach($projectGroupField as $key => $each){
            $each = 'prj.' . $each;
            $projectGroupField[$key] = $each;
        }
        return $projectGroupField;
    }

    /**
     * 프로젝트 리스트 AJAX 버전 쿼리
     * @return array
     */
    public function getListProjectWithAddInfoTable(){
        $addedField = [];

        foreach(ImsCodeMap::PROJECT_ADD_INFO as $key => $infoValue){
            //빈값 설정
            foreach(ImsCodeMap::PROJECT_ADD_INFO_KEY as $addInfoKey){
                $ucFirstAddInfoKey = ucfirst($addInfoKey);
                $addedField[] = "MAX(CASE WHEN added.fieldDiv = '{$key}' THEN added.{$addInfoKey} ELSE NULL END) AS {$key}{$ucFirstAddInfoKey}";
            }
            $addedField[] = "MAX(CASE WHEN added.fieldDiv = '{$key}' THEN added.commentCnt ELSE NULL END) AS {$key}CommentCnt";
        }

        $tableInfo = [
            //프로젝트
            'prj' => ['data' => [ ImsDBName::PROJECT ]
                , 'field' => [ implode(',',$this->getProjectField()), 'prj.sno as projectSno, 0 as sampleTotalCount, \'\' as negoText']
            ],
            //등록자
            'reg' => ['data' => [ DB_MANAGER, 'LEFT OUTER JOIN', 'prj.regManagerSno = reg.sno' ]
                , 'field' => ['reg.managerNm as regManagerNm']],
            //영업 담당자
            'sales' => ['data' => [ DB_MANAGER, 'LEFT OUTER JOIN', 'prj.salesManagerSno = sales.sno' ]
                , 'field' => ['sales.managerNm as salesManagerNm']
            ],
            //디자인 담당자
            'desg' =>['data' => [ DB_MANAGER, 'LEFT OUTER JOIN', 'prj.designManagerSno = desg.sno' ]
                , 'field' => ['desg.managerNm as designManagerNm']
            ],
            //고객정보
            'cust' =>['data' => [ ImsDBName::CUSTOMER, 'JOIN', 'prj.customerSno = cust.sno' ]
                , 'field' => ['cust.customerName']
            ],

        ];

        $params =  \Request::post()->toArray();

        if( empty($params['condition']['listStatus']) || 90 == $params['condition']['listStatus'] ){
            //추가정보
            $tableInfo['prd'] = ['data' => [ ImsDBName::PRODUCT, 'LEFT OUTER JOIN', "prj.sno = prd.projectSno and prd.delFl = 'n' "]
                , 'field' => [
                    "( select  subPrd.productName from sl_imsProjectProduct subPrd where  subPrd.delFl='n' and subPrd.projectSno = prj.sno order by subPrd.sort, subPrd.regDt limit 1 ) as styleName",
                    'count( distinct prd.sno ) as prdCnt',
                    'sum(  prd.prdExQty * if(prd.salePrice > 0,prd.salePrice,prd.targetPrice) )  as customerSize',
                    "if(count(1) = count(case when prd.salePrice > 0 then 1 else null end),'confirmed','expected') as customerSizeType",
                    'sum(prd.prdExQty)  as totalQty',
                    'sum(  prd.prdExQty * prd.salePrice ) as prdPrice',
                    'sum(  prd.prdExQty * prd.prdCost ) as prdCost',
                    ' 100 - ( (sum(prd.prdExQty * prd.prdCost)) / (sum(prd.prdExQty * prd.salePrice) ) * 100 )   as prdMargin',
                ]];
            $tableInfo['added'] = ['data' => [ ImsDBName::PROJECT_ADD_INFO, 'LEFT OUTER JOIN', "added.projectSno = -1"], 'field' => $addedField];
        }else{
            $tableInfo['prd'] = ['data' => [ ImsDBName::PRODUCT, 'LEFT OUTER JOIN', "prj.sno = prd.projectSno and prd.delFl = 'n' "]
                , 'field' => [
                    "( select  subPrd.productName from sl_imsProjectProduct subPrd where  subPrd.delFl='n' and subPrd.projectSno = prj.sno order by subPrd.sort, subPrd.regDt limit 1 ) as styleName",
                    'count( distinct prd.sno ) as prdCnt',
                    'sum(  prd.prdExQty * if(prd.salePrice > 0,prd.salePrice,prd.targetPrice) ) / COUNT( DISTINCT added.sno) as customerSize',
                    "if(count(1) = count(case when prd.salePrice > 0 then 1 else null end),'confirmed','expected') as customerSizeType",
                    'sum(prd.prdExQty) / COUNT( DISTINCT added.sno) as totalQty',
                    'sum(  prd.prdExQty * prd.salePrice ) / COUNT( DISTINCT added.sno) as prdPrice',
                    'sum(  prd.prdExQty * prd.prdCost ) / COUNT( DISTINCT added.sno) as prdCost',
                    ' 100 - (  (sum(  prd.prdExQty * prd.prdCost ) / COUNT( DISTINCT added.sno)) / (sum(  prd.prdExQty * prd.salePrice ) / COUNT( DISTINCT added.sno)) * 100 )   as prdMargin',
                ]];
            $tableInfo['added'] = ['data' => [ ImsDBName::PROJECT_ADD_INFO, 'LEFT OUTER JOIN', "prj.sno = added.projectSno"], 'field' => $addedField];
        }

        return DBUtil2::setTableInfo($tableInfo,false);
    }


    /**
     * List에서 추가 정보를 가져온다. ( 전체리스트에서는 가져오지 않는다, 성능이슈. )
     * @param $params
     * @return array
     */
    public function getListProjectWithAddInfo($params)
    {
        //(요청, 처리중 , 처리불가 , 반려 ===> 진행중 ) 와 완료 ( limit 50 )
        $searchVo = new SearchVo();
        $groupField = $this->getProjectField();
        $groupField[] = 'reg.managerNm';
        $groupField[] = 'sales.managerNm';
        $groupField[] = 'desg.managerNm';
        $groupField[] = 'cust.customerName';
        $searchVo->setGroup(implode(',',$groupField));
        //$searchVo->setHaving();

        $this->setCommonCondition($params['condition'], $searchVo); //Request쪽에 있음.
        $this->setListSort($params['condition']['sort'], $searchVo);

        $searchData = [
            'page' => gd_isset($params['condition']['page'], 1),
            'pageNum' => gd_isset($params['condition']['pageNum'], 200),
        ];

        $searchVo->setAddTotalField(', sum(prdCnt) as styleCnt');

        $allData = DBUtil2::getComplexListWithPaging($this->getListProjectWithAddInfoTable(), $searchVo, $searchData, false, false);
        $allData['pageData']->styleTotal = $allData['totalData']['styleCnt'];

        //리스트 셋업
        $request = \Request::request()->toArray();
        $initStatus = gd_isset($request['condition']['listStatus'],'');
        $listSetupFnc = 'setList'.$initStatus;
        $methods = SlCommonUtil::getMethodMap(__CLASS__);
        if( !empty($methods[$listSetupFnc]) ){
            $listSetup = $this->$listSetupFnc();
        }else{
            $listSetup = $this->setList();
        }

        return [
            'pageEx' => $allData['pageData']->getPage('#'),
            'page' => $allData['pageData'],
            'list' => SlCommonUtil::setEachData($allData['listData'], $this, 'decorationProjectWithAddInfo', $listSetup['defaultRowspan'])
        ];
    }


    /**
     * 프로젝트 리스트 추가 데이터
     * @param $each
     * @param $key
     * @param $defaultRowspan
     * @return array
     * @throws \Exception
     */
    public function decorationProjectWithAddInfo($each, $key, $defaultRowspan)
    {
        $each = DBTableField::parseJsonField(ImsDBName::PROJECT, $each);
        $searchVo = new SearchVo("delFl='n' and projectSno=?", $each['sno']);
        $searchVo->setOrder('sort, regDt desc');

        $each['workMemoBr'] = gd_htmlspecialchars_stripslashes($each['workMemo']);
        //$each['customerWaitMemoNl2br'] = nl2br($each['customerWaitMemo']);

        if( $each['prdCnt'] > 1 ){
            $each['styleName'] .= ' 외 '.($each['prdCnt']-1).'건';
        }

        $each['customerSizeKr'] = SlCommonUtil::numberToKorean($each['customerSize']);

        $each['addedInfo'] = json_decode($each['addedInfo'], true);
        $each['projectTypeKr'] = ImsCodeMap::PROJECT_TYPE[$each['projectType']];
        $each['projectStatusKr'] = ImsCodeMap::PROJECT_STATUS[$each['projectStatus']];

        $each['styleShow'] = false;
        $each['defaultRowspan'] = $defaultRowspan;
        $each['addRowspan'] = 0;
        $each['styleList'] = [];
        //$each['comment']['qbExpectedDt'] = 1;
        //$each['comment']['proposalExpectedDt'] = 1;

        $each['fabricStatusKr'] = ImsCodeMap::IMS_FABRIC_STATUS[$each['fabricStatus']]['name'];
        $each['btStatusKr'] = ImsCodeMap::IMS_BT_STATUS[$each['btStatus']]['name'];

        //긴급도
        $each['urgency'] = '';
        $sDate = date('Y-m-d'); //Today
        $eDate = gd_date_format('Y-m-d',$each['customerDeliveryDt']);
        $dateDiff = SlCommonUtil::getDateDiff($sDate, $eDate);
        if( 90 > $each['projectStatus'] && 100 > $dateDiff ){
            $each['urgency'] = '긴급';
        }else if( 90 > $each['projectStatus'] &&  120 >= $dateDiff ) {
            $each['urgency'] = '보통';
        }else if( 90 > $each['projectStatus'] &&  $dateDiff > 120 ){
            $each['urgency'] = '여유';
        }

        $meetingDt = SlCommonUtil::getSimpleWeekDay($each['meetingInfoExpectedDt'], true);
        $each['meetingInfo'] = "<div>{$meetingDt}</div><div>{$each['meetingInfoMemo']}</div>";
        $each['designAgree'] = $each['designAgreeMemo'];
        $each['qcAgree'] = $each['qcAgreeMemo'];
        $each['allAgree']  = "<div>{$each['allAgreeExpectedDt']}</div><div>{$each['allAgreeMemo']}</div>";
        $each['allAgree2'] = "<div>{$each['allAgree2ExpectedDt']}</div><div>{$each['allAgree2Memo']}</div>";

        $each['meetingMember'] = "{$each['meetingMemberMemo']}";
        $each['custMeetingInform'] = "{$each['custMeetingInformExpectedDt']}";
        $each['meetingReport'] = "";

        foreach(ImsCodeMap::PROJECT_ADD_INFO as $key => $infoValue){
            //빈값 설정
            foreach(ImsCodeMap::PROJECT_ADD_INFO_KEY as $addInfoKey){
                $fieldKey = $key.ucfirst($addInfoKey);
                //date형일 경우 처리
                if( in_array($addInfoKey, ['expectedDt','completeDt']) ) {
                    if(empty($each[$fieldKey]) || '0000-00-00' == $each[$fieldKey]){
                        $emptyWord = empty($infoValue[substr($addInfoKey,0,1)])?'미입력':''.$infoValue[substr($addInfoKey,0,1)];
                        $each[$fieldKey] = '';
                        $each[$fieldKey.'Short'] = '<span class="text-muted">'.$emptyWord.'</span>'; //여기에 분장 자료...
                        $each[$fieldKey.'Remain'] = '-';
                    }else{
                        $each[$fieldKey.'Short']  = SlCommonUtil::getSimpleWeekDay($each[$fieldKey], true);
                        $each[$fieldKey.'Remain'] = SlCommonUtil::getRemainDt2($each[$fieldKey]);
                    }
                }
            }

            $each[$key.'Delay'] = '';
            if( !empty($each[$key.'ExpectedDt']) && '0000-00-00' !== $each[$key.'ExpectedDt']
                && (empty($each[$key.'CompleteDt']) || '0000-00-00' === $each[$key.'CompleteDt']) ){
                $each[$key.'Delay'] = date('Y-m-d') > $each[$key.'ExpectedDt'];
            }
        }

        //미팅 보고서 파일가져오기
        $each['file']['fileEtc1'] = $this->getLatestFileList(['projectSno'=>$each['sno'],'fileDiv'=>'fileEtc1']);

        //기타 코멘트 가져오기
        $sql = "select commentDiv, count(1) as cnt from sl_imsComment where commentDiv in ('customerWait','meetingReport') and projectSno='{$each['sno']}' group by commentDiv ";
        $commentList = DBUtil2::runSelect($sql);
        $commentMap = [];
        foreach($commentList as $comment){
            $commentMap[$comment['commentDiv']] = number_format($comment['cnt']);
        }
        $each['customerWaitMemoCommentCnt'] = $commentMap['customerWait'];
        $each['meetingReportCommentCnt'] = $commentMap['meetingReport'];

        $each['prdCostKr'] = SlCommonUtil::numberToKorean($each['prdCost']);
        $each['prdPriceKr']= SlCommonUtil::numberToKorean($each['prdPrice']);
        $each['bizPlanYnKr']='y' === $each['bizPlanYn'] ? '포함':'미포함';

        $this->refineProjectIgnoreItem($each);

        return $each;
    }


    /**
     * 추가 정보 없으면 추가한다.
     * @return string
     */
    public function addProjectExtInfo(){
        $sql = "insert into sl_imsProjectExt (
            projectSno, salesStatus, salesExDt, salesDeliveryDt, salesStyleName, salesStyleData, extAmount, extMargin, designWorkType
        ) select 
            a.sno, 'wait', null, null, (select productName from sl_imsProjectProduct where projectSno=a.sno limit 1), null, null, null, 0 
        from sl_imsProject a 
        left join sl_imsProjectExt b on a.sno = b.projectSno
        where b.projectSno is null
        ";
        return DBUtil2::runSql($sql);
    }

}

