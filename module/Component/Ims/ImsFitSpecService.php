<?php
namespace Component\Ims;

use App;
use Component\Database\DBTableField;
use Component\Work\WorkCodeMap;
use SiteLabUtil\SlCommonUtil;
use SlComponent\Database\DBUtil2;
use SlComponent\Database\SearchVo;

use Controller\Admin\Ims\ImsPsNkTrait;
use Component\Ims\ImsServiceSortNkTrait;
use SlComponent\Util\SitelabLogger;
use SlComponent\Util\SlLoader;

//사이즈스펙관리 -> 기초정보관리
class ImsFitSpecService {
    use ImsPsNkTrait;
    use ImsServiceSortNkTrait;

    private $dpData;
    private $dpDataBasicOption;
    private $dpDataEtcCost;
    private $dpDataSampleRoom;

    public function __construct(){
        //사이즈 스펙
        $this->dpData = [
            ['type' => 'c', 'col' => 16, 'class' => 'ta-l pdl5', 'name' => 'fitSpecName', 'title' => '사이즈 스펙명', 'skip'=>true],
            ['type' => 's', 'col' => 4, 'class' => '', 'name' => 'fitSeasonHan', 'title' => '시즌', ],
            ['type' => 's', 'col' => 9, 'class' => 'ta-l pdl5', 'name' => 'fitStyleHan', 'title' => '스타일', ],
            ['type' => 's', 'col' => 6, 'class' => '', 'name' => 'fitName', 'title' => '핏', ],
            ['type' => 's', 'col' => 8, 'class' => '', 'name' => 'fitSizeName', 'title' => '구분',],
            ['type' => 's', 'col' => 4, 'class' => '', 'name' => 'fitSize', 'title' => '기준', ],
            ['type' => 's', 'col' => 0, 'class' => 'ta-l pdl5 font-11', 'name' => 'options', 'title' => '측정항목', 'skip'=>true],
            ['type' => 's', 'col' => 4, 'class' => '', 'name' => 'regManagerName', 'title' => '등록자', 'skip'=>true],
            ['type' => 'd2', 'col' => 4, 'class' => '', 'name' => 'regDt', 'title' => '등록일', 'skip'=>true ],
        ];

        $this->dpDataBasicOption = [
            ['type' => 'c', 'col' => 12, 'class' => '', 'name' => 'fitSeasonHan', 'title' => '시즌', ],
            ['type' => 'c', 'col' => 12, 'class' => '', 'name' => 'fitStyleHan', 'title' => '스타일', ],
            ['type' => 'title', 'col' => 0, 'class' => 'ta-l pdl5', 'name' => 'optionName', 'title' => '부위명', ],
            ['type' => 'c', 'col' => 8, 'class' => '', 'name' => 'optionRange', 'title' => '기본편차값', ],
            ['type' => 'c', 'col' => 8, 'class' => '', 'name' => 'optionValue', 'title' => '기본스펙값', ],
            ['type' => 'c', 'col' => 7, 'class' => '', 'name' => 'optionUnit', 'title' => '단위', ],
            ['type' => 'c', 'col' => 12, 'class' => '', 'name' => 'regManagerName', 'title' => '등록자', ],
            ['type' => 'c', 'col' => 16, 'class' => '', 'name' => 'regDt', 'title' => '등록일', ],
        ];

        $this->dpDataEtcCost = [
            ['type' => 'c', 'col' => 12, 'class' => '', 'name' => 'costTypeHan', 'title' => '유형', ],
            ['type' => 'c', 'col' => 7, 'class' => '', 'name' => 'costCode', 'title' => '코드', ],
            ['type' => 'title', 'col' => 20, 'class' => 'ta-l pdl5', 'name' => 'costName', 'title' => '구분명', ],
            ['type' => 'c', 'col' => 7, 'class' => '', 'name' => 'costUnitPrice', 'title' => '기본단가', ],
            ['type' => 'c', 'col' => 0, 'class' => '', 'name' => 'costDesc', 'title' => '내용', ],
            ['type' => 'c', 'col' => 12, 'class' => '', 'name' => 'regManagerName', 'title' => '등록자', ],
            ['type' => 'c', 'col' => 16, 'class' => '', 'name' => 'regDt', 'title' => '등록일', ],
        ];

        //샘플실 패턴실 관리
        $this->dpDataSampleRoom = [
            ['type' => 'c', 'col' => 12, 'class' => '', 'name' => 'factoryTypeHan', 'title' => '타입', ],
            ['type' => 'title', 'col' => 15, 'class' => 'ta-l pdl5', 'name' => 'factoryName', 'title' => '이름', ],
            ['type' => 'c', 'col' => 10, 'class' => '', 'name' => 'factoryPhone', 'title' => '전화번호', ],
            ['type' => 'c', 'col' => 0, 'class' => 'ta-l pdl5', 'name' => 'factoryAddress', 'title' => '주소', ],
            ['type' => 'c', 'col' => 8, 'class' => '', 'name' => 'regDt', 'title' => '등록일', ],
        ];

        //피팅항목 체크
        $this->dpDataFittingCheck = [
            ['type' => 'c', 'col' => 12, 'class' => '', 'name' => 'fitSeasonHan', 'title' => '시즌', ],
            ['type' => 'c', 'col' => 12, 'class' => '', 'name' => 'fitStyleHan', 'title' => '스타일', ],
            ['type' => 'title', 'col' => 0, 'class' => 'ta-l pdl5', 'name' => 'fittingCheckName', 'title' => '양식명', ],
            ['type' => 'c', 'col' => 12, 'class' => '', 'name' => 'regManagerName', 'title' => '등록자', ],
            ['type' => 'c', 'col' => 16, 'class' => '', 'name' => 'regDt', 'title' => '등록일', ],
        ];

    }

    public function getDisplay(){
        return $this->dpData;
    }
    public function getDisplayBasicOption(){
        return $this->dpDataBasicOption;
    }
    public function getDisplayEtcCost(){
        return $this->dpDataEtcCost;
    }
    public function getDisplaySampleRoom(){
        return $this->dpDataSampleRoom;
    }
    public function getDisplayFittingCheck(){
        return $this->dpDataFittingCheck;
    }

    public function getListFitSpec($params) {
        $sTableNm = ImsDBName::BASIC_SIZE_SPEC;
        $tableInfo=[
            'a' => ['data' => [ $sTableNm ]
                , 'field' => ["a.*","concat(season.codeValueKr,' ',style.codeValueKr,'/',a.fitName,' ',a.fitSizeName) as fitSpecName"]],
            'reg' => ['data' => [ DB_MANAGER
                , 'LEFT OUTER JOIN', 'a.regManagerSno = reg.sno' ]
                , 'field' => ["if(reg.sno is null, '미선택', reg.managerNm) as regManagerName"]],
            'season' => ['data' => [ "(select codeValueEn, codeValueKr from sl_imsCode where codeType='시즌')"
                , 'LEFT OUTER JOIN', 'a.fitSeason = season.codeValueEn' ]
                , 'field' => ["season.codeValueKr as fitSeasonHan"]],
            'style' => ['data' => [ "(select codeValueEn, codeValueKr from sl_imsCode where codeType='스타일')"
                , 'LEFT OUTER JOIN', 'a.fitStyle = style.codeValueEn' ]
                , 'field' => ["style.codeValueKr as fitStyleHan"]],
        ];

        if (!isset($params['upsertSnoGet'])) { //리스트를 return받으려는 경우
            //리스트에서 뿌려줄 필드리스트 정리
            $aFldList = $this->getDisplay();
        } else $aFldList = [];

        //정렬 조건 추가
        $sortCondition = explode(',', $params['sort']);
        $params['extSortMap'] = [
            'CF1' => "concat(a.fitSeason,style.codeValueKr,' ',a.fitName,' ',a.fitSizeName) {$sortCondition[1]}"
        ];

        $list = $this->fnRefineListUpsertForm($params, $sTableNm, $tableInfo, $aFldList);
        foreach($list['list'] as $key => $each){
            $optionNameList = [];
            foreach($each['jsonOptions'] as $option) {
                $optionNameList[] = $option['optionName'];
            }
            $each['options'] = implode(',', $optionNameList);
            $list['list'][$key]=$each;
        }

        return $list;
    }

    public function getListSampleRoom($params) {
        $sTableNm = ImsDBName::SAMPLE_FACTORY;
        $tableInfo=[
            'a' => ['data' => [ $sTableNm ], 'field' => ["a.*"]],
        ];
        if (!isset($params['upsertSnoGet'])) { //리스트를 return받으려는 경우
            //리스트에서 뿌려줄 필드리스트 정리
            $aFldList = $this->getDisplaySampleRoom();
        } else $aFldList = [];
        $aReturn = $this->fnRefineListUpsertForm($params, $sTableNm, $tableInfo, $aFldList);

        //tableImsSampleFactory() 에서는 sno 의 def값이 null이 아니라서 int로 변경필요(이거 안하면 등록/수정폼 깨진다)
        if (isset($params['upsertSnoGet'])) {
            $aReturn['info']['sno'] = (int)$aReturn['info']['sno'];
            $aReturn['info']['factoryTypeHan'] = implode(',', $this->convertCheckboxSumToArr(NkCodeMap::FACTORY_TYPE, (int)$aReturn['info']['factoryType'], 'text'));
            $aReturn['info']['factoryType'] = $this->convertCheckboxSumToArr(NkCodeMap::FACTORY_TYPE, (int)$aReturn['info']['factoryType']);
        } else {
            if (count($aReturn['list']) > 0) {
                foreach ($aReturn['list'] as $key=>$val) {
                    $aReturn['list'][$key]['factoryTypeHan'] = implode(',', $this->convertCheckboxSumToArr(NkCodeMap::FACTORY_TYPE, (int)$val['factoryType'], 'text'));
                }
            }
        }

        return $aReturn;
    }

    public function getListFittingCheck($params) {
        $sTableNm = ImsDBName::BASIC_FITTING_CHECK;
        $tableInfo=[
            'a' => ['data' => [ $sTableNm ], 'field' => ["a.*"]],
            'reg' => ['data' => [ DB_MANAGER, 'LEFT OUTER JOIN', 'a.regManagerSno = reg.sno' ], 'field' => ["if(reg.sno is null, '미선택', reg.managerNm) as regManagerName"]],
        ];
        if (!isset($params['upsertSnoGet'])) { //리스트를 return받으려는 경우
            //리스트에서 뿌려줄 필드리스트 정리
            $aFldList = $this->getDisplayFittingCheck();
        } else $aFldList = [];
        $aReturn = $this->fnRefineListUpsertForm($params, $sTableNm, $tableInfo, $aFldList);

        if (isset($params['upsertSnoGet']) && $params['upsertSnoGet'] == 0) { //등록인 경우
        } else {
            //row별로 스타일코드, 시즌코드 한글명 가져오기
            $imsService = SlLoader::cLoad('ims','imsService');
            $aStyleCodeList = $imsService->getCode('style','스타일');
            $aTmpList = DBUtil2::getList(ImsDBName::CODE, 'codeType', '시즌');
            $aSeasonCodeList = SlCommonUtil::arrayAppKeyValue($aTmpList,'codeValueEn','codeValueKr');
            if (!isset($params['upsertSnoGet'])) {
                if (count($aReturn['list']) > 0) {
                    foreach ($aReturn['list'] as $key=>$val) {
                        $aReturn['list'][$key]['fitStyleHan'] = $aStyleCodeList[$val['fitStyle']];
                        $aReturn['list'][$key]['fitSeasonHan'] = $aSeasonCodeList[$val['fitSeason']];
                    }
                }
            } else { //수정인 경우
                $aReturn['info']['fitStyleHan'] = $aStyleCodeList[$aReturn['info']['fitStyle']];
                $aReturn['info']['fitSeasonHan'] = $aSeasonCodeList[$aReturn['info']['fitSeason']];
            }            
        }
        return $aReturn;
    }

    public function setBasicProposalGuide($params) {
        $iResgisterSno = \Session::get('manager.sno');
        $sCurrDt = date('Y-m-d H:i:s');
        foreach ($params['list'] as $key => $val) {
            $iSno = (int)$val['sno'];
            unset($val['sno']);
            if ($iSno === 0) {
                $val['regManagerSno'] = $iResgisterSno;
                if ($params['bFlagRunSearch'] != 'true') $val['sortNum'] = $key + 1;
                $val['regDt'] = $sCurrDt;

                DBUtil2::insert(ImsDBName::BASIC_PROPOSAL_GUIDE, $val);
            } else {
                if ($params['bFlagRunSearch'] != 'true') $val['sortNum'] = $key + 1;
                $val['modDt'] = $sCurrDt;

                DBUtil2::update(ImsDBName::BASIC_PROPOSAL_GUIDE, $val, new SearchVo('sno=?', $iSno));
            }
        }
    }
}