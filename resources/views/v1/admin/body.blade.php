@section('body')

<body>
  <div class="wrapper">
    <!-- Navbar -->
    <nav class="main-header navbar navbar-expand navbar-white navbar-light">
      <!-- Left navbar links -->
      <ul class="navbar-nav">
        <li class="nav-item">
          <a class="nav-link" data-widget="pushmenu" href="#"><i class="fas fa-bars"></i></a>
        </li>
        <li class="nav-item d-none d-sm-inline-block">
          <a href="{{ route('home') }}" class="nav-link">{{ env('APP_NAME') }}</a>
        </li>
      </ul>

      <!-- Right navbar links -->
      <ul class="navbar-nav ml-auto">
        <li class="nav-item">
          <a class="nav-link" data-widget="control-sidebar" data-slide="true" href="#">
            <i class="fas fa-th-large"></i>
          </a>
        </li>
      </ul>
    </nav>
    <!-- /.navbar -->

    <!-- Main Sidebar Container -->
    <aside class="main-sidebar sidebar-dark-primary elevation-4">
      <!-- Brand Logo -->
      <a href="{{ route('home') }}" class="brand-link">
        <img src="{{ asset('assets/dist/img/sma.png') }}" alt="SMA Negeri 1 Grobogan Logo"
          class="brand-image img-circle elevation-3" style="opacity: .8">
        <span class="brand-text font-weight-light">SMA Negeri 1 Grobogan</span>
      </a>

      <!-- Sidebar -->
      <div class="sidebar">
        <!-- Sidebar Menu -->
        <nav class="mt-2">
          <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
            <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->
            <li class="nav-item">
              <a href="{{ route('admin.user.index') }}" class="nav-link">
                <i class="nav-icon fas fa-users"></i>
                <p>
                  Data Admin
                </p>
              </a>
            </li>
            <li class="nav-item">
              <a href="{{ route('admin.student.index') }}" class="nav-link">
                <i class="nav-icon fas fa-users"></i>
                <p>
                  Data Siswa
                </p>
              </a>
            </li>
            <!-- <li class="nav-item">
              <a href="../widgets.html" class="nav-link">
                <i class="nav-icon fas fa-book"></i>
                <p>
                  Data Guru
                </p>
              </a>
            </li> -->
            <li class="nav-item">
              <a href="{{ route('admin.course.index') }}" class="nav-link">
                <i class="nav-icon fas fa-book"></i>
                <p>
                  Data Mata Pelajaran
                </p>
              </a>
            </li>
            <li class="nav-item">
              <a href="{{ route('admin.crossInterestClass.index') }}" class="nav-link">
                <i class="nav-icon fas fa-arrows-alt-h"></i>
                <p>
                  Data Kelas Lintas Minat
                </p>
              </a>
            </li>
            <li class="nav-item">
              <a href="{{ route('admin.calresult.index') }}" class="nav-link">
                <i class="nav-icon fas fa-calculator"></i>
                <p>
                  Hasil Perhitungan
                </p>
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="{{ route('logout') }}" onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                <i class="nav-icon fas fa-sign-out-alt"></i>
                <p>
                  Keluar
                </p>
              </a>

              <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                @csrf
              </form>
            </li>
          </ul>
        </nav>
        <!-- /.sidebar-menu -->
      </div>
      <!-- /.sidebar -->
    </aside>
    @yield('content')
</body>
@endsection