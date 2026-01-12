<?php
namespace SlComponent\Util;

use SiteLabUtil\SlCommonUtil;
use Exception;
use Request;
use Framework\Debug\Exception\LayerException;
use Framework\Debug\Exception\LayerNotReloadException;
use Framework\Debug\Exception\AlertCloseException;
use Framework\Debug\Exception\AlertReloadException;
use Framework\Debug\Exception\AlertOnlyException;

/**
 * 컨트롤러에서 공통으로 사용하는 Trait
 */
Trait SlControllerTrait {
    public function setJson($code, $msg, $data = []){
        $this->json(
            [
                'code' => $code,
                'message' => $msg,
                'data' => $data,
            ]
        );
    }

    public function index(){
        $requestValue  = Request::request()->toArray();
        //gd_debug($requestValue);
        if (\Request::isAjax()) {
            $this->ajax($requestValue);
        } else {
            $ref = Request::getReferer();
            if( strpos($ref, 'gdadmin') !== false ){
                $this->iframe($requestValue);
            }else{
                $this->frontIframe($requestValue);
            }
        }
        exit();
    }

    public function frontIframe($requestValue){
        if( empty($requestValue['mode']) ){
            throw new AlertOnlyException('페이지가 없습니다. (mode empty)');
        }else{
            try {
                $apiService = $this->getMyService();
                $methodMap = SlCommonUtil::getReverseMap(get_class_methods(get_class($apiService)));
                if( !isset($methodMap[$requestValue['mode']]) ){
                    throw new AlertOnlyException('페이지가 없습니다. (mode undefined [' . $requestValue['mode'] . '])');
                }else{
                    $fncName = $requestValue['mode'];
                    try{
                        $apiResultData = $apiService->$fncName($requestValue);
                        //throw new AlertOnlyException(__(gd_isset($apiResultData['msg'],'처리 되었습니다.')));
                        echo "<script>alert('". gd_isset($apiResultData['msg'],'처리 되었습니다.') ."'); window.parent.postMessage('갱신', '*');</script>";
                    }catch(Exception $e){
                        throw new AlertOnlyException($e->getMessage());
                    }
                }
            }catch(Exception $e){
                throw new AlertOnlyException($e->getMessage());
            }
        }
    }

    public function iframe($requestValue){
        if( empty($requestValue['mode']) ){
            throw new LayerNotReloadException('페이지가 없습니다. (mode empty)');
        }else{
            try {
                $apiService = $this->getMyService();
                $methodMap = SlCommonUtil::getReverseMap(get_class_methods(get_class($apiService)));
                //SitelabLogger::logger('메소드맵');
                //SitelabLogger::logger($methodMap);
                if( !isset($methodMap[$requestValue['mode']]) ){
                    throw new LayerNotReloadException('페이지가 없습니다. (mode undefined [' . $requestValue['mode'] . '])');
                }else{
                    $fncName = $requestValue['mode'];
                    try{
                        $apiResultData = $apiService->$fncName($requestValue);
                        //$this->layer(__(gd_isset($apiResultData['msg'],'처리 되었습니다.')), null, null, null, 'self.close()');
                        //echo "<script>alert('". gd_isset($apiResultData['msg'],'처리 되었습니다.') ."'); window.parent.close()</script>"; ;
                        echo "<script>alert('". gd_isset($apiResultData['msg'],'처리 되었습니다.') ."'); window.parent.myFnc()</script>"; ;
                        //$this->layer(__('삭제 완료'), null, null, null, 'top.location.href="/";');

                    }catch(Exception $e){
                        throw new LayerNotReloadException($e->getMessage());
                    }
                }
            }catch(Exception $e){
                throw new LayerNotReloadException($e->getMessage());
            }
        }
    }

    public function ajax($requestValue){
        if( empty($requestValue['mode']) ){
            $this->setJson(404, __('페이지가 없습니다. (mode empty)'));
        }else{
            try {
                $apiService = $this->getMyService();
                $methodMap = SlCommonUtil::getReverseMap(get_class_methods(get_class($apiService)));
                if( !isset($methodMap[$requestValue['mode']]) ){
                    $this->setJson(404, __('페이지가 없습니다. (mode undefined [' . $requestValue['mode'] . '])'  ));
                }else{
                    $fncName = $requestValue['mode'];
                    try{
                        $apiResultData = $apiService->$fncName($requestValue);
                        $this->setJson( 200, $apiResultData['msg'], $apiResultData['data'] );
                    }catch(Exception $e){
                        $this->setJson(500, $e->getMessage());
                    }
                }
            }catch(Exception $e){
                $this->setJson(500, $e->getMessage());
            }
        }
    }


    public function runMethod($class) {

        $requestValue = Request::request()->toArray();

        if( empty($requestValue['mode']) ){
            $this->setJson(404, __('페이지가 없습니다. (mode empty)'));
        }else{
            try {
                $methodMap = SlCommonUtil::getReverseMap($class);
                if( !isset($methodMap[$requestValue['mode']]) ){
                    $this->setJson(404, __('페이지가 없습니다. (mode undefined [' . $requestValue['mode'] . '])'  ));
                }else{
                    $fncName = $requestValue['mode'];
                    $this->$fncName($requestValue);
                }
            }catch(Exception $e){
                $this->setJson(500, $e->getMessage());
            }catch(\Exception $e){
                $this->setJson(500, $e->getMessage());
            }
        }
        exit();
    }

}
