<?php

namespace Component\Imsv2;

use App;
use Component\Database\DBIms;
use Component\Database\DBTableField;
use Component\Ims\EnumType\PREPARED_TYPE;
use Component\Ims\ImsCodeMap;
use Component\Ims\ImsDBName;
use Component\Member\Manager;
use Component\Sms\Code;
use Controller\Admin\Ims\Step\ImsStepTrait;
use Framework\Debug\Exception\AlertBackException;
use Framework\Utility\DateTimeUtils;
use LogHandler;
use Request;
use Component\Scm\ScmAsianaTrait;
use Session;
use SiteLabUtil\SlCommonUtil;
use SlComponent\Database\DBUtil2;
use SlComponent\Database\SearchVo;
use SlComponent\Database\TableVo;
use SlComponent\Mail\MailService;
use SlComponent\Mail\SiteLabMailUtil;
use SlComponent\Util\SitelabLogger;
use SlComponent\Util\SlLoader;
use SlComponent\Util\SlSmsUtil;

/**
 * IMS 프로젝트 스케쥴 유틸
 * Class GoodsStock
 * @package Component\Goods
 */
class ImsFieldUtil
{
    /**
     * 영업TAB1 : 영업대기 필드
     */
    public static function getSalesTab2(){
        /**
         * TODO : 스케쥴에 코멘트 롤오버 기능 => 롤오버했을 때 POST로 가져온다. / 가져온건 캐싱한다.
         * [ 여기에 시즌은 없다 ]
         * 프로젝트타입
         * 프로젝트번호
         * 계약난이도
         * 사업계획(연도)
         * 업종
         * 고객명
         * 추정매출
         * 사전기획 예정일 => 승인 받으면 넘어간다
         * 사용 스타일 이걸 어떻게 표현 ? (고객정보에서 가져온다 ? )
         * 등록자/등록일
         */
        //그냥 커스텀하게 해도 되겠는데 - 사용스타일이 있어서.


        //꾸미기
        return [
            ['title'=>'진행타입','type'=>'s','name'=>'bidType2Kr','col'=>4,'rowspan'=>true],
            ['title'=>'프로젝트타입','type'=>'s','name'=>'projectTypeKr','col'=>4,'rowspan'=>true],
            ['title'=>'프로젝트NO','type'=>'c','name'=>'sno','col'=>4,'rowspan'=>true],
            ['title'=>'업종','type'=>'c','name'=>'industry','col'=>6,'rowspan'=>true, 'class'=>"ta-l pdl5"],
            ['title'=>'고객명','type'=>'c','name'=>'customerName','col'=>0,'rowspan'=>true, 'class'=>"ta-l pdl5"],

            ['title'=>'추정매출','type'=>'s','name'=>'amount','col'=>0,'rowspan'=>true],
            ['title'=>'입찰예정','type'=>'d1','name'=>'regDt','col'=>5,'rowspan'=>true], //CustomerDt Bid Dt ?
            ['title'=>'디자인실 참여','type'=>'s','name'=>'industry','col'=>0,'rowspan'=>true],
            ['title'=>'사전기획 예정','type'=>'d2','name'=>'exSalesReadyPlan','col'=>6,'rowspan'=>true],
            ['title'=>'영업담당자','type'=>'s','name'=>'salesManagerNm','col'=>5,'rowspan'=>true],
            ['title'=>'영업 메모','type'=>'s','name'=>'exSalesReadyPlan','col'=>15,'rowspan'=>true],
            ['title'=>'TM/EM<br>영업 내역','type'=>'s','name'=>'exSalesReadyPlan','col'=>4,'rowspan'=>true],
            ['title'=>'등록일','type'=>'d2s','name'=>'regDt','col'=>4,'rowspan'=>true],
            ['title'=>'등록','type'=>'s','name'=>'regManagerNm','col'=>4,'rowspan'=>true],
        ];
    }


    /**
     * 스타일 (팝업)
     * 팝업에서 보는 발주 스타일 화면
     * @return array[]
     */
    public static function getPopupStyleField()
    {
        return [
            ['title' => '스타일명', 'type' => 'prdStyle', 'name' => 'productName', 'col' => 10,'class' => 'ta-l pdl10'], //스타일
            ['title' => '생산처', 'type' => 's', 'name' => 'produceCompanyName', 'col' => 5,'class' => 'ta-l'], //스타일
            ['title' => 'Q/B','type'=>'c','name'=>'fabricStatusKr','col'=>5,],
            ['title' => '생산기간', 'type' => 'c', 'name' => 'prdPeriod', 'col' => 3 ],
            ['title' => '수량', 'type' => 'i', 'name' => 'prdExQty', 'col' => 3 ],
            ['title' => '대표원단', 'type' => 'c', 'name' => 'repFabric', 'col' => 6 , 'class'=>'font-10 ta-l'],
            ['title' => '생산MOQ', 'type' => 'c', 'name' => 'prdMoq', 'col' => 3 , 'class'=>'ta-r'],
            ['title' => '단가MOQ', 'type' => 'c', 'name' => 'priceMoq', 'col' => 3, 'class'=>'ta-r' ],
            ['title' => '판매가', 'type' => 'c', 'name' => 'salePrice', 'col' => 4, 'class'=>'ta-r text-danger' ],
            ['title' => '생산가', 'type' => 'c', 'name' => 'prdCost', 'col' => 4, 'class'=>'ta-r sl-blue' ],
            ['title' => '마진', 'type' => 's', 'name' => 'margin', 'col' => 2, 'valueSuffix'=>'%'],
            ['title' => '작지', 'type' => 'c', 'name' => 'workStatus', 'col' => 2, 'class'=>'' ],
        ];
    }


}



