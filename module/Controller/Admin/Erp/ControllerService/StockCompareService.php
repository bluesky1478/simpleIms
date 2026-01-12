<?php
namespace Controller\Admin\Erp\ControllerService;

use App;
use Component\Erp\ErpCodeMap;
use Component\Member\Manager;
use Component\Scm\ScmConst;
use Component\Storage\Storage;
use Component\Work\WorkCodeMap;
use Framework\Debug\Exception\AlertBackException;
use Framework\Utility\DateTimeUtils;
use LogHandler;
use Request;
use Session;
use SiteLabUtil\SlCommonUtil;
use SlComponent\Database\DBConst;
use SlComponent\Database\DBUtil;
use SlComponent\Database\DBUtil2;
use SlComponent\Database\SearchVo;
use SlComponent\Godo\ListInterface;
use SlComponent\Util\ListUtil;
use SlComponent\Util\SitelabLogger;
use SlComponent\Util\SlCodeMap;
use SlComponent\Util\SlCommonTrait;
use SlComponent\Util\SlLoader;

/**
 * 재고표
 * Class GoodsStock
 * @package Component\Goods
 */
class StockCompareService implements ListInterface {

    use SlCommonTrait;

    private $sql;
    private $search;

    const LIST_TITLES = [
        1 => '구분',       //attr1
        5 => '입고년도',    //attr5
        2 => '시즌',       //attr2
        3 => '상품구분',    //attr3
        4 => '색상',       //attr4
    ];

    public function getSummaryField($scmNo){
        $returnList = [];
        foreach(ScmConst::SCM_ITEM[$scmNo]['attr'] as $attrIndex){
            $returnList[$attrIndex] = self::LIST_TITLES[$attrIndex];
        }
        return $returnList;
    }

    public function __construct(){
        $this->sql =  SlLoader::sqlLoad(__CLASS__);
    }
    public function getSearch()
    {
        return $this->search;
    }
    public function setSearch($search)
    {
        $this->search = $search;
    }

    public function getDefaultOptionList($scmNo){
        $topBegin = ScmConst::SCM_ITEM[$scmNo]['option']['topBegin'];
        $bottomBegin = ScmConst::SCM_ITEM[$scmNo]['option']['bottomBegin'];
        $topAcc = ScmConst::SCM_ITEM[$scmNo]['option']['topAcc'];
        $bottomAcc = ScmConst::SCM_ITEM[$scmNo]['option']['bottomAcc'];
        $accCount = ScmConst::SCM_ITEM[$scmNo]['option']['accCount'];
        for($i=0; $accCount>$i; $i++){
            $top[] = $topBegin + ($topAcc*$i);
            $bottom[] = $bottomBegin + ($bottomAcc*$i);
        }
        return [
            'top' => $top,
            'bottom' => $bottom,
        ];
    }


    public function getTitle($searchData): array
    {
        return self::LIST_TITLES;
    }

    /**
     * 리스트 타이틀 목록 반환
     * @param $searchData
     * @return string[]
     */
    public function getSummaryTitle($searchData): array
    {
        $findAttrList = $this->getSummaryAttr($searchData);
        $titles = [];
        if(!empty($findAttrList)){
            $titles =  SlCommonUtil::arrayAppKeyValue($findAttrList, 'index', 'name');
        }
        return $titles;
    }
    public function getOptionTitle($searchData): array
    {
        $optionList = $this->getDefaultOptionList($searchData['scmNo']);
        $titles = [];
        foreach($optionList['top'] as $key =>$topValue){
            $titles[] = $topValue.'<br>('.$optionList['bottom'][$key].')';
        }
        return $titles;
    }

    /**
     * 검색 데이터 설정
     * @param $searchData
     */
    public function _setSearch($searchData){
        SlCommonUtil::refineTrimData($searchData);
        $setParam = [
            'combineSearch' => [
                'a.productName' => '품목명',
                'a.thirdPartyProductCode' => '품목코드',
            ],
        ];

        ListUtil::setSearch($this, $setParam);

        // 기본 텍스트 검색 설정
        $this->setSearchData([
            'key'
            ,'keyword'
            ,'scmNo'
            ,'attr'
        ],$searchData);

        $this->setCheckSearch([
            'attr',
        ],$searchData,'all');

        $this->setRadioSearch([],$searchData,'');
        $this->setCheckSearch([]);
        
        //TODO : 공급사! 관리자 화면 설정
        //gd_debug(\Session::);

        //기본값 설정.
        if(empty($this->search['scmNo'])){
            $this->search['scmNo'] = 6; //Default 한국타이어.
        }
    }

    public function getSummaryTotalList(){
        $requestParam = \Request::request()->toArray();
        $refineRequestParam = SlCommonUtil::getRefineValueAndExcelDownCheck($requestParam);
        $data = $this->getTraitList($refineRequestParam, 'getList');

        $getData = [];
        $getData['total'] = $this->sql->getTotalCount($data['search']);
        $getData['notSale'] = $this->sql->getTotalNotSaleCount($data['search']);
        $getData['mallCount'] = $this->sql->getTotalMallCount($data['search']);
        $getData['waitCount'] = $this->sql->getTotalWaitCount($data['search']);

        $data['data']= $getData;
        //gd_debug($data);

        return $data;
    }

    /**
     * 재고 현황
     * @param mixed  $searchData   검색 데이타
     * @return array 주문 리스트 정보
     */
    public function getList($searchData): array {
        return [];
    }

    /**
     * 리스트 데이터 Refine.
     * @param $each
     * @param $key
     * @param $mixData
     * @return mixed
     */
    public function setRefineEachListData($each, $key, &$mixData){
        return $each;
    }

}
