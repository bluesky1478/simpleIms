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
 * @link      http://www.godo.co.kr
 */
namespace Component\Order;

use App;
use Component\Mail\MailAutoObserver;
use Component\Godo\NaverPayAPI;
use Component\Member\Member;
use Component\Naver\NaverPay;
use Component\Database\DBTableField;
use Component\Delivery\OverseasDelivery;
use Component\Deposit\Deposit;
use Component\ExchangeRate\ExchangeRate;
use Component\Mail\MailMimeAuto;
use Component\Mall\Mall;
use Component\Mall\MallDAO;
use Component\Member\Manager;
use Component\Member\Util\MemberUtil;
use Component\Mileage\Mileage;
use Component\Policy\Policy;
use Component\Sms\Code;
use Component\Sms\SmsAuto;
use Component\Sms\SmsAutoCode;
use Component\Sms\SmsAutoObserver;
use Component\Validator\Validator;
use Component\Goods\SmsStock;
use Component\Goods\KakaoAlimStock;
use Component\Goods\MailStock;
use Encryptor;
use Exception;
use Framework\Application\Bootstrap\Log;
use Framework\Debug\Exception\AlertOnlyException;
use Framework\Debug\Exception\AlertRedirectException;
use Framework\Helper\MallHelper;
use Framework\Utility\ArrayUtils;
use Framework\Utility\ComponentUtils;
use Framework\Utility\NumberUtils;
use Framework\Utility\StringUtils;
use Framework\Utility\UrlUtils;
use Globals;
use Logger;
use LogHandler;
use Request;
use Session;
use Framework\Utility\DateTimeUtils;
use SiteLabUtil\SlCommonUtil;
use SlComponent\Database\DBConst;
use SlComponent\Database\DBUtil;
use SlComponent\Database\DBUtil2;
use SlComponent\Database\SearchVo;
use SlComponent\Database\TableVo;
use SlComponent\Godo\ScmService;
use SlComponent\Util\SitelabLogger;
use SlComponent\Util\SlCodeMap;
use Component\Storage\Storage;
use SlComponent\Util\SlKakaoUtil;
use SlComponent\Util\SlLoader;
use SlComponent\Util\SlSmsUtil;


/**
 * 관리자 주문 리스트 Trait
 */
trait OrderAdminListTrait{

    //영구크린 ( 주문건당 5천원 / 세척제의 경우 수량당 3천원 처리 )
    public function setAddCustomField12(){
        return "12 as customDeliveryCost, 0 as customWorkCost";
    }
    //무영건설 ( 주문건당 4천원 )
    public function setAddCustomField10(){
        return "4000 as customDeliveryCost, 0 as customWorkCost";
    }
    //TKE 배송비 ( 폴리백 -> 주문건당 : 4,000원 / 주문건당 : 6,500원 )
    //TKE 작업비 ( 1주문건 3장 이하 : 1천원 / 3장 초과 시 1천원 + 초과 장당 130원  )
    public function setAddCustomField8(){
        return "8 as customDeliveryCost, 0 as customWorkCost";
    }

    public function setDecorationList($list){
        //SitelabLogger::logger($list);
    }

    /**
     * 관리자 리스트 튜닝 1
     * @param $join
     * @param $addField
     * @param $searchData
     */
    public function setListTuneBegin(&$join, &$addField, $searchData){

        $isAcceptJoin = false;

        foreach( $addField as $addEach ){
            foreach( $addEach as $addEach2 ){
                if($addEach2 === 'oac.orderAcctStatus AS orderAcctStatusCode'){
                    $join[] = ' LEFT JOIN sl_orderAccept oac ON og.orderNo = oac.orderNo ';
                    $join[] = ' LEFT JOIN sl_setScmConfig scon ON scon.scmNo = og.scmNo ';
                    $isAcceptJoin = true;
                    break;
                }
            }
        }

        $addField[] = ["sorder.giftAmount"];
        $addField[] = ["sorder.addDeposit"];
        $addField[] = ["sorder.reqDeliveryDt"];
        $addField[] = ["sorder.memberType"];
        $addField[] = ["sorder.sno as sOrderNo"];
        $addField[] = ["dl.orderDeliveryName as deliverySubject"];
        $addField[] = ["o.userConsultMemo"];
        $addField[] = ["oex.deliveryBoxType"];

        $addField[] = ["concat(pcl.productName,'_',pcl.optionName) as customPrdName"];
        $addField[] = ["pcl.scmName as customScmName"];

        //Function 있는지 체크하고 처리.
        //$setAddCustomFieldFunctionName = 'setAddCustomField'.$searchData['scmNo'][0];
        /*if( !empty(method_exists($this, $setAddCustomFieldFunctionName ) )){
            $addField[] = [$this->$setAddCustomFieldFunctionName()];
        }*/
        $addField[] = ["case when (og.scmNo = 8)  then 8 
                             when (og.scmNo = 10) then 4000 
                             when (og.scmNo = 12) then 12 
                             else '0' end as customDeliveryCost
                             , 0 as customWorkCost
                      "];
        $join[] = ' LEFT JOIN sl_orderAddedData as sorder ON o.orderNo = sorder.orderNo ';

        //$join[] = ' LEFT OUTER JOIN sl_setScmDeliveryList as dl ON dl.receiverAddress = oi.receiverAddress ';
        $join[] = ' LEFT OUTER JOIN sl_orderSelectedDeliveryName as dl ON dl.orderNo = o.orderNo';

        $join[] = ' LEFT OUTER JOIN sl_orderExtend as oex ON o.orderNo = oex.orderNo ';

        $join[] = ' LEFT OUTER JOIN es_goodsOption as ego ON ego.sno = og.optionSno '; //추가..230105
        $join[] = ' LEFT OUTER JOIN sl_3plProduct as pcl ON pcl.thirdPartyProductCode = ego.optionCode ';
        //$join[] = ' LEFT OUTER JOIN sl_scmPrdCodeList as pcl ON pcl.prdCode = ego.optionCode ';

        //튜닝 추가
        if( empty($searchData['hankookMasterFl']) || 'all' == $searchData['hankookMasterFl'] ){
            $this->checked['hankookMasterFl']['all'] ='checked="checked"';
        }else{
            $this->checked['hankookMasterFl'][$searchData['hankookMasterFl']] ='checked="checked"';
            if( 1 == $searchData['hankookMasterFl']  ){
                $this->arrWhere[] = " sorder.sno > 0 AND og.scmNo = 6 ";
            }else{
                $this->arrWhere[] = " sorder.sno is null  ";
            }
        }

        if( !isset($searchData['hankookMasterOpenFl']) || 'all' == $searchData['hankookMasterOpenFl'] ){
            $this->checked['hankookMasterOpenFl']['all'] ='checked="checked"';
        }else{
            $this->checked['hankookMasterOpenFl'][$searchData['hankookMasterOpenFl']] ='checked="checked"';
            $this->arrWhere[] = " sorder.memberType = '{$searchData['hankookMasterOpenFl']}' AND og.scmNo = 6 ";
        }

        if( 'y' === $searchData['isAcceptOnly'] ){
            $this->checked['isAcceptOnly']['y'] = 'checked';
            if( !$isAcceptJoin ){
                $join[] = ' LEFT JOIN sl_orderAccept oac ON og.orderNo = oac.orderNo ';
                $join[] = ' LEFT JOIN sl_setScmConfig scon ON scon.scmNo = og.scmNo ';
                $isAcceptJoin = true;
            }
            $this->arrWhere[] = " ( oac.orderAcctStatus = '2' or scon.orderAcceptFl <> 'y' ) ";
        }else{
            $this->checked['isAcceptOnly']['y'] = '';
        }

        if( 'y' === $searchData['isOrderOnly'] ){
            $this->checked['isOrderOnly']['y'] = 'checked';
            $this->arrWhere[] = " og.handleSno ";
        }else{
            $this->checked['isOrderOnly']['y'] = '';
        }

        $this->search['reqDeliveryDt'] = gd_isset($searchData['reqDeliveryDt']);
        if( !empty($searchData['reqDeliveryDt']) ){
            $this->arrWhere[] = " ( '{$searchData['reqDeliveryDt']}' >= sorder.reqDeliveryDt or sorder.reqDeliveryDt is null ) ";
        }

        if( empty($searchData['memberType']) || 'all' == $searchData['memberType'] ){
            $this->checked['memberType']['all'] ='checked="checked"';
        }else{
            $this->checked['memberType'][$searchData['memberType']] ='checked="checked"';
            $join[] = ' LEFT OUTER JOIN sl_setMemberConfig smc ON o.memNo = smc.memNo ';
            $this->arrWhere[] = " smc.memberType ={$searchData['memberType']}  ";
        }

        $this->search['memberBranch'] = $searchData['memberBranch'];
        if( !empty( $searchData['memberBranch'] ) ){
            $join[] = ' JOIN sl_orderScm oscm ON o.orderNo = oscm.orderNo ';
            $join[] = ' JOIN sl_branchDept bd ON oscm.branchDept = bd.sno ';
            $this->arrWhere[] = " bd.branch = '{$searchData['memberBranch']}'  ";
        }
        $this->search['branchDept'] = $searchData['branchDept'];
        if( !empty( $searchData['branchDept'] ) ){
            $this->arrWhere[] = " oscm.branchDept = '{$searchData['branchDept']}'  ";
        }

        if( empty($searchData['orderAcctStatus']) || 'all' == $searchData['orderAcctStatus'] ){
            $this->checked['orderAcctStatus'][''] ='checked="checked"';
        }else{
            //gd_debug( $searchData['orderAcctStatus'] );
            $this->checked['orderAcctStatus'][$searchData['orderAcctStatus']] ='checked="checked"';
            if( !$isAcceptJoin ){
                $join[] = ' LEFT JOIN sl_orderAccept oac ON og.orderNo = oac.orderNo ';
                $join[] = ' LEFT JOIN sl_setScmConfig scon ON scon.scmNo = og.scmNo ';
            }
            $this->arrWhere[] = " oac.orderAcctStatus = {$searchData['orderAcctStatus']}  ";
            $this->arrWhere[] = " scon.orderAcceptFl = 'y'  ";
        }

        if( empty($searchData['userConsultMemo'])){
            $this->checked['userConsultMemo'][''] ='checked="checked"';
        }else{
            $this->checked['userConsultMemo'][$searchData['userConsultMemo']] ='checked="checked"';
            if( '1' == $searchData['userConsultMemo'] ){
                $this->arrWhere[] = " o.userConsultMemo = '1'  ";
            }else{
                $this->arrWhere[] = " o.userConsultMemo <> '1' ";
            }
        }

    }

    /**
     * 삼영출고(주문수량통합) 체크
     * @param $excelField
     * @return bool
     */
    public function isGroupCountList($excelField){
        $compareMap = [
            'receiverName',
            'receiverZonecode',
            'receiverAddressTotal',
            'receiverPhone',
            'receiverCellPhone',
            'orderMemo',
            'optionCode',
            'goodsNm',
            'optionInfo',
            'goodsCnt',
        ];
        $rslt = true;
        foreach($compareMap as $key => $value){
            if( $excelField[$key] !== $value ){
                $rslt = false;
                break;
            }
        }

        return $rslt;
    }

    public function setGroupCountList($join){
        $join[] = " LEFT OUTER JOIN sl_setScmDeliveryList AS sd ON oi.receiverAddress = sd.receiverAddress";
        $this->db->strJoin = implode('', $join);

        //Field : 'concat(oi.receiverAddress, oi.receiverAddressSub) as receiverAddressTotal',
        //Field : '' as orderMemo',
        $selectFieldList = [
            'sd.receiverName',
            'sd.receiverZonecode',
            'sd.receiverAddress',
            'IF( \'-\' = sd.receiverAddressSub, \'\', sd.receiverAddressSub ) AS receiverAddressSub ',
            '\'\' AS receiverPhone',
            'sd.receiverCellPhone',
            '(og.optionInfo) AS optionInfo ',
            '(og.goodsNm) AS goodsNm ',
            'sum(og.goodsCnt) AS goodsCnt',
            'sd.subject AS orderMemo'
        ];
        $groupFieldList = [
            'sd.receiverName',
            'sd.receiverZonecode',
            'sd.receiverAddress',
            'sd.receiverAddressSub',
            'sd.receiverCellPhone',
            'optionInfo ',
            'goodsNm ',
            'sd.subject'
        ];
        $this->db->strField = implode(',',$selectFieldList);
        //$this->db->strGroup = 'CONCAT('.implode(',',$groupFieldList).')';
        $this->db->strGroup = implode(',',$groupFieldList);
    }

}