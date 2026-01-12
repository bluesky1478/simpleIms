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
use Component\Claim\ClaimListService;
use Component\Sitelab\SiteLabDownloadUtil;
use Exception;
use Request;
use Framework\Debug\Exception\LayerNotReloadException;
use Framework\Debug\Exception\LayerException;
use SlComponent\Database\DBUtil;
use SlComponent\Database\DBUtil2;
use SlComponent\Database\SearchVo;
use SlComponent\Util\ExcelCsvUtil;
use SlComponent\Util\SlLoader;

/**
 * 주문 첨부파일 다운로드
 *
 * @package Bundle\Controller\Admin\Order
 * @author  su
 */
class DownloadHanNameController extends \Controller\Admin\Controller{
    public function index(){

        if( !empty(\Request::get()->get('summer') ) ){
            $this->hanName(1000000380); //하계명찰
        }else{
            $this->hanName(1000000339);
        }

        exit();
    }

    public function hanName($goodsNo){

        if(1000000380 === $goodsNo){
            $goodsNm = '한전_하계명찰';
        }else{
            $goodsNm = '한전_동계명찰';
        }


        $LIST_TITLES = [
            '신청내역',
            '우편번호',
            '주소',
            '수령자',
            '연락처',
            '주문수량',
        ];

        $excelBody = '';

        $sql = "select 
a.addField,
b.receiverName, 
b.receiverCellPhone, 
b.receiverZonecode,
b.receiverAddress,
b.receiverAddressSub,
c.goodsCnt
from es_order a 
join es_orderInfo b on a.orderNo = b.orderNo 
join es_orderGoods c on a.orderNo = c.orderNo 
where c.goodsNo = {$goodsNo} 
and c.orderStatus = 'p1'" ;

        $list = DBUtil2::runSelect($sql);
        foreach($list as $each){
            $addField = json_decode($each['addField'],true);
            $nameList = explode('<br/>',$addField[1]['data']);
            foreach($nameList as $nameEach){
                $fieldData = array();
                $fieldData[] = ExcelCsvUtil::wrapTd($nameEach);
                $fieldData[] = ExcelCsvUtil::wrapTd($each['receiverZonecode']);
                $fieldData[] = ExcelCsvUtil::wrapTd($each['receiverAddress']. ' ' . str_replace('-','',$each['receiverAddressSub']) );
                $fieldData[] = ExcelCsvUtil::wrapTd($each['receiverName']);
                $fieldData[] = ExcelCsvUtil::wrapTd($each['receiverCellPhone']);
                $fieldData[] = ExcelCsvUtil::wrapTd(number_format($each['goodsCnt']));
                $excelBody .=  "<tr>". implode('',$fieldData) . "</tr>";
            }
        }

        $simpleExcelComponent = SlLoader::cLoad('Excel','SimpleExcelComponent','sl');
        $simpleExcelComponent->simpleDownload($goodsNm . '_주문리스트_'.date('ymd'),$LIST_TITLES,$excelBody);


    }

}
