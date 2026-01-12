<?php

namespace Component\Imsv2;

use Component\Database\DBTableField;
use Component\Ims\ImsDBName;
use Component\Ims\ImsService;
use Component\Ims\ImsServiceTrait;
use Component\Ims\ImsServiceSortNkTrait;
use Component\Ims\ImsServiceSampleTrait;
use Component\Ims\ImsCodeMap;
use Component\Ims\NkCodeMap;
use Controller\Admin\Ims\ImsPsNkTrait;
use SiteLabUtil\SlCommonUtil;
use SlComponent\Database\DBUtil2;
use SlComponent\Database\SearchVo;
use SlComponent\Database\TableVo;
use SlComponent\Util\SlLoader;

class ImsNkMultiUpdateService
{
    use ImsServiceTrait;
    use ImsServiceSortNkTrait;
    use ImsPsNkTrait;
    use ImsServiceSampleTrait; //스타일(product)샘플의 첨부파일Div값을 가져오기 위함

    //영업 리스트 일괄수정
    public function updateProjectMulti($params)
    {
        $projectDataList = $params['project'];
        foreach ($projectDataList as $val) {
            $aUpdate = $val;
            unset($aUpdate['sno']);
            unset($aUpdate['customerSno']);
            DBUtil2::update(ImsDBName::PROJECT, $aUpdate, new SearchVo('sno=?', (int)$val['sno']));
            DBUtil2::update(ImsDBName::PROJECT_EXT, $aUpdate, new SearchVo('projectSno=?', (int)$val['sno']));
            //고객테이블에 담당자 update
            DBUtil2::update(ImsDBName::CUSTOMER, [
                'salesManagerSno' => $aUpdate['salesManagerSno'],
            ], new SearchVo('sno=?', (int)$val['customerSno']));
        }

        return ['data' => 0, 'msg' => '저장 완료'];
    }

    //기획/제작 일괄수정, 리오더/기성복 일괄수정, 프로젝트관리->전체 일괄수정
    public function updateDesignMulti($params)
    {
        $aDataList = $params['multi_data'];
        foreach ($aDataList as $val) {
            $aUpdateEx = $val;
            $iProjectSno = (int)$val['sno'];
            unset($aUpdateEx['sno']);
            $aUpdate = [];
            if (isset($aUpdateEx['customerDeliveryDt'])) { //고객납기는 ImsDBName::PROJECT, 그 외에는 ImsDBName::PROJECT_EXT
                $aUpdate['customerDeliveryDt'] = $aUpdateEx['customerDeliveryDt'];
                unset($aUpdateEx['customerDeliveryDt']);
            }
            if ($iProjectSno !== 0 && count($aUpdate) > 0) DBUtil2::update(ImsDBName::PROJECT, $aUpdate, new SearchVo('sno=?', $iProjectSno));
            if ($iProjectSno !== 0 && count($aUpdateEx) > 0) DBUtil2::update(ImsDBName::PROJECT_EXT, $aUpdateEx, new SearchVo('projectSno=?', $iProjectSno));
        }

        return ['data' => 0, 'msg' => '저장 완료'];
    }

    //발주 일괄수정
    public function updateQcMulti($params)
    {
        $aDataList = $params['multi_data'];
        foreach ($aDataList as $val) {
            $aUpdate = $val;
            $iProjectSno = (int)$val['sno'];
            $iProjectProductSno = (int)$val['styleSno'];
            unset($aUpdate['sno'], $aUpdate['styleSno']);

            $aUpdateEx = [];
            if (isset($aUpdate['exProductionOrder'])) {
                $aUpdateEx['exProductionOrder'] = $aUpdate['exProductionOrder'];
                unset($aUpdate['exProductionOrder']);
            }
            $aUpdateProduct = [];
            if (isset($aUpdate['prdCustomerDeliveryDt'])) {
                $aUpdateProduct['customerDeliveryDt'] = $aUpdate['prdCustomerDeliveryDt'];
                unset($aUpdate['prdCustomerDeliveryDt']);
            }
            if (isset($aUpdate['prdMsDeliveryDt'])) {
                $aUpdateProduct['msDeliveryDt'] = $aUpdate['prdMsDeliveryDt'];
                unset($aUpdate['prdMsDeliveryDt']);
            }

            if ($iProjectSno !== 0 && count($aUpdate) > 0) DBUtil2::update(ImsDBName::PROJECT, $aUpdate, new SearchVo('sno=?', $iProjectSno));
            if ($iProjectSno !== 0 && count($aUpdateEx) > 0) DBUtil2::update(ImsDBName::PROJECT_EXT, $aUpdateEx, new SearchVo('projectSno=?', $iProjectSno));
            if ($iProjectProductSno !== 0 && count($aUpdateProduct) > 0) DBUtil2::update(ImsDBName::PRODUCT, $aUpdateProduct, new SearchVo('sno=?', $iProjectProductSno));
        }

        return ['data' => 0, 'msg' => '저장 완료'];
    }

}