@extends('layout.master')

@section('header' , 'Scholarship Apps')

@section('content')
            
    <div class="row d-flex justify-content-center  mt-4">
        <div class="col-md-8">
            <div class="row">
                <form action="" method="get" class="d-flex justify-content-between column-gap-5">
                    @csrf
                    <input type="search" name="search" id="search" placeholder="Search matricola/name" class="form-control">
                    <select class="form-select" name="status">
                        <option class="text-dark" selected value="">All</option>
                        @foreach ($statuses as $status)
                            <option class="text-dark" value="{{ $status->value }}">{{ $status->value }}</option>
                        @endforeach
                    </select>
                    <x-actionbtn type="submit" color="bgc-cyan">Search</x-actionbtn>

                </form>
            </div>
                <div class="row my-5">
                    <x-table :headers="['Student Name' ,'Matricola','Date', 'Status' ,'Actions']">
                        @forelse ($applications as $app)
                            <tr>
                                <td>{{$app->student->name}}</td>
                                <td>{{$app->student->matricola}}</td>
                                <td>{{$app->created_at}}</td>
                                @php
                                    $config = match($app->status->value) {
                                    'approved' => ['color' => 'bgc-green', 'title' => 'Approved'],
                                    'pending' => ['color' => 'bgc-yellow text-dark','title' => 'Pending'],
                                    'rejected' => ['color' => 'bgc-red', 'title' => 'Rejected'],
                                    };
                                @endphp
                                <td><span class="badge {{ $config['color'] }}">{{$config['title']}}</span></td>
                                <td>
                                    <x-actionbtn type="button" color="bgc-pink"> <a href="{{ route('admin.scholarships.show', $app->id )}}">View</a> </x-actionbtn>
                                </td>
                            </tr>
                        @empty
                            <td colspan="5" class="text-center py-4 text-white">No Request available yet.</td>
                        @endforelse
                    </x-table>
                </div>
        </div>
    </div>



    
@endsection