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
  <!-- BS Stepper -->
  <link rel="stylesheet" href="{{ asset('') }}assets/plugins/bs-stepper/css/bs-stepper.min.css">
  <style>
    .has-error .help-block {
      color: red;
    }

    .has-error .form-control,
    .has-error .input-group-text,
    .has-error .input-group .form-control:focus~.input-group-append .input-group-text {
      border-color: red;
    }
  </style>

  <!-- jQuery -->
  <script src="{{ asset('') }}assets/plugins/jquery/jquery.min.js"></script>
  <!-- jQuery UI 1.11.4 -->
  <script src="{{ asset('') }}assets/plugins/jquery-ui/jquery-ui.min.js"></script>
  <!-- Bootstrap 4 -->
  <script src="{{ asset('') }}assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
  <!-- AdminLTE App -->
  <script src="{{ asset('') }}assets/dist/js/adminlte.js"></script>
  <!-- Sweetalert 2 -->
  <script src="{{ asset('') }}assets/plugins/sweetalert2/sweetalert2.all.min.js"></script>
  <!-- Jquery Validation -->
  <script src="{{ asset('') }}assets/plugins/jquery-validation/jquery.validate.min.js"></script>
  <!-- Jquery Validation PLugin -->
  <script src="{{ asset('') }}assets/plugins/jquery-validation/localization/messages_id.min.js"></script>
  <!-- BS-Stepper -->
  <script src="{{ asset('') }}assets/plugins/bs-stepper/js/bs-stepper.min.js"></script>
</head>

<body class="register-page"
  style="@if (!empty($background_auth)) background:url({{ $background_auth }});background-size:cover;background-position:center;background-repeat:no-repeat; @endif">
  <div class="register-box">
    <div class="register-logo">
      <a href="{{ url('') }}">
        @if (!empty($app_logo))
          <img src="{!! $app_logo !!}" class="w-50" />
        @else
          {!! $title_auth !!}
        @endif
      </a>
    </div>

    <div class="card card-default">
      <div class="card-body register-card-body">
        <p class="login-box-msg">Register a new membership</p>
        <div class="bs-stepper" style="margin-top: -20px;">
          <div class="bs-stepper-header" role="tablist">
            <!-- your steps here -->
            <div class="step" data-target="#tenant-part">
              <button type="button" class="step-trigger" role="tab" aria-controls="tenant-part"
                id="tenant-part-trigger">
                <span class="bs-stepper-circle">1</span>
                <span class="bs-stepper-label">Tenant</span>
              </button>
            </div>
            <div class="line"></div>
            <div class="step" data-target="#login-part">
              <button type="button" class="step-trigger" role="tab" aria-controls="login-part"
                id="login-part-trigger">
                <span class="bs-stepper-circle">2</span>
                <span class="bs-stepper-label">User</span>
              </button>
            </div>
          </div>
          <div class="bs-stepper-content px-0">
            <!-- your steps content here -->
            <div id="tenant-part" class="content" role="tabpanel" aria-labelledby="tenant-part-trigger">
              <div class="form-group">
                <label for="mku_id">Kategori Usaha</label>
                <select class="form-control" id="mku_id" name="mku_id">
                  @foreach ($opt_ku as $v)
                    <option value="{{ $v['mku_id'] }}">{{ $v['mku_nama'] }}</option>
                  @endforeach
                </select>
              </div>
              <div class="form-group">
                <label for="tenant_nama">Nama Brand/Usaha</label>
                <input type="text" class="form-control" id="tenant_nama" name="tenant_nama"
                  placeholder="Masukkan nama brand/usaha..." required>
              </div>
              <div class="form-group">
                <label for="tenant_desc">Deskripsi Usaha</label>
                <textarea class="form-control" cols="3" id="tenant_desc" name="tenant_desc"
                  placeholder="Masukkan deskripsi usaha..." required></textarea>
              </div>
              <button class="btn btn-primary" onclick="fnCekTenant()">Selanjutnya</button>
            </div>
            <div id="login-part" class="content" role="tabpanel" aria-labelledby="login-part-trigger">
              <div class="form-group">
                <label for="user_email">Email</label>
                <input type="text" class="form-control" id="user_email" name="user_email">
              </div>
              <div class="form-group">
                <label for="user_password">Password</label>
                <input type="password" class="form-control" id="user_password" name="user_password">
              </div>
              <div class="form-group">
                <label for="conf_user_password">Konfirmasi Password</label>
                <input type="password" class="form-control" id="conf_user_password" name="conf_user_password">
              </div>
              <button class="btn btn-primary" onclick="stepper.previous()">Sebelumnya</button>
              <button type="button" id="btnSubmit" class="btn btn-primary">Sign Up</button>
            </div>
          </div>
        </div>
        <p class="mb-0">
          <a href="{{ route('auth.index') }}" class="text-center">I already have a membership</a>
        </p>
      </div>
      <!-- /.card-body -->
    </div>
    <!-- /.card -->
  </div>
  <script>
    const mkuId = $("#mku_id"),
      tenantNama = $("#tenant_nama"),
      tenantDesc = $("#tenant_desc"),
      userEmail = $("#user_email"),
      userPassword = $("#user_password"),
      confUserPassword = $("#conf_user_password");

    const btnSubmit = $("#btnSubmit");

    function fnCekTenant() {
      let errMsg = "";
      $("#tenant-part").find(".has-error").removeClass("has-error")

      if (mkuId.val().trim() == "") {
        errMsg += "<li>Kategori Usaha tidak boleh kosong!</li>";
        mkuId.closest(".form-group").addClass("has-error");
      }

      if (tenantNama.val().trim() == "") {
        errMsg += "<li>Nama Brand/Usaha tidak boleh kosong!</li>";
        tenantNama.closest(".form-group").addClass("has-error");
      }

      if (tenantDesc.val().trim() == "") {
        errMsg += "<li>Deskripsi Usaha tidak boleh kosong!</li>";
        tenantDesc.closest(".form-group").addClass("has-error");
      }

      if (errMsg != "") {
        Swal.fire({
          title: "Error",
          html: "<ul>" + errMsg + "</ul>",
          icon: "error",
        });
        return
      }
      stepper.next();
    }

    $(document).ready(function() {
      $.ajaxSetup({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
      });

      // BS-Stepper Init
      window.stepper = new Stepper(document.querySelector('.bs-stepper'))

      btnSubmit.click(function() {
        let errMsg = "";
        $("#login-part").find(".has-error").removeClass("has-error")

        if (userEmail.val().trim() == "") {
          errMsg += "<li>Email tidak boleh kosong!</li>";
          userEmail.closest(".form-group").addClass("has-error");
        }

        if (userPassword.val().trim() == "") {
          errMsg += "<li>Password tidak boleh kosong!</li>";
          userPassword.closest(".form-group").addClass("has-error");
        }

        if (confUserPassword.val().trim() == "") {
          errMsg += "<li>Konfirmasi Password tidak boleh kosong!</li>";
          confUserPassword.closest(".form-group").addClass("has-error");
        }

        if (userPassword.val() != confUserPassword.val()) {
          errMsg += "<li>Konfirmasi Password harus sama dengan Password!</li>";
          confUserPassword.closest(".form-group").addClass("has-error");
        }

        if (errMsg != "") {
          Swal.fire({
            title: "Error",
            html: "<ul>" + errMsg + "</ul>",
            icon: "error",
          });
          return
        }

        btnSubmit.attr('disabled', 'disabled').text('Loading...')

        $.ajax({
          type: 'POST',
          url: "{{ route('auth.register.post') }}",
          data: {
            mku_id: mkuId.val(),
            tenant_nama: tenantNama.val(),
            tenant_desc: tenantDesc.val(),
            user_email: userEmail.val(),
            user_password: userPassword.val(),
          },
          error: function() {
            btnSubmit.removeAttr('disabled', 'disabled').text('Sign Up');
          },
          complete: function() {
            btnSubmit.removeAttr('disabled', 'disabled').text('Sign Up');
          },
          success: function(res) {
            if (res.status) {
              Swal.fire({
                icon: 'success',
                title: 'Berhasil Register! Silahkan cek email Anda untuk memvalidasi akun',
                showConfirmButton: false,
                timer: 2000
              });
            } else {
              if (typeof res.msg == 'object') {
                res.msg = JSON.stringify(res.msg);
              }
              Swal.fire('Error', res.msg, 'error');
            }
          }
        });
      });

    });
  </script>
</body>

</html>
