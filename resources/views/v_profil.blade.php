<!-- Content Header (Page header) -->
<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-12">
        <h1 class="m-0">{{ $__title }}</h1>
      </div><!-- /.col -->
    </div><!-- /.row -->
  </div><!-- /.container-fluid -->
</div>
<!-- /.content-header -->

<!-- Main content -->
<section class="content pb-2">
  <div class="container-fluid">
    <div class="row">
      <div class="col-md-6">
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">Profil</h3>
          </div>
          <div class="card-body">
            <form id="formProfil">
              <div class="form-group">
                <label class="control-label">Username</label>
                <input type="text" class="form-control" id="user_name" readonly
                  value="{{ $user_data['user_name'] }}">
              </div>
              <div class="form-group">
                <label class="control-label">Email</label>
                <input type="text" class="form-control" id="user_email" readonly
                  value="{{ $user_data['user_email'] }}">
              </div>
              <div class="form-group">
                <label class="control-label">Nama Lengkap</label>
                <input type="text" class="form-control" id="user_fullname" name="user_fullname"
                  value="{{ $user_data['user_fullname'] }}">
              </div>
              <div class="form-group">
                <label class="control-label">Foto Profil</label>
                <div class="row mb-3">
                  <div class="col-12">
                    <img src="" alt="" id="prevImgProfile" style="max-height: 128px;">
                  </div>
                </div>
                <input type="file" id="user_profile" name="user_profile">
              </div>
              <div id="rowTenant" style="display: none">
                <div class="form-group">
                  <label class="control-label">Nama Tenant</label>
                  <input type="text" class="form-control" id="tenant_nama" name="tenant_nama"
                    value="{{ $is_tenant ? $user_data['tenant']['tenant_nama'] : '' }}">
                </div>
                <div class="form-group">
                  <label class="control-label">Deskripsi Tenant</label>
                  <textarea class="form-control" id="tenant_desc" name="tenant_desc">{{ $is_tenant ? $user_data['tenant']['tenant_desc'] : '' }}</textarea>
                </div>
                <div class="form-group">
                  <label class="control-label">Kategori Usaha</label>
                  <select class="form-control" id="mku_id" name="mku_id">
                    @foreach ($kategori_usaha as $v)
                      <option value="{{ $v->mku_id }}"
                        {{ $is_tenant && $v->mku_id == $user_data['tenant']['mku_id'] ? 'selected' : '' }}>
                        {{ $v->mku_nama }}</option>
                    @endforeach
                  </select>
                </div>
              </div>
            </form>
          </div>
          <div class="card-footer">
            <div class="row text-center">
              <div class="col-md-12">
                <button class="btn btn-primary" type="button" id="btnSimpanProfil">Perbarui</button>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-6">
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">Form Ganti Password</h3>
          </div>
          <div class="card-body">
            <form id="formGantiPass">
              <div class="form-group">
                <label class="control-label">Password Lama</label>
                <input type="password" class="form-control" id="old_user_pass" name="old_user_pass">
              </div>
              <div class="form-group">
                <label class="control-label">Password Baru</label>
                <input type="password" class="form-control" id="user_pass" name="user_pass">
              </div>
              <div class="form-group">
                <label class="control-label">Retype Password Baru</label>
                <input type="password" class="form-control" id="conf_user_pass" name="conf_user_pass">
              </div>
            </form>
          </div>
          <div class="card-footer">
            <div class="row text-center">
              <div class="col-md-12">
                <button class="btn btn-primary" type="button" id="btnSimpanPass">Perbarui Password</button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div><!-- /.container-fluid -->
</section>
<!-- /.content -->

<script>
  // init component
  // global
  const baseDir = baseUrl + '/profil',
    rowTenant = $("#rowTenant"),
    isTenant = "{{ $is_tenant ? 'true' : 'false' }}";
  let imgProfile =
    "{{ !empty($user_data['user_profile']) ? asset('uploads/profile/' . $user_data['user_profile']) : asset('uploads/profile/default.png') }}";

  // form profil
  const formProfil = $("#formProfil"),
    prevImgProfile = $("#prevImgProfile"),
    userProfile = $("#user_profile"),
    btnSimpanProfil = $("#btnSimpanProfil");

  // form ganti password
  const formGantiPass = $("#formGantiPass"),
    btnSimpanPass = $("#btnSimpanPass");

  // Class definition
  var PageAdvanced = function() {

    var initFormProfil = function() {
      formProfil.validate({
        errorClass: 'help-block',
        errorElement: 'span',
        ignore: 'input[type=hidden]',
        rules: {
          tenant_nama: {
            required: function() {
              if (isTenant == "true") {
                return true;
              } else {
                return false;
              }
            },
          },
          mku_id: {
            required: function() {
              if (isTenant == "true") {
                return true;
              } else {
                return false;
              }
            },
          },
        },
        messages: {},
        highlight: function(el, errorClass) {
          $(el).parents('.form-group').first().addClass('has-error');
        },
        unhighlight: function(el, errorClass) {
          var $parent = $(el).parents('.form-group').first();
          $parent.removeClass('has-error');
          $parent.find('.help-block').hide();
        },
        errorPlacement: function(error, el) {
          error.appendTo(el.parents('.form-group'));
        },
        submitHandler: function(form) {
          btnSimpanProfil.attr('disabled', 'disabled').text('Loading...');
          var formData = new FormData();
          formData.append('user_fullname', $("#user_fullname").val());
          formData.append('tenant_nama', $("#tenant_nama").val());
          formData.append('tenant_desc', $("#tenant_desc").val());
          formData.append('mku_id', $("#mku_id").val());

          if (userProfile[0].files.length > 0) {
            formData.append('user_profile', userProfile[0].files[0]);
          }

          $.ajax({
            type: 'POST',
            url: baseDir,
            dataType: 'json',
            contentType: false,
            processData: false,
            data: formData,
            error: function() {
              btnSimpanProfil.removeAttr('disabled', 'disabled').text('Perbarui');
            },
            complete: function() {
              btnSimpanProfil.removeAttr('disabled', 'disabled').text('Perbarui');
            },
            success: function(res) {
              if (res.status) {
                Swal.fire({
                  icon: 'success',
                  title: 'Data berhasil diperbarui!',
                  showConfirmButton: false,
                  timer: 1500
                });

                window.location.replace(window.location.href);
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

    var initFormGantiPass = function() {
      formGantiPass.validate({
        errorClass: 'help-block',
        errorElement: 'span',
        ignore: 'input[type=hidden]',
        rules: {
          old_user_pass: {
            required: true,
          },
          user_pass: {
            required: true,
          },
          conf_user_pass: {
            required: true,
            equalTo: "#user_pass",
          },
        },
        messages: {},
        highlight: function(el, errorClass) {
          $(el).parents('.form-group').first().addClass('has-error');
        },
        unhighlight: function(el, errorClass) {
          var $parent = $(el).parents('.form-group').first();
          $parent.removeClass('has-error');
          $parent.find('.help-block').hide();
        },
        errorPlacement: function(error, el) {
          error.appendTo(el.parents('.form-group'));
        },
        submitHandler: function(form) {
          btnSimpanPass.attr('disabled', 'disabled').text('Loading...')
          var $data = $(form).serialize();
          $.ajax({
            type: 'POST',
            url: baseDir + "/ganti-password",
            data: $data,
            error: function() {
              btnSimpanPass.removeAttr('disabled', 'disabled').text('Perbarui Password');
            },
            complete: function() {
              btnSimpanPass.removeAttr('disabled', 'disabled').text('Perbarui Password');
            },
            success: function(res) {
              if (res.status) {
                Swal.fire({
                  icon: 'success',
                  title: 'Password berhasil diperbarui!',
                  showConfirmButton: false,
                  timer: 1500
                });

                window.location.replace(window.location.href);
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
        initFormProfil();
        initFormGantiPass();
      }
    }
  }();

  $(document).ready(function() {
    PageAdvanced.init();
    prevImgProfile.attr('src', imgProfile);

    if (isTenant == "true") {
      rowTenant.show();
    } else {
      rowTenant.hide();
    }

    userProfile.change(function() {
      var input = this;
      var url = $(this).val();
      var ext = url.substring(url.lastIndexOf('.') + 1).toLowerCase();
      if (input.files && input.files[0] && (ext == "gif" || ext == "png" || ext == "jpeg" || ext == "jpg")) {
        var reader = new FileReader();

        reader.onload = function(e) {
          prevImgProfile.attr('src', e.target.result);
        }
        reader.readAsDataURL(input.files[0]);
      } else {
        prevImgProfile.attr('src', imgProfile);
      }
    });

    btnSimpanProfil.click(function() {
      formProfil.submit();
    });

    btnSimpanPass.click(function() {
      formGantiPass.submit();
    });
  });
</script>
