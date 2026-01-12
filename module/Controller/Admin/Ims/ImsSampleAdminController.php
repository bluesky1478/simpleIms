<?php

namespace Controller\Admin\Ims;

use App;
use Component\Category\BrandAdmin;
use Component\Category\CategoryAdmin;
use Component\Ims\EnumType\PREPARED_TYPE;
use Component\Ims\ImsCodeMap;
use Component\Ims\ImsDBName;
use Component\Ims\ImsJsonSchema;
use Component\Stock\StockListService;
use Component\Work\WorkCodeMap;
use Controller\Admin\Erp\AdminErpControllerTrait;
use Controller\Admin\Erp\ControllerService\InoutListService;
use Globals;
use Request;
use SiteLabUtil\SlCommonUtil;
use SlComponent\Util\ExcelCsvUtil;
use SlComponent\Util\SlCodeMap;
use SlComponent\Util\SlLoader;
use function SlComponent\Util\SlLoader;

/**
 * 문서 리스트
 */
class ImsSampleAdminController extends \Controller\Admin\Controller{

    use ImsControllerTrait;

    public function index(){
        $request = \Request::request()->toArray();
        $this->callMenu('ims', 'customer', 'styleAdmin');
        $this->setDefault();

        //검색항목
        $search['combineSearch'] = [
            'cust.customerName' => '고객명',
            'prj.sno' => '프로젝트번호',
            'a.styleCode' => '스타일코드',
            'a.productName' => '스타일명',
        ];
        $this->setData('search', $search);

        $imsStyleService = SlLoader::cLoad('ims', 'imsStyleService');
        $dpStyle = $imsStyleService->getDisplayStyle($request);
        $tableTitleDate = SlCommonUtil::createHtmlTableTitle($dpStyle);
        $this->setData('tableTitleData',$tableTitleDate);

        $tableBodyData = SlCommonUtil::createHtmlTableVueBody($dpStyle); //
        $tableBodyData = str_replace('**eworkMainFl**','<i aria-hidden="true" class="fa fa-lg fa-check-circle text-green cursor-pointer" v-if="1==each.eworkMainFl" @click="openCommonPopup(\'ework\', 1300, 850, {sno:each.sno, tabMode:\'main\'})"></i>',$tableBodyData);
        $tableBodyData = str_replace('**eworkMarkFl**','<i aria-hidden="true" class="fa fa-lg fa-check-circle text-green cursor-pointer" v-if="1==each.eworkMarkFl" @click="openCommonPopup(\'ework\', 1300, 850, {sno:each.sno, tabMode:\'mark\'})"></i>',$tableBodyData);
        $tableBodyData = str_replace('**eworkPositionFl**','<i aria-hidden="true" class="fa fa-lg fa-check-circle text-green cursor-pointer" v-if="1==each.eworkPositionFl" @click="openCommonPopup(\'ework\', 1300, 850, {sno:each.sno, tabMode:\'position\'})"></i>',$tableBodyData);
        $tableBodyData = str_replace('**eworkSpecFl**','<i aria-hidden="true" class="fa fa-lg fa-check-circle text-green cursor-pointer" v-if="1==each.eworkSpecFl" @click="openCommonPopup(\'ework\', 1300, 850, {sno:each.sno, tabMode:\'spec\'})"></i>',$tableBodyData);
        $tableBodyData = str_replace('**eworkSpec2Fl**','<i aria-hidden="true" class="fa fa-lg fa-check-circle text-green cursor-pointer" v-if="1==each.eworkSpec2Fl" @click="openCommonPopup(\'ework\', 1300, 850, {sno:each.sno, tabMode:\'spec\'})"></i>',$tableBodyData);
        $tableBodyData = str_replace('**eworkMaterialFl**','<i aria-hidden="true" class="fa fa-lg fa-check-circle text-green cursor-pointer" v-if="1==each.eworkMaterialFl" @click="openCommonPopup(\'ework\', 1300, 850, {sno:each.sno, tabMode:\'material\'})"></i>',$tableBodyData);
        $tableBodyData = str_replace('**eworkPackingFl**','<i aria-hidden="true" class="fa fa-lg fa-check-circle text-green cursor-pointer" v-if="1==each.eworkPackingFl" @click="openCommonPopup(\'ework\', 1300, 850, {sno:each.sno, tabMode:\'packing\'})"></i>',$tableBodyData);
        $tableBodyData = str_replace('**eworkBatekFl**','<i aria-hidden="true" class="fa fa-lg fa-check-circle text-green cursor-pointer" v-if="1==each.eworkBatekFl" @click="openCommonPopup(\'ework\', 1300, 850, {sno:each.sno, tabMode:\'batek\'})"></i>',$tableBodyData);

        $this->setData('tableBodyData',$tableBodyData);

        //$allProgress = ImsCodeMap::PROJECT_STATUS;
        //unset($allProgress[98]); //보류 상태 제외
        //unset($allProgress[99]);
        //$this->setData('chkOrderProgress',implode(',',array_keys($allProgress)));

        $this->setData('chkOrderProgress',90);


        if(  !empty($request['simple_excel_download'])  ){
            $request['page'] = 1;
            $request['pageNum'] = 15000;
            $fncName = 'simpleExcelDownload'.$request['simple_excel_download'];

            $request['multiKey'] = json_decode($request['multiKey'], true);
            $request['projectTypeChk'] = json_decode($request['projectTypeChk'], true);
            $request['productionChk'] = json_decode($request['productionChk'], true);
            $request['orderProgressChk'] = json_decode($request['orderProgressChk'], true);

            $this->$fncName($request, $dpStyle);
            exit();
        }
    }

    /**
     * 일반화면 다운로드
     * @param $request
     * @param $dpData
     */
    public function simpleExcelDownload1($request, $dpData){
        $dpData = [
            'no' => ['name'=>'번호','col'=>'3','skip'=>true],
            'customerName' => ['name'=>'고객사','col'=>'10','class'=>"text-left pdl5"],
            'projectNo' => ['name'=>'프로젝트번호','col'=>'5'],
            'projectTypeKr' => ['name'=>'타입','col'=>'5'],
            'projectStatusKr' => ['name'=>'프로젝트상태','col'=>'7'],
            'sno' => ['name'=>'고유번호','col'=>'5'],
            'styleFullName' => ['name'=>'스타일명','col'=>'13','class'=>"text-left pdl5"], //Check
            'productName' => ['name'=>'제품명','col'=>'10','class'=>"text-left pdl5"],
            'styleCode' => ['name'=>'스타일코드','col'=>'9','class'=>"text-left pdl5"],
            'reqFactoryNm' => ['name'=>'생산처','col'=>'10'],
            'prdExQty' => ['name'=>'수량','col'=>'','type'=>'number'],
            'salePrice' => ['name'=>'판매가','col'=>'','type'=>'number','class'=>'text-danger'],
            'prdCost'   => ['name'=>'생산가','col'=>'','type'=>'number','class'=>'sl-blue'],
            'margin' => ['name'=>'마진','col'=>'5','type'=>'number','afterContents'=>'%'],
            'prdYearSeason' => ['name'=>'시즌','col'=>'3'],
        ];

        $title = [];
        foreach($dpData as $dpValue){
            $title[] = $dpValue['name'];
        }
        $imsStyleService = SlLoader::cLoad('ims', 'imsStyleService');
        $list = $imsStyleService->getListStyle($request);

        $contents = [];

        foreach($list['list'] as $key => $val){
            $contentsRows = [];
            $contentsRows[] = ExcelCsvUtil::wrapTd($list['page']->idx-$key);
            foreach($dpData as $dpKey => $dpValue){
                if(true !== $dpValue['skip']){
                    $contentsRows[] = ExcelCsvUtil::wrapTd($val[$dpKey]);
                    //$excelBody .= ExcelCsvUtil::wrapTd($val['styleFullName'],null,null,'rowspan=' . $defaultRowspan);
                }
            }
            $contents[] = '<tr>'.implode('',$contentsRows).'</tr>';
        }
        $simpleExcelComponent = SlLoader::cLoad('Excel','SimpleExcelComponent','sl');
        $simpleExcelComponent->simpleDownload('스타일리스트', $title, implode('',$contents));
    }


    /**
     * 관리원단표
     * @param $request
     * @param $dpData
     */
    public function simpleExcelDownload2($request, $dpData){

        $dpData = [
            'no' => ['name'=>'번호','col'=>'3','skip'=>true],
            'customerName' => ['name'=>'고객사','col'=>'10'],
            'projectNo' => ['name'=>'프로젝트번호','col'=>'5'],
            'projectStatusKr' => ['name'=>'프로젝트상태','col'=>'7'],
            'sno' => ['name'=>'고유번호','col'=>'5'],
            'styleFullName' => ['name'=>'스타일명','col'=>'13','class'=>"text-left pdl5"], //Check
            'productName' => ['name'=>'제품명','col'=>'10','class'=>"text-left pdl5"],
            'styleCode' => ['name'=>'스타일코드','col'=>'9','class'=>"text-left pdl5"],
            'prdYearSeason' => ['name'=>'시즌','col'=>'3'],
            'reqFactoryNm' => ['name'=>'생산처','col'=>'10'],
            //Fabric Join .
            'fabricName' => ['name'=>'원단'],
            'position' => ['name'=>'위치'],
            'attached' => ['name'=>'부착위치'],
            'fabricMix' => ['name'=>'혼용률'],
            'color' => ['name'=>'컬러'],
            'spec' => ['name'=>'규격'],
            'meas' => ['name'=>'가요척'],
            'weight' => ['name'=>'중량'],
            'fabricWidth' => ['name'=>'원단폭'],
            'afterMake' => ['name'=>'후가공'],
            'makeNational' => ['name'=>'제조국'],
            'fabricStatusKr' => ['name'=>'퀄리티 확정상태'],
            'fabricConfirmInfo' => ['name'=>'퀄리티 확정정보'],
            'fabricMemo' => ['name'=>'퀄리티 메모'],
        ];

        $title = [];
        foreach($dpData as $dpValue){
            $title[] = $dpValue['name'];
        }
        $imsStyleService = SlLoader::cLoad('ims', 'imsStyleService');

        $request['addJoinTable'] = [
            'fabric' => //생산처 정보
                [
                    'data' => [ ImsDBName::FABRIC, 'JOIN', 'a.sno = fabric.styleSno and fabric.fabricStatus <> 5 and \'한국원단\' <> fabric.fabricName and \'중국원단\' <> fabric.fabricName and \'원단확보\' <> fabric.fabricName and \'시장원단\' <> fabric.fabricName  ' ] //수배완료건만.
                    , 'field' => [
                        'fabricName',
                        'position',
                        'attached',
                        'fabricMix',
                        'color',
                        'spec',
                        'meas',
                        'weight',
                        'fabricWidth',
                        'afterMake',
                        'makeNational',
                        'fabricConfirmInfo',
                        'fabricMemo',
                    ]
                ]
        ];

        $list = $imsStyleService->getListStyle($request);

        $contents = [];

        foreach($list['list'] as $key => $val){
            $contentsRows = [];
            $contentsRows[] = ExcelCsvUtil::wrapTd($list['page']->idx-$key);
            foreach($dpData as $dpKey => $dpValue){
                if(true !== $dpValue['skip']){
                    $contentsRows[] = ExcelCsvUtil::wrapTd($val[$dpKey]);
                    //$excelBody .= ExcelCsvUtil::wrapTd($val['styleFullName'],null,null,'rowspan=' . $defaultRowspan);
                }
            }
            $contents[] = '<tr>'.implode('',$contentsRows).'</tr>';
        }

        $simpleExcelComponent = SlLoader::cLoad('Excel','SimpleExcelComponent','sl');
        $simpleExcelComponent->simpleDownload('스타일_관리원단_리스트', $title, implode('',$contents));
    }

    /**
     * 원자재표
     * @param $request
     * @param $dpData
     */
    public function simpleExcelDownload3($request, $dpData){

        $dpData = [
            'no' => ['name'=>'번호','col'=>'3','skip'=>true],
            'customerName' => ['name'=>'고객사','col'=>'10'],
            'projectNo' => ['name'=>'프로젝트번호','col'=>'5'],
            'projectStatusKr' => ['name'=>'프로젝트상태','col'=>'7'],
            'sno' => ['name'=>'고유번호','col'=>'5'],
            'styleFullName' => ['name'=>'스타일명','col'=>'13','class'=>"text-left pdl5"], //Check
            'productName' => ['name'=>'제품명','col'=>'10','class'=>"text-left pdl5"],
            'styleCode' => ['name'=>'스타일코드','col'=>'9','class'=>"text-left pdl5"],
            'prdYearSeason' => ['name'=>'시즌','col'=>'3'],
            'reqFactoryNm' => ['name'=>'생산처','col'=>'10'],
            //Fabric Join .
            'contents' => ['name'=>'원부자재정보'],
        ];

        $title = [];
        foreach($dpData as $dpValue){
            $title[] = $dpValue['name'];
        }
        $imsStyleService = SlLoader::cLoad('ims', 'imsStyleService');

        $request['addJoinTable'] = [
            'estimate' => //생산처 정보
                [
                    'data' => [ ImsDBName::ESTIMATE, 'JOIN', 'a.sno = estimate.styleSno and a.prdCostConfirmSno = estimate.sno' ] //수배완료건만.
                    , 'field' => [
                        'contents',
                        'sno as estimateSno',
                    ]
                ]
        ];

        $list = $imsStyleService->getListStyle($request);

        $contents = [];

        foreach($list['list'] as $key => $val){
            $contentsRows = [];
            $contentsRows[] = ExcelCsvUtil::wrapTd($list['page']->idx-$key);
            foreach($dpData as $dpKey => $dpValue){
                if(true !== $dpValue['skip']){
                    if( 'contents' !== $dpKey ){
                        $contentsRows[] = ExcelCsvUtil::wrapTd($val[$dpKey]);
                    }else{
                        $decodeContents = json_decode($val[$dpKey], true);

                        $subTable = "<table>";
                        foreach( $decodeContents['fabric'] as $fabric){
                            $subTable .= "<tr>";
                            $subTable .= "<td>{$fabric['no']}</td>";
                            $subTable .= "<td>{$fabric['fabricName']}</td>";
                            $subTable .= "<td>{$fabric['fabricCompany']}</td>";
                            $subTable .= "<td>{$fabric['fabricMix']}</td>";
                            $subTable .= "<td>{$fabric['color']}</td>";
                            $subTable .= "<td>{$fabric['spec']}</td>";
                            $subTable .= "<td>{$fabric['meas']}</td>";
                            $subTable .= "<td>{$fabric['unitPrice']}</td>";
                            $subTable .= "<td>{$fabric['price']}</td>";
                            $subTable .= "<td>{$fabric['memo']}</td>";
                            $subTable .= "<td>{$fabric['makeNational']}</td>";
                            $subTable .= "</tr>";
                        }
                        $subTable .= "</table>";

                        $contentsRows[] = ExcelCsvUtil::wrapTd($subTable);
                    }
                }
            }
            $contents[] = '<tr>'.implode('',$contentsRows).'</tr>';
        }

        $simpleExcelComponent = SlLoader::cLoad('Excel','SimpleExcelComponent','sl');
        $simpleExcelComponent->simpleDownload('스타일_생산가_원단', $title, implode('',$contents));
    }


    /**
     * 부자재표
     * @param $request
     * @param $dpData
     */
    public function simpleExcelDownload4($request, $dpData){

        $dpData = [
            'no' => ['name'=>'번호','col'=>'3','skip'=>true],
            'customerName' => ['name'=>'고객사','col'=>'10'],
            'projectNo' => ['name'=>'프로젝트번호','col'=>'5'],
            'projectStatusKr' => ['name'=>'프로젝트상태','col'=>'7'],
            'sno' => ['name'=>'고유번호','col'=>'5'],
            'styleFullName' => ['name'=>'스타일명','col'=>'13','class'=>"text-left pdl5"], //Check
            'productName' => ['name'=>'제품명','col'=>'10','class'=>"text-left pdl5"],
            'styleCode' => ['name'=>'스타일코드','col'=>'9','class'=>"text-left pdl5"],
            'prdYearSeason' => ['name'=>'시즌','col'=>'3'],
            'reqFactoryNm' => ['name'=>'생산처','col'=>'10'],
            //Fabric Join .
            'contents' => ['name'=>'원부자재정보'],
        ];

        $title = [];
        foreach($dpData as $dpValue){
            $title[] = $dpValue['name'];
        }
        $imsStyleService = SlLoader::cLoad('ims', 'imsStyleService');

        $request['addJoinTable'] = [
            'estimate' => //생산처 정보
                [
                    'data' => [ ImsDBName::ESTIMATE, 'JOIN', 'a.sno = estimate.styleSno and a.prdCostConfirmSno = estimate.sno' ] //수배완료건만.
                    , 'field' => [
                        'contents',
                        'sno as estimateSno',
                    ]
                ]
        ];

        $list = $imsStyleService->getListStyle($request);

        $contents = [];

        foreach($list['list'] as $key => $val){
            $contentsRows = [];
            $contentsRows[] = ExcelCsvUtil::wrapTd($list['page']->idx-$key);
            foreach($dpData as $dpKey => $dpValue){
                if(true !== $dpValue['skip']){
                    if( 'contents' !== $dpKey ){
                        $contentsRows[] = ExcelCsvUtil::wrapTd($val[$dpKey]);
                    }else{
                        $decodeContents = json_decode($val[$dpKey], true);

                        $subTable = "<table>";
                        foreach( $decodeContents['subFabric'] as $fabric){
                            $subTable .= "<tr>";
                            $subTable .= "<td>{$fabric['no']}</td>";
                            $subTable .= "<td>{$fabric['subFabricName']}</td>";
                            $subTable .= "<td>{$fabric['subFabricMix']}</td>";
                            $subTable .= "<td>{$fabric['company']}</td>";
                            $subTable .= "<td>{$fabric['color']}</td>";
                            $subTable .= "<td>{$fabric['spec']}</td>";
                            $subTable .= "<td>{$fabric['meas']}</td>";
                            $subTable .= "<td>{$fabric['unitPrice']}</td>";
                            $subTable .= "<td>{$fabric['price']}</td>";
                            $subTable .= "<td>{$fabric['memo']}</td>";
                            $subTable .= "</tr>";
                        }
                        $subTable .= "</table>";

                        $contentsRows[] = ExcelCsvUtil::wrapTd($subTable);
                    }
                }
            }
            $contents[] = '<tr>'.implode('',$contentsRows).'</tr>';
        }

        $simpleExcelComponent = SlLoader::cLoad('Excel','SimpleExcelComponent','sl');
        $simpleExcelComponent->simpleDownload('스타일_생산가_부자재', $title, implode('',$contents));
    }


}