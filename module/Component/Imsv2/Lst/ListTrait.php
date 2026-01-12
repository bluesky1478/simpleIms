<?php
namespace Component\Imsv2\Lst;

use App;
use Component\Database\DBTableField;
use Component\Member\Manager;
use Component\Sms\Code;
use Framework\Debug\Exception\AlertBackException;
use Framework\Utility\DateTimeUtils;
use LogHandler;
use Request;
use Session;
use SiteLabUtil\SlCommonUtil;
use SlComponent\Database\DBUtil2;
use SlComponent\Database\SearchVo;
use SlComponent\Util\SitelabLogger;
use SlComponent\Util\SlSmsUtil;

/**
 * IMS 상품관련 서비스
 * Class GoodsStock
 * @package Component\Goods
 */
trait ListTrait {

    /**
     * @param $params
     *   - condition 필수
     * @param SearchVo $searchVo
     * @return array
     */
    public function getTraitList($params, SearchVo $searchVo){
        /*if(null === $searchVo){
            $searchVo = new SearchVo();
        }*/

        //표현할 필드 설정
        $fieldData = $this->getListField();
        SlCommonUtil::setColWidth(95, $fieldData);

        //페이징 필요 정보 설정
        $searchData = [
            'page' => gd_isset($params['page'],1),
            'pageNum' => gd_isset($params['pageNum'],50),
        ];
        
        //검색 조건 설정
        $searchVo = $this->setCondition($params,$searchVo);

        //정렬 조건 설정
        if(!empty($params['sort'])){
            $this->setOrder($params['sort'], $searchVo);
        }

        //검색 테이블들(JOIN) 설정
        $tableInfo = $this->getTableInfo($params);
        $allData = DBUtil2::getComplexListWithPaging($tableInfo, $searchVo, $searchData, false, false);

        //리스트 데코레이션 처리
        $list = SlCommonUtil::setEachData($allData['listData'], $this, 'decorationDefault', $tableInfo);

        //RowSpan 처리 : usage : SlCommonUtil::setListRowSpan($list, ['reqSno'  => ['valueKey' => 'sno'],], $params);
        if( !empty($params['rowSpanData']) ){
            SlCommonUtil::setListRowSpan($list, $params['rowSpanData'], $params);
        }

        $pageEx = $allData['pageData']->getPage('#');
        return [
            'pageEx' => $pageEx,
            'page' => $allData['pageData'],
            'list' => $list,
            'fieldData' => $fieldData
        ];
    }

    /**
     * 기본 데코레이션
     * @param $each
     * @param $eachKey
     * @param $tableInfoList
     * @return array|mixed
     * @throws \Exception
     */
    public function decorationDefault($each, $eachKey, $tableInfoList){
        foreach($tableInfoList as $tableInfo){
            $tableName = $tableInfo->getTableName();
            $each = SlCommonUtil::refineDbData($each, $tableName);
            //$each = DBTableField::parseJsonField($tableName, $each);
            //$each = DBTableField::fieldStrip($tableName, $each);
        }
        $each = $this->decoration($each);
        return $each;
    }


    /**
     * 검색 (정의하지 않으면 무시)
     * @param $condition
     * @param SearchVo $searchVo
     * @return SearchVo
     */
    public function setCondition($condition,SearchVo $searchVo){
        $this->setSearchKeywordCondition($condition, $searchVo);
        return $searchVo;
    }

    /**
     * 리스트 추가 정보 더하기 (정의하지 않으면 무시)
     * @param $each
     * @return mixed
     */
    public function decoration($each){
        return $each;
    }

    /**
     * 멀티 키워드 기본 검색
     * @param $condition
     * @param SearchVo $searchVo
     */
    public function setSearchKeywordCondition($condition, SearchVo $searchVo){
        //멀티 검색
        if(!empty($condition['multiKey'])){
            $whereConditionList = [];
            foreach( $condition['multiKey'] as $keyIndex => $keyCondition ){
                $key = "REPLACE(".$keyCondition['key'].",' ','')";
                $keyword = str_replace(' ','',$keyCondition['keyword']);
                if(!empty($keyword)){
                    if( 'OR' != $condition['multiCondition'] ){
                        $searchVo->setWhere(DBUtil2::bind($key, DBUtil2::BOTH_LIKE));
                        $searchVo->setWhereValue($keyword);
                    }else{
                        $whereConditionList[] = " ( {$key} like '%{$keyword}%' ) ";
                    }
                }
            }
            if( 'OR' == $condition['multiCondition'] ){
                if(count($whereConditionList)>0){
                    $searchVo->setWhere(implode(' OR ', $whereConditionList));
                }
            }
        }
    }

}