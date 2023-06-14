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
                                    <h4 class="card-title">@lang('edit') @lang('admin') <span
                                                class="text-info">{{$admin->name}}</span></h4>
                                </div>

                            </div>
                            <div class="card-body">
                                <form action="{{ route('admin.update') }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <div class="row">

                                        <input type="hidden" name="id" id="id" value="{{$admin->id}}"
                                               class="form-control"/>
                                        <div class="modal-body">
                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label for="name">@lang('name')</label>
                                                    <input type="text" class="form-control"
                                                           placeholder="@lang('name')"
                                                           name="name" value="{{$admin->name}}">
                                                    <div class="invalid-feedback"></div>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label for="email">@lang('email')</label>
                                                    <input type="email" class="form-control"
                                                           placeholder="@lang('email')"
                                                           name="email" value="{{$admin->email}}">
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
                                            <div class="mb-5">
                                                <label>Roles</label>
                                                <div class="col-xs-12 col-sm-12 col-md-12">
                                                    <div class="form-group">
                                                        <strong>Role:</strong>
                                                        {!! Form::select('role[]', $roles,$userRole, array('class' => 'form-control','multiple')) !!}
                                                    </div>
                                                </div>
{{--                                                <div id="roleadmin">--}}
{{--                                                    @foreach($roles as $itemm)--}}
{{--                                                        <div class="form-check">--}}
{{--                                                            <input class="form-check-input" name="roles[]"--}}
{{--                                                                   type="checkbox" value="{{$itemm->id}}"--}}
{{--                                                                   id="flexCheckDefault" @checked(in_array($itemm->id, old('roles',$role_admin ))) >--}}
{{--                                                            <label class="form-check-label" for="flexCheckDefault">--}}
{{--                                                                {{$itemm->name}}--}}
{{--                                                            </label>--}}
{{--                                                        </div>--}}
{{--                                                    @endforeach--}}
{{--                                                </div>--}}

                                            </div>
                                            <div class="modal-footer">
                                                <a type="button" href="{{route('admin.index')}}"
                                                   class="btn btn-secondary"
                                                   data-dismiss="modal">@lang('close')</a>
                                                <button class="btn btn-primary">@lang('save changes')</button>
                                            </div>
                                        </div>

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

