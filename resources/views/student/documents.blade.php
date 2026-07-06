@extends('layout.master')

@section('header' , 'Scholarship')

@section('content')
    <div class="row d-flex justify-content-between  mt-4">
        <div class="col-md-3 ">
            @php
                $status = $scholarshipApplication?->status->value ?? 'not_submitted';
            
            
                $panelConfig = match($status) {
                    'approved' => ['color' => 'bgc-green', 'icon' => 'bi-check-circle-fill', 'title' => 'Approved'],
                    'pending' => ['color' => 'bgc-yellow text-dark', 'icon' => 'bi-hourglass-split', 'title' => 'Under Review'],
                    'rejected' => ['color' => 'bgc-red', 'icon' => 'bi-x-circle-fill', 'title' => 'Rejected'],
                    default => ['color' => 'bgc-dark', 'icon' => 'bi-info-circle-fill', 'title' => 'Not Submitted'],
            };
            @endphp

            <div class="tile {{ $panelConfig['color'] }} p-4 text-center text-white" style="height: auto; border: 2px solid rgba(255,255,255,0.2);">
                <i class="bi {{ $panelConfig['icon'] }} mb-3" style="font-size: 50px;"></i>
                <h3 class="fw-light mb-1">Scholarship Status</h3>
                <h2 class="fw-bold text-capitalize mb-4">{{ $panelConfig['title'] }}</h2>

                @if ($status == 'approved')
                    <hr class="border-light opacity-50">
                    <div class="text-start mt-3">
                        <h5 class="fw-light">Assigned Plan: <span class="fw-bold">{{ $discount_plan?->name }}</span></h5>
                        <h5 class="fw-light">Discount: <span class="fw-bold">{{ $discount_plan?->percentage }}%</span></h5>
                    </div>
                @elseif ($status == 'pending' || $status == 'rejected')
                    @if($scholarshipApplication->admin_note)
                        <div class="alert alert-light bg-transparent border-light text-start mt-3 rounded-0 p-2">
                            <strong>Admin Note:</strong> <br> {{ $scholarshipApplication->admin_note }}
                        </div>
                    @endif
                @endif
            </div>


            @if ($status != 'approved' && $status != 'pending')
                <form action="{{ route('student.documents.scholarship') }}" method="post" class="border border-success border-5 p-3 mt-3">
                            @csrf
                            <x-input name="note" label="any note for ERSU :" placeholder=""/>
                            <x-button  color="bgc-green">Send Scholarship Request !</x-button>
                </form>
            @endif

                
        </div>





        <div class="col-md-8">
            
                <div class="mb-5">
                    <x-table :headers="['Type' ,'is required','date', 'Actions']">
                        @forelse ($documentTypes as $type)
                            <tr>
                                <td class="text-start">
                                     <span class="fw-bold">{{ $type->title }}</span><br>
                                    <small style="opacity: 0.7;">{{ $type->description }}</small>
                                </td>
                                
                                <td>
                                    @if ($type->is_required)
                                        Mandatory
                                    @else
                                        Optional
                                    @endif
                                </td>
                                @if ($uploaded_docs->has($type->id))
                                    <td>{{$uploaded_docs[$type->id]->created_at}}</td>
                                @else
                                    <td>Not uploaded Yet</td>
                                @endif
                                
                                <td>
                                    <div class="d-flex justify-content-center column-gap-2">
                                        @if (!$uploaded_docs->has($type->id))
                                            <form action="{{route('student.documents.store') }}" method="post" enctype="multipart/form-data" class="d-flex justify-content-between column-gap-3">
                                                @csrf
                                                <input type="hidden" name="document_type_id" value="{{ $type->id }}">
                                                <input type="file" name="file" class="form-control form-control-sm rounded-0   d-inline " required>
                                                <x-actionbtn type="submit" color="bgc-green">Upload</x-actionbtn>
                                            </form>
                                        @else
                                          <x-actionbtn type="button" color="bgc-yellow"><a href="{{route('student.documents.show',$uploaded_docs[$type->id]->id) }}" target="_blank" rel="noopener noreferrer">View</a> </x-actionbtn>
                                            @if ($scholarshipApplication?->status->value != 'approved' &&  $scholarshipApplication?->status->value != 'pending')
                                                <form action="{{route('student.documents.delete',$uploaded_docs[$type->id]->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this doc?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <x-actionbtn type="submit" color="bgc-red">Delete</x-actionbtn>
                                                </form>
                                            @endif  
                                        @endif
                                        
              
                                    <div>
                                </td>
                            </tr>
                        @empty
                            <td colspan="4" class="text-center py-4 text-white">No Document Type available yet.</td>
                        @endforelse
                    </x-table>
                </div>
        </div>
    </div>



    
@endsection