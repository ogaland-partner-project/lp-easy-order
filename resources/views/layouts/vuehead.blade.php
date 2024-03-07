<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- API Token -->
  @if (Auth::check())
  <meta name="api-token" content="{{ Auth::user()->api_token }}">
  @endif
  <!-- CSRF Token -->
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <title>{{ config('app.name', 'Laravel') }}</title>

  <!-- Scripts -->
  {{-- <script src="{{ asset('js/app.js') }}" defer></script> --}}
  {{-- Viewを展開するjavascriptファイルをcompact引数で受け取る --}}
  <script src="{{ asset('js/'.$js_file) }}" defer></script>

  <!-- Fonts -->
  <link rel="dns-prefetch" href="//fonts.gstatic.com">
  <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

  <!-- Styles -->
  <link href="{{ asset('css/app.css') }}" rel="stylesheet">
  <!-- Styles(vuetify) -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:100,300,400,500,700,900">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@mdi/font@latest/css/materialdesignicons.min.css">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Material+Icons">
</head>