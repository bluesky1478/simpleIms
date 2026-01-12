<?php
namespace Component\Ims\EnumType;

//TO-DO List Type
abstract class TODO_TYPE
{
    const TODO  = ['val'=>'todo','name'=>'TODO리스트',];
    const PAYMENT  = ['val'=>'payment','name'=>'결재',];
}

//대상 타입
abstract class TODO_TARGET_TYPE
{
    const TARGET  = ['val'=>'target','name'=>'대상자',];
    const REF  = ['val'=>'ref','name'=>'참조자',];
}

//개별번호의 타입 (연결성을 위해)
abstract class TODO_EACH_DIV
{
    const PRODUCTION  = ['val'=>'reject','name'=>'생산',];
    const ESTIMATE    = ['val'=>'reject','name'=>'견적',];
    const COST        = ['val'=>'reject','name'=>'생산가',];
    const FABRIC      = ['val'=>'reject','name'=>'퀄리티/BT',];
}