<?php
namespace Controller\Admin\Ims\ControllerService;

use App;
use Component\Ims\ImsCodeMap;
use Component\Ims\ImsDBName;
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
use SlComponent\Util\SitelabLogger;
use SlComponent\Util\SlCommonTrait;
use SlComponent\Util\SlLoader;

/**
 * 입출고 리스트
 * Class GoodsStock
 * @package Component\Goods
 */
class ImsProjectListService implements ListInterface {
    use SlCommonTrait;
    use ImsListServiceTrait;

    private $imsService;

    const LIST_TITLES = [
        '현재상태', //projectStatus projectStatusKr
        '프로젝트번호', //projectNo
        '타입', //projectType  projectTypeKr
        '고객사명', //customerName join
        '영업담당자', // salesManagerSno join  salesManagerNm
        '수량', // sum join style init 0
        '발주', // customerOrderDt
        '납기', // customerDeliveryDt
        '고객확정', // confirmed
        '제안형태', // recommend refine.  recommendIcon
        '입찰', // bid
        '제안마감일', // recommendDt
        '디자인담당자', // designManagerSno join
        '디자인마감일', //  designEndDt
        'QC마감일', // prdEndDt
        '등록일자<br>수정일자', // regdt moddt
    ];

    public function runConstructionAddMethod(){
        $this->imsService = SlLoader::cLoad('ims','imsService');
    }

    /**
     * 검색 데이터 설정
     * @param $searchData
     */
    public function _setSearch($searchData){
        $searchKey = [
            'b.customerName' => '고객사명',
            'a.projectNo' => '프로젝트번호',
            'e.styleCode' => '스타일코드',
            'b.contactName' => '고객사 담당자명',
            'c.managerNm' => '영업담당자',
            'd.managerNm' => '디자인담당자',
            'e.productName' => '스타일명',
            'a.projectName' => '말머리(프로젝트별칭)',
        ];
        $sortList = [
            'a.projectYear asc' => __('생산년도 ↑'),
            'a.projectYear desc' => __('생산년도 ↓'),
            'a.customerDeliveryDt asc' => __('고객납기일 ↑'),
            'a.customerDeliveryDt desc' => __('고객납기일 ↓'),
            'a.regDt asc' => __('등록일 ↑'),
            'a.regDt desc' => __('등록일 ↓'),
            'b.customerName asc' => __('고객사명 ↑'),
            'b.customerName desc' => __('고객사명 ↓'),
        ];

        if( empty($searchData['status'])
            || 'step90' == $searchData['status'] ){
            $sort = 'a.projectYear desc';
        }else{
            $sort = 'a.customerDeliveryDt asc';
        }

        $setParam = [
            'combineSearch' => $searchKey,
            'sortList' => $sortList,
            'sort' => gd_isset( $searchData['sort'] ,$sort),
            'page' => gd_isset( $searchData['page'] ,1),
            'pageNum' => gd_isset( $searchData['pageNum'] ,30),
        ];
        ListUtil::setSearch($this, $setParam);

        // 기본 텍스트 검색 설정
        $this->setSearchData([
            'key'
            ,'keyword'
            ,'showMemo'
            ,'orderProgressFl'
            ,'projectType'
            ,'isAccOnly'
            ,'isExcludeRtw'
            ,'isExcludeNextSeason'
            ,'isProduction'
            ,'projectYear'
            ,'projectSeason'
            ,'procStatusKey'
            ,'procStatusValue'
        ],$searchData);

        $this->setRadioSearch([
            'showMemo'
        ],$searchData,'y');

        $this->setCheckSearch([
            'orderProgressFl',
            'projectType',
            'isAccOnly',
            'isExcludeRtw',
            'isExcludeNextSeason',
            'isProduction',
        ]);

        //전체 리스트 일때만...
        if( empty($searchData['status']) && empty($this->search['orderProgressFl'])){
            $this->search['orderProgressFl'] = [
                10,15,16,20,30,31,40,41,50,60,80,90
            ];
            $this->checked['orderProgressFl'] = [
                10 => 'checked="checked"',
                15 => 'checked="checked"',
                16 => 'checked="checked"',
                20 => 'checked="checked"',
                30 => 'checked="checked"',
                31 => 'checked="checked"',
                40 => 'checked="checked"',
                41 => 'checked="checked"',
                50 => 'checked="checked"',
                60 => 'checked="checked"',
                80 => 'checked="checked"',
                90 => 'checked="checked"',
            ];
        }

        if( empty($this->search['isProduction'])){
            $this->search['isProduction'] = [
                0,1
            ];
            $this->checked['isProduction'] = [
                0 => 'checked="checked"',
                1 => 'checked="checked"',
            ];
        }

        //날짜 기간 설정
        if( empty($searchData['treatDateFl']) ) $searchData['treatDateFl'] = 'a.customerDeliveryDt';
        //고정?
        //$searchData['treatDate'][0] = gd_isset($searchData['treatDate'][0],date('Y-m-d'));
        //$searchData['treatDate'][1] = gd_isset($searchData['treatDate'][1],date('Y-m-d'));
        $this->setRangeDate($searchData['treatDateFl'],  $searchData['treatDate'][0], $searchData['treatDate'][1]);

    }

    /**
     * 거래처 리스트
     * @param string  $searchData   검색 데이타
     *
     * @return array 주문 리스트 정보
     */
    public function getList($searchData): array {
        $data = $this->getTraitList($searchData, 'getList');
        $data['data'] = SlCommonUtil::setEachData($data['data'], $this, 'setRefineEachListData');
        //$data['totalInoutCount'] = $this->sql->getListSummaryData($searchData)[0];
        //gd_debug($data['data']);
        return $data;
    }

    /**
     * 리스트 데이터 Refine.
     * @param $each
     * @param $key
     * @param $data
     * @return mixed
     */
    public function setRefineEachListData($each, $key, $data){

        $each = $this->imsService->decorationEachData($each);
        $recommendIcon = [];
        foreach( $each['recommend'] as $recommendKey => $recommendValue ){
            $recommendIcon[] = '<span class="ims-recommend ims-recommend'.$recommendValue.'">'.substr(ImsCodeMap::RECOMMEND_TYPE[$recommendValue],0,3).'</span>';
        }
        $each['recommendIcon'] = implode('',$recommendIcon);

        $each['confirmed'] = 'y' === $each['confirmed']? '예':'아니오';

        /*if( 'y'==$each['use3pl'] ){
            $use3plAndMall[] = '3PL';
        }
        if( 'y'==$each['useMall'] ){
            $use3plAndMall[] = '폐쇄몰';
        }
        $mall3plStr = implode('/',$use3plAndMall);
        if(!empty($mall3plStr)){
            $each['use3plAndMall'] = '<span class="text-danger font-11">('.$mall3plStr.')</span>';
        }*/

        //아이콘 추가
        $this->imsService->setProjectIcon($each, $each['sno']);

        //미팅일자
        $each['meetingDt'] = SlCommonUtil::getSimpleWeekDay($each['meetingDt']);
        $each['readyDeadLineDt'] = SlCommonUtil::getSimpleWeekDay($each['readyDeadLineDt']);

        $each['customerOrderDt'] = gd_date_format('m/d',$each['customerOrderDt']);
        $each['customerDeliveryDt'] = gd_date_format('m/d',$each['customerDeliveryDt']);

        $each['customerSize'] = SlCommonUtil::numberToKorean($each['calcSize']);
        //$each['customerSize'] = $each['calcSize'];

        $each['recommendDt'] = gd_date_format('m/d',$each['recommendDt']);
        $each['designEndDt'] = gd_date_format('m/d',$each['designEndDt']);
        $each['prdEndDt'] = gd_date_format('m/d',$each['prdEndDt']);
        $each['regDt'] = gd_date_format('Y-m-d',$each['regDt']);
        $each['modDt'] = gd_date_format('Y-m-d',$each['regDt']);

        //$each['commentCnt'] = $this->imsService->getCommentCount(ImsCodeMap::PROJECT_STEP_COMMENT_DIV[$each['projectStatus']],$each['sno']);
        $each['commentCnt'] = $this->imsService->getCommentCount('',$each['sno']);

        $each['latestComment'] = $this->imsService->getLatestComment('',$each['sno']);
        $each['productList'] = $this->imsService->getProductList(['projectSno' => $each['sno']]);

        $each['prdCost'] = 0;
        foreach($each['productList'] as $prd){
            $each['prdCost'] += ($prd['prdExQty'] * $prd['prdCost']);
        }
        $prdCost = $each['prdCost'];
        $each['prdCost'] = SlCommonUtil::numberToKorean($each['prdCost']);

        $each['salePrice'] = 0;
        foreach($each['productList'] as $prd){
            $each['salePrice'] += ($prd['prdExQty'] * $prd['salePrice']);
        }
        $salePrice = $each['salePrice'];
        $each['salePrice'] = SlCommonUtil::numberToKorean($each['salePrice']);
        $each['msMargin'] = 100 - (($prdCost / $salePrice) * 100);
        if( $each['msMargin'] > 100) $each['msMargin'] = 100;

        return $each;
    }

}
