<?php
namespace Component\Ims;

use App;

use Component\Ims\ImsCodeMap;
use Component\Database\DBTableField;
use SiteLabUtil\SlCommonUtil;
use SlComponent\Database\DBUtil2;
use SlComponent\Database\SearchVo;
use SlComponent\Util\SlLoader;

use Controller\Admin\Ims\ImsPsNkTrait;
use Component\Ims\ImsServiceSortNkTrait;

class ImsCustomerService {
    use ImsPsNkTrait;
    use ImsServiceSortNkTrait;

    private $dpData;

    public function __construct(){
        $this->dpData = [ //고객발굴 리스트
            ['type' => 'c', 'col' => 5, 'class' => '', 'name' => 'customerTypeHan', 'title' => '고객구분', ],
            ['type' => 'company_name', 'col' => 0, 'class' => 'pdl5 ta-l', 'name' => 'customerName', 'title' => '업체명', ],
            ['type' => 'cate_name', 'col' => 5, 'class' => '', 'name' => 'parentCateName', 'title' => '업종', ],
            ['type' => 'i', 'col' => 3, 'class' => 'font-11', 'name' => 'employeeCnt', 'title' => '사원수', ],
            ['type' => 'c', 'col' => 6, 'class' => 'ta-l pdl5 font-11', 'name' => 'phone', 'title' => '대표번호', ],
            ['type' => 'c', 'col' => 4, 'class' => 'font-11', 'name' => 'dept', 'title' => '부서', ],
            ['type' => 'c', 'col' => 5, 'class' => 'font-11', 'name' => 'contactName', 'title' => '담당자명', ],
            ['type' => 'c', 'col' => 6, 'class' => 'ta-l pdl5 font-11', 'name' => 'contactPhone', 'title' => '담당자 연락처', ],
            ['type' => 'c', 'col' => 4, 'class' => 'ta-l pdl5 font-11', 'name' => 'contactEmail', 'title' => '담당자 이메일', ],
            ['type' => 'c', 'col' => 5, 'class' => 'font-11', 'name' => 'buyDiv', 'title' => '유니폼 종류', ],
            ['type' => 'i2', 'col' => 5, 'class' => 'font-11', 'name' => 'totalExpectSales', 'title' => '추정 매출', ],
            ['type' => 'c', 'col' => 4, 'class' => '', 'name' => 'buyMethod', 'title' => '구매 방식', ],
            ['type' => 'date', 'col' => 4, 'class' => 'font-11', 'name' => 'bidDt', 'title' => '입찰 예정일', ],
            ['type' => 'date', 'col' => 4, 'class' => 'font-11', 'name' => 'afterCallDt', 'title' => '후속 영업일', ],
            ['type' => 'date', 'col' => 4, 'class' => 'font-11', 'name' => 'contactDt', 'title' => '최근 영업일', ],
            ['type' => 'customer_detail', 'col' => 2, 'class' => '', 'name' => 'regImsYn', 'title' => 'IMS', ],
        ];
        $this->dpDataCustomer = [ //고객 리스트
            ['type' => 'c', 'col' => 0, 'class' => 'pdl5 ta-l', 'name' => 'customerName', 'title' => '업체명', ],
            ['type' => 'c', 'col' => 12, 'class' => 'font-11', 'name' => 'cateName', 'title' => '업종', ],
            ['type' => 'c', 'col' => 10, 'class' => 'font-11', 'name' => 'contactName', 'title' => '담당자명', ],
            ['type' => 'c', 'col' => 7, 'class' => 'font-11', 'name' => 'salesManagerNm', 'title' => '영업담당자', ],
            ['type' => 'c', 'col' => 10, 'class' => 'font-11', 'name' => 'use3pl', 'title' => '3PL 사용여부', ],
            ['type' => 'c', 'col' => 10, 'class' => 'font-11', 'name' => 'useMall', 'title' => '폐쇄몰 사용여부', ],
            ['type' => 'i', 'col' => 12, 'class' => 'font-11', 'name' => 'customerCost', 'title' => '총매입', ],
            ['type' => 'i', 'col' => 12, 'class' => 'font-11', 'name' => 'customerPrice', 'title' => '총매출', ],
        ];
    }
    public function getDisplay(){ return $this->dpData; }
    public function getDisplayCustomer(){ return $this->dpDataCustomer; }

    //고객 리스트(레퍼런스-리스트검색모듈에 쓰임)
    public function getListCustomerNk($params) {
        $sTableNm = ImsDBName::CUSTOMER;
        $tableInfo=[
            'a' => ['data' => [$sTableNm], 'field' => ["a.sno, customerName, contactName, if(use3pl='y', '예', '아니오') as use3pl, if(useMall='y', '예', '아니오') as useMall, customerCost, customerPrice"]],
            'sales' => ['data' => [ DB_MANAGER, 'LEFT OUTER JOIN', 'a.salesManagerSno = sales.sno' ], 'field' => ["if(sales.sno is null, '미선택', sales.managerNm) as salesManagerNm"]],
            'cate' => ['data' => [ ImsDBName::BUSI_CATE, 'LEFT OUTER JOIN', 'a.busiCateSno = cate.sno' ], 'field' => ["if(cate.sno is null, '미지정', cate.cateName) as cateName"]],
        ];
        if (!isset($params['upsertSnoGet'])) { //리스트를 return받으려는 경우
            //리스트에서 뿌려줄 필드리스트 정리
            $aFldList = $this->getDisplayCustomer();
        } else $aFldList = [];

        $aReturn = $this->fnRefineListUpsertForm($params, $sTableNm, $tableInfo, $aFldList);
        if (count($aReturn['list']) > 0) {
//            foreach ($aReturn['list'] as $key => $val) {
//                $aReturn['list'][$key]['use3pl'] = $val['use3pl'] == 'y' ? '예' : '아니오';
//                $aReturn['list'][$key]['useMall'] = $val['useMall'] == 'y' ? '예' : '아니오';
//            }
        }
        return $aReturn;
    }


    //고객발굴 리스트
    public function getListFindCustomer($params) {
        $sTableNm = ImsDBName::SALES_CUSTOMER;
        $tableInfo=[
            'a' => ['data' => [ $sTableNm ], 'field' => ["a.*, if(customerSno is null or customerSno = 0, 'X', 'O') as regImsYn"]],
            'cate2' => ['data' => [ ImsDBName::BUSI_CATE, 'LEFT OUTER JOIN', 'a.busiCateSno = cate2.sno' ], 'field' => ["if(cate2.sno is null, '미선택', cate2.cateName) as cateName, if(cate2.sno is null, 0, cate2.parentBusiCateSno) as parentBusiCateSno"]],
            'cate1' => ['data' => [ ImsDBName::BUSI_CATE, 'LEFT OUTER JOIN', 'cate2.parentBusiCateSno = cate1.sno' ], 'field' => ["if(cate1.sno is null, '미선택', cate1.cateName) as parentCateName"]],
            'sale_manager' => ['data' => [ DB_MANAGER, 'LEFT OUTER JOIN', 'a.salesManagerSno = sale_manager.sno' ], 'field' => ["if(sale_manager.sno is null, '미선택', sale_manager.managerNm) as salesManagerName"]],
            'reg' => ['data' => [ DB_MANAGER, 'LEFT OUTER JOIN', 'a.regManagerSno = reg.sno' ], 'field' => ["if(reg.sno is null, '미지정', reg.managerNm) as regManagerName"]],
            'contents' => ['data' => [ ImsDBName::SALES_CUSTOMER_CONTENTS, 'LEFT OUTER JOIN', 'a.sno = contents.salesSno' ], 'field' => ["if(contents.sno is null, '', max(contents.afterCallDt)) as afterCallDt"]],
        ];
        if (!isset($params['upsertSnoGet'])) { //리스트를 return받으려는 경우
            if (!isset($params['sort'])) $params['sort'] = 'sno,asc';
            //리스트에서 뿌려줄 필드리스트 정리
            $aFldList = $this->getDisplay();
        } else $aFldList = [];

        //fnRefineListUpsertForm() 소스 가져와서 변경 start
        $aReturn = [];
        $aUpsertForm = ['sno'=>0, 'customerName'=>'', 'parentBusiCateSno'=>0, 'busiCateSno'=>0, 'parentCateName'=>'', 'cateName'=>'', 'employeeCnt'=>'', 'phone'=>'', 'salesManagerSno'=>\Session::get('manager.sno'), 'buyDiv'=>'제작복', 'salesManagerName'=>\Session::get('manager.managerNm'), 'styleCode'=>'', 'regManagerName'=>'' ];
        if (isset($params['upsertSnoGet'])) {
            if ((int)$params['upsertSnoGet'] === 0) { //등록인 경우는 여기에서 return
                $aReturn['info'] = $aUpsertForm;
            }
        }
        if (!isset($aReturn['info'])) {
            //리스트 가져오기
            $iUpsertSnoGet = (int)$params['upsertSnoGet'];
            $searchVo = new SearchVo();
            if ($iUpsertSnoGet !== 0) $searchVo->setWhere('a.sno = '.$iUpsertSnoGet); //수정(==상세)인 경우(1개 record만 가져옴)
            else { //리스트 가져오는 경우
                if (isset($params['SCSnos']) && is_array($params['SCSnos']) && count($params['SCSnos']) > 0) {
                    $searchVo->setWhere("a.sno in (" . implode(',', $params['SCSnos']) . ")");
                }
                $searchData['condition'] = $params;
                $this->refineCommonCondition($searchData['condition'], $searchVo);
                //table order by 설정
                if (isset($searchData['condition']['sort'])) {
                    $searchData['condition']['sort_nk'] = $searchData['condition']['sort'];
                    unset($searchData['condition']['sort']);
                    $this->setListSortNk($searchData['condition']['sort_nk'], $searchVo);
                } else {
                    $searchVo->setOrder('a.regDt desc');
                }
            }
            $searchVo->setGroup('a.sno');

            if (!isset($searchData['page']) || (int)$searchData['page'] == 0) $searchData['page'] = gd_isset($searchData['condition']['page'], 1);
            if (!isset($searchData['pageNum']) || (int)$searchData['pageNum'] == 0) $searchData['pageNum'] = gd_isset($searchData['condition']['pageNum'], 15000);
            $allData = DBUtil2::getComplexListWithPaging(DBUtil2::setTableInfo($tableInfo,false), $searchVo, $searchData, false, true);
            foreach ($allData['listData'] as $key => $val) {
                $allData['listData'][$key] = SlCommonUtil::setDateBlank($val); //0000-00-00값이면 공백값으로 변경
            }

            if ($iUpsertSnoGet !== 0) { //수정(==상세)인 경우
                //upsert할때만 쓰이는 필드 정리
                foreach ($allData['listData'][0] as $key => $val) {
                    if (isset($aUpsertForm[$key])) $aUpsertForm[$key] = $val;
                }
                $aReturn['info'] = $aUpsertForm;
            } else { //리스트 가져오는 경우
                $aReturn = [
                    'pageEx' => $allData['pageData']->getPage('#'),
                    'page' => $allData['pageData'],
                    'list' => $allData['listData'],
                    'fieldData' => $aFldList
                ];
            }
        }
        //fnRefineListUpsertForm() 소스 가져와서 변경 end

        //고객발굴(==영업고객) 리스트 가져올때 아래 실행
        if (!isset($params['upsertSnoGet'])) {
            if (isset($aReturn['list']) && count($aReturn['list']) > 0) {
                //데이터 정제
                foreach ($aReturn['list'] as $key => $val) {
                    $aReturn['list'][$key]['customerTypeHan'] = NkCodeMap::SALES_CUST_TYPE[$val['customerType']];
                }
                // 통화이력, 활동이력 가져오기
                if (!isset($params['getSimple'])) {
                    $iSCSnos = [];
                    foreach ($aReturn['list'] as $key => $val) {
                        $iSCSnos[] = (int)$val['sno'];
                        $aReturn['list'][$key]['contentsList'] = [];
                    }
                    $oContentsSchVo = new SearchVo();
                    $oContentsSchVo->setWhere("salesSno in (" . implode(',', $iSCSnos) . ")");
                    $oContentsSchVo->setOrder('a.regDt desc');
                    $aTableInfo=[
                        'a' => ['data' => [ ImsDBName::SALES_CUSTOMER_CONTENTS ], 'field' => ["a.*"]],
                        'reg' => ['data' => [ DB_MANAGER, 'LEFT OUTER JOIN', 'a.regManagerSno = reg.sno' ], 'field' => ["if(reg.sno is null, '미선택', reg.managerNm) as regManagerName"]],
                    ];
                    $aContentsList = DBUtil2::getComplexList(DBUtil2::setTableInfo($aTableInfo,false), $oContentsSchVo, false, false, true);
                    $aContentsListBySCSno = [];
                    if (count($aContentsList) > 0) {
                        foreach ($aContentsList as $key => $val) {
                            $aContentsList[$key]['contentsTypeHan'] = NkCodeMap::SALES_CUST_CONTENTS_TYPE[$val['contentsType']];
                            if (!isset($aContentsListBySCSno[$val['salesSno']])) $aContentsListBySCSno[$val['salesSno']] = [];
                            $aContentsListBySCSno[$val['salesSno']][] = $aContentsList[$key];
                        }
                        foreach ($aReturn['list'] as $key => $val) {
                            if (isset($aContentsListBySCSno[$val['sno']])) {
                                $aReturn['list'][$key]['contentsList'] = $aContentsListBySCSno[$val['sno']];
                            }
                        }
                    }
                }
            }
        }

        return $aReturn;
    }
    //고객발굴 upsert
    public function setFindCustomer($params) {
        $iSno = (int)$params['data']['sno'];
        unset($params['data']['sno']);
        $sCurrDt = date('Y-m-d H:i:s');
        $iRegManagerSno = \Session::get('manager.sno');
        if (is_array($params['data']['jsonExpectSales'])) {
             if (count($params['data']['jsonExpectSales']) === 0) $params['data']['jsonExpectSales'] = '[]';
             else $params['data']['jsonExpectSales'] = json_encode($params['data']['jsonExpectSales']);
         }

        if ($iSno === 0) {
            //업체등록일땐 담당자정보를 넣지 않으므로 무조건 잠재고객으로 insert
            $params['data']['customerType'] = '10';
            $params['data']['regManagerSno'] = $iRegManagerSno;
            $params['data']['regDt'] = $sCurrDt;
            $iSno = DBUtil2::insert(ImsDBName::SALES_CUSTOMER, $params['data']);
        } else {
            //업체수정일때는 입찰예정일을 넣지 못하므로 가망고객으로 바꾸는 분기없음. 담당자정보(담당자명,이메일,전화번호) 있으면 관심고객으로
            //고객 구분 판별
            if (isset($params['data']['contactName']) && isset($params['data']['contactPhone']) && isset($params['data']['contactEmail']) && $params['data']['contactName'] != '' && $params['data']['contactPhone'] != '' && $params['data']['contactEmail'] != '') {
                $params['data']['customerType'] = 20;
            }
            $sCurrCustomerType = DBUtil2::getOne(ImsDBName::SALES_CUSTOMER, 'sno', $iSno)['customerType'];
            if ($params['data']['customerType'] <= (int)$sCurrCustomerType) unset($params['data']['customerType']);

            $params['data']['modDt'] = $sCurrDt;
            DBUtil2::update(ImsDBName::SALES_CUSTOMER, $params['data'], new SearchVo('sno=?', $iSno));
        }
    }

    //발굴 고객 정보 + TM.EM 리스트
    public function getListSalesCustomerContents($params) {
        $iSalesCustomerSno = (int)$params['sno'];
        $aReturn = ['info'=>[], 'list'=>[]];

        $searchVo = new SearchVo('a.sno=?', $iSalesCustomerSno);
        $tableInfo=[
            'a' => ['data' => [ ImsDBName::SALES_CUSTOMER ], 'field' => ["a.*"]],
            'cate2' => ['data' => [ ImsDBName::BUSI_CATE, 'LEFT OUTER JOIN', 'a.busiCateSno = cate2.sno' ], 'field' => ["if(cate2.sno is null, '미선택', cate2.cateName) as cateName, if(cate2.sno is null, 0, cate2.parentBusiCateSno) as parentBusiCateSno"]],
            'cate1' => ['data' => [ ImsDBName::BUSI_CATE, 'LEFT OUTER JOIN', 'cate2.parentBusiCateSno = cate1.sno' ], 'field' => ["if(cate1.sno is null, '미선택', cate1.cateName) as parentCateName"]],
            'sale_manager' => ['data' => [ DB_MANAGER, 'LEFT OUTER JOIN', 'a.salesManagerSno = sale_manager.sno' ], 'field' => ["if(sale_manager.sno is null, '미선택', sale_manager.managerNm) as salesManagerName"]],
        ];
        $aSalesCustomerInfo = DBUtil2::getComplexList(DBUtil2::setTableInfo($tableInfo,false), $searchVo, false, false, true);
        if (isset($aSalesCustomerInfo[0]['sno'])) {
            $aSalesCustomerInfo[0] = SlCommonUtil::setDateBlank($aSalesCustomerInfo[0]);
            if ($aSalesCustomerInfo[0]['jsonExpectSales'] == null || $aSalesCustomerInfo[0]['jsonExpectSales'] == '') $aSalesCustomerInfo[0]['jsonExpectSales'] = [];
            else $aSalesCustomerInfo[0]['jsonExpectSales'] = json_decode($aSalesCustomerInfo[0]['jsonExpectSales'], true);
            if ($aSalesCustomerInfo[0]['bidCntYear'] == null || $aSalesCustomerInfo[0]['bidCntYear'] == '') $aSalesCustomerInfo[0]['bidCntYear'] = 0;
            if ($aSalesCustomerInfo[0]['customerNeeds'] == null || $aSalesCustomerInfo[0]['customerNeeds'] == '' || $aSalesCustomerInfo[0]['customerNeeds'] == 0) $aSalesCustomerInfo[0]['customerNeeds'] = 1;
            if ($aSalesCustomerInfo[0]['mallInterest'] == null || $aSalesCustomerInfo[0]['mallInterest'] == '' || $aSalesCustomerInfo[0]['mallInterest'] == 0) $aSalesCustomerInfo[0]['mallInterest'] = 1;
            $aSalesCustomerInfo[0]['customerTypeHan'] = NkCodeMap::SALES_CUST_TYPE[$aSalesCustomerInfo[0]['customerType']];

            $aReturn['info'] = $aSalesCustomerInfo[0];
        } else return $aReturn;

        $aReturn['list'] = $this->getTmHistory(['salesSno' => $iSalesCustomerSno]);

        return $aReturn;
    }

    //TM.EM 리스트
    public function getTmHistory($params){
        $searchVo = new SearchVo();
        if( isset($params['salesSno']) ){
            $searchVo->setWhere('a.salesSno=?');
            $searchVo->setWhereValue($params['salesSno']);
        }
        if( isset($params['customerSno']) ){
            $searchVo->setWhere('b.customerSno=?');
            $searchVo->setWhereValue($params['customerSno']);
        }
        $searchVo->setOrder('a.regDt DESC');

        $tableInfo=[
            'a' => ['data' => [ ImsDBName::SALES_CUSTOMER_CONTENTS ], 'field' => ["a.*"]],
            'b' => ['data' => [ ImsDBName::SALES_CUSTOMER, 'JOIN', 'a.salesSno = b.sno' ], 'field' => ["a.sno as contentsSno"]],
            'reg' => ['data' => [ DB_MANAGER, 'LEFT OUTER JOIN', 'a.regManagerSno = reg.sno' ], 'field' => ["if(reg.sno is null, '없음', reg.managerNm) as regManagerName"]],
        ];

        $aContentsList = DBUtil2::getComplexList(DBUtil2::setTableInfo($tableInfo,false), $searchVo, false, false, true);
        if (count($aContentsList) > 0) {
            foreach ($aContentsList as $key => $val) {
                $aContentsList[$key] = SlCommonUtil::setDateBlank($aContentsList[$key]);
                $aContentsList[$key]['contentsTypeHan'] = NkCodeMap::SALES_CUST_CONTENTS_TYPE[$val['contentsType']];
                $aContentsList[$key]['afterCallReasonHan'] = NkCodeMap::SALES_CUST_CONTENTS_AFTER_CALL_REASON[$val['afterCallReason']];
            }
        }

        return $aContentsList;
    }
    //TM.EM upsert
    public function setSalesCustomerContents($params) {
        $iSCSno = (int)$params['customer_data']['sno'];
        $aSalesCustomerInfo = DBUtil2::getOne(ImsDBName::SALES_CUSTOMER, 'sno', $iSCSno);

        //고객발굴(==영업고객) update
        $aUpdateSCFldNames = ['contactName','dept','contactPhone','contactEmail','buyMethod','contactType','bidDt','meetingDt','bidCntYear','lastBidYear','currContractCompany','jsonExpectSales','totalExpectSales','customerNeeds','mallInterest'];
        $aUpdateSC = [];
        foreach ($aUpdateSCFldNames as $val) {
            if (isset($params['customer_data'][$val])) {
                if (is_array($params['customer_data'][$val])) {
                    if (count($params['customer_data'][$val]) === 0) $aUpdateSC[$val] = '[]';
                    else $aUpdateSC[$val] = json_encode($params['customer_data'][$val]);
                } else $aUpdateSC[$val] = $params['customer_data'][$val];
            }
        }
        //고객 구분 판별
        $aUpdateSC['customerType'] = 10;
        if ($aUpdateSC['contactName'] != '' && $aUpdateSC['contactPhone'] != '' && $aUpdateSC['contactEmail'] != '') $aUpdateSC['customerType'] = 20;
        if ($aUpdateSC['bidDt'] != '' && $aUpdateSC['bidDt'] != '0000-00-00' && $aUpdateSC['bidDt'] != null) $aUpdateSC['customerType'] = 30;
        $sCurrCustomerType = $aSalesCustomerInfo['customerType'];
        //하위 구분으로 떨어지는 거 막는 line. 아래 line 주석처리하면 하위 구분으로 이동 가능함 -> 발굴완료를 제외한 모든 구분은 구분값 재정의하도록 변경
//        if ($aUpdateSC['customerType'] <= (int)$sCurrCustomerType) unset($aUpdateSC['customerType']);
        if ((int)$sCurrCustomerType == 50) unset($aUpdateSC['customerType']);
        DBUtil2::update(ImsDBName::SALES_CUSTOMER, $aUpdateSC, new SearchVo('sno=?', $iSCSno));

        $aUpsertInfo = $params['contents_data'];
        //수정일때는 발굴고객sno update에서 제외
        if ($aUpsertInfo['sno'] > 0) {
            unset($aUpsertInfo['salesSno']);
            $aUpsertInfo['lastManagerSno'] = \Session::get('manager.sno');
        }
        $sCurrDate = date('Y-m-d');
        $mUpsertSno = $this->setSimpleDbTable(['data'=>$aUpsertInfo, 'table_number'=>9]);
        //받은요청 upsert
        if ((int)$mUpsertSno > 0) {
            $sCurrDt = date('Y-m-d H:i:s');
            if (isset($params['customer_data']['salesManagerSno']) && (int)$params['customer_data']['salesManagerSno'] > 0) $iSalesManagerSno = (int)$params['customer_data']['salesManagerSno'];
            else $iSalesManagerSno = \Session::get('manager.sno');

            if ($aUpsertInfo['sno'] == 0) {
                //영업이력 등록일때 sl_salesCustomerInfo 테이블의 contactDt(최근영업일자) 컬럼값 update
                DBUtil2::update(ImsDBName::SALES_CUSTOMER, ['contactDt' => $sCurrDate], new SearchVo('sno=?', $iSCSno));

                //영업이력 등록했을 때 후속연락일자가 있으면 TO DO LIST에 insert
                if (isset($aUpsertInfo['afterCallDt']) && $aUpsertInfo['afterCallDt'] != '') {
                    $aInsertTodoRequest = [
                        'todoType'=>'todo', 'hopeDt'=> $aUpsertInfo['afterCallDt'], 'subject'=>'[후속연락 예정일] '.$aSalesCustomerInfo['customerName'],
                        'contents'=>'발굴고객메뉴에서 영업이력을 등록하여 자동등록된 요청건입니다. \r\n 후속연락 사유 : '.NkCodeMap::SALES_CUST_CONTENTS_AFTER_CALL_REASON[$aUpsertInfo['afterCallReason']].' \r\n 내용 : '.$aUpsertInfo['contents'].' \r\n 등록일시 : '.$sCurrDt,
                        'eachSno'=>$mUpsertSno, 'eachDiv'=>'scc_after_call',
                        'regManagerSno'=>\Session::get('manager.sno'), 'regDt'=>$sCurrDt
                    ];
                    $iRequestSno = DBUtil2::insert(ImsDBName::TODO_REQUEST, $aInsertTodoRequest);
                    $aInsertTodoResponse = [
                        'reqSno'=>$iRequestSno, 'managerSno'=>$iSalesManagerSno, 'expectedDt'=>$aUpsertInfo['afterCallDt'], 'regDt'=>$sCurrDt
                    ];
                    DBUtil2::insert(ImsDBName::TODO_RESPONSE, $aInsertTodoResponse);
                }
                //입찰예정일자가 있으면 TO DO LIST에 insert
                if (isset($params['customer_data']['bidDt']) && $params['customer_data']['bidDt'] != '') {
                    $aInsertTodoRequest = [
                        'todoType'=>'todo', 'hopeDt'=> $params['customer_data']['bidDt'], 'subject'=>'[입찰 예정일] '.$aSalesCustomerInfo['customerName'],
                        'contents'=>'발굴고객메뉴에서 영업이력을 등록하여 자동등록된 요청건입니다. \r\n 후속연락 사유 : '.NkCodeMap::SALES_CUST_CONTENTS_AFTER_CALL_REASON[$aUpsertInfo['afterCallReason']].' \r\n 내용 : '.$aUpsertInfo['contents'].' \r\n 등록일시 : '.$sCurrDt,
                        'eachSno'=>$mUpsertSno, 'eachDiv'=>'scc_bid',
                        'regManagerSno'=>\Session::get('manager.sno'), 'regDt'=>$sCurrDt
                    ];
                    $iRequestSno = DBUtil2::insert(ImsDBName::TODO_REQUEST, $aInsertTodoRequest);
                    $aInsertTodoResponse = [
                        'reqSno'=>$iRequestSno, 'managerSno'=>$iSalesManagerSno, 'expectedDt'=>$params['customer_data']['bidDt'], 'regDt'=>$sCurrDt
                    ];
                    DBUtil2::insert(ImsDBName::TODO_RESPONSE, $aInsertTodoResponse);
                }
            } else {
                //영업이력 수정했을 때 후속연락일자가 있으면 TO DO LIST에 update
                if (isset($aUpsertInfo['afterCallDt']) && $aUpsertInfo['afterCallDt'] != '') {
                    $aCurrRequestInfo = DBUtil2::getOneBySearchVo(ImsDBName::TODO_REQUEST, new SearchVo(['eachSno=?', 'eachDiv=?'], [$mUpsertSno, 'scc_after_call']));
                    if (isset($aCurrRequestInfo['sno']) && (int)$aCurrRequestInfo['sno'] > 0) {
                        $aUpdateTodoRequest = [
                            'hopeDt'=> $aUpsertInfo['afterCallDt'], 'subject'=>'[후속연락 예정일] '.$aSalesCustomerInfo['customerName'],
                            'contents'=>'발굴고객메뉴에서 영업이력을 등록하여 자동등록된 요청건입니다. \r\n 후속연락 사유 : '.NkCodeMap::SALES_CUST_CONTENTS_AFTER_CALL_REASON[$aUpsertInfo['afterCallReason']].' \r\n 내용 : '.$aUpsertInfo['contents'].' \r\n 수정일시 : '.$sCurrDt,
                            'modDt'=>$sCurrDt
                        ];
                        DBUtil2::update(ImsDBName::TODO_REQUEST, $aUpdateTodoRequest, new SearchVo('sno=?', $aCurrRequestInfo['sno']));
                        $aUpdateTodoResponse = [
                            'managerSno'=>$iSalesManagerSno, 'expectedDt'=>$aUpsertInfo['afterCallDt'], 'modDt'=>$sCurrDt
                        ];
                        DBUtil2::update(ImsDBName::TODO_RESPONSE, $aUpdateTodoResponse, new SearchVo('reqSno=?', $aCurrRequestInfo['sno']));
                    }
                }
                //입찰예정일자가 있으면 TO DO LIST에 update
                if (isset($params['customer_data']['bidDt']) && $params['customer_data']['bidDt'] != '') {
                    $aCurrRequestInfo = DBUtil2::getOneBySearchVo(ImsDBName::TODO_REQUEST, new SearchVo(['eachSno=?', 'eachDiv=?'], [$mUpsertSno, 'scc_bid']));
                    if (isset($aCurrRequestInfo['sno']) && (int)$aCurrRequestInfo['sno'] > 0) {
                        $aUpdateTodoRequest = [
                            'hopeDt'=> $params['customer_data']['bidDt'], 'subject'=>'[입찰 예정일] '.$aSalesCustomerInfo['customerName'],
                            'contents'=>'발굴고객메뉴에서 영업이력을 등록하여 자동등록된 요청건입니다. \r\n 후속연락 사유 : '.NkCodeMap::SALES_CUST_CONTENTS_AFTER_CALL_REASON[$aUpsertInfo['afterCallReason']].' \r\n 내용 : '.$aUpsertInfo['contents'].' \r\n 수정일시 : '.$sCurrDt,
                            'modDt'=>$sCurrDt
                        ];
                        DBUtil2::update(ImsDBName::TODO_REQUEST, $aUpdateTodoRequest, new SearchVo('sno=?', $aCurrRequestInfo['sno']));
                        $aUpdateTodoResponse = [
                            'managerSno'=>$iSalesManagerSno, 'expectedDt'=>$params['customer_data']['bidDt'], 'modDt'=>$sCurrDt
                        ];
                        DBUtil2::update(ImsDBName::TODO_RESPONSE, $aUpdateTodoResponse, new SearchVo('reqSno=?', $aCurrRequestInfo['sno']));
                    }
                }
            }
        }
    }

    //고객등록+프로젝트등록+스타일등록
    public function registProjectBySaleCustomer($params) {
        $aSnos = $params['saleCustomerSnos'];
        if (!is_array($aSnos) || count($aSnos) == 0) return ['data'=>'접근오류'];

        $tableInfo=[
            'a' => ['data' => [ ImsDBName::SALES_CUSTOMER ], 'field' => ["a.*"]],
        ];
        $searchVo = new SearchVo();
        $searchVo->setWhere("a.sno in (" . implode(',', $aSnos) . ") ");
        $aSCList = DBUtil2::getComplexList(DBUtil2::setTableInfo($tableInfo,false), $searchVo, false, false, true);
        $aSCListBySno = [];
        foreach ($aSCList as $val) $aSCListBySno[$val['sno']] = $val;

        //무결성 검사
        $aStyleCodes = [];
        foreach ($aSnos as $val) {
            if (!isset($aSCListBySno[$val])) return ['data'=>'존재하지 않는 업체가 있습니다.'];
            if ($aSCListBySno[$val]['styleCode'] == null || $aSCListBySno[$val]['styleCode'] == '') return ['data'=>$aSCListBySno[$val]['customerName'].' 업체에 고객사 이니셜을 입력해 주세요.'];
            if ($aSCListBySno[$val]['customerSno'] != null && $aSCListBySno[$val]['customerSno'] != 0) return ['data'=>$aSCListBySno[$val]['customerName'].' 업체는 이미 등록되었습니다.'];
            $aStyleCodes[] = $aSCListBySno[$val]['styleCode'];
        }
        //무결성 검사 - 고객테이블에서 이미 존재하는 이니셜이 있는지 확인
        $oCustomerSearchVo = new SearchVo();
        $oCustomerSearchVo->setWhere("styleCode in ('" . implode("','", $aStyleCodes) . "') ");
        $aCustomerList = DBUtil2::getListBySearchVo(ImsDBName::CUSTOMER, $oCustomerSearchVo);
        if (count($aCustomerList) > 0) {
            return ['data'=>$aCustomerList[0]['styleCode'].' 고객사 이니셜은 이미 존재하는 값입니다.'];
        }

        $sCurrDt = date('Y-m-d H:i:s');
        $imsProjectService = SlLoader::cLoad('imsv2', 'ImsProjectService');
        $imsStyleService = SlLoader::cLoad('ims', 'ImsStyleService');
        foreach ($aSnos as $val) {
            //고객 등록
            $aInsertCustomer = [
                'customerName'=>$aSCListBySno[$val]['customerName'], 'styleCode'=>$aSCListBySno[$val]['styleCode'], 'busiCateSno'=>$aSCListBySno[$val]['busiCateSno'],
                'salesManagerSno'=>$aSCListBySno[$val]['salesManagerSno'], 'contactName'=>$aSCListBySno[$val]['contactName'], 'contactPosition'=>'',
                'contactMobile'=>$aSCListBySno[$val]['contactPhone'], 'contactPreference'=>'',
                'contactEmail'=>$aSCListBySno[$val]['contactEmail'], 'contactAddress'=>'', 'contactAddressSub'=>'',
            ];
            $iInsertCustomerSno = DBUtil2::insert(ImsDBName::CUSTOMER, $aInsertCustomer);
            //프로젝트 등록
            $aProducts = [];
            if ($aSCListBySno[$val]["jsonExpectSales"] != null && $aSCListBySno[$val]["jsonExpectSales"] != '') $aProducts = json_decode($aSCListBySno[$val]["jsonExpectSales"], true);
            $aInsertProject = [
                'project' =>[
                    'projectStatus'=>10, 'customerSno'=>$iInsertCustomerSno, 'salesManagerSno'=>$aSCListBySno[$val]['salesManagerSno'],
                    'customerDeliveryDtConfirmed'=>'y',
                    'bidType2'=>array_search($aSCListBySno[$val]['buyMethod'], ImsCodeMap::BID_TYPE),
                    'sampleCount'=>0, 'projectYear'=>date('y'),
                    'projectSeason'=> count($aProducts) > 0 ? $aProducts[0]['prdSeason'] : 'ALL',
                    'syncProduct'=>'y', 'regDt'=>$sCurrDt,
                ],
                'projectExt' =>[
                    'sno'=>'', 'projectSno'=>'', 'designWorkType'=>0, 'targetSalesYear'=>date('Y'), 'extAmount'=>$aSCListBySno[$val]['totalExpectSales'], 'exMeeting'=>$aSCListBySno[$val]['bidDt'], 'regDt'=>$sCurrDt,
                ],
            ];
            $iInsertProjectSno = $imsProjectService->saveImsProject($aInsertProject)['data'];
            //스타일 등록
            $aInsertProduct = [];
            if (count($aProducts) > 0) {
                foreach ($aProducts as $val2) {
                    $aTmp = [
                        'projectSno'=>$iInsertProjectSno, 'customerSno'=>$iInsertCustomerSno,
                        'styleCode'=>date('y').' '.$val2['prdSeason'].' '.$aSCListBySno[$val]['styleCode'].' '.$val2['prdStyle'],
                        'productName'=>$val2['productName'], 'prdYear'=>date('Y'),
                        'prdSeason'=>$val2['prdSeason'], 'prdStyle'=>$val2['prdStyle'],
                        'prdExQty'=>$val2['saleQty'], 'currentPrice'=>$val2['unitPrice'],
                    ];
                    $aInsertProduct[] = $aTmp;
                }
                $imsStyleService->saveStyleList($aInsertProduct);
            }

            DBUtil2::update(ImsDBName::SALES_CUSTOMER, ['customerType'=>'50', 'customerSno'=>$iInsertCustomerSno], new SearchVo('sno=?', $aSCListBySno[$val]['sno']));
        }

        return ['data'=>''];
    }

    //일일 집계정보 리스트
    public function getListSalesCustomerStats($params) {
        $oSV = new SearchVo();
        $oSV->setOrder('statsDt desc');
        $this->refineCommonCondition($params, $oSV);

        $tableInfo=[
            'a' => ['data' => [ ImsDBName::SALES_CUSTOMER_STATS ], 'field' => ["a.*"]],
        ];
        $allData = DBUtil2::getComplexListWithPaging(DBUtil2::setTableInfo($tableInfo,false), $oSV, $params, false, true);
        foreach ($allData['listData'] as $key => $val) {
            for ($i = 1; $i <= 4; $i++) {
                $allData['listData'][$key]['jsonIncCustomer'.$i] = json_decode($val['jsonIncCustomer'.$i], true);
                $allData['listData'][$key]['jsonDecCustomer'.$i] = json_decode($val['jsonDecCustomer'.$i], true);
            }
        }

        return [
            'pageEx' => $allData['pageData']->getPage('#'),
            'page' => $allData['pageData'],
            'list' => $allData['listData'],
            'fieldData' => []
        ];
    }


    //고객 담당자(sl_imsCustomerContact) 리스트 가져오기
    public function getListCustomerContact($params) {
        $iCustomerSno = (int)$params['customerSno'];
        if ($iCustomerSno === 0) return [];

        $aTableInfo=[
            'a' => ['data' => [ ImsDBName::CUSTOMER_CONTACT ], 'field' => ["a.*"]],
            'b' => ['data' => [ ImsDBName::CUSTOMER, 'LEFT OUTER JOIN', 'a.customerSno = b.sno' ], 'field' => ["if(a.cContactName = b.contactName and a.cContactMobile = b.contactMobile, 1, 2) as mainContactYn"]],
        ];

        $searchVo = new SearchVo('a.customerSno=?', $iCustomerSno);
        $searchVo->setOrder('a.regDt desc');

        $aList = DBUtil2::getComplexList(DBUtil2::setTableInfo($aTableInfo,false), $searchVo, false, false, true);
        //filed data 정리
        $aTmpFldList = DBTableField::callTableFunction(ImsDBName::CUSTOMER_CONTACT);
        $aFldList = [];
        $aSkipUpsertFlds = ['regManagerSno','regDt','modDt']; //frontend-backend 파라메터에서 제외. upsert하는 메소드에서 따로 값 넣어야함
        foreach ($aTmpFldList as $val) {
            if (!in_array($val['val'], $aSkipUpsertFlds)) {
                $aTmp = ['type' => 'c', 'col' => 10, 'class' => '', 'name' => '', 'title' => '' ];
                //예외
                if ($val['val'] == 'cContactName') $aTmp['type'] = 'modal_contact_detail'; //별도 액션
                if (in_array($val['val'], ['sno', 'customerSno'])) $aTmp['skip'] = true; //리스트에서 숨김
                //공통
                if ($val['typ'] == 'i') {
                    $aTmp['type'] = 'i';
                    $aTmp['class'] = 'ta-r';
                }
                $aTmp['name'] = $val['val'];
                $aTmp['title'] = $val['name'];
                $aFldList[] = $aTmp;
            }
        }

        return [
            'pageEx' => [],
            'page' => [],
            'list' => $aList,
            'fieldData' => $aFldList,
        ];
    }
    //고객담당자 upsert
    public function setCustomerContact($params) {
        $iSno = (int)$params['data']['sno'];
        unset($params['data']['sno']);
        if ($iSno === 0) {
            $params['data']['regManagerSno'] = \Session::get('manager.sno');
            $params['data']['regDt'] = date('Y-m-d H:i:s');
            DBUtil2::insert(ImsDBName::CUSTOMER_CONTACT, $params['data']);
        } else {
            $params['data']['modDt'] = date('Y-m-d H:i:s');
            DBUtil2::update(ImsDBName::CUSTOMER_CONTACT, $params['data'], new SearchVo('sno=?', $iSno));
        }
    }
    //고객담당자 메인담당자 지정(sl_imsCustomer update)
    public function setCustomerMainContact($params) {
        $iSno = (int)$params['sno'];
        $aInfo = DBUtil2::getOne(ImsDBName::CUSTOMER_CONTACT, 'sno', $iSno);
        if (!isset($aInfo['sno']) || $aInfo['sno'] == 0) {
            return false;
        }
        $iCustomerSno = (int)$aInfo['customerSno'];
        $aUpdate = [];
        foreach ($aInfo as $key => $val) {
            if (strpos($key, 'cC') !== false) {
                $aUpdate[str_replace('cC', 'c', $key)] = $val;
            }
        }
        DBUtil2::update(ImsDBName::CUSTOMER, $aUpdate, new SearchVo('sno=?', $iCustomerSno));
    }

}