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
    <div class="card">
      <div class="card-header">
        <h3 class="card-title">
          <i class="ion ion-clipboard mr-1"></i> {{ $__title }}
        </h3>
        <div class="card-tools">
          <button class="btn btn-sm btn-danger" title="Batal Tambah Data" id="btnBatal" style="display: none"><i
              class="fas fa-times"></i></button>
          <button class="btn btn-sm btn-primary" title="Tambah Data" id="btnTambah"><i class="fas fa-plus"></i>
            Data</button>
        </div>
      </div>
      <div class="card-body">
        <div class="row" id="rowForm" style="display:none;">
          <div class="col-12">
            <form id="formVendor">
              <input type="hidden" id="act" name="act" value="add">
              <input type="hidden" id="user_id" name="user_id">
              <div class="row form-group">
                <label class="col-md-3 control-label">Username</label>
                <div class="col-md-5">
                  <input type="text" class="form-control" id="user_name" name="user_name">
                </div>
                <input type="hidden" id="old_user_name">
              </div>
              <div class="row form-group">
                <label class="col-md-3 control-label">Nama Lengkap</label>
                <div class="col-md-5">
                  <input type="text" class="form-control" id="user_fullname" name="user_fullname">
                </div>
              </div>
              <div class="row form-group">
                <label class="col-md-3 control-label">Email</label>
                <div class="col-md-5">
                  <input type="text" class="form-control" id="user_email" name="user_email">
                </div>
                <input type="hidden" id="old_user_email">
              </div>
              <div class="row form-group">
                <label class="col-md-3 control-label">Password</label>
                <div class="col-md-5">
                  <input type="password" class="form-control" id="user_password" name="user_password">
                </div>
                <div class="col-md-3" id="colGantiPass" style="display: none">
                  <div class="form-check">
                    <input class="form-check-input" id="is_ganti_pass" name="is_ganti_pass" type="checkbox">
                    <label class="form-check-label" for="is_ganti_pass">Ganti Password</label>
                  </div>
                </div>
              </div>
              <div class="row form-group">
                <label class="col-md-3 control-label">Confirm Password</label>
                <div class="col-md-5">
                  <input type="password" class="form-control" id="confirm_password" name="confirm_password">
                </div>
              </div>
              <div class="row form-group">
                <label class="col-md-3 control-label">Hak Akses</label>
                <div class="col-md-3">
                  <select name="group_id" id="group_id" class="form-control">
                    @foreach ($opt_group as $v)
                      <option value="{{ $v->group_id }}">{{ $v->group_nama }}</option>
                    @endforeach
                  </select>
                </div>
              </div>
              <div class="row form-group">
                <label class="col-md-3 control-label">Status</label>
                <div class="col-md-3">
                  <select name="user_status" id="user_status" class="form-control">
                    <option value="1">Aktif</option>
                    <option value="0">Non Aktif</option>
                  </select>
                </div>
              </div>
              <div class="row text-center">
                <div class="col-md-12">
                  <button type="button" class="btn btn-sm btn-secondary" onclick="btnBatal.click();">Batal</button>
                  <button type="button" class="btn btn-sm btn-primary" id="btnSimpan">Simpan</button>
                </div>
              </div>
            </form>
          </div>
        </div>
        <div class="row" id="rowData">
          <div class="col-12">
            <table class="table table-sm table-striped table-bordered table-hover" id="tableVendor"
              style="width: 100%">
              <thead>
                <tr>
                  <th class="text-center">No</th>
                  <th class="text-center">Username</th>
                  <th class="text-center">Nama Lengkap</th>
                  <th class="text-center">Email</th>
                  <th class="text-center">Hak Akses</th>
                  <th class="text-center">Status</th>
                  <th class="text-center">Aksi</th>
                </tr>
              </thead>
            </table>
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
  const baseDir = baseUrl + '/ms-users',
    rowForm = $("#rowForm"),
    rowData = $("#rowData");

  // form
  const formVendor = $("#formVendor"),
    act = $("#act"),
    userId = $("#user_id"),
    userFullname = $("#user_fullname"),
    userName = $("#user_name"),
    oldUserName = $("#old_user_name"),
    userEmail = $("#user_email"),
    oldUserEmail = $("#old_user_email"),
    userPassword = $("#user_password"),
    confirmPassword = $("#confirm_password"),
    isGantiPass = $("#is_ganti_pass"),
    colGantiPass = $("#colGantiPass"),
    groupId = $("#group_id"),
    userStatus = $("#user_status"),
    btnBatal = $("#btnBatal"),
    btnSimpan = $("#btnSimpan");

  // datatable
  const tableVendor = $("#tableVendor"),
    btnTambah = $("#btnTambah");

  // Class definition
  var PageAdvanced = function() {
    // Shared variables
    var table;
    var dt;

    // Private functions
    var initDatatable = function() {
      dt = tableVendor.DataTable({
        responsive: true,
        searchDelay: 500,
        processing: true,
        serverSide: true,
        order: [
          [1, 'asc']
        ],
        ajax: {
          url: baseDir + "/get-data",
        },
        columnDefs: [{
          targets: [0, -1],
          orderable: false,
        }, {
          targets: [0, -2, -1],
          className: "text-center"
        }, {
          targets: [0],
          width: "5%"
        }, {
          targets: [-1],
          width: "12%"
        }, {
          targets: [-2],
          width: "8%"
        }, ],
        language: {
          lengthMenu: "Show _MENU_",
        },
      });

      table = dt.$;
    }

    var initForm = function() {
      formVendor.validate({
        errorClass: 'help-block',
        errorElement: 'span',
        ignore: 'input[type=hidden]',
        rules: {
          user_name: {
            required: true,
            remote: {
              url: baseDir + '/check-duplicate',
              cache: false,
              data: {
                act: function() {
                  return act.val();
                },
                key: "user_name",
                val: function() {
                  return userName.val();
                },
                old: function() {
                  return act.val() == 'edit' ? oldUserName.val() : "";
                }
              }
            },
          },
          user_email: {
            required: true,
            remote: {
              url: baseDir + '/check-duplicate',
              cache: false,
              data: {
                act: function() {
                  return act.val();
                },
                key: "user_email",
                val: function() {
                  return userEmail.val();
                },
                old: function() {
                  return act.val() == 'edit' ? oldUserEmail.val() : "";
                }
              }
            },
          },
          user_password: {
            required: function() {
              if (act.val() == 'edit' && !isGantiPass.prop('checked')) {
                return false;
              }
              return true;
            }
          },
          confirm_password: {
            required: function() {
              if (act.val() == 'edit' && !isGantiPass.prop('checked')) {
                return false;
              }
              return true;
            },
            equalTo: "#user_password",
          },
          group_id: {
            required: true,
          }
        },
        messages: {
          user_name: {
            remote: "Username sudah digunakan. Gunakan yang lain",
          },
          user_email: {
            remote: "Email sudah digunakan. Gunakan yang lain",
          },
        },
        highlight: function(el, errorClass) {
          $(el).parents('.form-group').first().addClass('has-error');
        },
        unhighlight: function(el, errorClass) {
          var $parent = $(el).parents('.form-group').first();
          $parent.removeClass('has-error');
          $parent.find('.help-block').hide();
        },
        errorPlacement: function(error, el) {
          error.appendTo(el.parents('.form-group').find('div:first'));
        },
        submitHandler: function(form) {
          btnSimpan.attr('disabled', 'disabled').text('Loading...')
          var $data = $(form).serialize();
          $.ajax({
            type: 'POST',
            url: baseDir,
            data: $data,
            error: function() {
              btnSimpan.removeAttr('disabled', 'disabled').text('Simpan');
            },
            complete: function() {
              btnSimpan.removeAttr('disabled', 'disabled').text('Simpan');
            },
            success: function(res) {
              if (res.status) {
                Swal.fire({
                  icon: 'success',
                  title: 'Data berhasil disimpan!',
                  showConfirmButton: false,
                  timer: 1500
                });

                btnBatal.click();
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
        initDatatable();
        initForm();
      }
    }
  }();

  function fnShowForm(isShow = true) {
    fnResetForm();
    if (isShow) {
      rowForm.slideDown(500);
      rowData.slideUp(500);
      btnBatal.show();
      btnTambah.hide();
    } else {
      rowForm.slideUp(500);
      rowData.slideDown(500);
      btnBatal.hide();
      btnTambah.show();
    }
  }

  function fnResetForm() {
    formVendor[0].reset();
    formVendor.validate().resetForm();
    $('.has-error').removeClass('has-error');
    act.val('add');
    userId.val('');
    oldUserEmail.val('');
    oldUserName.val('');
    colGantiPass.hide();
    userPassword.removeAttr('disabled', 'disabled');
    confirmPassword.removeAttr('disabled', 'disabled');
  }

  function fnLoadTbl() {
    tableVendor.DataTable().draw()
  }

  function fnEdit(id) {
    $.ajax({
      url: baseDir + '/' + id,
      dataType: 'json',
      cache: false,
      success: function(res) {
        if (res.status) {
          var dt = res.data;

          act.val('edit');
          userId.val(id);
          userFullname.val(dt.user_fullname);
          userName.val(dt.user_name);
          oldUserName.val(dt.user_name);
          userEmail.val(dt.user_email);
          oldUserEmail.val(dt.user_email);
          userStatus.val(dt.user_status);
          groupId.val(dt.group_id);

          colGantiPass.show();
          userPassword.attr("disabled", "disabled")
          confirmPassword.attr("disabled", "disabled")

          rowForm.slideDown(500);
          rowData.slideUp(500);
          btnBatal.show();
          btnTambah.hide();
        } else {
          Swal.fire('Error', res.msg, 'error');
        }
      },
    });
  }

  function fnDel(id, nama) {
    Swal.fire({
      title: `Apakah Anda yakin menghapus ${nama}?`,
      text: "Data yang dihapus tidak dapat dikembalikan!",
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: 'Iya',
      cancelButtonText: 'Tidak',
    }).then((result) => {
      if (result.isConfirmed) {
        $.ajax({
          url: baseDir + '/' + id,
          method: 'delete',
          dataType: 'json',
          cache: false,
          success: function(res) {
            if (res.status) {
              Swal.fire({
                icon: 'success',
                title: 'Data berhasil dihapus!',
                showConfirmButton: false,
                timer: 1500
              });
              fnLoadTbl();
            } else {
              Swal.fire('Error', res.msg, 'error');
            }
          },
        });
      }
    })
  }

  $(document).ready(function() {
    PageAdvanced.init();

    btnTambah.click(function() {
      fnShowForm();
    });

    btnBatal.click(function() {
      fnShowForm(false);
      fnLoadTbl();
    });

    btnSimpan.click(function() {
      formVendor.submit();
    });

    isGantiPass.change(function() {
      if ($(this).prop('checked')) {
        userPassword.removeAttr("disabled", "disablede");
        confirmPassword.removeAttr("disabled", "disablede");
      } else {
        userPassword.attr("disabled", "disablede");
        confirmPassword.attr("disabled", "disablede");
      }
    })

  });
</script>
