<?php

namespace Controller\Admin\Sales;

use App;
use Globals;
use Request;
use SiteLabUtil\SlCommonUtil;
use SlComponent\Database\DBUtil2;
use SlComponent\Database\SearchVo;
use SlComponent\Util\SlLoader;

class SalesViewController extends \Controller\Admin\Controller{

    public function index(){
        $this->addScript([
            '../../script/vue.js',
        ]);
        $this->addCss([
            '../../css/preloader.css',
        ]);

        $getValue = Request::request()->toArray();
        $recapData = $this->getProduceData($getValue['salesSno']);

        $callData = $this->getCallData($getValue['salesSno']);
        $this->setData('callData', $callData);
        $this->setData('recapData', $recapData);
        $this->getView()->setDefine('layout', 'layout_blank.php');
    }

    public function getProduceData($sno){
        $recapData = DBUtil2::getOne('sl_salesCustomerInfo','sno',$sno);
        $regManager = DBUtil2::getOne(DB_MANAGER, 'sno', $recapData['regManagerSno']);
        $modManager = DBUtil2::getOne(DB_MANAGER, 'sno', $recapData['lastManagerSno']);
        $recapData['regManagerName'] = empty($regManager['managerNm']) ? '시스템':$regManager['managerNm'];
        $recapData['modManagerName'] = $modManager['managerNm'];

        return $recapData;
    }

    public function getCallData($sno){
        $tableList= [
            'a' => //메인
                [
                    'data' => [ 'sl_salesCustomerContents' ]
                    , 'field' => ['*']
                ]
            , 'b' => //등록자
                [
                    'data' => [ DB_MANAGER, 'JOIN', 'a.regManagerSno = b.sno' ]
                    , 'field' => ['managerNm as regManagerName']
                ]
        ];
        $table = DBUtil2::setTableInfo($tableList);
        $searchVo = new SearchVo('a.salesSno=?', $sno);
        $searchVo->setOrder('a.regDt desc');

        return DBUtil2::getComplexList($table, $searchVo);
        //getComplexList(array $tableList,SearchVo $searchVo, $isAllCountMode=false, $isDebug = false, $isStrip = true){
    }

}