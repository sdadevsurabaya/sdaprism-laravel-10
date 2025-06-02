<!DOCTYPE html>
<html lang="en">
@include('layouts.header')

<body>

    <div class="main-wrapper"> {{-- Tidak perlu flex-grow-1 di sini --}}
        @include('layouts.menu')

        <div class="page-wrapper"> {{-- Flex-grow aktif di sini --}}
            <div class="page-content">
                @yield('content')
            </div>

            <footer class="footer border-top"> {{-- mt-auto penting agar footer dorong ke bawah --}}
                <div class="container d-flex flex-row align-items-center justify-content-between py-3 small">
                    <p class="text-secondary mb-1 mb-md-0">
                        Copyright © 2025
                        <a href="https://www.sda.co.id" target="_blank">SDA Global</a>.
                    </p>
                    <p class="text-secondary">
                        Handcrafted With
                        <i class="mb-1 text-primary ms-1 icon-sm" data-feather="heart"></i>
                    </p>
                </div>
            </footer>
        </div>
    </div>

    @include('layouts.footer')
    @stack('scripts')
</body>

</html>
