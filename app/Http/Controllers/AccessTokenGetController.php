<?php

namespace App\Http\Controllers;

use App\Exceptions\WebAPIException;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\SampleRequest;
use App\Http\Resources\LoginResource;
use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use packages\domain\model\authentication\Account;
use packages\domain\model\authentication\authorization\AccessTokenFactory;
use packages\domain\model\authentication\authorization\RefreshToken;
use packages\domain\model\authentication\authorization\RefreshTokenFactory;
use packages\service\authentication\AccessTokenGetInterface;
use packages\service\authentication\RefreshTokenUpdateInterface;
use packages\service\authentication\RefreshTokenUpdateService;
use packages\service\UserGetInterface;
use Illuminate\Support\Facades\Auth;
class AccessTokenGetController extends BaseController
{
    private AccessTokenGetInterface $accessTokenGet;
    private RefreshTokenUpdateInterface $refreshTokenUpdate;
    public function __construct(AccessTokenGetInterface $accessTokenGet,
                                RefreshTokenUpdateInterface $refreshTokenUpdate)
    {
        $this->accessTokenGet = $accessTokenGet;
        $this->refreshTokenUpdate = $refreshTokenUpdate;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index( AccessTokenGetInterface $accessTokenGet): \Illuminate\Http\Response
    {

        $accessToken = $accessTokenGet->execute(new RefreshToken('fdfdfdfd'));
        $refreshToken = $this->refreshTokenUpdate->execute($accessToken);
        return LoginResource::buildResult($accessToken,$refreshToken);
    }

}
