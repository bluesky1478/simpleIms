<?php
/*use Component\Ims\ImsCodeMap;use Component\Ims\ImsJsonSchema;

public function tableImsProject(): array
{
    return [
        ['val' => 'sno', 'typ' => 'i', 'def' => null, 'name' => '일련번호'],
        ['val' => 'projectName', 'typ' => 's', 'def' => null, 'name' => '프로젝트별칭', 'strip' => true],
        ['val' => 'projectNo', 'typ' => 'i', 'def' => null, 'name' => '프로젝트번호'],
        ['val' => 'projectType', 'typ' => 'i', 'def' => null, 'name' => '프로젝트타입', 'code' => ImsCodeMap::PROJECT_TYPE ],
        ['val' => 'projectStatus', 'typ' => 'i', 'def' => null, 'name' => '프로젝트상태', 'code' => ImsCodeMap::PROJECT_STATUS ],
        ['val' => 'customerSno', 'typ' => 'i', 'def' => null, 'name' => '고객사', 'required' => true],
        ['val' => 'customerSize', 'typ' => 's', 'def' => null, 'name' => '매출규모'],
        ['val' => 'produceCompanySno', 'typ' => 'i', 'def' => null, 'name' => '생산처'],
        ['val' => 'produceType', 'typ' => 'i', 'def' => 0, 'name' => '제작형태'],
        ['val' => 'projectMemo', 'typ' => 's', 'def' => null, 'name' => '프로젝트메모(고객사 요청사항)', 'strip' => true],
        ['val' => 'salesManagerSno', 'typ' => 'i', 'def' => null, 'name' => '영업담당자'],
        ['val' => 'salesStartDt', 'typ' => 's', 'def' => null, 'name' => '업무시작일'],


        //스케쥴 관리 관련 ------------------------------
        ['val' => 'customerOrderDt', 'typ' => 's', 'def' => null, 'name' => '고객발주일'], //★ 아소트확정일과 동일해야함

        //제안서 (상세 스케쥴을 안쓰면 가능)
        ['val' => 'proposalDt', 'typ' => 's', 'def' => null, 'name' => '제안서예정일'],
        ['val' => 'proposalEndDt', 'typ' => 's', 'def' => null, 'name' => '제안서완료일'],
        ['val' => 'proposalConfirm', 'typ' => 's', 'def' => 'n', 'name' => '제안서승인'],

        //생산가
        ['val' => 'prdCostApproval', 'typ' => 's', 'def' => 'n', 'name' => '생산가 결재 승인'],
        //판매가
        ['val' => 'prdPriceApproval', 'typ' => 's', 'def' => 'n', 'name' => '판매가 결재 승인'],

        //이노버 발주
        ['val' => 'customerOrderDeadLine', 'typ' => 's', 'def' => null, 'name' => '발주DL'], //★ 발주예정
        ['val' => 'msOrderDt', 'typ' => 's', 'def' => null, 'name' => '이노버발주일'],  //★ 실제 발주일 -> 발주완료시

        ['val' => 'msDeliveryDt', 'typ' => 's', 'def' => null, 'name' => '이노버납기일'],

        ['val' => 'customerDeliveryDt', 'typ' => 's', 'def' => null, 'name' => '고객납기일'],
        ['val' => 'customerDeliveryDtConfirmed', 'typ' => 's', 'def' => 'y', 'name' => '고객납기확정'],
        ['val' => 'customerDeliveryDtStatus', 'typ' => 'i', 'def' => 0, 'name' => '납기확보상태'],
        ['val' => 'customerDeliveryDtStatus2', 'typ' => 's', 'def' => 'n', 'name' => '납기확정상태'],
        //-- ------------------------------


        ['val' => 'bidType', 'typ' => 's', 'def' => null, 'name' => '입찰구분'],
        ['val' => 'bidType2', 'typ' => 's', 'def' => 'single', 'name' => '입찰구분2', 'code' => ImsCodeMap::BID_TYPE],
        ['val' => 'produceDeliveryDt', 'typ' => 's', 'def' => null, 'name' => '생산처납기일'],
        ['val' => 'produceNational', 'typ' => 's', 'def' => '', 'name' => '생산처국가'],
        ['val' => 'confirmed', 'typ' => 's', 'def' => 'n', 'name' => '고객확정'],
        ['val' => 'bid', 'typ' => 's', 'def' => null, 'name' => '입찰'],
        ['val' => 'recommend', 'typ' => 'i', 'def' => null, 'name' => '제안형태'],
        ['val' => 'recommendDt', 'typ' => 's', 'def' => null, 'name' => '제안마감일'],
        ['val' => 'designManagerSno', 'typ' => 'i', 'def' => null, 'name' => '디자인담당자'],
        ['val' => 'designEndDt', 'typ' => 's', 'def' => null, 'name' => '디자인마감일'],

        ['val' => 'planDt', 'typ' => 's', 'def' => null, 'name' => '기획서예정일'],
        ['val' => 'planEndDt', 'typ' => 's', 'def' => null, 'name' => '기획서완료일'],

        ['val' => 'planConfirm', 'typ' => 's', 'def' => 'n', 'name' => '기획서승인'], //n 대기, r 승인요청 ,p승인 ,f반려 ?
        ['val' => 'planMemo', 'typ' => 's', 'def' => null, 'name' => '기획서비고', 'strip' => true],
        ['val' => 'sampleStartDt', 'typ' => 's', 'def' => null, 'name' => '샘플예정일'],
        ['val' => 'sampleEndDt', 'typ' => 's', 'def' => null, 'name' => '샘플완료일'],
        ['val' => 'sampleConfirm', 'typ' => 's', 'def' => 'n', 'name' => '샘플승인'],

        ['val' => 'proposalMemo', 'typ' => 's', 'def' => null, 'name' => '제안서비고', 'strip' => true],
        ['val' => 'workDt', 'typ' => 's', 'def' => null, 'name' => '작지예정일'],
        ['val' => 'workEndDt', 'typ' => 's', 'def' => null, 'name' => '작지완료일'],
        ['val' => 'workConfirm', 'typ' => 's', 'def' => 'n', 'name' => '작지승인'],
        ['val' => 'workMemo', 'typ' => 's', 'def' => null, 'name' => '작지메모', 'strip' => true],
        ['val' => 'prdEndDt', 'typ' => 's', 'def' => null, 'name' => 'QC업무마감'],
        ['val' => 'sampleManagerSno', 'typ' => 'i', 'def' => null, 'name' => '샘플작업자'],
        ['val' => 'sampleFactorySno', 'typ' => 'i', 'def' => null, 'name' => '샘플실'],
        ['val' => 'sampleCost', 'typ' => 'i', 'def' => 0, 'name' => '샘플비용'],
        ['val' => 'sampleReturnInfo', 'typ' => 's', 'def' => null, 'name' => '샘플회수정보'],
        ['val' => 'sampleCount', 'typ' => 'i', 'def' => 0, 'name' => '샘플제작횟수'],
        ['val' => 'sampleMemo', 'typ' => 's', 'def' => null, 'name' => '샘플메모', 'strip' => true],

        ['val' => 'customerOrder2Confirm', 'typ' => 's', 'def' => 'n', 'name' => '고객발주확정'],
        ['val' => 'customerOrder2ConfirmDt', 'typ' => 's', 'def' => null, 'name' => '고객발주확정일자'],
        ['val' => 'customerEstimateConfirm', 'typ' => 's', 'def' => 'n', 'name' => '고객견적서승인'],
        ['val' => 'customerEstimateConfirmDt', 'typ' => 's', 'def' => null, 'name' => '고객견적서승인일자'],
        ['val' => 'customerWaitDt', 'typ' => 's', 'def' => null, 'name' => '고객승인대기일자'],
        ['val' => 'customerWaitMemo', 'typ' => 's', 'def' => null, 'name' => '고객승인대기메모', 'strip'=>true],
        ['val' => 'customerSaleConfirm', 'typ' => 's', 'def' => 'n', 'name' => '판매구매확정'],
        ['val' => 'customerSaleConfirmDt', 'typ' => 's', 'def' => null, 'name' => '판매구매확정일자'],
        ['val' => 'addedInfo', 'typ' => 's', 'def' => null, 'name' => '기타정보', 'json' => true, 'scheme'=>ImsJsonSchema::PROJECT_ADDINFO ],

        ['val' => 'projectYear', 'typ' => 's', 'def' => null, 'name' => '프로젝트 연도'],
        ['val' => 'projectSeason', 'typ' => 's', 'def' => null, 'name' => '프로젝트 시즌'],
        ['val' => 'fabricStatus', 'typ' => 'i', 'def' => null, 'name' => '원단확보상태'],
        ['val' => 'fabricStatusMemo', 'typ' => 's', 'def' => null, 'name' => '원단메모', 'strip' => true],
        ['val' => 'fabricNational', 'typ' => 's', 'def' => '0', 'name' => '원단국가'],

        ['val' => 'btStatus', 'typ' => 'i', 'def' => 0, 'name' => 'BT처리상태'], //BT 처리 상태.
        ['val' => 'workStatus', 'typ' => 'i', 'def' => 0, 'name' => '작지처리상태'],
        ['val' => 'costStatus', 'typ' => 'i', 'def' => 0, 'name' => '생산가확정처리상태'],
        ['val' => 'estimateStatus', 'typ' => 'i', 'def' => 0, 'name' => '견적처리상태'],

        //['val' => 'costConfirm', 'typ' => 'i', 'def' => 0, 'name' => ''],
        //['val' => 'costConfirm', 'typ' => 'i', 'def' => 0, 'name' => ''],

        ['val' => 'orderStatus', 'typ' => 'i', 'def' => null, 'name' => '가발주처리상태'],
        ['val' => 'productionStatus', 'typ' => 'i', 'def' => 0, 'name' => '생산상태'],
        ['val' => 'packingYn', 'typ' => 's', 'def' => 'n', 'name' => '분류패킹여부'],
        ['val' => 'deliveryMethod', 'typ' => 's', 'def' => null, 'name' => '납기방법', 'strip' => true],
        ['val' => 'deliveryCostMemo', 'typ' => 's', 'def' => null, 'name' => '배송비용협의사항', 'strip' => true],
        ['val' => 'syncProduct', 'typ' => 's', 'def' => 'y', 'name' => '프로젝트 상품 연동상태'],
        ['val' => 'use3pl', 'typ' => 's', 'def' => 'n', 'name' => '3PL 사용 여부'],
        ['val' => 'useMall', 'typ' => 's', 'def' => 'n', 'name' => '폐쇄몰 사용 여부'],

        ['val' => 'priceStatus', 'typ' => 'i', 'def' => 0, 'name' => '판매가확정상태'],
        ['val' => 'bizPlanYn', 'typ' => 's', 'def' => '', 'name' => '사업계획 포함여부'],

        ['val' => 'prdConfirmApproval', 'typ' => 's', 'def' => 'n', 'name' => '사양서 결재 승인'],

        ['val' => 'projectReady', 'typ' => 's', 'def' => 'y', 'name' => '프로젝트 준비 완료'],
        ['val' => 'srcProjectSno', 'typ' => 'i', 'def' => null, 'name' => '원본프로젝트'],

        ['val' => 'nextSeason', 'typ' => 'i', 'def' => 0, 'name' => '다음시즌리오더상태'],

        ['val' => 'assortMemo', 'typ' => 's', 'def' => null, 'name' => '아소트 비고', 'strip' => true],
        //아소트 입력 URL 발신정보
        ['val' => 'assortReceiver', 'typ' => 's', 'def' => null, 'name' => '아소트 입력 URL 수신자'],
        ['val' => 'assortEmail', 'typ' => 's', 'def' => null, 'name' => '아소트 입력 URL 수신 이메일'],
        ['val' => 'assortSendDt', 'typ' => 's', 'def' => '', 'name' => '아소트 입력URL 발신일 '],
        ['val' => 'assortApproval', 'typ' => 's', 'def' => 'n', 'name' => '아소트 결재 승인'], //최종상태
        //아소트 승인/확정 정보 ( admin-custom.js 발췌 )
        // 'n' : {name:"준비",'color':''},
        // 'r' : {name:"고객입력요청",'color':'sl-orange'},
        // 'f' : {name:"고객입력완료(검토필)",'color':'sl-blue'},
        // 'p' : {name:"완료",'color':'sl-green'},

        ['val' => 'assortApprovalName', 'typ' => 's', 'def' => '', 'name' => '아소트 입력 고객'],
        ['val' => 'assortApprovalManager', 'typ' => 's', 'def' => '', 'name' => '아소트 결정자'],
        ['val' => 'assortCustomerDt', 'typ' => 's', 'def' => '', 'name' => '아소트 고객 입력일자'],
        ['val' => 'assortManagerDt', 'typ' => 's', 'def' => '', 'name' => '아소트 결정일자 '],

        ['val' => 'customerOrderReceiver', 'typ' => 's', 'def' => null, 'name' => '아소트 입력 URL 수신자'], //추가
        ['val' => 'customerOrderEmail', 'typ' => 's', 'def' => null, 'name' => '아소트 입력 URL 수신 이메일'],
        ['val' => 'customerOrderSendDt', 'typ' => 's', 'def' => null, 'name' => '고객사양서발송일자'],
        ['val' => 'customerOrderConfirm', 'typ' => 's', 'def' => 'n', 'name' => '고객사양서확정상태'], //준비 요청 승인 반려
        ['val' => 'customerOrderConfirmDt', 'typ' => 's', 'def' => null, 'name' => '고객사양서확정일자'],
        ['val' => 'customerOrderConfirmManager', 'typ' => 's', 'def' => null, 'name' => '고객사양서확정자'],

        ['val' => 'mainApproval', 'typ' => 's', 'def' => 'n', 'name' => '작지 메인 결재 승인'],
        ['val' => 'markApproval', 'typ' => 's', 'def' => 'n', 'name' => '마크 결재 승인'],
        ['val' => 'careApproval', 'typ' => 's', 'def' => 'n', 'name' => '캐어라벨 결재 승인'],
        ['val' => 'specApproval', 'typ' => 's', 'def' => 'n', 'name' => '스펙 결재 승인'],
        ['val' => 'materialApproval', 'typ' => 's', 'def' => 'n', 'name' => '자재리스트 결재 승인'],
        ['val' => 'packingApproval', 'typ' => 's', 'def' => 'n', 'name' => '포장 결재 승인'],
        ['val' => 'batekApproval', 'typ' => 's', 'def' => 'n', 'name' => '바텍 결재 승인'], //n대기. r요청. p승인. f반려. x.없음

        ['val' => 'regManagerSno', 'typ' => 'i', 'def' => null, 'name' => '최초등록자'],
        ['val' => 'lastManagerSno', 'typ' => 'i', 'def' => null, 'name' => '마지막수정자'],
        ['val' => 'regDt', 'typ' => 's', 'def' => null, 'name' => '등록시간'],
        ['val' => 'modDt', 'typ' => 's', 'def' => null, 'name' => '수정시간'],

    ];
}



public static function tableImsProjectProduct(): array
    {
        return [
            ['val' => 'sno', 'typ' => 'i', 'def' => null, 'name' => '일련번호'],
            ['val' => 'projectSno', 'typ' => 'i', 'def' => null, 'name' => '프로젝트 번호'],
            ['val' => 'customerSno', 'typ' => 'i', 'def' => null, 'name' => '고객사 번호', 'required' => true],
            ['val' => 'styleCode', 'typ' => 's', 'def' => null, 'name' => '스타일 코드'],
            ['val' => 'addStyleCode', 'typ' => 's', 'def' => null, 'name' => '추가 스타일 코드'],
            ['val' => 'productName', 'typ' => 's', 'def' => null, 'name' => '제품명'],
            ['val' => 'prdYear', 'typ' => 'i', 'def' => null, 'name' => '생산년도'],
            ['val' => 'prdSeason', 'typ' => 's', 'def' => null, 'name' => '시즌'],
            ['val' => 'prdGender', 'typ' => 's', 'def' => null, 'name' => '성별', 'code' => ImsCodeMap::SEX_CODE],
            ['val' => 'prdStyle', 'typ' => 's', 'def' => null, 'name' => '스타일'],
            ['val' => 'prdColor', 'typ' => 's', 'def' => null, 'name' => '색상'],
            ['val' => 'produceType', 'typ' => 's', 'def' => 1, 'name' => '생산 구분', 'code' => ImsCodeMap::PRODUCE_TYPE],
            ['val' => 'produceCompanySno', 'typ' => 'i', 'def' => null, 'name' => '생산 업체'],
            ['val' => 'produceNational', 'typ' => 's', 'def' => null, 'name' => '생산국가'],
            ['val' => 'customerDeliveryDt', 'typ' => 's', 'def' => null, 'name' => '고객납기(스타일)'],
            ['val' => 'msDeliveryDt', 'typ' => 's', 'def' => null, 'name' => '이노버납기(스타일)'],
            ['val' => 'prdExQty', 'typ' => 'i', 'def' => null, 'name' => '기획 수량'],
            ['val' => 'prdMoq', 'typ' => 'i', 'def' => null, 'name' => '생산 MOQ'],
            ['val' => 'priceMoq', 'typ' => 'i', 'def' => null, 'name' => '단가 MOQ'],
            ['val' => 'addPrice', 'typ' => 'i', 'def' => null, 'name' => 'MOQ미달추가금'],
            ['val' => 'salePrice', 'typ' => 'i', 'def' => null, 'name' => '판매가'],
            ['val' => 'currentPrice', 'typ' => 'i', 'def' => null, 'name' => '현재 단가'],
            ['val' => 'targetPrice', 'typ' => 'i', 'def' => null, 'name' => '타겟 단가'],
            ['val' => 'targetPriceMax', 'typ' => 'i', 'def' => null, 'name' => '타겟 단가(최대)'],
            ['val' => 'targetPrdCost', 'typ' => 'i', 'def' => null, 'name' => '타겟 생산가'],

            //미사용 - 과거 원부자재 자료 (시작)
            ['val' => 'fabricCost', 'typ' => 'i', 'def' => 0, 'name' => '원자재 소계'],
            ['val' => 'subFabricCost', 'typ' => 'i', 'def' => 0, 'name' => '부자재 소계'],
            ['val' => 'laborCost', 'typ' => 'i', 'def' => 0, 'name' => '공임'],
            ['val' => 'marginCost', 'typ' => 'i', 'def' => 0, 'name' => '마진비용'],
            ['val' => 'dutyCost', 'typ' => 'i', 'def' => 0, 'name' => '물류및관세'],
            ['val' => 'managementCost', 'typ' => 'i', 'def' => null, 'name' => '관리비'],
            ['val' => 'memo', 'typ' => 's', 'def' => null, 'name' => '메모'],
            ['val' => 'sizeOption', 'typ' => 's', 'def' => null, 'name' => '사이즈', 'json' => true], //JSON
            ['val' => 'typeOption', 'typ' => 's', 'def' => null, 'name' => '타입', 'json' => true], //JSON

            ['val' => 'fabric', 'typ' => 's', 'def' => null, 'name' => '원단', 'json' => true], //JSON
            ['val' => 'subFabric', 'typ' => 's', 'def' => null, 'name' => '부자재', 'json' => true], //JSON
            ['val' => 'sizeSpec', 'typ' => 's', 'def' => null, 'name' => '사이즈스펙', 'json' => true], //JSON
            //미사용 - 과거 원부자재 자료. (끝)
            ['val' => 'fabricStatus', 'typ' => 'i', 'def' => 0, 'name' => '퀄리티 상태'],
            ['val' => 'fabricNational', 'typ' => 'i', 'def' => 0, 'name' => '퀄리티 제조국'],
            ['val' => 'btStatus', 'typ' => 'i', 'def' => 0, 'name' => 'BT처리상태'], //BT 처리 상태.

            ['val' => 'fileThumbnail', 'typ' => 's', 'def' => null, 'name' => '썸네일_기획'],
            ['val' => 'fileThumbnailWork', 'typ' => 's', 'def' => null, 'name' => '썸네일_작업'],
            ['val' => 'fileThumbnailReal', 'typ' => 's', 'def' => null, 'name' => '썸네일_실물'],
            ['val' => 'msMargin', 'typ' => 'i', 'def' => null, 'name' => '이노버마진'],
            ['val' => 'fabricCount', 'typ' => 'i', 'def' => null, 'name' => '원단수량'],
            ['val' => 'btCount', 'typ' => 'i', 'def' => null, 'name' => 'BT확인수량'],
            ['val' => 'sampleConfirmSno', 'typ' => 'i', 'def' => null, 'name' => '확정샘플'], //샘플 중 확정 번호 -1은 샘플 없이 진행

            ['val' => 'estimateCost', 'typ' => 'i', 'def' => null, 'name' => '가견적'],     //가견적
            ['val' => 'estimateCount', 'typ' => 'i', 'def' => null, 'name' => '견적수량'],
            ['val' => 'estimateConfirmSno', 'typ' => 'i', 'def' => null, 'name' => '가견적 선택여부'],     //가견적 확정 여부
            ['val' => 'estimateConfirmManagerSno', 'typ' => 'i', 'def' => null, 'name' => '선택한 사람'], //가견적 확정 여부
            ['val' => 'estimateConfirmDt', 'typ' => 's', 'def' => null, 'name' => '선택일자'],         //가견적 확정 일자
            ['val' => 'estimateStatus', 'typ' => 'i', 'def' => 0, 'name' => '가견적 선택여부'],     //가견적 확정 여부

            ['val' => 'prdCost', 'typ' => 'i', 'def' => null, 'name' => '생산확정가'],
            ['val' => 'prdCount', 'typ' => 'i', 'def' => null, 'name' => '확정가수량'],
            ['val' => 'prdCostConfirmSno', 'typ' => 'i', 'def' => null, 'name' => '생산가 확정여부'],
            ['val' => 'prdCostConfirmManagerSno', 'typ' => 'i', 'def' => null, 'name' => '생산가 확정자'],
            ['val' => 'prdCostConfirmDt', 'typ' => 'i', 'def' => null, 'name' => '생산가 확정 일자'],
            ['val' => 'prdCostStatus', 'typ' => 'i', 'def' => 0, 'name' => '생산 확정가 상태'],

            ['val' => 'inlineStatus', 'typ' => 'i', 'def' => 0, 'name' => '인라인여부'],
            ['val' => 'inlineMemo', 'typ' => 's', 'def' => null, 'name' => '인라인메모'],

            ['val' => 'workStatus', 'typ' => 'i', 'def' => 0, 'name' => '작지상태'], // IMS_PROC_STATUS
            ['val' => 'productionStatus', 'typ' => 'i', 'def' => 0, 'name' => '생산상태'], // IMS_PROC_STATUS

            ['val' => 'priceConfirm', 'typ' => 's', 'def' => 'n', 'name' => '판매가 승인 상태'],
            ['val' => 'priceConfirmDt', 'typ' => 's', 'def' => null, 'name' => '판매가 확정일'],

            ['val' => 'priceApprovalName', 'typ' => 's', 'def' => null, 'name' => '승인자'],
            ['val' => 'priceCustConfirm', 'typ' => 's', 'def' => 'n', 'name' => '판매가 고객 승인'],
            ['val' => 'priceCustConfirmDt', 'typ' => 's', 'def' => null, 'name' => '판매가 고객 확정일'],

            ['val' => 'regManagerSno', 'typ' => 'i', 'def' => null, 'name' => '최초등록자'],
            ['val' => 'lastManagerSno', 'typ' => 'i', 'def' => null, 'name' => '마지막수정자'],
            ['val' => 'masterStyleSno', 'typ' => 'i', 'def' => 0, 'name' => '마스터 스타일 번호'],

            ['val' => 'assortMemo', 'typ' => 's', 'def' => null, 'name' => '아소트 비고', 'strip' => true],
            ['val' => 'assort', 'typ' => 's', 'def' => null, 'name' => '아소트 정보', 'json' => true],
            ['val' => 'assortConfirm', 'typ' => 's', 'def' => 'n', 'name' => '아소트 승인'], //Freez 필요. (메인도안 승인시 ? )
            ['val' => 'moq', 'typ' => 'i', 'def' => 0, 'name' => 'MOQ'],

            ['val' => 'addedInfo', 'typ' => 's', 'def' => null, 'name' => '스타일 추가 정보', 'json' => true, 'scheme'=>ImsJsonSchema::PRD_ADD_INFO],
            ['val' => 'styleProcType', 'typ' => 'i', 'def' => null, 'name' => '스타일 진행타입'],

            ['val' => 'sort', 'typ' => 'i', 'def' => 0, 'name' => '정렬'],
            ['val' => 'delFl', 'typ' => 's', 'def' => 'n', 'name' => '삭제여부'],
            ['val' => 'regDt', 'typ' => 's', 'def' => null, 'name' => '등록시간'],
            ['val' => 'modDt', 'typ' => 's', 'def' => null, 'name' => '수정시간'],
        ];
    }

*/?><!--



-->