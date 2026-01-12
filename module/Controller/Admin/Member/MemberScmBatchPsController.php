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
namespace Controller\Admin\Member;

use Component\Storage\Storage;
use Framework\Debug\Exception\LayerException;
use Framework\Debug\Exception\LayerNotReloadException;
use Exception;
use Message;
use Request;
use Session;
use SlComponent\Database\DBUtil;
use SlComponent\Database\SearchVo;
use SlComponent\Util\SitelabLogger;

class MemberScmBatchPsController extends \Controller\Admin\Controller{
    public function index(){
            // --- 각 배열을 trim 처리
            $postValue = Request::post()->toArray();
            try {
                $updateData['ex1'] = $postValue['batchScmName'];
                if( empty($postValue['batchScmName']) || 0 >= count($postValue['chk'] )){
                    new Exception('고객사명 선택 필수 , 회원 선택 필수');
                    exit();
                }
                $searchVo = new SearchVo();
                $searchVo->setWhere(DBUtil::bind('memNo', DBUtil::IN, count($postValue['chk']) ));
                $searchVo->setWhereValueArray( $postValue['chk']  );
                DBUtil::update(DB_MEMBER,$updateData,$searchVo);
                $this->json(sprintf(__('고객사연결 완료')));
            } catch (Exception $e) {
                $item = ($e->getMessage() ? ' - ' . str_replace("\n", ' - ', $e->getMessage()) : '');
                throw new LayerException(__('처리중에 오류가 발생하여 실패되었습니다.') . $item);
            }
    }
}
