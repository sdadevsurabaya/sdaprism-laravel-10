@include('layouts.header')

<div class="main-wrapper">
    <div class="page-wrapper full-page">
        <div class="page-content container-xxl d-flex align-items-center justify-content-center">
            <div class="mx-0 row w-100 auth-page">
                <div class="mx-auto col-md-10 col-lg-8 col-xl-4">
                    <div class="card">
                        {{-- <div class="row"> --}}
                            {{-- <div class="col-md-4 pe-md-0">
                                <div class="auth-side-wrapper"></div>
                            </div> --}}
                            <div class="ps-md-0">
                                <div class="px-4 py-5 auth-form-wrapper">
                                    <a href="#" class="mb-2 text-center nobleui-logo d-block">PRISM <span>SDA</span></a>
                                    <h5 class="mb-4 text-center text-secondary fw-normal">Selamat Datang! Silahkan login
                                        terlebih dahulu.
                                    </h5>
                                    <form method="POST" action="{{ route('login.submit') }}">
                                        @csrf
                                        <div class="mb-3">
                                            <label for="userEmail" class="form-label">Email address</label>
                                            <input id="email" type="email"
                                                class="form-control @error('email') is-invalid @enderror" name="email"
                                                value="{{ old('email') }}" required autocomplete="email" autofocus>
                                            @error('email')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                        <div class="mb-3">
                                            <label for="userPassword" class="form-label">Password</label>
                                            <input id="password" type="password"
                                                class="form-control @error('password') is-invalid @enderror"
                                                name="password" required autocomplete="current-password">
                                            @error('password')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                        <div>
                                            <button class="mb-2 text-white btn btn-primary me-2 mb-md-0"
                                                type="submit">Login</a>
                                                <button type="submit"
                                                    class="mb-2 btn d-none btn-outline-light btn-icon-text mb-md-0">
                                                    <svg class='btn-icon-prepend' fill='currentColor'
                                                        viewBox="-3 0 262 262" xmlns="http://www.w3.org/2000/svg"
                                                        preserveAspectRatio="xMidYMid">
                                                        <g id="SVGRepo_bgCarrier" strokeWidth="0"></g>
                                                        <g id="SVGRepo_tracerCarrier" strokeLinecap="round"
                                                            strokeLinejoin="round"></g>
                                                        <g id="SVGRepo_iconCarrier">
                                                            <path
                                                                d="M255.878 133.451c0-10.734-.871-18.567-2.756-26.69H130.55v48.448h71.947c-1.45 12.04-9.283 30.172-26.69 42.356l-.244 1.622 38.755 30.023 2.685.268c24.659-22.774 38.875-56.282 38.875-96.027"
                                                                fill="#4285F4"></path>
                                                            <path
                                                                d="M130.55 261.1c35.248 0 64.839-11.605 86.453-31.622l-41.196-31.913c-11.024 7.688-25.82 13.055-45.257 13.055-34.523 0-63.824-22.773-74.269-54.25l-1.531.13-40.298 31.187-.527 1.465C35.393 231.798 79.49 261.1 130.55 261.1"
                                                                fill="#34A853"></path>
                                                            <path
                                                                d="M56.281 156.37c-2.756-8.123-4.351-16.827-4.351-25.82 0-8.994 1.595-17.697 4.206-25.82l-.073-1.73L15.26 71.312l-1.335.635C5.077 89.644 0 109.517 0 130.55s5.077 40.905 13.925 58.602l42.356-32.782"
                                                                fill="#FBBC05"></path>
                                                            <path
                                                                d="M130.55 50.479c24.514 0 41.05 10.589 50.479 19.438l36.844-35.974C195.245 12.91 165.798 0 130.55 0 79.49 0 35.393 29.301 13.925 71.947l42.211 32.783c10.59-31.477 39.891-54.251 74.414-54.251"
                                                                fill="#EB4335"></path>
                                                        </g>
                                                    </svg>
                                                    Continue with Google
                                                </button>
                                        </div>
                                        <p class="mt-3 d-none text-secondary">Don't have an account? <a
                                                href="register.html">Sign up</a></p>
                                    </form>
                                </div>
                            </div>
                        {{-- </div> --}}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@include('layouts.footer')
@stack('scripts')
</body>

</html>
