<?php

namespace App\Http\Controllers;

use App\Http\Requests\SampleRequest;
use App\Http\Resources\SampleResource;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use packages\Domain\Model\User\UserId;
use packages\Service\UserGetInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class AuthController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function login(SampleRequest $request, UserGetInterface $userGet)
    {
        //
        var_dump($request->validated());
        //exit;

        if (!Auth::attempt($request->validated())) {
            return response()->json([
                'Invalid credential'
            ], 400);
        }

        return response()->json([]);
    }

}
