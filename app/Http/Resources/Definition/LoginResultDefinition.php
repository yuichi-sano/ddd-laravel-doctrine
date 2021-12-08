<?php

namespace App\Http\Resources\Definition;

use App\Http\Resources\Definition\Basic\ResultDefinitionInterface;
use App\Http\Resources\Definition\Basic\AbstractResultDefinition;

class LoginResultDefinition extends AbstractResultDefinition implements ResultDefinitionInterface
{

    //アクセストークン
    protected string $accessToken = '';

    protected string $refreshToken = '';


    /**
     * @return mixed
     */
    public function getAccessToken()
    {
        return $this->accessToken;
    }


    /**
     * @param mixed accessToken
     */
    public function setAccessToken(string $accessToken): void
    {
        $this->accessToken = $accessToken;
    }

    /**
     * @return string
     */
    public function getRefreshToken(): string
    {
        return $this->refreshToken;
    }

    /**
     * @param string $refreshToken
     */
    public function setRefreshToken(string $refreshToken): void
    {
        $this->refreshToken = $refreshToken;
    }




}
