<?php

namespace App\Http\Controllers;

use App\models\Mapellm;
use App\models\Siswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

    public function getChartData()
    {
        $listMapel = Mapellm::select('kode_mapel')->get();
        $lintasMinat1 = DB::table('siswa')
            ->select(DB::raw('pilih_lm1, count(*) as jumlah, kode_mapel'))
            ->leftJoin('mapellm', 'siswa.pilih_lm1', '=', 'mapellm.id')
            ->groupBy('pilih_lm1')
            ->groupBy('kode_mapel')
            ->get();
        $lintasMinat2 = DB::table('siswa')
            ->select(DB::raw('pilih_lm2, count(*) as jumlah, kode_mapel'))
            ->leftJoin('mapellm', 'siswa.pilih_lm2', '=', 'mapellm.id')
            ->groupBy('pilih_lm2')
            ->groupBy('kode_mapel')
            ->get();
        $lintasMinat3 = DB::table('siswa')
            ->select(DB::raw('pilih_lm3, count(*) as jumlah, kode_mapel'))
            ->leftJoin('mapellm', 'siswa.pilih_lm3', '=', 'mapellm.id')
            ->groupBy('pilih_lm3')
            ->groupBy('kode_mapel')
            ->get();
        $lintasMinat1 = json_decode($lintasMinat1, true);
        $lintasMinat2 = json_decode($lintasMinat2, true);
        $lintasMinat3 = json_decode($lintasMinat3, true);

        $statistik = [];
        foreach ($listMapel as $mapel) {
            $statistik[$mapel['kode_mapel']] = [
                "pilihan1" => $lintasMinat1[array_search($mapel['kode_mapel'], array_column($lintasMinat1, 'kode_mapel'))]['jumlah'],
                "pilihan2" => $lintasMinat2[array_search($mapel['kode_mapel'], array_column($lintasMinat2, 'kode_mapel'))]['jumlah'],
                "pilihan3" => $lintasMinat3[array_search($mapel['kode_mapel'], array_column($lintasMinat3, 'kode_mapel'))]['jumlah'],
            ];
        }

        $data = [
            "listMapel" => $listMapel,
            "lintasMinat" => $statistik,
        ];

        return json_encode($data);
    }
}
