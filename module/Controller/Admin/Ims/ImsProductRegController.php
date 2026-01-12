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
class ImsProductRegController extends \Controller\Admin\Controller{

    use ImsControllerTrait;

    public function index(){
        //$this->callMenu('ims', 'project', 'all'); //TODO : 리스트에 따라 변경될 수 있음.
        $this->setDefault();

        if(!empty($this->getData('requestParam')['sno'])){
            $this->setData('title', '스타일 정보');
            $this->setData('saveBtnTitle', '수정');
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
    }

}