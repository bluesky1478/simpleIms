<?php

namespace Controller\Admin\Ims\Step;

use App;
use Component\Facebook\Facebook;
use Component\Godo\GodoPaycoServerApi;
use Component\Ims\ImsCodeMap;
use Component\Ims\ImsDBName;
use Component\Ims\ImsJsonSchema;
use Component\Ims\ImsService;
use Component\Ims\StatusValidService;
use Component\Member\MemberSnsService;
use Component\Member\MemberValidation;
use Component\Member\Util\MemberUtil;
use Component\Policy\SnsLoginPolicy;
use Component\SiteLink\SiteLink;
use Component\Storage\Storage;
use Controller\Front\Controller;
use Exception;
use Framework\Debug\Exception\AlertBackException;
use Framework\Debug\Exception\AlertRedirectException;
use Framework\Debug\Exception\AlertReloadException;
use Logger;
use Request;
use SiteLabUtil\ImsStatusUtil;
use SiteLabUtil\SlCommonUtil;
use SlComponent\Database\DBUtil2;
use SlComponent\Database\SearchVo;
use SlComponent\Util\SitelabLogger;
use SlComponent\Util\SlControllerTrait;
use SlComponent\Util\SlLoader;

/**
 * 리스트 설정 및 상태변경 Trait
 */
trait ImsStepTrait {

    //-------------------------------------------------------------------------
    // Step Check

    /**
     * projectStatus. projectSno, reason
     * 상태변경 , 이력 등록
     * @param $params
     * @param bool $checkCurrentStatus
     * @return mixed
     * @throws Exception
     */
    public function setStatus($params, $checkCurrentStatus=false)
    {
        return ImsStatusUtil::setStatus($params, $checkCurrentStatus);
    }

    //--- 리스트 설정

    /**
     * width 없는 부분 설정
     * @param $list
     * @return mixed
     */
    public function setListColWidth($list){
        //전체 Col 계산하여 97을 나눈다.
        $emptyCol = 0;
        $colWidth = 0;
        foreach($list['list'] as $key => $each){
            if( empty($each['col']) ){
                $emptyCol++;
            }else{
                $colWidth += $each['col'];
            }
        }
        $divCnt = floor((96-$colWidth)/$emptyCol);

        foreach($list['list'] as $key => $each){
            if( empty($each['col']) ){
                $each['col']=$divCnt;
            }
            $list['list'][$key] = $each; //재할당
        }
        return $list;
    }

    //전체
    public function setList(){
        $listSetupData = [
            'list' => [
                ['title'=>'등록일','col'=>4],
                ['title'=>'연도/시즌','col'=>3],
                ['title'=>'타입<br>계약형태','col'=>5],
                ['title'=>'고객사/프로젝트번호','col'=>14],
                ['title'=>'희망 납기','col'=>5],
                ['title'=>'발주 D/L','col'=>5],
                ['title'=>'매출규모','col'=>5],
                ['title'=>'스타일','col'=>14],
                ['title'=>'회계반영'   ,'col'=>4],
                ['title'=>'수량'  ,'field'=>'totalQty', 'format'=>'number',  'col'=>4],
                ['title'=>'생산가','field'=>'prdCostKr' , 'format'=>''   ,'col'=>4],
                ['title'=>'판매가','field'=>'prdPriceKr', 'format'=>''   ,'col'=>4],
                ['title'=>'마진'  ,'field'=>'prdMargin', 'format'=>'ratio'   ,'col'=>3],
                ['title'=>'3PL'  ,'field'=>'use3pl', 'format'=>'isYn'   ,'col'=>3],
                ['title'=>'폐쇄몰','field'=>'useMall', 'format'=>'isYn'      ,'col'=>3],
            ],
            'defaultRowspan' => 1,
            'masterSteper' => '',
            'masterInput' => '',
            'defaultSort' => 'P2,desc',
            //'stepCondition' => "90",
            //'viewCondition' => "90",
        ];
        return $this->setListColWidth($listSetupData);
    }

    /**
     * 10. 진행준비
     * @return mixed
     */
    public function setList10(){
        $listSetupData = [
            'list' => [
                ['title'=>'등록일','col'=>6],
                ['title'=>'연도/시즌','col'=>4],
                ['title'=>'타입<br>계약형태','col'=>5],
                ['title'=>'고객사/프로젝트번호','col'=>15],
                ['title'=>'희망 납기','col'=>7],
                ['title'=>'발주 D/L','col'=>5],
                ['title'=>'매출규모','col'=>5],
                ['title'=>'스타일','col'=>15],
                ['title'=>'미팅정보' ,'field'=>'meetingInfo'             , 'open'=>'mix8' ,'div'=>['meetingInfoExpectedDt','meetingInfoMemo'], 'col'=> 10 ],
                ['title'=>'디자인'  ,'field'=>'designAgree'              , 'open'=>'mix9' ,'div'=>['designAgree','designAgreeMemo']],
                ['title'=>'생산' ,'field'=>'qcAgree'                 , 'open'=>'mix10','div'=>['qcAgree','qcAgreeMemo']],
                ['title'=>'내부미팅' ,'field'=>'allAgree'  , 'open'=>'mix11','div'=>['allAgreeExpectedDt','allAgreeMemo']],
                //['title'=>'협의 완료' ,'field'=>'allAgree2'     , 'open'=>'mix12','div'=>['allAgree2ExpectedDt','allAgree2Memo']],
            ],
            'defaultRowspan' => 1,
            'masterSteper' => '영업',
            'masterInput' => '영업',
        ];
        return $this->setListColWidth($listSetupData);
    }

    /**
     * 15. 협상단계
     * @return mixed
     */
    public function setList15(){
        $listSetupData = [
            'list' => [
                ['title'=>'등록일','col'=>6],
                ['title'=>'연도/시즌','col'=>4],
                ['title'=>'타입<br>계약형태','col'=>5],
                ['title'=>'고객사/프로젝트번호','col'=>15],
                ['title'=>'희망 납기','col'=>7],
                ['title'=>'발주 D/L','col'=>5],
                ['title'=>'매출규모','col'=>5],
                ['title'=>'스타일','col'=>15],
                ['title'=>'사업계획' ,'field'=>'bizPlanYnKr', 'col'=>6],
                ['title'=>'협상 단계 메모' ,'field'=>'negoData', 'class'=>'text-left pdl10'],
            ],
            'defaultRowspan' => 1,
            'masterSteper' => '영업',
            'masterInput' => '영업',
        ];
        return $this->setListColWidth($listSetupData);
    }

    /**
     * 16. 고객사미팅
     * @return mixed
     */
    public function setList16(){
        $listSetupData = [
            'list' => [
                ['title'=>'등록일','col'=>6],
                ['title'=>'연도/시즌','col'=>4],
                ['title'=>'타입<br>계약형태','col'=>5],
                ['title'=>'고객사/프로젝트번호','col'=>15],
                ['title'=>'희망 납기','col'=>7],
                ['title'=>'발주 D/L','col'=>5],
                ['title'=>'매출규모','col'=>5],
                ['title'=>'스타일','col'=>15],

                ['title'=>'미팅정보'    ,'field'=>'meetingInfo'     ,'open'=>'mix8' ,'div'=>['meetingInfoExpectedDt','meetingInfoMemo'], 'col'=> 10 ],
                ['title'=>'참석자'      ,'field'=>'meetingMember'   , 'open'=>'mix13','div'=>['meetingMemberMemo','meetingMemberMemo']],
                ['title'=>'고객 안내일' ,'field'=>'custMeetingInform' , 'open'=>'mix15','div'=>['custMeetingInformExpectedDt','custMeetingInformExpectedDt']],
                ['title'=>'미팅보고서'  ,'field'=>'meetingReport'    , 'open'=>'mix14','div'=>['meetingReport','meetingReport']],
            ],
            'defaultRowspan' => 1,
            'masterSteper' => '영업',
            'masterInput' => '영업',
        ];
        return $this->setListColWidth($listSetupData);
    }

    /**
     * 20. 기획
     * @return mixed
     */
    public function setList20(){
        $parentList = $this->setList31();
        $parentList['list'][13]['approval'] = 'planConfirm';
        $parentList['list'][14]['completeBlank'] = true;
        $parentList['list'][15]['completeBlank'] = true;
        $parentList['list'][16]['completeBlank'] = true;
        $parentList['list'][17]['completeBlank'] = true;
        $parentList['viewCondition'] = "20";
        return $parentList;
    }

    /**
     * 30. 제안
     * @return mixed
     */
    public function setList30(){
        $parentList = $this->setList31();
        $parentList['list'][13]['approval'] = 'planConfirm'; //n:대기  r:요청  p  f
        $parentList['list'][14]['approval'] = 'proposalConfirm';
        $parentList['list'][17]['completeBlank'] = true;
        $parentList['viewCondition'] = "30";
        return $parentList;
    }

    /**
     * 31. 제안서확정
     */
    public function setList31(){
        $listSetupData = [
            'list' => [
                0=>['title'=>'등록일','col'=>4],
                1=>['title'=>'연도/시즌','col'=>3],
                2=>['title'=>'타입<br>계약형태','col'=>5],
                3=>['title'=>'고객사/프로젝트번호','col'=>14],
                4=>['title'=>'희망 납기','col'=>5],
                5=>['title'=>'발주 D/L','col'=>5],
                6=>['title'=>'매출규모','col'=>5],
                7=>['title'=>'스타일','col'=>14],
                8=>['title'=>'퀄리티'   ,'col'=>4],
                9=>['title'=>'BT'   ,'col'=>4],
                10=>['title'=>'구분'       ,'field'=>'urgency'],
                11=>['title'=>'담당자'     ,'field'=>'manager'],
                12=>['title'=>'스케줄'     ,'field'=>'split'],
                13=>['title'=>'기획'       ,'field'=>'plan'    ,'split'=>true       ,'period'=>'planExpected','periodTitle'=>'기획 예정일', 'fileDiv'=>'filePlan'],
                14=>['title'=>'제안서'     ,'field'=>'proposal','split'=>true       ,'period'=>'proposalExpected','periodTitle'=>'제안 예정일', 'fileDiv'=>'fileProposal'],
                //15=>['title'=>'가견적'     ,'field'=>'estimate','split'=>true       ,'period'=>'estimateExpected','periodTitle'=>'가견적 예정일'],
                16=>['title'=>'제안서<br>발송' ,'field'=>'custInform','split'=>true  ,'period'=>'custInformExpected','periodTitle'=>'제안서안내 예정일'],
                17=>['title'=>'고객제안서<br>확정예정','field'=>'custProposalConfirm','split'=>true,'period'=>'custProposalExpected','periodTitle'=>'제안서 확정 예정일'],
            ],
            'defaultRowspan' => 2,
            'masterSteper' => '영업',
            'masterInput' => '영업',
            'stepCondition' => "20,30,31",
            'viewCondition' => "31",
            'fileCondition' => "true",
        ];

        $listSetupData['list'][13]['approval'] = 'planConfirm';
        $listSetupData['list'][14]['approval'] = 'proposalConfirm';

        return $this->setListColWidth($listSetupData);
    }

    /**
     * 40. 샘플제안
     * @return mixed
     */
    public function setList40(){
        $listSetupData = [
            'list' => [
                0 => ['title' => '등록일', 'col' => 3.5],
                1 => ['title' => '연도/시즌', 'col' => 3],
                2 => ['title' => '타입<br>계약형태', 'col' => 5],
                3 => ['title' => '고객사/프로젝트번호', 'col' => 13],
                4 => ['title' => '희망 납기', 'col' => 5],
                5 => ['title' => '발주 D/L', 'col' => 5],
                6 => ['title' => '매출규모', 'col' => 5],
                7 => ['title' => '스타일', 'col' => 13],
                8 => ['title' => '퀄리티', 'col' => 4],
                9 => ['title' => 'BT', 'col' => 4],
                10 => ['title' => '구분', 'field' => 'urgency'],
                11 => ['title' => '담당자', 'field' => 'manager'],
                12 => ['title' => '스케줄', 'field' => 'split'],
                13 => ['title' => '샘플<br>지시서', 'field' => 'sampleOrder', 'split' => true, 'period' => 'sampleOrderExpectedDt', 'periodTitle' => '샘플지시서 예정일', 'col' => 4],
                14 => ['title' => '샘플<br>투입일', 'field' => 'sampleIn', 'split' => true, 'period' => 'sampleInExpectedDt', 'periodTitle' => '샘플투입일', 'col' => 4],
                15 => ['title' => '샘플<br>완료일', 'field' => 'sampleOut', 'split' => true, 'period' => 'sampleOutExpectedDt', 'periodTitle' => '샘플완료 예정일', 'col' => 4],
                16 => ['title' => '샘플<br>리뷰서', 'field' => 'sampleReview', 'split' => true, 'period' => 'sampleReviewExpectedDt', 'periodTitle' => '샘플리뷰서 예정일', 'col' => 4],

                18 => ['title' => '생산가<br>(매입가)', 'field' => 'cost', 'split' => true, 'period' => 'costExpected', 'periodTitle' => '생산가 예정일', 'col' => 4, 'approval'=>'prdCostApproval' ] , //'costStatus:IMS_PROC_STATUS 결재라인 ?

                17 => ['title' => '판매가', 'field' => 'salePrice', 'split' => true, 'period' => 'salePriceExpected', 'periodTitle' => '판매가 예정일', 'col' => 4, 'approval'=>'prdPriceApproval' ], //nrpf X => 0미진행,1진행,2완료 ( priceStatus:IMS_PROC_STATUS )
                19 => ['title' => '샘플발송', 'field' => 'custSampleInform', 'split' => true, 'period' => 'custSampleInformExpected', 'periodTitle' => '샘플발송 예정일'],
                20 => ['title' => '고객샘플<br>확정', 'field' => 'custSampleConfirm', 'split' => true, 'period' => 'custSampleConfirmExpected', 'periodTitle' => '고객샘플 확정 예정일', 'completeBlank'=>true],
            ],
            'defaultRowspan' => 2,
            'masterSteper' => '영업',
            'masterInput' => '영업,디자인,QC',
            'stepCondition' => "40",
            'viewCondition' => "40",
            'fileCondition' => "",
        ];
        return $this->setListColWidth($listSetupData);
    }

    /**
     * 41. 샘플확정
     */
    public function setList41(){
        $rslt = $this->setList40();
        unset($rslt['list'][20]['completeBlank']);
        $rslt['masterInput'] = '영업';
        $rslt['stepCondition'] = '41';
        $rslt['viewCondition'] = '41';
        return $rslt;
    }

    /** 50. 발주대기
     *
     */
    public function setList50(){
        $listSetupData = [
            'list' => [
                ['title'=>'등록일','col'=>4],
                ['title'=>'연도/시즌','col'=>3],
                ['title'=>'타입<br>계약형태','col'=>5],
                ['title'=>'고객사/프로젝트번호','col'=>14],
                ['title'=>'희망 납기','col'=>5],
                ['title'=>'발주 D/L','col'=>5],
                ['title'=>'매출규모','col'=>5],
                ['title'=>'스타일','col'=>14],
                ['title'=>'퀄리티'   ,'col'=>4],
                ['title'=>'BT'   ,'col'=>4],
                ['title'=>'구분'       ,'field'=>'urgency'],
                ['title'=>'담당자'     ,'field'=>'manager'],
                ['title'=>'스케줄'             ,'field'=>'split'],
                ['title'=>'생산가<br>(매입가)'  ,'field'=>'cost'       ,'split'=>true  ,'period'=>'costExpected'     ,'periodTitle'=>'생산가 예정일', 'approval'=>'prdCostApproval','col'=>4],
                ['title'=>'판매가'             ,'field'=>'salePrice'  ,'split'=>true  ,'period'=>'salePriceExpected','periodTitle'=>'판매가 예정일', 'approval'=>'prdPriceApproval','col'=>4],
                ['title'=>'고객발주'  ,'field'=>'custOrder'  ,'split'=>true       ,'period'=>'custOrderExpectedDt'   ,'periodTitle'=>'고객예상발주일'],
                ['title'=>'대기사유'   ,'field'=>'customerWait'       ,'split'=>false ,'col'=>10 ],
                ['title'=>'샘플회수'  ,'field'=>'custSampleInform'    ,'split'=>true  ,'period'=>'custSampleInformExpected','periodTitle'=>'샘플발송 예정일','col'=>4],
            ],
            'defaultRowspan' => 2,
            'masterSteper' => '영업',
            'masterInput' => '영업',
            'stepCondition' => "50",
            'viewCondition' => "50",
            'fileCondition' => "",
        ];
        return $this->setListColWidth($listSetupData);
    }

    /** 60. 발주
     *
    */
    public function setList60(){
        $listSetupData = [
            'list' => [
                ['title'=>'등록일','col'=>4],
                ['title'=>'연도/시즌','col'=>3],
                ['title'=>'타입<br>계약형태','col'=>5],
                ['title'=>'고객사/프로젝트번호','col'=>14],
                ['title'=>'희망 납기','col'=>5],
                ['title'=>'발주 D/L','col'=>5],
                ['title'=>'매출규모','col'=>5],
                ['title'=>'스타일','col'=>14],
                ['title'=>'퀄리티'   ,'col'=>4],
                ['title'=>'BT'   ,'col'=>4],
                ['title'=>'구분'       ,'field'=>'urgency'],
                ['title'=>'담당자'     ,'field'=>'manager'],
                ['title'=>'스케줄'     ,'field'=>'split'],
                ['title'=>'생산가<br>(매입가)'     ,'field'=>'cost'            ,'split'=>true  ,'period'=>'costExpected'     ,'periodTitle'=>'생산가 예정일', 'approval'=>'prdCostApproval',  'col'=>4],
                ['title'=>'판매가'    ,'field'=>'salePrice'       ,'split'=>true  ,'period'=>'salePriceExpected','periodTitle'=>'판매가 예정일', 'approval'=>'prdPriceApproval','col'=>4],
                ['title'=>'고객사<br>발주'       ,'field'=>'custOrder'  ,'split'=>true       ,'period'=>'custOrderCompleteDt'   ,'periodTitle'=>'고객사 발주일','col'=>4],
                ['title'=>'작지/사양서'          ,'field'=>'order'      ,'split'=>true       ,'period'=>'orderExpectedDt'    ,'periodTitle'=>'사양서','col'=>4, 'approval'=>'prdConfirmApproval', 'fileDiv'=>'fileConfirm' ],
                ['title'=>'사양서<br>발송'       ,'field'=>'custSpec'   ,'split'=>true       ,'period'=>'custSpecExpectedDt'    ,'periodTitle'=>'사양서 발송일','col'=>4],
                ['title'=>'사양서<br>확정'       ,'field'=>'custSpecConfirm'   ,'split'=>true       ,'period'=>'custSpecConfirmExpectedDt'    ,'periodTitle'=>'사양서 확정일','col'=>4],
                ['title'=>'발주<br>확정'         ,'field'=>'orderConfirm'   ,'split'=>true       ,'period'=>'orderExpectedDt'    ,'periodTitle'=>'발주 확정','col'=>4],
            ],
            'defaultRowspan' => 2,
            'masterSteper' => 'QC',
            'masterInput' => '영업,디자인,QC',
            'stepCondition' => "60",
            'viewCondition' => "60,90",
            'fileCondition' => "true",
        ];
        return $this->setListColWidth($listSetupData);
    }


    /*

                            프로젝트 View 및 리스트 일정수정 화면에서 보는 정보 설정

    */
    //10 협상단계
    public function setupStep15(){
        //,'custOrderConfirmExpectedDt','custOrderConfirmCompleteDt','Short'
        return [
            'list' => [
                ['등록일',5],
                ['시즌',5],
                ['프로젝트 타입',5],
                ['고객사',18],
                ['희망 납기',5],
                ['매출규모',5],
                ['계약 형태',5],
                ['스타일',15],
                ['메모',30,'negoMemo','negoMemo','Short','nego'],
            ]
        ];
    }

    //10 진행준비
    public function setupStep10(){
        //,'custOrderConfirmExpectedDt','custOrderConfirmCompleteDt','Short'
        return [
            'list' => [
                ['등록일',5],
                ['시즌',5],
                ['프로젝트 타입',5],
                ['고객사',18],
                ['희망 납기',5],
                ['매출규모',5],
                ['계약 형태',5],
                ['스타일',15],
                ['미팅 정보',8,'meetingInfoExpectedDt','meetingInfoMemo','Short','meetingInfo'],
                ['디자인',6,'designAgreeMemo','','','designAgree'],
                ['생산',6,'qcAgreeMemo','','','qcAgree'],
                ['유관 부서 협의',7,'allAgreeExpectedDt','allAgreeMemo','Short','allAgree'],
                ['협의 완료',10,'allAgreeCompleteDt','allAgreeEtcMemo','Short','allAgree'],
            ]
        ];
    }

    //16 고객사 미팅
    public function setupStep16(){
        return [
            'list' => [
                ['등록일',5],
                ['시즌',5],
                ['프로젝트 타입',5],
                ['고객사',18],
                ['희망 납기',5],
                ['매출규모',5],
                ['계약 형태',5],
                ['스타일',15],
                ['구분',5],
                ['미팅 일자',6],
                ['참석자',6],
                ['미팅보고서',6],
                ['고객 안내일',6],
            ]
        ];
    }

    //20. 기획
    public function setupStep20(){
        return [
            'list' => [
                ['등록일',4],
                ['연도/시즌',4],
                ['프로젝트 타입',4],
                ['고객사/프로젝트번호',18],
                ['희망 납기',4],
                ['매출규모',4],
                ['계약형태',4],
                ['스타일',18],
                ['구분',4],
                ['담당자',4],
                ['스케줄',4],
                ['Q/B 확정',4,'qbExpectedDt','qbCompleteDt','Short','qb'],
                ['기획',4,'planDt','planEndDt','Short2'],
                ['제안서',4,'proposalDt','proposalEndDt','Short2'],
                ['가견적',4,'estimateExpectedDt','estimateCompleteDt','Short','estimate'],
                ['고객제안서<br>확정예정',4,'custProposalConfirmExpectedDt','custProposalConfirmCompleteDt','Short','custProposalConfirm'],
                ['발주 D/L',4,'customerOrderDeadLineShort2','orderCompleteCompleteDtShort'],
                ['제안서 안내',4,'custInformExpectedDt','custInformCompleteDt','Short','custInform'],
            ]
        ];
    }
    //['Q/B확정일',4,'qbExpectedDt','qbCompleteDt','Short','qb','req'],
    
    //40. 샘플
    public function setupStep40(){
        return [
            'list' => [
                ['등록일',4],
                ['시즌',4],
                ['프로젝트 타입',4],
                ['고객사',14],
                ['희망 납기',4],
                ['매출규모',4],
                ['계약 형태',4],
                ['스타일',15],
                ['구분',4],
                ['담당자',4],
                ['스케줄',4],

                ['샘플지시서',4,'sampleOrderExpectedDt','sampleOrderCompleteDt','Short'],
                ['샘플투입일',4,'sampleInExpectedDt','sampleInCompleteDt','Short'],
                ['샘플완료일',4,'sampleOutExpectedDt','sampleOutCompleteDt','Short'],
                ['샘플리뷰서',4,'sampleReviewExpectedDt','sampleReviewCompleteDt','Short'],
                ['생산가<br>(매입가)',4,'costExpectedDt','costCompleteDt','Short'],
                ['고객사<br>확정일',4,'custSampleConfirmExpectedDt','custSampleConfirmCompleteDt','Short'],
                ['발주D/L',4,'customerOrderDeadLine','orderCompleteCompleteDt','Short'], //확인 customerOrderDeadLineShort2
                ['Q/B 확정일',4,'qbExpectedDt','qbCompleteDt','Short'],
            ]//['제안서 안내',4,'custInformExpectedDt','custInformCompleteDt','Short','custInform'],
        ];
    }
    
    //40. 발주대기
    public function setupStep50(){
        return [
            'list' => [
                ['등록일',4,''],
                ['시즌',4],
                ['프로젝트 타입',4],
                ['고객사',14],
                ['고객희망 납기일',4],
                ['매출규모',4],
                ['계약 형태',4],
                ['스타일',14],
                ['구분',4],
                ['담당자',4],
                ['스케줄',4],
                ['Q/B 확정일',4,'qbExpectedDt','qbCompleteDt','Short'],
                ['가발주',4,'fakeOrderExpected','fakeOrderComplete','Short'],
                ['고객예상발주일',4,'custOrderExpectedDt','custOrderCompleteDt','Short'],
                ['대기유형',16,'customerWaitMemo','',''],
                ['발주D/L',4,'customerOrderDeadLine','orderCompleteCompleteDt','Short'],
            ]
        ];
    }

    //60. 발주대기
    public function setupStep60(){
        return [
            'list' => [
                ['등록일',4,'','',],
                ['시즌',4,'','',],
                ['프로젝트 타입',4,'','',],
                ['고객사',16,'','',],
                ['고객희망 납기일',4,'','',],
                ['매출규모',4,'','',],
                ['계약 형태',4,'','',],
                ['스타일',16,'','',],
                ['구분',4,'','',],
                ['담당자',4,'','',],
                ['스케줄',4,'','',],
                ['Q/B확정일',4,'qbExpectedDt','qbCompleteDt','Short','qb','req'],
                ['가발주',4,'fakeOrderExpectedDt','fakeOrderCompleteDt','Short','fakeOrder'],
                ['고객사발주일',4,'custOrderExpectedDt','custOrderCompleteDt','Short','custOrder','req'],
                ['사양서발송일',4,'custSpecExpectedDt','custSpecCompleteDt','Short','custSpec','req'],
                ['사양서확정일',4,'custSpecConfirmExpectedDt','custSpecConfirmCompleteDt','Short','custSpecConfirm','req'],
                ['발주서완료일',4,'orderExpectedDt','orderCompleteDt','Short','order','req'],
                ['발주확정일',4,'orderConfirmExpectedDt','orderConfirmCompleteDt','Short','orderConfirm','req'],
            ]
        ];
    }

}