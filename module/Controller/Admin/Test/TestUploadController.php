<?php

namespace Controller\Admin\Test;

use Component\Database\DBIms;
use Component\Database\DBTableField;
use Component\Deposit\Deposit;
use Component\Ims\ImsDBName;
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
class TestUploadController extends \Controller\Admin\Controller{

    public function index(){
        //gd_debug('업종 등록1');
        gd_debug('사이즈스펙 등록');

        $files = Request::files()->toArray();
        gd_debug($files);

        if( !empty($files) ){
            $files = \Request::files()->toArray();
            $sheetData = PhpExcelUtil::readToArray($files,1);

            //$this->generalBusiCate($sheetData);
            $this->sizeSpecUpdate($sheetData);

            gd_debug('Complete...');

            exit();
        }
    }


    /**
     * 사이즈 스펙 등록
     * @param $sheetData
     */
    public function sizeSpecUpdate($sheetData)
    {
        // 엑셀 → DB insert용으로 그룹핑
        // key : fitSeason|fitStyle|fitSize|fitName|fitSizeName
        $grouped = [];

        foreach ($sheetData as $rowIndex => $each) {
            // 1. 빈행 스킵
            if (!isset($each[1]) || trim($each[1]) === '') {
                continue;
            }
            $col1 = trim($each[1]);

            // 2. 헤더행(한글/영문) 스킵
            //   1행 : 시즌 / 스타일 ...
            //   2행 : fitSeason / fitStyle ...
            if ($col1 === '시즌' || $col1 === 'fitSeason') {
                continue;
            }

            // 3. 기본키 컬럼 추출 (엑셀에서 1,2,3,4,5.. 순서대로)
            $fitSeason   = isset($each[1]) ? trim($each[1]) : '';
            $fitStyle    = isset($each[2]) ? trim($each[2]) : '';
            $fitSize     = isset($each[3]) ? trim($each[3]) : '';
            $fitName     = isset($each[4]) ? trim($each[4]) : '';
            $fitSizeName = isset($each[5]) ? trim($each[5]) : '';

            // 4. 옵션 컬럼 추출
            $optionName  = isset($each[6]) ? trim($each[6]) : '';
            $optionRange = isset($each[7]) ? trim($each[7]) : '';
            $optionUnit  = isset($each[8]) ? trim($each[8]) : '';
            $optionValue = isset($each[9]) ? trim($each[9]) : '';

            // 옵션명 없으면 의미 없는 행이므로 스킵
            if ($optionName === '') {
                continue;
            }

            // 5. 그룹 키 생성
            $key = implode('|', [
                $fitSeason,
                $fitStyle,
                $fitSize,
                $fitName,
                $fitSizeName,
            ]);

            // 6. 그룹 최초 생성
            if (!isset($grouped[$key])) {
                $grouped[$key] = [
                    'fitSeason'   => ($fitSeason !== '' ? $fitSeason : 'ALL'),
                    'fitStyle'    => $fitStyle,
                    'fitSize'     => $fitSize,
                    'fitName'     => $fitName,
                    'fitSizeName' => $fitSizeName,
                    'options'     => [],
                ];
            }

            // 7. 옵션 배열에 push
            $grouped[$key]['options'][] = [
                'optionName'  => $optionName,
                'optionRange' => $optionRange,
                'optionUnit'  => $optionUnit,
                'optionValue' => $optionValue,
            ];
        }

        if (empty($grouped)) {
            return;
        }

        // 등록자 / 등록일시
        // (관리자 일련번호는 사용 중인 세션/헬퍼에 맞춰서 수정)
        $regManagerSno = (int)\Session::get('manager.sno');
        foreach ($grouped as $item) {
            $insertData = [
                'regManagerSno' => $regManagerSno,
                'fitSeason'     => $item['fitSeason'],
                'fitStyle'      => $item['fitStyle'],
                'fitName'       => $item['fitName'],
                'fitSizeName'   => $item['fitSizeName'],
                'fitSize'       => $item['fitSize'],
                'jsonOptions'   => json_encode($item['options']),
            ];
            DBUtil2::insert('sl_imsBasicSizeSpec', $insertData);
            //gd_debug($insertData);
        }
    }

    /**
     * 업종 및 고객 매칭
     * @param $sheetData
     * @throws \Exception
     */
    public function customerUpdate($sheetData){
        $cateList = DBUtil2::getListBySearchVo('sl_imsBasicBusiCate', new SearchVo("parentBusiCateSno <> ?",'0'));
        $cateMap = [];
        foreach($cateList as $cateInfo){
            if( !empty($cateInfo['parentBusiCateSno']) ){
                $cateMap[$cateInfo['cateName']]=$cateInfo['sno'];
            }
        }

        foreach( $sheetData as $each ){
            $bizCode2 = $each[2];
            $imsCustomerName = $each[3];
            $contractName = $each[4];
            $bizCodeSno = $cateMap[$bizCode2];

            $updateData = [];
            if(!empty($imsCustomerName)){
                //IMS고객 번호 가져오기
                $imsCustomerInfo = DBUtil2::getOne(ImsDBName::CUSTOMER, 'customerName', $imsCustomerName);
                $updateData['customerSno'] = $imsCustomerInfo['sno'];
                if(!empty($updateData['customerSno'])){
                    DBUtil2::update(ImsDBName::CUSTOMER, ['busiCateSno'=>$bizCodeSno], new SearchVo('sno=?',$updateData['customerSno'])); //업종 업데이트
                }
            }

            //발굴고객 번호 가져오기 / 업데이트
            $contractInfo = DBUtil2::getOne('sl_salesCustomerInfo', 'customerName', $contractName);
            if($contractInfo['sno']){
                $contractUpdate = [
                    'busiCateSno' => $bizCodeSno
                ];
                if( !empty($updateData['customerSno']) ){
                    $contractUpdate['customerSno']=$updateData['customerSno'];
                }
                DBUtil2::update('sl_salesCustomerInfo', $contractUpdate, new SearchVo('sno=?',$contractInfo['sno'])); //카테고리와 IMS고객 번호 업데이트
            }
            

            if(empty($parentInfo)){
                $parentSno = DBUtil2::insert('sl_imsBasicBusiCate', [
                    'parentBusiCateSno' => 0,
                    'cateName' => $each[1],
                ]);
            }else{
                $parentSno = $parentInfo['sno'];
            }
            DBUtil2::insert('sl_imsBasicBusiCate', [
                'parentBusiCateSno' => $parentSno,
                'cateName' => $each[2],
            ]);
        }
    }

    /**
     * 업종 카테고리 저장
     * @param $sheetData
     */
    public function generalBusiCate($sheetData){
        foreach( $sheetData as $each ){
            $parentInfo = DBUtil2::getOne('sl_imsBasicBusiCate','parentBusiCateSno=0 and cateName',$each[1]);
            if(empty($parentInfo)){
                $parentSno = DBUtil2::insert('sl_imsBasicBusiCate', [
                    'parentBusiCateSno' => 0,
                    'cateName' => $each[1],
                ]);
            }else{
                $parentSno = $parentInfo['sno'];
            }
            DBUtil2::insert('sl_imsBasicBusiCate', [
                'parentBusiCateSno' => $parentSno,
                'cateName' => $each[2],
            ]);
        }
    }

}