@extends('admin.part.app')
@section('title')
    @lang('Promo Codes')
@endsection
@section('styles')
    <style>
        input[type="checkbox"] {
            transform: scale(1.5);
        }

        .date-cell {
            display: flex;
            flex-direction: column;
        }

        .date-cell span {
            padding: 2px 5px;
        }

        .date1 {
            background-color: #F0F0F0;
        }

        .date2 {
            background-color: #E0E0E0;
        }
    </style>
@endsection
@section('content')
    <div class="content-wrapper">
        <div class="content-header row">
            <div class="content-header-left col-md-9 col-12 mb-2">
                <div class="row breadcrumbs-top">
                    <div class="col-12">
                        <h2 class="content-header-title float-left mb-0">@lang('Promo Codes')</h2>
                        <div class="breadcrumb-wrapper">
                            <ol class="breadcrumb">
                                {{--                                <li class="breadcrumb-item"><a href="{{ route('admin.index') }}">@lang('home')</a>--}}
                                {{--                                </li>--}}
                                <li class="breadcrumb-item"><a
                                        href="{{ route('discount.index') }}">@lang('Promo Codes')</a>
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
                                    <h4 class="card-title">@lang('Promo Codes')</h4>
                                </div>
                                {{--                                @can('place-create')--}}
                                <div class="text-right">
                                    <div class="form-group">
                                        <button class="btn btn-outline-primary button_modal" type="button"
                                                data-toggle="modal" id=""
                                                data-target="#full-modal-stem"><span><i
                                                    class="fa fa-plus"></i>@lang('add')</span>
                                        </button>
                                        <button

                                            class="btn_delete_all btn btn-outline-danger " type="button">
                                            <span><i  aria-hidden="true"></i> @lang('delete')</span>
                                        </button>
                                        <button
                                            data-status="1" class="btn_status btn btn-outline-success " type="button">
                                            <span><i aria-hidden="true"></i> @lang('activate')</span>
                                        </button>
                                        <button
                                            data-status="0" class="btn_status btn btn-outline-warning " type="button">
                                            <span><i aria-hidden="true"></i> @lang('deactivate')</span>
                                        </button>
                                    </div>
                                </div>
                                {{--                                @endcan--}}
                            </div>
                                                        <div class="card-body">
                                                            <form id="search_form">
                                                                <div class="row">
                                                                    <div class="col-3">
                                                                        <div class="form-group">
                                                                            <label for="s_name">@lang('name')</label>
                                                                            <input id="s_name" type="text"
                                                                                   class="search_input form-control"
                                                                                   placeholder="@lang('name')">
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-3">
                                                                        <div class="form-group">
                                                                            <label for="s_discount">@lang('discount')</label>
                                                                            <input id="s_discount" type="text"
                                                                                   class="search_input form-control"
                                                                                   placeholder="@lang('discount')">
                                                                        </div>
                                                                    </div>


                                                                    <div class="col-md-3">
                                                                        <div class="form-group">
                                                                            <label for="s_status">@lang('status')</label>
                                                                            <select name="s_status" id="s_status" class="search_input form-control">
                                                                                <option selected disabled>@lang('select') @lang('status')</option>

                                                                                <option value="2"> @lang('inactive') </option>
                                                                                <option value="1"> @lang('active') </option>

                                                                            </select>
                                                                            <div class="invalid-feedback"></div>
                                                                        </div>
                                                                    </div>

                                                                    <div class="col-3">
                                                                        <div class="form-group">
                                                                            <label for="s_type">@lang('type')</label>
                                                                            <select name="s_type" id="s_type"
                                                                                    class="search_input form-control">
                                                                                <option selected
                                                                                        disabled>@lang('select')  @lang('type')</option>
                                                                                <option value="percent"> @lang('Percent') </option>
                                                                                <option value="fixed_price"> @lang('Fixed price') </option>
                                                                            </select>
                                                                        </div>
                                                                    </div>


                                                                    <div class="col-3">
                                                                        <div class="form-group">
                                                                            <label for="s_code">@lang('code')</label>
                                                                            <input id="s_code" type="text" class="search_input form-control"
                                                                                   placeholder="@lang('code')">
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-3" style="margin-top: 20px">
                                                                        <button id="search_btn" class="btn btn-outline-info" type="submit">
                                                                            <span><i class="fa fa-search"></i> @lang('search')</span>
                                                                        </button>
                                                                        <button id="clear_btn" class="btn btn-outline-secondary" type="submit">
                                                                            <span><i class="fa fa-undo"></i> @lang('reset')</span>
                                                                        </button>


                                                                        <div class="col-3" style="margin-top: 20px">

                                                                        </div>
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
                                        <th>@lang('code')</th>
                                        <th>@lang('discount')</th>
                                        <th>@lang('discount type')</th>
                                        <th>@lang('number of usage')</th>
                                        <th>@lang('number of usage for user')</th>
                                        <th>@lang('date')</th>
                                        <th>@lang('status')</th>
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

    <!-- Modal -->
    <div class="modal fade" class="full-modal-stem" id="full-modal-stem" tabindex="-1" role="dialog"
         aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">@lang('add')</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('discount.store') }}" method="POST" id="add-mode-form" class="add-mode-form"
                      enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        @foreach (locales() as $key => $value)
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="name_{{ $key }}">@lang('name') @lang($value)</label>
                                    <input type="text" class="form-control"
                                           placeholder="@lang('name') @lang($value)"
                                           name="name_{{ $key }}">
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                        @endforeach
                        <div class="col-12">
                            <div class="form-group">
                                <label for="name">@lang('code') </label>
                                <input type="text" class="form-control"
                                       placeholder="@lang('code')" name="code"
                                       id="code">
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label for="price">@lang('discount') </label>
                                <input type="number" class="form-control"
                                       placeholder="@lang('discount')" name="discount"
                                       id="discount">
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label for="discount_type">@lang('discount type')</label>
                                <select class="form-control" name="discount_type" required>
                                    <option value="">@lang('select') @lang('discount type')</option>
                                    <option value="{{ \App\Models\Discount::PERCENT }}"> @lang('Percent') </option>
                                    <option value="{{ \App\Models\Discount::FIXED_PRICE }}"> @lang('Fixed price') </option>

                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label for="price">@lang('number of usage') </label>
                                <input class="form-control"
                                       placeholder="@lang('number of usage')" name="number_of_usage"
                                       id="number_of_usage">
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label for="price">@lang('number of usage for user') </label>
                                <input class="form-control"
                                       placeholder="@lang('number of usage for user')" name="number_of_usage_for_user"
                                       id="number_of_usage_for_user">
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label for="from">@lang('from') </label>
                                <input type="date" class="form-control"
                                       placeholder="@lang('from')" name="date_from"
                                       id="name">
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label for="to">@lang('to') </label>
                                <input type="date" class="form-control"
                                       placeholder="@lang('to')" name="date_to"
                                       id="name">
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="col-12">
                            <h3>@lang('Where do you use it?') </h3>

                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="checkbox1" name="checkboxes[]"
                                       value="product">
                                <h3 class="form-check-label" for="checkbox1">@lang('products')</h3>
                            </div>
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="checkbox2" name="checkboxes[]"
                                       value="course">
                                <h3 class="form-check-label" for="checkbox2">@lang('courses')</h3>
                            </div>
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" name="checkboxes[]" value="service"
                                       id="checkbox2">
                                <h3 class="form-check-label" for="checkbox2">@lang('services')</h3>
                            </div>
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" name="checkboxes[]" value="location"
                                       id="checkbox2">
                                <h3 class="form-check-label text-primary" for="checkbox2">@lang('locations')</h3>
                            </div>
                            <!-- Add more checkboxes as needed -->
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button  class="btn btn-primary done">@lang('save')</button>

                        <button type="button" class="btn btn-secondary"
                                data-dismiss="modal">@lang('close')</button>
                    </div>

                </form>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="edit_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
         aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">@lang('edit')</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('discount.update') }}" method="POST" id="form_edit" class=""
                      enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="uuid" id="uuid" class="form-control"/>
                    <div class="modal-body">
                        @foreach (locales() as $key => $value)
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="name_{{ $key }}">@lang('name') @lang($value)</label>
                                    <input type="text" class="form-control"
                                           placeholder="@lang('name') @lang($value)"
                                           name="name_{{ $key }}" id="edit_name_{{ $key }}">
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                        @endforeach
                        <div class="col-12">
                            <div class="form-group">
                                <label for="name">@lang('code') </label>
                                <input type="text" class="form-control"
                                       placeholder="@lang('code')" name="code"
                                       id="edit_code">
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label for="price">@lang('discount') </label>
                                <input type="number" class="form-control"
                                       placeholder="@lang('discount')" name="discount"
                                       id="edit_discount">
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label for="discount_type">@lang('discount type')</label>
                                <select class="form-control" id="edit_discount_type" name="discount_type" required>
                                    <option value="">@lang('select') @lang('discount type')</option>
                                    <option value="{{ \App\Models\Discount::PERCENT }}"> @lang('Percent') </option>
                                    <option value="{{ \App\Models\Discount::FIXED_PRICE }}"> @lang('Fixed price') </option>

                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label for="price">@lang('number of usage') </label>
                                <input class="form-control"
                                       placeholder="@lang('number of usage')" name="number_of_usage"
                                       id="edit_number_of_usage">
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label for="price">@lang('number of usage for user') </label>
                                <input class="form-control"
                                       placeholder="@lang('number of usage for user')" name="number_of_usage_for_user"
                                       id="edit_number_of_usage_for_user">
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label for="from">@lang('from') </label>
                                <input type="date" class="form-control"
                                       placeholder="@lang('from')" name="date_from"
                                       id="edit_date_from">
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label for="to">@lang('to') </label>
                                <input type="date" class="form-control"
                                       placeholder="@lang('to')" name="date_to"
                                       id="edit_date_to">
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>

                        <div class="col-12">
                            <h3>@lang('Where do you use it?') </h3>

                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" name="checkboxes[]" value="product"
                                       id="edit_checkbox_product">
                                <h3 class="form-check-label text-primary">@lang('products')</h3>
                            </div>
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" name="checkboxes[]" value="course"
                                       id="edit_checkbox_course">
                                <h3 class="form-check-label text-primary">@lang('courses')</h3>
                            </div>
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" name="checkboxes[]" value="service"
                                       id="edit_checkbox_service">
                                <h3 class="form-check-label text-primary">@lang('services')</h3>
                            </div>
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" name="checkboxes[]" value="location"
                                       id="edit_checkbox_location">
                                <h3 class="form-check-label text-primary">@lang('locations')</h3>
                            </div>
                            <!-- Add more checkboxes as needed -->
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button  class="btn btn-primary done">@lang('save')</button>

                        <button type="button" class="btn btn-secondary"
                                data-dismiss="modal">@lang('close')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@section('scripts')

    <script src="https://cdn.ckeditor.com/ckeditor5/25.0.0/classic/ckeditor.js"></script>
    <script type="text/javascript">
        $('.input-images').imageUploader();

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        //bindTable
        var table = $('#datatable').DataTable({

            processing: true,
            serverSide: true,
            responsive: true,
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
            },
            ajax: {
                url: '{{ route('discount.indexTable', app()->getLocale()) }}',
                data: function (d) {
                    d.status = $('#s_status').val();
                    d.code = $('#s_code').val();
                    d.discount_type = $('#s_type').val();
                    d.name = $('#s_name').val();
                    d.discount = $('#s_discount').val();

                }
            },
            dom: '<"row"<"col-md-12"<"row"<"col-md-6"B><"col-md-6"f> > ><"col-md-12"rt> <"col-md-12"<"row"<"col-md-5"i><"col-md-7"p>>> >',
            "buttons": [
                {

                    "extend": 'excel',
                    text: '<span class="fa fa-file-excel-o"></span> @lang('Excel Export')',
                    "titleAttr": 'Excel',
                    "action": newexportaction,
                    "exportOptions": {
                        columns: ':not(:last-child)',
                    },
                    "filename": function () {
                        var d = new Date();
                        var l = d.getFullYear() + '-' + (d.getMonth() + 1) + '-' + d.getDate();
                        var n = d.getHours() + "-" + d.getMinutes() + "-" + d.getSeconds();
                        return 'List_' + l + ' ' + n;
                    },
                },
            ],
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
                    data: 'name_translate',
                    name: 'name'
                },
                {
                    data: 'code',
                    name: 'code'
                },
                {
                    data: 'discount',
                    name: 'discount'
                },
                {
                    data: 'type_text',
                    name: 'type_text'
                },
                {
                    data: 'number_of_usage',
                    name: 'number_of_usage'
                },
                {
                    data: 'number_of_usage_for_user',
                    name: 'number_of_usage_for_user'
                },
                {
                    data: 'date',
                    name: 'date'
                },

                {
                    data: 'status',
                    name: 'status'
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: true
                },
            ]

        });


        $(document).ready(function () {
            $(document).on('click', '.btn_edit', function (event) {
                $('input').removeClass('is-invalid');
                $('.invalid-feedback').text('');
                event.preventDefault();
                var button = $(this)
                var uuid = button.data('uuid')
                $('#uuid').val(uuid);
                console.log(button.data('checkboxes'))



                console.log(button.data('discount_type'))
                $('#edit_code').val(button.data('code'))
                $('#edit_discount').val(button.data('discount'))
                $('#edit_number_of_usage_for_user').val(button.data('number_of_usage_for_user'))
                $('#edit_number_of_usage').val(button.data('number_of_usage'))
                $('#edit_date_from').val(button.data('date_from'))
                $('#edit_date_to').val(button.data('date_to'))
                $('#edit_discount_type').val(button.data('discount_type')).trigger('change');
                @foreach (locales() as $key => $value)
                $('#edit_name_{{ $key }}').val(button.data('name_{{ $key }}'))
                @endforeach
                let fileArray = button.data('checkboxes').split(',') + '';
                if (fileArray.indexOf(',') >= 0) {
                    fileArray = button.data('checkboxes').split(',');
                }
                // console.log(button.data(button.data('checkboxes').split(',')))

                $.each(fileArray, function (index, fileName) {
                    $('#edit_checkbox_'+fileName).prop("checked", true)

                })

            });
        });
    </script>
@endsection
