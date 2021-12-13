# Yunke Extension Point Service SDK for PHP

## Overview

Yunke Extension Point Service (EPS) is a SVIP tenant customized service provided by Yunke.


## Run environment
- PHP 5.5+.
- cURL extension.

## Install

```sh
$ composer require xxx
```

## Usage

### __construct
| parameter | type | require | default | explain |
| --- | --- | --- | --- | --- |
| host | `string` | true | `` | server host |
| accessKeyId | `string` | true | `` | access key |
| accessKeySecret | `string` | true | `` | access secret |
| timeout | `int` | false | `30` | request timeout setting |

### instantiation
```php
$client =  new \ExtensionService\EpsClient($host, $accessKeyId, $accessKeySecret);
// set headers
$headers = [];
$client = $client->setHeaders($headers);
```

### send request
| parameter | type | require | default | explain |
| --- | --- | --- | --- | --- |
| method | `string` | true | `` | method expected to be executed |
| condition | `array` | true | `` | the method will be executed when conditions are met |
| businessData | `array` | true | `` | business data |
| execClass | `Executable` | false | `null` | class file to be executed when the method not found in EPS |
```
$rsp = $client->execute(string $method, array $condition, array $businessData, new $class());
```

### notice
- execute() expects parameter 4 to be `Executable`, that means the class must implement `Executable` interface

### examples
```php
<?php

class DemoClass implements Executable 
{
    public function execute(array $businessData) {
        // specific business logic
    }
}
```
more examples see testX.php files in `samples` dir ...

## Maintainers
[yaodx01@mingyuanyun.com]()

### Contributors
This project exists thanks to all the people who contribute. 
<br/>
<img src="https://wework.qpic.cn/wwhead/nMl9ssowtibVGyrmvBiaibzDmwbPnGNr4WpZQpE7J0pBKCibHKZCiaueHUC9cicpUnVqSMNCU1cfJvmck/0" height="45" width="45" />
<img src="http://wework.qpic.cn/bizmail/FpSdz7Qn4ALfxLEqia7mm929jNGksjGzIJFI0ZtLmkBSEp5K4msDQjA/0" height="45" width="45" />

## License
The code is distributed under the terms of the MIT license.


