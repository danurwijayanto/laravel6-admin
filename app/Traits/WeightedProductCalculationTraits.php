<?php

use App\Models\Siswa;
use Illuminate\Support\Facades\Log;

namespace App\Traits;

trait WeightedProductCalculationTraits
{
    public function doCalculate()
    {
        $convertion = $this->convertion();
        $calculateSValue = $this->calculateSValue($convertion);
        $calculateVValue = $this->calculateVValue($calculateSValue);
        $insertDb = $this->insertDb($calculateVValue);

        return $insertDb;
    }

    // Fungsi untuk melakukan konversi 
    private function convertion()
    {

        $dataSiswa = \App\Models\Siswa::with('detailLm1', 'detailLm2', 'detailLm3')->get()->toArray();
        if (empty($dataSiswa)) {
            return json_encode(['fail' => 'Student data is null']);
        }

        for ($i = 0; $i < count($dataSiswa); $i++) {
            $lintasMinat = [
                'lm1' => [
                    'k1' => $this->kriteria_convertion('k1', 1),
                    'k2' =>  $this->kriteria_convertion('k2', $dataSiswa[$i]['nilai_raport']),
                    'k3' => $this->kriteria_convertion('k3', $dataSiswa[$i]['detail_lm1']['nama_mapel']),
                ],
                'lm2' => [
                    'k1' =>  $this->kriteria_convertion('k1', 2),
                    'k2' => $this->kriteria_convertion('k2', $dataSiswa[$i]['nilai_raport']),
                    'k3' => $this->kriteria_convertion('k3', $dataSiswa[$i]['detail_lm2']['nama_mapel']),
                ],
                'lm3' => [
                    'k1' =>  $this->kriteria_convertion('k1', 3),
                    'k2' => $this->kriteria_convertion('k2', $dataSiswa[$i]['nilai_raport']),
                    'k3' => $this->kriteria_convertion('k3', $dataSiswa[$i]['detail_lm3']['nama_mapel']),
                ],
            ];
            $dataSiswa[$i]['hasil_konversi'] = $lintasMinat;
        }

        return $dataSiswa;
    }

    private function kriteria_convertion($kode_kriteria = "", $nilai = "")
    {
        if ($kode_kriteria == "k1") {
            if ($nilai == 1) {
                return 5;
            } elseif ($nilai == 2) {
                return 3;
            } else {
                return 1;
            }
        } elseif ($kode_kriteria == "k2") {
            if ($nilai > 85.0) {
                return 5;
            } elseif ($nilai >= 71.0 && $nilai <= 85.0) {
                return 4;
            } elseif ($nilai >= 56.0 && $nilai <= 70.0) {
                return 3;
            } elseif ($nilai >= 40.0 && $nilai <= 55.0) {
                return 2;
            } elseif ($nilai < 40.0) {
                return 1;
            }
        } elseif ($kode_kriteria == "k3") {
            if ($nilai == "geografi" || $nilai == "jerman") {
                return 1;
            } elseif ($nilai == "inggris") {
                return 2;
            } else {
                return 3;
            }
        }
        return 0;
    }

    private function calculateWeightNormalization()
    {
        $kriteriaCode = [
            'k1' => [
                'nama' => 'Minat Siswa',
                'bobot' => 5,
                'tingkat_kepentingan' => 'sangat tinggi',
            ],
            'k2' => [
                'nama' => 'Nilai rata - rata raport',
                'bobot' => 4,
                'tingkat_kepentingan' => 'tinggi',
            ],
            'k3' => [
                'nama' => 'Jumlah kelas',
                'bobot' => 2,
                'tingkat_kepentingan' => 'rendah',
            ],
        ];

        $totalW = $kriteriaCode['k1']['bobot'] + $kriteriaCode['k2']['bobot'] + $kriteriaCode['k3']['bobot'];

        $weightNormalization = [
            'w1' => $kriteriaCode['k1']['bobot'] / ($totalW),
            'w2' => $kriteriaCode['k2']['bobot'] / ($totalW),
            'w3' => $kriteriaCode['k3']['bobot'] / ($totalW),
        ];

        return $weightNormalization;
    }


    private function calculateSValue($data)
    {
        if (empty($data)) {
            return json_encode(['fail' => 'Convertion data is null']);
        }

        $weightNormalization = $this->calculateWeightNormalization();

        for ($i = 0; $i < count($data); $i++) {
            $sMinat = [
                'sMinat1' => pow($data[$i]['hasil_konversi']['lm1']['k1'], $weightNormalization['w1']) * pow($data[$i]['hasil_konversi']['lm1']['k2'], $weightNormalization['w2']) * pow($data[$i]['hasil_konversi']['lm1']['k3'], $weightNormalization['w3']),
                'sMinat2' => pow($data[$i]['hasil_konversi']['lm2']['k1'], $weightNormalization['w1']) * pow($data[$i]['hasil_konversi']['lm2']['k2'], $weightNormalization['w2']) * pow($data[$i]['hasil_konversi']['lm2']['k3'], $weightNormalization['w3']),
                'sMinat3' => pow($data[$i]['hasil_konversi']['lm3']['k1'], $weightNormalization['w1']) * pow($data[$i]['hasil_konversi']['lm3']['k2'], $weightNormalization['w2']) * pow($data[$i]['hasil_konversi']['lm3']['k3'], $weightNormalization['w3']),
            ];
            $data[$i]['s_value'] = $sMinat;
        }

        // \Illuminate\Support\Facades\Log::debug($data);

        return $data;
    }

    private function calculateVValue($data)
    {
        if (empty($data)) {
            return json_encode(['fail' => 'S data is null']);
        }

        for ($i = 0; $i < count($data); $i++) {
            $total_vector_s = $data[$i]['s_value']['sMinat1'] + $data[$i]['s_value']['sMinat2'] + $data[$i]['s_value']['sMinat3'];

            $vValue = [
                'v1' => $data[$i]['s_value']['sMinat1'] / $total_vector_s,
                'v2' => $data[$i]['s_value']['sMinat2'] / $total_vector_s,
                'v3' => $data[$i]['s_value']['sMinat3'] / $total_vector_s,
            ];

            $data[$i]['v_value'] = $vValue;
        }

        // \Illuminate\Support\Facades\Log::debug($data);
        return $data;
    }

    private function insertDb($data)
    {
        if (empty($data)) {
            return json_encode(['fail' => 'S data is null']);
        }

        for ($i = 0; $i < count($data); $i++) {
            $total_vector_s = $data[$i]['s_value']['sMinat1'] + $data[$i]['s_value']['sMinat2'] + $data[$i]['s_value']['sMinat3'];

            $vValue = [
                'v1' => $data[$i]['s_value']['sMinat1'] / $total_vector_s,
                'v2' => $data[$i]['s_value']['sMinat2'] / $total_vector_s,
                'v3' => $data[$i]['s_value']['sMinat3'] / $total_vector_s,
            ];

            $data[$i]['v_value'] = $vValue;
        }

        // \Illuminate\Support\Facades\Log::debug($data);
        return $data;
    }
}
