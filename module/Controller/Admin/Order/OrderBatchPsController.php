<?php
/**
 * This is commercial software, only users who have purchased a valid license
 * and accept to the terms of the License Agreement can install and use this
 * program.
 *
 * Do not edit or add to this file if you wish to upgrade Godomall5 to newer
 * versions in the future.
 *
 * @copyright ⓒ 2016, NHN godo: Corp.
 * @link http://www.godo.co.kr
 */

namespace Controller\Admin\Order;

use App;
use Component\Database\DBTableField;
use Component\Member\Manager;
use Component\Work\Code\DocumentDesignCodeMap;
use Exception;
use Framework\Debug\Exception\AlertCloseException;
use Framework\Utility\ComponentUtils;
use Globals;
use Request;
use Session;
use SiteLabUtil\SlCommonUtil;
use SlComponent\Database\DBUtil;
use SlComponent\Database\DBUtil2;
use SlComponent\Database\SearchVo;
use SlComponent\Download\SiteLabDownloadUtil;
use SlComponent\Util\DocumentStruct;
use SlComponent\Util\ExcelCsvUtil;
use SlComponent\Util\SitelabLogger;
use SlComponent\Util\SlControllerTrait;
use SlComponent\Util\SlLoader;
use UserFilePath;
use Framework\Debug\Exception\LayerException;

/**
 * 업무 처리
 */
class OrderBatchPsController extends \Controller\Admin\Controller
{
    use SlControllerTrait;

    public function index() {
        $this->runMethod(get_class_methods(__CLASS__));
    }

    /**
     * 로그인
     * @param $param
     */
    public function regBatchOrder($param){
        $files = \Request::files()->toArray();
        //SitelabLogger::logger($param);
        //SitelabLogger::logger($files);
        //$this->setJson(200, __('처리 완료'));
        $startRowCnt = 1;
        $result = ExcelCsvUtil::checkAndRead($files,$startRowCnt);
        if( empty($result['isOk'])  ){
            $this->layer(__($result['failMsg']));
        }

        $sheetData = $result['data']->sheets[0]['cells'];
        $fieldDataList = array();

        $fieldDataList[1] = [ '회원ID'  , 'memId' ] ;
        $fieldDataList[2] = [ '상품번호' , 'goodsNo' ] ;
        $fieldDataList[3] = [ '상품명'   , 'reqGoodsNm' ] ;
        $fieldDataList[4] = [ '수령자명' , 'receiverName' ] ;
        $fieldDataList[5] = [ '전화번호' , 'receiverCellPhone' ] ;
        $fieldDataList[6] = [ '옵션명'   , 'optionName' ] ;
        $fieldDataList[7] = [ '배송지점' , 'deliveryName' ] ;
        $fieldDataList[8] = [ '주문수량' , 'stockCnt' ] ;

        $param = [];
        foreach( $sheetData as $idx => $data ){
            if( ($startRowCnt+1) > $idx ) continue;

            $paramEachData = [];

            foreach( $fieldDataList as $key => $value ){
                if(isset( $data[$key] )){
                    if( 8 === $key ){
                        //수량 기본1
                        $paramEachData[$value[1]] = empty($data[$key])?1:$data[$key];
                    }else{
                        $paramEachData[$value[1]] = $data[$key];
                    }

                }
            }

            $param[] = $paramEachData;

        }
        SitelabLogger::logger('==== 일괄 주문 저장 데이터 확인 ====');
        SitelabLogger::logger($param);

        $orderBatchRegService = SlLoader::cLoad('order','orderBatchRegService');
        $errMsg = $orderBatchRegService->batchOrder( $param , true);

        if(!empty($errMsg)){
            $titleList = [
                '오류내역',
            ];
            foreach($errMsg as $msg){
                $excelBody .= "<tr>";
                $excelBody .= ExcelCsvUtil::wrapTd($msg);
                $excelBody .= "</tr>";
            }
            $simpleExcelComponent = SlLoader::cLoad('Excel','SimpleExcelComponent','sl');
            $simpleExcelComponent->simpleDownload('주문업로드_오류내역', $titleList, $excelBody);
        }

        $this->layer(__('처리 완료.'));
    }


}


