<?php
namespace Component\Work;

use Component\Work\Code\DocumentDesignCodeMap;
use SiteLabUtil\SlCommonUtil;

/**
 * Sitelab Code Key List
 * Class SlCode
 * @package SlComponent\Util
 */
class DocumentCodeMap {

    const DOC_SALES_NAME = [
        1 => '미팅준비보고서',
        2 => '미팅보고서',
        3 => '근무환경 보고서',
        4 => '생산견적 요청서', //없네.
        5 => '폐쇄몰준비자료',  //
        6 => '견적서',
        7 => '발주확정서',
        8 => '계약서',
    ];

    const DOC_DESIGN_NAME = [
        1 => '디자인컨셉',
        2 => '포트폴리오',
        3 => '샘플의뢰서',
        4 => '피팅체크리스트',
        5 => '작업지시서',
        6 => '유니폼디자인가이드',
    ];

    const DOC_SALES_LINK = [
        1 => 'sales_meeting_ready_reg',
        2 => 'sales_meeting_reg',
        3 => 'sales_workenv_reg',
        4 => 'sales_prd_estimate_reg',
        5 => 'sales_mall_document_reg',
        6 => 'sales_estimate_reg',
        7 => 'sales_order',
        8 => 'sales_contract',
    ];

    const DOC_MAIL_LINK = [
        'SALES' =>[
            '2' => [
                'subject' => '미팅보고서 안내',
                'link' => 'wcustomer/meeting_list.php',
                'template' => 'work_meeting.php',
            ],
            '6' => [
                'subject' => '견적서 안내',
                'link' => 'wcustomer/estimate_list.php',
                'template' => 'work_estimate.php',
            ],
            //FIXME : 뭘 보여줘야할 지 모르겠다. ? 유니폼 디자인 보내면 끝일 듯.
            '7' => [
                'subject' => '발주확정 안내',
                'link' => 'wcustomer/xxxxxx.php',
                'template' => 'work_xxxxxx.php',
            ],
            '8' => [
                'subject' => '계약서 전달',
                'link' => 'wcustomer/index.php',
                'template' => '-',
            ]
        ],
        'DESIGN' =>[
            '2' => [
                    'subject' => '포트폴리오 안내',
                    'link' => 'wcustomer/portfolio_view.php',
                    'template' => 'work_portfolio.php',
                ],
            '6' => [
                    'subject' => '유니폼 디자인 가이드(발주) 안내',
                    'link' => 'wcustomer/guide_view.php',
                    'template' => 'work_guide.php',
                ],
        ],
    ];

    const DOC_DESIGN_LINK = [
        1 => 'design_concept', //디자인컨셉
        2 => 'design_portfolio', //포트폴리오
        3 => 'design_sample', //샘플의뢰서
        4 => 'design_check', //피팅체크리스트
        5 => 'design_work', //작업지시서
        6 => 'design_order', //발주.
    ];
    const DOC_DESIGN_LOAD_DOC = [
        1 => ['name'=>'미팅보고서', 'docDept'=>'Sales', 'docNo' => 2],// 'design_concept',
        2 => ['name'=>'미팅보고서', 'docDept'=>'Sales', 'docNo' => 2],// 'design_portfolio',
        3 => ['name'=>'미팅보고서', 'docDept'=>'Sales', 'docNo' => 2],// 'design_sample',
        4 => ['name'=>'샘플의뢰서', 'docDept'=>'Design', 'docNo' => 3],// 'design_check',
        5 => ['name'=>'미팅보고서', 'docDept'=>'Sales', 'docNo' => 2],// 'design_work',
        6 => ['name'=>'미팅보고서', 'docDept'=>'Sales', 'docNo' => 2],// 'design_guide',
    ];

    const DOC_SALES = [
        //미팅 준비 보고서
        1 => [
            'managerSno' => ''                       //영업 담당자
            ,'proposalType' => '0'                            //고객 희망 제안 방향
            ,'compDiv' => '0'                                  //구분(신규업체, 기존업체미팅)
            ,'meetingPlanDt' => ''                          //미팅 예정일
            ,'companySno' => ''                            //업체명
            ,'companyManager' => ''                     //담당자
            ,'companyManagerPosition' => ''          //직급
            ,'companyManagerPhone' => ''          //연락처
            ,'companyEmail' => ''                          //이메일
            ,'companyAddress' => ''                      //주소
            ,'selectCriteria' => ''                           //업체 선정 기준
            ,'selectFactor' => ''                             //업체 선정 요소
            ,'producePeriod' => ''                        //생산 기간 안내
            ,'interestSample' => ''                       //이노버 관심 샘플
            ,'preferMall' => ''                             //폐쇄몰 선호도
            ,'workEnv' => ''                               //근무 환경 (필요, 불필요)
            ,'hopeData' => [  // 희망 데이터
                0 => [
                    'style' => ''                                 // 스타일
                    ,'count' => ''                              // 예상수량
                    ,'currentPrice' => ''                     // 현재단가
                    ,'targetPrice' => ''                      // 타겟단가
                    ,'hopePrice' => ''                      // 희망단가
                    ,'progMode' => ''                      // 진행형태
                    ,'orderDt' => ''                          // 예상발주
                    ,'deliveryDt' => ''                      // 희망납기
                    ,'discomfort' => ''                     // 불편사항
                ]
            ]
            ,'recommendData' => [  // 제안방향
                0 => [ 'contents' => '' ]                // 제안내용
            ]
            ,'checkList' => [  // 미팅 체크리스트
                0 => [
                    'item' => ''                           // 항목
                    ,'ready' => ''                         // 준비
                    ,'confirm' => ''                      // 확인
                    ,'etc' => ''                            // 비고
                ]
            ]
            , 'fileCustomer' => [] //고객사 정보 파일
            , 'fileSample' => [] //유니폼 샘플 파일
            , 'fileEstimate' => [] // 예상 견적서 파일
        ],
        //미팅보고서
        2 => [
            'managerSno' => ''                          //영업 담당자
            ,'managerName' => ''                       //영업 담당자
            ,'designManagerSno' => ''                       //디자인 담당자
            ,'proposalType' => '0'                            //고객 희망 제안 방향
            ,'proposalTypeName' => ''                            //고객 희망 제안 방향
            ,'msProposalType' => '0'                        //MS 고객 희망 제안 방향
            ,'msProposalTypeName' => ''                        //MS 고객 희망 제안 방향
            ,'compDiv' => '0'                                  //구분(신규업체, 기존업체미팅)
            ,'compDivName' => ''                                  //구분(신규업체, 기존업체미팅)
            ,'companySno' => ''                            //업체명
            ,'companyManager' => ''                     //담당자
            ,'companyManagerPosition' => ''          //직급
            ,'companyManagerPhone' => ''          //연락처
            ,'companyEmail' => ''                          //이메일
            ,'companyAddress' => ''                      //주소
            ,'selectCriteria' => ''                           //업체 선정 기준
            ,'selectFactor' => ''                             //업체 선정 요소
            ,'producePeriod' => ''                        //생산 기간 안내
            ,'preferMall' => ''                             //폐쇄몰 선호도
            ,'workEnv' => ''                               //근무 환경 (필요, 불필요)
            ,'sampleSupport' => ''                      //샘플제공여부
            ,'sampleCostType' => ''                     //샘플비
            ,'sampleCostTypeAmount' => ''          //샘플유상금액
            ,'designColor' => []                       //디자인 색상
            ,'designConcept' => []                       //디자인 컨셉
            ,'interestSample' => ''                       //이노버 관심 샘플
            ,'portfolioDt' => ''                               //포트폴리오 희망일
            ,'sampleDt' => ''                               //샘플 제작 희망일
            ,'meetingDt' => ''                             //미팅일자
            ,'hopeData' => [  // 희망 데이터
                0 => [
                    'style' => ''                                 // 스타일
                    ,'count' => ''                              // 예상수량
                    ,'currentPrice' => ''                     // 현재단가
                    ,'targetPrice' => ''                      // 타겟단가
                    ,'hopePrice' => ''                      // 희망단가
                    ,'progMode' => ''                      // 진행형태
                    ,'orderDt' => ''                          // 예상발주
                    ,'deliveryDt' => ''                      // 희망납기
                    ,'discomfort' => ''                     // 불편사항
                ]
            ]
            , 'fileCustomer' => [] //고객사 정보 파일
            , 'currentStep' => 0
            , 'stepData' => [
                0 => '', //미팅
                1 => '', //포트폴리오
                2 => '', //실물샘플
                3 => '', //발주
                4 => '', //생산완료
                5 => '', //납품
            ],
            'sendEmail' => '',
            'sendDt' => '',
        ],
        //근무환경 보고서
        3 => [
            'managerSno' => ''                               //영업 담당자
            ,'msProposalType' => ''                        //MS 고객 희망 제안 방향
            ,'compDiv' => ''                                  //구분(신규업체, 기존업체미팅)
            ,'companySno' => ''                            //업체명
            ,'companyManager' => ''                     //담당자
            ,'companyManagerPosition' => ''          //직급
            ,'companyManagerPhone' => ''          //연락처
            ,'companyEmail' => ''                          //이메일
            ,'companyAddress' => ''                      //주소
            , 'fileCustomer' => [] //고객사 정보 파일
            ,'arrivalDt' => ''                           //방문일
            ,'arrivalManager' => ''                  //방문자
            ,'arrivalDept' => ''                       //부서명
            ,'workProcess' => ''                     //작업공정
            ,'workContents' => ''                   //작업내용
            //작업환경
            ,'workEnvData' => [
                0 => [
                    'evn1' => ''                   //작업공정
                    ,'evn2' => ''                   //측정시간
                    ,'evn3' => ''                   //온도
                    ,'evn4' => ''                   //습도
                    ,'evn5' => ''                   //풍량
                    ,'evn6' => ''                   //소음
                    ,'evn7' => ''                   //조광
                    ,'evn8' => ''                   //비고
                ]
            ]
            , 'envRating' => [
                0 => [
                    'styleName' => '' ,
                    'ratingItem' => [],
                    'totalRatingGrade' => '' ,
                    'canvasInstance' => '',
                    'canvasDefaultImage' => '',
                    'ratingDescription' => '',
                ]
            ]
        ],
        //생산견적 요청서
        4 => [
            'managerSno' => ''                       //영업 담당자
            ,'msProposalType' => ''                        //MS 고객 희망 제안 방향
            ,'compDiv' => ''                                  //구분(신규업체, 기존업체미팅)
            ,'companySno' => ''                            //업체명
            ,'companyManager' => ''                     //담당자
            ,'companyManagerPosition' => ''          //직급
            ,'companyManagerPhone' => ''          //연락처
            ,'companyEmail' => ''                          //이메일
            ,'companyAddress' => ''                      //주소
            ,'selectCriteria' => ''                           //업체 선정 기준
            ,'selectFactor' => ''                             //업체 선정 요소
            ,'producePeriod' => ''                        //생산 기간 안내
            ,'preferMall' => ''                             //폐쇄몰 선호도
            ,'workEnv' => ''                               //근무 환경 (필요, 불필요)
            ,'sampleSupport' => ''                      //샘플제공여부
            ,'sampleCostType' => ''                     //샘플비
            ,'sampleCostTypeAmount' => ''          //샘플유상금액
            ,'designColor' => []                       //디자인 색상
            ,'designConcept' => []                       //디자인 컨셉
            ,'interestSample' => ''                       //이노버 관심 샘플
            ,'portfolioDt' => ''                               //포트폴리오 희망일
            ,'sampleDt' => ''                               //샘플 제작 희망일
            ,'hopeData' => [  // 희망 데이터
                0 => [
                    'style' => ''                                 // 스타일
                    ,'count' => ''                              // 예상수량
                    ,'currentPrice' => ''                     // 현재단가
                    ,'targetPrice' => ''                      // 타겟단가
                    ,'hopePrice' => ''                      // 희망단가
                    ,'progMode' => ''                      // 진행형태
                    ,'orderDt' => ''                          // 예상발주
                    ,'deliveryDt' => ''                      // 희망납기
                ]
            ]
            , 'orderPossible'  => '' //발주 가능성
            , 'isBtProc'  => '' //BT진행여부
            , 'isSgGet'  => '' //생지확보여부
            , 'hopeFactory'  => '' //희망생산처
            , 'fileDesign' => [] //디자인 정보 파일
            , 'fileEstimate' => [] //견적서 파일
        ],
        //폐쇄몰준비자료
        5 => [
            'managerSno' => ''                       //영업 담당자
            ,'msProposalType' => ''                    //MS 고객 희망 제안 방향
            ,'compDiv' => ''                                  //구분(신규업체, 기존업체미팅)
            ,'companySno' => ''                            //업체명
            ,'companyManager' => ''                     //담당자
            ,'companyManagerPosition' => ''          //직급
            ,'companyManagerPhone' => ''          //연락처
            ,'companyEmail' => ''                          //이메일
            ,'companyAddress' => ''                      //주소
            ,'mallData' => [  // 폐쇄몰 자료
                0 => [
                    'itemName' => '회원가입'
                    ,'itemValue' => ''
                    ,'itemEtc' => ''
                    ,'itemEtcPlaceHolder' => ''
                    ,'itemIdx' => '1'
                ],
               1 => [
                    'itemName' => '출고 횟수 / 요일'
                   ,'itemValue' => ''
                   ,'itemEtc' => ''
                   ,'itemEtcPlaceHolder' => ''
                   ,'itemIdx' => '2'
                   ,'itemText1' => ''
                   ,'itemText2' => ''
                ],
                2 => [
                    'itemName' => '관리자 출고 승인'
                    ,'itemValue' => ''
                    ,'itemEtc' => ''
                    ,'itemEtcPlaceHolder' => ''
                    ,'itemIdx' => '3'
                ],
                3 => [
                    'itemName' => '출고 수량 취합'
                    ,'itemValue' => ''
                    ,'itemEtc' => ''
                    ,'itemEtcPlaceHolder' => ''
                    ,'itemIdx' => '4'
                ],
                4 => [
                    'itemName' => '상품 결제'
                    ,'itemValue' => ''
                    ,'itemEtc' => ''
                    ,'itemEtcPlaceHolder' => '분할 결제시 기타에 체크 후 비율 기재'
                    ,'itemIdx' => '5'
                ],
                5 => [
                    'itemName' => '배송비 결제'
                    ,'itemValue' => ''
                    ,'itemEtc' => ''
                    ,'itemEtcPlaceHolder' => ''
                    ,'itemIdx' => '6'
                ],
                6 => [
                    'itemName' => '배송 방법'
                    ,'itemValue' => ''
                    ,'itemEtc' => ''
                    ,'itemEtcPlaceHolder' => ''
                    ,'itemIdx' => '7'
                ],
                7 => [
                    'itemName' => '정책 : 진행방법'
                    ,'itemValue' => ''
                    ,'itemEtc' => ''
                    ,'itemEtcPlaceHolder' => ''
                    ,'itemIdx' => '8'
                ],
                8 => [
                    'itemName' => '영수증 처리'
                    ,'itemValue' => ''
                    ,'itemEtc' => ''
                    ,'itemEtcPlaceHolder' => '발행 시점 / 처리방법'
                    ,'itemIdx' => '9'
                ],
                9 => [
                    'itemName' => '교환, 반품, AS 규정'
                    ,'itemValue' => ''
                    ,'itemEtc' => ''
                    ,'itemEtcPlaceHolder' => '택배비->단순변심:고객 부담/상품 불량 : 이노버 부담'
                    ,'itemIdx' => '10'
                    ,'itemText1' => ''
                ],
                10 => [
                    'itemName' => '재고 소진 (안전재고 미달시)'
                    ,'itemValue' => ''
                    ,'itemEtc' => ''
                    ,'itemEtcPlaceHolder' => ''
                    ,'itemIdx' => '11'
                ],

           ]
            ,'mallEtcData' => [
                0 => [
                    'itemName' => '',
                    'itemValue' => '',
                ]
            ]
        ],
        //견적서
        6 => [
            'managerSno' => ''                       //영업 담당자
            ,'msProposalType' => ''                    //MS 고객 희망 제안 방향
            ,'compDiv' => ''                                  //구분(신규업체, 기존업체미팅)
            ,'companySno' => ''                            //업체명
            ,'companyManager' => ''                     //담당자
            ,'companyManagerPosition' => ''          //직급
            ,'companyManagerPhone' => ''          //연락처
            ,'companyEmail' => ''                          //이메일
            ,'companyAddress' => ''                      //주소
            , 'client' => ''
            , 'clientPhone' => ''
            , 'designManagerSno' => '' //디자인 담당자
            , 'receiver' => '' //받는분
            , 'estimateDt' => '' //견적일자
            , 'paymentMethod' => '' //결제방법
            , 'estimateAvail' => '30' //유효일자
            , 'estimateData' => [
                0 => [
                    'product' => '',
                    'qty' => '',
                    'unitPrice' => '',
                    'amount' => '',
                    'vat' => '',
                ]
            ] //유효일자
            , 'totalAmount' => 0
            , 'totalVat' => 0
            , 'totalPrice' => 0
            , 'etc' => '' //기타사항
            , 'sendEmail' => '' //발송이메일
            , 'sendDt' => '' //발송일
        ],
        //발주확정서
        7 => [ 
            'managerSno' => ''                       //영업 담당자
            ,'companySno' => ''                            //업체명
            ,'companyManager' => ''                     //담당자
            ,'companyManagerPosition' => ''          //직급
            ,'companyManagerPhone' => ''          //연락처
            ,'companyEmail' => ''                          //이메일
            ,'companyAddress' => ''                      //주소
            //일정
            , 'cstOrderDt' => '' //고객 발주일
            , 'cstDeliveryDt' => '' //고객 납기
            , 'prdOrderDt' => '' //생산서 발주 확정일
            , 'prdDeliveryDt' => '' //생산처 납기
            , 'estimateDt' => '' //견적서 전달일.
            , 'contractDt' => '' //계약서 확정일
            , 'taxMethod' => '' //계산서 발행 방법
            , 'paymentCondition' => '' //결제조건
            , 'packingMethod' => '' //패킹방법
            , 'deliveryMethod' => '' //배송방법
            , 'mallCondition' => '' //폐쇄몰 비용 및 조건
            , 'n3plVolume' => '' //3PL 입고 물량
            , 'mallAcceptList' => '' //폐쇄몰 확정 리스트
            , 'etc' => '' //기타사항
            , 'sendEmail' => '' //발송이메일
            , 'sendDt' => '' //발송일
            , 'confirmedData' => [  // 확정 정보
                0 => [
                    'style' => ''                           // 스타일 (링크)
                    ,'count' => ''                         // 수량(링크)
                    ,'salesPrice' => ''                    // 판매가(링크)
                    ,'salesTotalPrice' => 0                    // 타겟단가(링크)
                    ,'factorySno' => ''                   // 생산처
                    ,'prdPrice' => ''                      // 생산처
                    ,'prdTotalPrice' => ''                      // 생산처
                    ,'etc' => ''                     // 비고
                ]
            ]
        ],
        //계약서
        8 => [
            'managerSno' => ''                           //영업 담당자
            ,'companySno' => ''                          //업체명
            ,'companyManager' => ''                   //담당자
            ,'companyManagerPosition' => ''        //직급
            ,'companyManagerPhone' => ''          //연락처
            ,'companyEmail' => ''                       //이메일
            ,'companyAddress' => ''                    //주소
            , 'etc' => '' //기타사항
            , 'sendEmail' => '' //발송이메일
            , 'sendDt' => '' //발송일
            , 'fileContract' => [] //계약서 파일
        ],
    ];

    const DOC_DESIGN = DocumentDesignCodeMap::DOC_DESIGN;

    const WORK_REQ_DEFAULT = [
        0 => [
            'writeManagerSno' => ''                 // 작성자
            ,'procManagerSno' => ''                 // 처리자
            ,'targetDeptNo' => ''                      // 대상부서
            ,'reqContents' => ''                       // 요청내용
            ,'resContents' => ''                        // 답변내용
            ,'completeRequestDt' => ''             // 처리완료요청일
            ,'isProcFl' => 'n'                            // 처리여부
            ,'isDelFl' => 'n'                            // 삭제여부
        ]
    ];

    const DOC_FIELD_KR = [
        //미팅 준비 보고서
        'managerSno' => [ 'name' => '영업 담당자' , 'type' => 'managerList'  ]
        ,'proposalType' => [
            'name' => '제안 방향',
            'type' => WorkCodeMap::PROPOSAL_TYPE,
        ]
        ,'msProposalType' => [
            'name' => '프로젝트',
            'type' => WorkCodeMap::MS_PROPOSAL_TYPE,
        ]
        ,'stepData' => [ 'name' => '진행계획' , 'type' => 'text'  ]
        ,'managerName' => [ 'name' => '담당자명' , 'type' => 'text'  ]
        ,'designManagerSno' => [ 'name' => '디자이너' , 'type' => 'text'  ]
        ,'designColor' => [ 'name' => '디자인 컬러', 'type' => 'text' ]
        ,'designConcept' => [ 'name' => '디자인 컨셉', 'type' => 'text' ]
        ,'sampleSupport' => [ 'name' => '기존 샘플 제공', 'type' => 'text' ]
        ,'sampleCostType' => [ 'name' => '샘플비', 'type' => 'text' ]
        ,'portfolioDt' => [ 'name' => '포트폴리오일자', 'type' => 'text' ]
        ,'sampleDt' => [ 'name' => '샘플일자', 'type' => 'text' ]
        ,'compDiv' => [ 'name' => '거래처구분', 'type' => WorkCodeMap::COMP_DIV ]
        ,'meetingPlanDt' => [ 'name' => '미팅 예정일' , 'type' => 'text' ]
        ,'companySno' => [ 'name' => '업체명' , 'type' => 'text' ]
        ,'companyManager' => [ 'name' => '담당자' , 'type' => 'text' ]
        ,'companyManagerPosition' => [ 'name' => '직급' , 'type' => 'text' ]
        ,'companyManagerPhone' => [ 'name' => '연락처' , 'type' => 'text' ]
        ,'companyEmail' => [ 'name' => '이메일' , 'type' => 'text' ]
        ,'companyAddress' => [ 'name' => '주소' , 'type' => 'text' ]
        ,'selectCriteria' => [ 'name' => '업체 선정 기준' , 'type' => 'text' ]
        ,'selectFactor' => [ 'name' => '업체 선정 요소' , 'type' => 'text' ]
        ,'producePeriod' => [ 'name' => '생산 기간 안내' , 'type' => 'text' ]
        ,'interestSample' => [ 'name' => '이노버 관심 샘플' , 'type' => 'text' ]
        ,'preferMall' => [ 'name' => '폐쇄몰 선호도' , 'type' => 'text' ]
        ,'workEnv' => [ 'name' => '근무 환경' , 'type' => 'text' ]
        ,'hopeData' => [ 'name' => '희망 단가/납기' , 'type' => 'text' ]
        , 'style' => [ 'name' => '스타일' , 'type' => 'text' ]
        ,'count' => [ 'name' => '예상수량' , 'type' => 'text' ]
        ,'currentPrice' => [ 'name' => '현재단가' , 'type' => 'text' ]
        ,'targetPrice' => [ 'name' => '타겟단가' , 'type' => 'text' ]
        ,'hopePrice' => [ 'name' => '희망단가' , 'type' => 'text' ]
        ,'progMode' => [ 'name' => '진행형태' , 'type' => 'text' ]
        ,'orderDt' => [ 'name' => '예상발주' , 'type' => 'text' ]
        ,'deliveryDt' => [ 'name' => '희망납기' , 'type' => 'text' ]
        ,'discomfort' => [ 'name' => '불편사항' , 'type' => 'text' ]
        ,'recommendData' => [ 'name' => '제안방향' , 'type' => 'text' ]
        , 'contents' => [ 'name' => '제안내용' , 'type' => 'text' ]
        , 'checkList' => [ 'name' => '미팅 체크리스트' , 'type' => 'text' ]
        , 'item' => [ 'name' => '항목' , 'type' => 'text' ]
        ,'ready' => [ 'name' => '준비' , 'type' => 'text' ]
        ,'confirm' => [ 'name' => '확인' , 'type' => 'text' ]
        ,'etc' => [ 'name' => '비고' , 'type' => 'text' ]
        ,'etcMemo' => [ 'name' => '기타' , 'type' => 'text' ]
        ,'arrivalDt' => [ 'name' => '방문일' , 'type' => 'text' ]
        ,'arrivalManager' => [ 'name' => '방문자' , 'type' => 'text' ]        
        ,'arrivalDept' => [ 'name' => '부서명' , 'type' => 'text' ]              
        ,'workProcess' => [ 'name' => '작업공정' , 'type' => 'text' ]         
        ,'workContents' => [ 'name' => '작업내용' , 'type' => 'text' ]       
        , 'fileCustomer' => [ 'name' => '고객사 정보 파일' , 'type' => 'text' ]
        , 'fileSample' => [ 'name' => '유니폼 샘플 파일' , 'type' => 'text' ]
        , 'fileEstimate' => [ 'name' => '예상 견적서 파일' , 'type' => 'text' ]
        ,'workEnvData' => [ 'name' => '작업환경' , 'type' => 'text' ]
        ,'evn1' => [ 'name' => '작업공정' , 'type' => 'text' ]
        ,'evn2' => [ 'name' => '측정시간' , 'type' => 'text' ]
        ,'evn3' => [ 'name' => '온도' , 'type' => 'text' ]
        ,'evn4' => [ 'name' => '습도' , 'type' => 'text' ]
        ,'evn5' => [ 'name' => '풍량' , 'type' => 'text' ]
        ,'evn6' => [ 'name' => '소음' , 'type' => 'text' ]
        ,'evn7' => [ 'name' => '조광' , 'type' => 'text' ]
        ,'evn8' => [ 'name' => '비고' , 'type' => 'text' ]
        , 'orderPossible'  => [ 'name' => '발주 가능성' , 'type' => 'text' ]
        , 'isBtProc'  => [ 'name' => 'BT진행여부' , 'type' => 'text' ]
        , 'isSgGet'  => [ 'name' => '생지확보여부' , 'type' => 'text' ]
        , 'hopeFactory'  => [ 'name' => '희망생산처' , 'type' => 'text' ]
        , 'mallData'  => [ 'name' => '폐쇄몰정보' , 'type' => 'text' ]
        , 'paymentMethod'  => [ 'name' => '결제방법' , 'type' => 'text' ]
        , 'estimateAvail'  => [ 'name' => '견적유효기간' , 'type' => 'text' ]
        , 'estimateDt'  => [ 'name' => '견적일자' , 'type' => 'text' ]
        , 'receiver'  => [ 'name' => '받는분' , 'type' => 'text' ]
        , 'client'  => [ 'name' => '클라이언트 이름' , 'type' => 'text' ]
        , 'clientPhone'  => [ 'name' => '클라이언트 전화번호' , 'type' => 'text' ]
        , 'sendEmail'  => [ 'name' => '수신자 메일' , 'type' => 'text' ]
        , 'estimateData'  => [ 'name' => '견적내용' , 'type' => 'text' ]
        , 'feedbackDt'  => [ 'name' => '피드백요청일' , 'type' => 'text' ]
        , 'cstOrderDt'  => [ 'name' => '고객 발주일' , 'type' => 'text' ]
        , 'cstDeliveryDt'  => [ 'name' => '고객 납기' , 'type' => 'text' ]
        , 'prdOrderDt'  => [ 'name' => '생산서 발주 확정일' , 'type' => 'text' ]
        , 'prdDeliveryDt'  => [ 'name' => '생산처 납기' , 'type' => 'text' ]
        , 'contractDt'  => [ 'name' => '계약서 확정일' , 'type' => 'text' ]
        , 'taxMethod'  => [ 'name' => '계산서 발행 방법' , 'type' => 'text' ]
        , 'paymentCondition'  => [ 'name' => '결제조건' , 'type' => 'text' ]
        , 'packingMethod'  => [ 'name' => '패킹방법' , 'type' => 'text' ]
        , 'deliveryMethod'  => [ 'name' => '배송방법' , 'type' => 'text' ]
        , 'mallCondition'  => [ 'name' => '폐쇄몰 비용 및 조건' , 'type' => 'text' ]
        , 'n3plVolume'  => [ 'name' => '3PL 입고 물량' , 'type' => 'text' ]
        , 'mallAcceptList'  => [ 'name' => '폐쇄몰 확정 리스트' , 'type' => 'text' ]
        , 'confirmedData'  => [ 'name' => '확정정보' , 'type' => 'text' ]
    ];

    /**
     * 문서에서 사용하는 코드맵
     */
    const PRJ_CODE_MAP = [
        '구매형태' => ['단독'=>'단독', '경쟁'=>'경쟁', '단가입찰'=>'단가입찰'],
        '경쟁업체' => ['기존업체'=>'기존업체', '다수업체'=>'다수업체'],
        '업체선정요소' => ['디자인'=>'디자인', '품질'=>'품질', '단가'=>'단가'],
    ];

    /**
     * 폐쇄몰 정보 항목
     */
    const PRIVATE_MALL_ITEM = [
        0 => ['엑셀 일괄등록', '구매자 개별 가입', '기타'], //회원가입
        1 => [ '출고 횟수 : 주 # 회', '출고 요일 : # ', '기타'], //출고횟수/요일
        2 => ['승인 후 출고', '출고관여 없음', '기타'], //관리자 출고 승인
        3 => ['사이트', '이메일 전달', '기타'], //출고 수량 취합
        4 => ['본사 일괄 결제','주문자 결제','기타'], // 상품결제
        5 => ['본사 일괄 결제','주문자 결제','기타'],  //배송비결제
        6 => ['선불만 가능','착불만 가능', '주문자 선택' ,'기타'], //배송방법
        7 => ['무상지급','본사+구매자 분할 구매', '기타'], //정책:진행방법
        8 => ['세금계산서','현금영수증', '카드결제' ,'기타'], // 영수증 처리
        9 => ['수령 후 7일내 접수','수령후 #일내 접수', '기타'], // 교환,반품AS
        10 => ['생산 예정 있음', '생산 안함', '논의 후 진행' , '기타'], // 재고소진
    ];
    const PRIVATE_MALL_ITEM_TIP = [
        0 => ['등록자료 필요'], //회원가입
        1 => [], //출고횟수/요일
        2 => [], //관리자 출고 승인
        3 => ['일정 기간동안 취합', '이메일로 전달시 임의주문서필요'], //출고 수량 취합
        4 => ['결제 시점 확인 필요','사이트 주문시 결제'], // 상품결제
        5 => ['결제 시점 확인 필요','사이트 주문시 결제'], // 상품결제
        6 => ['포장비용 택배비 포함','포장비용 추가 있음', '포장비용 추가 있음' ], //배송방법
        8 => ['','', '단가 인상될 수 있음' ], // 영수증 처리
    ];

    const COMMENT_SCHEMA = [
      'documentSno' => '',
      'writeManagerNm' => '',
      'writeManagerSno' => '',
      'contents' => '',
      'regDt' => '',
    ];

    /**
     * 폐쇄몰 선택 데이터
     * @param $controller
     */
    public static function setMallSelectData(){
    }

}


