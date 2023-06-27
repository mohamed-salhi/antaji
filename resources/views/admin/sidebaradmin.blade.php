<div class="main-menu menu-fixed menu-accordion menu-shadow menu-dark" data-scroll-to-active="true">
    <div class="navbar-header" style="height: unset !important;" style="background-color: #181E34">
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
                    <i class="fa fa-map" style="font-size:24px;"></i>
                    <span class="menu-title text-truncate"
                          data-i18n="Charts">@lang('country'),@lang('city')</span></a>
                <ul class="menu-content">
                    <li class="nav-item {{ request()->routeIs('countries.index') ? 'active' : '' }} ">
                        <a class="d-flex align-items-center" href="{{ route('countries.index') }}">
                            <i data-feather="file-text"></i><span
                                class="menu-title text-truncate">@lang('countries')</span>
                        </a>
                    </li>
                    <li class="nav-item {{ request()->routeIs('cities.index') ? 'active' : '' }} ">
                        <a class="d-flex align-items-center" href="{{ route('cities.index') }}">
                            <i data-feather="file-text"></i><span
                                class="menu-title text-truncate">@lang('cities')</span>
                        </a>
                    </li>


                </ul>

            </li>
            {{--            @endcan--}}
            <li class="nav-item{{ request()->routeIs('services.index') ? 'active' : '' }} ">
                <a class="d-flex align-items-center" href="{{ route('services.index') }}">
                    <i class="fa fa-home" style="font-size:24px;"></i>
                    <span class="menu-title text-truncate"
                          data-i18n="Charts">@lang('our services')
                    </span>
                </a>
            </li>


            <li class="nav-item " {{ request()->routeIs('intros.index') ? 'active' : '' }} style="">
                <a class="d-flex align-items-center" href="{{ route('intros.index') }}">
                    <i class="fa fa-first-order " style="font-size:24px;"></i>
                    <span class="menu-title text-truncate"
                          data-i18n="Charts">@lang('intros')</span></a>
            </li>

            <li class="nav-item has-sub  " style="">
                <a class="d-flex align-items-center" href="#">
                    <i class="fa fa-gear" style="font-size:24px;"></i>
                    <span class="menu-title text-truncate"
                          data-i18n="Charts">@lang('locations')</span></a>
                <ul class="menu-content">
                    <li class="nav-item {{ request()->routeIs('locations.categories.index') ? 'active' : '' }} ">
                        <a class="d-flex align-items-center" href="{{ route('locations.categories.index') }}">
                            <i data-feather="file-text"></i><span
                                class="menu-title text-truncate">@lang('categories')</span>
                        </a>
                    </li>
                    <li class="nav-item {{ request()->routeIs('locations.index') ? 'active' : '' }} ">
                        <a class="d-flex align-items-center" href="{{ route('locations.index') }}">
                            <i data-feather="file-text"></i><span
                                class="menu-title text-truncate">@lang('locations')</span>
                        </a>
                    </li>

                </ul>

            </li>


            <li class="nav-item has-sub  " style="">
                <a class="d-flex align-items-center" href="#">
                    <i class="fa fa-gear" style="font-size:24px;"></i>
                    <span class="menu-title text-truncate"
                          data-i18n="Charts">@lang('services')</span></a>
                <ul class="menu-content">
                    <li class="nav-item {{ request()->routeIs('servings.categories.index') ? 'active' : '' }} ">
                        <a class="d-flex align-items-center" href="{{ route('servings.categories.index') }}">
                            <i data-feather="file-text"></i><span
                                class="menu-title text-truncate">@lang('categories')</span>
                        </a>
                    </li>
                    <li class="nav-item {{ request()->routeIs('servings.index') ? 'active' : '' }} ">
                        <a class="d-flex align-items-center" href="{{ route('servings.index') }}">
                            <i data-feather="file-text"></i><span
                                class="menu-title text-truncate">@lang('services')</span>
                        </a>
                    </li>

                </ul>

            </li>



            <li class="nav-item has-sub  " style="">
                <a class="d-flex align-items-center" href="#">
                    <i class="fa fa-gear" style="font-size:24px;"></i>
                    <span class="menu-title text-truncate"
                          data-i18n="Charts">@lang('products')</span></a>
                <ul class="menu-content">
                    <li class="nav-item {{ request()->routeIs('categories.index') ? 'active' : '' }}">
                        <a class="d-flex align-items-center" href="{{ route('categories.index') }}">
                            <i data-feather="file-text"></i><span
                                class="menu-title text-truncate">@lang('categories')</span>
                        </a>
                    </li>
                    <li class="nav-item {{ request()->routeIs('products.leasing.index') ? 'active' : '' }} ">
                        <a class="d-flex align-items-center" href="{{ route('products.leasing.index') }}">
                            <i data-feather="file-text"></i><span
                                class="menu-title text-truncate">@lang('leasing products')</span>
                        </a>
                    </li>
                    <li class="nav-item {{ request()->routeIs('products.sales.index') ? 'active' : '' }} ">
                        <a class="d-flex align-items-center" href="{{ route('products.sales.index') }}">
                            <i data-feather="file-text"></i><span
                                class="menu-title text-truncate">@lang('selling products')</span>
                        </a>
                    </li>
                </ul>

            </li>


            <li class="nav-item {{ request()->routeIs('courses.index') ? 'active' : '' }}" style="">
                <a class="d-flex align-items-center" href="{{ route('courses.index') }}">
                    <i class="fa fa-gear" style="font-size:24px;"></i>
                    <span class="menu-title text-truncate"
                          data-i18n="Charts">@lang('courses')</span></a>
            </li>


            <li class="nav-item {{ request()->routeIs('users.index') ? 'active' : '' }}  " style="">
                <a class="d-flex align-items-center" href="{{ route('users.index') }}">
                    <i class="fa fa-user" style="font-size:24px;"></i>
                    <span class="menu-title text-truncate"
                          data-i18n="Charts">@lang('users')</span></a>
            </li>
            <li class="nav-item has-sub  " style="">
                <a class="d-flex align-items-center" href="#">
                    <i class="fa fa-home" style="font-size:24px;"></i>
                    <span class="menu-title text-truncate"
                          data-i18n="Charts">@lang('artists')</span></a>
                <ul class="menu-content">
                    <li class="nav-item {{ request()->routeIs('artists.index') ? 'active' : '' }} ">
                        <a class="d-flex align-items-center" href="{{ route('artists.index') }}">
                            <i data-feather="file-text"></i><span
                                class="menu-title text-truncate">@lang('artists')</span>
                        </a>
                    </li>
                    <li class="nav-item {{ request()->routeIs('business.video.index') ? 'active' : '' }} ">
                        <a class="d-flex align-items-center" href="{{ route('business.video.index') }}">
                            <i data-feather="file-text"></i><span
                                class="menu-title text-truncate">@lang('Business Gallery Video')</span>
                        </a>
                    </li>
                    <li class="nav-item {{ request()->routeIs('business.images.index') ? 'active' : '' }} ">
                        <a class="d-flex align-items-center" href="{{ route('business.images.index') }}">
                            <i data-feather="file-text"></i><span
                                class="menu-title text-truncate">@lang('Business photo gallery')</span>
                        </a>
                    </li>
                    <li class="nav-item {{ request()->routeIs('skills.index') ? 'active' : '' }} ">
                        <a class="d-flex align-items-center" href="{{ route('skills.index') }}">
                            <i data-feather="file-text"></i><span
                                class="menu-title text-truncate">@lang('skills')</span>
                        </a>
                    </li>
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
            <li class="nav-item has-sub  " style="">
                <a class="d-flex align-items-center" href="#">
                    <i class="fa fa-gear" style="font-size:24px;"></i>
                    <span class="menu-title text-truncate"
                          data-i18n="Charts">@lang('settings')</span></a>
                <ul class="menu-content">
                    <li class="nav-item {{ request()->routeIs('settings.terms_conditions.index') ? 'active' : '' }} ">
                        <a class="d-flex align-items-center" href="{{ route('settings.terms_conditions') }}">
                            <i data-feather="file-text"></i><span
                                class="menu-title text-truncate">@lang('terms conditions')</span>
                        </a>
                    </li>
                    <li class="nav-item {{ request()->routeIs('settings.about_application.index') ? 'active' : '' }} ">
                        <a class="d-flex align-items-center" href="{{ route('settings.about_application') }}">
                            <i data-feather="file-text"></i><span
                                class="menu-title text-truncate">@lang('about application')</span>
                        </a>
                    </li>
                    <li class="nav-item {{ request()->routeIs('settings.policies_privacy.index') ? 'active' : '' }} ">
                        <a class="d-flex align-items-center" href="{{ route('settings.policies_privacy') }}">
                            <i data-feather="file-text"></i><span
                                class="menu-title text-truncate">@lang('policies privacy')</span>
                        </a>
                    </li>
                    <li class="nav-item {{ request()->routeIs('settings.delete_my_account.index') ? 'active' : '' }} ">
                        <a class="d-flex align-items-center" href="{{ route('settings.delete_my_account') }}">
                            <i data-feather="file-text"></i><span
                                class="menu-title text-truncate">@lang('delete my account')</span>
                        </a>
                    </li>
                </ul>

            </li>
        </ul>
    </div>
</div>
