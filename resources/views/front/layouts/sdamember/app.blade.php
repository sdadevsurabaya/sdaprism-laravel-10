@include('front.layouts.sdamember.header')

<body class="text-center justify-content-evenly">

    @yield('content')

    @include('front.layouts.sdamember.footer')

    @stack('scripts')

</body>

</html>
