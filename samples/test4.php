<?php
/**
 * @author yuki
 * @date 2022/2/11 上午10:22
 */

/*$cacheDir = __DIR__ . '/../runtime/';
$cacheFileName = 'default';
$fileCacheClient = new \ExtensionService\Cache\FileCache($cacheDir, $cacheFileName);
$data = [
    [
       'interface_method' => 'demoInterfaceMethod',
       'extension_point_type' => 1,
       'condition' => '',
    ],
];
try {

    $fileCacheClient->store('demo', $data);
} catch (Exception $e) {
    var_dump($e->getMessage());
}

$data = $fileCacheClient->retrieve('demo', true);
var_dump($data);*/

$file = @file_get_contents(__DIR__.'/../runtime/c21f969b5f03d33d43e04f8f136e7682.cache.php');
var_dump($file);
