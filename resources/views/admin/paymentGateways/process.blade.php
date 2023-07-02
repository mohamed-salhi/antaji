@extends('part.app')
@section('title')
    @lang('payment')
@endsection
@section('styles')
    <style>
        input[type="checkbox"] {
            transform: scale(1.5);
        }

        body {
            margin-top: 20px;
        }

        .icon-box.medium {
            font-size: 20px;
            width: 50px;
            height: 50px;
            line-height: 50px;
        }

        .icon-box {
            font-size: 30px;
            margin-bottom: 33px;
            display: inline-block;
            color: #ffffff;
            height: 65px;
            width: 65px;
            line-height: 65px;
            background-color: #59b73f;
            text-align: center;
            border-radius: 0.3rem;
        }

        .social-icon-style2 li a {
            display: inline-block;
            font-size: 14px;
            text-align: center;
            color: #ffffff;
            background: #59b73f;
            height: 41px;
            line-height: 41px;
            width: 41px;
        }

        .rounded-3 {
            border-radius: 0.3rem !important;
        }

        .social-icon-style2 {
            margin-bottom: 0;
            display: inline-block;
            padding-left: 10px;
            list-style: none;
        }

        .social-icon-style2 li {
            vertical-align: middle;
            display: inline-block;
            margin-right: 5px;
        }

        a, a:active, a:focus {
            color: #616161;
            text-decoration: none;
            transition-timing-function: ease-in-out;
            -ms-transition-timing-function: ease-in-out;
            -moz-transition-timing-function: ease-in-out;
            -webkit-transition-timing-function: ease-in-out;
            -o-transition-timing-function: ease-in-out;
            transition-duration: .2s;
            -ms-transition-duration: .2s;
            -moz-transition-duration: .2s;
            -webkit-transition-duration: .2s;
            -o-transition-duration: .2s;
        }

        .text-secondary, .text-secondary-hover:hover {
            color: #59b73f !important;
        }

        .display-25 {
            font-size: 1.4rem;
        }

        .text-primary, .text-primary-hover:hover {
            color: #ff712a !important;
        }

        p {
            margin: 0 0 20px;
        }

        .mb-1-6, .my-1-6 {
            margin-bottom: 1.6rem;
        }
    </style>
@endsection
@section('content')
    <div class="content-wrapper">
        <div class="content-header row">
            <div class="content-header-left col-md-9 col-12 mb-2">
                <div class="row breadcrumbs-top">
                    <div class="col-12">
                        <h2 class="content-header-title float-left mb-0">@lang('payment')</h2>
                        <div class="breadcrumb-wrapper">
                            <ol class="breadcrumb">
                                {{--                                <li class="breadcrumb-item"><a href="{{ route('admin.index') }}">@lang('home')</a>--}}
                                {{--                                </li>--}}
                                <li class="breadcrumb-item"><a
                                        href="{{ route('payments.index') }}">@lang('payment')</a>
                                </li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="content-body">

            <section id="">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <div class="head-label">
                                    <h4 class="card-title">@lang('payment')</h4>
                                </div>
                            </div>
                            <div class="card-body">
                                <form id="search_form">
                                    <div class="row">
                                        <div class="col-3">
                                            <div class="form-group">
                                                <label for="s_user_name">@lang('user name')</label>
                                                <input id="s_user_name" type="text" class="search_input form-control"
                                                       placeholder="@lang('name')">
                                            </div>
                                        </div>
                                        <div class="col-3">
                                            <div class="form-group">
                                                <label for="s_user_phone">@lang('phone')</label>
                                                <input id="s_user_phone" type="text" class="search_input form-control"
                                                       placeholder="@lang('phone')">
                                            </div>
                                        </div>
                                        <div class="col-3">
                                            <div class="form-group">
                                                <label for="s_balance">@lang('balance')</label>
                                                <input id="s_balance" type="number"
                                                       class="search_input form-control"
                                                       placeholder="@lang('balance')">
                                            </div>
                                        </div>
                                        <div class="col-3">
                                            <div class="form-group">
                                                <label for="s_date">@lang('date')</label>
                                                <input id="s_date" type="date" class="search_input form-control"
                                                       placeholder="@lang('date')">
                                            </div>
                                        </div>


                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="s_process">@lang('process')</label>
                                                <select  id="s_process" class="search_input form-control">
                                                    <option selected disabled>@lang('select') @lang('process')</option>
                                                    <option value="1"> @lang('Charge to wallet') </option>
                                                    <option value="2"> @lang('Recharge to participate in the competition') </option>

                                                </select>
                                                <div class="invalid-feedback"></div>
                                            </div>
                                        </div>

                                        <div class="col-3" style="margin-top: 20px">
                                            <button id="search_btn" class="btn btn-outline-info" type="submit">
                                                <span><i class="fa fa-search"></i> @lang('search')</span>
                                            </button>
                                            <button id="clear_btn" class="btn btn-outline-secondary" type="submit">
                                                <span><i class="fa fa-undo"></i> @lang('reset')</span>
                                            </button>
{{--                                            <button id="btn_delete_all"--}}
{{--                                                    class="btn_delete_all btn btn-outline-danger " type="button">--}}
{{--                                                <span><i class="fa fa-lg fa-trash-alt" aria-hidden="true"></i> @lang('delete')</span>--}}
{{--                                            </button>--}}
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div class="table-responsive card-datatable" style="padding: 20px">
                                <table class="table" id="datatable">
                                    <thead>
                                    <tr>
                                        <th>@lang('user name')</th>
                                        <th>@lang('phone')</th>
                                        <th>@lang('balance')</th>
                                        <th >@lang('payment method')</th>
                                        <th>@lang('process')</th>
                                        <th style="width: 225px;">@lang('when')</th>

                                    </tr>
                                    </thead>
                                    <tbody></tbody>

                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

        </div>
    </div>

@endsection
@section('js')
@endsection
@section('scripts')
    <script src="https://cdn.ckeditor.com/ckeditor5/25.0.0/classic/ckeditor.js"></script>

    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        var table = $('#datatable').DataTable({
            processing: true,
            serverSide: true,
            "oLanguage": {
                @if (app()->isLocale('ar'))
                "sEmptyTable": "ليست هناك بيانات متاحة في الجدول",
                "sLoadingRecords": "جارٍ التحميل...",
                "sProcessing": "جارٍ التحميل...",
                "sLengthMenu": "أظهر _MENU_ مدخلات",
                "sZeroRecords": "لم يعثر على أية سجلات",
                "sInfo": "إظهار _START_ إلى _END_ من أصل _TOTAL_ مدخل",
                "sInfoEmpty": "يعرض 0 إلى 0 من أصل 0 سجل",
                "sInfoFiltered": "(منتقاة من مجموع _MAX_ مُدخل)",
                "sInfoPostFix": "",
                "sSearch": "ابحث:",
                "oAria": {
                    "sSortAscending": ": تفعيل لترتيب العمود تصاعدياً",
                    "sSortDescending": ": تفعيل لترتيب العمود تنازلياً"
                },
                @endif // "oPaginate": {"sPrevious": '<-', "sNext": '->'},
                "oPaginate": {
                    // remove previous & next text from pagination
                    "sPrevious": '&nbsp;',
                    "sNext": '&nbsp;'
                }
            },
            ajax: {
                url: '{{ route('payments.getData', app()->getLocale()) }}',

                data: function (d) {
                    d.user_name = $('#s_user_name').val();
                    d.user_phone = $('#s_user_phone').val();
                    d.balance = $('#s_balance').val();
                    d.date = $('#s_date').val();
                    d.process = $('#s_process').val();
                }
            },
            dom: 'B<"clear">lfrtip',
            buttons: {
                name: 'primary',

                buttons: [ 'excel' ]
            },
            columns: [

                {
                    data: 'user_name',
                    name: 'user_uuid',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'phone',
                    name: 'user_uuid',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'price',
                    name: 'price',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'pay_geteway',
                    name: 'pay_geteway',
                    orderable: false,
                    searchable: false
                },
                {
                    render: function (data, type, full, meta) {
                        return `<h3 class="btn btn-success btn-sm">${data}</h3> `;

                    },
                    data: 'progress_name',
                    name: 'progress_name',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'when',
                    name: 'created_at',

                },

            ]

        });

        //Edit
        $(document).ready(function () {
            {{--$(document).on('click', '.detail_btn', function (event) {--}}
            {{--    event.preventDefault();--}}
            {{--    $('input').removeClass('is-invalid');--}}
            {{--    $('.invalid-feedback').text('');--}}
            {{--    var button = $(this)--}}
            {{--    @foreach (locales() as $key => $value)--}}
            {{--    $('.detail_name_{{ $key }}').html(button.data('name_{{ $key }}'))--}}
            {{--    @endforeach--}}
            {{--    $('#detail_entry_price').html(button.data('entryprice'));--}}
            {{--    $('#detail_points_earned').html(button.data('pointsearned'));--}}
            {{--    $('#detail_expiry_date').html(button.data('expirydate'));--}}
            {{--    $('#detail_dest').html(button.data('dest'));--}}
            {{--    $('#detail_like').html(button.data('like'));--}}
            {{--    $('#detail_views').html(button.data('views'));--}}
            {{--    $('#detail-video-preview').attr('src', button.data('video'));--}}
            {{--    $('#detail_image').attr('src', button.data('image'));--}}
            {{--    $('#detail_number_subscriptions').html(button.data('number_subscriptions'));--}}
            {{--    console.log(button.data('user_winner'))--}}
            {{--    $('#detail_user_winner').html(button.data('user_winner'));--}}


            {{--});--}}
        });
    </script>
@endsection
