<?php

namespace App\Http\Controllers;

use App\Mail\ResetPasswordMail;
use App\Mail\TwoFactorCodeMail;
use App\Models\User_Account;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class ForgotPasswordController extends Controller
{
    private $request;
    public function __construct(Request $request)
    {
        $this->request = $request;
    }
    public function sendResetLink() {

        $token = Str::random(60);
        $email = $this->request->email;
        $link = env('APP_UI_URL') . "forgot_password/forgot_password_form?token=$token&email=$email";

        try {
            // Begin the transaction
            DB::beginTransaction();

            //check if user exists
            $user = User_Account::where('email', $this->request->email)->first();

            if(is_null($user))
            {
                return response()->json(['success' => false, 'message' => 'User does not exists'], Response::HTTP_INTERNAL_SERVER_ERROR);
            }

            $reset = DB::table('password_reset_tokens')->updateOrInsert(
                ['email' => $email], // Condition to check if email exists
                [
                    'token' => $token,
                    'created_at' => now(),
                    ]
                );

                // Send the email
                Mail::to($this->request->email)->send(new ResetPasswordMail($link));

                //return response()->json(['success' => false, 'message' => $mail], Response::HTTP_INTERNAL_SERVER_ERROR);
    // Commit the transaction if successful
    DB::commit();

    return response()->json(['success' => true, 'message' => 'Reset Password Sent'], Response::HTTP_OK);

    } catch (\Exception $e) {
        // Rollback the transaction on error
        DB::rollBack();

        return response()->json(['success' => false, 'message' => 'Failed to send email'], Response::HTTP_INTERNAL_SERVER_ERROR);
    }
    }

    public function resetPassword(Request $request) {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|confirmed|min:6',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->password = Hash::make($password);
                $user->save();
            }
        );

        return $status === Password::PASSWORD_RESET
            ? response()->json(['message' => 'Password reset successfully!'], 200)
            : response()->json(['message' => 'Failed to reset password.'], 400);
    }

    public function verify()
    {
        try {
        // Begin the transaction
        DB::beginTransaction();

        // Insert token in the password reset table
        $user = DB::table('password_reset_tokens')
        ->where('token', $this->request->token)
        ->where('email', $this->request->email)
        ->first();

        if (!$user) {
            return response()->json(['message' => 'Token has expired or is invalid.'], Response::HTTP_BAD_REQUEST);
        }

        // Commit the transaction if successful
        DB::commit();

        return response()->json(['success' => true, 'message' => 'The Token or Email existed!'], Response::HTTP_OK);

        } catch (\Exception $e) {
            // Rollback the transaction on error
            DB::rollBack();

            return response()->json(['success' => false, 'message' => 'Failed to search email or token'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
