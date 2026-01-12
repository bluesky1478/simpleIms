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

namespace Controller\Admin\Work;

use Component\Work\DocumentCodeMap;
use SiteLabUtil\SlCommonUtil;
use SlComponent\Database\DBUtil2;
use SlComponent\Database\SearchVo;
use SlComponent\Util\SitelabLogger;
use SlComponent\Util\SlControllerTrait;
use SlComponent\Util\SlLoader;
use Exception;
use SlComponent\Util\SlProjectCodeMap;

/**
 * Class 통계 엑셀 요청 처리 컨트롤러
 * @package Bundle\Controller\Admin\Order
 * @author  sueun
 */
class ProjectPsController extends \Controller\Admin\Controller{

    use SlControllerTrait;

    private $projectService;

    public function index() {
        $this->projectService = SlLoader::cLoad('work','projectService','');
        $this->runMethod(get_class_methods(__CLASS__));
    }

    /**
     * 프로젝트 저장
     * @param $param
     * @throws Exception
     */
    public function saveProject($param){
        if( empty( $param['projectName'] )) {
            throw new Exception("프로젝트명은 필수 입니다.");
        }

        $this->projectService->saveProject($param);

        $this->setJson(200, '저장되었습니다.');
    }

    /**
     * 프로젝트 정보 가져오기
     * @param $param
     */
    public function getProjectData($param){
        //SitelabLogger::logger(' get Project BEGIN ');
        //SitelabLogger::logger($param);
        $data = $this->projectService->getProjectDataWithDocument($param['sno']);
        //SitelabLogger::logger($data);
        //SitelabLogger::logger(' COMPLETE ');
        $this->setJson(200, '조회 되었습니다.', $data);
    }


    /**
     * 문서 type 반환
     * @param $param
     */
    public function getDocType($param){
        $docList = [];
        foreach( SlProjectCodeMap::PRJ_DOCUMENT[$param['docDept']]['typeDoc'] as $key => $value ){
            $docList[$key] = $value['name'];
        }
        $this->setJson(200, '정상조회 됨', $docList);
    }

    /**
     * 실시간 업데이트 설정
     * @param $param
     * @throws \Exception
     */
    public function updateResponseData($param){
        $result = [];
        $result['sno'] = $param['sno'];

        $saveData[$param['key']] = $param['value'];
        $searchVo = new SearchVo('sno=?' , $param['sno'] );
        $oldData = DBUtil2::getOneBySearchVo('sl_workRequest', $searchVo);

        $saveData[$param['key']] = $param['value'];

        if( 'isProcFl' == $param['key'] && 'y' == $param['value'] && 'y' != $oldData['isProcFl']  ) {
            $saveData['procDt'] = 'now()';
            $result['procDt'] = date('Y-m-d');
        }else if( 'isProcFl' == $param['key'] && 'n' == $param['value'] && 'n' != $oldData['isProcFl']  ){
            $saveData['procDt'] = '';
            $result['procDt'] = '';
        }else{
            $result['procDt'] = $oldData['prodDt'];
        }
        DBUtil2::update('sl_workRequest', $saveData, $searchVo );

        $this->setJson(200, '수정완료', $result);
    }


    /**
     * 승인라인 저장.
     * @param $param
     * @throws Exception
     */
    public function saveAccept($param){
        if( empty($param['sno']) ){
            DBUtil2::insert('sl_workAcceptLine', $param);
        }else{
            $sno = $param['sno'];
            unset($param['sno']);
            DBUtil2::update('sl_workAcceptLine', $param, new SearchVo('sno=?',$sno) );
        }
        $this->setJson(200, '처리완료', $result);
    }

    /**
     * 승인라인 삭제
     * @param $param
     * @throws Exception
     */
    public function removeAccept($param){
        $sno = $param['sno'];
        unset($param['sno']);
        DBUtil2::delete('sl_workAcceptLine', new SearchVo('sno=?',$sno) );
        $this->setJson(200, '처리완료', $result);
    }




}
