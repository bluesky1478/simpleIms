<?php
namespace Controller\Admin\Ims\Popup;

use Component\Ims\ImsDBName;
use Controller\Admin\Ims\ImsPsNkTrait;
use Request;
use Controller\Admin\Ims\ImsControllerTrait;
use SiteLabUtil\SlCommonUtil;
use SlComponent\Database\DBUtil2;
use SlComponent\Database\SearchVo;
use SlComponent\Util\SlLoader;
use Component\Ims\ImsCodeMap;
use Component\Ims\NkCodeMap;


class ImsPopUpsertMaterialController extends \Controller\Admin\Controller
{
    use ImsControllerTrait;
    use ImsPsNkTrait;

    private $aUpsertFrame = [];
    private $imsNkService;

    public function __construct() {
        parent::__construct();
        //타입에 따라 품목구분 가져오기
        $this->imsNkService = SlLoader::cLoad('imsv2','ImsNkService');
        $aTmp = $this->imsNkService->getListMaterialTypeDetail();
        $aTypeList = [1=>[0=>'선택'],2=>[0=>'선택'],3=>[0=>'선택'],4=>[0=>'선택']];
        foreach ($aTmp['list'] as $key => $val) {
            $aTypeList[$val['materialTypeByDetail']][$val['sno']] = $val['materialTypeText'];
        }
        //매입처 리스트 가져오기
        $imsBasicService = SlLoader::cLoad('ims', 'ImsFitSpecService');
        $aTmpBuyerList = SlCommonUtil::arrayAppKeyValue($imsBasicService->getListSampleRoom(['aChkboxSumSchFactoryType'=>[4], 'sort'=>'factoryName,asc'])['list'], 'sno', 'factoryName');
        $aBuyerList = ['없음', -1=>'신규등록'];
        if (count($aTmpBuyerList) > 0) {
            foreach ($aTmpBuyerList as $key => $val) $aBuyerList[$key] = $val;
        }

        //원단 upsert폼
        $iMType = 1;
        //namku(chk) 장점(merit) 상단에 배열 추가한다면 view페이지에서 foreach ($aUpsertForm as $key => $val) 안에 $key 조건문 바꿔야함(2단 레이아웃 감안)
        $this->aUpsertFrame[$iMType] = [
            ['input_type'=>'', 'default_val'=>0, 'flag_display'=>false, 'db_fld'=>'sno', 'fld_text'=>'', ],
            ['input_type'=>'', 'default_val'=>$iMType, 'flag_display'=>false, 'db_fld'=>'materialType', 'fld_text'=>'', ],
            ['input_type'=>'select', 'default_val'=>0, 'flag_display'=>true, 'db_fld'=>'buyerSno', 'fld_text'=>'매입처 선택', 'options'=>$aBuyerList, 'is_modify'=>true, ],
            ['input_type'=>'text_buyer_info', 'default_val'=>'', 'flag_display'=>true, 'db_fld'=>'factoryName', 'fld_text'=>'매입처정보', ],
            ['input_type'=>'select', 'default_val'=>0, 'flag_display'=>true, 'db_fld'=>'typeDetailSno', 'fld_text'=>'품목구분', 'options'=>$aTypeList[$iMType], 'is_modify'=>true, ],
            ['input_type'=>'_text_with_disabled', 'default_val'=>'', 'flag_display'=>true, 'db_fld'=>'materialTypeText', 'fld_text'=>'품목구분명', ],
            ['input_type'=>'text', 'default_val'=>'', 'flag_display'=>true, 'db_fld'=>'code', 'fld_text'=>'품목코드', ],
            ['input_type'=>'text', 'default_val'=>'', 'flag_display'=>true, 'db_fld'=>'name', 'fld_text'=>'자재명', 'flag_required'=>true],
            ['input_type'=>'text', 'default_val'=>'', 'flag_display'=>true, 'db_fld'=>'mixRatio', 'fld_text'=>'혼용률', ],
            ['input_type'=>'text', 'default_val'=>'', 'flag_display'=>true, 'db_fld'=>'spec', 'fld_text'=>'폭/규격', 'append_str_nm'=>'width_unit', ],
            ['input_type'=>'radio', 'default_val'=>'YD', 'flag_display'=>true, 'db_fld'=>'materialUnit', 'fld_text'=>'단위', 'options'=>NkCodeMap::MATERIAL_UNIT, ],
            ['input_type'=>'text', 'default_val'=>'', 'flag_display'=>true, 'db_fld'=>'weight', 'fld_text'=>'중량 (SQM)', 'append_str_nm'=>'weight_unit', ],
            ['input_type'=>'text', 'default_val'=>'', 'flag_display'=>true, 'db_fld'=>'afterMake', 'fld_text'=>'후가공', ],
            ['input_type'=>'text', 'default_val'=>'', 'flag_display'=>true, 'db_fld'=>'fastness', 'fld_text'=>'견뢰도', ],
            ['input_type'=>'checkbox', 'default_val'=>[], 'flag_display'=>true, 'db_fld'=>'usedStyle', 'fld_text'=>'사용 스타일', 'options'=>NkCodeMap::MATERIAL_USED_STYLE, ],

            ['input_type'=>'text', 'default_val'=>'', 'flag_display'=>true, 'db_fld'=>'ordererItemNo', 'fld_text'=>'발주처 ITEM NO', ],
            ['input_type'=>'text', 'default_val'=>'', 'flag_display'=>true, 'db_fld'=>'ordererItemName', 'fld_text'=>'발주처 ITEM NAME', ],
            ['input_type'=>'radio', 'default_val'=>'kr', 'flag_display'=>true, 'db_fld'=>'makeNational', 'fld_text'=>'생산국', 'options'=>ImsCodeMap::PRD_NATIONAL_CODE, ],
            ['input_type'=>'radio', 'default_val'=>1, 'flag_display'=>true, 'db_fld'=>'currencyUnit', 'fld_text'=>'화폐 단위', 'options'=>NkCodeMap::CURRENCY_UNIT, ],
            ['input_type'=>'text', 'default_val'=>'', 'flag_display'=>true, 'db_fld'=>'unitPrice', 'fld_text'=>'매입 단가', 'append_str_nm'=>'money_unit', ],
            ['input_type'=>'text', 'default_val'=>'', 'flag_display'=>true, 'db_fld'=>'materialTangbi', 'fld_text'=>'탕비', 'append_str_nm'=>'money_unit', ],
            ['input_type'=>'radio', 'default_val'=>'y', 'flag_display'=>true, 'db_fld'=>'btYn', 'fld_text'=>'BT 준비', 'options'=>NkCodeMap::MATERIAL_BT_YN, ],
            ['input_type'=>'text', 'default_val'=>'', 'flag_display'=>true, 'db_fld'=>'btPeriod', 'fld_text'=>'BT기간', 'append_str_nm'=>'text_day', ],
            ['input_type'=>'text', 'default_val'=>'', 'flag_display'=>true, 'db_fld'=>'materialColor', 'fld_text'=>'컬러', ],
            ['input_type'=>'radio', 'default_val'=>'y', 'flag_display'=>true, 'db_fld'=>'onHandYn', 'fld_text'=>'생지 보유', 'options'=>NkCodeMap::MATERIAL_ON_HAND, ],
            ['input_type'=>'text', 'default_val'=>'', 'flag_display'=>true, 'db_fld'=>'moq', 'fld_text'=>'MOQ', ],
            ['input_type'=>'text', 'default_val'=>40, 'flag_display'=>true, 'db_fld'=>'makePeriod', 'fld_text'=>'생산기간(생지有)', 'append_str_nm'=>'text_day', ],
            ['input_type'=>'text', 'default_val'=>60, 'flag_display'=>true, 'db_fld'=>'makePeriodNoOnHand', 'fld_text'=>'생산기간(생지無)', 'append_str_nm'=>'text_day', ],

            ['input_type'=>'_textarea', 'default_val'=>'', 'flag_display'=>true, 'db_fld'=>'merit', 'fld_text'=>'장점', ],
            ['input_type'=>'_textarea', 'default_val'=>'', 'flag_display'=>true, 'db_fld'=>'disadv', 'fld_text'=>'단점', ],
            ['input_type'=>'_textarea', 'default_val'=>'', 'flag_display'=>true, 'db_fld'=>'afterIssue', 'fld_text'=>'납품 후 이슈', ],
            ['input_type'=>'_textarea', 'default_val'=>'', 'flag_display'=>true, 'db_fld'=>'memo', 'fld_text'=>'메모', ],
            ['input_type'=>'radio', 'default_val'=>'1', 'flag_display'=>true, 'db_fld'=>'materialSt', 'fld_text'=>'상태', 'options'=>NkCodeMap::MATERIAL_ST, ],
        ];


        //충전재 upsert폼
        $iMType = 2;
        $this->aUpsertFrame[$iMType] = $this->aUpsertFrame[1];
        $this->aUpsertFrame[$iMType][1]['default_val'] = $iMType;
        $this->aUpsertFrame[$iMType][4]['options'] = $aTypeList[$iMType];

        //부자재 upsert폼
        $iMType = 3;
        $this->aUpsertFrame[$iMType] = $this->aUpsertFrame[1];
        $this->aUpsertFrame[$iMType][1]['default_val'] = $iMType;
        $this->aUpsertFrame[$iMType][4]['options'] = $aTypeList[$iMType];

        //마크 upsert폼
        $iMType = 4;
        $this->aUpsertFrame[$iMType] = $this->aUpsertFrame[1];
        $this->aUpsertFrame[$iMType][1]['default_val'] = $iMType;
        $this->aUpsertFrame[$iMType][4]['options'] = $aTypeList[$iMType];

        //기능 upsert폼
        $iMType = 5;
        $this->aUpsertFrame[$iMType] = $this->aUpsertFrame[1];
        $this->aUpsertFrame[$iMType][1]['default_val'] = $iMType;
        $this->aUpsertFrame[$iMType][4]['options'] = $aTypeList[$iMType];
    }

    public function index() {
        $this->setDefault();

        $iSno = (int)Request::get()->get('sno');
        $iMaterialType = (int)Request::get()->get('type');
        if ($iSno !== 0) {
            $aInfo = DBUtil2::getOne(ImsDBName::MATERIAL, 'sno', $iSno);
            if ($iMaterialType == 0) {
                $iMaterialType = (int)$aInfo['materialType'];
            }
        }

        if ($iMaterialType === 0 || !isset($this->aUpsertFrame[$iMaterialType])) {
            echo "접근오류";
            exit;
        }
        $aFldList = $this->aUpsertFrame[$iMaterialType];
        $aFldListHan = [];
        $aFldListHan['groupSno'] = 0;
        $aFldListHan['imgMaterial'] = $aFldListHan['imgSwatch'] = '';
        if ($iSno !== 0) {
            $aFldListHan['groupSno'] = $aInfo['groupSno'];
            $aFldListHan['imgMaterial'] = $aInfo['imgMaterial'];
            $aFldListHan['imgSwatch'] = $aInfo['imgSwatch'];
            foreach ($aFldList as $key => $val) {
                if (isset($aInfo[$val['db_fld']])) {
                    if ($val['input_type'] == 'radio') {
                        if ($val['db_fld'] == 'makeNational') $aFldListHan[$val['db_fld']] = ImsCodeMap::PRD_NATIONAL_CODE[$aInfo[$val['db_fld']]];
                        else if ($val['db_fld'] == 'btYn') $aFldListHan[$val['db_fld']] = NkCodeMap::MATERIAL_BT_YN[$aInfo[$val['db_fld']]];
                        else if ($val['db_fld'] == 'onHandYn') $aFldListHan[$val['db_fld']] = NkCodeMap::MATERIAL_ON_HAND[$aInfo[$val['db_fld']]];
                        else if ($val['db_fld'] == 'materialSt') $aFldListHan[$val['db_fld']] = NkCodeMap::MATERIAL_ST[$aInfo[$val['db_fld']]];
                        else if ($val['db_fld'] == 'currencyUnit') $aFldListHan[$val['db_fld']] = NkCodeMap::CURRENCY_UNIT[$aInfo[$val['db_fld']]];
                        else $aFldListHan[$val['db_fld']] = $aInfo[$val['db_fld']];
                    }
                    if ($val['db_fld'] == 'usedStyle') {
                        $aFldList[$key]['default_val'] = $this->convertCheckboxSumToArr(NkCodeMap::MATERIAL_USED_STYLE, (int)$aInfo[$val['db_fld']]);
                        $aFldListHan[$val['db_fld']] = implode(', ', $this->convertCheckboxSumToArr(NkCodeMap::MATERIAL_USED_STYLE, (int)$aInfo[$val['db_fld']], 'text'));
                    } else {
                        $aFldList[$key]['default_val'] = str_replace(["\n","\r","\r\n"],'\n',addslashes($aInfo[$val['db_fld']]));
                    }
                }
            }
            //매입처 선택한 경우 매입처명 가져오기
            if ($aInfo['buyerSno'] != 0) {
                $aBuyerInfo = DBUtil2::getOne(ImsDBName::SAMPLE_FACTORY, 'sno', $aInfo['buyerSno']);
                foreach ($aFldList as $key => $val) {
                    if ($val['db_fld'] == 'factoryName') $aFldList[$key]['default_val'] = $aBuyerInfo['factoryName'];
                }
            }
            //품목구분 선택한 경우 품목구분명 가져오기
            if ($aInfo['typeDetailSno'] != 0) {
                $aBuyerInfo = DBUtil2::getOne(ImsDBName::MATERIAL_TYPE_DETAIL, 'sno', $aInfo['typeDetailSno']);
                foreach ($aFldList as $key => $val) {
                    if ($val['db_fld'] == 'materialTypeText') $aFldList[$key]['default_val'] = $aBuyerInfo['materialTypeText'];
                }
            }

        }
        $this->setData('aUpsertForm', $aFldList);
        $this->setData('aUpsertFormHan', $aFldListHan);
        $this->setData('aTextTitle', NkCodeMap::MATERIAL_TYPE[$iMaterialType]);
        $this->setData('aTextMode', $iSno === 0 ? '등록' : '수정');

        //gnb, lnb 없애는 view단 구성
        $this->getView()->setDefine('layout', 'layout_blank.php');
    }
}