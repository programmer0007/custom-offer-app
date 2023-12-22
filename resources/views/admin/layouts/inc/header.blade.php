@php
  $query_p =http_build_query(request()->all()) ;
@endphp
<style>
  a{
    text-decoration: none;
  }
  .header-main-sec a.menu.active-menu {
      position: relative;
  }

  .header-main-sec a.menu.active-menu::after {
      content: '';
      position: absolute;
      border-bottom: 2px solid #e31e2d;
      width: 100%;
      bottom: -19px;
      left: 0;
  }

  @media (max-width: 991px) {
    .header-main-sec a.menu.active-menu {
      position: unset;
    }

    .header-main-sec a.menu.active-menu::after {
      content: '';
      display: none;
    }
  }
</style>
<header class="header-main-sec">
  <div class="header-logo">
    <a href="{{ route('dashboard') }}?{{$query_p}}"><img src="{{ asset('admin_/custom/images/dynamiclogo.png') }}" class="img-fluid dynamiclogo-img">Dynamic Dreamz</a>
  </div>
  <div class="header-menu">
  <ul>
  <li>
      
      <!-- <a href="{{ route('dashboard') }}">DashBoard</a> -->
      <a href="{{route('dashboard')}}?{{$query_p}}" class="menu {{ request()->is('dashboard','upsell-order/*','upsell','product/edit/*') ? 'active-menu' : '' }}">Dashboard</a>
    </li>
    
    <li>
      <a href="#" class="product-sync" class="menu">Product Sync</a>
    </li>

    {{-- <li>
      <a href="{{route('setting')}}?{{$query_p}}" class="menu {{ request()->is('product/setting') ? 'active-menu' : '' }}">Setting</a>
    </li> --}}

    <li>
      <a href="{{route('help')}}?{{$query_p}}" class="menu {{ request()->is('help') ? 'active-menu' : '' }}">Help</a>
    </li>
  </ul>
  </div>

  <div class="toggler-icon">
    <a href="#">
        <img src="{{ asset('admin_/custom/images/toggler-icon.png') }}" class="img-fluid toggler-img">
        {{-- <img src="../../../public/admin_/custom/images/toggler-icon.png" class="img-fluid toggler-img"> --}}
      </a>
  </div>
</header>


