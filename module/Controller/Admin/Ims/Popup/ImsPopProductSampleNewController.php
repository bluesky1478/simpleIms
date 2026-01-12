<?php
namespace Controller\Admin\Ims\Popup;

use Component\Ims\ImsDBName;
use Controller\Admin\Ims\ImsControllerTrait;
use Controller\Admin\Ims\ImsPsNkTrait;
use Request;
use SiteLabUtil\SlCommonUtil;
use SlComponent\Database\DBUtil2;
use SlComponent\Util\SlLoader;

class ImsPopProductSampleNewController extends \Controller\Admin\Controller
{
    use ImsControllerTrait;
    use ImsPsNkTrait;
    public function index() {
        $this->setDefault();

        $sTabMenu = Request::get()->get('tabmenu');
        $sTabMenu = $sTabMenu == '' ? 'instruct' : $sTabMenu;
        $iSampleSno = (int)Request::get()->get('sno');
        $this->setData('sTabMenu', $sTabMenu);
        $this->setData('iSno', $iSampleSno);
        $iStyleSno = (int)Request::get()->get('styleSno');
        if ($iStyleSno === 0 && $iSampleSno > 0) {
            $oSampleInfo = DBUtil2::getOne(ImsDBName::SAMPLE, 'sno', $iSampleSno);
            if (!isset($oSampleInfo['styleSno']) || $oSampleInfo['styleSno'] == 0) {
                echo "접근오류";
                exit;
            }
            $iStyleSno = (int)$oSampleInfo['styleSno'];
        }
        $this->setData('styleSno', $iStyleSno);
        $this->setData('productPlanSno', (int)Request::get()->get('productPlanSno'));

        //스타일의 스타일코드, 시즌코드 가져오기 -> 사이즈스펙 보기팝업버튼 클릭시 전달
        $oStyleInfo = DBUtil2::getOne(ImsDBName::PRODUCT, 'sno', $iStyleSno);
        if (!isset($oStyleInfo['sno']) || $oStyleInfo['sno'] == 0) {
            echo "접근오류";
            exit;
        }
        $this->setData('sSendPrdStyle', $oStyleInfo['prdStyle']);
        $this->setData('sSendPrdSeason', $oStyleInfo['prdSeason']);

        //json으로 저장되는 컬럼의 default form 가져오기
        if ($sTabMenu == 'instruct') {
            //ajax에서 가져올거라 아래 1줄 필요없음
//            $this->setData('aDefaultJson', $this->getJsonDefaultForm(ImsDBName::SAMPLE));
        } else {
            $this->setData('aDefaultJson', $this->getJsonDefaultForm(ImsDBName::SAMPLE.'_'.$sTabMenu));
        }


        //패턴실, 샘플실 리스트 가져오기
        $imsService = SlLoader::cLoad('ims', 'ImsFitSpecService');
        $this->setData('patternFactoryMap', SlCommonUtil::arrayAppKeyValue($imsService->getListSampleRoom(['aChkboxSumSchFactoryType'=>[2]])['list'], 'sno', 'factoryName'));
        $this->setData('sampleFactoryMap', SlCommonUtil::arrayAppKeyValue($imsService->getListSampleRoom(['aChkboxSumSchFactoryType'=>[1]])['list'], 'sno', 'factoryName'));
        //모든 패턴실/샘플실 리스트 가져오기 -> 전화번호 Map -> vue.js 에 넣기
        $this->setData('factoryTelMap', SlCommonUtil::arrayAppKeyValue(DBUtil2::getList(ImsDBName::SAMPLE_FACTORY, '1', '1'), 'sno', 'factoryPhone'));

        //샘플지시서 인쇄페이지 url
        $this->setData('sampleInstructUrl', SlCommonUtil::getHost() . '/ics/ics_sample_instruct.php');
        //샘플확정서 인쇄페이지 url
        $this->setData('sampleConfirmUrl', SlCommonUtil::getHost() . '/ics/ics_sample_confirm.php');

        //환율 가져오기(현재환율)
        $fDollerRatio = SlCommonUtil::getCurrentDollar();
        $this->setData('sCurrDollerRatio', $fDollerRatio);
        $this->setData('sCurrDollerRatioDt', date('Y-m-d'));

        //gnb, lnb 없애는 view단 구성
        $this->getView()->setDefine('layout', 'layout_blank.php');
    }
}