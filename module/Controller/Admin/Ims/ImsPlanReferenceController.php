<?php

namespace Controller\Admin\Ims;

use App;
use Component\Category\BrandAdmin;
use Component\Category\CategoryAdmin;
use Component\Database\DBTableField;
use Component\Ims\EnumType\PREPARED_TYPE;
use Component\Ims\ImsCodeMap;
use Component\Ims\ImsDBName;
use Component\Ims\ImsJsonSchema;
use Component\Ims\NkCodeMap;
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
use SlComponent\Util\PhpExcelUtil;
use SlComponent\Util\SlCodeMap;
use SlComponent\Util\SlLoader;
use function SlComponent\Util\SlLoader;

/**
 * 스타일 기획 레퍼런스
 * Class ImsAccountListController
 * @package Controller\Admin\Ims
 */
class ImsPlanReferenceController extends \Controller\Admin\Controller{

    use ImsPsNkTrait;
    use ImsControllerTrait;

    public function index() {
        $request = \Request::request()->toArray();
        $iInfoType = (int)$request['iInfoType'];
        $this->setData('iInfoType', $iInfoType);
        $sAppendInfoName = '';
        switch ($iInfoType) {
            case 1: $sAppendInfoName = '브랜드'; break;
            case 2: $sAppendInfoName = '컨셉'; break;
            case 3: $sAppendInfoName = '디자인'; break;
            case 4: $sAppendInfoName = '부가기능'; break;
        }
        $this->setData('sAppendInfoName', $sAppendInfoName);

        $this->callMenu('ims', 'customer', 'planRef');
        $this->setDefault();

        //검색항목
        if ($iInfoType == 0) {
//            $search['combineSearch'] = [
//                'mate.materialName' => '자재명',
//                'a.refName' => '레퍼런스명',
//            ];
        } else {
            $search['combineSearch'] = [
                'infoName' => $sAppendInfoName.'명',
            ];
        }
        $this->setData('search', $search);
    }

}