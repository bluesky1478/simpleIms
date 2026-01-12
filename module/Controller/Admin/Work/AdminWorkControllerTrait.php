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

namespace Controller\Admin\Work;


use Component\Work\DocumentCodeMap;
use Component\Work\WorkCodeMap;
use SiteLabUtil\SlCommonUtil;
use SlComponent\Util\SlLoader;
use Request;
use SlComponent\Util\SlProjectCodeMap;

trait AdminWorkControllerTrait {

    public function index(){
        $this->addScript([
            'jquery/jquery.multi_select_box.js',
        ]);

        $workControllerService=SlLoader::cLoad('work','workControllerService','');
        $workControllerService->setControllerData($this);

        $this->setData('COMP_TYPE', WorkCodeMap::COMP_TYPE);
        $this->setData('COMP_DIV', WorkCodeMap::COMP_DIV);
        $this->setData('PROPOSAL_TYPE', WorkCodeMap::PROPOSAL_TYPE);
        $this->setData('MS_PROPOSAL_TYPE', WorkCodeMap::MS_PROPOSAL_TYPE);

        $managerList = SlCommonUtil::getManagerList();
        $this->setData('managerList' , $managerList);
        $this->setData('managerInfo' , \Session::get('manager'));
        $this->setData('deptList' , SlCommonUtil::getDeptList());

        $requestList = \Request::request()->toArray();
        $this->setData('requestParam' , $requestList);

        $documentService=SlLoader::cLoad('work','documentService','');
        $emailPreviewData = $documentService->getSendEmailData($requestList['sno'], 'empty@mail.com');

        $this->setData('customerPreviewLink' , $emailPreviewData['link']);
        $this->setData('SEASON_TYPE', WorkCodeMap::SEASON);

        $this->setData('PRJ_STATUS', SlProjectCodeMap::PRJ_STATUS);
        $this->setData('PRJ_DOCUMENT', SlProjectCodeMap::PRJ_DOCUMENT);

        $this->workIndex();
    }

    /**
     * @return mixed
     */
    public function getParam(){
        return Request::request()->toArray();
    }

}
