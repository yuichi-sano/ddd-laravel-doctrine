<?php

namespace App\Http\Requests\Definition;


class LoginDefinition extends Basic\AbstractRequestDefinition implements Basic\DefinitionInterface
{
    /**
     * HttpRequestParameter
     * @var string
     */
    protected string $password = 'required|string';
    protected string $access_id = 'required|string';
}
