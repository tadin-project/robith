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
      </div>
      <div class="card-body">
        <div class="row" id="rowForm" style="display:none;">
          <div class="col-12">
            <form id="formVendor">
              <input type="hidden" id="as_id" name="as_id">
              <input type="hidden" id="as_jenis" name="as_jenis">
              <div class="row form-group">
                <label class="col-md-3 control-label">Nama</label>
                <div class="col-md-5">
                  <input type="text" class="form-control" id="as_nama" name="as_nama" disabled>
                </div>
              </div>
              <div class="row form-group">
                <label class="col-md-3 control-label">Deskripsi</label>
                <div class="col-md-5">
                  <textarea class="form-control" id="as_desc" name="as_desc" disabled></textarea>
                </div>
              </div>
              <div class="row form-group">
                <label class="col-md-3 control-label">Isi</label>
                <div class="col-md-5 as-value" id="colInput">
                  <textarea class="form-control" id="as_value_text" name="as_value_text"></textarea>
                </div>
                <div class="col-md-5 as-value" id="colGambar" style="display: none;">
                  <div class="mb-3 bg-secondary" style="max-width: 300px">
                    <img src="{{ asset('') }}/assets/img/default-file.png" alt="" id="prevAsValueGambar"
                      class="w-100">
                  </div>
                  <input type="file" id="as_value_gambar" name="as_value_gambar" />
                </div>
              </div>
              <div class="row text-center">
                <div class="col-md-12">
                  <button type="button" class="btn btn-sm btn-secondary" id="btnBatal">Batal</button>
                  <button type="button" class="btn btn-sm btn-primary" id="btnSimpan">Simpan</button>
                </div>
              </div>
            </form>
          </div>
        </div>
        <div class="row" id="rowData">
          <div class="col-12">
            <table class="table table-sm table-striped table-bordered table-hover" id="tableVendor" style="width: 100%">
              <thead>
                <tr>
                  <th class="text-center">No</th>
                  <th class="text-center">Nama</th>
                  <th class="text-center">Isi</th>
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
  const baseDir = baseUrl + '/app-settings',
    dfImg = baseUrl + "/assets/img/default-file.png",
    rowForm = $("#rowForm"),
    rowData = $("#rowData");

  // form
  const formVendor = $("#formVendor"),
    asId = $("#as_id"),
    asJenis = $("#as_jenis"),
    asNama = $("#as_nama"),
    asDesc = $("#as_desc"),
    colInput = $("#colInput"),
    asValueText = $("#as_value_text"),
    colGambar = $("#colGambar"),
    prevAsValueGambar = $("#prevAsValueGambar"),
    asValueGambar = $("#as_value_gambar"),
    btnBatal = $("#btnBatal"),
    btnSimpan = $("#btnSimpan");

  // datatable
  const tableVendor = $("#tableVendor");

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
          targets: [0, -1],
          className: "text-center"
        }, {
          targets: [0],
          width: "5%"
        }, {
          targets: [-1],
          width: "12%"
        }, ],
        language: {
          lengthMenu: "Show _MENU_",
        },
      });

      table = dt.$;
    }

    // Public methods
    return {
      init: function() {
        initDatatable();
      }
    }
  }();

  function fnShowForm(isShow = true) {
    fnResetForm();
    if (isShow) {
      rowForm.slideDown(500);
      rowData.slideUp(500);
    } else {
      rowForm.slideUp(500);
      rowData.slideDown(500);
    }
  }

  function fnResetForm() {
    formVendor[0].reset();
    formVendor.validate().resetForm();
    $('.has-error').removeClass('has-error');
    asId.val('');
    asJenis.val(1);
    $('.as-value').hide();
    colInput.show();
    asValueText.val('');
    asValueGambar.val('');
    prevAsValueGambar.attr('src', dfImg);
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

          asId.val(id);
          asJenis.val(dt.as_jenis);
          asNama.val(dt.as_nama);
          asDesc.val(dt.as_desc);

          $('.as-value').hide();
          if (dt.as_jenis == 2) {
            prevAsValueGambar.attr('src', dt.as_value)
            colGambar.show();
          } else {
            asValueText.val(dt.as_value);
            colInput.show();
          }

          rowForm.slideDown(500);
          rowData.slideUp(500);
        } else {
          Swal.fire('Error', res.msg, 'error');
        }
      },
    });
  }

  function fnFileOnChange(input, target) {
    var url = $(input).val();
    var ext = url.substring(url.lastIndexOf('.') + 1).toLowerCase();
    console.log(ext);
    console.log($(input)[0].files);
    if ($(input)[0].files && $(input)[0].files[0] && (ext == "gif" || ext == "png" || ext == "jpeg" || ext == "jpg")) {
      var reader = new FileReader();

      reader.onload = function(e) {
        $(target).attr('src', e.target.result);
      }
      reader.readAsDataURL($(input)[0].files[0]);
    } else {
      $(target).attr('src', dfImg);
    }
  }

  $(document).ready(function() {
    PageAdvanced.init();

    btnBatal.click(function() {
      fnShowForm(false);
      fnLoadTbl();
    });

    btnSimpan.click(function() {
      formVendor.submit();
    });

    asValueGambar.change(function() {
      fnFileOnChange('#' + asValueGambar.attr("id"), '#' + prevAsValueGambar.attr("id"));
    });

    formVendor.submit(async function(e) {
      e.preventDefault();
      let messageError = "";
      if (asJenis.val() == 2) {
        if (asValueGambar[0].files.length <= 0) {
          messageError = "Pilih gambar terlebih dahulu!";
        }
      } else {
        if (asValueText.val().trim() == "") {
          messageError = "Isi tidak boleh kosong!";
        }
      }

      if (messageError != "") {
        Swal.fire("Error", messageError, "error");
        return;
      }

      btnSimpan.attr('disabled', 'disabled').text('Loading...');

      const formData = new FormData();
      formData.append("as_id", asId.val());
      formData.append("as_jenis", asJenis.val());
      if (asJenis.val() == 2) {
        await formData.append("as_value", asValueGambar[0].files[0]);
      } else {
        formData.append("as_value", asValueText.val());
      }

      $.ajax({
        type: 'POST',
        url: baseDir,
        data: formData,
        cache: false,
        contentType: false,
        processData: false,
        dataType: "json",
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
    });

  });
</script>
