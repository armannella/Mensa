<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('pagetitle', 'Mensa')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="{{ URL::asset('css/metro.css') }}">
</head>
<body>
<body>

    <header class="container mt-4 mb-3">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h1 class="page-title m-0">@yield('header')</h1>
            </div>

            @include('partials.notifications')
        </div>

        <div class="row text-md-start text-start mt-3 mt-md-0">
            @if (session()->has('success'))
                    <div class="alert alert-success border-0 rounded-0 my-3" style="background-color: rgba(4, 160, 85, 0.9); color: white;">
                        <i class="bi bi-check-circle me-2"></i> {{ session('success') }}
                    </div>
                @endif
        </div>
        

    </header>

    <main class="container">
        <div class="row">
            <div class="col-lg-12">
                @yield('content')
            </div>
        </div>
    </main>

    <footer class="footer d-flex justify-content-between">
        <div>
            <span class="fs-5">Version 1.20</span>
        </div>
        <div class="d-flex justify-content-between column-gap-5">
            <div>
                <a href="javascript:history.back()" class="text-white">
                    <i class="bi bi-arrow-left fs-3"></i>
                </a>
            </div>
            
            <div>
                <a href="{{ route('welcome') }}" class="text-white">
                    <i class="bi bi-windows fs-3"></i>
                </a>
            </div>
            <div>
                @auth
                    <a href="{{ route('logout') }}" class="text-white">
                        <i class="bi bi-box-arrow-in-up-right fs-3"></i>
                    </a>    
                @endauth
                @guest
                    <a href="https://www.ersumessina.it/" target="blank" class="text-white">
                        <i class="bi bi-box-arrow-in-up-right fs-3"></i>
                    </a>
                @endguest
                
            </div>
            
        </div>
        <div>
            <span class="fs-5">Made by Arman</span>
        </div>
    </footer> 

</body>
</html>