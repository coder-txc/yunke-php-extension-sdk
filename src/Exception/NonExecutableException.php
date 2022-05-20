<?php


namespace ExtensionService\Exception;


use ExtensionService\EpsResponseCode;

class NonExecutableException extends \Exception
{
    public function __construct()
    {
        parent::__construct("不存在对应的扩展点且无原方法改造类可执行", EpsResponseCode::ExtensionPointNotFound);
    }
}
