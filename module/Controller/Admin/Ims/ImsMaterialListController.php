<?php

namespace Controller\Admin\Ims;

use App;
use Component\Category\BrandAdmin;
use Component\Category\CategoryAdmin;
use Component\Database\DBTableField;
use Component\Ims\EnumType\PREPARED_TYPE;
use Component\Ims\ImsCodeMap;
use Component\Ims\ImsDBName;
use Component\Ims\ImsJsonSchema;
use Component\Ims\NkCodeMap;
use Component\Stock\StockListService;
use Component\Work\WorkCodeMap;
use Controller\Admin\Erp\AdminErpControllerTrait;
use Controller\Admin\Erp\ControllerService\InoutListService;
use Globals;
use Request;
use SiteLabUtil\SlCommonUtil;
use SlComponent\Database\DBUtil2;
use SlComponent\Database\SearchVo;
use SlComponent\Util\ExcelCsvUtil;
use SlComponent\Util\PhpExcelUtil;
use SlComponent\Util\SlCodeMap;
use SlComponent\Util\SlLoader;
use function SlComponent\Util\SlLoader;

//자재정보 리스트
class ImsMaterialListController extends \Controller\Admin\Controller{

    use ImsPsNkTrait;
    use ImsControllerTrait;

    private $imsNkService;

    public function __construct() {
        parent::__construct();
        $this->imsNkService = SlLoader::cLoad('imsv2', 'imsNkService');
    }

    public function index() {
        //엑셀 다운로드
        $request = \Request::request()->toArray();
        if(isset($request['simple_excel_download']) && $request['simple_excel_download'] == 1) {
            $request['page'] = 1;
            $request['pageNum'] = 15000;
            $request['multiKey'] = json_decode($request['multiKey'], true);
            $request['aChkboxSchMaterialType'] = json_decode($request['aChkboxSchMaterialType'], true);
            $request['aChkboxSchMakeNational'] = json_decode($request['aChkboxSchMakeNational'], true);
            $request['aChkboxSumSchUsedStyle'] = json_decode($request['aChkboxSumSchUsedStyle'], true);
            $request['aChkboxSchOnHandYn'] = json_decode($request['aChkboxSchOnHandYn'], true);
            unset($request['simple_excel_download']);

            $this->simpleExcelDownload($request);
            exit();
        }
        $sExcelUploadResultHtml = '';
        //엑셀 업로드
        $aAppendFile = Request::files()->toArray();
        if (count($aAppendFile) > 0) {
            $iUploadType = 0;
            if (isset(Request::post()->toArray()['update_type'])) $iUploadType = (int)Request::post()->toArray()['update_type'];
            if ($iUploadType === 0) {
                $sExcelUploadResultHtml = "업로드할 엑셀파일을 첨부하세요";
            } else {
                $aTmpBuyerList = DBUtil2::getListBySearchVo(ImsDBName::SAMPLE_FACTORY, new SearchVo());
                $aBuyerList = [];
                foreach ($aTmpBuyerList as $val) $aBuyerList[trim($val['factoryName'])] = $val['sno'];
                $aTmpTypeDetailList = DBUtil2::getListBySearchVo(ImsDBName::MATERIAL_TYPE_DETAIL, new SearchVo());
                $aTypeDetailList = [];
                foreach ($aTmpTypeDetailList as $val) $aTypeDetailList[trim($val['materialTypeText'])] = $val['sno'];

                $aExcelList = PhpExcelUtil::readToArray($aAppendFile, 1);
                $sExcelUploadResultHtml = count($aExcelList).' 자재건 중 ';

                //없는 매입처 미리 등록해놓기
                $sCurrDt = date('Y-m-d H:i:s');
                foreach ($aExcelList as $val) {
                    if ($val[2] != '') {
                        if (!isset($aBuyerList[trim($val[2])])) {
                            $aBuyerList[trim($val[2])] = DBUtil2::insert(ImsDBName::SAMPLE_FACTORY, ['factoryName'=>trim($val[2]), 'factoryType'=>4, 'regDt'=>$sCurrDt]);
                        }
                    }
                }

                //insert배열 정리
                $bFlagErr = false;
                if ($iUploadType === 1) {
                    if (!isset($aExcelList[0]) || (count($aExcelList[0]) !== 27 && count($aExcelList[0]) !== 26)) {
                        $sExcelUploadResultHtml = "엑셀양식이 올바르지 않습니다. (필드갯수 안맞음)";
                        $bFlagErr = true;
                    }
                    $aInsertFlds = [
                        'code','buyerSno','typeDetailSno','name','ordererItemNo','ordererItemName','mixRatio',
                        'spec','materialUnit','unitPrice','makeNational', 'moq','materialTangbi','weight','afterMake','fastness','btYn','btPeriod',
                        'onHandYn','makePeriod','makePeriodNoOnHand','usedStyle','merit','disadv','afterIssue','memo',
                    ];
                } else {
                    if (!isset($aExcelList[0]) || (count($aExcelList[0]) !== 19 && count($aExcelList[0]) !== 18)) {
                        $sExcelUploadResultHtml = "엑셀양식이 올바르지 않습니다. (필드갯수 안맞음)";
                        $bFlagErr = true;
                    }
                    $aInsertFlds = [
                        'code','buyerSno','typeDetailSno','name','ordererItemNo','ordererItemName','mixRatio','materialColor','spec','materialUnit','unitPrice',
                        'makeNational','moq','makePeriod','merit','disadv','afterIssue','memo',
                    ];
                }

                if ($bFlagErr === false) {
                    $aInsert = $aErrDesc = [];
                    $sErrDesc = '';
                    foreach ($aExcelList as $val) {
                        if ($val[4] != '') {
                            $iKey = 0;
                            $bFlagPass = false;
                            $aTmp = ['sno'=>0, 'materialType'=>$iUploadType];
                            foreach ($val as $val2) {
                                if (isset($aInsertFlds[$iKey])) {
                                    $sData = trim($val2);
                                    $mData = $this->convertUploadExcelValue($aInsertFlds[$iKey], $sData, $aBuyerList, $aTypeDetailList, $sErrDesc);
                                    if ($mData === false) {
                                        $sErrDesc = $val[4].'(품목코드:'.$val[1].') 다음의 이유로 등록불가 - '.$sErrDesc;
                                        $aErrDesc[] = $sErrDesc;
                                        $sErrDesc = '';
                                        $bFlagPass = true;
                                        break;
                                    }
                                    $aTmp[$aInsertFlds[$iKey]] = $mData;
                                }
                                $iKey++;
                            }
                            if ($bFlagPass === false) $aInsert[] = $aTmp;
                        }
                    }
                    //insert
                    if (count($aInsert) > 0) {
                        foreach ($aInsert as $val) {
                            $this->setMaterialNk(['data'=>$val]);
                        }
                    }
                    $sExcelUploadResultHtml .= count($aInsert).' 건 등록 완료<br/><br/>';
                    if (count($aErrDesc) > 0) $sExcelUploadResultHtml .= implode('<br/>', $aErrDesc);
                }
            }
        }

        $this->callMenu('ims', 'customer', 'material');
        $this->setDefault();

        $this->setData('sExcelUploadResultHtml', $sExcelUploadResultHtml);

        //검색항목
        $search['combineSearch'] = [
            'a.code' => '품목코드',
            'cust.factoryName' => '매입처',
            'b.materialTypeText' => '품목구분',
            'a.name' => '자재명',
            'a.mixRatio' => '혼용률',
        ];
        $this->setData('search', $search);
    }

    /**
     * 엑셀 다운로드
     */
    public function simpleExcelDownload($request) {
        $aTmpDpData = DBTableField::callTableFunction(ImsDBName::MATERIAL);
        $dpData = [];
        foreach ($aTmpDpData as $key => $val) {
            if ($val['val'] == 'buyerSno') $dpData['customerName'] = ['name'=>$val['name']]; //매입처
            else if ($val['val'] == 'materialType') $dpData[$val['val'].'Han'] = ['name'=>$val['name']]; //타입
            else if ($val['val'] == 'typeDetailSno') $dpData['materialTypeText'] = ['name'=>$val['name']]; //품목구분
            else $dpData[$val['val']] = ['name'=>$val['name']];
        }
        unset($dpData['regManagerSno'], $dpData['sno']);
        $dpData = array_merge(['no'=>['name'=>'번호','skip'=>true]], $dpData);
        $title = [];
        foreach($dpData as $dpKey => $dpValue){
            if ($dpKey != 'makePeriodNoOnHand') {
                $title[] = $dpValue['name'];
            }
        }

        $list = $this->imsNkService->getListMaterial($request);
        foreach ($list['list'] as $key => $val) {
            $list['list'][$key]['usedStyle'] = implode(',',$this->convertCheckboxSumToArr(NkCodeMap::MATERIAL_USED_STYLE, $val['usedStyle'], 'str'));
        }
        $contents = [];
        foreach($list['list'] as $key => $val) {
            $contentsRows = [];
            $contentsRows[] = ExcelCsvUtil::wrapTd($key+1);
            foreach($dpData as $dpKey => $dpValue) {
                if(true !== $dpValue['skip']) {
                    if ($dpKey != 'makePeriodNoOnHand') {
                        $contentsRows[] = ExcelCsvUtil::wrapTd($val[$dpKey]);
                    }
                }
            }
            $contents[] = '<tr>'.implode('',$contentsRows).'</tr>';
        }
        $simpleExcelComponent = SlLoader::cLoad('Excel','SimpleExcelComponent','sl');
        $simpleExcelComponent->simpleDownload('자재리스트', $title, implode('',$contents));
    }
    //업로드엑셀파일에서 받은 값을 DB에 넣을 수 있도록 값 정제
    private function convertUploadExcelValue($sTargetFldNm, $sData, $aBuyerList, $aTypeDetailList, &$sErrDesc) {
        $mReturn = $sData;
        if (in_array($mReturn, ['해당 없음','해당없음','x','X'])) $mReturn = null;
        else {
            if ($sTargetFldNm == 'buyerSno') {
                $mReturn = $aBuyerList[$mReturn];
            } else if ($sTargetFldNm == 'typeDetailSno') {
                if (isset($aTypeDetailList[$mReturn])) $mReturn = $aTypeDetailList[$mReturn];
                else {
                    $sErrDesc = '품목구분 '.$mReturn.' 미존재';
                    //존재하지 않는 품목구분일때 insert안하고 결과알림
                    $mReturn = false;
                }
            } else if ($sTargetFldNm == 'makeNational') {
                $mNationalCode = array_search($mReturn, ImsCodeMap::PRD_NATIONAL_CODE);
                if ($mNationalCode !== false) $mReturn = $mNationalCode;
                else {
                    $sErrDesc = '생산국가 '.$mReturn.' 미존재';
                    //국가(한국,중국,베트남) 없으면 insert안하고 결과알림
                    $mReturn = false;
                }
            } else if ($sTargetFldNm == 'usedStyle') {
                $aTmpStyle = explode(',', $mReturn);
                $aTmpStyleInt = [];
                $mReturn = 0;
                foreach ($aTmpStyle as $key => $val) {
                    $val = trim($val);
                    if ($val != '') {
                        $mIntStyle = array_search($val, NkCodeMap::MATERIAL_USED_STYLE);
                        if ($mIntStyle === false) {
                            $sErrDesc = '사용스타일 '.$val.' 미존재';
                            //존재하지 않는 사용스타일이 있는 경우에 insert안하고 결과알림
                            $mReturn = false;
                            break;
                        } else $aTmpStyleInt[] = (int)$mIntStyle;
                    }
                }
                if ($mReturn !== false && count($aTmpStyleInt) > 0) $mReturn = array_sum($aTmpStyleInt);


            } else if ($sTargetFldNm == 'onHandYn') {
                $mReturn = strtolower($mReturn);
                if (!isset(NkCodeMap::MATERIAL_ON_HAND[$mReturn])) $mReturn = 'n';
            } else if ($sTargetFldNm == 'btYn') {
                $mReturn = strtolower($mReturn);
                if (!isset(NkCodeMap::MATERIAL_BT_YN[$mReturn])) $mReturn = 'n';
            } else if ($sTargetFldNm == 'fastness') $mReturn = str_replace(['(VAT 가공)'],'',$mReturn);
            else if (in_array($sTargetFldNm, ['moq', 'weight'])) $mReturn = str_replace(['G','g','확인중',','],'',$mReturn);
            else if (in_array($sTargetFldNm, ['unitPrice', 'materialTangbi'])) $mReturn = str_replace(['$',"\\",','],'',$mReturn);
            else if (in_array($sTargetFldNm, ['makePeriod', 'makePeriodNoOnHand', 'btPeriod'])) $mReturn = str_replace(['일'],'',$mReturn);
        }

        return $mReturn;
    }
}