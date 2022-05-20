<?php

require(__DIR__ . '/../vendor/autoload.php');

$host = "https://gateway-dev.myscrm.cn/ares-extension";
$accessKeyId = "f4c9e647719b6b5783fafb0f5b94f4b1";
$accessKeySecret = "xbuj4yYKZvRYefOgbD4ScTM2whfGlnAR";
$epsClient = new \ExtensionService\EpsClient($host, $accessKeyId, $accessKeySecret);
$interfaceMethod = "ares_extension.service.testExecuteGet1111111111111111111111111";
$orgCode = "yktyadmin";
$miniAppID = "wxxxxx1231321";
$condition = [
    "orgcode" => $orgCode,
    "mini_app_id" => $miniAppID,
];
$businessData = [
    "company_id" => 1,
    "node_id" => 15,
    "namespace_code" => "default",
    "service_name" => "jarvis-service"
];

class ExecuteClass implements \ExtensionService\Executable
{
    function execute($businessData)
    {
        $businessData["haha"] = "it is the result return from transformation class of original method";
        var_dump($businessData);
        return $businessData;
    }
}

try {
    $res = $epsClient->execute( // interfaceMethod is not exist, it will run execute func in ExecuteClass
        $interfaceMethod,
        $condition,
        $businessData,
        new ExecuteClass()
    );
    var_dump($res);
} catch (\Exception $e) {
    var_dump($e);
}

