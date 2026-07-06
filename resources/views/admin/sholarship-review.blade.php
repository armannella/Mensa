@extends('layout.master')

@section('header' , " {$scholarshipApplication->student->name } ")

@section('content')
    <div class="row d-flex justify-content-between  mt-4">
        <div class="col-md-3 ">
            @php
                $status = $scholarshipApplication->status->value;            
                $panelConfig = match($status) {
                    'approved' => ['color' => 'bgc-green', 'icon' => 'bi-check-circle-fill', 'title' => 'Approved'],
                    'pending' => ['color' => 'bgc-yellow text-dark', 'icon' => 'bi-hourglass-split', 'title' => 'Under Review'],
                    'rejected' => ['color' => 'bgc-red', 'icon' => 'bi-x-circle-fill', 'title' => 'Rejected'],
            };
            @endphp

            <div class="tile {{ $panelConfig['color'] }} p-4 text-center text-white" style="height: auto; border: 2px solid rgba(255,255,255,0.2);">
                <i class="bi {{ $panelConfig['icon'] }} mb-3" style="font-size: 50px;"></i>
                <h3 class="fw-light mb-1">Scholarship Status</h3>
                <h2 class="fw-bold text-capitalize mb-4">{{ $panelConfig['title'] }}</h2>

                @if ($status == 'approved')
                    <hr class="border-light opacity-50">
                    <div class="text-start mt-3">
                        <h5 class="fw-light">Assigned Plan: <span class="fw-bold">{{ $scholarshipApplication->student->discountPlan->name }}</span></h5>
                        <h5 class="fw-light">Discount: <span class="fw-bold">{{ $scholarshipApplication->student->discountPlan->percentage }}%</span></h5>
                    </div>
                @elseif ($status == 'pending' || $status == 'rejected')
                    @if($scholarshipApplication->admin_note)
                        <div class="alert alert-light  border-light text-start mt-3 rounded-0 p-2">
                            <strong>Admin Note:</strong> <br> {{ $scholarshipApplication->admin_note }}
                        </div>
                    @endif
                @endif
            </div>


            
            <form action="{{ route('admin.scholarships.approve',$scholarshipApplication->id) }}" method="post" class="border border-success border-5 p-3 mt-3">
                    @csrf
                    <select class="form-select" name="discount_plan_id">
                        <option class="text-dark" selected value="">Nothing</option>
                        @foreach ($discountplans as $plan)
                            <option class="text-dark" value="{{ $plan->id }}">{{ $plan->name }} -- {{$plan->percentage}}%</option>
                        @endforeach
                    </select>
                    <x-button  color="bgc-green">Approve !</x-button>
            </form>

            <form action="{{ route('admin.scholarships.reject',$scholarshipApplication->id) }}" method="post" class="border border-danger border-5 p-3 mt-3">
                    @csrf
                    <x-input name="note" label="any note for Student" placeholder=""/>
                    <x-button  color="bgc-red">Reject !</x-button>
            </form>
            

                
        </div>





        <div class="col-md-8">
            
                <div class="mb-5">
                    <x-table :headers="['Type' ,'is required','date', 'Actions']">
                        @forelse ($uploaded_documents as $doc)
                            <tr>
                                <td class="text-start">
                                     <span class="fw-bold">{{ $doc->documentType->title }}</span><br>
                                    <small style="opacity: 0.7;">{{ $doc->documentType->description }}</small>
                                </td>
                                
                                <td>
                                    @if ($doc->documentType->is_required)
                                        Mandatory
                                    @else
                                        Optional
                                    @endif
                                </td>
                                
                                <td>{{$doc->created_at}}</td>
                                
                                
                                <td>
                                    <div class="d-flex justify-content-center column-gap-2">
                                          <x-actionbtn type="button" color="bgc-yellow"><a href="{{route('admin.scholarships.document',$doc->id) }}" target="_blank" rel="noopener noreferrer">View</a> </x-actionbtn>
                                    <div>
                                </td>
                            </tr>
                        @empty
                            <td colspan="4" class="text-center py-4 text-white">No Document available yet.</td>
                        @endforelse
                    </x-table>

                    @if ($scholarshipApplication->student_note)
                        <div class="border border-2 bgc-purple text-warning fw-bold p-4 mt-5">Student Note : <span class="fs-6 text-white">{{$scholarshipApplication->student_note}}</span></div>
                    @endif
                </div>
        </div>
    </div>



    
@endsection