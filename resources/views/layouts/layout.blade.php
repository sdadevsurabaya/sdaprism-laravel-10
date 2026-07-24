<!DOCTYPE html>
<html lang="en">

<head>
    @include('layouts.header')
    @stack('css')
    <style>
        html, body {
            height: 100%;
            min-height: 100vh;
            margin: 0;
        }
        .main-wrapper {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .page-wrapper {
            flex: 1 0 auto;
            display: flex;
            flex-direction: column;
        }
        .page-content-wrapper {
            flex: 1 0 auto;
        }
    </style>
</head>

<body>

    <div class="main-wrapper">
        @include('layouts.menu')

        <div class="page-wrapper">
            <div class="container-fluid container-lg page-content-wrapper pb-4">
                @yield('content')
            </div>

            <footer class="footer border-top mt-auto bg-white">
                <div class="container d-flex flex-row align-items-center justify-content-between py-3 small">
                    <p class="text-secondary mb-1 mb-md-0">
                        Copyright © 2025
                        <a href="https://www.sda.co.id" target="_blank">SDA Global</a>.
                    </p>
                    <p class="text-secondary mb-0">
                        Handcrafted With
                        <i class="mb-1 text-primary ms-1 icon-sm" data-feather="heart"></i>
                    </p>
                </div>
            </footer>
        </div>
    </div>

    @include('layouts.footer')
    @stack('scripts')
    <script>
        @if (session('login'))
            toastr.success("{{ session('login') }}", "Success");
        @endif
    </script>
</body>

</html>
