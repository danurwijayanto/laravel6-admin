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
      <table id="user_table" class="table table-bordered table-striped">
        <thead>
          <tr>
            <th width="35%">First Name</th>
            <th width="35%">Last Name</th>
            <th width="30%">Action</th>
          </tr>
        </thead>
      </table>
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
          data: 'name',
          name: 'name'
        },
        {
          data: 'action',
          name: 'action',
          orderable: false
        }
      ]
    });
  });
</script>
@endpush
@endsection