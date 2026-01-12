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

namespace Controller\Front\Work\Download;


use Component\Work\Code\DocumentDesignCodeMap;
use Component\Work\DocumentCodeMap;
use Component\Work\WorkCodeMap;
use Controller\Admin\Work\AdminWorkControllerTrait;
use SlComponent\Database\DBUtil2;
use SlComponent\Util\SitelabLogger;
use SlComponent\Util\SlLoader;

class DownloadWorkController extends \Controller\Front\Controller{
    public function index(){
        $reqParam = \Request::get()->toArray();
        $docService=SlLoader::cLoad('work','documentService','');
        $projectService = SlLoader::cLoad('work','projectService','');

        //$documentData = $docService->getDocumentDataBySno($reqParam['sno']);
        $documentData = $projectService->getProjectDocument( ['sno'=>$reqParam['sno']]);

//        /SitelabLogger::logger($documentData);

        //gd_debug( $documentData );

        //$this->setData('projectData', $documentData);
        $this->setData('items', $documentData);

        $productData = $documentData['docData']['sampleData'][$reqParam['idx']];
        $productData['factoryName'] = DBUtil2::getOne('sl_workSampleFactory', 'sno', $productData['sampleFactorySno'])['factoryName'];
        $this->setData('productData', $productData);
        $this->getView()->setDefine('layout', 'layout_blank.php');
    }
}
