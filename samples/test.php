<?php

require(__DIR__ . '/../vendor/autoload.php');

$host = "https://gateway-dev.myscrm.cn/ares-extension";
$accessKeyId = "f4c9e647719b6b5783fafb0f5b94f4b1";
$accessKeySecret = "xbuj4yYKZvRYefOgbD4ScTM2whfGlnAR";
$epsClient = new \ExtensionService\EpsClient($host, $accessKeyId, $accessKeySecret);
$interfaceMethod = "ares_extension.service.testExecuteGet";
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

try {
    $res = $epsClient->execute( // it will run interfaceMethod
        $interfaceMethod,
        $condition,
        $businessData
    );
    var_dump($res);
} catch (\Exception $e) {
    var_dump($e);
}

