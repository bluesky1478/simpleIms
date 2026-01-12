<?php

namespace Controller\Admin\Test;

use Component\Database\DBTableField;
use Component\Deposit\Deposit;
use Component\Scm\ScmStockListService;
use Component\Sitelab\SiteLabSmsUtil;
use Component\Work\Code\DocumentDesignCodeMap;
use Component\Work\DocumentCodeMap;
use Component\Work\WorkCodeMap;
use Encryptor;
use Framework\Utility\DateTimeUtils;
use Framework\Utility\NumberUtils;
use Globals;
use Request;
use SiteLabUtil\SlCommonUtil;
use SlComponent\Database\DBUtil;
use SlComponent\Database\DBUtil2;
use SlComponent\Database\SearchVo;
use SlComponent\Util\DocumentStruct;
use SlComponent\Util\ExcelCsvUtil;
use SlComponent\Util\PhpExcelUtil;
use SlComponent\Util\SitelabUtil;
use SlComponent\Util\SlCode;
use SlComponent\Util\SlKakaoUtil;
use SlComponent\Util\SlLoader;
use SlComponent\Util\SlPostRequestUtil;
use SlComponent\Util\SlProjectCodeMap;
use SlComponent\Util\SlSmsUtil;
use UserFilePath;

/**
 * TEST 페이지
 */
class TestForm3Controller extends \Controller\Admin\Controller{

    public function index(){

        gd_debug('없음');
        
        //최근
        //gd_debug('아시아나 데이터 등록');
        gd_debug('현대EL 주문 등록');

        //gd_debug('지금은 처리중인 로직이 없습니다.');
        //gd_debug('TKE 회원 입력');
        //gd_debug('영구크린 수기주문');
        //gd_debug('TKE회원등록');
        //gd_debug('제품 년도 등록');
        //gd_debug('한국타이어 수기 주문');
        //gd_debug('사이즈 스펙 등록');
        //gd_debug('정비복 예치금 추가');
        //gd_debug('원부자재 등록');
        
        $files = Request::files()->toArray();
        gd_debug($files);

        if( !empty($files) ){
            //TODO : 영구크린 수기 주문
            //$ygService = SlLoader::cLoad('scm','ScmYoung9Service');
            //$ygService->setManualOrder($files);

            //TODO : 아시아나 데이터 등록
            $asianaService = SlLoader::cLoad('scm','scmAsianaService');
            //$result = PhpExcelUtil::readToArray($files, 1);
            //$asianaService->insertGoods($files);
            //$asianaService->insertOrderHistory($files);
            //$asianaService->insertAddOrderHistory($files);

            //TODO : 현대 서비스
            $hundaeService = SlLoader::cLoad('scm','scmHyundaeService');
            //$hundaeService->setManualPackingList($files);
            //$hundaeService->setManualPackingListV2($files);
            //$hundaeService->setManualPackingListV1($files);
            $hundaeService->setManualPackingListV5($files);

            //TODO : 한타 예치금 지급
            //$manualService = SlLoader::cLoad('godo','manualService','sl');
            //$manualService->setManualMaterial($files);
            //$manualService->setDeposit($files);  // 한국타이어 예치금 지급(정비복 포인트).

            //$manualService->manualOrderHk($files);
            //$manualService->insertSizeSpec($files);
            //$manualService->insertSizeSpec($files); //사이즈 스펙 추가 ( 새로 추가시 생각해봐야함 ).

            //원부자재 등록
            //$materialService = SlLoader::cLoad('ims','imsMaterialService');
            //$materialService->batchUpload($files);

            //$categoryService = SlLoader::cLoad('ims','imsCategoryService');
            //$categoryService->batchUpload($files);

            /*$files = \Request::files()->toArray();
            $params['instance'] =  SlLoader::cLoad('godo','manualService','sl');
            $params['fnc'] = 'setAttributeYear';
            $params['mixData'] = [
                'excelField' => [
                    'prdCode' => 1,
                ]
            ];
            $result = PhpExcelUtil::runExcelReadAndProcess($files, $params, 1);

            gd_debug($result);*/

            //$tkeService = SlLoader::cLoad('scm','scmTkeService');
            //$tkeService->addMemberTke($files);

            /*$files = \Request::files()->toArray();
            $params['instance'] =  SlLoader::cLoad('erp','erpService');
            $params['fnc'] = 'setYounguOrderTemp';
            $params['mixData'] = [
                'excelField' => [
                    'customerName' => 1,
                    'address' => 2,
                    'phone' => 3,
                    'mobile' => 3,
                    'qtyStr' => 4,
                ]
            ];
            $result = PhpExcelUtil::runExcelReadAndProcess($files, $params, 1);*/


            //$manualService = SlLoader::cLoad('godo','manualService','sl');
            //$result = PhpExcelUtil::readToArray($files, 1);
            //$manualService->manualOrderYounggu($result);

            //gd_debug($result);
            //$manualService->insertResearchTarget($result);
            //$manualService->inputPrdCode($files); //코드삽입
            //$this->checkTkeBuy(); // TKE 회원 특정 상품 구매 현황을 다운로드.
            //$this->joinStatus($files);  // 엑셀에 등록된 폰번호와 이메일로 회원 가입 현황을 다운로드.

            exit();
        }
    }

}