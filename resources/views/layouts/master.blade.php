<!DOCTYPE html>
<!--
This is a starter template page. Use this page to start your new project from
scratch. This page gets rid of all links and provides the needed markup only.
-->
<html>

@include('layouts._head')

<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

    @include('layouts._header')
    @include('layouts._sidebar')

    <div class="content-wrapper">

        <section class="content-header">
            <h1>
                {{ $page_title or "Page Title" }}
                <small>{{ $page_description or null }}</small>
            </h1>
        </section>

        <section class="content">
            @if(Session::has('message'))
                <div class="alert alert-success">
                    {{ Session::get('message') }}
                </div>
            @endif
            @yield('content')
        </section>

    </div>

    @include('layouts._footer')

</div>

@include('layouts._loading')

@include('layouts._scripts')

</body>
</html>