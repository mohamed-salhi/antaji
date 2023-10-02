@extends('admin.part.app')
@section('title')
    @lang('role')
@endsection
@section('styles')
    <style>
        input[type="checkbox"] {
            transform: scale(1.5);
        }
    </style>
@endsection
@section('content')
    <div class="container-fluid">
        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <div class="col-12">
                        <h2 class="content-header-title float-left mb-0">@lang('roles')</h2>
                        <div class="breadcrumb-wrapper">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('admin.index') }}">@lang('home')</a>
                                </li>
                                <li class="breadcrumb-item"><a href="{{ route('roles.index') }}">@lang('roles')</a>
                                </li>
                            </ol>
                        </div>
                    </div>

                </div>
            </div>
        </div>
        <!-- end page title -->

        <!-- Start Page-content-Wrapper -->
        <div class="page-content-wrapper">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <h3 class="m-0"></h3>
                                   <button class="btn btn-outline-primary button_modal" type="button"
                                                data-toggle="modal" id=""
                                                data-target="#full-modal-stem"><span><i
                                                    class="fa fa-plus"></i>@lang('add')</span>
                                        </button>
                            </div>

                            <br><br>
                            <div class="table-rep-plugin">

                                <div class="table-responsive card-datatable" style="padding: 20px">
                                    <table class="table" id="datatable">
                                        <thead>
                                        <tr>
{{--                                            <th><input name="select_all" id="example-select-all" type="checkbox"--}}
{{--                                                       onclick="CheckAll('box1', this)"/></th>--}}
                                            <th>@lang('name')</th>

                                            {{--                                        @can('user.delete'||'user.update')--}}
                                            <th style="width: 225px;">@lang('actions')</th>
                                            {{--                                        @endcan--}}
                                        </tr>
                                        </thead>
                                        <tbody></tbody>

                                    </table>
                                </div>


                            </div>

                        </div>
                    </div>
                </div>
                <!-- End Col -->
            </div>
            <!-- End Row -->

        </div>
        <!-- End Page-content-wrapper -->

    </div>
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
                <form action="{{ route('roles.store') }}" method="POST" id="add-mode-form" class="add-mode-form"
                      enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="col-12">
                            <div class="form-group">
                                <label for="name">@lang('name')</label>
                                <input type="text" class="form-control"
                                       placeholder="@lang('name')"
                                       name="name">
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="form-label select-label">@lang('select'),@lang('tags')</label>
                            <select name="permissions[]" id="permissions" class="select" multiple>
                                @foreach ($permissions as $item)
                                    <option value="{{ $item->id }}"> {{  __($item->name) }} </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-primary done">@lang('save')</button>

                            <button type="button" class="btn btn-secondary"
                                    data-dismiss="modal">@lang('close')</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>



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
                <form action="{{ route('roles.update') }}" method="POST" id="form_edit" class=""
                      enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="id" id="id" class="form-control"/>
                    <div class="modal-body">
                        <div class="col-12">
                            <div class="form-group">
                                <label for="name">@lang('name')</label>
                                <input type="text" class="form-control"
                                       placeholder="@lang('name')"
                                       name="name" id="edit-name">
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>

                        <div class="col-12">
                            <label class="form-label select-label">@lang('select'),@lang('tags')</label>
                            <select name="permissions[]" id="edit_permissions" class="select" multiple>
                                @foreach ($permissions as $item)
                                    <option value="{{ $item->id }}"> {{ __($item->name) }} </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-primary done">@lang('save')</button>

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
                url: '{{route('roles.indexTable',app()->getLocale())}}',
                data: function (d) {
                    d.name = $('#s_name').val();
                }
            },
            columns: [
//                 {
//                     "render": function (data, type, full, meta) {
//                         return `<td><input type="checkbox"  value="${data}" class="box1" ></td>
// `;
//                     },
//
//                     name: 'checkbox',
//                     data: 'checkbox',
//                     orderable: false,
//                     searchable: false
//                 },
                {
                    data: 'name',
                    name: 'name',
                    orderable: true,
                    searchable: true
                },
                    {{--                    @can('admin.delete'||'fuelType.update')--}}
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                },
                {{--                @endcan--}}
            ]

        });


        $(document).ready(function () {
            $(document).on('click', '.btn_edit', function (event) {
                $('input').removeClass('is-invalid');
                $('.invalid-feedback').text('');
                event.preventDefault();
                var button = $(this)
                var id = button.data('id')
                $('#id').val(id);
                $('#edit-name').val(button.data('name'));
                var permissions = button.data('permissions') + '';
                if (permissions.indexOf(',') >= 0) {
                    permissions = button.data('permissions').split(',');
                }
                $('#edit_permissions').val(permissions).trigger('change');
                {{--$.ajax({--}}
                {{--    url: "managers" + "/" + id + "/" + "role",--}}
                {{--    type: "GET",--}}
                {{--    dataType: "json",--}}
                {{--    success: function (data) {--}}
                {{--        $.each(data, function (key, value) {--}}
                {{--            --}}{{--$('#roleadmin').append(' <div class="form-check"> <input class="form-check-input" name="roles[]" type="checkbox" value="{{$itemm->id}}" id="flexCheckDefault" @checked(in_array($itemm->id, old('roles',$role_admin )))> <label class="form-check-label" for="flexCheckDefault">{{$itemm->name}}</label> </div>');--}}
                {{--        });--}}
                {{--    },--}}
                {{--});--}}

            });
        });
    </script>
@endsection
