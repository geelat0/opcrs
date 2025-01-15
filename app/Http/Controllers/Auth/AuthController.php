<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\LoginModel;
use Carbon\Carbon;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use PragmaRX\Google2FA\Google2FA;

class AuthController extends Controller
{

    protected $redirectTo = '/dash-home';

    public function index()
    {

        return view('auth.login');
    }
    public function OTP()
    {

        return view('auth.otp');
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false,'errors' => $validator->errors()], 200);
        }
        $credentials = $request->only('email', 'password');
        $remember = $request->has('remember');
        // dd($request->remember);

        if($request->remember == 1){
            $attempt = auth()->attempt($credentials, $remember);
        }else{
            $attempt = auth()->attempt($credentials);
        }

        $currentDate = Carbon::now();
        $formatDate = $currentDate->format('Y-m-d');

        if ($attempt) {
            if (auth()->user()->status == 'Active' ) {
                $loginS = new LoginModel();
                $loginS->status = 'Logged In';
                $loginS->user_id = Auth::id();
                $loginS->save();

                if (auth()->user()->is_change_password || auth()->user()->expiration_date ==  $formatDate) {
                    return response()->json(['success' => true, 'redirect' => route('change-password')]);
                }

                // if (auth()->user()->is_two_factor_enabled) {
                //     return response()->json(['success' => true, 'redirect' => route('auth.otp')]);
                // }

                return response()->json(['success' => true, 'redirect' => $this->redirectTo]);
            } else {
                auth()->logout();
                return response()->json(['success' => false, 'message' => 'Your account is not active. Please contact the Admin.']);
            }
        }

        return response()->json(['success' => false, 'message' => 'Invalid credentials']);
    }

    public function showLinkRequestForm()
    {
        return view('auth.email-forgot-password');
    }

    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        return $status === Password::RESET_LINK_SENT
                    ? back()->with(['status' => __($status)])
                    : back()->withErrors(['email' => __($status)]);
    }

    public function showResetForm($token)
    {
        return view('auth.forget-pass', ['token' => $token]);
    }
    
    public function ChangePassForm()
    {
        $user=Auth::user();
        return view('auth.change-password', compact('user'));
    }

    public function reset(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                    'remember_token' => Str::random(60),
                    'expiration_date' => Carbon::now()->addDays(90),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        return $status === Password::PASSWORD_RESET
                    ? redirect('/')->with('status', __($status))
                    : back()->withErrors(['email' => [__($status)]]);
    }

    public function logout(Request $request)
    {

        $user = Auth::user();
        $user->is_two_factor_verified = 1;
        $user->save();

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    public function check(Request $request)
    {
        $google2fa = new Google2FA();
        $secret = Auth::user()->twofa_secret;

        if ($google2fa->verify($request->input('otp'), $secret)) {
            session(["2fa_checked" => true]);
            $user = Auth::user();
            $user->is_two_factor_verified = 0;
            $user->save();

            return response()->json(['success' => true, 'redirect' => $this->redirectTo]);
        }

        return response()->json(['success' => false, 'message' => 'Incorrect value. Please try again']);

        // throw ValidationException::withMessages([
        //     'otp' => 'Incorrect value. Please try again...'
        // ]);
    }
}
