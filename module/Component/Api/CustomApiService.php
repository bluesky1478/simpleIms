<?php
namespace Component\Api;

use App;
use Component\Mall\Mall;
use Component\Mall\MallDAO;
use Component\Member\Util\MemberUtil;
use Component\Scm\ScmTkeService;
use Component\Sitelab\MallConfig;
use Exception;
use Framework\Utility\NumberUtils;
use Request;
use Session;
use SiteLabUtil\SlCommonUtil;
use SlComponent\Database\DBConst;
use SlComponent\Database\DBUtil;
use SlComponent\Database\DBUtil2;
use SlComponent\Database\SearchVo;
use SlComponent\Util\CustomApiUtil;
use SlComponent\Util\LogTrait;
use SlComponent\Util\PhpExcelUtil;
use SlComponent\Util\SitelabLogger;
use SlComponent\Util\SlCodeMap;
use SlComponent\Util\SlKakaoUtil;
use SlComponent\Util\SlLoader;
use Component\Coupon\Coupon;

/**
 * 공통 AJAX 서비스 (필요한 부분 모아두었다가 종류별로 많아지면 분리)
 * Class SlCode
 * @package SlComponent\Util
 */
class CustomApiService {
    private $sql;

    public function __construct(){
        $this->sql = App::load(CustomApiSql::class);
    }

    // --- 재입고 알림 시작

    /**
     * 품절상품 요청 저장.
     * @param $params
     * @return array
     * @throws Exception
     */
    public function saveSoldOutRequest($params){

        $memNo = \Session::get('member.memNo');
        $reqTable = 'sl_soldOutReqList';
        //신청내역 있는지 확인.

        $reqList = DBUtil2::getListBySearchVo($reqTable, new SearchVo(['memNo=?','goodsNo=? AND 0 = sendType'],[$memNo,$params['goodsNo']]));

        if(!empty($reqList)){
            throw new Exception('이미 신청되어 있습니다.');
        }
        //scmNo
        $saveData = SlCommonUtil::getAvailData($params,[
            'reqName',
            'goodsNo',
            'deliveryName',
            'deliveryCode',
            'cellPhone',
        ]);
        $saveData['memNo'] = $memNo;
        $saveData['scmNo'] = MemberUtil::getMemberScmNo();

        $reqSno = DBUtil::insert($reqTable, $saveData);

        foreach($params['reqCnt'] as $key => $optionCount){
            if($optionCount > 0){
                $saveData['reqCnt'] = $optionCount;
                $saveData['optionSno'] = $params['optionSno'][$key];
                $saveData['optionInfo'] = $params['optionValue'][$key];
                $saveData['reqSno'] = $reqSno;
                DBUtil::insert('sl_soldOutReqOptionList', $saveData);
            }
        }

        return ['data'=>$params,'msg'=>'저장되었습니다.'];
    }

    // --- 재입고 알림 끝


    //수기 주문
    public function excelUploadTke($params){
        DBUtil2::runSql("Truncate table sl_tkeOrder");
        DBUtil2::runSql("Truncate table sl_tkeOrderGoods");

        $files = \Request::files()->toArray();
        $params['instance'] = $this;
        $params['fnc'] = 'excelUploadTkeEachProc';
        $params['mixData'] = [
            'excelField' => [
                'memId'=> 1,
                'receiverName'=> 21,
                'receiverPhone'=> 22,
                'receiverCellPhone'=> 23,
                'receiverZipCode'=> 24,
                'receiverAddress'=> 25,
                'memo'=> 26,
            ]
        ];

        PhpExcelUtil::runExcelReadAndProcess($files, $params, 2);
    }

    public function excelUploadTkeEachProc($each, $key, &$mixData){
        if( 0 === $key ) return false;

        $excelData = PhpExcelUtil::getExcelDataList($each, $mixData['excelField']);

        if( !empty($excelData['memId']) && !empty($excelData['receiverName']) && !empty($excelData['receiverAddress'])  ){
            //배송정보 저장
            $tkeOrderSno = DBUtil2::insert('sl_tkeOrder' , $excelData);
            //상품 저장.
            //2~9 (pattern)    :  1000002052 , beginOption = 90, 5
            //10~20 (pattern)  :  1000002051 , beginOption = 24, 2
            $this->saveTkeOrderGoodsByExcelData($each, [
                'tkeOrderSno' => $tkeOrderSno,
                'beginExcelIndex' => 2,
                'endExcelIndex' => 9,
                'beginOption' => 90,
                'increaseOptionCnt' => 5,
                'goodsNo' => ScmTkeService::getPreOrderGoods(ScmTkeService::TEE),
            ]);
            $this->saveTkeOrderGoodsByExcelData($each, [
                'tkeOrderSno' => $tkeOrderSno,
                'beginExcelIndex' => 10,
                'endExcelIndex' => 20,
                'beginOption' => 24,
                'increaseOptionCnt' => 2,
                'goodsNo' => ScmTkeService::getPreOrderGoods(ScmTkeService::PANTS),
            ]);
        }
    }

    public function saveTkeOrderGoodsByExcelData($excelEachData, $saveCondition){
        $increaseCnt = 0;
        for($i=$saveCondition['beginExcelIndex']; $saveCondition['endExcelIndex']>=$i; $i++){
            $goodsCnt = trim($excelEachData[$i]);
            if( !empty( $goodsCnt ) ){
                $optionName = $saveCondition['beginOption'] + ( $saveCondition['increaseOptionCnt'] * $increaseCnt );
                $saveGoodsData['tkeOrderSno'] = $saveCondition['tkeOrderSno'];
                $saveGoodsData['goodsNo'] = $saveCondition['goodsNo'];
                $saveGoodsData['optionName'] = $optionName;
                $saveGoodsData['goodsCnt'] = $goodsCnt;
                DBUtil2::insert('sl_tkeOrderGoods', $saveGoodsData);
            }
            $increaseCnt++;
        }
    }

    public function setCartRemove(){
        $cart = \App::load('\\Component\\Cart\\Cart');
        $cart->setCartRemove();
    }

    public function setScmDelivery($params){
        //SitelabLogger::logger($params);
        DBUtil2::update(DB_ORDER_GOODS,['goodsDeliveryCollectFl'=>$params['deliveryCollectFl']],new SearchVo('sno=?', $params['sno']));
    }

    public function saveUploadData($param){
        //$requestUrl = "http://bcloud1478.godomall.com/ajax/custom_api_ps.php?mode=saveUploadData&key={$key}&name={$encodeFileName}";
        $explodeValue = explode('_',$param['key']);

        if(strpos($explodeValue[0],'filePrd')!==false){
            $table = 'sl_recapPrdFile';
        }else{
            $table = 'sl_recapFile';
        }

        $fileData = DBUtil2::getOneBySearchVo($table, new SearchVo("projectSno=?",$explodeValue[1]));
        $saveData = [
          'projectSno' => $explodeValue[1],
            $explodeValue[0] => $param['name']
        ];
        /*2023-07-16 18:21:26 - Array
        (
            [mode] => saveUploadData
            [key] => fileSales1_21
        [name]*/
        if( !empty($fileData) ){
            //SitelabLogger::logger('Update');
            //SitelabLogger::logger($saveData);
            DBUtil2::update($table,$saveData,new SearchVo('sno=?', $fileData['sno']));
        }else{
            //SitelabLogger::logger('Insert');
            //SitelabLogger::logger($saveData);
            DBUtil2::insert($table,$saveData);
        }
    }

}
