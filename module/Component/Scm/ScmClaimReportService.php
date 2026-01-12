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

/**
 *  공급사 재고 리포트 서비스
 * Class GoodsStock
 * @package Component\Goods
 */
class ScmClaimReportService {
    use SlCommonTrait;

    private $sql;
    private $scmGoodsInfo;
    private $scmGoodsNoGoodsInfo;
    private $scmGoodsStock;
    private $scmOrderTotal;

    public function __construct(){
        $this->sql = \App::load(\Component\Scm\Sql\ScmClaimReportSql::class);
    }

    /**
     * 공급사의 상품을 이 서비스내에 저장한다.
     * @param $reqData
     */
    public function setScmGoods($reqData){
        $this->scmGoodsInfo = $this->sql->getScmGoodsInfo($reqData);
        $this->scmOrderTotal = $this->sql->getTotalGoodsCnt($reqData);
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
        //gd_debug($this->scmGoodsInfo);
        return $returnData;
    }

    /**
     * 주문 집계 정보
     * @return mixed
     */
    public function getOrderTotal(){
        return $this->scmOrderTotal;
    }

    public function getClaimInfo($reqData){
        $result = array();
        $listData = $this->sql->getClaimInfo($reqData);

        //상품별 기본 값 셋팅
        foreach( SlCodeMap::CLAIM_BOARD_TYPE  as $claimBoardType => $claimName){
            foreach($this->getSelectedGoodsInfo() as $goodsNo => $goodsName ){

                $claimReasonList = SlCommonUtil::getClaimReasonByGoodsNo($goodsNo);
                foreach($claimReasonList as $claimReason => $claimReasonStr){
                    $result[$claimBoardType]['chartData'][$goodsNo][$claimReason] = 0;
                    $result[$claimBoardType]['chartLabel'][$goodsNo][$claimReason] = $claimReasonStr;
                }

            }
        }

        foreach($listData as $key => $value){
            $value['claimTypeStr'] = SlCodeMap::CLAIM_BOARD_TYPE[$value['claimBoardType']];
            $value['claimReasonStr'] = SlCodeMap::ADMIN_CLAIM_REASON[$value['claimReason']];
            if( empty($result[$value['claimBoardType']]['data'][$value['goodsNo']])  ){
                $value['rowspan'] = 'true';
            }
            ++$result[$value['claimBoardType']]['data'][$value['goodsNo']]['rowspan'];

            $result[$value['claimBoardType']]['data'][$value['goodsNo']]['data'][$value['claimReason']] = $value;
            $result[$value['claimBoardType']]['data'][$value['goodsNo']]['totalClaimCnt'] += $value['claimCnt'];

            //차트
            $result[$value['claimBoardType']]['chartData'][$value['goodsNo']][$value['claimReason']] = $value['claimCnt'];

        }

        //차트 데이터 JSON만들기
        foreach( SlCodeMap::CLAIM_BOARD_TYPE  as $claimBoardType => $claimName){
                $result[$claimBoardType]['chartData'] = json_encode($result[$claimBoardType]['chartData'], JSON_UNESCAPED_SLASHES);
                $result[$claimBoardType]['chartLabel'] = json_encode($result[$claimBoardType]['chartLabel'], JSON_UNESCAPED_SLASHES);
        }
        //gd_debug($result);
        //$result[$value['claimBoardType']][$value['goodsNo']]['data'][$value['claimReason']]
        //gd_debug($result);
        //$chartData[$goodsNo]['title'][$regDt] = $refineScmGoodsStock[$regDt]['data'][$goodsNo]['dpDate'];
        //$chartData[$goodsNo]['data'][$regDt] = $refineScmGoodsStock[$regDt]['data'][$goodsNo]['stockCnt'];
        //gd_debug( $result );

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
        foreach( $resultData as $key => $value ){
            if(  'orderCnt' == $key || 'acctCnt' == $key  ){
                $dpValue = empty($value)?'0건' : number_format($value).'건';    
            }else{
                $dpValue = empty($value)?'0장' : number_format($value).'장';
            }

            if( 'goodsCnt' !== $key && 'acctCnt' !== $key && 'orderCnt' !== $key && 'claimCnt' !== $key ){
                $ratio = 0;
                if( (int)$value > 0 ){
                    $ratio = (int) ((int)$value / (int)$resultData['goodsCnt'] * 100 );
                }
                $dpValue .= '<small>(</small>'. $ratio . '%)';
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
        $result['fileName'] = $fileName.'_'.$period;
        $result['period'] = $period;
        $result['title'] = $resultTitle;
        $result['data'] = $resultData;
        return $result;
    }

}
