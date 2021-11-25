<?php


namespace ExtensionService;

/**
 * Interface Executable
 *
 * 如果该租户不存在对应的扩展点，执行原方法
 * 原方法改造类必须实现这个接口才能被执行
 */
interface Executable
{
    /**
     * @param array $businessData 业务参数
     * @return array
     */
    function execute($businessData);
}
