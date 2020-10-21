<?php

namespace App\Http\Controllers;

use App\Helpers\Transformer;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Json not found response.
     *
     * @return  JsonResponse
     */
    private function notFoundResponse()
    {
        return Transformer::fail('User not found.', null, 404);
    }

    /**
     * Create new account.
     *
     * @param   Request  $request
     *
     * @return  JsonResponse
     */
    public function register(Request $request)
    {
        $payload = $this->validate($request, [
            'username' => 'required|string|regex:/^[a-z]+([_a-z0-9]+)?$/|min:8|max:20|unique:users,username',
            'password' => 'required|string|min:8|max:255|confirmed',
        ]);

        try {
            // Create user
            $user = User::create([
                'username' => $payload['username'],
                'password' => app('hash')->make($payload['password']),
            ]);

            // Authenthicate user
            $token = auth()->setTTL(User::$token_ttl)->login($user);

            return Transformer::ok('Success to create new account.', [
                'user' => new UserResource($user),
                'access_token' => $token,
                'token_type' => 'Bearer',
                'expired_in' => auth()->factory()->getTTL(),
            ], 201);
        } catch (\Throwable $th) {
            return Transformer::fail('Failed to create account.');
        }
    }

    /**
     * Change user password.
     *
     * @param   Request  $request
     * @param   int|string   $id
     *
     * @return  JsonResponse
     */
    public function changePassword(Request $request, $id)
    {
        $payload = $this->validate($request, [
            'old_password' => 'required|string|max:255',
            'password' => 'required|string|min:8|max:255|confirmed',
        ]);

        try {
            $user = User::findOrFail($id);

            // Check old password
            if (!app('hash')->check($payload['old_password'], $user->password)) {
                return Transformer::fail('The old password doesn\'t match.', null, 401);
            }

            // Update db
            $user->update([
                'password' => app('hash')->make($payload['password']),
            ]);

            return Transformer::ok('Success to update user password.', new UserResource($user));
        } catch (ModelNotFoundException $th) {
            return $this->notFoundResponse();
        } catch (\Throwable $th) {
            return Transformer::fail('Failed to update user password.');
        }
    }

    /**
     * Change user avatar.
     *
     * @param   Request  $request
     * @param   int|string   $id
     *
     * @return  JsonResponse
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'username' => 'required|string|regex:/^[a-z]+([_a-z0-9]+)?$/|min:8|max:20|unique:users,username,' . $id,
            'avatar' => 'sometimes|mimes:png,jpg,jpeg|max:4096',
        ]);

        try {
            $user = User::findOrFail($id);
            $payload =  $request->only('username');

            if ($request->hasFile('avatar')) {
                $avatar_path = base_path('public/avatars');

                // User has old avatar
                if (!is_null($user->avatar) && file_exists($avatar_path . '/' . $user->avatar)) {
                    unlink($avatar_path . '/' . $user->avatar);
                }

                // Store new avatar
                $avatar_file = $request->file('avatar');
                $file_name = time() . '.' . $avatar_file->getClientOriginalExtension();
                $avatar_file->move($avatar_path, $file_name);

                $payload['avatar'] = $file_name;
            }

            // Update the DB
            $user->update($payload);

            return Transformer::ok('Success to update user profile.', new UserResource($user));
        } catch (ModelNotFoundException $th) {
            return $this->notFoundResponse();
        } catch (\Throwable $th) {
            return Transformer::fail('Failed to update user profile.');
        }
    }
}
