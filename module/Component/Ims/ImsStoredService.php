<?php
namespace Component\Ims;


use Controller\Admin\Ims\ImsPsNkTrait;
use SlComponent\Database\DBUtil2;
use SlComponent\Database\SearchVo;
use SlComponent\Util\SitelabLogger;
use SlComponent\Util\SlLoader;
use SiteLabUtil\SlCommonUtil;

class ImsStoredService
{
    use ImsListTrait;
    use ImsPsNkTrait;

    private $instance_stored_sql;
    private $dpData;

    public function __construct(){
        $this->instance_stored_sql = SlLoader::sqlLoad(__CLASS__, false);
        $this->dpData = [
            'no' => ['name'=>'번호','col'=>'2','skip'=>true],
            'customerUsageName' => ['name'=>'사용처<br/>고객명','col'=>'5'],
            'fabricInfo' => ['name'=>'비축 자재명/혼용율/색상','col'=>'7'],
        ];
        $aTmpDpData = [
            'btn_input' => ['name'=>'입고 등록','col'=>'4'],
            'total_remain' => ['name'=>'총<br/>잔여수량','col'=>'4'],
            'no_input' => ['name'=>'입고<br/>번호','col'=>'3'],
            'inputDt' => ['name'=>'입고일','col'=>'5'],
            'expireDt' => ['name'=>'유효 기간<br/>만료일','col'=>'5'],
            'unitPrice' => ['name'=>'단가','type'=>'number','col'=>'4'],
            'inputQty' => ['name'=>'입고수량','type'=>'number','col'=>'4'],
            'total_input_price' => ['name'=>'금액','col'=>'4'],
            'total_out_qty' => ['name'=>'사용수량','col'=>'4'],
            'remain_qty' => ['name'=>'잔여수량','col'=>'4'],
            'inputUnit' => ['name'=>'단위','col'=>'2'],
            'btn_out' => ['name'=>'출고 등록','col'=>'4'],
            'inputReason' => ['name'=>'입고사유','col'=>'6'],
            'inputOwn' => ['name'=>'소유구분','col'=>'5'],
//소유고객->안씀            'customerName' => ['name'=>'소유권','col'=>'5'],
            'inputLocation' => ['name'=>'저장위치','col'=>'8'],
            'reqInputNm' => ['name'=>'등록자','col'=>'3'],
            'inputMemo' => ['name'=>'비고','col'=>'7'],
        ];
        if( in_array(\Session::get('manager.managerId'),ImsCodeMap::STORE_MANAGER) ) {
            $this->dpData['delFabric'] = ['name'=>'자재<br/>관리','col'=>'3'];
        }
        $this->dpData = array_merge($this->dpData, $aTmpDpData);
        if( in_array(\Session::get('manager.managerId'),ImsCodeMap::STORE_MANAGER) ) {
            $this->dpData['btn_update'] = ['name'=>'수정/삭제','col'=>'4'];
        }
    }

    public function getDisplayStored(){
        SlCommonUtil::createHtmlTableTitle($this->dpData);
        return $this->dpData;
    }

    /**
     * 비축 원부자재 리스트 불러오기(비축 원부자재 리스트 페이지, 비축 원부자재 리스트 엑셀다운로드))
     * @param $params
     * @return array
     */
    public function getListStored($params){
        //where 설정
        $searchData['condition'] = $params;
        $searchVo = new SearchVo(['a.delFl=?','b.delFl=?'], ['n','n']);
        $this->refineCommonCondition($searchData['condition'], $searchVo);

        //table group by 설정
        $searchVo->setGroup('a.sno');
        //table order by 설정
        $searchData['condition']['sort_nk'] = $searchData['condition']['sort'];
        unset($searchData['condition']['sort']);
        $this->setListSortNk($searchData['condition']['sort_nk'], $searchVo);

        if (!isset($searchData['page']) || (int)$searchData['page'] == 0) $searchData['page'] = gd_isset($searchData['condition']['page'], 1);
        if (!isset($searchData['pageNum']) || (int)$searchData['pageNum'] == 0) $searchData['pageNum'] = gd_isset($searchData['condition']['pageNum'], 15000);
        $allData = DBUtil2::getComplexListWithPaging($this->instance_stored_sql->getStoredTable($searchData), $searchVo, $searchData, false, true);
        $pageEx = $allData['pageData']->getPage('#');
        $aResponse = [
            'pageEx' => $pageEx,
            'page' => $allData['pageData'],
            'list' => $allData['listData']
        ];

        //입고건별 출고수량 select
        $searchVo = new SearchVo(['a.delFl=?','b.delFl=?'], ['n','n']);
        $searchVo->setGroup('b.sno');
        $aOutputList = DBUtil2::getComplexListWithQuery($this->instance_stored_sql->getStoredInputTable(), $searchVo, false, false, true)['list'];
        $aOutputQtyByInputSno = [];
        foreach ($aOutputList as $val) {
            $aOutputQtyByInputSno[$val['sno']] = (int)$val['outQty'];
        }

        //고객명 가져오기(select로 가져온 레코드의 고객명이 null인 경우 아예 안가져옴(join으로 가져온 값이라서)) -> 소유고객 안씀
//        foreach ($aResponse['list'] as $key => $val) {
//            $aResponse['list'][$key]['custSno'] = explode('___namku___',$val['customerSno']);
//            $aResponse['list'][$key]['customerSno'] = explode('___namku___',$val['customerSno']);
//            $aTmpNm = explode('___namku___',$val['customerName']);
//            $iTmp = 0;
//            foreach ($aResponse['list'][$key]['customerSno'] as $k2 => $v2) {
//                if ($v2 == 0) $aResponse['list'][$key]['customerSno'][$k2] = '';
//                else {
//                    $aResponse['list'][$key]['customerSno'][$k2] = $aTmpNm[$iTmp];
//                    $iTmp++;
//                }
//            }
//        }
        //데이터 정제
        $aInputFldNms = ['customerSno','inputSno','unitPrice','inputQty','inputUnit','inputReason','inputOwn','inputLocation','inputMemo','reqInputNm','inputDt','expireDt'];
        foreach ($aResponse['list'] as $key => $val) {
            foreach ($val as $key2 => $val2) {
                if (in_array($key2, $aInputFldNms)) {
                    $aResponse['list'][$key][$key2] = explode('___namku___', $val2);
                }
            }
        }
        //2 depth rowspan(사용처고객) 값 산출
        $aRowSpanByUsageSno = [];
        foreach ($aResponse['list'] as $key => $val) {
            if (!isset($aRowSpanByUsageSno[$val['customerUsageSno']])) $aRowSpanByUsageSno[$val['customerUsageSno']] = count($val['inputSno']);
            else $aRowSpanByUsageSno[$val['customerUsageSno']] += count($val['inputSno']);
        }
        //데이터 정제(계산)
        foreach ($aResponse['list'] as $key => $val) {
            $iTotalInputQty = 0;
            $iTmpNo = 1;
            foreach ($val['inputSno'] as $key2 => $val2) {
                $aResponse['list'][$key]['no_input'][$key2] = $iTmpNo;
                $iTmpNo++;
                //금액. 출고수량 계산안됨
                $aResponse['list'][$key]['total_input_price'][$key2] = $aResponse['list'][$key]['unitPrice'][$key2] * $aResponse['list'][$key]['inputQty'][$key2];
                if ($aResponse['list'][$key]['inputOwn'][$key2] != 3) $aResponse['list'][$key]['customerSno'][$key2] = NkCodeMap::STORED_INPUT_OWN[$aResponse['list'][$key]['inputOwn'][$key2]];
                $aResponse['list'][$key]['inputOwn'][$key2] = NkCodeMap::STORED_INPUT_OWN[$aResponse['list'][$key]['inputOwn'][$key2]];
                $aResponse['list'][$key]['total_out_qty'][$key2] = isset($aOutputQtyByInputSno[$val2]) ? $aOutputQtyByInputSno[$val2] : 0;
                $aResponse['list'][$key]['remain_qty'][$key2] = $aResponse['list'][$key]['inputQty'][$key2] - $aResponse['list'][$key]['total_out_qty'][$key2];
                $iTotalInputQty += $aResponse['list'][$key]['remain_qty'][$key2];
                //1 depth rowspan(자재) : 자재group의 입고건row들중 최상위row만 값주고 나머지는 0 -> frontend에서 이 변수값 0이면 td 없앤다.
                $aResponse['list'][$key]['rowspan'][$key2] = $key2 === 0 ? count($val['inputSno']) : 0;
                //2 depth rowspan(사용처고객) : 사용처고객group의 입고건row들중 최상위row만 값주고 나머지는 0 -> frontend에서 이 변수값 0이면 td 없앤다.
                if ($key2 === 0 && isset($aRowSpanByUsageSno[$val['customerUsageSno']])) { // 같은 사용처에 첫번째 자재row 다음 row에 값 안주려고 unset() line추가하고 조건추가(&& isset($aRowSpanByUsageSno[$val['customerUsageSno']]))
                    $aResponse['list'][$key]['rowspan_depth2'][$key2] = $aRowSpanByUsageSno[$val['customerUsageSno']];
                    unset($aRowSpanByUsageSno[$val['customerUsageSno']]);
                } else $aResponse['list'][$key]['rowspan_depth2'][$key2] = 0;
            }
            $aResponse['list'][$key]['total_remain'] = $iTotalInputQty;
        }
        //리스트 입고건별로 재정리 -> rowspan값 자재/사용처고객 1번째 row에만 주고 다른 row들 0값 주기 -> rowspan값 0이면 td 표시하지 않음
        $aRowPerInput = $aRowDepth1Info = [];
        foreach ($aResponse['list'] as $key => $val) { //자재건 반복. $key == 자재건 number
            $aTmp = [];
            foreach ($val as $key2 => $val2) { //fld 반복. $key2 == depth1(자재,입고) fld name
                if (!is_array($val2)) $aTmp[$key2] = $val2;
            }
            $aRowDepth1Info[$key] = $aTmp;
        }
        //최하위 row(입고건)별로 배열 담기
        foreach ($aResponse['list'] as $key => $val) { //자재건 반복. $key == 자재건 number
            foreach ($val['inputSno'] as $key2 => $val2) { //입고건 반복. $key2 == 입고건 number
                $aTmp = $aRowDepth1Info[$key];
                foreach ($val as $key22 => $val22) { //fld 반복. $key22 == depth1(자재,입고) fld name
                    if (is_array($val22)) $aTmp[$key22] = $val22[$key2];
                }
                $aRowPerInput[] = $aTmp;
            }
        }
        $aResponse['list'] = $aRowPerInput;

        return $aResponse;
    }

    //출고리스트 가져오기
    public function getListStoredOutput($params){
        $iInputSno = (int)$params['inputSno'];
        if ($iInputSno === 0) {
            echo "접근오류";
            exit;
        }
        unset($params['inputSno']);

        //where 설정
        $searchData['condition'] = $params;
        $searchVo = new SearchVo(['a.delFl=?', 'b.delFl=?', 'b.sno=?'], ['n', 'n', $iInputSno]);
        $this->refineCommonCondition($searchData['condition'], $searchVo);

        //table order by 설정
        $searchData['condition']['sort_nk'] = $searchData['condition']['sort'];
        unset($searchData['condition']['sort']);
        $this->setListSortNk($searchData['condition']['sort_nk'], $searchVo);

        if (!isset($searchData['page']) || (int)$searchData['page'] == 0) $searchData['page'] = gd_isset($searchData['condition']['page'], 1);
        if (!isset($searchData['pageNum']) || (int)$searchData['pageNum'] == 0) $searchData['pageNum'] = gd_isset($searchData['condition']['pageNum'], 15000);
        $allData = DBUtil2::getComplexListWithPaging($this->instance_stored_sql->getStoredOutputTable($searchData), $searchVo, $searchData, false, true);
        $pageEx = $allData['pageData']->getPage('#');
        $aResponse = [
            'pageEx' => $pageEx,
            'page' => $allData['pageData'],
            'list' => $allData['listData']
        ];

        foreach ($aResponse['list'] as $key => $val) {
            $sCusNm = '';
            if ($aResponse['list'][$key]['inputOwn'] == 3 && $aResponse['list'][$key]['customerName'] != null) $sCusNm = ' ('.$aResponse['list'][$key]['customerName'].')';
            $aResponse['list'][$key]['inputOwn'] = NkCodeMap::STORED_INPUT_OWN[$aResponse['list'][$key]['inputOwn']].$sCusNm;
        }
        return $aResponse;
    }
    //자재 수정
    public function modifyStoredFabric($params) {
        $params['StoredFabric']['delFl'] = 'n';
        $iSno = (int)$params['StoredFabric']['sno'];
        unset($params['StoredFabric']['sno']);

        $searchVo = new SearchVo(['sno<>?', 'fabricName=?', 'fabricMix=?', 'color=?', 'customerUsageSno=?'], [$iSno,$params['StoredFabric']['fabricName'],$params['StoredFabric']['fabricMix'],$params['StoredFabric']['color'],$params['StoredFabric']['customerUsageSno']]);
        $aInfo = DBUtil2::getOneBySearchVo(ImsDBName::STORED_FABRIC, $searchVo);
        if (isset($aInfo['sno'])) {
            return ['data'=> 500,'msg'=>'이미 존재하는 자재입니다'];
        } else {
            $searchVo = new SearchVo('sno=?', $iSno);
            DBUtil2::update(ImsDBName::STORED_FABRIC, $params['StoredFabric'], $searchVo);
            return ['data'=> 200,'msg'=>'저장 완료'];
        }
    }

    //자재 입고 등록
    public function saveStoredFabricInput($params) {
        if (isset($params['StoredFabric'])) {
            $iFabricSno = DBUtil2::insert(ImsDBName::STORED_FABRIC, $params['StoredFabric']);
            $params['StoredFabricInput']['fabricSno'] = $iFabricSno;
        }
        $params['StoredFabricInput']['delFl'] = 'n';
        DBUtil2::insert(ImsDBName::STORED_FABRIC_INPUT, $params['StoredFabricInput']);

        return ['data'=> 0,'msg'=>'저장 완료'];
    }

    //입고건 수정
    public function modifyStoredFabricInput($params) {
        if (isset($params['StoredFabric'])) {
            $iFabricSno = DBUtil2::insert(ImsDBName::STORED_FABRIC, $params['StoredFabric']);
            $params['StoredFabricInput']['fabricSno'] = $iFabricSno;
        }
        $params['StoredFabricInput']['delFl'] = 'n';
        $iSno = (int)$params['StoredFabricInput']['sno'];
        unset($params['StoredFabricInput']['sno']);
        $searchVo = new SearchVo('sno=?', $iSno);
        DBUtil2::update(ImsDBName::STORED_FABRIC_INPUT, $params['StoredFabricInput'], $searchVo);

        return ['data'=> 0,'msg'=>'저장 완료'];
    }

    //원단 출고 등록
    public function saveStoredFabricOutput($aInsertOutput) {
        $aInsertOutput['delFl'] = 'n';
        DBUtil2::insert(ImsDBName::STORED_FABRIC_OUT, $aInsertOutput);

        return ['data'=> 0,'msg'=>'저장 완료'];
    }


}