<?php

namespace Controller\Admin\Ims;

use App;
use Component\Category\BrandAdmin;
use Component\Category\CategoryAdmin;
use Component\Ims\ImsCodeMap;
use Component\Ims\ImsDBName;
use Component\Stock\StockListService;
use Component\Work\WorkCodeMap;
use Controller\Admin\Erp\AdminErpControllerTrait;
use Controller\Admin\Erp\ControllerService\InoutListService;
use Globals;
use Request;
use SiteLabUtil\SlCommonUtil;
use SlComponent\Database\DBUtil2;
use SlComponent\Database\SearchVo;
use SlComponent\Util\ExcelCsvUtil;
use SlComponent\Util\SlCodeMap;
use SlComponent\Util\SlLoader;
use function SlComponent\Util\SlLoader;

/**
 * 문서 리스트
 */
class ImsProductController extends \Controller\Admin\Controller{

    use ImsControllerTrait;

    public function index(){
        //$this->callMenu('ims', 'project', 'all'); //TODO : 리스트에 따라 변경될 수 있음.
        $this->setDefault();

        $projectSno = $this->getData('requestParam')['projectSno'];
        $styleSno = $this->getData('requestParam')['sno'];

        $findStyleSno = -1;
        if(!empty($styleSno)){
            $this->setData('title', '스타일 정보');
            $this->setData('saveBtnTitle', '수정');
            $findStyleSno = $styleSno;
        }else{
            $this->setData('title', '스타일 등록');
            $this->setData('saveBtnTitle', '저장');
        }

        $this->getView()->setDefine('layout', 'layout_blank.php');

        $imsService = SlLoader::cLoad('ims', 'imsService');

        $year = 2020;
        $yearMap = [];
        for($i=0;15>=$i;$i++){
            $yearMap[$year+$i] = $year+$i;
        }
        $this->setData('codeYear', $yearMap);
        //시즌
        $this->setData('codeSeason', $imsService->getCode('style','시즌'));

        //성별
        $this->setData('codeGender', $imsService->getCode('style','성별'));
        //스타일
        $this->setData('codeStyle', $imsService->getCode('style','스타일'));
        //색상
        $this->setData('codeColor', $imsService->getCode('style','색상'));

        $this->setData('sizeOptionStandard', json_encode($this->getStandardSizeOption2()) );


        //등록화면 구분
        if( empty($styleSno) ){
            $this->getView()->setPageName("ims/ims_style_reg.php");
        }else{
            if( SlCommonUtil::isDevId() ){
                $this->getView()->setPageName("ims/ims_style_dev.php");
            }else{
                $this->getView()->setPageName("ims/ims_style.php");
            }
        }

        $this->setData('thumbnailFieldList', [
            ['title' => '썸네일','field' => 'fileThumbnail',],
        ]);

        if(!empty($styleSno)){
            $sql = "select a.sno, concat(b.customerName,' ', a.productName, ' ', REPLACE(a.styleCode, ' ','')) as productName, a.sizeSpec from sl_imsProjectProduct a join sl_imsCustomer b on a.customerSno = b.sno join sl_imsProject c on a.projectSno = c.sno  
                 where a.sno <> {$findStyleSno} and a.delFl = 'n' and a.sizeSpec <> '' order by productName"; //발주 완료 중 (동일 스펙 있을수도 있어서 ). and c.projectStatus = 90
            $defaultStyleSettingData =  DBUtil2::runSelect($sql,null,false);
            $this->setData('defaultStyleSettingDataPrd', SlCommonUtil::arrayAppKeyValue($defaultStyleSettingData,'sno','productName'));

            foreach($defaultStyleSettingData as $key => $each){
                $each['sizeSpec'] = json_decode($each['sizeSpec']);
                $defaultStyleSettingData[$key] = $each;
            }
            $this->setData('defaultStyleSettingDataSpec',  addslashes(json_encode(SlCommonUtil::arrayAppKeyValue($defaultStyleSettingData,'sno','sizeSpec'))));
        }else{
            $this->setData('defaultStyleSettingDataSpec',  '[]');
        }


        $cnt = 0;
        $divCnt = 5;
        $eworkType1 = [];
        $eworkType2 = [];
        foreach( ImsCodeMap::EWORK_TYPE as $key => $value){
            if($divCnt > $cnt){
                $eworkType1[$key] = $value;
            }else{
                $eworkType2[$key] = $value;
            }
            $cnt++;
        }
        $this->setData('eworkType1',  $eworkType1);
        $this->setData('eworkType2',  $eworkType2);

        $this->setData('workBtn1', [
            ['title' => '썸네일','field' => 'fileThumbnail',],
        ]);


        $styleSno = \Request::get()->get('sno');
        if( 0 >= DBUtil2::getCount(ImsDBName::EWORK, new SearchVo('styleSno=?', $styleSno)) ){
            DBUtil2::insert(ImsDBName::EWORK, ['styleSno'=>$styleSno]);
        }

    }

    public function getStandardSizeOption2(){
        $standardList = [];
        for($i=0; 9 > $i; $i++){
            $standardList['top'][] = 85 + ($i*5);
            $standardList['bottom'][] = 28 + ($i*2);
        }
        for($i=0; 20>$i; $i++){
            $standardList['bottomKepid'][] = (24 + $i);
        }
        $standardList['bottomKepid'][]=52;

        return $standardList;
    }

}