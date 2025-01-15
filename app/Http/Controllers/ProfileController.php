<?php

namespace App\Http\Controllers;

use App\Models\Entries;
use App\Models\SuccessIndicator;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use PragmaRX\Google2FAQRCode\Google2FA;

class ProfileController extends Controller
{
    public function index()
    {
        $user=Auth::user();

        $currentYear = Carbon::now()->format('Y');
        $currentUser = Auth::user();
        $entriesCount = SuccessIndicator::whereNull('deleted_at')
        ->whereHas('org', function ($query) {
            $query->where('status', 'Active');
        })
        ->with('org')
        ->whereYear('created_at', $currentYear);

        $indicators = $entriesCount->get();

        $userDivisionIds = json_decode($currentUser->division_id, true);
        $filteredIndicators = $indicators->filter(function($indicator) use ($userDivisionIds) {
            $indicatorDivisionIds = json_decode($indicator->division_id, true);

            return !empty(array_intersect($userDivisionIds, $indicatorDivisionIds));
        });

        $currentMonth = Carbon::now()->format('m');
        $current_Year = Carbon::now()->format('Y');

        $currentDate = Carbon::now();

            if ($currentDate->day > 5) {
                $targetMonth = $currentDate->month;
                // $targetMonth = $currentDate->addMonth()->month;
            } else {
                $targetMonth = $currentDate->subMonth()->month;
            }

            $filteredIndicators = $filteredIndicators->filter(function($indicator) use ($targetMonth, $current_Year) {
                $completedEntries = Entries::where('indicator_id', $indicator->id)
                                        ->where('months', $targetMonth)
                                        ->whereYear('created_at', $current_Year)
                                        ->where('status', 'Completed')
                                        ->where('user_id',  Auth::user()->id)
                                        ->exists();
                return !$completedEntries;
            });

            // $entriesCount = Entries::whereNull('deleted_at')->with('indicator')->where('status', 'Pending')->count();
        $entriesCount = $filteredIndicators->count();
        return view('profile.profile', compact('user', 'entriesCount'));
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . Auth::user()->id,
            'mobile_number' => 'required|string|max:15',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()]);
        }

        $user = Auth::user();

        if ($request->hasFile('profile_image')) {
            // Delete old profile picture if exists
            if ($user->profile_image) {
                Storage::disk('public')->delete($user->profile_image);
            }
            // Store new profile picture
            $path = $request->file('profile_image')->store('profile_images', 'public');
            $user->profile_image = $path;
        }

        $user->update($request->except('profile_image'));

        // $user->update($request->all());

        return response()->json(['success' => true, 'message' => 'Profile updated successfully!']);
    }

    public function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()]);
        }

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json(['success' => false, 'errors' => ['current_password' => ['Current password is incorrect']]]);
        }
        $user->is_change_password = 0;
        $user->expiration_date = Carbon::now()->addDays(90);
        // $user->expiration_date = Carbon::now();
        $user->update(['password' => Hash::make($request->new_password)]);

        return response()->json(['success' => true, 'message' => 'Password changed successfully!']);
    }

    public function two_factor()
    {
        $user=Auth::user();
        $google2fa = new Google2FA();
        $secret = $google2fa->generateSecretKey();

        $qr_code = $google2fa->getQRCodeInline(
            "OPCR",
            $user->email,
            $secret
        );

        session([ "2fa_secret" => $secret]);

        return view('profile.two_factor', compact('user', 'qr_code', 'secret'));
    }

    public function twofaEnable(Request $request)
    {
        $google2fa = new Google2FA();

        // retrieve secret from the session
        $secret = session("2fa_secret");
        $user = Auth::user();

        if ($google2fa->verify($request->input('otp'), $secret)) {
            // store the secret in the user profile
            // this will enable 2FA for this user

            $user->is_two_factor_enabled = 1;
            $user->is_two_factor_verified = 1;
            $user->twofa_secret = $secret;
            $user->save();

            // avoid double OTP check
            session(["2fa_checked" => true]);

            return response()->json(['success' => true, 'message' => 'Two Factor Authentication Enabled Successfully.']);
        }

        return response()->json(['success' => false, 'message' => 'Incorrect value. Please try again']);

        // throw ValidationException::withMessages([
        //     'otp' => 'Incorrect value. Please try again...']);
    }
    public function twofaDisabled(Request $request)
    {
            $user = Auth::user();
            $user->is_two_factor_enabled = 0;
            $user->is_two_factor_verified = 0;
            $user->twofa_secret = null;
            $user->save();

            return response()->json(['success' => true, 'message' => 'Two Factor Authentication Disabled Successfully.']);

    }
}

