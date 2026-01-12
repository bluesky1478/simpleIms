<?php
namespace Component\Ims;

use App;

use SlComponent\Database\DBUtil2;
use SlComponent\Database\SearchVo;

use Controller\Admin\Ims\ImsPsNkTrait;
use Component\Ims\ImsServiceSortNkTrait;

class ImsBusiCateService {
    use ImsPsNkTrait;
    use ImsServiceSortNkTrait;

    private $dpData;
    public function __construct(){
        $this->dpData = [
            ['type' => 'c', 'col' => 10, 'class' => 'ta-l pdl5', 'name' => 'busiCateType', 'title' => '구분', ],
            ['type' => 'parent_cate_name', 'col' => 12, 'class' => 'ta-l pdl5', 'name' => 'parentCateName', 'title' => '상위업종명', ],
            ['type' => 'cate_name', 'col' => 18, 'class' => 'ta-l pdl5', 'name' => 'cateName', 'title' => '세부업종명', ],
            ['type' => 'c', 'col' => 0, 'class' => '', 'name' => 'cateDesc', 'title' => '업종설명', ],
        ];
    }
    public function getDisplay(){ return $this->dpData; }

    public function getListBusiCate($params) {
        $sTableNm = ImsDBName::BUSI_CATE;
        $tableInfo=[
            'a' => ['data' => [ $sTableNm ], 'field' => ["a.*"]],
            'parent' => ['data' => [ $sTableNm, 'LEFT OUTER JOIN', 'a.parentBusiCateSno = parent.sno' ], 'field' => ["parent.cateName as parentCateName"]],
        ];
        if (!isset($params['upsertSnoGet'])) { //리스트를 return받으려는 경우
            //리스트에서 뿌려줄 필드리스트 정리
            $aFldList = $this->getDisplay();
        } else $aFldList = [];
        $aReturn = $this->fnRefineListUpsertForm($params, $sTableNm, $tableInfo, $aFldList);

        if (!isset($params['upsertSnoGet'])) {
            if (isset($aReturn['list']) && count($aReturn['list']) > 0) {
                foreach ($aReturn['list'] as $key => $val) {
                    $aReturn['list'][$key]['busiCateType'] = $val['parentBusiCateSno'] == 0 ? '상위업종' : '세부업종';
                    $aReturn['list'][$key]['regDt'] = explode(' ', $val['regDt'])[0];
                }
            }
            //상위업종 리스트
            $aReturn['parent_cate_list'] = [0=>'없음'];
            $aParentList = DBUtil2::getListBySearchVo(ImsDBName::BUSI_CATE, new SearchVo('parentBusiCateSno=?', '0'));
            if (count($aParentList) > 0) {
                foreach ($aParentList as $val) {
                    $aReturn['parent_cate_list'][$val['sno']] = $val['cateName'];
                }
            }
        } else {
            $aReturn['info']['busiCateType'] = $aReturn['info']['parentBusiCateSno'] == 0 ? '상위업종' : '세부업종';
        }

        return $aReturn;
    }



}