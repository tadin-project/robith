@php
  $mkIdFirstIndex = $dtKriteria[0]['mk_id'];
@endphp
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
      <div class="overlay dark flex-column" id="spinnerForm" style="display: none;">
        <i class="fas fa-3x fa-sync-alt fa-spin"></i>
        <div class="text-bold pt-2 text-white">Loading...</div>
      </div>
      <div class="card-header">
        <h3 class="card-title">
          <i class="ion ion-clipboard mr-1"></i> {{ $__title }}
        </h3>
        <div class="card-tools">
          <button class="btn btn-sm btn-warning" title="Simpan Sementara" id="btnSimpanSementara">
            <i class="fas fa-save"></i> Simpan Sementara
          </button>
        </div>
      </div>
      <div class="card-body">
        <input type="hidden" id="prevTabValue" value="0">
        <input type="hidden" id="nextTabValue" value="{{ $dtKriteria[1]['mk_id'] }}">
        <div class="row">
          @foreach ($dtKriteria as $k => $v)
            <div class="col-12 colTitle" id="colTitle{{ $v['mk_id'] }}" style="{!! $mkIdFirstIndex == $v['mk_id'] ? '' : 'display:none' !!}">
              <h4>{{ $v['mk_nama'] }}</h4>
              <p>{{ $v['mk_desc'] }}</p>
              <input type="hidden" class="currentKriteria" value="{{ $v['mk_id'] }}">
              <input type="hidden" class="prevKriteria" value="{{ $k == 0 ? 0 : $dtKriteria[$k - 1]['mk_id'] }}">
              <input type="hidden" class="nextKriteria"
                value="{{ $k == count($dtKriteria) - 1 ? 'max' : $dtKriteria[$k + 1]['mk_id'] }}">
            </div>
          @endforeach
        </div>
        <div class="row" id="rowFormDetail">
          <div class="col-12">
            <table class="table table-striped table-bordered table-sm table-responsive-sm" style="width: 100%;">
              <thead>
                <tr>
                  <th class="text-center align-middle" rowspan="2">Subkriteria</th>
                  <th class="text-center" colspan="5">Penilaian</th>
                </tr>
                <tr>
                  @foreach ($dtConvertionValue as $a)
                    <th class="text-center align-middle" width="12%">{{ $a->cval_nama }}</th>
                  @endforeach
                </tr>
              </thead>
              <tbody>
                @foreach ($dtSubKriteria as $k => $v)
                  @foreach ($v as $k1 => $v1)
                    <tr class="colTr colTr{{ $k }}" style="{!! $mkIdFirstIndex == $k ? '' : 'display:none' !!}">
                      <td>
                        {{ $v1['msk_nama'] }}
                        <input type="hidden" class="msk-id" id="msk_id{{ $v1['msk_id'] }}"
                          value="{{ $v1['msk_id'] }}">
                        <input type="hidden" class="asd-id" id="asd_id{{ $v1['msk_id'] }}" value="">
                        @if ($v1['msk_is_submission'] == 1)
                          <span id="fileName{{ $v1['msk_id'] }}"></span>
                          <br>
                          <input type="file" class="asd-file" id="asd_file{{ $v1['msk_id'] }}">
                        @endif
                      </td>
                      @foreach ($dtConvertionValue as $a)
                        <td class="text-center align-middle">
                          <input type="radio" class="asd-value" name="asd_value{{ $v1['msk_id'] }}"
                            value="{{ $a->cval_nilai }}">
                        </td>
                      @endforeach
                    </tr>
                  @endforeach
                @endforeach
              </tbody>
            </table>
          </div> <!-- ./col-12 -->
        </div> <!-- ./row -->
        <div class="row rowBottomNavForm">
          <div class="col-md-12 text-right">
            <button class="btn btn-secondary" type="button" onclick="fnPrevTab()" id="btnPrevTab"
              style="display: none">
              <i class="fas fa-arrow-left"></i> Previous
            </button>
            <button class="btn btn-primary" type="button" onclick="fnNextTab()" id="btnNextTab">
              Next <i class="fas fa-arrow-right"></i>
            </button>
            <button class="btn btn-success" title="Simpan Final" id="btnSimpanFinal" style="display: none">
              <i class="fas fa-save"></i> Simpan Final
            </button>
          </div> <!-- ./col-md-12 text-right -->
        </div> <!-- ./row -->
      </div>
    </div>
  </div><!-- /.container-fluid -->
</section>
<!-- /.content -->

<script>
  // form
  const spinnerForm = $("#spinnerForm"),
    rowFormDetail = $("#rowFormDetail"),
    prevTabValue = $("#prevTabValue"),
    nextTabValue = $("#nextTabValue"),
    btnPrevTab = $("#btnPrevTab"),
    btnNextTab = $("#btnNextTab"),
    btnSimpanSementara = $("#btnSimpanSementara"),
    btnSimpanFinal = $("#btnSimpanFinal");

  let listHapusLampiran = [];

  function fnCek() {
    spinnerForm.show()
    $.ajax({
      url: "{{ route('asesmen.cek-data') }}",
      dataType: 'json',
      cache: false,
      error: function(err) {
        spinnerForm.hide();
        Swal.fire("Error", err, "error")
      },
      complete: function(err) {
        spinnerForm.hide();
      },
      success: function(res) {
        if (res.status) {
          if (res.data == 0) {
            return;
          }

          const dt = res.data;
          if (dt.as_status > 0) {
            btnSimpanFinal.hide();
            btnSimpanSementara.hide();
            rowFormDetail.find("input[type=file]").hide();
            rowFormDetail.find("input[type=radio]").attr("disabled", "disabled");
          } else {
            btnSimpanSementara.show();
            rowFormDetail.find("input[type=file]").show();
            rowFormDetail.find("input[type=radio]").removeAttr("disabled", "disabled");
          }

          let btnHapus = "";
          $.each(dt.detail, function(index, i) {
            $("[name=asd_value" + i.msk_id + "][value=" + i.asd_value + "]").prop("checked", true);
            $("#asd_id" + i.msk_id).val(i.id_detail)
            if (i.asd_file) {
              btnHapus = dt.as_status > 0 ? "" :
                ` <button class="btn btn-sm btn-danger" type="button" onclick="fnHapusLampiran(this,${i.id_detail})"><i class="fa fa-times"></i></button>`;
              $("#fileName" + i.msk_id).html(
                ` (<a target="_blank" href="{{ url($dirUploads) }}/${i.asd_file}">${i.asd_file}</a>)${btnHapus}`
              )
            }
          });
        } else {
          Swal.fire("Error", res.msg, "error")
        }
      },
    })
  }

  function fnResetForm() {
    listHapusLampiran = [];
    rowFormDetail.find('input[type=file]').val('');
    rowFormDetail.find('input[type=radio]').prop('checked', false);
  }

  function fnSimpanSementara() {
    fnSetBtnOnSubmit("tmp", "disabled");
    let formData = new FormData();
    let asdValue = [],
      mskId = [],
      idDetail = [],
      asdFile = [];
    $.each($('.asd-value:checked'), function(index, i) {
      const tr = $(i).closest('.colTr');
      const mskIdVal = tr.find('.msk-id').val();

      formData.append("msk_id[" + index + "]", mskIdVal);
      formData.append("asd_value[" + index + "]", $(i).val());

      if (!fnIsEmpty($("#asd_id" + mskIdVal).val())) {
        formData.append("asd_id[" + index + "]", $("#asd_id" + mskIdVal).val());
      }

      if ($("#asd_file" + mskIdVal).length > 0) {
        if ($("#asd_file" + mskIdVal)[0].files.length > 0) {
          formData.append("lampiran[" + index + "]", $("#asd_file" + mskIdVal)[0].files[0]);
        }
      }
    });
    formData.append("list_hapus_lampiran", listHapusLampiran);

    $.ajax({
      url: "{{ route('asesmen.save-tmp') }}",
      cache: false,
      data: formData,
      contentType: false,
      processData: false,
      type: "post",
      dataType: "json",
      error: function(err) {
        Swal.fire("Error", err, "error");
        fnSetBtnOnSubmit();
      },
      complete: function() {
        fnSetBtnOnSubmit();
      },
      success: function(res) {
        if (res.status) {
          Swal.fire({
            icon: 'success',
            title: 'Data berhasil disimpan!',
            showConfirmButton: false,
            timer: 1500
          });
          fnResetForm();
          fnCek();
        } else {
          Swal.fire("Error", res.msg, "error")
        }
      }
    })
  }

  function fnSetBtnOnSubmit(target, status) {
    if (status == "disabled") {
      btnSimpanSementara.attr("disabled", "disabled");
      btnSimpanFinal.attr("disabled", "disabled");
      if (target == "tmp") {
        btnSimpanSementara.text("Loading...");
      } else {
        btnSimpanFinal.text("Loading...");
      }
      spinnerForm.show();
    } else {
      spinnerForm.hide();
      btnSimpanSementara.removeAttr("disabled", "disabled").text("Simpan Sementara");
      btnSimpanFinal.removeAttr("disabled", "disabled").text("Simpan Final");
    }
  }

  function fnHapusLampiran(e, id) {
    Swal.fire({
      text: "Apakah Anda yakin ingin menghapus file bukti?",
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: 'Iya',
      cancelButtonText: 'Tidak',
    }).then((result) => {
      if (result.isConfirmed) {
        listHapusLampiran.push(id);
        $(e).closest("span").text("");
      }
    })
  }

  function fnSimpanFinal() {
    Swal.fire({
      title: "Apakah Anda yakin ingin memfinalisasi ini?",
      text: "Data yang sudah difinalisasi, tidak dapat dirubah",
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: 'Iya',
      cancelButtonText: 'Tidak',
    }).then((result) => {
      if (result.isConfirmed) {
        fnSetBtnOnSubmit("final", "disabled");
        let formData = new FormData();
        let asdValue = [],
          mskId = [],
          idDetail = [],
          asdFile = [];
        $.each($('.asd-value:checked'), function(index, i) {
          const tr = $(i).closest('.colTr');
          const mskIdVal = tr.find('.msk-id').val();

          formData.append("msk_id[" + index + "]", mskIdVal);
          formData.append("asd_value[" + index + "]", $(i).val());

          if (!fnIsEmpty($("#asd_id" + mskIdVal).val())) {
            formData.append("asd_id[" + index + "]", $("#asd_id" + mskIdVal).val());
          }

          if ($("#asd_file" + mskIdVal).length > 0) {
            if ($("#asd_file" + mskIdVal)[0].files.length > 0) {
              formData.append("lampiran[" + index + "]", $("#asd_file" + mskIdVal)[0].files[0]);
            }
          }
        });
        formData.append("list_hapus_lampiran", listHapusLampiran);

        $.ajax({
          url: "{{ route('asesmen.save') }}",
          cache: false,
          type: "post",
          data: formData,
          contentType: false,
          processData: false,
          dataType: "json",
          error: function(err) {
            Swal.fire("Error", err, "error");
            fnSetBtnOnSubmit();
          },
          complete: function() {
            fnSetBtnOnSubmit();
          },
          success: function(res) {
            if (res.status) {
              Swal.fire({
                icon: 'success',
                title: 'Data difinalisasi. Silahkan menunggu hasil finalisasi!',
                showConfirmButton: false,
                timer: 1500
              });
              fnResetForm();
              fnCek();
            } else {
              Swal.fire("Error", res.msg, "error")
            }
          }
        })
      }
    })
  }

  function fnNextTab() {
    const currTab = nextTabValue.val();
    $(".colTitle").hide();
    $(".colTr").hide();

    $("#colTitle" + currTab).show();
    $(".colTr" + currTab).show();
    const newNextTabValue = $("#colTitle" + currTab).find(".nextKriteria").val();
    const newPrevTabValue = $("#colTitle" + currTab).find(".prevKriteria").val();
    nextTabValue.val(newNextTabValue);
    prevTabValue.val(newPrevTabValue);
    fnShowHideNavForm();
  }

  function fnPrevTab() {
    const currTab = prevTabValue.val();
    $(".colTitle").hide();
    $(".colTr").hide();

    $("#colTitle" + currTab).show();
    $(".colTr" + currTab).show();
    const newPrevTabValue = $("#colTitle" + currTab).find(".prevKriteria").val();
    const newNextTabValue = $("#colTitle" + currTab).find(".nextKriteria").val();
    prevTabValue.val(newPrevTabValue);
    nextTabValue.val(newNextTabValue);
    fnShowHideNavForm();
  }

  function fnShowHideNavForm() {
    if (nextTabValue.val() == "max") {
      btnNextTab.hide();
      btnSimpanFinal.show();
    } else {
      btnNextTab.show();
      btnSimpanFinal.hide();
    }

    if (prevTabValue.val() == "0") {
      btnPrevTab.hide();
    } else {
      btnPrevTab.show();
    }
  }

  $(document).ready(function() {
    fnCek();
    btnSimpanSementara.click(function() {
      fnSimpanSementara();
    });
    btnSimpanFinal.click(function() {
      fnSimpanFinal();
    });
  })
</script>
