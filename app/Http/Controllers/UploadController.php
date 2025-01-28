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

class UploadController extends Controller
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
       
        return view('upload.upload-index', compact('user', 'userCount', 'roleCount', 'entriesCount', 'loggedInUsersCount', 'targetMonth', 'CompleteEntriesCount', 'categories'));
    }

    public function store(Request $request){

        try{
            $validator = Validator::make($request->all(), [
                'upload_category_id' => 'required|exists:category_upload,id',
                'file' => 'required|file|mimes:pdf,xlsx,xls|max:102400',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            // Handle the file
            $file = $request->file('file');
            $fileContents = file_get_contents($file->getRealPath()); // Get the file contents
            $base64File = base64_encode($fileContents); // Convert to Base64

            // Validate the Base64 data
            if (in_array($file->getMimeType(), ['application/pdf', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' ,'application/vnd.ms-excel'])) {
                if ($file->getMimeType() === 'application/pdf' && substr($fileContents, 0, 4) !== '%PDF') {
                    return response()->json(['errors' => ['file' => 'Invalid PDF file']], 422);
                }
                // Add additional checks for Excel and CSV if needed
            } else {
                return response()->json(['errors' => ['file' => 'Invalid file type']], 422);
            }

            // Generate the code
            $lastUpload = Upload::orderBy('id', 'desc')->first();
            $newCode = $lastUpload ? sprintf('UL-%04d', intval(substr($lastUpload->code, 3)) + 1) : 'UL-0001';

            // Save the file to the database
            $upload = new Upload();
            $upload->code = $newCode;
            $upload->upload_category_id = $request->upload_category_id;
            $upload->original_filename = $file->getClientOriginalName(); // Store the original filename
            $upload->file = $base64File;
            $upload->created_by = Auth::user()->user_name;
            $upload->save();

            $upload_logs = new UploadLogs();
            $upload_logs->upload_id = $upload->id;
            $upload_logs->user = Auth::user()->first_name . ' ' . Auth::user()->last_name;
            $upload_logs->activity = 'Uploaded a file';
            $upload_logs->save();

            return response()->json(['success'=>'true',  'message' => 'File uploaded successfully'], 200);


        }catch(\Exception $e){
            return response()->json(['error' => $e->getMessage()], 500);
        }
        
    }

    public function getCategoryUploads(Request $request){
        try{
            $searchTerm = $request->input('q'); // Capture search term
            $data = CategoryUpload::whereNull('deleted_at')
                                    ->where('category_name', 'like', "%{$searchTerm}%")
                                    ->get(['id', 'category_name']);

            return response()->json($data);

        }catch(\Exception $e){
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function list(Request $request){
        try{

            if($request->upload_category){
                $category = CategoryUpload::where('category_name', $request->upload_category)->first()->id;
            }else{
                $category = CategoryUpload::first()->id;
            }


            if(Auth::user()->role->name === 'SuperAdmin' || Auth::user()->role->name === 'Admin'){
                $query = Upload::whereNull('deleted_at')
                    ->with('upload_category')
                    ->where('upload_category_id', $category);           
            }
            else{
                $query = Upload::whereNull('deleted_at')
                    ->with('upload_category')
                    ->where('upload_category_id', $category)
                    ->where('created_by', Auth::user()->user_name);
            }

            $query->orderByRaw('MONTH(created_at) DESC');

            if ($request->has('date_range') && !empty($request->date_range)) {
                [$startDate, $endDate] = explode(' to ', $request->date_range);
                $startDate = Carbon::createFromFormat('m/d/Y', $startDate)->startOfDay();
                $endDate = Carbon::createFromFormat('m/d/Y', $endDate)->endOfDay();
    
               
            }else{
                $startDate = Carbon::now()->startOfMonth();
                $endDate = Carbon::now()->endOfMonth();
            }

            $query->whereBetween('created_at', [$startDate, $endDate]);

            $list = $query->get();

            return DataTables::of($list)
            ->editColumn('id', function($data) {
                return Crypt::encrypt($data->id);

            })

            ->editColumn('code', function($data) {
                return $data->code;
            })

            ->editColumn('file', function($data) {
                return $data->original_filename;
            })

            ->editColumn('content', function($data) {
                return $data->file;
            })
        
            ->editColumn('upload_category_id', function($data) {
                return $data->upload_category->category_name;
            })

            ->editColumn('category_id', function($data) {
                return $data->upload_category_id;
            })

            ->editColumn('created_at', function($data) {
                return $data->created_at->format('m/d/Y');
            })

            ->editColumn('updated_at', function($data) {
                return $data->updated_at->format('m/d/Y');
            })

            ->editColumn('created_by', function($data) {
                return $data->created_by;
            })

            ->editColumn('updated_by', function($data) {
                return $data->updated_by;
            })

            ->addColumn('month', function($data) {
                return $data->created_at->format('F');
            })
        
            ->make(true);
        

        }catch(\Exception $e){
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }


    public function download($id)
    {
        try {
            // Decrypt the ID
            $id = Crypt::decrypt($id);
    
            // Find the upload by ID
            $upload = Upload::findOrFail($id);
    
            // Decode the Base64 file content
            $fileContents = base64_decode($upload->file);
    
            // Determine the file's MIME type
            $mimeType = finfo_buffer(finfo_open(), $fileContents, FILEINFO_MIME_TYPE);
    
            // Use the original filename
            $originalFilename = $upload->original_filename;
    
            // Create a response with the file content
            return response($fileContents)
                ->header('Content-Type', $mimeType)
                ->header('Content-Disposition', 'attachment; filename="' . $originalFilename . '"');
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request){

        try{

            $validator = Validator::make($request->all(), [
                'upload_category_id' => 'required|exists:category_upload,id',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $base64File = null;
            $file = null;

            if ($request->hasFile('file')) {
                $validator = Validator::make($request->all(), [
                    'file' => 'required|file|mimes:pdf,xlsx,xls|max:102400',
                ]);

                if ($validator->fails()) {
                    return response()->json(['errors' => $validator->errors()], 422);
                }

                 // Handle the file
                $file = $request->file('file');
                $fileContents = file_get_contents($file->getRealPath()); // Get the file contents
                $base64File = base64_encode($fileContents); // Convert to Base64

                // Validate the Base64 data
                if (in_array($file->getMimeType(), ['application/pdf', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' ,'application/vnd.ms-excel'])) {
                    if ($file->getMimeType() === 'application/pdf' && substr($fileContents, 0, 4) !== '%PDF') {
                        return response()->json(['errors' => ['file' => 'Invalid PDF file']], 422);
                    }
                    // Add additional checks for Excel and CSV if needed
                } else {
                    return response()->json(['errors' => ['file' => 'Invalid file type']], 422);
                }


            }
       
            // Save the file to the database
            $upload = Upload::findOrFail(decrypt($request->id));
            $upload->upload_category_id = $request->upload_category_id;
            $upload->file = $base64File ? $base64File : $upload->file;
            $upload->updated_by = Auth::user()->user_name;
            $upload->original_filename = $file ? $file->getClientOriginalName() : $upload->original_filename;
            $upload->updated_at = now();
            $upload->update();

            $upload_logs = new UploadLogs();
            $upload_logs->upload_id = decrypt($request->id);
            $upload_logs->user = Auth::user()->first_name . ' ' . Auth::user()->last_name;
            $upload_logs->activity = 'Updated the uploaded file';
            $upload_logs->save();

            return response()->json(['success'=>'true',  'message' => 'Updated successfully'], 200);


        }catch(\Exception $e){
            return response()->json(['error' => $e->getMessage()], 500);
        }
        
    }

    public function destroy(Request $request){
        try{
            $upload = Upload::findOrFail(decrypt($request->id));
            $upload->updated_by = Auth::user()->user_name;
            $upload->deleted_at = now();
            $upload->update();

            $upload_logs = new UploadLogs();
            $upload_logs->upload_id = decrypt($request->id);
            $upload_logs->user = Auth::user()->first_name . ' ' . Auth::user()->last_name;
            $upload_logs->activity = 'Deleted the uploaded file';
            $upload_logs->save();

            return response()->json(['success'=>'true',  'message' => 'Deleted successfully'], 200);

        }catch(\Exception $e){
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }


    public function getUpload(Request $request){
        try{

            $query = UploadLogs::with('upload')->whereNotNull('upload_id');
        
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

            ->addColumn('code', function($data) {
                return $data->upload->code;
            })

            ->addColumn('file_name', function($data) {
                return $data->upload->original_filename;
            })

            ->editColumn('created_at', function($data) {
                return $data->created_at->format('m/d/Y');
            })

            ->editColumn('updated_at', function($data) {
                return $data->updated_at->format('m/d/Y');
            })

            ->make(true);

        }catch(\Exception $e){
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function uploadLogs(){
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
       
        return view('upload.upload-logs', compact('user', 'userCount', 'roleCount', 'entriesCount', 'loggedInUsersCount', 'targetMonth', 'CompleteEntriesCount'));
    }
    
    public function getCategories()
    {
        $categories = CategoryUpload::whereNull('deleted_at')->get();
        return response()->json($categories);
    }
}
