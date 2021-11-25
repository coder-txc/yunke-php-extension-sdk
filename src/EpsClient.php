<?php

namespace ExtensionService;

use ExtensionService\Exception\JsonEncodeException;
use ExtensionService\Exception\NonExecutableException;
use ExtensionService\Exception\ParamException;
use ExtensionService\Exception\ServerException;
use ExtensionService\Exception\SignException;

/**
 * Class EpsClient
 *
 * Extension Point Service(EPS)'s client class, which wraps all EPS APIs user could call to talk to EPS.
 */
class EpsClient
{
    /** @var string $host 扩展服务请求地址 */
    private $host;

    /** @var string $accessKeyId 访问密钥 ID */
    private $accessKeyId;

    /** @var string $accessKeySecret 访问密钥 */
    private $accessKeySecret;

    /** @var int $timeout 请求超时时间设置，单位秒，0秒即永不超时 */
    private $timeout;

    /**
     * Client constructor.
     *
     * @param string $host 扩展服务请求地址
     * @param string $accessKeyId 访问密钥 ID
     * @param string $accessKeySecret 访问密钥
     * @param int $timeout 请求超时时间设置，单位秒，默认为30秒
     * @throws ParamException
     */
    public function __construct($host, $accessKeyId, $accessKeySecret, $timeout = 30)
    {
        $host = trim($host);
        $accessKeyId = trim($accessKeyId);
        $accessKeySecret = trim($accessKeySecret);

        if (empty($host)) {
            throw new ParamException("extension server host is empty");
        }
        if (empty($accessKeyId)) {
            throw new ParamException("access key id is empty");
        }
        if (empty($accessKeySecret)) {
            throw new ParamException("access key secret is empty");
        }

        $this->host = $host;
        $this->accessKeyId = $accessKeyId;
        $this->accessKeySecret = $accessKeySecret;
        $this->timeout = $timeout;
    }

    /**
     * @param string $interfaceMethod 接口方法，即执行扩展点的方法
     * @param array $condition 匹配条件。当条件匹配时，执行方法下对应的扩展点
     * @param array $businessData 请求参数。将解析为请求扩展点的参数。
     *                    - GET、DELETE 请求将解析为查询参数，即解析为 ?username=zhangsan&password=123456
     *                    - POST、PUT 请求将解析为请求体，即 JSON 格式的 Body
     * @param Executable $execClass 原方法改造类，如果该租户不存在对应的扩展点，执行原方法改造类的execute
     * @return EpsResponse
     * @throws ParamException
     * @throws JsonEncodeException
     * @throws SignException
     * @throws NonExecutableException
     * @throws ServerException
     */
    public function execute($interfaceMethod, $condition, $businessData, $execClass = null)
    {
        if (empty($interfaceMethod)) {
            throw new ParamException("interfaceMethod is empty");
        }
        if (!is_string($interfaceMethod)) {
            throw new ParamException("interfaceMethod must be string");
        }
        if (empty($condition)) {
            throw new ParamException("condition is empty");
        }
        if (!is_array($condition)) {
            throw new ParamException("condition must be array");
        }
        if (!empty($businessData) && !is_array($businessData)) {
            throw new ParamException("data must be array");
        }

        $bodyStr = $this->buildBodyStr($interfaceMethod, $condition, $businessData);

        $headers = $this->buildHeaders($bodyStr);

        $responseBody = $this->handleExecute($bodyStr, $headers);

        $responseBodyArr = json_decode($responseBody, true);

        if (isset($responseBodyArr["code"])) {
            $code = $responseBodyArr["code"];
            $error = isset($responseBodyArr["error"]) ? $responseBodyArr["error"] : "";

            if ($code === EpsResponseCode::SignInvalidate) {
                throw new SignException($error);
            } else if ($code === EpsResponseCode::ExtensionPointNotFound) { // 扩展点找不到
                return $this->execClassExecute($execClass, $businessData);
            } else {
                throw new ServerException($error, $code);
            }

            // 其它code的处理
        }

        $respJson = isset($responseBodyArr["respJson"]) ? $responseBodyArr["respJson"] : "";
        $res = json_decode($respJson, true);

        return new EpsResponse($respJson, $res);
    }

    /**
     * @param Executable $execClass 原方法改造类，如果该租户不存在对应的扩展点，执行原方法改造类的execute
     * @param array $businessData 请求参数
     * @return EpsResponse
     * @throws NonExecutableException
     */
    private function execClassExecute($execClass, $businessData)
    {
        if ($execClass instanceof Executable) {
            $res = $execClass->execute($businessData);
            $respJson = json_encode($res);
            $respJson = $respJson === false ? "" : $respJson;
            return new EpsResponse($respJson, $res);
        } else {
            throw new NonExecutableException();
        }
    }

    /**
     * @param string $bodyStr
     * @param array $headers
     * @return string
     * @throws ServerException
     */
    private function handleExecute($bodyStr, $headers)
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->host . '/v1/sdk/ep/execute',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => $this->timeout,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $bodyStr,
            CURLOPT_HTTPHEADER => $headers,
        ));
        $responseBody = curl_exec($curl);

        curl_close($curl);

        if ($responseBody === false) { // the CURLOPT_RETURNTRANSFER option is set, it will return the result on success, false on failure.
            throw new ServerException("curl fail", -1);
        }

        return $responseBody;
    }

    /**
     * @param string $interfaceMethod
     * @param array $condition
     * @param array $businessData
     * @return string
     * @throws JsonEncodeException
     */
    private function buildBodyStr($interfaceMethod, $condition, $businessData)
    {
        $dataStr = json_encode($businessData);
        if ($dataStr === false) {
            throw new JsonEncodeException("data json encode fail.");
        }

        $body = [
            "interface_method" => $interfaceMethod, // 接口方法
            "condition" => $condition,
            "data" => $dataStr,
        ];

        $bodyStr = json_encode($body);
        if ($bodyStr === false) {
            throw new JsonEncodeException("body json encode fail");
        }

        return $bodyStr;
    }

    /**
     * @param string $bodyStr
     * @param int $timestamp
     * @return array
     */
    private function buildHeaders($bodyStr, $timestamp = 0)
    {
        if ($timestamp === 0) {
            $timestamp = time();
        }
        $sign = $this->sign($bodyStr, $timestamp);
        return [
            'access-key-id: ' . $this->accessKeyId,
            'timestamp: ' . $timestamp,
            'sign: ' . $sign,
            'Content-Type: application/json',
        ];
    }

    /**
     * @param string $bodyStr
     * @param int $timestamp
     * @return string
     */
    private function sign($bodyStr, $timestamp)
    {
        if ($timestamp === 0) {
            $timestamp = time();
        }

        $str = $this->accessKeyId . $this->accessKeySecret . (string)$timestamp . $bodyStr;

        return md5($str);
    }
}
