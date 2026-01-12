<?php
namespace Component\Addition;

use App;
use Component\Mall\Mall;
use Component\Mall\MallDAO;
use Component\Member\Util\MemberUtil;
use Component\Sitelab\MallConfig;
use Exception;
use Framework\Utility\NumberUtils;
use Request;
use Session;
use SlComponent\Database\DBConst;
use SlComponent\Database\DBUtil;
use SlComponent\Database\DBUtil2;
use SlComponent\Database\SearchVo;
use SlComponent\Database\TableVo;
use SlComponent\Util\CustomApiUtil;
use SlComponent\Util\ExcelCsvUtil;
use SlComponent\Util\LogTrait;
use SlComponent\Util\PhpExcelUtil;
use SlComponent\Util\SitelabLogger;
use SlComponent\Util\SlCodeMap;
use SlComponent\Util\SlKakaoUtil;
use SlComponent\Util\SlLoader;
use Component\Coupon\Coupon;

/**
 * 편의 기능 서비스
 * Class SlCode
 * @package SlComponent\Util
 */
class AdditionService {

    public function getPackingUploadData($files){
        $sheetData = PhpExcelUtil::readToArray($files,0);
        $field = [
            'no' => 1,
            'dept1' => 2,
            'dept2' => 3,
            'receiverName' => 4,
            'cellPhone' => 5,
            'zipcode' => 6,
            'address' => 7,
            'prdName' => 8,
        ];

        $totalColsCount = 0;
        $fieldCount = count($field);
        $optionCount = 0;
        $optionList = [];
        $dataList = [];
        $mergeDataIndex = 0;

        foreach($sheetData as $idx => $rowData){
            if(0 == $idx){
                $totalColsCount = count($rowData);
                $optionCount = count($rowData)-$fieldCount;
                for($i=$optionCount+1; $totalColsCount>=$i; $i++){
                    $optionList[] = $rowData[$i];
                }
            }else{
                if( !empty($rowData[$field['no']]) ){
                    //첫행
                    $mergeDataIndex = $rowData[$field['no']];
                    $dataList[$rowData[$field['no']]] = [
                        'compName' => $rowData[$field['dept1']],
                        'dept' => $rowData[$field['dept2']],
                        'zipcode' => $rowData[$field['zipcode']],
                        'address' => $rowData[$field['address']],
                        'cellPhone' => $rowData[$field['cellPhone']],
                        'receiverName' => $rowData[$field['receiverName']],
                        'prd' => [],
                    ];
                }

                $optionDataList = [];
                $optionNameIdx = 0;
                for($i=$optionCount+1; $totalColsCount>=$i; $i++){
                    if( !empty($rowData[$i]) ){
                        $optionDataList[$optionList[$optionNameIdx]] = $rowData[$i];
                    }
                    $optionNameIdx++;
                }
                $dataList[$mergeDataIndex]['prd'][] = [
                    'name' => $rowData[$field['prdName']],
                    'option' => $optionDataList
                ];
            }
        }

        return [
            'optionList' => $optionList,
            'dataList' => $dataList,
        ];
    }

    /**
     * 패킹 리스트 다운로드 (업로드 자료 바탕)
     * @param $files
     */
    public function downloadPackingList($files){
        $packingData = $this->getPackingUploadData($files);

        $option = $packingData['optionList'];
        $optionCount = count($option);
        $data = $packingData['dataList'];
        foreach($data as $idx => $each){
            $prdCount = count($each['prd']);
            break;
        }
        $totalRowSpanCount = $prdCount + 4;
        $totalColspan = $optionCount + 3;

        $excelBody = "<table border='0' style='font-size:20px; font-weight: bold' >";
        foreach($data as $idx => $each){
            $fieldData = [];
            $fieldData[] = "<tr>"; //Blank.
            $fieldData[] = ExcelCsvUtil::wrapTd('','','',"rowspan={$totalRowSpanCount}");
            $fieldData[] = ExcelCsvUtil::wrapTd($idx, 'text', 'vertical-align:middle;text-align:center;fot-weight:bold',"rowspan={$totalRowSpanCount}");
            for($i=0; $totalColspan>$i; $i++){
                $fieldData[] = ExcelCsvUtil::wrapTd('');
            }
            $fieldData[] = "</tr>"; //Blank.

            $fieldData[] = "<tr>";
            $fieldData[] = ExcelCsvUtil::wrapTh("고객사 / 부서명", 'packing-title');
            $fieldData[] = ExcelCsvUtil::wrapTh("품목 / 사이즈", 'packing-title', 'width:160px');
            foreach($option as $optionValue){
                $fieldData[] = ExcelCsvUtil::wrapTh($optionValue, 'packing-title2');
            }
            $fieldData[] = ExcelCsvUtil::wrapTd("소계",'packing-title3', 'width:160px');
            $fieldData[] = "</tr>";

            $total = 0;
            foreach($each['prd'] as $prdKey => $prdEach){
                $fieldData[] = "<tr>";
                if( 0 === $prdKey ){
                    $deptData = [];
                    if( !empty($each['compName']) ) $deptData[] = $each['compName'];
                    if( !empty($each['dept']) ) $deptData[] = $each['dept'];
                    $deptName = implode('<br>', $deptData);
                    $fieldData[] = ExcelCsvUtil::wrapTd("{$deptName}",'text','text-align:center;vertical-align:middle;border-bottom:solid .5pt #000000',"rowspan={$prdCount}");
                }
                $fieldData[] = ExcelCsvUtil::wrapTd($prdEach['name'],'packing-title3');
                $fieldTotal = 0;
                foreach($option as $optionValue){
                    $orderCount = $prdEach['option'][$optionValue];
                    $fieldTotal += $orderCount;
                    $total += $orderCount;
                    $fieldData[] = ExcelCsvUtil::wrapTd(number_format($orderCount), 'packing-title3');
                }
                $fieldData[] = ExcelCsvUtil::wrapTd(number_format($fieldTotal),'packing-title3');
                $fieldData[] = "</tr>";
            }

            $addressSpanCount = $optionCount-1;
            $fieldData[] = "<tr>";
            $fieldData[] = ExcelCsvUtil::wrapTd("주소/연락처",'tcenter', "border-bottom:solid .5pt #000000");

            $addressData = '';
            $customerData = '';
            if(!empty($each['zipcode'])){
                $addressData .= '(우) '.$each['zipcode'];
            }
            if(!empty($each['cellPhone'])){
                $customerData .= $each['cellPhone'];
            }
            if(!empty($each['receiverName'])){
                $customerData .= $each['receiverName'];
            }
            if(!empty($customerData)){
                $customerData = '<br>'.$customerData;
            }

            $addressData .= nl2br($each['address']).$customerData;

            $fieldData[] = ExcelCsvUtil::wrapTd($addressData,'tcenter','border-bottom:solid .5pt #000000;height:70px',"colspan={$addressSpanCount}");
            $fieldData[] = ExcelCsvUtil::wrapTd("총 합계", "total-title", '' ,'colspan=2');
            $fieldData[] = ExcelCsvUtil::wrapTd(number_format($total), "total-title");
            $fieldData[] = "</tr>";

            //Bottom 절취선.
            $fieldData[] = "<tr>";
            for($i=0; $totalColspan>$i; $i++){
                $fieldData[] = ExcelCsvUtil::wrapTd('', '', 'border-bottom:dashed 2px #000000');
            }
            $fieldData[] = ExcelCsvUtil::wrapTd('절취선','','font-size:15px');
            $fieldData[] = "</tr>";
            //gd_debug($fieldData);
            $excelBody .= implode('',$fieldData);
            //gd_debug($excelBody);
        }

        $excelBody .= "</table>";

        $simpleExcelComponent = SlLoader::cLoad('Excel','SimpleExcelComponent','sl');
        $simpleExcelComponent->downloadCommon($excelBody, '패킹리스트.xls', [
            '.line{border:solid 1px #000000}',
            '.packing-title{border:solid .5pt #000000;background-color:#d9d9d9;text-align:center;font-weight:bold;padding:10px;height:50px}',
            '.packing-title2{border:solid .5pt #000000;background-color:#d9d9d9;text-align:center;font-weight:bold;padding:10px;width:85px;height:50px}',
            '.packing-title3{border:solid .5pt #000000;text-align:center;font-weight:bold;padding:10px;height:50px}',
            '.total-title{border:solid .5pt #000000;background-color:#b4c6e7;text-align:center;font-weight:bold;padding:10px;height:50px}',
        ]);

    }

    /**
     * 업로드 폼 생성 데이터 전달
     * @param $request
     * @return string
     */
    public function getUploadFormData($request){
        $prdList = explode(",", preg_replace('/\r\n|\r|\n/',',',$request['prdList']) );
        $rowspanCount = count($prdList);

        $optionList = explode(',',trim($request['optionList']));
        $deliveryCount = trim($request['deliveryCount']);

        if( empty($deliveryCount) ){
            $deliveryCount = 15;
        }

        $excelBody = "<table border='1'>";
        $titleData[] = '<tr>';
        $titleData[] = ExcelCsvUtil::wrapTh('번호');
        $titleData[] = ExcelCsvUtil::wrapTh('부서구분1');
        $titleData[] = ExcelCsvUtil::wrapTh('부서구분2');
        $titleData[] = ExcelCsvUtil::wrapTh('담당자');
        $titleData[] = ExcelCsvUtil::wrapTh('연락처');
        $titleData[] = ExcelCsvUtil::wrapTh('우편번호');
        $titleData[] = ExcelCsvUtil::wrapTh('주소');
        $titleData[] = ExcelCsvUtil::wrapTh('품목');
        foreach($optionList as $option){
            $titleData[] = ExcelCsvUtil::wrapTh($option);
        }
        $titleData[] = '</tr>';
        $excelBody .= implode('', $titleData);

        for ($i=0; $deliveryCount>$i; $i++) {
            $fieldData = [];
            $idx = $i + 1;
            foreach( $prdList as $prdKey => $prd ){
                $fieldData[] = '<tr>';
                if( 0 == $prdKey ){
                    $fieldData[] = ExcelCsvUtil::wrapTd($idx,'','','rowspan='.$rowspanCount);
                    $fieldData[] = ExcelCsvUtil::wrapTd('','','','rowspan='.$rowspanCount);
                    $fieldData[] = ExcelCsvUtil::wrapTd('','','','rowspan='.$rowspanCount);
                    $fieldData[] = ExcelCsvUtil::wrapTd('','','','rowspan='.$rowspanCount);
                    $fieldData[] = ExcelCsvUtil::wrapTd('','','','rowspan='.$rowspanCount);
                    $fieldData[] = ExcelCsvUtil::wrapTd('','','','rowspan='.$rowspanCount);
                    $fieldData[] = ExcelCsvUtil::wrapTd('','','','rowspan='.$rowspanCount);
                }
                $fieldData[] = ExcelCsvUtil::wrapTd($prd);
                foreach($optionList as $option){
                    $fieldData[] = ExcelCsvUtil::wrapTd('');
                }
                $fieldData[] = '</tr>';
            }
            $excelBody .= implode('', $fieldData);
        }
        $excelBody .= "</table>";

        return $excelBody;
    }

    public function downloadPackingListUploadForm(){



    }


    public function getDummyOption(){
        return ['85','90','95','100','105','110','115'];
    }
    public function getDummyData(){

        return [
            [
                'compName' => '본사',
                'dept' => '총무부',
                'zipcode' => '06526',
                'address' => '서울특별시 서초구 강남대로 587',
                'cellPhone' => '010-6688-1289',
                'receiverName' => '홍길동',
                'prd' => [
                    0 => [
                        'name' => '남성티셔츠',
                        'option' => [
                            '100' => 12,
                            '105' => 2,
                            '110' => 2,
                            '115' => 1,
                        ],
                    ],
                    1 => [
                        'name' => '여성티셔츠',
                        'option' => [
                            '85' => 2,
                        ]
                    ]
                ],
            ],
            [
                'compName' => '본사',
                'dept' => 'QCS부',
                'zipcode' => '06526',
                'address' => '서울특별시 서초구 강남대로 587',
                'cellPhone' => '010-6688-1289',
                'receiverName' => '홍길동',
                'prd' => [
                    0 => [
                        'name' => '남성티셔츠',
                        'option' => [
                            '95' => 3,
                            '100' => 7,
                            '105' => 5,
                            '110' => 3,
                            '115' => 1,
                        ],
                    ],
                    1 => [
                        'name' => '여성티셔츠',
                        'option' => [
                            '85' => 1,
                            '95' => 4,
                        ]
                    ]
                ],
            ]


        ];



    }


}
