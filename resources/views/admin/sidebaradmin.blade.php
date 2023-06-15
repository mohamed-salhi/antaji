<div class="main-menu menu-fixed menu-accordion menu-shadow menu-dark" data-scroll-to-active="true">
    <div class="navbar-header" style="height: unset !important;" style="background-color: #181E34" >
        <ul class="nav navbar-nav flex-row">
            <li class="nav-item mr-auto" style="margin: 0 auto;">
                <a class="navbar-brand" href="{{ url('admin') }}">
                            <span class="brand-logo"><img alt="logo"
                                                          src="{{ asset('dashboard/app-assets/images/logo/Antaji.png') }}"
                                                          style="max-width: 70% !important; margin: 0 auto; display: flex;"/>
                        </span>

                </a>
            </li>
        </ul>
    </div>
    <div class="shadow-bottom"></div>
    <div class="main-menu-content">
        <ul class="navigation navigation-main" id="main-menu-navigation" data-menu="menu-navigation">
{{--            <li class="nav-item {{ request()->routeIs('main.index') ? 'active' : '' }} ">--}}
{{--                <a class="d-flex align-items-center" href="{{ route('main.index') }}">--}}
{{--                    <i data-feather="file-text"></i><span--}}
{{--                        class="menu-title text-truncate">@lang('main')</span>--}}
{{--                </a>--}}
{{--            </li>--}}

{{--            @can('place-list')--}}
                <li class="nav-item has-sub  " style="">
                    <a class="d-flex align-items-center" href="#">
                        <i class="fa fa-home" style="font-size:24px;"></i>
                        <span class="menu-title text-truncate"
                              data-i18n="Charts">@lang('country'),@lang('city')</span></a>
                    <ul class="menu-content">
                        <li class="nav-item {{ request()->routeIs('countries.index') ? 'active' : '' }} ">
                            <a class="d-flex align-items-center" href="{{ route('countries.index') }}">
                                <i data-feather="file-text"></i><span
                                    class="menu-title text-truncate">@lang('countries')</span>
                            </a>
                        </li>
                        <li class="nav-item {{ request()->routeIs('city.index') ? 'active' : '' }} ">
                            <a class="d-flex align-items-center" href="{{ route('city.index') }}">
                                <i data-feather="file-text"></i><span
                                    class="menu-title text-truncate">@lang('cities')</span>
                            </a>
                        </li>


                    </ul>

                </li>
{{--            @endcan--}}

            <li class="nav-item has-sub  " style="">
                <a class="d-flex align-items-center" href="#">
                    <i class="fa fa-home" style="font-size:24px;"></i>
                    <span class="menu-title text-truncate"
                          data-i18n="Charts">@lang('intros')</span></a>
                <ul class="menu-content">
                    <li class="nav-item {{ request()->routeIs('intros.index') ? 'active' : '' }} ">
                        <a class="d-flex align-items-center" href="{{ route('intros.index') }}">
                            <i data-feather="file-text"></i><span
                                class="menu-title text-truncate">@lang('intros')</span>
                        </a>
                    </li>
                </ul>

            </li>
            <li class="nav-item has-sub  " style="">
                <a class="d-flex align-items-center" href="#">
                    <i class="fa fa-gear" style="font-size:24px;"></i>
                    <span class="menu-title text-truncate"
                          data-i18n="Charts">@lang('settings')</span></a>
                <ul class="menu-content">
                    <li class="nav-item {{ request()->routeIs('settings.index') ? 'active' : '' }} ">
                        <a class="d-flex align-items-center" href="{{ route('settings.index') }}">
                            <i data-feather="file-text"></i><span
                                class="menu-title text-truncate">@lang('settings')</span>
                        </a>
                    </li>

                </ul>

            </li>
            <li class="nav-item has-sub  " style="">
                <a class="d-flex align-items-center" href="#">
                    <i class="fa fa-home" style="font-size:24px;"></i>
                    <span class="menu-title text-truncate"
                          data-i18n="Charts">@lang('services')</span></a>
                <ul class="menu-content">
                    <li class="nav-item {{ request()->routeIs('services.index') ? 'active' : '' }} ">
                        <a class="d-flex align-items-center" href="{{ route('services.index') }}">
                            <i data-feather="file-text"></i><span
                                class="menu-title text-truncate">@lang('services')</span>
                        </a>
                    </li>
                </ul>

            </li>
            <li class="nav-item has-sub  " style="">
                <a class="d-flex align-items-center" href="#">
                    <i class="fa fa-home" style="font-size:24px;"></i>
                    <span class="menu-title text-truncate"
                          data-i18n="Charts">@lang('skills')</span></a>
                <ul class="menu-content">
                    <li class="nav-item {{ request()->routeIs('skills.index') ? 'active' : '' }} ">
                        <a class="d-flex align-items-center" href="{{ route('skills.index') }}">
                            <i data-feather="file-text"></i><span
                                class="menu-title text-truncate">@lang('skills')</span>
                        </a>
                    </li>
                </ul>

            </li>

            <li class="nav-item has-sub  " style="">
                <a class="d-flex align-items-center" href="#">
                    <i class="fa fa-home" style="font-size:24px;"></i>
                    <span class="menu-title text-truncate"
                          data-i18n="Charts">@lang('specializations')</span></a>
                <ul class="menu-content">
                    <li class="nav-item {{ request()->routeIs('specializations.index') ? 'active' : '' }} ">
                        <a class="d-flex align-items-center" href="{{ route('specializations.index') }}">
                            <i data-feather="file-text"></i><span
                                class="menu-title text-truncate">@lang('specializations')</span>
                        </a>
                    </li>
                </ul>

            </li>
            <li class="nav-item has-sub  " style="">
                @php $count= \App\Models\Contact::query()->where('view',1)->count() @endphp
                <a class="d-flex align-items-center" href="#">
                    <i class="fa fa-recycle" style="font-size:24px;"></i>
                    <span class="menu-title text-truncate"
                          data-i18n="Charts">@lang('contact us')
                      <i class="text-danger" id="counthelps">{{($count==0)?'':$count}} </i>
                    </span>
                </a>
                <ul class="menu-content">
                    <li class="nav-item {{ request()->routeIs('contacts.index') ? 'active' : '' }} ">
                        <a class="d-flex align-items-center" href="{{ route('contacts.index') }}">
                            <i data-feather="file-text"></i><span
                                class="menu-title text-truncate">@lang('contact us')</span>
                        </a>
                    </li>

                </ul>

            </li>
        </ul>
    </div>
</div>
