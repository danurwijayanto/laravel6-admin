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
            <button type="button" class="btn btn-primary add-course">Add new data</button>
          </div>
          <table id="course-table" class="table table-striped table-bordered" style="width:100%">
            <thead>
              <tr>
                <th width="20%">Course Code</th>
                <th width="20%">Course Name</th>
                <th width="20%">Number of Class</th>
                <th width="20%">Class Quota</th>
                <th width="20%">Action</th>
              </tr>
            </thead>
          </table>
        </div>
      </div>

      <div id="formModal" class="modal fade" role="dialog">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="exampleModalLongTitle">Add New Course</h5>
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
                  <label class="col-form-label">Course code : </label>
                  <input type="text" name="course_code" id="course-code" class="form-control" maxlength="11" required/>
                </div>
                <div class="form-group">
                  <label class="col-form-label">Course name : </label>
                  <input type="text" name="course_name" id="course-name" class="form-control" maxlength="35" required/>
                </div>
                <div class="form-group">
                  <label class="col-form-label">Number of classes : </label>
                  <input type="number" name="number_of_classes" id="number-of-classes" class="form-control" min="1" required/>
                </div>
                <div class="form-group">
                  <label class="col-form-label">Class quota : </label>
                  <input type="number" name="class_quota" id="class-quota" class="form-control" min="1" required/>
                </div>
                <br />
                <div class="modal-footer">
                  <input type="hidden" name="action" id="action" value="Add" />
                  <input type="hidden" name="course_id" id="course-id" />
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
    $('#course-table').DataTable({
      processing: true,
      serverSide: true,
      ajax: {
        url: "{{ route('admin.course.datatablesGetalldata') }}",
      },
      columns: [{
          data: 'kode_mapel',
          name: 'course_code'
        },
        {
          data: 'nama_mapel',
          name: 'course_name'
        },
        {
          data: 'jumlah_kelas',
          name: 'number_of_classes'
        },
        {
          data: 'kuota_kelas',
          name: 'class_quota',
        },
        {
          data: 'action',
          name: 'action',
          orderable: false
        }
      ]
    });
  });

  $('#edit-form').on('submit', function(event) {
    event.preventDefault();
    var action_url = '';

    if ($('#action').val() == 'Add') {
      action_url = "{{ route('admin.course.store') }}";
    }

    if ($('#action').val() == 'Edit') {
      action_url = "{{ route('admin.course.update') }}";
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
          alert("Data successfully added or edited !");
          html = '<div class="alert alert-success">' + data.success + '</div>';
          $('#edit-form')[0].reset();
          $('#course-table').DataTable().ajax.reload();
        }
        $('#form-result').html(html);
        $('#formModal').modal('hide');
      }
    });
  });

  $(document).on('click', '.add-course', function() {
    $('#form-result').html('');
    $('#formModal').modal('show');
    $('#action_button').val('Add');
    $('#action').val('Add');
  });

  $(document).on('click', '.edit', function() {
    var id = $(this).attr('id');
    $('#form-result').html('');
    $.ajax({
      method: "GET",
      url: "/admin/course/get/" + id,
      dataType: "json",
      success: function(data) {
        $('#course-code').val(data.kode_mapel);
        $('#course-name').val(data.nama_mapel);
        $("#number-of-classes").val(data.jumlah_kelas)
        $("#class-quota").val(data.kuota_kelas)
        $('#course-id').val(id);
        $('.modal-title').text('Edit User Record');
        $('#action_button').val('Edit');
        $('#action').val('Edit');
        $('#formModal').modal('show');
      },
      error: function() {
        alert("Error : Cannot get data");
      }
    })
  });

  $(document).on('click', '.delete', function() {
    course_id = $(this).attr('id');
    $('#confirmModal').modal('show');
  });

  $('#ok-button').click(function() {
    $.ajax({
      url: "/admin/course/delete/" + course_id,
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
            $('#course-table').DataTable().ajax.reload();
            alert(errorMessage);
          } else {
            $('#confirmModal').modal('hide');
            $('#course-table').DataTable().ajax.reload();
            alert('Data Deleted');
          }
        }, 2000);
      }
    })
  });
</script>
@endpush
@endsection