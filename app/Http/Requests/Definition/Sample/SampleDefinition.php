<?php

namespace App\Http\Requests\Definition\Sample;
use App\Http\Requests\Definition\Basic\DefinitionInterface;
use App\Http\Requests\Definition\Basic\AbstractRequestDefinition;

class SampleDefinition extends AbstractRequestDefinition implements DefinitionInterface
{
    /**
     * HttpRequestParameter
     * @var string
     */
    
    //ログインID
    protected string $accessId = 'required|string';
       

    //ログインPASS
    protected string $password = 'required|string';
       

    //HOGE
    protected string $hoge = 'string';
       

}
