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
        $insertDb = $this->calculationInsertDb($doAssignClass);
        return $insertDb;
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
            $dataMapel[$i]['max_kuota_kelas'] = $dataMapel[$i]['jumlah_kelas'] * $dataMapel[$i]['kuota_kelas'];
            $dataMapel[$i]['kuota_kelas_terpakai'] = 0;
        }

        // \Illuminate\Support\Facades\Log::debug($dataMapel);

        for ($i = 0; $i < count($data); $i++) {
            $mapelSelected = 0;

            $idMakul1 = array_search($data[$i]['urutan_lintas_minat'][0]['mapel_id'], array_column($dataMapel, 'id'));
            $idMakul2 = array_search($data[$i]['urutan_lintas_minat'][1]['mapel_id'], array_column($dataMapel, 'id'));

            if ($dataMapel[$idMakul1]['kuota_kelas_terpakai'] <  $dataMapel[$idMakul1]['max_kuota_kelas']) {
                $mapelSelected = $data[$i]['urutan_lintas_minat'][0]['mapel_id'];
                $dataMapel[$idMakul1]['kuota_kelas_terpakai']++;
            } elseif ($dataMapel[$idMakul2]['kuota_kelas_terpakai'] <  $dataMapel[$idMakul2]['max_kuota_kelas']) {
                $mapelSelected = $data[$i]['urutan_lintas_minat'][1]['mapel_id'];
                $dataMapel[$idMakul2]['kuota_kelas_terpakai']++;
            } else {
                \Illuminate\Support\Facades\Log::debug("Siswa " . $data[$i]['nama_siswa'] . " harus memilih kelas 3 atau sekolah harus menambahkan kuota kelas");
            }
            $data[$i]['mapel_terpilih'] = $mapelSelected;
        }

        $returnData = [
            'data_siswa' => $data,
            'data_mapel' => $dataMapel
        ];

        \Illuminate\Support\Facades\Log::debug($data);
        return ($returnData);
        // \Illuminate\Support\Facades\Log::debug($data);
    }

    private function calculationInsertDb($data)
    {
        if (empty($data)) {
            return json_encode(['fail' => 'Class data is null']);
        }

        $studentData = $data['data_siswa'];
        $courseData = $data['data_mapel'];

        $dataMapel = \App\Models\Mapellm::get()->toArray();

        foreach ($dataMapel as $value) {
            $max_quota[$value['id']] = 0;
            $max_total_class[$value['id']] = 0;
        }

        $record = [];
        $className = "";

        for ($i = 0; $i < count($studentData); $i++) {
            if ($studentData[$i]['mapel_terpilih'] != 0) { // Hanya memproses siswa yang sudah ter-assign kelas
                $idSelectedMapel = array_search($studentData[$i]['mapel_terpilih'], array_column($courseData, 'id'));
                // $idSelectedMapel = $courseData[$idSelectedMapel][''];
                if ($max_quota[$studentData[$i]['mapel_terpilih']] < $courseData[$idSelectedMapel]['max_kuota_kelas'] && $max_total_class[$studentData[$i]['mapel_terpilih']] < $courseData[$idSelectedMapel]['jumlah_kelas']) {
                    // if ($max_quota[$studentData[$i]['mapel_terpilih']] == 0) {
                    //     $className = $courseData[$idSelectedMapel]['nama_mapel'] . '_' . chr(65);
                    // } else
                    // \Illuminate\Support\Facades\Log::debug($idSelectedMapel);
                    $className = $courseData[$idSelectedMapel]['nama_mapel'] . '_' . chr($max_total_class[$studentData[$i]['mapel_terpilih']] + 65);
                    if ($max_quota[$studentData[$i]['mapel_terpilih']] % $courseData[$idSelectedMapel]['kuota_kelas'] == 4) {
                        // $className = $courseData[$idSelectedMapel]['nama_mapel'] . '_' . chr($max_total_class[$studentData[$i]['mapel_terpilih']] + 65);
                        $max_total_class[$studentData[$i]['mapel_terpilih']]++;
                    }
                    $max_quota[$studentData[$i]['mapel_terpilih']]++;
                }

                // Save data
                // $value = [
                //     'id_siswa' => $data[$i]['id'],
                //     'id_mapellm' => $data[$i]['mapel_terpilih'],
                //     'nama_kelas' => $this->className($data[$i]),
                // ];
            }
            \Illuminate\Support\Facades\Log::debug($className);
        }
        // \App\Models\Siswa::insert($value);

        // \Illuminate\Support\Facades\Log::debug($max_quota);
        // \Illuminate\Support\Facades\Log::debug($max_total_class);
        return response()->json(['errors' => [0 => 'Fail to update data']]);
    }

    private function className($data)
    {
        if (empty($data)) {
            return json_encode(['fail' => 'Data inser is null']);
        }

        $dataMapel = \App\Models\Mapellm::get()->toArray();

        foreach ($dataMapel as $value) {
            $index[$value['id']] = 0;
        }

        foreach ($data as $value) {
            $index[$value['mapel_terpilih']]++;
        }
    }
}
