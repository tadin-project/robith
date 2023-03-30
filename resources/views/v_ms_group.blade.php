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
              <input type="hidden" id="group_id" name="group_id">
              <div class="row form-group">
                <label class="col-md-3 control-label">Kode</label>
                <div class="col-md-5">
                  <input type="text" class="form-control" id="group_kode" name="group_kode">
                </div>
                <input type="hidden" id="old_group_kode">
              </div>
              <div class="row form-group">
                <label class="col-md-3 control-label">Nama</label>
                <div class="col-md-5">
                  <input type="text" class="form-control" id="group_nama" name="group_nama">
                </div>
              </div>
              <div class="row form-group">
                <label class="col-md-3 control-label">Status</label>
                <div class="col-md-3">
                  <select name="group_status" id="group_status" class="form-control">
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
            <table class="table table-sm table-striped table-bordered table-hover" id="tableVendor">
              <thead>
                <tr>
                  <th class="text-center">No</th>
                  <th class="text-center">Kode</th>
                  <th class="text-center">Nama</th>
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

<!-- Modal -->
<div class="modal fade" id="modalAkses" tabindex="-1" aria-labelledby="modalAksesLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalAksesLabel">Modal title</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form id="settingGroupMenu">
          <input type="hidden" name="setting_group_id" id="setting_group_id">
          <div id="list_menu"></div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
        <button type="button" id="btnSimpanMenu" class="btn btn-primary">Simpan</button>
      </div>
    </div>
  </div>
</div>

<script>
  // init component
  // global
  const baseDir = baseUrl + '/ms-groups',
    rowForm = $("#rowForm"),
    rowData = $("#rowData");

  // form
  const formVendor = $("#formVendor"),
    act = $("#act"),
    groupId = $("#group_id"),
    groupNama = $("#group_nama"),
    groupKode = $("#group_kode"),
    oldGroupKode = $("#old_group_kode"),
    groupStatus = $("#group_status"),
    btnBatal = $("#btnBatal"),
    btnSimpan = $("#btnSimpan");

  // datatable
  const tableVendor = $("#tableVendor"),
    btnTambah = $("#btnTambah");

  const modalAkses = $("#modalAkses"),
    modalAksesLabel = $("#modalAksesLabel"),
    listMenu = $("#list_menu"),
    settingGroupId = $("#setting_group_id"),
    btnSimpanMenu = $("#btnSimpanMenu");

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
          targets: [1],
          width: "12%"
        }, {
          targets: [-1],
          width: "15%"
        }, {
          targets: [-2],
          width: "10%"
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
          group_kode: {
            required: true,
            remote: {
              url: baseDir + '/check-duplicate',
              cache: false,
              data: {
                act: function() {
                  return act.val();
                },
                key: "group_kode",
                val: function() {
                  return groupKode.val();
                },
                old: function() {
                  return act.val() == 'edit' ? oldGroupKode.val() : "";
                }
              }
            },
          },
        },
        messages: {
          group_nama: {
            remote: "Nama sudah digunakan. Gunakan yang lain",
          },
          group_kode: {
            remote: "Kode sudah digunakan. Gunakan yang lain",
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
    groupId.val('');
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
          groupId.val(id);
          groupNama.val(dt.group_nama);
          groupKode.val(dt.group_kode);
          oldGroupKode.val(dt.group_kode);
          groupStatus.val(dt.group_status);

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

  function fnDel(id, group_nama) {
    Swal.fire({
      title: `Apakah Anda yakin menghapus ${group_nama}?`,
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

  function fnAkses(id, nama) {
    modalAksesLabel.text('Setting Menu untuk Grup: ' + nama);
    modalAkses.modal('show');
    settingGroupId.val(id);
    listMenu.jstree(true).refresh(false, true);
  }

  function fnGetListMenu() {
    listMenu
      .jstree({
        "core": {
          "themes": {
            "responsive": false
          },
          // so that create works
          "check_callback": true,
          'data': {
            'url': function(node) {
              return baseDir + '/akses'
            },
            'data': function(node) {
              return {
                'parent_menu_id': node.id,
                'group_id': settingGroupId.val(),
              };
            }
          }
        },
        "types": {
          "default": {
            "icon": "fa fa-folder text-primary"
          },
          "file": {
            "icon": "fa fa-file  text-primary"
          }
        },
        "plugins": ["dnd", "wholerow", "checkbox", "state", "types"]
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

    btnSimpanMenu.click(function() {
      btnSimpanMenu.attr('disabled', 'disabled').text('Loading...');
      const data = {
        group_id: settingGroupId.val(),
        menu_id: [],
      };

      listMenu.find('.jstree-clicked').each(function(index, i) {
        data.menu_id.push($(i).attr('menu_id'));
      })

      listMenu.find('.jstree-undetermined').each(function(index, i) {
        data.menu_id.push($(i).parent().attr('menu_id'));
      })

      $.ajax({
        url: baseDir + '/akses',
        data: data,
        dataType: 'json',
        method: 'post',
        error: function(res) {
          btnSimpanMenu.removeAttr('disabled', 'disabled').text('Simpan');
        },
        complete: function() {
          btnSimpanMenu.removeAttr('disabled', 'disabled').text('Simpan');
        },
        success: function(res) {
          btnSimpanMenu.removeAttr('disabled', 'disabled').text('Simpan');
          if (res.status) {
            Swal.fire({
              icon: 'success',
              title: 'Sukses',
              html: 'Data berhasil disimpan',
              showConfirmButton: false,
              timer: 1500
            })
            modalAkses.modal('hide');
            settingGroupId.val('');
            fnLoadTbl();
          } else {
            Swal.fire({
              title: 'Gagal',
              text: res.msg,
              icon: 'error',
            });
          }
        }
      })
    });

    fnGetListMenu();

  });
</script>
