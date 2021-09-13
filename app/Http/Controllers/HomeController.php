<?php

namespace App\Http\Controllers;

use App\models\Mapellm;
use App\models\Siswa;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $jumlahSiswa = Siswa::count();
        $jumlahMapel = Mapellm::count();
        $jumlahKelas = Mapellm::sum("jumlah_kelas");
        return view('v1.admin.content.home')->with([
            "jumlahSiswa" => $jumlahSiswa,
            "jumlahMapel" => $jumlahMapel,
            "jumlahKelas" => $jumlahKelas,
        ]);
    }
}
