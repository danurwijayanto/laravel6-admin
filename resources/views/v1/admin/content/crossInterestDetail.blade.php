@extends('v1.admin.layout')
@extends('v1.admin.header')
@extends('v1.admin.body')
@extends('v1.admin.footer')

@section('content')
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1>{{ $detailController['pageDescription'] ?? '' }}</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item active">{{ $detailController['currentPage'] ?? '' }}</li>
          </ol>
        </div>
      </div>
    </div><!-- /.container-fluid -->
  </section>
  <!-- Main content -->
  <section class="content">
    <div class="container-fluid">
      <div class="card card-default color-palette-box">
        <div class="card-body">
          @if (isset($error))
          <div class="alert alert-warning" role="alert">
            {{ $error }}
          </div>
          @endif
          <input type="hidden" id="class-name" value="{{ $classData['nama_kelas'] ?? '' }}">
          <table style="margin-bottom:20px">
            <tr>
              <td>
                <h5>Nama Kelas</h5>
              </td>
              <td>
                <h5>:</h5>
              </td>
              <td>
                <h5>{{ $classData['nama_kelas'] ?? '' }}</h5>
              </td>
            </tr>
            <tr>
              <td>
                <h5>Jadwal</h5>
              </td>
              <td>
                <h5>:</h5>
              </td>
              <td>
                <h5>{{ $classData['jadwal'] ?? '' }}</h5>
              </td>
            </tr>
          </table>
          <div class="top-button-group" style="margin-bottom: 20px;">
            <a href="{{ route('admin.crossInterestClass.downloadExcelData', $classData['nama_kelas']) }}">
              <button type="button" class="btn btn-primary" id="download-cross-interest-class-data">
                Download File
              </button>
            </a>
          </div>
          <table id="cross-interest-class-table" class="table table-striped table-bordered" style="width:100%">
            <thead>
              <tr>
                <th width="20%">Kelas</th>
                <th width="20%">Nama siswa</th>
              </tr>
            </thead>
          </table>
        </div>
      </div>

      <div id="formModal" class="modal fade" role="dialog">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="exampleModalLongTitle">Detail kelas lintas minat</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              <span id="form-result"></span>
              <!-- <div class="alert alert-danger" role="alert">
                This is a danger alert—check it out!
              </div> -->
              <form method="post" id="edit-form" class="form-horizontal">
                @csrf
                <div class="form-group">
                  <label class="col-form-label">NIS : </label>
                  <input type="text" name="nis" id="nis" class="form-control" required />
                </div>
                <div class="form-group">
                  <label class="col-form-label">Nama : </label>
                  <input type="text" name="name" id="name" class="form-control" required />
                </div>
                <div class="form-group">
                  <label class="col-form-label">Kelas : </label>
                  <input type="text" name="class" id="class" class="form-control" required />
                </div>
                <div class="edit-content">
                  <div class="form-group">
                    <label class="col-form-label">Pilihan minat 1 : </label>
                    <input type="text" name="choice_interest_1" id="choice_interest_1" class="form-control" required />
                  </div>
                  <div class="form-group">
                    <label class="col-form-label">Pilihan minat 2 : </label>
                    <input type="text" name="choice_interest_2" id="choice_interest_2" class="form-control" required />
                  </div>
                  <div class="form-group">
                    <label class="col-form-label">Pilihan minat 3 : </label>
                    <input type="text" name="choice_interest_3" id="choice_interest_3" class="form-control" required />
                  </div>
                  <br />
                </div>
                <div class="modal-footer">
                  <input type="hidden" name="action" id="action" value="Add" />
                  <input type="hidden" name="cross-interest-class_id" id="cross-interest-class-id" />
                  <input type="submit" name="action_button" id="action_button" class="btn btn-primary" value="Edit" />
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>

      <div id="confirmModal" class="modal fade" role="dialog">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="exampleModalLongTitle">Konfirmasi</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              <h4 align="center" style="margin:0;">Apakah kamu ingin menghapus data ini ?</h4>
            </div>
            <div class="modal-footer">
              <button type="button" name="ok_button" id="ok-button" class="btn btn-danger">Ok</button>
              <button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
            </div>
          </div>
        </div>
      </div>
      <!-- /.card-body -->
    </div>
    <!-- /.card -->
  </section>
  <!-- /.content -->
</div>
<!-- /.content-wrapper -->
@push('scripts')
<script>
  $(document).ready(function() {
    $('#cross-interest-class-table').DataTable({
      processing: true,
      serverSide: true,
      ajax: {
        url: "/admin/lintas-minat/get-datatables-detail-data/" + $('#class-name').val(),
      },
      columns: [{
          data: 'student.kelas',
          name: 'student.kelas'
        },
        {
          data: 'student.nama_siswa',
          name: 'student.nama_siswa'
        },
      ]
    });
  });
  // $(document).on('click', '#download-cross-interest-class-data', function() {
  //   const action = $('#ok-button').data('action');

  //   $.ajax({
  //     url: action_url,
  //     method: "GET",
  //     data: $(this).serialize(),
  //     dataType: "json",
  //     success: function(data) {
  //       var html = '';
  //       if (data.errors) {
  //         html = '<div class="alert alert-danger">';
  //         for (var count = 0; count < data.errors.length; count++) {
  //           html += '<p>' + data.errors[count] + '</p>';
  //         }
  //         html += '</div>';
  //       }
  //       if (data.success) {
  //         html = '<div class="alert alert-success">' + data.success + '</div>';
  //         $('#edit-form')[0].reset();
  //         // $('#formModal').modal('hide');
  //         $('#cross-interest-class-table').DataTable().ajax.reload();
  //       }
  //       $('#form-result').html(html);
  //     }
  //   });
  // });

  $('#edit-form').on('submit', function(event) {
    event.preventDefault();
    var action_url = '';

    if ($('#action').val() == 'Add') {
      action_url = "{{ route('admin.lintas-minat.store') }}";
    }

    if ($('#action').val() == 'Edit') {
      action_url = "{{ route('admin.lintas-minat.update') }}";
    }

    $.ajax({
      url: action_url,
      method: "POST",
      data: $(this).serialize(),
      dataType: "json",
      success: function(data) {
        var html = '';
        if (data.errors) {
          html = '<div class="alert alert-danger">';
          for (var count = 0; count < data.errors.length; count++) {
            html += '<p>' + data.errors[count] + '</p>';
          }
          html += '</div>';
        }
        if (data.success) {
          html = '<div class="alert alert-success">' + data.success + '</div>';
          $('#edit-form')[0].reset();
          // $('#formModal').modal('hide');
          $('#cross-interest-class-table').DataTable().ajax.reload();
        }
        $('#form-result').html(html);
      }
    });
  });

  $(document).on('click', '.edit', function() {
    var id = $(this).attr('id');

    $('#form-result').html('');
    $('#formModal').modal('show');
    $("#edit-form :input").attr("disabled", false);
    $('.edit-content').hide();
    $('#action_button').show();
    $(".edit-content :input").prop('required', false);

    $.ajax({
      method: "GET",
      url: "/admin/lintas-minat/get/" + id,
      dataType: "json",
      success: function(data) {
        $('#nis').val(data.nis);
        $('#name').val(data.nama_siswa);
        $("#class").val(data.kelas)
        $('#cross-interest-class-id').val(id);
        $('.modal-title').text('Edit Cross Interest Class Record');
        $('#action_button').val('Edit');
        $('#action').val('Edit');
        // $('#formModal').modal('show');
      },
      error: function() {
        alert("Error : Cannot get data");
      }
    })
  });

  $(document).on('click', '.delete', function() {
    user_id = $(this).attr('id');
    $('#confirmModal').modal('show');
  });

  $('#ok-button').click(function() {
    $.ajax({
      url: "/admin/lintas-minat/delete/" + user_id,
      method: "DELETE",
      data: {
        "_token": "{{ csrf_token() }}",
      },
      beforeSend: function() {
        $('#ok-button').text('Deleting...');
      },
      success: function(data) {
        setTimeout(function() {
          if (data.errors) {
            errorMessage = '';
            for (var count = 0; count < data.errors.length; count++) {
              errorMessage += data.errors[count];
            }
            $('#confirmModal').modal('hide');
            $('#cross-interest-class-table').DataTable().ajax.reload();
            alert(errorMessage);
          } else {
            $('#confirmModal').modal('hide');
            $('#cross-interest-class-table').DataTable().ajax.reload();
            alert('Data Deleted');
          }
        }, 2000);
      }
    })
  });

  $(document).on('click', '.detail', function() {
    $('#formModal').modal('show');
    $('#form-result').html('');
    $("#edit-form :input").attr("disabled", true);
    $('.edit-content').show();
    $('#action_button').hide();
    $("#edit-form :input").prop('required', true);

    var id = $(this).attr('id');
    $.ajax({
      method: "GET",
      url: "/admin/lintas-minat/get/" + id,
      dataType: "json",
      success: function(data) {
        $('#nis').val(data.nis);
        $('#name').val(data.nama_siswa);
        $("#class").val(data.kelas)
        $('#choice_interest_1').val(data.detail_lm1.nama_mapel);
        $('#choice_interest_2').val(data.detail_lm2.nama_mapel);
        $('#choice_interest_3').val(data.detail_lm3.nama_mapel);
        $('.modal-title').text('View Cross Interest Class Record');
      },
      error: function() {
        alert("Error : Cannot get data");
      }
    })
  });
</script>
@endpush
@endsection