<?php

namespace App\Traits;

use App\Models\Siswa;
use Illuminate\Support\Facades\Log;

trait ClassCalculationTraits
{
    public function doClassCalculation()
    {
        $dataSiswa = \App\Models\Siswa::with('detailLm1', 'detailLm2', 'detailLm3')->get()->toArray();

        $doStudentClassOrder = $this->studentClassOrder($dataSiswa);
        $doAssignClass = $this->assignClass($doStudentClassOrder);
        $insertDb = $this->calculationInsertDb($doAssignClass);
        return $insertDb;
    }

    private function studentClassOrder($dataSiswa)
    {
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

        for ($i = 0; $i < count($data); $i++) {
            $data[$i]['selected_lintas_minat'] = 0;

            $idMakul1 = array_search($data[$i]['urutan_lintas_minat'][0]['mapel_id'], array_column($dataMapel, 'id'));
            $idMakul2 = array_search($data[$i]['urutan_lintas_minat'][1]['mapel_id'], array_column($dataMapel, 'id'));
            $idMakul3 = array_search($data[$i]['urutan_lintas_minat'][2]['mapel_id'], array_column($dataMapel, 'id'));

            if ($dataMapel[$idMakul1]['kuota_kelas_terpakai'] <  $dataMapel[$idMakul1]['max_kuota_kelas'] ||  $dataMapel[$idMakul2]['kuota_kelas_terpakai'] <  $dataMapel[$idMakul2]['max_kuota_kelas']) {
                if ($dataMapel[$idMakul1]['kuota_kelas_terpakai'] <  $dataMapel[$idMakul1]['max_kuota_kelas']) {
                    $dataMapel[$idMakul1]['kuota_kelas_terpakai']++;
                }
    
                if ($dataMapel[$idMakul2]['kuota_kelas_terpakai'] <  $dataMapel[$idMakul2]['max_kuota_kelas']) {
                    $dataMapel[$idMakul2]['kuota_kelas_terpakai']++;
                }
            }else{
                $dataMapel[$idMakul3]['kuota_kelas_terpakai']++;
            }
            // $data[$i]['mapel_terpilih'] = $mapelSelected;
        }

        $returnData = [
            'data_siswa' => $data,
            'data_mapel' => $dataMapel
        ];
        
        return ($returnData);
    }

    private function calculationInsertDb($data)
    {
        if (empty($data)) {
            return json_encode(['fail' => 'Class data is null']);
        }

        $studentData = $data['data_siswa'];
        $courseData = $data['data_mapel'];

        foreach ($courseData as $value) {
            $max_quota[$value['id']] = 0; // Total jumlah murid yang di kelas
            $max_total_class[$value['id']] = 0;
            $dataProcess[$value['id']] = [];
        }

        /**
         * Pilihan ke 1
         */
        for ($i = 0; $i < count($studentData); $i++) {
            $className = "";

            $mapelId1 = $studentData[$i]['urutan_lintas_minat'][0]['mapel_id'];

            $mapelVector1 = $studentData[$i]['urutan_lintas_minat'][0]['vector'];
            
            $idSelectedMapel1 = array_search($mapelId1, array_column($courseData, 'id'));
                
            if ($max_quota[$mapelId1] < $courseData[$idSelectedMapel1]['max_kuota_kelas']) {                        
                $max_quota[$mapelId1]++;

                // Save data
                $value = [
                    'id_siswa' => $studentData[$i]['id'],
                    'id_mapellm' => $mapelId1,
                    'nama_kelas' => '',
                    'jadwal' => \Carbon\Carbon::today('Asia/Jakarta')->format('Y-m-d H:i:s'),
                    'nilai' => $mapelVector1,
                ];

                Log::debug([
                    'pilihan' => 'satu',
                    'Nama Siswa' => $studentData[$i]['nama_siswa'],
                    'Kelas' => $mapelId1
                ]);

                // Tambah flag
                $studentData[$i]['selected_lintas_minat'] += 1; 

                array_push($dataProcess[$mapelId1], $value);

            }elseif($max_quota[$mapelId1] >= $courseData[$idSelectedMapel1]['max_kuota_kelas']){

                $arrayColumn = array_column($dataProcess[$mapelId1], 'nilai');
                $nilaiMinimal = min($arrayColumn);
                $nilaiMinimalArray = $dataProcess[$mapelId1][array_search($nilaiMinimal, $arrayColumn)];

                // Mencari pilihan mapel ke 3 dari dataProcess
                // $mapelPilihan3 = $dataProcess[$mapelId3][array_search(min($arrayColumn), $arrayColumn)];
                // $searchId = $studentData[array_search($mapelPilihan3['id_siswa'], array_column($studentData, 'id'))];
                // $idMapel = $studentData[$searchId['id']]['urutan_lintas_minat'][2]['mapel_id'];
                
                if ($mapelVector1 > $nilaiMinimal){

                    Log::debug([
                        'pilihan' => 'satu',
                        'Nama Siswa' => $studentData[$i]['nama_siswa'],
                        'Kelas' => $mapelId1,
                        'Nama Siswa Diganti' => $studentData[array_search($nilaiMinimalArray['id_siswa'], array_column($studentData, 'id'))]['nama_siswa'],
                    ]);
                    
                    // Save data
                    $value = [
                        'id_siswa' => $studentData[$i]['id'],
                        'id_mapellm' => $mapelId1,
                        'nama_kelas' => '',
                        'jadwal' => \Carbon\Carbon::today('Asia/Jakarta')->format('Y-m-d H:i:s'),
                        'nilai' => $mapelVector1,
                    ];
                    // Tambah flag
                    $studentData[$i]['selected_lintas_minat'] += 1; 
                    $studentData[array_search($nilaiMinimalArray['id_siswa'], array_column($studentData, 'id'))]['selected_lintas_minat'] -= 1;

                    // Ganti index dataprocess dengan nilai yang baru
                    $dataProcess[$mapelId1][array_search(min($arrayColumn), $arrayColumn)] = $value;
                }
            }
            Log::debug([
                'pilihan' => 'satu',
                'status' => 'penuh',
                'Nama Siswa' => $studentData[$i]['nama_siswa'],
                'Kelas' => $mapelId1,
                'Kuota Kelas' => $max_quota[$mapelId1]
            ]);
        // }

        /**
         * Pilihan ke 2
         */
        // for ($i = 0; $i < count($studentData); $i++) {
            $mapelId2 = $studentData[$i]['urutan_lintas_minat'][1]['mapel_id'];
            $mapelId3 = $studentData[$i]['urutan_lintas_minat'][2]['mapel_id'];
            
            $mapelVector2 = $studentData[$i]['urutan_lintas_minat'][1]['vector'];

            $idSelectedMapel2 = array_search($mapelId2, array_column($courseData, 'id'));
            
            if ($max_quota[$mapelId2] < $courseData[$idSelectedMapel2]['max_kuota_kelas']) {

                Log::debug([
                    'pilihan' => 'dua',
                    'Nama Siswa' => $studentData[$i]['nama_siswa'],
                    'Kelas' => $mapelId2
                ]);

                $max_quota[$mapelId2]++;

                // Save data
                $value = [
                    'id_siswa' => $studentData[$i]['id'],
                    'id_mapellm' => $mapelId2,
                    'nama_kelas' => '',
                    'jadwal' => \Carbon\Carbon::today('Asia/Jakarta')->format('Y-m-d H:i:s'),
                    'nilai' => $mapelVector2,
                ];

                // Tambah flag
                $studentData[$i]['selected_lintas_minat'] += 1; 

                array_push($dataProcess[$mapelId2], $value);

            }elseif($max_quota[$mapelId2] >= $courseData[$idSelectedMapel2]['max_kuota_kelas']){

                // Mencari minimal nilai dari pilihan ke 2 dari process data
                $arrayColumn = array_column($dataProcess[$mapelId2], 'nilai');
                $nilaiMinimal = min($arrayColumn);
                $listMinimalNilai = array_keys($arrayColumn,$nilaiMinimal);
                for ($j=0; $j < count($listMinimalNilai); $j++) {
                    // Log::debug($listMinimalNilai[$j]);
                    # code...
                    // Log::debug([
                    //     "listMinimalNilai" => $listMinimalNilai,
                    //     "data1" => $dataProcess[$mapelId2][$listMinimalNilai[0]],
                    //     "data2" => $dataProcess[$mapelId2][$listMinimalNilai[1]]
                    // ]);
                    $nilaiMinimalArray = $dataProcess[$mapelId2][$listMinimalNilai[$j]];
    
                    // Mencari pilihan mapel ke 3 dari dataProcess
                    $studentDataFound = $studentData[array_search($nilaiMinimalArray['id_siswa'], array_column($studentData, 'id'))];
                    
                    // mapel pilihan ke 3 dari student data
                    $idMapelPil3 = $studentDataFound['urutan_lintas_minat'][2]['mapel_id'];
                    $mapelVector3 = $studentDataFound['urutan_lintas_minat'][2]['vector'];
                    // Log::debug($idMapelPil3);
                    // Mencari index mapel ke 3 dari courseData
                    $idSelectedMapel3 = array_search($idMapelPil3, array_column($courseData, 'id'));
                    
                    // Log::debug($max_quota[$mapelId2]);
                    // Log::debug($max_quota[$idMapelPil3]);
                    // Log::debug($courseData[$idSelectedMapel3]['max_kuota_kelas']);
                    if (($mapelVector2 > $nilaiMinimal) && ($max_quota[$idMapelPil3] < $courseData[$idSelectedMapel3]['max_kuota_kelas'])){
                        Log::debug([
                            'pilihan' => 'dua',
                            'Nama Siswa' => $studentData[$i]['nama_siswa'],
                            'Kelas' => $mapelId2,
                            'Nama Siswa Diganti' => $studentDataFound['nama_siswa'],
                        ]);

                        // Save data
                        $value = [
                            'id_siswa' => $studentData[$i]['id'],
                            'id_mapellm' => $mapelId2,
                            'nama_kelas' => '',
                            'jadwal' => \Carbon\Carbon::today('Asia/Jakarta')->format('Y-m-d H:i:s'),
                            'nilai' => $mapelVector2,
                        ];
    
                        $valuePil3 = [
                            'id_siswa' => $studentDataFound['id'],
                            'id_mapellm' => $idMapelPil3,
                            'nama_kelas' => '',
                            'jadwal' => \Carbon\Carbon::today('Asia/Jakarta')->format('Y-m-d H:i:s'),
                            'nilai' => $mapelVector3,
                        ];
    
                        // Tambah flag
                        $studentData[$i]['selected_lintas_minat'] += 1; 
                        // $studentData[array_search($nilaiMinimalArray['id_siswa'], array_column($studentData, 'id'))]['selected_lintas_minat'] -= 1;
    
                        // Ganti index dataprocess dengan nilai yang baru
                        $dataProcess[$mapelId2][$listMinimalNilai[$j]] = $value;
                        
                        //Push ke mapel pilihan ke 3 data yang kegeser
                        array_push($dataProcess[$idMapelPil3], $valuePil3);
                        $max_quota[$idMapelPil3]++;

                        break;
                    }  
                }
            }
            Log::debug([
                'pilihan' => 'dua',
                'status' => 'penuh',
                'Nama Siswa' => $studentData[$i]['nama_siswa'],
                'Kelas' => $mapelId2,
                'Kuota Kelas' => $max_quota[$mapelId2]
            ]);
        // }

        /**
         * Pilihan ke 3
         */
        // for ($i = 0; $i < count($studentData); $i++) {
            $mapelId3 = $studentData[$i]['urutan_lintas_minat'][2]['mapel_id'];
            $mapelVector3 = $studentData[$i]['urutan_lintas_minat'][2]['vector'];
            
            $idSelectedMapel3 = array_search($mapelId3, array_column($courseData, 'id'));

            if ($studentData[$i]['selected_lintas_minat'] < 2)
            {
                if ($max_quota[$mapelId3] < $courseData[$idSelectedMapel3]['max_kuota_kelas']) {

                    $max_quota[$mapelId3]++;

                    Log::debug([
                        'pilihan' => 'tiga',
                        'Nama Siswa' => $studentData[$i]['nama_siswa'],
                        'Kelas' => $mapelId3
                    ]);

                    // Save data
                    $value = [
                        'id_siswa' => $studentData[$i]['id'],
                        'id_mapellm' => $mapelId3,
                        'nama_kelas' => '',
                        'jadwal' => \Carbon\Carbon::today('Asia/Jakarta')->format('Y-m-d H:i:s'),
                        'nilai' => $mapelVector3,
                    ];

                    // Tambah flag
                    $studentData[$i]['selected_lintas_minat'] += 1; 

                    array_push($dataProcess[$mapelId3], $value);

                }elseif($max_quota[$mapelId3] >= $courseData[$idSelectedMapel3]['max_kuota_kelas']){

                    $arrayColumn = array_column($dataProcess[$mapelId3], 'nilai');
                    $nilaiMinimal = min($arrayColumn);
                    $nilaiMinimalArray = $dataProcess[$mapelId3][array_search($nilaiMinimal, $arrayColumn)];
                    $studentDataFound = $studentData[array_search($nilaiMinimalArray['id_siswa'], array_column($studentData, 'id'))];

                    if ($mapelVector3 > $nilaiMinimal){

                        Log::debug([
                            'pilihan' => 'tiga',
                            'Nama Siswa' => $studentData[$i]['nama_siswa'],
                            'Kelas' => $mapelId2,
                            'Nama Siswa Diganti' => $studentDataFound['nama_siswa'],
                        ]);


                        // Save data
                        $value = [
                            'id_siswa' => $studentData[$i]['id'],
                            'id_mapellm' => $mapelId3,
                            'nama_kelas' => '',
                            'jadwal' => \Carbon\Carbon::today('Asia/Jakarta')->format('Y-m-d H:i:s'),
                            'nilai' => $mapelVector3,
                        ];

                        // Tambah flag
                        $studentData[$i]['selected_lintas_minat'] += 1; 
                        $studentData[array_search($nilaiMinimalArray['id_siswa'], array_column($studentData, 'id'))]['selected_lintas_minat'] -= 1;

                        // Ganti index dataprocess dengan nilai yang baru
                        $dataProcess[$mapelId3][array_search(min($arrayColumn), $arrayColumn)] = $value;
                    }
                }
            }
        }

        // Log::debug($dataProcess);
        // Index $courseData dimulai dari 0, lainnya mulai dari 1
        // Set Nama Kelas
        for ($i=0; $i < count($dataProcess); $i++) { 
            // Mencari detail mapel yang terpilih
            $idSelectedMapel = array_search($i+1, array_column($courseData, 'id'));
            $mapel = $courseData[$idSelectedMapel];
            
            for ($j=0; $j < count($dataProcess[$i+1]); $j++) { 
                
                
                $className = $mapel['nama_mapel'] . '_' . chr($max_total_class[$i+1] + 65);
                
                // Save Data
                $dataProcess[$i+1][$j]['nama_kelas'] = $className;
                unset($dataProcess[$i+1][$j]['nilai']);
                
                // Nama Kelas
                if ($j % $courseData[$idSelectedMapel]['max_kuota_kelas'] ==  $courseData[$idSelectedMapel]['kuota_kelas']-1) {
                    $max_total_class[$i+1]++;
                }
                
                if (!\App\Models\Kelaslm::insert($dataProcess[$i+1][$j])) {
                    return response()->json(['errors' => [0 => 'Fail to update data']]);
                }
            }
        }
    }
}
