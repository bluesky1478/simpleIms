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
class StockTableService implements ListInterface {

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
            ,'isViewMode'
            ,'inOutReason'
            ,'attr1'
            ,'attr2'
            ,'attr3'
            ,'attr4'
            ,'attr5'
        ],$searchData);

        $this->setCheckSearch([
            'attr',
            'inOutReason',
        ]);
        $this->setRadioSearch([
            'isViewMode',
        ],$searchData,'all');

        $this->setRadioSearch([],$searchData,'');
        $this->setCheckSearch([]);

        // 기간 설정
        $this->search['treatDate'][] = gd_isset($searchData['treatDate'][0], date('Y-m-d', strtotime('-1 year')));
        $this->search['treatDate'][] = gd_isset($searchData['treatDate'][1], date('Y-m-d'));

        //기본값 설정.
        if(empty($this->search['scmNo'])){
            if( Manager::isProvider() ){
                $this->search['scmNo'] = \Session::get('manager.scmNo');
            }else{
                $this->search['scmNo'] = 6; //Default 한국타이어.
            }
        }
    }

    /**
     * 통계 항목
     * @param $searchData
     * @return array|mixed
     */
    public function getSummaryAttr($searchData){
        $scmNo = $searchData['scmNo'];
        $findAttrListDefault = ScmConst::SCM_ITEM[$scmNo]['attr'];
        $findAttrList = [];
        if( !empty($searchData['attr']) and $searchData['attr'][0] != 'all' ){
            foreach($findAttrListDefault as $findAttrListEach){
                if( in_array($findAttrListEach, $searchData['attr']) ){
                    $findAttrList[] = [
                        'index' => $findAttrListEach,
                        'name' => self::LIST_TITLES[$findAttrListEach],
                    ];
                }
            }
        }else{
            foreach($findAttrListDefault as $findAttrListEach){
                $findAttrList[] = [
                    'index' => $findAttrListEach,
                    'name' => self::LIST_TITLES[$findAttrListEach],
                ];
            }
        }
        return $findAttrList;
    }

    public function getRefineOptionName($dataValue, $defaultOption){
        $optionName = $dataValue['optionName'];

        //TODO : SCM Option Name에 따라 추가
        $explodeOption = explode('(', $optionName); //TKE
        if( count($explodeOption) > 1 ){
            $optionName = preg_replace("/[^0-9]*/s", "", $explodeOption[1]);
        }
        $bottomMap = array_flip($defaultOption['bottom']);
        if( isset($bottomMap[$optionName]) ){
            $optionName = $defaultOption['top'][$bottomMap[$optionName]];
        }
        return $optionName;
    }

    /**
     * 재고 현황
     * @param mixed  $searchData   검색 데이타
     * @return array 주문 리스트 정보
     */
    public function getList($searchData): array {

        $data = $this->getTraitList($searchData, 'getList');

        $scmNo = $data['search']['scmNo'];
        $refineData = [];
        $totalStockCnt = 0;
        $keyStockCnt = [];

        $defaultOption = $this->getDefaultOptionList($scmNo);

        $codeIndex = 0;
        foreach($data['data'] as $dataValue){
            //gd_debug($dataValue);
            $findAttrList = $this->getSummaryAttr($data['search']);
            $totalStockCnt += $dataValue['stockCnt'];
            $key = '';
            $optionCategoryList = [];
            foreach( $findAttrList as $findAttrData ){
                $fieldName = 'attr'.$findAttrData['index'];
                $sortItem = array_flip(ScmConst::SCM_ITEM[$scmNo]['sort'][$findAttrData['index']]);
                if( isset($sortItem[$dataValue[$fieldName]]) ){
                    $key .= ($sortItem[$dataValue[$fieldName]]+1);
                }else{
                    if( empty($dataValue[$fieldName]) ){
                        $key .= '0';
                    }else{
                        $key .= $dataValue[$fieldName];
                    }
                }
                $optionCategoryList[] = $fieldName;
            }

            $refineData[$key]['info'] = SlCommonUtil::getAvailData($dataValue,$optionCategoryList) ;

            //옵션명 정제
            $optionName = $this->getRefineOptionName($dataValue, $defaultOption);
            
            $refineData[$key]['stockCnt'][$optionName] += $dataValue['stockCnt'];
            $keyStockCnt[$key] += $dataValue['stockCnt'];

            $codeIndex++;
            $refineData[$key]['codeList'][$dataValue['thirdPartyProductCode']]=$codeIndex;
            //이게 몇월에 입출고?
            //gd_debug($dataValue['thirdPartyProductCode']);
            //MSTSTC14
            //MSHKSC41
            //$monthStock[$key]
        }
        ksort($refineData);

        $data['data'] = $refineData;
        $data['totalData']['stockCnt'] = $totalStockCnt;
        $data['totalData']['keyStockCnt'] = $keyStockCnt;

        $isOutHistoryRawFl = false;

        $getToArray = \Request::get()->toArray();
        if(!empty($getToArray['detailKey'])){
            $detailKey = explode(',', $getToArray['detailKey']);
            //SitelabLogger::logger($detailKey);
            $isOutHistoryRawFl = true;
        }

        //월별 출고데이터
        foreach( $refineData as $refineKey => $refineEach ){
            $codeList = array_flip($refineEach['codeList']);
            $codeListStr = "'".implode("','",$codeList)."'";

            //gd_debug($sql);
            if( 'all' !== $searchData['isViewMode']){

                /*$sql = "select inOutType, left(inOutDate,7) as outMonth, optionName, sum(quantity) as qty from sl_3plStockInOut a join sl_3plProduct b on a.thirdPartyProductCode = b.thirdPartyProductCode ";
                $sql .= " where a.thirdPartyProductCode in ({$codeListStr}) group by inOutType, left(inOutDate,7), optionName ";*/

                //출고 Query
                if( $isOutHistoryRawFl ){
                    $sql = "select a.inOutType, left(inOutDate,4) as outYear, left(a.inOutDate,7) as outMonth, b.optionName, a.quantity, a.inOutDate, a.orderNo, a.customerName, a.address, a.invoiceNo, a.thirdPartyProductCode, b.productName ";
                }else{
                    $sql = "select inOutType, left(inOutDate,4) as outYear, left(inOutDate,7) as outMonth, optionName, sum(quantity) as qty ";
                }
                $sql .= "from sl_3plStockInOut a join sl_3plProduct b on a.thirdPartyProductCode = b.thirdPartyProductCode ";
                $sql .= " where a.thirdPartyProductCode in ({$codeListStr}) and inOutType = 2 ";
                $sql .= " and a.inOutDate >= '{$searchData['treatDate'][0]}' and a.inOutDate <= '{$searchData['treatDate'][1]}' ";

                if( !empty($searchData['inOutReason']) && 'all' !== $searchData['inOutReason'][0] ){
                    $inOutReasonImplode = implode(',',$searchData['inOutReason']);
                    $sql .= " and a.inOutReason in ( {$inOutReasonImplode} )";
                }

                if( !$isOutHistoryRawFl ){
                    $sql .= " group by inOutType, left(inOutDate,7), optionName  "; //inOutReason
                }else{
                    $sql .= " order by inOutDate, address, customerName, thirdPartyProductCode  ";
                }

                //gd_debug($sql);
                $outSummationHistory = DBUtil2::runSelect($sql);

                $refineInHistory = [];
                $refineOutTotalHistory = [];
                $refineOutHistory = [];

                if( $isOutHistoryRawFl ){
                    //Raw Data
                    foreach($outSummationHistory as $outEach){
                        //gd_debug($refineKey);
                        $optionName = $this->getRefineOptionName($outEach, $defaultOption);

                        if( 'totalData' == $detailKey[0] ){
                            if($detailKey[1] == $refineKey){
                                if( 'total' == $detailKey[2] ){
                                    $data['totalData']['outHistory'][] = $outEach;
                                }else{
                                    if($detailKey[2] == $optionName){
                                        $data['totalData']['outHistory'][] = $outEach;
                                    }
                                }
                            }
                        }else{
                            if( $outEach['outMonth'] == $detailKey[0] || $outEach['outYear'] == $detailKey[0] ) {
                                if ($detailKey[1] == $refineKey ) {
                                    if ('total' == $detailKey[2]) {
                                        $data['totalData']['outHistory'][] = $outEach;
                                    } else {
                                        if ($detailKey[2] == $optionName) {
                                            $data['totalData']['outHistory'][] = $outEach;
                                        }
                                    }
                                }
                            }
                        }

                    }

                }else{
                    foreach($outSummationHistory as $outEach){

                        $optionName = $this->getRefineOptionName($outEach, $defaultOption);

                        if( '1' == $outEach['inOutType'] ){
                            //입고
                            $refineInHistory[$optionName] += $outEach['qty'];
                            $refineInHistory['total'] += $outEach['qty'];
                        }else{
                            //출고
                            $refineOutTotalHistory[$optionName] += $outEach['qty'];
                            $refineOutTotalHistory['total'] += $outEach['qty'];

                            if( '2' == $searchData['isViewMode']){
                                //월별
                                $refineOutHistory[$outEach['outMonth']][$optionName] += $outEach['qty'];
                                $refineOutHistory[$outEach['outMonth']]['total'] += $outEach['qty'];
                            }else{
                                //년별
                                $refineOutHistory[$outEach['outYear']][$optionName] += $outEach['qty'];
                                $refineOutHistory[$outEach['outYear']]['total'] += $outEach['qty'];
                            }


                        }
                    }
                }

            }

            //gd_debug($refineOutHistory);
            $data['totalData']['outCnt'][$refineKey] = $refineOutHistory;
            $data['totalData']['inCnt'][$refineKey] = $refineInHistory;
            $data['totalData']['outTotalCnt'][$refineKey] = $refineOutTotalHistory;

        }
        //gd_debug($data['totalData']['outCnt']);
        //gd_debug($data['totalData']['inCnt']);

        //$data['totalData']['keyStockCnt'] = $keyStockCnt;

        return $data;
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
