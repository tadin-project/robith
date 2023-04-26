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
  <!-- Toastr -->
  <link rel="stylesheet" href="{{ asset('') }}assets/plugins/toastr/toastr.min.css">
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
  <!-- Toastr -->
  <script src="{{ asset('') }}assets/plugins/toastr/toastr.min.js"></script>
</head>

<body class="login-page">
  <div class="login-box">
    <div class="login-logo">
      <a href="{{ url('') }}">{!! $title_auth !!}</a>
    </div>

    <div class="card">
      <div class="card-body login-card-body">
        <p class="login-box-msg">Form reset password</p>
        <form id="formVendor" method="post">
          <input type="hidden" id="token" name="token" value="{{ $token }}">
          <div class="baris-form mb-3">
            <div class="input-group">
              <input type="password" class="form-control" placeholder="Password Baru" id="user_pass" name="user_pass">
              <div class="input-group-append">
                <div class="input-group-text">
                  <span class="fas fa-lock"></span>
                </div>
              </div>
            </div>
          </div>
          <div class="baris-form mb-3">
            <div class="input-group">
              <input type="password" class="form-control" placeholder="Retype Password Baru" id="conf_user_pass"
                name="conf_user_pass">
              <div class="input-group-append">
                <div class="input-group-text">
                  <span class="fas fa-lock"></span>
                </div>
              </div>
            </div>
          </div>
          <div class="row justify-content-end">
            <div class="col-4">
              <button type="submit" class="btn btn-primary btn-block" id="btnSubmit">Submit</button>
            </div>
          </div>
        </form>
        <p class="mb-0">
          <a href="{{ route('auth.index') }}">Back to Login</a>
        </p>
      </div>

    </div>
  </div>
  <script>
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
            user_pass: {
              required: true,
            },
            conf_user_pass: {
              required: true,
              equalTo: "#user_pass",
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
              url: "{{ route('auth.reset.post') }}",
              data: $data,
              error: function() {
                btnSubmit.removeAttr('disabled', 'disabled').text('Submit');
              },
              complete: function() {
                btnSubmit.removeAttr('disabled', 'disabled').text('Submit');
              },
              success: function(res) {
                if (res.status) {
                  Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: 'Password berhasil direset. Silahkan login dengan password baru!',
                    showConfirmButton: false,
                    timer: 1500
                  });
                  setTimeout(() => {
                    window.location.replace("{{ route('auth.index') }}");
                  }, 1500);
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
      $.ajaxSetup({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
      });

      PageAdvanced.init();

      btnSubmit.click(function() {
        formVendor.submit();
      });
    });
  </script>
</body>

</html>
