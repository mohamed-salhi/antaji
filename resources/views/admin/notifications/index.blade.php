@extends('admin.part.app')
@section('title')
    @lang('notifications')
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
                        <h2 class="content-header-title float-left mb-0">@lang('notifications')</h2>
                        <div class="breadcrumb-wrapper">
                            <ol class="breadcrumb">
                                {{--                                <li class="breadcrumb-item"><a href="{{ route('admin.index') }}">@lang('home')</a>--}}
                                {{--                                </li>--}}
                                <li class="breadcrumb-item"><a
                                        href="{{ route('notifications.index') }}">@lang('notifications')</a>
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
                                    <h4 class="card-title">@lang('notifications')</h4>
                                </div>
                                {{--                                @can('place-create')--}}
                                <div class="text-right">
                                    <div class="form-group">
                                        <button class="btn btn-outline-primary button_modal" type="button"
                                                data-toggle="modal" id=""
                                                data-target="#full-modal-stem"><span><i
                                                    class="fa fa-plus"></i>@lang('send notification')</span>
                                        </button>

                                    </div>
                                </div>
                                {{--                                @endcan--}}
                            </div>

                            <div class="table-responsive card-datatable" style="padding: 20px">
                                <table class="table" id="datatable">
                                    <thead>
                                    <tr>
                                        <th><input name="select_all" id="example-select-all" type="checkbox"
                                                   onclick="CheckAll('box1', this)"/></th>
                                        <th>@lang('title')</th>
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
                <form action="{{ route('notifications.store') }}" method="POST" id="add_model_form" class="add-mode-form">
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
                                        <label for="content_{{ $key }}">@lang('content') @lang($value)</label>
                                        <textarea rows="4" type="text" class="form-control"
                                               placeholder="@lang('content') @lang($value)" name="content_{{ $key }}"
                                               id="content_{{ $key }}"></textarea>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                            @endforeach

                            <div class="col-12 col-md-12">
                                <label class="form-label" for="notification_according_to">@lang('Notification According To')</label>
                                <select class="form-control" id="notification_according_to" name="notification_according_to">
                                    <option selected="" disabled="">إختر</option>
                                    <option value="1">@lang('city')</option>
                                    <option value="2">@lang('users')</option>
                                </select>
                            </div>
                            <div class="col-12 col-md-12" id="cities_div" style="">
                                <label class="form-label" for="city_id">@lang('cities')</label>
                                <div class="position-relative"><div class="position-relative"><select class="form-control select2 select2-hidden-accessible" id="city_id" name="city_id[]" multiple="" tabindex="-1" aria-hidden="true" data-select2-id="city_id">
                                            @foreach ($cities as $item)
                                                <option value="{{ $item->uuid }}"> {{ $item->name }} </option>
                                            @endforeach
                                        </select><span class="select2 select2-container select2-container--default select2-container--focus" dir="ltr" data-select2-id="89" style="width: auto;"><span class="selection"><span class="select2-selection select2-selection--multiple" role="combobox" aria-haspopup="true" aria-expanded="false" tabindex="-1" aria-disabled="false"><ul class="select2-selection__rendered"><li class="select2-search select2-search--inline"><input class="select2-search__field" type="search" tabindex="0" autocomplete="off" autocorrect="off" autocapitalize="none" spellcheck="false" role="searchbox" aria-autocomplete="list" placeholder="" style="width: 0.75em;"></li></ul></span></span><span class="dropdown-wrapper" aria-hidden="true"></span></span></div></div>
                                <div class="demo-inline-spacing">
                                    <div class="form-check form-switch">
                                        <input type="checkbox" class="form-check-input" id="customSwitch1" value="1" name="all_cities">
                                        <label class="form-check-label" for="customSwitch1">إختيار الكل</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-md-12" id="users_div" style="display: none">
                                <label class="form-label" for="user_id">@lang('users')</label>
                                <div class="position-relative"><div class="position-relative"><select class="form-control select2 select2-hidden-accessible" id="user_id" name="user_id[]" multiple="" tabindex="-1" aria-hidden="true" data-select2-id="user_id">
                                            @foreach ($users as $item)
                                                <option value="{{ $item->uuid }}"> {{ $item->name }} </option>
                                            @endforeach
                                        </select><span class="select2 select2-container select2-container--default" dir="ltr" data-select2-id="162" style="width: auto;"><span class="selection"><span class="select2-selection select2-selection--multiple" role="combobox" aria-haspopup="true" aria-expanded="false" tabindex="-1" aria-disabled="false"><ul class="select2-selection__rendered"><li class="select2-search select2-search--inline"><input class="select2-search__field" type="search" tabindex="0" autocomplete="off" autocorrect="off" autocapitalize="none" spellcheck="false" role="searchbox" aria-autocomplete="list" placeholder="" style="width: 0.75em;"></li></ul></span></span><span class="dropdown-wrapper" aria-hidden="true"></span></span></div></div>
                                <div class="demo-inline-spacing">
                                    <div class="form-check form-switch">
                                        <input type="checkbox" class="form-check-input" name="all_users" value="1" id="customSwitch2">
                                        <label class="form-check-label" for="customSwitch2">إختيار الكل</label>
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



    <div class="modal fade" id="btn_details" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
         aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">@lang('details')</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                    <div class="modal-body">
                        @foreach (locales() as $key => $value)
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="title_{{ $key }}">@lang('title') @lang($value)</label>
                                    <input readonly type="text" class="form-control"
                                           placeholder="@lang('title') @lang($value)" name="title_{{ $key }}"
                                           id="details_title_{{ $key }}">
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                        @endforeach
                        @foreach (locales() as $key => $value)
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="content_{{ $key }}">@lang('content') @lang($value)</label>
                                    <textarea readonly rows="4" type="text" class="form-control"
                                              placeholder="@lang('content') @lang($value)" name="content_{{ $key }}"
                                              id="details_content_{{ $key }}"></textarea>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                        @endforeach



                    </div>

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
                url: '{{ route('notifications.indexTable', app()->getLocale()) }}',

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
                    data: 'title_translate',
                    name: 'title'
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
            $(document).on('click', '.btn_details', function (event) {
                $('input').removeClass('is-invalid');
                $('.invalid-feedback').text('');
                event.preventDefault();
                var button = $(this)
                var uuid = button.data('uuid')
                $('#uuid').val(uuid);
                $('#detais_').val(button.data('price'));
                @foreach (locales() as $key => $value)
                $('#details_title_{{ $key }}').val(button.data('title_{{ $key }}'))
                $('#details_content_{{ $key }}').val(button.data('content_{{ $key }}'))
                @endforeach
            });
        });


        $('#notification_according_to').on('change', function (){
            var value = $(this).val();
            if(value == 1){
                $('#cities_div').show();
                $('#users_div').hide();
                $('#user_id').val(null).trigger('change');
            }else {
                $('#cities_div').hide();
                $('#users_div').show();
                $('#city_id').prop('selectedIndex', 0);
            }
        });



    </script>
    <script>
        $('#notification_according_to').on('change', function (){
            var value = $(this).val();
            if(value == 1){
                $('#cities_div').show();
                $('#users_div').hide();
                $('#user_id').val(null).trigger('change');
            }else {
                $('#cities_div').hide();
                $('#users_div').show();
                $('#city_id').prop('selectedIndex', 0);
            }
        });

        $('#customSwitch1').on('change', function (){
            if($(this).is(":checked")){
                $("#city_id").val("").trigger('change')
                $('#city_id').prop('disabled', true);
            }else{
                $('#city_id').prop('disabled', false);
            }
        });

        $('#customSwitch2').on('change', function (){
            if($(this).is(":checked")){
                $("#user_id").val("").trigger('change')
                $('#user_id').prop('disabled', true);
            }else{
                $('#user_id').prop('disabled', false);
            }
        });
    </script>
@endsection
