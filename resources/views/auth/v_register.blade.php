<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="{{ asset('') }}assets/plugins/fontawesome-free/css/all.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
  <!-- CSRF Token -->
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <!-- Title Web -->
  <title>{{ $__title }}</title>
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

  <script>
    const baseUrl = "{{ url('') }}"
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
  <!-- Jquery Validation -->
  <script src="{{ asset('') }}assets/plugins/jquery-validation/jquery.validate.min.js"></script>
  <!-- Jquery Validation PLugin -->
  <script src="{{ asset('') }}assets/plugins/jquery-validation/localization/messages_id.min.js"></script>
</head>

<body class="register-page">
  <div class="register-box">
    <div class="register-logo">
      <a href="{{ url('') }}"><b>Admin</b>LTE</a>
    </div>
    <div class="card">
      <div class="card-body register-card-body">
        <p class="login-box-msg">Register a new membership</p>
        <form id="formVendor" method="post">
          <div class="baris-form mb-3">
            <div class="input-group">
              <input type="text" class="form-control" placeholder="Username" id="user_name" name="user_name">
              <div class="input-group-append">
                <div class="input-group-text">
                  <span class="fas fa-user"></span>
                </div>
              </div>
            </div>
          </div>
          <div class="baris-form mb-3">
            <div class="input-group">
              <input type="email" class="form-control" placeholder="Email" id="user_email" name="user_email">
              <div class="input-group-append">
                <div class="input-group-text">
                  <span class="fas fa-envelope"></span>
                </div>
              </div>
            </div>
          </div>
          <div class="baris-form mb-3">
            <div class="input-group">
              <input type="password" class="form-control" placeholder="Password" id="user_password"
                name="user_password">
              <div class="input-group-append">
                <div class="input-group-text">
                  <span class="fas fa-lock"></span>
                </div>
              </div>
            </div>
          </div>
          <div class="baris-form mb-3">
            <div class="input-group">
              <input type="password" class="form-control" placeholder="Retype password" id="confirm_user_password"
                name="confirm_user_password">
              <div class="input-group-append">
                <div class="input-group-text">
                  <span class="fas fa-lock"></span>
                </div>
              </div>
            </div>
          </div>
          <div class="row justify-content-end">
            {{-- <div class="col-8">
              <div class="icheck-primary">
                <input type="checkbox" id="agreeTerms" name="terms" value="agree">
                <label for="agreeTerms">
                  I agree to the <a href="#">terms</a>
                </label>
              </div>
            </div> --}}

            <div class="col-4">
              <button id="btnSubmit" type="submit" class="btn btn-primary btn-block">Register</button>
            </div>

          </div>
        </form>
        <a href="{{ url('admin') }}" class="text-center">I already have a membership</a>
      </div>
    </div>
  </div>
  <script>
    const baseDir = "{{ url('') }}/admin"
    const formVendor = $("#formVendor"),
      btnSubmit = $("#btnSubmit");
    // Class definition
    var PageAdvanced = function() {
      var initForm = function() {
        formVendor.validate({
          errorClass: 'help-block',
          errorElement: 'span',
          ignore: 'input[type=hidden]',
          rules: {
            user_name: {
              required: true,
            },
            user_email: {
              required: true,
            },
            user_password: {
              required: true,
            },
            confirm_user_password: {
              required: true,
              equalTo: "#user_password"
            },
          },
          highlight: function(el, errorClass) {
            $(el).parents('.baris-form').first().addClass('has-error');
          },
          unhighlight: function(el, errorClass) {
            var $parent = $(el).parents('.baris-form').first();
            $parent.removeClass('has-error');
            $parent.find('.help-block').hide();
          },
          errorPlacement: function(error, el) {
            error.appendTo(el.parents('.baris-form'));
          },
          submitHandler: function(form) {
            btnSubmit.attr('disabled', 'disabled').text('Loading...')
            var $data = $(form).serialize();
            $.ajax({
              type: 'POST',
              url: baseDir + "/register",
              data: $data,
              error: function() {
                btnSubmit.removeAttr('disabled', 'disabled').text('Simpan');
              },
              complete: function() {
                btnSubmit.removeAttr('disabled', 'disabled').text('Simpan');
              },
              success: function(res) {
                if (res.status) {
                  Swal.fire({
                    icon: 'success',
                    title: 'Berhasil login!',
                    showConfirmButton: false,
                    timer: 1500
                  });

                } else {
                  if (typeof res.msg == 'object') {
                    res.msg = JSON.stringify(res.msg);
                  }
                  Swal.fire('Error', res.msg, 'error');
                }
              }
            });
            return false;
          }
        });
      }

      // Public methods
      return {
        init: function() {
          initForm();
        }
      }
    }();

    $(document).ready(function() {
      PageAdvanced.init();

      btnSubmit.click(function() {
        formVendor.submit();
      });

    });
  </script>

</body>
