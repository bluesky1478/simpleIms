<?php

namespace Controller\Admin\Ims;

use App;
use Component\Ims\ImsCodeMap;
use Component\Ims\ImsDBName;
use Globals;
use Request;
use SiteLabUtil\SlCommonUtil;
use SlComponent\Database\DBUtil2;
use SlComponent\Database\SearchVo;
use SlComponent\Util\SlLoader;

class CustomerViewController extends \Controller\Admin\Controller {

    use ImsControllerTrait;

    public function index(){

        $this->setDefault();

        $getValue = Request::request()->toArray();

        $imsService = SlLoader::cLoad('ims','imsService');
        $projectData = $imsService->getProject(['sno'=>$getValue['sno']]);
        $this->setData('projectData', $projectData);
        $this->setData('requestParam', $getValue);

        $this->setData('commentDivName', ImsCodeMap::PROJECT_COMMENT_DIV[$getValue['div']]);

        $list = $imsService->getCommentList($getValue['div'], $getValue['sno']);
        $this->setData('list', $list);

        if(!empty($getValue['commentSno'])){
            $commentData = DBUtil2::getOne(ImsDBName::PROJECT_COMMENT,'sno', $getValue['commentSno']);
            $this->setData('defaultData', $commentData['comment']);
        }

        $this->getView()->setDefine('layout', 'layout_blank.php');

        /*$managerId = \Session::get('manager.managerId');
        if( 'b1478'  === $managerId ){
            $this->getView()->setPageName('ims/customer_view_v3.php');
        }*/

    }


}

