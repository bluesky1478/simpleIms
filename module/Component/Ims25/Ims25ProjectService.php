<?php

namespace Component\Ims25;

use App;
use Component\Ims\ImsCodeMap;
use Component\Ims\ImsDBName;
use Component\Ims\ImsServiceConditionTrait;
use Component\Ims\ImsServiceSortTrait;
use Component\Ims\ImsServiceTrait;
use Component\Imsv2\ImsProjectService;
use Component\Imsv2\ImsScheduleUtil;
use Component\Member\Manager;
use Framework\Debug\Exception\AlertBackException;
use Framework\Utility\DateTimeUtils;
use LogHandler;
use Request;
use Session;
use SiteLabUtil\ImsUtil;
use SiteLabUtil\SlCommonUtil;
use SlComponent\Database\DBUtil2;
use SlComponent\Database\SearchVo;
use SlComponent\Util\SitelabLogger;

/**
 * IMS25Ver 프로젝트 리스트 서비스
 * Class GoodsStock
 * @package Component\Goods
 */
class Ims25ProjectService
{
    use ImsServiceTrait;

    /**
     * 25 고객 저장
     * @param $params
     * @return array
     * @throws \Exception
     */
    public function saveIms25Customer($params){
        return $this->imsSave('customer', $params['customer']);
    }

    /**
     * 25 프로젝트 저장 (참여자/스케쥴 정보 함께 갱신)
     * @param $params
     * @return array
     * @throws \Exception
     */
    public function saveIms25Project($params){
        $projectData = $params['project'];
        unset($projectData['projectStatus']); //상태(단계)는 저장하지 않는다.
        //프로젝트 저장
        $projectSno = $this->imsSave('project', $projectData);
        $projectExt = DBUtil2::getOne(ImsDBName::PROJECT_EXT, 'projectSno', $projectSno); //확장 정보 가져오기
        $projectExtData = $projectData;
        $projectExtData['sno'] = $projectExt['sno'];
        $this->imsSave('projectExt', $projectExtData); //확장 정보 저장

        //스케쥴 추가 참여자 정보 갱신 ( prefixSchedule + AddManager 가 있다면 저장 , 삭제 대상도 정리 )
        ImsScheduleUtil::setAddManager($projectSno, $params);
        //스케쥴 상태 갱신 ( 완료일 있으면 기본 완료 처리 , 특정 스케쥴 별도 처리 시 setCheck + suffix )
        ImsScheduleUtil::setProjectScheduleStatus($projectSno);

        return $projectSno;
    }

    /**
     * 25 상품(스타일) 정보 저장
     * @param $params
     * @return array
     * @throws \Exception
     */
    public function saveIms25Product($params){
        $rsltStyle = [];
        $styleList = $params['productList'];
        $projectSno = $params['projectSno'];
        $saveSnoList = [];
        $sort=1;
        foreach($styleList as $each){
            $each['sort']=$sort;
            unset($each['fabric']);
            unset($each['subFabric']);
            $sno = $this->imsSave('projectProduct', $each, true);
            $saveSnoList[] = $sno;
            $sort++;
            //생산 정보에 연동 처리
            $styleInfo = DBUtil2::getOne(ImsDBName::PRODUCT, 'sno', $sno); //현재 스타일 모든 정보 불러오기 ( 저장되는 값을 이용하지 않는다 )
            $rsltStyle[] = $styleInfo;

            $productionInfo = DBUtil2::getOneSortData(ImsDBName::PRODUCTION, 'styleSno=?', $sno, 'regDt desc');
            if(!empty($productionInfo)){
                //스타일의 납기일 수정 => 생산정보 수정
                //생산정보 납기일 수정 => 스타일정보 수정 (TODO: 확인 필요)
                DBUtil2::update(ImsDBName::PRODUCTION,[
                    'msDeliveryDt' => $styleInfo['msDeliveryDt'],
                    'customerDeliveryDt' => $styleInfo['customerDeliveryDt'],
                ], new SearchVo('sno=?', $productionInfo['sno']));
            }
        }
        return $rsltStyle;
    }

    /**
     * 프로젝트 저장 후처리
     * @param $projectSno
     * @return array
     * @throws \Exception
     */
    public function saveProjectAfterProc($params){
        $projectSno = $params['projectSno'];
        $updateRslt = [];
        $project = DBUtil2::getOne(ImsDBName::PROJECT, 'sno', $projectSno); //프로젝트 불러오기
        $prdList = DBUtil2::getList(ImsDBName::PRODUCT,"delFl='n' and projectSno",$projectSno); //상품 불러오기

        //연계 정보 업데이트 (고객 - 마지막 프로젝트 변경건으로)
        $updateRslt['managerUpdate'] = DBUtil2::update(ImsDBName::CUSTOMER, [
            'salesManagerSno' => $project['salesManagerSno'],
            'designManagerSno' => $project['designManagerSno'],
        ], new SearchVo('sno=?', $project['customerSno']));

        //상품별 연동 업데이트 처리.
        foreach($prdList as $prd){
            $updateData = [];
            //납기 연동인경우
            if('y' === $project['syncProduct']){
                $updateData['customerDeliveryDt'] = $project['customerDeliveryDt'];
                $updateData['msDeliveryDt'] = $project['msDeliveryDt'];
            }
            //생산처 연동 (값이 없을때만)
            if(!empty($project['produceCompanySno']) && empty($prd['produceCompanySno']) ){
                $updateData['produceCompanySno'] = $project['produceCompanySno'];
            }
            //생산국가 연동 (값이 없을때만)
            if(!empty($project['produceType']) && empty($prd['produceType']) ){
                $updateData['produceType'] = $project['produceType'];
            }
            //생산국가 연동 (값이 없을때만)
            if(!empty($project['produceNational']) && empty($prd['produceNational']) ){
                $updateData['produceNational'] = $project['produceNational'];
            }
            if(!empty($updateData) && count($updateData)>0){
                $updateData['sno']=$prd['sno'];
                $updateRslt['productUpdate'][] = $this->imsSave('projectProduct',$updateData);
            }
        }

        return $updateRslt;
    }


}


