<?php

namespace Controller\Admin\Ims;

use App;
use Component\Category\BrandAdmin;
use Component\Category\CategoryAdmin;
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
class ImsCustomerListController extends \Controller\Admin\Controller{

    use ImsControllerTrait;

    public function index(){
        $request = \Request::request()->toArray();

        $this->callMenu('ims', 'customer', 'list');
        $this->setDefault();

        //검색항목
        $search['combineSearch'] = [
            'cust.customerName' => '고객명',
            'sales.managerNm' => '영업담당자',
        ];
        $this->setData('search', $search);
        $this->setData('firstYear', date("y", strtotime("-2 year")));

        if(  !empty($request['simple_excel_download'])  ){
            $request['page'] = 1;
            $request['pageNum'] = 15000;
            $this->simpleExcelDownload($request);
            exit();
        }
    }


    public function simpleExcelDownload($request){
        $imsService = SlLoader::cLoad('imsv2','ImsCustomerListService');
        $orgTitleList = $imsService->getField();
        $list = $imsService->getCustomerList($request);

        $titles = [];
        $titles[] = '번호';
        $contents = [];

        $titleList=[];
        foreach($orgTitleList as $title){
            if(!$title['skip']){
                $titleList[] = $title;
            }
        }
        foreach($titleList as $title){
            $titles[] = $title['title'];
        }

        foreach($list['list'] as $key => $val){
            $contentsRows = [];
            $contentsRows[] = ExcelCsvUtil::wrapTd($list['page']->idx-$key);
            foreach($titleList as $dpKey => $dpValue){
                if( 'c' === $dpValue['type'] ){
                    $isPass = false;
                    $firstYear = date("y", strtotime("-2 year"));
                    for($n=0; 2>=$n; $n++){
                        if( 'sum'. ($firstYear+$n) .'Cost' === $dpValue['name'] ){
                            if('y'===$request['chkRtw']) { //기성 제외
                                $contentsRows[] = ExcelCsvUtil::wrapTd(number_format($val['customerYearPrice'][$firstYear+$n]['customerCost'] - $val['customerYearPrice'][$firstYear+$n]['customerRtwCost'] ));
                            }else if('n'===$request['chkRtw']) { //기성
                                $contentsRows[] = ExcelCsvUtil::wrapTd($val['customerYearPrice'][$firstYear+$n]['customerRtwCost']);
                            }else{
                                $contentsRows[] = ExcelCsvUtil::wrapTd(number_format($val['customerYearPrice'][$firstYear+$n]['customerCost']));
                            }
                            $isPass = true;
                        }else if( 'sum'. ($firstYear+$n) .'Price' === $dpValue['name']  ){
                            if('y'===$request['chkRtw']) { //기성 제외
                                $contentsRows[] = ExcelCsvUtil::wrapTd(number_format($val['customerYearPrice'][$firstYear + $n]['customerPrice'] - $val['customerYearPrice'][$firstYear + $n]['customerRtwPrice']));
                            }else if('n'===$request['chkRtw']) { //기성 제외
                                $contentsRows[] = ExcelCsvUtil::wrapTd($val['customerYearPrice'][$firstYear+$n]['customerRtwPrice']);
                            }else{
                                $contentsRows[] = ExcelCsvUtil::wrapTd(number_format($val['customerYearPrice'][$firstYear+$n]['customerPrice']));
                            }
                            $isPass = true;
                        }else if( 'sum'. ($firstYear+$n) .'Margin' === $dpValue['name']  ){
                            if('y'===$request['chkRtw']) { //기성 제외
                                $contentsRows[] = ExcelCsvUtil::wrapTd(
                                    SlCommonUtil::getMargin($val['customerYearPrice'][$firstYear + $n]['customerPrice'] - $val['customerYearPrice'][$firstYear + $n]['customerRtwPrice'], $val['customerYearPrice'][$firstYear + $n]['customerCost'] - $val['customerYearPrice'][$firstYear + $n]['customerRtwCost']) . '%'
                                );
                            }else if('n'===$request['chkRtw']) { //기성 제외
                                $contentsRows[] = ExcelCsvUtil::wrapTd(
                                    SlCommonUtil::getMargin($val['customerYearPrice'][$firstYear+$n]['customerRtwPrice'], $val['customerYearPrice'][$firstYear+$n]['customerRtwCost']).'%'
                                );
                            }else{
                                $contentsRows[] = ExcelCsvUtil::wrapTd(
                                    SlCommonUtil::getMargin($val['customerYearPrice'][$firstYear+$n]['customerPrice'], $val['customerYearPrice'][$firstYear+$n]['customerCost']).'%'
                                );
                            }

                            $isPass = true;
                        }
                    }
                    if( 'custNameAndCode' === $dpValue['name'] ){
                        $contentsRows[] = ExcelCsvUtil::wrapTd($val['customerName']);
                    }else if( 'customerCost' === $dpValue['name'] ){
                        if('y'===$request['chkRtw']){ //기성 제외
                            $contentsRows[] = ExcelCsvUtil::wrapTd(number_format($val['customerCost']-$val['customerRtwCost']));
                        }else{
                            $contentsRows[] = ExcelCsvUtil::wrapTd(number_format($val[$dpValue['name']]));
                        }
                    }else if( 'customerPrice' === $dpValue['name'] ){
                        if('y'===$request['chkRtw']){ //기성 제외
                            $contentsRows[] = ExcelCsvUtil::wrapTd(number_format($val['customerPrice']-$val['customerRtwPrice']));
                        }else{
                            $contentsRows[] = ExcelCsvUtil::wrapTd(number_format($val[$dpValue['name']]));
                        }
                    }else if( 'customerMargin' === $dpValue['name'] ){
                        if('y'===$request['chkRtw']){ //기성 제외
                            $margin = SlCommonUtil::getMargin($val['customerPrice'], $val['customerCost']).'%';
                            $contentsRows[] = ExcelCsvUtil::wrapTd($margin);
                        }else{
                            $margin = SlCommonUtil::getMargin($val['customerPrice']-$val['customerRtwPrice'], $val['customerCost']-$val['customerRtwCost']).'%';
                            $contentsRows[] = ExcelCsvUtil::wrapTd($margin);
                        }
                    }else{
                        if(!$isPass){
                            $contentsRows[] = ExcelCsvUtil::wrapTd($val[$dpValue['name']]);
                        }
                    }
                }else{
                    if('i' === $dpValue['type']){
                        $contentsRows[] = ExcelCsvUtil::wrapTd(number_format($val[$dpValue['name']]));
                    }else{
                        $contentsRows[] = ExcelCsvUtil::wrapTd($val[$dpValue['name']]);
                    }
                }
            }
            $contents[] = '<tr>'.implode('',$contentsRows).'</tr>';
        }

        $simpleExcelComponent = SlLoader::cLoad('Excel','SimpleExcelComponent','sl');
        $simpleExcelComponent->simpleDownload('고객사리스트', $titles, implode('',$contents));

    }

}