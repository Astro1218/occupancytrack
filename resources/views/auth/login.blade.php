<!DOCTYPE html>
<html lang="en">
<meta http-equiv="content-type" content="text/html;charset=UTF-8" />

<head>
    <meta charset="utf-8" />
    <title>Welcome to OT</title>
    <meta name="description" content="Login page example" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!--begin::Fonts-->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700" />
    <!--end::Fonts-->

    <!--begin::Page Custom Styles(used by this page)-->
    <link href="./assets/css/pages/login/classic/login-479e8.css?v=7.0.3" rel="stylesheet" type="text/css" />
    <!--end::Page Custom Styles-->

    <!--begin::Global Theme Styles(used by all pages)-->
    <link href="./assets/plugins/global/plugins.bundle79e8.css?v=7.0.3" rel="stylesheet" type="text/css" />
    <link href="./assets/plugins/custom/prismjs/prismjs.bundle79e8.css?v=7.0.3" rel="stylesheet" type="text/css" />
    <link href="./assets/css/style.bundle79e8.css?v=7.0.3" rel="stylesheet" type="text/css" />
    <!--end::Global Theme Styles-->

    <!--begin::Layout Themes(used by all pages)-->

    <!--end::Layout Themes-->

    <link href="/assets/css/myStyle.css" rel="stylesheet" type="text/css" />
    <link href="/assets/css/newcss.css" rel="stylesheet" type="text/css" />
    <link href="/assets/css/front-end.css" rel="stylesheet" type="text/css" />
</head>
<!--end::Head-->
<!--begin::Body-->

<body id="kt_body" style="background: white;"
    class="quick-panel-right demo-panel-right offcanvas-right header-fixed subheader-enabled page-loading">
    <!--begin::Main-->
    <div class="back-to-top" style="display: none;">
        <span class="back-top"><i class="fa fa-angle-up"></i></span>
    </div>
    <div class="elementor-background-overlay"></div>
    <div class="preloader-wrapper" id="preloader" style="display: none;">
        <div class="preloader">
            <div class="sk-circle">
                <div class="sk-circle1 sk-child"></div>
                <div class="sk-circle2 sk-child"></div>
                <div class="sk-circle3 sk-child"></div>
                <div class="sk-circle4 sk-child"></div>
                <div class="sk-circle5 sk-child"></div>
                <div class="sk-circle6 sk-child"></div>
                <div class="sk-circle7 sk-child"></div>
                <div class="sk-circle8 sk-child"></div>
                <div class="sk-circle9 sk-child"></div>
                <div class="sk-circle10 sk-child"></div>
                <div class="sk-circle11 sk-child"></div>
                <div class="sk-circle12 sk-child"></div>
            </div>
        </div>
    </div>
    <div class="d-flex flex-column flex-root">
        <!--begin::Login-->
        <div class="login login-4 login-signin-on d-flex flex-row-fluid" id="kt_login">
            <div class="d-flex flex-center flex-row-fluid bgi-size-cover bgi-position-top bgi-no-repeat bg-back-logo"
                style="background-image: url('./assets/logos/header-bg.png');">
                <div class="login-form text-center p-20 position-relative overflow-hidden">

                    <!--begin::Login Header-->
                    <div class="d-flex flex-center mb-2">
                        <a href="#">
                            <img src="./assets/logos/LOOGO-1024x322.png" class="max-h-100px" alt="" />
                        </a>
                    </div>
                    <h2 class="elementor-heading-title elementor-size-default mb-5">SIGN IN</h2>
                    <!--end::Login Header-->

                    <!--begin::Login Sign in form-->
                    <div class="login-signin">
                        <form class="form login-class" id="login_signin_form" name="login_signin_form"
                            action="{{ route('login') }}" method="POST">

                            @if(Session::has('error'))
                            <div class="alert alert-danger">
                            {{ Session::get('error')}}
                            </div>
                            @endif

                            @csrf
                            @error('email')
                            <div class="alert alert-danger">
                                <button type="button" class="close" data-dismiss="alert">&times;</button>
                                Email or Password Faild!
                            </div>
                            @enderror
                            @error('active')
                            <div class="alert alert-danger">
                                <button type="button" class="close" data-dismiss="alert">&times;</button>
                                This user is not allowed!
                            </div>
                            @enderror
                            <h4 class="logintitle">Your Email (required)</h4>
                            <div class="form-group mb-5">
                                <input class="form-control h-auto form-control-solid py-4 px-8" type="text"
                                    placeholder="Email" name="email" required />
                            </div>
                            <h4 class="logintitle">Your Password (required)</h4>
                            <div class="form-group mb-5">
                                <input class="form-control h-auto form-control-solid py-4 px-8" type="password"
                                    placeholder="Password" name="password" required />
                            </div>
                            {{-- <div class="form-group d-flex flex-wrap justify-content-between align-items-center">
								<label class="checkbox m-0 text-muted">
									<input type="checkbox" name="remember" />Remember me<span></span>
								</label>	<a href="javascript:;" id="kt_login_forgot" class="text-muted text-hover-primary">Forget Password ?</a>
							</div> --}}

                            <div class="btn-wrapper">
                                <button id="kt_login_signin_submit" class="boxed-btn btn-rounded">Sign In</button>
                            </div>
                        </form>
                        {{-- <div class="mt-10 login-class">
                            <span class="opacity-70 mr-4">
                                Don't have an account yet?
                            </span>
							<a href="javascript:;" id="kt_login_signup" class="text-muted text-hover-primary font-weight-bold">Sign Up!</a>
						</div> --}}
                    </div>
                    <!--end::Login Sign in form-->
                </div>
            </div>
        </div>
        <!--end::Login-->
    </div>
    <!--end::Main-->

    <script>
    var KTAppSettings = {
        "breakpoints": {
            "sm": 576,
            "md": 768,
            "lg": 992,
            "xl": 1200,
            "xxl": 1200
        },
        "colors": {
            "theme": {
                "base": {
                    "white": "#ffffff",
                    "primary": "#6993FF",
                    "secondary": "#E5EAEE",
                    "success": "#1BC5BD",
                    "info": "#8950FC",
                    "warning": "#FFA800",
                    "danger": "#F64E60",
                    "light": "#F3F6F9",
                    "dark": "#212121"
                },
                "light": {
                    "white": "#ffffff",
                    "primary": "#E1E9FF",
                    "secondary": "#ECF0F3",
                    "success": "#C9F7F5",
                    "info": "#EEE5FF",
                    "warning": "#FFF4DE",
                    "danger": "#FFE2E5",
                    "light": "#F3F6F9",
                    "dark": "#D6D6E0"
                },
                "inverse": {
                    "white": "#ffffff",
                    "primary": "#ffffff",
                    "secondary": "#212121",
                    "success": "#ffffff",
                    "info": "#ffffff",
                    "warning": "#ffffff",
                    "danger": "#ffffff",
                    "light": "#464E5F",
                    "dark": "#ffffff"
                }
            },
            "gray": {
                "gray-100": "#F3F6F9",
                "gray-200": "#ECF0F3",
                "gray-300": "#E5EAEE",
                "gray-400": "#D6D6E0",
                "gray-500": "#B5B5C3",
                "gray-600": "#80808F",
                "gray-700": "#464E5F",
                "gray-800": "#1B283F",
                "gray-900": "#212121"
            }
        },
        "font-family": "Poppins"
    };
    </script>

    <!--begin::Global Theme Bundle(used by all pages)-->
    <script src="./assets/js/jquery.min.js"></script>
    <script src="./assets/plugins/global/plugins.bundle79e8.js?v=7.0.3"></script>
    <script src="./assets/plugins/custom/prismjs/prismjs.bundle79e8.js?v=7.0.3"></script>
    <script src="./assets/js/scripts.bundle79e8.js?v=7.0.3"></script>
    <!--end::Global Theme Bundle-->

    <!--begin::Page Scripts(used by this page)-->
    <script src="./assets/js/pages/custom/login/login-general79e8.js?v=7.0.3"></script>
    <script src="./assets/js/myEvent.js"></script>
    <!--end::Page Scripts-->
</body>
<!--end::Body-->

</html>
