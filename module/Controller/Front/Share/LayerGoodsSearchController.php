<?php

namespace Controller\Front\Share;

use Component\Goods\Goods;
use Component\Member\Util\MemberUtil;
use Component\Storage\Storage;
use SiteLabUtil\SlCommonUtil;
use SlComponent\Util\SitelabLogger;
use SlComponent\Util\SlCodeMap;
use SlComponent\Util\SlLoader;

class LayerGoodsSearchController extends \Bundle\Controller\Front\Share\LayerGoodsSearchController
{
    public function index()
    {
        parent::index();

        $memNo = \Session::get('member.memNo');
        $this->setData('memberScmNo', MemberUtil::getMemberScmNo($memNo));

        $goods = SlLoader::cLoad('Goods','Goods');

        $list = $this->getData('list');
        foreach($list as $listKey => $listValue){
            $goodsOptionInfo = $goods->getGoodsOptionByGoodsNo($listValue['goodsNo']);
            $listValue['goodsOptionName'] = $goodsOptionInfo['goodsOptionNameStrList'];
            if( !empty($goodsOptionInfo['goodsOptionNameStrList'])) {
                $optionList = explode( '^|^'  , $goodsOptionInfo['goodsOptionNameStrList'] );
                $optionListTagArray = array();
                foreach($optionList as $key => $value){
                    $optionListTagArray[] =  '<option>'  . $value . '</option>';
                }
                $listValue['goodsOptionTag'] = implode('',$optionListTagArray);
            }
            $listValue['claimReason'] = SlCommonUtil::getClaimReasonByGoodsNo($listValue['goodsNo']);
            $list[$listKey] = $listValue;
        }

        //클레임 사유 목록
        //$this->setData('claimReason', SlCodeMap::CLAIM_REASON);
        $this->setData('orderNo', SlCodeMap::SCM_ORDER_INIT[MemberUtil::getMemberScmNo()]);

        $this->setData('list', $list);
    }
}
