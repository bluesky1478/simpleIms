<?php

namespace Controller\Admin\Test;

use Component\Member\Util\MemberUtil;
use Controller\Admin\Controller;
use Controller\Admin\Order\ControllerService\DeliveryListSql;
use Framework\Utility\NumberUtils;
use Component\Database\DBTableField;
use Component\Sitelab\SiteLabSmsUtil;
use SiteLabUtil\SlCommonUtil;
use SlComponent\Database\DBConst;
use SlComponent\Database\DBUtil2;
use SlComponent\Godo\FactoryService;
use SlComponent\Database\DBUtil;
use SlComponent\Database\SearchVo;
use SlComponent\Database\TableVo;
use SlComponent\Download\SiteLabDownloadUtil;
use SlComponent\Util\SitelabLogger;
use Globals;
use Component\Deposit\Deposit;
use SlComponent\Util\SitelabUtil;
use SlComponent\Util\SlCode;
use SlComponent\Util\SlCodeMap;
use SlComponent\Util\SlErpUtil;
use SlComponent\Util\SlKakaoUtil;
use SlComponent\Util\SlLoader;
use SlComponent\Util\SlPostRequestUtil;
use SlComponent\Util\SlSmsUtil;
use Request;
use Framework\Utility\DateTimeUtils;
use UserFilePath;
use App;
use Component\Godo\GodoSmsServerApi;
use Component\Member\Group\Util;
use Component\Page\Page;
use Component\Validator\Validator;
use Component\Sms\Sms;
use Component\Sms\SmsAutoCode;
use Component\Sms\Code;
use Component\Storage\Storage;
use Exception;
use Framework\Database\DBTool;
use Framework\Utility\ArrayUtils;
use Framework\Utility\StringUtils;
use Framework\Utility\ComponentUtils;
use Logger;
use Framework\Debug\Exception\AlertBackException;
use Component\Coupon\Coupon;

/**
 */
class CreateController extends Controller{

    private $orderService;

    public function index(){

        $this->migToClaim();

        gd_debug(" === Sql Run Complete ===");
        exit();
    }

    public function migFirst(){
        $changeBoard = 'asboard';
        $defaultCategoryKr = 'A/S';

        //복사
        $sqlFile = './module/Controller/Admin/Test/sql/createSql.sql';
        $defaultSql = SlCommonUtil::getFileData($sqlFile);

        $migBoardList = DBUtil2::getList('es_bd_'. $changeBoard);
        foreach($migBoardList as $key => $each){
            $searchVo = new SearchVo(['bdId=?','bdSno=?'],[$changeBoard, $each['sno']]);
            $sql = str_replace('?',$changeBoard, $defaultSql);
            $sql = str_replace('#',$each['sno'], $sql);
            $claimData = DBUtil2::getOneBySearchVo('sl_claimBoardData',$searchVo);

            $claimTypeKr = SlCodeMap::CLAIM_TYPE[$claimData['claimBoardType']];
            if( empty($claimTypeKr) ){
                $claimTypeKr = $defaultCategoryKr;
            }
            $sql = str_replace('|',$claimTypeKr, $sql);

            DBUtil2::runSql($sql);
            $newSno = DBUtil2::runSelect("SELECT @@identity AS id")[0]['id'];
            gd_debug( $each['sno'] . ' => ' . $newSno );
            //gd_debug( $sql );
            $updateData['bdId'] = 'qa';
            $updateData['bdSno'] = $newSno;
            DBUtil2::update('sl_claimBoardAdminData',$updateData,$searchVo);
            DBUtil2::update('sl_claimBoardData',$updateData,$searchVo);
        }
        //TODO : refund는 back 으로 업데이트 하기 -> 반품/환불 통합
        // update sl_scmClaimData set claimType = 'back' where claimType = 'refund'
        // update sl_claimBoardData set claimBoardType = 'back' where claimBoardType = 'refund'
        // update sl_claimBoardAdminData set claimBoardType = 'back' where claimBoardType = 'refund'

    }

    public function migLast(){
        $table = 'zzz_claimBoardData_0724';

    }

    public function migToClaim(){
        //gd_debug('migToClaim');
        $claimList = DBUtil2::getList('sl_claimBoardData ');
        $claimGoodsList = [];
        foreach($claimList as $each){
            //$bdData = DBUtil2::getOne('es_bd_qa', 'sno', $bdSno);
            //$prevClaimData = implode(':',$bdData['subSubject']);

            if('as' == $each['claimBoardType']) {
                $searchBdId = 'asboard';
            }else if( 'refund' == $each['claimBoardType'] || 'back' == $each['claimBoardType'] ){
                $searchBdId = 'exchange';
            }else{
                $searchBdId = $each['claimBoardType'];
            }
            $changeBoardData = DBUtil2::getOne('es_bd_qa', 'subSubject', $searchBdId.':'.$each['bdSno']);

            //$bdSno = $each['bdSno'];
            $bdSno = $changeBoardData['sno'];
            //gd_debug( $searchBdId.':'.$each['bdSno'] . ' ==>  ' . $bdSno );
            $goodsNo = $each['goodsNo'];

            if(empty($bdSno)) continue;

            if( empty($claimGoodsList[$bdSno]) ){
                //$claimGoodsList[$bdSno] = \SiteLabUtil\SlCommonUtil::getAvailData($each,['orderNo']);
                $claimGoodsList[$bdSno]['orderNo'] = $each['orderNo'];
                $claimGoodsList[$bdSno]['bdSno'] = $bdSno;
                $claimGoodsList[$bdSno]['bdId'] = 'qa';

                $claimGoodsList[$bdSno]['claimType'] = $each['claimBoardType'];

                if( 3 == $changeBoardData['replyStatus'] ){
                    $claimGoodsList[$bdSno]['claimStatus'] = 2;
                    $claimGoodsList[$bdSno]['claimCompleteDt'] = gd_date_format('Y-m-d',$each['regDt']);
                }else{
                    $claimGoodsList[$bdSno]['claimStatus'] = 1;
                }
                $claimGoodsList[$bdSno]['claimRegDt'] = gd_date_format('Y-m-d',$each['regDt']);

                $goodsInfo = DBUtil2::getOne(DB_GOODS, 'goodsNo', $each['goodsNo']);
                $claimGoodsList[$bdSno]['scmNo'] = $goodsInfo['scmNo'];
                $bdInfo = DBUtil2::getOne('es_bd_qa','sno',$bdSno);
                $memo = str_replace('exchange', '(구)교환/반품/환불 게시판' , $bdInfo['subSubject']);
                $memo = str_replace('back', '(구)교환/반품/환불 게시판' , $memo);
                $memo = str_replace('refund', '(구)교환/반품/환불 게시판' , $memo);
                $memo = str_replace('asboard', '(구)AS 게시판' , $memo);
                $claimGoodsList[$bdSno]['memo'] = $memo.'번';
            }

            $claimGoodsList[$bdSno]['claimGoods'][$goodsNo]['goodsNo'] = $each['goodsNo'];
            $claimGoodsList[$bdSno]['claimGoods'][$goodsNo]['option'][] = [
                'optionNo' => $each['optionIdx'],
                'optionName' => $each['optionName'],
                'optionCnt' => $each['optionCount'],
            ];
            $claimGoodsList[$bdSno]['claimGoods'][$goodsNo]['optionTotalCount'] += $each['optionCount'];
            $claimGoodsList[$bdSno]['claimGoodsCnt'] += $each['optionCount'];
        }

        foreach( $claimGoodsList as $claimGoodsKey => $claimGoods ){
            $refineClaimGoods = [];
            foreach($claimGoods['claimGoods'] as $each){
                $refineClaimGoods[] = $each;
            }
            $claimGoodsList[$claimGoodsKey]['claimGoods'] = json_encode($refineClaimGoods);
            DBUtil2::insert('sl_scmClaimData', $claimGoodsList[$claimGoodsKey]);
        }

        //gd_debug( $claimGoodsList );

    }

}