<?php

namespace Controller\Admin\Ims;

use App;
use Component\Category\BrandAdmin;
use Component\Category\CategoryAdmin;
use Component\Stock\StockListService;
use Component\Work\WorkCodeMap;
use Controller\Admin\Erp\AdminErpControllerTrait;
use Controller\Admin\Erp\ControllerService\InoutListService;
use Globals;
use Request;
use SiteLabUtil\SlCommonUtil;
use SlComponent\Util\ExcelCsvUtil;
use SlComponent\Util\SlCodeMap;
use SlComponent\Util\SlLoader;
use function SlComponent\Util\SlLoader;

/**
 * 프로젝트 리스트
 */
class ImsPreparedListController extends \Controller\Admin\Controller{

    use ImsControllerTrait;

    public function index(){
        $request=\Request::get()->toArray();
        $preparedType = empty($request['preparedType']) ? 'all' : $request['preparedType'];

        \Request::get()->set('projectStatus', SlCommonUtil::getOnlyNumber($request['status']));

        //작지만 prepared...
        if('work' === $preparedType) {
            $midType = 'prepared';
        }else{
            $midType = 'prdPrepared';
        }

        $this->callMenu('ims', 'prepared', $preparedType);

        $this->setDefault();
        $requestParam = $this->getData('requestParam');

        $controllerListService = SlLoader::controllerServiceLoad(__NAMESPACE__,'imsPreparedList');
        //$this->getView()->setPageName('ims/ims_prepared_list.php');

        $this->setData('targetPage','ims_project_view.php');
        if(empty($request['preparedType'])){
            $stepFncName = 'setStepCommon';
        }else{
            $stepFncName = 'setStep'.ucfirst($request['preparedType']);
        }
        $this->$stepFncName();
        $this->getView()->setPageName("ims/prepared/step_common.php");

        $listService = SlLoader::cLoad('godo','listService','sl');
        $listService->setList($controllerListService, $this);
    }

    public function setStepEstimate(){
        $this->setData('stepItem', [
            'produceCompany' => [
                'title' => '생산처',
                'type' => 'text',
                'col' => 4,
                'class' => 'font-11',
            ],
            'styleWithCount' => [
                'title' => '스타일',
                'type' => 'style',
                'col' => 10,
            ],
            'preparedStatusKr' => [
                'title' => '상태',
                'type' => 'text',
                'col' => 4,
                'class' => 'font-13',
            ],
            'preparedRegDtShort' => [
                'title' => '요청일자',
                'type' => 'text',
                'col' => 3,
                'addKey' => 'preparedRegManagerNm'
            ],
            'deadLineDtShort' => [
                'title' => '완료요청일(DL)',
                'type' => 'text',
                'col' => 3,
                'class' => 'font-14'
            ],
            'deadLineRemainDt' => [
                'title' => '완료남은시간(DL)',
                'type' => 'text',
                'col' => 3,
                'class' => 'font-14 text-danger'
            ],
            'acceptMemo' => [
                'title' => '승인/반려 사유',
                'type' => 'text',
                'col' => 15,
            ],
        ]);
    }

    public function setStepBt(){
        $this->setData('stepItem', [
            'produceCompany' => [
                'title' => '생산처',
                'type' => 'text',
                'col' => 4,
                'class' => 'font-11',
            ],
            'styleWithCount' => [
                'title' => '스타일',
                'type' => 'style',
                'col' => 10,
            ],
            'fabricCount' => [
                'title' => 'BT대상원단',
                'type' => 'style',
                'col' => 4,
                'class' => 'font-16',
            ],
            'btCount' => [
                'title' => 'BT컨펌완료',
                'type' => 'style',
                'col' => 4,
                'class' => 'font-16',
            ],
            'preparedStatusKr' => [
                'title' => '상태',
                'type' => 'text',
                'col' => 4,
                'class' => 'font-13',
            ],
            'preparedRegDtShort' => [
                'title' => '요청일자',
                'type' => 'text',
                'col' => 3,
                'addKey' => 'preparedRegManagerNm'
            ],
            'deadLineDtShort' => [
                'title' => '완료요청일(DL)',
                'type' => 'text',
                'col' => 3,
                'class' => 'font-14'
            ],
            'deadLineRemainDt' => [
                'title' => '완료남은시간(DL)',
                'type' => 'text',
                'col' => 3,
                'class' => 'font-14 text-danger'
            ],
            'sendType' => [
                'title' => '발송형태',
                'type' => 'text',
                'col' => 10,
            ],
            'sendInfo' => [
                'title' => '발송정보',
                'type' => 'text',
                'col' => 10,
            ],
            'sendDt' => [
                'title' => '발송예정일',
                'type' => 'text',
                'col' => 4,
                'class' => 'font-16 text-danger'
            ],
            'acceptMemo' => [
                'title' => '승인/반려 사유',
                'type' => 'text',
                'col' => 10,
            ],
        ]);
    }

    public function setStepWork(){
        $this->setData('stepItem', [
            'styleWithCount' => [
                'title' => '스타일',
                'type' => 'text',
                'col' => 10,
            ],
            'prdExQty' => [
                'title' => '수량',
                'type' => 'number',
                'col' => 3,
            ],
            'preparedStatusKr' => [
                'title' => '상태',
                'type' => 'text',
                'col' => 4,
                'class' => 'font-13',
            ],
            'preparedRegDtShort' => [
                'title' => '요청일자',
                'type' => 'text',
                'col' => 3,
                'addKey' => 'preparedRegManagerNm'
            ],
            'deadLineDtShort' => [
                'title' => '완료요청일(DL)',
                'type' => 'text',
                'col' => 3,
                'class' => 'font-14'
            ],
            'deadLineRemainDt' => [
                'title' => '완료남은시간(DL)',
                'type' => 'text',
                'col' => 3,
                'class' => 'font-14 text-danger'
            ],
            'preparedRegManagerNm' => [
                'title' => '요청자',
                'type' => 'text',
                'col' => 3,
            ],
            'customerDeliveryDtShort' => [
                'title' => '고객납기일',
                'type' => 'text',
                'col' => 4.5,
                'class' => 'font-14',
            ],
            'customerDeliveryRemainDt' => [
                'title' => '납기 D-day',
                'type' => 'text',
                'col' => 4.5,
                'class' => 'font-13'
            ],
        ]);
    }

    public function setStepCost(){
        $this->setData('stepItem', [
            'produceCompany' => [
                'title' => '생산처',
                'type' => 'text',
                'col' => 4,
                'class' => 'font-11',
            ],
            'styleWithCount' => [
                'title' => '스타일',
                'type' => 'style',
                'col' => 10,
            ],
            'preparedStatusKr' => [
                'title' => '상태',
                'type' => 'text',
                'col' => 4,
                'class' => 'font-13',
            ],
            'preparedRegDtShort' => [
                'title' => '요청일자',
                'type' => 'text',
                'col' => 3,
                'addKey' => 'preparedRegManagerNm'
            ],
            'deadLineDtShort' => [
                'title' => '완료요청일(DL)',
                'type' => 'text',
                'col' => 3,
                'class' => 'font-14'
            ],
            'deadLineRemainDt' => [
                'title' => '완료남은시간(DL)',
                'type' => 'text',
                'col' => 3,
                'class' => 'font-14 text-danger'
            ],
            'acceptMemo' => [
                'title' => '승인/반려 사유',
                'type' => 'text',
                'col' => 15,
            ],
        ]);
    }

    public function setStepOrder(){
        $this->setData('stepItem', [
            'produceCompany' => [
                'title' => '생산처',
                'type' => 'text',
                'col' => 4,
                'class' => 'font-11',
            ],
            'styleWithCount' => [
                'title' => '스타일',
                'type' => 'style',
                'col' => 10,
            ],
            'preparedStatusKr' => [
                'title' => '상태',
                'type' => 'text',
                'col' => 4,
                'class' => 'font-13',
            ],
            'preparedRegDtShort' => [
                'title' => '요청일자',
                'type' => 'text',
                'col' => 3,
                'addKey' => 'preparedRegManagerNm'
            ],
            'deadLineDtShort' => [
                'title' => '완료요청일(DL)',
                'type' => 'text',
                'col' => 3,
                'class' => 'font-14'
            ],
            'deadLineRemainDt' => [
                'title' => '완료남은시간(DL)',
                'type' => 'text',
                'col' => 3,
                'class' => 'font-14 text-danger'
            ],
            'workSendDt' => [
                'title' => '작업지시서 발송예정일',
                'type' => 'text',
                'col' => 6,
            ],
        ]);
    }

}