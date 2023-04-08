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
              <input type="hidden" id="msk_id" name="msk_id">
              <div class="row form-group">
                <label class="col-md-3 control-label">Dimensi</label>
                <div class="col-md-4">
                  <select class="form-control" id="md_id" name="md_id">
                    @foreach ($optDimensi as $v)
                      <option value="{{ $v->md_id }}">{{ $v->md_kode }} - {{ $v->md_nama }}</option>
                    @endforeach
                  </select>
                </div>
              </div>
              <div class="row form-group">
                <label class="col-md-3 control-label">Kriteria</label>
                <div class="col-md-5">
                  <select class="form-control" id="mk_id" name="mk_id">
                  </select>
                </div>
              </div>
              <div class="row form-group">
                <label class="col-md-3 control-label">Kode</label>
                <div class="col-md-2">
                  <input type="text" class="form-control" id="msk_kode" name="msk_kode" maxlength="2">
                </div>
                <input type="hidden" id="old_msk_kode">
              </div>
              <div class="row form-group">
                <label class="col-md-3 control-label">Nama</label>
                <div class="col-md-5">
                  <input type="text" class="form-control" id="msk_nama" name="msk_nama">
                </div>
              </div>
              <div class="row form-group">
                <label class="col-md-3 control-label">Bobot</label>
                <div class="col-md-2">
                  <input type="text" class="form-control" id="msk_bobot" name="msk_bobot">
                </div>
              </div>
              <div class="row form-group">
                <label class="col-md-3 control-label">Perlu Submission?</label>
                <div class="col-md-2">
                  <select name="msk_is_submission" id="msk_is_submission" class="form-control">
                    <option value="1">Ya</option>
                    <option value="0">Tidak</option>
                  </select>
                </div>
              </div>
              <div class="row form-group">
                <label class="col-md-3 control-label">Status</label>
                <div class="col-md-2">
                  <select name="msk_status" id="msk_status" class="form-control">
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
            <div class="row">
              <div class="col-md-6">
                <div class="row form-group">
                  <label class="col-md-3 control-label">Dimensi</label>
                  <div class="col-md-9">
                    <select class="form-control" id="fil_md_id" name="fil_md_id">
                      @foreach ($optDimensi as $v)
                        <option value="{{ $v->md_id }}">{{ $v->md_kode }} - {{ $v->md_nama }}</option>
                      @endforeach
                    </select>
                  </div>
                </div>
              </div>
              <div class="col-md-6">
                <div class="row form-group">
                  <label class="col-md-3 control-label">Kriteria</label>
                  <div class="col-md-9">
                    <select class="form-control" id="fil_mk_id" name="fil_mk_id">
                    </select>
                  </div>
                </div>
              </div>
            </div>
            <table class="table table-sm table-striped table-bordered table-hover" id="tableVendor"
              style="width: 100%">
              <thead>
                <tr>
                  <th class="text-center">No</th>
                  <th class="text-center">Kode</th>
                  <th class="text-center">Nama</th>
                  <th class="text-center">Bobot</th>
                  <th class="text-center">Butuh<br>Submission</th>
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
  const baseDir = baseUrl + '/ms-sub-kriteria',
    rowForm = $("#rowForm"),
    rowData = $("#rowData");

  // form
  const formVendor = $("#formVendor"),
    act = $("#act"),
    mskId = $("#msk_id"),
    mdId = $("#md_id"),
    mkId = $("#mk_id"),
    mskNama = $("#msk_nama"),
    mskKode = $("#msk_kode"),
    oldMskKode = $("#old_msk_kode"),
    mskBobot = $("#msk_bobot"),
    mskIsSubmission = $("#msk_is_submission"),
    mskStatus = $("#msk_status"),
    btnBatal = $("#btnBatal"),
    btnSimpan = $("#btnSimpan");

  // datatable
  const tableVendor = $("#tableVendor"),
    filMdId = $("#fil_md_id"),
    filMkId = $("#fil_mk_id"),
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
          data: function(d) {
            d.fil_mk_id = filMkId.val();
          },
        },
        columnDefs: [{
          targets: [0, -1],
          orderable: false,
        }, {
          targets: [-4],
          className: "text-right"
        }, {
          targets: [0, -3, -2, -1],
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
          msk_nama: {
            required: true,
          },
          msk_kode: {
            required: true,
            remote: {
              url: baseDir + '/check-duplicate',
              cache: false,
              data: {
                act: function() {
                  return act.val();
                },
                key: ["msk_kode", "mk_id"],
                val: function() {
                  return [mskKode.val(), mkId.val()];
                },
                old: function() {
                  return act.val() == 'edit' ? oldMskKode.val() : "";
                }
              }
            },
          },
        },
        messages: {
          msk_kode: {
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
      mdId.val(filMdId.val());
      fnGetKriteria("#" + mkId.attr("id"), filMdId.val(), filMkId.val());
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
    mskId.val('');
    oldMskKode.val('');
  }

  function fnLoadTbl() {
    tableVendor.DataTable().draw()
  }

  function fnGetKriteria(target, dimensi_id, selectedId = "") {
    $(target).find("option[value!=0]").remove();
    $.ajax({
      url: "{{ route('ms-sub-kriteria.get-kriteria') }}",
      cache: false,
      dataType: 'json',
      data: {
        dimensi_id: dimensi_id
      },
      success: function(res) {
        if (res.status) {
          if (res.data.length > 0) {
            let opt = "",
              selected = "";
            $.each(res.data, function(index, i) {
              selected = i.mk_id == selectedId ? "selected" : "";
              opt += `<option value="${i.mk_id}" ${selected}>${i.mk_kode} - ${i.mk_nama}</option>`;
            });
            $(target).append(opt);
            if (target == "#fil_mk_id") {
              fnLoadTbl();
            }
          }
        } else {
          Swal.fire("Error", res.msg, "error");
        }
      }
    })
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
          mskId.val(id);
          mdId.val(dt.md_id);
          fnGetKriteria("#" + mkId.attr("id"), dt.md_id, dt.mk_id);
          mskKode.val(dt.msk_kode);
          oldMskKode.val(dt.msk_kode);
          mskNama.val(dt.msk_nama);
          mskBobot.val(dt.msk_bobot);
          mskStatus.val(dt.msk_status);
          mskIsSubmission.val(dt.msk_is_submission);

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

    filMkId.change(function() {
      fnLoadTbl();
    });

    filMdId.change(function() {
      fnGetKriteria("#" + filMkId.attr("id"), $(this).val());
    });

    mdId.change(function() {
      fnGetKriteria("#" + mkId.attr("id"), $(this).val());
    });

    fnGetKriteria("#" + filMkId.attr("id"), filMdId.val());
  });
</script>
