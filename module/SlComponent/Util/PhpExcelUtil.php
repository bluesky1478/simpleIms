<?php
namespace SlComponent\Util;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Reader\Xls;
use PhpOffice\PhpSpreadsheet\Reader\Html;
use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use SiteLabUtil\SlCommonUtil;

class PhpExcelUtil{

    public static function checkContentsValidation($excelData, $ignoreIndex){
        if( empty($excelData[0])  ){ //첫행 검사
            throw new \Exception('엑셀 화일을 확인해 주세요. 엑셀 데이타가 존재하지 않습니다.'.($ignoreIndex+1).'번째 줄부터 작성을 하셔야 합니다.');
        }
    }

    public static function runExcelReadAndProcess($files, $params, $startRowCnt = 0){
        if( !empty($files) ){
            $sheetData = PhpExcelUtil::readToArray($files,$startRowCnt);
            try {
                PhpExcelUtil::checkContentsValidation($sheetData, $startRowCnt);
            } catch (\Exception $e) {
                throw new \Exception($e);
            }
            \SiteLabUtil\SlCommonUtil::setEachData($sheetData, $params['instance'], $params['fnc'], $params['mixData']);
            return $params['mixData'];
        }
        return [];
    }

    /**
     * 엑세 파일을 읽어 Array로 반환
     * @param $files
     * @param int $ignoreIndex
     * @return array
     */
    public static function readToArray($files, $ignoreIndex = 0){
        $objPHPExcel = IOFactory::load($files['excel']['tmp_name']);
        $worksheet = $objPHPExcel->getActiveSheet();

        $isHtml = false;
        $dataList=[];
        foreach ($worksheet->getRowIterator() as $sheetIdx => $row) {

            if( $sheetIdx === $ignoreIndex && 0 != $ignoreIndex ) continue;

            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(false);
            $data = [];
            $cellIdx = 1;
            foreach ($cellIterator as $cellKey => $cell) {
                $cellValue = $cell->getValue();
                if( !$isHtml && (strpos( $cellValue, '<table' ) !== false || strpos( $cellValue, '<td' ) !== false) ){
                    $isHtml = true;
                }
                $data[$cellIdx++] = $cell->getValue();
            }
            $dataList[] = $data;
        }

        if( $isHtml ){
            //HTML을 데이터로 가져오기.
            $readDataList = PhpExcelUtil::readHtml($dataList, $ignoreIndex);
        }else{
            $readDataList = $dataList;
        }

        return $readDataList;
    }

    /**
     * HTML을 반환
     * @param $dataList
     * @param $ignoreIndex
     * @return array
     */
    public static function readHtml($dataList, $ignoreIndex){
        $htmlStr = '';
        foreach($dataList as $eachData){
            $htmlStr .= implode('',$eachData);
        }
        // DOM 객체 생성
        $dom = new \DOMDocument();
        $dom->loadHTML('<?xml encoding="UTF-8">'.$htmlStr);

        $data = array();
        $rows = $dom->getElementsByTagName('tr');
        foreach ($rows as $rowIdx => $row) {
            if( $rowIdx === ($ignoreIndex-1) ) continue;
            $cols = $row->getElementsByTagName('td');
            $rowData = array();
            foreach ($cols as $colIdx => $col) {
                $rowData[($colIdx+1)] = $col->nodeValue;
            }
            $data[] = $rowData;
        }
        return $data;
    }

    /**
     * 엑셀 데이터를 가져온다.
     * @param $data
     * @param $fieldName
     * @param $code
     * @return string
     */
    public static function getExcelData($data, $fieldName, $code){
        return trim($data[$code[$fieldName]]);
    }

    /**
     * 엑셀 데이터를 정의한 필드에 맞게 모두 가져온다.
     * @param $data
     * @param $codeList
     * @return array
     */
    public static function getExcelDataList($data, $codeList){
        $resultList = [];
        foreach($codeList as $fieldName => $each){
            $resultList[$fieldName] = trim($data[$codeList[$fieldName]]);
        }
        return $resultList;
    }

    public static function convertHtmlToSpreadSheet($html, $subject='sheet1',array $addStyle=null){
        $tmpfile = './tmp/'.uniqid().'.html';
        file_put_contents($tmpfile, $html);
        $spreadsheet = IOFactory::createReader('Html')->load($tmpfile);
        $defaultStyle = [
            'font' => [
                'bold' => false,
                'size' => 10,
                'name' => '맑은 고딕',
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER
            ],
            'fill' => [
                'fillType' => Fill::FILL_GRADIENT_LINEAR,
                'rotation' => 90,
                'startColor' => ['argb' => 'FFFF0000'],
                'endColor' => ['argb' => 'FFFF0000']
            ]
        ];
        if(!empty($addStyle)){
            $defaultStyle = array_merge($defaultStyle, $addStyle);
        }

        $spreadsheet->getDefaultStyle()->applyFromArray($defaultStyle);
        $spreadsheet->getProperties()->setCreator("MS INNOVER")
            ->setLastModifiedBy("MS INNOVER")
            ->setTitle($subject)
            ->setSubject($subject)
            ->setDescription($subject);
        //
        //$sheet->setTitle($subject);//$sheet = $spreadsheet->getActiveSheet();
        unlink( $tmpfile );
        return $spreadsheet;
    }

}