<?php
namespace Controller\Admin\Ims\Popup;

use Controller\Admin\Ims\ImsControllerTrait;
use Request;
use SiteLabUtil\SlCommonUtil;
use Component\Ims\ImsCodeMap;

class ImsPopStoredOutputListController extends \Controller\Admin\Controller
{
    use ImsControllerTrait;

    private $dpData;

    public function __construct() {
        parent::__construct();
        $this->dpData = [
            'no' => ['name'=>'번호','col'=>'2','skip'=>true],
            'outputDt' => ['name'=>'출고일','col'=>'3'],
            'fabricName' => ['name'=>'비축 자재명','col'=>'6'],
            'fabricMix' => ['name'=>'혼용율','col'=>'3'],
            'color' => ['name'=>'색상','col'=>'3'],
            'inputOwn' => ['name'=>'소유권','col'=>'5'],
            'outQty' => ['name'=>'출고수량','col'=>'4'],
            'outReason' => ['name'=>'출고사유','col'=>'4'],
            'reqInputNm' => ['name'=>'등록자','col'=>'3'],
        ];
        if(in_array(\Session::get('manager.managerId'),ImsCodeMap::STORE_MANAGER)) {
            $this->dpData['btn_update'] = ['name'=>'관리','col'=>'3', 'afterContents' => "<span class='btn btn-sm btn-white hover-btn cursor-pointer' @click=\"modOutput(each.outputSno,each.sno)\">수정</span><span class='btn btn-sm btn-white hover-btn cursor-pointer' @click=\"delOutput(each.outputSno)\">삭제</span>",];
        }
    }

    private function getDisplayStoredList(){
        SlCommonUtil::createHtmlTableTitle($this->dpData);
        return $this->dpData;
    }

    public function index() {
        $iInputSno = (int)Request::get()->get('sno');
        if ($iInputSno === 0) {
            echo "접근오류";
            exit;
        }
        $this->setData('inputSno',$iInputSno);
        $this->setDefault();

        //검색항목
        $search['combineSearch'] = [
            'c.outReason' => '출고사유',
        ];
        $this->setData('search', $search);

        $dpStyle = $this->getDisplayStoredList();
        $tableTitleDate = SlCommonUtil::createHtmlTableTitle($dpStyle);
        $this->setData('tableTitleData',$tableTitleDate);
        $tableBodyData = SlCommonUtil::createHtmlTableVueBody($dpStyle);
        $tableBodyData = str_replace('<td>{% listTotal.idx - index %}</td>','<td>{% index + 1 %}</td>',$tableBodyData);
        $this->setData('tableBodyData',$tableBodyData);

        $this->getView()->setDefine('layout', 'layout_blank.php');
    }
}