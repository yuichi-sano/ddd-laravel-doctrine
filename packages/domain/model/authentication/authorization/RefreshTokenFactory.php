<?php

namespace packages\domain\model\authentication\authorization;

use Illuminate\Support\Carbon;
use packages\domain\basic\type\StringType;
use packages\domain\model\authentication\Account;
use packages\domain\model\User\UserId;
use packages\infrastructure\database\RefreshTokenRepository;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Facades\JWTFactory;

class RefreshTokenFactory implements StringType
{
    protected Carbon $carbon;
    protected $now;
    protected RefreshTokenRepository $refreshTokenRepository;

    public function __construct(Carbon $carbon = null, RefreshTokenRepository $refreshTokenRepository)
    {
        $this->carbon = $carbon ?? new Carbon();
        $this->now = $this->carbon->now();
        $this->refreshTokenRepository = $refreshTokenRepository;
    }

    public function create(Account $account): RefreshToken{
        $customClaims = $this->getJWTCustomClaims($account);
        $payload = JWTFactory::make($customClaims);
        $token =  new RefreshToken(JWTAuth::encode($payload)->get());
        $refreshTokenExpiresAt = new RefreshTokenExpiresAt($this->now->addMinute(config('jwt.refresh_ttl')));

        $authenticationRefreshToken = AuthenticationRefreshToken::create(
            $token, new UserId($account->getId()),  $refreshTokenExpiresAt
        );
        $this->refreshTokenRepository->save($authenticationRefreshToken);
        return $token;

    }

    public function update(RefreshToken $refreshToken): RefreshToken{
        //$customClaims = $this->getJWTCustomClaims($account);
        //$payload = JWTFactory::make($customClaims);
        //return new RefreshToken(JWTAuth::encode($payload)->get());
    }

    /**
     * アクセストークン用CustomClaimsを返却
     *
     * @return object
     */
    public function getJWTCustomClaims(Account $account) :object
    {
        $data = [
            'sub' => $account->getId(),
            'iat' => $this->now->timestamp,
            'exp' => $this->now->addMinute(config('jwt.refresh_ttl'))->timestamp
        ];

        return JWTFactory::customClaims($data);
    }
}
