<?php

namespace Controller\Admin\Erp;

use App;
use Component\Category\BrandAdmin;
use Component\Category\CategoryAdmin;
use Component\Ims\ImsCodeMap;
use Component\Ims\ImsDBName;
use Component\Stock\StockListService;
use Component\Work\WorkCodeMap;
use Controller\Admin\Erp\AdminErpControllerTrait;
use Controller\Admin\Erp\ControllerService\InoutListService;
use Controller\Admin\Ims\ImsControllerTrait;
use Controller\Admin\Ims\ImsListControllerTrait;
use Controller\Admin\Ims\Step\ImsStepTrait;
use Globals;
use Request;
use SiteLabUtil\SlCommonUtil;
use SlComponent\Database\DBUtil2;
use SlComponent\Database\SearchVo;
use SlComponent\Util\ExcelCsvUtil;
use SlComponent\Util\SitelabLogger;
use SlComponent\Util\SlCodeMap;
use SlComponent\Util\SlLoader;
use function SlComponent\Util\SlLoader;

/**
 * 폐쇄몰 재고 관리 (상세)
 */
class StockReservedPopupController extends \Controller\Admin\Controller{

    use ImsControllerTrait;

    public function index(){
        $this->setDefault();
        $this->getView()->setDefine('layout', 'layout_blank.php');
        $reqParam=$this->getData('requestParam');
        $goodsInfo = DBUtil2::getOne(DB_GOODS, 'goodsNo', $reqParam['goodsNo']);
        $this->setData('goodsNm', $goodsInfo['goodsNm']);
        $scmSno = $goodsInfo['scmNo'];

        $optionCode = SlCommonUtil::getOnlyNumber($reqParam['optionCode']);
        if(!empty($optionCode)){
            $optionInfo = DBUtil2::getOne(DB_GOODS_OPTION, 'sno', $optionCode);
            $this->setData('optionInfo', $optionInfo);
            $this->setData('optionName', $optionInfo['optionValue1'].$optionInfo['optionValue2'].$optionInfo['optionValue3'].$optionInfo['optionValue4'].$optionInfo['optionValue5']);
        }

        $stockService = SlLoader::cLoad('imsv2','ImsStockService');
        $thirdPartyCategory = $stockService->get3PlPrdAttr(['scmSno'=>$scmSno]);

        $this->setData('thirdPartyCategory', $thirdPartyCategory);
        $this->setData('scmSno', $scmSno);

        $search['combineSearch'] = [
            'productName' => '상품명',
            'optionName' => '옵션',
            'thirdPartyProductCode' => '코드',
        ];
        $search['ioSearch'] = [
            'ioHis.thirdPartyProductCode' => '상품코드',
            'tp.productName' => '상품명',
            'tp.optionName' => '옵션명',
        ];

        $this->setData('search', $search);

        $current_page = \Request::getRequestUri();
        if (!empty( \Session::get('view_last_page') ) && \Session::get('view_last_page') === $current_page) {
            $this->setData('isReload', 'y');
        } else {
            \Session::set('view_last_page',$current_page);
            $this->setData('isReload', 'n');
        }
    }

}