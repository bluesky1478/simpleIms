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
namespace Controller\Admin\Sitelab;

use Component\Storage\Storage;
use Framework\Debug\Exception\LayerException;
use Framework\Debug\Exception\LayerNotReloadException;
use Exception;
use Message;
use Request;
use Session;
use SlComponent\Database\DBUtil;
use SlComponent\Database\DBUtil2;
use SlComponent\Database\SearchVo;
use SlComponent\Util\SitelabLogger;
use SlComponent\Util\SlLoader;

class GoodsPsController extends \Controller\Admin\Goods\GoodsPsController{
    public function index(){
        try {
            parent::index();

            // --- 각 배열을 trim 처리
            $postValue = Request::post()->toArray();
            //SitelabLogger::logger($postValue);
            $goodsPolicyComponent = \App::load(\Component\Goods\GoodsPolicy::class);

            switch ($postValue['mode']) {
                case 'delete-size-file' :
                    $goodsPolicyComponent->deleteSizeImg($postValue);
                    //['data'=>$params,'msg'=>'등록 되었습니다.']
                    $this->json(
                        [
                            'code' => 200,
                            'message' => '저장되었습니다.',
                            'data' => $postValue,
                        ]
                    );
                    break;
                case 'save_soldout_memo' :
                    $goodsPolicyComponent->saveSoldoutMemo($postValue);
                    $files = Request::files()->toArray();
                    $goodsPolicyComponent->saveSizeImg($postValue, $files);
                    echo json_encode(1);
                    break;
                case 'save_open_package':
                    $updateData['isOpenFl'] = $postValue['isOpenFl'];
                    $updateData['openGoodsNo'] = $postValue['openGoodsNo'];

                    if( empty($postValue['isOpenFl']) ){
                        $updateData['goodsPermissionGroup'] = ''; //일반
                        $updateData['goodsAccessGroup'] = '';
                    }else{
                        $updateData['goodsPermissionGroup'] = 10; //오픈패키지 그룹으로
                        $updateData['goodsAccessGroup'] = 10;
                    }

                    DBUtil2::update(DB_GOODS, $updateData, new SearchVo('goodsNo=?', $postValue['goodsNo']));

                    echo json_encode(1);
                    exit;
                    break;
                case 'set_add_reason':
                    foreach($postValue['selectedGoods'] as $goodsNo) {
                        DBUtil2::update(DB_GOODS, ['addReason' => $postValue['addReason']], new SearchVo('goodsNo=?', $goodsNo));
                    }
                    echo json_encode(1);
                    exit;
                    break;
                case 'set_hankook_type':
                    foreach($postValue['selectedGoods'] as $goodsNo) {
                        DBUtil2::update(DB_GOODS, ['hankookType' => $postValue['hankookType']], new SearchVo('goodsNo=?', $goodsNo));
                    }
                    echo json_encode(1);
                    exit;
                    break;
                case 'set_member_type':
                    foreach($postValue['selectedGoods'] as $goodsNo){
                        DBUtil2::update(DB_GOODS, ['memberType' => $postValue['memberType']], new SearchVo('goodsNo=?', $goodsNo)  );
                    }
                    echo json_encode(1);
                    exit;
                    break;
                // 설문 정책불러오기
                case 'save_safe_stock':
                    $goods = SlLoader::cLoad('goods','goods');
                    echo json_encode($goods->saveSafeCnt($postValue));
                    exit;
                    break;
                // 설문 정책불러오기
                case 'get_survey_policy_list':
                    echo json_encode($goodsPolicyComponent->getSurveyPolicy());
                    exit;
                    break;
                // 무상 정책불러오기
                case 'get_free_policy_list':
                    echo json_encode($goodsPolicyComponent->getFreePolicy());
                    exit;
                    break;
                // 할인 정책불러오기
                case 'get_sale_policy_list':
                    echo json_encode($goodsPolicyComponent->getSalePolicy());
                    exit;
                    break;
                //무상 정책 등록
                case 'add_free_policy':
                    $goodsPolicyComponent->addFreePolicy($postValue);
                    echo true;
                    exit;
                    break;
                //할인 정책 등록
                case 'add_sale_policy':
                    $goodsPolicyComponent->addSalePolicy($postValue);
                    echo true;
                    exit;
                    break;
                //설문 정책 등록
                case 'add_survey_policy':
                    $goodsPolicyComponent->addSurveyPolicy($postValue);
                    echo true;
                    exit;
                    break;
                //정책 수정
                case 'update_policy' :
                    $goodsPolicyComponent->updatePolicy($postValue);
                    echo true;
                    exit;
                //정책 수정
                case 'delete_goods_link_policy' :
                    $goodsPolicyComponent->linkDeletePolicy($postValue);
                    echo true;
                    exit;
                // 정책연결
                case 'link_policy':
                    $goodsPolicyComponent->linkPolicy($postValue);
                    echo true;
                    break;
                // 회원검색
                case 'get_member_list':
                    echo json_encode($goodsPolicyComponent->getMemberList($postValue));
                    break;
                // 상품별 적용 회원검색
                case 'get_goods_member_list':
                    echo json_encode($goodsPolicyComponent->getPolicyGoodsMemberList($postValue));
                    break;
                // 회원연결
                case 'link_member':
                    $goodsPolicyComponent->linkMember($postValue);
                    echo true;
                    break;
                // 회원연결 삭제
                case 'link_delete_member':
                    $goodsPolicyComponent->linkDeleteMember($postValue);
                    echo true;
                    break;

            }

        } catch (Exception $e) {
            throw new LayerException($e->getMessage());
        }
    }
}
