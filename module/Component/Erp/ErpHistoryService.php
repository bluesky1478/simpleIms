<?php
namespace Component\Erp;

use App;
use Component\Database\DBTableField;
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
use SlComponent\Util\SitelabLogger;
use SlComponent\Util\SlCodeMap;
use SlComponent\Util\SlLoader;
use Component\Sms\Code;

/**
 * ERP 서비스
 * Class GoodsStock
 * @package Component\Goods
 */
class ErpHistoryService {

    private $erpService;

    public function __construct(){
        $this->erpService =  SlLoader::cLoad('erp','erpService');
    }

    /**
     * 입출고 식별키
     * @param $inOutType
     * @param $each
     * @return string
     */
    public function getIdentificationKey($each){
        $excelField = ErpService::INPUT_FIELD;
        $identification[] = SlCommonUtil::getExcelData($each,'regDate',$excelField);
        $identification[] = SlCommonUtil::getExcelData($each,'regTime',$excelField);
        $identification[] = SlCommonUtil::getExcelData($each,'thirdPartyProductCode',$excelField);
        $identification[] = SlCommonUtil::getExcelData($each,'quantity',$excelField);
        $identification[] = SlCommonUtil::getExcelData($each,'memo',$excelField);
        return '1_'.implode('_',$identification);
    }
    public function getIdentificationOutKey($each){
        $excelField = ErpService::OUTPUT_FIELD;
        $identification[] = SlCommonUtil::getExcelData($each,'regDate',$excelField);
        $identification[] = SlCommonUtil::getExcelData($each,'regTime',$excelField);
        $identification[] = SlCommonUtil::getExcelData($each,'thirdPartyProductCode',$excelField);
        $identification[] = SlCommonUtil::getExcelData($each,'quantity',$excelField);
        $identification[] = SlCommonUtil::getExcelData($each,'invoice',$excelField);
        return '2_'.implode('_',$identification);
    }

    /**
     * 입고 정보 입력
     * @param $each
     * @param $key
     * @param $mixData
     * @throws \Exception
     */
    public function saveInputHistory($each, $key, &$mixData){
        $searchKey = $this->getIdentificationKey($each);
        //$searchInOut = DBUtil2::getCount( ErpService::DB_3PL_IN_OUT , new SearchVo('identificationText=?', $searchKey));
        //이력 삽입.
        $excelField = ErpService::INPUT_FIELD;
        $memo = SlCommonUtil::getExcelData($each,'memo',$excelField);
        $code  = SlCommonUtil::getExcelData($each,'thirdPartyProductCode',$excelField);
        $prdName  = SlCommonUtil::getExcelData($each,'productName',$excelField);

        $productData = DBUtil2::getOne('sl_3plProduct','thirdPartyProductCode',$code);
        if( empty($productData) ){
            //Code 등록
            $productData = $this->erpService->getDivide3PlProductName($prdName);
            $productData['thirdPartyCode'] = 1;
            $productData['thirdPartyProductCode'] = $code;
            $prdSno = $this->erpService->insertProduct($productData);
            SitelabLogger::logger('신규 코드 등록');
            SitelabLogger::logger($productData);
        }else{
            $prdSno = $productData['sno'];
        }

        DBUtil2::insert(ErpService::DB_3PL_IN_OUT, [
            'productSno' => $prdSno,
            'thirdPartyProductCode' => $code,
            'inOutType' => ErpCodeMap::ERP_STOCK_TYPE['입고'],
            'inOutReason' => ErpService::getDivide3PlInputType($memo),
            'inOutDate' => SlCommonUtil::getExcelData($each,'inDate',$excelField),
            'quantity' => SlCommonUtil::getExcelData($each,'quantity',$excelField),
            'memo' => $memo,
            'identificationText' => $searchKey,
            'managerSno' => 1,
        ]);
    }


    /**
     * 출고 정보 입력
     * @param $each
     * @param $key
     * @param $mixData
     * @throws \Exception
     */
    public function saveOutputHistory($each, $key, &$mixData){
        $searchKey = $this->getIdentificationOutKey($each);
        //$searchOutHistory = DBUtil2::getCount( ErpService::DB_3PL_IN_OUT , new SearchVo('identificationText=?', $searchKey));
        //이력 삽입.
        $excelField = ErpService::OUTPUT_FIELD;
        $memo = SlCommonUtil::getExcelData($each,'memo',$excelField);
        $code  = SlCommonUtil::getExcelData($each,'thirdPartyProductCode',$excelField);

        /*if( empty($searchOutHistory) ){

        }else{
            SitelabLogger::logger($searchKey);
        }*/

        $productData = DBUtil2::getOne('sl_3plProduct','thirdPartyProductCode',$code);
        $saveData = [
            'productSno' => $productData['sno'],
            'thirdPartyProductCode' => $code,
            'inOutType' => ErpCodeMap::ERP_STOCK_TYPE['출고'],
            'inOutReason' => ErpCodeMap::ERP_STOCK_REASON['정기출고'],
            'inOutDate' => SlCommonUtil::getExcelData($each,'outDate',$excelField),
            'quantity' => SlCommonUtil::getExcelData($each,'quantity',$excelField),
            'managerSno' => 1,
            'orderNo' => SlCommonUtil::getExcelData($each,'orderNo',$excelField),
            'identificationText' => $searchKey,
            'customerName' => SlCommonUtil::getExcelData($each,'customerName',$excelField),
            'address' => SlCommonUtil::getExcelData($each,'address',$excelField),
            'phone' => SlCommonUtil::getExcelData($each,'phone',$excelField),
            'cellPhone' => SlCommonUtil::getExcelData($each,'cellPhone',$excelField),
            'memo' => $memo,
            'invoiceNo' => SlCommonUtil::getExcelData($each,'invoice',$excelField),
        ];
        DBUtil2::insert(ErpService::DB_3PL_IN_OUT, $saveData);

    }


    /**
     * 출고 이력 갱신
     * @param $yearMonth 2021-05
     */
    public function refreshOutHistory($yearMonth){
        //DBUtil2::runSql("delete from ");

    }

}

