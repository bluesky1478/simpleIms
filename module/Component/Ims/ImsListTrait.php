<?php
namespace Component\Ims;

use App;
use Component\Database\DBTableField;
use Component\Imsv2\ImsFieldUtil;
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
trait ImsListTrait {

    use ImsServiceConditionTrait;
    use ImsServiceSortTrait;
    use ImsServiceSortNkTrait;

    public function setField($filedType){

    }

    /**
     * @param $params "검색 파라미터"
     * 'condition' => 검색 파라미터
     * 'condition'->page => 페이지번호
     * 'condition'->pageNum => 한페이지 표현 수
     * 'condition'->sort => 검색
     *
     *  instance   => 호출 인스턴스 변수
     *  sqlInstance => 테이블 가져오기 함수가 있는 인스턴스 변수
     *  getTableFncName => 테이블 가져오기 함수 
     *
     * 'rowSpanData' => rowspan 필요시 사용
     * 'decorationName' => 데이터 장식 함수
     *
     * @param SearchVo $searchVo
     * @return array
     */
    public function getListCommonProc($params, SearchVo $searchVo){

        if(null === $searchVo){
            $searchVo = new SearchVo();
        }
        $searchVo = $this->setCommonCondition($params['condition'], $searchVo);

        $searchData = [
            'page' => gd_isset($params['condition']['page'],1),
            'pageNum' => gd_isset($params['condition']['pageNum'],100),
        ];

        if(!empty($params['condition']['sort'])){
            $this->setListSort($params['condition']['sort'], $searchVo);
        }
        if(!empty($params['condition']['sort_nk'])){
            $this->setListSortNk($params['condition']['sort_nk'], $searchVo);
        }

        /*gd_debug($params['sqlInstance']);
        $methodMap = new \ReflectionClass('\Component\Ims\ImsStyleServiceSql');
        $methods = $methodMap->getMethods();
        gd_debug($methods);*/
        $fncName = $params['getTableFncName'];
        $allData = DBUtil2::getComplexListWithPaging($params['sqlInstance']->$fncName($params), $searchVo, $searchData, false, false);

        if( !empty($params['decorationName']) ){
            $list = SlCommonUtil::setEachData($allData['listData'], $params['instance'], $params['decorationName']);
        }else{
            $list = $allData['listData'];
        }

        //Rowspan 설정(필요시) : SlCommonUtil::setListRowSpan($list, ['reqSno'  => ['valueKey' => 'sno'],], $params);
        if( !empty($params['rowSpanData']) ){
            SlCommonUtil::setListRowSpan($list, $params['rowSpanData'], $params);
        }

        $pageEx = $allData['pageData']->getPage('#');

        $fieldData = [];
        if(!empty($params['condition']['imsFieldType'])){
            $fncName = 'get'.ucfirst('popupStyleField');
            $fieldData = ImsFieldUtil::$fncName();
        }

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
     * @param $dbName
     * @return array|mixed
     * @throws \Exception
     */
    public function decorationDefault($each, $dbName){
        $each = DBTableField::parseJsonField($dbName, $each);
        $each = DBTableField::fieldStrip($dbName, $each);
        return $each;
    }

}