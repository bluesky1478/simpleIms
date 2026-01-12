<?php
namespace Component\Work;

use App;
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
use SlComponent\Util\SlProjectCodeMap;

/**
 * 업무공유 시스템 컨트롤러 서비스
 */
class WorkControllerService {

    function setControllerData($controller){
        $workService = SlLoader::cLoad('work','workService','');

        $request = \Request::request()->toArray();

        $controller->setData('requestParam', $request);

        //공통 set Data
        $controller->setData('companyAutoCreateField', [
            ['companyName', '업체명'],
            ['busiNo', '사업자번호'],
            ['ceo', '대표자명'],
            ['service', '업태'],
            ['item', '종목'],
            ['phone', '대표전화'],
            ['fax', 'FAX'],
            ['address', '주소'],
            ['etc', '비고'],
        ]);

        $controller->setData('companyManagerAutoCreateField', [
            ['name', '이름'],
            ['position', '직급'],
            ['phone', '전화번호'],
            ['cellPhone', '휴대전화'],
            ['email', '이메일'],
            ['etc', '비고'],
        ]);

        //선정기준
        $controller->setData('SEL_CRITERIA', [
            '단독', '경쟁', '단가입찰', '미확인'
        ]);
        //선정요소
        $controller->setData('SEL_FACTOR', [
            '디자인', '품질', '단가', '미확인'
        ]);
        //진행형태
        $controller->setData('PROG_MODE', [
            '통일', '개선', '기획', '기성복'
        ]);
        //상중하
        $controller->setData('GRADE_CODE', [
            '상', '중', '하'
        ]);
        //필요 여부
        $controller->setData('NECESSARY_CODE', [
            '필요', '불필요'
        ]);

        //기존샘플제공
        $controller->setData('SAMPLE_SUPPORT', [
            '제공', '미제공', '현장수령'
        ]);
        //샘플비용
        $controller->setData('SAMPLE_COST_TYPE', [
            '무상', '유상'
        ]);

        //진행여부
        $controller->setData('PROC_CODE', [
            '진행', '미진행'
        ]);

        //진행여부
        $controller->setData('PROC_CODE', [
            '진행', '미진행'
        ]);

        // -- 폐쇄몰 등록 정보 -- //
        //DocumentCodeMap::setMallSelectData($this);

        //프로젝트 상태
        $controller->setData('PRJ_STATUS', SlProjectCodeMap::PRJ_STATUS);
        //색상정보
        $controller->setData('COLOR_CODE', SlCommonUtil::getColorList());
        //컨셉정보
        $controller->setData('CONCEPT_CODE', gd_code('05100'));

        $projectSno = gd_isset(  $request['projectSno'] ,0);
        $documentSno = gd_isset(  $request['sno'] ,0);

        if( !empty($documentSno) ){
            $docData = DBUtil2::getOne('sl_workDocument', 'sno', $documentSno);

            $controller->setData('docDept', $docData['docDept']);
            $controller->setData('docType', $docData['docType']);

            $projectSno = $docData['projectSno'];
            if( 'n' == $docData['tempFl'] ){
                $controller->setData('headerSaveButtonName', '수정하기');
                $controller->setData('isHeaderHistoryButton', true); ////Header 저장 버튼 여부
            }else{
                $controller->setData('headerSaveButtonName', '저장하기'); //Header 등록 버튼 여부
                $controller->setData('isTempSaveButtonFl', true);
            }

            //승인자
            ///$acceptData = $documentService->getAcceptLine($docData);
            //$controller->setData('acceptData', $acceptData);
        }else{
            $controller->setData('docDept', $request['docDept']);
            $controller->setData('docType', $request['docType']);
            $controller->setData('headerSaveButtonName', '저장하기'); //Header 등록 버튼 여부
            $controller->setData('isTempSaveButtonFl', true);
        }



        //거래처
        $controller->setData('companyListMap', $workService->getCompanyMap());

        //문서번호(수정시)
        $controller->setData('projectSno', $projectSno );
        $controller->setData('documentSno', $documentSno );
        $controller->setData('selectedCompany' , $request['companySno'] ); //선택된 회사.

        $workFrontURL = \Request::getScheme()."://".\Request::getDefaultHost();
        $controller->setData('workFrontURL', $workFrontURL);
        $controller->setData('myName', \Session::get('manager.managerNm') );
        $controller->setData('mySno', \Session::get('manager.sno') );
        $controller->setData('myDept', WorkCodeMap::DEPT_STR[SlCommonUtil::getManagerInfo(\Session::get('manager.sno'))['departmentCd']]);
        //Link
        $controller->setData('salesLink', json_encode(DocumentCodeMap::DOC_SALES_LINK, JSON_UNESCAPED_UNICODE) );

        $controller->setData('COMPANY_STEP', WorkCodeMap::COMPANY_STEP);
        $controller->setData('PLAN_MOD_REASON_TYPE', WorkCodeMap::PLAN_MOD_REASON_TYPE);

        $controller->setData('secretKey', $request['key']);

        $sampleFactoryData = $workService->getSampleFactoryMap();
        $controller->setData('sampleFactoryData', json_encode($sampleFactoryData, JSON_UNESCAPED_UNICODE) );
        $controller->setData('sampleFactoryList', $sampleFactoryData );
        //gd_debug( $sampleFactoryData );

        $mailLinkData = SlProjectCodeMap::PRJ_DOCUMENT[$controller->getData('docDept')]['typeDoc'][$controller->getData('docType')];
        $linkUrl = gd_isset($mailLinkData['mailLink'], 'wcustomer/index.php');

        $documentCustomerPreviewUrl = URI_HOME.$linkUrl.'?key='.SlCommonUtil::aesEncrypt($projectSno).'&sno='.$documentSno;
        $controller->setData('documentCustomerPreviewUrl', $documentCustomerPreviewUrl);

        $controller->setData('privateMallItem', json_encode(DocumentCodeMap::PRIVATE_MALL_ITEM,JSON_UNESCAPED_UNICODE) );
        $controller->setData('privateMallItemTip', json_encode(DocumentCodeMap::PRIVATE_MALL_ITEM_TIP,JSON_UNESCAPED_UNICODE) );

    }

}
