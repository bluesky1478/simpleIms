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
class ImsScheduleConfig
{
    /**
     * 사용 스케쥴 목록 / 메인, 스케쥴 별도
     * 1 영업
     * 2 디자인
     * 4 생산

    [영업준비 10]
    salesReadyPlan	사전 기획

    [사전영업 15]
    meetingReady	입찰설명회/사전미팅
    sampleCust	샘플확보
    readyToDesign	(사전준비)디자인제안서
    readyToImprove	(사전준비)개선제안서
    readyToPrefer	(사전준비)선호도조사
    sampleTest	(사전준비)샘플테스트
    envSurvey	근무환경 조사
    researchField	리서치 시행
    meeting	미팅/입찰  ★
    salesPlan	영업 기획서
    salesGuide	고객 스케쥴 안내

    [기획 20]
    plan	디자인 기획서

    [제안 30 / 제안서확정대기 31]
    proposal	제안서 제작
    estimatePay1	견적/샘플 요청 비용 안내
    meetingProposal	제안
    sampleMakeConfirm	샘플 제작 여부

    [샘플 40 ]
    sampleOrder	샘플 지시서
    sampleComplete	샘플실 완료
    sampleReview	샘플 리뷰
    samplePayConfirm	샘플 생산가 확정
    sampleGuide	샘플 안내서

    [계약/샘플확정대기 41]
    sampleInform	샘플 제안/발송
    estimatePay2	제안견적 발송
    sampleConfirm	샘플 확정서
    costConfirm	최종 생산가 확정
    estimatePay3	최종 견적 발송
    salesConfirmation	영업 확정서/계약서
    sampleReturn	샘플 회수
    assortConfirm	고객발주(아소트 입력)

    [발주준비 50]
    qb	QB
    order	작업지시서/사양서 제작
    orderConfirm	사양서 확정

    [발주 60]
    productionOrder	발주

    [발주완료 60]
    qcReport	QC 리포트
    inline	인라인
    projectComplete	납품보고서

     */
    const SCHEDULE_LIST = [
        //------------------------------------------------------    사전기획
        'salesReadyPlan' => [
            'name'=>'사전 기획'
            ,'dept'=>'s','list'=>['s','d','q']
            ,'prep'=>'y',
        ],  //사전기획 대기 중 (사전기획 전일 경우 / 상태 . 영업대기)
        'meetingReady' => [
            'name'=>'입찰설명회/사전미팅'
            ,'dept'=>'s','list'=>['s',]
            ,'prep'=>'y'
        ],
        'sampleCust' => [
            'name'=>'샘플확보'
            ,'dept'=>'s','list'=>['s',]
            ,'prep'=>'y'
        ],
        'readyToDesign' => [
            'name'=>'(사전준비)디자인제안서'
            ,'dept'=>'s','list'=>['s',]
            ,'prep'=>'y'
        ],
        'readyToImprove' => [
            'name'=>'(사전준비)개선제안서'
            ,'dept'=>'s','list'=>['s',]
            ,'prep'=>'y'
        ],
        'readyToPrefer' => [
            'name'=>'(사전준비)선호도조사'
            ,'dept'=>'s','list'=>['s',]
            ,'prep'=>'y'
        ],
        'sampleTest' => [
            'name'=>'(사전준비)샘플테스트'
            ,'dept'=>'s','list'=>['s',]
            ,'prep'=>'y'
        ],
        'envSurvey' => [
            'name'=>'근무환경 조사'
            ,'dept'=>'s','list'=>['s',]
            ,'prep'=>'y'
        ],
        'researchField' => [
            'name'=>'리서치 시행'
            ,'dept'=>'s','list'=>['s',]
            ,'prep'=>'y'
        ],
        'meeting' => [
            'name'=>'미팅/입찰'
            ,'dept'=>'s','list'=>['s',]
            ,'prep'=>'y'
        ],
        'salesPlan' => [
            'name'=>'영업 기획서'
            ,'dept'=>'s','list'=>['s','d','q']
            ,'prep'=>'y'
            ,'auto'=>'y',
        ],
        'salesGuide' => [
            'name'=>'고객 스케쥴 안내'
            ,'dept'=>'s','list'=>['s',]
            ,'prep'=>'y'
        ],
        //------------------------------------------------------     기획/제안/샘플
        'plan' => [
            'name'=>'디자인 기획서'
            ,'dept'=>'d','list'=>['d',]
            ,'auto' => 'y',
        ],
        'proposal' => [
            'name'=>'제안서 제작'
            ,'dept'=>'d','list'=>['d',]
            ,'auto' => 'y',
        ],
        'estimatePay1' => [
            'name'=>'견적/샘플 요청 비용 안내'
            ,'dept'=>'s','list'=>['s',]
        ],
        'meetingProposal' => [
            'name'=>'제안서 전달'
            ,'dept'=>'s','list'=>['s',]
            ,'cust'=>'y'
        ],
        'sampleMakeConfirm' => [
            'name'=>'샘플 제작 여부'
            ,'dept'=>'s','list'=>['s',]
            ,'cust'=>'y'
            
        ],
        'sampleOrder' => [
            'name'=>'샘플 지시서'
            ,'dept'=>'d','list'=>['d',]
        ],
        'sampleComplete' => [
            'name'=>'샘플실 완료'
            ,'dept'=>'d','list'=>['d',]
        ],
        'sampleReview' => [
            'name'=>'샘플 리뷰'
            ,'dept'=>'d','list'=>['d',]
        ],
        'samplePayConfirm' => [
            'name'=>'샘플 생산가 확정'
            ,'dept'=>'d','list'=>['d',]
        ],
        'sampleGuide' => [
            'name'=>'샘플 안내서'
            ,'dept'=>'d','list'=>['d',]
        ],
        //------------------------------------------------------     샘플확정 / 계약
        'sampleInform' => [
            'name'=>'샘플 제안'
            ,'dept'=>'s','list'=>['s',]
            ,'cust'=>'y'
        ],
        'estimatePay2' => [
            'name'=>'제안견적 발송'
            ,'dept'=>'s','list'=>['s',]
        ],
        'sampleConfirm' => [
            'name'=>'샘플 확정'
            ,'dept'=>'s','list'=>['s','d']
            ,'cust'=>'y'
        ],
        'costConfirm' => [
            'name'=>'최종 생산가 확정'
            ,'dept'=>'q','list'=>['q',]
        ],
        'estimatePay3' => [
            'name'=>'최종 견적 발송'
            ,'dept'=>'s','list'=>['s',]
        ],
        'salesConfirmation' => [
            'name'=>'영업 확정서/계약서'
            ,'dept'=>'s','list'=>['s',]
        ],
        'sampleReturn' => [
            'name'=>'샘플 회수'
            ,'dept'=>'s','list'=>['s',]
        ],
        //------------------------------------------------------     발주
        'assortConfirm' => [
            'name'=>'고객발주(아소트 입력)'
            ,'dept'=>'s','list'=>['s',]
            ,'cust'=>'y'
            ,'auto'=>'y'
        ],
        'qb' => [
            'name'=>'QB'
            ,'dept'=>'d','list'=>['d',]
        ],
        'order' => [
            'name'=>'작업지시서/사양서 제작'
            ,'dept'=>'d','list'=>['d',]
            ,'auto' => 'y',
        ],
        'orderSend' => [
            'name'=>'사양서 발송'
            ,'dept'=>'s','list'=>['s',]
            ,'auto' => 'y',
        ],
        'orderConfirm' => [
            'name'=>'사양서 확정'
            ,'dept'=>'s','list'=>['s',]
            ,'auto' => 'y',
        ],
        'productionOrder' => [
            'name'=>'발주'
            ,'dept'=>'q','list'=>['q',]
            ,'auto' => 'y',
        ],
        'qcReport' => [
            'name'=>'QC 리포트'
            ,'dept'=>'q','list'=>['q',]
        ],
        'inline' => [
            'name'=>'인라인'
            ,'dept'=>'q','list'=>['q',]
        ],
        'projectComplete' => [
            'name'=>'납품보고서'
            ,'dept'=>'q','list'=>['q',]
        ],
    ];

    const SCHEDULE_TYPE = [
        'ex',
        'cp', //완료일
        'st', //상태
        'tx',
    ];

    const PROJECT_SCHEDULE_STATUS = [
        0 => ['name'=>'대기'   , 'color'=>'text-muted', 'icon'=>'stop-circle'],
        1 => ['name'=>'등록완료', 'color'=>'sl-blue', 'icon'=>'play'],
        2 => ['name'=>'결재단계'  , 'color'=>'sl-blue', 'icon'=>'play'],
        3 => ['name'=>'결재완료', 'color'=>'sl-blue', 'icon'=>'play'],
        4 => ['name'=>'발송완료', 'color'=>'sl-blue', 'icon'=>'play'],
        9 => ['name'=>'PASS'   , 'color'=>'text-green', 'icon'=>'check-circle'],
        10 => ['name'=>'완료'    , 'color'=>'text-green', 'icon'=>'check-circle'],
    ];


    //스케쥴 요약
    const SCHEDULE_LIST_SUMMARY = [
        'meeting'          => '미팅/입찰',
        'plan'             => '디자인 기획서',
        'proposal'         => '제안서 제작',
        'meetingProposal'  => '제안서 전달',
        'sampleMakeConfirm'=> '샘플 제작 여부',
        'sampleInform'     => '샘플 제안/발송',
        'assortConfirm'   => '고객발주<br>(아소트 확정)',
        'productionOrder'  => '발주',
        'customerDeliveryDt' => '납품',
    ];

    const SCHEDULE_LIST_CUSTOMER = [
        'meetingProposal'  => '제안서 전달',
        'sampleMakeConfirm'=> '샘플 제작 여부',
        'sampleInform'     => '샘플 제안',
        'sampleConfirm'    => '샘플 확정',
        'assortConfirm'    => '고객사 발주',
        'customerDeliveryDt' => '납품',
    ];

    //전체 스케쥴 - 사전기획
    const SCHEDULE_LIST_TYPE1 = [
        'salesReadyPlan'   => '사전 기획',
        'meetingReady'     => '입찰설명회/사전미팅',
        'sampleCust'       => '샘플확보',
        'readyToDesign'    => '(사전준비)<br>디자인제안서',
        'readyToImprove'   => '(사전준비)<br>개선제안서',
        'readyToPrefer'    => '(사전준비)<br>선호도조사',
        'sampleTest'       => '(사전준비)<br>샘플테스트',
        'envSurvey'        => '근무환경 조사',
        'researchField'    => '리서치 시행',
        'meeting'          => '미팅/입찰',
        'salesPlan'        => '영업 기획',
        'salesGuide'       => '고객 스케쥴 안내',
    ];


    const SCHEDULE_LIST_TYPE2 = [
        'plan'             => '디자인 기획서',
        'proposal'         => '제안서 제작',
        'estimatePay1'     => '견적/샘플 <br>요청비용 안내',
        'meetingProposal'  => '제안서 전달',
        'sampleMakeConfirm'=> '샘플 제작 여부',
        'sampleOrder'      => '샘플 지시서',
        'sampleComplete'   => '샘플실 완료',
        'sampleReview'     => '샘플 리뷰',
        'samplePayConfirm' => '샘플 생산가 확정',
        'sampleGuide'      => '샘플 안내서',
    ];
    const SCHEDULE_LIST_TYPE3 = [
        'sampleInform'       => '샘플 제안/발송',
        'estimatePay2'       => '제안견적 발송',
        'sampleConfirm'      => '샘플 확정',
        'costConfirm'        => '최종 생산가 확정',
        'estimatePay3'       => '최종 견적 발송',
        'salesConfirmation'  => '영업 확정서/ 계약서',
        'sampleReturn'       => '샘플 회수',
    ];
    const SCHEDULE_LIST_TYPE4 = [
        'assortConfirm'   => '고객발주(아소트 확정)',
        'qb'              => 'QB',
        'order'           => '작지 / 사양서',
        'orderSend'       => '사양서 발송',
        'orderConfirm'    => '사양서 확정',
        'productionOrder' => '발주(발주DL)',
        'qcReport'        => 'QC 리포트',
        'inline'          => '인라인',
        'projectComplete' => '납품보고서',
    ];

    /**
     * 영업 스케쥴
     */
    const SCHEDULE_LIST_SALES = [
        'estimatePay1'     => '견적/샘플 <br>요청비용 안내',
        'meetingProposal'  => '제안서 전달',
        'sampleMakeConfirm'=> '샘플 제작 여부',
        'sampleInform'       => '샘플 제안/발송',
        'estimatePay2'       => '제안견적 발송',
        'sampleConfirm'      => '샘플 확정',
        'estimatePay3'       => '최종 견적 발송',
        'salesConfirmation'  => '영업 확정서/ 계약서',
        'sampleReturn'       => '샘플 회수',
        'assortConfirm'   => '고객발주<br>(아소트 확정)',
        'orderSend'       => '사양서 발송',
        'orderConfirm'    => '사양서 확정',
    ];

    /**
     * 디자인 스케쥴
     */
    const SCHEDULE_LIST_DESIGN = [
        'plan'             => '디자인 기획서',
        'proposal'         => '제안서 제작',
        'sampleOrder'      => '샘플 지시서',
        'sampleComplete'   => '샘플실 완료',
        'sampleReview'     => '샘플 리뷰',
        'samplePayConfirm' => '샘플 생산가 확정',
        'sampleGuide'      => '샘플 안내서',
        'qb'               => 'QB',
        'order'            => '작지/사양서',
    ];
    
    /**
     * 생산팀 스케쥴
     */
    const SCHEDULE_LIST_QC = [
        'costConfirm'     => '최종 생산가 확정',
        'productionOrder' => '발주(발주DL)',
        'qcReport'        => 'QC 리포트',
        'inline'          => '인라인',
        'projectComplete' => '납품보고서',
    ];

}



