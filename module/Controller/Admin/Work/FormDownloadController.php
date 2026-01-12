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
 * @link      http://www.godo.co.kr
 */

namespace Controller\Admin\Work;


use Component\Work\Code\DocumentDesignCodeMap;
use Component\Work\DocumentCodeMap;
use Component\Work\WorkCodeMap;
use SlComponent\Database\DBUtil2;
use SlComponent\Util\ExcelCsvUtil;
use SlComponent\Util\SitelabLogger;
use SlComponent\Util\SlLoader;
use SlComponent\Util\SlProjectCodeMap;

class FormDownloadController extends \Controller\Admin\Controller
{
    public function index(){
        $requestParam = \Request::request()->toArray();
        $functionName = $requestParam['type'];
        $item = json_decode(urldecode($requestParam['specItemStr']),true);
        //SitelabLogger::logger($specItem);

        $excelData = self::$functionName($item, $requestParam);

        $simpleExcelComponent = SlLoader::cLoad('Excel','SimpleExcelComponent','sl');
        $simpleExcelComponent->simpleDownload($excelData['fileName'], $excelData['title'], $excelData['body'], false);
        exit();
    }


    /**
     * 샘플의뢰서 , 피팅체크 리스트 스펙 양식 다운로드
     * @param $item
     * @param $requestParam
     * @return array
     */
    public function downloadSampleSpecItem($item, $requestParam){
        $excelTitles = [
            '구분', 
            '완성스펙',
            '기준스펙',
            '단위',
            '측정부위',
        ];

        $excelBody = '';
        foreach($item as $eachItem){
            $excelBody .= '<tr>';
            $excelBody .= ExcelCsvUtil::wrapTd( $eachItem['specItemName'] );
            $excelBody .= ExcelCsvUtil::wrapTd( $eachItem['completeSpec'] );
            $excelBody .= ExcelCsvUtil::wrapTd( $eachItem['guideSpec'] );
            $excelBody .= ExcelCsvUtil::wrapTd( $eachItem['specUnit'] );
            $excelBody .= ExcelCsvUtil::wrapTd( $eachItem['specDescription'] );
            $excelBody .= '</tr>';
        }

        return [
          'fileName' => '사이즈스펙_업로드_양식',
          'title' => $excelTitles,
          'body' => $excelBody,
        ];
    }

    /**
     * 작업지시서 양식 다운로드
     * @param $item
     * @param $requestParam
     * @return array
     */
    public function downloadWorkSpecItem($item, $requestParam){
        $excelTitlesPrefix = [
            '구분',
            '고객안내',
            '편차',
        ];
        $optionList = explode(',',$requestParam['optionList']);
        $excelTitlesSuffix = [
            '단위',
            '측정부위',
        ];

        $excelTitles = array_merge($excelTitlesPrefix, $optionList, $excelTitlesSuffix);

        $excelBody = '';
        foreach($item as $eachItem){
            $excelBody .= '<tr>';
            $excelBody .= ExcelCsvUtil::wrapTd( $eachItem['specItemName'] );
            $excelBody .= ExcelCsvUtil::wrapTd( $eachItem['isCustomerGuideFl'] );
            $excelBody .= ExcelCsvUtil::wrapTd( $eachItem['avg'] );
            foreach($eachItem['checkSpec'] as $checkSpec){
                $excelBody .= ExcelCsvUtil::wrapTd( $checkSpec );
            }
            $excelBody .= ExcelCsvUtil::wrapTd( $eachItem['specUnit'] );
            $excelBody .= ExcelCsvUtil::wrapTd( $eachItem['specDescription'] );
            $excelBody .= '</tr>';
        }

        return [
          'fileName' => '사이즈스펙_업로드_양식',
          'title' => $excelTitles,
          'body' => $excelBody,
        ];
    }

}
