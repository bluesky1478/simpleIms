<?php

namespace Controller\Admin\Sales;

use App;
use Component\Facebook\Facebook;
use Component\Godo\GodoPaycoServerApi;
use Component\Member\MemberSnsService;
use Component\Member\MemberValidation;
use Component\Policy\SnsLoginPolicy;
use Component\SiteLink\SiteLink;
use Component\Storage\Storage;
use Controller\Admin\Sales\ControllerService\SalesListService;
use Controller\Front\Controller;
use Framework\Debug\Exception\AlertBackException;
use Framework\Debug\Exception\AlertRedirectException;
use Framework\Debug\Exception\AlertReloadException;
use Logger;
use Request;
use SiteLabUtil\SlCommonUtil;
use SlComponent\Database\DBUtil;
use SlComponent\Database\DBUtil2;
use SlComponent\Database\SearchVo;
use SlComponent\Recap\RecapService;
use SlComponent\Util\PhpExcelUtil;
use SlComponent\Util\SitelabLogger;
use SlComponent\Util\SlControllerTrait;
use SlComponent\Util\SlLoader;

/**
 * API
 */
class SalesPsController extends Controller {

    use SlControllerTrait;

    public function getMyService(){
        return $this;
    }

    public function saveRecapProduceData($inputParams){
        DBUtil2::runSql("truncate table sl_salesCustomerContents");
        DBUtil2::runSql("truncate table sl_salesCustomerInfo");
        $files = \Request::files()->toArray();
        $params['instance'] = $this;
        $params['fnc'] = 'saveEachProduceData';
        PhpExcelUtil::runExcelReadAndProcess($files, $params, 3);
        return ['data'=>$params,'msg'=>'저장되었습니다.'];
    }

    /**
     * 일괄 등록
     * @param $each
     * @param $key
     * @param $mixData
     * @throws \Exception
     */
    public function saveEachProduceData($each, $key, &$mixData){
        if( 2 > $key ) return false;

        $save = [];
        $saveContents = [];
        foreach( [
                    'targetSource' => 2
                     ,'level' => 3
                     ,'customerType' =>4
                     ,'customerName' =>5
                     ,'industry' =>6
                     ,'employeeCnt'=>7
                     ,'phone'=>8
                     ,'dept'=>9
                     ,'contactName'=>10
                     ,'contactPhone'=>11
                     ,'contactEmail'=>12
                     ,'contactContents'=>13
                     ,'contactDt'=>14
                     ,'memo'=>15
                     ,'buyMethod'=>16
                     ,'buyDiv'=>17
                     ,'buyExt'=>18
                     ,'buyItem'=>19
                     ,'buyCnt'=>20
                 ] as $key => $eachDataIndex ){

            if('contactDt' === $key){
                //날짜
                $save['contactDt'] = date( 'Y-m-d',  strtotime(jdtogregorian($each[$eachDataIndex] + 2415023)));
            }else if('customerType' === $key){
                $save['customerType'] = SlCommonUtil::getFlipData(SalesListService::SALES_STATUS_MAP,$each[$eachDataIndex]);
            }else if( 'contactContents' === $key) {
                $saveContents['contents'] = $each[$eachDataIndex];
            }else{
                $save[$key] = $each[$eachDataIndex];
            }
        }

        $save['regManagerSno'] = \Session::get('manager.sno');

        $sno = DBUtil2::insert('sl_salesCustomerInfo', $save);
        $saveContents['salesSno'] = $sno;
        $saveContents['regManagerSno'] = \Session::get('manager.sno');
        DBUtil2::insert('sl_salesCustomerContents', $saveContents);
    }

    public function saveProduceEachData($inputParams){

        if( empty($inputParams['sno']) ){
            DBUtil2::insert('sl_salesCustomerInfo',$inputParams);
        }else{
            $sno = $inputParams['sno'];
            $inputParams['lastManagerSno'] = \Session::get('manager.sno');
            unset($inputParams['sno']);
            DBUtil2::update('sl_salesCustomerInfo',$inputParams, new SearchVo('sno=?', $sno));
        }
        return ['data'=>$inputParams,'msg'=>'저장되었습니다.'];
    }
    public function saveProduceEachData2($inputParams){
        $sno = $inputParams['sno'];
        $callContents = $inputParams['callContents'];
        if( !empty($callContents) ){
            $contentsParams['regManagerSno'] =  \Session::get('manager.sno');
            $contentsParams['contents'] =  $callContents;
            $contentsParams['salesSno'] =  $sno;
            $inputParams['contactDt'] = 'now()';
            DBUtil2::insert('sl_salesCustomerContents',$contentsParams);
        }

        unset($inputParams['callContents']);
        unset($inputParams['mode']);
        $inputParams['lastManagerSno'] = \Session::get('manager.sno');
        unset($inputParams['sno']);
        DBUtil2::update('sl_salesCustomerInfo',$inputParams, new SearchVo('sno=?', $sno));

        return ['data'=>$inputParams,'msg'=>'저장되었습니다.'];
    }

    public function setBatchStatus($inputParams){
        $snoListStr = $inputParams['snoList'];
        $changeStatus = $inputParams['changeStatus'];
        $snoList = explode(',',$snoListStr);
        $searchVo = new SearchVo();
        $searchVo->setWhere(DBUtil2::bind('sno', DBUtil2::IN, count($snoList) ));
        $searchVo->setWhereValueArray( $snoList );
        DBUtil2::update('sl_salesCustomerInfo', [
            'customerType'=>$changeStatus,
            'lastManagerSno'=>\Session::get('manager.sno'),
        ], $searchVo);
        return ['data'=>$inputParams,'msg'=>'설정되었습니다.'];
    }

    public function deleteItem($params){
        if(!empty($params['sno'])){
            DBUtil2::delete('sl_salesCustomerInfo', new SearchVo('sno=?', $params['sno']) );
        }
        return ['data'=>$params,'msg'=>'처리완료'];
    }

}


