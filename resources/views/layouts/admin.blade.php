<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'GOAPP') }}</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">

    <!-- Material Design Icons (MDI) -->
    <link href="https://cdn.jsdelivr.net/npm/@mdi/font/css/materialdesignicons.min.css" rel="stylesheet">

    <!-- FontAwesome (Optional) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <style>
    [v-cloak] {
      display: none !important;
    }
  </style>
</head>
<body>
    <div id="app" v-cloak>
        <v-app app>
            <v-navigation-drawer permanent expand-on-hover fixed class="grey lighten-3" elevation="16">
                <v-list class="border border-bottom">
                    <v-list-item class="px-2">
                      <v-list-item-avatar>
                        <v-img src="{{asset('images/icon.png')}}"></v-img>
                      </v-list-item-avatar>
                        <v-list-item-content>
                            <v-list-item-title class="text-h6">
                                TrueWeb App
                            </v-list-item-title>
                        </v-list-item-content>
                    </v-list-item>
                </v-list>
                <v-list dense nav shaped>
                    <v-list-item-group>
                        <v-list-item href="/admin/dashboard" class="{{ request()->routeIs('admin.index') ? 'active' : '' }}">
                            <v-list-item-icon>
                              <v-icon>mdi-view-dashboard-outline</v-icon>
                            </v-list-item-icon>
                            <v-list-item-title>Dashboard</v-list-item-title>
                        </v-list-item>
                        <v-list-item href="{{route('users.list')}}" class="{{ request()->routeIs('users.list') ? 'active' : '' }}">
                          <v-list-item-icon>
                            <v-icon>mdi-account-edit</v-icon>
                          </v-list-item-icon>
                          <v-list-item-title>Users</v-list-item-title>
                        </v-list-item>
                        <v-list-item href="{{route('products.list')}}" class="{{ request()->routeIs('products.list') ? 'active' : '' }}">
                            <v-list-item-icon>
                              <v-icon>mdi-format-list-text</v-icon>
                            </v-list-item-icon>
                            <v-list-item-title>Products</v-list-item-title>
                        </v-list-item>
                        <v-list-item href="{{route('productoffers.list')}}" class="{{ request()->routeIs('productoffers.list') ? 'active' : '' }}">
                          <v-list-item-icon>
                            <v-icon>mdi-offer</v-icon>
                          </v-list-item-icon>
                          <v-list-item-title>Product Offers</v-list-item-title>
                        </v-list-item>
                        <v-list-item href="{{route('mainmcats.list')}}" class="{{ request()->routeIs('mainmcats.list') ? 'active' : '' }}">
                            <v-list-item-icon>
                              <v-icon>mdi-storefront</v-icon>
                            </v-list-item-icon>
                            <v-list-item-title>Main Categories</v-list-item-title>
                        </v-list-item>
                        <v-list-item href="{{route('mcats.list')}}" class="{{ request()->routeIs('mcats.list') ? 'active' : '' }}">
                            <v-list-item-icon>
                              <v-icon>mdi-storefront</v-icon>
                            </v-list-item-icon>
                            <v-list-item-title>Categories</v-list-item-title>
                        </v-list-item>
                        <v-list-item href="{{route('msubcats.list')}}" class="{{ request()->routeIs('msubcats.list') ? 'active' : '' }}">
                            <v-list-item-icon>
                              <v-icon>mdi-format-list-group</v-icon>
                            </v-list-item-icon>
                            <v-list-item-title>Sub-Categories</v-list-item-title>
                        </v-list-item>
                        <v-list-item href="{{ route('moptions.list') }}" class="{{ request()->routeIs('moptions.list') ? 'active' : '' }}">
                            <v-list-item-icon>
                              <v-icon>mdi-filter-variant</v-icon>
                            </v-list-item-icon>
                            <v-list-item-title>Options</v-list-item-title>
                        </v-list-item>
                        <v-list-item href="{{ route('mbrands.list') }}" class="{{ request()->routeIs('mbrands.list') ? 'active' : '' }}">
                            <v-list-item-icon>
                              <v-icon>mdi-domain</v-icon>
                            </v-list-item-icon>
                            <v-list-item-title>Brands</v-list-item-title>
                        </v-list-item>
                        {{-- <v-list-item href="{{ route('largebanners.list') }}" class="{{ request()->routeIs('largebanners.list') ? 'active' : '' }}">
                            <v-list-item-icon>
                              <v-icon>mdi-size-xxs</v-icon>
                            </v-list-item-icon>
                            <v-list-item-title>Home Large Banners</v-list-item-title>
                        </v-list-item>
                        <v-list-item href="{{ route('smallbanners.list') }}" class="{{ request()->routeIs('smallbanners.list') ? 'active' : '' }}">
                            <v-list-item-icon>
                              <v-icon>mdi-size-s</v-icon>
                            </v-list-item-icon>
                            <v-list-item-title>Home Small Banners</v-list-item-title>
                        </v-list-item>
                        <v-list-item href="{{ route('exploredealbanners.list') }}" class="{{ request()->routeIs('exploredealbanners.list') ? 'active' : '' }}">
                            <v-list-item-icon>
                              <v-icon>mdi-sale</v-icon>
                            </v-list-item-icon>
                            <v-list-item-title>Home Deal Banners</v-list-item-title>
                        </v-list-item>
                        <v-list-item href="{{ route('fruitbanners.list') }}" class="{{ request()->routeIs('fruitbanners.list') ? 'active' : '' }}">
                            <v-list-item-icon>
                              <v-icon>mdi-fruit-cherries</v-icon>
                            </v-list-item-icon>
                            <v-list-item-title>Home Fruit Banners</v-list-item-title>
                        </v-list-item> --}}
                        <v-list-item href="{{ route('browsebanners.list') }}" class="{{ request()->routeIs('browsebanners.list') ? 'active' : '' }}">
                            <v-list-item-icon>
                              <v-icon>mdi-simple-icons</v-icon>
                            </v-list-item-icon>
                            <v-list-item-title>Browse Banners</v-list-item-title>
                        </v-list-item>
                        <v-list-item href="/admin/logout">
                            <v-list-item-icon>
                              <v-icon>mdi-logout</v-icon>
                            </v-list-item-icon>
                            <v-list-item-title>Log Out</v-list-item-title>
                        </v-list-item>
                    </v-list-item-group>
                </v-list>
            </v-navigation-drawer>
            <v-main style="padding-left: 66px" class="py-5 pe-3">
                @yield('content')
            </v-main>
        </v-app>
    </div>
    <script src="{{asset('js/vapp.js')}}"></script>
</body>
</html>
