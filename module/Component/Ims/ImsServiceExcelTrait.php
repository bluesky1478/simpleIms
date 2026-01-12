<?php
namespace Component\Ims;

use App;
use Component\Database\DBTableField;
use Component\Member\Manager;
use Component\Sms\Code;
use Framework\Debug\Exception\AlertBackException;
use Framework\Utility\DateTimeUtils;
use LogHandler;
use Request;
use Session;
use SiteLabUtil\SlCommonUtil;
use SlComponent\Database\DBUtil2;
use SlComponent\Database\SearchVo;
use SlComponent\Util\SitelabLogger;
use SlComponent\Util\SlLoader;
use SlComponent\Util\SlSmsUtil;
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
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

/**
 * IMS 상품관련 서비스
 * Class GoodsStock
 * @package Component\Goods
 */
trait ImsServiceExcelTrait {

    public function setExcelTitle($sheet, $cell){
        $sheet->getStyle($cell)->getFont()->setBold(true);
        $sheet->getStyle($cell)->getFill()->setFillType(Fill::FILL_SOLID);
        $sheet->getStyle($cell)->getFill()->getStartColor()->setARGB('ffdbdbdb');
        $sheet->getStyle($cell)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle($cell)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
    }

    public function setExcelBottomBold($sheet, $cell){
        $sheet->getStyle($cell)->applyFromArray(
            [
                'borders' => [
                    'bottom' => [
                        'style' => Border::BORDER_THICK,
                        'color' => ['argb' => 'FF000000'],
                    ],
                ],
            ]
        );
    }


    public function downloadFabricRequestForm($params){
        //Param Refine
        foreach($params as $key => $each){
            $params[$key] = str_replace('null','',$params[$key]);
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

//공통 양식
        $spreadsheet->getDefaultStyle()->getFont()->setName('맑은 고딕')->setSize(10);
        $sheet->getColumnDimension("A")->setWidth(5);
        $sheet->getColumnDimension("B")->setWidth(5);
        $sheet->getColumnDimension("C")->setWidth(8);
        $sheet->getColumnDimension("G")->setWidth(5);
        $sheet->getColumnDimension("H")->setWidth(5);

        $sheet->getStyle('A1:M37')->applyFromArray([
            'borders' => [
                'outline' => [
                    'style' => Border::BORDER_THICK,
                    'color' => ['argb' => 'FF000000'],
                ],
                'inside' => [
                    'style' => Border::BORDER_THIN,
                    'color' => ['argb' => 'FF000000'],
                ],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_LEFT,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);
        $sheet->getPageSetup()->setPrintArea('A1:M37');

        $maxRow = 37; // 예를 들어, 최대 100행까지 설정
        for ($row = 1; $row <= $maxRow; $row++) {
            $sheet->getRowDimension($row)->setRowHeight(16.5);
        }

        $sheet->getRowDimension(8)->setRowHeight(25);
        $sheet->getRowDimension(9)->setRowHeight(25);
        $sheet->getRowDimension(10)->setRowHeight(25);
        $sheet->getRowDimension(11)->setRowHeight(25);

// 문서 제목 설정
        $sheet->mergeCells('A1:M3');
        $sheet->mergeCells("H8:M11");
        $sheet->mergeCells("G8:G11");
        $sheet->mergeCells("A12:M12");
        $sheet->mergeCells("A25:M25");
        $sheet->mergeCells("A13:M24");

        $sheet->mergeCells("A26:C26");
        $sheet->mergeCells("D26:F26");
        $sheet->mergeCells("G26:J26");
        $sheet->mergeCells("K26:M26");

        $sheet->mergeCells("A27:C37");
        $sheet->mergeCells("D27:F37");
        $sheet->mergeCells("G27:J37");
        $sheet->mergeCells("K27:M37");

        //$sheet->setCellValue('A1', 'BEAKER TEST (  ●  ) QUALITY (     ) BULK (     )');
        $sheet->setCellValue('A1', 'BEAKER TEST (     ) QUALITY (     ) BULK (     )');
        $sheet->getStyle('A1')->getFont()->setName('HY헤드라인M');
        $sheet->getStyle('A1')->getFont()->setSize(22);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A1')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);

// 기본 데이터 입력
        for($i=4; 7>=$i; $i++){
            $sheet->mergeCells("A{$i}:B{$i}");//Title
            $sheet->mergeCells("C{$i}:F{$i}");//Value
            $this->setExcelTitle($sheet, "A{$i}");//Title Form

            $sheet->mergeCells("G{$i}:H{$i}");//Title
            $sheet->mergeCells("I{$i}:M{$i}");//Value
            $this->setExcelTitle($sheet, "G{$i}");//Title Form
        }

        for($i=8; 11>=$i; $i++){
            $sheet->mergeCells("A{$i}:B{$i}");//Title

            if(10 !== $i){
                $sheet->mergeCells("C{$i}:F{$i}");//Value
            }else{
                $this->setExcelTitle($sheet, "D10");//Title Form
                $sheet->mergeCells("E10:F10");//원단폭 Value
            }

            $this->setExcelTitle($sheet, "A{$i}");
            $this->setExcelTitle($sheet, "G{$i}");
        }

        $this->setExcelTitle($sheet, "A12");//Title Form
        $this->setExcelTitle($sheet, "A25");//Title Form

        $sheet->setCellValue('A4', '업체명');
        $sheet->setCellValue('C4', $params['produceCompany']);

        $sheet->setCellValue('G4', '고객사');
        $sheet->setCellValue('I4', $params['customerName']);

        $sheet->setCellValue('A5', '발송일');

        $sheet->setCellValue('G5', '제품품목');
        $sheet->setCellValue('I5', $params['productName']);

        $sheet->setCellValue('A6', '확정일');
        $sheet->setCellValue('G6', 'S/NO.');
        $sheet->setCellValue('I6', $params['styleCode']);

        $sheet->setCellValue('A7', '수량');
        $sheet->setCellValue('C7', $params['prdExQty']);

        $sheet->setCellValue('G7', '납기');
        $sheet->setCellValue('I7', $params['msDeliveryDt']);

        $sheet->setCellValue('A8', '원단명');
        $sheet->setCellValue('C8', $params['fabricName']);

        $sheet->setCellValue('G8', "이\n미\n지");
        $sheet->getStyle('G8')->getAlignment()->setWrapText(true);
        //이미지 처리
        if(!empty($params['fileThumbnail'])){
            $imagePath = tempnam(sys_get_temp_dir(), 'img');
            file_put_contents($imagePath, fopen($params['fileThumbnail'], 'r'));
            $objDrawing = new Drawing();
            $objDrawing->setName('work image');
            $objDrawing->setDescription('work image');
            $objDrawing->setPath($imagePath);
            $objDrawing->setCoordinates('H8'); // 이미지를 삽입할 셀 위치
            $objDrawing->setWorksheet($spreadsheet->getActiveSheet());
            list($originalWidth, $originalHeight) = getimagesize($imagePath);
            $targetHeight = 122; // 목표 높이 설정
            $scale = $targetHeight / $originalHeight; // 비율 계산
            $targetWidth = $originalWidth * $scale; // 목표 폭 계산
            $objDrawing->setWidth($targetWidth); // 계산된 폭으로 설정
            $objDrawing->setHeight($targetHeight); // 설정한 높이로 설정
            // 셀에 여백을 주기 위해 이미지의 위치 조정
            $objDrawing->setOffsetX(5); // 셀의 왼쪽 가장자리로부터 10픽셀 오른쪽에 위치
            $objDrawing->setOffsetY(5); // 셀의 상단 가장자리로부터 10픽셀 아래에 위치
        }

        $sheet->setCellValue('A9', '혼용률');
        $sheet->setCellValue('C9', $params['fabricMix']);

        $sheet->setCellValue('A10', '중량');
        $sheet->setCellValue('C10', $params['weight']);

        $sheet->setCellValue('D10', '원단폭');
        $sheet->setCellValue('E10', $params['fabricWidth']);

        $sheet->setCellValue('A11', '후가공');
        $sheet->setCellValue('C11', $params['afterMake']);

        $this->setExcelBottomBold($sheet, 'A7:M7');
        $this->setExcelBottomBold($sheet, 'A1:M11');
        $this->setExcelBottomBold($sheet, 'A24:M24');

        $sheet->setCellValue('A12', '(타 겟) 오 리 지 널');
        $sheet->setCellValue('A25', 'B/T의뢰');

        $fileName = '퀄리티_BT의뢰서.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="'. urlencode($fileName) .'"');
        header('Cache-Control: max-age=0');

        $objWriter = IOFactory::createWriter($spreadsheet, 'Xlsx');
        ob_start();
        $objWriter->save('php://output');
        ob_get_contents();
        ob_end_flush();

        unlink($imagePath);

        exit;
    }
}