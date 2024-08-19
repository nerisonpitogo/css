<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="cupcake">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- <title>{{ config('app.name', 'Laravel') }}</title> --}}
    <title>{{ $title ?? config('app.name', 'CCSM') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    {{-- IMAGE CROPPING --}}
    {{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.js"></script> --}}
    {{-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.css" /> --}}
    {{-- END IMAGE CROPPING --}}

    {{-- CURRENCY --}}
    <script type="text/javascript" src="https://cdn.jsdelivr.net/gh/robsontenorio/mary@0.44.2/libs/currency/currency.js">
    </script>
    {{-- END CURRENCY --}}



    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased bg-base-200">
    {{-- The navbar with `sticky` and `full-width` --}}
    <x-mary-nav class="bg-base-200">

        <x-slot:brand>
            {{-- Drawer toggle for "main-drawer" --}}
            <label for="main-drawer" class="mr-3 lg:hidden">
                <x-mary-icon name="o-bars-3" class="cursor-pointer" />
            </label>

            {{-- Brand --}}
            <img src="https://laravel.com/img/logomark.min.svg" alt="logo" class="h-12" />
        </x-slot:brand>

        {{-- Right side actions --}}
        <x-slot:actions>
            <x-mary-theme-toggle darkTheme="dark" lightTheme="light" class="btn btn-circle btn-ghost" />
            {{-- <x-mary-button label="Messages" icon="o-envelope" link="###" class="btn-ghost btn-sm" responsive />
            <x-mary-button label="Notifications" icon="o-bell" link="###" class="btn-ghost btn-sm" responsive /> --}}
        </x-slot:actions>
    </x-mary-nav>

    {{-- The main content with `full-width` --}}
    <x-mary-main with-nav>

        {{-- This is a sidebar that works also as a drawer on small screens --}}
        {{-- Notice the `main-drawer` reference here --}}
        <x-slot:sidebar drawer="main-drawer" collapsible class="bg-base-200">
            {{-- User --}}
            @if ($user = auth()->user())
                <x-mary-list-item :item="$user" value="name" sub-value="email" no-separator no-hover
                    class="">
                    <x-slot:actions>
                        <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                            @csrf
                            <x-mary-button type="submit" icon="o-power" class="btn-circle btn-ghost btn-xs"
                                tooltip-left="logoff" />
                        </form>
                    </x-slot:actions>
                </x-mary-list-item>

                <x-mary-menu-separator />
            @endif

            {{-- Activates the menu item when a route matches the `link` property --}}
            <x-mary-menu activate-by-route>
                <x-mary-menu-item title="Dashboard" icon="o-chart-pie" link="{{ route('dashboard') }}" />
                <x-mary-menu-item title="Offices" icon="o-building-office-2" link="{{ route('offices') }}" />

                @can('Manage Users')
                    <x-mary-menu-item title="Users" icon="o-users" link="{{ route('users') }}" />
                @endcan
                @can('Manage Settings')
                    <x-mary-menu-item title="Services Library" icon="o-list-bullet" link="{{ route('libservices') }}" />

                    <x-mary-menu-sub title="RBAC" icon="o-cog-6-tooth">
                        <x-mary-menu-item title="Permissions" icon="o-key" link="{{ route('permissions') }}" />
                        <x-mary-menu-item title="Roles" icon="o-key" link="{{ route('roles') }}" />
                    </x-mary-menu-sub>
                @endcan
            </x-mary-menu>
        </x-slot:sidebar>

        {{-- The `$slot` goes here --}}
        <x-slot:content>

            {{ $slot }}

        </x-slot:content>
    </x-mary-main>

    {{--  TOAST area --}}
    <x-mary-toast position="toast-top toast-center" />
</body>

</html>
