<?php
namespace SlComponent\Util;

use SiteLabUtil\SlCommonUtil;
use Exception;
use Request;

/**
 * 컨트롤러에서 공통으로 사용하는 Trait
 */
Trait ApiTrait {

    public function getDefaultApiUrl(){
        return \Request::getScheme()."://gdadmin.".\Request::getDefaultHost();
    }

    public function setClaimApiUrl($controller){
        $url =  $this->getDefaultApiUrl().'/ajax/claim_service.php';
        $controller->setData('claimApiUrl',$url);
    }
}
