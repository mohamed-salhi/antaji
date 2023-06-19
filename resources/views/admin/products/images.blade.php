@extends('admin.part.app')
@section('title')
    @lang('images')
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
                        <h2 class="content-header-title float-left mb-0">@lang('images')</h2>
                        <div class="breadcrumb-wrapper">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a
                                        href="{{ route('products.leasing.index') }}">@lang('projects')</a>
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
                                    <h4 class="card-title">@lang('projects')</h4>
                                </div>
                                <div class="text-right">
                                    <div class="form-group">
                                        <button class="btn btn-outline-primary button_modal" type="button"
                                                data-toggle="modal" id=""
                                                data-target="#full-modal-stem"><span><i
                                                    class="fa fa-plus"></i>@lang('add')</span>
                                        </button>

                                        <button id="btn_delete_all"
                                                class="btn_delete_all btn btn-outline-danger " type="button">
                                            <span><i class="fa fa-lg fa-trash-alt" aria-hidden="true"></i> @lang('delete')</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <form id="search_form">
                                    <div class="col-3" style="margin-top: 20px">

                                    </div>
                                </form>
                            </div>
                            <div class="table-responsive card-datatable" style="padding: 20px">
                                <table class="table" id="datatable">
                                    <thead>
                                    <tr>
                                        <th><input name="select_all" id="example-select-all" type="checkbox"
                                                   onclick="CheckAll('box1', this)"/></th>
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
    <div class="modal fade" class="full-modal-stem" id="full-modal-stem"  tabindex="-1" role="dialog"
         aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">@lang('add')</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('products.leasing.images.store') }}" method="POST" id="add_model_form" class="add-mode-form"
                      enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                   <input type="hidden" value="{{$uuid}}" name="uuid">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="images">@lang('images')</label>
                                <input type="file" multiple accept="image/*" class="form-control"
                                       placeholder="@lang('images')" name="images[]" id="image">
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">@lang('close')</button>
                        <button class="btn btn-primary">@lang('add')</button>
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
                <form action="{{ route('products.leasing.images.update') }}" method="POST" id="form_edit" class=""
                      enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="uuid" id="uuid" class="form-control"/>
                    <input type="hidden" name="imageable_id" id="imageable_id" class="form-control"/>
                    <div class="modal-body">
                        <div class="col-12">
                            <div class="form-group">
                                <br>
                                <input type="file" accept="image/*" name="images" class="file-input">
                                <div class="image">
                                    <img class="image-preview" alt="...">
                                </div>
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
                url: '{{ route('products.leasing.images.imageIndexTable',$uuid) }}',
                data: function (d) {

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
                    data: 'image',
                    name: 'image',
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
                {{--                @endif--}}
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
                $('#imageable_id').val(button.data('imageable_id'));
                console.log(button.data('image'));
                $('.image-preview').attr('src', button.data('image'));


            });
        });
    </script>
@endsection
