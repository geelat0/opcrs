<?php

namespace App\Http\Controllers;

use App\Models\CategoryUpload;
use App\Models\Entries;
use App\Models\Role;
use App\Models\Sessions;
use App\Models\SuccessIndicator;
use App\Models\Upload;
use App\Models\UploadLogs;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Contracts\Session\Session;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;
class UploadCategoryController extends Controller
{
    public function index(){

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

        $categories = CategoryUpload::whereNull('deleted_at')->get();
       
        return view('upload_category.index', compact('user', 'userCount', 'roleCount', 'entriesCount', 'loggedInUsersCount', 'targetMonth', 'CompleteEntriesCount', 'categories'));
        
    }

    public function store(Request $request){
        try{
            $validator = Validator::make($request->all(), [
                'category_name' => [
                    'required',
                    'string',
                    'regex:/^[a-zA-Z\s]+$/',
                    function ($attribute, $value, $fail) use ($request) {
                        $existingCategory = CategoryUpload::where('category_name', $value)
                            ->first();
                        if ($existingCategory) {
                            $fail('Upload category already exists');
                        }
                    },
                ],
            ]);
            
            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()]);
            }

            $category = new CategoryUpload();
            $category->category_name = $request->category_name;
            $category->created_by = Auth::user()->user_name;
            $category->save();

            return response()->json(['success' => 'true', 'message' => 'Upload Category added succesfully']);

        }catch(\Exception $e){
            return response()->json(['error' => $e->getMessage()], 500);
        }

    }

    public function update(Request $request){
        try{
            $validator = Validator::make($request->all(), [
                'category_name' => [
                    'required',
                    'string',
                    'regex:/^[a-zA-Z\s]+$/',
                    function ($attribute, $value, $fail) use ($request) {
                        $existingCategory = CategoryUpload::where('category_name', $value)
                            ->where('id', '<>', decrypt($request->id))
                            ->first();
                        if ($existingCategory) {
                            $fail('Upload category already exists');
                        }
                    },
                ],
            ]);
            
            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()]);
            }

            $category = CategoryUpload::findOrFail(decrypt($request->id));
            $category->category_name = $request->category_name;
            $category->updated_at = Auth::user()->user_name;
            $category->updated_at = now();
            $category->save();

            return response()->json(['success' => 'true', 'message' => 'Upload Category updated succesfully']);

        }catch(\Exception $e){
            return response()->json(['error' => $e->getMessage()], 500);
        }

    }

    public function deleted(Request $request){
        try{
            $category = CategoryUpload::findOrFail(decrypt($request->id));
            $category->updated_at = Auth::user()->user_name;
            $category->deleted_at = now();
            $category->save();

            return response()->json(['success' => true, 'message' => 'Upload category deleted successfully']);

        }catch(\Exception $e){
            return response()->json(['error' => $e->getMessage()], 500);

        }

    }

    public function list(Request $request){
        $query = CategoryUpload::whereNull('deleted_at') ->orderBy('created_at', 'desc');

        if ($request->has('date_range') && !empty($request->date_range)) {
            [$startDate, $endDate] = explode(' to ', $request->date_range);
            $startDate = Carbon::createFromFormat('m/d/Y', $startDate)->startOfDay();
            $endDate = Carbon::createFromFormat('m/d/Y', $endDate)->endOfDay();

            $query->whereBetween('created_at', [$startDate, $endDate]);
        }


        $list = $query->get();

        return DataTables::of($list)
            ->editColumn('id', function($data) {
                return Crypt::encrypt($data->id);

            })
            ->editColumn('created_at', function($data) {
                return $data->created_at->format('m/d/Y');
            })
            ->editColumn('updated_at', function($data) {
                return $data->updated_at->format('m/d/Y');
            })

            ->make(true);
    }


}
