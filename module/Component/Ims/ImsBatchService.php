<?php
namespace Component\Ims;

use App;
use Component\Database\DBIms;
use Component\Database\DBTableField;
use Component\Ims\EnumType\PREPARED_TYPE;
use Component\Member\Manager;
use Component\Scm\ScmTkeService;
use Framework\Debug\Exception\AlertBackException;
use Framework\Utility\DateTimeUtils;
use LogHandler;
use Request;
use Session;
use SiteLabUtil\SlCommonUtil;
use SlComponent\Database\DBUtil2;
use SlComponent\Database\SearchVo;
use SlComponent\Mail\SiteLabMailUtil;
use SlComponent\Util\ExcelCsvUtil;
use SlComponent\Util\PhpExcelUtil;
use SlComponent\Util\SitelabLogger;
use SlComponent\Util\SlCodeMap;
use SlComponent\Util\SlLoader;
use Component\Sms\Code;

/**
 * IMS 서비스
 * Class GoodsStock
 * @package Component\Goods
 */
class ImsBatchService {

    private $imsService;

    public function __construct(){
        $this->imsService = SlLoader::cLoad('ims', 'imsService');
    }

    /**
     * 회원 일괄 등록
     * @param $inputParams
     * @param $filesValue
     * @throws \Exception
     */
    public function saveBatchCustomer($inputParams, $filesValue){
        $params['instance'] = $this;
        $params['fnc'] = 'saveBatchCustomerEach';

        $params['mixData'] = [
            'excelField' => [
                'customerDiv' => 1, //치환
                'salesManagerSno' => 2, //치환
                'customerName' => 3,
                'styleCode' => 4,
                'contactName' => 5,
                'contactPosition' => 6,
                'contactMobile' => 7,
                'contactEmail' => 8,
                'msContract' => 9,
                'msContractPeriod' => 10,
                'msContractMaintain' => 11,
                'msRemainPeriod' => 12,
                'msRecontractCondition' => 13,
                'useMall' => 14, //치환
                'use3pl' => 15 //치환
            ]
        ];
        PhpExcelUtil::runExcelReadAndProcess($filesValue, $params, 1);
    }


    /**
     * 고객 일괄 등록
     * @param $each
     * @param $key
     * @param $mixData
     */
    public function saveBatchCustomerEach($each, $key, &$mixData){
        $saveData = PhpExcelUtil::getExcelDataList($each, $mixData['excelField']);
        $customerDivMap = [
            '잠재고객' => 0,
            '신규고객사' => 1,
            '기존고객사' => 2,
        ];
        $saveData['customerDiv'] = $customerDivMap[$saveData['customerDiv']];
        $saveData['useMall'] = '유' === $saveData['useMall'] ? 'y' : 'n' ;
        $saveData['use3pl'] = '유' === $saveData['use3pl'] ? 'y' : 'n' ;

        $saveData['salesManagerSno'] = DBUtil2::getOne(DB_MANAGER, 'managerNm', $saveData['salesManagerSno'])['sno'];

        $customerData = DBUtil2::getOne(ImsDBName::CUSTOMER,'customerName',$saveData['customerName']);
        $saveData['sno'] = $customerData['sno'];

        $this->imsService->saveCustomer($saveData);
    }


    /**
     * 프로젝트 일괄 등록
     * @param $inputParams
     * @param $filesValue
     * @throws \Exception
     */
    public function saveBatchProject($inputParams, $filesValue){
        $params['instance'] = $this;
        $params['fnc'] = 'saveBatchProjectEach2';
        $params['mixData'] = [
            'excelField' => [
                'projectNo' => 1,
                'customerName' => 2,
                'prdYear' => 3,
                'prdSeason' => 4,
                'prdGender' => 5,
                'prdStyle' => 6,
                'addStyleCode' => 7,
                'styleCode' => 8,
                'productName' => 9,
            ]
        /*'excelField' => [
            'projectNo' => 1,
            'projectStatus' => 2,
            'projectType' => 3,
            'customerDeliveryDt' => 4,
            'customerOrderDeadLine' => 5,
            'customerSno' => 6,
            'styleCode' => 7,
            'productName' => 8,
            'prdExQty' => 9,
            'targetPrice' => 10,
            'salesStartDt' => 11,
            'customerOrderDt' => 12,
            'projectMemo' => 13,
            'targetPrdCost' => 14,
            'prdCost' => 15,
            'bid' => 16,
            'recommend' => 17,
            'recommendDt' => 18,
            'designManagerSno' => 19,
            'designEndDt' => 20,
            'planDt' => 21,
            'planEndDt' => 22,
            'proposalDt' => 23,
            'proposalEndDt' => 24,
            'sampleStartDt' => 25,
            'sampleEndDt' => 26,
            'workDt' => 27,
            'workEndDt' => 28,
            'prdEndDt' => 29,
        ]*/

        ];

        PhpExcelUtil::runExcelReadAndProcess($filesValue, $params, 1);
    }


    /**
     * 프로젝트 일괄 등록
     * @param $each
     * @param $key
     * @param $mixData
     */
    public function saveBatchProjectEach($each, $key, &$mixData){

        $saveData = PhpExcelUtil::getExcelDataList($each, $mixData['excelField']);
        $divMap = [
            '신규' => 0,
            '리오더' => 1,
            '입찰' => 2,
        ];

        $prj = DBUtil2::getOne(ImsDBName::PROJECT,'projectNo', $saveData['projectNo']);
        if(empty($prj)){
            $saveData['projectType'] = $divMap[$saveData['projectType']];
            $customerData = DBUtil2::getOne(ImsDBName::CUSTOMER, 'customerName', $saveData['customerSno']);
            if(empty($customerData)){
                SitelabLogger::logger($saveData['customerSno']);
            }else{
                $saveData['customerSno'] = $customerData['sno'];
            }
            $saveData['designManagerSno'] = DBUtil2::getOne(DB_MANAGER, 'managerNm', $saveData['designManagerSno'])['sno'];

            $projectSno = DBUtil2::insert(ImsDBName::PROJECT, $saveData);
        }else{
            $projectSno = $prj['sno'];
        }

        $prdSaveData = SlCommonUtil::getAvailData($saveData,[
            'styleCode',
            'productName',
            'prdExQty',
            'targetPrice',
            'targetPrdCost',
            'prdCost',
        ]);
        $prdSaveData['projectSno'] = $projectSno;
        DBUtil2::insert(ImsDBName::PRODUCT, $prdSaveData);

    }

    public function saveBatchProjectEach2($each, $key, &$mixData){
/*
        'projectNo' => 1,
        'customerName' => 2,
        'prdYear' => 3,
        'prdSeason' => 4,
        'prdGender' => 5,
        'prdStyle' => 6,
        'addStyleCode' => 7,
        'styleCode' => 8,
        */
        $excelData = PhpExcelUtil::getExcelDataList($each, $mixData['excelField']);
        //SitelabLogger::logger($excelData);

        $prj = DBUtil2::getOne(ImsDBName::PROJECT,'projectNo', $excelData['projectNo']);

        if(empty($prj)){
            $saveData = [];
            $saveData['projectNo'] = $excelData['projectNo'];
            $saveData['projectType'] = 1;
            $saveData['projectStatus'] = 99;
            $customerData = DBUtil2::getOne(ImsDBName::CUSTOMER, 'customerName', $excelData['customerName']);
            if(empty($customerData)){
                SitelabLogger::logger($excelData['customerName']);
            }else{
                $saveData['customerSno'] = $customerData['sno'];
            }
            //SitelabLogger::logger($saveData);
            $projectSno = DBUtil2::insert(ImsDBName::PROJECT, $saveData);
            DBUtil2::runSql("update sl_imsProject set btStatus = null, costStatus=null, estimateStatus=null, workStatus=null, orderStatus=null where sno = '{$projectSno}' ");
        }else{
            $projectSno = $prj['sno'];
        }

        $prdSaveData = SlCommonUtil::getAvailData($excelData,[
            'styleCode',
            'productName',
            'prdYear',
            'prdSeason',
            'prdGender',
            'prdStyle',
            'addStyleCode',
        ]);
        $prdSaveData['projectSno'] = $projectSno;
        DBUtil2::insert(ImsDBName::PRODUCT, $prdSaveData);

    }

    /**
     * 생산 일괄 등록
     * @param $inputParams
     * @param $filesValue
     * @throws \Exception
     */
    public function saveBatchProduce($inputParams, $filesValue){
        $params['instance'] = $this;
        $params['fnc'] = 'saveBatchProduceEach';
        $params['mixData'] = [
            'excelField' => [
                'projectNo' => 1,
                'tmp1' => 2,
                'tmp2' => 3,
                'styleCode' => 4,
                'tmp4' => 5,
                'msOrderDt' => 6,
                'customerDeliveryDt' => 7,
                'prdExQty' => 8,
                'targetPrice' => 9,
                'prdCost' => 10,
                'confirmMemo' => 11,
                'produceCompanySno' => 12,
                'produceStatus' => 13,
                'prdStep10_1' => 14,
                'prdStep10_2' => 15,
                'prdStep10_3' => 16,
                'prdStep20_1' => 17,
                'prdStep20_2' => 18,
                'prdStep20_3' => 19,
                'prdStep30_1' => 20,
                'prdStep30_2' => 21,
                'prdStep30_3' => 22,
                'prdStep40_1' => 23,
                'prdStep40_2' => 24,
                'prdStep40_3' => 25,
                'prdStep50_1' => 26,
                'prdStep50_2' => 27,
                'prdStep50_3' => 28,
                'prdStep60_1' => 29,
                'prdStep60_2' => 30,
                'prdStep60_3' => 31,
                'prdStep70_1' => 32,
                'prdStep70_2' => 33,
                'prdStep70_3' => 34,
                'prdStep80_1' => 35,
                'prdStep80_2' => 36,
                'prdStep80_3' => 37,
                'prdStep90_1' => 38,
                'prdStep90_2' => 39,
                'prdStep90_3' => 40,
                'msMemo' => 41
            ]
        ];

        PhpExcelUtil::runExcelReadAndProcess($filesValue, $params, 1);
    }


    public function getUpdateDataWithoutBlank($filedName, $data, &$updateData){
        if( !empty($data[$filedName]) ){
            $updateData[$filedName] = $data[$filedName];
        }
    }

    /**
     * 생산 일괄 등록
     * @param $each
     * @param $key
     * @param $mixData
     * @throws \Exception
     */
    public function saveBatchProduceEach($each, $key, &$mixData){

        //SitelabLogger::logger($each);

        $loadData = PhpExcelUtil::getExcelDataList($each, $mixData['excelField']);
        $projectData = DBUtil2::getOne(ImsDBName::PROJECT, 'projectNo', $loadData['projectNo']);

        $projectUpdate = [];
        $this->getUpdateDataWithoutBlank('msOrderDt', $loadData, $projectUpdate);
        $this->getUpdateDataWithoutBlank('customerDeliveryDt', $loadData, $projectUpdate);
        if(!empty($projectUpdate['customerDeliveryDt'])){
            $projectUpdate['msDeliveryDt'] = $projectUpdate['customerDeliveryDt'];
        }
        $this->getUpdateDataWithoutBlank('produceCompanySno', $loadData, $projectUpdate);

        //SitelabLogger::logger('### PROJECT UPDATE : ' . $projectData['sno']);
        //SitelabLogger::logger($projectUpdate);

        DBUtil2::update(ImsDBName::PROJECT, $projectUpdate, new SearchVo('sno=?',$projectData['sno']));

        $productUpdate = [];
        $this->getUpdateDataWithoutBlank('prdExQty', $loadData, $productUpdate); //수량
        $this->getUpdateDataWithoutBlank('targetPrice', $loadData, $productUpdate); //판매가
        $this->getUpdateDataWithoutBlank('prdCost', $loadData, $productUpdate); //생산가
        if(!empty($productUpdate['prdCost'])){
            $productUpdate['targetPrdCost'] = $productUpdate['prdCost'];
        }

        //SitelabLogger::logger('### PRODUCT UPDATE : ' . $loadData['styleCode']);
        //SitelabLogger::logger($productUpdate);
        DBUtil2::update(ImsDBName::PRODUCT, $productUpdate, new SearchVo('styleCode=?',$loadData['styleCode']));


        //합친다.
        $prdStep = [];
        for($i=10; 90>=$i; $i+=10){
            $prdStep['prdStep'.$i]['expectedDt'] = $loadData['prdStep'.$i.'_1'] ;
            $prdStep['prdStep'.$i]['completeDt'] = $loadData['prdStep'.$i.'_2'] ;
            if( !empty($loadData['prdStep'.$i.'_2']) ){
                $prdStep['prdStep'.$i]['confirmYn'] = 'y';
            }else{
                $prdStep['prdStep'.$i]['confirmYn'] = '';
            }
            $prdStep['prdStep'.$i]['memo'] = $loadData['prdStep'.$i.'_3'] ;
        }
        $saveData['prdStep'] = json_encode($prdStep);
        $saveData['projectSno'] = $projectData['sno'];
        $saveData['produceCompanySno'] = $loadData['produceCompanySno'];
        $saveData['produceStatus'] = $loadData['produceStatus'];
        $saveData['confirmMemo'] = $loadData['confirmMemo'];
        $saveData['msMemo'] = $loadData['msMemo'];

        $produceData = DBUtil2::getOne(ImsDBName::PRODUCE, 'projectSno', $projectData['sno']);
        if( empty($produceData) ){
            DBUtil2::insert(ImsDBName::PRODUCE, $saveData);
        }else{
            $updateData = [];
            $this->getUpdateCompareData('confirmMemo', $produceData, $loadData, $updateData);
            $this->getUpdateCompareData('msMemo', $produceData, $loadData, $updateData);
            if(!empty($updateData)){
                DBUtil2::update(ImsDBName::PRODUCE, $saveData, new SearchVo('projectSno=?', $projectData['sno']));
            }
        }
    }

    public function getUpdateCompareData($fieldName, $src, $target, &$updateData){
        if(empty($src[$fieldName]) && !empty($target[$fieldName])){
            $updateData[$fieldName] = $target[$fieldName];
        }
    }

}

