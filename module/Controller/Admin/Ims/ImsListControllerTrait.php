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

namespace Controller\Admin\Ims;


use Component\Erp\ErpCodeMap;
use Component\Ims\EnumType\PREPARED_TYPE;
use Component\Ims\ImsApprovalService;
use Component\Ims\ImsCodeMap;
use Component\Ims\ImsDBName;
use Component\Work\DocumentCodeMap;
use Component\Work\WorkCodeMap;
use SiteLabUtil\ImsUtil;
use SiteLabUtil\SlCommonUtil;
use SlComponent\Database\DBUtil2;
use SlComponent\Database\SearchVo;
use SlComponent\Database\TableVo;
use SlComponent\Util\SlCodeMap;
use SlComponent\Util\SlLoader;
use Request;
use SlComponent\Util\SlProjectCodeMap;
use Bundle\Component\CurrencyExchangeRate\CurrencyExchangeRate;
use Bundle\Component\CurrencyExchangeRate\CurrencyExchangeRateAdmin;
use Framework\Debug\Exception\LayerException;

trait ImsListControllerTrait {

    public function setEmergencyTodoList(){
        //긴급 처리 요청건 가져오기
        $imsService = SlLoader::cLoad('ims','imsService');
        $this->setData('emergencyTodoList', $imsService->getEmergencyTodoRequest());
    }

    public function listDownload(){
        //imsPageReload : 기본셋
        $request = Request::get()->toArray();
        if(  !empty($request['simple_excel_download']) &&  $request['simple_excel_download'] == 1 ){
            $request['page'] = 1;
            $request['pageNum'] = 15000;

            $decodeField = [
                'multiKey',
                'projectTypeChk',
                'salesStatusChk',
                'orderProgressChk',
                'delayStatus',
                'customerStatus',
            ];
            foreach($decodeField as $each){
                $request[$each] = json_decode($request[$each], true);
            }
            $request['viewType'] = 'style';
            unset($request['simple_excel_download']);
            ImsUtil::simpleExcelDownload($request);
            exit();
        }
    }

}
