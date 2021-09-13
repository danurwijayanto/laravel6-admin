@section('header')
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<title>@yield('title')</title>
<!-- Tell the browser to be responsive to screen width -->
<meta name="viewport" content="width=device-width, initial-scale=1">
<!-- Bootstrap 4 -->

<!-- Font Awesome -->
<link href="//cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
<!-- Ionicons -->
<link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
<!-- Theme style -->
<link rel="stylesheet" href="{{asset('assets/dist/css/adminlte.min.css')}}">
<!-- Google Font: Source Sans Pro -->
<link rel="stylesheet" href="{{asset('assets/plugins/fonts/SourceSansPro.css')}}">

<link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.3/css/bootstrap.css" rel="stylesheet">

<link href="https://cdn.datatables.net/1.10.21/css/dataTables.bootstrap4.min.css" rel="stylesheet">

<!-- jQuery -->
<script src="//code.jquery.com/jquery-3.5.1.js"></script>

<style>
  .color-palette {
    height: 35px;
    line-height: 35px;
    text-align: right;
    padding-right: .75rem;
  }

  .color-palette.disabled {
    text-align: center;
    padding-right: 0;
    display: block;
  }

  .color-palette-set {
    margin-bottom: 15px;
  }

  .color-palette span {
    display: none;
    font-size: 12px;
  }

  .color-palette:hover span {
    display: block;
  }

  .color-palette.disabled span {
    display: block;
    text-align: left;
    padding-left: .75rem;
  }

  .color-palette-box h4 {
    position: absolute;
    left: 1.25rem;
    margin-top: .75rem;
    color: rgba(255, 255, 255, 0.8);
    font-size: 12px;
    display: block;
    z-index: 7;
  }
</style>
@endsection