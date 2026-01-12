<?php
namespace Component\Ims25\Sql;

use App;
use Component\Database\DBIms;
use Component\Database\DBTableField;
use Component\Ims\ImsCodeMap;
use Component\Ims\ImsDBName;
use Component\Member\Manager;
use Component\Scm\ScmTkeService;
use Framework\Debug\Exception\AlertBackException;
use Framework\Utility\DateTimeUtils;
use LogHandler;
use Request;
use Session;
use SiteLabUtil\SlCommonUtil;
use SlComponent\Database\DBUtil;
use SlComponent\Database\DBUtil2;
use SlComponent\Database\SearchVo;
use SlComponent\Mail\SiteLabMailUtil;
use SlComponent\Util\ExcelCsvUtil;
use SlComponent\Util\SitelabLogger;
use SlComponent\Util\SlCodeMap;
use SlComponent\Util\SlLoader;
use Component\Sms\Code;

/**
 * IMS25 프로젝트 리스트 쿼리 모음
 * Class GoodsStock
 * @package Component\Goods
 */
class Ims25ListSql {

    //검색을 위해 => 스타일은 조인해야함 X
    //집계를 한다 => 스타일 수정시 갱신)
    //스타일 / 시즌 / 스타일 코드에 대한 검색
    /**
     * 프로젝트 리스트
     * @param bool $withStyle
     * @return array
     * @throws \Exception
     */
    public function getIms25ListSql($withStyle=false){
        //프로젝트 추가 정보 + 스케쥴 필드 설정
        $extField = array_flip(DBTableField::getTableKey(ImsDBName::PROJECT_EXT));
        $extField = array_flip(SlCommonUtil::unsetByList($extField,['regDt','modDt', 'sno']));

        $tables = [
            //프로젝트
            'prj'  =>['data' => [ ImsDBName::PROJECT ], 'field' => ['prj.*']],
            //고객정보
            'cust' =>['data' => [ ImsDBName::CUSTOMER, 'JOIN', 'prj.customerSno = cust.sno' ]
                , 'field' => ['cust.customerName','cust.busiCateSno','cust.styleCode','cust.customerStatus']],
            //확장정보
            'ext'  =>['data' => [ ImsDBName::PROJECT_EXT, 'LEFT OUTER JOIN', 'prj.sno = ext.projectSno' ]
                , 'field' => ['ext.'.implode(',ext.',$extField)]],
            //업종
            'biz'  =>['data' => [ ImsDBName::BUSI_CATE, 'LEFT OUTER JOIN', 'cust.busiCateSno = biz.sno' ]
                , 'field' => ['biz.cateName as bizName']],
            //부모 업종
            'pBiz'  =>['data' => [ ImsDBName::BUSI_CATE, 'LEFT OUTER JOIN', 'biz.parentBusiCateSno = pBiz.sno' ]
                , 'field' => ['pBiz.cateName as pBizName']],
        ];

        if($withStyle){
            $styleFieldList = [
                'prd.sno as styleSno',
                'prd.productName',
                'right(prd.prdYear,2) as prdYear',
                'prd.prdSeason',
                'prd.styleCode',
                'prd.prdCost',
                'prd.salePrice',
                'prd.prdExQty',
                'prd.prdCostConfirmSno',
                'prd.estimateConfirmSno',
                'prd.workStatus as prdWorkStatus',
                'prd.priceConfirm',
                'prd.prdCostStatus',
                'prd.msDeliveryDt as prdMsDeliveryDt',
                'prd.customerDeliveryDt as prdCustomerDeliveryDt',
                'prd.fabricStatus as prdFabricStatus',
                'prd.btStatus as prdBtStatus',
            ];
            $styleField = implode(',', $styleFieldList);

            $tables['prd'] = [
                'data' => [ ImsDBName::PRODUCT, 'LEFT OUTER JOIN', 'prj.sno = prd.projectSno and prd.delFl = \'n\'' ]
                , 'field' => [$styleField]
            ];
            $tables['estimate'] = [
                'data' => [ ImsDBName::ESTIMATE, 'LEFT OUTER JOIN', 'prd.estimateConfirmSno = estimate.sno' ]
                , 'field' => ['estimate.contents as estimateContents, estimate.estimateCost']
            ];
            $tables['cost'] = [
                'data' => [ ImsDBName::ESTIMATE, 'LEFT OUTER JOIN', 'prd.prdCostConfirmSno = cost.sno' ]
                , 'field' => ['cost.contents as costContents, cost.estimateCost as prdEstimateCost']
            ];
        }

        //메인
        return DBUtil2::setTableInfo($tables, false);
    }


}



