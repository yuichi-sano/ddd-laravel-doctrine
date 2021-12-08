<?php

namespace App\Http\Requests\Definition;

class SampleDefinition extends Basic\AbstractRequestDefinition implements Basic\DefinitionInterface
{
    /**
     * HttpRequestParameter
     * @var string
     */
    protected string $hoge = 'required|numeric';
    protected string $password = 'required|string';
    protected string $access_id = 'required|string';
}
