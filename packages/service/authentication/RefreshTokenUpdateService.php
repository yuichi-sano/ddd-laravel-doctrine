<?php

namespace packages\service\authentication;

use packages\domain\model\authentication\authorization\RefreshToken;
use packages\domain\model\authentication\authorization\RefreshTokenFactory;
use packages\infrastructure\database\RefreshTokenRepository;


class RefreshTokenUpdateService implements RefreshTokenUpdateInterface
{
    protected RefreshTokenRepository $refreshTokenRepository;

    public function __construct(RefreshTokenRepository $refreshTokenRepository)
    {
        $this->refreshTokenRepository = $refreshTokenRepository;
    }
    public function execute (RefreshToken $refreshToken): void{
        $this->refreshTokenRepository->save($refreshToken);
    }

}
