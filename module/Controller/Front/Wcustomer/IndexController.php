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
 * @link http://www.godo.co.kr
 */

namespace Controller\Front\Wcustomer;

use App;
use Framework\Debug\Exception\AlertCloseException;
use Globals;
use Request;
use Session;
use SiteLabUtil\SlCommonUtil;
use SlComponent\Util\SlLoader;
use UserFilePath;

/**
 * Index - 영업문서 등록 (추후 대쉬보드)
 */
class IndexController extends \Controller\Front\Controller
{
    public function index() {

        $workControllerService=SlLoader::cLoad('work','workControllerService','');
        $workControllerService->setControllerData($this);

        $request = \Request::request()->toArray();
        $projectSno = SlCommonUtil::aesDecrypt($request['key']);

        $projectService = SlLoader::cLoad('work','projectService','');
        $projectData = $projectService->getProjectDataWithDocument($projectSno);
        $this->setData('projectData', $projectData);

        $documentList = [];
        $setDocType = [
            0=>['docDept'=>'SALES','docType'=>20],
            1=>['docDept'=>'SALES','docType'=>60],
            2=>['docDept'=>'DESIGN','docType'=>20],
            3=>['docDept'=>'ORDER2','docType'=>10],
        ];
        foreach($setDocType as $each){
            $document = $projectData['planData'][$each['docDept']]['typeDoc'][$each['docType']];
            if(!empty($document['document'])){
                $documentList[] = $document;
            }
        }
        //gd_debug( $documentList );

        $this->setData('documentList', $documentList);

    }

}

