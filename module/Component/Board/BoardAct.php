<?php

/**
 * This is commercial software, only users who have purchased a valid license
 * and accept to the terms of the License Agreement can install and use this
 * program.
 *
 * Do not edit or add to this file if you wish to upgrade Smart to newer
 * versions in the future.
 *
 * @copyright ⓒ 2016, NHN godo: Corp.
 * @link http://www.godo.co.kr
 */
namespace Component\Board;

use Bundle\Component\Admin\AdminMenu;
use Component\Member\Util\MemberUtil;
use Component\Goods\AddGoodsAdmin;
use Component\Goods\Goods;
use Component\Mail\MailAuto;
use Component\Order\Order;
use Component\Page\Page;
use Component\Storage\Storage;
use Component\Validator\Validator;
use Component\Database\DBTableField;
use Framework\Debug\Exception\RequiredLoginException;
use Framework\Utility\ArrayUtils;
use Framework\Utility\ImageUtils;
use Framework\Utility\SkinUtils;
use Framework\Utility\StringUtils;
use Request;
use App;
use Session;
use Respect\Validation\Rules\MyValidator;
use SiteLabUtil\SlCommonUtil;
use SlComponent\Database\DBUtil;
use SlComponent\Database\DBUtil2;
use SlComponent\Database\SearchVo;
use SlComponent\Mail\SiteLabMailUtil;
use SlComponent\Util\SitelabLogger;
use SlComponent\Util\SlCodeMap;
use SlComponent\Util\SlKakaoUtil;
use SlComponent\Util\SlLoader;

/**
 * 게시판 처리 Class
 */
class BoardAct extends \Bundle\Component\Board\BoardAct{

    public function saveData(){

        //원본 답변 상태 가져오기
        $orgData = DBUtil2::runSelect("select * from es_bd_qa where sno = " . $this->req['sno'])[0];
        //SitelabLogger::logger($orgData);

        //게시판 정보 저장
        parent::saveData();

        //게시판 추가 정보 저장
        //수정모드일때 실행
        if( 'modify' === $this->req['mode'] && !empty(SlCodeMap::CLAIM_TYPE[$this->req['claimType']]) ){
            //SitelabLogger::logger('=====> 게시판 추가 정보 저장  saveData ');
            $claimBoardService = SlLoader::cLoad('claim','claimBoardService');
            $claimBoardService->saveBoardClaimInfo($this->req);

            //SitelabLogger::logger("테스트~!!!");
            //SitelabLogger::logger("update es_bd_qa set replyStatus = {$orgData['replyStatus']} where sno = {$this->req['sno']}");

            DBUtil2::runSql("update es_bd_qa set replyStatus = '{$orgData['replyStatus']}' where sno = {$this->req['sno']}");
        }
    }

    /**
     * 게시판 저장시 처리
     * @param $data
     * @param null $msgs
     * @throws \Exception
     */
    protected function handleAfterWrite($data, &$msgs = null){
        parent::handleAfterWrite($data, $msgs);
        if( !empty(SlCodeMap::CLAIM_TYPE[$this->req['claimType']]) ){
            //SitelabLogger::logger('=====> 게시판 저장 처리 handleAfterWrite ');
            $claimBoardService = SlLoader::cLoad('claim','claimBoardService');
            $reqDataAddSno = $this->req;
            $reqDataAddSno['sno'] = $data['sno'];//+SNO
            $claimBoardService->saveBoardClaimInfo($reqDataAddSno);
        }

        if( $this->req['bdId'] == 'qa' && !SlCommonUtil::isDev() ){
            $msgParam['writerName'] = $data['writerNm'];
            //1:1문의 알림
            $subject = "(1:1문의) {$data['writerNm']} 님 문의 건";
            $contents = "<br>{$data['subject']}<br>";
            $contents .= "<br>{$data['contents']}<br>";
            $to = implode(',',SlCodeMap::BOARD_MAIL_LIST);
            SiteLabMailUtil::sendSystemMail($subject, nl2br($contents), $to);
            SlKakaoUtil::send(4 , $data['cellPhone'] ,  $msgParam); //문의 등록
        }
    }

}
