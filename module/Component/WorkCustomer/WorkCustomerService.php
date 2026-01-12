<?php
namespace Component\WorkCustomer;

use App;
use Component\Work\WorkCodeMap;
use Session;
use Component\Database\DBTableField;
use Component\Member\Manager;
use Framework\Utility\DateTimeUtils;
use LogHandler;
use Request;
use Exception;
use Framework\Debug\Exception\AlertBackException;
use SiteLabUtil\SlCommonUtil;
use SlComponent\Database\DBUtil;
use SlComponent\Database\DBUtil2;
use SlComponent\Database\SearchVo;
use SlComponent\Database\TableVo;
use SlComponent\Util\SitelabLogger;
use SlComponent\Util\SlCodeMap;
use SlComponent\Util\SlCommonTrait;
use SlComponent\Util\SlLoader;
use Globals;
use Framework\Debug\Exception\AlertCloseException;
use Component\Member\Util\MemberUtil;
use UserFilePath;
use Framework\Utility\NumberUtils;

/**
 * 이노버 거래처 고객 서비스
 */
class WorkCustomerService {
    /**
     * 팝업 데이터 셋팅
     * @param $controller
     * @param $param
     */
    public function setPopView($controller, $param) {
        //title
        $popupTitle = __($param['title']);
        //$documentService = SlLoader::cLoad('work','documentService','');
        $projectService = SlLoader::cLoad('work','projectService','');

        $request = \Request::request()->toArray();
        $projectSno = SlCommonUtil::aesDecrypt( $request['key'] ); //검증용.
        $documentData = $projectService->getProjectDocument( ['sno'=>$request['sno']] );
        //gd_debug( $companySno );
        //gd_debug( $documentData );
        if( $projectSno != $documentData['projectSno']){
            gd_debug('오류 : 관리자에 문의 바랍니다.');
            exit();
        }

        $controller->setData('popupTitle', gd_isset($popupTitle));
        $controller->setData('gMall', gd_htmlspecialchars((Globals::get('gMall'))));
        $controller->setData( 'uriHome' , URI_HOME );
        $controller->setData( 'documentData' , $documentData );
        $controller->setData( 'projectData' , $documentData['projectData'] );
        $controller->setData( 'docData' , $documentData['docData'] );

        $this->setCommonCustomerData($controller, $documentData['projectData'], $request);

    }

    /**
     * @param $controller
     * @param $projectData
     * @param $request
     */
    public function setCommonCustomerData($controller, $projectData, $request){
        if( empty($request['sendNo']) ){
            $companyManager = $projectData['companyManagers'][0];
        }else{
            $mailHistory = DBUtil2::getOne('sl_sendMailHistory', 'sno', $request['sendNo']);
            $companyManager['name'] = $mailHistory['mailReceiverName'];
            foreach($projectData['companyManagers'] as $eachManager){
                if( $eachManager['name'] == $companyManager['name']){
                    $companyManager['phone'] = $eachManager['phone'];
                    $companyManager['cellPhone'] = $eachManager['cellPhone'];
                }
            }
        }

        $controller->setData('companyManager', $companyManager['name']);
        $controller->setData('companyManagerPhone', gd_isset($companyManager['cellPhone'], $companyManager['phone']));
    }


    /**
     * 리스트 셋팅
     * @param $controller
     * @param $dept
     * @param $type
     */
    public function setListData($controller, $dept, $type){
        $request = \Request::request()->toArray();
        $workControllerService=SlLoader::cLoad('work','workControllerService','');
        $workControllerService->setControllerData($controller);

        $projectSno = SlCommonUtil::aesDecrypt($request['key']); //필수.

        $projectService = SlLoader::cLoad('work','projectService','');
        $documentService = SlLoader::cLoad('work','documentService','');

        $projectData = $projectService->getProjectData($projectSno);
        $documentData = $documentService->getLatestDocumentData($dept, $type, $projectSno);

        $controller->setData('projectData', $projectData);
        $controller->setData('documentData', $documentData);
        $controller->setData('docData' , $documentData['docData'] );
        $controller->setData('projectSno' , $projectSno);

        //팝업과 공통으로 사용하는 데이터 셋팅
        $this->setCommonCustomerData($controller, $projectData, $request);

        //리스트 데이터
        $documentList = $documentService->getDocumentList($dept, $type, ['projectSno' =>$projectSno]);
        //gd_debug( $documentList );
        $controller->setData('documentList', $documentList);
    }

    /**
     * VIEW 데이터 셋팅
     * @param $controller
     * @param $sno
     */
    public function setViewData($controller, $sno){
        $projectService = SlLoader::cLoad('work','projectService','');
        $documentData = $projectService->getProjectDocument(['sno'=>$sno]);
        $controller->setData('documentData', $documentData);
        $controller->setData('COMPANY_STEP', WorkCodeMap::COMPANY_STEP);
    }


}
