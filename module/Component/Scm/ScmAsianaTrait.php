<?php
namespace Component\Scm;

use App;
use Component\Database\DBTableField;
use Component\Ims\EnumType\APPROVAL_STATUS;
use Component\Ims\EnumType\TODO_STATUS;
use Component\Ims\EnumType\TODO_TYPE;
use Component\Member\Manager;
use Component\Member\Member;
use Component\Sms\Code;
use Framework\Debug\Exception\AlertBackException;
use Framework\Utility\DateTimeUtils;
use LogHandler;
use Request;
use Session;
use SiteLabUtil\SlCommonUtil;
use SlComponent\Database\DBUtil2;
use SlComponent\Database\SearchVo;
use SlComponent\Mail\SiteLabMailUtil;
use SlComponent\Util\SitelabLogger;
use SlComponent\Util\SlCodeMap;
use SlComponent\Util\SlKakaoUtil;
use SlComponent\Util\SlLoader;
use SlComponent\Util\SlSmsUtil;

/**
 * 아시아나 Trait
 * Class GoodsStock
 * @package Component\Goods
 */
trait ScmAsianaTrait {

    public function getAsianaCart($params){
        $service = SlLoader::cLoad('scm','ScmAsianaService');
        return $service->getCartList();
    }

}