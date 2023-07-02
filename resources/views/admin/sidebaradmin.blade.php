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
            <li class="nav-item{{ request()->routeIs('services.index') ? 'active' : '' }} ">
                <a class="d-flex align-items-center" href="{{ route('services.index') }}">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-boxes" viewBox="0 0 16 16">
                        <path d="M7.752.066a.5.5 0 0 1 .496 0l3.75 2.143a.5.5 0 0 1 .252.434v3.995l3.498 2A.5.5 0 0 1 16 9.07v4.286a.5.5 0 0 1-.252.434l-3.75 2.143a.5.5 0 0 1-.496 0l-3.502-2-3.502 2.001a.5.5 0 0 1-.496 0l-3.75-2.143A.5.5 0 0 1 0 13.357V9.071a.5.5 0 0 1 .252-.434L3.75 6.638V2.643a.5.5 0 0 1 .252-.434L7.752.066ZM4.25 7.504 1.508 9.071l2.742 1.567 2.742-1.567L4.25 7.504ZM7.5 9.933l-2.75 1.571v3.134l2.75-1.571V9.933Zm1 3.134 2.75 1.571v-3.134L8.5 9.933v3.134Zm.508-3.996 2.742 1.567 2.742-1.567-2.742-1.567-2.742 1.567Zm2.242-2.433V3.504L8.5 5.076V8.21l2.75-1.572ZM7.5 8.21V5.076L4.75 3.504v3.134L7.5 8.21ZM5.258 2.643 8 4.21l2.742-1.567L8 1.076 5.258 2.643ZM15 9.933l-2.75 1.571v3.134L15 13.067V9.933ZM3.75 14.638v-3.134L1 9.933v3.134l2.75 1.571Z"/>
                    </svg>
                    <span class="menu-title text-truncate"
                          data-i18n="Charts">@lang('our services')
                    </span>
                </a>
            </li>
            <li class="nav-item " {{ request()->routeIs('intros.index') ? 'active' : '' }} style="">
                <a class="d-flex align-items-center" href="{{ route('intros.index') }}">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-skip-start-circle" viewBox="0 0 16 16">
                        <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                        <path d="M10.229 5.055a.5.5 0 0 0-.52.038L7 7.028V5.5a.5.5 0 0 0-1 0v5a.5.5 0 0 0 1 0V8.972l2.71 1.935a.5.5 0 0 0 .79-.407v-5a.5.5 0 0 0-.271-.445z"/>
                    </svg>                    <span class="menu-title text-truncate"
                          data-i18n="Charts">@lang('intros')</span></a>
            </li>
            <li class="nav-item has-sub  " style="">
                <a class="d-flex align-items-center" href="#">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pin-map-fill" viewBox="0 0 16 16">
                        <path fill-rule="evenodd" d="M3.1 11.2a.5.5 0 0 1 .4-.2H6a.5.5 0 0 1 0 1H3.75L1.5 15h13l-2.25-3H10a.5.5 0 0 1 0-1h2.5a.5.5 0 0 1 .4.2l3 4a.5.5 0 0 1-.4.8H.5a.5.5 0 0 1-.4-.8l3-4z"/>
                        <path fill-rule="evenodd" d="M4 4a4 4 0 1 1 4.5 3.969V13.5a.5.5 0 0 1-1 0V7.97A4 4 0 0 1 4 3.999z"/>
                    </svg>                    <span class="menu-title text-truncate"
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
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-menu-button-wide" viewBox="0 0 16 16">
                        <path d="M0 1.5A1.5 1.5 0 0 1 1.5 0h13A1.5 1.5 0 0 1 16 1.5v2A1.5 1.5 0 0 1 14.5 5h-13A1.5 1.5 0 0 1 0 3.5v-2zM1.5 1a.5.5 0 0 0-.5.5v2a.5.5 0 0 0 .5.5h13a.5.5 0 0 0 .5-.5v-2a.5.5 0 0 0-.5-.5h-13z"/>
                        <path d="M2 2.5a.5.5 0 0 1 .5-.5h3a.5.5 0 0 1 0 1h-3a.5.5 0 0 1-.5-.5zm10.823.323-.396-.396A.25.25 0 0 1 12.604 2h.792a.25.25 0 0 1 .177.427l-.396.396a.25.25 0 0 1-.354 0zM0 8a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V8zm1 3v2a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2H1zm14-1V8a1 1 0 0 0-1-1H2a1 1 0 0 0-1 1v2h14zM2 8.5a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9a.5.5 0 0 1-.5-.5zm0 4a.5.5 0 0 1 .5-.5h6a.5.5 0 0 1 0 1h-6a.5.5 0 0 1-.5-.5z"/>
                    </svg>                    <span class="menu-title text-truncate"
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
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-box2-fill" viewBox="0 0 16 16">
                        <path d="M3.75 0a1 1 0 0 0-.8.4L.1 4.2a.5.5 0 0 0-.1.3V15a1 1 0 0 0 1 1h14a1 1 0 0 0 1-1V4.5a.5.5 0 0 0-.1-.3L13.05.4a1 1 0 0 0-.8-.4h-8.5ZM15 4.667V5H1v-.333L1.5 4h6V1h1v3h6l.5.667Z"/>
                    </svg>                    <span class="menu-title text-truncate"
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
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-camera-video-fill" viewBox="0 0 16 16">
                        <path fill-rule="evenodd" d="M0 5a2 2 0 0 1 2-2h7.5a2 2 0 0 1 1.983 1.738l3.11-1.382A1 1 0 0 1 16 4.269v7.462a1 1 0 0 1-1.406.913l-3.111-1.382A2 2 0 0 1 9.5 13H2a2 2 0 0 1-2-2V5z"/>
                    </svg>                    <span class="menu-title text-truncate"
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
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-person-vcard" viewBox="0 0 16 16">
                        <path d="M5 8a2 2 0 1 0 0-4 2 2 0 0 0 0 4Zm4-2.5a.5.5 0 0 1 .5-.5h4a.5.5 0 0 1 0 1h-4a.5.5 0 0 1-.5-.5ZM9 8a.5.5 0 0 1 .5-.5h4a.5.5 0 0 1 0 1h-4A.5.5 0 0 1 9 8Zm1 2.5a.5.5 0 0 1 .5-.5h3a.5.5 0 0 1 0 1h-3a.5.5 0 0 1-.5-.5Z"/>
                        <path d="M2 2a2 2 0 0 0-2 2v8a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2H2ZM1 4a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1v8a1 1 0 0 1-1 1H8.96c.026-.163.04-.33.04-.5C9 10.567 7.21 9 5 9c-2.086 0-3.8 1.398-3.984 3.181A1.006 1.006 0 0 1 1 12V4Z"/>
                    </svg>                    <span class="menu-title text-truncate"
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
                <a class="d-flex align-items-center" href="#">
                    <i class="fa fa-money-bill-alt" style="font-size:24px;"></i>
                    <span class="menu-title text-truncate"
                          data-i18n="Charts">@lang('paymentGateways')</span></a>
                <ul class="menu-content">
                    <li class="nav-item {{ request()->routeIs('paymentGateways.index') ? 'active' : '' }} ">
                        <a class="d-flex align-items-center" href="{{ route('paymentGateways.index') }}">
                            <i data-feather="file-text"></i><span
                                class="menu-title text-truncate">@lang('paymentGateways')</span>
                        </a>
                    </li>

                </ul>
{{--                <ul class="menu-content">--}}
{{--                    <li class="nav-item {{ request()->routeIs('payments.index') ? 'active' : '' }} ">--}}
{{--                        <a class="d-flex align-items-center" href="{{ route('payments.index') }}">--}}
{{--                            <i data-feather="file-text"></i><span--}}
{{--                                class="menu-title text-truncate">@lang('payment process')</span>--}}
{{--                        </a>--}}
{{--                    </li>--}}

{{--                </ul>--}}
            </li>
            <li class="nav-item has-sub  " style="">
                @php $count= \App\Models\Contact::query()->where('view',1)->count() @endphp
                <a class="d-flex align-items-center" href="#">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-messenger" viewBox="0 0 16 16">
                        <path d="M0 7.76C0 3.301 3.493 0 8 0s8 3.301 8 7.76-3.493 7.76-8 7.76c-.81 0-1.586-.107-2.316-.307a.639.639 0 0 0-.427.03l-1.588.702a.64.64 0 0 1-.898-.566l-.044-1.423a.639.639 0 0 0-.215-.456C.956 12.108 0 10.092 0 7.76zm5.546-1.459-2.35 3.728c-.225.358.214.761.551.506l2.525-1.916a.48.48 0 0 1 .578-.002l1.869 1.402a1.2 1.2 0 0 0 1.735-.32l2.35-3.728c.226-.358-.214-.761-.551-.506L9.728 7.381a.48.48 0 0 1-.578.002L7.281 5.98a1.2 1.2 0 0 0-1.735.32z"/>
                    </svg>                    <span class="menu-title text-truncate"
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
                    <li class="nav-item {{ request()->routeIs('settings.index') ? 'active' : '' }} ">
                        <a class="d-flex align-items-center" href="{{ route('settings.index') }}">
                            <i data-feather="file-text"></i><span
                                class="menu-title text-truncate">@lang('settings')</span>
                        </a>
                    </li>
                    <li class="nav-item {{ request()->routeIs('settings.terms_conditions') ? 'active' : '' }} ">
                        <a class="d-flex align-items-center" href="{{ route('settings.terms_conditions') }}">
                            <i data-feather="file-text"></i><span
                                class="menu-title text-truncate">@lang('terms conditions')</span>
                        </a>
                    </li>
                    <li class="nav-item {{ request()->routeIs('settings.about_application') ? 'active' : '' }} ">
                        <a class="d-flex align-items-center" href="{{ route('settings.about_application') }}">
                            <i data-feather="file-text"></i><span
                                class="menu-title text-truncate">@lang('about application')</span>
                        </a>
                    </li>
                    <li class="nav-item {{ request()->routeIs('settings.policies_privacy') ? 'active' : '' }} ">
                        <a class="d-flex align-items-center" href="{{ route('settings.policies_privacy') }}">
                            <i data-feather="file-text"></i><span
                                class="menu-title text-truncate">@lang('policies privacy')</span>
                        </a>
                    </li>
                    <li class="nav-item {{ request()->routeIs('settings.delete_my_account') ? 'active' : '' }} ">
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
