<?php
namespace Component\Database;


trait DBNkIms
{
    /**
     * 원부자재
     * @return array[]
     */
    public static function tableImsStoredFabric(): array
    {
        return [
            ['val' => 'sno', 'typ' => 'i', 'def' => null, 'name' => '번호'],
            ['val' => 'regManagerSno', 'typ' => 'i', 'def' => null, 'name' => '등록자 일련번호'],
            ['val' => 'customerUsageSno', 'typ' => 'i', 'def' => 0, 'name' => '사용처고객 일련번호'],
            ['val' => 'fabricName', 'typ' => 's', 'def' => null, 'name' => '비축 자재명'],
            ['val' => 'fabricMix', 'typ' => 's', 'def' => null, 'name' => '혼용율'],
            ['val' => 'color', 'typ' => 's', 'def' => null, 'name' => '색상'],
            ['val' => 'regDt', 'typ' => 's', 'def' => null, 'name' => '등록시간'],
            ['val' => 'modDt', 'typ' => 's', 'def' => null, 'name' => '수정시간'],
            ['val' => 'delFl', 'typ' => 's', 'def' => 'n', 'name' => '삭제여부'],
        ];
    }

    /**
     * 원부자재 입고
     * @return array[]
     */
    public static function tableImsStoredFabricInput(): array
    {
        return [
            ['val' => 'sno', 'typ' => 'i', 'def' => null, 'name' => '번호'],
            ['val' => 'fabricSno', 'typ' => 'i', 'def' => null, 'name' => '원부자재(원단) 일련번호'],
            ['val' => 'regManagerSno', 'typ' => 'i', 'def' => null, 'name' => '등록자 일련번호'],
            ['val' => 'customerSno', 'typ' => 'i', 'def' => 0, 'name' => '소유고객 일련번호'],
            ['val' => 'unitPrice', 'typ' => 'i', 'def' => null, 'name' => '단가'],
            ['val' => 'inputQty', 'typ' => 'i', 'def' => null, 'name' => '입고수량'],
            ['val' => 'inputUnit', 'typ' => 's', 'def' => 'YD', 'name' => '단위(YD, EA, SET)'],
            ['val' => 'inputReason', 'typ' => 's', 'def' => null, 'name' => '입고사유'],
            ['val' => 'inputOwn', 'typ' => 'i', 'def' => 1, 'name' => '소유권구분(하나어패럴, 이노버, 고객)'],
            ['val' => 'inputLocation', 'typ' => 's', 'def' => null, 'name' => '저장위치'],
            ['val' => 'inputMemo', 'typ' => 's', 'def' => null, 'name' => '비고'],
            ['val' => 'regDt', 'typ' => 's', 'def' => null, 'name' => '등록시간'],
            ['val' => 'modDt', 'typ' => 's', 'def' => null, 'name' => '수정시간'],
            ['val' => 'inputDt', 'typ' => 's', 'def' => null, 'name' => '입고일자'],
            ['val' => 'expireDt', 'typ' => 's', 'def' => null, 'name' => '만료일자'],
            ['val' => 'delFl', 'typ' => 's', 'def' => 'n', 'name' => '삭제여부'],
        ];
    }

    /**
     * 원부자재 출고
     * @return array[]
     */
    public static function tableImsStoredFabricOut(): array
    {
        return [
            ['val' => 'sno', 'typ' => 'i', 'def' => null, 'name' => '번호'],
            ['val' => 'inputSno', 'typ' => 'i', 'def' => null, 'name' => '입고건 일련번호'],
            ['val' => 'regManagerSno', 'typ' => 'i', 'def' => null, 'name' => '등록자 일련번호'],
            ['val' => 'outQty', 'typ' => 'i', 'def' => null, 'name' => '출고수량'],
            ['val' => 'outReason', 'typ' => 's', 'def' => null, 'name' => '출고사유'],
            ['val' => 'regDt', 'typ' => 's', 'def' => null, 'name' => '등록시간'],
            ['val' => 'modDt', 'typ' => 's', 'def' => null, 'name' => '수정시간'],
            ['val' => 'delFl', 'typ' => 's', 'def' => 'n', 'name' => '삭제여부'],
        ];
    }

    //원부자재 분류
    public static function tableImsMaterialTypeDetail(): array
    {
        return [
            ['val' => 'sno', 'typ' => 'i', 'def' => null, 'name' => '번호'],
            ['val' => 'regManagerSno', 'typ' => 'i', 'def' => null, 'name' => '등록자 일련번호'],
            ['val' => 'materialTypeByDetail', 'typ' => 'i', 'def' => 1, 'name' => '등록타입'],
            ['val' => 'materialTypeText', 'typ' => 's', 'def' => null, 'name' => '품목구분명'],
            ['val' => 'regDt', 'typ' => 's', 'def' => null, 'name' => '등록시간'],
            ['val' => 'modDt', 'typ' => 's', 'def' => null, 'name' => '수정시간'],
        ];
    }
    //원부자재
    public static function tableImsMaterial(): array
    {
        return [
            ['val' => 'sno', 'typ' => 'i', 'def' => null, 'name' => '번호'],
            ['val' => 'groupSno', 'typ' => 'i', 'def' => 0, 'name' => '유사퀄리티(그룹) 일련번호'],
            ['val' => 'regManagerSno', 'typ' => 'i', 'def' => null, 'name' => '등록자 일련번호'],
            ['val' => 'buyerSno', 'typ' => 'i', 'def' => 0, 'name' => '매입처'],
            ['val' => 'materialType', 'typ' => 'i', 'def' => 1, 'name' => '자재타입'],
            ['val' => 'typeDetailSno', 'typ' => 'i', 'def' => 0, 'name' => '품목구분'],
            ['val' => 'code', 'typ' => 's', 'def' => null, 'name' => '품목코드'],
            ['val' => 'name', 'typ' => 's', 'def' => null, 'name' => '자재명'],
            ['val' => 'ordererItemNo', 'typ' => 's', 'def' => null, 'name' => '발주처 ITEM NO'],
            ['val' => 'ordererItemName', 'typ' => 's', 'def' => null, 'name' => '발주처 ITEM NAME'],
            ['val' => 'materialUnit', 'typ' => 's', 'def' => null, 'name' => '단위'],
            ['val' => 'currencyUnit', 'typ' => 'i', 'def' => 1, 'name' => '화폐단위'], //1:원화, 2:달러화
            ['val' => 'unitPrice', 'typ' => 's', 'def' => null, 'name' => '매입 단가'],
            ['val' => 'materialTangbi', 'typ' => 's', 'def' => null, 'name' => '탕비'],
            ['val' => 'makeNational', 'typ' => 's', 'def' => null, 'name' => '제조국'],
            ['val' => 'mixRatio', 'typ' => 's', 'def' => null, 'name' => '혼용률'],
            ['val' => 'weight', 'typ' => 'i', 'def' => null, 'name' => '중량'],
            ['val' => 'afterMake', 'typ' => 's', 'def' => null, 'name' => '후가공'],
            ['val' => 'fastness', 'typ' => 's', 'def' => null, 'name' => '견뢰도'],
            ['val' => 'btYn', 'typ' => 's', 'def' => null, 'name' => 'BT준비 여부'],
            ['val' => 'btPeriod', 'typ' => 'i', 'def' => null, 'name' => 'BT기간'],
            ['val' => 'onHandYn', 'typ' => 's', 'def' => null, 'name' => '생지보유 유무'],
            ['val' => 'moq', 'typ' => 'i', 'def' => null, 'name' => 'MOQ'],
            ['val' => 'materialColor', 'typ' => 's', 'def' => null, 'name' => '컬러'],
            ['val' => 'spec', 'typ' => 's', 'def' => null, 'name' => '폭/규격'],
            ['val' => 'makePeriod', 'typ' => 'i', 'def' => null, 'name' => '생산기간'],
            ['val' => 'makePeriodNoOnHand', 'typ' => 'i', 'def' => null, 'name' => '생산기간(생지없는경우)'],
            ['val' => 'merit', 'typ' => 's', 'def' => null, 'name' => '장점'],
            ['val' => 'disadv', 'typ' => 's', 'def' => null, 'name' => '단점'],
            ['val' => 'afterIssue', 'typ' => 's', 'def' => null, 'name' => '납품 후 이슈'],
            ['val' => 'usedStyle', 'typ' => 'i', 'def' => 0, 'name' => '사용 스타일', 'checkbox_sum'=>true],
            ['val' => 'memo', 'typ' => 's', 'def' => null, 'name' => '비고'],
            ['val' => 'imgMaterial', 'typ' => 's', 'def' => null, 'name' => '자재이미지url'],
            ['val' => 'imgSwatch', 'typ' => 's', 'def' => null, 'name' => '스와치사진url'],
            ['val' => 'materialSt', 'typ' => 'i', 'def' => 1, 'name' => '상태'],
            ['val' => 'regDt', 'typ' => 's', 'def' => null, 'name' => '등록일'],
            ['val' => 'modDt', 'typ' => 's', 'def' => null, 'name' => '수정일']
        ];
    }
    //자재 수정이력
    public static function tableImsMaterialUpdateLog(): array
    {
        return [
            ['val' => 'sno', 'typ' => 'i', 'def' => null, 'name' => '번호'],
            ['val' => 'materialSno', 'typ' => 'i', 'def' => 1, 'name' => '자재 일련번호'],
            ['val' => 'regManagerSno', 'typ' => 'i', 'def' => null, 'name' => '등록자 일련번호'],
            ['val' => 'updateDesc', 'typ' => 's', 'def' => null, 'name' => '수정내용'],
            ['val' => 'regDt', 'typ' => 's', 'def' => null, 'name' => '등록시간'],
        ];
    }
    //자재 그룹(유사퀄리티)
    public static function tableImsMaterialGroup(): array
    {
        return [
            ['val' => 'sno', 'typ' => 'i', 'def' => 0, 'name' => '번호'],
            ['val' => 'regManagerSno', 'typ' => 'i', 'def' => null, 'name' => '등록자 일련번호'],
            ['val' => 'grpName', 'typ' => 's', 'def' => null, 'name' => '그룹명'],
            ['val' => 'regDt', 'typ' => 's', 'def' => null, 'name' => '등록일시'],
            ['val' => 'modDt', 'typ' => 's', 'def' => null, 'name' => '수정일시'],
        ];
    }

    //부가판매/매입(프로젝트별)
    public static function tableImsAddedBuySale(): array
    {
        return [
            ['val' => 'sno', 'typ' => 'i', 'def' => null, 'name' => '번호'],
            ['val' => 'projectSno', 'typ' => 'i', 'def' => null, 'name' => '프로젝트 일련번호'],
            ['val' => 'regManagerSno', 'typ' => 'i', 'def' => null, 'name' => '등록자 일련번호'],
            ['val' => 'buyManagerSno', 'typ' => 'i', 'def' => null, 'name' => '매입처 일련번호'],
            ['val' => 'buyManagerSnoHan', 'typ' => 's', 'def' => null, 'name' => '매입처명'],
            ['val' => 'addedType', 'typ' => 'i', 'def' => 1, 'name' => '유형(판매,매입)'],
            ['val' => 'addedName', 'typ' => 's', 'def' => null, 'name' => '항목명'],
            ['val' => 'addedDesc', 'typ' => 's', 'def' => null, 'name' => '내용'],
            ['val' => 'addedBuyAmount', 'typ' => 'i', 'def' => null, 'name' => '매입단가'],
            ['val' => 'addedSaleAmount', 'typ' => 'i', 'def' => 0, 'name' => '판매단가'],
            ['val' => 'addedQty', 'typ' => 'i', 'def' => 1, 'name' => '수량'],
            ['val' => 'regDt', 'typ' => 's', 'def' => null, 'name' => '등록일시'],
            ['val' => 'modDt', 'typ' => 's', 'def' => null, 'name' => '수정일시'],
        ];
    }
    //프로젝트 -> 스타일(기성복) -> 생산가관리(근거)
    public static function tableImsProjectProductPrdCost(): array
    {
        return [
            ['val' => 'sno', 'typ' => 'i', 'def' => null, 'name' => '번호'],
            ['val' => 'styleSno', 'typ' => 'i', 'def' => null, 'name' => '스타일 일련번호'],
            ['val' => 'sortNum', 'typ' => 'i', 'def' => null, 'name' => '순서'],
            ['val' => 'prdCostName', 'typ' => 's', 'def' => null, 'name' => '품목명'],
            ['val' => 'prdCostAmount', 'typ' => 'i', 'def' => null, 'name' => '비용(vat별도)'],
            ['val' => 'prdCostBuyer', 'typ' => 's', 'def' => null, 'name' => '매입처'],
            ['val' => 'prdCostMemo', 'typ' => 's', 'def' => null, 'name' => '비고'],
            ['val' => 'regDt', 'typ' => 's', 'def' => null, 'name' => '등록일시'],
            ['val' => 'modDt', 'typ' => 's', 'def' => null, 'name' => '수정일시'],
        ];
    }
    //고객 담당자(메인담당자 그외 담당자 모두 포함(메인담당자정보는 customer테이블에 넣음))
    public static function tableImsCustomerContact(): array
    {
        return [
            ['val' => 'sno', 'typ' => 'i', 'def' => null, 'name' => '번호'],
            ['val' => 'customerSno', 'typ' => 'i', 'def' => null, 'name' => '고객사 일련번호'],
            ['val' => 'regManagerSno', 'typ' => 'i', 'def' => null, 'name' => '등록직원sno'],
            ['val' => 'cContactName', 'typ' => 's', 'def' => null, 'name' => '담당자명'],
            ['val' => 'cContactPosition', 'typ' => 's', 'def' => null, 'name' => '직함'],
            ['val' => 'cContactMobile', 'typ' => 's', 'def' => null, 'name' => '연락처'],
            ['val' => 'cContactPreference', 'typ' => 's', 'def' => null, 'name' => '담당자 성향'],
            ['val' => 'cContactEmail', 'typ' => 's', 'def' => null, 'name' => '이메일'],
            ['val' => 'cContactMemo', 'typ' => 's', 'def' => null, 'name' => '담당자 비고'],
            ['val' => 'regDt', 'typ' => 's', 'def' => null, 'name' => '등록일시'],
            ['val' => 'modDt', 'typ' => 's', 'def' => null, 'name' => '수정일시'],
        ];
    }

    //프로젝트 최초기획스케쥴
    public static function tableImsProjectPlanSchedule(): array
    {
        return [
            ['val' => 'sno', 'typ' => 'i', 'def' => null, 'name' => '번호'],
            ['val' => 'projectSno', 'typ' => 'i', 'def' => null, 'name' => '프로젝트sno'],
            ['val' => 'regManagerSno', 'typ' => 'i', 'def' => null, 'name' => '등록자sno'],
            ['val' => 'scheType', 'typ' => 'i', 'def' => null, 'name' => '일정구분'],
            ['val' => 'scheStep', 'typ' => 'i', 'def' => null, 'name' => '일정단계'],
            ['val' => 'scheDt', 'typ' => 's', 'def' => null, 'name' => '날짜'],
            ['val' => 'regDt', 'typ' => 's', 'def' => null, 'name' => '등록일시'],
            ['val' => 'modDt', 'typ' => 's', 'def' => null, 'name' => '수정일시'],
        ];
    }
    //스타일기획
    public static function tableImsProjectProductPlan(): array
    {
        return [
            ['val' => 'sno', 'typ' => 'i', 'def' => null, 'name' => '번호'],
            ['val' => 'styleSno', 'typ' => 'i', 'def' => null, 'name' => '스타일sno'],
            ['val' => 'regManagerSno', 'typ' => 'i', 'def' => null, 'name' => '등록자sno'],
            ['val' => 'prdCustomerSno', 'typ' => 'i', 'def' => null, 'name' => '생산처 일련번호'],
            ['val' => 'fitSpecSno', 'typ' => 'i', 'def' => null, 'name' => '사이즈스펙 일련번호'],
            ['val' => 'refStylePlanSno', 'typ' => 'i', 'def' => null, 'name' => '스타일기획 레퍼런스 일련번호'],
            ['val' => 'sortNum', 'typ' => 'i', 'def' => 1, 'name' => '순서'],
            ['val' => 'filePlan', 'typ' => 's', 'def' => null, 'name' => '기획이미지파일 url'],
            ['val' => 'planPrdCost', 'typ' => 'i', 'def' => null, 'name' => '기획 생산가'],
            ['val' => 'targetPrdCost', 'typ' => 'i', 'def' => null, 'name' => '타겟 생산가'],
            ['val' => 'targetPrice', 'typ' => 'i', 'def' => null, 'name' => '타겟 판매가'],
            ['val' => 'changeQty', 'typ' => 'i', 'def' => null, 'name' => '발주수량변동'],
            ['val' => 'prdGender', 'typ' => 's', 'def' => null, 'name' => '성별'],
            ['val' => 'planConcept', 'typ' => 's', 'def' => null, 'name' => '디자인 컨셉'],
            ['val' => 'planQty', 'typ' => 'i', 'def' => null, 'name' => '견적수량'],
            ['val' => 'planMemo', 'typ' => 's', 'def' => null, 'name' => '메모'],
            ['val' => 'prdCustomerName', 'typ' => 's', 'def' => null, 'name' => '생산처명'],
            ['val' => 'produceType', 'typ' => 's', 'def' => null, 'name' => '생산타입'],
            ['val' => 'producePeriod', 'typ' => 'i', 'def' => null, 'name' => '생산기간'],
            ['val' => 'produceNational', 'typ' => 's', 'def' => null, 'name' => '생산국가'],
            ['val' => 'estimateStatus', 'typ' => 's', 'def' => null, 'name' => '견적진행상태'],
            ['val' => 'produceMemo', 'typ' => 's', 'def' => null, 'name' => '생산처메모'],
            ['val' => 'dollerRatio', 'typ' => 's', 'def' => null, 'name' => '환율'],
            ['val' => 'dollerRatioDt', 'typ' => 's', 'def' => null, 'name' => '환율기준일'],
            ['val' => 'laborCost', 'typ' => 'i', 'def' => null, 'name' => '공임'],
            ['val' => 'marginCost', 'typ' => 'i', 'def' => null, 'name' => '마진(10%)'],
            ['val' => 'dutyCost', 'typ' => 'i', 'def' => null, 'name' => '물류/관세'],
            ['val' => 'deliveryType', 'typ' => 's', 'def' => null, 'name' => '운송 형태'],
            ['val' => 'prdMoq', 'typ' => 'i', 'def' => null, 'name' => '생산 MOQ'],
            ['val' => 'priceMoq', 'typ' => 'i', 'def' => null, 'name' => '단가 MOQ'],
            ['val' => 'addPrice', 'typ' => 'i', 'def' => null, 'name' => 'MOQ미달 추가금'],
            ['val' => 'fitName', 'typ' => 's', 'def' => null, 'name' => '사이즈스펙 핏이름'],
            ['val' => 'fitSize', 'typ' => 'i', 'def' => null, 'name' => '사이즈스펙 기준사이즈'],
            ['val' => 'jsonFitSpec', 'typ' => 's', 'def' => null, 'name' => '사이즈스펙 json', 'json' => true],
            ['val' => 'jsonFixedSpec', 'typ' => 's', 'def' => null, 'name' => '확정스펙 json', 'json' => true],
            ['val' => 'jsonUtil', 'typ' => 's', 'def' => null, 'name' => '기능 json', 'json' => true],
            ['val' => 'fabric', 'typ' => 's', 'def' => null, 'name' => '원단 json', 'json' => true],
            ['val' => 'subFabric', 'typ' => 's', 'def' => null, 'name' => '부자재 json', 'json' => true],
            ['val' => 'jsonMark', 'typ' => 's', 'def' => null, 'name' => '마크 json', 'json' => true],
            ['val' => 'jsonLaborCost', 'typ' => 's', 'def' => null, 'name' => '공임 json', 'json' => true],
            ['val' => 'jsonEtc', 'typ' => 's', 'def' => null, 'name' => '기타 json', 'json' => true],
            ['val' => 'productPlanSt', 'typ' => 'i', 'def' => 1, 'name' => '기획상태'],
            ['val' => 'regDt', 'typ' => 's', 'def' => null, 'name' => '등록일시'],
            ['val' => 'modDt', 'typ' => 's', 'def' => null, 'name' => '수정일시'],
        ];
    }


    /**
     * 발송이력 저장
     * @return array[]
     */
    public static function tableImsSendHistory(): array
    {
        return [
            ['val' => 'sno', 'typ' => 'i', 'def' => null, 'name' => '번호'],
            ['val' => 'sendType', 'typ' => 's', 'def' => null, 'name' => '발송타입'],
            ['val' => 'projectSno', 'typ' => 'i', 'def' => null, 'name' => '프로젝트 번호'],
            ['val' => 'sendManagerSno', 'typ' => 'i', 'def' => null, 'name' => '발송자'],
            ['val' => 'receiverName', 'typ' => 's', 'def' => null, 'name' => '수신자명'],
            ['val' => 'receiverMail', 'typ' => 's', 'def' => null, 'name' => '수신자이메일'],
            ['val' => 'ccList', 'typ' => 's', 'def' => null, 'name' => '참조자'],
            ['val' => 'subject', 'typ' => 's', 'def' => null, 'name' => '수신자명'],
            ['val' => 'contents', 'typ' => 's', 'def' => null, 'name' => '내용', 'json' => true],
            ['val' => 'regDt', 'typ' => 's', 'def' => null, 'name' => '등록시간'],
            ['val' => 'modDt', 'typ' => 's', 'def' => null, 'name' => '수정시간']
        ];
    }
    //게시물 수정이력(1 record = 1 field update)
    public static function tableImsUpdateHistoryNk(): array
    {
        return [
            ['val' => 'sno', 'typ' => 'i', 'def' => null, 'name' => '일련번호'],
            ['val' => 'regManagerSno', 'typ' => 'i', 'def' => null, 'name' => '등록직원 일련번호'],
            ['val' => 'tableType', 'typ' => 'i', 'def' => null, 'name' => '수정Table'],
            ['val' => 'eachSno', 'typ' => 'i', 'def' => null, 'name' => '수정대상 일련번호'],
            ['val' => 'fldName', 'typ' => 's', 'def' => null, 'name' => '수정컬럼명'],
            ['val' => 'fldNameHan', 'typ' => 's', 'def' => null, 'name' => '수정컬럼명(한글명)'],
            ['val' => 'beforeValue', 'typ' => 's', 'def' => null, 'name' => '이전값'],
            ['val' => 'afterValue', 'typ' => 's', 'def' => null, 'name' => '수정값'],
            ['val' => 'regDt', 'typ' => 's', 'def' => null, 'name' => '등록시간'],
        ];
    }
    //프로젝트/스타일 이슈리스트
    public static function tableImsProjectIssue(): array
    {
        return [
            ['val' => 'sno', 'typ' => 'i', 'def' => 0, 'name' => '일련번호'],
            ['val' => 'projectSno', 'typ' => 'i', 'def' => null, 'name' => '프로젝트 일련번호'],
            ['val' => 'styleSno', 'typ' => 'i', 'def' => 0, 'name' => '스타일 일련번호'],
            ['val' => 'regManagerSno', 'typ' => 'i', 'def' => null, 'name' => '등록직원 일련번호'],
            ['val' => 'issueType', 'typ' => 'i', 'def' => null, 'name' => '유형'],
            ['val' => 'issueTypeText', 'typ' => 's', 'def' => null, 'name' => '유형 한글문구'],
            ['val' => 'issueSubject', 'typ' => 's', 'def' => null, 'name' => '제목'],
            ['val' => 'issueContents', 'typ' => 's', 'def' => null, 'name' => '내용'],
            ['val' => 'issueReason', 'typ' => 's', 'def' => null, 'name' => '원인'],
            ['val' => 'issueRange', 'typ' => 's', 'def' => null, 'name' => '영향범위'],
            ['val' => 'isRepeat', 'typ' => 's', 'def' => 'n', 'name' => '반복 유무'],
            ['val' => 'isLongUnprocess', 'typ' => 's', 'def' => 'n', 'name' => '장기미처리 유무'],
            ['val' => 'issueSt', 'typ' => 'i', 'def' => 1, 'name' => '상태'],
            ['val' => 'regDt', 'typ' => 's', 'def' => null, 'name' => '등록시간'],
            ['val' => 'modDt', 'typ' => 's', 'def' => null, 'name' => '수정시간'],
        ];
    }
    //프로젝트/스타일 이슈 조치리스트
    public static function tableImsProjectIssueAction(): array
    {
        return [
            ['val' => 'sno', 'typ' => 'i', 'def' => 0, 'name' => '일련번호'],
            ['val' => 'issueSno', 'typ' => 'i', 'def' => null, 'name' => '이슈 일련번호'],
            ['val' => 'regManagerSno', 'typ' => 'i', 'def' => null, 'name' => '등록직원 일련번호'],
            ['val' => 'actionContents', 'typ' => 's', 'def' => null, 'name' => '처리사항'],
            ['val' => 'regDt', 'typ' => 's', 'def' => null, 'name' => '등록시간'],
            ['val' => 'modDt', 'typ' => 's', 'def' => null, 'name' => '수정시간'],
        ];
    }

    //고객 제공 샘플 측정항목
    public static function tableImsCustomerFitSpecOption(): array
    {
        return [
            ['val' => 'sno', 'typ' => 'i', 'def' => 0, 'name' => '일련번호'],
            ['val' => 'productPlanSno', 'typ' => 'i', 'def' => null, 'name' => '스타일기획 일련번호'],
            ['val' => 'sortNum', 'typ' => 'i', 'def' => 1, 'name' => '순서'],
            ['val' => 'optionSize', 'typ' => 's', 'def' => null, 'name' => '제공사이즈'],
            ['val' => 'optionName', 'typ' => 's', 'def' => null, 'name' => '부위명'],
            ['val' => 'optionValue', 'typ' => 's', 'def' => null, 'name' => '스펙'],
            ['val' => 'optionUnit', 'typ' => 's', 'def' => null, 'name' => '단위'],
            ['val' => 'regDt', 'typ' => 's', 'def' => null, 'name' => '등록시간'],
            ['val' => 'modDt', 'typ' => 's', 'def' => null, 'name' => '수정시간'],
        ];
    }

    //공임비용/기타비용 항목리스트(스타일기획, 샘플 등록/수정시 사용)
    public static function tableImsBasicSampleEtcCost(): array
    {
        return [
            ['val' => 'sno', 'typ' => 'i', 'def' => 0, 'name' => '일련번호'],
            ['val' => 'regManagerSno', 'typ' => 'i', 'def' => null, 'name' => '등록직원 일련번호'],
            ['val' => 'costType', 'typ' => 'i', 'def' => 1, 'name' => '유형'],
            ['val' => 'costCode', 'typ' => 's', 'def' => null, 'name' => '코드'],
            ['val' => 'costName', 'typ' => 's', 'def' => null, 'name' => '구분명'],
            ['val' => 'costUnitPrice', 'typ' => 'i', 'def' => null, 'name' => '기본단가'],
            ['val' => 'costDesc', 'typ' => 's', 'def' => null, 'name' => '내용'],
            ['val' => 'regDt', 'typ' => 's', 'def' => null, 'name' => '등록시간'],
            ['val' => 'modDt', 'typ' => 's', 'def' => null, 'name' => '수정시간'],
        ];
    }


    //차량관련 테이블 4개
    public static function tableImsEtcCar(): array
    {
        return [
            ['val' => 'sno', 'typ' => 'i', 'def' => 0, 'name' => '일련번호'],
            ['val' => 'carType', 'typ' => 's', 'def' => null, 'name' => '명의구분'],
            ['val' => 'carImage', 'typ' => 's', 'def' => null, 'name' => '썸네일 이미지'],
            ['val' => 'carName', 'typ' => 's', 'def' => null, 'name' => '차종(차량명)'],
            ['val' => 'carNumber', 'typ' => 's', 'def' => null, 'name' => '차량번호'],
            ['val' => 'totalCheckDt', 'typ' => 's', 'def' => null, 'name' => '종합검사일자'],
            ['val' => 'regDt', 'typ' => 's', 'def' => null, 'name' => '등록시간'],
            ['val' => 'modDt', 'typ' => 's', 'def' => null, 'name' => '수정시간'],
        ];
    }
    public static function tableImsEtcCarMaintain(): array
    {
        return [
            ['val' => 'sno', 'typ' => 'i', 'def' => 0, 'name' => '일련번호'],
            ['val' => 'carSno', 'typ' => 'i', 'def' => 0, 'name' => '자동차 일련번호'],
            ['val' => 'regManagerSno', 'typ' => 'i', 'def' => null, 'name' => '등록직원 일련번호'],
            ['val' => 'maintainType', 'typ' => 's', 'def' => null, 'name' => '정비구분'],
            ['val' => 'maintainDt', 'typ' => 's', 'def' => null, 'name' => '정비일자'],
            ['val' => 'maintainUser', 'typ' => 's', 'def' => null, 'name' => '정비자'],
            ['val' => 'maintainCost', 'typ' => 'i', 'def' => null, 'name' => '정비금액'],
            ['val' => 'currKm', 'typ' => 'i', 'def' => 0, 'name' => '정비당시 차량 운행km(계기판 기준)'],
            ['val' => 'maintainMemo', 'typ' => 's', 'def' => null, 'name' => '정비 메모'],
            ['val' => 'regDt', 'typ' => 's', 'def' => null, 'name' => '등록시간'],
            ['val' => 'modDt', 'typ' => 's', 'def' => null, 'name' => '수정시간'],
        ];
    }
    public static function tableImsEtcCarAddr(): array
    {
        return [
            ['val' => 'sno', 'typ' => 'i', 'def' => 0, 'name' => '일련번호'],
            ['val' => 'regManagerSno', 'typ' => 'i', 'def' => null, 'name' => '등록직원 일련번호'],
            ['val' => 'addrType', 'typ' => 's', 'def' => null, 'name' => '주소지 분류'],
            ['val' => 'topYn', 'typ' => 'i', 'def' => 2, 'name' => '상단고정 여부'],
            ['val' => 'addrName', 'typ' => 's', 'def' => null, 'name' => '명칭'],
            ['val' => 'addrAddr', 'typ' => 's', 'def' => null, 'name' => '주소지 주소'],
            ['val' => 'regDt', 'typ' => 's', 'def' => null, 'name' => '등록시간'],
            ['val' => 'modDt', 'typ' => 's', 'def' => null, 'name' => '수정시간'],
        ];
    }
    public static function tableImsEtcCarDrive(): array
    {
        return [
            ['val' => 'sno', 'typ' => 'i', 'def' => 0, 'name' => '일련번호'],
            ['val' => 'carSno', 'typ' => 'i', 'def' => 0, 'name' => '자동차 일련번호'],
            ['val' => 'regManagerSno', 'typ' => 'i', 'def' => null, 'name' => '등록직원 일련번호'],
            ['val' => 'startAddrSno', 'typ' => 'i', 'def' => 0, 'name' => '출발지 일련번호'],
            ['val' => 'arriveAddrSno', 'typ' => 'i', 'def' => 0, 'name' => '도착지 일련번호'],
            ['val' => 'driveType', 'typ' => 's', 'def' => null, 'name' => '운행구분'],
            ['val' => 'driveDt', 'typ' => 's', 'def' => null, 'name' => '운행일자'],
            ['val' => 'driveStartTime', 'typ' => 's', 'def' => null, 'name' => '운행시작시간'],
            ['val' => 'driveEndTime', 'typ' => 's', 'def' => null, 'name' => '운행종료시간'],
            ['val' => 'driveDepartment', 'typ' => 's', 'def' => null, 'name' => '운행자 부서'],
            ['val' => 'driveName', 'typ' => 's', 'def' => null, 'name' => '운행자명'],
            ['val' => 'driveBeforeCluster', 'typ' => 'i', 'def' => 0, 'name' => '운행전 계기판km'],
            ['val' => 'driveKm', 'typ' => 'i', 'def' => null, 'name' => '주행km'],
            ['val' => 'driveCost', 'typ' => 'i', 'def' => null, 'name' => '비용(주차,톨비 등등)'],
            ['val' => 'driveMemo', 'typ' => 's', 'def' => null, 'name' => '비고'],
            ['val' => 'regDt', 'typ' => 's', 'def' => null, 'name' => '등록시간'],
            ['val' => 'modDt', 'typ' => 's', 'def' => null, 'name' => '수정시간'],
        ];
    }

    public static function tableImsBasicBusiCate(): array
    {
        return [
            ['val' => 'sno', 'typ' => 'i', 'def' => 0, 'name' => '일련번호'],
            ['val' => 'parentBusiCateSno', 'typ' => 'i', 'def' => 0, 'name' => '상위업종 일련번호'],
            ['val' => 'cateName', 'typ' => 's', 'def' => null, 'name' => '업종명'],
            ['val' => 'cateDesc', 'typ' => 's', 'def' => null, 'name' => '업종설명'],
            ['val' => 'regDt', 'typ' => 's', 'def' => null, 'name' => '등록시간'],
            ['val' => 'modDt', 'typ' => 's', 'def' => null, 'name' => '수정시간'],
        ];
    }
    public static function tableSalesCustomerStats(): array
    {
        return [
            ['val' => 'sno', 'typ' => 'i', 'def' => 0, 'name' => '일련번호'],
            ['val' => 'statsDt', 'typ' => 's', 'def' => null, 'name' => '통계일자'],
            ['val' => 'cntTm', 'typ' => 'i', 'def' => 0, 'name' => 'TM횟수'],
            ['val' => 'sumMinTm', 'typ' => 'i', 'def' => 0, 'name' => 'TM업무시간(분)합산'],
            ['val' => 'cntEm', 'typ' => 'i', 'def' => 0, 'name' => 'EM횟수'],
            ['val' => 'sumMinEm', 'typ' => 'i', 'def' => 0, 'name' => 'EM업무시간(분)합산'],
            ['val' => 'cntCustomer1', 'typ' => 'i', 'def' => null, 'name' => '잠재고객 갯수'],
            ['val' => 'jsonIncCustomer1', 'typ' => 's', 'def' => null, 'name' => '증가된 잠재고객 일련번호 json', 'json' => true],
            ['val' => 'jsonDecCustomer1', 'typ' => 's', 'def' => null, 'name' => '감소된 잠재고객 일련번호 json', 'json' => true],
            ['val' => 'jsonCustomer1', 'typ' => 's', 'def' => null, 'name' => '잠재고객 일련번호 json', 'json' => true],
            ['val' => 'cntCustomer2', 'typ' => 'i', 'def' => null, 'name' => '관심고객 갯수'],
            ['val' => 'jsonIncCustomer2', 'typ' => 's', 'def' => null, 'name' => '증가된 관심고객 일련번호 json', 'json' => true],
            ['val' => 'jsonDecCustomer2', 'typ' => 's', 'def' => null, 'name' => '감소된 관심고객 일련번호 json', 'json' => true],
            ['val' => 'jsonCustomer2', 'typ' => 's', 'def' => null, 'name' => '관심고객 일련번호 json', 'json' => true],
            ['val' => 'cntCustomer3', 'typ' => 'i', 'def' => null, 'name' => '가망고객 갯수'],
            ['val' => 'jsonIncCustomer3', 'typ' => 's', 'def' => null, 'name' => '증가된 가망고객 일련번호 json', 'json' => true],
            ['val' => 'jsonDecCustomer3', 'typ' => 's', 'def' => null, 'name' => '감소된 가망고객 일련번호 json', 'json' => true],
            ['val' => 'jsonCustomer3', 'typ' => 's', 'def' => null, 'name' => '가망고객 일련번호 json', 'json' => true],
            ['val' => 'cntCustomer4', 'typ' => 'i', 'def' => null, 'name' => '발굴완료 갯수'],
            ['val' => 'jsonIncCustomer4', 'typ' => 's', 'def' => null, 'name' => '증가된 발굴고객 일련번호 json', 'json' => true],
            ['val' => 'jsonDecCustomer4', 'typ' => 's', 'def' => null, 'name' => '감소된 발굴고객 일련번호 json', 'json' => true],
            ['val' => 'jsonCustomer4', 'typ' => 's', 'def' => null, 'name' => '발굴완료 일련번호 json', 'json' => true],
            ['val' => 'regDt', 'typ' => 's', 'def' => null, 'name' => '등록시간'],
            ['val' => 'modDt', 'typ' => 's', 'def' => null, 'name' => '수정시간'],
        ];
    }

    //프로젝트별 세부스케쥴. 작업용. 피드백에 따라 바뀔수 있음
    public static function tableNmsProjectScheDetail(): array
    {
        return [
            ['val' => 'sno', 'typ' => 'i', 'def' => 0, 'name' => '일련번호'],
            ['val' => 'projectSno', 'typ' => 'i', 'def' => null, 'name' => '프로젝트 일련번호'],
            ['val' => 'scheDetailSno', 'typ' => 'i', 'def' => null, 'name' => '세부스케쥴 일련번호'],
            ['val' => 'sortSche', 'typ' => 'i', 'def' => null, 'name' => '순서'],
            ['val' => 'ownerManagerSno', 'typ' => 'i', 'def' => null, 'name' => '담당직원 일련번호'],
            ['val' => 'departName', 'typ' => 's', 'def' => null, 'name' => '담당부서 한글명'],
            ['val' => 'deadlineDt', 'typ' => 's', 'def' => null, 'name' => 'D/L일자'],
            ['val' => 'expectedDt', 'typ' => 's', 'def' => null, 'name' => '예정일자'],
            ['val' => 'completeDt', 'typ' => 's', 'def' => null, 'name' => '완료일자'],
            ['val' => 'scheMemo', 'typ' => 's', 'def' => null, 'name' => '메모'],
            ['val' => 'scheSt', 'typ' => 'i', 'def' => null, 'name' => '상태'],
            ['val' => 'regDt', 'typ' => 's', 'def' => null, 'name' => '등록시간'],
            ['val' => 'modDt', 'typ' => 's', 'def' => null, 'name' => '수정시간'],
        ];
    }

    public static function tableImsBasicFittingCheck(): array
    {
        return [
            ['val' => 'sno', 'typ' => 'i', 'def' => 0, 'name' => '일련번호'],
            ['val' => 'regManagerSno', 'typ' => 'i', 'def' => null, 'name' => '등록직원 일련번호'],
            ['val' => 'fitStyle', 'typ' => 's', 'def' => '', 'name' => '스타일코드'],
            ['val' => 'fitSeason', 'typ' => 's', 'def' => '', 'name' => '시즌코드'],
            ['val' => 'fittingCheckName', 'typ' => 's', 'def' => null, 'name' => '양식명'],
            ['val' => 'jsonOptions', 'typ' => 's', 'def' => null, 'name' => '항목 json', 'json' => true],
            ['val' => 'regDt', 'typ' => 's', 'def' => null, 'name' => '등록시간'],
            ['val' => 'modDt', 'typ' => 's', 'def' => null, 'name' => '수정시간'],
        ];
    }

    //사이즈스펙 양식
    public static function tableImsBasicSizeSpec(): array
    {
        return [
            ['val' => 'sno', 'typ' => 'i', 'def' => 0, 'name' => '일련번호'],
            ['val' => 'regManagerSno', 'typ' => 'i', 'def' => null, 'name' => '등록직원 일련번호'],
            ['val' => 'fitStyle', 'typ' => 's', 'def' => '', 'name' => '스타일'],
            ['val' => 'fitSeason', 'typ' => 's', 'def' => '', 'name' => '시즌'],
            ['val' => 'fitSizeName', 'typ' => 's', 'def' => null, 'name' => '양식명'],
            ['val' => 'fitName', 'typ' => 's', 'def' => '', 'name' => '핏이름'],
            ['val' => 'fitSize', 'typ' => 's', 'def' => null, 'name' => '기준사이즈'],
            ['val' => 'jsonOptions', 'typ' => 's', 'def' => null, 'name' => '항목 json', 'json' => true],
            ['val' => 'regDt', 'typ' => 's', 'def' => null, 'name' => '등록시간'],
            ['val' => 'modDt', 'typ' => 's', 'def' => null, 'name' => '수정시간'],
        ];
    }

    //기초정보-제안서가이드양식
    public static function tableImsBasicProposalGuide(): array
    {
        return [
            ['val' => 'sno', 'typ' => 'i', 'def' => 0, 'name' => '일련번호'],
            ['val' => 'regManagerSno', 'typ' => 'i', 'def' => null, 'name' => '등록직원 일련번호'],
            ['val' => 'sortNum', 'typ' => 'i', 'def' => null, 'name' => '순서'],
            ['val' => 'guideName', 'typ' => 's', 'def' => '', 'name' => '양식명'],
            ['val' => 'guideFileUrl', 'typ' => 's', 'def' => '', 'name' => '내용이미지파일 url'],
            ['val' => 'guideDesc', 'typ' => 's', 'def' => '', 'name' => '제안서 설명'],
            ['val' => 'regDt', 'typ' => 's', 'def' => null, 'name' => '등록일시'],
            ['val' => 'modDt', 'typ' => 's', 'def' => null, 'name' => '수정일시'],
        ];
    }

    //기초정보-영업기획서양식
    public static function tableImsBasicSalesPlan(): array
    {
        return [
            ['val' => 'sno', 'typ' => 'i', 'def' => 0, 'name' => '일련번호'],
            ['val' => 'regManagerSno', 'typ' => 'i', 'def' => null, 'name' => '등록직원 일련번호'],
            ['val' => 'basicFormName', 'typ' => 's', 'def' => '', 'name' => '양식명'],
            ['val' => 'jsonBasicFormContents', 'typ' => 's', 'def' => '', 'name' => '내용 json', 'json' => true],
            ['val' => 'regDt', 'typ' => 's', 'def' => null, 'name' => '등록일시'],
            ['val' => 'modDt', 'typ' => 's', 'def' => null, 'name' => '수정일시'],
        ];
    }

    //영업기획서작성
    public static function tableImsProjectSalesPlanFill(): array
    {
        return [
            ['val' => 'sno', 'typ' => 'i', 'def' => 0, 'name' => '일련번호'],
            ['val' => 'projectSno', 'typ' => 'i', 'def' => null, 'name' => '프로젝트 일련번호'],
            ['val' => 'basicSalesPlanSno', 'typ' => 'i', 'def' => null, 'name' => '영업기획서양식 일련번호'],
            ['val' => 'regManagerSno', 'typ' => 'i', 'def' => null, 'name' => '등록직원 일련번호'],
            ['val' => 'jsonProposalGuide', 'typ' => 's', 'def' => '', 'name' => '제안서정보 json', 'json' => true],
            ['val' => 'proposalGuideDesc', 'typ' => 's', 'def' => null, 'name' => '제안서정보 추가필요사항'],
            ['val' => 'salesPlanFillSt', 'typ' => 'i', 'def' => 1, 'name' => '상태'],
            ['val' => 'regDt', 'typ' => 's', 'def' => null, 'name' => '등록일시'],
            ['val' => 'modDt', 'typ' => 's', 'def' => null, 'name' => '수정일시'],
        ];
    }
    //영업기획서작성 - 필드(cell)별 작성
    public static function tableImsProjectSalesPlanFillDetail(): array
    {
        return [
            ['val' => 'sno', 'typ' => 'i', 'def' => 0, 'name' => '일련번호'],
            ['val' => 'salesPlanFillSno', 'typ' => 'i', 'def' => null, 'name' => '영업기획서작성 일련번호'],
            ['val' => 'textGroup', 'typ' => 's', 'def' => null, 'name' => '그룹 text'],
            ['val' => 'textQuestion', 'typ' => 's', 'def' => null, 'name' => '문항 text'],
            ['val' => 'textCell', 'typ' => 's', 'def' => null, 'name' => '필드(cell) text'],
            ['val' => 'cellValue', 'typ' => 's', 'def' => null, 'name' => '작성값'],
            ['val' => 'regDt', 'typ' => 's', 'def' => null, 'name' => '등록일시'],
        ];
    }
    //영업기획서작성 - json형식(추가/삭제 가능한 문항그룹)
    public static function tableImsProjectSalesPlanFillJson(): array
    {
        return [
            ['val' => 'sno', 'typ' => 'i', 'def' => 0, 'name' => '일련번호'],
            ['val' => 'salesPlanFillSno', 'typ' => 'i', 'def' => null, 'name' => '영업기획서작성 일련번호'],
            ['val' => 'textGroup', 'typ' => 's', 'def' => null, 'name' => '그룹 text'],
            ['val' => 'jsonValue', 'typ' => 's', 'def' => null, 'name' => '작성값', 'json' => true],
            ['val' => 'regDt', 'typ' => 's', 'def' => null, 'name' => '등록일시'],
        ];
    }


    /**
     * 작업지시서 원부자재 테이블
     * 10/28 송준호 DBIMS 에서 이동
     * 
     * TODO : 기획서와 같은 원부자재 및 기타 등등에 대한 객관화 작업 필요
     *      * 객관화 된 원단은 추가 원단 정보(성적서->세탁이화확 정보)를 입력할 수 있게 한다.
     *      * 원단 검색창에는 해당 원단의
     *      * 기획서와 레이아웃은 비슷하지만 저장은 테이블로해서 원단 검색시 해당 정보를 사용할 수 있게 한다.
     *
     * @return array
     */
    public static function tableImsPrdMaterial(): array
    {
        return [
            ['val' => 'sno', 'typ' => 'i', 'def' => null, 'name' => '번호'],
            ['val' => 'typeStr', 'typ' => 's', 'def' => null, 'name' => '타입'], //fabric, subFabric
            ['val' => 'styleSno', 'typ' => 'i', 'def' => null, 'name' => '스타일번호'],
            ['val' => 'materialSno', 'typ' => 'i', 'def' => 0, 'name' => '자재 일련번호'],

            ['val' => 'position', 'typ' => 's', 'def' => null, 'name' => '위치/1차부자재_2차부자재'],
            ['val' => 'attached', 'typ' => 's', 'def' => null, 'name' => '부착위치'],
            ['val' => 'fabricName', 'typ' => 's', 'def' => null, 'name' => '원단명'], //자재명
            ['val' => 'fabricMix', 'typ' => 's', 'def' => null, 'name' => '혼용률'],
            ['val' => 'color', 'typ' => 's', 'def' => null, 'name' => '컬러'],
            ['val' => 'spec', 'typ' => 's', 'def' => null, 'name' => '규격'], //
            ['val' => 'unit', 'typ' => 's', 'def' => null, 'name' => '단위'],
            ['val' => 'weight', 'typ' => 's', 'def' => null, 'name' => '중량'],
            ['val' => 'afterMake', 'typ' => 's', 'def' => null, 'name' => '후가공'],
            ['val' => 'meas', 'typ' => 's', 'def' => null, 'name' => '가요척'], // 수량
            ['val' => 'unitPrice', 'typ' => 's', 'def' => null, 'name' => '단가'],
            ['val' => 'makeNational', 'typ' => 's', 'def' => '', 'name' => '제조국'],
            ['val' => 'makeCompany', 'typ' => 's', 'def' => '', 'name' => '매입처'],
            ['val' => 'memo', 'typ' => 's', 'def' => '', 'name' => '비고'],

            //['val' => 'cate1', 'typ' => 's', 'def' => '', 'name' => '카테1'],
            //['val' => 'cate2', 'typ' => 's', 'def' => '', 'name' => '카테2'],

            ['val' => 'sort', 'typ' => 'i', 'def' => null, 'name' => '순서'],
            ['val' => 'regDt', 'typ' => 's', 'def' => null, 'name' => '등록일'],
            ['val' => 'modDt', 'typ' => 's', 'def' => null, 'name' => '수정일']
        ];
    }

    //시험성적서 작성
    public static function tableImsTestReportFill(): array
    {
        return [
            ['val' => 'sno', 'typ' => 'i', 'def' => 0, 'name' => '번호'],
            ['val' => 'materialSno', 'typ' => 'i', 'def' => null, 'name' => '자재 일련번호'],
            ['val' => 'materialColor', 'typ' => 's', 'def' => null, 'name' => '작지에서 입력한 컬러'],
            ['val' => 'customerSno', 'typ' => 'i', 'def' => null, 'name' => '고객 일련번호'],
            ['val' => 'regManagerSno', 'typ' => 'i', 'def' => null, 'name' => '등록직원 일련번호'],
            ['val' => 'testType', 'typ' => 'i', 'def' => null, 'name' => '테스트유형'],
            ['val' => 'totalAvg', 'typ' => 's', 'def' => null, 'name' => '전체평균값'],
            ['val' => 'jsonFillContents', 'typ' => 's', 'def' => null, 'name' => '작성내용 json', 'json' => true],
            ['val' => 'regDt', 'typ' => 's', 'def' => null, 'name' => '등록일'],
            ['val' => 'modDt', 'typ' => 's', 'def' => null, 'name' => '수정일']
        ];
    }

    //스타일 QC/인라인 검수
    public static function tableImsProjectProductInspect(): array
    {
        return [
            ['val' => 'sno', 'typ' => 'i', 'def' => 0, 'name' => '번호'],
            ['val' => 'styleSno', 'typ' => 'i', 'def' => null, 'name' => '스타일 일련번호'],
            ['val' => 'regManagerSno', 'typ' => 'i', 'def' => null, 'name' => '등록직원 일련번호'],
            ['val' => 'jsonInspectList', 'typ' => 's', 'def' => null, 'name' => '검수 json', 'json' => true],
            ['val' => 'jsonInspectSizeSpec', 'typ' => 's', 'def' => null, 'name' => '사이즈스펙 측정 json', 'json' => true],
            ['val' => 'jsonInspectCheck', 'typ' => 's', 'def' => null, 'name' => '내역 json', 'json' => true],
            ['val' => 'jsonInspectComment1', 'typ' => 's', 'def' => null, 'name' => '생산처의견 json', 'json' => true],
            ['val' => 'jsonInspectComment2', 'typ' => 's', 'def' => null, 'name' => '이노버의견 json', 'json' => true],
            ['val' => 'regDt', 'typ' => 's', 'def' => null, 'name' => '등록일'],
            ['val' => 'modDt', 'typ' => 's', 'def' => null, 'name' => '수정일']
        ];
    }

    //분류패킹 납품건(분류패킹 master테이블)
    public static function tableImsCustomerPacking(): array
    {
        return [
            ['val' => 'sno', 'typ' => 'i', 'def' => 0, 'name' => '번호'],
            ['val' => 'customerSno', 'typ' => 'i', 'def' => null, 'name' => '고객사 일련번호'],
            ['val' => 'regManagerSno', 'typ' => 'i', 'def' => null, 'name' => '등록직원 일련번호'],
            ['val' => 'styleSnos', 'typ' => 's', 'def' => null, 'name' => '스타일 일련번호들'],
            ['val' => 'jsonCntSizeTotal', 'typ' => 's', 'def' => null, 'name' => '스타일/Assort/사이즈별 총수량 json', 'json' => true],
            ['val' => 'jsonCntSizeTotalims', 'typ' => 's', 'def' => null, 'name' => '스타일/사이즈별 생산수량 json', 'json' => true],
            ['val' => 'packingSt', 'typ' => 'i', 'def' => 1, 'name' => '상태'],
            ['val' => 'regDt', 'typ' => 's', 'def' => null, 'name' => '등록일'],
            ['val' => 'modDt', 'typ' => 's', 'def' => null, 'name' => '수정일']
        ];
    }

    //분류패킹 고객담당자
    public static function tableImsCustomerReceiver(): array
    {
        return [
            ['val' => 'sno', 'typ' => 'i', 'def' => 0, 'name' => '번호'],
            ['val' => 'customerSno', 'typ' => 'i', 'def' => null, 'name' => '고객사 일련번호'],
            ['val' => 'regManagerSno', 'typ' => 'i', 'def' => null, 'name' => '등록직원 일련번호'],
            ['val' => 'sortNum', 'typ' => 'i', 'def' => null, 'name' => '순서'],
            ['val' => 'branchName', 'typ' => 's', 'def' => null, 'name' => '지점명'],
            ['val' => 'departmentName', 'typ' => 's', 'def' => null, 'name' => '부서명'],
            ['val' => 'managerName', 'typ' => 's', 'def' => null, 'name' => '담당자명'],
            ['val' => 'managerEmail', 'typ' => 's', 'def' => null, 'name' => '담당자 메일주소'],
            ['val' => 'managerPhone', 'typ' => 's', 'def' => null, 'name' => '담당자 전화번호'],
            ['val' => 'managerAddrPost', 'typ' => 's', 'def' => null, 'name' => '담당자 주소-우편번호'],
            ['val' => 'managerAddr', 'typ' => 's', 'def' => null, 'name' => '담당자 주소'],
            ['val' => 'regDt', 'typ' => 's', 'def' => null, 'name' => '등록일'],
            ['val' => 'modDt', 'typ' => 's', 'def' => null, 'name' => '수정일']
        ];
    }
    //분류패킹 고객담당자 패킹현황
    public static function tableImsCustomerReceiverDelivery(): array
    {
        return [
            ['val' => 'sno', 'typ' => 'i', 'def' => 0, 'name' => '번호'],
            ['val' => 'customerSno', 'typ' => 'i', 'def' => null, 'name' => '고객사 일련번호'],
            ['val' => 'packingSno', 'typ' => 'i', 'def' => null, 'name' => '패킹(발주건) 일련번호'],
            ['val' => 'receiverSno', 'typ' => 'i', 'def' => null, 'name' => '분류패킹 고객담당자 일련번호'],
            ['val' => 'regManagerSno', 'typ' => 'i', 'def' => null, 'name' => '등록직원 일련번호'],
            ['val' => 'managerName', 'typ' => 's', 'def' => null, 'name' => '담당자명'],
            ['val' => 'managerEmail', 'typ' => 's', 'def' => null, 'name' => '담당자 메일주소'],
            ['val' => 'managerPhone', 'typ' => 's', 'def' => null, 'name' => '담당자 전화번호'],
            ['val' => 'managerAddrPost', 'typ' => 's', 'def' => null, 'name' => '담당자 주소-우편번호'],
            ['val' => 'managerAddr', 'typ' => 's', 'def' => null, 'name' => '담당자 주소'],
            ['val' => 'pinNumber', 'typ' => 's', 'def' => null, 'name' => '접속키값'],
            ['val' => 'jsonContents', 'typ' => 's', 'def' => null, 'name' => '사이즈별 받을수량 json', 'json' => true],
            ['val' => 'wishReceivePlace', 'typ' => 's', 'def' => null, 'name' => '희망 납품장소'],
            ['val' => 'invoiceNums', 'typ' => 's', 'def' => null, 'name' => '운송장번호'],
            ['val' => 'deliveryCompanyCode', 'typ' => 's', 'def' => null, 'name' => '배송회사코드'],
            ['val' => 'deliverySt', 'typ' => 'i', 'def' => 1, 'name' => '상태'],
            ['val' => 'regDt', 'typ' => 's', 'def' => null, 'name' => '등록일'],
            ['val' => 'modDt', 'typ' => 's', 'def' => null, 'name' => '수정일']
        ];
    }

    //납품검수(납품보고서)
    public static function tableImsProjectProductInspectDelivery(): array
    {
        return [
            ['val' => 'sno', 'typ' => 'i', 'def' => 0, 'name' => '번호'],
            ['val' => 'styleSno', 'typ' => 'i', 'def' => null, 'name' => '스타일 일련번호'],
            ['val' => 'regManagerSno', 'typ' => 'i', 'def' => null, 'name' => '등록직원 일련번호'],
            ['val' => 'testManagerName', 'typ' => 's', 'def' => null, 'name' => '점검인원'],
            ['val' => 'testStartDate', 'typ' => 's', 'def' => null, 'name' => '점검 시작일'],
            ['val' => 'testStartHour', 'typ' => 'i', 'def' => null, 'name' => '점검 시작시간'],
            ['val' => 'testEndDate', 'typ' => 's', 'def' => null, 'name' => '점검 종료일'],
            ['val' => 'testEndHour', 'typ' => 'i', 'def' => null, 'name' => '점검 종료시간'],
            ['val' => 'testPlace', 'typ' => 's', 'def' => null, 'name' => '점검 장소'],
            ['val' => 'testMemo', 'typ' => 's', 'def' => null, 'name' => '메모'],
            ['val' => 'cntTest', 'typ' => 'i', 'def' => 0, 'name' => '검사갯수'],
            ['val' => 'jsonTestSizeSpec', 'typ' => 's', 'def' => null, 'name' => '사이즈스펙 측정 json', 'json' => true],
            ['val' => 'jsonTestCheck', 'typ' => 's', 'def' => null, 'name' => '검수내역 json', 'json' => true],
            ['val' => 'regDt', 'typ' => 's', 'def' => null, 'name' => '등록일'],
            ['val' => 'modDt', 'typ' => 's', 'def' => null, 'name' => '수정일']
        ];
    }

    //스타일기획 레퍼런스
    public static function tableImsRefStylePlan(): array
    {
        return [
            ['val' => 'sno', 'typ' => 'i', 'def' => 0, 'name' => '번호'],
            ['val' => 'regManagerSno', 'typ' => 'i', 'def' => null, 'name' => '등록직원 일련번호'],
            ['val' => 'refSeason', 'typ' => 's', 'def' => null, 'name' => '시즌코드(product테이블의 prdSeason과 동일)'],
            ['val' => 'refStyle', 'typ' => 's', 'def' => null, 'name' => '스타일코드(product테이블의 prdStyle과 동일)'],
            ['val' => 'refName', 'typ' => 's', 'def' => null, 'name' => '스타일명'],
            ['val' => 'refThumbImg', 'typ' => 's', 'def' => null, 'name' => '썸네일이미지'],
            ['val' => 'refType', 'typ' => 'i', 'def' => null, 'name' => '타입'], //0:없음, 1:납품스타일, 2:개발샘플, 3:납품스타일+개발샘플
            ['val' => 'refGender', 'typ' => 's', 'def' => null, 'name' => '성별(M:남자, F:여자, 값없는건 공용)'],
            ['val' => 'refUnitPrice', 'typ' => 'i', 'def' => null, 'name' => '단가'], //자동계산
            ['val' => 'mainFabricUnitPrice', 'typ' => 'i', 'def' => null, 'name' => '메인원단 단가'], //자동계산
            ['val' => 'mainFabricOnHandYn', 'typ' => 's', 'def' => null, 'name' => '메인원단 생지 유무'],
            ['val' => 'dollerRatio', 'typ' => 's', 'def' => null, 'name' => '환율'],
            ['val' => 'dollerRatioDt', 'typ' => 's', 'def' => null, 'name' => '환율기준일'],
            ['val' => 'regDt', 'typ' => 's', 'def' => null, 'name' => '등록일'],
            ['val' => 'modDt', 'typ' => 's', 'def' => null, 'name' => '수정일']
        ];
    }

    //스타일기획 레퍼런스 부가정보(브랜드, 컨셉, 디자인, 부가기능)
    public static function tableImsRefStylePlanAppendInfo(): array
    {
        return [
            ['val' => 'sno', 'typ' => 'i', 'def' => 0, 'name' => '번호'],
            ['val' => 'regManagerSno', 'typ' => 'i', 'def' => null, 'name' => '등록직원 일련번호'],
            ['val' => 'infoType', 'typ' => 'i', 'def' => null, 'name' => '유형'], //1:브랜드, 2:컨셉, 3:디자인, 4:부가기능
            ['val' => 'sortNum', 'typ' => 'i', 'def' => null, 'name' => '순서'],
            ['val' => 'infoName', 'typ' => 's', 'def' => null, 'name' => '부가정보명'],
            ['val' => 'regDt', 'typ' => 's', 'def' => null, 'name' => '등록일'],
            ['val' => 'modDt', 'typ' => 's', 'def' => null, 'name' => '수정일']
        ];
    }

    //스타일기획 레퍼런스 부가정보 릴레이션
    public static function tableImsRefStylePlanAppendInfoRelation(): array
    {
        return [
            ['val' => 'sno', 'typ' => 'i', 'def' => 0, 'name' => '번호'],
            ['val' => 'refStylePlanSno', 'typ' => 'i', 'def' => null, 'name' => '스타일기획 레퍼런스 일련번호'],
            ['val' => 'infoSno', 'typ' => 'i', 'def' => null, 'name' => '부가정보 일련번호'],
            ['val' => 'infoType', 'typ' => 'i', 'def' => null, 'name' => '유형'], //and 조건절애 사용. 1:브랜드, 2:컨셉, 3:디자인, 4:부가기능
            ['val' => 'regDt', 'typ' => 's', 'def' => null, 'name' => '등록일'],
            ['val' => 'modDt', 'typ' => 's', 'def' => null, 'name' => '수정일']
        ];
    }

    //스타일기획 레퍼런스 고객사 릴레이션
    public static function tableImsRefStylePlanCustomerRelation(): array
    {
        return [
            ['val' => 'sno', 'typ' => 'i', 'def' => 0, 'name' => '번호'],
            ['val' => 'refStylePlanSno', 'typ' => 'i', 'def' => null, 'name' => '스타일기획 레퍼런스 일련번호'],
            ['val' => 'customerSno', 'typ' => 'i', 'def' => null, 'name' => '고객사 일련번호'],
            ['val' => 'regDt', 'typ' => 's', 'def' => null, 'name' => '등록일'],
            ['val' => 'modDt', 'typ' => 's', 'def' => null, 'name' => '수정일']
        ];
    }

    //스타일기획 레퍼런스 원부자재 리스트
    public static function tableImsRefStylePlanMaterial(): array
    {
        return [
            ['val' => 'sno', 'typ' => 'i', 'def' => 0, 'name' => '번호'],
            ['val' => 'refStylePlanSno', 'typ' => 'i', 'def' => null, 'name' => '스타일기획 레퍼런스 일련번호'],
            ['val' => 'eachSno', 'typ' => 'i', 'def' => null, 'name' => '부가정보 일련번호'],
            ['val' => 'materialType', 'typ' => 's', 'def' => null, 'name' => '원부자재 유형'], //fabric:원단,충전재, subFabric:부자재, jsonUtil:기능, jsonMark:마크, jsonLaborCost:공임, jsonEtc:기타
            ['val' => 'sortNum', 'typ' => 'i', 'def' => null, 'name' => '순서'],
            ['val' => 'materialCode', 'typ' => 's', 'def' => null, 'name' => '원부자재/공임/기타 코드'],
            ['val' => 'materialNo', 'typ' => 's', 'def' => null, 'name' => '원부자재 부위'],
            ['val' => 'materialAttached', 'typ' => 's', 'def' => null, 'name' => '원부자재 부착위치'],
            ['val' => 'materialName', 'typ' => 's', 'def' => null, 'name' => '원부자재/공임/기타 이름'],
            ['val' => 'fabricMix', 'typ' => 's', 'def' => null, 'name' => '원부자재 혼용률'],
            ['val' => 'materialColor', 'typ' => 's', 'def' => null, 'name' => '원부자재 색상'],
            ['val' => 'materialSpec', 'typ' => 's', 'def' => null, 'name' => '원부자재 규격'],
            ['val' => 'materialQty', 'typ' => 's', 'def' => null, 'name' => '원부자재/공임/기타 가요척/수량'],
            ['val' => 'currencyUnit', 'typ' => 'i', 'def' => 1, 'name' => '화폐단위'], //1:원화, 2:달러화
            ['val' => 'unitPriceDoller', 'typ' => 's', 'def' => null, 'name' => '원부자재 단가(달러)'],
            ['val' => 'unitPrice', 'typ' => 'i', 'def' => null, 'name' => '원부자재/공임/기타 단가(원화)'],
            ['val' => 'makeNational', 'typ' => 's', 'def' => null, 'name' => '원부자재 제조국가'],
            ['val' => 'materialMoq', 'typ' => 's', 'def' => null, 'name' => '원부자재 MOQ'],
            ['val' => 'onHandYn', 'typ' => 's', 'def' => null, 'name' => '원부자재 생지 유무'],
            ['val' => 'btYn', 'typ' => 's', 'def' => null, 'name' => '원부자재 BT 유무'],
            ['val' => 'makePeriod', 'typ' => 's', 'def' => null, 'name' => '원부자재 생산기간(생지있는경우)'],
            ['val' => 'makePeriodNoOnHand', 'typ' => 's', 'def' => null, 'name' => '원부자재 생산기간(생지없는경우)'],
            ['val' => 'produceManagerSno', 'typ' => 'i', 'def' => null, 'name' => '생산처 일련번호'], //es_manager fk. 0값 가능
            ['val' => 'fabricCompany', 'typ' => 's', 'def' => null, 'name' => '원부자재 생산처'],
            ['val' => 'materialMemo', 'typ' => 's', 'def' => null, 'name' => '원부자재/공임/기타 메모'],
            ['val' => 'grpMaterialNames', 'typ' => 's', 'def' => null, 'name' => '원부자재 유사퀄리티 자재명(들)'],
            ['val' => 'regDt', 'typ' => 's', 'def' => null, 'name' => '등록일'],
            ['val' => 'modDt', 'typ' => 's', 'def' => null, 'name' => '수정일']
        ];
    }



}