<?php

namespace App\Http\Controllers\Authentication;

use App\Http\Controllers\Controller;
use App\Http\Requests\Authentication\Auth\AuthChangePasswordRequest;
use App\Http\Requests\Authentication\Auth\AuthForgotPasswordRequest;
use App\Http\Requests\Authentication\Auth\AuthLoginRequest;
use App\Http\Requests\Authentication\Auth\AuthResetPasswordRequest;
use App\Models\Authentication\PasswordReset;
use App\Models\Authentication\PassworReset;
use App\Models\Authentication\Permission;
use App\Models\Authentication\System;
use App\Models\Ignug\Catalogue;
use App\Models\Authentication\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Mail\Message;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class  AuthController extends Controller
{
    public function logout(Request $request)
    {
        $request->user()->token()->revoke();
        return response()->json(['data' => 'successfully logged out'], 201);
    }

    public function logoutAll(Request $request)
    {
        DB::table('oauth_access_tokens')
            ->where('user_id', $request->user_id)
            ->update([
                'revoked' => true
            ]);
        return response()->json(['data' => 'successfully all logged out'], 201);
    }

    public function changePassword(AuthChangePasswordRequest $request)
    {
        $data = $request->json()->all();
        $dataUser = $data['user'];
        $user = User::findOrFail($dataUser['id']);
        if (!$user) {
            return response()->json(['error' => 'not found', 'detail' => 'user not found', 'code' => 404], 404);
        }

        if (!Hash::check(trim($dataUser['password']), $user->password)) {
            return response()->json(['error' => 'password no valid', 'detail' => 'current password is not valid', 'code' => 400], 400);
        }
        $user->update(['password' => Hash::make(trim($dataUser['new_password'])), 'change_password' => true]);
        return response()->json(['data' => $user], 201);
    }

    public function forgotPassword(AuthForgotPasswordRequest $request)
    {
        $user = User::where('username', $request->username)
            ->orWhere('email', $request->username)
            ->orWhere('personal_email', $request->username)
            ->first();
        if (!$user) {
            return response()->json(['error' => 'not found', 'detail' => 'user not found', 'code' => 404], 404);
        }
        $token = Str::random(70);
        PasswordReset::create([
            'username' => $user->username,
            'token' => $token
        ]);

        Mail::send('Mails.forgot', ['token' => $token, 'user' => $user], function (Message $message) use ($user) {
            $message->to($user->email);
            $message->subject('Notificación de restablecimiento de contraseña');
        });
        $domainEmail = strlen($user->email) - strpos($user->email, "@");
        return response()->json(['data' => $this->hiddenString($user->email, 3, $domainEmail)], 201);
    }

    public function resetPassword(AuthResetPasswordRequest $request)
    {
        $passworReset = PasswordReset::where('token', $request->token)->first();
        if (!$passworReset) {
            return response()->json(['error' => 'token not found', 'detail' => 'invalid token', 'code' => 400], 400);
        }
        if (!$passworReset->is_valid) {
            return response()->json(['error' => 'invalid token', 'detail' => 'used token', 'code' => 403], 403);
        }
        if ((new Carbon($passworReset->created_at))->addMinutes(10) <= Carbon::now()) {
            $passworReset->update(['is_valid' => false]);
            return response()->json(['error' => 'invalid token', 'detail' => 'expired token', 'code' => 403], 403);
        }

        if (!$user = User::where('username', $passworReset->username)->first()) {
            return response()->json(['error' => 'not found', 'detail' => 'user not found', 'code' => 404], 404);
        }

        $user->password = Hash::make($request->password);
        $user->save();
        $passworReset->update(['is_valid' => false]);
        return response()->json(['data' => 'password is reset'], 201);
    }

    private function hiddenString($str, $start = 1, $end = 1)
    {
        $len = strlen($str);
        return substr($str, 0, $start) . str_repeat('*', $len - ($start + $end)) . substr($str, $len - $end, $end);
    }
}
