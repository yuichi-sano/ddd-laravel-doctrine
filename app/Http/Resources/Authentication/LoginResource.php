<?php

namespace App\Http\Resources\Authentication;

use App\Http\Resources\Basic\AbstractJsonResource;
use App\Http\Resources\Definition\Authentication\LoginResultDefinition;


class LoginResource  extends AbstractJsonResource
{
    public static function buildResult(): LoginResource
    {
        $definition = new LoginResultDefinition();
        return new LoginResource($definition);
    }
}
