<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Transformers\UserTransformer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends ApiController
{
    public function login(Request $request)
    {
        if (Auth::attempt(['email' => $request->get('email'), 'password' => $request->get('password')])) {
            /** @var User $user */
            $user = Auth::user();
            $accessToken = $user->createToken('api')->accessToken;

            return response()->json([
                'userId' => $user->id,
                'token' => $accessToken
            ], 200);
        } else {
            return response()->json(['error' => 'We couldn\'t find any account matching your credentials!'], 401);
        }
    }

    public function me()
    {
        $user = auth()
            ->guard('api')
            ->user();
        return $this->item($user, new UserTransformer);
    }
}
