@extends('v1.admin.header')
@extends('v1.admin.body')
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
      <!-- COLOR PALETTE -->
      <div class="card card-default color-palette-box">
        <!-- <div class="card-header">
          <h3 class="card-title">
            <i class="fas fa-tag"></i>
            {{ $detailController['currentPage'] }}
          </h3>
        </div> -->
        <div class="card-body">
          <div class="col-12">
            <h5>Theme Colors</h5>
          </div>
          <!-- /.col-12 -->
          <div class="row">
            <div class="col-sm-4 col-md-2">
              <h4 class="text-center">Primary</h4>

              <div class="color-palette-set">
                <div class="bg-primary color-palette"><span>#007bff</span></div>
                <div class="bg-primary disabled color-palette"><span>Disabled</span></div>
              </div>
            </div>
            <!-- /.col -->
            <div class="col-sm-4 col-md-2">
              <h4 class="text-center">Secondary</h4>

              <div class="color-palette-set">
                <div class="bg-secondary color-palette"><span>#6c757d</span></div>
                <div class="bg-secondary disabled color-palette"><span>Disabled</span></div>
              </div>
            </div>
            <!-- /.col -->
            <div class="col-sm-4 col-md-2">
              <h4 class="text-center">Info</h4>

              <div class="color-palette-set">
                <div class="bg-info color-palette"><span>#17a2b8</span></div>
                <div class="bg-info disabled color-palette"><span>Disabled</span></div>
              </div>
            </div>
            <!-- /.col -->
            <div class="col-sm-4 col-md-2">
              <h4 class="text-center">Success</h4>

              <div class="color-palette-set">
                <div class="bg-success color-palette"><span>#28a745</span></div>
                <div class="bg-success disabled color-palette"><span>Disabled</span></div>
              </div>
            </div>
            <!-- /.col -->
            <div class="col-sm-4 col-md-2">
              <h4 class="text-center bg-warning">Warning</h4>

              <div class="color-palette-set">
                <div class="bg-warning color-palette"><span>#ffc107</span></div>
                <div class="bg-warning disabled color-palette"><span>Disabled</span></div>
              </div>
            </div>
            <!-- /.col -->
            <div class="col-sm-4 col-md-2">
              <h4 class="text-center">Danger</h4>

              <div class="color-palette-set">
                <div class="bg-danger color-palette"><span>#dc3545</span></div>
                <div class="bg-danger disabled color-palette"><span>Disabled</span></div>
              </div>
            </div>
            <!-- /.col -->
          </div>
          <!-- /.row -->
          <div class="col-12">
            <h5 class="mt-3">Black/White Nuances</h5>
          </div>
          <!-- /.col-12 -->
          <div class="row">
            <div class="col-sm-4 col-md-2">
              <h4 class="text-center">Black</h4>

              <div class="color-palette-set">
                <div class="bg-black color-palette"><span>#000000</span></div>
                <div class="bg-black disabled color-palette"><span>Disabled</span></div>
              </div>
            </div>
            <!-- /.col -->
            <div class="col-sm-4 col-md-2">
              <h4 class="text-center">Gray Dark</h4>

              <div class="color-palette-set">
                <div class="bg-gray-dark color-palette"><span>#343a40</span></div>
                <div class="bg-gray-dark disabled color-palette"><span>Disabled</span></div>
              </div>
            </div>
            <!-- /.col -->
            <div class="col-sm-4 col-md-2">
              <h4 class="text-center">Gray</h4>

              <div class="color-palette-set">
                <div class="bg-gray color-palette"><span>#adb5bd</span></div>
                <div class="bg-gray disabled color-palette"><span>Disabled</span></div>
              </div>
            </div>
            <!-- /.col -->
            <div class="col-sm-4 col-md-2">
              <h4 class="text-center bg-light">Light</h4>

              <div class="color-palette-set">
                <div class="bg-light color-palette"><span>#1f2d3d</span></div>
                <div class="bg-light disabled color-palette"><span>Disabled</span></div>
              </div>
            </div>
            <!-- /.col -->
          </div>
          <!-- /.row -->
          <div class="col-12">
            <h5 class="mt-3">Colors</h5>
          </div>
          <!-- /.col-12 -->
          <div class="row">
            <div class="col-sm-4 col-md-2">
              <h4 class="text-center bg-indigo">Indigo</h4>

              <div class="color-palette-set">
                <div class="bg-indigo color-palette"><span>#6610f2</span></div>
                <div class="bg-indigo disabled color-palette"><span>Disabled</span></div>
              </div>
            </div>
            <!-- /.col -->
            <div class="col-sm-4 col-md-2">
              <h4 class="text-center bg-navy">Navy</h4>

              <div class="color-palette-set">
                <div class="bg-navy color-palette"><span>#001f3f</span></div>
                <div class="bg-navy disabled color-palette"><span>Disabled</span></div>
              </div>
            </div>
            <!-- /.col -->
            <div class="col-sm-4 col-md-2">
              <h4 class="text-center bg-purple">Purple</h4>

              <div class="color-palette-set">
                <div class="bg-purple color-palette"><span>#605ca8</span></div>
                <div class="bg-purple disabled color-palette"><span>Disabled</span></div>
              </div>
            </div>
            <!-- /.col -->
            <div class="col-sm-4 col-md-2">
              <h4 class="text-center bg-fuchsia">Fuchsia</h4>

              <div class="color-palette-set">
                <div class="bg-fuchsia color-palette"><span>#f012be</span></div>
                <div class="bg-fuchsia disabled color-palette"><span>Disabled</span></div>
              </div>
            </div>
            <!-- /.col -->
            <div class="col-sm-4 col-md-2">
              <h4 class="text-center bg-pink">Pink</h4>

              <div class="color-palette-set">
                <div class="bg-pink color-palette"><span>#e83e8c</span></div>
                <div class="bg-pink disabled color-palette"><span>Disabled</span></div>
              </div>
            </div>
            <!-- /.col -->
            <div class="col-sm-4 col-md-2">
              <h4 class="text-center bg-maroon">Maroon</h4>

              <div class="color-palette-set">
                <div class="bg-maroon color-palette"><span>#d81b60</span></div>
                <div class="bg-maroon disabled color-palette"><span>Disabled</span></div>
              </div>
            </div>
            <!-- /.col -->
            <div class="col-sm-4 col-md-2">
              <h4 class="text-center bg-orange">Orange</h4>

              <div class="color-palette-set">
                <div class="bg-orange color-palette"><span>#ff851b</span></div>
                <div class="bg-orange disabled color-palette"><span>Disabled</span></div>
              </div>
            </div>
            <!-- /.col -->
            <div class="col-sm-4 col-md-2">
              <h4 class="text-center bg-lime">Lime</h4>

              <div class="color-palette-set">
                <div class="bg-lime color-palette"><span>#01ff70</span></div>
                <div class="bg-lime disabled color-palette"><span>Disabled</span></div>
              </div>
            </div>
            <!-- /.col -->
            <div class="col-sm-4 col-md-2">
              <h4 class="text-center bg-teal">Teal</h4>

              <div class="color-palette-set">
                <div class="bg-teal color-palette"><span>#39cccc</span></div>
                <div class="bg-teal disabled color-palette"><span>Disabled</span></div>
              </div>
            </div>
            <!-- /.col -->
            <div class="col-sm-4 col-md-2">
              <h4 class="text-center bg-olive">Olive</h4>

              <div class="color-palette-set">
                <div class="bg-olive color-palette"><span>#3d9970</span></div>
                <div class="bg-olive disabled color-palette"><span>Disabled</span></div>
              </div>
            </div>
            <!-- /.col -->
          </div>
          <!-- /.row -->
        </div>
        <!-- /.card-body -->
      </div>
      <!-- /.card -->
  </section>
  <!-- /.content -->
</div>
<!-- /.content-wrapper -->
@endsection