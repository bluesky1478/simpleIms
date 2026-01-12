<?php
namespace Component\Ims;

use App;
use Component\Database\DBTableField;
use Component\Ims\EnumType\APPROVAL_STATUS;
use Component\Ims\EnumType\TODO_STATUS;
use Component\Ims\EnumType\TODO_TYPE;
use Component\Member\Manager;
use Component\Member\Member;
use Component\Sms\Code;
use Framework\Debug\Exception\AlertBackException;
use Framework\Utility\DateTimeUtils;
use LogHandler;
use Request;
use Session;
use SiteLabUtil\SlCommonUtil;
use SlComponent\Database\DBUtil2;
use SlComponent\Database\SearchVo;
use SlComponent\Mail\SiteLabMailUtil;
use SlComponent\Util\SitelabLogger;
use SlComponent\Util\SlCodeMap;
use SlComponent\Util\SlKakaoUtil;
use SlComponent\Util\SlLoader;
use SlComponent\Util\SlSmsUtil;

/**
 * IMS 발송 서비스
 * Class GoodsStock
 * @package Component\Goods
 */
trait ImsSendTrait {

    /**
     * 결재 관련 메일 발송
     * @param $sno
     * @param $message
     * @param string $sendTargetType
     */
    public function sendApprovalMsg($sno, $message, $sendTargetType='target'){
        $appData = $this->getApprovalData(['sno' => $sno]);
        $approvalTypeKr = ImsApprovalService::APPROVAL_TYPE[$appData['approvalType']]['name'];

        if( 'target' ==  $sendTargetType ){
            foreach($appData['targetManagerList'] as $appManager){
                if( 'proc' === $appManager['status'] ){
                    $managerInfo = SlCommonUtil::getManagerInfo($appManager['sno']);
                    $subject = "{$managerInfo['managerNm']}님 IMS [({$approvalTypeKr}){$appData['subject']}] " . $message;
                    $msg = "고객명 : {$appData['customerName']}<br>프로젝트번호 : {$appData['projectNo']}<br>요청자 : {$appData['regManagerNm']}";
                    SiteLabMailUtil::sendSystemMail($subject, $subject.'<br><br>'.$msg, $managerInfo['email']);
                    break;
                }
            }
        }

        if( 'req' ==  $sendTargetType ){
            $regManager = SlCommonUtil::getManagerInfo(SlCommonUtil::getManagerSno());
            $managerInfo = SlCommonUtil::getManagerInfo($appData['regManagerSno']);
            //SitelabLogger::logger2(__METHOD__.__LINE__, $managerInfo['email']);

            $subject = "{$managerInfo['managerNm']}님 IMS [({$approvalTypeKr}){$appData['subject']}] " . $message;
            $msg = "고객명 : {$appData['customerName']}<br>프로젝트번호 : {$appData['projectNo']}<br>결재자 : {$regManager['managerNm']}";
            SiteLabMailUtil::sendSystemMail($subject, $subject.'<br><br>'.$msg, $managerInfo['email']);
        }
        //SitelabLogger::logger2(__METHOD__.__LINE__, $managerInfo['email']);
        //TODO 카카오 //TODO : 알림톡으로 변경 혹은 추가
        /*$sendParams=[
            'subject'=>$appData['subject'],
            'managerNm'=>$managerInfo['managerNm'],
            'customerName'=>$appData['customerName'],
            'projectNo'=>$appData['projectNo'],
            'regManagerNm'=>$appData['regManagerNm'],
        ];*/
    }

    /**
     * customerSno
     * projectSno
     * ccList
     * @param $params
     * @throws \Exception
     */
    public function sendMailToCustomer($params){
        $type = $params['type'];
        $map = [
            'meetingReport' => [
                'typeName'  => '회의록',
                'title'     => ' 고객님과의 미팅 회의록을 전달 드립니다.',
                'template'  => 'ims_meeting_report.php',
                'afterFncName'  => '',
            ],
            'sampleGuide' => [
                'typeName'  => '샘플 안내서',
                'title'     => ' 고객님의 샘플 안내서를 전달 드립니다.',
                'template'  => 'ims_sample_guide.php',
                'afterFncName'  => 'afterSampleGuideMailSend', //발송 후 샘플 안내서 완료 처리
            ],
            'proposal' => [
                'typeName'  => '제안서',
                'title'     => ' 고객님의 제안서 전달 드립니다.',
                'template'  => 'ims_proposal.php',
                'afterFncName'  => 'afterProposalMailSend',
            ],
            'designGuide' => [
                'typeName'  => '사양서',
                'title'     => ' 고객님의 사양서를 전달 드립니다.',
                'template'  => 'ims_design_guide.php',
                'afterFncName'  => 'afterDesignGuideMailSend',
            ],
            'assort' => [
                'typeName'  => '아소트',
                'title'     => ' 고객님 발주 수량 (제품 사이즈별 수량) 입력 요청 드립니다.',
                'template'  => 'ims_assort.php',
                'afterFncName'  => 'afterAssortMailSend',
            ],
        ];

        $api = $map[$params['type']];

        if(!isset($params['ccList'])){
            $params['ccList'] = [];
        }
        $params['ccList'][] = SlCommonUtil::getManagerMail();
        $ccList = implode(',',$params['ccList']);

        $mailUtil = SlLoader::cLoad('mail','SiteLabMailUtil','sl');
        $customerData = DBUtil2::getOne(ImsDBName::CUSTOMER, 'sno', $params['customerSno']);

        //메일 발송
        $mailData['subject'] = '(이노버) '. $customerData['customerName'].$api['title'];
        $mailData['from'] = 'innover@msinnover.com';
        $mailData['to'] = $params['email'];

        $fncName = 'get'.ucfirst($type).'Contents';
        $method = SlCommonUtil::getMethodMap($this);
        if(!empty($method[$fncName])){
            $replace = $this->$fncName($params, $customerData);
        }else{
            $replace = $this->getCommonContents($params, $customerData);
        }

        $mailData['body'] = $mailUtil->getMailTemplate($replace,$api['template']);

        $mailUtil->send($mailData['subject'], $mailData['body'], $mailData['from'], $mailData['to'], null, $ccList);

        //발송이력 insert
        $insertHistory = [
            'sendType'=>$api['typeName'],
            'projectSno'=>$params['sno'],
            'sendManagerSno'=>\Session::get('manager.sno'),
            'receiverName'=>$params['receiver'],
            'receiverMail'=>$params['email'],
            'ccList'=>$ccList,
            'subject'=>$mailData['subject'],
            'contents'=>json_encode($params,true),
            'regDt'=>date('Y-m-d H:i:s'),
        ];

        DBUtil2::insert(ImsDBName::SEND_HISTORY, $insertHistory);

        //AfterFnc
        if(!empty($api['afterFncName'])){
            $fncName = $api['afterFncName'];
            $this->$fncName($params);
        }
    }

    /**
     * 파일 컨텐츠
     * @param $params
     * @param $replace
     * @return mixed
     */
    public function getFileListContents($params, $replace){
        //파일 수 만큼 동적으로 만든다 nas url 추가 해서
        $fileUrlList = json_decode($params['fileUrl'],true);
        $linkList = [];
        foreach($fileUrlList as $fileUrl){
            $path = ImsCodeMap::NAS_URL.'/'.$fileUrl['filePath'];
            $name = $fileUrl['fileName'];
            $linkList[] = "<a href='{$path}' target='_blank'>{$name}</a>";
        }
        $replace['rc_fileList'] = implode('<br>',$linkList);
        return $replace;
    }

    /**
     * 기본 컨텐츠
     * @param $params
     * @param $customerData
     * @return mixed
     */
    public function getCommonContents($params, $customerData){
        $replace = [];
        $replace['rc_companyName'] = $customerData['customerName'];
        $host = SlCommonUtil::getHost();
        $replace['rc_mallDomain'] = $host.'/ics25/html/ics_login.php';
        $replace = $this->getFileListContents($params, $replace);
        return $replace;
    }

    /**
     * 사양서 내용 구성
     * @param $params
     * @param $customerData
     * @return mixed
     */
    public function getDesignGuideContents($params, $customerData){
        $key = SlCommonUtil::aesEncrypt($params['sno']);
        $replace = $this->getCommonContents($params, $customerData);
        $host = SlCommonUtil::getHost();
        $replace['rc_confirmUrl'] = "{$host}/ics/ics_guide.php?key={$key}";
        return $replace;
    }

    /**
     * 아소트 내용 구성
     * @param $params
     * @param $customerData
     * @return mixed
     */
    public function getAssortContents($params, $customerData){
        $key = SlCommonUtil::aesEncrypt($params['sno']);
        $replace = $this->getCommonContents($params, $customerData);
        $host = SlCommonUtil::getHost();
        $replace['rc_confirmUrl'] = "{$host}/ics/ics_assort.php?key={$key}";
        return $replace;
    }

    /**
     * 제안서 발송 후처리
     * @param $params
     * @throws \Exception
     */
    public function afterProposalMailSend($params){
        DBUtil2::update(ImsDBName::PROJECT, ['proposalDt'=>'now()'], new SearchVo('sno=?', $params['sno']));
    }

    /**
     * 사양서 발송 후 처리
     * @param $params
     * @throws \Exception
     */
    public function afterDesignGuideMailSend($params){
        $searchVo = new SearchVo('projectSno=?', $params['sno']);
        $searchVo->setWhere(" (cpOrderSend = '0000-00-00' OR cpOrderSend is null) ");
        DBUtil2::update(ImsDBName::PROJECT_EXT, ['stOrderSend'=>4,'cpOrderSend'=>'now()'], $searchVo);
    }

    /**
     * 아소트 발송 후 처리
     * @param $params
     * @throws \Exception
     */
    public function afterAssortMailSend($params){
        //우선 저장
        DBUtil2::update(ImsDBName::PROJECT, [
            'assortApproval' =>'r', //고객 입력요청상태로 변경
            'assortReceiver' =>$params['receiver'],
            'assortEmail' =>$params['email'],
            'assortSendDt' =>'now()',
        ], new SearchVo('sno=?', $params['sno']));

        $searchVo = new SearchVo('projectSno=?', $params['sno']);
        $searchVo->setWhere("4 > stAssortConfirm");
        DBUtil2::update(ImsDBName::PROJECT_EXT, ['stAssortConfirm'=>4], $searchVo); //발송 상태로 변경
    }

    /**
     * 샘플 안내서 발송 후처리
     * 완료일 등록
     * @param $params
     * @throws \Exception
     */
    public function afterSampleGuideMailSend($params){
        $searchVo = new SearchVo('projectSno=?', $params['sno']);
        $searchVo->setWhere("cpSampleGuide = '0000-00-00' OR cpSampleGuide is null");
        DBUtil2::update(ImsDBName::PROJECT_EXT, ['stSampleGuide'=>10,'cpSampleGuide'=>'now()'], $searchVo);
    }


}