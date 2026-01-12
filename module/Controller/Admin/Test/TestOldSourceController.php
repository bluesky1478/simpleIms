<?php

namespace Controller\Admin\Test;

use App;
use Component\Database\DBIms;
use Component\Database\DBTableField;
use Component\Deposit\Deposit;
use Component\Erp\ErpCodeMap;
use Component\Erp\ErpService;
use Component\Goods\GoodsPolicy;
use Component\Ims\EnumType\APPROVAL_STATUS;
use Component\Ims\EnumType\PREPARED_TYPE;
use Component\Ims\EnumType\TODO_STATUS;
use Component\Ims\EnumType\TODO_TARGET_TYPE;
use Component\Ims\EnumType\TODO_TYPE;
use Component\Ims\EnumType\TODO_TYPE2;
use Component\Ims\ImsApprovalService;
use Component\Ims\ImsCodeMap;
use Component\Ims\ImsDBName;
use Component\Ims\ImsJsonSchema;
use Component\Ims\ImsSendMessage;
use Component\Ims\ImsService;
use Component\Scm\ScmTkeService;
use Component\Sitelab\SiteLabSmsUtil;
use Component\Work\Code\DocumentDesignCodeMap;
use Component\Work\DocumentCodeMap;
use Component\Work\WorkCodeMap;
use Controller\Admin\Sales\ControllerService\SalesListService;
use Encryptor;
use Framework\Utility\DateTimeUtils;
use Framework\Utility\NumberUtils;
use Globals;
use Request;
use SiteLabUtil\SlCommonUtil;
use SlComponent\Api\ExchangeRateService;
use SlComponent\Database\DBUtil;
use SlComponent\Database\DBUtil2;
use SlComponent\Database\SearchVo;
use SlComponent\Mail\SiteLabMailUtil;
use SlComponent\Util\ApiTrait;
use SlComponent\Util\CUrlUtil;
use SlComponent\Util\DocumentStruct;
use SlComponent\Util\ExcelCsvUtil;
use SlComponent\Util\SitelabLogger;
use SlComponent\Util\SitelabUtil;
use SlComponent\Util\SlCode;
use SlComponent\Util\SlCodeMap;
use SlComponent\Util\SlKakaoUtil;
use SlComponent\Util\SlLoader;
use SlComponent\Util\SlPostRequestUtil;
use SlComponent\Util\SlProjectCodeMap;
use SlComponent\Util\SlSmsUtil;
use UserFilePath;
use Framework\Utility\StringUtils;
use Component\Storage\Storage;
use Framework\Security\Digester;
use Framework\Utility\GodoUtils;
use DateTime;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Reader\Html;
use PhpOffice\PhpSpreadsheet\Writer\Xls;
use PhpOffice\PhpSpreadsheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Cell;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

use Bundle\Component\CurrencyExchangeRate\CurrencyExchangeRate;
use Bundle\Component\CurrencyExchangeRate\CurrencyExchangeRateAdmin;
use Framework\Debug\Exception\LayerException;

/**
 * TEST 페이지
 */
class TestOldSourceController extends \Controller\Admin\Controller{

    /**
     * @throws \Exception
     */
    public function index(){
        gd_debug('구 소스 테스트');
        exit();
    }

    public function setEworkData(&$result){

        $productData = $result['product'];

        //styleSno로 전산 작지 정보 찾기
        $eworkFileNameList = ImsCodeMap::EWORK_FILE_LIST;
        $eworkData = DBUtil2::getOne('sl_imsEwork','styleSno',$productData['sno'], false);
        //$eworkData = DBTableField::parseJsonField('sl_imsEwork',$eworkData);

        //기본 설정 (전산 작업 지시서)
        $result['ework'] = $this->setDefaultFile($eworkFileNameList, ['customerSno' => $productData['customerSno'],'styleSno' => $productData['styleSno']]);

        if(!empty($eworkData)) {
            $result['ework']['data'] = $eworkData;
            foreach ($eworkFileNameList as $fileName) {
                $fileData = json_decode(stripslashes($eworkData[$fileName]), true);
                if (!empty($fileData)) {
                    $result['ework']['fileList'][$fileName] = $fileData;
                }
            }
            $result['ework']['data'] = DBTableField::parseJsonField('sl_imsEwork',$result['ework']['data']); //Decode 한번더.
            $result['ework']['data'] = DBTableField::fieldStrip('sl_imsEwork',$result['ework']['data']);
            $specDataList = $result['ework']['data']['specData'];
            foreach($specDataList as $specDataKey => $specData){
                $specRange = explode(',',$specData['specRange']);
                $baseIndex = array_search($specData['standard'], $specRange);
                foreach($specData['specData'] as $specKey => $spec){
                    $spec = SlCommonUtil::setJsonField($spec,ImsJsonSchema::SPEC_DATA);
                    // 각 사이즈에 따른 값 계산
                    $spec['specList'] = [];
                    //$spec['correction'] = empty($spec['correction'])?[]:$spec['correction'];
                    $spec['correctionList'] = [];

                    foreach ($specRange as $index => $size) {
                        // 편차 계산 (현재 인덱스 - 기준 인덱스) * 편차
                        if( is_numeric($spec['deviation']) || $size == $specData['standard'] ){
                            $offset = ($index - $baseIndex) * $spec['deviation'];
                            $spec['specList'][$size] = $spec['spec'] + $offset;
                            if( 0 == $spec['spec'] ) $spec['specList'][$size] = '';
                        }else{
                            $spec['specList'][$size] = '';
                        }

                        // 보정값
                        $spec['correctionList'][$size] = $spec['correction'][$size];
                    }
                    unset($spec['correction']);
                    $specData['specData'][$specKey] = $spec;
                }

                $specDataList[$specDataKey] = $specData;
            }
            $result['ework']['data']['specData'] = $specDataList;
        }else{
            $result['ework']['data'] = DBTableField::getTableKeyAndBlankValue('sl_imsEwork');
        }

        $result['ework']['markCnt'] = 1;

        for($i=1; 10>=$i; $i++){
            //SitelabLogger::logger2(__METHOD__, $i . ' =================');
            $result['ework']['data']['markInfo'.$i] = ImsJsonSchema::setDefaultSchema($result['ework']['data']['markInfo'.$i], ImsJsonSchema::EWORK_MARK_INFO);
            $checkStr = implode('',$result['ework']['data']['markInfo'.$i]);
            //SitelabLogger::logger2(__METHOD__, $checkStr);

            if( $i > 1 && ( !empty($result['ework']['fileList']['fileMark'.$i][0]['fileName']) || !empty($checkStr) )  ){
                $result['ework']['markCnt']++;
                //SitelabLogger::logger2(__METHOD__, 'OK...');
            }else{
                //SitelabLogger::logger2(__METHOD__, $result['ework']['fileList']['fileMark'.$i][0]['fileName']);
                //SitelabLogger::logger2(__METHOD__, $checkStr);
            }
        }

        //마이그레이션 시 사용한 키 생성 로직 ( 스타일번호, idx-range-gender. )
        //$key= md5($styleSno.$val[$idxRange].$val[$idxGender]);
        //SitelabLogger::logger2(__METHOD__, '작지 저장 데이터 확인');
        //SitelabLogger::logger2(__METHOD__, $result['ework']['data']);

        //스펙데이터 기본 설정
        if( empty($result['ework']['data']['specData']) ){
            $pantsCodeList = ['PT','PP'];
            //$result['ework']['data']['specData']
            $defaultSpecData = SlCommonUtil::setJsonField([], ImsJsonSchema::SPEC_DATA);

            if( in_array($productData['prdStyle'], $pantsCodeList) ){
                $defaultSpecData['specList']['32'] = '';
                $defaultSpecData['correctionList']['32'] = '';
            }else{
                $defaultSpecData['specList']['100'] = '';
                $defaultSpecData['correctionList']['100'] = '';
            }

            /*$defaultSpecData['specList']['90'] = '';
            $defaultSpecData['specList']['95'] = '';
            $defaultSpecData['specList']['100'] = '';
            $defaultSpecData['specList']['105'] = '';
            $defaultSpecData['specList']['110'] = '';
            $defaultSpecData['specList']['115'] = '';
            $defaultSpecData['specList']['120'] = '';*/
            //SitelabLogger::logger2(__METHOD__, $defaultSpecData);

            $result['ework']['data']['specData'][] = [
                'styleSno' => $productData['styleSno'],
                'specRange' => in_array($productData['prdStyle'], $pantsCodeList) ? '28,30,32,34,36,38,40':'90,95,100,105,110,115,120',
                'standard' => in_array($productData['prdStyle'], $pantsCodeList) ? '32':'100',
                'gender' => '남',
                'specData' => [$defaultSpecData],
            ];

        }

        //SitelabLogger::logger2(__METHOD__, '작지 저장 데이터 확인');
        //SitelabLogger::logger2(__METHOD__, $result['ework']['data']);
    }

}
