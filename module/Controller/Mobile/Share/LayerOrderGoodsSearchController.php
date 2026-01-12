<?php

namespace Controller\Mobile\Share;

use Component\Goods\Goods;
use Component\Member\Util\MemberUtil;
use Component\Storage\Storage;
use SlComponent\Util\SitelabLogger;
use SlComponent\Util\SlCodeMap;
use SlComponent\Util\SlLoader;
use Component\Board\Board;
use Component\Goods\AddGoodsAdmin;
use Component\Order\OrderAdmin;
use Framework\Utility\SkinUtils;

class LayerOrderGoodsSearchController extends \Bundle\Controller\Mobile\Share\LayerOrderSearchController
{
    public function index()
    {
        parent::index();
        $this->setData('claimReason', SlCodeMap::CLAIM_REASON);
        $data = $this->getData('data');
        //gd_debug($list);
        foreach($data as $listKey => $listValue){
            //gd_debug($listValue['optionInfo']);
            $optionData = json_decode(gd_htmlspecialchars_stripslashes($listValue['optionInfo']), true);
            //gd_debug($optionData);
            $optionArray = array();
            if( !empty( $optionData )){
                foreach($optionData as $val){
                    $optionArray[] = $val[1];
                }
                $optionName = implode('/',$optionArray);
                $listValue['optionName'] = $optionName;
            }
            //

            $data[$listKey] = $listValue;
        }
        $this->setData('data', $data);
    }
}
