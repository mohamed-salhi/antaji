@extends('admin.part.app')
@section('title')
    @lang('locations')
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
                        <h2 class="content-header-title float-left mb-0">@lang('locations')</h2>
                        <div class="breadcrumb-wrapper">
                            <ol class="breadcrumb">
                                {{--                                <li class="breadcrumb-item"><a href="{{ route('admin.index') }}">@lang('home')</a>--}}
                                {{--                                </li>--}}
                                <li class="breadcrumb-item"><a
                                        href="{{ route('locations.index') }}">@lang('locations')</a>
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
                                    <h4 class="card-title">@lang('locations')</h4>
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
                            <div class="card-body">
                                <form id="search_form">
                                    <div class="row">
                                        <div class="col-3">
                                            <div class="form-group">
                                                <label for="s_user_name">@lang('name') @lang('location owner')</label>
                                                <input id="s_user_name" type="text"
                                                       class="search_input form-control"
                                                       placeholder="@lang('name') @lang('location owner')">
                                            </div>
                                        </div>
                                        <div class="col-3">
                                            <div class="form-group">
                                                <label for="s_name">@lang('location name')</label>
                                                <input id="s_name" type="text"
                                                       class="search_input form-control"
                                                       placeholder="@lang('location name')">
                                            </div>
                                        </div>
                                        <div class="col-3">
                                            <div class="form-group">
                                                <label for="s_price">@lang('price')</label>
                                                <input id="s_price" type="text"
                                                       class="search_input form-control"
                                                       placeholder="@lang('price')">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="s_status">@lang('status')</label>
                                                <select name="s_status" id="s_status" class="search_input form-control">
                                                    <option selected disabled>@lang('select') @lang('status')</option>
                                                    <option value="1"> @lang('active') </option>
                                                    <option value="2"> @lang('inactive') </option>
                                                </select>
                                                <div class="invalid-feedback"></div>
                                            </div>
                                        </div>
                                        <div class="col-3">
                                            <div class="form-group">
                                                <label for="s_category_contents_uuid">@lang('categories')</label>
                                                <select name="s_category_contents_uuid" id="s_category_contents_uuid"
                                                        class="search_input form-control">
                                                    <option selected
                                                            disabled>@lang('select')  @lang('categories')</option>
                                                    @foreach ($category_contents as $item)
                                                        <option value="{{ $item->uuid }}"> {{ $item->name }} </option>
                                                    @endforeach
                                                </select>
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
                                        <th>@lang('location owner')</th>
                                        <th>@lang('location name')</th>
                                        <th>@lang('price')</th>
                                        <th>@lang('category')</th>
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
                <form action="{{ route('locations.store') }}" method="POST" id="add_model_form" class="add-mode-form"
                      enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">

                        <div class="col-12">
                            <div class="form-group">
                                <label for="name">@lang('name') </label>
                                <input type="text" class="form-control"
                                       placeholder="@lang('name')" name="name"
                                       id="name">
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label for="price">@lang('price') </label>
                                <input type="number" class="form-control"
                                       placeholder="@lang('price')" name="price"
                                       id="price">
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label for="price">@lang('details') </label>
                                <textarea class="form-control"
                                          placeholder="@lang('details')" name="details"
                                          id="details"></textarea>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        {{--                        <div class="col-12">--}}
                        {{--                            <div class="form-group">--}}
                        {{--                                <label for="">@lang('categories')</label>--}}
                        {{--                                <select name="category_contents_uuid" id="category_contents_uuid"--}}
                        {{--                                        class="select form-control"--}}
                        {{--                                        data-select2-id="select2-data-1-bgy2" tabindex="-1" aria-hidden="true">--}}
                        {{--                                    <option selected disabled>@lang('select') @lang('categories')</option>--}}
                        {{--                                    @foreach ($category_contents as $item)--}}
                        {{--                                        <option value="{{ $item->uuid }}"> {{ $item->name }} </option>--}}
                        {{--                                    @endforeach--}}
                        {{--                                </select>--}}
                        {{--                                <div class="invalid-feedback"></div>--}}
                        {{--                            </div>--}}
                        {{--                        </div>--}}
                        <div class="col-12">
                            <label class="form-label select-label">@lang('select'),@lang('categories')</label>
                            <select name="category_contents_uuid[]" class="select" multiple>
                                @foreach ($category_contents as $item)
                                    <option value="{{ $item->uuid }}"> {{ $item->name }} </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label for="">@lang('users')</label>
                                <select name="user_uuid" id="country_uuid" class="select form-control"
                                        data-select2-id="select2-data-1-bgy2" tabindex="-1" aria-hidden="true">
                                    <option selected disabled>@lang('select') @lang('users')</option>
                                    @foreach ($users as $item)
                                        <option value="{{ $item->uuid }}"> {{ $item->name }} </option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="input-field">
                                <label class="active">Photos</label>
                                <div class="input-images" style="padding-top: .5rem;"></div>
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
                <form action="{{ route('locations.update') }}" method="POST" id="form_edit"
                      enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="uuid" id="uuid" class="form-control"/>
                    <div class="modal-body">

                        <div class="col-12">
                            <div class="form-group">
                                <label for="name">@lang('name')</label>
                                <input type="text" class="form-control"
                                       placeholder="@lang('name')"
                                       name="name" id="edit_name">
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label for="price">@lang('price')</label>
                                <input type="text" class="form-control"
                                       placeholder="@lang('price')"
                                       name="price" id="edit_price">
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label for="details">@lang('details')</label>
                                <textarea type="text" class="form-control"
                                          placeholder="@lang('details')"
                                          name="details" id="edit_details"></textarea>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label for="user_uuid">@lang('users')</label>
                                <select class="form-control" id="edit_user_uuid" name="user_uuid" required>
                                    <option value="">@lang('select') @lang('users')</option>
                                    @foreach ($users as $item)
                                        <option value="{{ $item->uuid }}"> {{ $item->name }} </option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="col-12">
                            <label for="edit_category_contents_uuid" class="form-label select-label">@lang('select')
                                ,@lang('categories')</label>
                            <select id="edit_category_contents_uuid" name="category_contents_uuid[]" class="select"
                                    multiple>
                                @foreach ($category_contents as $item)
                                    <option value="{{ $item->uuid }}"> {{ $item->name }} </option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>
                <div class="add_images">
                    <div class="col-12 edit_images">
                        <div class="input-field">
                            <label class="active">Photos</label>
                            <div class="input-images-2" style="padding-top: .5rem;"></div>
                        </div>
                        <div class="invalid-feedback"></div>
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
@endsection
@section('scripts')

    <script src="https://cdn.ckeditor.com/ckeditor5/25.0.0/classic/ckeditor.js"></script>
    <script type="text/javascript">


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
                url: '{{ route('locations.indexTable', app()->getLocale()) }}',
                data: function (d) {
                    d.status = $('#s_status').val();
                    d.price = $('#s_price').val();
                    d.name = $('#s_name').val();
                    d.category_contents_uuid = $('#s_category_contents_uuid').val();
                    d.user_name = $('#s_user_name').val();
                }
            },
            dom: '<"row"<"col-md-12"<"row"<"col-md-6"B><"col-md-6"f> > ><"col-md-12"rt> <"col-md-12"<"row"<"col-md-5"i><"col-md-7"p>>> >',
            buttons: [
                {
                    extend: 'excel',
                    text: '<span class="fa fa-file-excel-o"></span> @lang('Excel Export')',
                    exportOptions: {
                        columns: [1,2,3,4,5],
                        modifier: {
                            search: 'applied',
                            order: 'applied'
                        }
                    }
                }
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
                    data: 'user_name',
                    name: 'user_name'
                },
                {
                    data: 'name',
                    name: 'name'
                },
                {
                    data: 'price',
                    name: 'price'
                },
                {
                    data: 'categories',
                    name: 'categories',
                    orderable: false,
                    searchable: false,
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


        $(document).ready(function () {

            $(document).on('click', '.btn_edit', function (event) {

                $('input').removeClass('is-invalid');
                $('.invalid-feedback').text('');
                event.preventDefault();
                var button = $(this)
                // console.log(button.data('images'))
                // console.log(button.data('images_uuid'))
                var uuid = button.data('uuid')
                $('#uuid').val(uuid);

// console.log(button.data('images_uuid').split(',') + '')
                $('#edit_name').val(button.data('name'))
                $('#edit_price').val(button.data('price'))
                $('#edit_details').val(button.data('details'))
                $('#edit_user_uuid').val(button.data('user_uuid')).trigger('change');
                var category_contents_uuids = button.data('category_contents_uuid') + '';
                if (category_contents_uuids.indexOf(',') >= 0) {
                    category_contents_uuids = button.data('category_contents_uuid').split(',');
                }
                $('#edit_category_contents_uuid').val(category_contents_uuids).trigger('change');


                let fileArray = button.data('images').split(',');
                let fileArrayUuids = button.data('images_uuid').split(',');

                console.log(fileArray);
                var preloaded = []; // Empty array
                $.each(fileArray, function (index, fileName) {
                    var object = {
                        id: fileArrayUuids[index],
                        src: '{{ url('/') }}/upload/location/images/' + fileName
                    };
                    preloaded.push(object)
                })
                console.log(preloaded)
                $('.input-images-2').imageUploader({
                    preloaded: preloaded,
                    imagesInputName: 'images[]',
                    preloadedInputName: 'delete_images',
                    maxSize: 2 * 1024 * 1024,
                    maxFiles: 10
                });
            });
        });
    </script>
@endsection
