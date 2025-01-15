<?php

namespace App\Http\Controllers;

use App\Models\Entries;
use App\Models\Permission;
use App\Models\Role;
use App\Models\SuccessIndicator;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class PermissionController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:permissions',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false,'errors' => $validator->errors()], 200);
        }

        $user = new Permission();
        $user->name = ucfirst($request->name);
        $user->save();

        return response()->json(['success' => true, 'message' => 'Permission created successfully'], 200);
    }

    public function editPermissions(Role $role)
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
        $permissions = Permission::all();
        $roles = Role::all(); // Get all roles
        return view('role_page.permissions', compact('role', 'permissions', 'user', 'entriesCount', 'roles'));
    }

    public function updatePermissions(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'role_id' => 'required|exists:role,id',
            'permissions' => 'array|exists:permissions,id',
            // 'permissions' => 'required',
        ],[
            // 'permissions.required' => 'Please select atleast one permission.'
        ]
        );

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 200);
        }

        // Find the role by ID
        $role = Role::findOrFail($request->role_id);

        // Sync the permissions with the role
        $role->permissions()->sync($request->permissions);

        // Redirect back with a success message
        return response()->json(['success' => true, 'message' => 'Permissions updated successfully']);
    }

    public function fetchPermissions(Request $request)
    {
        $roleId = $request->input('role_id');
        $role = Role::findOrFail($roleId);

        // Get the permission IDs associated with the role
        $permissions = $role->permissions->pluck('id');

        return response()->json([
            'success' => true,
            'permissions' => $permissions
        ]);
    }
}
