<?php
namespace Controller\Admin\Ims\Popup;

use Component\Ims\ImsDBName;
use Request;
use Controller\Admin\Ims\ImsControllerTrait;
use SiteLabUtil\SlCommonUtil;
use SlComponent\Database\DBUtil2;
use SlComponent\Database\SearchVo;
use SlComponent\Util\ExcelCsvUtil;
use SlComponent\Util\PhpExcelUtil;
use SlComponent\Util\SlLoader;

class ImsPopCheckCustomerDeliveryController extends \Controller\Admin\Controller
{
    use ImsControllerTrait;

    public function index() {
        //분류패킹 엑셀 다운로드
        $iExcelDownload = (int)Request::get()->get('excel_download');
        if($iExcelDownload === 1) {
            $iPackingSno = (int)Request::get()->get('packing_sno');
            if ($iPackingSno === 0) {
                echo "접근오류";
                exit;
            }
            $this->excelDownload($iPackingSno);
            exit();
        }

        //송장번호 update start
        $aAppendFile = Request::files()->toArray();
        $sResultMsgExcelUpload = '';
        $aResultMsgExcelUploadSuccess = $aResultMsgExcelUploadDup = $aResultMsgExcelUploadNotSelect = $aResultMsgExcelUploadFail =  $aDeliveryNums = [];
        if (count($aAppendFile) > 0) {
            $iPackingSno = (int)Request::post()->get('packing_sno');
            $sDeliveryCompanyCode = Request::post()->get('delivery_company');
            $aDeliveryReceiverSnos = explode(', ', Request::post()->get('delivery_receivers'));
            if ($iPackingSno === 0 || $sDeliveryCompanyCode == '' || count($aDeliveryReceiverSnos) == 0) {
                echo "접근오류";
                exit;
            }

            $fileTableInfo=[
                'a' => ['data' => [ ImsDBName::CUSTOMER_RECEIVER_DELIVERY ], 'field' => ["a.*"]],
                'b' => ['data' => [ ImsDBName::CUSTOMER_RECEIVER, 'LEFT OUTER JOIN', 'a.receiverSno = b.sno' ], 'field' => ["b.branchName"]],
            ];
            $aDeliveryList = DBUtil2::getComplexList(DBUtil2::setTableInfo($fileTableInfo,false), new SearchVo('packingSno=?', $iPackingSno), false, false, true);
            $aDeliverySnoByInfo = $aDeliverySnoByInfoDetail = $aInvoiceNumBySno = [];
            foreach ($aDeliveryList as $val) {
                $aTmp = explode(' ', $val['managerAddr']);
                $aDeliverySnoByInfo[$val['managerName'].'___'.$aTmp[0].' '.$aTmp[1].' '.$aTmp[2].' '.$aTmp[3]] = $val['sno'];
                $aDeliverySnoByInfoDetail[$val['managerName'].'___'.$aTmp[0].' '.$aTmp[1].' '.$aTmp[2].' '.$aTmp[3].'___'.$val['branchName']] = $val['sno'];
                $aInvoiceNumBySno[$val['sno']] = [];
            }

            $aExcelList = PhpExcelUtil::readToArray($aAppendFile);
            $iTotalRow = $iStartRow = $iInvoiceFld = $iNameFld = $iAddrFld = $iBranchFld = 0;
            foreach ($aExcelList as $key => $val) {
                if ($val[2] == '' && $val[3] == '') continue;
                else {
                    if ($iStartRow === 0) { //타이틀ROW
                        $iStartRow = $key + 1;
                        foreach ($val as $key2 => $val2) {
                            if (strpos($val2, '송장번호') !== false) $iInvoiceFld = $key2;
                            else if ($val2 === '받는분' || $val2 === '수령자') $iNameFld = $key2;
                            else if (strpos($val2, '주소') !== false) $iAddrFld = $key2;
                            else if (strpos($val2, '지점') !== false || strpos($val2, '부서') !== false) $iBranchFld = $key2;
                        }
                    } else {
                        $iTotalRow++;
                    }
                }
            }

            if ($iInvoiceFld === 0 || $iNameFld === 0 || $iAddrFld === 0) {
                $sResultMsgExcelUpload = "엑셀형식이 올바르지 않습니다. '송장번호', '받는분'(또는 수령자), '주소' 필드가 존재하는 엑셀파일을 업로드 하시기 바랍니다.";
            } else {
                foreach ($aExcelList as $key => $val) {
                    if ($key >= $iStartRow) {
                        if (in_array($val[$iInvoiceFld], $aDeliveryNums)) {
                            $aResultMsgExcelUploadDup[] = ['name'=>$val[$iNameFld], 'addr'=>$val[$iAddrFld], 'invoice'=>$val[$iInvoiceFld], 'branch'=>$iBranchFld==0?'':$val[$iBranchFld]];
                        } else {
                            $aTmp = explode(' ', $val[$iAddrFld]);
                            if ($iBranchFld !== 0) { //지점,부서정보까지 있는 엑셀
                                if (isset($aDeliverySnoByInfoDetail[$val[$iNameFld].'___'.$aTmp[0].' '.$aTmp[1].' '.$aTmp[2].' '.$aTmp[3].'___'.$val[$iBranchFld]])) {
                                    $iDeliverySno = $aDeliverySnoByInfoDetail[$val[$iNameFld].'___'.$aTmp[0].' '.$aTmp[1].' '.$aTmp[2].' '.$aTmp[3].'___'.$val[$iBranchFld]];
                                } else $iDeliverySno = 0;
                            } else { //받는분, 주소정보까지 있는 엑셀
                                if (isset($aDeliverySnoByInfo[$val[$iNameFld].'___'.$aTmp[0].' '.$aTmp[1].' '.$aTmp[2].' '.$aTmp[3]])) {
                                    $iDeliverySno = $aDeliverySnoByInfo[$val[$iNameFld].'___'.$aTmp[0].' '.$aTmp[1].' '.$aTmp[2].' '.$aTmp[3]];
                                } else $iDeliverySno = 0;
                            }

                            if ($iDeliverySno !== 0) {
                                if (in_array($iDeliverySno, $aDeliveryReceiverSnos)) {
                                    $aInvoiceNumBySno[$iDeliverySno][] = $val[$iInvoiceFld];
                                    $aResultMsgExcelUploadSuccess[] = ['name'=>$val[$iNameFld], 'addr'=>$val[$iAddrFld], 'invoice'=>$val[$iInvoiceFld], 'branch'=>$iBranchFld==0?'':$val[$iBranchFld]];
                                } else {
                                    $aResultMsgExcelUploadNotSelect[] = ['name'=>$val[$iNameFld], 'addr'=>$val[$iAddrFld], 'invoice'=>$val[$iInvoiceFld], 'branch'=>$iBranchFld==0?'':$val[$iBranchFld]];
                                }
                            } else {
                                $aResultMsgExcelUploadFail[] = ['name'=>$val[$iNameFld], 'addr'=>$val[$iAddrFld], 'invoice'=>$val[$iInvoiceFld], 'branch'=>$iBranchFld==0?'':$val[$iBranchFld]];
                            }
                            $aDeliveryNums[] = $val[$iInvoiceFld];
                        }
                    }
                }
                foreach ($aInvoiceNumBySno as $key => $val) {
                    if (is_array($val) && count($val) > 0) {
                        DBUtil2::update(ImsDBName::CUSTOMER_RECEIVER_DELIVERY, ['invoiceNums'=>implode(', ', $val), 'deliveryCompanyCode'=>$sDeliveryCompanyCode], new SearchVo('sno=?', $key));
                    }
                }

                $sResultMsgExcelUpload = "전체 : ".number_format($iTotalRow)."건<br/>성공 : ".count($aResultMsgExcelUploadSuccess)."건<br/>실패(존재하는 배송지점이지만 미선택) : ".count($aResultMsgExcelUploadNotSelect)."건<br/>실패(중복된 송장번호) : ".count($aResultMsgExcelUploadDup)."건<br/>실패(담당자(배송지점) 정보 미매칭) : ".count($aResultMsgExcelUploadFail)."건";
            }
        }
        //송장번호 update end

        $this->setDefault();

        $this->setData('sResultMsgExcelUpload', $sResultMsgExcelUpload);
        $this->setData('aResultMsgExcelUploadSuccess', $aResultMsgExcelUploadSuccess);
        $this->setData('aResultMsgExcelUploadDup', $aResultMsgExcelUploadDup);
        $this->setData('aResultMsgExcelUploadNotSelect', $aResultMsgExcelUploadNotSelect);
        $this->setData('aResultMsgExcelUploadFail', $aResultMsgExcelUploadFail);
        $this->setData('iPackingSno', (int)Request::get()->get('sno'));

        $this->getView()->setDefine('layout', 'layout_blank.php');
    }

    //엑셀 다운로드
    public function excelDownload($iPackingSno) {
        $fileTableInfo=[
            'a' => ['data' => [ ImsDBName::CUSTOMER_RECEIVER_DELIVERY ], 'field' => ["a.*"]],
            'b' => ['data' => [ ImsDBName::CUSTOMER_RECEIVER, 'LEFT OUTER JOIN', 'a.receiverSno = b.sno' ], 'field' => ["b.branchName, b.departmentName"]],
            'c' => ['data' => [ ImsDBName::CUSTOMER_PACKING, 'LEFT OUTER JOIN', 'a.packingSno = c.sno' ], 'field' => ["c.packingSt"]],
            'd' => ['data' => [ ImsDBName::CUSTOMER, 'LEFT OUTER JOIN', 'c.customerSno = d.sno' ], 'field' => ["d.customerName"]],
        ];
        $aList = DBUtil2::getComplexList(DBUtil2::setTableInfo($fileTableInfo,false), new SearchVo('a.packingSno=?', $iPackingSno), false, false, true);
        if (count($aList) === 0) {
            echo "배송지점이 없습니다.";
            exit;
        }

        $aDefaultFormByAssortNm = [];
        foreach ($aList as $val) {
            $aTmpJson = json_decode($val['jsonContents'], true);
            foreach ($aTmpJson as $val2) {
                foreach ($val2['aAssortList'] as $val3) {
                    if (!isset($aDefaultFormByAssortNm[$val2['styleSno'].'___'.$val3['assortType'].'___'.$val3['assortCharge']])) $aDefaultFormByAssortNm[$val2['styleSno'].'___'.$val3['assortType'].'___'.$val3['assortCharge']] = ['styleName'=>$val2['styleName'], 'sizeList'=>array_keys($val3['oSizeList'])];
                }
            }
        }
        ksort($aDefaultFormByAssortNm);
        $aSumByAssortSize = [];
        $aSumByStyle = [];
        foreach ($aList as $key => $val) {
            foreach ($aDefaultFormByAssortNm as $key2 => $val2) {
                foreach ($val2['sizeList'] as $val3) {
                    $aList[$key][$key2][$val3] = 0;
                    if (!isset($aSumByAssortSize[$key2][$val3])) $aSumByAssortSize[$key2][$val3] = 0;
                }
                if (!isset($aSumByStyle[explode('___', $key2)[0]])) $aSumByStyle[explode('___', $key2)[0]] = 0;
            }
            $aTmpJson = json_decode($val['jsonContents'], true);
            foreach ($aTmpJson as $val2) {
                foreach ($val2['aAssortList'] as $val3) {
                    foreach ($val3['oSizeList'] as $key4 => $val4) {
                        $aList[$key][$val2['styleSno'].'___'.$val3['assortType'].'___'.$val3['assortCharge']][$key4] += (int)$val4['expectQty'];
                        $aSumByAssortSize[$val2['styleSno'].'___'.$val3['assortType'].'___'.$val3['assortCharge']][$key4] += (int)$val4['expectQty'];
                        $aSumByStyle[$val2['styleSno']] += (int)$val4['expectQty'];
                    }
                }
            }
        }

        //$aDefaultFormByAssortNm[스타일sno+아소트구분+아소트청구] = ['styleName'=>'', 'sizeList'=>[90,95,100,~~~]];
        //$aList[key담당자][스타일sno+아소트구분+아소트청구][사이즈값] = 값

        $sExcelFileName = $aList[0]['customerName'].'_출고패킹리스트_'.date('Y-m-d');
        //title
        $contents = [];
        $iAllColspan = 6;
        foreach ($aDefaultFormByAssortNm as $val) $iAllColspan += count($val['sizeList']);
        $contentsRows = ['<td colspan="'.$iAllColspan.'" style="line-height:25px; font-size:18px; font-weight:bold; text-align:center;">'.$sExcelFileName.'<br/><span style="color:red;">배송지 주소 같아도 수령자 별 패킹 必<br/>서울 본사는 화물발송 예정</span></td>'];
        //th
        $contents[] = '<tr>'.implode('',$contentsRows).'</tr>';
        $contentsRows = ['<th rowspan="2" style="background-color:#E6E6E6;">번호</th>'];
        foreach ($aDefaultFormByAssortNm as $val) {
            $contentsRows[] = '<th colspan="'.count($val['sizeList']).'" style="background-color:#E6E6E6;">'.$val['styleName'].'</th>';
        }
        $contentsRows = array_merge($contentsRows, ['<th rowspan="2" style="width:80px; background-color:#E6E6E6;">합계</th>', '<th rowspan="2" style="background-color:#E6E6E6;">지점/부서</th>', '<th rowspan="2" style="width:100px; background-color:#E6E6E6;">수령자</th>', '<th rowspan="2" style="background-color:#E6E6E6;">연락처</th>', '<th rowspan="2" style="background-color:#E6E6E6;">배송지 주소</th>']);
        $contents[] = '<tr>'.implode('',$contentsRows).'</tr>';
        $contentsRows = [];
        foreach ($aDefaultFormByAssortNm as $val) {
            foreach ($val['sizeList'] as $val2) {
                $contentsRows[] = '<th style="width:40px; background-color:#E6E6E6;">'.$val2.'</th>';
            }
        }
        $contents[] = '<tr>'.implode('',$contentsRows).'</tr>';
        //td
        foreach ($aList as $key => $val) {
            $iSum = 0;
            $contentsRows = ['<td>'.($key+1).'</td>'];
            foreach ($aDefaultFormByAssortNm as $key2 => $val2) {
                foreach ($val2['sizeList'] as $val3) {
                    $contentsRows[] = $val[$key2][$val3] == 0 ? '<td></td>' : '<td style="text-align:center;">'.number_format($val[$key2][$val3]).'</td>';
                    $iSum += $val[$key2][$val3];
                }
            }
            $contentsRows = array_merge($contentsRows, ['<td style="color:red; font-weight:bold; text-align:center;">'.number_format($iSum).'</td>', '<td>'.$val['branchName'].' '.$val['departmentName'].'</td>', '<td style="text-align:center;">'.$val['managerName'].'</td>', '<td>'.$val['managerPhone'].'</td>', '<td>'.$val['managerAddr'].'</td>']);
            $contents[] = '<tr>'.implode('',$contentsRows).'</tr>';
        }
        //사이즈별 합계
        $iSum = 0;
        $contentsRows = ['<td rowspan="2" style="background-color:#E6E6E6;">TOTAL</td>'];
        foreach ($aDefaultFormByAssortNm as $key2 => $val2) {
            foreach ($val2['sizeList'] as $val3) {
                $contentsRows[] = $aSumByAssortSize[$key2][$val3] == 0 ? '<td style="background-color:#E6E6E6;"></td>' : '<td style="background-color:#E6E6E6;">'.number_format($aSumByAssortSize[$key2][$val3]).'</td>';
                $iSum += $aSumByAssortSize[$key2][$val3];
            }
        }
        $contentsRows = array_merge($contentsRows, ['<td style="background-color:#E6E6E6; color:red; font-weight:bold; text-align:center;">'.number_format($iSum).'</td>', '<td rowspan="2" colspan="4" style="background-color:#E6E6E6;"></td>']);
        $contents[] = '<tr>'.implode('',$contentsRows).'</tr>';
        //스타일별 합계
        $iSum = 0;
        $contentsRows = [];
        foreach ($aDefaultFormByAssortNm as $key2 => $val2) {
            $contentsRows[] = $aSumByStyle[explode('___', $key2)[0]] == 0 ? '<td colspan="'.count($val2['sizeList']).'" style="background-color:#E6E6E6;"></td>' : '<td colspan="'.count($val2['sizeList']).'" style="background-color:#E6E6E6; text-align:center;">'.number_format($aSumByStyle[explode('___', $key2)[0]]).'</td>';
            $iSum += $aSumByStyle[explode('___', $key2)[0]];
        }
        $contentsRows = array_merge($contentsRows, ['<td style="background-color:#E6E6E6; color:red; font-weight:bold; text-align:center;">'.number_format($iSum).'</td>']);
        $contents[] = '<tr>'.implode('',$contentsRows).'</tr>';

        $simpleExcelComponent = SlLoader::cLoad('Excel','SimpleExcelComponent','sl');
        $simpleExcelComponent->simpleDownload($sExcelFileName, [], implode('',$contents));
    }
}