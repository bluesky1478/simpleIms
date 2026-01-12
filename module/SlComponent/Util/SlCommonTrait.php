<?php
namespace SlComponent\Util;

use SiteLabUtil\SlCommonUtil;

Trait SlCommonTrait {

    private $checkStr = 'checked="checked"';

    /**
     * @param $whereCondition
     * @param $value
     * @param $object
     */
    public function addWhere($whereCondition,$value,$object){
        $object->arrWhere[] = $whereCondition;
        $object->db->bind_param_push($object->arrBind, 'i', $value);
    }

    /**
     * 텍스트 검색 설정
     * @param array $fieldList
     * @param $searchData
     */
    public function setSearchData(array $fieldList, $searchData){
        foreach($fieldList as $k => $fieldName){
            $this->search[$fieldName] = gd_isset($searchData[$fieldName]);
        }
    }

    /**
     * 라디오 체크 검색 설정
     * @param array $fieldList
     * @param $searchData
     * @param $defaultField
     */
    public function setRadioSearch(array $fieldList, $searchData, $defaultField){
        foreach($fieldList as $k => $fieldName){
            $this->search[$fieldName] = gd_isset($searchData[$fieldName], $defaultField); //this설정
            $this->checked[$fieldName][$this->search[$fieldName]] = $this->checkStr;
        }
    }

    /**
     * 기간 설정
     * @param $dateFieldName
     * @param $startDate
     * @param $endDate
     */
    public function setRangeDate( $dateFieldName, $startDate, $endDate ){
        if ( !empty($dateFieldName) && !empty($startDate) && !empty($endDate) ) {
            //기간
            $this->search['treatDateFl'] = $dateFieldName;
            $this->search['treatDate'][0] = $startDate. ' 00:00:00';
            $this->search['treatDate'][1] = $endDate. ' 23:59:59';
        }
    }

    /**
     * 체크박스 검색 설정
     * @param array $fieldList
     */
    public function setCheckSearch(array $fieldList){
        foreach($fieldList as $fieldListKey => $fieldName){
            if(empty($this->search[$fieldName])){
                $this->checked[$fieldName]['all'] = $this->checkStr;
            }else{
                foreach($this->search[$fieldName] as $k => $v){
                    $this->checked[$fieldName][$v] = $this->checkStr;
                }
            }
        }
    }


    /**
     * 리스트를 가져오기 위한 공통 로직
     * @param $searchData
     * @param $fnc
     * @return mixed
     */
    public function getTraitList($searchData,$fnc){

        // --- 검색 설정 (WHERE 을 여기서 설정...)
        SlCommonUtil::refineTrimData($searchData);
        $this->_setSearch($searchData);

        //검색 값 설정
        if (empty($this->search) === false) {
            $getData['search'] = $this->search;
        }
        // 라디오 체크값 설정
        if (empty($this->checked) === false) {
            $getData['checked'] = $this->checked;
        }

        $stockList = $this->sql->$fnc($this->search);

        $getData['page'] = $stockList['pageData'];
        $getData['data'] = $stockList['listData'];

        return $getData;
    }

    /**
     * 기본 날짜 설정
     * @param $searchData
     * @param $period
     * @return array
     */
    public function setDefaultSearchDate($searchData , $period) {
        if( empty($searchData['treatDateFl']) ){
            $searchData['treatDateFl'] = 'a.regDt';
        }
        $searchData['treatDate'][0] = gd_isset($searchData['treatDate'][0],date('Y-m-d', strtotime('-' . $period . ' day')));
        $searchData['treatDate'][1] = gd_isset($searchData['treatDate'][1],date('Y-m-d'));
        return $searchData;
    }


}
