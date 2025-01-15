<?php

namespace App\Http\Controllers;

use App\Models\Entries;
use App\Models\Organizational;
use App\Models\SuccessIndicator;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class OutcomeController extends Controller
{
    public function index(){

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
        return view('outcome.index', compact('user', 'entriesCount'));
    }

    public function list(Request $request)
    {
        $query = Organizational::whereNull('deleted_at')->orderBy('created_at', 'desc');

        if ($request->has('date_range') && !empty($request->date_range)) {
            [$startDate, $endDate] = explode(' to ', $request->date_range);
            $startDate = Carbon::createFromFormat('m/d/Y', $startDate)->startOfDay();
            $endDate = Carbon::createFromFormat('m/d/Y', $endDate)->endOfDay();

            $query->whereBetween('created_at', [$startDate, $endDate]);
        }

        // if ($request->has('search') && !empty($request->search)) {
        //     $searchTerm = $request->search;

        //     $query->where(function($subQuery) use ($searchTerm) {
        //         $subQuery->where('organizational_outcome', 'like', "%{$searchTerm}%")
        //                  ->orWhere('created_by', 'like', "%{$searchTerm}%")
        //                  ->orWhere('status', 'like', "%{$searchTerm}%");

        //     });
        // }

        $orgs = $query->get();

        return DataTables::of($orgs)
            ->editColumn('id', function($org) {
                return Crypt::encrypt($org->id);

            })
            ->editColumn('created_at', function($org) {
                return $org->created_at->format('m/d/Y');
            })

            ->make(true);
    }

    public function store(Request $request)
    {
        // Custom validation rule to check unique organizational outcomes where deleted_at is null and year is the same or current year
        Validator::extend('unique_with_soft_delete_and_year', function ($attribute, $value, $parameters, $validator) {
            $currentYear = Carbon::now()->format('Y');
            $count = DB::table($parameters[0])
                ->where($parameters[1], $value)
                ->where(function($query) use ($currentYear) {
                    $query->whereNull('deleted_at')
                          ->where(function($subQuery) use ($currentYear) {
                              $subQuery->whereYear('created_at', $currentYear)
                                       ->orWhere('created_at', '>', now()->startOfYear());
                          });
                })
                ->count();
            return $count === 0;
        }, 'The :input has already been taken for the current or same year.');

        $validator = Validator::make($request->all(), [

            'organizational_outcome.*' => 'required|string|max:255|unique_with_soft_delete_and_year:org_otc,organizational_outcome',
            'order.*' => 'required',
            'status' => 'required|string',

        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 200);
        }

        foreach ($request->organizational_outcome as $index => $outcome) {
            $organizational = new Organizational();
            $organizational->organizational_outcome = ucfirst($outcome);
            $organizational->order = $request->order[$index];
            $organizational->category = $request->category[$index];
            $organizational->status = ucfirst($request->status);
            $organizational->created_by = Auth::user()->user_name;
            $organizational->save();
        }

        return response()->json(['success' => true, 'message' => 'Organization Outcome(s) created successfully'], 200);
    }

    public function update(Request $request)
    {
        $id = Crypt::decrypt($request->id);
        // dd($id);
        $validator = Validator::make($request->all(), [
            'order' => 'required',
            'organizational_outcome' => [
            'required',
            'string',
            'max:255',
            Rule::unique('org_otc')->whereNull('deleted_at')->ignore($id), // Ensures unique name except for the current role
        ],
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 200);
        }

        $org = Organizational::findOrFail($id);

        $org->organizational_outcome = ucfirst($request->organizational_outcome);
        $org->order = ucfirst($request->order);
        $org->status = ucfirst($request->status);
        $org->category = ucfirst($request->category);
        $org->created_by = Auth::user()->user_name;
        $org->save();

        return response()->json(['success' => true, 'message' => 'Organization Outcome updated successfully']);
    }

    public function destroy(Request $request)
    {
        $validator = Validator::make($request->all(), [
            // 'id' => 'required|exists:org_otc,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 200);
        }

        // $ifExist = SuccessIndicator::whereNull('deleted_at')->where('org_id', Crypt::decrypt($request->id))->exists();

        // if($ifExist){

        //     return response()->json(['success' => false, 'errors' => 'The Organizational Outcome is being used on Indicator table']);
        // }

        $role = Organizational::findOrFail(Crypt::decrypt($request->id));
        $role->created_by = Auth::user()->user_name;
        $role->delete();

        return response()->json(['success' => true, 'message' => 'Organization Outcome deleted successfully']);
    }

    public function getOrg(Request $request){
        $searchTerm = $request->input('q'); // Capture search term
        $data = Organizational::where('status', 'Active')
                              ->whereNull('deleted_at')
                              ->where('organizational_outcome', 'like', "%{$searchTerm}%")
                              ->get(['id', 'organizational_outcome']);
        return response()->json($data);

    }
}
