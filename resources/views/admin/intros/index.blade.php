@extends('admin.part.app')
@section('title')
    @lang('intros')
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
                        <h2 class="content-header-title float-left mb-0">@lang('intros')</h2>
                        <div class="breadcrumb-wrapper">
                            <ol class="breadcrumb">
{{--                                <li class="breadcrumb-item"><a href="{{ route('admin.index') }}">@lang('home')</a>--}}
{{--                                </li>--}}
                                <li class="breadcrumb-item"><a href="{{ route('intros.index') }}">@lang('Countrys')</a>
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
                                    <h4 class="card-title">@lang('intros')</h4>
                                </div>
{{--                                @can('place-create')--}}
                                    <div class="text-right">
                                        <div class="form-group">
                                            <button class="btn btn-outline-primary button_modal" type="button"
                                                    data-toggle="modal" id=""
                                                    data-target="#full-modal-stem"><span><i
                                                            class="fa fa-plus"></i>@lang('add')</span>
                                            </button>
                                        </div>
                                    </div>
{{--                                @endcan--}}
                            </div>
                            <div class="card-body">
{{--                                <form id="search_form">--}}
{{--                                    <div class="row">--}}
{{--                                        <div class="col-md-3">--}}
{{--                                            <div class="form-group">--}}
{{--                                                <label for="s_status">@lang('status')</label>--}}
{{--                                                <select name="s_status" id="s_status" class="search_input form-control">--}}
{{--                                                    <option selected disabled>@lang('select') @lang('status')</option>--}}
{{--                                                    <option value="1"> @lang('active') </option>--}}
{{--                                                    <option  value="2"> @lang('inactive') </option>--}}
{{--                                                </select>--}}
{{--                                                <div class="invalid-feedback"></div>--}}
{{--                                            </div>--}}
{{--                                        </div>--}}

{{--                                        <div class="col-3" style="margin-top: 20px">--}}
{{--                                            <button id="search_btn" class="btn btn-outline-info" type="submit">--}}
{{--                                                <span><i class="fa fa-search"></i> @lang('search')</span>--}}
{{--                                            </button>--}}
{{--                                            <button id="clear_btn" class="btn btn-outline-secondary" type="submit">--}}
{{--                                                <span><i class="fa fa-undo"></i> @lang('reset')</span>--}}
{{--                                            </button>--}}
{{--                                            <button id="btn_delete_all"--}}
{{--                                                    class="btn_delete_all btn btn-outline-danger " type="button">--}}
{{--                                                <span><i class="fa fa-lg fa-trash-alt" aria-hidden="true"></i> @lang('delete')</span>--}}
{{--                                            </button>--}}
{{--                                        </div>--}}
{{--                                    </div>--}}
{{--                                </form>--}}
                            </div>
                            <div class="table-responsive card-datatable" style="padding: 20px">
                                <table class="table" id="datatable">
                                    <thead>
                                    <tr>
                                        <th><input name="select_all" id="example-select-all" type="checkbox"
                                                   onclick="CheckAll('box1', this)"/></th>
                                            <th>@lang('title')</th>
                                            <th>@lang('sub title')</th>
                                            <th>@lang('image')</th>
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
                <form action="{{ route('intros.store') }}" method="POST" id="add_model_form" class="add-mode-form">
                    <div class="modal-body">
                        @foreach (locales() as $key => $value)
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="title_{{ $key }}">@lang('title') @lang($value)</label>
                                    <input type="text" class="form-control"
                                           placeholder="@lang('title') @lang($value)" name="title_{{ $key }}"
                                           id="title_{{ $key }}">
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>

                        @endforeach
                            @foreach (locales() as $key => $value)
                                <div class="col-12">
                                    <div class="form-group">
                                        <label for="sup_title_{{ $key }}">@lang('sub title') @lang($value)</label>
                                        <input type="text" class="form-control"
                                               placeholder="@lang('sub title') @lang($value)" name="sup_title_{{ $key }}"
                                               id="sup_title_{{ $key }}">
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>

                            @endforeach
                            <div class="col-12">
                                <div class="form-group">
                                    <br>
                                    <input type="file" accept="image/*" name="image" class="file-input">
                                <div class="image">
                                    <img class="image-preview">
                                </div>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">@lang('close')</button>
                        <button type="submit" class="btn btn-primary">@lang('add')</button>
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
                <form action="{{ route('intros.update') }}" method="POST" id="form_edit" class=""
                      enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="uuid" id="uuid" class="form-control"/>
                    <div class="modal-body">
                        @foreach (locales() as $key => $value)
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="title_{{ $key }}">@lang('title') @lang($value)</label>
                                    <input type="text" class="form-control"
                                           placeholder="@lang('title') @lang($value)"
                                           name="title_{{ $key }}" id="edit_title_{{ $key }}">
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                        @endforeach
                            @foreach (locales() as $key => $value)
                                <div class="col-12">
                                    <div class="form-group">
                                        <label for="sup_title_{{ $key }}">@lang('sub title') @lang($value)</label>
                                        <input type="text" class="form-control"
                                               placeholder="@lang('sub title') @lang($value)"
                                               name="sup_title_{{ $key }}" id="edit_sup_title_{{ $key }}">
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                            @endforeach
                            <div class="col-12">
                                <div class="form-group">
                                    <br>
                                    <input type="file" accept="image/*" name="image" class="file-input">
                                    <img class="image-preview" >
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary"
                                    data-dismiss="modal">@lang('close')</button>
                            <button class="btn btn-primary">@lang('save changes')</button>
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
             "bPaginate": false,
        "bFilter": false,
        "bInfo": false,

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
                url: '{{ route('intros.indexTable', app()->getLocale()) }}',

                data: function (d) {
                    d.status = $('#s_status').val();
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
                    data: 'title_translate',
                    name: 'title'
                },
                {
                    data: 'sup_title_translate',
                    name: 'sup_title'
                },
                {
                    "data": 'image',
                    "name": 'image',
                    render: function (data, type, full, meta) {
                        return `<img src="${data}" style="width:100px;height:100px;"  class="img-fluid img-thumbnail">`;
                    },
                    orderable: false,
                    searchable: false
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
            $(document).on('click', '.edit_btn', function (event) {
                event.preventDefault();
                $('input').removeClass('is-invalid');
                $('.invalid-feedback').text('');
                var button = $(this)
                var uuid = button.data('uuid');
                $('#uuid').val(uuid);
                @foreach (locales() as $key => $value)
                $('#edit_title_{{ $key }}').val(button.data('title_{{ $key }}'))
                $('#edit_sup_title_{{ $key }}').val(button.data('sup_title_{{ $key }}'))

                @endforeach
                $('.image-preview').attr('src', button.data('image'));

            });
        });
    </script>
@endsection
