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
            <li class="breadcrumb-item"><a href="#">Beranda</a></li>
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
          <table id="user-table" class="table table-striped table-bordered" style="width:100%">
            <thead>
              <tr>
                <th width="35%">Nama pengguna</th>
                <th width="35%">Email</th>
                <th width="35%">Wewenang</th>
                <th width="30%">Aksi</th>
              </tr>
            </thead>
          </table>
        </div>
      </div>

      <div id="formModal" class="modal fade" role="dialog">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="exampleModalLongTitle">Tambah pengguna baru</h5>
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
                  <label class="col-form-label">Nama pengguna : </label>
                  <input type="text" name="username" id="username" class="form-control" />
                </div>
                <div class="form-group">
                  <label class="col-form-label">Email : </label>
                  <input type="text" name="email" id="email" class="form-control" />
                </div>
                <div class="form-group">
                  <label class="col-form-label">Wewenang : </label>
                  <select class="custom-select" name="role" id="role">
                    <option selected>Role List</option>
                    @if (!empty($roleList))
                    @foreach ($roleList as $role)
                    <option value="{{ $role->id }}">{{ $role->name }}</option>
                    @endforeach
                    @endif
                  </select>
                </div>
                <!-- <div class="form-group">
                  <label class="col-form-label">Password : </label>
                  <input type="password" name="password" id="password" class="form-control" />
                </div>
                <div class="form-group">
                  <label class="col-form-label">Confirm Password : </label>
                  <input type="password" name="confirmPassword" id="confirm-password" class="form-control" />
                </div> -->
                <br />
                <div class="modal-footer">
                  <input type="hidden" name="action" id="action" value="Add" />
                  <input type="hidden" name="user_id" id="user-id" />
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
              <h5 class="modal-title" id="exampleModalLongTitle">Konfirmasi</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              <h4 align="center" style="margin:0;">Apakah anda ingin menghapus data ini ?</h4>
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
    $('#user-table').DataTable({
      processing: true,
      serverSide: true,
      ajax: {
        url: "{{ route('admin.pengguna.datatablesGetalldata') }}",
      },
      columns: [{
          data: 'name',
          name: 'name'
        },
        {
          data: 'email',
          name: 'email'
        },
        {
          data: 'role.name',
          name: 'role.name'
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
      action_url = "{{ route('admin.pengguna.store') }}";
    }

    if ($('#action').val() == 'Edit') {
      action_url = "{{ route('admin.pengguna.update') }}";
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
          $('#formModal').modal('hide');
          $('#user-table').DataTable().ajax.reload();
        }
        $('#form-result').html(html);
      }
    });
  });

  $(document).on('click', '.edit', function() {
    var id = $(this).attr('id');
    $('#form-result').html('');
    $.ajax({
      method: "GET",
      url: "/admin/pengguna/get/" + id,
      dataType: "json",
      success: function(data) {
        $('#username').val(data.name);
        $('#email').val(data.email);
        $("#role").val(data.role_id)
        $('#user-id').val(id);
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
    user_id = $(this).attr('id');
    $('#confirmModal').modal('show');
  });

  $('#ok-button').click(function() {
    $.ajax({
      url: "/admin/pengguna/delete/" + user_id,
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
            $('#user-table').DataTable().ajax.reload();
            alert(errorMessage);
          } else {
            $('#confirmModal').modal('hide');
            $('#user-table').DataTable().ajax.reload();
            alert('Data Deleted');
          }
        }, 2000);
      }
    })
  });
</script>
@endpush
@endsection