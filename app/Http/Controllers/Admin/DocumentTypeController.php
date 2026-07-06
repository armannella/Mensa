<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DocumentType;
use Illuminate\Http\Request;

class DocumentTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $documentTypes = DocumentType::latest()->get();
        return view('admin.documenttypes',compact('documentTypes'));
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
        $data = $request->validate(['title' =>[ 'required' , 'string' , 'min:3'] , 
                                    'description' => ['string' ,'nullable', 'max:255']]);

        $data['is_required'] = $request->boolean('is_required');
        
        DocumentType::create($data);
        return back()->with(['success' => 'you added new Document Type']);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
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
    public function update(Request $request, DocumentType $documentType)
    {
        $data = $request->validate(['title' =>[ 'required' , 'string' , 'min:3'] , 'description' => ['string' ,'nullable', 'max:255']]);
        $data['is_required'] = $request->boolean('is_required');
        $documentType->update($data);
        return back()->with(['success' => 'you edited Document Type']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DocumentType $documentType)
    {
        $documentType->delete();
        return back()->with(['success' => 'you deleted Document Type successfully']);
    }
}
