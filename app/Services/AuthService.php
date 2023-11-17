<?php
namespace App\Services;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;
use Validator;

/**
 * Class AuthService
 * @package App\Services
 */
class AuthService
{
    /**
     * @param $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function login($request) {
        $validator = Validator::make($request->all(), [
            'email'     => ['required', 'string', 'max:255', ' email'],
            'password'  => ['required', 'string', 'max:255'],
        ]);

        if ($validator->fails()) {
            return sendError('Unprocessable Content.', Response::HTTP_UNPROCESSABLE_ENTITY, $validator->errors()->all());
        }

        if($this->checkTooManyAttempts()) {
            throw new \Exception('IP address blocked too many login attempts.');
        }
        $credentials = $request->only(['email', 'password']);

        if(!Auth::attempt($credentials, false, false)) {
            RateLimiter::hit($this->throttleLogin(), $seconds = 3600);
            return sendError('Unauthorized.', Response::HTTP_UNAUTHORIZED);
        }

        $user               = $request->user();

        if(!is_null($user)) {
            $token = hash('sha256', random_bytes(40));
            $expires_at = Carbon::now()->addWeeks(1);

            $user_access_token = $user->update(['api_token' => $token, 'last_login_ip' => request()->ip(), 'expires_at' => $expires_at, 'updated_at' => Carbon::now()]);
            if ($user_access_token)
                return $this->respondWithToken($token, $expires_at);
        }
        return sendError('Unauthorized.', Response::HTTP_UNAUTHORIZED);
    }

    /**
     * @param $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout($request) {
        $user               = $request->user();

        if($user) {
            $logout         = $user->update(['api_token' => null, 'expires_at' => null]);
            if($logout) {
                return sendResponse('User is logged out.', 'User is logged out.');
            }
        }
        return sendError('Unauthorized.', Response::HTTP_UNAUTHORIZED);
    }

    /**
     * @param $token
     * @return \Illuminate\Http\JsonResponse
     */
    public function respondWithToken($token, $expires_at)
    {
        return sendResponse(
            ['access_token'  => $token,
             'token_type'    => 'Bearer',
             'expires_at'    => Carbon::parse($expires_at)->toDateTimeString()//Not sure if you want to add expire date or not
            ], 'Bearer token to be used to access API.');
    }

    /**
     * Get the rate limiting throttle key for the request.
     *
     * @return string
     */
    public function throttleLogin()
    {
        return strtolower(request('email')) . '|' . request()->ip();
    }

    /**
     * Ensure the login request is not rate limited.
     *
     * @return void
     */
    public function checkTooManyAttempts()
    {
        if (!RateLimiter::tooManyAttempts($this->throttleLogin(), 10)) {
            return false;
        }
        return true;
    }
}
