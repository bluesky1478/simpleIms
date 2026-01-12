<?php

namespace SlComponent\Util;
use App;

class SlLoader{

    public static function cLoad($package, $class, $type = ''){
        //gd_debug(__CLASS__ . __METHOD__ ,' : '.'\\'.ucfirst($type).'Component\\'.ucfirst($package).'\\'.ucfirst($class));
        return App::load('\\'.ucfirst($type).'Component\\'.ucfirst($package).'\\'.ucfirst($class));
    }

    /**
     * SQL 클래스 호출
     * @param $classNamePath
     * @param bool $withoutServiceStr
     * @return string
     */
    public static function sqlLoad($classNamePath, $withoutServiceStr = true){
        if( true === $withoutServiceStr ){
            $classNamePath = substr ( $classNamePath , 0 , strlen($classNamePath)-7) . 'Sql' ;
        }else{
            $classNamePath .= 'Sql' ;
        }
        return App::load('\\'.$classNamePath);
    }

    /**
     * 컨트롤러의 서비스 클래스 호출
     * @param $nameSpace
     * @param $serviceName
     * @return string
     */
    public static function controllerServiceLoad($nameSpace,$serviceName){
        return App::load('\\'.$nameSpace.'\\ControllerService\\'.ucfirst($serviceName).'Service');
    }

}