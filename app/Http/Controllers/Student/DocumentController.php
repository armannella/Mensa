<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\DocumentType;
use App\Models\ScholarshipApplication;
use App\Models\Student;
use App\Policies\DocumentPolicy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $student = Auth::user()->student;
        $documentTypes = DocumentType::latest()->get();
        $uploaded_docs = $student->documents?->keyBy('document_type_id');
        $scholarshipApplication = $student->scholarshipApplication;
        $discount_plan = $student->discountPlan;
        return view('student.documents' , compact('documentTypes' , 'uploaded_docs' , 'scholarshipApplication' , 'discount_plan'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create' , Document::class);
        $student = Auth::user()->student;
        $request->validate(['document_type_id' => ['required' , 'exists:document_types,id' ,'integer'],
                                    'file' => ['required' , 'file' , 'mimes:png,jpg,pdf' , 'max:5000']]);
        
        if($request->hasFile('file')){

            $checkfileexist = $student->documents()->where('document_type_id' , $request->document_type_id)->first();
            if($checkfileexist){
                Storage::disk('local')->delete($checkfileexist->file_path);
                $checkfileexist->delete();
            }

            $file = $request->file('file');
            $file_path = $file->store("documents/students/{$student->matricola}",'local');
            $student->documents()->create(['file_path' => $file_path , 'document_type_id' => $request->document_type_id]);
            return back()->with(['success' => 'you uploaded successfully the document']);
            
        }
        return back()->withErrors(['file' => 'there is problem in uploading the document try again']);
        
        
    }

    /**
     * Display the specified resource.
     */
    public function show(Document $document)
    {
        $this->authorize('view' , $document);
        return Storage::disk('local')->response($document->file_path);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Document $document)
    {
        $this->authorize('delete' , $document);
        $checkfileexist = Storage::disk('local')->exists($document->file_path);
        if($checkfileexist){
            Storage::disk('local')->delete($document->file_path);
            $document->delete();
            
        }

        return back()->with(['success' => 'you deleted successfully the document']);
    }

    public function scholarshipRequest(Request $request){
        $request->validate(['note' => ['string' , 'nullable','max:255']]);
        $this->authorize('scholarship' , Document::class);

        $student = Auth::user()->student ;

        $reqdocumentTypes = DocumentType::query()->where('is_required' , true)->pluck('id')->toArray();
        $uploadedDocTypes = $student->documents()->pluck('document_type_id')->toArray();
        
        $missingDocs = array_diff($reqdocumentTypes, $uploadedDocTypes);
        
        if (count($missingDocs) > 0) {
            return back()->withErrors(['note' => 'You must upload all REQUIRED documents before submitting your application.']);
        }


        $scholarshipApplication = $student->scholarshipApplication;
        if($scholarshipApplication){
            $scholarshipApplication->delete();
        }

        $student->scholarshipApplication()->create(['student_note' => $request->note]);
        return back()->with(['success' => 'your request successfully sent to Admins'] );

    }
}
