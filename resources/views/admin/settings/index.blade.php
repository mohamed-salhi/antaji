@extends('admin.part.app')
@section('title')
    @lang('settings')
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
                        <h2 class="content-header-title float-left mb-0">@lang('settings')</h2>
                        <div class="breadcrumb-wrapper">
                            <ol class="breadcrumb">
                                {{--                                <li class="breadcrumb-item"><a href="{{ route('admin.index') }}">@lang('home')</a>--}}
                                {{--                                </li>--}}
                                <li class="breadcrumb-item"><a
                                        href="{{ route('settings.about_application') }}">@lang('settings')</a>
                                </li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="content-body">
            <form action="{{ route('settings.index') }}" method="POST" id="add-mode-form" class="add-mode-form"
                  enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <div class="col-3">
                        <div class="form-group">
                            <label for="commission">@lang('commission')</label>
                            <input id="commission" name="commission" type="text" value="{{@$settings->commission }}"
                                   class="form-control"
                                   placeholder="@lang('commission')">
                        </div>
                    </div>


                </div>
                <div class="col-3" style="margin-top: 20px">
                    <div class="modal-footer float-left">
                        <button type="submit" class="btn btn-primary">@lang('save')</button>
                    </div>
                </div>

            </form>

        </div>
    </div>
@endsection
@section('scripts')
    <script type="text/javascript">
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    </script>

@endsection
