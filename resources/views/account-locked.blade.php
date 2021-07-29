@section('title', __('account-lock::translations.account-locked-successfully'))
@section('message')
    <p>{{ __('account-lock::translations.account-locked-successfully') }}</p>
    <p>{!! __('account-lock::translations.account-locked-redirection', ['url' => '/'])  !!}</p>
    <p>{!! __('account-lock::translations.account-locker-contact-us', ['mail' => config('mail.from.address')])  !!}</p>
@endsection


<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>@yield('title')</title>

    <!-- Fonts -->
    <link rel="stylesheet" media="screen" href="https://fontlibrary.org//face/nunito-sans" type="text/css"/>

    <!-- Styles -->
    <style>
        body,html{background-color:#fff;color:#636b6f;font-family:Nunito,sans-serif;font-style:normal;font-weight:100;height:100vh;margin:0}.full-height{height:100vh}.flex-center{align-items:center;display:flex;justify-content:center}.position-ref{position:relative}.content{text-align:center}.title{font-size:24px;padding:20px}
    </style>
</head>
<body>
<div class="flex-center position-ref full-height">
    <div class="content">
        <div class="title">
            @yield('message')
        </div>
    </div>
</div>
</body>
</html>
