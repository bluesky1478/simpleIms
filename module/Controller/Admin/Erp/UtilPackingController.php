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
 * @link http://www.godo.co.kr
 */
namespace Controller\Admin\Erp;

use Component\Database\DBTableField;
use Exception;
use Framework\Debug\Exception\LayerException;
use Request;
use SiteLabUtil\SlCommonUtil;
use SlComponent\Util\ExcelCsvUtil;
use SlComponent\Util\PhpExcelUtil;
use SlComponent\Util\SitelabLogger;
use SlComponent\Util\SlLoader;
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

class UtilPackingController extends \Controller\Admin\Controller
{

    /**
     * 공급사 커스텀 정보 수정
     */
    public function index(){

        $this->callMenu('erp', 'util', 'packing');

        $request = \Request::request()->toArray();
        if($request['mode']){
            $fncName = $request['mode'];
            $fileName = '패킹리스트.xls';
            $spreadsheet = $this->$fncName($request);
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="'.$fileName.'"');
            header('Cache-Control: max-age=0');
            $writer = new Xls($spreadsheet);
            $writer->save('php://output');
            exit();
        }
        //uploadForm
    }

    public function downloadUploadForm($request){

        $additionService = SlLoader::cLoad('addition','additionService');
        $html = $additionService->getUploadFormData($request);
        $spreadsheet = PhpExcelUtil::convertHtmlToSpreadSheet($html,'패킹리스트'); //추가할 스타일 있으면 array로 추가
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->getColumnDimension("B")->setWidth(13); //부서구분1
        $sheet->getColumnDimension("C")->setWidth(13); //부서구분2
        $sheet->getColumnDimension("D")->setWidth(13); //담당자
        $sheet->getColumnDimension("E")->setWidth(13); //연락처
        $sheet->getColumnDimension("F")->setWidth(10); //우편번호
        $sheet->getColumnDimension("G")->setWidth(60); //주소
        $sheet->getColumnDimension("H")->setWidth(20); //품목

        /*for ($i = 1; $i <= $maxRowKey; $i++) {
            for ($j = 'A'; $j <= $maxCol; $j++) {
                $cell = $sheet->getStyle($j . $i);
                $cellKey = $j . $i;

                if( $i === 1 ){
                    $cell->getFont()->setBold(true);
                }
                $cell->getBorders()->getOutline()->setBorderStyle(Border::BORDER_THIN);
                $cell->getBorders()->getOutline()->getColor()->setARGB('FF000000');

                if( ( 'R' === $j || 'A' === $j) && 1 !== $i ){
                    $font = $cell->getFont();
                    $font->setColor(new Color('FF0000'));
                    $cell->setFont($font);
                    $cell->getFont()->setColor(new Color('FFFF0000'));
                }
                if('C' === $j && $i !== 1 ){
                    $sheet->setCellValueExplicit('C'.$i, ''.$styleMap['C'.$i]['value'].''  , \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                    $cell->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);
                }

                if( 'line' === $styleMap[$cellKey]['rowClass'] ){
                    $cell->getFill()->setFillType(Fill::FILL_SOLID);
                    $cell->getFill()->getStartColor()->setARGB('fffde9d9');
                }
            }
        }*/
        return $spreadsheet;
    }

}
