<?php

namespace Controller\Admin\Order;

use App;
use Component\Order\OrderPolicyListService;
use Component\Scm\ScmOrderListService;
use Controller\Admin\Ims\ImsControllerTrait;
use Exception;
use Component\Category\CategoryAdmin;
use Component\Category\BrandAdmin;
use Globals;
use Request;
use SiteLabUtil\SlCommonUtil;
use SlComponent\Database\DBUtil;
use SlComponent\Database\DBUtil2;
use SlComponent\Util\ExcelCsvUtil;
use SlComponent\Util\SitelabLogger;
use SlComponent\Util\SlCodeMap;
use SlComponent\Util\SlLoader;
use function SlComponent\Util\SlLoader;
use Component\Member\Manager;
use Framework\Utility\DateTimeUtils;
use Component\Storage\Storage;
use Framework\StaticProxy\Proxy\FileHandler;
use UserFilePath;
use Framework\Debug\Exception\AlertOnlyException;

class ScmOrderCalcController extends \Controller\Admin\Controller{

    use ImsControllerTrait;

    public function index(){

        $this->setDefault();

        $isProvider = Manager::isProvider();
        if( $isProvider ){
            $this->callMenu('statistics', 'accept', 'calc2');
        }else{
            $this->callMenu('order', 'order', 'scalc');
        }
        $this->setData('isProvider', $isProvider);

        $this->setData('titleList', $this->getTitleList());

        $exclude = implode(',' , SlCodeMap::SCM_ORDER_EXCLUDE_MEM_NO);

        /*if( SlCommonUtil::isDev() ){
            $memberList = DBUtil2::getList(DB_MEMBER, 'ex1', '한국타이어');
            foreach($memberList as $memberKey => $member){
                $member['memberConfig'] = DBUtil2::getOne('sl_setMemberConfig','memNo',$member['memNo']);
                $memberList[$memberKey] = $member;
            }

            $this->setData('memberList', $memberList);
            $this->setData('goodsList', DBUtil2::getList(DB_GOODS, 'scmNo', 6, 'goodsNo desc'));

            $this->setData('mapData', $this->getMapData(6));
        }else{
            $memberList = DBUtil2::getList(DB_MEMBER, " memNo not in ({$exclude}) and ex1", '오티스(OEK)', 'memNm');
            foreach($memberList as $memberKey => $member){
                $member['memberConfig'] = DBUtil2::getOne('sl_setMemberConfig','memNo',$member['memNo']);
                $memberList[$memberKey] = $member;
            }

            $this->setData('memberList', $memberList);
            $this->setData('goodsList', DBUtil2::getList(DB_GOODS, 'scmNo', 21, 'goodsNo desc'));
            $this->setData('mapData', $this->getMapData(21));
        }*/

        $memberList = DBUtil2::getList(DB_MEMBER, 'ex1', '엠에스이노버');
        foreach($memberList as $memberKey => $member){
            $member['memberConfig'] = DBUtil2::getOne('sl_setMemberConfig','memNo',$member['memNo']);
            $memberList[$memberKey] = $member;
        }

        $this->setData('memberList', $memberList);
        $this->setData('goodsList', DBUtil2::getList(DB_GOODS, 'scmNo', 7, 'goodsNo desc'));
        $this->setData('mapData', $this->getMapData(7));

        $this->getView()->setPageName('order/scm_order_calc.php');

    }

    public function getTitleList(){
        $default = [
            '이름',
            '회원아이디',
            '닉네임',
            '주문수량',
        ];
        return $default;
    }

    public function getMapData($scmNo){
        $sql = "select 
	b.memNo, 
	a.goodsNo,	
	sum(a.goodsCnt) as cnt
from es_orderGoods a 
join es_order b 
on a.orderNo = b.orderNo 
where a.scmNo = {$scmNo}
and left(a.orderStatus,1) in ( 's' , 'd' , 'g' , 'p' , 'o' )
group by b.memNo, a.goodsNo
 ";
        $list =  DBUtil2::runSelect($sql);
        $refineList = [];
        foreach($list as $each){
            $refineList[$each['memNo']][$each['goodsNo']] = $each['cnt'];
        }
        return $refineList;
    }

}