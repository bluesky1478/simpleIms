<?php

namespace Controller\Admin\Test;

use App;
use Bundle\Component\CurrencyExchangeRate\CurrencyExchangeRate;
use Bundle\Component\CurrencyExchangeRate\CurrencyExchangeRateAdmin;
use Component\Deposit\Deposit;
use Component\Ims\EnumType\TODO_TYPE2;
use Component\Ims\ImsDBName;
use Component\Sitelab\SiteLabSmsUtil;
use Component\Storage\Storage;
use Component\Work\Code\DocumentDesignCodeMap;
use Component\Work\DocumentCodeMap;
use Component\Work\WorkCodeMap;
use Encryptor;
use Framework\Debug\Exception\LayerException;
use Framework\Security\Digester;
use Framework\Utility\DateTimeUtils;
use Framework\Utility\GodoUtils;
use Framework\Utility\NumberUtils;
use Framework\Utility\StringUtils;
use Globals;
use PhpOffice\PhpSpreadsheet\Cell;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\Html;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Writer\Xls;
use Request;
use SlComponent\Database\DBUtil2;
use SlComponent\Database\SearchVo;
use SlComponent\Util\SitelabUtil;
use SlComponent\Util\SlLoader;
use UserFilePath;

/**
 * TEST 페이지
 */
class CsrController extends \Controller\Admin\Controller{
    /**
     * @throws \Exception
     */
    public function index(){
        gd_debug('CSR처리');

        //$this->hyundaeOrder();

        //원부자재 복사 (프로젝트 번호로)

        //현재 리오더 프로젝트 중  50 . type = 1
        /*$list = DBUtil2::getList(ImsDBName::PROJECT, 'projectType=1 and projectStatus', '50');
        foreach($list as $project){
            if( !empty($project['srcProjectSno']) ){
                $this->copyPrdMaterial($project['sno']);
            }
        }*/

        //$this->copyPrdMaterial(825);
        //$this->copyPrdMaterial(880);
        //$this->copyPrdMaterial(673);
        //$this->copyPrdMaterial(837);

        //원부자재 복사 (스타일 번호로 타겟팅)
        //$eworkService = SlLoader::cLoad('ims','ImsEworkService');
        //$eworkService->copyBeforeStyle(1215 , 4220);
        //$eworkService->copyBeforeStyle(2364, 4108);

        //Test안해봄 ( 프로젝트 번호 단위로 원부자재 복사 )
        //DBUtil2::getList(ImsDBName::PROJECT, '', '');

        //$this->recoveryEworkMaterial(); //원부자재 복구
        //$this->imsWorkStatusSet(765, 'n'); // 작지 전체 풀기`
        //$this->imsWorkStatusSet(607, 'p'); // 작지 전체 승인
        //$this->imsWorkStatusSet(765, 'p'); // 작지 전체 승인

        //$this->asianaChangeEmpCode(); //아시아나 사원코드 변경
        //$this->refineAsianaHistory(); //아시아나 지급이력 정제
        //$this->recoveryImsData();//삭제 자료 복구

        exit();
    }

    public function hyundaeOrder(){
        gd_debug('현대EL처리');

        /*$deleteTime = "2025-10-26 13:40:00";
        $sql = "SELECT DISTINCT orderNo FROM es_orderGoods WHERE regDt = '{$deleteTime}' and scmNo = 32";
        $list = DBUtil2::runSelect($sql);
        $manualService = SlLoader::cLoad('godo','manualService','sl');
        foreach($list as $orderNo){
            $manualService->deleteOrder($orderNo['orderNo']);
        }*/

        gd_debug('지우는건 안함^~^');

        $hyundaeService = SlLoader::cLoad('scm','scmHyundaeService');
        $hyundaeService->createOrders();

        //$managerInfo = SlCommonUtil::getManagerInfo();
        //gd_debug($managerInfo);

        //$order = \App::load(\Component\Order\Order::class);
        //$order->sendOrderInfo('ORDER', 'sms', '2504292039408873');
    }


    /**
     * 상품 이동
     * @param $prdSno
     * @param $moveProjectSno
     */
    public function csrMovePrd($prdSno, $moveProjectSno){
        $imsService = SlLoader::cLoad('ims', 'imsService');
        $imsService->movePrd($prdSno, $moveProjectSno, '');
    }

    /**
     * 원부자재 복사
     * @param $projectSno
     */
    public function copyPrdMaterial($projectSno){
        //원부자재 복사
        $list = DBUtil2::getList(ImsDBName::PRODUCT, 'projectSno', $projectSno);
        foreach($list as $prd){
            //데이터가 없을 때만 넣는다.
            $cnt = DBUtil2::getCount('sl_imsPrdMaterial', new SearchVo('styleSno=?', $prd['sno']));
            if(0 >= $cnt){
                $sql = "insert into sl_imsPrdMaterial(typeStr, styleSno, position, attached, fabricName, fabricMix, color, spec, unit, weight, afterMake, meas, unitPrice, makeNational, makeCompany, memo, sort, cate1, cate2)";
                $sql .= "select typeStr, '{$prd['sno']}', position, attached, fabricName, fabricMix, color, spec, unit, weight, afterMake, meas, unitPrice, makeNational, makeCompany, memo, sort, cate1, cate2 from sl_imsPrdMaterial where styleSno='{$prd['parentSno']}'";
                $rslt = DBUtil2::runSql($sql);
                gd_debug($sql);
                gd_debug('>>>>> Result  '. $projectSno . '-' . $prd['sno'] .'    : ' . $rslt);
            }
        }
    }

    /**
     * 작지 승인 상태 풀기 / 승인
     * @param $projectSno
     * @param $flag
     * @throws \Exception
     */
    public function imsWorkStatusSet($projectSno, $flag){
        $cnt = 0;
        gd_debug($projectSno . ' : ' . $flag);
        $styleList = DBUtil2::getList(ImsDBName::PRODUCT, 'projectSno', $projectSno);
        foreach($styleList as $style){
            $cnt += DBUtil2::update(ImsDBName::EWORK, [
                'mainApproval'=>$flag
            ], new SearchVo("styleSno=?",$style['sno']));
            //['val' => 'mainApproval', 'typ' => 's', 'def' => 'n', 'name' => '작지 메인 결재 승인'],
        }
        gd_debug('Result : '  . $cnt);

        $imsService = SlLoader::cLoad('ims', 'imsService');
        $imsService->setSyncStatus($projectSno, __CLASS__);
    }

    /**
     * 아시아나 사번 변경
     */
    public function asianaChangeEmpCode(){
        gd_debug('아시아나 사번 변경');
        $chList = [
            '514008' => '983248',
            '514013' => '983249',
            '514030' => '983253',
            '514031' => '983254',
            '514024' => '983250',
            '514027' => '983251',
            '514029' => '983252',
            '514038' => '983257',
            '514039' => '983258',
            '514033' => '983255',
            '514037' => '983256',
            '514040' => '983259',
            '514041' => '983260',
            '514048' => '983261',
        ];

        foreach($chList as $before => $after){
            $rslt1 = DBUtil2::update('sl_asianaEmployee', ['retiredFl'=>'n','companyId' => $after], new SearchVo('companyId=?',$before));
            $rslt2 = DBUtil2::update('sl_asianaOrderHistory', ['companyId' => $after], new SearchVo('companyId=?',$before));
            gd_debug($before . ' => ' . $after);
            gd_debug('M1:' . $rslt1. ' / M2:' . $rslt2);
        }
    }


    /**
     * 원부자재 복구
     */
    public function recoveryEworkMaterial(){
        gd_debug('원부자재 복구');
        $eworkService = SlLoader::cLoad('ims','ImsEworkService');
        //$eworkService->replaceMaterial(2791,269);
        //$rslt = $eworkService->replaceMaterial($each['styleSno'],$each['sno']);
        //gd_debug($each['styleSno'].'('.$each['sno'].')');
        //gd_debug($rslt);
        $list = DBUtil2::runSelect("SELECT * FROM `sl_imsEworkHistory` where styleSno in ( select sno from sl_imsProjectProduct where projectSno = 883 and delFl = 'n' )");
        foreach($list as $each){
            $rslt = $eworkService->replaceMaterial($each['styleSno'],$each['sno']);
            gd_debug($each['styleSno'].'('.$each['sno'].')');
            gd_debug($rslt);
        }
    }


    /**
     * 아시아나 지급이력 정제
     */
    public function refineAsianaHistory(){
        gd_debug('run refineAsianaHistory');
        $service = SlLoader::cLoad('scm','ScmAsianaService');
        $refreshList = DBUtil2::runSelect("select distinct companyId from sl_asianaOrderHistory");
        foreach($refreshList as $data){
            $service->saveEmpAllHistory($data['companyId']);
        }
    }


    /**
     * 삭제 자료 복구
     */
    public function recoveryImsData(){
        //Ims 자료 복구.
        $deleteProject = DBUtil2::getOne('sl_imsDeleteHistory', 'sno', 2082, false);
        $decodeProject = json_decode($deleteProject['contents'],true);
        $sn = DBUtil2::insert(ImsDBName::PRODUCT, $decodeProject);
        gd_debug($sn);

    }

}

