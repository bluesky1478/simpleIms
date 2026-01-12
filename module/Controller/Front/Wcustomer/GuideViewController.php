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

namespace Controller\Front\Wcustomer;

use App;
use Component\Work\DocumentCodeMap;
use Component\Work\WorkCodeMap;
use Framework\Debug\Exception\AlertCloseException;
use Globals;
use Request;
use Session;
use SiteLabUtil\SlCommonUtil;
use SlComponent\Util\SlLoader;
use UserFilePath;

/**
 * 고객 - 유니폼 디자인 가이드
 */
class GuideViewController extends \Controller\Front\Controller
{
    public function index() {
        $workCustomerService = SlLoader::cLoad('workCustomer','workCustomerService','');
        $workCustomerService->setListData($this, 'ORDER2', 10);

        $request = \Request::request()->toArray();

        $orderData = $this->getData('documentData');

        $projectService = SlLoader::cLoad('work','projectService','');
        $projectDocumentData = $projectService->getProjectDocument(['projectSno'=>$this->getData('projectSno'),'sno'=>$request['sno']]);

        $this->setData('documentSno',  $request['sno'] );

        $viewID = gd_isset($request['view'],'info1');

        $activeTargetIdList = [
          'info', 'prd', 'plan', 'mall'
        ];
        $activeMenu = [];
        foreach($activeTargetIdList as $eachKey){
            if (strpos($viewID, $eachKey) !== false){
                $activeMenu[$eachKey] = 'active';
            }else{
                $activeMenu[$eachKey] = '';
            }
        }

        $this->setData('viewID',  $viewID);
        $this->setData('activeMenu',  $activeMenu);
        $this->setData('queryString',  "guide_view.php?key={$request['key']}&sno={$request['sno']}" );

        $nextMap = [
          'info1' => [ 'prdAll1', '' ],
          'info2' => ['prdAll1', '' ],
          'prdAll1' => ['prd0', '1' ],
          'plan1' => ['mall1', '' ],
          'mall1' => ['mall2', '' ],
          'mall2' => ['info3', '' ],
          'info3' => ['', '1' ],
        ];
        foreach( $orderData['docData']['sampleData'] as $key => $value) {
            if( $key == count($orderData['docData']['sampleData'])-1  ){ //마지막
                $nextMap['prd' . $key] = [ 'plan1', '1' ] ;
            }else{
                $nextMap['prd' . $key] = [ 'prd'.($key+1), '1'] ;
            }
        }

        $this->setData('nextViewID',  $nextMap[$viewID][0] );
        $this->setData('isLoadView',  $nextMap[$viewID][1] );
        $this->setData('selectedPrdNo',  preg_replace("/[^0-9]*/s", "", $viewID) );
        $optionOrderData = [];

        foreach( $orderData['docData']['sampleData'] as $key => $sampleData ){
            $optionKeyArray = [];
            foreach( $sampleData['optionList'] as $optionKey => $optionData){
                $optionKeyArray[] = $optionData['optionName'];
            }
            $optionKey = md5(implode('', $optionKeyArray));
            $optionOrderData[$optionKey][] = $sampleData;
        }

        $this->setData('optionOrderData',  $optionOrderData);
        //gd_debug( $projectDocumentData['mallData']['docData'] );
        $this->setData('mallData',  $projectDocumentData['mallData']['docData']['mallData']);
        $this->setData('privateMallItem', DocumentCodeMap::PRIVATE_MALL_ITEM);
        //$this->setData('commentList', json_encode($orderData.docData.commentList));
    }

}

