<?php

namespace App\Http\Requests;
use App\Http\Requests\Definition\SampleDefinition;

class SampleRequest extends Basic\AbstractFormRequest
{
    public function __construct(SampleDefinition $definition = null)
    {
        parent::__construct($definition);
    }
    protected function transform(array $attrs): array
    {
        return $attrs;
    }

    /**
     * Getters
     * Requestにつめられたマジックメソッド経由のプロパティをビジネスモデルに流し込みget{Model名}にて取得
     */
    public function getHoge(): string
    {
        return $this->hoge;
    }
}
