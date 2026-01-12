<?php
namespace Component\Ims;

use App;
use Component\Database\DBTableField;
use Component\Work\WorkCodeMap;
use SiteLabUtil\SlCommonUtil;
use SlComponent\Database\DBUtil2;
use SlComponent\Database\SearchVo;

use Controller\Admin\Ims\ImsPsNkTrait;
use Component\Ims\ImsServiceSortNkTrait;
use SlComponent\Util\SitelabLogger;
use SlComponent\Util\SlLoader;

//분류패킹 입고설정, 출고, 실제입고
class ImsCustomerReceiverService {
    use ImsPsNkTrait;
    use ImsServiceSortNkTrait;

    private $dpData;

    public function __construct(){
        $this->dpData = [
            ['type' => 'c', 'col' => 10, 'class' => '', 'name' => 'branchName', 'title' => '지점', ],
            ['type' => 'c', 'col' => 10, 'class' => '', 'name' => 'departmentName', 'title' => '부서', ],
            ['type' => 'c', 'col' => 10, 'class' => '', 'name' => 'managerName', 'title' => '담당자', ],
            ['type' => 'c', 'col' => 12, 'class' => '', 'name' => 'managerEmail', 'title' => '이메일', ],
            ['type' => 'c', 'col' => 12, 'class' => '', 'name' => 'managerPhone', 'title' => '연락처', ],
            ['type' => 'postcode', 'col' => 12, 'class' => '', 'name' => 'managerAddrPost', 'title' => '우편번호', ],
            ['type' => 'addr', 'col' => 0, 'class' => '', 'name' => 'managerAddr', 'title' => '주소', ],
        ];
    }

    public function getDisplay(){
        return $this->dpData;
    }

    public function getListCustomerReceiver($params) {
        $params['sRadioSchCustomerSno'] = 6; //namkuuu 후순위 작업. 향후 DB or 세션에서 가져와야함

        $sTableNm = ImsDBName::CUSTOMER_RECEIVER;
        $tableInfo=[
            'a' => ['data' => [ $sTableNm ], 'field' => ["a.*"]],
        ];
        if (!isset($params['upsertSnoGet'])) { //리스트를 return받으려는 경우
            //리스트에서 뿌려줄 필드리스트 정리
            $aFldList = $this->getDisplay();
        } else $aFldList = [];
        $aReturn = $this->fnRefineListUpsertForm($params, $sTableNm, $tableInfo, $aFldList);

        return $aReturn;
    }

    public function setCustomerReceiver($params) {
        $iCustomerSno = 6; //namkuuu 후순위 작업. 향후 DB or 세션에서 가져와야함

        $iResgisterSno = \Session::get('manager.sno');
        $sCurrDt = date('Y-m-d H:i:s');
        foreach ($params['list'] as $key => $val) {
            $iSno = (int)$val['sno'];
            unset($val['sno']);
            if ($iSno === 0) {
                $val['customerSno'] = $iCustomerSno;
                $val['regManagerSno'] = $iResgisterSno;
                $val['sortNum'] = $key + 1;
                $val['regDt'] = $sCurrDt;

                DBUtil2::insert(ImsDBName::CUSTOMER_RECEIVER, $val);
            } else {
                $val['sortNum'] = $key + 1;
                $val['modDt'] = $sCurrDt;

                DBUtil2::update(ImsDBName::CUSTOMER_RECEIVER, $val, new SearchVo('sno=?', $iSno));
            }
        }
    }

    public function registCustomerReceiverDelivery($params) {
        $iCustomerSno = 6; //namkuuu 후순위 작업. 향후 DB or 세션에서 가져와야함

        $iPackingSno = (int)$params['packingSno'];
        $aReceiverSnos = $params['list'];
        if ($iPackingSno === 0 || !is_array($aReceiverSnos) || count($aReceiverSnos) === 0) return ['data'=>'접근오류'];
        //분류패킹master 가져오기
        $oSVPacking = new SearchVo('sno=?', $iPackingSno);
        $oSVPacking->setWhere('customerSno = '.$iCustomerSno);
        $aPackingInfo = DBUtil2::getOneBySearchVo(ImsDBName::CUSTOMER_PACKING, $oSVPacking);
        if (!isset($aPackingInfo['sno']) || $aPackingInfo['styleSnos'] == '') return ['data'=>'유효하지 않은 분류패킹입니다.'];
        //고객사관리자가 선택한 담당자정보 가져오기
        $oSV = new SearchVo();
        $oSV->setWhere("sno in (".implode(",",$aReceiverSnos).")");
        $aReceiverList = DBUtil2::getListBySearchVo(ImsDBName::CUSTOMER_RECEIVER, $oSV);
        if (count($aReceiverList) === 0) return ['data'=>'유효하지 않은 담당자입니다.'];
        //스타일리스트(고객에게만 보여주는) 가져오기
        $oSVStyle = new SearchVo();
        $oSVStyle->setWhere("sno in (".$aPackingInfo['styleSnos'].")");
        $aStyleList = DBUtil2::getListBySearchVo(ImsDBName::PRODUCT, $oSVStyle);
        $aStyleNameBySno = [];
        foreach ($aStyleList as $val) $aStyleNameBySno[$val['sno']] = $val['productName'];

        //해당 발주건의 분류패킹 담당자들 비운다
        DBUtil2::delete(ImsDBName::CUSTOMER_RECEIVER_DELIVERY, new SearchVo('packingSno=?', $iPackingSno));

        $aTmpCustomerAssortList = json_decode($aPackingInfo['jsonCntSizeTotal'], true);
        if (!is_array($aTmpCustomerAssortList) || count($aTmpCustomerAssortList) === 0) return ['data'=>'분류패킹 가능한 스타일이 없습니다. 관리자에게 문의 바랍니다.'];
        $aCustomerAssortList = $aTmpCustomerAssortList[0];
        if (!is_array($aCustomerAssortList) || count($aCustomerAssortList) === 0) return ['data'=>'분류패킹 가능한 스타일이 없습니다. 관리자에게 문의 바랍니다.'];
        //sl_imsCustomerReceiverDelivery(담당자==배송지) json 정리 -> sl_imsCustomerReceiverDelivery insert
        $iResgisterSno = \Session::get('manager.sno');
        $aInsert = [];
        foreach ($aReceiverList as $val) {
            $aTmpJsonContents = [];
            $bFlagInsert = true;
            foreach ($aCustomerAssortList as $key2 => $val2) {
                if (!is_array($val2) || count($val2) === 0) {
                    $bFlagInsert = false;
                } else {
                    //assort 내용 jsonContents 형태로 바꾸기
                    $aTmpJsonStyle = ['styleSno'=>(int)$key2, 'styleName'=>$aStyleNameBySno[$key2], 'aAssortList'=>[]];
                    $iTmpKey = 0;
                    foreach ($val2 as $key3 => $val3) {
                        //assort 내용 jsonContents 형태로 바꾸기
                        $aTmpJsonStyle['aAssortList'][$iTmpKey] = ['assortType'=>explode('___',$key3)[0], 'assortCharge'=>explode('___',$key3)[1], 'oSizeList'=>[]];
                        foreach ($val3 as $key4 => $val4) {
                            $aTmpJsonStyle['aAssortList'][$iTmpKey]['oSizeList'][$key4] = ['sizeName'=>$key4, 'expectQty'=>'']; //expectQty === currQty
                        }
                        $iTmpKey++;
                    }
                    $aTmpJsonContents[] = $aTmpJsonStyle;
                }
            }
            
            if ($bFlagInsert === true) { //스타일에 assort정보가 없으면 insert하지 않는다
                //6자리 랜덤코드 만들기(대문자 + 숫자 혼합)
                $sTmpRand = '';
                for($i = 0; $i < 6; $i++) {
                    $aTmpRand = [rand(49,57), rand(65,90)];
                    $sTmpRand .= chr($aTmpRand[rand(0,1)]);
                }
                $aTmpInsert = [
                    'customerSno'=>$aPackingInfo['customerSno'], 'packingSno'=>$iPackingSno, 'receiverSno'=>$val['sno'], 'regManagerSno'=>$iResgisterSno,
                    'managerName'=>$val['managerName'], 'managerEmail'=>$val['managerEmail'], 'managerPhone'=>$val['managerPhone'], 'managerAddrPost'=>$val['managerAddrPost'], 'managerAddr'=>$val['managerAddr'],
                    'pinNumber'=>$sTmpRand, 'jsonContents'=>json_encode($aTmpJsonContents)
                ];
                $aInsert[] = $aTmpInsert;
            }
        }

        if (count($aInsert) > 0) {
            foreach ($aInsert as $val) DBUtil2::insert(ImsDBName::CUSTOMER_RECEIVER_DELIVERY, $val);
            return ['data'=>''];
        } else return ['data'=>'분류패킹 가능한 스타일이 없습니다. 관리자에게 문의 바랍니다.'];
    }

    public function getListPackingInfo($params) {
        $iPackingSno = (int)$params['packingSno'];
        if ($iPackingSno === 0) return false;

        $aPackingInfo = DBUtil2::getOne(ImsDBName::CUSTOMER_PACKING, 'sno', $iPackingSno);
        if (!isset($aPackingInfo['sno']) || $aPackingInfo['styleSnos'] == '') return false;
        $aCntSizeTotal = json_decode($aPackingInfo['jsonCntSizeTotal'], true)[0];

        $oSVStyle = new SearchVo();
        $oSVStyle->setWhere("sno in (".$aPackingInfo['styleSnos'].")");
        $aStyleList = DBUtil2::getListBySearchVo(ImsDBName::PRODUCT, $oSVStyle);
        $aStyleListBySno = [];
        foreach ($aStyleList as $val) {
            $aStyleListBySno[$val['sno']] = ['cntTotal'=>0, 'sizeList'=>[], 'styleName'=>$val['productName']];
        }
        foreach ($aCntSizeTotal as $key => $val) {
            foreach ($val as $key2 => $val2) {
                foreach ($val2 as $key3 => $val3) {
                    $aStyleListBySno[$key]['sizeList'][] = $key3;
                }
                break;
            }
        }
        foreach ($aCntSizeTotal as $key => $val) {
            foreach ($val as $key2 => $val2) {
                foreach ($val2 as $key3 => $val3) {
                    $aStyleListBySno[$key]['cntTotal'] += (int)$val3['customerQty'];
                }
            }
        }

        return ['info'=>$aCntSizeTotal, 'info2'=>$aStyleListBySno, 'info_st'=>$aPackingInfo['packingSt']];
    }

    public function getListCustomerReceiverDelivery($params) {
        $oSV = new SearchVo();
        $iPackingSno = (int)$params['packingSno'];
        if ($iPackingSno > 0) $oSV->setWhere("packingSno = ".$iPackingSno);
        $iReceiverSno = (int)$params['receiverSno'];
        if ($iReceiverSno > 0) $oSV->setWhere("receiverSno = ".$iReceiverSno);
        $iDeliverySno = (int)$params['deliverySno'];
        if ($iDeliverySno > 0) { //파우치-담당자가 pin번호 인증으로 접근시
            $oSV->setWhere("a.sno = ".$iDeliverySno);
            $oSV->setWhere("pinNumber = '".$params['pinNum']."'");
        }

        $fileTableInfo=[
            'a' => ['data' => [ ImsDBName::CUSTOMER_RECEIVER_DELIVERY ], 'field' => ["a.*"]],
            'b' => ['data' => [ ImsDBName::CUSTOMER_RECEIVER, 'LEFT OUTER JOIN', "a.receiverSno = b.sno" ], 'field' => ["if (b.sno is null, '삭제담당자', b.branchName) as branchName, if (b.sno is null, '삭제담당자', b.departmentName) as departmentName"]],
        ];
        $aDeliveryList = DBUtil2::getComplexList(DBUtil2::setTableInfo($fileTableInfo,false), $oSV, false, false, true);
        foreach ($aDeliveryList as $key => $val) {
            $aDeliveryList[$key]['deliveryStHan'] = NkCodeMap::RECEIVER_DELIVERY_ST[$val['deliverySt']];
            $aDeliveryList[$key]['jsonContents'] = json_decode($val['jsonContents'], true);
            //담당자별, 스타일별 rowspan 구하기
            $aDeliveryList[$key]['rowspan'] = 0;
            foreach ($aDeliveryList[$key]['jsonContents'] as $key2 => $val2) {
                $aDeliveryList[$key]['rowspan'] += count($val2['aAssortList']);
                $aDeliveryList[$key]['jsonContents'][$key2]['rowspan'] = count($val2['aAssortList']);
            }
        }

        return $aDeliveryList;
    }

    public function modifyCustomerReceiverDelivery($params) {
        $iPackingSno = 0;
        foreach ($params['list'] as $val) {
            $iPackingSno = (int)$val['packingSno'];
            if (!isset($val['jsonContents']) || count($val['jsonContents']) == 0) {
                DBUtil2::delete(ImsDBName::CUSTOMER_RECEIVER_DELIVERY, new SearchVo('sno=?', $val['sno']));
                continue;
            }
            DBUtil2::update(ImsDBName::CUSTOMER_RECEIVER_DELIVERY, ['jsonContents'=>json_encode($val['jsonContents'])], new SearchVo('sno=?', $val['sno']));
        }

        //스타일/아소트구분/사이즈별 입력수량 합산값 저장
        //스타일/사이즈별 입력수량 합산값 저장 - IMS에서 납품검수할때 쓰임. IMS에서 고객확정완료해도 수정 가능하므로 내용저장할때마다 수행
        if ($iPackingSno > 0 && isset($params['cnt_size_list']) && is_array($params['cnt_size_list']) && count($params['cnt_size_list']) > 0) {
            $aPackingInfo = DBUtil2::getOne(ImsDBName::CUSTOMER_PACKING, 'sno', $iPackingSno); //makeQty를 가져와야 하므로 select쿼리 필요
            $aSizeTotalims = json_decode($aPackingInfo['jsonCntSizeTotalims'], true);
            foreach ($aSizeTotalims[0] as $key => $val) {
                foreach ($val as $key2 => $val2) {
                    $aSizeTotalims[0][$key][$key2]['currQty'] = 0;
                }
            }
            foreach ($params['cnt_size_list'] as $key => $val) {
                foreach ($val as $key2 => $val2) {
                    foreach ($val2 as $key3 => $val3) {
                        if (!isset($aSizeTotalims[0][$key][$key3])) $aSizeTotalims[0][$key][$key3] = ['makeQty'=>0, 'currQty'=>0, 'storageQty'=>0];
                        $aSizeTotalims[0][$key][$key3]['currQty'] += (int)$val3['currQty'];
                    }
                }
            }
            //jsonCntSizeTotalims : 창고수량의 default 는 생산 - 분류패킹
            foreach ($aSizeTotalims[0] as $key => $val) {
                foreach ($val as $key2 => $val2) {
                    $aSizeTotalims[0][$key][$key2]['storageQty'] = (int)$val2['makeQty'] - (int)$val2['currQty'];
                }
            }

            DBUtil2::update(ImsDBName::CUSTOMER_PACKING, ['jsonCntSizeTotal'=>json_encode([$params['cnt_size_list']]), 'jsonCntSizeTotalims'=>json_encode($aSizeTotalims)], new SearchVo('sno=?', $iPackingSno));
        }
    }

    public function requestWriteReceiverDelivery($params) {
        $aSnos = $params['deliverySnos'];
        if (!is_array($aSnos) || count($aSnos) === 0) return ['data'=>false];
        //고객관리자가 값 수정 or 아소트제외 후 내용저장 안하고 바로 입력요청 누르는 경우 감안하여 아래 함수 실행
        $this->modifyCustomerReceiverDelivery($params);

        $oSV = new SearchVo();
        $oSV->setWhere("a.sno in (" . implode(',', $aSnos) . ") ");
        $fileTableInfo=[
            'a' => ['data' => [ ImsDBName::CUSTOMER_RECEIVER_DELIVERY ], 'field' => ["a.sno, a.managerName, a.managerEmail, a.managerPhone, a.pinNumber"]],
        ];
        $aList = DBUtil2::getComplexList(DBUtil2::setTableInfo($fileTableInfo,false), $oSV, false, false, true);
        if (count($aList) === 0) return ['data'=>false];

        $mailUtil = SlLoader::cLoad('mail','SiteLabMailUtil','sl');
        foreach ($aList as $val) {
            $mailData['subject'] = $val['managerName'].' 님 분류패킹 입력요청 드립니다.';
            $mailData['from'] = 'innover@msinnover.com';
            $mailData['to'] = $val['managerEmail'];

            $replace['rc_managerName'] = $val['managerName'];
            $replace['rc_pinNumber'] = $val['pinNumber'];
            $replace['rc_confirmFullUrl'] = \Request::getScheme()."://".\Request::getDefaultHost().'/ics/ics_packing.php?no='.$val['sno'];
            $replace['rc_confirmUrl'] = '분류패킹 입력URL 이동';
            $mailData['body'] = $mailUtil->getMailTemplate($replace,'customer_receiver_delivery_request_write.php');
            $mailUtil->send($mailData['subject'], $mailData['body'], $mailData['from'], $mailData['to'], null, SlCommonUtil::getManagerMail());

            //발송이력 insert
            $aInsertHistory = [
                'sendType'=>'분류패킹요청',
                'projectSno'=>$val['sno'],
                'sendManagerSno'=>\Session::get('manager.sno'),
                'receiverName'=>$val['managerName'],
                'receiverMail'=>$val['managerEmail'],
                'subject'=>$mailData['subject'],
                'contents'=>$mailData['body'],
                'regDt'=>date('Y-m-d H:i:s'),
            ];
            DBUtil2::insert(ImsDBName::SEND_HISTORY, $aInsertHistory);

            DBUtil2::update(ImsDBName::CUSTOMER_RECEIVER_DELIVERY, ['deliverySt'=>2], new SearchVo('sno=?', $val['sno']));
        }

        return ['data'=>true];
    }

    public function modifyCRD($params) {
        $iSno = (int)$params['deliverySno'];
        $aInfo = DBUtil2::getOne(ImsDBName::CUSTOMER_RECEIVER_DELIVERY, 'sno', $iSno);
        if (!isset($aInfo['sno'])) return ['data'=>'배송정보가 올바르지 않습니다.'];
        if ($aInfo['pinNumber'] != $params['pinNumber']) return ['data'=>'핀번호가 올바르지 않습니다.'];
        if ($aInfo['deliverySt'] > 2) return ['data'=>'이미 입력완료하셨습니다.'];

        $iType = (int)$params['type'];
        $aUpdate = ['jsonContents'=>json_encode($params['list']), 'wishReceivePlace'=>$params['wishPlace']];
        if ($iType == 2) $aUpdate['deliverySt'] = 3;
        DBUtil2::update(ImsDBName::CUSTOMER_RECEIVER_DELIVERY, $aUpdate, new SearchVo('sno=?', $iSno));
        return ['data'=>''];
    }

    public function confirmReceiverDelivery($params) {
        $iSno = (int)$params['packingSno'];
        if ($iSno === 0) return ['data'=>false];

        //고객관리자가 값 수정 후 내용저장 안하고 바로 확정하기 누르는 경우 감안하여 아래 함수 실행
        $this->modifyCustomerReceiverDelivery($params);

        DBUtil2::update(ImsDBName::CUSTOMER_RECEIVER_DELIVERY, ['deliverySt'=>4], new SearchVo('packingSno=?', $iSno));
        DBUtil2::update(ImsDBName::CUSTOMER_PACKING, ['packingSt'=>2], new SearchVo('sno=?', $iSno));
    }

    public function getListCustomerPacking($params) {
        $oSV = new SearchVo();
        $iCustomerSno = (int)$params['customerSno'];
        if ($iCustomerSno > 0) $oSV->setWhere("a.customerSno = ".$iCustomerSno); //리스트 가져오는 경우
        else { //정보 가져오는 경우(수량확인)
            $iPackingSno = (int)$params['packingSno'];
            //스타일sno를 파라메터로 던져준 경우 packingSno 가져오기
            $iStyleSno = (int)$params['styleSno'];
            if ($iPackingSno == 0 && $iStyleSno > 0) {
                $aStyleInfo = DBUtil2::getOne(ImsDBName::PRODUCT, 'sno', $iStyleSno);
                if (isset($aStyleInfo['sno'])) $iPackingSno = (int)$aStyleInfo['packingSno'];
                if ($iPackingSno === 0) return [];
            }
            if ($iPackingSno > 0) $oSV->setWhere("a.sno = ".$iPackingSno);
        }

        $fileTableInfo=[
            'a' => ['data' => [ ImsDBName::CUSTOMER_PACKING ], 'field' => ["a.*"]],
            'manager' => ['data' => [ DB_MANAGER, 'LEFT OUTER JOIN', 'a.regManagerSno = manager.sno' ], 'field' => ["if(manager.sno is null, '미선택', manager.managerNm) as regManagerName"]],
            'b' => ['data' => [ ImsDBName::PRODUCT, 'LEFT OUTER JOIN', 'a.sno = b.packingSno' ], 'field' => ["group_concat(productName separator ', ') as styleNames"]],
        ];
        $oSV->setGroup('a.sno');
        $aPackingList = DBUtil2::getComplexList(DBUtil2::setTableInfo($fileTableInfo,false), $oSV, false, false, true);

        //리스트 정제
        $aPackingSnos = [];
        foreach ($aPackingList as $key => $val) {
            $aPackingSnos[] = (int)$val['sno'];
            $aPackingList[$key]['packingStHan'] = NkCodeMap::CUSTOMER_PACKING_ST[$val['packingSt']];
            $aPackingList[$key]['jsonCntSizeTotalims'] = json_decode($val['jsonCntSizeTotalims'], true);
        }

        //고객담당자가 지정한 담당직원(==배송지점) 갯수 가져오기 -> 아소트 확정취소시 검사
        $oSVD = new SearchVo();
        $oSVD->setWhere("packingSno in (".implode(",",$aPackingSnos).")");
        $oSVD->setGroup('packingSno');
        $aTableInfo=[
            'a' => ['data' => [ ImsDBName::CUSTOMER_RECEIVER_DELIVERY ], 'field' => ["packingSno, count(a.sno) as cntDelivery"]],
        ];
        $aTmpPackingDList = DBUtil2::getComplexList(DBUtil2::setTableInfo($aTableInfo,false), $oSVD, false, false, true);
        $aPackingDList = [];
        foreach ($aTmpPackingDList as $val) $aPackingDList[$val['packingSno']] = $val['cntDelivery'];
        foreach ($aPackingList as $key => $val) $aPackingList[$key]['cntDelivery'] = isset($aPackingDList[$val['sno']]) ? $aPackingDList[$val['sno']] : 0;

        return $aPackingList;
    }

    public function modifyDeliveryInfo($params) {
        $aSameDeliveryList = DBUtil2::getListBySearchVo(ImsDBName::CUSTOMER_RECEIVER_DELIVERY, new SearchVo('packingSno=?', $params['info']['packingSno']));
        $aExistInvoiceNums = [];
        foreach ($aSameDeliveryList as $val) {
            if ($val['sno'] != $params['info']['sno']) $aExistInvoiceNums = array_merge($aExistInvoiceNums, explode(', ', $val['invoiceNums']));
        }
        $aTmp = explode(', ', $params['info']['invoiceNums']);
        $aTmpInvoiceNums = [];
        foreach ($aTmp as $val) {
            if (!in_array($val, $aExistInvoiceNums)) {
                $aExistInvoiceNums[] = $val;
                $aTmpInvoiceNums[] = $val;
            }
        }
        DBUtil2::update(ImsDBName::CUSTOMER_RECEIVER_DELIVERY, ['invoiceNums'=>implode(', ', $aTmpInvoiceNums), 'deliveryCompanyCode'=>$params['info']['deliveryCompanyCode']], new SearchVo('sno=?', $params['info']['sno']));
    }
}