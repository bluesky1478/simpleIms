<?php

/**
 * This is commercial software, only users who have purchased a valid license
 * and accept to the terms of the License Agreement can install and use this
 * program.
 *
 * Do not edit or add to this file if you wish to upgrade Godomall5 to newer
 * versions in the future.
 *
 * @copyright ⓒ 2016, NHN godo: Corp.
 * @link      http://www.godo.co.kr
 */

namespace Component\Database;

use Component\Ims\ImsCodeMap;
use Component\Ims\ImsJsonSchema;
use Component\Imsv2\ImsScheduleConfig;
use Component\Imsv2\ImsScheduleUtil;
use Session;
use SiteLabUtil\SlCommonUtil;

/**
 * DB Table 기본 Field 클래스 - DB 테이블의 기본 필드를 설정한 클래스 이며, prepare query 생성시 필요한 기본 필드 정보임
 * @package Component\Database
 * @static  tableConfig
 */
trait DBIms
{
    /**
     * IMS 코드 관리
     * @return array[]
     */
    public static function tableImsCode(): array
    {
        return [
            ['val' => 'sno', 'typ' => 'i', 'def' => null, 'name' => '일련번호'],
            ['val' => 'codeType', 'typ' => 's', 'def' => null, 'name' => '코드타입'],
            ['val' => 'codeDiv', 'typ' => 's', 'def' => null, 'name' => '코드구분'],
            ['val' => 'codeValueKr', 'typ' => 's', 'def' => null, 'name' => '코드한글값'],
            ['val' => 'codeValueEn', 'typ' => 's', 'def' => null, 'name' => '코드영문값'],
            ['val' => 'codeDescription', 'typ' => 's', 'def' => null, 'name' => '코드설명'],
            ['val' => 'codeSort', 'typ' => 'i', 'def' => 0, 'name' => '코드순서'],
            ['val' => 'regDt', 'typ' => 's', 'def' => null],
            ['val' => 'modDt', 'typ' => 's', 'def' => null],
        ];
    }

    /**
     *
     * @return array[]
     */
    public static function tableImsCustomer(): array
    {
        return [
            ['val' => 'sno', 'typ' => 'i', 'def' => null, 'name' => '일련번호'],
            ['val' => 'customerStatus', 'typ' => 'i', 'def' => 0, 'name' => '고객 상태'], //0신규, 1계약중, 2재입찰, 9유찰, 10이탈
            ['val' => 'busiCateSno', 'typ' => 'i', 'def' => 0, 'name' => '업종 일련번호'],
            ['val' => 'customerName', 'typ' => 's', 'def' => null, 'name' => '고객사명', 'required' => true],
            ['val' => 'styleCode', 'typ' => 's', 'def' => null, 'name' => '고객코드(Style 기본 Code)', 'required' => true],
            ['val' => 'salesType', 'typ' => 's', 'def' => null, 'name' => '영업형태'],
            ['val' => 'salesManagerSno', 'typ' => 'i', 'def' => null, 'name' => '영업담당자'],
            ['val' => 'industry', 'typ' => 's', 'def' => null, 'name' => '업종'],
            ['val' => 'contactName', 'typ' => 's', 'def' => null, 'name' => '담당자_이름'],
            ['val' => 'contactPosition', 'typ' => 's', 'def' => null, 'name' => '담당자_직함'],
            ['val' => 'contactZipcode', 'typ' => 's', 'def' => null, 'name' => '담당자_사무실주소(우편번호)'],
            ['val' => 'contactAddress', 'typ' => 's', 'def' => null, 'name' => '담당자_사무실주소'],
            ['val' => 'contactAddressSub', 'typ' => 's', 'def' => null, 'name' => '담당자_사무실주소(상세)'],
            ['val' => 'contactEmail', 'typ' => 's', 'def' => null, 'name' => '담당자_E-MAIL'],
            ['val' => 'contactMobile', 'typ' => 's', 'def' => null, 'name' => '담당자_휴대전화'],
            ['val' => 'contactGender', 'typ' => 's', 'def' => null, 'name' => '담당자_성별'],
            ['val' => 'contactPreference', 'typ' => 's', 'def' => null, 'name' => '담당자_성향'],
            ['val' => 'contactDept', 'typ' => 's', 'def' => null, 'name' => '담당자_부서'],
            ['val' => 'contactNumber', 'typ' => 's', 'def' => null, 'name' => '담당자_내선번호'],
            ['val' => 'contactAge', 'typ' => 'i', 'def' => null, 'name' => '담당자_나이'],
            ['val' => 'contactMemo', 'typ' => 's', 'def' => null, 'name' => '담당자_메모'],

            ['val' => 'customerDiv', 'typ' => 'i', 'def' => 0, 'name' => '고객상태'],
            ['val' => 'msContract', 'typ' => 's', 'def' => null, 'name' => '계약연도'],
            ['val' => 'msContractPeriod', 'typ' => 's', 'def' => null, 'name' => '계약기간'],
            ['val' => 'msContractMaintain', 'typ' => 's', 'def' => null, 'name' => '계약유지기간'],
            ['val' => 'msRemainPeriod', 'typ' => 's', 'def' => null, 'name' => '잔여계약기간'],
            ['val' => 'msRecontractCondition', 'typ' => 's', 'def' => null, 'name' => '재계약조건'],

            ['val' => 'use3pl', 'typ' => 's', 'def' => 'n', 'name' => '3PL 사용 여부', 'code'=> ImsCodeMap::YES_OR_NO_TYPE],
            ['val' => 'useMall', 'typ' => 's', 'def' => 'n', 'name' => '폐쇄몰 사용 여부', 'code'=> ImsCodeMap::YES_OR_NO_TYPE],
            ['val' => 'addedInfo', 'typ' => 's', 'def' => null, 'name' => '기타정보', 'json' => true, 'scheme'=>ImsJsonSchema::CUSTOMER_ADDINFO ], //json.

            ['val' => 'customerCost', 'typ' => 'i', 'def' => null, 'name' => '고객 매입가'],
            ['val' => 'customerPrice', 'typ' => 'i', 'def' => null, 'name' => '고객 매출가'],
            ['val' => 'customerRtwCost', 'typ' => 'i', 'def' => null, 'name' => '기성 매입가'],
            ['val' => 'customerRtwPrice', 'typ' => 'i', 'def' => null, 'name' => '기성 매출가'],
            ['val' => 'customerYearPrice', 'typ' => 's', 'def' => null, 'name' => '고객 연 매출가'],
            ['val' => 'customerYearRtw', 'typ' => 's', 'def' => null, 'name' => '고객 연 매입가'], //미사용
            ['val' => 'latestProjectSno', 'typ' => 'i', 'def' => null, 'name' => '최근 프로젝트 번호',],
            
            ['val' => 'regManagerSno', 'typ' => 'i', 'def' => null, 'name' => '최초등록자'],
            ['val' => 'lastManagerSno', 'typ' => 'i', 'def' => null, 'name' => '마지막수정자'],
            ['val' => 'regDt', 'typ' => 's', 'def' => null, 'name' => '등록시간'],
            ['val' => 'modDt', 'typ' => 's', 'def' => null, 'name' => '수정시간'],
        ];
    }

    public static function tableImsAddInfo(): array
    {
        return [
            ['val' => 'sno', 'typ' => 'i', 'def' => null, 'name' => '번호'],
            ['val' => 'fieldDiv', 'typ' => 's', 'def' => null, 'name' => '구분'],
            ['val' => 'customerSno', 'typ' => 'i', 'def' => null, 'name' => '고객사'],
            ['val' => 'projectSno', 'typ' => 'i', 'def' => null, 'name' => '프로젝트'],
            ['val' => 'styleSno', 'typ' => 'i', 'def' => null, 'name' => '스타일'],
            ['val' => 'eachSno', 'typ' => 'i', 'def' => null, 'name' => '기타번호'],
            ['val' => 'expectedDt', 'typ' => 's', 'def' => null, 'name' => '예정일'],
            ['val' => 'completeDt', 'typ' => 's', 'def' => null, 'name' => '완료일'],
            ['val' => 'memo', 'typ' => 's', 'def' => null, 'name' => '대체텍스트'],
            ['val' => 'etcMemo', 'typ' => 's', 'def' => null, 'name' => '메모/기타'],
            ['val' => 'alterText', 'typ' => 's', 'def' => null, 'name' => '대체문자'],
            ['val' => 'fieldStatus', 'typ' => 'i', 'def' => 0, 'name' => '상태'],
            ['val' => 'regManagerSno', 'typ' => 'i', 'def' => 0, 'name' => '등록자'],
            ['val' => 'commentCnt', 'typ' => 'i', 'def' => 0, 'name' => '코멘트수'],
            ['val' => 'regDt', 'typ' => 's', 'def' => null, 'name' => '등록일'],
            ['val' => 'modDt', 'typ' => 's', 'def' => null, 'name' => '수정일']
        ];
    }


    /**
     * 프로젝트 확장 정보
     * @return array[]
     */
    public static function tableImsProjectExt():array {
        $rslt = [
            ['val' => 'sno', 'typ' => 'i', 'def' => null, 'name' => '일련번호'],
            ['val' => 'priority', 'typ' => 'i', 'def' => null, 'name' => '우선순위'],
            ['val' => 'projectSno', 'typ' => 'i', 'def' => null, 'name' => '프로젝트번호'],
            ['val' => 'salesStatus', 'typ' => 's', 'def' => 'wait', 'name' => '영업상태' , 'code'=>ImsCodeMap::SALES_STATUS],
            ['val' => 'salesExDt', 'typ' => 's', 'def' => null, 'name' => '영업예정일'],
            ['val' => 'salesDeliveryDt', 'typ' => 's', 'def' => null, 'name' => '예정납기'],
            ['val' => 'salesStyleName', 'typ' => 's', 'def' => null, 'name' => '대표 스타일 명'],
            ['val' => 'salesStyleCount', 'typ' => 'i', 'def' => null, 'name' => '스타일 수량'],
            ['val' => 'commentCount', 'typ' => 's', 'def' => null, 'name' => '코멘트 수량 정보'],
            ['val' => 'addManager', 'typ' => 's', 'def' => null, 'name' => '추가 담당자'],
            ['val' => 'salesTarget', 'typ' => 's', 'def' => null, 'name' => '매출목표', 'code'=>ImsCodeMap::PERIOD_TYPE ],
            ['val' => 'contractDifficult', 'typ' => 's', 'def' => 0, 'name' => '계약난이도', 'code'=>ImsCodeMap::RATING_TYPE2],
            ['val' => 'extDesigner', 'typ' => 's', 'def' => null, 'name' => '투입 예정 디자이너', 'json' => true, 'strip'=>true],
            ['val' => 'extAmount', 'typ' => 's', 'def' => null, 'name' => '추정매출' ],
            ['val' => 'extMargin', 'typ' => 's', 'def' => null, 'name' => '예상마진'],
            ['val' => 'designWorkType', 'typ' => 'i', 'def' => 0,   'name' => '디자인 업무타입', 'code'=>ImsCodeMap::DESIGN_WORK_TYPE],
            ['val' => 'targetSalesYear', 'typ' => 's', 'def' => null, 'name' => '목표 매출 년도'],
            ['val' => 'accountingMessage', 'typ' => 's', 'def' => null, 'name' => '회계 전달 메세지', 'strip' => true],
            ['val' => 'planScheMemo', 'typ' => 's', 'def' => null, 'name' => '최초기획일정 비고'],

            ['val' => 'designTeamInfo', 'typ' => 'i', 'def' => null, 'name' => '디자인팀 참여정보'], //, 'code'=>ImsCodeMap::DESIGN_JOIN_TYPE
            ['val' => 'salesMemo', 'typ' => 's', 'def' => null, 'name' => '영업메모', 'strip' => true],
            ['val' => 'holdMemo', 'typ' => 's', 'def' => null, 'name' => '유찰/보류 메모', 'strip' => true],

            ['val' => 'extPriceStatus', 'typ' => 'i', 'def' => null, 'name' => '프로젝트 매출가 상태'],
            ['val' => 'extPrice', 'typ' => 'i', 'def' => null, 'name' => '프로젝트 매출가'],
            ['val' => 'extCostStatus', 'typ' => 'i', 'def' => null, 'name' => '프로젝트 매입가 상태'],
            ['val' => 'extCost', 'typ' => 'i', 'def' => null, 'name' => '프로젝트 매입가'],

            ['val' => 'regDt', 'typ' => 's', 'def' => null, 'name' => '등록시간'],
            ['val' => 'modDt', 'typ' => 's', 'def' => null, 'name' => '수정시간'],
        ];
        //스케쥴 필드
        $ims25Field = ImsScheduleUtil::getScheduleMap();
        foreach( $ims25Field as $fieldKey => $fieldName ){
            foreach(ImsScheduleConfig::SCHEDULE_TYPE as $type){
                $fieldType = 's';
                $fieldDefault = null;
                if( 'st' === $type ){
                    $fieldType = 'i';
                    $fieldDefault = 0;
                }
                $rslt[] = ['val' => $type.ucfirst($fieldKey), 'typ' => $fieldType, 'def' => $fieldDefault, 'name' => $fieldName];
            }
        }
        return $rslt;
    }

    /**
     * 프로젝트 추가 참여자
     * @return array
     */
    public static function tableImsProjectManager():array
    {
        return [
            ['val' => 'sno', 'typ' => 'i', 'def' => null, 'name' => '일련번호'],
            ['val' => 'projectSno', 'typ' => 'i', 'def' => null, 'name' => '프로젝트번호'],
            ['val' => 'scheduleDiv', 'typ' => 's', 'def' => null, 'name' => '스케쥴구분'],
            ['val' => 'scheduleStatus', 'typ' => 'i', 'def' => 0, 'name' => '참여상태(0:x, 1:참여)'],  //ex-not-empty => 1  , exEmpty - cp or tx not empty => 0
            ['val' => 'managerSno', 'typ' => 'i', 'def' => null, 'name' => '담당자'],
            ['val' => 'regDt', 'typ' => 's', 'def' => null, 'name' => '등록시간'],
            ['val' => 'modDt', 'typ' => 's', 'def' => null, 'name' => '수정시간'],
        ];
    }


    /**
     * 프로젝트
     * @return array
     */
    public static function tableImsProject(): array
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
            ['val' => 'produceType', 'typ' => 'i', 'def' => 0, 'name' => '제작형태', 'code' => ImsCodeMap::PRODUCE_TYPE  ],
            ['val' => 'projectMemo', 'typ' => 's', 'def' => null, 'name' => '프로젝트메모(고객사 요청사항)', 'strip' => true],
            ['val' => 'salesManagerSno', 'typ' => 'i', 'def' => 0, 'name' => '영업담당자'],
            ['val' => 'salesManagerName', 'typ' => 's', 'def' => null, 'name' => '영업담당자'],
            ['val' => 'salesStartDt', 'typ' => 's', 'def' => null, 'name' => '업무시작일'],

        //스케쥴 관리 관련 ------------------------------
            ['val' => 'customerOrderDt', 'typ' => 's', 'def' => null, 'name' => '고객발주일'], //★ 아소트확정일과 동일해야함
            //제안서 (상세 스케쥴을 안쓰면 가능)
            ['val' => 'proposalConfirm', 'typ' => 's', 'def' => 'n', 'name' => '제안서승인'],
            //생산가
            ['val' => 'prdCostApproval', 'typ' => 's', 'def' => 'n', 'name' => '생산가 결재 승인'],
            //판매가
            ['val' => 'prdPriceApproval', 'typ' => 's', 'def' => 'n', 'name' => '판매가 결재 승인'],
            //영업 기획서
            ['val' => 'salesPlanApproval', 'typ' => 's', 'def' => 'n', 'name' => '영업기획서 결재 승인'],

            //이노버 발주
            ['val' => 'customerOrderDeadLine', 'typ' => 's', 'def' => null, 'name' => '발주DL'], //★ 발주예정
            ['val' => 'customerOrderDeadLineText', 'typ' => 's', 'def' => null, 'name' => '발주DL대체'], //★ 발주예정
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
            ['val' => 'designManagerSno', 'typ' => 'i', 'def' => 0, 'name' => '디자인담당자'],
            ['val' => 'designManagerName', 'typ' => 's', 'def' => null, 'name' => '디자인담당자'],
            ['val' => 'designEndDt', 'typ' => 's', 'def' => null, 'name' => '디자인마감일'],

            ['val' => 'planDt', 'typ' => 's', 'def' => null, 'name' => '기획서예정일'],
            ['val' => 'planEndDt', 'typ' => 's', 'def' => null, 'name' => '기획서완료일'],

            ['val' => 'planConfirm', 'typ' => 's', 'def' => 'n', 'name' => '기획서승인'], //n 대기, r 승인요청 ,p승인 ,f반려 ?
            ['val' => 'planMemo', 'typ' => 's', 'def' => null, 'name' => '기획서비고', 'strip' => true],
            ['val' => 'sampleStartDt', 'typ' => 's', 'def' => null, 'name' => '샘플예정일'],
            ['val' => 'sampleEndDt', 'typ' => 's', 'def' => null, 'name' => '샘플완료일'],
            ['val' => 'sampleConfirm', 'typ' => 's', 'def' => 'n', 'name' => '샘플승인'],

            ['val' => 'proposalDt', 'typ' => 's', 'def' => null, 'name' => '제안서발송일'], //구)제안서 예정일 재사용
            
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
            ['val' => 'workStatus', 'typ' => 'i', 'def' => 0, 'name' => '작지처리상태'], //Project WorkStatus
            ['val' => 'costStatus', 'typ' => 'i', 'def' => 0, 'name' => '생산가확정처리상태'],
            ['val' => 'estimateStatus', 'typ' => 'i', 'def' => 0, 'name' => '견적처리상태'],

            //['val' => 'costConfirm', 'typ' => 'i', 'def' => 0, 'name' => ''],
            //['val' => 'costConfirm', 'typ' => 'i', 'def' => 0, 'name' => ''],

            ['val' => 'orderStatus', 'typ' => 'i', 'def' => null, 'name' => '가발주처리상태'],
            ['val' => 'productionStatus', 'typ' => 'i', 'def' => 0, 'name' => '생산상태', 'code'=> ImsCodeMap::IMS_PRD_PROC_STATUS ],
            ['val' => 'packingYn', 'typ' => 's', 'def' => '', 'name' => '분류패킹여부', 'code'=> ImsCodeMap::YES_OR_NO_TYPE],
            ['val' => 'directDeliveryYn', 'typ' => 's', 'def' => '', 'name' => '직접납품여부', 'code'=> ImsCodeMap::YES_OR_NO_TYPE],
            ['val' => 'deliveryMethod', 'typ' => 's', 'def' => null, 'name' => '납기방법', 'strip' => true], //납품 계획 메모
            ['val' => 'deliveryCostMemo', 'typ' => 's', 'def' => null, 'name' => '배송비용협의사항', 'strip' => true],
            ['val' => 'syncProduct', 'typ' => 's', 'def' => 'y', 'name' => '프로젝트 상품 연동상태'],
            ['val' => 'use3pl', 'typ' => 's', 'def' => 'n', 'name' => '3PL 사용 여부', 'code'=> ImsCodeMap::YES_OR_NO_TYPE ],
            ['val' => 'useMall', 'typ' => 's', 'def' => 'n', 'name' => '폐쇄몰 사용 여부', 'code'=> ImsCodeMap::YES_OR_NO_TYPE ],

            ['val' => 'priceStatus', 'typ' => 'i', 'def' => 0, 'name' => '판매가확정상태'],

            ['val' => 'bizPlanYn', 'typ' => 's', 'def' => 'n', 'name' => '사업계획 포함여부'],
            ['val' => 'bizPlanYear', 'typ' => 'i', 'def' => null, 'name' => '사업계획 년도'],

            ['val' => 'prdConfirmApproval', 'typ' => 's', 'def' => 'n', 'name' => '사양서 결재 승인'],

            ['val' => 'projectReady', 'typ' => 's', 'def' => 'y', 'name' => '프로젝트 준비 완료'],
            ['val' => 'srcProjectSno', 'typ' => 'i', 'def' => null, 'name' => '원본프로젝트'],

            ['val' => 'nextSeason', 'typ' => 'i', 'def' => 0, 'name' => '다음시즌리오더상태'],

            ['val' => 'assortMemo', 'typ' => 's', 'def' => null, 'name' => '아소트 비고', 'strip' => true],
            //아소트 입력 URL 발신정보
            ['val' => 'assortReceiver', 'typ' => 's', 'def' => null, 'name' => '아소트 입력 URL 수신자'],
            ['val' => 'assortEmail', 'typ' => 's', 'def' => null, 'name' => '아소트 입력 URL 수신 이메일'],
            ['val' => 'assortSendDt', 'typ' => 's', 'def' => '', 'name' => '아소트 입력URL 발신일 '],
            ['val' => 'assortApproval', 'typ' => 's', 'def' => 'n', 'name' => '아소트 결재 승인', 'code'=> ImsCodeMap::PROJECT_CONFIRM_TYPE_SIMPLE], //최종상태
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
            ['val' => 'regManagerName', 'typ' => 's', 'def' => null, 'name' => '최초등록자'],
            ['val' => 'lastManagerSno', 'typ' => 'i', 'def' => null, 'name' => '마지막수정자'],
            ['val' => 'lastManagerName', 'typ' => 's', 'def' => null, 'name' => '마지막수정자'],

            ['val' => 'isBookRegistered', 'typ' => 's', 'def' => 'n', 'name' => '회계반영여부'],
            ['val' => 'isBookRegisteredDt', 'typ' => 's', 'def' => null, 'name' => '회계반영날짜'],
            ['val' => 'refineOrder', 'typ' => 's', 'def' => 'n', 'name' => '작지정제'],
            ['val' => 'refineOrderDt', 'typ' => 's', 'def' => null, 'name' => '작지정제날짜'],
            ['val' => 'confirmStock', 'typ' => 's', 'def' => 'n', 'name' => '수량확인'],
            ['val' => 'confirmStockDt', 'typ' => 's', 'def' => null, 'name' => '수량확인날짜'],

            ['val' => 'projectCost', 'typ' => 'i', 'def' => null, 'name' => '프로젝트 매입가'],
            ['val' => 'projectPrice', 'typ' => 'i', 'def' => null, 'name' => '프로젝트 매출가'],

            ['val' => 'projectConfirm', 'typ' => 's', 'def' => null, 'name' => '프로젝트확인'],

            ['val' => 'regDt', 'typ' => 's', 'def' => null, 'name' => '등록시간'],
            ['val' => 'modDt', 'typ' => 's', 'def' => null, 'name' => '수정시간'],

        ];
    }

    /**
     * 스타일. 혹은 상품.
     * @return array
     */
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
            ['val' => 'prdType', 'typ' => 'i', 'def' => 'ALL', 'name' => '타입'], //신규. 리오더. 리오더개선. 기성. 추가...
            ['val' => 'prdSeason', 'typ' => 's', 'def' => 'ALL', 'name' => '시즌'],
            ['val' => 'prdGender', 'typ' => 's', 'def' => null, 'name' => '성별', 'code' => ImsCodeMap::SEX_CODE],
            ['val' => 'prdStyle', 'typ' => 's', 'def' => null, 'name' => '스타일'],
            ['val' => 'prdColor', 'typ' => 's', 'def' => null, 'name' => '색상'],
            ['val' => 'produceType', 'typ' => 's', 'def' => 1, 'name' => '생산 구분', 'code' => ImsCodeMap::PRODUCE_TYPE],
            ['val' => 'produceCompanySno', 'typ' => 'i', 'def' => null, 'name' => '생산 업체'],
            ['val' => 'produceNational', 'typ' => 's', 'def' => null, 'name' => '생산국가'],
            ['val' => 'customerDeliveryDt', 'typ' => 's', 'def' => null, 'name' => '고객납기(스타일)'],
            ['val' => 'msDeliveryDt', 'typ' => 's', 'def' => null, 'name' => '이노버납기(스타일)'],
            ['val' => 'prdExQt', 'typ' => 'i', 'def' => null, 'name' => '기획 수량'],
            ['val' => 'msQty', 'typ' => 'i', 'def' => null, 'name' => '미청구수량'],
            ['val' => 'prdMoq', 'typ' => 'i', 'def' => null, 'name' => '생산 MOQ'],
            ['val' => 'priceMoq', 'typ' => 'i', 'def' => null, 'name' => '단가 MOQ'],
            ['val' => 'fabricMoq', 'typ' => 'i', 'def' => null, 'name' => '원단 MOQ'],

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
            ['val' => 'memo', 'typ' => 's', 'def' => null, 'name' => '메모', 'strip' => true],
            ['val' => 'sizeOption', 'typ' => 's', 'def' => null, 'name' => '사이즈', 'json' => true], //JSON
            ['val' => 'typeOption', 'typ' => 's', 'def' => null, 'name' => '타입', 'json' => true], //JSON

            ['val' => 'fabric', 'typ' => 's', 'def' => null, 'name' => '원단', 'json' => true], //JSON
            ['val' => 'subFabric', 'typ' => 's', 'def' => null, 'name' => '부자재', 'json' => true], //JSON
            ['val' => 'sizeSpec', 'typ' => 's', 'def' => null, 'name' => '사이즈스펙', 'json' => true], //JSON
            //미사용 - 과거 원부자재 자료. (끝)
            ['val' => 'fabricStatus', 'typ' => 'i', 'def' => 0, 'name' => '퀄리티 상태'],
            ['val' => 'fabricPass', 'typ' => 's', 'def' => 'n', 'name' => '퀄리티 PASS'],
            ['val' => 'fabricNational', 'typ' => 'i', 'def' => 0, 'name' => '퀄리티 제조국'],
            ['val' => 'btStatus', 'typ' => 'i', 'def' => 0, 'name' => 'BT처리상태'], //BT 처리 상태.

            ['val' => 'fileWork', 'typ' => 's', 'def' => null, 'name' => '파일작지'], //프로젝SNO가 기록될수도 있음 <- 프로젝트 단위 작지
            ['val' => 'fileThumbnail', 'typ' => 's', 'def' => null, 'name' => '썸네일_기획'],
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
            ['val' => 'parentSno', 'typ' => 'i', 'def' => null, 'name' => '리오더 원본 상품번호'],
            ['val' => 'issueMemo', 'typ' => 's', 'def' => null, 'name' => '리비젼 이슈 메모'],

            ['val' => 'sort', 'typ' => 'i', 'def' => 0, 'name' => '정렬'],
            ['val' => 'delFl', 'typ' => 's', 'def' => 'n', 'name' => '삭제여부'],
            ['val' => 'regDt', 'typ' => 's', 'def' => null, 'name' => '등록시간'],
            ['val' => 'modDt', 'typ' => 's', 'def' => null, 'name' => '수정시간'],

            ['val' => 'planConfirmSno', 'typ' => 'i', 'def' => 0, 'name' => '확정기획 일련번호'],
            ['val' => 'packingSno', 'typ' => 'i', 'def' => 0, 'name' => '납품(발주) 일련번호'],
        ];
    }


    /**
     * 미팅 정보 ( 프로젝트 1:N )
     * @return array[]
     */
    public static function tableImsMeeting(): array
    {
        return [
            ['val' => 'sno', 'typ' => 'i', 'def' => null, 'name' => '일련번호'],
            ['val' => 'projectSno', 'typ' => 'i', 'def' => null, 'name' => '프로젝트번호'],
            ['val' => 'meetingDt', 'typ' => 's', 'def' => null, 'name' => '미팅일자'],
            ['val' => 'meetingTime', 'typ' => 's', 'def' => null, 'name' => '미팅시간'],
            ['val' => 'location', 'typ' => 's', 'def' => null, 'name' => '장소'],
            ['val' => 'readyDeadLineDt', 'typ' => 's', 'def' => null, 'name' => '준비마감일'],
            ['val' => 'readyItem', 'typ' => 's', 'def' => null, 'name' => '준비품목'],
            ['val' => 'readySample', 'typ' => 's', 'def' => null, 'name' => '준비샘플'],
            ['val' => 'readyContents', 'typ' => 's', 'def' => null, 'name' => '사전준비'],
            ['val' => 'regManagerSno', 'typ' => 'i', 'def' => null, 'name' => '최초등록자'],
            ['val' => 'lastManagerSno', 'typ' => 'i', 'def' => null, 'name' => '마지막수정자'],
            ['val' => 'regDt', 'typ' => 's', 'def' => null, 'name' => '등록시간'],
            ['val' => 'modDt', 'typ' => 's', 'def' => null, 'name' => '수정시간'],
        ];
    }


    /**
     * 업데이트 이력
     * @return array[]
     */
    public static function tableImsUpdateHistory(): array
    {
        return [
            ['val' => 'sno', 'typ' => 'i', 'def' => null, 'name' => '일련번호'],
            ['val' => 'tableName', 'typ' => 's', 'def' => null, 'name' => '업데이트 테이블'],
            ['val' => 'tableSno', 'typ' => 'i', 'def' => null, 'name' => '테이블일련번호'],
            ['val' => 'contents', 'typ' => 's', 'def' => null, 'name' => '업데이트 이전 내역'],
            ['val' => 'comment', 'typ' => 's', 'def' => null, 'name' => '업데이트 내역 코멘트'],
            ['val' => 'regManagerSno', 'typ' => 'i', 'def' => null, 'name' => '수정자'],
            ['val' => 'regDt', 'typ' => 's', 'def' => null],
            ['val' => 'modDt', 'typ' => 's', 'def' => null],
        ];
    }

    /**
     * 스케쥴 업데이트 이력
     * @return array[]
     */
    public static function tableImsScheduleHistory(): array
    {
        return [
            ['val' => 'sno', 'typ' => 'i', 'def' => null, 'name' => '일련번호'],
            ['val' => 'projectSno', 'typ' => 'i', 'def' => null, 'name' => '프로젝트 번호'],
            ['val' => 'scheduleDiv', 'typ' => 's', 'def' => null, 'name' => '스케쥴 구분'],
            ['val' => 'before', 'typ' => 'i', 'def' => null, 'name' => '이전'],
            ['val' => 'after', 'typ' => 's', 'def' => null, 'name' => '이후'],
            ['val' => 'comment', 'typ' => 's', 'def' => null, 'name' => '변경 코멘트'],
            ['val' => 'regManagerSno', 'typ' => 'i', 'def' => null, 'name' => '수정자'],
            ['val' => 'regDt', 'typ' => 's', 'def' => null],
            ['val' => 'modDt', 'typ' => 's', 'def' => null],
        ];
    }

    /**
     * 상태값 변경 히스토리
     * @return array[]
     */
    public static function tableImsStatusHistory(): array
    {
        return [
            ['val' => 'sno', 'typ' => 'i', 'def' => null, 'name' => '일련번호'],
            ['val' => 'customerSno', 'typ' => 'i', 'def' => null, 'name' => '고객번호'],
            ['val' => 'projectSno', 'typ' => 'i', 'def' => null, 'name' => '프로젝트번호'],
            ['val' => 'styleSno', 'typ' => 'i', 'def' => null, 'name' => '스타일번호'],
            ['val' => 'eachSno', 'typ' => 'i', 'def' => null, 'name' => '스타일하위번호'], //ex: 샘플번호, 원단번호, 생산번호..
            ['val' => 'historyDiv', 'typ' => 's', 'def' => null, 'name' => '이력 구분'],
            ['val' => 'historyDivName', 'typ' => 's', 'def' => null, 'name' => '이력명'],
            ['val' => 'beforeStatus', 'typ' => 's', 'def' => null, 'name' => '이전상태'],
            ['val' => 'afterStatus', 'typ' => 's', 'def' => null, 'name' => '변경상태'],
            ['val' => 'reason', 'typ' => 's', 'def' => null, 'name' => '사유'],
            ['val' => 'regManagerSno', 'typ' => 'i', 'def' => null, 'name' => '변경자'],
            ['val' => 'regDt', 'typ' => 's', 'def' => null],
            ['val' => 'modDt', 'typ' => 's', 'def' => null],
        ];
    }

    public static function tableImsProjectProductTmp(): array
    {
        return DBIms::tableImsProjectProduct();
    }

    /*public static function tableImsProductStatus(): array
    {
        return [
            ['val' => 'sno', 'typ' => 'i', 'def' => null, 'name' => '일련번호'],
            ['val' => 'regDt', 'typ' => 's', 'def' => null, 'name' => '등록시간'],
            ['val' => 'modDt', 'typ' => 's', 'def' => null, 'name' => '수정시간']
        ];
    }*/

    /**
     * 샘플정보
     * @return array
     */
    public static function tableImsSample(): array
    {
        return [
            ['val' => 'sno', 'typ' => 'i', 'def' => null, 'name' => '번호'],
            ['val' => 'customerSno', 'typ' => 'i', 'def' => null, 'name' => '고객 번호', 'required' => true],
            ['val' => 'projectSno', 'typ' => 'i', 'def' => null, 'name' => '프로젝트 번호', 'required' => true],
            ['val' => 'styleSno', 'typ' => 'i', 'def' => null, 'name' => '스타일 번호', 'required' => true],
            ['val' => 'sampleName', 'typ' => 's', 'def' => null, 'name' => '샘플명', 'required' => true],
            ['val' => 'sampleFactorySno', 'typ' => 'i', 'def' => null, 'name' => '샘플실'],
            ['val' => 'patternFactorySno', 'typ' => 'i', 'def' => null, 'name' => '패턴실'],
            ['val' => 'fitSpecSno', 'typ' => 'i', 'def' => null, 'name' => '사이즈스펙 일련번호'],
            ['val' => 'productPlanSno', 'typ' => 'i', 'def' => 0, 'name' => '스타일기획 일련번호'],
            ['val' => 'sampleFactoryBeginDt', 'typ' => 's', 'def' => null, 'name' => '샘플 투입일'],
            ['val' => 'sampleFactoryEndDt', 'typ' => 's', 'def' => null, 'name' => '샘플실 마감일'],
            ['val' => 'sampleCount', 'typ' => 'i', 'def' => 1, 'name' => '제작수량'],
            ['val' => 'sampleTerm', 'typ' => 'i', 'def' => 1, 'name' => '제작차수'],
            ['val' => 'sampleType', 'typ' => 'i', 'def' => 1, 'name' => '샘플구분'],
            ['val' => 'fitName', 'typ' => 's', 'def' => null, 'name' => '사이즈스펙 핏이름'],
            ['val' => 'fitSize', 'typ' => 'i', 'def' => null, 'name' => '사이즈스펙 기준사이즈'],
            ['val' => 'recentLocation', 'typ' => 's', 'def' => null, 'name' => '최근샘플위치'],
            ['val' => 'jsonLocation', 'typ' => 's', 'def' => null, 'name' => '샘플위치json', 'json' => true],
            ['val' => 'jsonFitSpec', 'typ' => 's', 'def' => null, 'name' => '사이즈스펙json', 'json' => true],
            ['val' => 'jsonFixedSpec', 'typ' => 's', 'def' => null, 'name' => '확정스펙json', 'json' => true],

            ['val' => 'fabric', 'typ' => 's', 'def' => null, 'name' => '원단', 'json' => true],
            ['val' => 'subFabric', 'typ' => 's', 'def' => null, 'name' => '부자재', 'json' => true],
            ['val' => 'jsonUtil', 'typ' => 's', 'def' => null, 'name' => '기능json', 'json' => true],
            ['val' => 'jsonMark', 'typ' => 's', 'def' => null, 'name' => '마크json', 'json' => true],
            ['val' => 'jsonLaborCost', 'typ' => 's', 'def' => null, 'name' => '공임json', 'json' => true],
            ['val' => 'jsonEtc', 'typ' => 's', 'def' => null, 'name' => '기타json', 'json' => true],
            ['val' => 'jsonReviewSpec', 'typ' => 's', 'def' => null, 'name' => '리뷰서 - 사이즈스펙 피팅체크', 'json' => true],
            ['val' => 'jsonReviewCheck', 'typ' => 's', 'def' => null, 'name' => '피팅체크', 'json' => true],
            ['val' => 'jsonReviewComment', 'typ' => 's', 'def' => null, 'name' => '샘플실의견', 'json' => true],
            ['val' => 'jsonConfirmSpec', 'typ' => 's', 'def' => null, 'name' => '확정서 - 사이즈스펙 확정사이즈', 'json' => true],
            ['val' => 'jsonConfirmSuggest', 'typ' => 's', 'def' => null, 'name' => '이노버 추가 제안 내용 (사이즈스펙 외)', 'json' => true],
            ['val' => 'jsonConfirmRequest', 'typ' => 's', 'def' => null, 'name' => '고객사 요청 사항', 'json' => true],
            ['val' => 'jsonConfirmGuide', 'typ' => 's', 'def' => null, 'name' => '안내사항', 'json' => true],

            ['val' => 'dollerRatio', 'typ' => 's', 'def' => '0.00', 'name' => '환율'],
            ['val' => 'dollerRatioDt', 'typ' => 's', 'def' => null, 'name' => '환율기준일'],
            ['val' => 'fabricCost', 'typ' => 'i', 'def' => 0, 'name' => '원단가격'],
            ['val' => 'subFabricCost', 'typ' => 'i', 'def' => 0, 'name' => '부자재가격'],
            ['val' => 'addCost', 'typ' => 'i', 'def' => 0, 'name' => '공임비용'],
            ['val' => 'sampleUnitCost', 'typ' => 'i', 'def' => 0, 'name' => '샘플단가'],
            ['val' => 'sampleCost', 'typ' => 'i', 'def' => 0, 'name' => '샘플비용'],
            ['val' => 'sampleConfirm', 'typ' => 's', 'def' => 'n', 'name' => '확정여부'],
            ['val' => 'sampleConfirmManager', 'typ' => 's', 'def' => 'n', 'name' => '확정여부'],
            ['val' => 'sampleConfirmDt', 'typ' => 's', 'def' => 'n', 'name' => '확정여부'],
            ['val' => 'sampleMemo', 'typ' => 's', 'def' => null, 'name' => '샘플 메모'],
            ['val' => 'sampleManagerSno', 'typ' => 'i', 'def' => null, 'name' => '샘플담당자'],
            ['val' => 'patternRequestDt', 'typ' => 's', 'def' => null, 'name' => '패턴의뢰일'],
            ['val' => 'patternDeliveryDt', 'typ' => 's', 'def' => null, 'name' => '패턴납기일'],
            ['val' => 'sampleRequestDt', 'typ' => 's', 'def' => null, 'name' => '샘플의뢰일'],
            ['val' => 'sampleDeliveryDt', 'typ' => 's', 'def' => null, 'name' => '샘플납기일'],
            ['val' => 'sampleFile1Approval', 'typ' => 's', 'def' => 'n', 'name' => '샘플의뢰서(지시서) 승인여부'],
            ['val' => 'regDt', 'typ' => 's', 'def' => null, 'name' => '등록시간'],
            ['val' => 'modDt', 'typ' => 's', 'def' => null, 'name' => '수정시간']
        ];
    }


    /**
     * 상태값 변경 히스토리
     * @return array[]
     */
    public static function tableImsFile(): array
    {
        return [
            ['val' => 'sno', 'typ' => 'i', 'def' => null, 'name' => '일련번호'],
            ['val' => 'customerSno', 'typ' => 'i', 'def' => null, 'name' => '고객번호'],
            ['val' => 'projectSno', 'typ' => 'i', 'def' => null, 'name' => '프로젝트번호'],
            ['val' => 'styleSno', 'typ' => 'i', 'def' => null, 'name' => '스타일번호'],
            ['val' => 'eachSno', 'typ' => 'i', 'def' => null, 'name' => '스타일하위번호'], //ex: 샘플번호, 원단번호, 생산번호..
            ['val' => 'fileDiv', 'typ' => 's', 'def' => null, 'name' => '파일구분'],
            ['val' => 'fileList', 'typ' => 's', 'def' => null, 'name' => '파일정보'],
            ['val' => 'memo', 'typ' => 's', 'def' => null, 'name' => '메모'],
            ['val' => 'rev', 'typ' => 'i', 'def' => null, 'name' => 'revision'],
            ['val' => 'regManagerSno', 'typ' => 'i', 'def' => null, 'name' => '변경자'],
            ['val' => 'regDt', 'typ' => 's', 'def' => null],
            ['val' => 'modDt', 'typ' => 's', 'def' => null],
        ];
    }

    /**
     * 선행 준비 작업
     *   0      1         2         3    4         5
     *  요청    처리중    처리완료    처리불가    재요청    승인
     * @return array[]
     */
    public static function tableImsPrepared(): array
    {
        return [
            ['val' => 'sno', 'typ' => 'i', 'def' => null, 'name' => '일련번호'],
            ['val' => 'projectSno', 'typ' => 'i', 'def' => null, 'name' => '프로젝트번호'],
            ['val' => 'preparedStatus', 'typ' => 'i', 'def' => 0, 'name' => '상태'],
            ['val' => 'preparedType', 'typ' => 's', 'def' => null, 'name' => '준비타입'],
            ['val' => 'produceCompanySno', 'typ' => 'i', 'def' => null, 'name' => '생산처'],
            ['val' => 'reqCnt', 'typ' => 'i', 'def' => 0, 'name' => '재요청차수'],
            ['val' => 'contents', 'typ' => 's', 'def' => null, 'name' => '기타정보', 'json' => true],
            ['val' => 'reqMemo', 'typ' => 's', 'def' => null, 'name' => '요청자 메모'],
            ['val' => 'procMemo', 'typ' => 's', 'def' => null, 'name' => '처리자 메모'],
            ['val' => 'acceptMemo', 'typ' => 's', 'def' => null, 'name' => '승인메모'],
            ['val' => 'acceptManager', 'typ' => 's', 'def' => null, 'name' => '승인처리자'],
            ['val' => 'acceptDt', 'typ' => 's', 'def' => null, 'name' => '승인처리일자'],
            ['val' => 'deadLineDt', 'typ' => 's', 'def' => null, 'name' => '완료 요청일'], //완료 요청일
            ['val' => 'completeDt', 'typ' => 's', 'def' => null, 'name' => '처리 완료일'], //처리완료일
            ['val' => 'regManagerSno', 'typ' => 'i', 'def' => null, 'name' => '등록자'],
            ['val' => 'lastManagerSno', 'typ' => 'i', 'def' => null, 'name' => '마지막수정자'],
            ['val' => 'regDt', 'typ' => 's', 'def' => null], //의뢰일
            ['val' => 'modDt', 'typ' => 's', 'def' => null], //수정일
        ];
    }

    /**
     * 생산관리
     * @return array[]
     */
    public static function tableImsProduce(): array
    {
        return [
            ['val' => 'sno', 'typ' => 'i', 'def' => null, 'name' => '일련번호'],
            ['val' => 'projectSno', 'typ' => 'i', 'def' => null, 'name' => '프로젝트번호'],
            ['val' => 'produceCompanySno', 'typ' => 'i', 'def' => null, 'name' => '생산처'],
            ['val' => 'produceStatus', 'typ' => 'i', 'def' => 10, 'name' => '상태'],
            ['val' => 'prdStep', 'typ' => 's', 'def' => null, 'name' => '스텝별정보', 'json' => true],

            ['val' => 'shipExpectedDt', 'typ' => 's', 'def' => null, 'name' => '선적(예정)', 'json' => true],
            ['val' => 'shipCompleteDt', 'typ' => 's', 'def' => null, 'name' => '선적(확정)', 'json' => true],
            ['val' => 'globalDeliveryDiv', 'typ' => 's', 'def' => null, 'name' => '운송(구해외배송방법)', 'json' => true],
            ['val' => 'planPayDiv', 'typ' => 's', 'def' => null, 'name' => '비행기배송', 'json' => true], //이노버 , 생산처 , 고객
            ['val' => 'planPayMemo', 'typ' => 's', 'def' => null, 'name' => '비행기배송메모', 'json' => true],
            ['val' => 'privateMallDeliveryDt', 'typ' => 's', 'def' => null, 'name' => '폐쇄몰출고가능일', 'json' => true],
            ['val' => 'deliveryMethod', 'typ' => 's', 'def' => null, 'name' => '납품방법', 'json' => true],

            ['val' => 'confirmMemo', 'typ' => 's', 'def' => null, 'name' => '확정메모'],
            ['val' => 'memo', 'typ' => 's', 'def' => null, 'name' => '비고'],
            ['val' => 'msMemo', 'typ' => 's', 'def' => null, 'name' => '이노버 메모'],
            ['val' => 'regManagerSno', 'typ' => 'i', 'def' => null, 'name' => '등록자'],
            ['val' => 'lastManagerSno', 'typ' => 'i', 'def' => null, 'name' => '마지막수정자'],
            ['val' => 'regDt', 'typ' => 's', 'def' => null, 'name' => '의뢰일'],
            ['val' => 'modDt', 'typ' => 's', 'def' => null, 'name' => '수정일'],
        ];
    }

    /**
     * 샘플실정보
     * @return array[]
     */
    public static function tableImsSampleFactory()
    {
        return [
            ['val' => 'sno', 'typ' => 'i', 'def' => null, 'name' => '일련번호'],
            ['val' => 'factoryName', 'typ' => 's', 'def' => null, 'name' => '샘플실 명'],
            ['val' => 'factoryType', 'typ' => 's', 'def' => null, 'name' => '샘플실 타입', 'checkbox_sum'=>true],
            ['val' => 'factoryPhone', 'typ' => 's', 'def' => null, 'name' => '샘플실 전화'],
            ['val' => 'factoryAddress', 'typ' => 's', 'def' => null, 'name' => '샘플실 주소'],
            ['val' => 'regDt', 'typ' => 's', 'def' => null, 'name' => '등록일'],
            ['val' => 'modDt', 'typ' => 's', 'def' => null, 'name' => '수정일'],
        ];
    }

    /**
     * 삭제 이력
     * @return array[]
     */
    public static function tableImsDeleteHistory(): array
    {
        return DBIms::tableImsUpdateHistory();
    }


    /**
     * 영업 고객 관리
     * @return array[]
     */
    public static function tableSalesCustomerInfo(): array
    {
        return [
            ['val' => 'sno', 'typ' => 'i', 'def' => null, 'name' => '일련번호'],
            ['val' => 'targetSource', 'typ' => 's', 'def' => null, 'name' => '출처'],
            ['val' => 'level', 'typ' => 's', 'def' => null, 'name' => '고객 등급'],
            ['val' => 'customerType', 'typ' => 'i', 'def' => null, 'name' => '고객 구분'],
            ['val' => 'customerName', 'typ' => 's', 'def' => null, 'name' => '고객사명'],
            ['val' => 'industry', 'typ' => 's', 'def' => null, 'name' => '업종 구분'], //TODO : 변경
            ['val' => 'employeeCnt', 'typ' => 'i', 'def' => null, 'name' => '사원수'],
            ['val' => 'phone', 'typ' => 's', 'def' => null, 'name' => '대표번호'],
            ['val' => 'dept', 'typ' => 's', 'def' => null, 'name' => '부서'],
            ['val' => 'contactName', 'typ' => 's', 'def' => null, 'name' => '담당자'],
            ['val' => 'contactPhone', 'typ' => 's', 'def' => null, 'name' => '직통번호'],
            ['val' => 'contactEmail', 'typ' => 's', 'def' => null, 'name' => '이메일'],
            ['val' => 'contactDt', 'typ' => 's', 'def' => null, 'name' => '통화일자'],
            ['val' => 'memo', 'typ' => 's', 'def' => null, 'name' => '비고'],
            ['val' => 'buyMethod', 'typ' => 's', 'def' => null, 'name' => '구매방식'],
            ['val' => 'buyDiv', 'typ' => 's', 'def' => null, 'name' => '의류 구분'],
            ['val' => 'buyExt', 'typ' => 's', 'def' => null, 'name' => '구매 예정일'],
            ['val' => 'buyItem', 'typ' => 's', 'def' => null, 'name' => '구매 품목'],
            ['val' => 'buyCnt', 'typ' => 's', 'def' => null, 'name' => '구매 수량'],
            ['val' => 'regManagerSno', 'typ' => 'i', 'def' => null, 'name' => '등록자'],
            ['val' => 'lastManagerSno', 'typ' => 'i', 'def' => null, 'name' => '마지막수정자'],
            ['val' => 'regDt', 'typ' => 's', 'def' => null, 'name' => '등록일'],
            ['val' => 'modDt', 'typ' => 's', 'def' => null, 'name' => '수정일'],
            ['val' => 'busiCateSno', 'typ' => 'i', 'def' => null, 'name' => '업종 일련번호'],
            ['val' => 'salesManagerSno', 'typ' => 'i', 'def' => 0, 'name' => '영업담당자 일련번호'],
            ['val' => 'customerSno', 'typ' => 'i', 'def' => 0, 'name' => '고객사 일련번호'], //IMS 고객 번호
            ['val' => 'styleCode', 'typ' => 's', 'def' => null, 'name' => '고객사 이니셜'],
            ['val' => 'contactType', 'typ' => 's', 'def' => null, 'name' => '담당자 컨텍경로'],
            ['val' => 'bidDt', 'typ' => 's', 'def' => null, 'name' => '입찰 예정일'],
            ['val' => 'meetingDt', 'typ' => 's', 'def' => null, 'name' => '미팅 희망일'],
            ['val' => 'bidCntYear', 'typ' => 'i', 'def' => null, 'name' => '입찰 주기(년수)'],
            ['val' => 'lastBidYear', 'typ' => 'i', 'def' => null, 'name' => '마지막 입찰 년도'],
            ['val' => 'currContractCompany', 'typ' => 's', 'def' => null, 'name' => '현재 계약 업체'],
            ['val' => 'jsonExpectSales', 'typ' => 's', 'def' => null, 'name' => '추정 매출', 'json' => true],
            ['val' => 'totalExpectSales', 'typ' => 's', 'def' => null, 'name' => '추정 매출금액 합산'],
            ['val' => 'customerNeeds', 'typ' => 'i', 'def' => null, 'name' => '고객 니즈'],
            ['val' => 'mallInterest', 'typ' => 'i', 'def' => null, 'name' => '폐쇄몰 관심도'],
            ['val' => 'proposalType', 'typ' => 's', 'def' => null, 'name' => '제안서 타입'],
        ];
    }

    /**
     * 통화내역
     * @return array[]
     */
    public static function tableSalesCustomerContents(): array
    {
        return [
            ['val' => 'sno', 'typ' => 'i', 'def' => null, 'name' => '일련번호'],
            ['val' => 'salesSno', 'typ' => 'i', 'def' => null, 'name' => '영업고객번호'],
            ['val' => 'contentsType', 'typ' => 'i', 'def' => null, 'name' => '영업활동구분'],
            ['val' => 'contentsMinute', 'typ' => 'i', 'def' => null, 'name' => '업무 시간'],
            ['val' => 'afterCallReason', 'typ' => 'i', 'def' => null, 'name' => '후속 연락 사유'],
            ['val' => 'afterCallDt', 'typ' => 's', 'def' => null, 'name' => '후속 연락 일정'],
            ['val' => 'contents', 'typ' => 's', 'def' => null, 'name' => '통화내역'],
            ['val' => 'proposalType', 'typ' => 's', 'def' => null, 'name' => '제안서 타입'],
            ['val' => 'regManagerSno', 'typ' => 'i', 'def' => null, 'name' => '등록자'],
            ['val' => 'lastManagerSno', 'typ' => 'i', 'def' => null, 'name' => '마지막수정자'],
            ['val' => 'regDt', 'typ' => 's', 'def' => null, 'name' => '등록일'],
            ['val' => 'modDt', 'typ' => 's', 'def' => null, 'name' => '수정일'],
        ];
    }

    /**
     * 코멘트
     * @return array[]
     */
    public static function tableImsComment(): array
    {
        return [
            ['val' => 'sno', 'typ' => 'i', 'def' => null, 'name' => '일련번호'],
            ['val' => 'commentDiv', 'typ' => 's', 'def' => null, 'name' => '코멘트타입'],
            ['val' => 'customerSno', 'typ' => 'i', 'def' => null, 'name' => '프로젝트번호'],
            ['val' => 'projectSno', 'typ' => 'i', 'def' => null, 'name' => '프로젝트번호'],
            ['val' => 'styleSno', 'typ' => 'i', 'def' => null, 'name' => '스타일번호'],
            ['val' => 'eachSno', 'typ' => 'i', 'def' => null, 'name' => '개별번호'],
            ['val' => 'comment', 'typ' => 's', 'def' => null, 'name' => '코멘트'],
            ['val' => 'isShare', 'typ' => 's', 'def' => 'y', 'name' => '공유'],
            ['val' => 'regManagerSno', 'typ' => 'i', 'def' => null, 'name' => '등록자'],
            ['val' => 'regDt', 'typ' => 's', 'def' => null, 'name' => '등록일'],
            ['val' => 'modDt', 'typ' => 's', 'def' => null, 'name' => '수정일'],
        ];
    }


    /**
     * QB 원단관리
     * @return array
     */
    public static function tableImsFabric(): array
    {
        return [
            ['val' => 'sno', 'typ' => 'i', 'def' => null, 'name' => '번호'],
            ['val' => 'customerSno', 'typ' => 'i', 'def' => null, 'name' => '고객 번호'],
            ['val' => 'projectSno', 'typ' => 'i', 'def' => null, 'name' => '프로젝트 번호'],
            ['val' => 'styleSno', 'typ' => 'i', 'def' => null, 'name' => '프로젝트 번호'],
            ['val' => 'position', 'typ' => 's', 'def' => null, 'name' => '위치'],
            ['val' => 'attached', 'typ' => 's', 'def' => null, 'name' => '부착위치'],
            ['val' => 'fabricName', 'typ' => 's', 'def' => null, 'name' => '원단명', 'required' => true],
            ['val' => 'fabricMix', 'typ' => 's', 'def' => null, 'name' => '혼용률'],
            ['val' => 'color', 'typ' => 's', 'def' => null, 'name' => '컬러'],
            ['val' => 'spec', 'typ' => 's', 'def' => null, 'name' => '규격'],
            ['val' => 'meas', 'typ' => 's', 'def' => null, 'name' => '가요척'],

            ['val' => 'weight', 'typ' => 's', 'def' => null, 'name' => '중량'],
            ['val' => 'fabricWidth', 'typ' => 's', 'def' => null, 'name' => '원단폭'],
            ['val' => 'afterMake', 'typ' => 's', 'def' => null, 'name' => '후가공'],

            ['val' => 'unitPrice', 'typ' => 's', 'def' => null, 'name' => '단가'],
            ['val' => 'price', 'typ' => 's', 'def' => null, 'name' => '금액'],
            ['val' => 'makeNational', 'typ' => 's', 'def' => '', 'name' => '제조국'],

            ['val' => 'fabricConfirmInfo', 'typ' => 's', 'def' => null, 'name' => '퀄리티 확정정보'],
            ['val' => 'fabricMemo', 'typ' => 's', 'def' => null, 'name' => '퀄리티 메모'],
            ['val' => 'fabricStatus', 'typ' => 'i', 'def' => 0, 'name' => '원단확보상태'],

            ['val' => 'btConfirmInfo', 'typ' => 's', 'def' => null, 'name' => 'BT확정정보'],
            ['val' => 'btMemo', 'typ' => 's', 'def' => null, 'name' => 'BT 메모'],
            ['val' => 'btStatus', 'typ' => 'i', 'def' => 0, 'name' => 'BT상태'],

            ['val' => 'bulkConfirmInfo', 'typ' => 's', 'def' => null, 'name' => '벌크 확정정보'],
            ['val' => 'bulkMemo', 'typ' => 's', 'def' => null, 'name' => '벌크 메모'],
            ['val' => 'bulkStatus', 'typ' => 'i', 'def' => 0, 'name' => '벌크 상태'],

            //defrecated...
            ['val' => 'reqStatus', 'typ' => 'i', 'def' => 0, 'name' => '요청상태'],
            ['val' => 'reqManagerSno', 'typ' => 'i', 'def' => null, 'name' => '요청자'],
            ['val' => 'reqCount', 'typ' => 'i', 'def' => 0, 'name' => '요청횟수'], // ?
            ['val' => 'reqDt', 'typ' => 's', 'def' => null, 'name' => '요청일자'],
            ['val' => 'reqFactory', 'typ' => 'i', 'def' => null, 'name' => '요청 생산처'],
            ['val' => 'reqDeliveryInfo', 'typ' => 's', 'def' => null, 'name' => '이노버 발송 정보'],
            ['val' => 'resDeliveryInfo', 'typ' => 's', 'def' => null, 'name' => '생산처 발송 정보'],
            ['val' => 'reqMemo', 'typ' => 's', 'def' => null, 'name' => '요청메모'],
            ['val' => 'resMemo', 'typ' => 's', 'def' => null, 'name' => '생산처 메모'],
            ['val' => 'completeDeadLineDt', 'typ' => 's', 'def' => null, 'name' => '처리완료D/L'],
            ['val' => 'completeDt', 'typ' => 's', 'def' => null, 'name' => '처리완료일자'],

            ['val' => 'delFl', 'typ' => 's', 'def' => 'n', 'name' => '삭제 여부'],
            ['val' => 'regManagerSno', 'typ' => 'i', 'def' => null, 'name' => '최초등록자'],
            ['val' => 'lastManagerSno', 'typ' => 'i', 'def' => null, 'name' => '마지막수정자'],
            ['val' => 'regDt', 'typ' => 's', 'def' => null, 'name' => '등록시간'],
            ['val' => 'modDt', 'typ' => 's', 'def' => null, 'name' => '수정시간']
        ];
    }


    /**
     * QB요청 리스트
     * @return array[]
     */
    public static function tableImsFabricRequest()
    {
        return [
            ['val' => 'sno', 'typ' => 'i', 'name' => '번호', 'def' => null],
            ['val' => 'customerSno', 'typ' => 'i', 'name' => '고객 번호', 'def' => null],
            ['val' => 'projectSno', 'typ' => 'i', 'name' => '프로젝트 번호', 'def' => null],
            ['val' => 'styleSno', 'typ' => 'i', 'name' => '스타일 번호', 'def' => null],
            ['val' => 'fabricSno', 'typ' => 'i', 'name' => '원단 번호', 'def' => null],
            ['val' => 'reqType', 'typ' => 'i', 'name' => '요청 타입', 'def' => 0],
            ['val' => 'reqStatus', 'typ' => 'i', 'name' => '요청 상태', 'def' => 0],
            ['val' => 'reqCount', 'typ' => 'i', 'name' => '요청 횟수', 'def' => null],
            ['val' => 'reqFactory', 'typ' => 'i', 'name' => '요청 생산처', 'def' => null],
            ['val' => 'reqDeliveryInfo', 'typ' => 's', 'name' => '발송 정보', 'def' => null],
            ['val' => 'resDeliveryInfo', 'typ' => 's', 'name' => '생산처 발송 정보', 'def' => null],
            ['val' => 'resMemo', 'typ' => 's', 'name' => '생산처 메모', 'def' => null],
            ['val' => 'confirmInfo', 'typ' => 's', 'name' => '확정 정보(퀄, B, BULK)', 'def' => null],
            ['val' => 'rejectMemo', 'typ' => 's', 'name' => '반려정보', 'def' => null],
            ['val' => 'completeDeadLineDt', 'typ' => 's', 'name' => '완료 예정일', 'def' => null],
            ['val' => 'completeDt', 'typ' => 's', 'name' => '완료일', 'def' => null],
            ['val' => 'fabricReqFile', 'typ' => 's', 'name' => '의뢰서', 'def' => null],
            ['val' => 'reqManagerSno', 'typ' => 'i', 'name' => '요청자', 'def' => null],
            ['val' => 'regDt', 'typ' => 's', 'name' => '등록 시간', 'def' => null],
            ['val' => 'modDt', 'typ' => 's', 'name' => '수정 시간', 'def' => null]
        ];
    }

    /**
     * Defrecated ( 기존 QB 요청 HISTORY )
     * @return array[]
     */
    public static function tableImsFabricReqHistory(): array
    {
        return [
            ['val' => 'sno', 'typ' => 'i', 'def' => null, 'name' => '번호'],
            ['val' => 'customerSno', 'typ' => 'i', 'def' => null, 'name' => '고객 번호'],
            ['val' => 'projectSno', 'typ' => 'i', 'def' => null, 'name' => '프로젝트 번호'],
            ['val' => 'styleSno', 'typ' => 'i', 'def' => null, 'name' => '프로젝트 번호'],
            ['val' => 'fabricSno', 'typ' => 'i', 'def' => null, 'name' => '원단 번호'],
            ['val' => 'reqStatus', 'typ' => 'i', 'def' => 0, 'name' => '요청상태'],
            ['val' => 'reqManagerSno', 'typ' => 'i', 'def' => null, 'name' => '요청자'],
            ['val' => 'reqCount', 'typ' => 'i', 'def' => 0, 'name' => '요청횟수'],
            ['val' => 'reqDt', 'typ' => 's', 'def' => null, 'name' => '요청일자'],
            ['val' => 'reqFactory', 'typ' => 'i', 'def' => null, 'name' => '요청 생산처'],
            ['val' => 'reqDeliveryInfo', 'typ' => 's', 'def' => null, 'name' => '이노버 발송 정보'],
            ['val' => 'resDeliveryInfo', 'typ' => 's', 'def' => null, 'name' => '생산처 발송 정보'],
            ['val' => 'reqMemo', 'typ' => 's', 'def' => null, 'name' => '요청메모'],
            ['val' => 'resMemo', 'typ' => 's', 'def' => null, 'name' => '생산처 메모'],
            ['val' => 'completeDeadLineDt', 'typ' => 's', 'def' => null, 'name' => '처리완료D/L'],
            ['val' => 'completeDt', 'typ' => 's', 'def' => null, 'name' => '처리완료일자'],
            ['val' => 'regDt', 'typ' => 's', 'def' => null, 'name' => '등록시간'],
            ['val' => 'modDt', 'typ' => 's', 'def' => null, 'name' => '수정시간']
        ];
    }

    /**
     * 생산가견적 정보
     * @return array
     */
    public static function tableImsEstimate(): array
    {
        return [
            ['val' => 'sno', 'typ' => 'i', 'def' => null, 'name' => '번호'],
            ['val' => 'customerSno', 'typ' => 'i', 'def' => null, 'name' => '고객 번호'],
            ['val' => 'projectSno', 'typ' => 'i', 'def' => null, 'name' => '프로젝트 번호'],
            ['val' => 'styleSno', 'typ' => 'i', 'def' => null, 'name' => '스타일 번호'],
            ['val' => 'estimateType', 'typ' => 's', 'def' => 'estimate', 'name' => '견적타입'],
            ['val' => 'contents', 'typ' => 's', 'def' => null, 'name' => '견적 내용', 'json' => true],
            ['val' => 'reqStatus', 'typ' => 'i', 'def' => 0, 'name' => '요청상태'],
            ['val' => 'reqManagerSno', 'typ' => 'i', 'def' => null, 'name' => '요청자'],
            ['val' => 'reqCount', 'typ' => 'i', 'def' => 0, 'name' => '요청횟수'],
            ['val' => 'reqDt', 'typ' => 's', 'def' => null, 'name' => '요청일자'],
            ['val' => 'reqFactory', 'typ' => 'i', 'def' => null, 'name' => '의뢰처', 'required' => true],
            ['val' => 'reqFiles', 'typ' => 's', 'def' => null, 'name' => '요청 참고 파일', 'json' => true],
            ['val' => 'reqMemo', 'typ' => 's', 'def' => null, 'name' => '요청내용', 'strip' => true],
            ['val' => 'reqMemo1', 'typ' => 's', 'def' => null, 'name' => '원단설명', 'strip' => true],
            ['val' => 'reqMemo2', 'typ' => 's', 'def' => null, 'name' => '메인원단 생지여부', 'strip' => true],
            ['val' => 'reqMemo3', 'typ' => 's', 'def' => null, 'name' => '기능(단가변동)', 'strip' => true],
            ['val' => 'resMemo', 'typ' => 's', 'def' => null, 'name' => '생산처 메모', 'strip' => true],
            ['val' => 'estimateCount', 'typ' => 'i', 'def' => 0, 'name' => '견적수량', 'required' => true],
            ['val' => 'estimateCost', 'typ' => 'i', 'def' => 0, 'name' => '견적금액'],
            ['val' => 'completeDeadLineDt', 'typ' => 's', 'def' => null, 'name' => '처리완료D/L', 'required' => true],
            ['val' => 'completeDt', 'typ' => 's', 'def' => null, 'name' => '처리완료일자'],
            ['val' => 'regManagerSno', 'typ' => 'i', 'def' => null, 'name' => '최초등록자'],
            ['val' => 'lastManagerSno', 'typ' => 'i', 'def' => null, 'name' => '마지막수정자'],
            ['val' => 'regDt', 'typ' => 's', 'def' => null, 'name' => '등록시간'],
            ['val' => 'modDt', 'typ' => 's', 'def' => null, 'name' => '수정시간']
        ];
    }

    public static function tableImsProductCostDetail(): array
    {
        return [
            ['val' => 'sno', 'typ' => 'i', 'def' => null, 'name' => '번호'],
            ['val' => 'styleSno', 'typ' => 'i', 'def' => null, 'name' => '스타일 번호'],
            ['val' => 'contents', 'typ' => 's', 'def' => null, 'name' => '견적 내용', 'json' => true],
            ['val' => 'estimateSno', 'typ' => 'i', 'def' => 0, 'name' => '생산가 확정 번호'], //0일수 있다 -> 일반등록건 

            ['val' => 'estimateCount', 'typ' => 'i', 'def' => 0, 'name' => '견적수량'],
            ['val' => 'estimateCost', 'typ' => 'i', 'def' => 0, 'name' => '견적금액'],

            ['val' => 'mainFabric', 'typ' => 's', 'def' => null, 'name' => '메인원단'],
            ['val' => 'mainFabricNational', 'typ' => 's', 'def' => null, 'name' => '메인원단제조국'],

            ['val' => 'regDt', 'typ' => 's', 'def' => null, 'name' => '등록시간'],
            ['val' => 'modDt', 'typ' => 's', 'def' => null, 'name' => '수정시간']
        ];
    } //이력이 없고 ERP에서 등록한 건.

    /**
     * 신 생산관리
     * @return array
     */
    public static function tableImsProduction(): array
    {
        return [
            ['val' => 'sno', 'typ' => 'i', 'def' => null, 'name' => '번호'],
            ['val' => 'customerSno', 'typ' => 'i', 'def' => null, 'name' => '고객 번호'],
            ['val' => 'projectSno', 'typ' => 'i', 'def' => null, 'name' => '프로젝트 번호'],
            ['val' => 'styleSno', 'typ' => 'i', 'def' => null, 'name' => '스타일 번호'],
            ['val' => 'produceCompanySno', 'typ' => 'i', 'def' => null, 'name' => '생산처', 'required' => true],
            ['val' => 'produceStatus', 'typ' => 'i', 'def' => 0, 'name' => '생산단계'],
            ['val' => 'confirmMemo', 'typ' => 's', 'def' => null, 'name' => '확정메모'],
            ['val' => 'planPayDiv', 'typ' => 's', 'def' => null, 'name' => '항공비용부담'],
            ['val' => 'planPayMemo', 'typ' => 's', 'def' => null, 'name' => '항공비용메모', 'strip' => true],
            ['val' => 'privateMallDeliveryDt', 'typ' => 's', 'def' => null, 'name' => '폐쇄몰출고가능일자'],
            ['val' => 'sizeOptionQty', 'typ' => 's', 'def' => null, 'name' => '사이즈옵션수량(아소트)', 'json' => true],
            ['val' => 'totalQty', 'typ' => 'i', 'def' => 0, 'name' => '수량', 'required' => true],
            ['val' => 'msDeliveryDt', 'typ' => 's', 'def' => null, 'name' => '이노버 희망 납기일', 'required' => true],
            ['val' => 'firstData', 'typ' => 's', 'def' => null, 'name' => '생산 최초데이터', 'json' => true],
            ['val' => 'globalDeliveryDiv', 'typ' => 's', 'def' => 'n', 'name' => '운송(구해외배송방법)'],

            ['val' => 'assortConfirm', 'typ' => 's', 'def' => 'n', 'name' => '아소트 컨펌'],
            ['val' => 'workConfirm', 'typ' => 's', 'def' => 'n', 'name' => '작지 컨펌'],
            ['val' => 'label', 'typ' => 's', 'def' => null, 'name' => '라벨'],

            ['val' => 'scheduleCheck', 'typ' => 's', 'def' => 'n', 'name' => '일정체크'],
            ['val' => 'scheduleCheckDt', 'typ' => 's', 'def' => null, 'name' => '최종체크'],

            ['val' => 'regManagerSno', 'typ' => 'i', 'def' => null, 'name' => '등록자'],
            ['val' => 'lastManagerSno', 'typ' => 'i', 'def' => null, 'name' => '마지막수정자'],
            ['val' => 'regDt', 'typ' => 's', 'def' => null, 'name' => '의뢰일'],
            ['val' => 'modDt', 'typ' => 's', 'def' => null, 'name' => '수정일'],

            /*세탁*/
            ['val' => 'washExpectedDt', 'typ' => 's', 'def' => null, 'name' => '세탁(예정)'],
            ['val' => 'washCompleteDt', 'typ' => 's', 'def' => null, 'name' => '세탁(확정)'],
            ['val' => 'washMemo', 'typ' => 's', 'def' => null, 'name' => '세탁(메모)'],
            ['val' => 'washConfirm', 'typ' => 's', 'def' => 'n', 'name' => '세탁(승인)'],
            /*원부자재확정*/
            ['val' => 'fabricConfirmExpectedDt', 'typ' => 's', 'def' => null, 'name' => '원부자재확정(예정)'],
            ['val' => 'fabricConfirmCompleteDt', 'typ' => 's', 'def' => null, 'name' => '원부자재확정(확정)'],
            ['val' => 'fabricConfirmMemo', 'typ' => 's', 'def' => null, 'name' => '원부자재확정(메모)'],
            ['val' => 'fabricConfirmConfirm', 'typ' => 's', 'def' => 'n', 'name' => '원부자재확정(승인)'],
            /*재단*/
            ['val' => 'cuttingExpectedDt', 'typ' => 's', 'def' => null, 'name' => '재단(예정)'],
            ['val' => 'cuttingCompleteDt', 'typ' => 's', 'def' => null, 'name' => '재단(확정)'],
            ['val' => 'cuttingMemo', 'typ' => 's', 'def' => null, 'name' => '재단(메모)'],
            ['val' => 'cuttingConfirm', 'typ' => 's', 'def' => 'n', 'name' => '재단(승인)'],
            /*봉제*/
            ['val' => 'sewExpectedDt', 'typ' => 's', 'def' => null, 'name' => '봉제(예정)'],
            ['val' => 'sewCompleteDt', 'typ' => 's', 'def' => null, 'name' => '봉제(확정)'],
            ['val' => 'sewMemo', 'typ' => 's', 'def' => null, 'name' => '봉제(메모)'],
            ['val' => 'sewConfirm', 'typ' => 's', 'def' => 'n', 'name' => '봉제(승인)'],
            /*원부자재선적*/
            ['val' => 'fabricShipExpectedDt', 'typ' => 's', 'def' => null, 'name' => '원부자재 선적(예정)'],
            ['val' => 'fabricShipCompleteDt', 'typ' => 's', 'def' => null, 'name' => '원부자재 선적(확정)'],
            ['val' => 'fabricShipMemo', 'typ' => 's', 'def' => null, 'name' => '원부자재 선적(메모)'],
            ['val' => 'fabricShipConfirm', 'typ' => 's', 'def' => 'n', 'name' => '원부자재 선적(승인)'],
            /*QC*/
            ['val' => 'qcExpectedDt', 'typ' => 's', 'def' => null, 'name' => 'QC(예정)'],
            ['val' => 'qcCompleteDt', 'typ' => 's', 'def' => null, 'name' => 'QC(확정)'],
            ['val' => 'qcMemo', 'typ' => 's', 'def' => null, 'name' => 'QC(메모)'],
            ['val' => 'qcConfirm', 'typ' => 's', 'def' => 'n', 'name' => 'QC(승인)'],
            /*인라인*/
            ['val' => 'inlineExpectedDt', 'typ' => 's', 'def' => null, 'name' => '인라인(예정)'],
            ['val' => 'inlineCompleteDt', 'typ' => 's', 'def' => null, 'name' => '인라인(확정)'],
            ['val' => 'inlineMemo', 'typ' => 's', 'def' => null, 'name' => '인라인(메모)'],
            ['val' => 'inlineConfirm', 'typ' => 's', 'def' => 'n', 'name' => '인라인(승인)'],
            /*선적*/
            ['val' => 'shipExpectedDt', 'typ' => 's', 'def' => null, 'name' => '선적(예정)'],
            ['val' => 'shipCompleteDt', 'typ' => 's', 'def' => null, 'name' => '선적(확정)'],
            ['val' => 'shipMemo', 'typ' => 's', 'def' => null, 'name' => '선적(메모)'],
            ['val' => 'shipConfirm', 'typ' => 's', 'def' => 'n', 'name' => '선적(승인)'],
            /*도착*/
            ['val' => 'arrivalExpectedDt', 'typ' => 's', 'def' => null, 'name' => '도착(예정)'],
            ['val' => 'arrivalCompleteDt', 'typ' => 's', 'def' => null, 'name' => '도착(확정)'],
            ['val' => 'arrivalMemo', 'typ' => 's', 'def' => null, 'name' => '도착(메모)'],
            ['val' => 'arrivalConfirm', 'typ' => 's', 'def' => 'n', 'name' => '도착(승인)'],
            /*검수*/
            ['val' => 'checkExpectedDt', 'typ' => 's', 'def' => null, 'name' => '검수(예정)'],
            ['val' => 'checkCompleteDt', 'typ' => 's', 'def' => null, 'name' => '검수(확정)'],
            ['val' => 'checkMemo', 'typ' => 's', 'def' => null, 'name' => '검수(메모)'],
            ['val' => 'checkConfirm', 'typ' => 's', 'def' => 'n', 'name' => '검수(승인)'],
            /*납기*/
            ['val' => 'deliveryExpectedDt', 'typ' => 's', 'def' => null, 'name' => '납기(예정)'],
            ['val' => 'deliveryCompleteDt', 'typ' => 's', 'def' => null, 'name' => '납기(확정)'],
            ['val' => 'deliveryMemo', 'typ' => 's', 'def' => null, 'name' => '납기(메모)'],
            ['val' => 'deliveryConfirm', 'typ' => 's', 'def' => 'n', 'name' => '납기(승인)'],

            //기능 추가로 만들어진 필드
            ['val' => 'washMemo2', 'typ' => 's', 'name' => '세탁 메모2'],
            ['val' => 'washComment', 'typ' => 's', 'name' => '세탁 코멘트'],
            ['val' => 'fabricConfirmMemo2', 'typ' => 's', 'name' => '원부자재 확정 메모2'],
            ['val' => 'fabricConfirmComment', 'typ' => 's', 'name' => '원부자재 확정 코멘트'],
            ['val' => 'fabricShipMemo2', 'typ' => 's', 'name' => '원부자재 선적 메모2'],
            ['val' => 'fabricShipComment', 'typ' => 's', 'name' => '원부자재 선적 코멘트'],
            ['val' => 'qcMemo2', 'typ' => 's', 'name' => 'QC 메모2'],
            ['val' => 'qcComment', 'typ' => 's', 'name' => 'QC 코멘트'],
            ['val' => 'cuttingMemo2', 'typ' => 's', 'name' => '봉제 메모2'],
            ['val' => 'cuttingComment', 'typ' => 's', 'name' => '봉제 코멘트'],
            ['val' => 'sewMemo2', 'typ' => 's', 'name' => '봉제 메모2'],
            ['val' => 'sewComment', 'typ' => 's', 'name' => '봉제 코멘트'],
            ['val' => 'inlineMemo2', 'typ' => 's', 'name' => '인라인 메모2'],
            ['val' => 'inlineComment', 'typ' => 's', 'name' => '인라인 코멘트'],
            ['val' => 'shipMemo2', 'typ' => 's', 'name' => '선적 메모2'],
            ['val' => 'shipComment', 'typ' => 's', 'name' => '선적 코멘트'],
            ['val' => 'arrivalMemo2', 'typ' => 's', 'name' => '도착 메모2'],
            ['val' => 'arrivalComment', 'typ' => 's', 'name' => '도착 코멘트'],
            ['val' => 'checkMemo2', 'typ' => 's', 'name' => '검수 메모2'],
            ['val' => 'checkComment', 'typ' => 's', 'name' => '검수 코멘트'],
            ['val' => 'deliveryMemo2', 'typ' => 's', 'name' => '납기완료 메모2'],
            ['val' => 'deliveryComment', 'typ' => 's', 'name' => '납기완료 코멘트'],

            ['val' => 'sewPeriod', 'typ' => 'i', 'name' => '봉제 기간'],
            ['val' => 'deliveryPlace', 'typ' => 's', 'name' => '입고지']
        ];
    }

    /**
     * (신규) 미팅준비
     * @return array[]
     */
    public static function tableImsNewMeeting(): array
    {
        return [
            ['val' => 'sno', 'typ' => 'i', 'def' => null, 'name' => '번호'],
            ['val' => 'customerSno', 'typ' => 'i', 'def' => null, 'name' => '고객 번호', 'required' => true],
            ['val' => 'meetingStatus', 'typ' => 'i', 'def' => 0, 'name' => '미팅상태'],
            ['val' => 'expectedDt', 'typ' => 's', 'def' => null, 'name' => '희망납기'],
            ['val' => 'readyItem', 'typ' => 's', 'def' => null, 'name' => '제안방향', 'strip' => true],
            ['val' => 'readyDeadLineDt', 'typ' => 's', 'def' => null, 'name' => '준비마감일'],
            ['val' => 'meetingDt', 'typ' => 's', 'def' => null, 'name' => '미팅일자'],
            ['val' => 'meetingTime', 'typ' => 's', 'def' => null, 'name' => '미팅시간'],
            ['val' => 'location', 'typ' => 's', 'def' => null, 'name' => '장소'],
            ['val' => 'attend', 'typ' => 's', 'def' => null, 'name' => '참석자'],
            ['val' => 'purpose', 'typ' => 's', 'def' => null, 'name' => '목적', 'required' => true],
            ['val' => 'meetingFiles', 'typ' => 's', 'def' => null, 'name' => '미팅준비자료'],
            ['val' => 'checkList', 'typ' => 's', 'def' => null, 'name' => '미팅 체크리스트(JSON)', 'json' => true],
            ['val' => 'style', 'typ' => 's', 'def' => null, 'name' => '스타일(JSON)', 'json' => true],
            ['val' => 'meetingContents', 'typ' => 's', 'def' => null, 'name' => '미팅내용', 'strip' => true],
            ['val' => 'regManagerSno', 'typ' => 'i', 'def' => null, 'name' => '최초등록자'],
            ['val' => 'lastManagerSno', 'typ' => 'i', 'def' => null, 'name' => '마지막수정자'],
            ['val' => 'regDt', 'typ' => 's', 'def' => null, 'name' => '등록시간'],
            ['val' => 'modDt', 'typ' => 's', 'def' => null, 'name' => '수정시간']
        ];
    }

    /**
     * 생산관리 개별 코멘트
     * @return array[]
     */
    public static function tableImsProductionComment(): array
    {
        return [
            ['val' => 'sno', 'typ' => 'i', 'def' => null, 'name' => '번호'],
            ['val' => 'productionSno', 'typ' => 'i', 'def' => null, 'name' => '생산 번호'],
            ['val' => 'commentType', 'typ' => 's', 'def' => null, 'name' => '구분', 'required' => true],
            ['val' => 'comment', 'typ' => 's', 'def' => null, 'name' => '코멘트', 'required' => true],
            ['val' => 'regManagerSno', 'typ' => 'i', 'def' => null, 'name' => '최초등록자'],
            ['val' => 'regDt', 'typ' => 's', 'def' => null, 'name' => '등록시간'],
            ['val' => 'modDt', 'typ' => 's', 'def' => null, 'name' => '수정시간']
        ];
    }


    /**
     * TO-DO LIST
     * 내 요청.
     * (enumType / TO-DO TYPE  참고)
     * @return array[]
     */
    public static function tableImsTodoRequest(): array
    {
        return [
            ['val' => 'sno', 'typ' => 'i', 'def' => null, 'name' => '번호'],
            ['val' => 'todoType', 'typ' => 's', 'def' => null, 'name' => '구분'],
            ['val' => 'hopeDt', 'typ' => 's', 'def' => null, 'name' => '완료 희망일'],
            ['val' => 'subject', 'typ' => 's', 'def' => null, 'name' => '제목', 'required' => true],
            ['val' => 'contents', 'typ' => 's', 'def' => null, 'name' => '요청 내용', 'required' => true, 'strip' => true],
            ['val' => 'customerSno', 'typ' => 'i', 'def' => null, 'name' => '고객사'],
            ['val' => 'projectSno', 'typ' => 'i', 'def' => null, 'name' => '프로젝트'],
            ['val' => 'styleSno', 'typ' => 'i', 'def' => null, 'name' => '스타일'],
            ['val' => 'eachSno', 'typ' => 'i', 'def' => null, 'name' => '기타번호'],
            ['val' => 'eachDiv', 'typ' => 's', 'def' => null, 'name' => '기타구분'],
            ['val' => 'todoFile1', 'typ' => 's', 'def' => null, 'name' => '첨부파일', 'json' => true],
            ['val' => 'regManagerSno', 'typ' => 'i', 'def' => null, 'name' => '요청자'],

            ['val' => 'approvalType', 'typ' => 's', 'def' => null, 'name' => '승인타입'],
            ['val' => 'approvalStatus', 'typ' => 's', 'def' => 'ready', 'name' => '승인상태'], //ready 기안, proc 진행, reject 반려, accept 결재완료
            ['val' => 'appManagers', 'typ' => 's', 'def' => null, 'name' => '결재정보', 'json' => true],
            ['val' => 'refManagers', 'typ' => 's', 'def' => null, 'name' => '참조', 'json' => true], //임시저장용, 기안이 등록되었을 때 테이블 등록
            ['val' => 'emergency', 'typ' => 's', 'def' => 'n', 'name' => '긴급'],

            ['val' => 'delFl', 'typ' => 's', 'def' => 'n', 'name' => '삭제여부'],
            ['val' => 'regDt', 'typ' => 's', 'def' => null, 'name' => '등록일'],
            ['val' => 'modDt', 'typ' => 's', 'def' => null, 'name' => '수정일'],
        ];
    }

    /**
     * TO-DO List
     * 받은 요청
     * (enumType / TO-DO TYPE  참고)
     * @return array
     */
    public static function tableImsTodoResponse(): array
    {
        return [
            ['val' => 'sno', 'typ' => 'i', 'def' => null, 'name' => '번호'],
            ['val' => 'reqSno', 'typ' => 'i', 'def' => null, 'name' => '연결번호'],
            ['val' => 'managerSno', 'typ' => 'i', 'def' => null, 'name' => '대상자'],
            ['val' => 'targetType', 'typ' => 's', 'def' => 'target', 'name' => '대상타입'], // target, ref (참조)
            ['val' => 'status', 'typ' => 's', 'def' => 'ready', 'name' => '상태'], //approval ?
            ['val' => 'reqRead', 'typ' => 's', 'def' => 'n', 'name' => '조회'],
            ['val' => 'expectedDt', 'typ' => 's', 'def' => null, 'name' => '완료 예정일'],
            ['val' => 'completeDt', 'typ' => 's', 'def' => null, 'name' => '완료일'],
            ['val' => 'completeManagerSno', 'typ' => 'i', 'def' => null, 'name' => '처리자'],
            ['val' => 'reason', 'typ' => 's', 'def' => null, 'name' => '전결사유'],
            ['val' => 'emergencyConfirmDt', 'typ' => 's', 'def' => null, 'name' => '긴급건확인체크'],
            ['val' => 'regDt', 'typ' => 's', 'def' => null, 'name' => '등록일'],
            ['val' => 'modDt', 'typ' => 's', 'def' => null, 'name' => '수정일'],
        ];
    }

    /**
     * TO DO List Comment
     * @return array[]
     */
    public static function tableImsTodoComment(): array
    {
        return [
            ['val' => 'sno', 'typ' => 'i', 'def' => null, 'name' => '번호'],
            ['val' => 'commentDiv', 'typ' => 's', 'def' => 'todo', 'name' => '코멘트타입'],
            ['val' => 'todoSno', 'typ' => 'i', 'def' => null, 'name' => 'TODO번호', 'required' => true], //
            ['val' => 'comment', 'typ' => 's', 'def' => null, 'name' => '코멘트', 'required' => true, 'strip' => true],
            ['val' => 'regManagerSno', 'typ' => 'i', 'def' => null, 'name' => '등록자'],
            ['val' => 'regDt', 'typ' => 's', 'def' => null, 'name' => '의뢰일'],
            ['val' => 'modDt', 'typ' => 's', 'def' => null, 'name' => '수정일'],
        ];
    }

    /**
     * 결재선 Default
     * @return array
     */
    public static function tableImsApprovalLine(): array
    {
        return [
            ['val' => 'sno', 'typ' => 'i', 'def' => null, 'name' => '번호'],
            ['val' => 'approvalType', 'typ' => 's', 'def' => null, 'name' => '결재타입'], //기획, 제안, ...
            ['val' => 'subject', 'typ' => 's', 'def' => null, 'name' => '결재선이름'],
            ['val' => 'appManagers', 'typ' => 's', 'def' => null, 'name' => '결재정보', 'json' => true],
            ['val' => 'refManagers', 'typ' => 's', 'def' => null, 'name' => '참조', 'json' => true], //임시저장용, 기안이 등록되었을 때 테이블 등록
            ['val' => 'regManagerSno', 'typ' => 'i', 'def' => 0, 'name' => '등록자'], //0일경우 공통 (공통은 내가 수기 관리)
            ['val' => 'regDt', 'typ' => 's', 'def' => null, 'name' => '의뢰일'],
            ['val' => 'modDt', 'typ' => 's', 'def' => null, 'name' => '수정일'],
        ];
    }


    public static function tableImsSchedule(): array
    {
        return [
            ['val' => 'sno', 'typ' => 'i', 'def' => null, 'name' => '번호'],
            ['val' => 'approvalType', 'typ' => 's', 'def' => null, 'name' => '결재타입'], //기획, 제안, ...
            ['val' => 'subject', 'typ' => 's', 'def' => null, 'name' => '결재선이름'],
            ['val' => 'appManagers', 'typ' => 's', 'def' => null, 'name' => '결재정보', 'json' => true],
            ['val' => 'refManagers', 'typ' => 's', 'def' => null, 'name' => '참조', 'json' => true], //임시저장용, 기안이 등록되었을 때 테이블 등록
            ['val' => 'regManagerSno', 'typ' => 'i', 'def' => 0, 'name' => '등록자'], //0일경우 공통 (공통은 내가 수기 관리)
            ['val' => 'regDt', 'typ' => 's', 'def' => null, 'name' => '의뢰일'],
            ['val' => 'modDt', 'typ' => 's', 'def' => null, 'name' => '수정일'],
        ];
    }


    /**
     * 스케쥴 별 설정
     * @return array[]
     */
    public static function tableImsScheduleConfig(): array
    {
        return [
            ['val' => 'sno', 'typ' => 'i', 'def' => null, 'name' => '번호'],
            ['val' => 'scheduleType', 'typ' => 's', 'def' => null, 'name' => '스케쥴타입'],
            ['val' => 'relationSchedule', 'typ' => 's', 'def' => null, 'name' => '연관 스케쥴타입'],
            ['val' => 'remain240', 'typ' => 'i', 'def' => 0, 'name' => '8개월전'],
            ['val' => 'remain210', 'typ' => 'i', 'def' => 0, 'name' => '7개월전'],
            ['val' => 'remain180', 'typ' => 'i', 'def' => 0, 'name' => '6개월전'],
            ['val' => 'remain150', 'typ' => 'i', 'def' => 0, 'name' => '5개월전'],
            ['val' => 'regDt', 'typ' => 's', 'def' => null, 'name' => '등록일'],
            ['val' => 'modDt', 'typ' => 's', 'def' => null, 'name' => '수정일'],
        ];
    }


    /**
     * 고객 이슈 (=고객 코멘트)
     * @return array[]
     */
    public static function tableImsCustomerIssue(): array
    {
        return [
            ['val' => 'sno', 'typ' => 'i', 'def' => null, 'name' => '일련번호'],
            ['val' => 'issueType', 'typ' => 's', 'def' => null, 'name' => '이슈타입', 'required' => true],   // 'req'=요청 , 'accident'=사고  'delivery' => '납품', 'meeting' => '협상/미팅'
            ['val' => 'inboundType', 'typ' => 's', 'def' => null, 'name' => '접수타입'], // 'mail', 'phone', 'meeting' (메일, 전화, 미팅)
            ['val' => 'customerSno', 'typ' => 'i', 'def' => null, 'name' => '고객번호', 'required' => true],
            ['val' => 'projectSno', 'typ' => 'i', 'def' => null, 'name' => '프로젝트번호'],
            ['val' => 'subject', 'typ' => 's', 'def' => null, 'name' => '제목', 'required' => true],
            ['val' => 'contents', 'typ' => 's', 'def' => null, 'name' => '내용', 'strip' => true, 'required' => true],
            ['val' => 'fileData', 'typ' => 's', 'def' => null, 'name' => '첨부파일', 'isFile' => true, 'json' => true],
            ['val' => 'isShare', 'typ' => 's', 'def' => 'n', 'name' => '공유'],
            ['val' => 'regManagerSno', 'typ' => 'i', 'def' => null, 'name' => '등록자'],
            ['val' => 'regDt', 'typ' => 's', 'def' => null, 'name' => '등록일'],
            ['val' => 'modDt', 'typ' => 's', 'def' => null, 'name' => '수정일'],
        ];
    }

    /**
     * 고객 견적서
     * @return array[]
     */
    public static function tableImsCustomerEstimate(): array
    {
        return [
            ['val' => 'sno', 'typ' => 'i', 'def' => null, 'name' => '일련번호'],
            ['val' => 'customerSno', 'typ' => 'i', 'def' => null, 'name' => '고객번호', 'required' => true],
            ['val' => 'projectSno', 'typ' => 'i', 'def' => null, 'name' => '프로젝트번호'],
            ['val' => 'estimateType', 'typ' => 's', 'def' => 'estimate', 'name' => '견적타입', 'required' => true],   // 'estimate' = 가견적 , 'confirm' = 확정견적
            ['val' => 'subject', 'typ' => 's', 'def' => null, 'name' => '발송제목', 'required' => true],
            ['val' => 'receiverInfo', 'typ' => 's', 'def' => null, 'name' => '수신자 정보', 'json' => true, 'required' => true],
            ['val' => 'estimateMemo', 'typ' => 's', 'def' => null, 'name' => '비고(고객용)'],
            ['val' => 'innoverMemo', 'typ' => 's', 'def' => null, 'name' => '비고(내부)'],
            ['val' => 'contents', 'typ' => 's', 'def' => null, 'name' => '내용', 'json' => true, 'required' => true], //견적내용
            ['val' => 'supply', 'typ' => 'i', 'def' => null, 'name' => '공급가액'],
            ['val' => 'tax', 'typ' => 'i', 'def' => null, 'name' => '부가세'],
            ['val' => 'regManagerSno', 'typ' => 'i', 'def' => null, 'name' => '등록자'], //=발송자
            ['val' => 'estimateManagerSno', 'typ' => 'i', 'def' => null, 'name' => '견적담당자'],
            ['val' => 'expireDay', 'typ' => 'i', 'def' => 30, 'name' => '만료일'],
            ['val' => 'estimateDt', 'typ' => 's', 'def' => '', 'name' => '견적일자'],

            ['val' => 'approvalStatus', 'typ' => 's', 'def' => 'n', 'name' => '고객승인상태'],
            ['val' => 'approvalName', 'typ' => 's', 'def' => '', 'name' => '승인자명'],
            ['val' => 'approvalDt', 'typ' => 's', 'def' => '', 'name' => '승인일자'],

            ['val' => 'regDt', 'typ' => 's', 'def' => null, 'name' => '등록일'], //=발송일
            ['val' => 'modDt', 'typ' => 's', 'def' => null, 'name' => '수정일'],
        ];
    }

    /**
     * 전산작지
     * @return array
     */
    public static function tableImsEwork(): array
    {
        return [
            ['val' => 'sno', 'typ' => 'i', 'def' => null, 'name' => '일련번호'],
            ['val' => 'styleSno', 'typ' => 'i', 'def' => null, 'name' => '스타일번호'],
            ['val' => 'prdFabricInfo', 'typ' => 's', 'def' => null, 'name' => ' 사양서 원단 정보', 'strip' => true],

            ['val' => 'filePrd', 'typ' => 's', 'def' => null, 'name' => '사양서썸네일', 'json' => true],
            ['val' => 'fileMain', 'typ' => 's', 'def' => null, 'name' => '메인파일', 'json' => true],
            ['val' => 'fileBatek', 'typ' => 's', 'def' => null, 'name' => '바텍파일', 'json' => true],
            ['val' => 'fileAi', 'typ' => 's', 'def' => null, 'name' => 'AI파일', 'json' => true],
            ['val' => 'fileMarkAi', 'typ' => 's', 'def' => null, 'name' => 'AI파일', 'json' => true],
            ['val' => 'fileCareAi', 'typ' => 's', 'def' => null, 'name' => 'AI파일', 'json' => true],
            ['val' => 'fileMark1', 'typ' => 's', 'def' => null, 'name' => '마크파일1', 'json' => true],
            ['val' => 'fileMark2', 'typ' => 's', 'def' => null, 'name' => '마크파일2', 'json' => true],
            ['val' => 'fileMark3', 'typ' => 's', 'def' => null, 'name' => '마크파일3', 'json' => true],
            ['val' => 'fileMark4', 'typ' => 's', 'def' => null, 'name' => '마크파일4', 'json' => true],
            ['val' => 'fileMark5', 'typ' => 's', 'def' => null, 'name' => '마크파일5', 'json' => true],
            ['val' => 'fileMark6', 'typ' => 's', 'def' => null, 'name' => '마크파일6', 'json' => true],
            ['val' => 'fileMark7', 'typ' => 's', 'def' => null, 'name' => '마크파일7', 'json' => true],
            ['val' => 'fileMark8', 'typ' => 's', 'def' => null, 'name' => '마크파일8', 'json' => true],
            ['val' => 'fileMark9', 'typ' => 's', 'def' => null, 'name' => '마크파일9', 'json' => true],
            ['val' => 'fileMark10', 'typ' => 's', 'def' => null, 'name' => '마크파일10', 'json' => true],

            ['val' => 'fileMarkPosition1', 'typ' => 's', 'def' => null, 'name' => '마크위치1', 'json' => true],
            ['val' => 'fileMarkPosition2', 'typ' => 's', 'def' => null, 'name' => '마크위치2', 'json' => true],
            ['val' => 'fileMarkPosition3', 'typ' => 's', 'def' => null, 'name' => '마크위치3', 'json' => true],
            ['val' => 'fileMarkPosition4', 'typ' => 's', 'def' => null, 'name' => '마크위치4', 'json' => true],
            ['val' => 'fileMarkPosition5', 'typ' => 's', 'def' => null, 'name' => '마크위치5', 'json' => true],
            ['val' => 'fileMarkPosition6', 'typ' => 's', 'def' => null, 'name' => '마크위치6', 'json' => true],
            ['val' => 'fileMarkPosition7', 'typ' => 's', 'def' => null, 'name' => '마크위치7', 'json' => true],
            ['val' => 'fileMarkPosition8', 'typ' => 's', 'def' => null, 'name' => '마크위치8', 'json' => true],
            ['val' => 'fileMarkPosition9', 'typ' => 's', 'def' => null, 'name' => '마크위치9', 'json' => true],
            ['val' => 'fileMarkPosition10', 'typ' => 's', 'def' => null, 'name' => '마크위치10', 'json' => true],

            ['val' => 'filePosition', 'typ' => 's', 'def' => null, 'name' => '위치파일', 'json' => true],
            ['val' => 'fileCare', 'typ' => 's', 'def' => null, 'name' => '케어라벨', 'json' => true],
            ['val' => 'fileSpec', 'typ' => 's', 'def' => null, 'name' => '사이즈스펙파일', 'json' => true],
            ['val' => 'filePacking1', 'typ' => 's', 'def' => null, 'name' => '접는방법', 'json' => true],
            ['val' => 'filePacking2', 'typ' => 's', 'def' => null, 'name' => '포장방법', 'json' => true],
            ['val' => 'filePacking3', 'typ' => 's', 'def' => null, 'name' => '박스패킹', 'json' => true],

            ['val' => 'markInfo1', 'typ' => 's', 'def' => null, 'name' => '마크정보1', 'json' => true],
            ['val' => 'markInfo2', 'typ' => 's', 'def' => null, 'name' => '마크정보2', 'json' => true],
            ['val' => 'markInfo3', 'typ' => 's', 'def' => null, 'name' => '마크정보3', 'json' => true],
            ['val' => 'markInfo4', 'typ' => 's', 'def' => null, 'name' => '마크정보4', 'json' => true],
            ['val' => 'markInfo5', 'typ' => 's', 'def' => null, 'name' => '마크정보5', 'json' => true],
            ['val' => 'markInfo6', 'typ' => 's', 'def' => null, 'name' => '마크정보6', 'json' => true],
            ['val' => 'markInfo7', 'typ' => 's', 'def' => null, 'name' => '마크정보7', 'json' => true],
            ['val' => 'markInfo8', 'typ' => 's', 'def' => null, 'name' => '마크정보8', 'json' => true],
            ['val' => 'markInfo9', 'typ' => 's', 'def' => null, 'name' => '마크정보9', 'json' => true],
            ['val' => 'markInfo10', 'typ' => 's', 'def' => null, 'name' => '마크정보10', 'json' => true],

            ['val' => 'warnMain', 'typ' => 's', 'def' => null, 'name' => '메인 주의사항', 'strip' => true],
            ['val' => 'warnMaterial', 'typ' => 's', 'def' => null, 'name' => '원부자재유의사항', 'strip' => true],
            ['val' => 'warnBatek', 'typ' => 's', 'def' => null, 'name' => '바텍유의사항', 'strip' => true],
            ['val' => 'warnMark', 'typ' => 's', 'def' => null, 'name' => '마크유의사항', 'strip' => true],
            ['val' => 'warnPosition', 'typ' => 's', 'def' => null, 'name' => '위치유의사항', 'strip' => true],
            ['val' => 'warnSpec', 'typ' => 's', 'def' => null, 'name' => '스펙유의사항', 'strip' => true],
            ['val' => 'warnPacking', 'typ' => 's', 'def' => null, 'name' => '포장유의사항', 'strip' => true],

            ['val' => 'specData', 'typ' => 's', 'def' => null, 'name' => '사이즈스펙', 'json' => true],
            ['val' => 'beforeSpecData', 'typ' => 's', 'def' => null, 'name' => '사이즈스펙', 'json' => true],

            ['val' => 'useBatek', 'typ' => 's', 'def' => 'y', 'name' => '바텍 사용여부'],
            ['val' => 'useMark', 'typ' => 's', 'def' => 'y', 'name' => '마크 사용여부'],
            ['val' => 'usePacking', 'typ' => 's', 'def' => 'y', 'name' => '패킹 사용여부'],

            ['val' => 'mainApproval', 'typ' => 's', 'def' => 'n', 'name' => '작지 메인 결재 승인'],
            ['val' => 'markApproval', 'typ' => 's', 'def' => 'n', 'name' => '마크 결재 승인'],
            ['val' => 'careApproval', 'typ' => 's', 'def' => 'n', 'name' => '캐어라벨 결재 승인'],
            ['val' => 'specApproval', 'typ' => 's', 'def' => 'n', 'name' => '스펙 결재 승인'],
            ['val' => 'materialApproval', 'typ' => 's', 'def' => 'n', 'name' => '자재리스트 결재 승인'], //중요 필드 (결재 전/후를 구분한다)
            ['val' => 'packingApproval', 'typ' => 's', 'def' => 'n', 'name' => '포장 결재 승인'],
            ['val' => 'batekApproval', 'typ' => 's', 'def' => 'n', 'name' => '바텍 결재 승인'], //n대기. r요청. p승인. f반려. x.없음

            //생산 유의 사항
            ['val' => 'produceWarning', 'typ' => 's', 'name' => '생산시 유의 사항', 'json' => true,'scheme'=>ImsJsonSchema::EWORK_WARNING], //n대기. r요청. p승인. f반려. x.없음
            //리비전 (등록)
            ['val' => 'revision', 'typ' => 's', 'def' => null, 'name' => '작지 리비전', 'json' => true],

            ['val' => 'writeDt', 'typ' => 's', 'def' => null, 'name' => '작성일'],
            ['val' => 'requestDt', 'typ' => 's', 'def' => null, 'name' => '의뢰일'],

            ['val' => 'regDt', 'typ' => 's', 'def' => null, 'name' => '등록일'], //=발송일
            ['val' => 'modDt', 'typ' => 's', 'def' => null, 'name' => '수정일'],
        ];
    }


    /**
     * 고객 스타일
     * 구)마스터 스타일
     * @return array
     */
    public static function tableImsCustomerStyle(): array
    {
        return [
            ['val' => 'sno', 'typ' => 'i', 'def' => null, 'name' => '일련번호'],
            ['val' => 'customerSno', 'typ' => 'i', 'def' => null, 'name' => '고객사 번호'],
            ['val' => 'productName', 'typ' => 's', 'def' => null, 'name' => '제품명'],
            ['val' => 'prdSeason', 'typ' => 's', 'def' => null, 'name' => '시즌'],  //KEY
            ['val' => 'prdStyle', 'typ' => 's', 'def' => null, 'name' => '스타일'],  //KEY
            ['val' => 'addStyleCode', 'typ' => 's', 'def' => null, 'name' => '추가 스타일 코드'],  //KEY
            ['val' => 'latestSalePrice', 'typ' => 'i', 'def' => null, 'name' => '최종 판매가'],
            ['val' => 'latestPrdCost', 'typ' => 'i', 'def' => null, 'name' => '최종 생산가'],
            ['val' => 'materials', 'typ' => 's', 'def' => null, 'name' => '자재정보'], //확정된 fabric, subfabric만 뽑아서 (자재와 가격은 분리 ? )
            ['val' => 'searchKeyword', 'typ' => 's', 'def' => null, 'name' => '검색키워드'], //수배 완료된 관리원단 내용을 검색 키워드로 등록 (원부자재 검색시 함께 검색)
            ['val' => 'sizeList', 'typ' => 's', 'def' => null, 'name' => '사이즈리스트'],
            ['val' => 'sizeSpec', 'typ' => 's', 'def' => null, 'name' => '사이즈스펙'],
            ['val' => 'fileThumbnail', 'typ' => 's', 'def' => null, 'name' => '썸네일_기획'], //fileThumbnailReal or fileThumbnail (여기 변경되는 사진은 real에 반영)
            ['val' => 'fileInline', 'typ' => 's', 'def' => null, 'name' => '인라인이미지'],
            ['val' => 'designCopyright', 'typ' => 's', 'def' => null, 'name' => '디자인저작권'],
            ['val' => 'lastManagerSno', 'typ' => 'i', 'def' => null, 'name' => '마지막수정자'],
            ['val' => 'regDt', 'typ' => 's', 'def' => null, 'name' => '등록시간'],
            ['val' => 'modDt', 'typ' => 's', 'def' => null, 'name' => '수정시간'],
        ];
    }

    /**
     * 기준 스펙
     * @return array
     */
    public static function tableImsCalendar(): array
    {
        return [
            ['val' => 'sno', 'typ' => 'i', 'def' => null, 'name' => '일련번호'],
            ['val' => 'customerSno', 'typ' => 'i', 'def' => null, 'name' => '고객번호'],
            ['val' => 'projectSno', 'typ' => 'i', 'def' => null, 'name' => '프로젝트번호'],

            ['val' => 'title', 'typ' => 's', 'def' => null, 'name' => '일정제목', 'required' => true],
            ['val' => 'contents', 'typ' => 's', 'def' => null, 'name' => '상세내용'],
            ['val' => 'start', 'typ' => 's', 'def' => null, 'name' => '시작일자', 'required' => true],
            ['val' => 'end', 'typ' => 's', 'def' => null, 'name' => '종료일자'],
            ['val' => 'type', 'typ' => 'i', 'def' => null, 'name' => '타입'], //0, 1:중요 2:연차, 3:미팅, 4:기타

            ['val' => 'regManagerSno', 'typ' => 'i', 'def' => null, 'name' => '등록자'],
            ['val' => 'regDt', 'typ' => 's', 'def' => null, 'name' => '등록시간'],
            ['val' => 'modDt', 'typ' => 's', 'def' => null, 'name' => '수정시간'],
        ];
    }

    /**
     * IMS 카테고리 데이터
     * @return array[]
     */
    public static function tableImsCategory(): array
    {
        return [
            ['val' => 'sno', 'typ' => 'i', 'def' => null, 'name' => '번호'],
            ['val' => 'cateType', 'typ' => 's', 'def' => null, 'name' => '카테고리 타입'],
            ['val' => 'cateCd', 'typ' => 's', 'def' => null, 'name' => '분류1'],
            ['val' => 'cateName', 'typ' => 's', 'def' => null, 'name' => '분류2'],
            ['val' => 'regDt', 'typ' => 's', 'def' => null, 'name' => '등록일'],
            ['val' => 'modDt', 'typ' => 's', 'def' => null, 'name' => '수정일']
        ];
    }


    /**
     * 업데이트 이력
     * @return array[]
     */
    public static function tableImsEworkHistory(): array
    {
        return [
            ['val' => 'sno', 'typ' => 'i', 'def' => null, 'name' => '일련번호'],
            ['val' => 'styleSno', 'typ' => 'i', 'def' => null, 'name' => '스타일번호'],
            ['val' => 'updateType', 'typ' => 's', 'def' => null, 'name' => '구분'],
            ['val' => 'contents', 'typ' => 's', 'def' => null, 'name' => '업데이트 이전 내역'],
            ['val' => 'comment', 'typ' => 's', 'def' => null, 'name' => '업데이트 내역 코멘트'],
            ['val' => 'managerSno', 'typ' => 'i', 'def' => null, 'name' => '수정자'],
            ['val' => 'regDt', 'typ' => 's', 'def' => null],
            ['val' => 'modDt', 'typ' => 's', 'def' => null],
        ];
    }

    /**
     * 폐쇄몰 상품 링크
     * @return array[]
     */
    public static function tableGoodsOptionLink(): array
    {
        return [
            ['val' => 'sno', 'typ' => 'i', 'def' => null, 'name' => '번호'],
            ['val' => 'goodsNo', 'typ' => 'i', 'def' => null, 'name' => '폐쇄몰 상품 번호'],
            ['val' => 'optionSno', 'typ' => 'i', 'def' => null, 'name' => '폐쇄몰 옵션 번호'],
            ['val' => 'code', 'typ' => 's', 'def' => null, 'name' => '창고 옵션 코드'],
            ['val' => 'otherCnt', 'typ' => 'i', 'def' => null, 'name' => '다른 곳 맵핑 수량'],
            ['val' => 'sort', 'typ' => 'i', 'def' => 1, 'name' => '순서'],
            ['val' => 'regDt', 'typ' => 's', 'def' => null, 'name' => '등록시간'],
            ['val' => 'modDt', 'typ' => 's', 'def' => null, 'name' => '수정시간']
        ];
    }

    /**
     * 상품 옵션 추가 정보
     * @return array[]
     */
    public static function tableGoodsOptionExt(): array
    {
        return [
            ['val' => 'sno', 'typ' => 'i', 'def' => null, 'name' => '번호'],
            ['val' => 'goodsNo', 'typ' => 'i', 'def' => null, 'name' => '폐쇄몰 상품 번호' , 'required' => true],
            ['val' => 'optionSno', 'typ' => 'i', 'def' => null, 'name' => '폐쇄몰 옵션 번호' , 'required' => true],
            ['val' => 'reserveCnt', 'typ' => 'i', 'def' => 0, 'name' => '예약수량'],
            ['val' => 'realCnt', 'typ' => 'i', 'def' => 0, 'name' => '실제수량'],
            ['val' => 'realCntOfYear', 'typ' => 'i', 'def' => 0, 'name' => '생산 연도별 재고 수량'],
            ['val' => 'inCnt', 'typ' => 'i', 'def' => 0, 'name' => '입고수량'],
            ['val' => 'outCnt', 'typ' => 'i', 'def' => 0, 'name' => '출고수량'],
            ['val' => 'otherMappingCnt', 'typ' => 'i', 'def' => 0, 'name' => '다른상품 맵핑 수량'],
            ['val' => 'regDt', 'typ' => 's', 'def' => null, 'name' => '등록시간'],
            ['val' => 'modDt', 'typ' => 's', 'def' => null, 'name' => '수정시간']
        ];
    }

}
