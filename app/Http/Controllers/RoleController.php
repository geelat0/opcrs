<?php

namespace App\Http\Controllers;

use App\Models\Entries;
use App\Models\Permission;
use App\Models\Role;
use App\Models\SuccessIndicator;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class RoleController extends Controller
{

    public function roles()
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
        return view('role_page.roles', compact('user', 'entriesCount'));
    }

    public function getRole(Request $request){

        $roles = Role::where('status', 'Active')->whereNull('deleted_at')->get(['id', 'name']);
        return response()->json($roles);


    }

    public function list(Request $request)
    {
        $query = Role::whereNull('deleted_at') ->orderBy('created_at', 'desc');

        if ($request->has('date_range') && !empty($request->date_range)) {
            [$startDate, $endDate] = explode(' to ', $request->date_range);
            $startDate = Carbon::createFromFormat('m/d/Y', $startDate)->startOfDay();
            $endDate = Carbon::createFromFormat('m/d/Y', $endDate)->endOfDay();

            $query->whereBetween('created_at', [$startDate, $endDate]);
        }

        // if ($request->has('search') && !empty($request->search)) {
        //     $searchTerm = $request->search;

        //     $query->where(function($subQuery) use ($searchTerm) {
        //         $subQuery->where('name', 'like', "%{$searchTerm}%")
        //                 //  ->orWhere('created_by', 'like', "%{$searchTerm}%")
        //                  ->orWhere('status', 'like', "%{$searchTerm}%");

        //     });
        // }

        $roles = $query->get();

        return DataTables::of($roles)
            ->editColumn('id', function($role) {
                return Crypt::encrypt($role->id);

            })
            ->editColumn('created_at', function($role) {
                return $role->created_at->format('m/d/Y');
            })

            ->make(true);
    }


    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:role',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false,'errors' => $validator->errors()], 200);
        }

        $user = new Role();
        $user->name = ucfirst($request->name);
        $user->status = ucfirst($request->status);
        $user->created_by = Auth::user()->user_name;
        $user->save();

        return response()->json(['success' => true, 'message' => 'Role created successfully'], 200);
    }


    public function update(Request $request)
    {
        $id = Crypt::decrypt($request->id);
        // dd($id);
        $validator = Validator::make($request->all(), [
            'name' => [
            'required',
            'string',
            'max:255',
            Rule::unique('role')->ignore($id), // Ensures unique name except for the current role
        ],
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 200);
        }

        $user = Role::findOrFail($id);

        $user->name = ucfirst($request->name);
        $user->status = ucfirst($request->status);
        $user->created_by = Auth::user()->user_name;
        $user->save();

        return response()->json(['success' => true, 'message' => 'Role updated successfully']);
    }



    public function destroy(Request $request)
    {
        $validator = Validator::make($request->all(), [
            // 'id' => 'required|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 200);
        }

        $ifExist = User::whereNull('deleted_at')->where('role_id', Crypt::decrypt($request->id))->exists();

        if($ifExist){

            return response()->json(['success' => false, 'errors' => 'The role is being use in by users, Cannot be deleted']);
        }

        $role = Role::findOrFail(Crypt::decrypt($request->id));
        $role->created_by = Auth::user()->user_name;
        $role->delete();

        return response()->json(['success' => true, 'message' => 'Role deleted successfully']);
    }


}
