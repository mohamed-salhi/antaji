@extends('admin.auth.register')
@section('register')
    {{--@if (session('status'))--}}
    {{--    <div class="mb-4 font-medium text-sm text-green-600">--}}
    {{--        {{ session('status') }}--}}
    {{--    </div>--}}
    {{--@endif--}}
    {{--        <form method="POST" action="{{ route('password.email') }}">--}}
    {{--            @csrf--}}

    {{--            <!-- Email Address -->--}}
    {{--            <div>--}}
    {{--                <input type="email" name="email" :value="old('email')" required  class="form-control" id="useremail" placeholder="Enter email">--}}


    {{--            </div>--}}
    {{--            <button class="btn btn-primary w-md waves-effect waves-light" type="submit">Log In</button>--}}


    {{--            <div class="flex items-center justify-end mt-4">--}}

    {{--            </div>--}}
    {{--        </form>--}}


    <!-- Begin page -->
    <div class="wrapper-page">

        <div class="card">
            <div class="card-body">

                <div class="auth-logo">
                    <h3 class="text-center">

                    </h3>
                </div>

                <div class="p-3">
                    <h4 class="text-muted font-size-18 mb-3 text-center">Reset Password</h4>

                    @if ($errors->any())
                        @foreach ($errors->all() as $error)
                            <div class="alert alert-danger" role="alert">

                                {{ $error }}

                            </div>
                        @endforeach
                    @elseif(session('status'))
                        <div class="alert alert-success" role="alert">
                            تم الارسال بنجااح
                        </div>
                    @else
                        <div class="alert alert-info" role="alert">
                            Enter your Email and instructions will be sent to you!
                        </div>
                    @endif


                    <form method="POST" action="{{ route('password.update') }}">
                        @csrf
                        <input type="hidden" name="token" value="{{ $request->route('token') }}">
                        <div class="mb-3">
                            <label class="form-label" for="useremail">Email</label>
                            <input type="email" name="email" :value="old('email')" required class="form-control" id="useremail" placeholder="Enter email">
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="userpassword">Password</label>
                            <input type="password"  name="password"  required  class="form-control" id="userpassword" placeholder="Enter password">
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="userpassword">Password</label>
                            <input type="password"  name="password_confirmation"  required  class="form-control" id="userpassword" placeholder="Enter password">
                        </div>

                        <div class="mb-3 row">
                            <div class="col-12 text-end">
                                <button class="btn btn-primary w-md waves-effect waves-light" type="submit">Unlock</button>
                            </div>
                        </div>

                    </form>
                </div>

            </div>
        </div>

        <div class="text-center">
            <p class="text-muted">Remember It ? <a href="pages-login.html" class="text-white"> Sign In Here </a> </p>
            <p class="text-muted">©
                <script>document.write(new Date().getFullYear())</script> Agroxa. Crafted with <i
                    class="mdi mdi-heart text-primary"></i> by Themesbrand
            </p>
        </div>

    </div>


    <!-- Right Sidebar -->
    <div class="right-bar">
        <div data-simplebar class="h-100">
            <div class="rightbar-title px-3 py-4">
                <a href="javascript:void(0);" class="right-bar-toggle float-end">
                    <i class="mdi mdi-close noti-icon"></i>
                </a>
                <h5 class="m-0">Settings</h5>
            </div>

            <!-- Settings -->
            <hr class="" />
            <h6 class="text-center mb-0">Choose Layouts</h6>

            <div class="p-4">
                <div class="mb-2">
                    <img src="assets/images/layouts/layout-1.png" class="img-fluid img-thumbnail" alt="">
                </div>

                <div class="form-check form-switch mb-3">
                    <input type="checkbox" class="form-check-input theme-choice" id="light-mode-switch" checked />
                    <label class="form-check-label" for="light-mode-switch">Light Mode</label>
                </div>

                <div class="mb-2">
                    <img src="assets/images/layouts/layout-2.png" class="img-fluid img-thumbnail" alt="">
                </div>

                <div class="form-check form-switch mb-3">
                    <input type="checkbox" class="form-check-input theme-choice" id="dark-mode-switch"
                           data-bsStyle="assets/css/bootstrap-dark.min.css" data-appStyle="assets/css/app-dark.min.css" />
                    <label class="form-check-label" for="dark-mode-switch">Dark Mode</label>
                </div>

                <div class="mb-2">
                    <img src="assets/images/layouts/layout-3.png" class="img-fluid img-thumbnail" alt="">
                </div>
                <div class="form-check form-switch mb-5">
                    <input type="checkbox" class="form-check-input theme-choice" id="rtl-mode-switch"
                           data-appStyle="assets/css/app-rtl.min.css" />
                    <label class="form-check-label" for="rtl-mode-switch">RTL Mode</label>
                </div>

            </div>

        </div>
        <!-- end slimscroll-menu-->
    </div>

@stop
