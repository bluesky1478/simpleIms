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

namespace Controller\Admin\Ims\ControllerService;


use Component\Erp\ErpCodeMap;
use Component\Ims\ImsCodeMap;
use Component\Work\DocumentCodeMap;
use Component\Work\WorkCodeMap;
use SiteLabUtil\SlCommonUtil;
use SlComponent\Util\SlCodeMap;
use SlComponent\Util\SlLoader;
use Request;
use SlComponent\Util\SlProjectCodeMap;

trait ImsListServiceTrait {
    private $sql;
    private $search;

    public function __construct(){
        $this->sql =  SlLoader::sqlLoad(__CLASS__);
        $this->runConstructionAddMethod();
    }
    public function getSearch()
    {
        return $this->search;
    }
    public function setSearch($search)
    {
        $this->search = $search;
    }
    /**
     * 리스트 타이틀 목록 반환
     * @param $searchData
     * @return string[]
     */
    public function getTitle($searchData): array
    {
        return self::LIST_TITLES;
    }
    //재정의 해서 사용
    public function runConstructionAddMethod(){}

    public function isProduceCompany(){
        $mId = \Session::get('manager.managerId');
        if( in_array($mId, ImsCodeMap::PRODUCE_COMPANY_MANAGER) ){
            return true;
        }else{
            return false;
        }
    }


}
