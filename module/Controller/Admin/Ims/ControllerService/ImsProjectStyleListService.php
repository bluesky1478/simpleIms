<?php
namespace Controller\Admin\Ims\ControllerService;

use App;
use Component\Ims\ImsCodeMap;
use Component\Member\Manager;
use Component\Storage\Storage;
use Framework\Debug\Exception\AlertBackException;
use Framework\Utility\DateTimeUtils;
use LogHandler;
use Request;
use Session;
use SiteLabUtil\SlCommonUtil;
use SlComponent\Database\DBConst;
use SlComponent\Godo\ListInterface;
use SlComponent\Util\ListUtil;
use SlComponent\Util\SlCommonTrait;

/**
 * 입출고 리스트
 * Class GoodsStock
 * @package Component\Goods
 */
class ImsProjectStyleListService implements ListInterface {
    use SlCommonTrait;
    use ImsListServiceTrait;

    const LIST_TITLES = [
        '프로젝트번호<br>현재상태', //projectStatus projectStatusKr
        '타입', //projectType  projectTypeKr
        '고객사명', //customerName join
        
        '이미지', //
        '제품명(스타일)', //
        '스타일코드', //
        '제작수량', //
        '현재 단가', //currentPrice
        '타겟 단가', //targetPrice
        '타겟 생산가', //targetPrdCost
        '생산가', //prdCost
        '마진', //

        '발주', // customerOrderDt
        '납기', // customerDeliveryDt
        '고객확정', // confirmed
        '등록일자<br>수정일자', // regdt moddt
    ];

    /**
     * 검색 데이터 설정
     * @param $searchData
     */
    public function _setSearch($searchData){
        $searchKey = [
            'b.customerName' => '고객사명',
            'b.contactName' => '고객사 담당자명',
            'b.styleCode' => '고객명(Style code)',
            'b.industry' => '업종',
            'b.contactAddress' => '사무실 주소',
            'b.contactAddressSub' => '사무실 주소 상세',
        ];
        $sortList = [
            'a.customerDeliveryDt asc' => __('납기일 ↑'),
            'a.customerDeliveryDt desc' => __('납기일 ↓'),
            'a.regDt asc' => __('등록일 ↑'),
            'a.regDt desc' => __('등록일 ↓'),
            'a.customerName asc' => __('고객사명 ↑'),
            'a.customerName desc' => __('고객사명 ↓'),
        ];

        $setParam = [
            'combineSearch' => $searchKey,
            'sortList' => $sortList,
            'sort' => gd_isset( $searchData['sort'] ,'a.customerDeliveryDt desc'),
            'page' => gd_isset( $searchData['page'] ,1),
            'pageNum' => gd_isset( $searchData['pageNum'] ,100),
        ];
        ListUtil::setSearch($this, $setParam);

        // 기본 텍스트 검색 설정
        $this->setSearchData([
            'key'
            ,'keyword'
        ],$searchData);
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

        $each['projectStatusKr'] = ImsCodeMap::PROJECT_STATUS[$each['projectStatus']];
        $each['projectTypeKr'] = ImsCodeMap::PROJECT_TYPE[$each['projectType']];

        $recommendIcon[] = '<span class="ims-recommend ims-recommend1">기</span>';
        $recommendIcon[] = '<span class="ims-recommend ims-recommend2">제</span>';
        $recommendIcon[] = '<span class="ims-recommend ims-recommend3">샘</span>';

        if( !empty($each['targetPrice']) ){
            $each['margin'] = round(100-($each['targetPrdCost'] / $each['targetPrice'] * 100)) . '%';
        }else{
            $each['margin'] = '';
        }


        $each['recommendIcon'] = implode('',$recommendIcon);
        $each['confirmed'] = 'y' === $each['confirmed']? '예':'아니오';
        $each['use3plKr'] = 'y' === $each['confirmed']? '<span class="sm-danger">(3PL)</span>':'';

        $each['customerOrderDt'] = gd_date_format('m/d',$each['customerOrderDt']);
        $each['customerDeliveryDt'] = gd_date_format('m/d',$each['customerDeliveryDt']);
        $each['recommendDt'] = gd_date_format('m/d',$each['recommendDt']);
        $each['designEndDt'] = gd_date_format('m/d',$each['designEndDt']);
        $each['prdEndDt'] = gd_date_format('m/d',$each['prdEndDt']);
        $each['regDt'] = gd_date_format('Y-m-d',$each['regDt']);
        $each['modDt'] = gd_date_format('Y-m-d',$each['regDt']);
        return $each;
    }

}
