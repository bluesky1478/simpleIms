<?php

namespace Controller\Admin\Member;

use App;
use Component\Excel\ExcelVisitStatisticsConvert;
use Component\Godo\NaverPayAPI;
use Component\Sms\SmsAutoOrder;
use DB;
use Exception;
use Framework\Debug\Exception\LayerException;
use Framework\Debug\Exception\LayerNotReloadException;
use Message;
use Request;
use SlComponent\Download\SiteLabDownloadUtil;
use SlComponent\Util\SitelabLogger;
use SlComponent\Util\SlLoader;

class MemberCustomPsController extends \Controller\Admin\Controller{
    public function index(){

        $requestData = Request::request()->toArray();
        $memberService = SlLoader::cLoad('godo','memberService','sl');

        switch ($requestData['mode']) {
            case 'add_member_tke':
                //SitelabLogger::logger('TKE 회원 등록 시작');
                $filesValue = Request::files()->toArray();

                $tkeService = SlLoader::cLoad('scm','scmTkeService');
                $tkeService->addMemberTke($filesValue);

                //SitelabLogger::logger('TKE 회원 등록 종료');
                $this->layer(__('추가 완료!'), null, 2000);
                break;
            case 'add_member':
                try {
                    if( !empty($requestData['targetScm']) ){
                        $filesValue = Request::files()->toArray();
                        $memberService->addMember($filesValue, $requestData['targetScm']);
                        $this->layer(__('추가 완료!'), null, 2000);
                    }else{
                        throw new LayerNotReloadException('공급사를 선택해주세요!', null, null, null, 7000);
                    }
                } catch (Exception $e) {
                    if (Request::isAjax()) {
                        $this->json([
                            'code' => 0,
                            'message' => $e->getMessage(),
                        ]);
                    } else {
                        throw new LayerException($e->getMessage(), null, null, null, 7000);
                    }
                }
                break;
            case 'member_sample_down':
                SiteLabDownloadUtil::download('./data/sample/member.xls','회원등록양식.xls');
                break;
            case 'member_sample_down_tke':
                SiteLabDownloadUtil::download('./data/sample/member_tke.xls','회원등록양식_tke.xls');
                break;

        }
    }
}
