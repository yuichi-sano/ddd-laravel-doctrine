<?php

namespace packages\service\authentication;

use packages\domain\model\authentication\authorization\AccessToken;
use packages\domain\model\authentication\authorization\AccessTokenFactory;
use packages\domain\model\authentication\authorization\RefreshToken;



class AccessTokenGetService implements AccessTokenGetInterface
{
    protected AccessTokenFactory $accessTokenFactory;

    public function __construct(AccessTokenFactory $accessTokenFactory)
    {
        $this->accessTokenFactory = $accessTokenFactory;
    }
    public function execute (RefreshToken $refreshToken): AccessToken{
        return $this->accessTokenFactory->createForRefreshToken($refreshToken);
    }

}
