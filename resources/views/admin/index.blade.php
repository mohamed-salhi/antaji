@extends('part.app')
@section('title')
    @lang('admins')
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
                        <h2 class="content-header-title float-left mb-0">@lang('admins')</h2>
                        <div class="breadcrumb-wrapper">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ url('/admin') }}">@lang('home')</a>
                                </li>
                                <li class="breadcrumb-item"><a href="{{ route('admin.index') }}">@lang('admins')</a>
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
                                    <h4 class="card-title">@lang('admins')</h4>
                                </div>
{{--                                @can('admin.create')--}}
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
                                <form id="search_form">
                                    <div class="row">


                                        <div class="col-3" style="margin-top: 20px">
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
                                        <th>@lang('status')</th>
{{--                                        @can('admin.delete'||'admin.update')--}}
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
                <form action="{{ route('admin.store') }}" method="POST" id="add-mode-form" class="add-mode-form"
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
                            <div class="form-group">
                                <label for="email">@lang('email')</label>
                                <input type="email" class="form-control"
                                       placeholder="@lang('email')"
                                       name="email">
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label for="password">@lang('password')</label>
                                <input type="password" class="form-control"
                                       placeholder="@lang('password')"
                                       name="password">
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
{{--                        <div class="mb-5">--}}
{{--                            <label>Roles</label>--}}
{{--                            <div class="col-xs-12 col-sm-12 col-md-12">--}}
{{--                                <div class="form-group">--}}
{{--                                    <strong>Role:</strong>--}}
{{--                                    {!! Form::select('role[]', $roles,[], array('class' => 'form-control','multiple')) !!}--}}
{{--                                </div>--}}
{{--                            </div>--}}
{{--                        </div>--}}
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary"
                                    data-dismiss="modal">@lang('close')</button>
                            <button class="btn btn-primary">@lang('add')</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal -->
{{--    <div class="modal fade" id="edit_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"--}}
{{--         aria-hidden="true">--}}
{{--        <div class="modal-dialog modal-dialog-centered" role="document">--}}
{{--            <div class="modal-content">--}}
{{--                <div class="modal-header">--}}
{{--                    <h5 class="modal-title" id="exampleModalLongTitle">@lang('edit')</h5>--}}
{{--                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">--}}
{{--                        <span aria-hidden="true">&times;</span>--}}
{{--                    </button>--}}
{{--                </div>--}}
{{--                <form action="{{ route('admin.update') }}" method="POST" id="form_edit" class=""--}}
{{--                      enctype="multipart/form-data">--}}
{{--                    @csrf--}}
{{--                    <input type="hidden" name="id" id="id" class="form-control"/>--}}
{{--                    <div class="modal-body">--}}
{{--                        <div class="col-12">--}}
{{--                            <div class="form-group">--}}
{{--                                <label for="name">@lang('name')</label>--}}
{{--                                <input type="text" class="form-control"--}}
{{--                                       placeholder="@lang('name')"--}}
{{--                                       name="name" id="edit-name">--}}
{{--                                <div class="invalid-feedback"></div>--}}
{{--                            </div>--}}
{{--                        </div>--}}
{{--                        <div class="col-12">--}}
{{--                            <div class="form-group">--}}
{{--                                <label for="email">@lang('email')</label>--}}
{{--                                <input type="email" class="form-control"--}}
{{--                                       placeholder="@lang('email')"--}}
{{--                                       name="email" id="edit-email">--}}
{{--                                <div class="invalid-feedback"></div>--}}
{{--                            </div>--}}
{{--                        </div>--}}
{{--                        <div class="col-12">--}}
{{--                            <div class="form-group">--}}
{{--                                <label for="password">@lang('password')</label>--}}
{{--                                <input type="password" class="form-control"--}}
{{--                                       placeholder="@lang('password')"--}}
{{--                                       name="password">--}}
{{--                                <div class="invalid-feedback"></div>--}}
{{--                            </div>--}}
{{--                        </div>--}}
{{--                        <div class="mb-5">--}}
{{--                            <label>Roles</label>--}}
{{--                            --}}{{--                            @php--}}
{{--                            --}}{{--                                $role_admin=$item->roles->pluck('id')->toArray();--}}
{{--                            --}}{{--                            @endphp--}}
{{--                            <div id="roleadmin">--}}
{{--                                @foreach($roles as $itemm)--}}
{{--                                    <div class="form-check">--}}
{{--                                        <input class="form-check-input" name="roles[]" type="checkbox"--}}
{{--                                               value="{{$itemm->id}}" id="flexCheckDefault">--}}
{{--                                        <label class="form-check-label" for="flexCheckDefault">--}}
{{--                                            {{$itemm->name}}--}}
{{--                                        </label>--}}
{{--                                    </div>--}}
{{--                                @endforeach--}}
{{--                            </div>--}}

{{--                        </div>--}}
{{--                        <div class="modal-footer">--}}
{{--                            <button type="button" class="btn btn-secondary"--}}
{{--                                    data-dismiss="modal">@lang('close')</button>--}}
{{--                            <button class="btn btn-primary">@lang('save changes')</button>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                </form>--}}
{{--            </div>--}}
{{--        </div>--}}
{{--    </div>--}}
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
                url: '{{route('admin.indexTable',app()->getLocale())}}',
                data: function (d) {
                    d.name = $('#s_name').val();
                }
            },
            columns: [
                {
                    "render": function (data, type, full, meta) {
                        return `<td><input type="checkbox"  value="${data}" class="box1" ></td>
`;
                    },
                    name: 'checkbox',
                    data: 'checkbox',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'name',
                    name: 'name',
                    orderable: true,
                    searchable: true
                },
                {
                    data: 'email',
                    name: 'email',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'status',
                    name: 'status',

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
                $('#edit-email').val(button.data('email'));
                $.ajax({
                    url: "admins" + "/" + id + "/" + "role",
                    type: "GET",
                    dataType: "json",
                    success: function (data) {
                        $.each(data, function (key, value) {
                            {{--$('#roleadmin').append(' <div class="form-check"> <input class="form-check-input" name="roles[]" type="checkbox" value="{{$itemm->id}}" id="flexCheckDefault" @checked(in_array($itemm->id, old('roles',$role_admin )))> <label class="form-check-label" for="flexCheckDefault">{{$itemm->name}}</label> </div>');--}}
                        });
                    },
                });

            });
        });
    </script>
@endsection
