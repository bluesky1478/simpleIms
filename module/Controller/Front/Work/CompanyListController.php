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
use Component\Work\WorkCodeMap;
use Framework\Debug\Exception\AlertCloseException;
use Globals;
use Request;
use Session;
use SiteLabUtil\SlCommonUtil;
use SlComponent\Util\SlLoader;
use UserFilePath;

/**
 * 거래처 리스트
 * @author Lee Hakyoung <haky2@godo.co.kr>
 */
class CompanyListController extends \Controller\Front\Controller
{
    use WorkControllerTrait;

    public function workIndex() {
        $this->setMenu('영업관리', '거래처리스트', 'sales2Active');
        $this->setData('headerSaveButtonName', '등록하기'); //Header 등록 버튼 여부

        $workService=SlLoader::cLoad('work','workService','');
        $dataList = $workService->getCompanyList();

        $this->setData('compType', WorkCodeMap::COMP_TYPE);

        $documentService = SlLoader::cLoad('work','documentService','');
        foreach( $dataList as $key => $value){
            $value['salesManagerName'] = SlCommonUtil::getManagerInfo($value['salesManagerSno'])['managerNm'];
            $value['COMPANY_STEP'] = WorkCodeMap::COMPANY_STEP;
            $value['latestDocData'] = $documentService->getLatestDocumentForCompany('SALES', 2, $value['sno']);// 최근 미팅보고서 가져오기
            $dataList[$key] = $value;
        }

        $this->setData('dataList' , $dataList);
    }

}

