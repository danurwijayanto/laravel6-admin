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
          <div class="top-button-group" style="margin-bottom: 20px;">
            <!-- <button type="button" class="btn btn-primary" id="add-cross-interest-class-data">Add new data</button> -->
          </div>
          <table id="cross-interest-class-table" class="table table-striped table-bordered" style="width:100%">
            <thead>
              <tr>
                <th width="25%">Class Name</th>
                <th width="25%">Total Students</th>
                <th width="25%">Schedule</th>
                <th width="25%">Action</th>
              </tr>
            </thead>
          </table>
        </div>
      </div>
      <div id="formModal" class="modal fade" role="dialog">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="exampleModalLongTitle">Detail Cross Interest Class</h5>
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
                  <label class="col-form-label">Course Code : </label>
                  <input type="text" id="course-code" class="form-control" />
                </div>
                <div class="form-group">
                  <label class="col-form-label">Class : </label>
                  <input type="text" id="class" class="form-control" />
                </div>
                <div class="form-group">
                  <label class="col-form-label">Teacher : </label>
                  <input type="text" id="teacher" class="form-control" name="teacher" />
                </div>
                <div class="form-group">
                  <label class="col-form-label">Schedule : </label>
                  <div class="row">
                    <div class="col">
                      <select id="day" class="form-control" name="day" required>
                        <option selected>Choose...</option>
                        <option>Sunday</option>
                        <option>Monday</option>
                        <option>Tuesday</option>
                        <option>Wednesday</option>
                        <option>Thursday</option>
                        <option>Saturday</option>
                      </select>
                    </div>
                    <div class="col">
                      <input type="time" name="time" id="time" class="form-control" required />
                    </div>
                  </div>
                </div>
                <div class="modal-footer">
                  <input type="hidden" name="action" id="action" value="Add" />
                  <input type="hidden" name="cross_interest_class_id" id="cross-interest-class-id" />
                  <input type="submit" name="action_button" id="action_button" class="btn btn-primary" value="Add" />
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
              <h5 class="modal-title" id="exampleModalLongTitle">Confirmation</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              <h4 align="center" style="margin:0;">Are you sure you want to remove this data?</h4>
            </div>
            <div class="modal-footer">
              <button type="button" name="ok_button" id="ok-button" class="btn btn-danger">OK</button>
              <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
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
        url: "{{ route('admin.crossInterestClass.datatablesGetalldata') }}",
      },
      columns: [{
          data: 'nama_kelas',
          name: 'nama_kelas'
        },
        {
          data: 'jumlah_siswa',
          name: 'jumlah_siswa'
        },
        {
          data: 'jadwal',
          name: 'jadwal'
        },
        {
          data: 'action',
          name: 'action',
          orderable: false
        }
      ]
    });
  });

  $(document).on('click', '#add-cross-interest-class-data', function() {
    $('#upload-result').html('');
    $('#formModal').modal('show');
  })

  $(document).on('click', '.detail', function(){
    window.open("/admin/cross-interest/detail/"+$(this).attr('id'));
  })

  $('#edit-form').on('submit', function(event) {
    event.preventDefault();
    var action_url = '';

    if ($('#action').val() == 'Add') {
      action_url = "{{ route('admin.crossInterestClass.store') }}";
    }

    if ($('#action').val() == 'Edit') {
      action_url = "{{ route('admin.crossInterestClass.update') }}";
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
      url: "/admin/cross-interest/get/" + id,
      dataType: "json",
      success: function(data) {
        $('#course-code').val(data.course.kode_mapel);
        $("#course-code").prop('disabled', true);
        $('#class').val(data.nama_kelas);
        $("#class").prop('disabled', true);
        $('#cross-interest-class-id').val(data.id);
        $('.modal-title').text('Edit Cross Interest Class Record');
        $('#action_button').val('Save');
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
      url: "/admin/crossInterestClass/delete/" + user_id,
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
</script>
@endpush
@endsection