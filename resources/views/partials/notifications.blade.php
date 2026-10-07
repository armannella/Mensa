<div class="col-md-4 d-flex justify-content-end dropdown">
    
    <div class="d-flex column-gap-3 align-items-center w-100 justify-content-end" 
         data-bs-toggle="dropdown" 
         aria-expanded="false" 
         style="cursor: pointer;">
        
        <div class="leftprofile d-flex flex-column justify-content-center">
                <h4 class="profile-title">{{ Auth::user()->name ?? "Mensa Simulation Project"}}</h4>
                <h4 class="profile-desc">{{Auth::user()->role->value ?? "made By Arman Khademi"}}</h4>
        </div>
        <div class="rightprofile position-relative d-flex flex-column justify-content-center">
                <img src="{{ asset("/storage/". (Auth::user()->image_path ?? 'profile-avatars/siteprofile.jpg')) }}" alt="Logo" width="60px" height="60px" style="object-fit: cover;" >      
            
            @if(Auth::check() && Auth::user()->unreadNotifications->count() > 0)
                <span class="position-absolute top-0 start-100 translate-middle badge bg-danger rounded-0 border border-dark" style="font-size: 11px;">
                    {{ Auth::user()->unreadNotifications->count() }}
                </span>
            @endif
        </div>
    </div>

    
    
    <div class="dropdown-menu dropdown-menu-end rounded-0 shadow-lg p-0 border-0" style="width: 400px; background-color: #2b2b2b;">
        
        
        <div class="p-3 d-flex justify-content-between align-items-center text-white" style="background-color: #6f42c1; border-bottom: 3px solid #563399;">
            <h6 class="mb-0 fw-bold text-uppercase" style="letter-spacing: 1px;">
                <i class="bi bi-bell-fill me-2"></i>Notifications
            </h6>
            @if(Auth::check() && Auth::user()->unreadNotifications->count() > 0)
                <span class="badge bg-white text-dark rounded-0 px-2 py-1 shadow-sm">{{ Auth::user()->unreadNotifications->count() }} New</span>
            @endif
        </div>
        
        
        <div class="notification-list" style="max-height: 380px; overflow-y: auto;">
            @if(Auth::check() && Auth::user()->notifications->count() > 0)
                @foreach(Auth::user()->notifications()->take(6)->get() as $notification)
                    
                    @if(!$notification->read_at)
                        <form action="{{ route('notifications.markRead', $notification->id) }}" method="POST" class="m-0 p-0">
                            @csrf
                            <button type="submit" class="dropdown-item p-3 border-bottom border-secondary bg-dark w-100 text-start" 
                                 style="white-space: normal; transition: background-color 0.2s;">
                                
                                <div class="d-flex align-items-start gap-3">
                                    <div class="d-flex justify-content-center align-items-center rounded-0 flex-shrink-0 mt-1"
                                         style="width: 42px; height: 42px; background-color: {{ ($notification->data['type'] ?? 'info') === 'success' ? 'rgba(25, 135, 84, 0.15)' : 'rgba(220, 53, 69, 0.15)' }}; border: 1px solid {{ ($notification->data['type'] ?? 'info') === 'success' ? '#198754' : '#dc3545' }};">
                                        <i class="{{ $notification->data['icon'] ?? 'bi bi-info-circle' }} text-{{ $notification->data['type'] ?? 'info' }} fs-5"></i>
                                    </div>
                                    
                                    <div class="flex-grow-1">
                                        <div class="d-flex justify-content-between align-items-start mb-1">
                                            <h6 class="mb-0 text-white fw-bold" style="font-size: 14.5px;">{{ $notification->data['title'] }}</h6>
                                            <small class="text-white-50 ms-2 text-end flex-shrink-0" style="font-size: 11px; margin-top: 2px;">
                                                {{ $notification->created_at->diffForHumans(null, true, true) }}
                                            </small>
                                        </div>
                                        <p class="mb-0 text-light" style="font-size: 13px; line-height: 1.5; opacity: 0.9;">
                                            {!! str_replace(['{', '}'], ['<b class="text-info">', '</b>'], $notification->data['message']) !!}
                                        </p>
                                    </div>
                                </div>
                            </button>
                        </form>
                    @else
                        <div class="dropdown-item p-3 border-bottom border-secondary bg-transparent" 
                             style="white-space: normal; cursor: default;">
                            
                            <div class="d-flex align-items-start gap-3" style="opacity: 0.6;">
                                <div class="d-flex justify-content-center align-items-center rounded-0 flex-shrink-0 mt-1"
                                     style="width: 42px; height: 42px; background-color: {{ ($notification->data['type'] ?? 'info') === 'success' ? 'rgba(25, 135, 84, 0.15)' : 'rgba(220, 53, 69, 0.15)' }}; border: 1px solid {{ ($notification->data['type'] ?? 'info') === 'success' ? '#198754' : '#dc3545' }};">
                                    <i class="{{ $notification->data['icon'] ?? 'bi bi-info-circle' }} text-{{ $notification->data['type'] ?? 'info' }} fs-5"></i>
                                </div>
                                
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between align-items-start mb-1">
                                        <h6 class="mb-0 text-white fw-bold" style="font-size: 14.5px;">{{ $notification->data['title'] }}</h6>
                                        <small class="text-white-50 ms-2 text-end flex-shrink-0" style="font-size: 11px; margin-top: 2px;">
                                            {{ $notification->created_at->diffForHumans(null, true, true) }}
                                        </small>
                                    </div>
                                    <p class="mb-0 text-light" style="font-size: 13px; line-height: 1.5;">
                                        {!! str_replace(['{', '}'], ['<b class="text-info">', '</b>'], $notification->data['message']) !!}
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endif

                @endforeach
            @else
                
                <div class="p-5 text-center" style="background-color: #252525;">
                    <div class="rounded-circle d-inline-flex justify-content-center align-items-center mb-3" style="width: 70px; height: 70px; background-color: #333;">
                        <i class="bi bi-bell-slash fs-2 text-secondary"></i>
                    </div>
                    <h6 class="text-white fw-bold">All Caught Up!</h6>
                    <p class="text-white-50 mb-0" style="font-size: 13px;">You have no new notifications.</p>
                </div>
            @endif
        </div>
        
        
        @if(Auth::check() && Auth::user()->unreadNotifications->count() > 0)
            <div class="p-0 text-center" style="background-color: #1e1e1e; border-top: 1px solid #444;">
                <form action="{{ route('notifications.markAllRead') }}" method="POST" class="m-0 p-0">
                    @csrf
                    <button type="submit" class="btn btn-link text-info text-decoration-none fw-bold text-uppercase w-100 p-3 rounded-0" style="letter-spacing: 1px; font-size: 13px; transition: 0.2s;">
                        <i class="bi bi-check-all me-1"></i> Mark All As Read
                    </button>
                </form>
            </div>
        @endif
    </div>