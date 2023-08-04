@php
    $menus = dashboard_menu()->getAll();
@endphp
@foreach ($menus as $menu)
    @php $menu = apply_filters(BASE_FILTER_DASHBOARD_MENU, $menu); @endphp
    @if (
  $menu['id']=='cms-plugins-ads' || $menu['id']=='cms-plugins-newsletter' || $menu['id']=='cms-plugins-simple-slider' || $menu['id']=='cms-plugins-payments' || $menu['id']=='cms-plugins-location'
  || $menu['id']=='cms-core-plugins' || $menu['id']=='cms-core-platform-administration'
   || $menu['id']=='cms-core-tools' || $menu['id']=='cms-core-settings' || $menu['id']=='cms-core-appearance'
   || $menu['id']=='cms-plugins-translations')


    @else
        <li class="nav-item @if ($menu['active']) active @endif" id="{{ $menu['id'] }}">
            <a href="{{ $menu['url'] }}" class="nav-link nav-toggle">
                <i class="{{ $menu['icon'] }}"></i>
                <span class="title">
                {{ !is_array(trans($menu['name'])) ? trans($menu['name']) : null }}
                    {!! apply_filters(BASE_FILTER_APPEND_MENU_NAME, null, $menu['id']) !!}</span>
                @if (isset($menu['children']) && count($menu['children'])) <span class="arrow @if ($menu['active']) open @endif"></span> @endif
            </a>
            @if (isset($menu['children']) && count($menu['children']))
                <ul class="sub-menu @if (!$menu['active']) hidden-ul @endif">
                    @foreach ($menu['children'] as $item)
                        <li class="nav-item @if ($item['active']) active @endif" id="{{ $item['id'] }}">
                            <a href="{{ $item['url'] }}" class="nav-link">
                                <i class="{{ $item['icon'] }}"></i>
                                {{ trans($item['name']) }}
                                {!! apply_filters(BASE_FILTER_APPEND_MENU_NAME, null, $item['id']) !!}
                            </a>
                        </li>
                    @endforeach
                </ul>
            @endif
        </li>

    @endif


@endforeach

