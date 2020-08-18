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
          <table id="user_table" class="table table-striped table-bordered" style="width:100%">
            <thead>
              <tr>
                <th width="35%">Username</th>
                <th width="35%">Email</th>
                <th width="35%">Role</th>
                <th width="30%">Action</th>
              </tr>
            </thead>
          </table>
        </div>
      </div>

      <div id="formModal" class="modal fade" role="dialog">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="exampleModalLongTitle">Add New User</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              <span id="form_result"></span>
              <div class="alert alert-danger" role="alert">
                This is a danger alert—check it out!
              </div>
              <form method="post" id="sample_form" class="form-horizontal">
                @csrf
                <div class="form-group">
                  <label class="col-form-label">Username : </label>
                  <input type="text" name="username" id="username" class="form-control" />
                </div>
                <div class="form-group">
                  <label class="col-form-label">Email : </label>
                  <input type="text" name="email" id="email" class="form-control" />
                </div>
                <div class="form-group">
                  <label class="col-form-label">Role : </label>
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
                  <input type="hidden" name="hidden_id" id="hidden_id" />
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
              <button type="button" class="close" data-dismiss="modal">&times;</button>
              <h2 class="modal-title">Confirmation</h2>
            </div>
            <div class="modal-body">
              <h4 align="center" style="margin:0;">Are you sure you want to remove this data?</h4>
            </div>
            <div class="modal-footer">
              <button type="button" name="ok_button" id="ok_button" class="btn btn-danger">OK</button>
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
    $('#user_table').DataTable({
      processing: true,
      serverSide: true,
      ajax: {
        url: "{{ route('admin.user.datatablesGetalldata') }}",
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
          name: 'roles'
        },
        {
          data: 'action',
          name: 'action',
          orderable: false
        }
      ]
    });
  });

  $(document).on('click', '.edit', function() {
    var id = $(this).attr('id');
    $('#form_result').html('');
    $.ajax({
      method: "GET",
      url: "/admin/user/get/" + id,
      dataType: "json",
      success: function(data) {
        console.log(data);
        $('#username').val(data.name);
        $('#email').val(data.email);
        $("#role").val(data.role_id)
        $('#hidden_id').val(id);
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
</script>
@endpush
@endsection