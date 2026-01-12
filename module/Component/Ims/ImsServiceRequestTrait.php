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
use Component\Page\Page;
use SlComponent\Util\SitelabLogger;

/**
 * IMS 요청 관련 서비스
 * Class GoodsStock
 * @package Component\Goods
 */
trait ImsServiceRequestTrait {
    /**
     * 퀄리티 BT 검색
     * @param $params
     * @return mixed
     */
    public function getListQb($params){

        //(요청, 처리중 , 처리불가 , 반려 ===> 진행중 ) 와 완료 ( limit 50 )
        $searchVo = new SearchVo('a.reqStatus <> 0');
        $this->setCommonCondition($params['condition'], $searchVo);
        $this->setListSort($params['condition']['sort'], $searchVo);
        //$qbList = DBUtil2::getComplexList($this->sql->getFabricListTable(), $searchVo);

        $searchData = [
            'page' => gd_isset($params['condition']['page'],1),
            'pageNum' => gd_isset($params['condition']['pageNum'],100),
        ];
        $qbAllData = DBUtil2::getComplexListWithPaging($this->sql->getFabricReqListTable(), $searchVo, $searchData);
        $qbList = $qbAllData['listData'];
        //SitelabLogger::logger2(__METHOD__, $qbAllData);
        //$qbList = SlCommonUtil::setEachData($qbList, $this, 'decorationFabric');

        //List에서 파일 정보 추가
        foreach($qbList as $key => $each){
            $each = $this->setFabricFileDefault($each); //파일 정보 추가.
            $each = $this->decorationQbRequest($each, $key, $mixData); //꾸미기
            $qbList[$key] = $each;
        }

        $pageEx = $qbAllData['pageData']->getPage('#');

        return [
            'pageEx' => $pageEx,
            'page' => $qbAllData['pageData'],
            'list' => $qbList
        ];
    }

    public function decorationQbRequest($each, $key, $mixData){
        $each['reqStatusKr'] = ImsCodeMap::IMS_BT_REQ_STATUS[$each['reqStatus']];
        $each['styleFullName'] = implode(' ',[substr($each['prdYear'],2,2),$each['prdSeason'],$each['productName']]);
        foreach(ImsCodeMap::IMS_QB_REQ_TYPE as $key => $value){
            if(!empty($value) && ($key & $each['reqType']) ){
                $each['reqTypeList'][$key] = $value;
            }
        }
        $each['reqTypeKr'] = implode(',', $each['reqTypeList']);
        $each['fabricReqFile'] = json_decode($each['fabricReqFile'], true);

        $btRslt['styleSno']=$each['styleSno'];
        $btRslt['fileDiv']='btFile2';
        $btRslt = $this->setDefaultFile(['btFile2'], $btRslt);

        $each['btResultCheckFile'] = $each['fabricReqFile'];
        $each['btResultFile'] = $btRslt['fileList']['btFile2']['files'];

        $each['projectTypeEn'] = ImsCodeMap::PROJECT_TYPE_EN[$each['projectType']];

        return $each;
    }


    /**
     * 생산 가견적 검색
     * @param $params
     * @return mixed
     */
    public function getListEstimate($params){
        //(요청, 처리중 , 처리불가 , 반려 ===> 진행중 ) 와 완료 ( limit 50 )
        $searchVo = new SearchVo('a.reqStatus <> 0');
        $this->setCommonCondition($params['condition'], $searchVo);
        $this->setListSort($params['condition']['sort'], $searchVo);

        /*$estimateType = 'estimate';
        if(!empty($params['estimateType'])){
            $estimateType = $params['estimateType'];
        }
        $searchVo->setWhere('estimateType=?');
        $searchVo->setWhereValue($estimateType);*/

        $searchData = [
            'page' => gd_isset($params['condition']['page'],1),
            'pageNum' => gd_isset($params['condition']['pageNum'],100),
        ];

        //$estimateList = DBUtil2::getComplexList($this->sql->getEstimateListTable(), $searchVo, false, false, false);
        $estimateAllData = DBUtil2::getComplexListWithPaging($this->sql->getEstimateListTable(), $searchVo, $searchData, false, false);
        $estimateList = $estimateAllData['listData'];

        foreach($estimateList as $key => $each){
            $each = $this->decorationEstimate($each, $key, null); //꾸미기
            $estimateList[$key] = $each;
        }

        $pageEx = $estimateAllData['pageData']->getPage('#');

        //--- Rowspan설정
        SlCommonUtil::setListRowSpan($estimateList, [
            'customer'  => ['valueKey' => 'customerSno']
        ], $params);

        return [
            'pageEx' => $pageEx,
            'page' => $estimateAllData['pageData'],
            'list' => $estimateList
        ];
    }

    /*public function setSortCommon($sort, &$searchVo){
        switch ($sort) {
            case 'A1':
                //처리완료일순
                $searchVo->setOrder('a.completeDeadLineDt desc, a.customerSno, a.projectSno, a.styleSno');
                break;
            case 'A2':
                //처리완료일순
                $searchVo->setOrder('a.completeDeadLineDt, a.customerSno, a.projectSno, a.styleSno');
                break;
            case 'B1':
                //고객사별
                $searchVo->setOrder('cust.customerName desc, a.projectSno, a.styleSno, a.completeDeadLineDt');
                break;
            case 'B2':
                //고객사별
                $searchVo->setOrder('cust.customerName, a.projectSno, a.styleSno, a.completeDeadLineDt');
                break;
        }
    }*/

    /**
     * 생산가 확정 검색
     * @param $params
     * @return mixed
     */
    public function getListCost($params){
        //$params['estimateType'] = 'cost';
        return $this->getListEstimate($params);
    }

    // ==== 기능 =====

    /**
     * 처리중으로 변경
     * @param $params
     * @return array
     */
    public function setRevokeQb($params){
        $saveSnoList = [];
        foreach($params['snoList'] as $sno){
            $reqData =  DBUtil2::getOne(ImsDBName::FABRIC, 'sno', $sno);
            //if($reqData['reqStatus'] >= 3){ //처리완료 이상
            $saveData['sno'] = $sno;
            //$saveData['btStatus'] = 1; //진행중.
            $saveData['reqStatus'] = $params['reqStatus']; //처리완료.
            $saveData['completeDt'] = '0000-00-00';
            $saveSnoList[] = $this->save(ImsDBName::FABRIC_REQ, $saveData);
            //}
        }
        return ['data'=> $saveSnoList,'msg'=>'저장 완료'];
    }


    /**
     * 처리 완료 시키기.
     * @param $params
     * @return array
     * @throws \Exception
     */
    public function setCompleteQb($params){
        //완료 처리시 발송정보 필수.
        if( 3 == $params['reqStatus'] && empty($params['resDeliveryInfo']) ) throw new \Exception('발송정보는 필수 입니다.');

        $saveSnoList = [];
        foreach($params['snoList'] as $sno){
            $saveData['sno'] = $sno;
            $saveData['resDeliveryInfo'] = $params['resDeliveryInfo']; //처리완료 발송정보.
            $saveData['resMemo'] = $params['resMemo']; //처리완료 메모.

            if(!empty($params['reqStatus'])){
                $saveData['reqStatus'] = $params['reqStatus']; //처리완료.
                $saveData['completeDt'] = 'now()';
            }
            $saveSnoList[] = $this->save(ImsDBName::FABRIC_REQ, $saveData); //업데이트
        }
        return ['data'=> $saveSnoList,'msg'=>'저장 완료'];
    }


    /**
     * 확정BT검색
     * @param $params
     * @return mixed
     */
    public function getListCqb($params){

        $searchVo = new SearchVo('fabric.btStatus = 2 and a.reqStatus = 5');

        $this->setCommonCondition($params['condition'], $searchVo);
        $this->setListSort($params['condition']['sort'], $searchVo);

        //$qbList = DBUtil2::getComplexList($this->sql->getFabricListTable(), $searchVo);
        //$list = DBUtil2::getComplexList($this->sql->getFabricListTable(), $searchVo);
        //$list = SlCommonUtil::setEachData($list, $this, 'decorationFabric');

        $searchData = [
            'page' => gd_isset($params['condition']['page'],1),
            'pageNum' => gd_isset($params['condition']['pageNum'],100),
        ];
        $qbAllData = DBUtil2::getComplexListWithPaging($this->sql->getFabricReqListTable(), $searchVo, $searchData);
        $qbList = $qbAllData['listData'];

        //List에서 파일 정보 추가
        foreach($qbList as $key => $each){

            $fileData = $this->setFabricFileDefault([
                'customerSno' =>$each['customerSno'],
                'projectSno'  =>$each['projectSno'],
                'styleSno'    =>$each['styleSno'],
                'sno'     =>$each['fabricSno'],
            ]);

            $each['fileList'] = $fileData;
            //파일 정보 추가.
            /*SitelabLogger::logger2(__METHOD__, 'List에서 파일 추가...');
            SitelabLogger::logger2(__METHOD__, $each['fabricSno']);
            SitelabLogger::logger2(__METHOD__, $fileData);*/

            $each = $this->decorationQbRequest($each, $key, $mixData); //꾸미기
            $qbList[$key] = $each;
        }

        //Rowspan을 위한 데이터 넣기.
        $rowspanMap = [
            'project' => [],
            'fabric' => [],
        ];
        foreach($qbList as $key => $value){
            $keyMap = $this->getRowspanKey($params, $value);
            $rowspanMap['project'][$keyMap['projectKey']]++;
            $rowspanMap['fabric'][$keyMap['fabricKey']]++;
        }
        //한건 안하기.
        $dpRowspanMap = [
            'project' => [],
            'fabric' => [],
        ];
        foreach($qbList as $key => $value){
            $keyMap = $this->getRowspanKey($params, $value);
            $value['projectRowspanKey'] = $keyMap['projectKey'];
            $value['fabricRowspanKey'] = $keyMap['fabricKey'];

            if( empty($dpRowspanMap['project'][$keyMap['projectKey']]) ){
                $value['projectRowspan'] = $rowspanMap['project'][$keyMap['projectKey']];
                $dpRowspanMap['project'][$keyMap['projectKey']] = true;
            }else{
                $value['projectRowspan'] = 0;
            }

            if( empty($dpRowspanMap['fabric'][$keyMap['fabricKey']]) ){
                $value['fabricRowspan'] = $rowspanMap['fabric'][$keyMap['fabricKey']];
                $dpRowspanMap['fabric'][$keyMap['fabricKey']] = true;
            }else{
                $value['fabricRowspan'] = 0;
            }
            $qbList[$key] = $value;
        }


        $pageEx = $qbAllData['pageData']->getPage('#');
        //SitelabLogger::logger2(__METHOD__, $qbAllData['pageData']);

        return [
            'pageEx' => $pageEx,
            'page' => $qbAllData['pageData'],
            'list' => $qbList
        ];
    }

}