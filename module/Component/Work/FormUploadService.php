<?php
namespace Component\Work;

use App;
use Component\Member\Manager;
use Component\Stock\StockListService;
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
use SlComponent\Util\DownloadTemplate;
use SlComponent\Util\ExcelCsvUtil;
use SlComponent\Util\SitelabLogger;
use SlComponent\Util\SlCodeMap;
use SlComponent\Util\SlLoader;
use SlComponent\Util\SlSmsUtil;

/**
 * 양식 업로드 서비스
 */
class FormUploadService {

    /**
     * 원/부자재 업로드 공통
     * @param $files
     * @param $inputDataIndex
     * @return array
     * @throws \Exception
     */
    public function getUploadPartDataCommon($files, $inputDataIndex){
        $excelFile = [];
        $excelFile['excel'] = $files['file'];
        $startRowCnt = 1;
        $sheetData = ExcelCsvUtil::getUploadData($excelFile, $startRowCnt);
        $listData = [];
        foreach( $sheetData as $idx => $data ) {
            $isCheckSpec = false;
            $checkSpec = [];

            if (($startRowCnt + 1) > $idx) continue;

            $inputData = [];
            foreach($inputDataIndex as $key => $dataIndex){
                if ( strpos($key, 'checkSpec') !== false ){
                    $isCheckSpec = true;
                    $checkSpec[] = $data[$dataIndex];
                }else{
                    $inputData[$key] = $data[$dataIndex];
                }
            }
            if( $isCheckSpec ){
                $inputData['checkSpec'] = $checkSpec;
            }
            $listData[] = $inputData;
        }
        return $listData;
    }

    /**
     * 원자재 파일 정보를 반환
     * @param $files
     * @return array
     * @throws \Exception
     */
    public function getUploadPartInfo($files){
        $inputDataIndex = [
            'type' => 1,
            'desc' => 2,
            'size' => 3,
            'yochuck' => 4,
            'partName' => 5,
            'color' => 6,
            'etc' => 7,
        ];
        return $this->getUploadPartDataCommon($files, $inputDataIndex);
    }

    /**
     * 부자재 파일 정보를 반환
     * @param $files
     * @return array
     * @throws \Exception
     */
    public function getUploadSubPartInfo($files){
        $inputDataIndex = [
            'type' => 1,
            'desc' => 2,
            'size' => 3,
            'soyo' => 4,
            'partName' => 5,
            'color' => 6,
            'etc' => 7,
        ];
        return $this->getUploadPartDataCommon($files, $inputDataIndex);
    }

    /**
     * 원자재 파일 정보를 반환
     * @param $files
     * @return array
     * @throws \Exception
     */
    public function getUploadWorkPartData($files){
        $inputDataIndex = [
            'type' => 1,
            'desc' => 2,
            'size' => 3,
            'yochuck' => 4,
            'partName' => 5,
            'color' => 6,
            'etc' => 7,
            'size2' => 8,
            'memo' => 9,
        ];
        return $this->getUploadPartDataCommon($files, $inputDataIndex);
    }


    /**
     * 부자재 파일 정보를 반환
     * @param $files
     * @return array
     * @throws \Exception
     */
    public function getUploadWorkSubPartData($files){
        $inputDataIndex = [
            'type' => 1,
            'desc' => 2,
            'size' => 3,
            'soyo' => 4,
            'partName' => 5,
            'color' => 6,
            'etc' => 7,
            'yochuck' => 8,
            'memo' => 9,
        ];
        return $this->getUploadPartDataCommon($files, $inputDataIndex);
    }


    /**
     * 샘플스펙 업로드
     * @param $files
     * @return array
     * @throws \Exception
     */
    public function getUploadSampleItem($files){
        $inputDataIndex = [
            'specItemName' => 1,
            'completeSpec' => 2,
            'guideSpec' => 3,
            'specUnit' => 4,
            'specDescription' => 5,
        ];
        return $this->getUploadPartDataCommon($files, $inputDataIndex);
    }

    public function getUploadCheckList($files){
        $inputDataIndex = [
            'specItemName' => 1,
            'isCustomerGuideFl' => 2,
            'avg' => 3,
        ];
        $optionCount = \Request::post()->get('optionCount');
        for($i=0; $optionCount > $i; $i++){
            $inputDataIndex['checkSpec'.$i] = 4+$i;
        }
        $inputDataIndex['specUnit'] = count($inputDataIndex)+1;
        $inputDataIndex['specDescription'] = count($inputDataIndex)+1;

        return $this->getUploadPartDataCommon($files, $inputDataIndex);
    }

}
