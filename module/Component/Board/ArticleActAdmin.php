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

use App;
use Component\Mail\MailMimeAuto;
use Framework\StaticProxy\Proxy\Globals;
use Framework\StaticProxy\Proxy\Session;
use Framework\Utility\ImageUtils;
use Logger;
use Request;
use SlComponent\Database\DBUtil;
use SlComponent\Database\DBUtil2;
use SlComponent\Database\SearchVo;
use SlComponent\Util\SitelabLogger;
use SlComponent\Util\SlKakaoUtil;
use SlComponent\Util\SlLoader;

class ArticleActAdmin extends \Bundle\Component\Board\ArticleActAdmin{
    public function saveData(){
        //게시판 정보 저장
        parent::saveData();
        //수정 모드일때 실행 (수정은 고객화면에서 실행)
        /*if( 'modify' === $this->req['mode'] ){
            $claimBoardService = SlLoader::cLoad('claim','claimBoardService');
            $claimBoardService->saveBoardClaimInfo($this->req);
        }*/
    }

    /**
     * 답변
     * @throws \Exception
     */
    public function updateAnswer(){
        parent::updateAnswer();
        $boardData = DBUtil2::getOne('es_bd_'.$this->req['bdId'], 'sno', $this->req['sno']);
        $msgParam['writerName'] = $boardData['writerNm'];
        SlKakaoUtil::send(5 , $boardData['writerMobile'] ,  $msgParam); //답변
    }

}
