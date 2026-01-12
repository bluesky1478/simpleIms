<?php

namespace Controller\Admin\Ims;

use App;
use Component\Facebook\Facebook;
use Component\Godo\GodoPaycoServerApi;
use Component\Ims\ImsCodeMap;
use Component\Ims\ImsDBName;
use Component\Ims\ImsJsonSchema;
use Component\Member\MemberSnsService;
use Component\Member\MemberValidation;
use Component\Member\Util\MemberUtil;
use Component\Policy\SnsLoginPolicy;
use Component\SiteLink\SiteLink;
use Component\Storage\Storage;
use Controller\Front\Controller;
use Exception;
use Framework\Debug\Exception\AlertBackException;
use Framework\Debug\Exception\AlertRedirectException;
use Framework\Debug\Exception\AlertReloadException;
use Logger;
use Request;
use SiteLabUtil\SlCommonUtil;
use SlComponent\Database\DBUtil2;
use SlComponent\Database\SearchVo;
use SlComponent\Util\SitelabLogger;
use SlComponent\Util\SlControllerTrait;
use SlComponent\Util\SlLoader;

/**
 * API
 */
class ImsPsController extends \Controller\Admin\Controller{

    use SlControllerTrait;

    use ImsPsProductTrait;
    use ImsPsTodoTrait;
    use ImsPsTrait;
    use ImsPsNkTrait;
    use ImsPsScmTrait;
    use Ims25PsTrait;

    use ImsPsListTrait; //ims25 관리자 리스트 Trait.


    private $imsService;
    private $imsStyleService;
    private $imsStoredService;
    private $imsProduceService;
    private $imsCustomerEstimateService;

    public function __construct(){
        parent::__construct();
        $this->imsService = SlLoader::cLoad('ims', 'imsService');
        $this->imsStyleService = SlLoader::cLoad('ims', 'imsStyleService');
        $this->imsStoredService = SlLoader::cLoad('ims', 'imsStoredService');
        $this->imsProduceService = SlLoader::cLoad('ims', 'imsProduceService'); //구 생산관리 서비스
        $this->imsCustomerEstimateService = SlLoader::cLoad('ims', 'imsCustomerEstimateService'); //구 생산관리 서비스
    }

    /**
     * target 데이터 가져오기
     * ==> front에서 해당 메소드 사용하는 경우 인터럽트 당함. (관리자에서만 사용)
     * @param $params
     * @return array
     */
    public function getData($params){
        $fncName = 'get'.ucfirst($params['target']);
        $rslt = $this->imsService->$fncName($params);
        return ['data'=>$rslt,'msg'=>'조회 완료'];
    }

    /**
     * 생산가 확정 강제 초기화
     * @param $params
     * @return array
     * @throws Exception
     */
    public function costReset($params){
        //$prj = DBUtil2::getOne(ImsDBName::PROJECT, 'sno', $params['projectSno']);
        $updateData =[
            'prdCostConfirmSno'=>'0',
            'prdCostConfirmManagerSno'=>'0',
            'prdCostConfirmDt'=>'0000-00-00',
        ];

        ///if(4 != $prj['projectType']){
        $updateData['prdCost']=0; //기성복이 아니라면 0원으로
        //}else{
        $updateData['estimateStatus']=0;
        $updateData['prdCostStatus']=0;
        $updateData['estimateConfirmSno'] = 0;
        $updateData['prdCostConfirmSno'] = 0;
        $updateData['estimateConfirmManagerSno'] = 0;
        $updateData['prdCostConfirmManagerSno'] = 0;
        $updateData['prdCostConfirmDt'] = '0000-00-00';
        $updateData['estimateConfirmDt'] = '0000-00-00';
        //}

        DBUtil2::update(ImsDBName::PRODUCT, $updateData, new SearchVo('projectSno=?',$params['projectSno']));

        $searchVo = new SearchVo("projectSno=?", $params['projectSno']);
        $searchVo->setWhere('reqStatus=?');
        $searchVo->setWhereValue(5);
        $searchVo->setWhere('estimateType=?');
        $searchVo->setWhereValue('cost');

        //확정된 것들은 => 완료 상태로 변경
        DBUtil2::update(ImsDBName::ESTIMATE, ['reqStatus'=>'3'], $searchVo);

        DBUtil2::update(ImsDBName::PROJECT, ['prdCostApproval'=>'n'], new SearchVo("sno=?", $params['projectSno']));

        //결재이력 초기화
        DBUtil2::update(ImsDBName::TODO_REQUEST,['delFl'=>'y'],new SearchVo( " approvalType='cost' and delFl='n' and projectSno=?", $params['projectSno'] ));

        //상태변경.
        $imsService = SlLoader::cLoad('ims', 'imsService');
        $imsService->setSyncStatus($params['projectSno']);

        return ['data'=>$params,'msg'=>'조회 완료'];
    }

    /**
     *
     * @param $params
     * @return array
     * @throws Exception
     */
    public function saveSyncProductionCnt($params){
        $updateData['sizeOptionQty'] = '{"\ubcc4\ucca8":"'. $params['assortCnt'] .'"}';
        $updateData['totalQty'] = $params['assortCnt'];
        $productionData = DBUtil2::getOneSortData(ImsDBName::PRODUCTION, 'styleSno=?', $params['prdSno'], 'regDt desc');
        if( !empty($productionData) ){
            DBUtil2::update(ImsDBName::PRODUCTION, $updateData, new SearchVo('sno=?',$productionData['sno']));
        }
        return ['data'=>$params,'msg'=>'처리 완료'];
    }

}
