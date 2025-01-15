<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use App\Mail\TempPasswordMail;
use App\Models\Division;
use App\Models\Entries;
use App\Models\LoginModel;
use App\Models\SuccessIndicator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;

use function GuzzleHttp\json_encode;

class UserController extends Controller
{

    protected $redirectTo = '/dash-home';

    // public function create()
    // {
    //     return view('auth.register');
    // }

    public function user_create()
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

        return view('user_page.user', compact('user', 'entriesCount'));
    }

    public function list(Request $request)
    {
        $query = User::with(['role', 'division'])->whereNull('deleted_at')->orderBy('created_at', 'desc');

        if ($request->has('date_range') && !empty($request->date_range)) {
            [$startDate, $endDate] = explode(' to ', $request->date_range);
            $startDate = Carbon::createFromFormat('m/d/Y', $startDate)->startOfDay();
            $endDate = Carbon::createFromFormat('m/d/Y', $endDate)->endOfDay();

            $query->whereBetween('created_at', [$startDate, $endDate]);
        }

        // if ($request->has('search') && !empty($request->search)) {
        //     $searchTerm = $request->search;

        //     if (strpos($searchTerm, ' ') !== false) {
        //         [$firstName, $lastName] = explode(' ', $searchTerm, 2);
        //         $query->whereHas('role', function($subQuery) use ($firstName, $lastName) {
        //             $subQuery->where('first_name', 'like', "%{$firstName}%")
        //                     ->where('last_name', 'like', "%{$lastName}%");
        //         });
        //     } else {
        //         $query->whereHas('role',function($subQuery) use ($searchTerm) {
        //             $subQuery->where('first_name', 'like', "%{$searchTerm}%")
        //                     ->orWhere('last_name', 'like', "%{$searchTerm}%")
        //                     ->orWhere('user_name', 'like', "%{$searchTerm}%")
        //                     ->orWhere('email', 'like', "%{$searchTerm}%")
        //                     ->orWhere('position', 'like', "%{$searchTerm}%")
        //                     ->orWhere('name', 'like', "%{$searchTerm}%")
        //                     ->orWhere('province', 'like', "%{$searchTerm}%");
        //         });
        //     }
        // }

        $users = $query->get();

        return DataTables::of($users)
            ->editColumn('id', function($user) {
                return Crypt::encrypt($user->id);
            })
            ->addColumn('name', function($user) {
                return ucfirst($user->first_name) . ' ' . ucfirst($user->last_name);
            })
            ->editColumn('created_at', function($user) {
                return $user->created_at->format('m/d/Y');
            })
            ->addColumn('role', function($user) {
                return $user->role ? $user->role->name : 'N/A';
            })
            ->editColumn('division_id', function($user) {
                $divisionIds = json_decode($user->division_id, true);
                if (is_array($divisionIds)) {
                    $divisions = Division::whereIn('id', $divisionIds)->pluck('division_name')->toArray();
                    return implode(', ', $divisions);
                }
                return '';
            })
            ->make(true);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'middle_name' => 'required|string|max:255',
            'province' => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'mobile_number' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'status' => 'required|string|max:255',
            'role_id' => 'required',
            'division_id' => 'required',
        ],[
            'role_id' => 'The role field is required',
            'division_id' => 'The division field is required',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false,'errors' => $validator->errors()], 200);
        }

        $user = new User();
        $user->first_name = ucfirst($request->first_name);
        $user->last_name = ucfirst($request->last_name);
        $user->middle_name = ucfirst($request->middle_name);
        $user->user_name = ucfirst(strtolower(substr($request->first_name, 0, 1) . '.' . substr($request->last_name, 0, 4)));
        $user->province = ucfirst($request->province);
        $user->position = ucfirst($request->position);
        $user->mobile_number = $request->mobile_number;
        $user->role_id = $request->role_id;
        $user->divsion_id = $request->divsion_id;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->status = ucfirst($request->status);
        $user->expiration_date = Carbon::now()->addDays(90);
        $user->created_by = ucfirst(strtolower(substr($request->first_name, 0, 1) . '.' . substr($request->last_name, 0, 4)));
        $user->save();

        return response()->json(['success' => true, 'message' => 'User registered successfully'], 200);
    }

    public function UserStore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'middle_name' => 'required|string|max:255',
            'province' => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'mobile_number' => [
                            'required',
                            'string',

                            'max:255',
                            'regex:/^09\d{9}$/', // Ensures the number starts with 09 and is followed by 9 digits
                            Rule::unique('users')->whereNull('deleted_at'),
                        ],
            'email' => [
                            'required',
                            'string',
                            'email',
                            'max:255',
                            Rule::unique('users')->whereNull('deleted_at'),
                        ],
            'role_id' => 'required',
            'division_id' => 'required',
            'division_id.*' => 'exists:divisions,id',
        ],[
            'role_id.required' => 'The role field is required',
            'division_id.required' => 'The division field is required',
            'division_id.*.exists' => 'The selected division is invalid',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false,'errors' => $validator->errors()], 200);
        }

       $randomString = Str::random(10);

        $user = new User();
        $user->first_name = ucfirst($request->first_name);
        $user->last_name = ucfirst($request->last_name);
        $user->middle_name = ucfirst($request->middle_name);
        $user->user_name = ucfirst(strtolower(substr($request->first_name, 0, 1) . '.' . substr($request->last_name, 0, 4)));
        $user->province = ucfirst($request->province);
        $user->position = ucfirst($request->position);
        $user->mobile_number = $request->mobile_number;
        $user->role_id = $request->role_id;
        $user->division_id = json_encode($request->division_id);
        $user->email = $request->email;
        $user->password = Hash::make($randomString);
        $user->status = 'Active';
        $user->created_by = Auth::user()->user_name;
        $user->save();

        return response()->json(['success' => true, 'message' => 'User added successfully'], 200);
    }

    public function update(Request $request)
    {
        $id = Crypt::decrypt($request->id);
        // dd($id);
        $validator = Validator::make($request->all(), [
            // 'id' => 'required|exists:users,id',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'middle_name' => 'required|string|max:255',
            'province' => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'mobile_number' => [
                            'required',
                            'string',
                            'regex:/^09\d{9}$/',
                            'max:255',
                            Rule::unique('users')->whereNull('deleted_at')->ignore($id),
                        ],
            'email' => [
                            'required',
                            'string',
                            'email',
                            'max:255',
                            Rule::unique('users')->whereNull('deleted_at')->ignore($id),
                        ],
            // 'password' => 'required|string|min:8',
            'role_id' => 'required|exists:role,id',
            'division_id' => 'required',
            'division_id.*' => 'exists:divisions,id',
        ],[
            'role_id.required' => 'The role field is required',
            'division_id.required' => 'The division field is required',
            'division_id.*.exists' => 'The selected division is invalid',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 200);
        }

        $user = User::findOrFail($id);

        $user->first_name = ucfirst($request->first_name);
        $user->last_name = ucfirst($request->last_name);
        $user->middle_name = ucfirst($request->middle_name);
        $user->user_name = ucfirst(strtolower(substr($request->first_name, 0, 1) . '.' . substr($request->last_name, 0, 4)));
        $user->province = ucfirst($request->province);
        $user->position = ucfirst($request->position);
        $user->mobile_number = $request->mobile_number;
        $user->role_id = $request->role_id;
        $user->division_id = json_encode($request->division_id);
        $user->email = $request->email;
        // $user->password = Hash::make($randomString); // Uncomment if you need to update the password
        $user->status = 'Active';
        $user->created_by = Auth::user()->user_name;
        $user->save();

        return response()->json(['success' => true, 'message' => 'User updated successfully']);
    }

    public function destroy(Request $request)
    {
        $validator = Validator::make($request->all(), [
            // 'id' => 'required|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 200);
        }

        $ifExist = Entries::whereNull('deleted_at')->where('user_id', Crypt::decrypt($request->id))->exists();

        if($ifExist){

            return response()->json(['success' => false, 'errors' => 'The user has existing entries, Cannot be deleted']);
        }

        $user = User::findOrFail(Crypt::decrypt($request->id));
        $user->created_by = Auth::user()->user_name;
        $user->delete();

        return response()->json(['success' => true, 'message' => 'User deleted successfully']);
    }

    public function temp_password(Request $request)
    {
        try {
            // Decrypt the ID and find the user
            $user = User::findOrFail(Crypt::decrypt($request->id));

            // Generate a random password
            $randomString = Str::random(10);

            // Update user password
            $user->is_change_password = 1;
            $user->password = Hash::make($randomString);
            $user->save();

            // Check if the email is not null and valid
            if (!empty($user->email)) {
                $emailData = [
                    'first_name' => $user->first_name,
                    'last_name' => $user->last_name,
                    'created_at' => now()->format('F j, Y'),
                    'password' => $randomString,
                    'email' => $user->email,
                ];

                // Send the email
                Mail::to($user->email)->send(new TempPasswordMail($emailData));
            } else {
                // Handle the case where email is null
                return response()->json(['success' => false, 'message' => 'Email address is not provided.']);
            }

            return response()->json(['data' => $randomString, 'success' => true, 'message' => 'Successfully created a temporary password.']);
        } catch (\Exception $e) {
            // Catch any exception and return an error message
            return response()->json(['success' => false, 'message' => 'An error occurred: ' . $e->getMessage()]);
        }
    }

    public function proxy(Request $request){

        $user = User::findOrFail(Crypt::decrypt($request->id));

        $randomString = Str::random(10);

        $user->proxy_password = Hash::make($randomString);

        $loginS = new LoginModel();
        $loginS->status = 'Proxy Logged In';
        $loginS->user_id =$user->id;
        $loginS->save();


        Auth::login($user);

        return response()->json(['success' => true, 'redirect' => $this->redirectTo]);

    }

    public function changeStatus(Request $request)
    {
        $user = User::findOrFail(Crypt::decrypt($request->id));
        if ($user) {
            $user->status = $request->status;
            $user->save();

            return response()->json(['success' => true, 'message' => 'User status updated successfully']);
        }

        return response()->json(['success' => false, 'message' => 'User not found']);
    }


    public function twoAuthDisabled(Request $request)
    {
        $user = User::findOrFail(Crypt::decrypt($request->id));
        $user->is_two_factor_enabled = 0;
        $user->is_two_factor_verified = 0;
        $user->twofa_secret = null;
        $user->save();

        return response()->json(['success' => true, 'message' => 'Two Factor Authentication Disabled Successfully.']);
    }

    // public function getDivision(Request $request)
    // {
    //     $user = User::find(Crypt::decrypt($request->id));
    //     if ($user) {

    //         $divisionIds = json_decode($user->division_id, true);
    //             if (is_array($divisionIds)) {
    //                 $divisions = Division::whereIn('id', $divisionIds)->pluck('id')->toArray();
    //                 $divisionId =  implode(', ', $divisions);
    //             }
    //         return response()->json(['success' => true, 'division_ids' => $divisionId]);
    //     }
    //     return response()->json(['success' => false, 'message' => 'User not found'], 404);
    // }

    public function getDivision(Request $request)
    {
        $user = User::find(Crypt::decrypt($request->id));
        if ($user) {
            $divisionIds = json_decode($user->division_id, true);
            if (is_array($divisionIds)) {
                $divisions = Division::whereIn('id', $divisionIds)->get(['id', 'division_name']);

                $divisionData = $divisions->map(function ($division) {
                    return [
                        'id' => $division->id,
                        'division_name' => $division->division_name
                    ];
                });

                return response()->json(['success' => true, 'divisions' => $divisionData]);
            }
        }
        return response()->json(['success' => false, 'message' => 'User not found'], 404);
    }
}
