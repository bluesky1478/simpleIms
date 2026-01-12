<?php

namespace Component\Ims;

use App;
use Component\Database\DBIms;
use Component\Database\DBTableField;
use Component\Ims\EnumType\PREPARED_TYPE;
use Component\Member\Manager;
use Component\Sms\Code;
use Framework\Debug\Exception\AlertBackException;
use Framework\Utility\DateTimeUtils;
use LogHandler;
use Request;
use Session;
use SiteLabUtil\SlCommonUtil;
use SlComponent\Database\DBUtil2;
use SlComponent\Database\SearchVo;
use SlComponent\Util\SitelabLogger;
use SlComponent\Util\SlLoader;
use SlComponent\Util\SlSmsUtil;

/**
 * IMS 서비스
 * Class GoodsStock
 * @package Component\Goods
 */
trait ImsReorderServiceTrait
{
    /**
     * 리오더시 처리.
     * @param $sno
     * @return void
     */
    public function setProjectReOrder($sno)
    {
        $filePath = './module/Component/Ims/Sql/';
        $reorderFile1 = $filePath.'reorder1.sql';
        $sql1 = SlCommonUtil::getFileData($reorderFile1);
        $sql1 .= $sno;
        DBUtil2::runSql($sql1); //프로젝트 상태 변경

        $reorderFile2 = $filePath.'reorder2.sql';
        $sql2 = SlCommonUtil::getFileData($reorderFile2);
        $sql2 .= $sno;
        DBUtil2::runSql($sql2); //상품 상태 변경

        gd_debug( $sql1);
        gd_debug( $sql2);
    }


    public function setProjectReOrderAuto($sno)
    {
        /*gd_debug( '프로젝트 복사 확인');
        $filePath = './module/Component/Ims/Sql/';
        $reorderFile1 = $filePath.'reorder1.sql';
        $sql1 = SlCommonUtil::getFileData($reorderFile1);
        $sql1 .= $sno;
        DBUtil2::runSql($sql1); //프로젝트 상태 변경

        $reorderFile2 = $filePath.'reorder3.sql';
        $sql2 = SlCommonUtil::getFileData($reorderFile2);
        $sql2 .= $sno;
        DBUtil2::runSql($sql2); //상품 상태 변경

        /*gd_debug( '프로젝트 복사 확인');
        gd_debug( $sql1);
        gd_debug( $sql2);*/
    }


    /**
     * 스케쥴 등록.
     * @param $newProjectSno
     * @param $project
     * @param $fieldDiv
     * @param $expectedDt
     */
    public function saveReOrderSchedule($newProjectSno, $project, $fieldDiv, $expectedDt){
        DBUtil2::insert(ImsDBName::PROJECT_ADD_INFO, [
            'fieldDiv'=>$fieldDiv,
            'customerSno'=>$project['customerSno'],
            'projectSno'=>$newProjectSno,
            'expectedDt'=>$expectedDt,
        ]);
    }

    /**
     * 리오더 (과거이력 등록되지 않게 한다 , 혼선유발)
     * @param $projectSno
     * @param $initStatus
     * @return mixed
     * @throws \Exception
     */
    public function reOrderProject($projectSno, $initStatus, $prdYear, $orderDt, $deliveryDt){
        $projectData = DBUtil2::getOne(ImsDBName::PROJECT, 'sno', $projectSno, false);

        $projectYear = empty($prdYear)?$projectData['projectYear']+1:$prdYear;

        if(empty($projectData)) throw new \Exception('복사할 프로젝트 정보가 없습니다!');

        $unsetList = [
            'sno', 'regDt', 'modDt'
        ];
        $projectData['projectMemo'] .= '  ' . $projectData['projectNo'] . '로 부터 복사함 ' . \Session::get('manager.managerNm') . ' ' . date('y/m/d');
        $projectData['projectNo'] = $this->createProjectNo();
        foreach ($unsetList as $unsetField) {
            unset($projectData[$unsetField]);
        }
        $projectData['projectReady'] = 'n';
        $projectData['srcProjectSno'] = $projectSno;
        $projectData['projectStatus'] = $initStatus;
        $projectData['prdCostApproval'] = 'n';
        $projectData['prdPriceApproval'] = 'n';
        $projectData['projectYear'] = $projectYear; //TODO - 추가 , 수정AS는 +1 하지 않는다.  //CustomerSno 만 ?

        $projectData['customerDeliveryDtConfirmed'] = 'y';
        $projectData['customerDeliveryDtStatus2'] = 'n';
        $projectData['customerDeliveryDtStatus'] = 0;

        $projectData['priceStatus'] = 0;
        $projectData['productionStatus'] = 0;
        $projectData['orderStatus'] = 0;
        $projectData['estimateStatus'] = 0;
        $projectData['costStatus'] = 0;
        $projectData['workStatus'] = 0;
        $projectData['btStatus'] = 0;
        $projectData['customerWaitMemo'] = '시스템 일괄등록 (이전프로젝트:' . $projectData['projectNo'].')';
        $projectData['customerWaitDt'] = '';
        $projectData['bidType'] = '';
        $projectData['bidType2'] = '';

        $resetDateList = ['customerOrderDt','recommendDt','msDeliveryDt','recommendDt','msOrderDt'];
        foreach($resetDateList as $resetDate){
            $projectData[$resetDate] = '';
        }

        //기성복은 기성복 그대로
        if( 4 != $projectData['projectType'] ){
            $projectData['projectType'] = 1; //리오더
        }

        //납기일,발주DL 설정. (발주DL셋팅 삭제)


        if( !empty($projectData['customerDeliveryDt']) && '0000-00-00' != $projectData['customerDeliveryDt'] ){
            $projectData['customerDeliveryDt'] = SlCommonUtil::getDateCalc($projectData['customerDeliveryDt'],'+365','day');
        }

        $newProjectSno = DBUtil2::insert(ImsDBName::PROJECT, $projectData); //새 프로젝트 번호. (복사)

        //프로젝트 파일 처리
        //미팅보고서 최신 파일 가져오기
        //$this->copyReOrderFile(['srcProjectSno'=>$projectSno], $newProjectSno, 'fileEtc1');

        //사양서 최신 파일
        //$this->copyReOrderFile(['srcProjectSno'=>$projectSno], $newProjectSno, 'fileConfirm');

        if(50 == $initStatus){
            //리오더 발주 단계 스케쥴 등록 (고객 납기일 있을 경우)
            //if( !empty($projectData['customerDeliveryDt']) && '0000-00-00' != $projectData['customerDeliveryDt'] ){
                //$this->saveReOrderSchedule($newProjectSno, $projectData, 'cost', SlCommonUtil::getDateCalc(date('Y-m-d'),'+30','day')); //생산가
                //$this->saveReOrderSchedule($newProjectSno, $projectData, 'salePrice', SlCommonUtil::getDateCalc(date('Y-m-d'),'+30','day')); //판매가
                //$this->saveReOrderSchedule($newProjectSno, $projectData, 'custOrder', SlCommonUtil::getDateCalc($projectData['customerDeliveryDt'],'-150','day')); //발주DL
            //}
        }else{
            //미팅정보에 시스템 일괄 등록 정보 메모
            DBUtil2::insert(ImsDBName::PROJECT_ADD_INFO, [
                'fieldDiv' => 'meetingInfo',
                'projectSno' => $newProjectSno,
                'memo' => '시스템 일괄 등록 (이전프로젝트:' . $projectData['projectNo'].')'
            ]);
        }

        //상품 복사
        $prdList = DBUtil2::getList(ImsDBName::PRODUCT, "delFl = 'n' and projectSno", $projectSno, true);
        foreach($prdList as $prd){
            $srcSno = $prd['sno'];
            $prd['projectSno'] = $newProjectSno;
            unset($prd['sno']);
            unset($prd['regDt']);
            unset($prd['modDt']);

            $prd['prdYear'] = date('Y');
            $codeYear = date('y');
            for($i=20;$codeYear-1 > $i;$i++){
                $prd['styleCode'] = str_replace($i,$codeYear,$prd['styleCode']);
            }
            $newPrdSno = DBUtil2::insert(ImsDBName::PRODUCT, $prd); //상품(스타일) 복사

            //$this->copyReOrderEstimate($prd['estimateConfirmSno'], $newProjectSno, $newPrdSno);
            //$this->copyReOrderQb($srcSno, $newProjectSno, $newPrdSno);

            //작지복사 . (최근 것만)
            /*$copyFileList = ['fileWork','fileCareMark','filePrdMark','filePrdEtc'];
            foreach($copyFileList as $copyFile){
                $this->copyReOrderFile([
                    'srcStyleSno' => $srcSno,
                    'newStyleSno' => $newPrdSno,
                ], $newProjectSno, $copyFile);
            }*/

        }
        //생산견적 복사 (선택된 견적서 복사 하면 된다. + 기본 선택된 상태)
        //판매가 복사 (승인만 안된 상태)

        //estimateConfirmSno 해주기!
        $this->setProjectReOrderAuto($newProjectSno);

        $this->setRefinePreparedStatus($newProjectSno); //이게 안되나 ?

        //스케쥴 셋팅 . (고객 발주일로 부터 .... 150 계산)

        return $newProjectSno;
    }

    /**
     * QB정보 복사
     * @param $srcStyleSno
     * @param $newProjectSno
     * @param $newPrdSno
     */
    public function copyReOrderQb($srcStyleSno, $newProjectSno, $newPrdSno){
        //FIXME : 원래 확정된것만 가져와야하지만. 데이터 정제가 부족하여 사용안함 빼고 다 가져온다.
        $fabricDataList = DBUtil2::getList(ImsDBName::FABRIC, "delFl='n' and 5 <> fabricStatus and styleSno", $srcStyleSno);
        foreach($fabricDataList as $fabricData){
            SlCommonUtil::setDefaultCopyObject($fabricData);
            $fabricData['projectSno']=$newProjectSno;
            $fabricData['styleSno']=$newPrdSno;
            $newFabricSno = DBUtil2::insert(ImsDBName::FABRIC, $fabricData);

            //BT결과 파일 복사
            $this->copyReOrderFile([
                'srcStyleSno' => $srcStyleSno,
                'srcEachSno' => $fabricData['sno'],
                'newStyleSno' => $newPrdSno,
                'newEachSno' => $newFabricSno,
            ], $newProjectSno, 'btFile2');
        }
    }

    /**
     * 파일 복사
     * srcStyleSno
     * srcEachSno
     * newStyleSno
     * newEachSno
     *
     * @param $params
     * @param $newProjectSno
     * @param $fileDiv
     * @param $memoType
     */
    public function copyReOrderFile($params, $newProjectSno, $fileDiv, $memoType='param'){
        $searchParams = [];
        $searchParams['fileDiv'] = $fileDiv;

        if(!empty($params['srcProjectSno'])) $searchParams['projectSno'] = $params['srcProjectSno'];
        if(!empty($params['srcStyleSno']))   $searchParams['styleSno'] = $params['srcStyleSno'];
        if(!empty($params['srcEachSno']))    $searchParams['eachSno'] = $params['srcEachSno'];

        $file = $this->getLatestFileList($searchParams);

        if(!empty($file['files'])){
            SlCommonUtil::setDefaultCopyObject($file);
            $file['projectSno'] = $newProjectSno;
            $file['rev'] = 1;
            $file['fileList'] = json_encode($file['files']);
            if( 'param' === $memoType ){
                $file['memo'] = json_encode($params) ;
            }
            if(!empty($params['newStyleSno']))   $file['styleSno'] = $params['newStyleSno'];
            if(!empty($params['newEachSno']))    $file['eachSno']  = $params['newEachSno'];

            DBUtil2::insert(ImsDBName::PROJECT_FILE, $file);
        }
    }

    /**
     * 복사한 리오더 새 스타일에 직전 견적 복사
     * @param $selectedSno
     * @param $newProjectSno
     * @param $newPrdSno
     * @return int|mixed
     */
    public function copyReOrderEstimate($selectedSno, $newProjectSno, $newPrdSno){
        $newEstimateConfirmSno = 0;
        if(!empty($selectedSno)){
            $estimateData = DBUtil2::getOne(ImsDBName::ESTIMATE, 'sno', $selectedSno, false);
            SlCommonUtil::setDefaultCopyObject($estimateData);
            $estimateData['reqCount'] = 1;
            $estimateData['reqStatus'] = 3;
            $estimateData['projectSno'] = $newProjectSno;
            $estimateData['styleSno'] = $newPrdSno;
            $estimateData['reqMemo'] = '리오더 직전 생산견적가';
            $newEstimateConfirmSno = DBUtil2::insert(ImsDBName::ESTIMATE, $estimateData);
        }
        return $newEstimateConfirmSno;
    }

}


