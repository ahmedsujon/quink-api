<div>
    <div class="container-fluid p-0">
        <div class="row g-0">

            <div class="col-xl-9">
                <div class="auth-full-bg pt-lg-5 p-4">
                    <div class="w-100">
                        <div class="bg-overlay"></div>
                        <div class="d-flex h-100 flex-column">
                            <div class="p-4 mt-auto">
                                <div class="row justify-content-center">
                                    <div class="col-lg-7">
                                        <div class="text-center">
                                            <h4 class="mb-3">
                                                <i class="bx bxs-quote-alt-left text-primary h1 align-middle me-3"></i><span class="text-primary">Quink</span></h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- end col -->

            <div class="col-xl-3">
                <div class="auth-full-page-content p-md-5 p-4">
                    <div class="w-100">

                        <div class="d-flex flex-column h-100">
                            {{-- <div class="mb-4 mb-md-5">
                                <a href="#" class="d-block auth-logo">
                                    <img src="{{ asset('assets/images/logo.png') }}" alt="" height="50"
                                        class="auth-logo-dark">
                                    <img src="{{ asset('assets/images/logo.png') }}" alt="" height="50"
                                        class="auth-logo-light">
                                </a>
                            </div> --}}
                            <div class="my-auto">

                                <div>
                                    <h5 class="text-primary">Welcome Back !</h5>
                                    <p class="text-muted">Sign in to continue to Quink Admin.</p>
                                </div>
                                @if (session()->has('error'))
                                    <div class="alert alert-danger text-center">{{ session('error') }}</div>
                                @endif
                                @if (session()->has('success'))
                                    <div class="alert alert-success text-center">{{ session('success') }}</div>
                                @endif
                                <div class="mt-4">
                                    <form wire:submit.prevent='adminLogin'>
                                        <div class="mb-3">
                                            <label for="email" class="form-label">Email</label>
                                            <input type="text" class="form-control" id="email" wire:model='email'
                                                placeholder="Enter email">
                                            @error('email')
                                                <span class="text-danger"
                                                    style="font-size: 12px;">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Password</label>
                                            <div class="input-group auth-pass-inputgroup">
                                                <input type="password" class="form-control" wire:model='password'
                                                    placeholder="Enter password" aria-label="Password"
                                                    aria-describedby="password-addon">
                                                <button class="btn btn-light " type="button" id="password-addon"><i
                                                        class="mdi mdi-eye-outline"></i></button>
                                            </div>
                                            @error('password')
                                                <span class="text-danger"
                                                    style="font-size: 12px;">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="remember-check">
                                            <label class="form-check-label" for="remember-check">
                                                Remember me
                                            </label>
                                        </div>
                                        <div class="mt-3 d-grid">
                                            <button class="btn btn-primary waves-effect waves-light" type="submit">
                                                {!! loadingStateWithText('adminLogin', 'Log In') !!}
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            <div class="mt-4 mt-md-5 text-center">
                                <p class="mb-0">©
                                    <script>
                                        document.write(new Date().getFullYear())
                                    </script> <strong>Quink</strong>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
