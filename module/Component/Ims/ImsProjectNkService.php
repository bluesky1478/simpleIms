<?php
namespace Component\Ims;

use App;

use Component\Ims\ImsCodeMap;
use Component\Database\DBTableField;
use SiteLabUtil\SlCommonUtil;
use SlComponent\Database\DBUtil2;
use SlComponent\Database\SearchVo;
use SlComponent\Util\SlLoader;

use Controller\Admin\Ims\ImsPsNkTrait;
use Component\Ims\ImsServiceSortNkTrait;

class ImsProjectNkService {
    use ImsPsNkTrait;
    use ImsServiceSortNkTrait;

    private $dpData;
    public function __construct() {
        $this->dpData = [
            ['type' => 'c', 'col' => 5, 'class' => '', 'name' => 'customerTypeHan', 'title' => '고객구분', ],
        ];
    }
    public function getDisplay(){ return $this->dpData; }

    public function getListBasicFormToSalesPlanPage($params) {
        $aReturn['json_default_form'] = $this->getJsonDefaultForm(ImsDBName::PROJECT_SALES_PLAN_FILL);
        $aReturn['guide_list'] = $this->getListBasicFormProposalGuide()['list'];
        $aReturn['basic_form_list'] = $this->getListBasicFormSalesPlan();

        $aReturn['info'] = ['sno'=>0, 'jsonProposalGuide'=>[], 'proposalGuideDesc'=>''];
        $aReturn['fill_detail'] = []; //$aReturn['fill_detail'][그룹text][문항text][필드text] = 값
        $aReturn['fill_json'] = []; //$aReturn['fill_json'][그룹text] = json_decode(값)

        //프로젝트sno 로 영업기획서 작성한 내용 가져오기
        $iProjectSno = (int)$params['projectSno'];
        if ($iProjectSno > 0) {
            $aFillInfo = DBUtil2::getOne(ImsDBName::PROJECT_SALES_PLAN_FILL, 'projectSno', $iProjectSno);
            if (isset($aFillInfo['sno'])) {
                $aReturn['info']['sno'] = $aFillInfo['sno'];
                $aReturn['info']['jsonProposalGuide'] = json_decode($aFillInfo['jsonProposalGuide'], true);
                $aReturn['info']['proposalGuideDesc'] = $aFillInfo['proposalGuideDesc'];

                $aFillDetailList = DBUtil2::getListBySearchVo(ImsDBName::PROJECT_SALES_PLAN_FILL_DETAIL, new SearchVo('salesPlanFillSno=?', $aFillInfo['sno']));
                foreach ($aFillDetailList as $val) {
                    $aReturn['fill_detail'][$val['textGroup']][$val['textQuestion']][$val['textCell']] = $val['cellValue'];
                }
                $aFillJsonList = DBUtil2::getListBySearchVo(ImsDBName::PROJECT_SALES_PLAN_FILL_JSON, new SearchVo('salesPlanFillSno=?', $aFillInfo['sno']));
                foreach ($aFillJsonList as $val) {
                    $aReturn['fill_json'][$val['textGroup']] = json_decode($val['jsonValue'], true);
                }
            }
        }

        return $aReturn;
    }

    //제안서가이드리스트 가져오기
    public function getListBasicFormProposalGuide($params) {
        $oSV = new SearchVo();
        $oSV->setOrder('sortNum asc');
        $this->refineCommonCondition($params, $oSV);

        $tableInfo=[
            'a' => ['data' => [ ImsDBName::BASIC_PROPOSAL_GUIDE ], 'field' => ["a.*"]],
        ];
        if (!isset($params['page']) || (int)$params['page'] == 0) $params['page'] = gd_isset($params['condition']['page'], 1);
        if (!isset($params['pageNum']) || (int)$params['pageNum'] == 0) $params['pageNum'] = gd_isset($params['condition']['pageNum'], 15000);
        $allData = DBUtil2::getComplexListWithPaging(DBUtil2::setTableInfo($tableInfo,false), $oSV, $params, false, true);
        $aList = [];
        foreach ($allData['listData'] as $val) {
            $aTmp = ['sno'=>$val['sno'], 'guideName'=>$val['guideName'], 'guideFileUrl'=>$val['guideFileUrl'], 'guideDesc'=>$val['guideDesc']];
            $aList[] = $aTmp;
        }

        return [
            'pageEx' => $allData['pageData']->getPage('#'),
            'page' => $allData['pageData'],
            'list' => $aList,
            'fieldData' => []
        ];
    }
    //영업기획양식 가져오기
    //namkuuuuu 후순위작업. 영업기획서양식이 여러개 필요하다면 DB테이블(sl_imsBasicSalesPlan)에 아래 배열을 json_encode해서 insert
    public function getListBasicFormSalesPlan() {
        //grpType : normal or json
        //cellType : fixed, check, radio, text, date

        //namku(chkd) $aReturn[pk]['jsonBasicFormContents'][x]['questions'][y]['cells'][z]['cellValue'] : cellType이 text일때는 placeholder
        //namku(chkd) pk==양식pk, x==그룹key, y==문항key, z==필드(cell)key

        $aReturn = [
            1 => [
                'basicFormName' => '영업기획서 기본양식', 'jsonBasicFormContents' =>
                    [
                        [
                            'colNumber'=>'12', 'grpType'=>'normal', 'grpTitle'=>'입찰 정보 (현재 상황)', 'questions'=>
                            [
                                [
                                    'cells'=>
                                        [
                                            ['cellType'=>'fixed', 'cellTitle'=>'구분', 'cellValue'=>'변경 사유', 'options'=>[]],
                                            ['cellType'=>'check', 'cellTitle'=>'내용', 'cellValue'=>'', 'options'=>['디자인 및 브랜드 이미지 개선', '장기간 착용으로 교체 시기', '원가 절감', '현장 불편 민원 접수', '재입찰 주기']],
                                            ['cellType'=>'fixed', 'cellTitle'=>'기타 (직접 입력)', 'cellValue'=>'', 'options'=>[]],
                                        ]
                                ], //문항 1개
                                [
                                    'cells'=>
                                        [
                                            ['cellType'=>'fixed', 'cellTitle'=>'구분', 'cellValue'=>'현재 계약 업체', 'options'=>[]],
                                            ['cellType'=>'text', 'cellTitle'=>'내용', 'cellValue'=>'(예: 코오롱)', 'options'=>[]],
                                            ['cellType'=>'fixed', 'cellTitle'=>'기타 (직접 입력)', 'cellValue'=>'', 'options'=>[]],
                                        ]
                                ], //문항 1개
                                [
                                    'cells'=>[
                                        ['cellType'=>'fixed', 'cellTitle'=>'구분', 'cellValue'=>'현재 계약 업체 평가 (장점)', 'options'=>[]],
                                        ['cellType'=>'check', 'cellTitle'=>'내용', 'cellValue'=>'', 'options'=>['가격 경쟁력 우수', '납기 준수율 우수', '품질 우수', '응대 및 커뮤니케이션 원활', '긴급 대응 가능', '기타 (직접 입력)']],
                                        ['cellType'=>'text', 'cellTitle'=>'기타 (직접 입력)', 'cellValue'=>'기타 (직접 입력)', 'options'=>[]],
                                    ]
                                ], //문항 1개
                                [
                                    'cells'=>[
                                        ['cellType'=>'fixed', 'cellTitle'=>'구분', 'cellValue'=>'현재 계약 업체 평가 (단점)', 'options'=>[]],
                                        ['cellType'=>'check', 'cellTitle'=>'내용', 'cellValue'=>'', 'options'=>['가격 변동성', '납기 지연', '품질 이슈', '응대 지연', '긴급 대응 미흡', '기타 (직접 입력)']],
                                        ['cellType'=>'text', 'cellTitle'=>'기타 (직접 입력)', 'cellValue'=>'기타 (직접 입력)', 'options'=>[]],
                                    ]
                                ],
                            ]
                        ], //그룹 1개
                        [
                            'colNumber'=>'12', 'grpType'=>'normal', 'grpTitle'=>'입찰 정보', 'questions'=>
                            [
                                [
                                    'cells'=>
                                        [
                                            ['cellType'=>'fixed', 'cellTitle'=>'구분', 'cellValue'=>'입찰 설명회 예정일', 'options'=>[]],
                                            ['cellType'=>'prjDate', 'cellTitle'=>'내용', 'cellValue'=>'', 'options'=>[], 'model'=>'exMeetingReady'],
                                        ]
                                ],
                                [
                                    'cells'=>
                                        [
                                            ['cellType'=>'fixed', 'cellTitle'=>'구분', 'cellValue'=>'입찰 예상 시기', 'options'=>[]],
                                            ['cellType'=>'prjDate', 'cellTitle'=>'내용', 'cellValue'=>'', 'options'=>[], 'model'=>'exMeeting'],
                                        ]
                                ],
                                [
                                    'cells'=>
                                        [
                                            ['cellType'=>'fixed', 'cellTitle'=>'구분', 'cellValue'=>'참여 업체 (경쟁사)', 'options'=>[]],
                                            ['cellType'=>'text', 'cellTitle'=>'내용', 'cellValue'=>'(예: 5개 업체: 코오롱, 반도, GNF)', 'options'=>[]],
                                        ]
                                ],
                            ]
                        ], //그룹 1개
                        [
                            'colNumber'=>'12', 'grpType'=>'normal', 'grpTitle'=>'제안/품평 방식 (절차)', 'questions'=>
                            [
                                [
                                    'cells'=>
                                        [
                                            ['cellType'=>'fixed', 'cellTitle'=>'구분', 'cellValue'=>'제안서 + 샘플 동시 제출 (동시 진행)', 'options'=>[]],
                                            ['cellType'=>'check', 'cellTitle'=>'선택', 'cellValue'=>'', 'options'=>['선택']],
                                            ['cellType'=>'text', 'cellTitle'=>'비고', 'cellValue'=>'', 'options'=>[]],
                                        ]
                                ],
                                [
                                    'cells'=>
                                        [
                                            ['cellType'=>'fixed', 'cellTitle'=>'구분', 'cellValue'=>'1차 제안서 제출 → 2차 샘플 제출 (1차 합격 업체)', 'options'=>[]],
                                            ['cellType'=>'check', 'cellTitle'=>'선택', 'cellValue'=>'', 'options'=>['선택']],
                                            ['cellType'=>'text', 'cellTitle'=>'비고', 'cellValue'=>'', 'options'=>[]],
                                        ]
                                ],
                                [
                                    'cells'=>
                                        [
                                            ['cellType'=>'fixed', 'cellTitle'=>'구분', 'cellValue'=>'1차 제안 PT → 2차 샘플 제출 (1차 합격 업체)', 'options'=>[]],
                                            ['cellType'=>'check', 'cellTitle'=>'선택', 'cellValue'=>'', 'options'=>['선택']],
                                            ['cellType'=>'text', 'cellTitle'=>'비고', 'cellValue'=>'', 'options'=>[]],
                                        ]
                                ],
                                [
                                    'cells'=>
                                        [
                                            ['cellType'=>'fixed', 'cellTitle'=>'구분', 'cellValue'=>'1차 제안 PT + 샘플 전시 (동시 진행)', 'options'=>[]],
                                            ['cellType'=>'check', 'cellTitle'=>'선택', 'cellValue'=>'', 'options'=>['선택']],
                                            ['cellType'=>'text', 'cellTitle'=>'비고', 'cellValue'=>'', 'options'=>[]],
                                        ]
                                ],
                                [
                                    'cells'=>
                                        [
                                            ['cellType'=>'fixed', 'cellTitle'=>'구분', 'cellValue'=>'1차 샘플 제출 → 2차 품평회 진행 (1차 합격 업체)', 'options'=>[]],
                                            ['cellType'=>'check', 'cellTitle'=>'선택', 'cellValue'=>'', 'options'=>['선택']],
                                            ['cellType'=>'text', 'cellTitle'=>'비고', 'cellValue'=>'', 'options'=>[]],
                                        ]
                                ],
                            ]
                        ], //그룹 1개
                        [
                            'colNumber'=>'12', 'grpType'=>'normal', 'grpTitle'=>'업체 선정 기준 (평가 항목)', 'questions'=>
                            [
                                [
                                    'cells'=>
                                        [
                                            ['cellType'=>'fixed', 'cellTitle'=>'평가 항목', 'cellValue'=>'디자인 (자체 평가)', 'options'=>[]],
                                            ['cellType'=>'fixed', 'cellTitle'=>'유형', 'cellValue'=>'정성', 'options'=>[]],
                                            ['cellType'=>'check', 'cellTitle'=>'선택', 'cellValue'=>'', 'options'=>['선택']],
                                            ['cellType'=>'text', 'cellTitle'=>'배점(%)', 'cellValue'=>'', 'options'=>[]],
                                            ['cellType'=>'radio', 'cellTitle'=>'상태', 'cellValue'=>'', 'options'=>['확정', '추정', '불명확']],
                                            ['cellType'=>'text', 'cellTitle'=>'비고', 'cellValue'=>'', 'options'=>[]],
                                        ]
                                ],
                                [
                                    'cells'=>
                                        [
                                            ['cellType'=>'fixed', 'cellTitle'=>'평가 항목', 'cellValue'=>'디자인 (현장 품평)', 'options'=>[]],
                                            ['cellType'=>'fixed', 'cellTitle'=>'유형', 'cellValue'=>'정성', 'options'=>[]],
                                            ['cellType'=>'check', 'cellTitle'=>'선택', 'cellValue'=>'', 'options'=>['선택']],
                                            ['cellType'=>'text', 'cellTitle'=>'배점(%)', 'cellValue'=>'', 'options'=>[]],
                                            ['cellType'=>'radio', 'cellTitle'=>'상태', 'cellValue'=>'', 'options'=>['확정', '추정', '불명확']],
                                            ['cellType'=>'text', 'cellTitle'=>'비고', 'cellValue'=>'', 'options'=>[]],
                                        ]
                                ],
                                [
                                    'cells'=>
                                        [
                                            ['cellType'=>'fixed', 'cellTitle'=>'평가 항목', 'cellValue'=>'품질', 'options'=>[]],
                                            ['cellType'=>'fixed', 'cellTitle'=>'유형', 'cellValue'=>'정성', 'options'=>[]],
                                            ['cellType'=>'check', 'cellTitle'=>'선택', 'cellValue'=>'', 'options'=>['선택']],
                                            ['cellType'=>'text', 'cellTitle'=>'배점(%)', 'cellValue'=>'', 'options'=>[]],
                                            ['cellType'=>'radio', 'cellTitle'=>'상태', 'cellValue'=>'', 'options'=>['확정', '추정', '불명확']],
                                            ['cellType'=>'text', 'cellTitle'=>'비고', 'cellValue'=>'', 'options'=>[]],
                                        ]
                                ],
                                [
                                    'cells'=>
                                        [
                                            ['cellType'=>'fixed', 'cellTitle'=>'평가 항목', 'cellValue'=>'단가', 'options'=>[]],
                                            ['cellType'=>'fixed', 'cellTitle'=>'유형', 'cellValue'=>'정성', 'options'=>[]],
                                            ['cellType'=>'check', 'cellTitle'=>'선택', 'cellValue'=>'', 'options'=>['선택']],
                                            ['cellType'=>'text', 'cellTitle'=>'배점(%)', 'cellValue'=>'', 'options'=>[]],
                                            ['cellType'=>'radio', 'cellTitle'=>'상태', 'cellValue'=>'', 'options'=>['확정', '추정', '불명확']],
                                            ['cellType'=>'text', 'cellTitle'=>'비고', 'cellValue'=>'', 'options'=>[]],
                                        ]
                                ],
                                [
                                    'cells'=>
                                        [
                                            ['cellType'=>'fixed', 'cellTitle'=>'평가 항목', 'cellValue'=>'납기', 'options'=>[]],
                                            ['cellType'=>'fixed', 'cellTitle'=>'유형', 'cellValue'=>'정성', 'options'=>[]],
                                            ['cellType'=>'check', 'cellTitle'=>'선택', 'cellValue'=>'', 'options'=>['선택']],
                                            ['cellType'=>'text', 'cellTitle'=>'배점(%)', 'cellValue'=>'', 'options'=>[]],
                                            ['cellType'=>'radio', 'cellTitle'=>'상태', 'cellValue'=>'', 'options'=>['확정', '추정', '불명확']],
                                            ['cellType'=>'text', 'cellTitle'=>'비고', 'cellValue'=>'', 'options'=>[]],
                                        ]
                                ],
                            ]
                        ], //그룹 1개
                        [
                            'colNumber'=>'12', 'grpType'=>'json', 'grpTitle'=>'의사 결정 라인', 'questions'=>
                            [
                                [
                                    'cells'=>
                                        [
                                            ['cellType'=>'text', 'cellTitle'=>'단계', 'cellValue'=>'jsonFld1', 'options'=>[]],
                                            ['cellType'=>'text', 'cellTitle'=>'담당자', 'cellValue'=>'jsonFld2', 'options'=>[]],
                                            ['cellType'=>'text', 'cellTitle'=>'부서', 'cellValue'=>'jsonFld3', 'options'=>[]],
                                            ['cellType'=>'text', 'cellTitle'=>'직책', 'cellValue'=>'jsonFld4', 'options'=>[]],
                                            ['cellType'=>'text', 'cellTitle'=>'성향', 'cellValue'=>'jsonFld5', 'options'=>[]],
                                            ['cellType'=>'text', 'cellTitle'=>'영향도', 'cellValue'=>'jsonFld6', 'options'=>[]],
                                            ['cellType'=>'text', 'cellTitle'=>'역할', 'cellValue'=>'jsonFld7', 'options'=>[]],
                                        ]
                                ],
                            ]
                        ], //그룹 1개
                        [
                            'colNumber'=>'6', 'grpType'=>'normal', 'grpTitle'=>'폐쇄몰 서비스', 'questions'=>
                            [
                                [
                                    'cells'=>
                                        [
                                            ['cellType'=>'fixed', 'cellTitle'=>'구분', 'cellValue'=>'관심 수준', 'options'=>[]],
                                            ['cellType'=>'radio', 'cellTitle'=>'내용', 'cellValue'=>'', 'options'=>['매우 관심', '보통', '낮음', '무관심']],
                                        ]
                                ],
                                [
                                    'cells'=>
                                        [
                                            ['cellType'=>'fixed', 'cellTitle'=>'구분', 'cellValue'=>'코멘트', 'options'=>[]],
                                            ['cellType'=>'text', 'cellTitle'=>'내용', 'cellValue'=>'(예: 재고 관리 편리할 듯 / 불필요)', 'options'=>[]],
                                        ]
                                ],
                            ]
                        ], //그룹 1개
                        [
                            'colNumber'=>'6', 'grpType'=>'normal', 'grpTitle'=>'현장 리서치', 'questions'=>
                            [
                                [
                                    'cells'=>
                                        [
                                            ['cellType'=>'fixed', 'cellTitle'=>'구분', 'cellValue'=>'관심 수준', 'options'=>[]],
                                            ['cellType'=>'radio', 'cellTitle'=>'내용', 'cellValue'=>'', 'options'=>['매우 관심', '보통', '낮음', '무관심']],
                                        ]
                                ],
                                [
                                    'cells'=>
                                        [
                                            ['cellType'=>'fixed', 'cellTitle'=>'구분', 'cellValue'=>'코멘트', 'options'=>[]],
                                            ['cellType'=>'text', 'cellTitle'=>'내용', 'cellValue'=>'(예: 재고 관리 편리할 듯 / 불필요)', 'options'=>[]],
                                        ]
                                ],
                            ]
                        ], //그룹 1개
                        [
                            'colNumber'=>'6', 'grpType'=>'normal', 'grpTitle'=>'분류 패킹 진행 여부', 'questions'=>
                            [
                                [
                                    'cells'=>
                                        [
                                            ['cellType'=>'fixed', 'cellTitle'=>'항목', 'cellValue'=>'분류 패킹 진행 여부', 'options'=>[]],
                                            ['cellType'=>'radio', 'cellTitle'=>'내용', 'cellValue'=>'', 'options'=>['진행', '미진행']],
                                        ]
                                ],
                                [
                                    'cells'=>
                                        [
                                            ['cellType'=>'fixed', 'cellTitle'=>'항목', 'cellValue'=>'지점별 배송비 처리 여부', 'options'=>[]],
                                            ['cellType'=>'radio', 'cellTitle'=>'내용', 'cellValue'=>'', 'options'=>['옷값에 포함', '별도 청구']],
                                        ]
                                ],
                                [
                                    'cells'=>
                                        [
                                            ['cellType'=>'fixed', 'cellTitle'=>'항목', 'cellValue'=>'코멘트', 'options'=>[]],
                                            ['cellType'=>'text', 'cellTitle'=>'내용', 'cellValue'=>'', 'options'=>[]],
                                        ]
                                ],
                            ]
                        ], //그룹 1개
                        [
                            'colNumber'=>'6', 'grpType'=>'normal', 'grpTitle'=>'재고 관리 여부', 'questions'=>
                            [
                                [
                                    'cells'=>
                                        [
                                            ['cellType'=>'fixed', 'cellTitle'=>'항목', 'cellValue'=>'재고 관리 진행 여부', 'options'=>[]],
                                            ['cellType'=>'radio', 'cellTitle'=>'내용', 'cellValue'=>'', 'options'=>['진행', '미진행']],
                                        ]
                                ],
                                [
                                    'cells'=>
                                        [
                                            ['cellType'=>'fixed', 'cellTitle'=>'항목', 'cellValue'=>'재고 관리 비용', 'options'=>[]],
                                            ['cellType'=>'radio', 'cellTitle'=>'내용', 'cellValue'=>'', 'options'=>['옷값에 포함', '별도 청구']],
                                        ]
                                ],
                                [
                                    'cells'=>
                                        [
                                            ['cellType'=>'fixed', 'cellTitle'=>'항목', 'cellValue'=>'코멘트', 'options'=>[]],
                                            ['cellType'=>'text', 'cellTitle'=>'내용', 'cellValue'=>'', 'options'=>[]],
                                        ]
                                ],
                            ]
                        ], //그룹 1개
                        [
                            'colNumber'=>'6', 'grpType'=>'normal', 'grpTitle'=>'추가 정보', 'questions'=>
                            [
                                [
                                    'cells'=>
                                        [
                                            ['cellType'=>'fixed', 'cellTitle'=>'항목', 'cellValue'=>'로고 방식', 'options'=>[]],
                                            ['cellType'=>'radio', 'cellTitle'=>'내용', 'cellValue'=>'', 'options'=>['전체 공통 로고', '일부 수량 관계사/협력서 별도 로고']],
                                        ]
                                ],
                                [
                                    'cells'=>
                                        [
                                            ['cellType'=>'fixed', 'cellTitle'=>'항목', 'cellValue'=>'명찰 진행 유무', 'options'=>[]],
                                            ['cellType'=>'radio', 'cellTitle'=>'내용', 'cellValue'=>'', 'options'=>['사면 봉제 방식', '벨크로 타입', '명찰 걸이(핀셋 타입)', '명찰 걸이(자석 타입)', '미진행']],
                                        ]
                                ],
                                [
                                    'cells'=>
                                        [
                                            ['cellType'=>'fixed', 'cellTitle'=>'항목', 'cellValue'=>'단가 민감도', 'options'=>[]],
                                            ['cellType'=>'radio', 'cellTitle'=>'내용', 'cellValue'=>'', 'options'=>['상 (단가 인하 필요)', '중 (상승 불가)', '하 (옵션 제안 가능)']],
                                        ]
                                ],
                                [
                                    'cells'=>
                                        [
                                            ['cellType'=>'fixed', 'cellTitle'=>'항목', 'cellValue'=>'납기 민감도', 'options'=>[]],
                                            ['cellType'=>'radio', 'cellTitle'=>'내용', 'cellValue'=>'', 'options'=>['상(납기 준수 필수, 일정 변경 불가)', '중 (일부 조정 가능)', '하 (납기 유연, 일정 제안 가능)']],
                                        ]
                                ],
                                [
                                    'cells'=>
                                        [
                                            ['cellType'=>'fixed', 'cellTitle'=>'항목', 'cellValue'=>'색상 민감도', 'options'=>[]],
                                            ['cellType'=>'radio', 'cellTitle'=>'내용', 'cellValue'=>'', 'options'=>['상(지정색 대비 90% 이상 근접 요구)', '중 (탕차이 ±80% 수용 가능)', '하 (색상 변경 또는 유사색 제안 가능)']],
                                        ]
                                ],
                                [
                                    'cells'=>
                                        [
                                            ['cellType'=>'fixed', 'cellTitle'=>'항목', 'cellValue'=>'품질 민감도', 'options'=>[]],
                                            ['cellType'=>'radio', 'cellTitle'=>'내용', 'cellValue'=>'', 'options'=>['상 (최상급 품질 수준 요구)', '중 (표준 품질 이상 유지 요청)', '하 (표준 이상이면 수용 가능)']],
                                        ]
                                ],
                            ]
                        ], //그룹 1개
                        [
                            'colNumber'=>'6', 'grpType'=>'normal', 'grpTitle'=>'근무 환경 / 세탁 환경', 'questions'=>
                            [
                                [
                                    'cells'=>
                                        [
                                            ['cellType'=>'fixed', 'cellTitle'=>'항목', 'cellValue'=>'근무 형태', 'options'=>[]],
                                            ['cellType'=>'radio', 'cellTitle'=>'내용', 'cellValue'=>'', 'options'=>['서비스 (고객 접객)', '제조/생산직', '믈류/현장직', '건설/현장직', '사무/관리직']],
                                        ]
                                ],
                                [
                                    'cells'=>
                                        [
                                            ['cellType'=>'fixed', 'cellTitle'=>'항목', 'cellValue'=>'근무 강도', 'options'=>[]],
                                            ['cellType'=>'radio', 'cellTitle'=>'내용', 'cellValue'=>'', 'options'=>['상 (지속적인 힘 사용 / 중량물 취급)', '중 (반복적 움직임 / 일정한 근력 요구)', '하(정적인 자세 / 단순 검사 관리)']],
                                        ]
                                ],
                                [
                                    'cells'=>
                                        [
                                            ['cellType'=>'fixed', 'cellTitle'=>'항목', 'cellValue'=>'원단 적용 특이 사항', 'options'=>[]],
                                            ['cellType'=>'radio', 'cellTitle'=>'내용', 'cellValue'=>'', 'options'=>['화학 약품 취급', '정전기 발생', '기름/먼지/분진 접촉', '용접/절단 등 고온 작업', '날가로운 자재 노출']],
                                        ]
                                ],
                                [
                                    'cells'=>
                                        [
                                            ['cellType'=>'fixed', 'cellTitle'=>'항목', 'cellValue'=>'평균 연령대 / 남녀 비율', 'options'=>[]],
                                            ['cellType'=>'text', 'cellTitle'=>'내용', 'cellValue'=>'(예: 20~30대: 30% / 30~40대: 50% / 50~60대: 10%) (예: 남자: 70% / 여자: 30%)', 'options'=>[]],
                                        ]
                                ],
                                [
                                    'cells'=>
                                        [
                                            ['cellType'=>'fixed', 'cellTitle'=>'항목', 'cellValue'=>'세탁 방법', 'options'=>[]],
                                            ['cellType'=>'radio', 'cellTitle'=>'내용', 'cellValue'=>'', 'options'=>['세탁 전문 업체', '회사 내 공용 세탁기 이용', '개인 세탁']],
                                        ]
                                ],
                                [
                                    'cells'=>
                                        [
                                            ['cellType'=>'fixed', 'cellTitle'=>'항목', 'cellValue'=>'세탁 조건', 'options'=>[]],
                                            ['cellType'=>'text', 'cellTitle'=>'내용', 'cellValue'=>'(예: 세탁 온도 60도 / 세탁 시간 40분 / 세탁 세제: 퍼실)', 'options'=>[]],
                                        ]
                                ],
                                [
                                    'cells'=>
                                        [
                                            ['cellType'=>'fixed', 'cellTitle'=>'항목', 'cellValue'=>'건조 조건', 'options'=>[]],
                                            ['cellType'=>'text', 'cellTitle'=>'내용', 'cellValue'=>'(예: 건조 온도 60도 / 건조 시간 40분)', 'options'=>[]],
                                        ]
                                ],

                            ]
                        ], //그룹 1개
                    ]
            ],
        ];


        return $aReturn;
    }


    public function setProjectSalesPlanFill($params) {
        //테이블정보 가져와서 json컬럼 고르기 -> json컬럼은 json_encode 해주거나 [] 값으로 넣어주기
        $aTmpFlds = DBTableField::callTableFunction(ImsDBName::PROJECT_SALES_PLAN_FILL);
        $aJsonFlds = [];
        foreach ($aTmpFlds as $val) {
            if (isset($val['json']) && $val['json'] === true) $aJsonFlds[] = $val['val'];
        }
        if (count($aJsonFlds) > 0) {
            foreach ($aJsonFlds as $val) {
                if (isset($params['data'][$val]) && is_array($params['data'][$val]) && count($params['data'][$val]) > 0) $params['data'][$val] = json_encode($params['data'][$val]);
                else $params['data'][$val] = '[]';
            }
        }
        $aTmpFlds = DBTableField::callTableFunction(ImsDBName::PROJECT_SALES_PLAN_FILL_DETAIL);
        $aJsonFlds = [];
        foreach ($aTmpFlds as $val) {
            if (isset($val['json']) && $val['json'] === true) $aJsonFlds[] = $val['val'];
        }
        if (count($aJsonFlds) > 0) {
            foreach ($aJsonFlds as $val) {
                foreach ($params['detail'] as $key2 => $val2) {
                    if (isset($params['detail'][$key2][$val]) && is_array($params['detail'][$key2][$val]) && count($params['detail'][$key2][$val]) > 0) $params['detail'][$key2][$val] = json_encode($params['detail'][$key2][$val]);
                    else $params['detail'][$key2][$val] = '[]';
                }
            }
        }
        $aTmpFlds = DBTableField::callTableFunction(ImsDBName::PROJECT_SALES_PLAN_FILL_JSON);
        $aJsonFlds = [];
        foreach ($aTmpFlds as $val) {
            if (isset($val['json']) && $val['json'] === true) $aJsonFlds[] = $val['val'];
        }
        if (count($aJsonFlds) > 0) {
            foreach ($aJsonFlds as $val) {
                foreach ($params['json'] as $key2 => $val2) {
                    if (isset($params['json'][$key2][$val]) && is_array($params['json'][$key2][$val]) && count($params['json'][$key2][$val]) > 0) $params['json'][$key2][$val] = json_encode($params['json'][$key2][$val]);
                    else $params['json'][$key2][$val] = '[]';
                }
            }
        }

        $iSno = (int)$params['data']['sno'];
        unset($params['data']['sno']);
        $sCurrDt = date('Y-m-d H:i:s');
        $iRegManagerSno = \Session::get('manager.sno');
        if ($iSno === 0) {
            $params['data']['regManagerSno'] = $iRegManagerSno;
            $params['data']['regDt'] = $sCurrDt;
            $iSno = DBUtil2::insert(ImsDBName::PROJECT_SALES_PLAN_FILL, $params['data']);
        } else {
            $params['data']['modDt'] = $sCurrDt;
            DBUtil2::update(ImsDBName::PROJECT_SALES_PLAN_FILL, $params['data'], new SearchVo('sno=?', $iSno));

            DBUtil2::delete(ImsDBName::PROJECT_SALES_PLAN_FILL_DETAIL, new SearchVo('salesPlanFillSno=?', $iSno));
            DBUtil2::delete(ImsDBName::PROJECT_SALES_PLAN_FILL_JSON, new SearchVo('salesPlanFillSno=?', $iSno));
        }

        foreach ($params['detail'] as $val) {
            $val['salesPlanFillSno'] = $iSno;
            DBUtil2::insert(ImsDBName::PROJECT_SALES_PLAN_FILL_DETAIL, $val);
        }
        foreach ($params['json'] as $val) {
            $val['salesPlanFillSno'] = $iSno;
            DBUtil2::insert(ImsDBName::PROJECT_SALES_PLAN_FILL_JSON, $val);
        }

        return ['data'=>$iSno];
    }


    //납품검수(납품보고서) 가져오기
    public function getListStyleInspectDelivery($params) {
        $sTableNm = ImsDBName::PRODUCT_INSPECT_DELIVERY;
        $tableInfo=[
            'a' => ['data' => [ $sTableNm ], 'field' => ["a.*"]],
            'reg' => ['data' => [ DB_MANAGER, 'LEFT OUTER JOIN', 'a.regManagerSno = reg.sno' ], 'field' => ["if(reg.sno is null, '미선택', reg.managerNm) as regManagerName"]],
        ];
        if (!isset($params['upsertSnoGet'])) { //리스트를 return받으려는 경우
            //리스트에서 뿌려줄 필드리스트 정리
            $aFldList = $this->getDisplay();
        } else $aFldList = [];
        $aReturn = $this->fnRefineListUpsertForm($params, $sTableNm, $tableInfo, $aFldList, [], 'styleSno');
        //작지의 리비전리스트 가져오기
        if (isset($params['upsertSnoGet'])) { //등록 or 수정(==상세)
            $aReturn['info2'] = [];
            $aEWorkInfo = DBUtil2::getOne(ImsDBName::EWORK, 'styleSno', $params['upsertSnoGet']);
            if (isset($aEWorkInfo['revision'])) $aReturn['info2'] = json_decode($aEWorkInfo['revision'], true);
        }

        return $aReturn;
    }

}