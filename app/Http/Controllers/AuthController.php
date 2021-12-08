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
use packages\domain\model\authentication\authorization\RefreshTokenFactory;
use packages\service\UserGetInterface;
use Illuminate\Support\Facades\Auth;
class AuthController extends BaseController
{
    private $accessTokenFactory;
    private $refreshTokenFactory;
    public function __construct(AccessTokenFactory $accessTokenFactory,RefreshTokenFactory $refreshTokenFactory)
    {
        $this->accessTokenFactory = $accessTokenFactory;
        $this->refreshTokenFactory = $refreshTokenFactory;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function login(LoginRequest $request, UserGetInterface $userGet)
    {

        //Auth::guard('api')->getProvider()->setHasher(app('md5hash'));
        if (! Auth::attempt($request->validated())) {
            throw new WebAPIException('W_0000000',[],500);
        }
        $account = Auth::getLastAttempted();
        $token = $this->accessTokenFactory->create($account);
        $refreshToken = $this->refreshTokenFactory->create($account);
        return LoginResource::buildResult($token,$refreshToken);;
    }

}
