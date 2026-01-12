<?php


namespace Controller\Admin\Ims;

use Component\Ims\ImsDBName;
use Component\Ims\NkCodeMap;
use SiteLabUtil\SlCommonUtil;
use SlComponent\Database\DBUtil2;
use SlComponent\Database\SearchVo;
use SlComponent\Util\SlLoader;

trait ImsPsListTrait
{

    /**
     * 전체 리스트 반환
     * @param $params
     * @return string[]
     */
    public function getIms25AllList($params) {
        $service = SlLoader::cLoad('ims25','ims25ListService');
        return ['msg'=>'조회 완료','data'=>$service->getIms25List('all', $params)];
    }


}