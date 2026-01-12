<?php


namespace Component\Goods\Sql;


use SlComponent\Database\DBUtil;
use SlComponent\Database\SearchVo;
use SlComponent\Database\TableVo;

/**
 * 상품 정책 관리
 * Class GoodsPolicySql
 * @package Component\Goods\Sql
 */
class GoodsPolicySql{

    /**
     * 상품번호 IN SearchVO 반환
     * @param $goodsNoList
     * @return SearchVo
     */
    public function getInConditionByGoodsNo($goodsNoList){
        $searchVo = new SearchVo();
        $searchVo->setWhere(DBUtil::bind('goodsNo',DBUtil::IN,count($goodsNoList)) );
        $searchVo->setWhereValueArray($goodsNoList);
        return $searchVo;
    }

    /**
     * 상품 정책 정보 반환
     * @param $goodsNoList
     * @return mixed
     */
    public function getGoodsPolicyInfo($goodsNoList){
        //정책 가져오기
        $tableList['goodsPolicyTable'] = new TableVo('sl_goodsPolicy','tableGoodsPolicy','a');
        $tableList['goodsPolicyTable']->setField('a.goodsNo,a.policyFreeSaleSno,a.policySaleSno,a.policySurveySno');

        $tableList['freePolicyTable'] = new TableVo('sl_policyFreeSale','tableFreeSalePolicy','b');
        $tableList['freePolicyTable']->setJoinType('LEFT OUTER JOIN');
        $tableList['freePolicyTable']->setJoinCondition('a.policyFreeSaleSno = b.sno');
        $tableList['freePolicyTable']->setField('b.policyName as policyFreeSaleName');

        $tableList['salePolicyTable'] = new TableVo('sl_policySale','tablePolicySale','c');
        $tableList['salePolicyTable']->setJoinType('LEFT OUTER JOIN');
        $tableList['salePolicyTable']->setJoinCondition('a.policySaleSno = c.sno');
        $tableList['salePolicyTable']->setField('c.policyName as policySaleName');

        $tableList['surveyPolicyTable'] = new TableVo('sl_policySurvey','tablePolicySurvey','d');
        $tableList['surveyPolicyTable']->setJoinType('LEFT OUTER JOIN');
        $tableList['surveyPolicyTable']->setJoinCondition('a.policySurveySno = d.sno');
        $tableList['surveyPolicyTable']->setField('d.policyName as policySurveyName');

        return DBUtil::getComplexList($tableList,$this->getInConditionByGoodsNo($goodsNoList));
    }

    /**
     * 상품 정책별 회원수 반환
     * @param $goodsNoList
     */
    public function getGoodsPolicyMemberCount($goodsNoList){
        $tableVo = new TableVo('sl_goodsPolicyMember','tableGoodsPolicyMember');
        $tableVo->setField('goodsNo, count(1) memberCount');
        $searchVo = $this->getInConditionByGoodsNo($goodsNoList);
        $searchVo->setGroup('goodsNo');
        return DBUtil::getComplexList([$tableVo],$searchVo);
    }

    /**
     * 상품 회원 연결정보
     * @param $searchData
     * @return mixed
     */
    public function getPolicyGoodsMemberList($searchData){
        $tableList['goodsPolicyTable'] = new TableVo('sl_goodsPolicyMember','tableGoodsPolicyMember','a');
        $tableList['goodsPolicyTable']->setField('a.sno,a.goodsNo,a.memNo');
        $tableList['freePolicyTable'] = new TableVo('es_member','tableMember','b');
        $tableList['freePolicyTable']->setJoinType('JOIN');
        $tableList['freePolicyTable']->setJoinCondition('a.memNo = b.memNo');
        $tableList['freePolicyTable']->setField('b.memNm,b.memId,b.ex1');
        return DBUtil::getComplexList($tableList,new SearchVo('goodsNo=?',$searchData['goodsNo']));
    }

    /**
     * 상품+회원별 정책 가져오기
     * @param $searchData
     * @return mixed
     */
    public function getPolicyByGoodsMember($searchData){

        /*$saveData['policyInfo'] = $unitOrderGoodsApplyPolicyInfo['unitPolicyInfo'];
        $saveData['freeDcCount'] = $unitOrderGoodsApplyPolicyInfo['unitCustomDcInfo']['freeDcCount'];
        $saveData['freeDcAmount'] = $unitOrderGoodsApplyPolicyInfo['unitCustomDcInfo']['freeDcAmount'];
        $saveData['companyPayment'] = $unitOrderGoodsApplyPolicyInfo['unitCustomDcInfo']['companyPayment'];
        $saveData['buyerPayment'] = $unitOrderGoodsApplyPolicyInfo['unitCustomDcInfo']['buyerPayment'];*/

        //상품별 정책
        if( DBUtil::runSelect("select count(1) cnt from es_member where memNo='{$searchData['memNo']}' AND freeFl = 'y' ")[0]['cnt'] > 0 ){
            $tableList['table1'] = new TableVo(DB_GOODS,'tableGoods','a');
            $tableList['table1']->setField('a.goodsNo, -1 as policyFreeSaleSno, null as policySaleSno, null as policySurveySno, 9999 AS freeBuyerCount, \'자동무상정책\' AS policyFreeSaleName');
            //상품별 회원
            $tableList['table2'] = new TableVo(" ( select '{$searchData['memNo']}' AS memNo , {$searchData['goodsNo']} as goodsNo ) ",null,'b');
            $tableList['table2']->setField("b.memNo");
            $tableList['table2']->setJoinType('JOIN');
            $tableList['table2']->setJoinCondition('a.goodsNo = b.goodsNo');

            //무상정책
/*            $tableList['table3'] = new TableVo(DB_GOODS,'tableGoods','c');
            $tableList['table3']->setField('-1 AS sno,   9999 AS freeBuyerCount, \'자동무상정책\' AS policyFreeSaleName');
            $tableList['table3']->setJoinType('LEFT OUTER JOIN');
            $tableList['table3']->setJoinCondition('a.policyFreeSaleSno = c.sno ');*/
        }else{
            $tableList['table1'] = new TableVo('sl_goodsPolicy','tableGoodsPolicy','a');
            $tableList['table1']->setField('a.goodsNo, a.policyFreeSaleSno, a.policySaleSno, a.policySurveySno');

            //상품별 회원
            $tableList['table2'] = new TableVo('sl_goodsPolicyMember','tableGoodsPolicyMember','b');
            $tableList['table2']->setField('b.memNo');
            $tableList['table2']->setJoinType('JOIN');
            $tableList['table2']->setJoinCondition('a.goodsNo = b.goodsNo');

            //무상정책
            $tableList['table3'] = new TableVo('sl_policyFreeSale','tablePolicyFreeSale','c');
            $tableList['table3']->setField('c.freeBuyerCount, c.policyName as policyFreeSaleName');
            $tableList['table3']->setJoinType('LEFT OUTER JOIN');
            $tableList['table3']->setJoinCondition('a.policyFreeSaleSno = c.sno AND c.useFl=\'y\'');

            //할인정책
            $tableList['table4'] = new TableVo('sl_policySale','tablePolicySale','d');
            $tableList['table4']->setField('d.companyRatio, d.policyName as policySaleName');
            $tableList['table4']->setJoinType('LEFT OUTER JOIN');
            $tableList['table4']->setJoinCondition('a.policySaleSno = d.sno AND d.useFl=\'y\'');

            //설문정책
            $tableList['table5'] = new TableVo('sl_policySurvey','tablePolicySurvey','e');
            $tableList['table5']->setField('e.surveyDayCount,e.surveyAddress, e.policyName as policySurveyName');
            $tableList['table5']->setJoinType('LEFT OUTER JOIN');
            $tableList['table5']->setJoinCondition('a.policySurveySno = e.sno AND e.useFl=\'y\'');
        }

        $searchVo = new SearchVo('a.goodsNo=?',$searchData['goodsNo']);
        $searchVo->setWhere('b.memNo=?');
        $searchVo->setWhereValue($searchData['memNo']);
        return DBUtil::getComplexList($tableList,$searchVo)[0];
    }

    public function getFreeCountByGoodsNoAndMemNo($goodsNo, $memNo){
        //'SUM(freeDcCount) '
        $tableVo = new TableVo('sl_orderGoodsPolicy','tableOrderGoodsPolicy');
        $tableVo->setField('SUM(freeDcCount) AS freeDcCount');
        $searchVo = new SearchVo(['goodsNo=?','memNo=?'],[$goodsNo,$memNo]);
        return DBUtil::getOneBySearchVo($tableVo, $searchVo);
    }

    public function getGoodsMemberLinkInfo($goodsNoList, $memNo){
        $strSQL = "SELECT memNo, count(1) cnt FROM sl_goodsPolicyMember WHERE goodsNo IN (  {$goodsNoList } ) AND memNo = {$memNo} GROUP BY memNo";
        return DBUtil::runSelect($strSQL, null);
    }

}