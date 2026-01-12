<?php
namespace Component\Ims\EnumType;

//처리 상태 (승인, TO-DO)
abstract class TODO_STATUS
{
    const READY     = ['val'=>'ready','name'=>'요청',]; //기본 -> 준비(그냥 저장) , 요청하기->진행 , 저장+요청하기->진행 ,
    const REQ       = ['val'=>'request','name'=>'요청',];//미사용.
    const PROC      = ['val'=>'proc','name'=>'진행',];
    const COMPLETE  = ['val'=>'complete','name'=>'완료',];
    const ACCEPT    = ['val'=>'accept','name'=>'승인',];
    const REJECT    = ['val'=>'reject','name'=>'반려',];

    public static function getName($value){
        return ENUM_STATUS::getNameWithClass($value, 'Component\Ims\EnumType\TODO_STATUS');
    }
}

