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

namespace Controller\Front\Work;

use App;
use Component\Work\DocumentCodeMap;
use Component\Work\WorkCodeMap;
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

        $docType = \Request::get()->get('docType');

        $menuMap = [
            1 => [
                'title' => '영업관리',
                'titleSub' => '미팅준비보고서 리스트',
                'menuActive' => 'sales3Active',
                'docDept' => 'SALES',
                'docType' => 1,
                'regLocation' => 'sales_meeting_ready_reg.php',
            ],
            2 => [
                'title' => '영업관리',
                'titleSub' => '미팅보고서 리스트',
                'menuActive' => 'sales4Active',
                'docDept' => 'SALES',
                'docType' => 2,
                'regLocation' => 'sales_meeting_reg.php',
            ],
            3 => [
                'title' => '영업관리',
                'titleSub' => '근무환경 보고서',
                'menuActive' => 'sales5Active',
                'docDept' => 'SALES',
                'docType' => 3,
                'regLocation' => 'sales_workenv_reg.php',
            ],
            4 => [
                'title' => '영업관리',
                'titleSub' => '생산견적 요청서',
                'menuActive' => 'sales6Active',
                'docDept' => 'SALES',
                'docType' => 4,
                'regLocation' => 'sales_prd_estimate_reg.php',
            ],
            5 => [
                'title' => '영업관리',
                'titleSub' => '폐쇄몰 준비자료',
                'menuActive' => 'sales7Active',
                'docDept' => 'SALES',
                'docType' => 5,
                'regLocation' => 'sales_mall_document_reg.php',
            ],
            6 => [
                'title' => '영업관리',
                'titleSub' => '견적서',
                'menuActive' => 'sales8Active',
                'docDept' => 'SALES',
                'docType' => 6,
                'regLocation' => 'sales_estimate_reg.php',
            ],
            7 => [
                'title' => '영업관리',
                'titleSub' => '발주확정서',
                'menuActive' => 'sales9Active',
                'docDept' => 'SALES',
                'docType' => 7,
                'regLocation' => 'sales_order.php',
            ],
            8 => [
                'title' => '영업관리',
                'titleSub' => '견적서',
                'menuActive' => 'sales10Active',
                'docDept' => 'SALES',
                'docType' => 8,
                'regLocation' => 'sales_contract.php',
            ],
        ];

        $this->setData('title' , $menuMap[$docType]['title']);
        $this->setData('titleSub', $menuMap[$docType]['titleSub']);
        $this->setData('regLocation', $menuMap[$docType]['regLocation']);
        $this->setData($menuMap[$docType]['menuActive'], 'active');

        //Header 등록 버튼 여부
        $this->setData('headerSaveButtonName', '등록하기'); //Header 등록 버튼 여부

        $documentService=SlLoader::cLoad('work','documentService','');


        $dataList = $documentService->getDocumentList(  $menuMap[$docType]['docDept'] , $menuMap[$docType]['docType'] );
        $this->setData( 'dataList', $dataList );

        $this->setData('isHeaderHistoryButton', false);
        $this->setData('isTempSaveButtonFl', false);

    }
}

