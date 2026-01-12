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

namespace Component\Ims;

use Component\Database\DBTableField;
use Component\Work\DocumentCodeMap;
use Component\Work\WorkCodeMap;
use Request;
use SiteLabUtil\SlCommonUtil;
use SlComponent\Database\DBUtil2;
use SlComponent\Database\SearchVo;
use SlComponent\Mail\SiteLabMailUtil;
use SlComponent\Util\SitelabLogger;
use SlComponent\Util\SlLoader;

class ImsCustomerEstimateService {

    use ImsServiceConditionTrait;
    use ImsServiceSortTrait;

    public function __construct(){
        $this->sql = SlLoader::sqlLoad(__CLASS__, false);
    }

    /**
     * 기본 데이터
     * @return array
     */
    public static function getDefaultData(){
        $data = DBTableField::getTableBlankData('tableImsCustomerEstimate'); //초기 데이터.
        $data['receiverInfo'] = [ SlCommonUtil::setListEmptyValue(ImsJsonSchema::CUSTOMER_ESTIMATE_RECEIVER) ];
        $data['contents'] = [ SlCommonUtil::setListEmptyValue(ImsJsonSchema::CUSTOMER_ESTIMATE_PRD) ];
        $data['estimateDt'] = date('Y-m-d');//오늘 날짜
        $data['estimateType'] = 'estimate'; //기본 가견적
        $data['estimateManagerSno'] = SlCommonUtil::getManagerSno();
        unset($data['sno']);
        unset($data['regDt']);
        unset($data['modDt']);

        return $data;
    }

    /**
     * 고객 견적 상세
     * @param $params
     * @return array|mixed
     */
    public function getCustomerEstimate($params){
        if(empty($params['sno'])){
            $data = self::getDefaultData();
        }else{
            $data = $this->getListCustomerEstimate(['condition' => ['sno' => $params['sno']]])['list'][0];
            //$data = DBTableField::refineGetData(ImsDBName::CUSTOMER_ESTIMATE, $data);
        }
        return $data;
    }

    /**
     * 고객 견적 리스트
     * @param $params
     * @return array
     */
    public function getListCustomerEstimate($params){
        $searchVo = new SearchVo();
        $totalSearchVo = new SearchVo ();
        $this->setCommonCondition($params['condition'], $searchVo);
        $this->setCommonCondition($params['condition'], $totalSearchVo);
        $this->setListSort($params['condition']['sort'], $searchVo);

        $searchData = [
            'page' => gd_isset($params['condition']['page'],1),
            'pageNum' => gd_isset($params['condition']['pageNum'],100),
        ];
        $allData = DBUtil2::getComplexListWithPaging($this->sql->getCustomerEstimateTable(), $searchVo, $searchData);
        $list = SlCommonUtil::setEachData($allData['listData'], $this, 'decorationCustomerEstimate');

        $pageEx = $allData['pageData']->getPage('#');

        return [
            'pageEx' => $pageEx,
            'page' => $allData['pageData'],
            'list' => $list
        ];
    }

    /**
     * 고객 견적 데이터 꾸미기
     * @param $each
     * @param null $key
     * @param null $mixData
     * @return array|mixed
     */
    public function decorationCustomerEstimate($each, $key=null, $mixData=null){
        $each = DBTableField::refineGetData(ImsDBName::CUSTOMER_ESTIMATE, $each);
        $each['key'] = SlCommonUtil::aesEncrypt($each['sno']);
        if( !empty($each['regManagerCellPhone']) ){
            $each['regManagerCellPhone'] = SlCommonUtil::getCellPhoneFormat($each['regManagerCellPhone']);
        }
        return SlCommonUtil::setDateBlank($each);
    }


    /**
     * 고객 견적 저장 및 발송
     * @param $params
     * @return mixed
     * @throws \Exception
     */
    public function sendCustomerEstimate($params){
        //SitelabLogger::logger2(__METHOD__, '====> DEBUG>>>>');
        //SitelabLogger::logger2(__METHOD__, $params);

        $mailUtil = SlLoader::cLoad('mail','SiteLabMailUtil','sl');
        $customerData = DBUtil2::getOne(ImsDBName::CUSTOMER, 'sno', $params['customerSno']);

        //우선 저장
        $saveData = $params;
        unset($saveData['isUpdate']);
        $sno = $this->saveCustomerEstimate($saveData);

        if(empty($sno)){
            SitelabLogger::logger2(__METHOD__, '========= 견적 오류 체크 (파라미터 체크) ==========');
            SitelabLogger::logger2(__METHOD__, $saveData);
            throw new \Exception('견적서 등록 오류 (개발팀 문의)');
        }

        $key = SlCommonUtil::aesEncrypt($sno);

        //견적 발송 (메일 발송)
        foreach($params['receiverInfo'] as $idx => $receiver){
            $receiverName = urlencode($receiver['name'].' '.$receiver['position']);

            $mailData['subject'] = $params['subject'];
            $mailData['from'] = 'innover@msinnover.com';
            $mailData['to'] = $receiver['mail'];

            if(0 == $idx){
                $mailData['cc'] = 'syhan@msinnover.com';
            }else{
                $mailData['cc'] = null;
            }

            $host = SlCommonUtil::getHost();
            $replace['rc_companyName'] = $customerData['customerName'];
            $replace['rc_confirmFullUrl'] = "{$host}/ics/customer_estimate.php?key={$key}&receiver={$receiverName}";
            $replace['rc_confirmUrl'] = '견적서 확인 하기';

            $mailData['body'] = $mailUtil->getMailTemplate($replace,'work_estimate.php');
            $mailUtil->send($mailData['subject'], $mailData['body'], $mailData['from'], $mailData['to'], null, $mailData['cc']);
            //SitelabLogger::logger2(__METHOD__, '메일 발송 정보 확인'); //메일발송 내용.
            //SitelabLogger::logger2(__METHOD__, $mailData); //메일발송 내용.
        }

        //업데이트 여부.
        if( 'true' == $params['isUpdate']){
            foreach($params['contents'] as $prd){
                DBUtil2::update(ImsDBName::PRODUCT, [
                    'salePrice' => $prd['unitPrice']
                    ,'prdExQty' => $prd['qty']
                ], new SearchVo('sno=?', $prd['styleSno']));
            }

            //만약 확정상태라면 확정 풀기.
            if( 'p' === $params['prdPriceApproval'] ){
                DBUtil2::update(ImsDBName::PRODUCT, ['priceConfirm'=>'n','priceConfirmDt'=>''], new SearchVo('projectSno=?', $params['projectSno']));
                DBUtil2::update(ImsDBName::PROJECT, ['prdPriceApproval'=>'r'], new SearchVo('sno=?', $params['projectSno']));
                //최신 결재자.
                $request = DBUtil2::getOneSortData(ImsDBName::TODO_REQUEST, " todoType='approval' and approvalType='salePrice' and projectSno=?", $params['projectSno'], 'regDt desc');
                DBUtil2::update(ImsDBName::TODO_REQUEST, ['approvalStatus'=>'proc'], new SearchVo('sno=?', $request['sno']));
                DBUtil2::update(ImsDBName::TODO_RESPONSE, ['completeDt'=>'','status'=>'proc'], new SearchVo('reqSno=?', $request['sno']));
            }
        }

        return $sno;
    }


    /**
     * 고객 견적 저장
     * @param $params
     * @return mixed
     * @throws \Exception
     */
    public function saveCustomerEstimate($params){
        $saveData = DBTableField::checkRequiredWithJsonEncode(ImsDBName::CUSTOMER_ESTIMATE, $params);
        $saveData['regManagerSno'] = SlCommonUtil::getManagerSno();
        unset($saveData['mode']);
        //SitelabLogger::logger2(__METHOD__, '저장데이터 확인 결과');
        //SitelabLogger::logger2(__METHOD__, $saveData);
        return DBUtil2::insert(ImsDBName::CUSTOMER_ESTIMATE, $saveData);
    }

    /**
     * 상태변경.
     * @param $params
     * @throws \Exception
     */
    public function setApprovalCustEstimate($params){
        $updateData = SlCommonUtil::getAvailData($params,[
            'approvalStatus',
            'approvalName',
        ]);

        $custEstimateData = $this->getCustomerEstimate(['sno'=>$params['sno']]);

        $mainContents = [];
        $mainContents[] = '고객명 : '.$custEstimateData['customerName'];
        $mainContents[] = '프로젝트번호 : '.$custEstimateData['projectNo'];

        //SitelabLogger::logger2(__METHOD__, $params);
        if( 'p' === $params['approvalStatus'] ){ //승인
            if( empty($params['approvalName']) ){
                throw new \Exception('승인자명은 필수 입니다.');
            }
            $updateData['approvalDt'] = 'now()';
            foreach($custEstimateData['contents'] as $prd){
                DBUtil2::update(ImsDBName::PRODUCT,[
                    'priceCustConfirm' => 'p', //고객 승인
                    'priceCustConfirmDt' => 'now()', //승인일자
                    'priceApprovalName' => $params['approvalName'], //승인자
                ], new SearchVo('sno=?', $prd['styleSno']));
            }

            //영업에게 알림 메일 발송
            $mainContents[] = $params['approvalName'].'님이 전달한 견적서를 승인 했습니다.';
            $targetManagerInfo = DBUtil2::getOne(DB_MANAGER, 'departmentCd', '02001001'); //영업에게 메일 알림
            $targetEmail=[];
            //이메일 리스트
            if (!empty($targetManagerInfo['email'])) {
                $targetEmail[] = $targetManagerInfo['email'];
            }
            $to = implode(',',$targetEmail);
            if(!empty($to)){
                SiteLabMailUtil::sendSystemMail('고객 견적 승인 알림', implode('<br>',$mainContents), $to);
            }
        }
        if( 'f' === $params['approvalStatus'] ){ //재요청
            if( empty($params['approvalName']) ){
                throw new \Exception('재요청 사유는 필수 입니다.');
            }
            $updateData['approvalDt'] = 'now()';
            $mainContents[] = $params['approvalName'].'과(와) 같은 사유로 견적서를 재요청 합니다.';
            $mainContents[] = '참고 : 고객에게는 TODO LIST의 댓글/내용은 보여지지 않습니다.';

            $hopeDt = SlCommonUtil::getDateCalc(date('Y-m-d'), 5);
            $this->saveCustomerTodo($hopeDt, '고객 견적서 재요청', implode('<br>',$mainContents), $custEstimateData);
        }
        DBUtil2::update(ImsDBName::CUSTOMER_ESTIMATE, $updateData, new SearchVo('sno=?', $params['sno']));
    }

    /**
     * 고객의 TO-DO 요청
     * @param $hopeDt
     * @param $subject
     * @param $contents
     * @param $data
     */
    public function saveCustomerTodo($hopeDt, $subject, $contents, $data){
        $requestData = [
            'todoType' => 'todo',
            'hopeDt' => $hopeDt,
            'subject' => $subject,
            'contents' => $contents,
            'customerSno' => $data['customerSno'],
            'projectSno' => $data['projectSno'],
            'regManagerSno' => -1,
        ];

        $sno = DBUtil2::insert(ImsDBName::TODO_REQUEST, $requestData);

        $responseData = [
            'reqSno' => $sno,
            'managerSno' => '02001001', //영업에 뿌려준다.
            'expectedDt' => $hopeDt,
        ];
        DBUtil2::insert(ImsDBName::TODO_RESPONSE, $responseData);
    }

}
