@extends('layout.master')

@section('header', 'My Profile')

@section('content')
<div class="row justify-content-center mt-4 mb-5">
    <div class="col-md-10">
        @error('error')
            <div class="alert alert-danger border-0 rounded-0 mb-4">{{ $message }}</div>
        @enderror

        <div class="row">
            
            <div class="col-md-4 mb-4">
                <div class="card bg-dark border-secondary shadow-sm text-center">
                    <div class="card-header bgc-purple text-white">
                        <h6 class="m-0">Profile Picture</h6>
                    </div>
                    <div class="card-body">
            
                        <div class="mb-4">
                            @if($user->image_path)
                                <img src="{{ asset('storage/' . $user->image_path) }}" alt="Avatar" class="rounded-circle border border-3 border-secondary" style="width: 150px; height: 150px; object-fit: cover;">
                            @else
                                <div class="rounded-circle border border-3 border-secondary d-flex align-items-center justify-content-center mx-auto bg-secondary text-white" style="width: 150px; height: 150px; font-size: 3rem;">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                            @endif
                        </div>

                        <form action="{{ route('student.profile.avatar.update') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="mb-3 text-start">
                                <label for="avatar" class="form-label text-white-50 small">Upload New Picture</label>
                                <input class="form-control form-control-sm bg-dark text-white border-secondary" type="file" id="avatar" name="avatar" required>
                                @error('avatar')
                                    <small class="text-danger mt-1 d-block">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="d-grid">
                                <x-button color="bg-info text-dark">Update Picture</x-button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            
            <div class="col-md-8">
                <div class="card bg-dark border-secondary shadow-sm">
                    <div class="card-header bg-secondary text-white">
                        <h6 class="m-0">Security Settings</h6>
                    </div>
                    <div class="card-body p-4">
                        <form action="{{ route('student.profile.password.update') }}" method="POST">
                            @csrf
                            
                            <div class="mb-4">
                                <label class="form-label text-white">Current Password</label>
                                <input type="password" name="current_password" class="form-control bg-dark text-white border-secondary" required>
                                @error('current_password')
                                    <small class="text-danger mt-1 d-block">{{ $message }}</small>
                                @enderror
                            </div>

                            <hr class="border-secondary">

                            <div class="mb-3">
                                <label class="form-label text-white">New Password</label>
                                <input type="password" name="password" class="form-control bg-dark text-white border-secondary" required>
                                @error('password')
                                    <small class="text-danger mt-1 d-block">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label class="form-label text-white">Confirm New Password</label>
                                <input type="password" name="password_confirmation" class="form-control bg-dark text-white border-secondary" required>
                            </div>

                            <div class="d-flex justify-content-end">
                                <x-button color="bgc-green" class="px-5">Change Password</x-button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection