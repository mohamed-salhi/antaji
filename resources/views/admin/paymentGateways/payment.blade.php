<!DOCTYPE html>
<html class="loading" lang="en" data-textdirection="rtl">
<!-- BEGIN: Head-->

<head>
    <link rel="stylesheet" type="text/css"
          href="{{ asset('dashboard/app-assets/vendors/css/extensions/toastr.min.css') }}">
    <link rel="stylesheet" type="text/css"
          href="{{ asset('dashboard/app-assets/css' . rtl_assets() . '/plugins/extensions/ext-component-toastr.min.css') }}">
    <!-- BEGIN: Theme JS-->
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta name="description"
          content="Vuexy admin is super flexible, powerful, clean &amp; modern responsive bootstrap 4 admin template with unlimited possibilities.">
    <meta name="keywords"
          content="admin template, Vuexy admin template, dashboard template, flat admin template, responsive admin template, web app">
    <meta name="author" content="PIXINVENT">
    <title>Aqar</title>
    <link rel="apple-touch-icon" href="{{ asset('dashboard/app-assets/images/ico/apple-icon-120.png') }}">
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('dashboard/app-assets/images/ico/favicon.ico') }}">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,300;0,400;0,500;0,600;1,400;1,500;1,600"
          rel="stylesheet">

    <!-- BEGIN: Vendor CSS-->
    <link rel="stylesheet" type="text/css"
          href="{{ asset('dashboard/app-assets/vendors/css/vendors' . rtl_assets() . '.min.css') }}">
    <!-- END: Vendor CSS-->

    <!-- BEGIN: Theme CSS-->
    <link rel="stylesheet" type="text/css" href="{{ asset('dashboard/app-assets/css' . rtl_assets() . '/bootstrap.css') }}">
    <link rel="stylesheet" type="text/css"
          href="{{ asset('dashboard/app-assets/css' . rtl_assets() . '/bootstrap-extended.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('dashboard/app-assets/css' . rtl_assets() . '/colors.css') }}">
    <link rel="stylesheet" type="text/css"
          href="{{ asset('dashboard/app-assets/css' . rtl_assets() . '/components.css') }}">
    <link rel="stylesheet" type="text/css"
          href="{{ asset('dashboard/app-assets/css' . rtl_assets() . '/themes/dark-layout.css') }}">
    <link rel="stylesheet" type="text/css"
          href="{{ asset('dashboard/app-assets/css' . rtl_assets() . '/themes/bordered-layout.css') }}">
    <link rel="stylesheet" type="text/css"
          href="{{ asset('dashboard/app-assets/css' . rtl_assets() . '/themes/semi-dark-layout.css') }}">

    <!-- BEGIN: Page CSS-->
    <link rel="stylesheet" type="text/css"
          href="{{ asset('dashboard/app-assets/css' . rtl_assets() . '/core/menu/menu-types/vertical-menu.css') }}">
    <link rel="stylesheet" type="text/css"
          href="{{ asset('dashboard/app-assets/css' . rtl_assets() . '/plugins/forms/form-validation.css') }}">
    <link rel="stylesheet" type="text/css"
          href="{{ asset('dashboard/app-assets/css' . rtl_assets() . '/pages/page-auth.css') }}">
    <!-- END: Page CSS-->

    <!-- BEGIN: Custom CSS-->

    <link rel="stylesheet" type="text/css" href="{{ asset('dashboard/assets/css/style' . rtl_assets() . '.css') }}">
    <!-- END: Custom CSS-->
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400&display=swap" rel="stylesheet">

    <style>
        * {
            font-family: 'Cairo', sans-serif;
        }
    </style>

</head>
<!-- END: Head-->

<!-- BEGIN: Body-->

<body class="vertical-layout vertical-menu-modern blank-page navbar-floating footer-static  " data-open="click"
      data-menu="vertical-menu-modern" data-col="blank-page">
<!-- BEGIN: Content-->

<div class="app-content content ">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>
    <div class="content-wrapper">
        <div class="content-header row">
        </div>
        <div class="content-body">
            <div class="auth-wrapper auth-v1 px-2">
                <div class="auth-inner py-2">
                    <!-- Login v1 -->
                    <div class="card mb-0">
                        <div class="card-body">
                            <a href="javascript:void(0);" class="brand-logo">
                                  <span class="brand-logo"><img alt="logo" src="{{ asset('dashboard/app-assets/images/logo/Antaji.png') }}"
                                                                style="width: 70px;" />
                        </span>
                                <h2 class="brand-text text-primary ml-1 mt-3">Antaji</h2>
                            </a>
                            {{--                                @if ($errors->has('email'))--}}
                            <div id="err">

                            </div>
                            {{--                                @endif--}}
                            <form action="{{route('paymentGateways.pay',$payment->uuid)}}" class="paymentWidgets" data-brands="VISA MASTER AMEX MADA"></form>



                        </div>
                    </div>
                    <!-- /Login v1 -->
                </div>
            </div>

        </div>
    </div>
</div>
<!-- END: Content-->
<script src="https://eu-test.oppwa.com/v1/paymentWidgets.js?checkoutId={{$payment->transaction_id}}"></script>


<!-- BEGIN: Vendor JS-->
<script src="{{ asset('dashboard/app-assets/vendors/js/vendors.min.js') }}"></script>
<!-- BEGIN Vendor JS-->

<!-- BEGIN: Page Vendor JS-->
<!-- END: Page Vendor JS-->
<script src="{{ asset('dashboard/app-assets/js/scripts/extensions/ext-component-toastr.min.js') }}"></script>

<script src="{{ asset('dashboard/app-assets/vendors/js/extensions/toastr.min.js') }}"></script>


<script src="{{ asset('dashboard/app-assets/js/core/app-menu.min.js') }}"></script>
<script src="{{ asset('dashboard/app-assets/js/core/app.min.js') }}"></script>
<script src="{{ asset('dashboard/app-assets/js/scripts/customizer.min.js') }}"></script>
<!-- END: Theme JS-->


<!-- BEGIN: Page JS-->
{{--    <script src="{{ asset('dashboard/app-assets/js/scripts/pages/page-auth-login.js') }}"></script>--}}
<!-- END: Page JS-->

<script>

    $(window).on('load', function() {
        if (feather) {
            feather.replace({
                width: 14,
                height: 14
            });
        }
    })
</script>
</body>
<!-- END: Body-->

</html>
