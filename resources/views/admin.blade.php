<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ config('app.name', '') }}</title>
    <link rel="stylesheet" href="{{ mix('css/app.css') }}" type="text/css"/>
    <link rel="shortcut icon" type="image/x-icon" href="/favicon.ico"/>

    <script>
        window.csrfToken = '{{ csrf_token() }}';
        window.formAdmin = {};
        window.formAdmin.webAdminPrefix = '{{ \jhoopes\LaravelVueForms\Facades\LaravelVueForms::webAdminPrefix() }}'
        window.formAdmin.apiPrefix      = '{{ \jhoopes\LaravelVueForms\Facades\LaravelVueForms::apiPrefix() }}'
        window.formAdmin.apiAdminPrefix = '{{ \jhoopes\LaravelVueForms\Facades\LaravelVueForms::adminApiPrefix() }}'
        window.formAdmin.useJsonApi     = {{ \jhoopes\LaravelVueForms\Facades\LaravelVueForms::useJSONAPI() ? 'true' : 'false' }}
    </script>
</head>
<body>

<div class="wrap">
    <div id="admin">
        <form-admin></form-admin>
    </div>
</div>



<!-- Scripts -->

<!-- Javascript - jquery should come first -->
@section('scripts')


    <script type="text/javascript" src="{{ mix('/js/app.js') }}"></script>

@show
</body>
</html>
