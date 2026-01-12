<?php
namespace SlComponent\Util;

use SiteLabUtil\SlCommonUtil;

Trait SlPsTrait {

    public function index() {

        $requestValue = \Request::request()->toArray();

        if( empty($requestValue['mode']) ){
            $this->setJson(404, __('페이지가 없습니다. (mode empty)'));
        }else{
            try {
                $methodMap = SlCommonUtil::getReverseMap(get_class_methods(__CLASS__));
                if( empty($methodMap[$requestValue['mode']]) ){
                    $this->setJson(404, __('페이지가 없습니다. (mode undefined [' . $requestValue['mode'] . '])'  ));
                }else{
                    $fncName = $requestValue['mode'];
                    $this->$fncName($requestValue);
                }
            }catch(Exception $e){
                $this->setJson(500, $e->getMessage());
            }
        }
        exit();
    }


}
