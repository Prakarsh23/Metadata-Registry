    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
        <meta name="csrf-token" content="{{ csrf_token() }}">

    	  <link rel="icon" href="{{ config('app.fav_icon', 'registry_favicon_prod.ico') }}"/>

        <title>@yield('title', app_name())</title>

        <!-- Meta -->
        <meta name="description" content="@yield('meta_description', 'Open Metadata Registry')">
        <meta name="author" content="@yield('meta_author', 'Jon Phipps')">
        @yield('meta')

        <!-- Styles -->
        @yield('before-styles')
        {{ Html::style('fonts/font-awesome-4.7.0/css/font-awesome.min.css') }}

    <!-- Check if the language is set to RTL, so apply the RTL layouts -->
        <!-- Otherwise apply the normal LTR layouts -->
        @langRTL
            {{ Html::style(getRtlCss(mix('css/frontend.css'))) }}
        @else
            {{ Html::style(mix('css/frontend.css')) }}
        @endif
        {{ Html::style(mix('css/all.css')) }}

    @yield('after-styles')

        <!-- Scripts -->
        <script>
            window.Laravel = <?php echo json_encode([
                'csrfToken' => csrf_token(),
            ]); ?>
        </script>
    </head>
