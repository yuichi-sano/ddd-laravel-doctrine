<?php

namespace App\Http\Resources;

use App\Http\Resources\Basic\AbstractJsonResource;
use App\Http\Resources\Definition\LoginResultDefinition;
use packages\domain\model\authentication\authorization\AccessToken;
use packages\domain\model\authentication\authorization\RefreshToken;

/**
 * Class AddressStateListResource
 * @package App\Http\Resources
 */
class LoginResource extends AbstractJsonResource
{
    public static function buildResult(AccessToken $token,RefreshToken $refreshToken): LoginResource
    {
        $definition = new LoginResultDefinition();
        $definition->setAccessToken($token->toString());
        //setcookie('Authorization','Bear '.$token->toString(),0,'/','',true,true);
        $definition->setRefreshToken($refreshToken->toString());
        return new LoginResource($definition);
    }
}
