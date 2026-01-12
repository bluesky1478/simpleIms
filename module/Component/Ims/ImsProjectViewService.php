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

namespace Component\Ims;

use Component\Erp\ErpCodeMap;
use Component\Ims\ImsCodeMap;
use Component\Work\DocumentCodeMap;
use Component\Work\WorkCodeMap;
use Controller\Admin\Ims\Step\ImsStepTrait;
use SiteLabUtil\SlCommonUtil;
use SlComponent\Util\SlCodeMap;
use SlComponent\Util\SlLoader;
use Request;
use SlComponent\Util\SlProjectCodeMap;

class ImsProjectViewService {

    use ImsStepTrait;

    /**
     * 프로젝트 뷰 상세 설정 값
     */
    public static function getProjectViewFieldData() {
        $dataList = [
            //근무 환경
            'area10'=>[
                'title' => '근무환경',
                'field' => [
                    [
                        ['key'=>'info001','type'=>'text', 'params'=>['model' => 'project.addedInfo'] ], //'고객사 근무 환경'
                    ],
                    [
                        ['key'=>'info002','type'=>'text', 'params'=>['model' => 'project.addedInfo']], //'착용자 연령/성별'
                    ],
                ]
            ],
            //고객사 샘플 정보
            'area20'=>[
                'title' => '고객사 샘플 정보',
                'field' => [
                    [
                        ['key'=>'info003','type'=>'radio','params'=>[
                            'model' => 'project.addedInfo',
                            'listCode' => 'ableType'
                        ]], //'샘플 확보'
                    ],
                    [
                        ['key'=>'info004','type'=>'radio','params'=>[
                            'model' => 'project.addedInfo',
                            'listCode' => 'existType'
                        ]], //'샘플 확보'
                    ],
                ]
            ],
            //기타 사항
            'area30'=>[
                'title' => '기타사항',
                'field' => [
                    [
                        ['key'=>'info005','type'=>'text', 'params'=>['model' => 'project.addedInfo'] ], //'발주 물량 변동'
                    ],
                    [
                        ['key'=>'info006','type'=>'text', 'params'=>['model' => 'project.addedInfo']], //'계약기간'
                    ],
                    [
                        ['key'=>'info007','type'=>'text', 'params'=>['model' => 'project.addedInfo']], //'선호컨셉'
                    ],
                    [
                        ['key'=>'info008','type'=>'text', 'params'=>['model' => 'project.addedInfo']], //'선호컬러'
                    ],
                ]
            ],
            //고객성향
            'area40'=>[
                'title' => '고객성향',
                'field' => [
                    [
                        ['key'=>'info009','type'=>'radio','params'=>[
                            'model' => 'project.addedInfo','listCode' => 'ratingType'
                        ]], //'색상'
                        ['key'=>'info010','type'=>'radio','params'=>[
                            'model' => 'project.addedInfo','listCode' => 'ratingType'
                        ]], //'품질'
                    ],
                    [
                        ['key'=>'info011','type'=>'radio','params'=>[
                            'model' => 'project.addedInfo','listCode' => 'ratingType'
                        ]], //'단가'
                        ['key'=>'info012','type'=>'radio','params'=>[
                            'model' => 'project.addedInfo','listCode' => 'ratingType'
                        ]], //'납기'
                    ],
                    [
                        ['key'=>'info013','type'=>'text', 'params'=>['model' => 'project.addedInfo']],
                        ['key'=>'info015','type'=>'radio','params'=>[
                            'model' => 'project.addedInfo','listCode' => 'ratingType'
                        ]], //'폐쇄몰 관심도'
                    ], //'이노버 제공 샘플 선호도'
                    [
                        ['key'=>'info014','type'=>'text', 'params'=>['model' => 'project.addedInfo']], //'고객 희망 기능'
                    ],
                ]
            ],
            //샘플 제작 정보
            'area50'=>[
                'title' => '샘플 제작 정보',
                'field' => [
                    [
                        ['key'=>'info016', 'key2'=>'info017','type'=>'complex1','params'=>[
                            'model' => 'project.addedInfo',
                            'keyDesc' => '유상 비용 (숫자만)',
                            'model2' => 'project.addedInfo',
                            'listCode' => 'existType2',
                        ]], //'샘플 비용 정보'
                    ],
                    [
                        ['key'=>'info018','type'=>'radio','params'=>[
                            'model' => 'project.addedInfo','listCode' => 'ratingType'
                        ]], //'샘플 결제 방법'
                    ],
                    [
                        ['key'=>'info019', 'key2'=>'info020','type'=>'complex2','params'=>[
                            'model' => 'project.addedInfo',
                            'model2' => 'project.addedInfo',
                        ]], //'샘플 제출 일시
                    ],
                    [
                        ['key'=>'info021', 'key2'=>'info022','type'=>'complex3','params'=>[
                            'model' => 'project.addedInfo',
                            'model2' => 'project.addedInfo',
                            'keyDesc' => '장소',
                            //'key2Desc' => '접수자 정보',
                        ]], //'샘플 제출 장소
                    ],
                ]
            ],
            'area60'=>[
                'title' => '마크사양/스케쥴 공유',
                'field' => [
                    [
                        ['key'=>'info023', 'key2'=>'info024','type'=>'complex4','params'=>[
                            'model' => 'project.addedInfo', 'model2' => 'project.addedInfo',
                            'listCode' => 'existType',
                        ]], //마크유무
                    ],
                    [
                        ['key'=>'info025','type'=>'radio','params'=>[
                            'model' => 'project.addedInfo','listCode' => 'scheduleShareType'
                        ]], //'생산스케쥴 공유 여부'
                    ],
                    [
                        ['key'=>'info026','type'=>'text', 'params'=>['model' => 'project.addedInfo'] ], //'TODO 공유 받을 고객 정보'
                    ],
                ]
            ],
            'area70'=>[
                'title' => '분류패킹',
                'field' => [
                    [
                        ['key'=>'info023', 'key2'=>'info024','type'=>'complex3','params'=>[
                            'model' => 'project.addedInfo', 'model2' => 'project.addedInfo',
                            'listCode' => 'existType',
                        ]], //마크유무
                    ],
                    /*[
                        ['key'=>'info025','type'=>'radio','params'=>[
                            'model' => 'project.addedInfo','listCode' => 'scheduleShareType'
                        ]], //'생산스케쥴 공유 여부'
                    ],
                    [
                        ['key'=>'info026','type'=>'text', 'params'=>['model' => 'project.addedInfo'] ], //'TODO 공유 받을 고객 정보'
                    ],*/
                ]
            ],
        ];

        foreach($dataList as $key1 => $viewRow) {
            foreach ($viewRow as $key2 => $divData) {
                foreach($divData as $key3 =>$field ) {
                    foreach($field as $key4 => $fieldData) {
                        $fieldData['params']['key'] = $fieldData['key'];
                        $fieldData['params']['model'] .= '.'.$fieldData['key'];
                        $fieldData['params']['keyDesc'] = empty($fieldData['params']['keyDesc'])?ImsJsonSchema::ADD_INFO[$fieldData['key']]:$fieldData['params']['keyDesc'];

                        if( isset($fieldData['key2']) ){
                            $fieldData['params']['key2'] = $fieldData['key2'];
                            $fieldData['params']['model2'] .= '.'.$fieldData['key2'];
                            $fieldData['params']['key2Desc'] = empty($fieldData['params']['key2Desc'])?ImsJsonSchema::ADD_INFO[$fieldData['key2']]:$fieldData['params']['key2Desc'];
                        }
                        $dataList[$key1][$key2][$key3][$key4] = $fieldData;
                    }
                }
            }
        }

        return $dataList;
    }

    public function setViewRowList($controller){
        $info = self::getProjectViewFieldData();
        $viewRowListData = [
            30 => [ //기획(20), 제안(30), 제안확정(31)
                [
                    [$info['area10']/*근무환경*/ , $info['area20']/*고객사샘플정보*/],
                    [$info['area30']/*기타사항*/ , $info['area40']/*고객성향*/],
                ]
            ], //샘플(40), 샘플확정대기(41)
            40 => [
                [
                    [$info['area50']/*근무환경*/ , $info['area40']/*고객성향*/],
                ]
            ],
            50 => [
                [
                    [$info['area60']/*마크사양 스케쥴공유*/ , $info['area40']/*고객성향*/],
                ]
            ],
            0 => [
                //전체리스트
                [
                    [$info['area10']/*근무환경*/ , $info['area20']/*고객사샘플정보*/],
                    [$info['area30']/*기타사항*/ , $info['area40']/*고객성향*/],
                ]
            ],
        ];

        $request = \Request::request()->toArray();
        $status = empty($request['status'])?0:$request['status'];

        $statusMap = [
            15 => 15, //협상단계
            10 => 10, //진행준비
            16 => 16, //고객사미팅
            20 => 20, //기획
            30 => 20, //제안
            31 => 20, //제안서 확정대기
            40 => 40, //샘플
            41 => 40, //샘플확정 대기
            50 => 50, //고객발주대기
            60 => 60, //발주작업
        ];

        //15, 10, 16, 20, 40, 50, 60, all
        //20

        $viewRowList = $viewRowListData[$statusMap[$status]];
        if(empty($viewRowList)) $viewRowList = $viewRowListData[0]; //단계 없으면 전체 리스트로 나온다.

        //gd_debug($viewRowList);
        $controller->setData('viewRowList', $viewRowList);
    }


    /**
     * TO-DO 리스트 설정
     */
    const TODO_INFO_LIST = [
        [
            'title' => '영업 TO-DO 리스트',
            'dept' => 'sales',
            'link' => '02001001',
            'icon' => 'fa-suitcase',
            'listType' => ['list','completeList'],
        ],
        [
            'title' => '디자인실 TO-DO 리스트',
            'dept' => 'design',
            'link' => '02001002',
            'icon' => 'fa-paint-brush',
            'listType' => ['list','completeList'],
        ],
        [
            'title' => 'QC/기타 TO-DO 리스트',
            'dept' => 'etc',
            'link' => '02001003',
            'icon' => 'fa-users',
            'listType' => ['list','completeList'],
        ],
        [
            'title' => '생산처 TO-DO 리스트',
            'dept' => 'factory',
            'link' => '02001006',
            'icon' => 'fa-ship',
            'listType' => ['list','completeList'],
        ],
    ];


    /**
     * 프로젝트 상세 화면 설정
     * @param $controller
     */
    public function setProjectViewData($controller){
        //gd_debug($viewRowList);

        $this->setViewRowList($controller);
        $controller->setData('dpTodoInfoList',[
            [self::TODO_INFO_LIST[0],self::TODO_INFO_LIST[1]],
            [self::TODO_INFO_LIST[2],self::TODO_INFO_LIST[3]],
        ]);

        $controller->setData('todoInfoList',self::TODO_INFO_LIST);
        $controller->setData('addInfo',ImsJsonSchema::ADD_INFO);

        $fieldMap = [
            'all'       => $this->setupStep10(),//전체 TODO 3
            'step10' => $this->setupStep10(),//진행준비
            'step16' => $this->setupStep16(),//고객사미팅 TODO  2
            'step20' => $this->setList20(), //기획
            'step30' => $this->setList30(), //제안
            'step31' => $this->setList31(), //제안
            'step40' => $this->setList40(),//샘플제안
            'step41' => $this->setList41(),//샘플제안
            'step50' => $this->setList50(),//발주대기
            'step60' => $this->setList60(),//발주
            'step90' => $this->setList(),//발주
        ];

        $controller->setData('fieldList', $fieldMap);

        //템플릿 형태
        /*$templateList = [
            'text','radio'
            ,'complex1' //number + radio
            ,'complex2' //picker + text
            ,'complex3' //text + text
            ,'complex4' //text + radio
        ];
        foreach($templateList as $templateKey){
            $filePath = './admin/html_template/'.$templateKey.'.html';
            $template[$templateKey] = SlCommonUtil::getFileData($filePath);
        }
        $controller->setData('htmlTemplate',$template);*/


        //gd_debug($template);
    }


}
