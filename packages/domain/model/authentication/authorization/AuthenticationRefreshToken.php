<?php

namespace packages\domain\model\authentication\authorization;


use packages\domain\model\User\UserId;

class AuthenticationRefreshToken
{
    protected RefreshToken $refreshToken;
    protected UserId $userId;
    protected RefreshTokenExpiresAt $refreshTokenExpiresAt;
    protected RefreshTokenSignsAt $refreshTokenSignsAt;


    public function __construct(RefreshToken $refreshToken,  UserId $userId,
                                RefreshTokenSignsAt $refreshTokenSignsAt,
                                RefreshTokenExpiresAt $refreshTokenExpiresAt
                                )
    {
        $this->refreshToken  = $refreshToken;
        $this->userId = $userId;
        $this->refreshTokenExpiresAt = $refreshTokenExpiresAt;
        $this->refreshTokenSignsAt = $refreshTokenSignsAt;
    }

    public  function update(RefreshTokenExpiresAt $refreshTokenExpiresAt): AuthenticationRefreshToken{
        return new AuthenticationRefreshToken(
            $this->refreshToken, $this->userId, $this->refreshTokenSignsAt, $refreshTokenExpiresAt
        );
    }

    public static function create(RefreshToken $refreshToken, UserId $userId, RefreshTokenExpiresAt $refreshTokenExpiresAt): AuthenticationRefreshToken{
        return new AuthenticationRefreshToken(
            $refreshToken, $userId, new RefreshTokenSignsAt(), $refreshTokenExpiresAt
        );
    }

    public function isExpired(): string {
        return $this->refreshTokenExpiresAt->isExpired();
    }

    public function toArray(): array
    {
        return [
            'refreshToken'=>$this->refreshToken->toString(),
            'userId'      =>$this->userId->toInteger(),
            'signsAt'     => $this->refreshTokenSignsAt->toLocalDateTime(),
            'expiresAt'   =>$this->refreshTokenExpiresAt->toLocalDateTime(),
        ];
    }
}
