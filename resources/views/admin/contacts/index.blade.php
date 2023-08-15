@extends('admin.part.app')
@section('title')
    @lang('contact us')
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
                        <h2 class="content-header-title float-left mb-0">@lang('contact us')</h2>
                        <div class="breadcrumb-wrapper">
                            <ol class="breadcrumb">
                                {{--                                <li class="breadcrumb-item"><a href="{{ route('admin.index') }}">@lang('home')</a>--}}
                                {{--                                </li>--}}
                                <li class="breadcrumb-item"><a
                                        href="{{ route('contacts.index') }}">@lang('contact us')</a>
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
                                    <h4 class="card-title">@lang('contact us')</h4>
                                </div>
                            </div>
                            <div class="card-body">
                                <form id="search_form">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="s_importance">@lang('importance')</label>
                                                <select name="s_importance" id="s_importance"
                                                        class="search_input form-control">
                                                    <option selected
                                                            disabled>@lang('select') @lang('importance')</option>
                                                    <option value="1"> @lang('normal') </option>
                                                    <option value="2"> @lang('important') </option>
                                                    <option value="3"> @lang('very important') </option>
                                                </select>
                                                <div class="invalid-feedback"></div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="s_view">@lang('view')</label>
                                                <select name="s_view" id="s_view" class="search_input form-control">
                                                    <option selected disabled>@lang('select') @lang('view')</option>
                                                    <option value="2"> @lang('messages that have been read') </option>
                                                    <option value="1"> @lang('Messages not yet read') </option>

                                                </select>
                                                <div class="invalid-feedback"></div>
                                            </div>
                                        </div>
                                        <div class="col-3">
                                            <div class="form-group">
                                                <label for="s_name">@lang('name')</label>
                                                <input id="s_name" type="text" class="search_input form-control"
                                                       placeholder="@lang('name')">
                                            </div>
                                        </div>
                                        <div class="col-3">
                                            <div class="form-group">
                                                <label for="s_email">@lang('email')</label>
                                                <input id="s_email" type="text" class="search_input form-control"
                                                       placeholder="@lang('email')">
                                            </div>
                                        </div>
                                        <div class="col-3" style="margin-top: 20px">
                                            <button id="search_btn" class="btn btn-outline-info" type="submit">
                                                <span><i class="fa fa-search"></i> @lang('search')</span>
                                            </button>
                                            <button id="clear_btn" class="btn btn-outline-secondary" type="submit">
                                                <span><i class="fa fa-undo"></i> @lang('reset')</span>
                                            </button>
                                            <button id="btn_delete_all"
                                                    class="btn_delete_all btn btn-outline-danger " type="button">
                                                <span><i class="fa fa-lg fa-trash-alt" aria-hidden="true"></i> @lang('delete')</span>
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div class="table-responsive card-datatable" style="padding: 20px">
                                <table class="table" id="datatable">
                                    <thead>
                                    <tr>
                                        <th><input name="select_all" id="example-select-all" type="checkbox"
                                                   onclick="CheckAll('box1', this)"/></th>
                                        <th>@lang('name')</th>
                                        <th>@lang('email')</th>


                                            <th>@lang('importance')</th>

                                            <th style="width: 225px;">@lang('actions')</th>

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
    <div class="modal fade" id="detail_modal" data-backdrop="static" data-keyboard="false" tabindex="-1"
         aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">@lang('details')</h5>
                    <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close">x</button>
                </div>
                <div class="modal-body">
                    <div class="container">
                        <div class="row justify-content-center">
                            <div class="col-lg-8">
                                <div class="ps-lg-1-6 ps-xl-5">
                                    <div class="mb-5 wow fadeIn">
                                        <div class="text-start mb-1-6 wow fadeIn">
                                            <h2 class="h1 mb-0 text-primary">@lang('name')</h2>
                                        </div>

                                        <p id="detail_name"></p>
                                    </div>
                                    <div class="mb-5 wow fadeIn">
                                        <div class="text-start mb-1-6 wow fadeIn">
                                            <h2 class="h1 mb-0 text-primary">@lang('email')</h2>
                                        </div>

                                        <p id="detail_email"></p>
                                    </div>
                                    <div class="mb-5 wow fadeIn">
                                        <div class="text-start mb-1-6 wow fadeIn">
                                            <h2 class="h1 mb-0 text-primary">@lang('details')</h2>
                                        </div>
                                        <p id="detail_description"></p>
                                    </div>
                                    <img id="detail_image" alt="...">

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button style="color: #343D55" type="button" class="btn btn-secondary"
                            data-dismiss="modal">@lang('close')</button>

                </div>
            </div>
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
            "createdRow": function (row, data, index) {
                if (data['view'] == 1) {
                    $(row).css("background-color", "#343D55");
                }

            },
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
                url: '{{ route('contacts.indexTable', app()->getLocale()) }}',
                data: function (d) {
                    d.view = $('#s_view').val();
                    d.importance = $('#s_importance').val();
                    d.name = $('#s_name').val();
                    d.email = $('#s_email').val();

                }
            },
            columns: [{
                "render": function (data, type, full, meta) {
                    return `<td><input type="checkbox" onclick="checkClickFunc()" value="${data}" class="box1" ></td>
`;
                },
                name: 'checkbox',
                data: 'checkbox',
                orderable: false,
                searchable: false
            },
                {
//                     "render": function (data, type, full, meta) {
//                         return `<h3><h3  class="text-primary" >${data}</h3>
// `;
//                     },
                    data: 'name',
                    name: 'name'
                },

                {
//                     "render": function (data, type, full, meta) {
//                         return `<h3><h3  class="text-primary" >${data}</h3>
// `;
//                     },
                    data: 'email',
                    name: 'email'
                },

                {
                    "render": function (data, type, full, meta) {
                        console.log(data)
                        if (data == 1) {
                            return `<i>@lang('normal')</i>`

                        } else if (data == 2) {
                            return `<i>@lang('important')</i>`

                        } else {
                            return `<i>@lang('very important')</i>`
                        }

                    },
                    data: 'importance',
                    name: 'importance'
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: true
                },
            ]

        });

        //Edit
        $(document).ready(function () {

            $(document).on('click', '.detail_btn', function (event) {


                event.preventDefault();
                $('input').removeClass('is-invalid');
                $('.invalid-feedback').text('');
                var button = $(this)
                console.log(button.data('url'))
                if (button.data('view') == 1) {
                    $.ajax({

                        type: "post",
                        cache: false,
                        contentType: false,
                        processData: false,
                        url: button.data('url'),

                        data: {
                            _token: '{{csrf_token()}}',
                        },

                        beforeSend: function () {
                            console.log(button.data('url'))
                        },
                        success: function (result) {
                            var count = $('#counthelps').text();
                            $('#counthelps').html(parseInt(count) - 1)
                            table.draw()
                        },
                    });
                }
                $('#detail_name').html(button.data('name'));
                $('#detail_email').html(button.data('email'));

                $('#detail_description').html(button.data('description'));
                $('#detail_image').attr('src', button.data('image'));


            });
            $(document).on('click', '.dropdown-item', function (event) {
                var button = $(this)
                console.log(button.data('url'))
                $.ajax({
                    type: "post",
                    cache: false,
                    contentType: false,
                    processData: false,
                    url: button.data('url'),

                    beforeSend: function () {
                    },
                    success: function (result) {
                        toastr.success('@lang('done_successfully')', '', {
                            rtl: isRtl
                        });
                        table.draw()
                    },
                    error: function (data) {
                        if (data.status === 422) {
                            var response = data.responseJSON;
                            $.each(response.errors, function (key, value) {
                                toastr.error(value);
                            });
                        } else {
                            toastr.error('@lang('something_wrong')', '', {
                                rtl: isRtl
                            });
                        }
                    }
                });
            });
        });
    </script>
@endsection
