<?php
namespace Component\Ims;

use App;
use Component\Database\DBTableField;
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
use SlComponent\Util\SlSmsUtil;

/**
 * IMS 상품관련 서비스
 * Class GoodsStock
 * @package Component\Goods
 */
trait ImsServiceFabricTrait {

    /**
     * QB관리 기본 정보
     * @param $src
     * @return array
     */
    public function getFabricViewDefaultData($src){
        $result = DBTableField::getTableKeyAndBlankValue('sl_imsFabric');
        $result['fileList'] = $this->setFabricFileDefault([
            'customerSno' => $src['product']['customerSno'],
            'projectSno' => $src['product']['projectSno'],
            'styleSno' => $src['product']['sno'],
            'eachSno' => -1,
        ])['fileList'];

        //$result['fabricStatus'] = '0';
        //$result['Status'] = '0';
        //$this->setFabricTestValue($result);

        return $result;
    }

    //테스트 데이터 셋팅
    public function setFabricTestValue(&$result){
        $result['position'] = 'G1';
        $result['fabricName'] = '메인원단';
        $result['fabricMix'] = 'P70 / E30';
        $result['color'] = '#RED F00';
        $result['spec'] = 'NS100';
        $result['meas'] = '1.5';
        $result['unitPrice'] = '5000';
        $result['fabricMemo'] = 'Memo.';
        $result['btConfirmInfo'] = 'BT확정정보.';
        $result['btMemo'] = 'BT메모.';
        $result['makeNational'] = '';
    }


    /**
     * 원단 파일의 구조를 셋팅.
     * @param $each
     * @return mixed
     */
    public function setFabricFileDefault($each){
        $checkFileList = [
            'btFile1', // BT 의뢰서
            'btFile2', // BT 결과
            'bulkFile', // BULK 결과
        ];
        return $this->setDefaultFile($checkFileList, $each);
    }

    /**
     * 원단 저장
     * @param $params
     * @return mixed
     * @throws \Exception
     */
    public function saveFabric($params){
        //unset($params['resDeliveryInfo']);
        //unset($params['resMemo']);
        DBTableField::checkRequired(ImsDBName::FABRIC, $params); //필수값 체크.

        //상태 연동
        //$fabric = DBUtil2::getOne(ImsDBName::FABRIC,'sno',$sno);
        if( $params['reqStatus'] > 0 && 2 == $params['btStatus'] ){  //요청이 있었을 때 연동
            $params['reqStatus']=5;
        }//확정

        if( $params['reqStatus'] > 0 && 4 == $params['btStatus'] ){  //요청이 있었을 때 연동
            $params['reqStatus']=6;
        }//반려

        //저장시
        $sno = $this->save(ImsDBName::FABRIC, $params);

        //화면 갱신용 리스트
        $list = $this->getFabricList([
            'styleSno' => $params['styleSno']
        ]);

        //상품의 원단 진행상태 변경
        $fabricListMap = $this->setSyncProductFabricStatus($params['styleSno']);

        //상품의 BT 진행상태 변경
        $this->setSyncProductBtStatus($params['styleSno'], $fabricListMap);

        $this->setSyncStatus($fabricListMap[$sno]['projectSno'],__METHOD__);

        return [
            'sno' => $sno,
            'list' => $list,
        ];
    }

    public function setSyncProductFabricStatus($styleSno){
        $fabricList = DBUtil2::getList(ImsDBName::FABRIC, 'styleSno', $styleSno);
        $national = [];

        //* IMS_FABRIC_STATUS : 0. 미확보 , 1. 확보중, 2. 확보완료, 3. 리오더, 4. 반려 ?
        //0 = 0
        //1,4 = 1 (1이나 4가 있으면 ===> 1 )
        //2,3 = 2 (모두가 2,3 이면  ===> 2 )
        $process = false;
        $allComplete = true;
        foreach($fabricList as $fabric){
            if( 1 == $fabric['fabricStatus'] || 4 == $fabric['fabricStatus'] ){
                $process |= true;
            }
            if( 2 == $fabric['fabricStatus'] || 3 == $fabric['fabricStatus'] ){
                $allComplete &= true;
                $process |= true;
            }else{
                $allComplete &= false;
            }
            $national[] = array_flip(ImsCodeMap::FABRIC_BUY_TYPE)[$fabric['makeNational']];
        }
        $status = 0;
        if($allComplete){
            $status = 2;
        }
        if(!$allComplete && $process){
            $status = 1;
        }

        $statusUpdateData = [
            'fabricStatus' => $status,
            'fabricNational' => SlCommonUtil::sumUnique($national),
        ];

        //SitelabLogger::logger2(__METHOD__, '원단 업데이트 체크');
        //SitelabLogger::logger2(__METHOD__, $statusUpdateData);

        DBUtil2::update(ImsDBName::PRODUCT,$statusUpdateData,new SearchVo('sno=?', $styleSno));

        return SlCommonUtil::arrayAppKey($fabricList, 'sno');
    }

    /**
     * QB 요청
     * @param $params
     * @return array
     * @throws \Exception
     */
    public function saveFabricReq($params){

        //SitelabLogger::logger2(__METHOD__, 'QB요청!');
        //SitelabLogger::logger2(__METHOD__, $params);

        if( empty($params['reqFactory']) ) throw new \Exception('의뢰처는 필수입니다.');
        if( empty($params['reqDeliveryInfo']) ) throw new \Exception('발송 정보는 필수입니다.');
        //if( empty($params['completeDeadLineDt']) || '0000-00-00' == $params['completeDeadLineDt']  ) throw new \Exception('완료 D/L은 필수입니다.');
        if( 0 >= count($params['reqType']) ) throw new \Exception('요청 타입을 선택해주세요.');

        $sendSms = false;

        foreach( $params['snoList'] as $sno ){
            //fabricStatus          ( 0:미확보, 1:확보중, 2:확보완료, 3:리오더, 4:반려, 5:사용안함),
            //btStatus , bulkStatus ( 0:미확정, 1:진행중, 2:확정,    3:리오더, 4:반려  )
            //요청은 한거니깐 미확보나 미확정이면 진행 중으로 변경한다.
            foreach( $params['reqType'] as $reqTypeValue ){
                switch ($reqTypeValue){
                    case 1 :
                        DBUtil2::update(ImsDBName::FABRIC, ['fabricStatus'=>1], new SearchVo('fabricStatus = 0 and sno=?', $sno));
                        break;
                    case 2 :
                        DBUtil2::update(ImsDBName::FABRIC, ['btStatus'=>1], new SearchVo('btStatus = 0 and sno=?', $sno));
                        break;
                    case 4 :
                        DBUtil2::update(ImsDBName::FABRIC, ['bulkStatus'=>1], new SearchVo('bulkStatus = 0 and sno=?', $sno));
                        break;
                }
            }

            $fabricReqCount = DBUtil2::getCount(ImsDBName::FABRIC_REQ, new SearchVo("reqFactory={$params['reqFactory']} AND fabricSno=?", $sno));

            //기존에는 요청해도 다시 요청 안되었지만 . 이제 요청한 내용은 다 저장된다.
            $saveData = $params;
            $saveData['reqManagerSno'] = SlCommonUtil::getManagerSno();
            $saveData['reqCount'] = $fabricReqCount+1;
            $saveData['resMemo'] = ''; //초기화
            $saveData['resDeliveryInfo'] = ''; //초기화
            $saveData['completeDt'] = '0000-00-00';
            $saveData['reqType'] = array_sum($params['reqType']);
            $saveData['reqStatus'] = 1;//요청상태
            $saveData['fabricSno'] = $sno;
            $saveData['fabricReqFile'] = json_encode($params['fabricReqFile']);

            unset($saveData['snoList']);
            unset($saveData['mode']);
            //요청 이력 저장.
            //SitelabLogger::logger2(__METHOD__, $saveData);
            DBUtil2::insert(ImsDBName::FABRIC_REQ, $saveData);

            $sendSms = true;
        }

        //알림톡 발송
        if($sendSms){
            $styleInfo = DBUtil2::getOne(ImsDBName::PRODUCT, 'sno', $params['styleSno']);
            foreach( ImsCodeMap::FACTORY_SMS[$params['reqFactory']] as $phone ){
                SlSmsUtil::sendSmsSimple("[이노버]{$styleInfo['prdYear']}{$styleInfo['prdSeason']} {$styleInfo['productName']} QB요청합니다. ", $phone);
            }
        }

        //화면 갱신용 리스트
        $list = $this->getFabricList([
            'styleSno' => $params['styleSno']
        ]);

        $this->setSyncStatus($params['projectSno'], __METHOD__);

        return [
            'sno' => $sno,
            'list' => $list,
        ];
    }

    /**
     * QB요청 정보 업데이트
     * @param $params
     * @return mixed
     */
    public function updateFabricReq($params){
        //SitelabLogger::logger2(__METHOD__, $params);
        return $this->save(ImsDBName::FABRIC_REQ, $params);
    }

    /**
     * 관리 원단 리스트 조회
     * @param $params
     * @return mixed
     * @throws \Exception
     */
    public function getFabricList($params){
        $searchVo = new SearchVo();
        $searchVo->setOrder('a.sno desc');
        $isContinue=false;

        if(!empty($params['ignoreStatus'])){
            $searchVo->setWhere('a.fabricStatus <> ?');
            $searchVo->setWhereValue($params['ignoreStatus']);
        }
        if(!empty($params['projectSno'])){
            $searchVo->setWhere('prj.sno = ?');
            $searchVo->setWhereValue($params['projectSno']);
            $isContinue=true;
        }
        if(!empty($params['styleSno'])){
            $searchVo->setWhere("a.styleSno=?");
            $searchVo->setWhereValue($params['styleSno']);
            $isContinue=true;
        }

        if( false === $isContinue ) throw new \Exception('원단 리스트 불러오기 실패(개발팀 문의)');
        
        $list = DBUtil2::getComplexList($this->sql->getFabricListTable(), $searchVo);
        $list = $this->decorationFabricAndAddQbList($list);

        //List에서 파일 정보 추가.
        foreach($list as $key => $each){
            $list[$key] = $this->setFabricFileDefault($each);
        }

        return $list;
    }

    /**
     * 꾸미면서 QB리스트도 함께 전달.
     * @param $list
     * @return mixed
     */
    public function decorationFabricAndAddQbList($list){
        $list = SlCommonUtil::setEachData($list, $this, 'decorationFabric');
        $searchParams['condition']['sort'] = 'D,desc';
        foreach($list as $key => $each){
            $searchParams['condition']['fabricSno'] = $each['sno'];

            //요청 리스트
            $fabricRequestList = $this->getListQb($searchParams); 
            $each['fabricRequest'] = $fabricRequestList; //전체 리퀘스트
            $each['latestFabricRequest'] = []; //최근 Fabric 리퀘스트
            $each['latestBtRequest'] = []; //최근 BT 리퀘스트

            foreach($fabricRequestList['list'] as $fabricRequest){
                if( 0 >= count($each['latestFabricRequest']) && ($fabricRequest['reqType'] & 1) ){ //최근 퀄리티 요청
                    $each['latestFabricRequest'] = SlCommonUtil::setDateBlank($fabricRequest); //전체 리퀘스트
                }
                if( 0 >= count($each['latestBtRequest']) && ($fabricRequest['reqType'] & 2) ){ //최근 BT 요청
                    $each['latestBtRequest'] = SlCommonUtil::setDateBlank($fabricRequest);//전체 리퀘스트
                }
            }

            $list[$key] = $each;
        }
        return $list;
    }

    /**
     * 원단 관리 정보 꾸미기
     * @param $each
     * @param $key
     * @param $mixData
     * @return mixed
     */
    public function decorationFabric($each, $key, $mixData){
        $each['bulkStatusKr'] = ImsCodeMap::IMS_BT_STATUS[$each['bulkStatus']]['name'];
        $each['btStatusKr'] = ImsCodeMap::IMS_BT_STATUS[$each['btStatus']]['name'];
        $each['fabricStatusKr'] = ImsCodeMap::IMS_FABRIC_STATUS[$each['fabricStatus']]['name'];
        $each['reqStatusKr'] = ImsCodeMap::IMS_BT_REQ_STATUS[$each['reqStatus']];
        $each['styleFullName'] = implode(' ',[substr($each['prdYear'],2,2),$each['prdSeason'],$each['productName']]);
        $each['projectTypeEn'] = ImsCodeMap::PROJECT_TYPE_EN[$each['projectType']];
        //$this->setProjectIcon($each, $each['projectSno']);
        return $each;
    }


    /**
     * 요청사항 처리
     * @param $params
     * @return array
     */
    public function saveQbRequest($params){
        foreach($snoList as $sno){
            $saveData = $params;
            $saveData['sno'] = $sno;
            $this->save(ImsDBName::FABRIC, $params);
        }
        //$list = $this->getRequest($params);
        return ['data'=> $list,'msg'=>'저장 완료'];
    }

    /**
     * 반려 처리
     * @param $params
     * @return array
     */
    public function setRejectQb($params){
        $sno = $params['sno'];
        //$reqData =  DBUtil2::getOne(ImsDBName::FABRIC, 'sno', $sno);
        //if($reqData['reqStatus'] >= 3){ //처리완료 이상
        $saveData['sno'] = $sno;
        $saveData['btStatus'] = 4; //반려
        $saveData['reqStatus'] = 6; //반려
        //$saveData['completeDt'] = 'now()';
        $saveData['completeDeadLineDt'] = '0000-00-00';
        $this->save(ImsDBName::FABRIC, $saveData);
        return ['data'=> $sno,'msg'=>'저장 완료'];
    }

    /**
     * 가견적, 확정견적에서 QB등록하기.
     * @param $params
     * @throws \Exception
     */
    public function addQb($params){
        $styleData = DBUtil2::getOne(ImsDBName::PRODUCT, 'sno',$params['styleSno']);
        $saveData = SlCommonUtil::getAvailData($styleData,[
            'customerSno','projectSno'
        ]);
        $saveData['styleSno']=$params['styleSno'];
        $saveData = array_merge($saveData, $params['fabric']);
        $saveData['position'] = $saveData['no'];
        $saveData['fabricMemo'] = $saveData['memo'];
        unset($saveData['no']);
        unset($saveData['memo']);
        $this->saveFabric($saveData);
    }

    /**
     * QB삭제하기
     * @param $params
     * @throws \Exception
     */
    public function deleteQb($params){
        /*$fabricInfo = DBUtil2::getOne(ImsDBName::FABRIC, 'sno' ,$params['sno']);
        if( 0 == $fabricInfo['reqStatus'] || 4 == $fabricInfo['reqStatus']){
            $this->delete(ImsDBName::FABRIC, $params['sno']);
        }else{
            throw new \Exception('생산처에 의뢰중 혹은 확정된 원단은 삭제할 수 없습니다.');
        }*/

        $this->delete(ImsDBName::FABRIC, $params['sno']);

    }


    /**
     * BT상태 (fabric 상태에 종속된 작업)
     * @param $styleSno
     * @param $fabricList
     * @return array
     * @throws \Exception
     */
    public function setSyncProductBtStatus($styleSno, $fabricList){
        $process = false;
        $allComplete = true;
        foreach($fabricList as $bt){
            if( 1 == $bt['btStatus'] || 4 == $bt['btStatus'] ){
                $process |= true;
            }
            if( 2 == $bt['btStatus'] || 3 == $bt['btStatus'] ){
                $allComplete &= true;
                $process |= true;
            }else{
                $allComplete &= false;
            }
        }
        $status = 0;
        if($allComplete){
            $status = 2;
        }
        if(!$allComplete && $process){
            $status = 1;
        }

        $statusUpdateData = [
            'btStatus' => $status,
        ];
        DBUtil2::update(ImsDBName::PRODUCT,$statusUpdateData,new SearchVo('sno=?', $styleSno));
        return $fabricList;
    }





}