<?php

namespace App\Http\Controllers;

use App\Helpers\Transformer;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Token response structure.
     *
     * @param  string $token
     *
     * @return  array
     */
    private function respondWithToken(string $token)
    {
        return [
            'access_token' => $token,
            'type' => 'Bearer',
            'expired_in' => auth()->factory()->getTTL(),
        ];
    }

    /**
     * Login user.
     *
     * @param  Request  $request
     *
     * @return  JsonResponse
     */
    public function login(Request $request)
    {
        $payload = $this->validate($request, [
            'username' => 'required|string|max:20',
            'password' => 'required|string|max:255',
        ]);

        try {
            $token = Auth::setTTL(User::$token_ttl)->attempt($payload);

            if (!$token) {
                return Transformer::fail('Invalid login credentials.', null, 401);
            }

            return Transformer::ok(
                'Success to authenticated user.',
                array_merge(
                    $this->respondWithToken($token),
                    ['user' => new UserResource(auth()->user())]
                ),
                200
            );
        } catch (\Throwable $th) {
            return Transformer::fail('Failed to authenticated user.');
        }
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        try {
            return Transformer::ok(
                'Success to get user details.',
                [
                    'user' => new UserResource(auth()->user()),
                ]
            );
        } catch (\Throwable $th) {
            return Transformer::fail('Failed to get user details.', [
                'errors' => $th
            ]);
        }
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        try {
            return Transformer::ok(
                'Success to refresh token.',
                $this->respondWithToken(auth()->setTTL($this->token_ttl)->refresh())
            );
        } catch (\Throwable $th) {
            return Transformer::fail('Failed to refresh token.', [
                'errors' => $th
            ]);
        }
    }

    /**
     * Logout user.
     *
     * @return JsonResponse
     */
    public function logout()
    {
        try {
            Auth::logout();

            return Transformer::ok('Success to logged out user.');
        } catch (\Throwable $th) {
            return Transformer::ok('Failed to logged out user.');
        }
    }
}
