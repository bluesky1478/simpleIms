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
 * @link http://www.godo.co.kr
 */

namespace Controller\Front\Partner;

use App;
use Component\Work\WorkCodeMap;
use Controller\Front\Work\WorkControllerTrait;
use Framework\Debug\Exception\AlertCloseException;
use Globals;
use Request;
use Session;
use SiteLabUtil\SlCommonUtil;
use SlComponent\Util\SlLoader;
use SlComponent\Util\SlProjectCodeMap;
use UserFilePath;

/**
 * 프로젝트 리스트
 * @author Lee Hakyoung <haky2@godo.co.kr>
 */
class StockListController extends \Controller\Front\Controller
{

    public function index() {
        //로그인 여부 확인
        if( empty(\Session::get('manager'))){
            $linkUrl = \Request::getPhpSelf() . '?' . \Request::getQueryString();
            $this->redirect('login.php?linkUrl=' . $linkUrl);
            exit();
        }else{
            $workControllerService=SlLoader::cLoad('work','workControllerService','');
            $workControllerService->setControllerData($this);
            $this->workIndex();
        }
    }

    public function setMenu($docDept, $docType){

        $projectListMap = [
            1=>[
                'name' => '주문요청',
                'accept' => 'y',
                'href' => 'order_req.php',
            ],
            2=>[
                'name' => '출고 리스트',
                'accept' => 'y',
                'href' => 'order_list.php',
            ],
            3=>[
                'name' => '재고 리스트',
                'accept' => 'y',
                'href' => 'stock_list.php',
            ]
        ];
        if( 'PROJECT' === $docDept ){
            $projectListMap[$docType]['active']='active';
        }
        $menuList = [
            'PROJECT' => [
                'title' => '주문관리',
                'subMenuList' => $projectListMap,
            ],
        ];
        $this->setData('title' , $menuList[$docDept]['title']);
        $this->setData('titleSub', $menuList[$docDept]['subMenuList'][$docType]['name']);

        $this->setData('menuList', $menuList);
    }

    public function workIndex() {
        $this->setMenu('PROJECT', 3);
        $this->setData('headerSaveButtonName', '등록하기'); //Header 등록 버튼 여부
        $this->setData('isTempSaveButtonFl',false); //Header 등록 버튼 여부

        //$projectService=SlLoader::cLoad('work','workService','');
        $projectListService = \App::load(\Controller\Admin\Work\ControllerService\ProjectListService::class);
        $projectListData = $projectListService->getList([
            'pageNum' => 99999,
            'treatDate' => [
                0=>'3000-01-01',
                1=>'3000-01-01'
            ],
        ]);
        //gd_debug($dataList);
        $this->setData('dataList' , $projectListData['data']);
        $this->setData('regLocation' , $base = \Request::getScheme()."://gdadmin.".\Request::getDefaultHost().'/work/project_list.php'  );
        $this->setData('regLocation2' , $base = \Request::getScheme()."://gdadmin.".\Request::getDefaultHost().'/work/project_view.php'  );
    }

}

