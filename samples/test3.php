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
$data = [
    "company_id" => 1,
    "node_id" => 15,
    "namespace_code" => "default",
    "service_name" => "jarvis-service"
];

class OtherClass
{
    function execute($data)
    {
        $data["wuwu"] = "this class does not impl Executable";
        return $data;
    }
}

try {
    $res = $epsClient->execute( // throw ExtensionService\Exception\NonExecutableException
        $interfaceMethod,
        $condition,
        $data,
        new OtherClass()
    );
    var_dump($res);
} catch (\Exception $e) {
    var_dump($e);
}

