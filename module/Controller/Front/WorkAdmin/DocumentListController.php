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

namespace Controller\Front\WorkAdmin;

use App;
use Component\Work\DocumentCodeMap;
use Component\Work\WorkCodeMap;
use Controller\Front\Work\WorkControllerTrait;
use Framework\Debug\Exception\AlertCloseException;
use Globals;
use Request;
use Session;
use SlComponent\Util\SlLoader;
use UserFilePath;

/**
 * 미팅준비 보고서 등록
 */
class DocumentListController extends \Controller\Front\Controller
{
    use WorkControllerTrait;

    public function workIndex() {

        $docDept = \Request::get()->get('docDept');
        $docType = \Request::get()->get('docType');

        $this->setMenu($docDept, $docType);
        $this->setData('regLocation', "document.php?docDept={$docDept}&docType={$docType}");

        //Header 등록 버튼 여부
        $this->setData('headerSaveButtonName', '등록하기'); //Header 등록 버튼 여부
        $documentService=SlLoader::cLoad('work','documentService','');

        $dataList = $documentService->getDocumentList(  $docDept , $docType );
        $this->setData( 'dataList', $dataList );

        $this->setData('isHeaderHistoryButton', false);
        $this->setData('isTempSaveButtonFl', false);

        $this->setData('headerSaveButtonName', ''); //Header 등록 버튼 여부

    }
}

