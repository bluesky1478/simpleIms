<?php

namespace Controller\Front\Test;

use Component\Sitelab\MallConfig;
use Component\Sitelab\SiteLabDownloadUtil;
use Component\Sitelab\SiteLabGodoUtil;
use Component\Sitelab\SlLoader;
use Component\Storage\Storage;
use Domain\Sl\Estimate\EstimateService;
use SlComponent\Database\DBUtil;
use SlComponent\Database\DBUtil2;
use SlComponent\Database\SearchVo;
use UserFilePath;
use FileHandler;
use Component\Sitelab\SitelabDBTable;
use Component\Sitelab\SitelabLogger;
use Component\Database\DBTableField;

use Component\Mall\Mall;
use Component\Mall\MallDAO;
use Session;
use Component\Member\Util\MemberUtil;
use Component\Validator\Validator;
use Component\Cart\Cart;
use App;
use Framework\Security\Digester;
use Framework\Utility\GodoUtils;
use Component\Member\Manager;

/**
 * 주문 상세 페이지
 * [관리자 모드] 주문 상세 페이지
 *
 * @package Bundle\Controller\Admin\Order
 * @author  Jong-tae Ahn <qnibus@godo.co.kr>
 */
class TestController extends \Controller\Front\Controller{

    public function index(){
        gd_debug("사이트랩 테스트 페이지 (User PC2)");
        //gd_debug(\Request::isMobile());


        gd_debug(0);
        $manager = \App::load(Manager::class);
        gd_debug(1);
        $managerId = 'b1478';
        $managerPw = 'dndbwnsgh00';
        $diskData = $manager->adminDiskCheck();
        gd_debug(2);
        $managerInfo = $manager->validateManagerLogin(['managerId' => $managerId,'managerPw' => $managerPw,], $diskData['adminAccess']);
        $manager->afterManagerLogin($managerInfo);


        //$this->tkeMemberInsert();
        //gd_debug(Digester::digest('1234'));

        gd_debug("완료");
        exit();
    }

    public function tkeMemberInsert(){

        //gd_debug('기존 회원 삭제.');
        //TKE(티센크루프)
        //groupSno 1

        //select * from es_member where ex1='TKE(티센크루프)' and groupSno =1 and memNo not in (4991,5469,4746,5639,5000,1)
        //delete from es_member where ex1='TKE(티센크루프)' and groupSno =1 and memNo not in (4991,5469,4746,5639,5000,1)
        //DBUtil2::runSql("update sl_tkeMember a join sl_tkeMemberPwd2 b on a.memId = b.memId set a.memPw = b.memPw");

        $groupInfo = [
            'NI' => 5,
            'SVC' => 6,
            'MFG' => 7,
        ];

        $tkeMemberGroup = [
            '정직원',
            '파견직',
            '컨설턴트',
        ];

        $searchVo = new SearchVo('memPw=?', '');
        //$searchVo = new SearchVo('1', '1');
        //$searchVo->setLimit(1);
        $list = DBUtil2::getListBySearchVo('sl_tkeMember', $searchVo);

        gd_debug('PWD MAKE : '.count($list));

        foreach($list as $paramData){
            if( in_array($paramData['groupName'], $tkeMemberGroup) ){
                if( '컨설턴트' === $paramData['groupName'] || '파견직' === $paramData['groupName'] ){
                    if( 12 > strlen($paramData['memId']) ){
                        $pwdStr = 'a' . preg_replace("/[^0-9]*/s", "", $paramData['memId']); //정직원처럼 (사번일경우)
                    }else{
                        $pwdStr = 'a' . preg_replace("/[^0-9]*/s", "", $paramData['cellPhone']); //파트너
                    }
                }else{
                    $pwdStr = 'a' . preg_replace("/[^0-9]*/s", "", $paramData['memId']); //정직원
                }
            }else{
                $saveData['groupSno'] = $groupInfo[strtoupper($paramData['groupName'])];
                $pwdStr = 'a' . preg_replace("/[^0-9]*/s", "", $paramData['cellPhone']); //파트너
            }
            $password = Digester::digest($pwdStr);
            //gd_debug($password);
            //gd_debug($pwdStr);
            //gd_debug($paramData['sno']);
            //DBUtil2::update('sl_tkeMember', ['memPw'=>$password], new SearchVo('sno=?', $paramData['sno']));
            DBUtil2::update('sl_tkeMember', ['memPw'=>$password], new SearchVo('memId=?', $paramData['memId']));
        }

        gd_debug(count($list));


        //TODO : MERGE 작업

        /*gd_debug(MemberUtil::getMemberScmNo());

        $scmNo = MemberUtil::getMemberScmNo();
        $searchData = DBUtil::getOneBySearchVo('sl_scmPopup', new SearchVo(['scmNo=?','popupSno=?'],['4','6']));
        gd_debug(empty($searchData));


        $cateDepth = 4;
        //gd_debug($cateDepth);
        $category = \App::load('\\Component\\Category\\Category');
        $getData = $category->getCategoryCodeInfo(null, $cateDepth, false, false, 'pc');

        if ($getData) {
            $getData = array_chunk($getData, 6);
        }

        gd_debug($getData);*/

        /*MemberUtil::guestOrder('1801251651469144', '김지윤'); //로그인 다시 시킴!ㄴ
        gd_debug(\Session::get('guest'));*/

        //$config = MallConfig::getOrderPath();
        //gd_debug($config);

        //$member = \Session::get('member');
        //gd_debug($member);

        //SiteLabDownloadUtil::download('./data/excel/sample/1704040102087775_1.xls','test.xls');

        //gd_debug( Validator::required('1000000010', true) );
        //gd_debug( Validator::required(1000000010, true) );

        /*
        $a = '가가가\n나나나나';
        gd_debug($a);

        $a =  nl2br('가가가\r\n나나나나');
        gd_debug($a);
        */

        //TODO 나중에 요긴하게 써먹자...
        //\Session::get('manager.managerNm')


        //$this->commonLogicTest();
        //$this->loaderTest();

        //$this->commonLogicTest2();

        //$this->changeStatusTest();

        //$this->cartTest();
        //$this->cartTest2();


        /*$obj = SlLoader::load('sl','estimate','estimatePrintInfo');
        $data = $obj->getDataById(31);
        gd_debug($data['printInfo']);
        $conversion = json_decode($data['printInfo']);
        gd_debug($conversion);
        $arrData['sno'] = 31;*/


        //$arrData['printInfo'] = '';
        //$obj->save($arrData);


        /*$order = \App::load('\\Component\\Order\\Order');
        $result = $order->isGuestOrder('1702131533162419', '테스트');
        gd_debug($result);*/

        //$this->designApplyTest();

    }

    public function designApplyTest(){
        $esService = SlLoader::load('sl','estimate','estimate');
        //$arrData['orderNo'] = '1702132134323739';
        //$arrData['orderNo'] = '1702131533162419';
        $arrData['orderNo'] = '1702121717191073';
        $esService->designApply($arrData);
    }

    //주문 상품 복사 테스트
    public function copyLogicTest(){
        $estimateGoodsService =  SlLoader::load('sl','estimate','estimateGoods');
        $estimateGoodsService->copyFromOrderGoods('1702121702444388','TEST01');
    }


    //로더 테스트
    public function loaderTest(){
        //$obj = SlLoader::load('es','logOrder','sno');
        $obj = SlLoader::nLoader('es','logOrder','sno');
        gd_debug($obj->getDataById(204));
    }

    //추가상품 테스트
    public function addGoodsTest(){
        $arrData['addContent'] = '추가3';
        $arrData['addAmount'] = '5000';
        $arrData['estimateNo'] = '1702022343584657';

        $service = SlLoader::load('sl','estimate','estimateGoods');
        $service->addEstimateAddAmount($arrData);

    }

    //기본 로직 테스트
    public function commonLogicTest(){
        $arrData['sno'] = 34;
        $arrData['deliveryCharge'] = (int)0.00;
        $arrData['orderNo'] = '1701300940173415';

        $esTabeService = \App::load('\\Domain\\Es\\Common\\EsTableService',new \Domain\TableVo('es','orderDelivery','sno') );
        //$esTabeService->save($arrData);
        $esTabeService->update($arrData,'orderNo');

        gd_debug('------------------- 최종 결과 -------------------');
        $result = $esTabeService->getDataById($arrData);
        gd_debug($result);
    }

    public function commonLogicTest2(){
        $consultService = SlLoader::nLoader('es','orderConsult','sno');
        $findConditionArray[] = 'orderNo';
        $arrData['orderNo'] = '1701301400038899';

        $joinInfo['field']='b.managerId,b.managerNm';
        $joinInfo['table']=DB_MANAGER.' b on a.managerNo = b.sno';
        //$joinInfo['whereStr'] = ' AND b.sno = 1';

        gd_debug($joinInfo);

        $consultData = $consultService->getList($arrData,$findConditionArray, 'regDt desc',$joinInfo);
        gd_debug($consultData);
    }

    public function changeStatusTest(){
        $obj = SlLoader::load('sl','estimate','estimate');
        $obj->changeStatus('1702011045423266','o2');
    }


    public function goodsOptionTest(){
        $goodsOptionService = \App::load('\\Domain\\Es\\Goods\\GoodsOptionService');
        $goodsOption = $goodsOptionService->getDataById('34');
        $goodsService = \App::load('\\Domain\\Es\\Goods\\GoodsService');
        $goodsResult = $goodsService->getDataById('1000000011');
        $goodsOptionTitle = explode('^|^',$goodsResult['optionName']);

        $goodsOptionArray = Array();
        foreach( $goodsOptionTitle as $key => $value ){
            $goodsOptionArray[$key][] = $value;
            $goodsOptionArray[$key][] = $goodsOption['optionValue'.($key+1)];
            //마지막이면 자체코드를 붙인다.
            if( count($goodsOptionTitle) == $key+1 ){
                $goodsOptionArray[$key][] = $goodsOption['optionCode'];
                $goodsOptionArray[$key][] = (int)$goodsOption['optionPrice'];
            }else{
                $goodsOptionArray[$key][] = null;
                $goodsOptionArray[$key][] = 0;
            }
        }
        gd_debug($goodsOptionArray);
        gd_debug(json_encode($goodsOptionArray, JSON_UNESCAPED_UNICODE));

        //---------------

        /*$goodsService = \App::load('\\Domain\\Es\\Goods\\GoodsService');
        $goodsResult = $goodsService->getDataById('1000000012');

        $goodsOptionService = \App::load('\\Domain\\Es\\Goods\\GoodsOptionService');
        $result = $tragetGoods['optionInfo'] = $goodsOptionService->getOptionJsonStr($goodsResult, '41');
        gd_debug($result);*/
    }

    public function cartTest(){
        $cart = \App::load('\\Component\\Cart\\Cart');
        $cart->truncateDirectCart();
        //$Cart->saveInfoCart($arrData);
        $arrData['mallSno'] = Mall::getSession('mallSno');;
        $arrData['goodsNo'] = 1000000000;
        $arrData['optionSno'] = 1;
        $arrData['goodsCnt'] = 1;
        $arrData['addGoodsNo'] = '';
        $arrData['addGoodsCnt'] = '';
        $arrData['optionText'] = '';
        $arrData['deliveryCollectFl'] = 'pre';
        $arrData['memberCouponNo'] = '';
        $arrData['tmpOrderNo'] = '';
        $arrData['printInfo'] = '';
        $arrData['scmNo'] = 1;
        $arrData['cartMode'] = 'd';
        $arrData['goodsPrice'] = 1000;
        $result = $cart->saveGoodsToCart($arrData);
        gd_debug($result);
    }

    public function cartTest2(){
        $cart = \App::load('\\Component\\Cart\\Cart');
        $cart->truncateDirectCart();
        //$Cart->saveInfoCart($arrData);
        $arrData['mallSno'] = Mall::getSession('mallSno');;
        $arrData['goodsNo'] = 1000000012;
        $arrData['optionSno'] = 41;
        $arrData['goodsCnt'] = 1;
        $arrData['addGoodsNo'][] = '1000000000';
        $arrData['addGoodsCnt'][] = '1';
        $arrData['optionText'] = '';
        $arrData['deliveryCollectFl'] = 'pre';
        $arrData['memberCouponNo'] = '';
        $arrData['tmpOrderNo'] = '';
        $arrData['printInfo'] = '';
        $arrData['scmNo'] = 1;
        $arrData['cartMode'] = 'd';
        $arrData['goodsPrice'] = 100000;
        //$arrData['addGoodsNo'] = '1000000000';
        //$arrData['addGoodsCnt'] = 1;
        $result = $cart->saveGoodsToCart($arrData);
        gd_debug($arrData);
        gd_debug($result);
    }


    public function esServiceTest(){
        //1. 주문 찾기....
        $orderService = \App::load('\\Domain\\Es\\Order\\OrderService');
        $orderInfo = $orderService->getDataById('1701261955326886');
        //gd_debug($orderInfo);
        unset($orderInfo['orderNo']);
        $esService = \App::load('\\Domain\\Sl\\Estimate\\EstimateService');
        $orderInfo['orderNo'] = '123';
        $esService->save($orderInfo);
        $result = $esService->getDataById('123');
        gd_debug($result);
    }



    public function oldTest(){

        /*$orderGoodsService = \App::load('\\Domain\\Es\\Order\\OrderGoodsService');
        $result = $orderGoodsService->getDataByOrderNo('1701261955326886');
        gd_debug($result);*/

        //$esGoodsService = \App::load('\\Domain\\Sl\\Estimate\\EstimateGoodsService');
        //$esGoodsService->copyFromOrderGoods('1701261955326886','123');


        //$orderGoodsService = \App::load('\\Domain\\Es\\Order\\OrderInfoService');
        //$result = $orderGoodsService->getDataByOrderNo('1701261955326886');
        //gd_debug($result);

        //$esGoodsService = \App::load('\\Domain\\Sl\\Estimate\\EstimateInfoService');
        //$esGoodsService->copyFromOrderInfo('1701261955326886','123');

        //$this->cartTest();
        //$this->cartTest2();
        //$cart = \App::load('\\Component\\Cart\\Cart');
        //$cart->truncateDirectCart();

        //$this->goodsOptionTest();

        $rslt = DBTableField::tableOrder();
        gd_debug($rslt);

        /*$orderDeliveryService = \App::load('\\Domain\\Es\\Order\\OrderDeliveryService');
        $deliveryInfo = $orderDeliveryService->getDataById(30);
        gd_debug($deliveryInfo);*/


        gd_debug(EstimateService::getEstimateStatus());
        gd_debug(EstimateService::getEstimateStatus(2));

    }

    /*
    조인예제
//------------------------------------------//
    //TODO : 컨설트 못 불러오니 직접 가져온다.
    $consultService = SlLoader::nLoader('es','orderConsult','sno');
    $joinInfo['field']='b.managerId,b.managerNm';
    $joinInfo['table']=DB_MANAGER.' b on a.managerNo = b.sno';
    $findConditionArray[] = 'orderNo';
    $arrData['orderNo'] = Request::get()->get('orderNo');
    $consultData = $consultService->getList($arrData,$findConditionArray, 'regDt desc', $joinInfo);
    $this->setData('consult',$consultData);
    //------------------------------------------//
*/


}