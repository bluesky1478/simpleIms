<?php
namespace SlComponent\Util;

use SlComponent\Database\DBUtil;
use SlComponent\Database\SearchVo;

/**
 * Sitelab Code Key List
 * Class SlCode
 * @package SlComponent\Util
 */
class SlProjectCodeMap {

    //프로젝트 상태
    const PRJ_STATUS = [
        0 => '견적'
        , 1 => '주문'
        , 2 => '생산'
        /*, 9 => '취소'*/
    ];

    /**
     * 부서코드
     */
    const DEPT_STR = [
        '02001001' => 'SALES'
        , '02001002' => 'DESIGN'
        , '02001003' => 'QC'
    ];
    const DEPT_CODE = [
        'SALES' => '02001001'
        , 'DESIGN' => '02001002'
        , 'QC' => '02001003'
    ];

    const PRJ_DOCUMENT = [
        //영업
        'SALES' => [
            'typeName' => '영업',
            'typeDoc' => [
                10=>[
                    'name' => '미팅관리',
                    'accept' => 'n',
                ]
                ,20=> [ //메일
                    'name' =>  '미팅보고서',
                    'customerLink' => 'pop_meeting_view.php',
                    'customerLinkPopup' => 'y',
                    'accept' => 'y',
                    'mailLink' => 'wcustomer/meeting_list.php',
                    'mailTemplate' => 'work_meeting.php',
                ]
                ,30=> [
                    'name' =>  '근무환경 보고서',
                    'accept' => 'y',
                ]
                ,40=> [ //메일
                    'name' =>  '계약서',
                    'accept' => 'y',
                    'link' => 'wcustomer/index.php',
                    'mailTemplate' => 'TODO', //TODO 계약서 템플릿 만들기
                ]
                ,50=> [
                    'name' =>  '발주 확정서',
                    'accept' => 'y',
                ]
                ,60=> [
                    'name' =>  '견적서' ,
                    'customerLink' => 'pop_estimate_view.php',
                    'customerLinkPopup' => 'y',
                    'mailLink' => 'wcustomer/estimate_list.php',
                    'mailTemplate' => 'work_estimate.php',
                ]
                ,90=> [
                    'name' =>  '폐쇄몰 준비자료' ,
                ]
            ]
        ],

        //디자인
        'DESIGN' => [
            'typeName' => '디자인',
            'typeDoc' => [
                10=> [
                    'name' =>  '디자인 기획서',
                    'accept' => 'y',
                ],
                20=> [
                    'name' =>  '포트폴리오',
                    'customerLink' => 'portfolio_view.php',
                    'accept' => 'y',
                    'customerConfirm' => 'y',
                    'mailLink' => 'wcustomer/portfolio_view.php',
                    'mailTemplate' => 'work_portfolio.php',
                ],
                30=> [
                    'name' =>  '샘플의뢰서',
                    'accept' => 'y',
                ],
                40=> [
                    'name' =>  '피팅체크리스트',
                    'accept' => 'y',
                ],
            ]
        ],
        
        //생산
        'QC' => [
            'typeName' => '생산',
            'typeDoc' => [
                10=> [
                    'name' =>  '가견적/일정',
                    'accept' => 'y',
                ],
                20=> [
                    'name' =>  'BT의뢰',
                    'accept' => 'y',
                ],
                30=> [
                    'name' =>  '생지확보',
                    'accept' => 'y',
                ],
                40=> [
                    'name' =>  '구매 확정서',
                    'accept' => 'y',
                ],
            ],
        ],
        
        //최종확정
        'ORDER1' => [
            'typeName' => '최종확정',
            'typeDoc' => [
                10=> [
                    'name' =>  '판매구매확정서',
                    'accept' => 'y',
                ],
            ],
        ],

        //사양서
        'ORDER2' => [
            'typeName' => '사양서',
            'typeDoc' => [
                10=> [ //메일, 컨펌
                    'name' =>  '유니폼 디자인 가이드',
                    'customerLink' => 'guide_view.php',
                    'accept' => 'y',
                    'customerConfirm' => 'y',
                    'mailLink' => 'wcustomer/guide_view.php',
                    'mailTemplate' => 'work_guide.php',
                ],
            ]
        ],

        //작업지시서
        'ORDER3' => [
            'typeName' => '작업지시서',
            'typeDoc' => [
                10=> [
                    'name' =>  '작업지시서',
                    'accept' => 'y',
                ],
            ]
        ],
    ];


    //영업 문서
    const PRJ_DOC_SALES = [
        1=>'미팅보고서' // 고객,
        ,2=>'근무환경 보고서' // 첨부
        ,3=>'고객사 샘플리뷰서' // 고객(승)
        ,4=>'생산견적요청서' // 첨부
        ,5=>'견적서' // 고객
        ,6=>'발주 확정서' // 고객(승)
        ,7=>'폐쇄몰 준비자료'//고객(승)
        ,8=>'계약서' //첨부
    ];
    const PRJ_DOC_DESIGN = [
        1=>'디자인컨셉'
        ,2=>'포트폴리오' //고객(승)
        ,3=>'샘플 의뢰서'
        ,4=>'피팅 체크 리스트'
        ,5=>'사양서' //고객(승)
        ,6=>'작업지시서'
        ,7=>'원단확인요청서'
    ];
    const PRJ_DOC_QC = [
        1=>'원단확인요청서'
        ,2=>'가견적 요청서'
        ,3=>'샘플검사&고객수정요청사항'
        ,4=>'발주서'
        ,5=>'원부자재표'
        ,6=>'판매구매확정서'
        ,7=>'세탁테스트 이화학검사'
        ,8=>'QA검품결과서'
        ,9=>'납품 평가서'
        ,10=>'생산 사고 보고서'
        ,11=>'그레이딩 및 마카요청서'
    ];

    const PRJ_DOC = [
      'SALES' => SlProjectCodeMap::PRJ_DOC_SALES    //영업
      , 'DESIGN' => SlProjectCodeMap::PRJ_DOC_DESIGN  //디자인
      , 'QC' => SlProjectCodeMap::PRJ_DOC_QC  //생산관리
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


