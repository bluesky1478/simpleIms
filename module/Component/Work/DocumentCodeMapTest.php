<?php
namespace Component\Work;

/**
 * Sitelab Code Key List
 * Class SlCode
 * @package SlComponent\Util
 */
class DocumentCodeMapTest {

    const DOC_SALES = [
        //미팅 준비 보고서
        1 => [
            'managerSno' => ''                       //영업 담당자
            ,'proposalType' => '2'                            //고객 희망 제안 방향
            ,'compDiv' => '2'                                  //구분(신규업체, 기존업체미팅)
            ,'meetingPlanDt' => '2021-11-15'                  //미팅 예정일
            ,'companySno' => '1'                            //업체명
            ,'companyManager' => '오징어'                     //담당자
            ,'companyManagerPosition' => '대리'          //직급
            ,'companyEmail' => 'bluesky1478@hanmail.net'                          //이메일
            ,'companyAddress' => '인천광역시 연수구 송도과학로 32 (송도테크노파크IT센터) M동 801호'                      //주소
            ,'selectCriteria' => '경쟁'                           //업체 선정 기준
            ,'selectFactor' => '단가'                             //업체 선정 요소
            ,'producePeriod' => '3'                        //생산 기간 안내
            ,'interestSample' => '동계점퍼'                       //이노버 관심 샘플
            ,'preferMall' => '중'                             //폐쇄몰 선호도
            ,'workEnv' => '필요'                               //근무 환경 (필요, 불필요)
            ,'hopeData' => [  // 희망 데이터
                0 => [
                    'style' => '스타일1'                                 // 스타일
                    ,'count' => '10'                              // 예상수량
                    ,'currentPrice' => '5000'                     // 현재단가
                    ,'targetPrice' => '4000'                      // 타겟단가
                    ,'progMode' => '개선'                      // 진행형태
                    ,'orderDt' => '2021-11-15'                          // 예상발주
                    ,'deliveryDt' => '2021-11-20'                      // 희망납기
                    ,'discomfort' => '특별히 없음'                     // 불편사항
                ]
            ]
            ,'recommendData' => [  // 제안방향
                0 => [ 'contents' => '제안내용1' ],                // 제안내용
                1 => [ 'contents' => '제안내용2' ],                // 제안내용
                2 => [ 'contents' => '제안내용3' ],                // 제안내용
            ]
            ,'checkList' => [  // 미팅 체크리스트
                0 => [
                    'item' => '기존 유니폼 샘플확보'                           // 항목
                    ,'ready' => '완료'                         // 준비
                    ,'confirm' => '완료'                      // 확인
                    ,'etc' => '비고내용....1'                            // 비고
                ],
                1 => [
                    'item' => '기존 유니폼 샘플 분석'                           // 항목
                    ,'ready' => '완료'                         // 준비
                    ,'confirm' => '완료'                      // 확인
                    ,'etc' => '비고내용....2'                            // 비고
                ],
                2 => [
                    'item' => '폐쇄몰 자료 준비'                           // 항목
                    ,'ready' => '준비중'                         // 준비
                    ,'confirm' => '확인중'                      // 확인
                    ,'etc' => '비고내용....3'                            // 비고
                ],
            ]
            /*,'workRequest' => [
                0 => [
                    // 타부서 업무요청
                    'writeManagerSno' => ''                 // 작성자
                    ,'targetDeptNo' => ''                      // 대상부서
                    ,'reqContents' => ''                       // 요청내용
                    ,'resContents' => ''                        // 답변내용
                    ,'completeRequestDt' => ''             // 처리완료요청일
                    ,'isProcFl' => 'n'                            // 처리여부
                ]
            ]*/
            , 'fileCustomer' => [] //고객사 정보 파일
            , 'fileSample' => [] //유니폼 샘플 파일
            , 'fileEstimate' => [] // 예상 견적서 파일
        ]
        , 2 => [
            'companySno' => '45'
        ]
    ];


    /**
     * 문서에서 사용하는 코드맵
     */
    const PRJ_CODE_MAP = [
        '구매형태' => ['단독'=>'단독', '경쟁'=>'경쟁', '단가입찰'=>'단가입찰'],
        '경쟁업체' => ['기존업체'=>'기존업체', '다수업체'=>'다수업체'],
        '업체선정요소' => ['디자인'=>'디자인', '품질'=>'품질', '단가'=>'단가'],
    ];



}


