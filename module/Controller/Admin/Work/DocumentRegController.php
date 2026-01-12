<?php
/**
 * This is commercial software, only users who have purchased a valid license
 * and accept to the terms of the License Agreement can install and use this
 * program.
 *
 * Do not edit or add to this file if you wish to upgrade Godomall5 to newer
 * versions in the future.
 *
 * @copyright ⓒ 2016, NHN godo: Corp.
 * @link      http://www.godo.co.kr
 */

namespace Controller\Admin\Work;


use Component\Work\Code\DocumentDesignCodeMap;
use Component\Work\DocumentCodeMap;
use Component\Work\WorkCodeMap;
use SlComponent\Database\DBUtil2;
use SlComponent\Util\SitelabLogger;
use SlComponent\Util\SlLoader;
use SlComponent\Util\SlProjectCodeMap;

class DocumentRegController extends \Controller\Admin\Controller
{
    use AdminWorkControllerTrait;

    public function workIndex(){
        $reqParam = $this->getData('requestParam');
        $docDept = $this->getData('docDept');
        $docType = $this->getData('docType');
        //$this->callMenu('work', strtolower($docDept) , 'doclist'.$docType );

        //TODO : 어디서 왔는지 찾아서 콜메뉴 셋팅
        $this->callMenu('work', 'project', 'total');

        $documentStruct =  SlProjectCodeMap::PRJ_DOCUMENT[$docDept];
        $title = $documentStruct['typeDoc'][$docType]['name'];
        $includeFile = $docDept.$docType;

        $this->setData('title', $title);
        $this->setData('incFile', $includeFile . '.php' );

        $this->setData('docDept', $docDept);
        $this->setData('docType', $docType );

        $workService=SlLoader::cLoad('work','workService','');
        $sampleFactoryData = $workService->getSampleFactoryMap();
        $this->setData('sampleFactoryData', json_encode($sampleFactoryData, JSON_UNESCAPED_UNICODE) );
        $this->setData('sampleFactoryList', $sampleFactoryData );

        //gd_debug( $workService->getStyleNameListMap() );
        $this->setData('styleList', $workService->getStyleNameListMap() );

        $base = \Request::getScheme()."://gdadmin.".\Request::getDefaultHost();
        $this->setData('downloadBasePath', $base);

        $downloadParamList = [
            'simple_excel_download=1'
        ];
        $this->setData('requestUrl', basename(\Request::getPhpSelf()) .'?'.implode('&',$downloadParamList). '&' . \Request::getQueryString() );

        //SitelabLogger::logger('#Request Param');
        //SitelabLogger::logger($reqParam);

        //엑셀 다운로드.
        if(  !empty($reqParam['simple_excel_download'])  ){
            $downloadFncName = 'download'.ucfirst(strtolower($docDept)).$docType;
            $downloadService=SlLoader::cLoad('work','documentDownloadService','');
            $downloadService->$downloadFncName($reqParam);
            exit();
        }

    }
}
