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
 * @link http://www.godo.co.kr
 */
namespace Controller\Front\Order;

use Bundle\Component\Mall\MallDAO;
use Component\CartRemind\CartRemind;
use Component\Member\Member;
use Component\Member\Util\MemberUtil;
use Component\Scm\ScmAsianaCodeMap;
use Component\Scm\ScmHyundaeService;
use Framework\Debug\Exception\AlertRedirectException;
use Component\Mall\Mall;
use Message;
use Globals;
use Session;
use Request;
use SiteLabUtil\SlCommonUtil;
use SlComponent\Database\DBUtil2;
use SlComponent\Util\SlCodeMap;
use SlComponent\Util\SlLoader;
use SlComponent\Util\SlSkinUtil;

/**
 * 주문 완료 페이지
 *
 * @package Bundle\Controller\Front\Order
 * @author  Jong-tae Ahn <qnibus@godo.co.kr>
 */
class OrderController extends \Bundle\Controller\Front\Order\OrderController
{
    /**
     * index
     *
     */
    public function index()
    {
        $locale = \Globals::get('gGlobal.locale');
        // 날짜 픽커를 위한 스크립트와 스타일 호출
        $this->addCss([
            'plugins/bootstrap-datetimepicker.min.css',
            'plugins/bootstrap-datetimepicker-standalone.css',
        ]);
        $this->addScript([
            'moment/moment.js',
            'moment/locale/' . $locale . '.js',
            'jquery/datetimepicker/bootstrap-datetimepicker.min.js',
        ]);
        parent::index();
        $orderService = SlLoader::cLoad('order','orderService');
        $orderService->setOrderController($this);

        $this->setData('isDevIp',SlCommonUtil::isDevIp());

        //아시아나 우선 프론트만 분기
        $scmNo = MemberUtil::getMemberScmNo();
        if(34==$scmNo){
            //아시아나 마스터는 이 작업을 하지 않는다.
            if(!in_array( \Session::get('member.memNo'), ScmAsianaCodeMap::MASTER_NO)) {

                $choiceGoodsCnt = 0; //TODO : 택1상품이 늘어나면 이 로직 수정해야한다.

                //카트 상품 기준 가져오기
                $cartData = $this->getData('cartInfo');
                foreach($cartData as $cartKey1 => $cartData1){
                    foreach($cartData1 as $cartKey2 => $cartData2){
                        foreach($cartData2 as $cartKey3 => $cartData3){
                            if(SlCommonUtil::isDev()){
                                $cartData3['provideInfo'] = ScmAsianaCodeMap::GOODS_PROVIDE_INFO_DEV[$cartData3['goodsNo']];
                                //gd_debug($cartData3['provideInfo']);
                            }else{
                                $cartData3['provideInfo'] = ScmAsianaCodeMap::GOODS_PROVIDE_INFO[$cartData3['goodsNo']];
                            }

                            foreach( ScmAsianaCodeMap::CHOICE_GOODS as $choiceGoods ){
                                if(in_array($cartData3['goodsNm'], $choiceGoods)){  //단 이력에 동일한 상품이 있을 경우에만!!!
                                    $choiceGoodsCnt++;
                                }
                                if($choiceGoodsCnt > 1){
                                    $cartData3['choiceGoods'] = implode(',', $choiceGoods).' 중 택 1';
                                    $cartData3['orderPossible'] = 'n';
                                }
                            }
                            $cartData[$cartKey1][$cartKey2][$cartKey3] = $cartData3;
                        }
                    }
                }
                //gd_debug($cartData);
                $this->setData('cartInfo', $cartData);
            }else{
                $this->setData('isMaster', 1);
            }

            $this->setData('allowTeam', json_encode(ScmAsianaCodeMap::FW_BOOTS_ALLOW_TEAM['team']));
            $this->setData('allowPart1', json_encode(ScmAsianaCodeMap::FW_BOOTS_ALLOW_TEAM['part1']));
            $this->setData('allowPart2', json_encode(ScmAsianaCodeMap::FW_BOOTS_ALLOW_TEAM['part2']));

            $this->getView()->setPageName('order/order_asiana');
        }

    }

}

