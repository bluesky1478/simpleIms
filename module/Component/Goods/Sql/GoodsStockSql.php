<?php


namespace Component\Goods\Sql;


use SlComponent\Database\DBUtil;
use SlComponent\Database\SearchVo;
use SlComponent\Database\TableVo;

/**
 * 상품 재고 관리 SQL
 * Class GoodsStockSql
 * @package Component\Goods\Sql
 */
class GoodsStockSql{

    /**
     * 상품 옵션 정보를 가져온다.
     * @param $goodsNo
     * @return mixed
     */
    public function getGoodsOptionInfoAndStockCheck($goodsNo){
        $table['table1'] = new TableVo(DB_GOODS_OPTION,'tableGoodsOption','a');
        $table['table2'] = new TableVo(DB_GOODS,'tableGoods','b');
        $table['table2']->setField('b.goodsNm');
        $table['table2']->setJoinType('JOIN');
        $table['table2']->setJoinCondition('a.goodsNo = b.goodsNo');
        $searchVo = new SearchVo('a.goodsNo=?',$goodsNo);
        $searchVo->setWhere('b.stockFl=?');
        $searchVo->setWhereValue('y');
        $searchVo->setOrder('a.optionNo');
        return DBUtil::getComplexList($table,$searchVo);
    }

}