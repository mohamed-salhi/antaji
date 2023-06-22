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
                                <li class="breadcrumb-item"><a href="{{ route('settings.policies_privacy') }}">@lang('settings')</a>
                                </li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="content-body">
@if(session('done'))
                <div class="alert alert-primary" role="alert" style="height: 50px">
                    <h1 class="text-success">done</h1>
                </div>
            @endif
            <section id="">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <div class="head-label">
                                    <h4 class="card-title">@lang('settings')</h4>
                                </div>

                            </div>
                            <div class="card-body">
                                <form action="{{route('settings.policies_privacy')}}" method="post">
                                    @csrf
                                  <div class="row " >
                                      <div>
                                          <h1 class="text-primary">@lang('policies privacy') @lang('Arabic')</h1>
                                          <textarea id="about_application" class="myTextarea1" name="policies_privacy_ar" >{{$settings->getTranslation('policies_privacy', 'ar') }}</textarea>
                                          <div class="invalid-feedback"></div>
                                      </div>

                                      <div>
                                          <h1 class="text-primary">@lang('policies privacy') @lang('English')</h1>
                                          <textarea id="policies_privacy" class="myTextarea3" name="policies_privacy_en">{{$settings->getTranslation('policies_privacy', 'en') }}</textarea>
                                          <div class="invalid-feedback"></div>
                                      </div>
                                  </div>
                                    <br>

                                    <br>
                                    <div class="modal-footer float-left">
                                        <button type="submit" class="btn btn-primary">@lang('save')</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

        </div>
    </div>
@endsection
@section('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/5.10.2/tinymce.min.js" integrity="sha512-MbhLUiUv8Qel+cWFyUG0fMC8/g9r+GULWRZ0axljv3hJhU6/0B3NoL6xvnJPTYZzNqCQU3+TzRVxhkE531CLKg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script type="text/javascript">
        tinymce.init({
            selector: '.myTextarea1',

        });
        tinymce.init({
            selector: '.myTextarea3',

        });


    </script>

@endsection
