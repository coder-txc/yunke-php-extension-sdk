<?php


namespace ExtensionService\Exception;


use ExtensionService\EpsResponseCode;

class SignException extends \Exception
{
    public function __construct($message = "")
    {
        $message = $message === "" ? "签名验证失败" : $message;
        parent::__construct($message, EpsResponseCode::ExtensionPointNotFound);
    }
}
