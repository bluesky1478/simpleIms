<?php

namespace Controller\Admin\Ims\Popup;

use App;
use Component\Category\BrandAdmin;
use Component\Category\CategoryAdmin;
use Component\Ims\ImsCodeMap;
use Component\Stock\StockListService;
use Component\Work\WorkCodeMap;
use Controller\Admin\Erp\AdminErpControllerTrait;
use Controller\Admin\Erp\ControllerService\InoutListService;
use Controller\Admin\Ims\ImsControllerTrait;
use Globals;
use Request;
use SiteLabUtil\SlCommonUtil;
use SlComponent\Util\ExcelCsvUtil;
use SlComponent\Util\SlCodeMap;
use SlComponent\Util\SlLoader;
use function SlComponent\Util\SlLoader;

/**
 * 문서 리스트
 */
class ImsPopProjectSimpleController extends \Controller\Admin\Controller{

    use ImsControllerTrait;

    public function index(){
        $this->setDefault();
        $this->getView()->setDefine('layout', 'layout_blank.php');

        $requestArray = \Request::request()->toArray();

        $tmpMap = [
            'customerWait'=>'customerWait',
            'designAgreeMemo'=>'mix9',
            'qcAgreeMemo'=>'mix10',
            'allAgreeExpectedDt'=>'mix11',
            'allAgreeCompleteDt'=>'mix12',
            'meetingReport'=>'mix14',
            'meetingMember'=>'mix13',
            'custMeetingInform'=>'mix15',
            'mix9'=>'mix9',
            'mix10'=>'mix10',
            'mix11'=>'mix11',
            'mix12'=>'mix12',
            'mix14'=>'mix14',
        ];

        $specialTypeField = array_keys($tmpMap);
        if( in_array($requestArray['type'], $specialTypeField)  ){
            $modifyType = $tmpMap[$requestArray['type']];
        }else{
            $modifyType = empty(ImsCodeMap::PROJECT_ADD_INFO[$requestArray['type']]['modifyType']) ? 'picker' : ImsCodeMap::PROJECT_ADD_INFO[$requestArray['type']]['modifyType'];
        }

        //gd_debug( $modifyType );
        $this->setData('modifyType',$modifyType);
    }

}