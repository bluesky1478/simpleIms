<?php

namespace Component\Imsv2;

use App;
use Component\Database\DBTableField;
use Component\Ims\ImsCodeMap;
use Component\Ims\ImsDBName;
use Component\Ims\ImsServiceConditionTrait;
use Component\Ims\ImsServiceSortTrait;
use Component\Ims\ImsServiceTrait;
use Component\Imsv2\Util\ImsProjectListServiceUtil;
use Component\Member\Manager;
use Component\Sms\Code;
use Controller\Admin\Ims\Step\ImsStepTrait;
use Framework\Debug\Exception\AlertBackException;
use Framework\Utility\DateTimeUtils;
use LogHandler;
use Request;
use Session;
use SiteLabUtil\SlCommonUtil;
use SlComponent\Database\DBUtil2;
use SlComponent\Database\SearchVo;
use SlComponent\Util\SitelabLogger;
use SlComponent\Util\SlLoader;

/**
 * IMSv2 관리 제품 서비스
 * Class GoodsStock
 * @package Component\Goods
 */
class ImsProductService
{
    use ImsServiceTrait;
    use ImsServiceConditionTrait;

    private $sql;

    public function __construct(){
        $this->sql = \App::load('\\Component\\Imsv2\\Sql\\ImsProductServiceSql');
    }

    /**
     * Ims 스타일 반환
     * @param $params
     * @param string $delFl 삭제여부
     * @return array
     */
    public function getProductList($params, $delFl='n'){
        $searchVo = $this->setCommonCondition($params, new SearchVo('prd.delFl=?', $delFl));
        return DBUtil2::getComplexList($this->sql->getProductList(),$searchVo, false, false, false);
    }

}


