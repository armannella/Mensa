<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ScholarshipStatus;
use App\Http\Controllers\Controller;
use App\Models\DiscountPlan;
use App\Models\Document;
use App\Models\ScholarshipApplication;
use App\Notifications\ScholarshipAssignNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ScholarshipController extends Controller
{
    public function index(Request $request){
        $applications = ScholarshipApplication::query()
        ->when($request->filled('search') , function ($query) use ($request) {
            $search = "%".$request->search ."%";
            $query->whereHas('student', function($q) use ($search ,$request){
                $q->where('name' , 'like' , $search)->orWhere('matricola' , 'like' , $search);
            });
        })
        ->when($request->filled('status'), function ($query) use ($request){
            $query->where("status" , $request->status);
        })
        ->with('student')->latest()->paginate(10)->withQueryString();

        $statuses = ScholarshipStatus::cases();
        return view("admin.scholarships" , compact('applications','statuses'));
    }

    public function show(ScholarshipApplication $scholarshipApplication){
        $discountplans = DiscountPlan::all();
        $uploaded_documents = $scholarshipApplication->student->documents;
        return view('admin.sholarship-review' , compact('discountplans' , 'uploaded_documents','scholarshipApplication'));
    }

    public function viewDoc(Document $document)
    {
        return Storage::disk('local')->response($document->file_path);
    }

    public function assignScholarship(Request $request , ScholarshipApplication $scholarshipApplication){
        $request->validate(['discount_plan_id' => ['required' , 'integer' , 'exists:discount_plans,id']]);
        $student = $scholarshipApplication->student;
        DB::transaction(function() use ($request , $scholarshipApplication , $student){
            $scholarshipApplication->update(['status' => ScholarshipStatus::APPROVED->value]);
            $student->update(['discount_plan_id'=>$request->discount_plan_id]);
        });
        $student->user->notify(new ScholarshipAssignNotification($scholarshipApplication , ScholarshipStatus::APPROVED));
        return back()->with(['success' => 'you approved successfully']);
    }

    public function rejectScholarship(Request $request , ScholarshipApplication $scholarshipApplication){
        $request->validate(['note' => ['nullable' , 'string' , 'max:250']]);
        $scholarshipApplication->update(['status' => ScholarshipStatus::REJECTED->value , 'admin_note'=> $request->note]);

        $scholarshipApplication->student->user->notify(new ScholarshipAssignNotification($scholarshipApplication , ScholarshipStatus::REJECTED));
        return back()->with(['success' => 'you rejected successfully']);
    }

}
