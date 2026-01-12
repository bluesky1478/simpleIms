<?php

namespace Component\Imsv2;

use Component\Database\DBTableField;
use Component\Erp\ErpService;
use Component\Ims\ImsDBName;
use Component\Ims\ImsService;
use Component\Ims\ImsServiceConditionTrait;
use Component\Ims\ImsServiceTrait;
use Component\Ims\ImsServiceSortNkTrait;
use Component\Ims\ImsServiceSampleTrait;
use Component\Ims\ImsCodeMap;
use Component\Ims\NkCodeMap;
use Controller\Admin\Ims\ImsPsNkTrait;
use SiteLabUtil\SlCommonUtil;
use SlComponent\Database\DBUtil;
use SlComponent\Database\DBUtil2;
use SlComponent\Database\SearchVo;
use SlComponent\Database\TableVo;
use SlComponent\Util\SitelabLogger;
use SlComponent\Util\SlLoader;

/**
 * IMS (+폐쇄몰) 통틀어서 재고/입출고 관리.
 *
 * Class ImsStockService
 * @package Component\Imsv2
 */
class ImsStockService
{
    const EXCLUDE_GOODS = [
        1000000255, //영구크린공기정화
    ];

    /**
     * 검색 처리
     * @param $condition
     * @param SearchVo $searchVo
     * @return mixed
     */
    public function setCondition($condition, SearchVo $searchVo){
        $searchVo->setWhere("goods.delFl='n'");
        $searchVo->setWhere("goods.stockFl='y'"); //재고 관리 대상만 처리 FIXME : 검색 툴 만들기

        if( 'all' !== $condition['scmSno'] && !empty($condition['scmSno']) ){
            $searchVo->setWhere('goods.scmNo=?');
            $searchVo->setWhereValue($condition['scmSno']);
        }
        if( 'all' !== $condition['goodsNo'] && !empty($condition['goodsNo']) ){
            $searchVo->setWhere('goods.goodsNo=?');
            $searchVo->setWhereValue($condition['goodsNo']);
        }

        //멀티 검색
        if( !empty($condition['multiKey']) ){
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
                    $searchVo->setWhere('('.implode(' OR ', $whereConditionList).')');
                }
            }
        }

        //카테고리 검색
        $lastSelectCate = '';
        foreach($condition['scmCate'] as $scmCate){
            if(!empty($scmCate)){
                $lastSelectCate = $scmCate;
            }
        }
        if(!empty($lastSelectCate)){
            //카테고리에 포함된 상품만 처리
            $searchVo->setWhere("goods.goodsNo in ( select distinct goodsNo from es_goodsLinkCategory where cateCd like '{$lastSelectCate}%' )");
        }

        return $searchVo;
    }

    /**
     * 정렬 처리
     * @param $sort
     * @param $searchVo
     */
    public function setSort($sort, &$searchVo){
        //정렬
        //$searchVo->setOrder(empty($sort)?'goods.regDt desc':$sort);
        $searchVo->setOrder('goods.regDt desc, gop.optionNo asc');
    }
    /**
     * 상품 재고 리스트 반환
     * @param $params
     * @return mixed
     */
    public function getScmStockList($params) {
        $searchData = [
            'page' => gd_isset($params['page'], 1),
            'pageNum' => gd_isset($params['pageNum'], 200),
        ];
        $searchData['condition'] = $params;

        $searchVo = new SearchVo('goods.delFl=?','n');

        $searchVo->setWhere('goods.goodsSellFl=?');
        $searchVo->setWhereValue('y');
        //$searchVo->setWhere('goods.goodsDisplayFl=?');
        //$searchVo->setWhereValue('y');
        /*$searchVo->setWhere('goods.goodsSellFl=?');
        $searchVo->setWhereValue('y');*/
        $searchVo->setWhere('goods.goodsNo not in (1000000586)');
        $searchVo->setWhere("gop.optionViewFl='y'");

        $this->setSort($searchData['condition']['sort'], $searchVo);
        $this->setCondition($searchData['condition'],$searchVo);

        $tableInfo = [
            'gop'   => ['data' => [ DB_GOODS_OPTION ], 'field' => ['gop.*']],
            'goods' => ['data' => [ DB_GOODS, 'JOIN', 'gop.goodsNo = goods.goodsNo' ], 'field' => ['goods.goodsNm','goods.scmNo']],
            'gext'  => ['data' => [
                'sl_goodsOptionExt', 'LEFT OUTER JOIN', 'gext.goodsNo = goods.goodsNo and gop.sno = gext.optionSno' ]
                , 'field' => ['gext.reserveCnt','gext.realCnt','gext.inCnt','gext.outCnt','gext.otherMappingCnt','gext.realCntOfYear']
            ],
        ];
        $tableInfo = DBUtil2::setTableInfo($tableInfo, false);

        $allData = DBUtil2::getComplexListWithPaging($tableInfo, $searchVo, $searchData, false, false);
        $allData['listData'] = SlCommonUtil::setEachData($allData['listData'], $this, 'decorationList', $isGroup);
        //SitelabLogger::logger2(__METHOD__, $allData['listData']);

        return $allData;
    }

    /**
     * 리스트 꾸미기
     * @param $each
     * @param $key
     * @param $addParams
     * @return mixed
     */
    public function decorationList($each, $key, $addParams){
        return $each;
    }

    /**
     * 재고 상세 정보 가져오기
     * @param $params
     * @return array
     */
    public function getGoodsStockTotalInfoDetail($params){
        $detailData = $this->getScmStockList($params)['listData'];
        foreach($detailData as $key => $each){
            $searchVo=new SearchVo('a.optionSno=?', $each['sno']);
            //$searchVo->setWhere("a.optionViewFl='y'");
            $searchVo->setOrder('a.sort');
            $linkData=DBUtil2::getJoinList('sl_goodsOptionLink', [
                'b' => [ 'sl_3plProduct', 'a.code=b.thirdPartyProductCode'
                , 'b.scmName, b.productName, b.optionName, b.stockCnt, a.otherCnt, b.attr1, b.attr2, b.attr3, b.attr4, b.attr5,b.inCnt,b.outCnt',]
            ],$searchVo);
            $detailData[$key]['3pl']=$linkData;
        }
        return $detailData;
    }

    /**
     * 재고 예약 정보 가져오기
     * @param $params
     * @return array
     */
    public function getReservedList($params){
        $searchVo=new SearchVo('a.goodsNo=?', $params['goodsNo']);
        $searchVo->setOrder('a.orderNo');
        $searchVo->setWhere("a.orderStatus in ( 'o1', 'p1', 'p2', 'p3', 'g1', 'g3' )");
        $list = DBUtil2::getJoinList(DB_ORDER_GOODS, [
            'b' => [ DB_ORDER, 'a.orderNo=b.orderNo'
                , 'b.memNo'
            ],
            'c' => [ DB_GOODS_OPTION, 'a.goodsNo=c.goodsNo and a.optionSno = c.sno'
                , 'c.optionValue1', 'c.optionValue2', 'c.optionValue3', 'c.optionValue4', 'c.optionValue5'
            ],
            'd' => [ DB_MEMBER, 'b.memNo=d.memNo'
                , 'd.memNm', 'd.memId'
            ],
            'e' => [ DB_ORDER_INFO, 'a.orderNo=e.orderNo'
                , 'e.receiverName'
            ],
        ],$searchVo);
        foreach($list as $key => $each){
            $each['orderStatusKr'] = SlCommonUtil::getOrderStatusName2($each['orderStatus']);
            $list[$key] = $each;
        }
        return $list;
    }


    /**
     * 상품 재고 종합 정보
     * @param $params
     * @return array
     */
    public function getGoodsStockTotalInfo($params){
        $params['page']=1;
        $params['pageNum']=10000;
        $list = $this->getScmStockList($params);
        $goodsInfoMap = [];
        $maxOptionCnt = 0;

        foreach($list['listData'] as $each){

            $optionValueList = [];
            for($i=1;5>=$i;$i++){
                if(!empty($each['optionValue'.$i])){
                    $optionValueList[] = $each['optionValue'.$i];
                }
            }
            $optionName=implode(' ',$optionValueList);

            $key = str_replace(' ','',$each['goodsNm']).'||'.$each['goodsNo'];

            if(empty($goodsInfoMap[$key])){
                $goodsInfoMap[$key] = [
                    'goodsNo' => $each['goodsNo'],
                    'goodsNm' => $each['goodsNm'],
                    'scmNo' => $each['scmNo'],
                    'regDt' => $each['regDt'],
                ];
            }
            $goodsInfoMap[$key]['optionCnt']++;
            $goodsInfoMap[$key]['totalStock'] = $goodsInfoMap[$key]['totalStock'] += $each['stockCnt'];
            $goodsInfoMap[$key]['realStock'] = $goodsInfoMap[$key]['realStock'] += $each['realCnt'];
            $goodsInfoMap[$key]['reserveStock'] = $goodsInfoMap[$key]['reserveStock'] += $each['reserveCnt'];
            $goodsInfoMap[$key]['inCnt'] += $each['inCnt'];
            $goodsInfoMap[$key]['outCnt'] += $each['outCnt'];
            $maxOptionCnt = $goodsInfoMap[$key]['optionCnt'] > $maxOptionCnt ? $goodsInfoMap[$key]['optionCnt']:$maxOptionCnt;
            $outRate = 0;
            $burnRate = 0;

            $goodsInfoMap[$key]['option']['o'.$each['sno']] = [
                'optionName' => $optionName,
                'stockCnt' => $each['stockCnt'],     //판매수량
                'reserveCnt' => $each['reserveCnt'], //출고 예약수량
                'realCnt' => $each['realCnt'],       //창고 수량 -$each['otherMappingCnt']
                'realCntOfYear' => $each['realCntOfYear'],       //창고 수량 -$each['otherMappingCnt']
                'otherMappingCnt' => $each['otherMappingCnt'],       //창고 수량
                'inCnt' => $each['inCnt'], //입고 수량
                'outCnt' => $each['outCnt'], //출고 수량
                'outRate' => $outRate, //출고율
                'burnRate' => $burnRate, //소진율
            ];
        }

        //정렬
        krsort($goodsInfoMap);

        foreach($goodsInfoMap as $key => $value){
            foreach($value['option'] as $subKey => $each){
                if( $goodsInfoMap[$key]['outCnt'] > 0 && $goodsInfoMap[$key]['option'][$subKey]['outCnt'] > 0){
                    $goodsInfoMap[$key]['option'][$subKey]['outRate'] = round($goodsInfoMap[$key]['option'][$subKey]['outCnt']/$goodsInfoMap[$key]['outCnt']*100);
                }
                if( $goodsInfoMap[$key]['option'][$subKey]['outCnt'] && $goodsInfoMap[$key]['option'][$subKey]['inCnt'] > 0 ){
                    $goodsInfoMap[$key]['option'][$subKey]['burnRate'] = round($goodsInfoMap[$key]['option'][$subKey]['outCnt']/$goodsInfoMap[$key]['option'][$subKey]['inCnt']*100);
                }
            }
            if($goodsInfoMap[$key]['inCnt'] > 0){
                $goodsInfoMap[$key]['totalBurnRatio'] = round($goodsInfoMap[$key]['outCnt']/$goodsInfoMap[$key]['inCnt']*100,0); //총 출고율
            }else{
                $goodsInfoMap[$key]['totalBurnRatio'] = 0; //총 출고율
            }
        }

        return [
            'goodsList' => $goodsInfoMap,
            'maxOptionCnt' => $maxOptionCnt,
            'goodsCnt' => count($goodsInfoMap),
        ];
    }

    /**
     * 미연결 코드 리스트 반환
     * @param $params
     * @return string
     * @throws \Exception
     */
    public function getGoodsStockUnlink($params){
        if(empty($params['scmSno'])) throw new \Exception('고객사 번호가 없습니다.');

        $attrCondition = [];
        foreach($params['attr'] as $attr){
            $attrConditionEach = [];
            foreach($attr as $key => $attrValue){
                if(!empty($attrValue)){
                    $attrConditionEach[]="attr{$key}='{$attrValue}'";
                }
            }
            if(count($attrConditionEach) > 0){
                $attrCondition[] = '(' . implode(' AND ',$attrConditionEach) . ')';
            }
        }

        $attrConditionSql = '';
        if(count($attrCondition) > 0){
            $attrConditionSql = ' AND ('.implode(' OR ', $attrCondition).')';
        }

        $keywordCondition = [];
        foreach($params['multiKey'] as $multiKey){
            if(!empty($multiKey['keyword'])){
                $keywordCondition[]="{$multiKey['key']} LIKE '%{$multiKey['keyword']}%'";
            }
        }
        $keywordConditionSql='';
        if(count($keywordCondition) > 0){
            $keywordConditionSql = ' AND (' . implode(' '.$params['multiCondition'].' ',$keywordCondition) . ')';
        }

        //한국타이어는 21년 상품 제외.
        if( 6 == $params['scmSno'] ){
            $attrConditionSql .= " and attr5 <> 21 and attr1 not in ('TTS','TBX')";
        }

        $sql = "select * from sl_3plProduct 
                where thirdPartyProductCode not in  (
	                select distinct code from sl_goodsOptionLink a join es_goods b on a.goodsNo = b.goodsNo where  b.scmNo = {$params['scmSno']} and b.delFl = 'n'
                ) and scmNo = {$params['scmSno']} and stockCnt > 0 {$attrConditionSql} {$keywordConditionSql}
                order by productName, CONVERT(optionName, UNSIGNED INTEGER)";

        return DBUtil2::runSelect($sql);
    }

    /**
     * 출고 예약 수량
     * @param $optionSno
     * @return int
     */
    public function getReserveCnt($optionSno){
        //출고 예약 조건 6개월 (o 입금대기 / p 결재완료(출고대기도) / g1 g3 상품준비중)
        //창고 수량은 매일 8시, 20시 2회 갱신한다.
        //조회조건 : 최근 6개월/옵션번호 의 goodsCnt es_orderGoods
        $sql = "
select sum(goodsCnt) as cnt from es_orderGoods 
where optionSno='{$optionSno}' and ( orderStatus='o1' or orderStatus='p1' or orderStatus='p2' or orderStatus='p3' or orderStatus='g1' or orderStatus='g3' )";
        $cntInfo = DBUtil2::runSql($sql);
        return $cntInfo[0]['cnt'];
    }


    /**
     * 상품 3PL 코드 연결 해제
     * @param $params
     * @return bool
     * @throws \Exception
     */
    public function goods3plUnlink($params){
        if(empty($params['linkSno'])){
            throw new \Exception('연결 번호가 없습니다.');
        }else{
            DBUtil2::delete('sl_goodsOptionLink', new SearchVo('sno=?',$params['linkSno']));
        }
        return true;
    }

    /**
     * 상품 3PL 코드 연결
     * @param $params
     * @return bool
     * @throws \Exception
     */
    public function saveGoods3plProduct($params){
        //데이터 저장
        //SitelabLogger::logger2(__METHOD__, $params);

        //삭제
        foreach($params['delCode'] as $delCodeSno){
            DBUtil2::delete('sl_goodsOptionLink', new SearchVo('sno=?', $delCodeSno));
        }
        //추가 코드 저장
        $goodsNo = 0;
        $totalStock = 0;
        foreach($params['data'] as $data){
            $goodsNo = $data['goodsNo'];
            $totalStock += $data['stockCnt'];
            foreach($data['3pl'] as $idx => $tp){

                if(empty($tp['sno']) && !empty($tp['code']) ){
                    $saveData = [
                        'goodsNo' => $data['goodsNo'],
                        'optionSno' => $data['sno'],
                        'code' => trim($tp['code']),
                        //'sort' => $idx+1,
                        'sort' => $tp['sort'],
                        'otherCnt' => $tp['otherCnt'],
                    ];
                    //추가
                    DBUtil2::insert('sl_goodsOptionLink', $saveData);
                }else if( !empty($tp['sno']) ){
                    DBUtil2::update('sl_goodsOptionLink', [
                        'sort' => $tp['sort'],
                        'otherCnt' => $tp['otherCnt'],
                        'code' => $tp['code'],
                    ], new SearchVo('sno=?',$tp['sno']));
                }
            }
            //판매수량이 변경되면 그것 도 수정
            //SitelabLogger::logger2(__METHOD__, $data);
            $searchVo = new SearchVo('sno=?',$data['sno']);
            $searchVo->setWhere('stockCnt <> '.$data['stockCnt']);
            DBUtil2::update(DB_GOODS_OPTION,['stockCnt'=>$data['stockCnt']],$searchVo);
        }

        //수정 후 판매 수량 재 정리
        $searchVo = new SearchVo('goodsNo=?',$goodsNo);
        $searchVo->setWhere('totalStock <> '.$totalStock);
        DBUtil2::update(DB_GOODS,['totalStock'=>$totalStock],$searchVo);

        //수량 갱신
        $batchService = SlLoader::cLoad('batch','BatchService','sl');
        $batchService->runRefineStockCnt();

        return true;
    }


    /**
     * 카테고리 가져오기
     * @param $params
     * @return \string[][]
     */
    public function getGoodsCate($params){
        //gd_debug( $params );
        $searchVo = new SearchVo();
        $searchVo->setWhere( DBUtil::bind( 'cateCd', DBUtil::AFTER_LIKE ) );
        $searchVo->setWhereValue( $params['cateCd'] ); //001 . Like
        $searchVo->setWhere('length(cateCd)=?');
        $searchVo->setWhereValue(strlen($params['cateCd'])+3);
        $searchVo->setWhere("cateDisplayFl='y'");
        $searchVo->setOrder('cateSort');

        $categoryList = DBUtil2::getListBySearchVo(DB_CATEGORY_GOODS, $searchVo);
        $rslt = [];
        foreach($categoryList as $each){
            $rslt[] = [
                'id' => $each['cateCd'],
                'name' => $each['cateNm'],
            ];
        }
        return $rslt;
    }

    /**
     * 3PL 카테고리 속성 가져오기
     * @param $params
     * @return array
     */
    public function get3PlPrdAttr($params){
        $attr = [];
        for($i=1; 5>=$i; $i++){
            $sql = "select distinct attr{$i} as attr from sl_3plProduct where scmNo = {$params['scmSno']} ";
            $list = DBUtil2::runSelect($sql);
            foreach($list as $each){
                if(!empty($each['attr'])){
                    $attr[$i][] = $each['attr'];
                }
            }
        }
        return $attr;
    }

    /**
     * 3PL코드연결
     * @param $params
     * @throws \Exception
     */
    public function link3plCode($params){
        if(empty($params['linkGoods'])) throw new \Exception('연결 상품 번호가 없습니다.');

        foreach($params['linkCode'] as $linkCode){
            DBUtil2::insert('sl_goodsOptionLink',[
                'goodsNo' => $params['goodsNo'],
                'optionSno' => $params['linkGoods'],
                'code' => $linkCode,
            ]);
        }

        //수량 갱신
        $batchService = SlLoader::cLoad('batch','BatchService','sl');
        $batchService->runRefineStockCnt();

    }

    //입출고 이력 가져오기
    public function getInOutList($params){
        $searchData = [
            'page' => gd_isset($params['page'], 1),
            'pageNum' => gd_isset($params['pageNum'], 50),
        ];
        $searchData['condition'] = $params;
        $searchVo = new SearchVo();
        $searchVo = $this->setInOutListCondition($searchData, $searchVo);
        //TODO : SORT
        $tableInfo = $this->sql->selectInOutListTable();
        $allData = DBUtil2::getComplexListWithPaging($tableInfo, $searchVo, $searchData, false, false);
        $allData['listData'] = SlCommonUtil::setEachData($allData['listData'], $this, 'decorationInOutList');
        return $allData;
    }
    /**
     * 입출고 이력 꾸미기
     * @param $each
     * @param $key
     * @return mixed
     */    
    public function decorationInOutList($each, $key){
        return $each;
    }
    /**
     * 입출고 이력 검색 조건 설정
     * @param $params
     * @param $searchVo
     */
    public function setInOutListCondition($params, SearchVo $searchVo){
        //setCondition($condition, SearchVo $searchVo)
    }


    /**
     * 재고 리포트
     * @return mixed
     */
    public function getReport(){

        $excludeGoods = implode(',',ImsStockService::EXCLUDE_GOODS);

        $startYear = date('y', strtotime('-2 year'));
        $midYear = date('y', strtotime('-1 year'));
        $currentYear = date('y');

        $sql = "select 
            a.scmNo, -- 회사번호
            a.companyNm, -- 회사명
            sum(g.stockCnt) as totalStock,
            sum(if({$startYear} >= attr5, g.stockCnt, 0)) as stock1  , -- 23년 재고
            sum(if({$midYear} = attr5, g.stockCnt, 0))  as stock2  , -- 24년 재고
            sum(if(attr5 >= {$currentYear} , g.stockCnt, 0))  as stock3  -- 25년 재고
        from es_scmManage a 
            join sl_setScmConfig b on a.scmNo = b.scmNo 
            join es_goods c on a.scmNo = c.scmNo 
            join es_goodsOption d on c.goodsNo = d.goodsNo 
            join sl_goodsOptionLink e on d.sno = e.optionSno	
            join sl_3plProduct g on e.code = g.thirdpartyProductCode
        where b.stockManageFl = 'y'
        group by a.scmNo, a.companyNm
        order by sum(g.stockCnt) desc";
        //SitelabLogger::logger2(__METHOD__, $sql);

        $reportList = DBUtil2::runSelect($sql);

        $startPrevOneMonth = date('Y-m-d',strtotime('-1 month'));

        $sql = "select 
                    b.scmNo, 
                    replace(b.goodsNm, d.companyNm, '') as goodsNm,
                    b.goodsNo,
                    c.optionValue1, 
                    c.optionValue2,
                    c.optionValue3,
                    c.optionValue4,
                    c.optionValue5,
                    c.stockCnt,
                    a.inCnt,
                    a.outCnt,
                    ( a.outCnt / a.inCnt * 100) as outRate
            from  sl_goodsOptionExt a
            join es_goods b on a.goodsNo = b.goodsNo 
            join es_goodsOption c on a.optionSno = c.sno
            join es_scmManage d on b.scmNo = d.scmNo 
            where ( a.outCnt / a.inCnt * 100) >= 80
            and b.goodsNo not in ( {$excludeGoods} )  
            and b.stockFl = 'y'  
            and b.goodsNo in (
                select distinct goodsNo 
                from es_orderGoods 
                where regDt >= '{$startPrevOneMonth}' 
            )
            order by b.goodsNm, c.optionNo ";

        $burnList = DBUtil2::runSelect($sql);
        $burnGoods = [];
        foreach($burnList as $burnData){
            $goodsNo = $burnData['goodsNo'];

            $outRate = round($burnData['outRate'],1);

            $optionNameArray = [];
            $optionNameArray[] = $burnData['optionValue1'];
            $optionNameArray[] = $burnData['optionValue2'];
            $optionNameArray[] = $burnData['optionValue3'];
            $optionNameArray[] = $burnData['optionValue4'];
            $optionNameArray[] = $burnData['optionValue5'];
            $optionName = implode('',$optionNameArray);

            if( $burnData['outRate'] >= 80 && 90 > $burnData['outRate'] ){
                $burnGoods[$burnData['scmNo']][80][$goodsNo]['goodsNm'] = $burnData['goodsNm'];
                $burnGoods[$burnData['scmNo']][80][$goodsNo]['option'][] = [
                    'optionName' => $optionName,
                    'inCnt' => $burnData['inCnt'],
                    'outCnt' => $burnData['outCnt'],
                    'outRate' => $outRate,
                    'stockCnt' => $burnData['stockCnt'],
                ];
            }else if( $burnData['outRate'] >= 90 && 100 > $burnData['outRate'] ){
                $burnGoods[$burnData['scmNo']][90][$goodsNo]['goodsNm'] = $burnData['goodsNm'];
                $burnGoods[$burnData['scmNo']][90][$goodsNo]['option'][] = [
                    'optionName' => $optionName,
                    'inCnt' => $burnData['inCnt'],
                    'outCnt' => $burnData['outCnt'],
                    'outRate' => $outRate,
                    'stockCnt' => $burnData['stockCnt'],
                ];
            }else if( $burnData['outRate'] >= 100 ){
                $burnGoods[$burnData['scmNo']][100][$goodsNo]['goodsNm'] = $burnData['goodsNm'];
                $burnGoods[$burnData['scmNo']][100][$goodsNo]['option'][] = [
                    'optionName' => $optionName,
                    'inCnt' => $burnData['inCnt'],
                    'outCnt' => $burnData['outCnt'],
                    'outRate' => $outRate,
                    'stockCnt' => $burnData['stockCnt'],
                ];
            }
        }

        foreach($reportList as $idx => $report){
            $report['comment'] = '';
            $report['burnGoods'] = $burnGoods[$report['scmNo']];
            $reportList[$idx]= $report;
        }

        return $reportList;
    }

    /**
     * 재고 관리 코멘트 등록
     * @param $params
     * @throws \Exception
     */
    public function saveStockComment($params){
        //필수 값 확인
        $tableName = 'sl_stockReportComment';
        $saveData = DBTableField::checkAndRefineSaveData($tableName, [
            'scmNo' => $params['scmNo'],
            'comment' => $params['comment'],
            'regManagerSno' => \Session::get('manager.sno'),
        ]);
        DBUtil2::insert($tableName, $saveData);
    }

    /**
     * 재고 관리 코멘트 가져오기
     * @param $params
     * @return array
     */
    public function getStockComment($params){
        $searchVo = new SearchVo();
        $searchVo->setOrder('regDt desc');
        $list=DBUtil2::getJoinList('sl_stockReportComment', [
            'b' => [ DB_MANAGER, 'a.regManagerSno = b.sno', 'b.managerNm' ]
        ],$searchVo);

        $map = [];
        foreach($list as $each){
            $map[$each['scmNo']][] = $each;
        }
        return $map;
    }

}