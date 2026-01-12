<?php

namespace Component\Imsv2\Lst;

use Component\Database\DBTableField;
use SlComponent\Database\DBUtil2;
use SlComponent\Database\SearchVo;
use SlComponent\Util\SitelabLogger;

/**
 * IMS (+폐쇄몰) 통틀어서 재고/입출고 관리.
 *
 * Class ImsStockService
 * @package Component\Imsv2
 */
class StockInOutList implements ListInterface
{
    use ListTrait;

    public function getListField(){
        return [
            ['title' => '구분', 'type' => 's', 'name' => 'inOutTypeKr', 'col' => 5],
            ['title' => '입/출고일자', 'type' => 'd1', 'name' => 'inOutDate', 'col' => 8],
            ['title' => '코드', 'type' => 's', 'name' => 'thirdPartyProductCode', 'col' => 10 ,'class'=>'font-11 ta-l pdl5'],
            ['title' => '상품명', 'type' => 's', 'name' => 'productName', 'col' => 13,'class'=>'font-11 ta-l pdl5'],
            ['title' => '옵션', 'type' => 's', 'name' => 'optionName', 'col' => 8],
            ['title' => '수량', 'type' => 'i', 'name' => 'quantity', 'col' => 5],
            ['title' => '주문번호', 'type' => 's', 'name' => 'orderNo', 'col' => 10, 'class'=>'font-11'],
            ['title' => '주문자', 'type' => 's', 'name' => 'memNm', 'col' => 8,'class'=>'font-11'],
            ['title' => '수령자/메모', 'type' => 'c', 'name' => 'receiverName', 'class'=> 'font-11 ta-l pdl10',  'col' => 20],
            ['title' => '배송정보', 'type' => 's', 'name' => 'invoiceNo', 'col' => 8,'class'=>'font-11'],
        ];
    }

    /**
     * 정렬 설정
     * @param $sortCondition
     * @param SearchVo $searchVo
     */
    public function setOrder($sort, SearchVo $searchVo){
        //기본 Order
        $sortCondition = explode(',', $sort);
        $sortMap = [
            'IO1' => "ioHis.inOutDate {$sortCondition[1]}",
        ];
        $sort = $sortMap[$sortCondition[0]];
        if(!empty($sort)){
            $searchVo->setOrder($sort);
        }
    }

    /**
     * 입출고 리스트 반환
     * @param $params
     * @return array
     */
    public function getList($params){
        $searchVo = new SearchVo();
        return $this->getTraitList($params, $searchVo);
    }

    /**
     * 리스트 가져올 테이블
     * @param $params
     * @return array
     * @throws \Exception
     */
    public function getTableInfo($params){
        $tplFieldList = DBTableField::getDefaultFieldList('sl_3plProduct',['thirdPartyProductCode','payedFl','workPayedFl']);

        return DBUtil2::setTableInfo([
            'ioHis' => //메인 (입출고 리스트)
                [
                    'data' => [ 'sl_3plStockInOut' ] //Fact !
                    , 'field' => ['*']
                ],
            'tpl' =>  //3PL 상품 정보
                [
                    'data' => [ 'sl_3plProduct', 'LEFT OUTER JOIN', 'ioHis.productSno = tpl.sno' ]
                    , 'field' => $tplFieldList
                ],
            'member' => //주문 회원 정보
                [
                    'data' => [ DB_MEMBER, 'LEFT OUTER JOIN', 'ioHis.memNo = member.memNo' ]
                    , 'field' => ['memNm']
                ],
            'og' => //폐쇄몰 주문 상품 정보
                [
                    'data' => [ DB_ORDER_GOODS, 'LEFT OUTER JOIN', 'ioHis.orderGoodsSno = og.sno' ]
                    , 'field' => ['handleSno','orderStatus','goodsNo','goodsNm','optionSno']
                ],
        ]);
    }

    /**
     * 검색 조건 설정
     * @param $condition
     * @param SearchVo $searchVo
     * @return SearchVo
     */
    public function setCondition($condition,SearchVo $searchVo){
        $this->setSearchKeywordCondition($condition, $searchVo);

        //날짜 검색
        if( !empty($condition['startDt']) ){
            $searchVo->setWhere('ioHis.inOutDate  >= ?');
            $searchVo->setWhereValue( $condition['startDt'] );
        }
        if( !empty($condition['endDt']) ){
            $searchVo->setWhere('ioHis.inOutDate <= ?');
            $searchVo->setWhereValue( $condition['endDt'].' 23:59:59' );
        }
        
        //입출고 구분
        if( !empty($condition['inOutType']) ){
            $searchVo->setWhere('ioHis.inOutType=?');
            $searchVo->setWhereValue($condition['inOutType']);
        }

        //특정 고객사
        if( !empty($condition['scmNo']) ){
            $searchVo->setWhere('tpl.scmNo=?');
            $searchVo->setWhereValue($condition['scmNo']);
        }

        //해당 상품의 코드들만 가져온다.
        if( !empty($condition['goodsNo']) ){
            $searchVo->setWhere("ioHis.thirdPartyProductCode in (select code from sl_goodsOptionLink where goodsNo = '{$condition['goodsNo']}' )");
        }
        
        return $searchVo;
    }

    /**
     * 리스트 추가 데이터
     * @param $each
     * @return mixed
     */
    public function decoration($each){
        //$each['inOutTypeKr'] = SlCodeMap::STOCK_TYPE;
        return $each;
    }

}