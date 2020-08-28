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
            <button type="button" class="btn btn-primary" id="do-calculation">Do Calculation</button>
            <button type="button" class="btn btn-secondary" id="do-class-divison">Do Class Division</button>
          </div>
          <table id="user-table" class="table table-striped table-bordered" style="width:100%">
            <thead>
              <tr>
                <th width="35%">NIS</th>
                <th width="35%">Name</th>
                <th width="35%">Class</th>
                <th width="30%">Choice Interest 1</th>
                <th width="30%">Choice Interest 2</th>
                <th width="30%">Choice Interest 3</th>
                <th width="30%">Vector 1</th>
                <th width="30%">Vector 2</th>
                <th width="30%">Vector 3</th>
              </tr>
            </thead>
          </table>
        </div>
      </div>
      <div id="confirmModal" class="modal fade" role="dialog" data->
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="exampleModalLongTitle">Confirmation</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              <h4 align="center" style="margin:0;">Are you sure you want to do some calculation ? </h4>
            </div>
            <div class="modal-footer">
              <button type="button" name="ok_button" id="ok-button" data-action="" class="btn btn-danger">OK</button>
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
    $('#user-table').DataTable({
      processing: true,
      serverSide: true,
      ajax: {
        url: "{{ route('admin.calresult.datatablesGetalldata') }}",
      },
      columns: [{
          data: 'nis',
          name: 'nis'
        },
        {
          data: 'nama_siswa',
          name: 'nama_siswa'
        },
        {
          data: 'kelas',
          name: 'kelas'
        },
        {
          data: 'detail_lm1.nama_mapel',
          name: 'detail_lm1.nama_mapel',
        },
        {
          data: 'detail_lm2.nama_mapel',
          name: 'detail_lm2.nama_mapel',
        },
        {
          data: 'detail_lm3.nama_mapel',
          name: 'detail_lm3.nama_mapel',
        },
        {
          data: 'vektor_v1',
          name: 'vektor_v1',
        },
        {
          data: 'vektor_v2',
          name: 'vektor_v2',
        },
        {
          data: 'vektor_v3',
          name: 'vektor_v3',
        }
      ]
    });
  });

  $(document).on('click', '#do-calculation', function() {
    $('#confirmModal').modal('show');
    $('#ok-button').data('action', 'do-calculation');
  });

  $(document).on('click', '#do-class-divison', function() {
    $('#confirmModal').modal('show');
    $('#ok-button').data('action', 'do-class-division');
  })

  $('#ok-button').click(function() {
    const action = $('#ok-button').data('action');

    if (action == "do-calculation") {
      $.ajax({
        url: "/admin/calresult/do-calculation/",
        method: "GET",
        data: {
          "_token": "{{ csrf_token() }}",
        },
        beforeSend: function() {
          $('#ok-button').text('Calculating...');
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
              alert('Calculated Successfully');
            }
          }, 2000);
        }
      })
    } else {
      $.ajax({
        url: "/admin/calresult/do-class-division/",
        method: "GET",
        data: {
          "_token": "{{ csrf_token() }}",
        },
        beforeSend: function() {
          $('#ok-button').text('Calculating...');
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
              alert('Calculated Successfully');
            }
          }, 2000);
        }
      })
    }
  });
</script>
@endpush
@endsection