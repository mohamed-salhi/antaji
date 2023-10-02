@extends('admin.part.app')
@section('title')
    @lang('users')
@endsection
@section('styles')
    <style>
        #map {
            height: 400px;
            width: 100%;
        }

        #edit_map {
            height: 400px;
            width: 100%;
        }

        body {
            margin-top: 20px;
        }

        .icon-box.medium {
            font-size: 20px;
            width: 50px;
            height: 50px;
            line-height: 50px;
        }

        .icon-box {
            font-size: 30px;
            margin-bottom: 33px;
            display: inline-block;
            color: #ffffff;
            height: 65px;
            width: 65px;
            line-height: 65px;
            background-color: #59b73f;
            text-align: center;
            border-radius: 0.3rem;
        }

        .social-icon-style2 li a {
            display: inline-block;
            font-size: 14px;
            text-align: center;
            color: #ffffff;
            background: #59b73f;
            height: 41px;
            line-height: 41px;
            width: 41px;
        }

        .rounded-3 {
            border-radius: 0.3rem !important;
        }

        .social-icon-style2 {
            margin-bottom: 0;
            display: inline-block;
            padding-left: 10px;
            list-style: none;
        }

        .social-icon-style2 li {
            vertical-align: middle;
            display: inline-block;
            margin-right: 5px;
        }

        a, a:active, a:focus {
            color: #616161;
            text-decoration: none;
            transition-timing-function: ease-in-out;
            -ms-transition-timing-function: ease-in-out;
            -moz-transition-timing-function: ease-in-out;
            -webkit-transition-timing-function: ease-in-out;
            -o-transition-timing-function: ease-in-out;
            transition-duration: .2s;
            -ms-transition-duration: .2s;
            -moz-transition-duration: .2s;
            -webkit-transition-duration: .2s;
            -o-transition-duration: .2s;
        }

        .text-secondary, .text-secondary-hover:hover {
            color: #59b73f !important;
        }

        .display-25 {
            font-size: 1.4rem;
        }

        .text-primary, .text-primary-hover:hover {
            color: #ff712a !important;
        }

        p {
            margin: 0 0 20px;
        }

        .mb-1-6, .my-1-6 {
            margin-bottom: 1.6rem;
        }
    </style>
    <style>
        #map2 {
            height: 200px;
        }
    </style>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.3/dist/leaflet.css"
          integrity="sha256-kLaT2GOSpHechhsozzB+flnD+zUyjE2LlfWPgU04xyI=" crossorigin=""/>
    <script src="https://unpkg.com/leaflet@1.9.3/dist/leaflet.js"
            integrity="sha256-WBkoXOwTeyKclOHuWtc+i2uENFpDZ9YPdf5Hf+D7ewM=" crossorigin=""></script>
@endsection
@section('content')
    <div class="content-wrapper">
        <div class="content-header row">
            <div class="content-header-left col-md-9 col-12 mb-2">
                <div class="row breadcrumbs-top">
                    <div class="col-12">
                        <h2 class="content-header-title float-left mb-0">@lang('users')</h2>
                        <div class="breadcrumb-wrapper">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('users.index') }}">@lang('home')</a>
                                </li>
                                <li class="breadcrumb-item"><a href="{{ route('users.index') }}">@lang('users')</a>
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
                                    <h4 class="card-title">@lang('users')</h4>
                                </div>

                                <div class="text-right">
                                    <div class="form-gruop">
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

                            </div>


                            <div class="card-body">
                                <form id="search_form">
                                    <div class="row">

                                        <div class="col-3">
                                            <div class="form-group">
                                                <label for="s_mobile">@lang('mobile')</label>
                                                <input id="s_mobile" type="text" class="search_input form-control"
                                                       placeholder="@lang('mobile')">
                                            </div>
                                        </div>
                                        <div class="col-3">
                                            <div class="form-group">
                                                <label for="s_name">@lang('name')</label>
                                                <input id="s_name" type="text" class="search_input form-control"
                                                       placeholder="@lang('name')">
                                            </div>
                                        </div>
                                        <div class="col-3">
                                            <div class="form-group">
                                                <label for="s_email">@lang('email')</label>
                                                <input id="s_email" type="text" class="search_input form-control"
                                                       placeholder="@lang('email')">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="s_country_uuid">@lang('country')</label>
                                                <select name="country_uuid" id="s_country_uuid"
                                                        class="search_input form-control"
                                                        data-select2-id="select2-data-1-bgy2" tabindex="-1"
                                                        aria-hidden="true">
                                                    <option selected disabled>Select @lang('country')</option>
                                                    @foreach ($countries as $itemm)
                                                        <option value="{{ $itemm->uuid }}"> {{ $itemm->name }} </option>
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <div class="invalid-feedback"></div>
                                            </div>
                                        </div>
                                        <div class="col-3">
                                            <div class="form-group">
                                                <label for="s_city_uuid">@lang('city')</label>
                                                <select name="city_uuid" id="s_city_uuid"
                                                        class="search_input form-control"
                                                        data-select2-id="select2-data-1-bgy2" tabindex="-1"
                                                        aria-hidden="true">
                                                    <option selected disabled> @lang('select') @lang('city')</option>

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
                                            <div class="form-group">
                                                <button id="search_btn" class="btn btn-outline-info" type="submit">
                                                    <span><i class="fa fa-search"></i> @lang('search')</span>
                                                </button>
                                                <button id="clear_btn" class="btn btn-outline-secondary" type="submit">
                                                    <span><i class="fa fa-undo"></i> @lang('reset')</span>
                                                </button>
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
                                        <th>@lang('name')</th>
                                        <th>@lang('email')</th>
                                        <th>@lang('mobile')</th>
                                        <th>@lang('image')</th>
                                        <th>@lang('country')</th>
                                        <th>@lang('city')</th>
                                        <th>@lang('status')</th>
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
            </section>

        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" class="full-modal-stem" id="full-modal-stem" tabindex="-1" role="dialog"
         aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">@lang('add')</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('users.store') }}" method="POST" id="add-mode-form" class="add-mode-form"
                      enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="">@lang('name')</label>
                                    <input type="text" class="form-control" placeholder="@lang('name')"
                                           name="name" id="">
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="mobile">@lang('mobile')</label>
                                    <input type="number" class="form-control" placeholder="@lang('mobile')"
                                           name="mobile" id="mobile">
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="mobile">@lang('intro')</label>
                                    <input type="number" class="form-control" placeholder="@lang('intro')"
                                           name="prefix" id="intro">
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>

                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="about">@lang('email')
                                    </label>
                                    <input type="email" class="form-control" placeholder="@lang('email')"
                                           name="email" id="email">
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="about">@lang('address')
                                    </label>
                                    <input type="text" class="form-control" placeholder="@lang('address')"
                                           name="address" id="address">
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="">@lang('country')</label>
                                    <select name="country_uuid" id="" class="select form-control"
                                            data-select2-id="select2-data-1-bgy2" tabindex="-1" aria-hidden="true">
                                        <option selected disabled>@lang('select') @lang('country')</option>
                                        @foreach ($countries as $itemm)
                                            <option value="{{ $itemm->uuid }}"> {{ $itemm->name }} </option>
                                        @endforeach
                                    </select>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="">@lang('city')</label>
                                    <select name="city_uuid" id="" class="select form-control"
                                            data-select2-id="select2-data-1-bgy2" tabindex="-1" aria-hidden="true">
                                        <option selected disabled>@lang('select') @lang('city')</option>

                                    </select>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="about">@lang('brief')
                                </label>
                                <textarea class="form-control" placeholder="@lang('brief')"
                                          name="brief" id="address"></textarea>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-6">
                                <label for="icon">@lang('personal photo')</label>
                                <div>
                                    <div class="fileinput fileinput-exists"
                                         data-provides="fileinput">
                                        <div class="fileinput-preview thumbnail"
                                             data-trigger="fileinput"
                                             style="width: 200px; height: 150px;">
                                            <img id="flag"
                                                 src="{{asset('dashboard/app-assets/images/placeholder.jpeg')}}"
                                                 alt=""/>
                                        </div>
                                        <div class="form-group">
                                                    <span class="btn btn-secondary btn-file">
                                                        <span class="fileinput-new"> @lang('select_image')</span>
                                                        <span class="fileinput-exists"> @lang('select_image')</span>
                                                        <input class="form-control" type="file" name="personal_photo">
                                                    </span>
                                            <div class="invalid-feedback" style="display: block;"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <label for="icon">@lang('cover image')</label>
                                <div>
                                    <div class="fileinput fileinput-exists"
                                         data-provides="fileinput">
                                        <div class="fileinput-preview thumbnail"
                                             data-trigger="fileinput"
                                             style="width: 200px; height: 150px;">
                                            <img id="flag"
                                                 src="{{asset('dashboard/app-assets/images/placeholder.jpeg')}}"
                                                 alt=""/>
                                        </div>
                                        <div class="form-group">
                                                    <span class="btn btn-secondary btn-file">
                                                        <span class="fileinput-new"> @lang('select_image')</span>
                                                        <span class="fileinput-exists"> @lang('select_image')</span>
                                                        <input class="form-control" type="file" name="cover_Photo">
                                                    </span>
                                            <div class="invalid-feedback" style="display: block;"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <br>
                                <input type="file" id="file-input" name="video">
                                <video id="video-preview" controls></video>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>

                        <div id="map"></div>
                        <input type="hidden" name="lat" id="lat">
                        <input type="hidden" name="lng" id="lng">
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


    <!-- Modal -->
    <div class="modal fade" id="edit_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
         aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">Modal title</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('users.update') }}" method="POST" id="form_edit" class="form_edit"
                      enctype="multipart/form-data">
                    @csrf

                    <input type="hidden" id="uuid" name="uuid">
                    <div class="modal-body">

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="">@lang('name')</label>
                                    <input type="text" class="form-control" placeholder="@lang('name')"
                                           name="name" id="edit_name">
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="mobile">@lang('mobile')</label>
                                    <input type="number" class="form-control" placeholder="@lang('mobile')"
                                           name="mobile" id="edit_mobile">
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="mobile">@lang('intro')</label>
                                    <input type="number" class="form-control" placeholder="@lang('intro')"
                                           name="prefix" id="edit_prefix">
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>


                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="about">@lang('email')
                                    </label>
                                    <input type="email" class="form-control" placeholder="@lang('email')"
                                           name="email" id="edit_email">
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="about">@lang('address')
                                    </label>
                                    <input type="text" class="form-control" placeholder="@lang('address')"
                                           name="address" id="edit_address">
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="">@lang('country')</label>
                                    <select name="country_uuid" id="edit_country_uuid" class="select form-control"
                                            data-select2-id="select2-data-1-bgy2" tabindex="-1" aria-hidden="true">
                                        <option selected disabled>Select @lang('country')</option>
                                        @foreach ($countries as $itemm)
                                            <option value="{{ $itemm->uuid }}"> {{ $itemm->name }} </option>
                                        @endforeach
                                    </select>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="">@lang('city')</label>
                                    <select name="city_uuid" id="edit_city_uuid" class="select form-control"
                                            data-select2-id="select2-data-1-bgy2" tabindex="-1" aria-hidden="true">
                                        <option selected disabled>Select @lang('city')</option>

                                    </select>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                        </div>
                        {{--                        <div class="row">--}}
                        {{--                            <div class="col-md-6">--}}
                        {{--                                <div class="form-group">--}}
                        {{--                                    <label for="">@lang('type')</label>--}}
                        {{--                                    <select name="type" id="edit_type" class="select form-control"--}}
                        {{--                                            data-select2-id="select2-data-1-bgy2" tabindex="-1" aria-hidden="true">--}}
                        {{--                                        <option selected disabled>Select @lang('type')</option>--}}

                        {{--                                        <option value="artist"> @lang('artist') </option>--}}
                        {{--                                        <option value="user"> @lang('user') </option>--}}
                        {{--                                    </select>--}}
                        {{--                                    <div class="invalid-feedback"></div>--}}
                        {{--                                </div>--}}
                        {{--                            </div>--}}
                        {{--                            <div class="col-md-6">--}}
                        {{--                                <div class="form-group">--}}
                        {{--                                    <label for="about">@lang('address')--}}
                        {{--                                    </label>--}}
                        {{--                                    <input type="text" class="form-control" placeholder="@lang('address')"--}}
                        {{--                                           name="address" id="edit_address">--}}
                        {{--                                    <div class="invalid-feedback"></div>--}}
                        {{--                                </div>--}}
                        {{--                            </div>--}}
                        {{--                        </div>--}}
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="about">@lang('brief')
                                </label>
                                <textarea class="form-control" placeholder="@lang('brief')"
                                          name="brief" id="edit_brief"></textarea>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-6">
                                <label for="icon">@lang('personal photo')</label>
                                <div>
                                    <div class="fileinput fileinput-exists"
                                         data-provides="fileinput">
                                        <div class="fileinput-preview thumbnail"
                                             data-trigger="fileinput"
                                             style="width: 200px; height: 150px;">
                                            <img id="edit_src_image_personal_photo"
                                                 src="{{asset('dashboard/app-assets/images/placeholder.jpeg')}}"
                                                 alt=""/>
                                        </div>
                                        <div class="form-group">
                                                    <span class="btn btn-secondary btn-file">
                                                        <span class="fileinput-new"> @lang('select_image')</span>
                                                        <span class="fileinput-exists"> @lang('select_image')</span>
                                                        <input class="form-control" type="file" name="personal_photo">
                                                    </span>
                                            <div class="invalid-feedback" style="display: block;"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <label for="icon">@lang('cover image')</label>
                                <div>
                                    <div class="fileinput fileinput-exists"
                                         data-provides="fileinput">
                                        <div class="fileinput-preview thumbnail"
                                             data-trigger="fileinput"
                                             style="width: 200px; height: 150px;">
                                            <img id="edit_src_image_cover_Photo"
                                                 src="{{asset('dashboard/app-assets/images/placeholder.jpeg')}}"
                                                 alt=""/>
                                        </div>
                                        <div class="form-group">
                                                    <span class="btn btn-secondary btn-file">
                                                        <span class="fileinput-new"> @lang('select_image')</span>
                                                        <span class="fileinput-exists"> @lang('select_image')</span>
                                                        <input class="form-control" type="file" name="cover_Photo">
                                                    </span>
                                            <div class="invalid-feedback" style="display: block;"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <br>
                                <input type="file" accept="video/*" name="video" id="file-input">
                                <video id="video-1" class="video-preview" controls></video>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>

                        <div id="edit_map"></div>
                        <input type="hidden" name="lat" id="edit_lat">
                        <input type="hidden" name="lng" id="edit_lng">
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




    </div>
@endsection
@section('scripts')
    <script>
        let map, edit_map;
        let marker, edit_marker;

        async function initMap() {
            // The location of Uluru
            const position = {lat: 24.121894767907012, lng: 46.74972295072583};
            // Request needed libraries.
            //@ts-ignore
            const {Map} = await google.maps.importLibrary("maps");

            // The map, centered at Uluru
            map = new Map(document.getElementById("map"), {
                zoom: 4,
                center: position,
                mapId: "DEMO_MAP_ID",
            });

            marker = new google.maps.Marker({
                map: map,
                position: position,
                title: "Center"
            });

            google.maps.event.addListener(map, 'click', function (e) {
                let myLatlng = e["latLng"];
                marker.setPosition(myLatlng);
                map.setCenter(myLatlng);
                $('#lat').val(myLatlng.lat)
                $('#lng').val(myLatlng.lng)

            });


            // The map, centered at Uluru
            edit_map = new Map(document.getElementById("edit_map"), {
                zoom: 4,
                center: position,
                mapId: "DEMO_MAP_ID",
            });

            edit_marker = new google.maps.Marker({
                map: edit_map,
                position: position,
                title: "Center"
            });


            google.maps.event.addListener(edit_map, 'click', function (e) {
                let myLatlng = e["latLng"];
                edit_marker.setPosition(myLatlng);
                edit_map.setCenter(myLatlng);
                $('#edit_lat').val(myLatlng.lat)
                $('#edit_lng').val(myLatlng.lng)
            });

        }

    </script>
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
            searching: false,
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
                url: '{{ route('users.indexTable', app()->getLocale()) }}',
                data: function (d) {
                    d.mobile = $('#s_mobile').val();
                    d.city_uuid = $('#s_city_uuid').val();
                    d.country_uuid = $('#s_country_uuid').val();
                    d.name = $('#s_name').val();
                    d.email = $('#s_email').val();
                    d.status = $('#s_status').val();

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
            columns: [
                {
                    "render": function (data, type, full, meta) {
                        return `<td><input type="checkbox" value="${data}" class="box1" ></td>
`;
                    },
                    name: 'checkbox',
                    data: 'checkbox',
                    orderable: false,
                    searchable: false
                },

                {
                    data: 'name',
                    name: 'name'
                },
                {
                    data: 'email',
                    name: 'email'
                },
                {
                    data: 'mobile',
                    name: 'mobile'
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

                {
                    data: 'country_name',
                    name: 'country_name'
                },
                {
                    data: 'city_name',
                    name: 'city_name'
                },
                {
                    data: 'status',
                    name: 'status'
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
            $(document).on('click', '.btn_edit', function (event) {

                $('input').removeClass('is-invalid');
                $('.invalid-feedback').text('');
                event.preventDefault();
                var button = $(this);
                console.log(button.data('mobile'))
                var uuid = button.data('uuid');
                $('#edit_country_uuid').attr('data-city_uuid', button.data('city_uuid'))
                $('#edit_country_uuid').val(button.data('country_uuid')).trigger('change');
                $('#edit_city').val(button.data('city'));
                $('#edit_type').val(button.data('type')).trigger('change');
                $('#edit_mobile').val(button.data('mobile'));
                $('#edit_prefix').val(button.data('intro'));

                console.log('d',button.data('mobile'),button.data('intro'))
                $('#edit_lat').val(button.data('lat'))
                $('#edit_email').val(button.data('email'))
                $('#edit_lng').val(button.data('lng'))
                $('#edit_name').val(button.data('name'))
                console.log(button.data('address'))
                $('#edit_address').val(button.data('address'))
                let latlng = {lat: parseFloat(button.data('lat')), lng: parseFloat(button.data('lng'))};
                edit_marker.setPosition(latlng);
                edit_map.setCenter(latlng);
                $('#video-1').attr('src', button.data('video'));
                $('#edit_src_image_personal_photo').attr('src', button.data('personal_photo'));
                $('#edit_src_image_cover_Photo').attr('src', button.data('cover_user'));
                $('#edit_brief').val(button.data('brief'))
                $('#edit_specialization_uuid').val(button.data('specialization_uuid')).trigger('change');
                $('#uuid').val(uuid);
                var category_contents_uuids = button.data('category_contents_uuid') + '';
                if (category_contents_uuids.indexOf(',') >= 0) {
                    category_contents_uuids = button.data('category_contents_uuid').split(',');
                }
                $('#edit_category_contents_uuid').val(category_contents_uuids).trigger('change');

            });
        });
    </script>
    <script>
        $('select[name="country_uuid"]').on('change', function () {
            var country_uuid = $(this).val();
            var city_uuid = $(this).data('city_uuid');
            if (country_uuid) {
                $.ajax({
                    url: "users/country" + "/" + country_uuid,
                    type: "GET",
                    dataType: "json",
                    success: function (data) {
                        console.log('ccc')
                        $('select[name="city_uuid"]').empty();
                        $('select[name="city_uuid"]').append(`
                                 <option selected  disabled>Select @lang('city')</option>
                                 `)
                        $.each(data, function (key, value) {
                            $('select[name="city_uuid"]').append('<option value="' +
                                key + '">' + value + '</option>');
                        });
                        if (city_uuid != null) {
                            $('#edit_city_uuid').val(city_uuid).trigger('change')
                        }
                    },
                });
            } else {
                console.log('AJAX load did not work');
            }
        });

    </script>


    <script async
            src="https://maps.googleapis.com/maps/api/js?key={{ GOOGLE_API_KEY }}&callback=initMap">
    </script>
@endsection
