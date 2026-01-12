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
 * 문서 서비스
 */
class DocumentDownloadService {

    /**
     * 샘플 의뢰서 (샘플지시서) 다운로드
     * @param $reqParam
     */
    public function downloadDesign30($reqParam){
        //$reqParam['index']

        $filePath = './module/Component/Work/Html/download_sample.html';
        $contents = SlCommonUtil::getFileData($filePath);

        $docService=SlLoader::cLoad('work','documentService','');
        $documentData = $docService->getDocumentDataBySno($reqParam['sno']);

        $data = $documentData['docData']['sampleData'][$reqParam['index']];

        //regDt
        $replaceField = [
            'serial', 'season', 'companyName', 'productName', 'produceType', 'requestDt', 'specType', 'produceCountry', 'completeDt', 'etc', 'sampleSize'
        ];
        $replace = [];
        foreach( $replaceField as $replaceKey ){
            $replace[$replaceKey] = $data[$replaceKey];
        }

        $replace['regDt'] = gd_date_format('Y-m-d', $documentData['regDt']);
        $replace['specUnit'] = $data['sampleItem'][0]['specUnit'];

        //원단 정보
        $partInfoList = $data['partInfo'];
        if( count($data['partInfo']) % 2 != 0  ){
            $partInfoList[] = [
                'size' => '',
                'yochuck' => '',
            ];
        }
        $sampleFabricList = [];
        $defaultTemplate = DownloadTemplate::sampleFabric;
        foreach($partInfoList as $index => $partData){
            $idx = $index%2==0?1:2;
            $partReplace['index'. $idx] = $index+1;
            $partReplace['width'. $idx] = $partData['size'];
            $partReplace['yochuck'. $idx] = $partData['yochuck'];
            $defaultTemplate = SlCommonUtil::replaceContents($partReplace, '{% ', ' %}', $defaultTemplate);
            if( $index%2 != 0 ){
                $sampleFabricList[] = $defaultTemplate;
                $defaultTemplate = DownloadTemplate::sampleFabric;
                $partReplace = [];
            }
        }
        $replace['sampleFabric'] = implode('', $sampleFabricList);

        //완성제품스펙사이즈
        $sampleSpecList = [];
        foreach($data['sampleItem'] as $sampleItem){
            $sampleSpecList[] = '<tr ><td class="text-center">'.$sampleItem['specItemName'].'</td><td class="text-center">'.$sampleItem['guideSpec'].'</td><td ></td><td colspan=3>'.$sampleItem['specDescription'].'</td></tr>';
        }
        $replace['sampleSpecList'] = implode('', $sampleSpecList);

        //이미지
        $imgPath = $data['fileSample'][0]['path'];

        //SitelabLogger::logger($imgPath);
        //$base64Image = base64_encode( file_get_contents($imgPath) );
        //SitelabLogger::logger($base64Image);
        //$replace['sampleImage'] = "<img src='data:image/png;base64,{$imgPath}' style='max-width:900px;max-height:500px;'>";

        $size = getimagesize($imgPath);

        $width = $size[0] > 900 ? 900 : $size[0];
        $height = $size[1] > 500 ? 500 : $size[1];

        $replace['sampleImage'] = "<img src='{$imgPath}' width='{$width}' height='{$height}'  >";


        //최종 Replace
        $contents = SlCommonUtil::replaceContents($replace, '{% ', ' %}', $contents);

        $title = '샘플지시서';
        $fileName = str_replace('/','_',urlencode($title));
        $simpleExcelComponent = SlLoader::cLoad('Excel','SimpleExcelComponent','sl');
        $simpleExcelComponent->downloadCommon($contents, $fileName);
    }


    /**
     * 작업지시서 다운로드
     * @param $reqParam
     */
    public function downloadOrder310($reqParam){
        $contents = $this->getCurlData(URI_HOME."work/download/download_work.php?sno={$reqParam['sno']}&idx={$reqParam['index']}");
        $title = '작업지시서';
        $fileName = str_replace('/','_',urlencode($title));
        $simpleExcelComponent = SlLoader::cLoad('Excel','SimpleExcelComponent','sl');
        $simpleExcelComponent->downloadCommon($contents, $fileName);
    }


    /**
     * @param $url
     * @return bool|string
     */
    public function getCurlData($url){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }

}
