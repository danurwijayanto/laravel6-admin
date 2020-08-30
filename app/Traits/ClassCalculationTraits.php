<?php

namespace App\Traits;

use App\Models\Siswa;
use Illuminate\Support\Facades\Log;

trait ClassCalculationTraits
{
    public function doClassCalculation()
    {
        $doStudentClassOrder = $this->studentClassOrder();
        $doAssignClass = $this->assignClass($doStudentClassOrder);
        return $doAssignClass;
    }

    private function studentClassOrder()
    {
        $dataSiswa = \App\Models\Siswa::with('detailLm1', 'detailLm2', 'detailLm3')->get()->toArray();

        if (empty($dataSiswa)) {
            return json_encode(['fail' => 'Student data is null']);
        }

        for ($i = 0; $i < count($dataSiswa); $i++) {
            $lintasMinatOrder = [
                0 => [
                    'mapel_id' =>  $dataSiswa[$i]['pilih_lm1'],
                    'vector' => $dataSiswa[$i]['vektor_v1']
                ],
                1 => [
                    'mapel_id' =>  $dataSiswa[$i]['pilih_lm2'],
                    'vector' => $dataSiswa[$i]['vektor_v2']
                ],
                2 => [
                    'mapel_id' => $dataSiswa[$i]['pilih_lm3'],
                    'vector' => $dataSiswa[$i]['vektor_v3']
                ],
            ];
            // arsort($lintasMinatOrder);
            usort($lintasMinatOrder, function ($a, $b) {
                return $b['vector'] <=> $a['vector'];
            });

            $dataSiswa[$i]['urutan_lintas_minat'] = $lintasMinatOrder;
        }
        // \Illuminate\Support\Facades\Log::debug($dataSiswa);

        return $dataSiswa;
    }

    private function assignClass($data)
    {
        $idMakul1 = 0;
        $idMakul2 = 0;
        $idMakul3 = 0;

        if (empty($data)) {
            return json_encode(['fail' => 'StudentClassOrder data is null']);
        }

        $dataMapel = \App\Models\Mapellm::get()->toArray();

        if (empty($dataMapel)) {
            return json_encode(['fail' => 'Mapel data is null']);
        }

        // Check max class quota
        for ($i = 0; $i < count($dataMapel); $i++) {
            $dataMapel[$i]['kuota_kelas'] = $dataMapel[$i]['jumlah_kelas'] * $dataMapel[$i]['kuota_kelas'];
            $dataMapel[$i]['kuota_kelas_terpakai'] = 0;
        }

        // \Illuminate\Support\Facades\Log::debug($dataMapel);

        for ($i = 0; $i < count($data); $i++) {
            $mapelSelected = 0;
            // for ($j = 0; $j < count($data[$i]['urutan_lintas_minat']); $j++) {
            $idMakul1 = array_search($data[$i]['urutan_lintas_minat'][0]['mapel_id'], array_column($dataMapel, 'id'));
            $idMakul2 = array_search($data[$i]['urutan_lintas_minat'][1]['mapel_id'], array_column($dataMapel, 'id'));
            // }
            \Illuminate\Support\Facades\Log::debug($idMakul1);
            if ($dataMapel[$idMakul1]['kuota_kelas_terpakai'] <  $dataMapel[$idMakul1]['kuota_kelas']){
                $mapelSelected = $data[$i]['urutan_lintas_minat'][0]['mapel_id'];
                $dataMapel[$idMakul1]['kuota_kelas_terpakai']++;
            }elseif($dataMapel[$idMakul2]['kuota_kelas_terpakai'] <  $dataMapel[$idMakul2]['kuota_kelas']){
                $mapelSelected = $data[$i]['urutan_lintas_minat'][1]['mapel_id'];
                $dataMapel[$idMakul2]['kuota_kelas_terpakai']++;
            }else{
                // \Illuminate\Support\Facades\Log::debug("Kelas ".$dataMapel[$idMakul1]['kode_mapel']." telah penuh untuk siswa ".$data[$i]['nama_siswa']);
                \Illuminate\Support\Facades\Log::debug("Siswa ".$data[$i]['nama_siswa']." harus memilih kelas 3 atau sekolah harus menambahkan kuota kelas");
            }
            $data[$i]['mapel_terpilih'] = $mapelSelected;
            // Mencari index data mapel
            // if (array_search($data[$i]['urutan_lintas_minat']['pilih_lm1'], array_column($dataMapel, 'nama_mapel'))){

            // };

            // $studentStatus = [
            //     'kelas' => ,
            //     'status' =>
            // ];

            // $data[$i]['status_murid'] = $studentStatus;
        }
        \Illuminate\Support\Facades\Log::debug($dataMapel);
        \Illuminate\Support\Facades\Log::debug($data);
    }
}
