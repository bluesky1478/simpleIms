<?php
/**
 * Created by PhpStorm.
 * User: SSong2016
 * Date: 2017-12-05
 * Time: 오전 11:20
 */

namespace SlComponent\Excel;

use SlComponent\Util\ExcelCsvUtil;
use SlComponent\Util\SitelabLogger;

class SimpleExcelComponent {

    public function downloadCommon($excelBody, $fileName, $addStyleClass=null){
        //SitelabLogger::logger($fileName);
        $fileName .= '.xls';
        $fileName = iconv('utf-8', 'euc-kr', $fileName);
        header( "Content-type: application/vnd.ms-excel; charset=utf-8");
        header( "Content-Disposition: attachment; filename = {$fileName}" );
        $excelHederFooter = ExcelCsvUtil::getExcelHeaderFooter($addStyleClass);
        //Download
        echo $excelHederFooter['header'];
        echo $excelBody;
        echo $excelHederFooter['footer'];
    }

    public function simpleDownload($title,$tableTitleList,$tableBody, $isTopTitle = true){
        $fileName = str_replace('/','_',urlencode($title));
        $title = str_replace('_', ' ', $title);

        $colCount = count($tableTitleList);
        //Excel Body
        $excelBody = "<table border='1'>";
        //열 제목
        if( !empty($tableTitleList) ){
            if( $isTopTitle ){
                $excelBody .= "<tr><td colspan='{$colCount}' style='font-size:20px;font-weight: bold;text-align: center; '>{$title}</td></tr>";
            }
            $excelBody .= "<tr>";
            foreach($tableTitleList as $cellTitle){
                $excelBody .= ExcelCsvUtil::wrapTh($cellTitle);
            }
            $excelBody .= "</tr>";
        }
        $excelBody .= $tableBody;
        $excelBody .= "</table>";
        $this->downloadCommon($excelBody, $fileName);
    }

}