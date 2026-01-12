<?php
namespace Component\Work;

use App;
use Component\Member\Manager;
use Component\Storage\Storage;
use Component\Work\Code\DocumentDesignCodeMap;
use Couchbase\Document;
use Framework\Debug\Exception\AlertBackException;
use Framework\Utility\DateTimeUtils;
use LogHandler;
use Request;
use Session;
use SiteLabUtil\SlCommonUtil;
use SlComponent\Database\DBUtil2;
use SlComponent\Database\SearchVo;
use SlComponent\Util\ExcelCsvUtil;
use SlComponent\Util\SitelabLogger;
use SlComponent\Util\SlCodeMap;
use SlComponent\Util\SlLoader;
use SlComponent\Util\SlSmsUtil;

/**
 * 문서 꾸밈 서비스
 */
class DocumentDecorateService {

    private $workCodeMap;
    private $documentCodeMap;

    public function __construct(){
        $this->workCodeMap = new \ReflectionClass('\Component\Work\WorkCodeMap');
        $this->documentCodeMap = new \ReflectionClass('\Component\Work\DocumentCodeMap');
    }

    /**
     * 포트 폴리오 데이터 꾸미기.
     * @param $documentData
     * @return mixed
     */
    /*public function setDecoratedDataDesign2($documentData){
        //코멘트는 기본적으로 닫기.
        foreach( $documentData['docData']['portData'] as $styleIdx => $styleValue){
            foreach($styleValue as $portEachIdx => $portEachValue){
                $documentData['docData']['portData'][$styleIdx][$portEachIdx]['showCommentReg'] = 0;
            }
        }
        return $documentData;
    }*/

    /**
     * 피팅 체크 리스트 기본 데이터
     * @param $documentData
     * @return array
     */
    /*public function setDefaultDataDesign4($documentData){
        foreach( WorkCodeMap::STYLE_TYPE as $key => $styleName ){
            $portDefaultData = DocumentDesignCodeMap::DESIGN_PORT_DATA;
            $portDefaultData['styleName'] = $styleName;
            $portDefaultData['styleType'] = 'A';
            $documentData['docData']['portData'][$key][] = $portDefaultData;
        }
        return $documentData;
    }*/

}
