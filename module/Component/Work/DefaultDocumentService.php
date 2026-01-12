<?php
namespace Component\Work;

use App;
use Component\Member\Manager;
use Component\Storage\Storage;
use Component\Work\Code\DocumentDesignCodeMap;
use Framework\Debug\Exception\AlertBackException;
use Framework\Utility\DateTimeUtils;
use LogHandler;
use Request;
use Session;
use SiteLabUtil\SlCommonUtil;
use SlComponent\Util\DocumentStruct;
use SlComponent\Util\SitelabLogger;
use SlComponent\Util\SlLoader;
use SlComponent\Util\SlProjectCodeMap;

/**
 * 기본 문서 서비스
 */
class DefaultDocumentService {

    /**
     * 기본 프로젝트 문서 셋팅
     * @param $documentData
     * @param $param
     * @return mixed
     */
    public function setDefaultProjectDocument($documentData, $param){
        $docDept = $documentData['docDept'];
        $docType = $documentData['docType'];
        $setFncName = 'setDefaultProjectDocument' . ucfirst(strtolower($docDept)).$docType;
        $methodMap = SlCommonUtil::getReverseMap(get_class_methods(__CLASS__));
        if( isset($methodMap[$setFncName]) ){
            $documentData = self::$setFncName( $documentData, $param );
        }
        return $documentData;
    }

    /**
     * Preview set
     * @param $documentData
     * @param $previewFieldList
     * @param $defaultField
     * @return mixed
     */
    private function setDefaultPreviewImage(&$documentData, $previewFieldList, $defaultField){
        //PREVIEW Data는 업로드된 파일을 기준으로 보여준다.
        foreach($documentData['docData']['sampleData'] as $firstKey => $sampleData){

            $documentData['docData']['sampleData'][$firstKey] = array_merge(DocumentStruct::DOC_PART[$defaultField], $sampleData);

            foreach( $documentData['docData']['sampleData'][$firstKey]['checkList'] as $checkKey => $checkData){
                $documentData['docData']['sampleData'][$firstKey]['checkList'][$checkKey]['checkSpec'] = gd_isset($documentData['docData']['sampleData'][$firstKey]['checkList'][$checkKey]['checkSpec'],[]);
            }

            foreach($previewFieldList as $previewField){
                $documentData['docData']['sampleData'][$firstKey][$previewField.'Preview'] = [];
                foreach( $sampleData[$previewField] as $fileSample ){
                    $documentData['docData']['sampleData'][$firstKey][$previewField.'Preview'][] = $fileSample['path'];
                }
            }
        }
        return $documentData;
    }

    /**
     * 미팅 준비 보고서
     * @param $documentData
     * @param $param
     * @return mixed
     */
    public function setDefaultProjectDocumentSales10( $documentData, $param ){
        if( $this->isNewDocument($param) ){
            $documentData['docData']['hopeData'] = [ DocumentStruct::DOC_PART['hopeData'] ];
        }
        return $documentData;
    }

    /**
     * 미팅 보고서
     * @param $documentData
     * @param $param
     * @return mixed
     */
    public function setDefaultProjectDocumentSales20( $documentData, $param ){
        //신규 문서일 경우 처리 - 미팅 준비보고서 기본 셋팅
        if( $this->isNewDocument($param) ){
            $documentData['docData']['hopeData'] = [ DocumentStruct::DOC_PART['hopeData'] ];
            $documentData['docData']['meetingDt'] = gd_isset($documentData['docData']['meetingDt'], date('Y-m-d'));
            $documentService = SlLoader::cLoad('work','documentService','');
            $latestDocument = $documentService->getLatestDocumentData('SALES','10',$param['projectSno']);
            $notOverrideField = [ 'fileDefault' ];
            //원 문서 기준으로 기존 데이터 있으면 넣기.
            if( !empty($latestDocument) ){
                foreach( $documentData['docData'] as $docKey => $docData ){
                    if( !in_array($docKey,$notOverrideField) && !empty($latestDocument['docData'][$docKey])  ){
                        $documentData['docData'][$docKey] = $latestDocument['docData'][$docKey]; //덮어쓰기.
                    }
                }
            }
        }

        return $documentData;
    }

    /**
     * 견적서
     * @param $documentData
     * @param $param
     * @return mixed
     */
    public function setDefaultProjectDocumentSales60( $documentData, $param ){
        if( $this->isNewDocument($param) ){
            $documentData['docData']['estimateDt'] = gd_isset($documentData['docData']['estimateDt'],date('Y-m-d'));
        }
        return $documentData;
    }

    /**
     * 폐쇄몰 준비 자료
     * @param $documentData
     * @param $param
     * @return mixed
     */
    public function setDefaultProjectDocumentSales90( $documentData, $param ){
        return $documentData;
    }


    /**
     * 디자인 기획서 (컨셉)
     * @param $documentData
     * @param $param
     * @return mixed
     */
    public function setDefaultProjectDocumentDesign10($documentData, $param){
        if(empty($documentData['docData']['designDirection'])){
            $hopeList = $documentData['projectData']['meetingData']['docData']['hopeData'];
            foreach($hopeList as $key => $each){
                $documentData['docData']['designDirection'][$key]['styleName'] = $each['style'];
                $documentData['docData']['designDirection'][$key]['styleType'] = '';
                $documentData['docData']['designDirection'][$key]['recommendCnt'] = $each['count'];
                $documentData['docData']['designDirection'][$key]['description'] = '';
            }
        }
        return $documentData;
    }

    /**
     * 디자인 포트폴리오 데이터 셋팅
     * @param $documentData
     * @param $param
     * @return mixed
     * @throws \Exception
     */
    public function setDefaultProjectDocumentDesign20($documentData, $param){
        $documentData['docData']['styleData'] = [];
        foreach($documentData['docData']['portData'] as $styleKey => $styleData){
            $isStyleAccept = false;
            foreach( $styleData as $styleIdxKey => $styleIdxData ){
                if( 2 == $styleIdxData['status']){
                    $isStyleAccept = true; //1가지라도 Accept면.
                }
                foreach( $styleIdxData['commentList'] as $commentKey => $commentData ){
                    $firstDate  = new \DateTime($commentData['regDt']);
                    $secondDate = new \DateTime();
                    $interval = $firstDate->diff($secondDate);
                    $commentData['interval'] = $interval->days;
                    if( 2 > $interval->days){
                        $commentData['isNew'] = 'y';
                    }else{
                        $commentData['isNew'] = 'n';
                    }
                    $documentData['docData']['portData'][$styleKey][$styleIdxKey]['commentList'][$commentKey] = $commentData;
                }
                $documentData['docData']['styleData'][$styleKey] = $isStyleAccept;
            }
        }
        return $documentData;
    }

    /**
     * 디자인 샘플 데이터 셋팅
     * @param $documentData
     * @param $param
     * @return mixed
     */
    public function setDefaultProjectDocumentDesign30($documentData, $param){
        $this->setDefaultPreviewImage($documentData, ['fileSample'], 'designSample');
        return $documentData;
    }

    /**
     * 피팅 체크 데이터 셋팅
     * @param $documentData
     * @param $param
     * @return mixed
     */
    public function setDefaultProjectDocumentDesign40($documentData, $param){
        //신규 문서일 경우 처리 - 샘플의뢰서 기본 셋팅
        if( $this->isNewDocument($param) ){
            $workService = SlLoader::cLoad('work','workService','');
            $latestDocument = $this->getLatestSampleData($param['projectSno']);
            $sampleData = $latestDocument['docData']['sampleData'];
            foreach($sampleData as $key => $each){
                $checkItem = $workService->getCheckList($each['styleType']);
                $each['checkItem'] = $checkItem;
                $sampleData[$key] = $each;
            }
            $documentData['docData']['sampleData'] = $sampleData;
        }
        $this->setDefaultPreviewImage($documentData, ['fileSample'], 'designCheck');

        return $documentData;
    }

    /**
     * 작업지시서 기본 값 셋팅
     * @param $documentData
     * @param $param
     * @return mixed
     */
    public function setDefaultProjectDocumentOrder310($documentData, $param){
        //신규 문서일 경우 처리 - 샘플의뢰서 기본 셋팅
        if( $this->isNewDocument($param) ){

            $workService = SlLoader::cLoad('work','workService','');

            $latestSampleDocument = $this->getLatestSampleData($param['projectSno']);

            $loadDataList = $latestSampleDocument['docData']['sampleData'];
            $defaultSampleData = DocumentStruct::DOC_PART['designWork'];
            $notOverrideField = [ 'partInfo', 'subPartInfo', 'sampleItem' ];

            foreach( $loadDataList as $loadData ){
                foreach($defaultSampleData as $key => $each){
                    if( !in_array($key,$notOverrideField) && !empty($loadData[$key]) ){
                        $defaultSampleData[$key] = $loadData[$key];
                    }
                }
                $specCheckInfo = $workService->getStyle($loadData['styleType'])[0]['specCheckInfo'];
                foreach($specCheckInfo as $specKey => $specData){
                    $defaultSampleData['checkList'][] = array_merge(DocumentStruct::DOC_PART['designSpec'] ,$specData);
                }

                $documentData['docData']['sampleData'][] = $defaultSampleData;
            }
        }

        $this->setDefaultPreviewImage($documentData, ['fileSample'], 'designWork');

        return $documentData;
    }

    /**
     * 유니폼 디자인 가이드
     * @param $documentData
     * @param $param
     * @return mixed
     */
    public function setDefaultProjectDocumentOrder210($documentData, $param){
        $documentService = SlLoader::cLoad('work','documentService','');
        //신규 문서일 경우 처리 - 작업지시서 기본 셋팅
        if( $this->isNewDocument($param) ){
            //작업 지시서
            $latestWorkDocument = $documentService->getLatestDocumentData('ORDER3','10',$param['projectSno']);
            $productDataList = $latestWorkDocument['docData']['sampleData'];
            $defaultData = DocumentStruct::DOC_PART['designOrder'];
            $notOverrideField = ['sampleItem'];
            foreach( $productDataList as $productData ){
                foreach($defaultData as $key => $each){
                    if( !in_array($key,$notOverrideField) && !empty($productData[$key]) ){
                        $defaultData[$key] = $productData[$key];
                    }
                }

                foreach($defaultData['typeList'] as $typeKey => $typeData){
                    $typeData['inputCount'] = $typeData['optionCount'];
                    $defaultData['typeList'][$typeKey] = $typeData;
                }

                $documentData['docData']['sampleData'][] = $defaultData;
            }

            //포트폴리오
            $latestPortfolio = $documentService->getLatestDocumentData('DESIGN','20',$param['projectSno']);
            $documentData['docData']['step1'] = gd_date_format('Y-m-d', $latestPortfolio['isCustomerApplyDt']);

        }

        $this->setDefaultPreviewImage($documentData, ['fileSample','fileThumbnail'], 'designOrder');

        //가장 최근의 폐쇄몰 준비 자료 가져오기
        $mallData = $documentService->getLatestDocumentData('SALES', 90, $documentData['projectSno']);
        $documentData['mallData'] = $mallData;

        return $documentData;
    }


    /**
     * 새 문서 여부 판단
     * @param $param
     * @return bool
     */
    private function isNewDocument($param){
        return empty($param['sno']) && empty($param['latest']);
    }

    /**
     * 최근 샘플 의뢰서 가져오기
     * @param $projectSno
     * @return mixed
     */
    private function getLatestSampleData($projectSno){
        $documentService = SlLoader::cLoad('work','documentService','');
        return $documentService->getLatestDocumentData('DESIGN','30',$projectSno);
    }

}
