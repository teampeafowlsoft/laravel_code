<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Module PaymentModule</title>

    {{-- Laravel Mix - CSS File --}}
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.2/css/all.css">
    @stack('script')
</head>
<body>
<script>
    localStorage.theme && document.querySelector('body').setAttribute("theme", localStorage.theme);
    localStorage.dir && document.querySelector('html').setAttribute("dir", localStorage.dir);
</script>
@yield('content')

{{-- Laravel Mix - JS File --}}
{{-- <script src="{{ mix('js/paymentmodule.js') }}"></script> --}}
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
</body>
</html>
