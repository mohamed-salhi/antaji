@extends('admin.part.app')
@section('title')
    @lang('products')
@endsection
@section('styles')
    <style>
        input[type="checkbox"] {
            transform: scale(1.5);
        }
        .input-images-2 .image-uploader .uploaded .uploaded-image img:hover  {
            transform: scale(2.2); /* تكبير الصورة عند تحويم المؤشر */

        }
    </style>
@endsection
@section('content')
    <div class="content-wrapper">
        <div class="content-header row">
            <div class="content-header-left col-md-9 col-12 mb-2">
                <div class="row breadcrumbs-top">
                    <div class="col-12">
                        <h2 class="content-header-title float-left mb-0">@lang('products')</h2>
                        <div class="breadcrumb-wrapper">
                            <ol class="breadcrumb">
                                {{--                                <li class="breadcrumb-item"><a href="{{ route('admin.index') }}">@lang('home')</a>--}}
                                {{--                                </li>--}}
                                <li class="breadcrumb-item"><a
                                        href="{{ route('products.rent.index') }}">@lang('products')</a>
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
                                    <h4 class="card-title">@lang('products')</h4>
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
                                                <label for="s_name">@lang('product name')</label>
                                                <input id="s_name" type="text"
                                                       class="search_input form-control"
                                                       placeholder="@lang('product name')">
                                            </div>
                                        </div>

                                        <div class="col-3">
                                            <div class="form-group">
                                                <label for="s_user_name">@lang('user name')</label>
                                                <input id="s_user_name" type="text"
                                                       class="search_input form-control"
                                                       placeholder="@lang('user name')">
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

                                        <div class="col-3">
                                            <div class="form-group">
                                                <label for="s_category_uuid">@lang('categories')</label>
                                                <select  name="category_uuid" id="s_category_uuid"
                                                        class="search_input form-control">
                                                    <option selected
                                                            disabled>@lang('select')  @lang('categories')</option>
                                                    @foreach ($categories as $item)
                                                        <option value="{{ $item->uuid }}"> {{ $item->name }} </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-3">
                                            <div class="form-group">
                                                <label for="s_sub_category_uuid">@lang('sub categories')</label>
                                                <select name="sub_category_uuid" id="s_sub_category_uuid"
                                                        class="search_input form-control">
                                                    <option selected
                                                            disabled>@lang('select')  @lang('sub categories')</option>
                                                </select>
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
                                        <th>@lang('product owner')</th>
                                        <th>@lang('product name')</th>
                                        <th>@lang('price')</th>
                                        <th>@lang('category')</th>
                                        <th>@lang('sub category')</th>
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
        <div class="modal-dialog  modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">@lang('add')</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('products.rent.store') }}" method="POST" id="add-mode-form" class="add-mode-form"
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
                        <div class="col-12">
                            <div class="form-group">
                                <label for="">@lang('categories')</label>
                                <select name="category_uuid" id="category_uuid" class="select form-control"
                                        data-select2-id="select2-data-1-bgy2" tabindex="-1" aria-hidden="true">
                                    <option selected disabled>@lang('select') @lang('categories')</option>
                                    @foreach ($categories as $item)
                                        <option value="{{ $item->uuid }}"> {{ $item->name }} </option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label for="">@lang('sub category')</label>
                                <select name="sub_category_uuid" id="sub_category_uuid" class="select form-control"
                                        data-select2-id="select2-data-1-bgy2" tabindex="-1" aria-hidden="true">
                                    <option selected disabled>Select @lang('sub category')</option>

                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
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
                            <div class="form-group">
                                <label for="about">@lang('address')
                                </label>
                                <input type="text" class="form-control" placeholder="@lang('address')"
                                       name="address" id="address">
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header bg-dark">
                                    <h4 class="m-0"  style="color: white">@lang('specifications')</h4>
                                </div>
                                <div class="card-body">
                                    <div class="text-right mt-3">
                                        <a class="add_row btn btn-sm btn-dark">@lang('Add Row')</a>
                                    </div>
                                    <br>
                                    <br>
                                    <div class="row_data">
                                        <div class="row mb-3">
                                            <div class="col-md-11">
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <input type="text" name="fname[]" class="form-control"
                                                               placeholder="@lang('key')" required>
                                                    </div>

                                                    <div class="col-md-4">
                                                        <input type="text" name="fvalue[]" class="form-control"
                                                               placeholder="@lang('value')" required>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-1">
                                                <a class="btn btn-danger w-100 remove_row"><i class="fas fa-times"></i></a>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="input-field">
                                <label class="active">@lang('Photos')</label>
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
        <div class="modal-dialog  modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">@lang('edit')</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('products.rent.update') }}" method="POST" id="form_edit" class=""
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
                                <input type="number" class="form-control"
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
                            <div class="form-group">
                                <label for="edit_category_uuid">@lang('categories')</label>
                                <select class="form-control" id="edit_category_uuid" name="category_uuid" required>
                                    <option value="">@lang('select') @lang('categories')</option>
                                    @foreach ($categories as $item)
                                        <option value="{{ $item->uuid }}"> {{ $item->name }} </option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label for="">@lang('sub category')</label>
                                <select name="sub_category_uuid" id="edit_sub_category_uuid" class="select form-control"
                                        data-select2-id="select2-data-1-bgy2" tabindex="-1" aria-hidden="true">
                                    <option selected disabled>Select @lang('sub category')</option>

                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="spe">
                            <div class="data"><div class="col-md-12">
                                    <div class="card">
                                        <div class="card-header bg-dark">
                                            <h4 class="m-0"  style="color: white">@lang('specifications')</h4>
                                        </div>
                                        <div class="card-body">
                                            <div class="text-right mt-3">
                                                <a id="addRow" class="add_row btn btn-sm btn-dark">@lang('Add Row')</a>
                                            </div>
                                            <br>
                                            <br>
                                            <div class="row_data">

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="add_images">
                            <div class="col-12 edit_images">
                                <div class="input-field">
                                    <label class="active">@lang('Photos')</label>
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
                url: '{{ route('products.rent.indexTable', app()->getLocale()) }}',
                data: function (d) {
                    d.status = $('#s_status').val();
                    d.sub_category_uuid = $('#s_sub_category_uuid').val();
                    d.category_uuid = $('#s_category_uuid').val();
                    d.user_name = $('#s_user_name').val();
                    d.price = $('#s_price').val();
                    d.name = $('#s_name').val();

                }
            },
            dom: '<"row"<"col-md-12"<"row"<"col-md-6"B><"col-md-6"f> > ><"col-md-12"rt> <"col-md-12"<"row"<"col-md-5"i><"col-md-7"p>>> >',
            buttons: [
                {
                    extend: 'excel',
                    text: '<span class="fa fa-file-excel-o"></span> @lang('Excel Export')',
                    exportOptions: {
                        columns: [1,2,3,4,5,6],
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
                    data: 'category_name',
                    name: 'category_name',
                    orderable: false,
                    searchable: false,
                },
                {
                    data: 'sub_category_name',
                    name: 'sub_category_name',
                    orderable: false,
                    searchable: false,
                }, {
                    data: 'status',
                    name: 'status'
                },
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
            $('select[name="category_uuid"]').on('change', function () {
                var category_uuid = $(this).val();
                var sub_category_uuid = $(this).data('sub_category_uuid');
                console.log(category_uuid);
                if (category_uuid) {
                    $.ajax({
                        url: "rent/category" + "/" + category_uuid,
                        type: "GET",
                        dataType: "json",
                        success: function (data) {
                            console.log('ccc')
                            $('select[name="sub_category_uuid"]').empty();
                            $('select[name="sub_category_uuid"]').append(`
                                 <option selected  disabled>Select @lang('sub category')</option>
                                 `)
                            $.each(data, function (key, value) {
                                $('select[name="sub_category_uuid"]').append('<option value="' +
                                    key + '">' + value + '</option>');
                            });
                            if (sub_category_uuid != null) {
                                $('#edit_sub_category_uuid').val(sub_category_uuid).trigger('change')
                            }
                        },
                    });
                } else {
                    console.log('AJAX load did not work');
                }
            });
        });
console.log()
        $(document).ready(function () {
            $(document).on('click', '.btn_edit', function (event) {
                $('input').removeClass('is-invalid');
                $('.invalid-feedback').text('');
                event.preventDefault();
                var button = $(this)
                var uuid = button.data('uuid')
                $('#uuid').val(uuid);
                $('#edit_name').val(button.data('name'))
                console.log(button.data('name'))
                $('#edit_price').val(button.data('price'))
                $('#edit_address').val(button.data('address'))

                $('#edit_details').val(button.data('details'))
                $('#edit_user_uuid').val(button.data('user_uuid')).trigger('change');
                $('#edit_category_uuid').attr('data-sub_category_uuid', button.data('sub_category_uuid'))
                $('#edit_category_uuid').val(button.data('category_uuid')).trigger('change');
                let fileArray = button.data('images').split(',');
                let fileArrayUuids = button.data('images_uuid').split(',');
                let fileArrayKey = button.data('key').split(',');
                let fileArrayVlaue = button.data('value').split(',');
                console.log(fileArrayKey.length)
                if(fileArrayKey.length>=0){
                    $.each(fileArrayKey, function (index, fileName) {
                        $('.row_data').append(`<div class="row mb-3">
                        <div class="col-md-11">
                            <div class="row">
                                <div class="col-md-4">
                                    <input type="text" name="fname[]" value="${fileArrayKey[index]}" class="form-control" placeholder="{{__('key')}}" required>
                                </div>

                                <div class="col-md-4">
                                    <input type="text" name="fvalue[]" value="${fileArrayVlaue[index]}" class="form-control" placeholder="{{__('value')}}" required>
                                </div>



                            </div>
                        </div>
                        <div class="col-md-1">
                            <a class="btn btn-danger w-100 remove_row"><i class="fas fa-times"></i></a>
                        </div>
                    </div>`);
                    })

                }
                console.log(fileArrayKey)



                var preloaded = []; // Empty array
                $.each(fileArray, function (index, fileName) {
                    var object = {
                        id: fileArrayUuids[index],
                        src: '{{ url('/') }}/upload/product/images/' + fileName
                    };
                    preloaded.push(object)
                })
                console.log(preloaded)
                $('.input-images-2').imageUploader({
                    preloaded: preloaded,
                    imagesInputName: 'images[]',
                    preloadedInputName: 'delete_images',
                    maxSize: 2 * 1024 * 1024,
                    maxFiles: 20,
                    with: 100
                });
            });
        });

        $('.add_row').click(function(e) {
            e.preventDefault();
            console.log('ddd')
            const row = `<div class="row mb-3">
                        <div class="col-md-11">
                            <div class="row">
                                <div class="col-md-4">
                                    <input type="text" name="fname[]" class="form-control" placeholder="{{__('key')}}" required>
                                </div>

                                <div class="col-md-4">
                                    <input type="text" name="fvalue[]" class="form-control" placeholder="{{__('value')}}" required>
                                </div>



                            </div>
                        </div>
                        <div class="col-md-1">
                            <a class="btn btn-danger w-100 remove_row"><i class="fas fa-times"></i></a>
                        </div>
                    </div>`;

            $('.row_data').append(row);
            $('body').on('click', '.remove_row', function(e) {
                e.preventDefault();
                $(this).parent().parent().remove();
            })
        })
    </script>
@endsection
