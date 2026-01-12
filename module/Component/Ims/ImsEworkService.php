<?php
namespace Component\Ims;

use Component\Database\DBIms;
use Component\Database\DBTableField;
use SiteLabUtil\SlCommonUtil;
use SlComponent\Database\DBUtil2;
use SlComponent\Database\SearchVo;
use SlComponent\Util\SitelabLogger;
use SlComponent\Util\SlLoader;

/**
 * 전산 작지 서비스
 * Class SlCode
 * @package SlComponent\Util
 */
class ImsEworkService {

    public static function getEworkTypeList ($mode=null){
        $typeList = [];
        foreach(ImsCodeMap::EWORK_TYPE as $key => $value){
            if(empty($mode)){
                $typeList[$key] = 'ework'.ucfirst($key);
            }else{
                $typeList['ework'.ucfirst($key)] = $value;
            }
        }
        return $typeList;
    }

    /**
     * 전산작지 반환
     * @param $styleSno
     * @return mixed
     * @throws \Exception
     */
    public function getEworkData($styleSno){
        $rslt['product']['sno']=$styleSno;
        $this->setEworkData($rslt);
        return $rslt;
    }

    /**
     * 전산 작업지시서 데이터 셋팅
     * @param $result
     * @throws \Exception
     */
    public function setEworkData(&$result){
        $productData = $result['product'];
        $specGuide = $productData['sizeSpec'];
        $standard = $specGuide['standard']; //기준 사이즈
        $specRange = explode(',',$specGuide['specRange']); //스펙 범위
        $baseIndex = array_search($standard, $specRange);  //기준 사이즈 위치

        $eworkData = DBUtil2::getOne('sl_imsEwork','styleSno',$productData['sno'], false);
        $eworkOrg = $eworkData;

        $eworkData['revision'] = stripslashes($eworkData['revision']);

        $eworkData = DBTableField::parseJsonField('sl_imsEwork',$eworkData); //Decode 한번더.
        $eworkData = DBTableField::fieldStrip('sl_imsEwork',$eworkData);

        //리비전 데이터 없으면 기본설정
        if( empty($eworkData['revision']) ){
            $eworkData['revision'] = [];
        }else{
            $imsService = SlLoader::cLoad('ims', 'imsService');
            $workRev = $imsService->getCode('workRev','작지변경사유');
            $workRevType = $imsService->getCode('workRevType','작지변경구분');
            foreach($eworkData['revision'] as $revKey => $revision){
                $revision['revReasonKr'] = $workRev[$revision['revReason']];
                $revision['revTypeKr'] = $workRevType[$revision['revType']];
                $eworkData['revision'][$revKey]=$revision;
            }
        }

        //파일 리스트 디코드 (1차원)
        foreach (ImsCodeMap::EWORK_FILE_LIST as $fileName) {
            $fileData = json_decode(gd_htmlspecialchars_stripslashes($eworkOrg[$fileName]), true);
            if (!empty($fileData)) {
                $result['ework']['fileList'][$fileName] = $fileData; //별도 저장
            }else{
                $result['ework']['fileList'][$fileName] = []; //별도 저장
            }
        }

        //스펙데이터 없으면 기본 값셋팅 ( Ework에서 가져오던것을 Prd에서 가져오도록 수정 )
        //$specData = json_decode(gd_htmlspecialchars_stripslashes($eworkOrg['specData']), true); //addslashes x
        $specData = $specGuide['specData'];

        foreach($productData['sizeSpec']['specData'] as $specDefaultKey => $specDefault){
            if( empty($specData[$specDefaultKey]) ){
                $specData[$specDefaultKey] = SlCommonUtil::setJsonField([], ImsJsonSchema::SPEC_DATA);
                $specData[$specDefaultKey]['title'] = $specDefault['title'];
                $specData[$specDefaultKey]['unit'] = $specDefault['unit'];
                $specData[$specDefaultKey]['share'] = 'y';
            }
        }

        foreach( $specData as $specKey => $spec ){
            $deviation = $spec['deviation'];
            $spec = SlCommonUtil::setJsonField($spec,ImsJsonSchema::SPEC_DATA); //구조 변경 대비 기본값 설정
            if(empty($spec['deviation'])){
                $spec['deviation'] = $deviation;
            }

            $spec['specList'] = [];
            $spec['correctionList'] = [];
            foreach ($specRange as $index => $size) {
                // 편차 계산 (현재 인덱스 - 기준 인덱스) * 편차
                if( is_numeric($spec['deviation']) || $size == $standard ){
                    $offset = ($index - $baseIndex) * $spec['deviation'];
                    $spec['specList'][$size] = $spec['spec'] + $offset;
                    if( 0 == $spec['spec'] ) $spec['specList'][$size] = '';
                }else{
                    $spec['specList'][$size] = '';
                }
                // 보정값
                if( !empty($spec['correction'][$size]) ){
                    $spec['correctionList'][$size] = $spec['correction'][$size];
                }else{
                    $spec['correctionList'][$size] =  '';
                }
            }

            $eworkData['specData'][$specKey] = $spec;

        }

        ksort($eworkData['specData']);

        //$result['ework']['fileList'] = $eworkFileData['fileList']; //History사용시
        //SitelabLogger::logger2(__METHOD__, $eworkData);
        $result['ework']['data'] = SlCommonUtil::setDateBlank($eworkData);

        //마크 기본 설정
        $result['ework']['markCnt'] = 1;
        for($i=1; 10>=$i; $i++){
            $result['ework']['data']['markInfo'.$i] = ImsJsonSchema::setDefaultSchema($result['ework']['data']['markInfo'.$i], ImsJsonSchema::EWORK_MARK_INFO);
            $checkStr = implode('',$result['ework']['data']['markInfo'.$i]);
            if( $i > 1 && ( !empty($result['ework']['fileList']['fileMark'.$i][0]['fileName']) || !empty($checkStr) )  ){
                $result['ework']['markCnt']++;
            }
        }
        //SitelabLogger::logger2(__METHOD__, '========> Result');
        //SitelabLogger::logger2(__METHOD__, $result['ework']);

        $result = $this->setProduceWarning($result);
    }

    /**
     * 생산시 주의 사항 기본 설정
     * @param $result
     * @return mixed
     */
    public function setProduceWarning($result){
        $result['ework']['data']['produceWarning'] = SlCommonUtil::setJsonField($result['ework']['data']['produceWarning'],ImsJsonSchema::EWORK_WARNING); //생산시 유의 사항 설정
        if(!is_array($result['ework']['data']['produceWarning']['sampleSizeCnt'])){
            $result['ework']['data']['produceWarning']['sampleSizeCnt'] = [];
        }
        //2.스타일 변경 내용
        if(0 >= count($result['ework']['data']['produceWarning']['contents1'])){
            $result['ework']['data']['produceWarning']['contents1'][] = ImsJsonSchema::EWORK_WARNING_CONTENTS;
        }
        //3.고객사 요청사항 / 확정되지 않은 사양
        if(0 >= count($result['ework']['data']['produceWarning']['contents2'])){
            $result['ework']['data']['produceWarning']['contents2'][] = ImsJsonSchema::EWORK_WARNING_CONTENTS;
        }
        //4. 원부자재 비축 요청
        if(0 >= count($result['ework']['data']['produceWarning']['storedFabric'])){
            $result['ework']['data']['produceWarning']['storedFabric'][] = ImsJsonSchema::EWORK_WARNING_FABRIC;
        }
        //5. 비고
        if(0 >= count($result['ework']['data']['produceWarning']['contents3'])){
            $result['ework']['data']['produceWarning']['contents3'][] = ImsJsonSchema::EWORK_WARNING_CONTENTS;
        }

        return $result;
    }


    /**
     * 자재 정보 등록
     * @param $params
     * @return void
     * @throws \Exception
     */
    public function saveEworkFabric($params){
        //스타일 번호가 있는 것 중 저장데이터에 들어오지 않은 데이터는 삭제
        $prdList = DBUtil2::getList(ImsDBName::PRD_MATERIAL, 'styleSno', $params['styleSno']);
        $prdMap = SlCommonUtil::arrayAppKey($prdList, 'sno');

        $sort = 1;
        foreach($params['fabric'] as $fabric){
            $sno = $fabric['sno'];
            $fabric['sort'] = $sort;
            unset($fabric['sno']);

            //제조국은 무조건 소문자로 한다 / market으로 들어온 정보는 mk로 한다.
            $fabric['makeNational'] = strtolower($fabric['makeNational']);
            if( 'market' === $fabric['makeNational'] ){
                $fabric['makeNational'] = 'mk';
            }

            if(empty($sno)){
                $fabric['typeStr'] = 'fabric';
                $fabric['styleSno'] = $params['styleSno'];
                DBUtil2::insert(ImsDBName::PRD_MATERIAL, $fabric);
            }else{
                DBUtil2::update(ImsDBName::PRD_MATERIAL, $fabric, new SearchVo('sno=?',$sno));
                unset($prdMap[$sno]);
            }
            $sort++;
        }

        $sort = 1;
        foreach($params['subFabricList'] as $fabric){
            $sno = $fabric['sno'];
            $fabric['sort'] = $sort;
            unset($fabric['sno']);
            if(empty($sno)){
                $fabric['typeStr'] = 'subFabric';
                $fabric['styleSno'] = $params['styleSno'];
                DBUtil2::insert(ImsDBName::PRD_MATERIAL, $fabric);
            }else{
                DBUtil2::update(ImsDBName::PRD_MATERIAL, $fabric, new SearchVo('sno=?',$sno));
                unset($prdMap[$sno]);
            }
            $sort++;
        }

        foreach($prdMap as $key=>$value){
            DBUtil2::delete(ImsDBName::PRD_MATERIAL,new SearchVo('sno=?', $key));
        }
    }

    /**
     * 원부자재 이력 저장
     * @param $params
     * styleSno
     * @param string $title
     */
    public function saveMaterialHistory($params, $title='생산확정견적'){
        $materialList = DBUtil2::getList(ImsDBName::PRD_MATERIAL, 'styleSno',$params['styleSno']);
        $saveHistory = [
            'styleSno' => $params['styleSno'],
            'updateType' => 'material',
            'contents' => json_encode($materialList),
            'comment' => $title.' (으)로 업데이트',
            'managerSno' => SlCommonUtil::getManagerSno()
        ];
        DBUtil2::insert(ImsDBName::EWORK_HISTORY, $saveHistory);
    }

    /**
     * 사이즈 스펙 이력 저장
     * @param $params
     * styleSno
     * @param $comment
     */
    public function saveSpecHistory($params, $comment='스펙 수정'){
        $prdInfo = DBUtil2::getOne(ImsDBName::PRODUCT, 'sno',$params['styleSno'],false);
        $saveHistory = [
            'styleSno' => $params['styleSno'],
            'updateType' => 'spec',
            //'contents' => json_encode(json_decode($prdInfo['sizeSpec'],true)),
            'contents' => $prdInfo['sizeSpec'],
            'comment' => $comment,
            'managerSno' => SlCommonUtil::getManagerSno()
        ];
        DBUtil2::insert(ImsDBName::EWORK_HISTORY, $saveHistory);
    }

    /**
     * 확정견적 원부자재 복사
     * @param $params
     * styleSno : 스타일 번호
     * costSno  : 확정견적 번호 
     * @throws \Exception
     */
    public function copyMaterial($params){
        if( empty($params['styleSno']) || empty($params['costSno']) ){
            throw new \Exception('스타일 또는 확정견적 번호가 없습니다.');
        }
        //복사하기
        $encodeEstimate = DBUtil2::getOne(ImsDBName::ESTIMATE, 'sno', $params['costSno'], false);
        $estimate = json_decode($encodeEstimate['contents'],true);

        $this->setMaterial($params, $estimate['fabric'], $estimate['subFabric'], '생산확정견적');
    }

    /**
     * 샘플 원부자재 복사
     * @param $params
     * @param $sampleInfo
     * @throws \Exception
     */
    public function copySampleMaterial($params, $sampleInfo){
        if( empty($params['styleSno']) ){
            throw new \Exception('스타일 번호가 없습니다.');
        }
        $this->setMaterial($params, $sampleInfo['fabric'], $sampleInfo['subFabric'], '샘플');
    }

    /**
     * 이전 스타일 원부자재 복사
     * @param $srcSno
     * @param $targetSno
     */
    public function copyBeforeStyle($srcSno, $targetSno){
        $materialList = DBUtil2::getList(ImsDBName::PRD_MATERIAL, 'styleSno', $srcSno);
        foreach($materialList as $material){
            unset($material['sno']);
            unset($material['regDt']);
            $material['styleSno']=$targetSno;
            DBUtil2::insert(ImsDBName::PRD_MATERIAL, $material);
        }
    }

    /**
     * 원부자재 복사
     * @param $params
     * @param $fabricList
     * @param $subFabricList
     * @param $memo
     * @throws \Exception
     */
    public function setMaterial($params, $fabricList, $subFabricList, $memo){
        if( empty($params['styleSno'])){
            throw new \Exception('스타일 또는 확정견적 번호가 없습니다.');
        }

        //이력 기록
        $this->saveMaterialHistory($params, $memo);

        //기존 원부자재 정보 지움
        DBUtil2::delete(ImsDBName::PRD_MATERIAL, new SearchVo('styleSno=?', $params['styleSno']));

        //복사하기
        //$encodeEstimate = DBUtil2::getOne(ImsDBName::ESTIMATE, 'sno', $params['costSno'], false);
        //$estimate = json_decode($encodeEstimate['contents'],true);

        foreach($fabricList as $fabric){
            if( !SlCommonUtil::isAllEmptyOrZero($fabric) ){
                $saveData = SlCommonUtil::getAvailData($fabric,[
                    'attached',
                    'fabricName',
                    'fabricMix',
                    'color',
                    'spec',
                    'meas',
                    'unitPrice',
                    'makeNational',
                    'memo',
                    'unit',
                ]);
                $saveData['typeStr'] = 'fabric';
                $saveData['styleSno'] = $params['styleSno'];
                $saveData['position'] = gd_isset($fabric['no'], $fabric['position']);
                $saveData['makeCompany'] = gd_isset($fabric['fabricCompany'],$fabric['makeCompany']);

                //제조국은 무조건 소문자로 한다 / market으로 들어온 정보는 mk로 한다. (복사시 처리)
                $saveData['makeNational'] = strtolower($saveData['makeNational']);
                if( 'market' === $saveData['makeNational'] ){
                    $saveData['makeNational'] = 'mk';
                }

                DBUtil2::insert(ImsDBName::PRD_MATERIAL, $saveData);
            }
        }

        foreach($subFabricList as $fabric){
            if( !SlCommonUtil::isAllEmptyOrZero($fabric) ){
                $saveData = SlCommonUtil::getAvailData($fabric,[
                    'attached',
                    'color',
                    'spec',
                    'meas',
                    'unitPrice',
                    'memo',
                    'unit',
                ]);
                $saveData['typeStr'] = 'subFabric';
                $saveData['fabricName'] = gd_isset($fabric['subFabricName'],$fabric['fabricName']);
                $saveData['fabricMix'] = gd_isset($fabric['subFabricMix'],$fabric['fabricMix']);

                $saveData['styleSno'] = $params['styleSno'];
                $saveData['position'] = gd_isset($fabric['no'],$fabric['position']);
                $saveData['makeCompany'] = gd_isset($fabric['company'], $fabric['makeCompany']);
                DBUtil2::insert(ImsDBName::PRD_MATERIAL, $saveData);
            }
        }
    }


    /**
     * 원부자재 이전자료로 복원
     * @param $styleSno
     * @param $replaceSno
     * @return array
     * @throws \Exception
     */
    public function replaceMaterial($styleSno, $replaceSno){
        $updateRslt = [];
        //현재 상태를 저장
        $this->saveMaterialHistory(['styleSno'=>$styleSno], $title='이전자료로 원복');
        DBUtil2::delete(ImsDBName::PRD_MATERIAL, new SearchVo('styleSno=?', $styleSno));
        //새롭게 저장
        $eworkHistory = DBUtil2::getOne(ImsDBName::EWORK_HISTORY, 'sno', $replaceSno);
        $contents = json_decode($eworkHistory['contents'],true);
        foreach($contents as $content){
            unset($content['sno']);
            $updateRslt[] = DBUtil2::insert(ImsDBName::PRD_MATERIAL, $content);
        }
        return $updateRslt;
    }


    /**
     * 작지 리비전 저장
     * @param $params
     * @throws \Exception
     */
    public function saveEworkRevision($params){
        $searchVo = new SearchVo('styleSno=?', $params['styleSno']);
        $beforeData = DBUtil2::getOneBySearchVo(ImsDBName::EWORK, $searchVo);
        $imsService = SlLoader::cLoad('ims', 'imsService');
        $imsService->recordHistory('update', ImsDBName::EWORK, $beforeData, '리비전 등록/수정');
        $revision = json_encode($params['revision']);
        DBUtil2::update(ImsDBName::EWORK, [
            'revision' =>$revision
        ], $searchVo);
    }


    /**
     * 작지 + 상품 정보(납기일, 스타일명 등 일부) 저장
     * @param $params
     * @throws \Exception
     */
    public function saveEworkWithPrdInfo($params){
        $imsService = SlLoader::cLoad('ims', 'imsService');
        //SitelabLogger::logger2(__METHOD__, '### 작지 + 상품 정보(납기일, 스타일명 등 일부) 저장');
        //SitelabLogger::logger2(__METHOD__, $params);

        $eworkData = $this->getEworkData($params['styleSno']);
        $eworkDbInfo = SlCommonUtil::arrayAppKeyValue(DBIms::tableImsEwork(),'val','name');
        //$eworkDB = SlCommonUtil::arrayAppKeyValue(DBIms::tableImsEwork(),'val','name');

        $prdData = DBUtil2::getOne(ImsDBName::PRODUCT, 'sno', $params['styleSno'], false);
        $prdDbInfo = SlCommonUtil::arrayAppKeyValue(DBIms::tableImsProjectProduct(),'val','name');

        //작지 정보 수정 사항 처리
        $updatePrdField = [];
        $updateEworkField = [];

        $idx = '▶';
        $isSpecUpdate=false;

        //작지 수정 사항
        foreach($params['eworkData']['data'] as $dataKey => $dataValue){
            if(isset($eworkDbInfo[$dataKey])){
                $updateEworkField[] = '<b>'.$idx.$eworkDbInfo[$dataKey].' 변경</b><br>';
                //만약 SpecData가 바뀐 부분이 있다면
                if( 'specData' === $dataKey ) {
                    $specStrArray = [];
                    $sizeSpec = json_decode($prdData['sizeSpec'], true);
                    foreach ($dataValue as $eachIdx => $eachSpecData) {
                        foreach ($eachSpecData as $specUpdateKey => $specUpdateData) {
                            if ('correctionList' === $specUpdateKey) {
                                foreach ($specUpdateData as $correctionKey => $correctionValue) {
                                    $specStrArray[] = "보정값({$correctionKey}):" . $sizeSpec['specData'][$eachIdx]['correction'][$correctionKey] . '→' . $correctionValue;
                                    $sizeSpec['specData'][$eachIdx]['correction'][$correctionKey] = $correctionValue;
                                }
                            } else {
                                $specStrArray[] = $specUpdateKey . ":" . $sizeSpec['specData'][$eachIdx][$specUpdateKey] . '→' . $specUpdateData;
                                $sizeSpec['specData'][$eachIdx][$specUpdateKey] = $specUpdateData;
                            }
                        }
                    }
                    $params['prdInfo']['sizeSpec'] = json_encode($sizeSpec); //상품 데이터 저장
                    $isSpecUpdate=true;
                }else{
                    $excludeBeforeAfterKey = ['produceWarning', 'revision', 'markInfo'];
                    $refineDataKey = preg_replace('/[0-9]/', '', $dataKey);
                    if(!in_array($dataKey,$excludeBeforeAfterKey)){
                        $before = is_array($eworkData['ework']['data'][$dataKey])? implode(',', $eworkData['ework']['data'][$dataKey]) : $eworkData['ework']['data'][$dataKey];
                        $after = is_array($params['eworkData']['data'][$dataKey])? implode(',', $params['eworkData']['data'][$dataKey]): $params['eworkData']['data'][$dataKey];
                        $updateEworkField[] = $prdDbInfo[$dataKey].'*전: '.$before . ' → 후: ' . $after. ' <br>';
                    }
                }
            }
        }

        //업데이트 처리
        if(!empty($updateEworkField) && !empty($params['eworkData']['data'])){
            //스펙 처리
            if( isset($params['eworkData']['data']['specData']) ){
                $params['eworkData']['data']['specData'] = json_encode($params['eworkData']['data']['specData']);
                //상품에도 저장될 수 있게한다.
                $updatePrdField[] = '<b>'.$idx.'사이즈 스펙 데이터 변경(별도 이력 기록)</b><br>';
            }
            //마크 처리
            for($i=1; 10>=$i; $i++){
                if(isset($params['eworkData']['data']['markInfo'.$i])){
                    foreach($params['eworkData']['data']['markInfo'.$i] as $markUpdateKey => $markUpdateValue){
                        $eworkData['ework']['data']['markInfo'.$i][$markUpdateKey] = $markUpdateValue;
                        $params['eworkData']['data']['markInfo'.$i] = json_encode($eworkData['ework']['data']['markInfo'.$i]);
                    }
                    $updatePrdField[] = "<b>{$idx}마크{$i} 정보 변경</b><br>";
                }
            }
            //생산 유의 사항 처리
            if( isset($params['eworkData']['data']['produceWarning']) ){
                $params['eworkData']['data']['produceWarning'] = json_encode($params['eworkData']['data']['produceWarning']);
            }
            DBUtil2::update(ImsDBName::EWORK, $params['eworkData']['data'], new SearchVo('styleSno=?',$params['styleSno'])); //업데이트
        }

        //상품 저장 전에 스펙 데이터 저장
        if($isSpecUpdate){
            $this->saveSpecHistory(['styleSno'=>$params['styleSno']]);
        }
        //상품 수정 사항
        foreach($params['prdInfo'] as $dataKey => $dataValue){
            if(isset($prdDbInfo[$dataKey])){
                $excludeBeforeAfterKey = ['sizeSpec'];
                if(!in_array($dataKey,$excludeBeforeAfterKey)){
                    $updatePrdField[] = '<b>'.$idx.$prdDbInfo[$dataKey].'변경</b><br>';
                    $before = is_array($prdData[$dataKey])? implode(',', $prdData[$dataKey]) : $prdData[$dataKey];
                    $after = is_array($params['prdInfo'][$dataKey])? implode(',', $params['prdInfo'][$dataKey]) : $params['prdInfo'][$dataKey];
                    $updatePrdField[] = '*전: '.$before . ' → 후: ' . $after.'<br>';
                }
            }
        }
        if(!empty($updatePrdField) && !empty($params['prdInfo']) ){
            DBUtil2::update(ImsDBName::PRODUCT, $params['prdInfo'], new SearchVo('sno=?',$params['styleSno'])); //업데이트
        }

        //원부자재 처리
        if(!empty($params['fabricList']) || !empty($params['subFabricList']) ){
            $updateEworkField[] = "<b>{$idx}. 원부자재 정보 변경(이전 내용 별도 기록)</b><br>";
            $this->saveMaterialHistory(['styleSno'=>$params['styleSno']], '수기 ');
            $this->saveEworkFabric([
                'styleSno' => $params['styleSno'],
                'fabric' => $params['fabricList'],
                'subFabricList' => $params['subFabricList'],
            ]);
        }

        $updateField = array_merge($updatePrdField, $updateEworkField);
        if(!empty($updateField)){
            $recordData['sno'] = $params['styleSno'];
            $imsService->recordHistory('update', ImsDBName::EWORK, $recordData, $updateField);
        }

    }

    /**
     * 전산작지 특정 업데이트 정보 반환
     * @param $params
     * @return mixed
     */
    public function getEworkHistory($params){
        $searchVo = new SearchVo(['a.styleSno=?','a.updateType=?'],[$params['styleSno'], $params['historyDiv']]);
        $searchVo->setOrder('regDt desc');
        $list=DBUtil2::getJoinList(ImsDBName::EWORK_HISTORY, [
            'b' => [ DB_MANAGER, 'a.managerSno=b.sno', 'b.managerNm as regManagerNm' ]
        ],$searchVo, 'material' === $params['historyDiv']);

        foreach($list as $key => $each){
            $each['contents'] = json_decode($each['contents'], true);
            //gd_debug($each['contents']);

            if( 'material' === $params['historyDiv'] ){
                usort($each['contents'], function($a, $b) {
                    return [$a['typeStr'],$a['sort']] <=> [$b['typeStr'],$b['sort']];
                });
                //자재 저장 정보 처리
                $typeStrMap = ['fabric'=>'원자재', 'subFabric'=>'부자재'];
                foreach($each['contents'] as $contentsKey => $contentsValue){
                    $contentsValue['typeStr'] = $typeStrMap[$contentsValue['typeStr']];
                    unset($contentsValue['sort']);
                    unset($contentsValue['regDt']);
                    unset($contentsValue['modDt']);
                    unset($contentsValue['styleSno']);
                    $each['contents'][$contentsKey]= $contentsValue;
                }
            }else if('spec' === $params['historyDiv']){
                //SPEC_DATA
                foreach($each['contents']['specData'] as $specKey => $specValue){
                    $specValue = SlCommonUtil::setJsonField($specValue, ImsJsonSchema::SPEC_DATA);
                    $each['contents']['specData'][$specKey] = $specValue;
                }
                //$each['contents']['specData'] = SlCommonUtil::setJsonField($each['contents']['specData'], ImsJsonSchema::SPEC_DATA);
            }

            $list[$key] = $each;
        }
        return $list;
    }

}

