<!DOCTYPE html>
<!DOCTYPE html>
<html class="loaded semi-dark-layout" lang="en" data-textdirection="rtl">
<!-- BEGIN: Head-->

<head>

    <style>
        .ex1 {
            width: 300px;
            height: 300px;
            overflow: scroll;
        }

        .buttons-excel {
            border: 1px solid #15a30b;
            border-radius: 10px;
            background-color: transparent;
            color: #15a30b;
            padding: 11px 19px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 15px;
        }

        .buttons-pdf {
            background-color: red; /* Green */
            border: 2px solid red;
            border-radius: 25px;
            color: white;
            padding: 15px 32px;
            text-align: center;

            text-decoration: none;
            display: inline-block;
            font-size: 16px;
        }

        .image-upload {
            position: relative;
            width: 100px;
            height: 100px;
            overflow: hidden;
            border-radius: 50%;
        }

        .image-upload-label {
            display: block;
            width: 100%;
            height: 100%;
            cursor: pointer;
        }

        .image-preview {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transform: scale(0.5);
            /* تحديد حجم النصف */
        }

        #detail_image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transform: scale(0.9);
            /* تحديد حجم النصف */
        }

        #video-preview {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transform: scale(0.5);
            /* تحديد حجم النصف */
        }

        .video-preview {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transform: scale(0.5);
            /* تحديد حجم النصف */
        }

        #detail-video-preview {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transform: scale(0.9);
            /* تحديد حجم النصف */
        }

        .multiselect {
            width: 200px;
        }

        .selectBox {
            position: relative;
        }

        .selectBox select {
            width: 100%;
            font-weight: bold;
        }

        .overSelect {
            position: absolute;
            left: 0;
            right: 0;
            top: 0;
            bottom: 0;
        }

        #checkboxes {
            display: none;
            border: 1px #dadada solid;
        }

        #checkboxes label {
            display: block;
        }

        #checkboxes label:hover {
            background-color: #1e90ff;
        }
    </style>
    @vite('resources/js/app.js')

    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <title>@yield('title')</title>
    <!-- Favicons -->
    <link href="{{asset('dashboard/app-assets/images/logo/Antaji.png')}}" rel="icon">
    <link href="{{asset('dashboard/app-assets/images/logo/Antaji.png')}}"
          rel="apple-touch-icon">{{--    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('dashboard/app-assets/images/ico/favicon.ico') }}">--}}
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,300;0,400;0,500;0,600;1,400;1,500;1,600"
          rel="stylesheet">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <!-- BEGIN: Vendor CSS-->
    <link type="text/css" rel="stylesheet" href="{{asset('dashboard/dist/image-uploader.min.css')}}">

    <link rel="stylesheet" type="text/css"
          href="{{ asset('dashboard/app-assets/vendors/css/vendors' . rtl_assets() . '.min.css') }}">
    <link rel="stylesheet" type="text/css"
          href="{{ asset('dashboard/app-assets/vendors/css/tables/datatable/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" type="text/css"
          href="{{ asset('dashboard/app-assets/vendors/css/tables/datatable/responsive.bootstrap4.min.css') }}">
    <link rel="stylesheet" type="text/css"
          href="{{ asset('dashboard/app-assets/vendors/css/tables/datatable/buttons.bootstrap4.min.css') }}">
    <link rel="stylesheet" type="text/css"
          href="{{ asset('dashboard/app-assets/vendors/css/tables/datatable/rowGroup.bootstrap4.min.css') }}">
    <link rel="stylesheet" type="text/css"
          href="{{ asset('dashboard/app-assets/vendors/css/pickers/flatpickr/flatpickr.min.css') }}">
    <link rel="stylesheet" type="text/css"
          href="{{ asset('dashboard/app-assets/vendors/css/extensions/sweetalert2.min.css') }}">
    <link rel="stylesheet" type="text/css"
          href="{{ asset('dashboard/app-assets/fonts/font-awesome/css/font-awesome.min.css') }}">
    <link rel="stylesheet" type="text/css"
          href="{{ asset('dashboard/app-assets/vendors/css/extensions/toastr.min.css') }}">
    <link rel="stylesheet" type="text/css"
          href="{{ asset('dashboard/app-assets/vendors/css/forms/select/select2.min.css') }}">
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/jasny-bootstrap/3.1.3/css/jasny-bootstrap.min.css">
    <!-- END: Vendor CSS-->

    <!-- BEGIN: Theme CSS-->
    <link rel="stylesheet" type="text/css"
          href="{{ asset('dashboard/app-assets/css' . rtl_assets() . '/bootstrap.min.css') }}">
    <link rel="stylesheet" type="text/css"
          href="{{ asset('dashboard/app-assets/css' . rtl_assets() . '/bootstrap-extended.min.css') }}">
    <link rel="stylesheet" type="text/css"
          href="{{ asset('dashboard/app-assets/css' . rtl_assets() . '/colors.min.css') }}">
    <link rel="stylesheet" type="text/css"
          href="{{ asset('dashboard/app-assets/css' . rtl_assets() . '/components.min.css') }}">
    <link rel="stylesheet" type="text/css"
          href="{{ asset('dashboard/app-assets/css' . rtl_assets() . '/themes/dark-layout.min.css') }}">
    <link rel="stylesheet" type="text/css"
          href="{{ asset('dashboard/app-assets/css' . rtl_assets() . '/themes/bordered-layout.min.css') }}">
    <link rel="stylesheet" type="text/css"
          href="{{ asset('dashboard/app-assets/css' . rtl_assets() . '/themes/semi-dark-layout.min.css') }}">

    <!-- BEGIN: Page CSS-->
    <link rel="stylesheet" type="text/css"
          href="{{ asset('dashboard/app-assets/css' . rtl_assets() . '/core/menu/menu-types/vertical-menu.min.css') }}">
    <link rel="stylesheet" type="text/css"
          href="{{ asset('dashboard/app-assets/css' . rtl_assets() . '/plugins/extensions/ext-component-toastr.min.css') }}">
    <!-- END: Page CSS-->
    @yield('styles')

    <!-- BEGIN: Custom CSS-->
    @if (LaravelLocalization::getCurrentLocaleDirection() == 'rtl')
        <link rel="stylesheet" type="text/css"
              href="{{ asset('dashboard/app-assets/css' . rtl_assets() . '/custom' . rtl_assets() . '.min.css') }}">
    @endif
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

<body class="vertical-layout vertical-menu-modern  navbar-floating footer-static  " data-open="click"
      data-menu="vertical-menu-modern" data-col="">
<!-- BEGIN: Header-->
<nav
    class="header-navbar navbar navbar-expand-lg align-items-center floating-nav navbar-light "
    style="background-color: #E3071C">
    <div class="navbar-container d-flex content">
        <ul class="nav navbar-nav align-items-center ml-auto">
            <x-not />
            <li class="nav-item dropdown dropdown-language">
                <a class="nav-link dropdown-toggle" id="dropdown-flag" href="javascript:void(0);"
                   data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <span class="selected-language"
                          style="color:#ffffff;">{{ LaravelLocalization::getCurrentLocaleNative() }}</span>
                </a>
                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdown-flag">
                    @foreach (LaravelLocalization::getSupportedLocales() as $localeCode => $properties)
                        <a class="dropdown-item"
                           href="{{ LaravelLocalization::getLocalizedURL($localeCode, null, [], true) }}"
                           data-language="{{ $localeCode }}">{{ $properties['native'] }}</a>
                    @endforeach
                </div>
            </li>
            <li class="nav-item dropdown dropdown-user">
                <a class="nav-link dropdown-toggle dropdown-user-link" id="dropdown-user"
                   href="javascript:void(0);" data-toggle="dropdown" aria-haspopup="true"
                   aria-expanded="false">
                    <div class="user-nav d-sm-flex d-none"><span class="font-weight-bolder " style="color:#ffffff;">Admin</span>
                    </div>
                </a>
                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdown-user">
                    <a class="dropdown-item" href="{{ url('/admin/profile') }}"><i class="mr-50"
                                                                                   data-feather="user"></i>
                        Profile</a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item"
                       onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="mr-50" data-feather="power"></i>Logout</a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST"
                          style="display: none;">
                        {{ csrf_field() }}
                    </form>
                </div>
            </li>

        </ul>
    </div>
</nav>
<!-- END: Header-->

@include('admin.sidebaradmin')
<!-- BEGIN: Main Menu-->
{{--@auth('web')--}}
{{--    @include('admin.sidebaradmin')--}}
{{--@endauth--}}
{{--    @auth('users')--}}
{{--        @include('users.sidebaruser')--}}
{{--    @endauth--}}
<!-- END: Main Menu-->

<!-- BEGIN: Content-->
<div class="app-content content ">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>
    @yield('content')
</div>
<!-- END: Content-->


<div class="sidenav-overlay"></div>
<div class="drag-target"></div>

<!-- BEGIN: Footer-->
<footer class="footer footer-static footer-light">
</footer>
<button class="btn btn-primary btn-icon scroll-top" type="button"><i data-feather="arrow-up"></i></button>
<!-- END: Footer-->

<script></script>

<!-- BEGIN: Vendor JS-->
<script src="{{ asset('dashboard/app-assets/vendors/js/vendors.min.js') }}"></script>
<!-- BEGIN Vendor JS-->

<!-- BEGIN: Page Vendor JS-->
<script src="{{ asset('dashboard/app-assets/vendors/js/tables/datatable/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('dashboard/app-assets/vendors/js/tables/datatable/datatables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('dashboard/app-assets/vendors/js/tables/datatable/dataTables.responsive.min.js') }}"></script>
<script src="{{ asset('dashboard/app-assets/vendors/js/tables/datatable/responsive.bootstrap4.js') }}"></script>
<script src="{{ asset('dashboard/app-assets/vendors/js/pickers/flatpickr/flatpickr.min.js') }}"></script>
<script src="{{ asset('dashboard/app-assets/vendors/js/extensions/sweetalert2.all.min.js') }}"></script>
<script src="{{ asset('dashboard/app-assets/vendors/js/extensions/toastr.min.js') }}"></script>
<script src="{{ asset('dashboard/app-assets/vendors/js/forms/select/select2.full.min.js') }}"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/jasny-bootstrap/3.1.3/js/jasny-bootstrap.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.6/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.print.min.js"></script>

<script type="text/javascript" src="{{ asset('dashboard/dist/image-uploader.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('dashboard/dist/image-uploader_2.min.js') }}"></script>

<script type="text/javascript" src="{{ asset('dashboard/src/image-uploader.js') }}"></script>
<script>


    function newexportaction(e, dt, button, config) {
        var self = this;
        var oldStart = dt.settings()[0]._iDisplayStart;
        dt.one('preXhr', function (e, s, data) {
            // Just this once, load all data from the server...
            data.start = 0;
            data.length = 2147483647;
            dt.one('preDraw', function (e, settings) {
                // Call the original action function
                if (button[0].className.indexOf('buttons-copy') >= 0) {
                    $.fn.dataTable.ext.buttons.copyHtml5.action.call(self, e, dt, button, config);
                } else if (button[0].className.indexOf('buttons-excel') >= 0) {
                    $.fn.dataTable.ext.buttons.excelHtml5.available(dt, config) ?
                        $.fn.dataTable.ext.buttons.excelHtml5.action.call(self, e, dt, button, config) :
                        $.fn.dataTable.ext.buttons.excelFlash.action.call(self, e, dt, button, config);
                } else if (button[0].className.indexOf('buttons-csv') >= 0) {
                    $.fn.dataTable.ext.buttons.csvHtml5.available(dt, config) ?
                        $.fn.dataTable.ext.buttons.csvHtml5.action.call(self, e, dt, button, config) :
                        $.fn.dataTable.ext.buttons.csvFlash.action.call(self, e, dt, button, config);
                } else if (button[0].className.indexOf('buttons-pdf') >= 0) {
                    $.fn.dataTable.ext.buttons.pdfHtml5.available(dt, config) ?
                        $.fn.dataTable.ext.buttons.pdfHtml5.action.call(self, e, dt, button, config) :
                        $.fn.dataTable.ext.buttons.pdfFlash.action.call(self, e, dt, button, config);
                } else if (button[0].className.indexOf('buttons-print') >= 0) {
                    $.fn.dataTable.ext.buttons.print.action(e, dt, button, config);
                }
                dt.one('preXhr', function (e, s, data) {
                    // DataTables thinks the first item displayed is index 0, but we're not drawing that.
                    // Set the property to what it was before exporting.
                    settings._iDisplayStart = oldStart;
                    data.start = oldStart;
                });
                // Reload the grid with the original page. Otherwise, API functions like table.cell(this) don't work properly.
                setTimeout(dt.ajax.reload, 0);
                // Prevent rendering of the full data to the DOM
                return false;
            });
        });
        // Requery the server with the new one-time export settings
        dt.ajax.reload();
    };







</script>
<!-- END: Page Vendor JS-->

<!-- BEGIN: Theme JS-->
<script src="{{ asset('dashboard/app-assets/js/core/app-menu.min.js') }}"></script>
<script src="{{ asset('dashboard/app-assets/js/core/app.min.js') }}"></script>
<script src="{{ asset('dashboard/app-assets/js/scripts/customizer.min.js') }}"></script>
<!-- END: Theme JS-->

<!-- BEGIN: Page JS-->
{{-- <script src="{{asset('dashboard/app-assets/js/scripts/tables/table-datatables-basic.min.js')}}"></script> --}}
<script src="{{ asset('dashboard/app-assets/js/scripts/extensions/ext-component-toastr.min.js') }}"></script>
<!-- END: Page JS-->


<script>
    $(window).on('load', function () {
        $('.phpdebugbar').remove()
        if (feather) {
            feather.replace({
                width: 14,
                height: 14
            });
        }
    })
</script>

<script>
    var isRtl = '{{ LaravelLocalization::getCurrentLocaleDirection() }}' === 'rtl';
    var selectedIds = function () {
        return $("input[name='table_ids[]']:checked").map(function () {
            return this.value;
        }).get();
    };
    $('select').select2({
        dir: '{{ LaravelLocalization::getCurrentLocaleDirection() }}',
        placeholder: "@lang('select')",
    });
</script>

@yield('scripts')
<script type="text/javascript">



    function CheckAll(className, elem) {
        var elements = document.getElementsByClassName(className);
        var l = elements.length;

        if (elem.checked) {
            for (var i = 0; i < l; i++) {
                elements[i].checked = true;
            }
        } else {
            for (var i = 0; i < l; i++) {
                elements[i].checked = false;
            }
        }
    }


</script>
<script>
    var pageNot = 2
    $("#seemore").on("click", function (e) {
        $.ajax({
            type: "get",
            cache: false,
            contentType: false,
            processData: false,
            url: $(this).data('url') + "/?page=" + pageNot,
            beforeSend: function () {
            },
            success: function (result) {
                pageNot++
                $('#not-seeall').append(result);
            },
            error: function (data) {
                console.log('err')
            }
        });
    })

</script>
<script>


    function checkClickFunc() {
        var elements = document.getElementsByClassName('box1');
        var l = elements.length;

        var selected = new Array();
        $(".box1:checked").each(function () {
            selected.push(this.value);
        });
        if (l != selected.length) {
            var box = document.getElementById('example-select-all').checked = false;
            console.log(l)
            console.log(selected.length)

        } else {

            var box = document.getElementById('example-select-all').checked = true;
        }


    }

    $('#search_btn').on('click', function (e) {
        table.draw();
        e.preventDefault();
    });

    $('#clear_btn').on('click', function (e) {
        e.preventDefault();
        $('.search_input').val("").trigger("change")
        table.draw();
    });
    $(document).ready(function () {
        //image
        $('.file-input').on('change', function () {
            var input = $(this)[0];
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function (e) {
                    $('.image-preview').attr('src', e.target.result);
                }
                reader.readAsDataURL(input.files[0]);
            }
        });
        $(document).ready(function () {
            $('#file-input').on('change', function () {
                var input = $(this)[0];
                if (input.files && input.files[0]) {
                    var reader = new FileReader();
                    reader.onload = function (e) {
                        $('#video-preview').attr('src', e.target.result);
                    }
                    reader.readAsDataURL(input.files[0]);
                }
            });
        });
    })

    $('.input-images').imageUploader({
        imagesInputName: 'images[]'
    });
    $('.add-mode-form').on('submit', function (event) {
        $('.search_input').val("").trigger("change")

        event.preventDefault();
        var data = new FormData(this);
        let url = $(this).attr('action');
        var method = $(this).attr('method');
        var form = $(this).find('form');
        form.find('select').val('').trigger("change")
        $('input').removeClass('is-invalid');
        $('select').removeClass('is-invalid');
        $('.invalid-feedback').text('');
        console.log(data)
        $.ajax({
            type: method,
            cache: false,
            contentType: false,
            processData: false,
            url: url,
            data: data,

            beforeSend: function () {
                $('.done').html('saving ...').prop('disabled', true);
            },
            success: function (result) {

                // form[0].reset();
                // $('#add_model_form').trigger("reset");

                toastr.success('@lang('done_successfully')', '', {
                    rtl: isRtl
                });

                $('.done').html('@lang("save")').prop('disabled', false);

                $('#full-modal-stem').modal('hide');
                table.draw()





                $('#model-excel').modal('hide');


            },
            error: function (data) {
                $('.done').html('@lang("save")').prop('disabled', false);

                if (data.status === 422) {
                    var response = data.responseJSON;
                    $.each(response.errors, function (key, value) {
                        toastr.error(value);
                        var str = (key.split("."));
                        if (str[1] === '0') {
                            key = str[0] + '[]';
                        }
                        $('[name="' + key + '"], [name="' + key + '[]"]').addClass(
                            'is-invalid');
                        $('[name="' + key + '"], [name="' + key + '[]"]').closest(
                            '.form-group').find('.invalid-feedback').html(value[0]);
                    });
                } else {
                    toastr.error('@lang('something_wrong')', '', {
                        rtl: isRtl
                    });
                }
            }
        });
    });


    $(document).on("click", ".btn_delete", function (e) {
        var button = $(this)
        e.preventDefault();


        var uuid = button.data('uuid')
        var deletes = '@lang('confirm_delete')'

        Swal.fire({
            title: '@lang('delete_confirmation')',
            text: deletes,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: '@lang('yes')',
            cancelButtonText: '@lang('cancel')',
            customClass: {
                confirmButton: 'btn btn-primary',
                cancelButton: 'btn btn-outline-danger'
            },
            buttonsStyling: true
        }).then(function (result) {
            if (result.value) {


                var url_path = window.location.href
                const url = url_path.split("?")[0] + '/' + uuid;

                $.ajax({
                    url: url,
                    method: 'DELETE',
                    type: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                }).done(function () {
                    table.draw()
                    toastr.success('@lang('deleted')', '', {
                        rtl: isRtl
                    });


                }).fail(function () {
                    toastr.error('@lang('something_wrong')', '', {
                        rtl: isRtl
                    });
                });
            } else if (result.dismiss === Swal.DismissReason.cancel) {
                toastr.info('@lang('delete_canceled')', '', {
                    rtl: isRtl
                })
            }
        });
    });
    $(document).on("click", ".btn_delete_all", function (e) {
        var button = $(this)
        e.preventDefault();
        var selected = new Array();
        $("#datatable input[type=checkbox]:checked").each(function () {
            selected.push(this.value);
        });
        if (selected.length > 0) {
            $('input[id="delete_all_id"]').val(selected);
            var uuid = selected;
            Swal.fire({
                title: '@lang('delete_confirmation')',
                text: '@lang('confirm_deletes')',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: '@lang('yes')',
                cancelButtonText: '@lang('cancel')',
                customClass: {
                    confirmButton: 'btn btn-primary',
                    cancelButton: 'btn btn-outline-danger'
                },
                buttonsStyling: true
            }).then(function (result) {
                if (result.value) {


                    var url_path = window.location.href
                    const url = url_path.split("?")[0] + '/' + uuid;
                    $.ajax({
                        url: url,
                        method: 'DELETE',
                        type: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                    }).done(function () {
                        toastr.success('@lang('deleted')', '', {
                            rtl: isRtl
                        });
                        table.draw()

                    }).fail(function () {
                        toastr.error('@lang('something_wrong')', '', {
                            rtl: isRtl
                        });
                    });
                } else if (result.dismiss === Swal.DismissReason.cancel) {
                    toastr.info('@lang('delete_canceled')', '', {
                        rtl: isRtl
                    })
                }
            });
        }

    });

    $('#form_edit').on('submit', function (event) {
        $('.search_input').val("").trigger("change")
        event.preventDefault();
        var data = new FormData(this);
        let url = $(this).attr('action');
        let method = $(this).attr('method');

        $.ajax({
            type: method,
            cache: false,
            contentType: false,
            processData: false,
            url: url,
            data: data,
            beforeSend: function () {
                $('input').removeClass('is-invalid');
                $('.text-danger').text('');
                $('.btn-file').addClass('');
                $('.done').html('saving ...').prop('disabled', true);

            },
            success: function (result) {
                table.draw()
                $('#edit_modal').modal('hide');
                $('.form_edit').trigger("reset");
                $('.done').html('@lang('save')').prop('disabled', false);
                toastr.success('@lang('done_successfully')', '', {
                    rtl: isRtl
                });

            },
            error: function (data) {
                $('.done').html('save').prop('disabled', false);

                if (data.status === 422) {

                    var response = data.responseJSON;
                    $.each(response.errors, function (key, value) {
                        var str = (key.split("."));
                        if (str[1] === '0') {
                            key = str[0] + '[]';
                        }
                        $('[name="' + key + '"], [name="' + key + '[]"]').addClass(
                            'is-invalid');
                        $('[name="' + key + '"], [name="' + key + '[]"]').closest(
                            '.form-group').find('.invalid-feedback').html(value[0]);
                    });
                } else {
                    toastr.error('@lang('something_wrong')', '', {
                        rtl: isRtl
                    });
                }
            }
        });
    })

    $(document).on('click', '.button_modal', function (event) {
        document.getElementById("add_model_form").reset();

        $('#add_model_form').trigger("reset");
        $('select').removeClass('is-invalid');
        // $('.image-preview').remove();
        // $('.image').append(`<img class="image-preview">`)
        $('.invalid-feedback').text('');
    });
    $('#edit_modal').on('hidden.bs.modal', function () {
        $('#form_edit').trigger("reset");

        $('.edit_images').remove()

    });


    $(document).on("click", "#btn_update", function (e) {
        var button = $(this)
        e.preventDefault();
        var url = button.data('url')
        var deletes = '@lang('confirm_update')'

        Swal.fire({
            title: '@lang('update_confirmation')',
            text: deletes,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: '@lang('yes')',
            cancelButtonText: '@lang('cancel')',
            customClass: {
                confirmButton: 'btn btn-primary',
                cancelButton: 'btn btn-outline-danger'
            },
            buttonsStyling: true
        }).then(function (result) {
            if (result.value) {
                $.ajax({
                    url: url,
                    method: 'put',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                }).done(function () {
                    toastr.success('@lang('done_successfully')', '', {
                        rtl: isRtl
                    });
                    table.draw()

                }).fail(function () {
                    toastr.error('@lang('something_wrong')', '', {
                        rtl: isRtl
                    });
                });
            } else if (result.dismiss === Swal.DismissReason.cancel) {
                toastr.info('@lang('delete_canceled')', '', {
                    rtl: isRtl
                })
            }
        });
    });
    $(document).on("click", ".btn_status", function (e) {
        var button = $(this)
        var status = $(this).data('status')
        e.preventDefault();
        var selected = new Array();
        $("#datatable input[type=checkbox]:checked").each(function () {
            selected.push(this.value);
        });
        if (selected.length > 0) {
            $('input[id="delete_all_id"]').val(selected);
            var uuid = selected;
            Swal.fire({
                title: '@lang('update_confirmation')',
                text: '@lang('confirm_update')',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: '@lang('yes')',
                cancelButtonText: '@lang('cancel')',
                customClass: {
                    confirmButton: 'btn btn-primary',
                    cancelButton: 'btn btn-outline-info'
                },
                buttonsStyling: true
            }).then(function (result) {
                if (result.value) {
                    var url_path = window.location.href + '/updateStatus/' + status
                    const url = url_path.split("?")[0] + '/' + uuid;
                    $.ajax({
                        url: url,
                        method: 'put',
                        type: 'put',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                    }).done(function () {
                        toastr.success('@lang('update')', '', {
                            rtl: isRtl
                        });
                        table.draw()

                    }).fail(function () {
                        toastr.error('@lang('something_wrong')', '', {
                            rtl: isRtl
                        });
                    });
                } else if (result.dismiss === Swal.DismissReason.cancel) {
                    toastr.info('@lang('update_canceled')', '', {
                        rtl: isRtl
                    })
                }
            });
        }

    });
    // $('#edit_modal').on('hidden.bs.modal', function () {
    //
    // })

    $(document).ready(function () {
    {{--    @can('help-list')--}}

    {{--    window.Echo.private('NotificationAdmin.help-list')--}}
    {{--        .listen('.NotificationAdmin', (e) => {--}}
    {{--          var count=  $('#count_not').text()--}}
    {{--            $('#count_not').html(parseInt(count)+1)--}}
    {{--            var x = new Audio('{{asset('dashboard/not/notification.mp3')}}');--}}
    {{--            // Show loading animation.--}}
    {{--            var playPromise = x.play();--}}

    {{--            if (playPromise !== undefined) {--}}
    {{--                playPromise.then(_ => {--}}
    {{--                    x.play();--}}
    {{--                })--}}
    {{--                    .catch(error => {--}}
    {{--                    });--}}
    {{--            }--}}
    {{--            $('#not').append(`--}}
    {{--        <div class="dropdown-divider "></div>--}}
    {{--        <a href="${e[3]}" class="dropdown-item" style="background-color: #1AB7EA">--}}
    {{--            <i class="fas fa-recycle mr-2"></i> ${e[2]}--}}
    {{--        <span class="float-right text-muted text-sm">منذ 1 ثانية</span>--}}
    {{--        </a>--}}

    {{--        `)--}}
    {{--        })--}}
    {{--    @endcan--}}
    {{--    @can('user-list')--}}

    {{--    window.Echo.private('NotificationAdmin.user-list')--}}
    {{--        .listen('.NotificationAdmin', (e) => {--}}
    {{--            console.log(e)--}}
    {{--            var count=  $('#count_not').text()--}}
    {{--            console.log(count);--}}
    {{--            $('#count_not').html(parseInt(count)+1)--}}
    {{--            var x = new Audio('{{asset('dashboard/not/notification.mp3')}}');--}}
    {{--            // Show loading animation.--}}
    {{--            var playPromise = x.play();--}}

    {{--            if (playPromise !== undefined) {--}}
    {{--                playPromise.then(_ => {--}}
    {{--                    x.play();--}}
    {{--                })--}}
    {{--                    .catch(error => {--}}
    {{--                    });--}}

    {{--            }--}}
    {{--            $('#not').append(`--}}
    {{--        <div class="dropdown-divider "></div>--}}
    {{--        <a href="${e[3]}" class="dropdown-item" style="background-color: #1AB7EA">--}}
    {{--            <i class="fas fa-recycle mr-2"></i> ${e[2]}--}}
    {{--        <span class="float-right text-muted text-sm">منذ 1 ثانية</span>--}}
    {{--        </a>--}}

    {{--        `)--}}
    {{--        })--}}
    {{--    @endcan--}}
    {{--    @can('competition-list')--}}

    {{--    window.Echo.private('NotificationAdmin.competition-list')--}}
    {{--        .listen('.NotificationAdmin', (e) => {--}}
    {{--            console.log(e)--}}
    {{--            var count=  $('#count_not').text()--}}
    {{--            console.log(count);--}}
    {{--            $('#count_not').html(parseInt(count)+1)--}}
    {{--            var x = new Audio('{{asset('dashboard/not/notification.mp3')}}');--}}
    {{--            // Show loading animation.--}}
    {{--            var playPromise = x.play();--}}

    {{--            if (playPromise !== undefined) {--}}
    {{--                playPromise.then(_ => {--}}
    {{--                    x.play();--}}
    {{--                })--}}
    {{--                    .catch(error => {--}}
    {{--                    });--}}

    {{--            }--}}
    {{--            $('#not').append(`--}}
    {{--        <div class="dropdown-divider "></div>--}}
    {{--        <a href="${e[3]}" class="dropdown-item" style="background-color: #1AB7EA">--}}
    {{--            <i class="fas fa-recycle mr-2"></i> ${e[2]}--}}
    {{--        <span class="float-right text-muted text-sm">منذ 1 ثانية</span>--}}
    {{--        </a>--}}

    {{--        `)--}}
    {{--        })--}}
    {{--    @endcan--}}
        @can('product')


    window.Echo.channel('NotificationAdmin.product')
        .listen('.NotificationAdmin', (e) => {
            console.log(e)
            var count=  $('#count_not').text()
            console.log(count);
            $('#count_not').html(parseInt(count)+1)
            var x = new Audio('{{asset('dashboard/not/notification.mp3')}}');
            // Show loading animation.
            var playPromise = x.play();

            if (playPromise !== undefined) {
                playPromise.then(_ => {
                    x.play();
                })
                    .catch(error => {
                    });

            }
            $('#not').append(`
            <div class="dropdown-divider "></div>
            <a href="${e[3]}" class="dropdown-item" style="background-color: #1AB7EA">
                <i class="fas fa-recycle mr-2"></i> ${e[2]}
            <span class="float-right text-muted text-sm">منذ 1 ثانية</span>
            </a>

            `)
        })
        @endcan
        @can('user')


        window.Echo.channel('NotificationAdmin.user')
            .listen('.NotificationAdmin', (e) => {
                console.log(e)
                var count=  $('#count_not').text()
                console.log(count);
                $('#count_not').html(parseInt(count)+1)
                var x = new Audio('{{asset('dashboard/not/notification.mp3')}}');
                // Show loading animation.
                var playPromise = x.play();

                if (playPromise !== undefined) {
                    playPromise.then(_ => {
                        x.play();
                    })
                        .catch(error => {
                        });

                }
                $('#not').append(`
            <div class="dropdown-divider "></div>
            <a href="${e[3]}" class="dropdown-item" style="background-color: #1AB7EA">
                <i class="fas fa-recycle mr-2"></i> ${e[2]}
            <span class="float-right text-muted text-sm">منذ 1 ثانية</span>
            </a>

            `)
            })
        @endcan
        @can('artist')


        window.Echo.channel('NotificationAdmin.artist')
            .listen('.NotificationAdmin', (e) => {
                console.log(e)
                not()

            })
        @endcan
        @can('location')


        window.Echo.channel('NotificationAdmin.location')
            .listen('.NotificationAdmin', (e) => {
                console.log(e)
                not()

            })
        @endcan
        @can('course')


        window.Echo.channel('NotificationAdmin.course')
            .listen('.NotificationAdmin', (e) => {
                console.log(e)
                not()

            })
        @endcan
        @can('service')


        window.Echo.channel('NotificationAdmin.service')
            .listen('.NotificationAdmin', (e) => {
                console.log(e)
                var count=  $('#count_not').text()
                console.log(count);
                $('#count_not').html(parseInt(count)+1)
                var x = new Audio('{{asset('dashboard/not/notification.mp3')}}');
                // Show loading animation.
                var playPromise = x.play();

                if (playPromise !== undefined) {
                    playPromise.then(_ => {
                        x.play();
                    })
                        .catch(error => {
                        });

                }
                $('#not').append(`
            <div class="dropdown-divider "></div>
            <a href="${e[3]}" class="dropdown-item" style="background-color: #1AB7EA">
                <i class="fas fa-recycle mr-2"></i> ${e[2]}
            <span class="float-right text-muted text-sm">منذ 1 ثانية</span>
            </a>

            `)

            })
        @endcan
    {{--})--}}
    // window.Echo.channel('msg')
    //     .listen('.msg', (e) => {
    //         console.log(e)
    //         if (e[2] == "admin") {
    //             console.log(e[2])
    //             $('#' + e[3]).append(`
    //      <div class="messag e-data">
    //           <span class="message-data-time">منذ 1 ثانية</span>
    //      </div>
    //                    <div class="message my-message"> ${e[0]}</div>
    //     `)
    //             console.log(e[2])
    //         } else {
    //             $('#' + e[3]).append(`
    //           <li class="clearfix">
    //                                                                 <div class="message-data text-right">
    //                                                                     <span class="message-data-time">منذ 1 ثانية</span>
    //                                                                     <img
    //                                                                         src="${e[4]}"
    //                                                                         alt="avatar">
    //                                                                 </div>
    //                                                                 <div class="message other-message float-right">
    //                                                                    ${e[0]}
    //                                                                 </div>
    //                                                             </li>
    //         `)
    //         }
        })

    function not(){
        var count=  $('#count_not').text()
        console.log(count);
        $('#count_not').html(parseInt(count)+1)
        var x = new Audio('{{asset('dashboard/not/notification.mp3')}}');
        // Show loading animation.
        var playPromise = x.play();

        if (playPromise !== undefined) {
            playPromise.then(_ => {
                x.play();
            })
                .catch(error => {
                });

        }
        $('#not').append(`
            <div class="dropdown-divider "></div>
            <a href="${e[3]}" class="dropdown-item" style="background-color: #1AB7EA">
                <i class="fas fa-recycle mr-2"></i> ${e[2]}
            <span class="float-right text-muted text-sm">منذ 1 ثانية</span>
            </a>

            `)
    }
</script>

</body>

<!-- END: Body-->

</html>
