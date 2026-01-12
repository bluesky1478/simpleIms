<?php
namespace SlComponent\Godo;

use Component\Member\Member;
use Component\Member\Util\MemberUtil;
use Component\Scm\ScmConst;
use SiteLabUtil\SlCommonUtil;
use SlComponent\Database\DBUtil;
use SlComponent\Database\DBUtil2;
use SlComponent\Database\SearchVo;
use SlComponent\Excel\SimpleExcelComponent;
use SlComponent\Util\ExcelCsvUtil;
use SlComponent\Util\PhpExcelUtil;
use SlComponent\Util\SitelabLogger;
use SlComponent\Util\SlCode;
use SlComponent\Util\SlCodeMap;
use Component\Storage\Storage;
use SlComponent\Util\SlLoader;

/**
 * 고객사 서비스
 * Class SlCode
 * @package SlComponent\Godo
 */
class ScmService {
    private $sql;

    const SCM_CONFIG_TABLE = 'sl_setScmConfig';
    private $storageName = 'http://innowork.hgodo.com';
    const DOC_PATH = 'scm/';

    public function __construct(){
        //$this->sql = \App::load(\SlComponent\Godo\Sql\MemberSql::class);
    }

    const CODE_GOLF = '2';
    const CODE_HONDA = '3';
    const CODE_JEIL = '4';
    const CODE_MAX = '5';
    const CODE_HANKOOK = '6';
    const CODE_INNOVER = '7';
    const CODE_TKE = '8';
    const CODE_RINNAI = '9';
    const CODE_MOOYOUNG = '10'; //
    const CODE_OTIS = '11';
    const CODE_YOUNGGU = '12'; // 5 / 3
    const CODE_SAMSUNG = '13';

    const POINT_REASON = [
        '01005001'	=> '상품구매 시 포인트 사용',
        '01005501'	=> '취소 시 사용 포인트 복원',
        '01005003'	=> '환불 시 사용 포인트 복원',
        '01005011'	=> '기타',
    ];

    /**
     * 저장시 메뉴 권한 변경
     * @param $managerData
     * @param $saveData
     * @throws \Exception
     */
    public function setManagerAuth($managerData, $saveData){
        $permitMenu = json_decode($managerData['permissionMenu'],true);
        $authList = [];
        //회원 리스트.
        $authList[] = SlCodeMap::SCM_MENU_ACCEPT_MEMBER;
        //주문 리스트.
        $authList[] = SlCodeMap::SCM_MENU_ACCEPT_ORDER;

        $authList2[] = 'slab00100';
        $authList2[] = 'slab00101';
        $authList2[] = 'slab00022'; //출고 통계
        $authList2[] = 'slab00023'; //교환 반품

        //재고표 관리하는 업체
        if( 6 == $saveData['scmNo'] || 8 == $saveData['scmNo']){
            $authList[] = 'slab00221';  //(신)재고표
            $authList2[] = 'slab00221'; //(신)재고표
        }

        //포인트 관리
        /*$availScm = [7];
        if(in_array($saveData['scmNo'], $availScm)){
            $authList[] = 'slab00231';   //포인트관리
            $authList2[] = 'slab00231';  //포인트관리
        }*/

        //TKE (정산)
        $availScm = [8];
        if(in_array($saveData['scmNo'], $availScm)){
            $authList[] = 'slab00260';   //정산관리.
            $authList2[] = 'slab00260';  //정산관리
        }

        //배송지점을 사용하는 곳
        $scmConfig = $this->getScmConfigData($saveData['scmNo']);
        if( 'y' == $scmConfig['deliverySelectFl'] ){
            $authList[] = 'slab00073';   //배송지점 리스트
            $authList2[] = 'slab00073';  //배송지점 리스트
        }

        //한국타이어 별도
        if( 6 == $saveData['scmNo'] ){
            $authList[] = 'slab00108';  //한타 별도 재고표
            $authList2[] = 'slab00108'; //한타 별도 재고표
        }

        //현대EL + 아시아나 별도 (교환 리스트)  || SlCommonUtil::isDev()
        if( 34 == $saveData['scmNo'] || 32 == $saveData['scmNo'] ){
            $authList[] = 'slab00072';  //교환리스트
            $authList2[] = 'slab00072'; //교환리스트
        }

        //21 오티스 별도였던 것
        /*if( 7 == $saveData['scmNo'] || SlCommonUtil::isDev() ){
            $authList[] = 'slab00070';  //주문수량집계
            $authList2[] = 'slab00070'; //주문수량집계
        }*/

        //아시아나 별도  || SlCommonUtil::isDev()
        if( 34 == $saveData['scmNo']){
            $authList[] = 'slab00069';  //지급이력
            $authList2[] = 'slab00069'; //지급이력
        }


        $permitMenu['permission_1'] = ['godo00445','godo00138']; //탑 레벨

        $permitMenu['permission_2']['godo00138'] = ['godo00804'];
        $permitMenu['permission_2']['godo00445'] = ['slab00016','slab00013','slab00080'];

        $permitMenu['permission_3']['godo00804'] = ['godo00139'];
        $permitMenu['permission_3']['slab00080'] = ['slab00081','slab00082','slab00083','slab00084']; //B2B 제안 정보

        //회원 정보 관리
        /*$memberMenuList = [
            'godo00139', 'godo00804' , 'godo00138', 'godo00000'
        ];
        foreach($memberMenuList as $memberMenu){
            $authList[] = $memberMenu;
            $authList2[] = $memberMenu;
        }*/

        $permitMenu['permission_3'][SlCodeMap::SCM_MENU_ACCEPT_TOP] = $authList;
        $permitMenu['permission_3']['slab00013'] = $authList2;

        //SCM_MENU_ACCEPT_TOP

        DBUtil2::update(DB_MANAGER,  ['permissionMenu' => json_encode($permitMenu, JSON_UNESCAPED_UNICODE) ] , new SearchVo('sno=?' , $managerData['sno']) );
    }


    /**
     * 공급처에 연결할 카테고리 반환(1차 카테고리)
     * @return array
     */
    public function getScmCategory(){
        $sql = "SELECT * FROM es_categoryGoods WHERE LENGTH(cateCd) = 3";
        return SlCommonUtil::listToMap(DBUtil2::runSelect($sql), 'cateCd', 'cateNm' );;
    }

    /**
     * 파일 업로드
     * @param $tempFileName
     * @param $filePath
     * @return mixed
     */
    public function uploadFile($tempFileName, $filePath){
        Storage::disk(Storage::PATH_CODE_ETC, $this->storageName)->upload($tempFileName,$filePath); //Upload
        return Storage::disk(Storage::PATH_CODE_ETC, $this->storageName)->getHttpPath($filePath);
    }

    /**
     * 공급사 커스텀 정보 저장
     * @param $saveData
     * @param $fileData
     * @throws \Exception
     */
    public function saveScmCustomInfo($saveData, $fileData){
        $scmData = $this->getScmConfigData($saveData['scmNo']);
        $uploadFileInfo = [];
        /*SitelabLogger::logger('T----E----S----T');
        SitelabLogger::logger($saveData);
        SitelabLogger::logger($fileData);*/
        //scmNo
        foreach($fileData['files']['name'] as $key => $fileName){
            //SitelabLogger::logger($key . ' : ' . $fileName);
            if( !empty($fileName) ){
                $dataFileName = md5($fileName);
                $filePath = self::DOC_PATH.$saveData['scmNo'].'/'.$dataFileName;
                $uploadPath = $this->uploadFile($fileData['files']['tmp_name'][$key], $filePath);
                $uploadFileInfo[] = [
                    'name' => $fileName,
                    'path' => $uploadPath,
                ];
            }
        }

        if( empty($scmData['scmNo']) ){
            $saveData['files'] = json_encode($uploadFileInfo);
            DBUtil2::insert(ScmService::SCM_CONFIG_TABLE, $saveData);
        }else{
            if( empty($scmData['files']) ){
                $saveData['files'] = json_encode($uploadFileInfo);
            }else{
                $beforeFiles = json_decode($scmData['files']);
                $saveData['files'] = json_encode(array_merge($beforeFiles, $uploadFileInfo));
            }
            DBUtil2::update(ScmService::SCM_CONFIG_TABLE, $saveData, new SearchVo('scmNo=?' , $scmData['scmNo']) , ['directAddressFl', 'deliverySelectFl' , 'orderAcceptFl', 'memberAcceptFl', 'files'] );
        }

        //권한 처리
        $managerList = DBUtil2::getList(DB_MANAGER, 'scmNo', $saveData['scmNo']);
        foreach($managerList as $managerData){
            $this->setManagerAuth($managerData, $saveData);
        }
    }


    /**
     * 공급사 커스텀 설정 정보 반환
     * @param $scmNo
     * @return mixed
     */
    public function getScmConfigData($scmNo){
        $scmData = DBUtil::getOne(ScmService::SCM_CONFIG_TABLE, 'scmNo', $scmNo);
        $scmData['files'] = json_decode($scmData['files'], true);
        return $scmData;
    }

    /**
     * 배송지 저장
     * @param $param
     * @throws \Exception
     */
    public function saveScmAddress($param){
        DBUtil2::merge('sl_setScmDeliveryList', $param, new SearchVo('sno=?', $param['sno']) );
    }

    /**
     * 배송지 제거
     * @param $param
     * @throws \Exception
     */
    public function deleteScmAddress($param){
        DBUtil2::delete('sl_setScmDeliveryList', new SearchVo('sno=?', $param['sno']) );
    }

    /**
     * SCM File 제거
     */
    public function deleteScmFile($param){
        $scmData = $this->getScmConfigData($param['scmNo']);
        unset($scmData['files'][$param['fileKey']]);
        $updateFileJson = json_encode($scmData['files']);
        DBUtil2::update('sl_setScmConfig', ['files'=>$updateFileJson] ,new SearchVo('scmNo=?', $param['scmNo']));
    }

    /**
     * 배송지 목록 반환
     * @param $scmNo
     * @return mixed
     */
    public function getScmAddressList($scmNo){
        //subject in ( '영구이사208호', '영구이사209호', '영구이사223호', '영구이사235호', '영구이사241호', '영구이사270호', '영구이사277호', '영구이사280호', '영구이사340호', '영구이사341호', '영구이사453호', '영구이사454호' ) AND
        return DBUtil::getList('sl_setScmDeliveryList', "scmNo", $scmNo, 'sno');
    }

    /**
     * 공급사번호로 카테고리 번호 반환
     * @param $scmNo
     * @return mixed
     */
    public function getScmCategoryCode($scmNo){
        return $this->getScmConfigData($scmNo)['cateCd'];
    }

    /**
     * 주소 수정 가능 여부 셋팅
     * @param $scmNo
     * @param $controller
     */
    public function setDeliverySelectFl($scmNo, $controller){

        $scmConfigData = $this->getScmConfigData($scmNo);
        $deliverySelectFl = $scmConfigData['deliverySelectFl'];
        $directAddressFl = $scmConfigData['directAddressFl'];

        //파트너 사의 경우는 제외
        $memberData = DBUtil2::getOne('sl_setMemberConfig', 'memNo', \Session::get('member.memNo') );
        if(  2 ==  $memberData['memberType'] ){
            $deliverySelectFl = 'n';
        }

        if( 'y' == $deliverySelectFl ){

            $deliveryList = $this->getScmAddressList($scmNo);

            //230822 추가 : 이노버 사이트에서 TKE 주문시 배송지는 모두 자기 이름.
            /*if( 8 == MemberUtil::getMemberScmNo(\Session::get('member.memNo')) && 'innoverb2b.com'  === \Request::getDefaultHost() ){
                foreach($deliveryList as $deliveryKey => $deliveryData){
                    $deliveryData['receiverName'] = \Session::get('member.memNm');
                    $deliveryData['receiverCellPhone'] = \Session::get('member.cellPhone');
                    $deliveryList[$deliveryKey] = $deliveryData;
                }
            }*/

            $controller->setData('deliveryList', $deliveryList); //주소지
            $controller->setData('isNotModifyAddress', true); //수정 불가

            if( 'y' == $directAddressFl ){
                $controller->setData('isDirectAddress', true);
            }else{
                $controller->setData('isDirectAddress', false);
            }

        }else{
            $controller->setData('isNotModifyAddress', false); //수정 가능
            $controller->setData('isDirectAddress', true); //직접 입력 가능
        }
        $controller->setData('isRequiredBranch', false);

        //주소 수정 불가면 다이렉트 입력가능 체크
        //이외는 TRUE


        //TKE MASTER 는 자율 입력
        if( 12 == \Session::get('member.groupSno') ){
            $controller->setData('isDirectAddress', true); //직접 입력 가능
        }

    }

    public function addAddressBatch($files, $scmNo){
        $FIELD_NAME = 1;
        $startRowCnt = 1;

        $result = ExcelCsvUtil::checkAndRead($files,$startRowCnt);

        if( empty($result['isOk'])  ){
            throw new \Exception($result['failMsg']);
        }

        $sheetData = $result['data']->sheets[0];
        $sheetData = $sheetData['cells'];
        $fieldDataList = array();

        $fieldDataList[1] = [ '배송지명' , 'subject' ] ;
        $fieldDataList[2] = [ '우편번호' , 'receiverZonecode' ] ;
        $fieldDataList[3] = [ '주소' , 'receiverAddress' ] ;
        $fieldDataList[4] = [ '주소상세' , 'receiverAddressSub' ] ;
        $fieldDataList[5] = [ '전화번호' , 'receiverCellPhone' ] ;
        $fieldDataList[6] = [ '수령자명' , 'receiverName' ] ;

        //$fieldDataList[7] = [ '소속팀' , 'teamList' ] ;

        foreach( $sheetData as $idx => $data ){

            $teamList = explode(',',$data[7]);

            /*foreach( $teamList as $team ){
                $refineTeamName = trim($team);
                $refineDeliveryName = trim($data[1]);
                DBUtil2::update('sl_setMemberConfig', ['deliveryName'=>$refineDeliveryName],new SearchVo('teamName=?', $refineTeamName));
            }*/
            $saveData = [];
            if( ($startRowCnt+1) > $idx ) continue;
            foreach( $fieldDataList as $key => $value ){
                if( 4 == $key  ){
                    if( empty($data[$key]) ){
                        $saveData[$value[$FIELD_NAME]] = '-';
                    }else{
                        $saveData[$value[$FIELD_NAME]] = $data[$key];
                    }
                }else{
                    $saveData[$value[$FIELD_NAME]] = $data[$key];
                }
            }
            $saveData['scmNo'] = $scmNo;

            //DBUtil2::insert('sl_setScmDeliveryList', $saveData);
            $beforeData = DBUtil2::getOne('sl_setScmDeliveryList','receiverAddress',$saveData['receiverAddress']);
            if( !empty($beforeData) ){
                DBUtil2::update('sl_setScmDeliveryList', $saveData, new SearchVo('receiverAddress=?',$saveData['receiverAddress']) );
            }else{
                DBUtil2::insert('sl_setScmDeliveryList', $saveData);
            }

        }

    }

    /**
     * 매장유형 - 로고 디자인에대한 옵션 정제
     * @param $controller
     */
    public function setRefineGoodsOption($controller) {
        //이 상품은 로고 적용 상품의 경우
        $goodsViewData = $controller->getData('goodsView');
        if( 'd' != $goodsViewData['optionDisplayFl'] ){
            $newGoodsViewData = $this->setRefineGoodsOptionSingle($goodsViewData);
        }else{
            $newGoodsViewData = $this->setRefineGoodsOptionDivision($goodsViewData);
        }

        /*foreach($newGoodsViewData['option'] as $key => $value){
            $value['stockCnt'] = 5;
            $newGoodsViewData['option'][$key] = $value;
        }*/

        $controller->setData('goodsView', $newGoodsViewData);
        //gd_debug( $goodsViewData['option'], $newGoodsViewData );

        //제작상품의 경우
        $goodsNo = \Request::get()->get('goodsNo');
        $controller->setData('produceGoodsTargetGoodsNo', SlCodeMap::PRODUCE_GOODS_INFO[$goodsNo]);

    }

    /**
     * 일반 옵션 처리
     * @param $goodsViewData
     * @return mixed
     */
    public function setRefineGoodsOptionSingle($goodsViewData) {
        $excludeGoodsNoList = [
            1000000319,
            1000000318,
            1000000317,
            1000000316,
            1000000315,
            1000000314,
            1000000313,
            1000000312,
            1000000311,
            1000000310,
            1000000309,
            1000000308,
            1000000306,
            1000000305,
        ];
        if(strpos($goodsViewData['optionName'], "로고 디자인") !== false && !in_array($goodsViewData['goodsNo'],$excludeGoodsNoList)) {
            $memberInfo = DBUtil2::getOne( DB_MEMBER,  'memNo', \Session::get('member.memNo') );
            $newOption = [];
            //기준 루프가 상품 매장 유형 기준으로.
            foreach( SlCodeMap::HANKOOK_GOODS_OPTION_TYPE as $typeKey => $typeValue){
                if(  ($memberInfo['hankookType'] & $typeKey) && ($goodsViewData['hankookType'] & $typeKey)  ) {
                    foreach($goodsViewData['option'] as $optionValue){
                        if(strpos($optionValue['optionValue'], $typeValue) !== false) {
                            $newOption[] = $optionValue;
                        }
                    }
                }
            }
            $goodsViewData['option']  = $newOption;
            //$controller->setData('goodsView', $goodsViewData);
        }
        return $goodsViewData;
    }

    /**
     * 분리형 옵션 처리
     * @param $goodsViewData
     * @return mixed
     */
    public function setRefineGoodsOptionDivision($goodsViewData) {

        $excludeGoodsNoList = [
            1000000319,
            1000000318,
            1000000317,
            1000000316,
            1000000315,
            1000000314,
            1000000313,
            1000000312,
            1000000311,
            1000000310,
            1000000309,
            1000000308,
            1000000306,
            1000000305,
        ];

        //분리형
        if(strpos($goodsViewData['optionName'][0], "로고 디자인") !== false && !in_array($goodsViewData['goodsNo'],$excludeGoodsNoList) ) {
            $memberInfo = DBUtil2::getOne( DB_MEMBER,  'memNo', \Session::get('member.memNo') );
            $newOptionDivision = [];
            //기준 루프가 상품 매장 유형 기준으로.
            foreach( SlCodeMap::HANKOOK_GOODS_OPTION_TYPE as $typeKey => $typeValue){
                if(  ($memberInfo['hankookType'] & $typeKey) && ($goodsViewData['hankookType'] & $typeKey)  ) {
                    foreach($goodsViewData['optionDivision'] as $optionValue){
                        if(strpos($optionValue, $typeValue) !== false) {
                            $newOptionDivision[] = $typeValue;
                        }
                    }
                }
            }
            $goodsViewData['optionDivision']  = $newOptionDivision;
            //$controller->setData('goodsView', $goodsViewData);
        }

        return $goodsViewData;
    }

    /**
     * 특정 조건(회원유형)에 맞는 배송비 변경
     * @param $data
     */
    public function setScmDelivery(&$data){
        $groupSno = \Session::get('member.groupSno');
        if( SlCommonUtil::isDev() ){
            if( 6 == MemberUtil::getMemberScmNo() && ( $groupSno == 4 ) ){
                $data['deliverySno'] = 2054;
            }
        }else{
            //TKE 이면서 회원 번호가 SVC(6) , MFG(7) 일 경우
            /*if( 8 == MemberUtil::getMemberScmNo() && ( $groupSno == 6 || $groupSno == 7 ) ){
                $data['deliverySno'] = 51;
            }*/
            //NI는 배송비 무료
            /*if( 8 == MemberUtil::getMemberScmNo() && $groupSno == 5 ){
                $data['deliverySno'] = 32;
            }*/
        }
    }

    /**
     * TKE 회원 중 svc , mfg 일 경우 fixed price 반값표기.
     * @param $controller
     */
    public function setListTkeFixedPrice($controller){
        $groupSno = \Session::get('member.groupSno');
        if( 8 == MemberUtil::getMemberScmNo() ){
            //TKE 이면서 회원 번호가 NI(5) SVC(6) , MFG(7) 일 경우
            $goodsList = $controller->getData('goodsList');
            $newGoodsList = [];
            if($groupSno == 5 || $groupSno == 6 || $groupSno == 7 ){
                $exclude = [
                ];
                foreach($goodsList as $key => $each){
                    foreach($each as $subKey => $subEach){
                        if( in_array($subEach['goodsNo'], $exclude) ){
                            continue;
                        }
                        $newGoodsList[$key][] = $subEach;
                    }
                    /*if( SlCommonUtil::isDevIp() ){
                        gd_debug($key);
                        gd_debug($each);
                    }*/

                }
                /*$goodsList = $controller->getData('goodsList');
                foreach($goodsList as $key => $each){
                    foreach($each as $eachKey => $eachSub){
                        $eachSub['fixedPrice'] = $eachSub['fixedPrice'] / 2;
                        $goodsList[$key][$eachKey] = $eachSub;
                        //gd_debug($eachSub);
                    }
                }*/
                $controller->setData('goodsList', $newGoodsList);
            }else{
                $exclude = [
                ];
                foreach($goodsList as $key => $each){
                    foreach($each as $subKey => $subEach){
                        if( in_array($subEach['goodsNo'], $exclude) ){
                            continue;
                        }
                        $newGoodsList[$key][] = $subEach;
                    }
                    /*if( SlCommonUtil::isDevIp() ){
                        gd_debug($key);
                        gd_debug($each);
                    }*/
                }
            }

            $controller->setData('goodsList', $newGoodsList);
        }
    }

    public function setTkeFixedPrice($controller){
        $groupSno = \Session::get('member.groupSno');
        //TKE 이면서 회원 번호가 SVC(6) , MFG(7) 일 경우
        if( 8 == MemberUtil::getMemberScmNo() && ( $groupSno == 6 || $groupSno == 7 ) ){
            $goodsView = $controller->getData('goodsView');
            $goodsView['fixedPrice'] = $goodsView['fixedPrice'] / 2;
            $controller->setData('goodsView', $goodsView);
        }
    }


    /**
     * 공급사 팝업 연결
     * @param $param
     * @throws \Exception
     */
    public function setLinkScmPopup($param){
        foreach( $param['scmList'] as $popupSno => $scmNo ){
            $searchVo = new SearchVo('popupSno=?', $popupSno);
            $data = DBUtil2::getOneBySearchVo('sl_scmPopup', $searchVo);
            if(!empty($data)){
                if( $data['scmNo'] != $scmNo){
                    DBUtil2::update('sl_scmPopup', ['scmNo'=>$scmNo], $searchVo);
                }
            }else{
                if( !empty($scmNo) ){
                    DBUtil2::insert('sl_scmPopup', [
                        'scmNo'=>$scmNo,
                        'popupSno'=>$popupSno,
                    ]);
                }
            }
        }
    }


    /**
     * 송장 엑셀 일괄 등록
     * @param $files
     * @param $params
     * @throws \Exception
     */
    public function setOrderInvoiceByExcel($files, $params){
        $startRowCnt = 1;
        $result = ExcelCsvUtil::checkAndRead($files,$startRowCnt);

        if( empty($result['isOk'])  ){
            throw new \Exception($result['failMsg']);
        }

        $sheetData = $result['data']->sheets[0];
        $sheetData = $sheetData['cells'];

        //SitelabLogger::logger($params);

        $uploadMap = [];
        $invoiceCompany = $params['invoiceExcelBatchCompany'];

        foreach( $sheetData as $idx => $data ) {
            if (($startRowCnt + 1) > $idx) continue;
            //$orderNo = sprintf("%.0f",$data[13]);
            //$orderNo = (int)$data[13];

            $orderNo = substr($data[14],0,15);
            $invoiceNo = $data[12];
            $prdCode = $data[8];
            /*$orderNo = substr($data[14],0,15);
            $invoiceNo = $data[12];*/
            $optionSnoList = DBUtil2::getList(DB_GOODS_OPTION, 'optionCode', $prdCode);

            foreach( $optionSnoList as $optionSno ){
                DBUtil2::update(DB_ORDER_GOODS, ['invoiceCompanySno'=>$invoiceCompany,'invoiceNo'=>$invoiceNo,'invoiceDt'=>'now()'], new SearchVo([
                    'left(orderNo,15)=?',
                    'optionSno=?',
                ],[
                    $orderNo,
                    $optionSno['sno'],
                ]));
            }
        }
    }

    public function setOrderInvoiceByExcelOrder($files, $params){

        $erpService = SlLoader::cLoad('erp','erpService');

        $startRowCnt = 1;
        $result = ExcelCsvUtil::checkAndRead($files,$startRowCnt);
        //PhpExcelUtil::runExcelReadAndProcess($files, $params, 1); //TODO : 변경 해야함.

        if( empty($result['isOk'])  ){
            throw new \Exception($result['failMsg']);
        }

        $sheetData = $result['data']->sheets[0];
        $sheetData = $sheetData['cells'];

        //SitelabLogger::logger($params);

        $uploadMap = [];
        $invoiceCompany = $params['invoiceExcelBatchCompany'];

        foreach( $sheetData as $idx => $data ) {

            $outDate = $data[1];

            $orgOrderNo = $data[14];
            if (($startRowCnt + 1) > $idx) continue;
            $orderNo = substr($orgOrderNo,0,15);
            $invoiceNo = $data[12];
            $uploadMap[$orderNo] = $invoiceNo;

            //만약 골프존이라면 그대로 출고 처리
            $prdCode = $data[8];
            $prdName = $data[9];
            $quantity = $data[13];
            $identificationText = $data[19] . $data[20]; //등록날짜 + //등록시간

            //수기 주문 처리. 다시 생각. - 어차피 새로 짜긴 해야겠다.
            if( strpos($prdCode,'GOLF') !== false || strpos($prdName,'골프존') !== false
                || strpos($prdCode,'KTNG') !== false || strpos($prdName,'KTNG') !== false
            ){
                $searchInoutStockList = DBUtil2::getListBySearchVo('sl_3plStockInOut', new SearchVo(
                    [
                        'memo=?',
                        'identificationText=?',
                        'thirdPartyProductCode=?',
                    ]
                    ,[
                        $invoiceNo,
                        $identificationText,
                        $prdCode,
                    ]
                ));
                //수량 복구 및 삭제.
                foreach($searchInoutStockList as $inoutStockList){
                    $erpService->updateStock($inoutStockList['productSno'], $inoutStockList['quantity']);
                    DBUtil2::delete('sl_3plStockInOut', new SearchVo('sno=?', $inoutStockList['sno']));
                }

                //출고 등록.
                $each['thirdPartyProductCode'] = $prdCode;
                $each['goodsCnt'] = $quantity;
                $each['orderNo'] = $orgOrderNo;
                $each['memo'] = $invoiceNo;
                $each['identificationText'] = $identificationText;

                $erpService->insertOutStock($each);

                //개별 출고 히스토리 기록
                $searchVo = new SearchVo(['productCode=?','orderNo=?'],[$prdCode,$orgOrderNo]);
                $tmpData = DBUtil2::getOneBySearchVo('sl_3plOrderTmp', $searchVo);
                if(!empty($tmpData)){
                    $deleteSno = $tmpData['sno'];
                    //SitelabLogger::logger('삭제 확인 : ' . $deleteSno);
                    unset($tmpData['sno']);
                    $tmpData['orderDt'] = $outDate;
                    $tmpData['invoiceNo'] = $invoiceNo;

                    //이미 있다면 추가 X
                    if( empty(DBUtil2::getOneBySearchVo('sl_3plOrderHistory', $searchVo)) ){
                        DBUtil2::insert('sl_3plOrderHistory',$tmpData);
                    }
                    DBUtil2::delete('sl_3plOrderTmp',new SearchVo('sno=?', $deleteSno));
                    //SitelabLogger::logger('삭제 Complete... : ' . $deleteSno);
                }

            }

        }

        $invoiceNoList = [];
        $orderNoList = [];
        foreach( $uploadMap as $orderNo => $invoiceNo ) {
            $invoiceNoList[] = " when left(orderNo,15) = '{$orderNo}' then '{$invoiceNo}' ";
            $orderNoList[] = '\''.$orderNo.'\'';
        }
        $invoiceNoListSql = implode(' ',$invoiceNoList);
        $orderNoListSql = implode(',',$orderNoList);
        $sql = "update es_orderGoods set invoiceDt=now(), invoiceCompanySno = {$invoiceCompany}, invoiceNo = ( case {$invoiceNoListSql} end) where left(orderNo,15) in ( {$orderNoListSql} )";
        DBUtil2::runSql($sql);
    }

    public function setRestockReq($controller){
        $goodsViewData = $controller->getData('goodsView');
        $memNo = \Session::get('member.memNo');
        $reqList = $this->getRestockReqList($memNo, $goodsViewData['goodsNo']);
        $controller->setData('isRestockReq', !empty($reqList));
    }

    public function getRestockReqList($memNo, $goodsNo){
        $list = DBUtil2::getListBySearchVo('sl_soldOutReqList', new SearchVo(['memNo=?','goodsNo=? AND 0 = sendType'],[$memNo,$goodsNo]));
        return $list;
    }

}
