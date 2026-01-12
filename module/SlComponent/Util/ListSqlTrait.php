<?php
namespace SlComponent\Util;

use SlComponent\Database\DBUtil;
use SlComponent\Database\SearchVo;

Trait ListSqlTrait {

    public function createDefaultSearchVo($searchData){
        $searchVo = new SearchVo();
        if( !empty($searchData['treatDateFl']) && !empty($searchData['treatDate'][0]) && !empty($searchData['treatDate'][1]) ){
            if( 10 >= strlen($searchData['treatDate'][0]) ){
                $searchData['treatDate'][0] .= ' 00:00:00';
                $searchData['treatDate'][1] .= ' 23:59:59';
            }
            $searchVo->setWhere( DBUtil::bind( $searchData['treatDateFl'], DBUtil::GTS_EQ ) );
            $searchVo->setWhereValue( $searchData['treatDate'][0] );
            $searchVo->setWhere( DBUtil::bind( $searchData['treatDateFl'], DBUtil::LTS_EQ ) );
            $searchVo->setWhereValue( $searchData['treatDate'][1] );
        }
        //2. 검색어
        if( !empty($searchData['keyword']) ){
            if( is_array($searchData['keyword']) ){
                foreach( $searchData['keyword'] as $keyIndex => $keyCondition ){
                    if( !empty($keyCondition) ){
                        $searchVo->setWhere( DBUtil::bind( $searchData['key'][$keyIndex], DBUtil::BOTH_LIKE ) );
                        $searchVo->setWhereValue( $keyCondition );
                    }
                }
            }else{
                $searchVo->setWhere( DBUtil::bind( $searchData['key'], DBUtil::BOTH_LIKE ) );
                $searchVo->setWhereValue( $searchData['keyword'] );
            }
        }
        return $searchVo;
    }

    /**
     * Radio 검색 설정
     * @param $searchData
     * @param $searchVo
     * @param $param
     */
    public function setRadioSearchVo($searchData, &$searchVo, $param ){
        if( isset($searchData[$param['field']]) && 'all' !== $searchData[$param['field']]  ){
            $searchVo->setWhere($param['where']);
            $searchVo->setWhereValue($searchData[$param['field']]);
        }
    }

}
