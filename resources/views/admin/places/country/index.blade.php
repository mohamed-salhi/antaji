@extends('admin.part.app')
@section('title')
    @lang('Countrys')
@endsection
@section('styles')
    <style>
        input[type="checkbox"] {
            transform: scale(1.5);
        }
    </style>
@endsection
@section('content')
    <div class="content-wrapper">
        <div class="content-header row">
            <div class="content-header-left col-md-9 col-12 mb-2">
                <div class="row breadcrumbs-top">
                    <div class="col-12">
                        <h2 class="content-header-title float-left mb-0">@lang('Countrys')</h2>
                        <div class="breadcrumb-wrapper">
                            <ol class="breadcrumb">
{{--                                <li class="breadcrumb-item"><a href="{{ route('admin.index') }}">@lang('home')</a>--}}
{{--                                </li>--}}
                                <li class="breadcrumb-item"><a href="{{ route('countries.index') }}">@lang('Countrys')</a>
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
                                    <h4 class="card-title">@lang('Countrys')</h4>
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
                                                    data-status="1"   class="btn_status btn btn-outline-success " type="button">
                                                <span><i  aria-hidden="true"></i> @lang('activate')</span>
                                            </button>
                                            <button
                                                    data-status="0"  class="btn_status btn btn-outline-warning " type="button">
                                                <span><i  aria-hidden="true"></i> @lang('deactivate')</span>
                                            </button>
                                        </div>
                                    </div>
{{--                                @endcan--}}
                            </div>
                            <div class="card-body">
                                <form id="search_form">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="s_status">@lang('status')</label>
                                                <select name="s_status" id="s_status" class="search_input form-control">
                                                    <option selected disabled>@lang('select') @lang('status')</option>
                                                    <option value="1"> @lang('active') </option>
                                                    <option  value="2"> @lang('inactive') </option>
                                                </select>
                                                <div class="invalid-feedback"></div>
                                            </div>

                                        </div>
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
                                                <label for="s_key">@lang('key')</label>
                                                <input id="s_key" type="number"
                                                       class="search_input form-control"
                                                       placeholder="@lang('key')">
                                            </div>
                                        </div>
                                        <div class="col-3" style="margin-top: 20px">
                                            <button id="search_btn" class="btn btn-outline-info" type="submit">
                                                <span><i class="fa fa-search"></i> @lang('search')</span>
                                            </button>
                                            <button id="clear_btn" class="btn btn-outline-secondary" type="submit">
                                                <span><i class="fa fa-undo"></i> @lang('reset')</span>
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
                                            <th>@lang('key')</th>
                                            <th>@lang('image')</th>
{{--                                        @can('place-edit')--}}
                                            <th>@lang('status')</th>
{{--                                        @endcan--}}
{{--                                        @if(\Illuminate\Support\Facades\Auth::user()->can('place-edit')||\Illuminate\Support\Facades\Auth::user()->can('place-delete'))--}}
                                            <th style="width: 225px;">@lang('actions')</th>
{{--                                        @endif--}}
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
    <div class="modal fade" id="full-modal-stem" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
         aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">@lang('add')</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('countries.store') }}" method="POST" id="add_model_form" class="add-mode-form">
                    <div class="modal-body">
                        @foreach (locales() as $key => $value)
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="name_{{ $key }}">@lang('name') @lang($value)</label>
                                    <input type="text" class="form-control"
                                           placeholder="@lang('name') @lang($value)" name="name_{{ $key }}"
                                           id="name_{{ $key }}">
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                        @endforeach
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="phone">@lang('key')</label>
                                    <input type="text" class="form-control" placeholder="@lang('key')"
                                           name="key" id="key">
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            <div class="col-12">
                                <label for="icon">@lang('flag')</label>
                                <div>
                                    <div class="fileinput fileinput-exists"
                                         data-provides="fileinput">
                                        <div class="fileinput-preview thumbnail"
                                             data-trigger="fileinput"
                                             style="width: 200px; height: 150px;">
                                            <img id="flag"
                                                 src="{{asset('dashboard/app-assets/images/logo/placeholder.jpeg')}}"
                                                 alt=""/>
                                        </div>
                                        <div class="form-group">
                                                    <span class="btn btn-secondary btn-file">
                                                        <span class="fileinput-new"> @lang('select_image')</span>
                                                        <span class="fileinput-exists"> @lang('select_image')</span>
                                                        <input class="form-control" type="file" name="image">
                                                    </span>
                                            <div class="invalid-feedback" style="display: block;"></div>
                                        </div>
                                    </div>
                                </div>
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
                <form action="{{ route('countries.update') }}" method="POST" id="form_edit" class=""
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
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="phone">@lang('key')</label>
                                    <input type="text" class="form-control" placeholder="@lang('key')"
                                           name="key" id="edit_key">
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            <div class="col-12">
                                <label for="icon">@lang('flag')</label>
                                <div>
                                    <div class="fileinput fileinput-exists"
                                         data-provides="fileinput">
                                        <div class="fileinput-preview thumbnail"
                                             data-trigger="fileinput"
                                             style="width: 200px; height: 150px;">
                                            <img id="edit_src_image"
                                                 src="https://demo.opencart.com/image/cache/no_image-100x100.png"
                                                 alt=""/>
                                        </div>
                                        <div class="form-group">
                                                    <span class="btn btn-secondary btn-file">
                                                        <span class="fileinput-new"> @lang('select_image')</span>
                                                        <span class="fileinput-exists"> @lang('select_image')</span>
                                                        <input class="form-control" type="file" name="image">
                                                    </span>
                                            <div class="invalid-feedback" style="display: block;"></div>
                                        </div>
                                    </div>
                                </div>
                           </div>
                            <div class="modal-footer">
                                <button  class="btn btn-primary done">@lang('save')</button>

                                <button type="button" class="btn btn-secondary"
                                        data-dismiss="modal">@lang('close')</button>
                            </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Modal -->
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
                url: '{{ route('countries.indexTable', app()->getLocale()) }}',

                data: function (d) {
                    d.status = $('#s_status').val();
                    d.key = $('#s_key').val();
                    d.name = $('#s_name').val();
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
                    data: 'key',
                    name: 'key'
                },
                {
                    "data": 'image',
                    "name": 'image',
                    render: function (data, type, full, meta) {
                        return `<img src="${data}" style="width:50px;height:50px;" class="avatar avatar-sm me-3">`;
                    },
                    orderable: false,
                    searchable: false
                },
{{--                    @can('place-edit')--}}
                {
                    data: 'status',
                    name: 'status'
                },
{{--                    @endcan--}}
{{--                    @if(\Illuminate\Support\Facades\Auth::user()->can('place-edit')||\Illuminate\Support\Facades\Auth::user()->can('place-delete'))--}}
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: true
                },
{{--                @endif--}}
            ]

        });

        //Edit
        $(document).ready(function () {
            $(document).on('click', '.edit_btn', function (event) {
                event.preventDefault();
                $('input').removeClass('is-invalid');
                $('.invalid-feedback').text('');
                var button = $(this)
                var uuid = button.data('uuid');
                $('#uuid').val(uuid);
                @foreach (locales() as $key => $value)
                $('#edit_name_{{ $key }}').val(button.data('name_{{ $key }}'))
                @endforeach
                $('#edit_key').val(button.data('key'));
                $('#edit_src_image').attr('src', button.data('image'));

            });
        });
    </script>
@endsection
