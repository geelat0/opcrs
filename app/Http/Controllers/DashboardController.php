<?php

namespace App\Http\Controllers;

use App\Models\Entries;
use App\Models\LoginModel;
use App\Models\Role;
use App\Models\Sessions;
use App\Models\SuccessIndicator;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Contracts\Session\Session;
use Illuminate\Support\Facades\Crypt;
use Yajra\DataTables\DataTables;

class DashboardController extends Controller
{
    public function index()
    {
            $userCount = User::count();
            $roleCount = Role::count();
            $user = Auth::user();

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


            $activeThreshold = now()->subMinutes(5)->timestamp;

            // Query active sessions
            $loggedInUsersCount = Sessions::with('user')
                ->whereNotNull('user_id')
                ->where('last_activity', '>=', $activeThreshold)  // Only get active sessions
                ->distinct('user_id')
                ->count('user_id');

            $CompleteEntriesCount = Entries::whereNull('deleted_at')->with('indicator')->where('months', $targetMonth)
            ->whereYear('created_at', $current_Year)
            ->where('status', 'Completed')
            ->where('user_id',  Auth::user()->id)
            ->count();

            $entriesCount = $filteredIndicators->count();
           
            return view('dashboard', compact('user', 'userCount', 'roleCount', 'entriesCount', 'loggedInUsersCount', 'targetMonth', 'CompleteEntriesCount'));
    }

    public function filter(Request $request)
    {
        $startDate = $request->input('start_date') ? Carbon::parse($request->input('start_date'))->startOfDay() : null;
        $endDate = $request->input('end_date') ? Carbon::parse($request->input('end_date'))->endOfDay() : null;
        $month = $request->input('month');
        $year = $request->input('year');

        $userQuery = User::query();
        $roleQuery = Role::query();

        // Apply date range filter
        if ($startDate && $endDate) {
            $userQuery->whereBetween('created_at', [$startDate, $endDate]);
            $roleQuery->whereBetween('created_at', [$startDate, $endDate]);
        } else if ($startDate) {
            $userQuery->where('created_at', '>=', $startDate);
            $roleQuery->where('created_at', '>=', $startDate);
        } else if ($endDate) {
            $userQuery->where('created_at', '<=', $endDate);
            $roleQuery->where('created_at', '<=', $endDate);
        }

        // Apply month and year filters
        if ($month) {
            $userQuery->whereMonth('created_at', $month);
            $roleQuery->whereMonth('created_at', $month);
        }

        if ($year) {
            $userQuery->whereYear('created_at', $year);
            $roleQuery->whereYear('created_at', $year);
        }

        $userCount = $userQuery->count();
        $roleCount = $roleQuery->count();


        $currentYear = Carbon::now()->format('Y');
        $currentUser = Auth::user();
        $entriesCount = SuccessIndicator::whereNull('deleted_at')->whereYear('created_at', $currentYear);

        // Apply date range filter for SuccessIndicator entries
        // if ($startDate && $endDate) {
        //     $entriesCount->whereBetween('created_at', [$startDate, $endDate]);
        // } else if ($startDate) {
        //     $entriesCount->where('created_at', '>=', $startDate);
        // } else if ($endDate) {
        //     $entriesCount->where('created_at', '<=', $endDate);
        // }

        // Apply month and year filters for SuccessIndicator entries
        // if ($month) {
        //     $entriesCount->whereMonth('created_at', $month);
        // }

        if ($year) {
            $entriesCount->whereYear('created_at', $year);
        }

        $indicators = $entriesCount->get();
        $userDivisionIds = json_decode($currentUser->division_id, true);
        $filteredIndicators = $indicators->filter(function($indicator) use ($userDivisionIds) {
            $indicatorDivisionIds = json_decode($indicator->division_id, true);
            return !empty(array_intersect($userDivisionIds, $indicatorDivisionIds));
        });

        $currentDate = Carbon::now();

        $targetMonth = $currentDate->day > 5 ? $currentDate->month : $currentDate->subMonth()->month;

        $filteredIndicators = $filteredIndicators->filter(function($indicator) use ($targetMonth, $currentYear) {
            $completedEntries = Entries::where('indicator_id', $indicator->id)
                ->where('months', $targetMonth)
                ->whereYear('created_at', $currentYear)
                ->where('status', 'Completed')
                ->where('user_id', Auth::user()->id)
                ->exists();
            return !$completedEntries;
        });

        $entriesCount = $filteredIndicators->count();

        return response()->json([
            'userCount' => $userCount,
            'roleCount' => $roleCount,
            'entriesCount' => $entriesCount,
        ]);
    }

    public function Loginlist(Request $request)
    {
        // Define the threshold for active sessions (e.g., 15 minutes)
        $activeThreshold = now()->subMinutes(5)->timestamp;

        // Query active sessions
        $query = Sessions::with('user')
            ->whereNotNull('user_id')
            ->where('last_activity', '>=', $activeThreshold)  // Only get active sessions
            ->distinct('user_id');

        $login_in = $query->get();

        return DataTables::of($login_in)
            ->editColumn('id', function($data) {
                return $data->id;
            })
            ->addColumn('user', function($data) {
                return ucfirst($data->user->first_name). ' ' . ucfirst(substr($data->user->middle_name, 0, 1)).'.'.' ' . ucfirst($data->user->last_name);
            })
            ->addColumn('user_name', function($data) {
                return ucfirst($data->user->user_name);
            })
            ->addColumn('position', function($data) {
                return ucfirst($data->user->position);
            })
            ->addColumn('role', function($data) {
                $role = Role::where('id',  $data->user->role_id)->first();
                return $role->name;
            })
            ->editColumn('last_activity', function($data) {
                // $role = Role::where('id',  $data->user->role_id)->first();
                return Carbon::createFromTimestamp($data->last_activity)->diffForHumans();
            })
            ->make(true);
    }

    public function fetchDashboardData(Request $request)
    {
        // You can use the same logic you have in the index method
        $userCount = User::count();
        $roleCount = Role::count();
        $currentYear = Carbon::now()->format('Y');
        $currentUser = Auth::user();

        $entriesCount = SuccessIndicator::whereNull('deleted_at')->whereYear('created_at', $currentYear);

        $indicators = $entriesCount->get();
        $userDivisionIds = json_decode($currentUser->division_id, true);
        $filteredIndicators = $indicators->filter(function($indicator) use ($userDivisionIds) {
            $indicatorDivisionIds = json_decode($indicator->division_id, true);
            return !empty(array_intersect($userDivisionIds, $indicatorDivisionIds));
        });

        $currentDate = Carbon::now();
        $targetMonth = $currentDate->day > 5 ? $currentDate->month : $currentDate->subMonth()->month;
        $filteredIndicators = $filteredIndicators->filter(function($indicator) use ($targetMonth, $currentYear) {
            $completedEntries = Entries::where('indicator_id', $indicator->id)
                ->where('months', $targetMonth)
                ->whereYear('created_at', $currentYear)
                ->where('status', 'Completed')
                ->where('user_id', Auth::user()->id)
                ->exists();
            return !$completedEntries;
        });

        $entriesCount = $filteredIndicators->count();
        $activeThreshold = now()->subMinutes(5)->timestamp;

        // Query active sessions
        $loggedInUsersCount = Sessions::with('user')
            ->whereNotNull('user_id')
            ->where('last_activity', '>=', $activeThreshold)  // Only get active sessions
            ->distinct('user_id')
            ->count('user_id');

        return response()->json([
            'userCount' => $userCount,
            'roleCount' => $roleCount,
            'entriesCount' => $entriesCount,
            'loggedInUsersCount' => $loggedInUsersCount,
        ]);
    }
}
