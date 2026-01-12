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
use SlComponent\Database\DBUtil2;
use SlComponent\Database\SearchVo;
use SlComponent\Mail\SiteLabMailUtil;
use SlComponent\Util\ExcelCsvUtil;
use SlComponent\Util\SitelabLogger;
use SlComponent\Util\SlCodeMap;
use SlComponent\Util\SlLoader;
use Component\Sms\Code;

/**
 * IMS 생산관련 서비스
 * Class GoodsStock
 * @package Component\Goods
 */
class ImsProduceService {

    use ImsServiceTrait;

    private $sql;

    public function __construct(){
        $this->sql =  SlLoader::sqlLoad(__CLASS__, false);
    }

    /**
     * 생산 데이터 반환
     * @param $projectSno
     * @return array
     */
    public function getProduceData($projectSno){
        //생산정보 가져오기
        $eachData = DBUtil2::getOne(ImsDBName::PRODUCE,'projectSno', $projectSno, false);
        return $this->decorationProduceEachData($eachData);
    }

    /**
     * 개별데이터 꾸미기
     * @param $eachData
     * @return mixed
     */
    public function decorationProduceEachData($eachData){

        //$eachData['confirmMemo'] = gd_htmlspecialchars_stripslashes($eachData['confirmMemo']);
        $eachData['msMemo'] = gd_htmlspecialchars_stripslashes($eachData['msMemo']);
        $eachData['deliveryMethod'] = gd_htmlspecialchars_stripslashes($eachData['deliveryMethod']);
        $eachData['deliveryMethodBr'] = nl2br($eachData['deliveryMethod']);
        $eachData['msMemoBr'] = nl2br($eachData['msMemo']);

        $eachData['memo'] = gd_htmlspecialchars_stripslashes($eachData['memo']);
        $eachData['memoBr'] = nl2br($eachData['memo']);

        $eachData['produceStatusKr'] = ImsCodeMap::PRODUCE_STATUS[$eachData['produceStatus']];

        $prdStepParse = json_decode($eachData['prdStep'],true);

        foreach( ImsCodeMap::PRODUCE_STEP_MAP as $stepKey => $stepTitle){
            $stepName = 'prdStep'.$stepKey;
            if( empty($prdStepParse[$stepName]) ){
                //저장 없다면 스키마 넣기
                $eachData[$stepName] = ImsJsonSchema::PRODUCE_STEP;
            }else{
                //저장있다면 불러오기 (단 최신 스키마 기준으로)
                $eachData[$stepName] = $prdStepParse[$stepName];
                foreach(ImsJsonSchema::PRODUCE_STEP as $prdStepKey => $prdStepData){
                    if( !isset($eachData[$stepName][$prdStepKey])){
                        $eachData[$stepName][$prdStepKey]='';
                    }
                }
            }

            if( strpos($stepTitle,'ⓒ')!==false ){
                $class = ImsCodeMap::PRODUCE_CONFIRM_TYPE[$eachData[$stepName]['confirmYn']]['class'];
                $name = ImsCodeMap::PRODUCE_CONFIRM_TYPE[$eachData[$stepName]['confirmYn']]['name'];
                $eachData[$stepName]['confirmYnKr'] = "<span class='{$class}'>{$name}</span>";
            }else{
                $eachData[$stepName]['confirmYnKr'] = '<span class="text-muted">승인없음</span>';
            }
        }
        return $eachData; //리스트든 개별이든 공통으로 데코레이션
    }

    /**
     * PrdStep 저장용으로 묶기.
     * @param $saveData
     * @return mixed
     */
    public function setEncodePrdStep($saveData){
        $prdStepList = [];
        foreach(ImsCodeMap::PRODUCE_STEP_MAP as $key => $value){
            $stepName = 'prdStep'.$key;
            unset($saveData[$stepName]['confirmYnKr']);
            $prdStepList[$stepName] = $saveData[$stepName];
            unset($saveData[$stepName]);
        }
        $saveData['prdStep'] = json_encode($prdStepList);
        return $saveData;
    }

    /**
     * 생산 정보 저장
     * @param $param
     * @return mixed
     * @throws \Exception
     */
    public function saveProduce($param){
        $saveData = $param['saveData'];
        $saveData = $this->setEncodePrdStep($saveData);
        /*$prdStepList = [];
        foreach(ImsCodeMap::PRODUCE_STEP_MAP as $key => $value){
            $stepName = 'prdStep'.$key;
            $prdStepList[$stepName] = $saveData[$stepName];
            unset($saveData[$stepName]);
        }
        $saveData['prdStep'] = json_encode($prdStepList);*/

        $saveProduceCompany['produceCompanySno'] = $saveData['produceCompanySno'];
        $saveProduceCompany['sno'] =  $saveData['projectSno'];
        $this->save(ImsDBName::PROJECT, $saveProduceCompany);

        return $this->save(ImsDBName::PRODUCE, $saveData);
    }

    /**
     * 생산등록
     * @param $params
     * @return array
     * @throws \Exception
     */
    public function addProduce($params){
        $produceData = DBUtil2::getOne(ImsDBName::PRODUCE, 'projectSno', $params['projectSno']);

        if(!empty($produceData)){
            throw new \Exception('이미 생산 등록되어있습니다.');
        }
        $params['projectSno'];
        $this->save(ImsDBName::PRODUCE, [
            'projectSno' => $params['projectSno']
        ]);
        return $params;
    }


    /**
     * @param $params
     * @return mixed
     * @throws \Exception
     */
    public function setBatchProduceChangeStep($params){
        $snoList = explode(',',$params['snoList']);
        foreach($snoList as $sno){
            $this->setProduceChangeStep([
                'sno' => $sno,
                'changeStatus' => $params['changeStep'],
                'reason' => $params['reason'],
            ]);
        }
        return $params;
    }

    /**
     * 다음 스텝으로 변경
     * @param $params
     * @return mixed
     * @throws \Exception
     */
    public function setProduceChangeStep($params){
        $beforeData = DBUtil2::getOne(ImsDBName::PRODUCE, 'sno', $params['sno']);

        $this->saveStatusHistory([
            'historyDiv' => 'produce',
            'projectSno' => $beforeData['projectSno'],
            'beforeStatus' => $beforeData['produceStatus'],
            'afterStatus' => $params['changeStatus'],
            'reason' => $params['reason'],
        ]);

        //생산완료 처리
        if( 99 == $params['changeStatus'] ){
            $beforeProjectData = DBUtil2::getOne(ImsDBName::PROJECT, 'sno', $beforeData['projectSno']);
            DBUtil2::update(ImsDBName::PROJECT,['projectStatus' => 90],new SearchVo('sno=?', $beforeData['projectSno']));
            $this->saveStatusHistory([
                'projectSno' => $beforeData['projectSno'],
                'beforeStatus' => $beforeProjectData['projectStatus'],
                'afterStatus' => 90,
                'reason' => $params['reason'],
            ]);
        }

        $this->save(ImsDBName::PRODUCE, [
            'sno' => $params['sno'],
            'produceStatus' => $params['changeStatus'],
        ]);

        return $params;
    }


    /**
     * Step 별 카운트3pl
     * @return mixed
     */
    public function getProduceStepCount(){

        //생산처일 경우에는 본인 리스트만 보이게
        $mId = \Session::get('manager.managerId');
        $mSno = \Session::get('manager.sno');
        if( in_array($mId, ImsCodeMap::PRODUCE_COMPANY_MANAGER) ){
            $sql = " select produceStatus, count(1) as cnt from sl_imsProduce a join sl_imsProject b on a.projectSno = b.sno where a.produceCompanySno={$mSno} group by produceStatus "; //생산처
        }else{
            $sql = " select produceStatus, count(1) as cnt from sl_imsProduce a join sl_imsProject b on a.projectSno = b.sno group by produceStatus "; //이노버
        }

        $projectStatus = SlCommonUtil::arrayAppKeyValue(DBUtil2::runSelect($sql), 'produceStatus', 'cnt');
        $result = [];
        foreach( ImsCodeMap::PRODUCE_STATUS as $status => $statusName ) {
            $result[$status] = empty($projectStatus[$status])?0:$projectStatus[$status];
        }
        return $result;
    }


    /**
     * Step 정보 변경.
     * @param $params
     * @return mixed
     * @throws \Exception
     */
    public function setPrdStepConfirm($params){
        $produceData = $this->getProduceData($params['projectSno']);
        $this->saveStatusHistory([
            'historyDiv' => $params['prdStep'],
            'projectSno' => $produceData['projectSno'],
            'beforeStatus' => ImsCodeMap::PRODUCE_CONFIRM_TYPE[$produceData[$params['prdStep']]['confirmYn']]['name'],
            'afterStatus' => ImsCodeMap::PRODUCE_CONFIRM_TYPE[$params['confirmStatus']]['name'],
            'reason' => $params['memo'],
        ]);

        $produceData[$params['prdStep']]['confirmYn'] = $params['confirmStatus'];

        unset($produceData[$params['prdStep']]['confirmYnKr']);
        /*SitelabLogger::logger('### CHANGE DATA .');
        SitelabLogger::logger($params['prdStep']); //dropzoneid
        SitelabLogger::logger($params['confirmStatus']);
        SitelabLogger::logger('### 여기에 드롭존 아이디 있나 확인');
        SitelabLogger::logger($produceData);*/

        $produceData = $this->setEncodePrdStep($produceData);

        $this->save(ImsDBName::PRODUCE,[
            'sno' => $produceData['sno'],
            'prdStep' => $produceData['prdStep']
        ]);

        return $this->getProduceData($params['projectSno']);
    }



}

