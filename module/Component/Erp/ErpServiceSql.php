<?php
namespace Component\Erp;

use App;
use Component\Member\Manager;
use Framework\Debug\Exception\AlertBackException;
use Framework\Utility\DateTimeUtils;
use LogHandler;
use Request;
use Session;
use SiteLabUtil\SlCommonUtil;
use SlComponent\Database\DBUtil;
use SlComponent\Database\DBUtil2;
use SlComponent\Database\SearchVo;
use SlComponent\Util\ListSqlTrait;
use SlComponent\Util\SitelabLogger;

/**
 * ERP 서비스 SQL
 * Class GoodsStock
 * @package Component\Goods
 */
class ErpServiceSql {

    use ListSqlTrait;

    public function selectSummaryInOutListCommonSql($searchData){
        //Search
        $searchVo = $this->createDefaultSearchVo($searchData);
        $searchVo = $this->setCondition($searchData, $searchVo); //기본 외 조건 추가 검색
        return [
            'searchVo' => $searchVo,
            'searchData' => $searchData,
        ];
    }


    /**
     * 재고 이력 반환
     * @param $searchData
     * @return array
     */
    public function selectInOutList($searchData){
        $commonData = $this->selectSummaryInOutListCommonSql($searchData);
        $searchData = $commonData['searchData'];
        $searchVo  = $commonData['searchVo'];

        $tableList= [
            'a' => //메인
                [
                    'data' => [ 'sl_3plStockInOut' ]
                    , 'field' => [
                        'a.sno',
                        'a.productSno',
                        'a.thirdPartyProductCode',
                        'a.inOutType',
                        'a.inOutReason',
                        'a.inOutDate'
                    ]
                ]
            , 'b' => //상품 정보
                [
                    'data' => [ 'sl_3plProduct', 'JOIN', 'a.productSno = b.sno' ]
                    , 'field' => ['b.productName', 'b.optionName', 'b.scmName']
                ]
        ];
        $table = DBUtil2::setTableInfo($tableList, false);
        return DBUtil2::getComplexListWithPaging($table ,$searchVo, $searchData);
    }

    /**
     * 재고 이력 반환
     * @param $searchData
     * @return array
     */
    public function selectSummaryInOutList($searchData){

        $commonData = $this->selectSummaryInOutListCommonSql($searchData);
        $searchData = $commonData['searchData'];
        $searchVo  = $commonData['searchVo'];;

        $filedList = [
            'a.productSno',
            'a.thirdPartyProductCode',
            'a.inOutType',
            'a.inOutReason',
            'a.inOutDate',
        ];
        $tableList= [
            'a' => //메인
                [
                    'data' => [ 'sl_3plStockInOut' ]
                    , 'field' => array_merge($filedList, ['sum(a.quantity) as quantity'])
                ]
            , 'b' => //상품 정보
                [
                    'data' => [ 'sl_3plProduct', 'JOIN', 'a.productSno = b.sno' ]
                    , 'field' => ['b.productName', 'b.optionName', 'b.scmName']
                ]
        ];
        $table = DBUtil2::setTableInfo($tableList, false);
        $searchVo->setGroup( implode(',',array_merge($filedList, $tableList['b']['field'])));
        //정렬 설정
        $searchVo->setOrder($searchData['sort']);

        return DBUtil2::getComplexListWithPaging($table ,$searchVo, $searchData);
    }

    /**
     * 재고 이력 Count
     * @param $searchData
     * @return mixed
     */
    public function selectInOutStockCount($searchData){
        $commonData = $this->selectSummaryInOutListCommonSql($searchData);
        $searchVo  = $commonData['searchVo'];
        $tableList= [
            'a' => //메인
                [
                    'data' => [ 'sl_3plStockInOut' ]
                    , 'field' => ['sum(a.quantity) as totalStockCnt']
                ]
            , 'b' => //상품 정보
                [
                    'data' => [ 'sl_3plProduct', 'JOIN', 'a.productSno = b.sno' ]
                    , 'field' => [
                        'sum(if(1 = a.inOutType,a.quantity,0)) as inStockCnt, 
                        sum(if(2=a.inOutType,(a.quantity),0)) as outStockCnt'
                    ]
                ]
        ];
        $table = DBUtil2::setTableInfo($tableList, false);

        return DBUtil2::getComplexList($table ,$searchVo)[0];
    }


    /**
     * @param $searchData
     * @param $searchVo
     * @return mixed
     */
    public function setCondition($searchData, $searchVo){
        //1. 공급사
        if( !empty($searchData['scmFl']) && 'all' !== $searchData['scmFl']  ){
            if( 'n' === $searchData['scmFl']){
                //본사
                $searchVo->setWhere('b.scmNo = ?');
                $searchVo->setWhereValue('1');
            }else{
                //공급사 검색
                $searchVo->setWhere(DBUtil2::bind('b.scmNo', DBUtil2::IN, count($searchData['scmNo']) ));
                $searchVo->setWhereValueArray( $searchData['scmNo']  );
            }
        }
        //2. 유형
        if( !empty($searchData['inOutType']) ){
            $searchVo->setWhere( 'a.inOutType = ?');
            $searchVo->setWhereValue( $searchData['inOutType']  );
        }
        //3. 사유
        if( !empty($searchData['inOutReason'])  && 'all' !== $searchData['inOutReason'][0]  ){
            $searchVo->setWhere(DBUtil2::bind('a.inOutReason', DBUtil2::IN, count($searchData['inOutReason']) ));
            $searchVo->setWhereValueArray( $searchData['inOutReason'] );
        }

        //4. 마감일자
        if( !empty($searchData['closingDate']) ){
            $searchVo->setWhere('a.closingDate = ?');
            $searchVo->setWhereValueArray( $searchData['closingDate'] );
        }
        //5. 마감번호
        if( isset($searchData['closingSno']) ){
            if( empty($searchData['closingSno']) ){
                $searchVo->setWhere('(a.closingSno is null or a.closingSno = 0 )');
            }else{
                $searchVo->setWhere('a.closingSno=?');
                $searchVo->setWhereValueArray( $searchData['closingSno'] );
            }
        }

        return $searchVo;
    }


    public function selectStockCount(){
        return DBUtil2::runSelect("select sum(stockCnt) as totalStockCnt from sl_3plProduct")[0]['totalStockCnt'];
    }

    public function selectProductList(){
        /*$tableList= [
            'a' => //메인
                [
                    'data' => [ 'sl_3plProduct' ]
                    , 'field' => ['*']
                ]
        ];
        $table = DBUtil2::setTableInfo($tableList);*/
        //return DBUtil2::getComplexList($table , $searchVo);
        //(array $tableList,SearchVo $searchVo, $isAllCountMode=false, $isDebug = false, $isStrip = true){
        $searchVo = new SearchVo('1=?','1');
        $searchVo->setOrder('thirdPartyProductCode');
        return DBUtil2::getListBySearchVo('sl_3plProduct',$searchVo);
    }


    public function selectStockCompareData($searchData){

        $commonData = $this->selectSummaryInOutListCommonSql($searchData);
        $searchVo  = $commonData['searchVo'];

        $tableList= [
            'a' => //메인
                [
                    'data' => [ 'sl_3plProduct' ]
                    , 'field' => [
                        'scmName',
                        'thirdPartyProductCode',
                        'productName',
                        'optionName',
                        'stockCnt as prdStockCnt',
                    ]
                ]
            , 'b' => //상품 옵션 정보
                [
                    'data' => [ 'es_goodsOption', 'JOIN', 'a.thirdPartyProductCode = b.optionCode' ]
                    , 'field' => ['optionValue1','optionValue2','optionValue3','optionValue4','optionValue5','stockCnt']
                ]
            , 'c' => //상품 정보
                [
                    'data' => [ 'es_goods', 'JOIN', 'b.goodsNo = c.goodsNo' ]
                    , 'field' => ['goodsNo', 'goodsNm', 'goodsDisplayFl', 'goodsSellFl']
                ]
        ];
        $table = DBUtil2::setTableInfo($tableList);

        return DBUtil2::getComplexList($table ,$searchVo);
    }


}
