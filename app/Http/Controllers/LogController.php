<?php

namespace App\Http\Controllers;

use App\Models\Entries;
use App\Models\LoginModel;
use App\Models\Role;
use App\Models\Sessions;
use App\Models\SuccessIndicator;
use App\Models\User;
use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Yajra\DataTables\Facades\DataTables;

class LogController extends Controller
{
    public function index()
    {

        // dd($filteredIndicators->count());



        // $currentYear = Carbon::now()->format('Y');
        // dd($currentYear);


    // if (!$hasAccess) {
    //     return response()->json([
    //         'success' => false,
    //         'message' => 'You do not have permission to update this indicator.'
    //     ], 403);
    // }

        $indicators = SuccessIndicator::all();
        $matchingUserIds = [];

        foreach ($indicators as $indicator) {
            // Decode the division_id from JSON string to an array in the indicator
            $indicatorDivisionIds = json_decode($indicator->division_id, true);

            if (is_array($indicatorDivisionIds)) {
                // Fetch all users
                $users = User::all();

                foreach ($users as $user) {
                    // Decode the division_id from JSON string to an array in the user
                    $userDivisionIds = json_decode($user->division_id, true);

                    if (is_array($userDivisionIds)) {
                        // Check if there is any common division_id between the indicator and the user
                        $commonDivisions = array_intersect($indicatorDivisionIds, $userDivisionIds);

                        if (!empty($commonDivisions)) {
                            // If a match is found, add the user id to the array
                            $matchingUserIds[] = $user->id;
                        }
                    }
                }
            }
        }

        // // Remove duplicate user IDs (if any)
        // $matchingUserIds = array_unique($matchingUserIds);



        // $indicator = SuccessIndicator::all();

        // dd($matchingUserIds);


        $logPath = storage_path('logs');
        $files = File::files($logPath);

        $latestFile = collect($files)->sortByDesc(function ($file) {
            return $file->getCTime();
        })->first();

        $logContent = $latestFile ? File::get($latestFile) : '';

        return view('logs.index', [
            'logContent' => $logContent,
            'fileName' => $latestFile ? $latestFile->getFilename() : 'No log file found'
        ]);
    }

    public function login_in()
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

        return view('logs.login_in', compact('user', 'entriesCount'));
    }


    public function clear()
    {
        $logPath = storage_path('logs');
        $files = File::files($logPath);

        foreach ($files as $file) {
            File::delete($file);
        }

        return redirect()->route('logs.index')->with('success', 'Logs cleared successfully.');
    }

    public function list(Request $request)
    {
        $query = LoginModel::with('user')->orderBy('created_at', 'desc');

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
        //         $query->whereHas('user', function($subQuery) use ($firstName, $lastName) {
        //             $subQuery->where('first_name', 'like', "%{$firstName}%")
        //                     ->where('last_name', 'like', "%{$lastName}%");
        //         });
        //     } else {
        //         $query->whereHas('user', function($subQuery) use ($searchTerm) {
        //             $subQuery->where('user_name', 'like', "%{$searchTerm}%");

        //         });
        //     }
        // }

        $login_in = $query->get();
        return DataTables::of($login_in)
            ->addColumn('id', function($data) {
                return Crypt::encrypt($data->id);

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
               ;
            })
            ->editColumn('created_at', function($data) {
                return $data->created_at->format('m/d/Y H:i:s');
            })

            ->make(true);
    }

}
