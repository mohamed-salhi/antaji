@extends('admin.part.app')
@section('title')
    @lang('packages')
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
                        <h2 class="content-header-title float-left mb-0">@lang('packages')</h2>
                        <div class="breadcrumb-wrapper">
                            <ol class="breadcrumb">
                                {{--                                <li class="breadcrumb-item"><a href="{{ route('admin.index') }}">@lang('home')</a>--}}
                                {{--                                </li>--}}
                                <li class="breadcrumb-item"><a
                                        href="{{ route('packages.index') }}">@lang('packages')</a>
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
                                    <h4 class="card-title">@lang('packages')</h4>
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
                                            <span><i aria-hidden="true"></i> @lang('delete')</span>
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
                            {{--                            <div class="card-body">--}}
                            {{--                                <form id="search_form">--}}
                            {{--                                    <div class="row">--}}
                            {{--                                        <div class="col-3">--}}
                            {{--                                            <div class="form-group">--}}
                            {{--                                                <label for="s_name">@lang('name')</label>--}}
                            {{--                                                <input id="s_name" type="text"--}}
                            {{--                                                       class="search_input form-control"--}}
                            {{--                                                       placeholder="@lang('name')">--}}
                            {{--                                            </div>--}}
                            {{--                                        </div>--}}
                            {{--                                        <div class="col-3">--}}
                            {{--                                            <div class="form-group">--}}
                            {{--                                                <label for="s_price">@lang('price')</label>--}}
                            {{--                                                <input id="s_price" type="text"--}}
                            {{--                                                       class="search_input form-control"--}}
                            {{--                                                       placeholder="@lang('price')">--}}
                            {{--                                            </div>--}}
                            {{--                                        </div>--}}


                            {{--                                        <div class="col-md-3">--}}
                            {{--                                            <div class="form-group">--}}
                            {{--                                                <label for="s_status">@lang('status')</label>--}}
                            {{--                                                <select name="s_status" id="s_status" class="search_input form-control">--}}
                            {{--                                                    <option selected disabled>@lang('select') @lang('status')</option>--}}

                            {{--                                                    <option value="2"> @lang('inactive') </option>--}}
                            {{--                                                    <option value="1"> @lang('active') </option>--}}

                            {{--                                                </select>--}}
                            {{--                                                <div class="invalid-feedback"></div>--}}
                            {{--                                            </div>--}}
                            {{--                                        </div>--}}
                            {{--                                        <div class="col-3">--}}
                            {{--                                            <div class="form-group">--}}
                            {{--                                                <label for="s_category_contents_uuid">@lang('categories')</label>--}}
                            {{--                                                <select name="s_category_contents_uuid" id="s_category_contents_uuid"--}}
                            {{--                                                        class="search_input form-control">--}}
                            {{--                                                    <option selected--}}
                            {{--                                                            disabled>@lang('select')  @lang('categories')</option>--}}
                            {{--                                                    @foreach ($category_contents as $item)--}}
                            {{--                                                        <option value="{{ $item->uuid }}"> {{ $item->name }} </option>--}}
                            {{--                                                    @endforeach--}}
                            {{--                                                </select>--}}
                            {{--                                            </div>--}}
                            {{--                                        </div>--}}
                            {{--                                        <div class="col-3">--}}
                            {{--                                            <div class="form-group">--}}
                            {{--                                                <label for="s_city_uuid">@lang('cities')</label>--}}
                            {{--                                                <select name="s_city_uuid" id="s_city_uuid"--}}
                            {{--                                                        class="search_input form-control">--}}
                            {{--                                                    <option selected--}}
                            {{--                                                            disabled>@lang('select')  @lang('cities')</option>--}}
                            {{--                                                    @foreach ($cities as $item)--}}
                            {{--                                                        <option value="{{ $item->uuid }}"> {{ $item->name }} </option>--}}
                            {{--                                                    @endforeach--}}
                            {{--                                                </select>--}}
                            {{--                                            </div>--}}
                            {{--                                        </div>--}}
                            {{--                                        <div class="col-3">--}}
                            {{--                                            <div class="form-group">--}}
                            {{--                                                <label for="s_working_condition">@lang('working condition')</label>--}}
                            {{--                                                <select name="s_working_condition" id="s_working_condition"--}}
                            {{--                                                        class="search_input form-control">--}}
                            {{--                                                    <option selected--}}
                            {{--                                                            disabled>@lang('select')  @lang('working condition')</option>--}}
                            {{--                                                    <option value="hour"> @lang('hour')</option>--}}
                            {{--                                                    <option value="contract"> @lang('contract')</option>--}}
                            {{--                                                    <option value="Fixed_price"> @lang('Fixed price')</option>--}}
                            {{--                                                </select>--}}
                            {{--                                            </div>--}}
                            {{--                                        </div>--}}
                            {{--                                        <div class="col-3">--}}
                            {{--                                            <div class="form-group">--}}
                            {{--                                                <label for="s_from">@lang('from')</label>--}}
                            {{--                                                <input id="s_from" type="date" class="search_input form-control"--}}
                            {{--                                                       placeholder="@lang('from')">--}}
                            {{--                                            </div>--}}
                            {{--                                        </div>--}}
                            {{--                                        <div class="col-3">--}}
                            {{--                                            <div class="form-group">--}}
                            {{--                                                <label for="s_to">@lang('to')</label>--}}
                            {{--                                                <input id="s_to" type="date" class="search_input form-control"--}}
                            {{--                                                       placeholder="@lang('to')">--}}
                            {{--                                            </div>--}}
                            {{--                                        </div>--}}
                            {{--                                        <div class="col-3" style="margin-top: 20px">--}}
                            {{--                                            <button id="search_btn" class="btn btn-outline-info" type="submit">--}}
                            {{--                                                <span><i class="fa fa-search"></i> @lang('search')</span>--}}
                            {{--                                            </button>--}}
                            {{--                                            <button id="clear_btn" class="btn btn-outline-secondary" type="submit">--}}
                            {{--                                                <span><i class="fa fa-undo"></i> @lang('reset')</span>--}}
                            {{--                                            </button>--}}


                            {{--                                            <div class="col-3" style="margin-top: 20px">--}}

                            {{--                                            </div>--}}
                            {{--                                        </div>--}}
                            {{--                                    </div>--}}
                            {{--                                </form>--}}
                            {{--                            </div>--}}

                            <div class="table-responsive card-datatable" style="padding: 20px">
                                <table class="table" id="datatable">
                                    <thead>
                                    <tr>
                                        <th><input name="select_all" id="example-select-all" type="checkbox"
                                                   onclick="CheckAll('box1', this)"/></th>
                                        <th>@lang('name')</th>
                                        <th>@lang('number of products in each section')</th>
                                        <th>@lang('percentage of sale')</th>
                                        <th>@lang('quality')</th>
                                        <th>@lang('price')</th>

                                        <th>@lang('type')</th>
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
                <form action="{{ route('packages.update') }}" method="POST" id="form_edit" class=""
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
                        @foreach (locales() as $key => $value)
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="details_{{ $key }}">@lang('details') @lang($value)</label>
                                    <textarea id="edit_details_{{ $key }}" class="myTextarea1"
                                              name="details_{{ $key }}"
                                              placeholder="@lang('details') @lang($value)"></textarea>
                                    <div class="invalid-feedback"></div>

                                </div>
                            </div>
                        @endforeach
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="price">@lang('price') </label>
                                    <input type="number" class="form-control"
                                           placeholder="@lang('price')" name="price"
                                           id="edit_price">
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label for="name">@lang('number of products in each section') </label>
                                <input type="number" class="form-control"
                                       placeholder="@lang('number of products in each section')" name="number_of_products_in_each_section"
                                       id="edit_number_of_products_in_each_section">
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label for="price">@lang('quality') </label>
                                <select class="form-control" name="quality" id="edit_quality" required>
                                    <option value="">@lang('select') @lang('quality')</option>
                                    <option value="4k"> @lang('4k') </option>
                                    <option value="1080p"> @lang('1080p') </option>

                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label for="price">@lang('percentage of sale') </label>
                                <input class="form-control"
                                       placeholder="@lang('percentage of sale')" name="percentage_of_sale"
                                       id="edit_percentage_of_sale">
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>

                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary"
                                data-dismiss="modal">@lang('close')</button>
                        <button class="btn btn-primary">@lang('save changes')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@section('scripts')


    <script src="https://cdn.ckeditor.com/ckeditor5/25.0.0/classic/ckeditor.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/5.10.2/tinymce.min.js"
            integrity="sha512-MbhLUiUv8Qel+cWFyUG0fMC8/g9r+GULWRZ0axljv3hJhU6/0B3NoL6xvnJPTYZzNqCQU3+TzRVxhkE531CLKg=="
            crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script type="text/javascript">
        tinymce.init({
            selector: '.myTextarea1',

        });
        tinymce.init({
            selector: '.myTextarea3',

        });


    </script>
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
                url: '{{ route('packages.indexTable', app()->getLocale()) }}',

            },
            {{--dom: '<"row"<"col-md-12"<"row"<"col-md-6"B><"col-md-6"f> > ><"col-md-12"rt> <"col-md-12"<"row"<"col-md-5"i><"col-md-7"p>>> >',--}}
                {{--buttons: [--}}
                {{--    {--}}
                {{--        extend: 'excel',--}}
                {{--        text: '<span class="fa fa-file-excel-o"></span> @lang('Excel Export')',--}}
                {{--        exportOptions: {--}}
                {{--            columns: [1, 2, 3, 4, 5, 6, 7, 8],--}}
                {{--            modifier: {--}}
                {{--                search: 'applied',--}}
                {{--                order: 'applied'--}}
                {{--            }--}}
                {{--        }--}}
                {{--    }--}}
                {{--],--}}
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
                    data: 'number_of_products_in_each_section',
                    name: 'number_of_products_in_each_section'
                },

                {
                    data: 'percentage_of_sale',
                    name: 'percentage_of_sale'
                },
                {
                    data: 'quality',
                    name: 'quality'
                },
                {
                    data: 'price',
                    name: 'price'
                },
                {
                    data: 'type',
                    name: 'type'
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
                $('#edit_price').val(button.data('price'));

                $('#edit_number_of_products_in_each_section').val(button.data('number_of_products_in_each_section'))
                $('#edit_percentage_of_sale').val(button.data('percentage_of_sale'))
                $('#edit_quality').val(button.data('quality')).trigger('change')
                @foreach (locales() as $key => $value)
                $('#edit_name_{{ $key }}').val(button.data('name_{{ $key }}'))
                $('#edit_details_{{ $key }}').val(button.data('details_{{ $key }}'))
                @endforeach
            });
        });
    </script>
@endsection
