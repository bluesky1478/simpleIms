<?php
namespace Component\Work\Code;

use SiteLabUtil\SlCommonUtil;

/**
 * Sitelab Code Key List
 * Class SlCode
 * @package SlComponent\Util
 */
class DocumentDesignCodeMap {

    const DOC_DESIGN = [
        //디자인 컨셉
        1 => [
            'companySno' => '',                             //업체명
            'designManagerSno' => '',                    //디자인 담당자
            'loadDocumentVersion' => '',
            'designDirection' => [],                           //디자인 방향
            'fileEtc' => []                           //첨부파일
        ],
        //포트폴리오
        2 => [
            'companySno' => '',                             //업체명
            'designManagerSno' => '',                    //디자인 담당자
            'loadDocumentVersion' => '',
            'feedbackDt' => '', //피드백 요청일
            'feedbackStatus' => 0, //피드백 상태
            'selectedStyleNo' => 0,
            'sendEmail' => '',
            'sendDt' => '',
            'portData' => []
        ],
        //샘플의뢰서
        3 => [
            'companySno' => '',                             //업체명
            'designManagerSno' => '',                    //디자인 담당자
            'loadDocumentVersion' => '',
            'sampleData' => []
        ],
        //피팅 체크 리스트
        4 => [
            'companySno' => '',                             //업체명
            'designManagerSno' => '',                    //디자인 담당자
            'loadDocumentVersion' => '',
            'sampleData' => []
        ],
        //작업지시서
        5 => [
            'companySno' => '',                             //업체명
            'designManagerSno' => '',                    //디자인 담당자
            'loadDocumentVersion' => '',
            'sampleData' => [],
        ],

        //유니폼 디자인 가이드
        6 => [
            'companySno' => '',                             //업체명
            'designManagerSno' => '',                    //디자인 담당자
            'loadDocumentVersion' => '',
            'feedbackDt' => '',
            'feedbackStatus' => 0, //피드백 상태
            'fileManual' => [],
            'step1' => '',
            'step2' => '',
            'step3' => '',
            'step4' => '',
            'contractFl' => '유', //무
            'contractPub' => '',
            'payCondition' => '',
            'payMethod' => '',
            'deliveryMethod' => '',
            'deliveryAddress' => '',
            'deliveryManager' => '',
            'deliveryPhone' => '',
            'sendEmail' => '',
            'sendDt' => '',
            'sampleData' => [],
            'commentList' => [],
        ],
    ];

    /**
     * 디자인 포트 폴리오 데이터
     */
/*    const DESIGN_PORT_DATA = [
        'styleName' => '',
        'styleType' => '',
        'imageThumbnailFile' => '',
        'imageThumbnail' => '',
        'imageDetailFile' => '',
        'imageDetail' => '',    
        'status' => 0,
        'showCommentReg' => 0,
        'comment' => '',
        'commentList' => [],
    ];*/
    
    /**
     * 디자인-샘플 포맷
     */
/*    const DESIGN_SAMPLE = [
        'styleType' => '', //스타일타입 (0. 춘추점퍼, 1. 동계점퍼, 2. 조끼)
        'styleName' => '', //스타일명
        'serial' => '', //시리얼
        'sizeDisplay' => '', //사이즈(표기기준)
        'fit' => '', //핏
        'fabricInfo' => '', //원단정보 ( 유니폼 가이드에서 사용 )
        'season' => '', //시즌
        'productName' => '', //제품명
        'produceType' => '샘플', //생산구분
        'produceCountry' => '', //생산국가
        'sampleFactorySno' => '', //샘플공장(샘플처)
        'requestDt' => '', //의뢰일
        'completeDt' => '', //완료 요청일
        'receiveMethod' => '', //수령방법
        //사이즈스팩
        'specType' => '', //구분
        'sampleSize' => '', //샘플제작사이즈
        'sampleItem' => [
            //초기화시 등록
        ],
        'partInfo' => [], //원자재
        'subPartInfo' => [], //부자재
        'etc' => '',
        'fileSample' => [],
        'fileThumbnail' => [],
        'fileEtc' => [],
        'fileSamplePreview' => [],
        'fileThumbnailPreview' => [],
        //여기서 부터 작업 지시서
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
    ];*/

    /**
     * 디자인-체크 포맷
     */
    const DESIGN_CHECK = [
        'styleType' => '', //스타일타입 (0. 춘추점퍼, 1. 동계점퍼, 2. 조끼)
        'styleName' => '', //스타일명
        'serial' => '', //시리얼
        'sizeDisplay' => '', //사이즈(표기기준)
        'fit' => '', //핏
        'sampleFactorySno' => '', //샘플공장(샘플처)
        'requestDt' => '', //의뢰일
        'completeDt' => '', //완료 요청일
        'receiveMethod' => '', //수령방법
        //사이즈스팩
        'specType' => '', //구분
        'sampleSize' => '', //샘플제작사이즈
        'sampleItem' => [
            //초기화시 등록
        ],
        'checkItem' => [
            0 => [
                'check1' => '', //체크사항
                'check2' => '',   //문제사항
                'check3' => '',   //비고
            ]
        ],
        'etc' => '',
        'fileSample' => [],
        'fileEtc' => [],
        'fileSamplePreview' => [],
    ];

    /**
     * 디자인 - 사이즈 스펙
     */
/*    const DESIGN_DEFAULT_SPEC = [
        'specItemName'=> '',
        'completeSpec'=> 0,
        'guideSpec'=> 0,
        'specDiff'=> 0,
        'specUnit'=> 'cm',
        'specDescription'=> '',
        'isCustomerGuideFl'=> 'y',
    ];*/

    /**
     * 유니폼 디자인 가이드 상품
     */
    const DESIGN_PRODUCT = [
        'style' => '',
        'styleName' => '',
        'serial' => '',
        'size' => '',
        'fit' => '',
        'fabricInfo' => '',
        'fileImage' => [],
        'option' => [],
    ];

    /**
     * 유니폼 디자인 가이드 상품 옵션
     */
    const DESIGN_PRODUCT_OPTION = [
        'optionName' => '',
        'optionType' => [
            0 => [
                'optionTypeName' => '', //타입
                'orderCnt' => '', //수량
                'inputCnt' => '',
            ]
        ],
        'checkList' => [],
    ];


}


