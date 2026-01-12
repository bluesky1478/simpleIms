<?php
namespace SlComponent\Util;

use SlComponent\Database\DBUtil;
use SlComponent\Database\SearchVo;

/**
 * 문서 구조
 */
class DocumentStruct {

    /**
     * 문서 구조
     */
    const DOCUMENT = [
        //영업
        'SALES' => [
                //미팅 준비 보고서
                10=>[
                    'meetingPlanDt' => ''               //미팅 예정일
                    ,'selectCriteria' => ''              //업체 선정 기준
                    ,'selectFactor' => ''                //업체 선정 요소
                    ,'producePeriod' => ''               //생산 기간 안내
                    ,'interestSample' => ''              //이노버 관심 샘플
                    ,'preferMall' => ''                  //폐쇄몰 선호도
                    ,'workEnv' => ''                     //근무 환경 (필요, 불필요)
                    ,'hopeData' => []  // 희망 데이터
                    ,'recommendData' => [  // 제안방향
                        0 => [ 'contents' => '' ]                // 제안내용
                    ]
                    ,'checkList' => [  // 미팅 체크리스트
                        0 => [
                            'item' => ''                          // 항목
                            ,'ready' => ''                        // 준비
                            ,'confirm' => ''                      // 확인
                            ,'etc' => ''                          // 비고
                        ]
                    ]
                    , 'fileSample' => []   //유니폼 샘플 파일
                    , 'fileEstimate' => [] // 예상 견적서 파일
                ]
                //미팅 보고서
                ,20=> [
                    'selectCriteria' => ''                 //업체 선정 기준
                    ,'selectFactor' => ''                  //업체 선정 요소
                    ,'producePeriod' => ''                 //생산 기간 안내
                    ,'preferMall' => ''                    //폐쇄몰 선호도
                    ,'workEnv' => ''                       //근무 환경 (필요, 불필요)
                    ,'sampleSupport' => ''                 //샘플제공여부
                    ,'sampleCostType' => ''                //샘플비
                    ,'sampleCostTypeAmount' => ''          //샘플유상금액
                    ,'designColor' => []                   //디자인 색상
                    ,'designConcept' => []                 //디자인 컨셉
                    ,'interestSample' => ''                //이노버 관심 샘플
                    ,'portfolioDt' => ''                   //포트폴리오 희망일
                    ,'sampleDt' => ''                      //샘플 제작 희망일
                    ,'meetingDt' => ''                     //미팅일자
                    ,'hopeData' => []
                ]
                //근무환경 보고서
                ,30=> [
                    'arrivalDt' => ''                      //방문일
                    ,'arrivalManager' => ''                //방문자
                    ,'arrivalDept' => ''                   //부서명
                    ,'workProcess' => ''                   //작업공정
                    ,'workContents' => ''                  //작업내용
                    ,'workEnvData' => [                    //작업환경
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
                    , 'envRating' => [                     //환경평가
                        0 => [
                            'styleName' => '' ,
                            'ratingItem' => [],
                            'totalRatingGrade' => '' ,
                            'canvasInstance' => '',
                            'canvasDefaultImage' => '',
                            'ratingDescription' => '',
                        ]
                    ]
                ]
                //계약서
                ,40=> []
                //발주 확정서
                ,50=> [
                    'cstOrderDt' => ''          //고객 발주일
                    , 'cstDeliveryDt' => ''     //고객 납기
                    , 'prdOrderDt' => ''        //생산서 발주 확정일
                    , 'prdDeliveryDt' => ''     //생산처 납기
                    , 'estimateDt' => ''        //견적서 전달일.
                    , 'contractDt' => ''        //계약서 확정일
                    , 'taxMethod' => ''         //계산서 발행 방법
                    , 'paymentCondition' => ''  //결제조건
                    , 'packingMethod' => ''     //패킹방법
                    , 'deliveryMethod' => ''    //배송방법
                    , 'mallCondition' => ''     //폐쇄몰 비용 및 조건
                    , 'n3plVolume' => ''        //3PL 입고 물량
                    , 'mallAcceptList' => ''    //폐쇄몰 확정 리스트
                    , 'confirmedData' => [      // 확정 정보
                        0 => [
                            'style' => ''                    // 스타일 (링크)
                            ,'count' => ''                   // 수량(링크)
                            ,'salesPrice' => ''              // 판매가(링크)
                            ,'salesTotalPrice' => 0          // 타겟단가(링크)
                            ,'factorySno' => ''              // 생산처
                            ,'prdPrice' => ''                // 생산처
                            ,'prdTotalPrice' => ''           // 생산처
                            ,'etc' => ''                     // 비고
                        ]
                    ]
                ]
                //견적서
                ,60=> [
                    'client' => ''
                    , 'clientPhone' => ''
                    , 'designManagerSno' => ''  //디자인 담당자
                    , 'receiver' => ''          //받는분
                    , 'estimateDt' => ''        //견적일자
                    , 'paymentMethod' => ''     //결제방법
                    , 'estimateAvail' => '30'   //유효일자
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
                ]
                //폐쇄몰 준비 자료
                ,90=>[
                    'mallData' => [
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
                ]
        ],
        //디자인
        'DESIGN' => [
            //디자인 기획서
            10=> [
                'designDirection' => [],          //디자인 방향
            ],
            //포트폴리오
            20=> [
                'feedbackDt' => '',             //피드백 요청일
                'selectedStyleNo' => 0,
                'portData' => [],
                'styleData' => [],
            ],
            //샘플의뢰서
            30=> [
                'sampleData' => []
            ],
            //피팅체크리스트
            40=> [
                'sampleData' => []
            ],
        ],
        //생산
        'QC' => [
            //가견적/일정
            10=> [],
            //BT의뢰
            20=> [],
            //생지확보
            30=> [],
            //구매 확정서
            40=> [],
        ],
        
        //최종확정
        'ORDER1' => [
            10=> [], //판매구매확정서
        ],
        //사양서
        'ORDER2' => [
            //유니폼 디자인 가이드
            10=> [
                'feedbackDt' => '',     //피드백 날짜             
                'fileManual' => [],     //매뉴얼 파일
                'step1' => '',          //[일정] 디자인 확정
                'step2' => '',          //[일정] 샘플 확정
                'step3' => '',          //[일정] 고객 발주
                'step4' => '',          //[일정] 납품 예정
                'contractFl' => '유',   //계약서 진행
                'contractPub' => '',    //계약서 발행
                'payCondition' => '',   //결제 조건
                'payMethod' => '',      //결제 방법
                'deliveryMethod' => '', //발송 방법
                'deliveryAddress' => '',//발송 주소
                'deliveryManager' => '',//담당자
                'deliveryPhone' => '',  //담당자 연락처
                'sampleData' => [],
                'commentList' => [],
            ],
        ],
        //작업지시서
        'ORDER3' => [
            //작업지시서
            10=> [
                'sampleData' => []
            ],
        ],
    ];


    /**
     * 문서 부분 구조
     */
    const DOC_PART = [
        //미팅 보고서 스타일 희망 데이터
        'hopeData' => [
            'style' => ''                // 스타일
            ,'count' => ''               // 예상수량
            ,'currentPrice' => ''        // 현재단가
            ,'targetPrice' => ''         // 타겟단가
            ,'hopePrice' => ''           // 희망단가
            ,'progMode' => ''            // 진행형태
            ,'orderDt' => ''             // 예상발주
            ,'deliveryDt' => ''          // 희망납기
            ,'discomfort' => ''          // 불편사항
        ],
        //포트폴리오 데이터
        'portData' => [
            'styleType' => '',          //스타일타입
            'styleName' => '',          //스타일명
            'imageThumbnailFile' => '', //이미지 썸네일(파일)
            'imageThumbnail' => '',     //이미지 썸네일
            'imageDetailFile' => '',    //이미지 상세(파일)
            'imageDetail' => '',        //이미지 상세
            'status' => 0,              //포트폴리오 진행 상태
            'showCommentReg' => 0,      //
            'comment' => '',
            'commentList' => [],        //
        ],
        //디자인 스펙
        'designSpec' => [
            'specItemName'=> '',
            'completeSpec'=> 0,
            'guideSpec'=> 0,
            'specDiff'=> 0,
            'avg'=> 0,
            'specUnit'=> 'cm',
            'specDescription'=> '',
            'isCustomerGuideFl'=> 'y',
            'checkSpec'=> [],
        ],
        //디자인 샘플
        'designSample' => [
            'styleType' => '',        //스타일타입 (0. 춘추점퍼, 1. 동계점퍼, 2. 조끼)
            'styleName' => '',        //스타일명
            'serial' => '',           //시리얼
            'sizeDisplay' => '',      //사이즈(표기기준)
            'fit' => '',              //핏
            'season' => '',           //시즌
            'productName' => '',      //제품명
            'produceType' => '샘플',   //생산구분
            'produceCountry' => '',   //생산국가
            'sampleFactorySno' => '', //샘플공장(샘플처)
            'requestDt' => '',        //의뢰일
            'completeDt' => '',       //완료 요청일
            'receiveMethod' => '',    //수령방법
            //사이즈스팩
            'specType' => '',         //구분
            'sampleSize' => '',       //샘플제작사이즈
            'sampleItem' => [
                //초기화시 등록
            ],
            'partInfo' => [],    //원자재
            'subPartInfo' => [], //부자재
            'fileSample' => [],         //샘플 도식화 이미지
            'fileSamplePreview' => [],  //샘플 도식화 이미지 미리보기
            'fileEtc' => [],            //솜 중량 정보
            'etcMemo' => '',            //기타
        ],
        //디자인 체크
        'designCheck' => [
            'styleType' => '',        //스타일타입 (0. 춘추점퍼, 1. 동계점퍼, 2. 조끼)
            'styleName' => '',        //스타일명
            'serial' => '',           //시리얼
            'sizeDisplay' => '',      //사이즈(표기기준)
            'fit' => '',              //핏
            'sampleFactorySno' => '', //샘플공장(샘플처)
            'requestDt' => '',        //의뢰일
            'completeDt' => '',       //완료 요청일
            'receiveMethod' => '',    //수령방법
            //사이즈스팩
            'specType' => '',   //구분
            'sampleSize' => '', //샘플제작사이즈
            'sampleItem' => [
                //초기화시 등록
            ],
            'checkItem' => [
                0 => [
                    'check1' => '',   //체크사항
                    'check2' => '',   //문제사항
                    'check3' => '',   //비고
                ]
            ],
            'fileSample' => [],
            'fileSamplePreview' => [],
        ],
        //디자인 작업지시서
        'designWork' => [
            'styleType' => '',        //스타일타입 (0. 춘추점퍼, 1. 동계점퍼, 2. 조끼)
            'styleName' => '',        //스타일명
            'serial' => '',           //시리얼
            'sizeDisplay' => '',      //사이즈(표기기준)
            'fit' => '',              //핏
            'season' => '',           //시즌
            'productName' => '',      //제품명
            'produceType' => '샘플',   //생산구분
            'produceCountry' => '',   //생산국가
            'sampleFactorySno' => '', //샘플공장(샘플처)
            'requestDt' => '',        //의뢰일
            'completeDt' => '',       //완료 요청일
            'receiveMethod' => '',    //수령방법
            //사이즈스팩
            'specType' => '',       //구분
            'sampleItem' => [],
            'partInfo'   => [],     //원자재
            'subPartInfo' => [],    //부자재
            //-- 비공통
            'optionList' => [
                0=> [
                    'optionName' => '',
                    'optionTotalCount' => 0,
                    'optionInputTotalCount' => 0,
                ],
            ],
            'typeList' => [
                0=> [
                    'typeName' => '',
                    'optionCount' => [''],
                    'inputCount' => [''],
                    'typeTotalCount' => 0,
                    'typeInputTotalCount' => 0,
                ],
            ],
            'checkList' => [],
            'itemTotalCount' => 0,
            'itemInputTotalCount' => 0,
            'markList' => [
                0 => [
                    'fileMark' => [],
                    'position' => '', //위치
                    'kind' => '', //종류
                    'color' => '', //색상
                    'size' => '', //크기
                ]
            ],
            'markCaution' => '', //마크 작업 유의사항
            'labelCaution' => '', //마크 라벨 작업 유의사항
            'sizeCaution' => '', //사이즈 스펙 유의사항
            'partCaution' => '', //원부자재 관련 유의사항
            'foldCaution' => '', //접는방법 유의사항
            'fileSizeSpec' => [],
            'fileMarkLabel' => [],
            'fileFold1' => [],
            'fileFold2' => [],
            'fileFold3' => [],
            'fileSample' => [],         //샘플 도식화 이미지
            'fileSamplePreview' => [],  //샘플 도식화 이미지 미리보기
            'etcMemo' => '',            //기타
        ],
        //유니폼 디자인 가이드
        'designOrder' => [
            'styleType' => '',        //스타일타입 (0. 춘추점퍼, 1. 동계점퍼, 2. 조끼)
            'styleName' => '',        //스타일명
            'serial' => '',           //시리얼
            'sizeDisplay' => '',      //사이즈(표기기준)
            'fit' => '',              //핏
            'season' => '',           //시즌
            'productName' => '',      //제품명
            'produceType' => '샘플',   //생산구분
            'produceCountry' => '',   //생산국가
            'sampleFactorySno' => '', //샘플공장(샘플처)
            'requestDt' => '',        //의뢰일
            'completeDt' => '',       //완료 요청일
            'receiveMethod' => '',    //수령방법
            'fabricInfo' => '',       //원단정보
            //사이즈스팩
            'specType' => '',       //구분
            'sampleItem' => [],
            'partInfo'   => [],     //원자재
            'subPartInfo' => [],    //부자재
            //-- 비공통
            'optionList' => [
                0=> [
                    'optionName' => '',
                    'optionTotalCount' => 0,
                    'optionInputTotalCount' => 0,
                ],
            ],
            'typeList' => [
                0=> [
                    'typeName' => '',
                    'optionCount' => [''],
                    'inputCount' => [''],
                    'typeTotalCount' => 0,
                    'typeInputTotalCount' => 0,
                ],
            ],
            'checkList' => [],
            'itemTotalCount' => 0,
            'itemInputTotalCount' => 0,
            'markList' => [
                0 => [
                    'fileMark' => [],
                    'position' => '', //위치
                    'kind' => '', //종류
                    'color' => '', //색상
                    'size' => '', //크기
                ]
            ],
            'fileSizeSpec' => [],
            'fileMarkLabel' => [],
            'fileSample' => [],         //상품 도식화 이미지
            'fileSamplePreview' => [],  //상품 도식화 이미지 미리보기
            'fileThumbnail' => [],      //썸네일 
            'fileThumbnailPreview' => [],  //썸네일 미리보기
        ],
    ];

    /**
     * 프로젝트 상품 구조
     */
    const PRJ_PRODUCT = [
        'prdName' => '',
        'prdStyleType' => '', //hidden
        'prdStyleName' => '', //hidden
        'count' => '',
        'factorySno' => '',
        'producePlan' => [],
        'companyEtc' => '',
        'factoryEtc' => '',
    ];

    /**
     * 프로젝트 상품 체크 항목
     */
    const PRJ_PRODUCT_PLAN_LIST = [
      0 => ['planName' => '고객발주', 'planNameShort' => '고객발주' ],
      1 => ['planName' =>  '생산발주', 'planNameShort' => '생산발주' ],
      2 => ['planName' =>  '생산납기', 'planNameShort' => '생산납기' ],
      3 => ['planName' =>  '고객납기', 'planNameShort' => '고객납기' ],
      4 => ['planName' =>  '세탁&<br>이화학검사', 'planNameShort' => '세탁/이화학' ],
      5 => ['planName' =>  '원부자재 확정', 'planNameShort' => '원부자재확정' ],
      6 => ['planName' =>  '원부자재 선적', 'planNameShort' => '원부자재선적' ],
      7 => ['planName' =>  'QC샘플', 'planNameShort' => 'QC샘플' ],
      8 => ['planName' =>  '생산투입', 'planNameShort' => '생산투입' ],
      9 => ['planName' =>  '인라인샘플', 'planNameShort' => '인라인샘플' ],
      10 => ['planName' =>  '생산완료<br>(선적예정)', 'planNameShort' => '생산완료' ],
      11 => ['planName' =>  '도착&검수', 'planNameShort' => '도착&검수' ],
      12 => ['planName' =>  '고객납품', 'planNameShort' => '고객납품' ],
      13 => ['planName' =>  '납품평가서', 'planNameShort' => '납품평가서' ],
    ];

}


