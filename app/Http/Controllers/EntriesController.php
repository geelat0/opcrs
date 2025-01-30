<?php

namespace App\Http\Controllers;

use App\Models\Division;
use App\Models\Entries;
use App\Models\Role;
use App\Models\SuccessIndicator;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class EntriesController extends Controller
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

        return view('entries.index', compact('user', 'entriesCount'));
    }

    public function  create(Request $request){
        $id = $request->query('id');

        $entries = SuccessIndicator::find(Crypt::decrypt($id));

        if ($entries && $entries->file) {
            // Decode the Base64 file to get the original contents
            $fileContents = base64_decode($entries->file);
            // Save the file temporarily or just pass the necessary data to the view
            $fileName = 'entry_' . $entries->id . '.pdf'; // Example file name
            Storage::put('public/entries/' . $fileName, $fileContents);
            $fileUrl = Storage::url('public/entries/' . $fileName);
        } else {
            $fileUrl = null;
        }

        $user=Auth::user();

        $currentYear = Carbon::now()->format('Y');
        $currentUser = Auth::user();
        $entriesCount = SuccessIndicator::whereNull('deleted_at')->whereYear('created_at', $currentYear);

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

        if ($currentDate->month == 1 && $currentDate->day <= 5) {
            $targetMonth = $currentDate->subMonth()->month;
            $current_Year = $currentDate->subYear()->format('Y');
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
        // $entries_id = $entries->id;
        // dd($entries_id);

        return view('entries.create', compact('user', 'entries', 'fileUrl', 'entriesCount'));
    }

    public function getMeasureDetails(Request $request)
    {
        $id = $request->input('id');

        $user = User::find(Auth::id());
        $userDivisionIds = json_decode($user->division_id, true); // Get the user's division IDs
        $measure = SuccessIndicator::findOrFail($id);

        $measureDivisionIds = json_decode($measure->division_id, true); // Get the measure's division IDs

        // Filter measureDivisionIds to only include those in userDivisionIds
        $filteredDivisionIds = array_intersect($measureDivisionIds, $userDivisionIds);

        $filteredDivisionIds = array_values($filteredDivisionIds);

        $division_targets = [];
        $division_budget = [];

        if( Auth::user()->role->name === 'SuperAdmin'){
            foreach ($measureDivisionIds as $division_id) {
                $division = Division::find($division_id);
                $cleanedDivisionName = preg_replace('/\s*PO$/', '', $division->division_name);
    
                $column_name = "{$cleanedDivisionName}_target";
                $division_targets[$division_id] = $measure->$column_name ?? '';
    
                $column_name_budget = "{$cleanedDivisionName}_budget";
                $division_budget[$division_id] = $measure->$column_name_budget ?? '';
                $division_name[$division_id] = $division->division_name;
            }

        }else{
            foreach ($filteredDivisionIds as $division_id) {
                $division = Division::find($division_id);
                $cleanedDivisionName = preg_replace('/\s*PO$/', '', $division->division_name);
    
                $column_name = "{$cleanedDivisionName}_target";
                $division_targets[$division_id] = $measure->$column_name ?? '';
    
                $column_name_budget = "{$cleanedDivisionName}_budget";
                $division_budget[$division_id] = $measure->$column_name_budget ?? '';
                $division_name[$division_id] = $division->division_name;
            }

        }

        

        $divisions = [];
        if (is_array($userDivisionIds)) {

        if (Auth::user()->role->name !== 'SuperAdmin') {
            $divisions = Division::whereIn('id', $filteredDivisionIds)->get(['id', 'division_name']);
        }else{

            $divisions = Division::get(['id', 'division_name']);
        }

            $divisionData = $divisions->map(function ($division) {
                return [
                    'id' => $division->id,
                    'division_name' => $division->division_name
                ];
            });
        }

        $data = [
            'measure' => $measure,
            'division_ids' => Auth::user()->role->name === 'SuperAdmin' ? $measureDivisionIds : $filteredDivisionIds, // Show all division IDs for super users // Return the filtered division IDs
            'division_targets' => $division_targets,
            'division_budget' => $division_budget,
            'divisions' => $divisionData ?? [],
            'division_name' => $division_name,
        ];

        return response()->json($data);
    }

    public function edit(Request $request){

        $id = $request->query('id');

        $entries = Entries::find(Crypt::decrypt($id));

        if ($entries && $entries->file) {
            // Decode the Base64 file to get the original contents
            $fileContents = base64_decode($entries->file);
            // Save the file temporarily or just pass the necessary data to the view
            $fileName = 'entry_' . $entries->id . '.pdf'; // Example file name
            Storage::put('public/entries/' . $fileName, $fileContents);
            $fileUrl = Storage::url('public/entries/' . $fileName);
        } else {
            $fileUrl = null;
        }

        $user=Auth::user();

        $currentYear = Carbon::now()->format('Y');
        $currentUser = Auth::user();
        $entriesCount = SuccessIndicator::whereNull('deleted_at')->whereYear('created_at', $currentYear);

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
        return view('entries.edit', compact('user', 'entries', 'fileUrl', 'entriesCount'));
    }

    public function view(Request $request){

        $id = $request->query('id');

        $entries = Entries::find(Crypt::decrypt($id));
        $indicator = SuccessIndicator::find($entries->indicator_id);

        if ($entries && $entries->file) {
            // Decode the Base64 file to get the original contents
            $fileContents = base64_decode($entries->file);
            // Save the file temporarily or just pass the necessary data to the view
            $fileName = 'entry_' . $entries->id . '.pdf'; // Example file name
            Storage::put('public/entries/' . $fileName, $fileContents);
            $fileUrl = Storage::url('public/entries/' . $fileName);
        } else {
            $fileUrl = null;
        }

        $user=Auth::user();

        $currentYear = Carbon::now()->format('Y');
        $currentUser = Auth::user();
        $entriesCount = SuccessIndicator::whereNull('deleted_at')->whereYear('created_at', $currentYear);

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

        $indicatorDivisionIds = json_decode($indicator->division_id, true);
        $indicatorDivisionIds = array_map('intval', $indicatorDivisionIds);

        // Keep only the divisions that match the user's divisions
        $filteredDivisionIds = array_intersect($userDivisionIds, $indicatorDivisionIds);

        if(Auth::user()->role->name === 'SuperAdmin'){
            foreach ($indicatorDivisionIds as $division_id) {
                $division = Division::find($division_id);
                $cleanedDivisionName = preg_replace('/\s*PO$/', '', $division->division_name);
                $column_name = "{$cleanedDivisionName}_target";
                $division_targets[$division_id] = $entries->$column_name ?? '';
    
                $column_name_accomplishment = "{$cleanedDivisionName}_accomplishment";
                $division_accomplishment[$division_id] = $entries->$column_name_accomplishment ?? '';
            }

        }else{
            foreach ($filteredDivisionIds as $division_id) {
                $division = Division::find($division_id);
                $cleanedDivisionName = preg_replace('/\s*PO$/', '', $division->division_name);
                $column_name = "{$cleanedDivisionName}_target";
                $division_targets[$division_id] = $entries->$column_name ?? '';
    
                $column_name_accomplishment = "{$cleanedDivisionName}_accomplishment";
                $division_accomplishment[$division_id] = $entries->$column_name_accomplishment ?? '';
            }

        }

       

        $division_ids = $filteredDivisionIds;


        return view('entries.view', compact('user', 'entries', 'fileUrl', 'entriesCount', 'indicator', 'division_ids', 'division_targets', 'division_accomplishment', 'entriesCount'));
    }


    public function getIndicator(Request $request){

        if(in_array(Auth::user()->role->name, ['SuperAdmin', 'Admin'])){
            $searchTerm = $request->input('q'); // Capture search term
            $data = SuccessIndicator::where('status', 'Active')
                                  ->whereNull('deleted_at')
                                  ->where('target', 'like', "%{$searchTerm}%")
                                  ->orWhere('measures', 'like', "%{$searchTerm}%")
                                  ->get(['id', 'target', 'measures']);
            return response()->json($data);

        }else{
            $searchTerm = $request->input('q');

            // Get the current user's division IDs
            $userDivisionIds = User::where('id', Auth::user()->id)
                ->pluck('division_id')
                ->first();
            $userDivisionIds = json_decode($userDivisionIds, true);
            $userDivisionIds = array_map('intval', $userDivisionIds);

            // Fetch success indicators where the user's division_id exists in the success indicator's division_id field
            $data = SuccessIndicator::where('status', 'Active')
                ->whereNull('deleted_at')
                ->where('measures', 'like', "%{$searchTerm}%")
                ->get(['id', 'measures', 'division_id', 'target'])
                ->filter(function($indicator) use ($userDivisionIds) {
                    $indicatorDivisionIds = json_decode($indicator->division_id, true);
                    $indicatorDivisionIds = array_map('intval', $indicatorDivisionIds);
                    return !empty(array_intersect($userDivisionIds, $indicatorDivisionIds));
                })
                ->values(); // Re-index the array

            return response()->json($data);
        }
    }

    public function list(Request $request){
        $currentUser = Auth::user();
        $currentYear = Carbon::now()->format('Y');

        // Build the initial query for SuccessIndicator
        $query = SuccessIndicator::whereNull('deleted_at')
        ->whereHas('org', function ($query) {
            $query->where('status', 'Active');
        })
        ->with(['division', 'org'])->whereYear('created_at', $currentYear) ->orderBy('created_at', 'desc');

        // Filter by date range
        if ($request->has('date_range') && !empty($request->date_range)) {
            [$startDate, $endDate] = explode(' to ', $request->date_range);
            $startDate = Carbon::createFromFormat('m/d/Y', $startDate)->startOfDay();
            $endDate = Carbon::createFromFormat('m/d/Y', $endDate)->endOfDay();
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }

        // Fetch all indicators
        $indicators = $query->get();

        // Filter indicators based on current user's division_id
        $userDivisionIds = json_decode($currentUser->division_id, true);
        $filteredIndicators = $indicators->filter(function($indicator) use ($userDivisionIds) {
            $indicatorDivisionIds = json_decode($indicator->division_id, true);
            return !empty(array_intersect($userDivisionIds, $indicatorDivisionIds));
        });

        // Get the current month
        $currentMonth = Carbon::now()->format('m');
        $current_Year = Carbon::now()->format('Y');
        $current_day = Carbon::now()->format('d');


        $currentDate = Carbon::now();

        if ($currentDate->day > 5) {
            $targetMonth = $currentDate->month;
        } else {
            $targetMonth = $currentDate->subMonth()->month;
        }

        // Further filter indicators based on the entries table
        $filteredIndicators = $filteredIndicators->filter(function($indicator) use ($targetMonth, $current_Year) {
            $completedEntries = Entries::where('indicator_id', $indicator->id)
                                       ->where('months', $targetMonth)
                                       ->whereYear('created_at', $current_Year)
                                       ->where('status', 'Completed')
                                       ->where('user_id',  Auth::user()->id)
                                       ->exists();
            return !$completedEntries;
        });

        return DataTables::of($filteredIndicators)
            ->editColumn('id', function($data) {
                return Crypt::encrypt($data->id);
            })
            // ->editColumn('org_id', function($data) {
            //     return $data->org->organizational_outcome;
            // })
            ->editColumn('indicator_id', function($data) {
                return '(' . $data->target . ')' . '  ' . $data->measures;
            })
            ->editColumn('responsible_user', function($data) {
                return Auth::user()->first_name. ' ' .Auth::user()->last_name;
            })
            ->editColumn('file', function($data) {
                return '';
            })
            ->editColumn('status', function($data) {
                return 'Pending';
            })
            ->editColumn('created_at', function($data) {
                return $data->created_at->format('m/d/Y');
            })
            ->editColumn('year', function($data) {
                $currentDate = Carbon::now();
                // return $data->created_at->format('Y');
                if ($currentDate->month == 1 && $currentDate->day <= 5) {
                    $current_Year = $currentDate->subYear()->format('Y');
                    // dd($targetMonth, $current_Year);
    
                }else{
                    $current_Year = Carbon::now()->format('Y');
                }
                return $current_Year;
            })
            ->editColumn('months', function($data) {
                $currentDate = Carbon::now();

                if ($currentDate->day > 5) {
                    $targetMonth = $currentDate->month;
                } else {
                    $targetMonth = $currentDate->subMonth()->month;
                }
                return $data->months ? date('F', mktime(0, 0, 0, $data->months, 10)) : date('F', mktime(0, 0, 0, $targetMonth, 10));
            })
            ->make(true);
    }

    public function completed_list(Request $request){
        if(in_array(Auth::user()->role->name, ['SuperAdmin', 'Admin'])){

            $query = Entries::whereNull('deleted_at')->with('indicator')->where('status', 'Completed')->orderBy('created_at', 'desc');

        }else{

            $query = Entries::whereNull('deleted_at')->with(['indicator', 'user'])->where('status', 'Completed')->where('user_id', Auth::user()->id)->orderBy('created_at', 'desc');
        }

        if ($request->has('date_range') && !empty($request->date_range)) {
            [$startDate, $endDate] = explode(' to ', $request->date_range);
            $startDate = Carbon::createFromFormat('m/d/Y', $startDate)->startOfDay();
            $endDate = Carbon::createFromFormat('m/d/Y', $endDate)->endOfDay();

            $query->whereBetween('created_at', [$startDate, $endDate]);
        }

        $indicator = $query->get();

        return DataTables::of($indicator)
            ->editColumn('id', function($data) {
                return Crypt::encrypt($data->id);
            })
           
            ->editColumn('indicator_id', function($data) {
                return '(' .$data->indicator->target .')' . '  '. $data->indicator->measures;
            })
            ->editColumn('responsible_user', function($data) {
                return  $data->user->first_name . ' ' .$data->user->last_name;
            })
            ->editColumn('created_at', function($data) {
                return $data->created_at->format('m/d/Y');
            })
            ->editColumn('year', function($data) {
                return $data->year ?? '';
            })
            ->editColumn('months', function($data) {
                return $data->months ? date('F', mktime(0, 0, 0, $data->months, 10)): '';
            })

            ->make(true);
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'accomplishment' => 'required|string',
            'file' => 'nullable|file|mimes:pdf|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $entry = Entries::find($request->input('id'));

        if ($request->hasFile('file')) {
            // Handle the file
            $file = $request->file('file');
            $fileContents = file_get_contents($file->getRealPath());
            $base64File = base64_encode($fileContents);

            // Validate the Base64 data (Check for PDF magic number)
            if (substr($fileContents, 0, 4) !== '%PDF') {
                return response()->json(['errors' => ['file' => 'Invalid PDF file']], 422);
            }

        }else{
            //fetch the existing file
            $base64File = $entry->file;
        }

        $currentDate = Carbon::now();

        if ($currentDate->day > 5) {
            $targetMonth = $currentDate->addMonth()->month;
        } else {
            $targetMonth = $currentDate->subMonth()->month;
        }

        if ($currentDate->month == 1 && $currentDate->day <= 5) {
            $targetMonth = $currentDate->subMonth()->month;
            $current_Year = $currentDate->subYear()->format('Y');
        }

        $entry->accomplishment = $request->input('accomplishment');
        $entry->file = $base64File;
        // $entry->months = $targetMonth;
        $entry->status = 'Completed';
        $entry->created_by = Auth::user()->user_name;
        $entry->save();

        return response()->json([
            'success' => true,
            'message' => 'Accomplishment updated successfully!',
            'entry' => $entry
        ]);
    }

    public function destroy(Request $request)
    {
        $validator = Validator::make($request->all(), [
            // 'id' => 'required|exists:org_otc,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 200);
        }

        $role = Entries::findOrFail(Crypt::decrypt($request->id));
        $role->updated_by = Auth::user();
        $role->delete();

        return response()->json(['success' => true, 'message' => 'Accomplishment deleted successfully']);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'indicator_id' => 'required|exists:success_indc,id',
            'total_accomplishment' => 'required',
            'accomplishment_text' => 'required',
            'file' => 'required|file|mimes:pdf|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Handle the file
        $file = $request->file('file');
        $fileContents = file_get_contents($file->getRealPath()); // Get the file contents
        $base64File = base64_encode($fileContents); // Convert to Base64

        // Validate the Base64 data (Check for PDF magic number)
        if (substr($fileContents, 0, 4) !== '%PDF') {
            return response()->json(['errors' => ['file' => 'Invalid PDF file']], 422);
        }

        $currentDate = Carbon::now();

        if ($currentDate->day > 5) {
            $current_Year = Carbon::now()->format('Y');
            $targetMonth = $currentDate->month;
        } else {

            if ($currentDate->month == 1 && $currentDate->day <= 6) {
                $current_Year = $currentDate->subYear()->format('Y');
                $targetMonth = $currentDate->subMonth()->month;

            }else{
                $current_Year = Carbon::now()->format('Y');
                $targetMonth = $currentDate->subMonth()->month;
            }
        }

        $entry = Entries::create([
            'indicator_id' => $request->input('indicator_id'),
            'file' => $base64File, // Store the Base64 string directly
            'months' => $targetMonth,
            'Albay_accomplishment' => str_replace(['[', ']', '"'], '', json_encode($request->input('Albay_accomplishment') ?? 0)),
            'Camarines_Sur_accomplishment' => str_replace(['[', ']', '"'], '', json_encode($request->input('Camarines_Sur_accomplishment') ?? 0)),
            'Camarines_Norte_accomplishment' => str_replace(['[', ']', '"'], '', json_encode($request->input('Camarines_Norte_accomplishment') ?? 0)),
            'Catanduanes_accomplishment' => str_replace(['[', ']', '"'], '', json_encode($request->input('Catanduanes_accomplishment') ?? 0)),
            'Masbate_accomplishment' => str_replace(['[', ']', '"'], '', json_encode($request->input('Masbate_accomplishment') ?? 0)),
            'Sorsogon_accomplishment' => str_replace(['[', ']', '"'], '', json_encode($request->input('Sorsogon_accomplishment') ?? 0)),
            'year' => $current_Year,
            'total_accomplishment' => $request->total_accomplishment,
            'accomplishment_text' => trim($request->accomplishment_text) ,
            'user_id' => Auth::user()->id,
            'created_by' => Auth::user()->user_name,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Accomplishment created successfully!',
            'entry' => $entry
        ]);
    }
}
