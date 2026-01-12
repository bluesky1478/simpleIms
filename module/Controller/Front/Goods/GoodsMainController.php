<?php

namespace Controller\Front\Goods;

use Framework\Debug\Exception\AlertBackException;
use Framework\Debug\Exception\AlertOnlyException;
use Framework\Debug\Exception\AlertRedirectException;
use Framework\Debug\Exception\Framework\Debug\Exception;
use Message;
use Globals;
use Request;
use Cookie;
use Framework\Utility\StringUtils;
use Framework\Utility\SkinUtils;

class GoodsMainController extends \Bundle\Controller\Front\Goods\GoodsMainController{

    public function index(){
        try {
            //gd_debug('Goods Main Test');
            parent::index();
        } catch (\Exception $e) {
            throw new AlertRedirectException($e->getMessage(),null,null,"/");
        }
    }

}