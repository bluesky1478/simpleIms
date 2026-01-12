<?php
namespace Component\Imsv2;

use App;
use Component\Ims\ImsCodeMap;
use Component\Ims\ImsDBName;
use Component\Ims\ImsServiceConditionTrait;
use Component\Ims\ImsServiceSortTrait;
use Component\Member\Manager;
use Component\Storage\Storage;
use Framework\Debug\Exception\AlertBackException;
use Framework\Utility\DateTimeUtils;
use LogHandler;
use Request;
use Session;
use SiteLabUtil\SlCommonUtil;
use SlComponent\Database\DBConst;
use SlComponent\Database\DBUtil2;
use SlComponent\Database\SearchVo;
use SlComponent\Godo\ListInterface;
use SlComponent\Util\ListUtil;
use SlComponent\Util\SlCommonTrait;
use SlComponent\Util\SlLoader;

/**
 * 입출고 리스트
 * Class GoodsStock
 * @package Component\Goods
 */
class ImsCustomerListService {
    use ImsServiceConditionTrait;
    use ImsServiceSortTrait;

    private $sql;

    public function __construct(){
        $this->sql = \App::load('\\Component\\Imsv2\\Sql\\ImsCustomerListSql');
    }

    /**
     * 필드 데이터
     * @return array[]
     */
    public function getField(){
        $field = [
            ['title'=>'고객사명','type'=>'c','name'=>'custNameAndCode','col'=>6,'rowspan'=>'2','colspan'=>'1'],
            //['title'=>'담당자정보','type'=>'c','name'=>'contactInfo','col'=>9,],
            /*{% each.contactName %}<span class="font-10">({% each.contactMobile %} / {% each.contactEmail %})</span>*/
            ['title'=>'담당자','type'=>'s','name'=>'contactName','col'=>2,'rowspan'=>'2','colspan'=>'1'],
            ['title'=>'연락처','type'=>'s','name'=>'contactMobile','col'=>3,'class'=>'pdl5 ta-l','rowspan'=>'2','colspan'=>'1'],
            ['title'=>'이메일','type'=>'s','name'=>'contactEmail','col'=>3,'class'=>'pdl5 ta-l','rowspan'=>'2','colspan'=>'1'],

            //['title'=>'SF/기타 스타일','type'=>'c','name'=>'etcStyle','col'=>7,],
            //['title'=>'SS 스타일','type'=>'c','name'=>'ssStyle','col'=>7,],
            //['title'=>'FW 스타일','type'=>'c','name'=>'fwStyle','col'=>7,],

            //['title'=>'3PL','type'=>'s','name'=>'use3plKr','col'=>2,'rowspan'=>'2','colspan'=>'1'],
            //['title'=>'폐쇄몰','type'=>'s','name'=>'useMallKr','col'=>2,'rowspan'=>'2','colspan'=>'1'],
            ['title'=>'담당','type'=>'s','name'=>'salesManagerNm','col'=>2,'rowspan'=>'2','colspan'=>'1'],
            //['title'=>'등록일','type'=>'d1','name'=>'regDt','col'=>2,'rowspan'=>'2','colspan'=>'1'],

            ['title'=>'총매입','type'=>'c','name'=>'customerCost','col'=>2,'rowspan'=>'2','colspan'=>'1', 'class'=>'text-blue ta-r'],
            ['title'=>'총매출','type'=>'c','name'=>'customerPrice','col'=>2,'rowspan'=>'2','colspan'=>'1', 'class'=>'text-danger ta-r'],
            ['title'=>'총마진','type'=>'c','name'=>'customerMargin','col'=>2,'rowspan'=>'2','colspan'=>'1', 'class'=>'ta-r text-left'],
            //alter table sl_imsCustomer add customerCost int(10) NULL  COMMENT '고객 매입'  after addedInfo;
            //alter table sl_imsCustomer add customerPrice int(10) NULL  COMMENT '고객 매출'  after addedInfo;

        ];

        $firstYear = date("y", strtotime("-2 year"));
        for($n=2; $n>=0; $n--){
            $field[] = ['title'=>($firstYear+$n).'년집계','type'=>'title','name'=>'sum'.($firstYear+$n),'col'=>2,'class'=>'pdl5 ta-l','rowspan'=>'1','colspan'=>'3','skip'=>true];
            $field[] = ['title'=>($firstYear+$n).'매입','type'=>'c','name'=>'sum'. ($firstYear+$n) .'Cost','col'=>2,'class'=>'ta-r','subRow'=>true];
            $field[] = ['title'=>($firstYear+$n).'매출','type'=>'c','name'=>'sum'. ($firstYear+$n) .'Price','col'=>2,'class'=>'ta-r','subRow'=>true];
            $field[] = ['title'=>($firstYear+$n).'마진','type'=>'c','name'=>'sum'. ($firstYear+$n) .'Margin','col'=>2,'class'=>'ta-r','subRow'=>true];
        }
        return $field;
    }

    /**
     * 고객 리스트 준비
     * @param $listType
     * @param $sortDefault
     * @param $params
     * @param $isGroup
     * @param $isSchedule
     * @return array
     */
    public function preparedList($listType, $sortDefault, $params){
        $setConditionFnc = 'set'.ucfirst($listType).'Condition';

        $searchData = [
            'page' => gd_isset($params['page'], 1),
            'pageNum' => gd_isset($params['pageNum'], 200),
        ];
        $searchData['condition'] = $params;

        $searchVo = new SearchVo();
        $searchVo->setGroup('cust.sno');

        $searchVo->setExcludeTableAlias(['added']);

        $searchVo = $this->$setConditionFnc($searchData['condition'], $searchVo);
        $this->setListSort(gd_isset($searchData['condition']['sort'],$sortDefault), $searchVo);

        $tableInfo = $this->sql->getCustomerListTableInfo();
        $allData = DBUtil2::getComplexListWithPaging($tableInfo, $searchVo, $searchData, false, false);
        $addParams = [];
        $allData['listData'] = SlCommonUtil::setEachData($allData['listData'], $this, 'decorationList', $addParams);
        return $allData;
    }

    /**
     * 고객 리스트 추가 정보
     * @param $each
     * @param $key
     * @param $addParams
     * @return mixed
     */
    public function decorationList($each, $key, $addParams){
        $each['customerYearPrice'] = json_decode($each['customerYearPrice'], true);

        $each['use3plKr'] = ImsCodeMap::YES_OR_NO_TYPE[$each['use3pl']];
        $each['useMallKr'] = ImsCodeMap::YES_OR_NO_TYPE[$each['useMall']];

        //착용 스타일
        $each['useMallKr'] = ImsCodeMap::YES_OR_NO_TYPE[$each['useMall']];

        $usePrdList = [];
        $sql = "select 
                    distinct concat(c.codeValueKr,' ',b.codeValueKr,' ',a.addStyleCode) as styleName,
                    a.prdSeason  
                from sl_imsProjectProduct a 
                    left outer join sl_imsCode b on a.prdStyle = b.codeValueEn and '스타일' = b.codeType
                    left outer join sl_imsCode c on a.prdSeason = c.codeValueEn and '시즌' = c.codeType
                    join sl_imsProject prj on a.projectSno = prj.sno 
                where a.customerSno={$each['sno']} 
                  and a.delFl='n'
                  and prj.projectType in ( 0, 2, 6, 8, 5, 1  ) 
                order by productName ";
        $seasonPrdList = DBUtil2::runSelect($sql);

        foreach( $seasonPrdList as $seasonPrd ){
            if( in_array($seasonPrd['prdSeason'],['FW','SS']) ){
                $each[strtolower($seasonPrd['prdSeason']).'Style'][] = $seasonPrd;
            }else{
                $each['etcStyle'][] = $seasonPrd;
            }
        }

        return $each;
    }

    public function setCustomerCondition($condition, SearchVo $searchVo){
        return $this->setCommonCondition($condition, $searchVo);
    }

    /**
     * 리오더 프로젝트 리스트
     * @param $params
     * @return array
     */
    public function getCustomerList($params)
    {
        $allData = $this->preparedList('customer', 'D1,desc', $params); //FIXME : 정렬 방법 다양화 하기
        $fieldData = $this->getField();
        $list = $allData['listData'];
        SlCommonUtil::setColWidth(95, $fieldData);
        //--- Rowspan설정
        return [
            'pageEx' => $allData['pageData']->getPage('#'),
            'page' => $allData['pageData'],
            'list' => $list,
            'fieldData' => $fieldData
        ];
    }


}
