<?php
namespace Component\Scm;

use App;
use Session;
use Component\Database\DBTableField;
use Component\Member\Manager;
use Framework\Utility\DateTimeUtils;
use LogHandler;
use Request;
use Exception;
use Framework\Debug\Exception\AlertBackException;
use SiteLabUtil\SlCommonUtil;
use SlComponent\Database\DBUtil;
use SlComponent\Database\SearchVo;
use SlComponent\Database\TableVo;
use SlComponent\Util\SitelabLogger;
use SlComponent\Util\SlCodeMap;
use SlComponent\Util\SlCommonTrait;
use DateTime;

/**
 *  공급사 재고 리포트 서비스
 * Class GoodsStock
 * @package Component\Goods
 */
class ScmStockReportService {
    use SlCommonTrait;

    private $sql;
    private $scmGoodsInfo;
    private $scmGoodsNoGoodsInfo;
    private $scmGoodsStock;
    private $scmOrderTotal;

    public function __construct(){
        $this->sql = \App::load(\Component\Scm\Sql\ScmStockReportSql::class);
    }

    /**
     * 공급사의 상품을 이 서비스내에 저장한다. (리포트화면 여러 지표에서 사용)
     * @param $reqData
     */
    public function setScmGoods($reqData){
        $this->scmGoodsInfo = $this->sql->getScmGoodsInfo($reqData);
        //gd_debug($this->scmGoodsInfo);

        foreach($this->scmGoodsInfo as $value){
            $this->scmGoodsNoGoodsInfo[$value['goodsNo']] = $value;
        }
        $this->scmGoodsStock = $this->sql->getScmGoodsStock($reqData);
        $this->scmOrderTotal = $this->sql->getTotalGoodsCnt($reqData);
        //gd_debug($this->scmGoodsInfo);
        //gd_debug($this->scmGoodsStock);
        //gd_debug($this->scmGoodsNoGoodsInfo);
    }

    /**
     * select 용 상품정보
     * @return array
     */
    public function getSelectedGoodsInfo(){
        $returnData = array();
        foreach($this->scmGoodsInfo as $goodsInfo){
            $returnData[$goodsInfo['goodsNo']] = $goodsInfo['goodsNm'];
        }
        return $returnData;
    }

    /**
     * 주문 집계 정보
     * @return mixed
     */
    public function getOrderTotal(){
        return $this->scmOrderTotal;
    }
    
    /**
     * 재고현황
     * @param $reqData
     * @return array
     */
    public function getCurrentStat($reqData){
        $fileName = '재고현황';
        $result = array();

        $optionClassData = array();
        $chartData = array();
        foreach($this->scmGoodsInfo  as $scmGoodsKey => $scmGoodsData){
            $goodsNo = $scmGoodsData['goodsNo'];
            $optionNo = $scmGoodsData['optionNo'];
            $optionClass = $scmGoodsData['optionClass'];
            $scmGoodsData['optionTotalPrice'] = $scmGoodsData['goodsPrice'] + $scmGoodsData['optionPrice'];

            $optionArray = array();
            for($i=1; $i<=5; $i++){
                if(!empty($scmGoodsData['optionValue'.$i])){
                    $optionArray[] = $scmGoodsData['optionValue'.$i];
                }
            }
            $optionName = implode( '/',$optionArray );
            $optionClassData[$optionClass]['title']['goodsNo'] = '상품코드';
            $optionClassData[$optionClass]['title']['goodsImage'] = '이미지';
            $optionClassData[$optionClass]['title']['goodsNm'] = '상품명';
            $optionClassData[$optionClass]['title']['goodsPrice'] = '판매가';
            $optionClassData[$optionClass]['title'][md5($optionName)] = $optionName;

            $chartData[$goodsNo]['title'][md5($optionName)] = $optionName;
            $chartData[$goodsNo]['data1'][$optionNo] = number_format($scmGoodsData['stockCnt']);
            $chartData[$goodsNo]['data2'][$optionNo] = number_format($scmGoodsData['safeCnt']);

            $optionClassData[$optionClass]['data'][$goodsNo]['goodsNo'] = $goodsNo;
            $optionClassData[$optionClass]['data'][$goodsNo]['goodsImage'] = gd_html_goods_image($goodsNo, $scmGoodsData['imageName'], $scmGoodsData['imagePath'], $scmGoodsData['imageStorage'], 40, $scmGoodsData['goodsNm'], '_blank');
            $optionClassData[$optionClass]['data'][$goodsNo]['goodsNm'] = $scmGoodsData['goodsNm'];
            $optionClassData[$optionClass]['data'][$goodsNo]['goodsPrice'] = number_format($scmGoodsData['goodsPrice']);
            $optionClassData[$optionClass]['data'][$goodsNo]['totalStock'] = $scmGoodsData['totalStock'];
            $optionClassData[$optionClass]['data'][$goodsNo]['option'][$optionNo] = $scmGoodsData;
            //$optionClassData[$optionClass]['title']['total'] = '합계';
        }

        $chartData = json_encode($chartData, JSON_UNESCAPED_SLASHES);

        $period = $reqData['searchDate'][0] . '~' . $reqData['searchDate'][1];
        $result['fileName'] = $fileName;
        $result['period'] = $period;
        //$result['title'] = $resultTitle;
        $result['data'] = $optionClassData;
        $result['chartData'] = $chartData;
        //gd_debug($optionClassData);
        //gd_debug($result['data']);
        //gd_debug($chartData);
        return $result;
    }


    /**
     * 기간별 출고 현황
     * @param $reqData
     * @return array
     */
    public function getPeriodStat($reqData){
        $fileName = '기간별출고현황';
        $result = array();
        $isWeek = false;
        $weekKorArr = [__('일요일'), __('월요일'), __('화요일'), __('수요일'), __('목요일'), __('금요일'), __('토요일')];

        $refineScmGoodsStock = array();
        if( 'stockWeek' === $reqData['linkId']  ){
            $isWeek = true;
        }
        foreach( $this->scmGoodsStock as $stockKey => $stockData){
            $keyDate = $stockData['regDt'];
            if( $isWeek ){
                $dt = new DateTime($stockData['regDt']);
                $keyDate = $dt->format('w');
            }
            //gd_debug($stockData['regDt']  . ':' .  $weekKorArr[$dt->format('w')] );

            if( empty($refineScmGoodsStock[$keyDate]['data'][$stockData['goodsNo']]) ){
                $refineScmGoodsStock[$keyDate]['data'][$stockData['goodsNo']]['stockCnt']  = $stockData['stockCnt'];
            }else{
                $refineScmGoodsStock[$keyDate]['data'][$stockData['goodsNo']]['stockCnt']  += $stockData['stockCnt'];
            }
        }
        ksort($refineScmGoodsStock);

        $chartData = array();
        foreach($refineScmGoodsStock as $regDt => $stockData){
            //gd_debug($regDt);
            foreach( $this->scmGoodsNoGoodsInfo as $goodsNo => $goodsInfo ){
                $stockCnt = empty($refineScmGoodsStock[$regDt]['data'][$goodsNo]['stockCnt'])?0 : $refineScmGoodsStock[$regDt]['data'][$goodsNo]['stockCnt'];

                $refineScmGoodsStock[$regDt]['data'][$goodsNo]['stockCnt'] = $stockCnt;
                $refineScmGoodsStock[$regDt]['data'][$goodsNo]['goodsNo'] = $goodsInfo['goodsNo'];
                $refineScmGoodsStock[$regDt]['data'][$goodsNo]['goodsNm'] = $goodsInfo['goodsNm'];
                $refineScmGoodsStock[$regDt]['data'][$goodsNo]['goodsPrice'] = $goodsInfo['goodsPrice'];

                if( $isWeek ){
                    $refineScmGoodsStock[$regDt]['data'][$goodsNo]['dpDate'] = $weekKorArr[$regDt];
                }else{
                    $refineScmGoodsStock[$regDt]['data'][$goodsNo]['dpDate'] = $regDt;
                }

                ksort($refineScmGoodsStock[$regDt]['data']);

                $refineScmGoodsStock[$regDt]['total'] += $stockCnt;

                $chartData[$goodsNo]['title'][$regDt] = $refineScmGoodsStock[$regDt]['data'][$goodsNo]['dpDate'];
                $chartData[$goodsNo]['data'][$regDt] = $refineScmGoodsStock[$regDt]['data'][$goodsNo]['stockCnt'];
            }
        }
        //gd_debug($chartData);
        //gd_debug($refineScmGoodsStock);
        $chartData = json_encode($chartData, JSON_UNESCAPED_SLASHES);
        $period = $reqData['searchDate'][0] . '~' . $reqData['searchDate'][1];
        $result['fileName'] = $fileName;
        $result['period'] = $period;
        $result['data'] = $refineScmGoodsStock;
        $result['chartData'] = $chartData;
        $result['goodsNoGoodsInfo'] = $this->scmGoodsNoGoodsInfo;

        return $result;
    }

    /**
     *  출고사이즈 비율
     * @param $reqData
     */
    public function getRatioStat($reqData){
        $fileName = '출고사이즈비율';
        $defaultData = $this->getCompareStat($reqData);
        $chartData = $defaultData['chartDataOrg'];

        //비율로 전환
        foreach( $defaultData['data'] as $optionClass => $data){
            foreach($data['data'] as $dataKey => $dataValue){
                $total = $dataValue['total'];
                foreach($dataValue['option'] as $dataValueKey => $optionValue){
                    if( !empty($optionValue['outStockCnt']) ){
                        $optionValue['outStockRatio'] = round($optionValue['outStockCnt'] / $total * 100,2);
                    }else{
                        $optionValue['outStockRatio'] = 0;
                    }
                    $defaultData['data'][$optionClass]['data'][$dataKey]['option'][$dataValueKey] = $optionValue;
                }
            }
        }

        $result['fileName'] = $fileName;
        $result['period'] = $defaultData['period'];
        $result['data'] = $defaultData['data'];

        //$chartData
        //gd_debug($chartData);
        foreach($chartData as $key => $value){
            foreach( $value['data'] as  $dataKey => $dataValue ){
                if(!empty($dataValue)){
                    $dataValue = round($dataValue / $value['total'] * 100,2);
                }
                $chartData[$key]['data'][$dataKey] = $dataValue;
            }
            //$value['total'];
        }

        $chartData = json_encode($chartData, JSON_UNESCAPED_SLASHES);
        $result['chartData'] = $chartData;
        //gd_debug($optionClassData);
        //gd_debug($result['data']['035f819a11e9257d75af49bf921c2c91']);
        //gd_debug($chartData);
        //gd_debug($chartData);
        return $result;
    }

    /**
     * 출고량 비교
     * @param $reqData
     */
    public function getCompareStat($reqData){
        $fileName = '출고량비교';
        
        $result = array();

        $refineScmGoodsStock = array();
        foreach( $this->scmGoodsStock as $stockKey => $stockValue){
            //gd_debug($stockValue);
            if( empty($refineScmGoodsStock[$stockValue['goodsNo']][$stockValue['optionNo']]) ){
                $refineScmGoodsStock[$stockValue['goodsNo']][$stockValue['optionNo']]  = $stockValue['stockCnt'];
            }else{
                $refineScmGoodsStock[$stockValue['goodsNo']][$stockValue['optionNo']]  += $stockValue['stockCnt'];
            }
        }

        $optionClassData = array();
        $chartData = array();

        foreach($this->scmGoodsInfo  as $scmGoodsKey => $scmGoodsData){
            $goodsNo = $scmGoodsData['goodsNo'];
            $optionNo = $scmGoodsData['optionNo'];
            $optionClass = $scmGoodsData['optionClass'];

            $scmGoodsData['optionTotalPrice'] = $scmGoodsData['goodsPrice'] + $scmGoodsData['optionPrice'];

            //추가되는 부분.
            $scmGoodsData['outStockCnt'] = $refineScmGoodsStock[$goodsNo][$optionNo];

            $optionArray = array();
            for($i=1; $i<=5; $i++){
                if(!empty($scmGoodsData['optionValue'.$i])){
                    $optionArray[] = $scmGoodsData['optionValue'.$i];
                }
            }
            $optionName = implode( '/',$optionArray );
            $optionClassData[$optionClass]['title']['goodsNo'] = '상품코드';
            $optionClassData[$optionClass]['title']['goodsImage'] = '이미지';
            $optionClassData[$optionClass]['title']['goodsNm'] = '상품명';
            $optionClassData[$optionClass]['title']['goodsPrice'] = '판매가';
            $optionClassData[$optionClass]['title'][md5($optionName)] = $optionName;

            $chartData[$goodsNo]['title'][md5($optionName)] = $optionName;;
            $chartData[$goodsNo]['data'][$optionNo] = number_format($scmGoodsData['outStockCnt']);
            $chartData[$goodsNo]['total'] += $scmGoodsData['outStockCnt'];

            //$chartData[$goodsNo]['total'] += $scmGoodsData['outStockCnt'];

            $optionClassData[$optionClass]['data'][$goodsNo]['goodsNo'] = $goodsNo;
            $optionClassData[$optionClass]['data'][$goodsNo]['goodsImage'] = gd_html_goods_image($goodsNo, $scmGoodsData['imageName'], $scmGoodsData['imagePath'], $scmGoodsData['imageStorage'], 40, $scmGoodsData['goodsNm'], '_blank');
            $optionClassData[$optionClass]['data'][$goodsNo]['goodsNm'] = $scmGoodsData['goodsNm'];
            $optionClassData[$optionClass]['data'][$goodsNo]['goodsPrice'] = number_format($scmGoodsData['goodsPrice']);
            $optionClassData[$optionClass]['data'][$goodsNo]['totalStock'] = $scmGoodsData['totalStock'];
            $optionClassData[$optionClass]['data'][$goodsNo]['option'][$optionNo] = $scmGoodsData;
            $optionClassData[$optionClass]['data'][$goodsNo]['total'] += $scmGoodsData['outStockCnt'];
            //$optionClassData[$optionClass]['title']['total'] = '합계';
        }

        $result['chartDataOrg'] = $chartData;
        $chartData = json_encode($chartData, JSON_UNESCAPED_SLASHES);

        $period = $reqData['searchDate'][0] . '~' . $reqData['searchDate'][1];
        $result['fileName'] = $fileName;
        $result['period'] = $period;
        //$result['title'] = $resultTitle;
        $result['data'] = $optionClassData;
        $result['chartData'] = $chartData;
        //gd_debug($optionClassData);
        //gd_debug($result['data']);
        //gd_debug($chartData);
        return $result;
    }

    /**
     * 전체 출고 현황
     * @param $reqData
     * @return array
     */
    public function getTotalStat($reqData){
        $fileName = '전체출고현황';

        $goodsStat = $this->scmOrderTotal;
        $claimStat = $this->sql->getTotalClaimCnt($reqData);

        $resultData = array_merge($goodsStat,$claimStat);
        $resultData['fineCnt'] =  $goodsStat['goodsCnt'] - $claimStat['claimCnt'];

        //gd_debug($resultData['goodsCnt']);

        $chartData = array();

        foreach( $resultData as $key => $value ){

            if(  'orderCnt' == $key || 'acctCnt' == $key  ){
                $dpValue = empty($value)?'0건' : number_format($value).'건';
            }else{
                $dpValue = empty($value)?'0장' : number_format($value).'장';
            }


            if( 'goodsCnt' !== $key ){
                $ratio = 0;
                if( (int)$value > 0 ){
                    $ratio = (int) ((int)$value / (int)$resultData['goodsCnt'] * 100 );
                }
                $dpValue .= '<small>(</small>'. $ratio . '%)';
                $chartData[$key] = $ratio;
            }
            $resultData['dp_'.$key] = $dpValue;
        }
        foreach( $resultData as $key => $value ){
            //gd_debug($value);
            $resultData[$key] = $value;
        }

        $resultTitle = [
            '전체 주문수량'
            , '<span style="color:red">미접수</span>'
            , '반품'
            , '교환'
            , '환불'
            , 'AS'
        ];
        $period = $reqData['searchDate'][0] . '~' . $reqData['searchDate'][1];
        $result['fileName'] = $fileName;
        $result['period'] = $period;
        $result['title'] = $resultTitle;
        $result['data'] = $resultData;
        $result['chartData'] = $chartData;
        return $result;
    }

}
