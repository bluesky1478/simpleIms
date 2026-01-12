<?php
namespace Component\Ims\EnumType;

//처리 상태 (승인, TO-DO)
class ENUM_STATUS
{
    /**
     * @param $value
     * @param $class
     * @return mixed
     * @throws \ReflectionException
     */
    public static function getNameWithClass($value, $class){
        $reflector = new \ReflectionClass($class);
        $constants = $reflector->getConstants();
        return $constants[strtoupper($value)]['name'];
    }
}

