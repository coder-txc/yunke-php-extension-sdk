<?php


namespace ExtensionService;


/**
 * Class EpsResponse
 *
 * Extension Point Service(EPS)'s standard response.
 */
class EpsResponse
{
    public $respJson;

    public $res;

    public function __construct($respJson, $res)
    {
        $this->respJson = $respJson;
        $this->res = $res;
    }
}
