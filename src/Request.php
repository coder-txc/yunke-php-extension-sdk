<?php
/**
 * @author yuki
 * @date 2022/2/10 上午11:38
 */

namespace ExtensionService;


use ExtensionService\Exception\ServerException;

class Request
{
    /**
     * 实例化Request
     * @return Request
     * @author yuki
     * @date 2022/2/10
     */
    public static function instance()
    {
        return new self();
    }

    /**
     * 发送curl请求
     * @param string $method
     * @param string $url
     * @param array $header
     * @param string $bodyStr
     * @param int $timeout
     * @return mixed
     * @throws ServerException
     * @author yuki
     * @date 2022/2/10
     */
    public function curlRequest($method, $url, $header = [], $bodyStr = '', $timeout = 3)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);

        if (stripos($url, "https://") !== FALSE) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        }

        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $bodyStr);
        }

        $output = curl_exec($ch);
        $errno = curl_errno($ch);
        if ($errno) {
            $error = curl_error($ch);
            curl_close($ch);
            throw new ServerException("curl fail, error message: {$error}", $errno);
        }

        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        if ($httpCode != 200) {
            throw new ServerException("curl fail, http code: {$httpCode}");
        }

        return json_decode($output, true);
    }
}