<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- CSRF Token -->
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <!-- Title Web -->
  <title>{{ $__title }}</title>

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="{{ asset('') }}assets/plugins/fontawesome-free/css/all.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="{{ asset('') }}assets/dist/css/adminlte.min.css">
  <!-- Sweetalert 2 -->
  <link rel="stylesheet" href="{{ asset('') }}assets/plugins/sweetalert2/sweetalert2.min.css">
  <!-- Jquery UI -->
  <link rel="stylesheet" href="{{ asset('') }}assets/plugins/jquery-ui/jquery-ui.min.css">
  <!-- Select2 -->
  <link rel="stylesheet" href="{{ asset('') }}assets/plugins/select2/css/select2.min.css">
  <!-- Datatables Plugin -->
  <link rel="stylesheet" href="{{ asset('') }}assets/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
  <link rel="stylesheet"
    href="{{ asset('') }}assets/plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
  <!-- Jstree -->
  <link rel="stylesheet" href="{{ asset('') }}assets/plugins/jstree/css/style.min.css">
  <!-- Bootstrap Datepicker -->
  <link rel="stylesheet"
    href="{{ asset('') }}assets/plugins/bootstrap-datepicker/css/bootstrap-datepicker.min.css">
  <style>
    .has-error .help-block {
      color: red;
    }

    .has-error .form-control {
      border-color: red;
    }
  </style>

  <!-- jQuery -->
  <script src="{{ asset('') }}assets/plugins/jquery/jquery.min.js"></script>
  <!-- Jquery UI -->
  <script src="{{ asset('') }}assets/plugins/jquery-ui/jquery-ui.min.js"></script>
  <!-- Bootstrap 4 -->
  <script src="{{ asset('') }}assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>

  <script>
    const baseUrl = "{{ url('') }}";

    function number_format(number, decimals = 0, dec_point = '.', thousands_sep = ',') {
      // Strip all characters but numerical ones.
      number = (number + '').replace(/[^0-9+\-Ee.]/g, '');
      var n = !isFinite(+number) ? 0 : +number,
        prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
        sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
        dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
        s = '',
        toFixedFix = function(n, prec) {
          var k = Math.pow(10, prec);
          return '' + Math.round(n * k) / k;
        };
      // Fix for IE parseFloat(0.55).toFixed(0) = 0;
      s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
      if (s[0].length > 3) {
        s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
      }
      if ((s[1] || '').length < prec) {
        s[1] = s[1] || '';
        s[1] += new Array(prec - s[1].length + 1).join('0');
      }
      return s.join(dec);
    }

    $(document).ready(function() {
      $.ajaxSetup({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
      });
    });
  </script>
  <!-- AdminLTE App -->
  <script src="{{ asset('') }}assets/dist/js/adminlte.js"></script>
  <!-- Sweetalert 2 -->
  <script src="{{ asset('') }}assets/plugins/sweetalert2/sweetalert2.all.min.js"></script>
  <!-- Select2 -->
  <script src="{{ asset('') }}assets/plugins/select2/js/select2.full.min.js"></script>
  <!-- Jquery Validation -->
  <script src="{{ asset('') }}assets/plugins/jquery-validation/jquery.validate.min.js"></script>
  <!-- Jquery Validation PLugin -->
  <script src="{{ asset('') }}assets/plugins/jquery-validation/localization/messages_id.min.js"></script>
  <!-- Datatables -->
  <script src="{{ asset('') }}assets/plugins/datatables/jquery.dataTables.min.js"></script>
  <!-- Datatables Plugin -->
  <script src="{{ asset('') }}assets/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
  <script src="{{ asset('') }}assets/plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
  <script src="{{ asset('') }}assets/plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
  <!-- Jstree -->
  <script src="{{ asset('') }}assets/plugins/jstree/js/jstree.min.js"></script>
  <!-- Inputmask -->
  <script src="{{ asset('') }}assets/plugins/inputmask/jquery.inputmask.min.js"></script>
  <!-- Bootstrap Datepicker -->
  <script src="{{ asset('') }}assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js"></script>
</head>

<body class="hold-transition sidebar-mini layout-fixed">
  <div class="wrapper">
    <!-- Preloader -->
    <div class="preloader flex-column justify-content-center align-items-center">
      @if (array_key_exists('use_logo_icon_admin', $__sess_app))
        @if ($__sess_app['use_logo_icon_admin'] == 'Y')
          <img class="animation__shake" src="{{ asset($__sess_app['dir_logo_icon_admin']) }}"
            alt="{{ $__sess_app['app_nama'] }}" height="60" width="60">
        @endif
      @endif
    </div>

    <!-- Navbar -->
    <nav class="main-header navbar navbar-expand navbar-white navbar-light">
      <!-- Left navbar links -->
      <ul class="navbar-nav">
        <li class="nav-item">
          <a class="nav-link" data-widget="pushmenu" href="#" role="button">
            <i class="fas fa-bars"></i>
          </a>
        </li>
      </ul>

      <!-- Right navbar links -->
      <ul class="navbar-nav ml-auto">
        <!-- Notifications Dropdown Menu -->
        <li class="nav-item dropdown">
          <a class="nav-link" data-toggle="dropdown" href="#">
            <i class="fas fa-user"></i>
          </a>
          <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right"><a href="#" class="dropdown-item">
              <!-- Message Start -->
              <div class="media align-items-center">
                <img src="{{ asset('') }}assets/dist/img/user1-128x128.jpg" alt="User Avatar"
                  class="img-size-50 mr-3 img-circle">
                <div class="media-body">
                  <h3 class="dropdown-item-title">
                    @if (!empty($__user['user_fullname']))
                      {{ $__user['user_fullname'] }}
                    @else
                      {{ $__user['user_name'] }}
                    @endif
                  </h3>
                  <p class="text-sm">{{ $__user['user_email'] }}</p>
                </div>
              </div>
              <!-- Message End -->
            </a>
            <div class="dropdown-divider"></div>
            <a href="{{ route('profil.index') }}" class="dropdown-item">
              <i class="fas fa-user mr-2"></i> Profil
            </a>
            {{-- <div class="dropdown-divider"></div>
              <a href="#" class="dropdown-item">
                <i class="fas fa-file mr-2"></i> 3 new reports
                <span class="float-right text-muted text-sm">2 days</span>
              </a> --}}
            <div class="dropdown-divider"></div>
            <a href="{{ route('logout') }}" class="dropdown-item dropdown-footer">Logout</a>
          </div>
        </li>
      </ul>
    </nav>
    <!-- /.navbar -->

    <!-- Main Sidebar Container -->
    <aside class="main-sidebar sidebar-dark-primary elevation-4">
      <!-- Brand Logo -->
      <a href="index3.html" class="brand-link">
        @if (array_key_exists('use_logo_icon_admin', $__sess_app))
          @if ($__sess_app['use_logo_icon_admin'] == 'Y')
            <img src="{{ asset($__sess_app['dir_logo_icon_admin']) }}" alt="{{ $__sess_app['app_nama'] }}"
              class="brand-image img-circle elevation-3" style="opacity: .8">
          @endif
        @endif
        <span class="brand-text font-weight-light">{{ $__sess_app['app_nama'] }}</span>
      </a>

      <!-- Sidebar -->
      <x-admin-sidebar>
        {!! $__sidebar !!}
      </x-admin-sidebar>
      <!-- /.sidebar -->
    </aside>

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
      {!! $__view !!}
    </div>
    <!-- /.content-wrapper -->
    <footer class="main-footer">

      @if (array_key_exists('dev_web', $__sess_app))
        <strong>Copyright &copy; 2014-2021 <a
            href="{{ $__sess_app['dev_web'] }}">{{ $__sess_app['dev_nama'] }}</a>.</strong>
      @else
        <strong>Copyright &copy; 2014-2021 <a href="#">{{ $__sess_app['dev_nama'] }}</a>.</strong>
      @endif
      All rights reserved.
      <div class="float-right d-none d-sm-inline-block">
        <b>Version</b> 3.2.0
      </div>
    </footer>
  </div>
  <!-- ./wrapper -->
</body>

</html>
