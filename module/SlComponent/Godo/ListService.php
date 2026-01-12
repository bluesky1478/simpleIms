<?php
namespace SlComponent\Godo;

use SiteLabUtil\SlCommonUtil;
use SlComponent\Util\SitelabLogger;

/**
 * 리스트 처리 서비스
 * 중복 코드 제거 / 모모티에도 적용 여부 판단하기
 * @package SlComponent\Godo
 */
class ListService {

    public function setList( $listService, $controller ){

        $requestParam = \Request::request()->toArray();

        $refineRequestParam = SlCommonUtil::getRefineValueAndExcelDownCheck($requestParam);

        $getData = $listService->getList($refineRequestParam);

        if( !empty($requestParam['simple_excel_download']) && !empty($requestParam['detailKey']) ){
            $controller->simpleExcelDownloadDetail($getData);
            exit();
        }else if(!empty($requestParam['simple_excel_download'])){
            $controller->simpleExcelDownload($getData);
            exit();
        }

        //라디오 체크
        $controller->setData('checked', $getData['checked']);

        //검색정보
        $controller->setData('search', $getData['search']);

        //페이지
        $controller->setData('page', $getData['page']);

        //타이틀
        $controller->setData('listTitles',$listService->getTitle());

        //리스트 데이터
        $controller->setData('data',$getData['data']);

        $controller->setData('listAllData',$getData); //전체 데이타

        $controller->setData('requestUrl', basename(\Request::getPhpSelf()) .'?simple_excel_download=1&'.  \Request::getQueryString() );
    }


}
