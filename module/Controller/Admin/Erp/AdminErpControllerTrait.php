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
 * @link      http://www.godo.co.kr
 */

namespace Controller\Admin\Erp;


use Component\Erp\ErpCodeMap;
use Component\Ims\ImsCodeMap;
use Component\Work\DocumentCodeMap;
use Component\Work\WorkCodeMap;
use SiteLabUtil\SlCommonUtil;
use SlComponent\Util\SlCodeMap;
use SlComponent\Util\SlLoader;
use Request;
use SlComponent\Util\SlProjectCodeMap;

trait AdminErpControllerTrait {

    public function index(){
        $this->addScript([
            'jquery/jquery.multi_select_box.js',
        ]);

        $this->addCss([
            '../../css/font_awesome/css/font-awesome.css',
        ]);

        $managerList = SlCommonUtil::getManagerList();
        $this->setData('managerList' , $managerList);
        $this->setData('managerInfo' , \Session::get('manager'));
        $this->setData('deptList' , SlCommonUtil::getDeptList());
        $requestList = \Request::request()->toArray();
        $this->setData('requestParam' , $requestList);
        $this->setData('inoutReason' , array_flip(ErpCodeMap::ERP_STOCK_REASON) );

        $mId = \Session::get('manager.managerId');
        if( in_array($mId, ImsCodeMap::SALES_COMPANY_MANAGER) ){
            $this->setData('isSalesCompany' , true);
        }

        $this->workIndex();
    }

    /**
     * @return mixed
     */
    public function getParam(){
        return Request::request()->toArray();
    }

}
