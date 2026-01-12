<?php
namespace Component\Ims\EnumType;

//처리 상태 (승인, TO-DO)
abstract class APPROVAL_STATUS
{
    const READY     = ['val'=>'ready','name'=>'기안',];
    const REQ       = ['val'=>'request','name'=>'요청',];
    const PROC      = ['val'=>'proc','name'=>'진행',];
    const COMPLETE  = ['val'=>'complete','name'=>'PASS',]; //패스로 사용
    const ACCEPT    = ['val'=>'accept','name'=>'결재완료',];
    const REJECT    = ['val'=>'reject','name'=>'반려',];

    /**
     * @param $value
     * @return mixed
     * @throws \ReflectionException
     */
    public static function getName($value){
        return ENUM_STATUS::getNameWithClass($value, 'Component\Ims\EnumType\APPROVAL_STATUS');
    }
}

