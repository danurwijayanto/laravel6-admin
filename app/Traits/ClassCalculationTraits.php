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
            $mapelSelected = 0;

            $idMakul1 = array_search($data[$i]['urutan_lintas_minat'][0]['mapel_id'], array_column($dataMapel, 'id'));
            $idMakul2 = array_search($data[$i]['urutan_lintas_minat'][1]['mapel_id'], array_column($dataMapel, 'id'));
            $idMakul3 = array_search($data[$i]['urutan_lintas_minat'][2]['mapel_id'], array_column($dataMapel, 'id'));

            if ($dataMapel[$idMakul1]['kuota_kelas_terpakai'] <  $dataMapel[$idMakul1]['max_kuota_kelas'] ||  $dataMapel[$idMakul2]['kuota_kelas_terpakai'] <  $dataMapel[$idMakul2]['max_kuota_kelas']) {
                if ($dataMapel[$idMakul1]['kuota_kelas_terpakai'] <  $dataMapel[$idMakul1]['max_kuota_kelas']) {
                    $mapelSelected = $data[$i]['urutan_lintas_minat'][0]['mapel_id'];
                    $dataMapel[$idMakul1]['kuota_kelas_terpakai']++;
                }
    
                if ($dataMapel[$idMakul2]['kuota_kelas_terpakai'] <  $dataMapel[$idMakul2]['max_kuota_kelas']) {
                    $mapelSelected = $data[$i]['urutan_lintas_minat'][1]['mapel_id'];
                    $dataMapel[$idMakul2]['kuota_kelas_terpakai']++;
                }
            }else{
                $mapelSelected = $data[$i]['urutan_lintas_minat'][2]['mapel_id'];
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

        \Illuminate\Support\Facades\Log::debug($studentData);

        foreach ($courseData as $value) {
            $max_quota[$value['id']] = 0; // Total jumlah murid yang di kelas
            $max_total_class[$value['id']] = 0;
            $dataProcess[$value['id']] = [];
        }

        $ignoredRecord = [];
        $ignoredRecord2 = [];

        for ($i = 0; $i < count($studentData); $i++) {
            $className = "";

            $mapelId1 = $studentData[$i]['urutan_lintas_minat'][0]['mapel_id'];
            $mapelId2 = $studentData[$i]['urutan_lintas_minat'][1]['mapel_id'];
            $mapelId3 = $studentData[$i]['urutan_lintas_minat'][2]['mapel_id'];

            $mapelVector1 = $studentData[$i]['urutan_lintas_minat'][0]['vector'];
            $mapelVector2 = $studentData[$i]['urutan_lintas_minat'][1]['vector'];
            
            $idSelectedMapel1 = array_search($mapelId1, array_column($courseData, 'id'));
            $idSelectedMapel2 = array_search($mapelId2, array_column($courseData, 'id'));
            $idSelectedMapel3 = array_search($mapelId3, array_column($courseData, 'id'));
                
            if ($max_quota[$mapelId1] < $courseData[$idSelectedMapel1]['max_kuota_kelas'] || $max_quota[$mapelId2] < $courseData[$idSelectedMapel2]['max_kuota_kelas']) {
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
                        "note" => "set pil 1", 
                        "nilai minimal" => "", 
                        "mapel set" => "",
                        "mapel set" => $value]);

                    array_push($dataProcess[$mapelId1], $value);

                }elseif($max_quota[$mapelId1] >= $courseData[$idSelectedMapel1]['max_kuota_kelas']){
                    // $studentScore = $studentData[$i]['nilai_raport'];

                    $arrayColumn = array_column($dataProcess[$mapelId1], 'nilai');
                    $nilaiMinimal = min($arrayColumn);
                    $nilaiMinimalArray = $dataProcess[$mapelId1][array_search($nilaiMinimal, $arrayColumn)];
                    $studentDataMinimal = $studentData[array_search($nilaiMinimalArray['id_siswa'], array_column($studentData, 'id'))];

                    // Mencari pilihan mapel ke 3 dari dataProcess
                    $mapelPilihan3 = $dataProcess[$mapelId3][array_search(min($arrayColumn), $arrayColumn)];
                    $searchId = $studentData[array_search($mapelPilihan3['id_siswa'], array_column($studentData, 'id'))];
                    $idMapel = $studentData[$searchId['id']]['urutan_lintas_minat'][2]['mapel_id'];
                    
                    if ($mapelVector1 > $nilaiMinimal && $max_quota[$idMapel] < $courseData[$idMapel]['max_kuota_kelas']){
                        
                        // Save data
                        $value = [
                            'id_siswa' => $studentData[$i]['id'],
                            'id_mapellm' => $mapelId1,
                            'nama_kelas' => '',
                            'jadwal' => \Carbon\Carbon::today('Asia/Jakarta')->format('Y-m-d H:i:s'),
                            'nilai' => $mapelVector1,
                        ];

                        Log::debug([
                            "note" => "switch pil 1", 
                            "nilai minimal" => $nilaiMinimal, 
                            "mapel yang diganti" => $dataProcess[$mapelId1][array_search(min($arrayColumn), $arrayColumn)],
                            "id mapel 3 yang diganti" => $idMapel,
                            "uotaa id mapel 3 yang diganti" =>$max_quota[$idMapel],
                            "max uotaa id mapel 3 yang diganti" =>$courseData[$idMapel]['max_kuota_kelas'],
                            "mapel pengganti" => $value,
                            "student data minimal" => $studentDataMinimal,
                            "student data" => $studentData[$i],
                        ]);

                        // Masukkan ke ignored record index yang kereplace
                        array_push($ignoredRecord, $studentDataMinimal);

                        // Ganti index dataprocess dengan nilai yang baru
                        $dataProcess[$mapelId1][array_search(min($arrayColumn), $arrayColumn)] = $value;

                    }else{
                        array_push($ignoredRecord, $studentData[$i]);
                    }
                }

                if ($max_quota[$mapelId2] < $courseData[$idSelectedMapel2]['max_kuota_kelas']) {

                    $max_quota[$mapelId2]++;

                    // Save data
                    $value = [
                        'id_siswa' => $studentData[$i]['id'],
                        'id_mapellm' => $mapelId2,
                        'nama_kelas' => '',
                        'jadwal' => \Carbon\Carbon::today('Asia/Jakarta')->format('Y-m-d H:i:s'),
                        'nilai' => $mapelVector2,
                    ];

                    Log::debug([
                        "note" => "set pil 2", 
                        "nilai minimal" => "", 
                        "mapel set" => "",
                        "mapel set" => $value]);

                    array_push($dataProcess[$mapelId2], $value);

                }elseif($max_quota[$mapelId2] >= $courseData[$idSelectedMapel2]['max_kuota_kelas']){
                    // $studentScore = $studentData[$i]['nilai_raport'];

                    $arrayColumn = array_column($dataProcess[$mapelId2], 'nilai');
                    $nilaiMinimal = min($arrayColumn);
                    $nilaiMinimalArray = $dataProcess[$mapelId2][array_search($nilaiMinimal, $arrayColumn)];
                    $studentDataMinimal = $studentData[array_search($nilaiMinimalArray['id_siswa'], array_column($studentData, 'id'))];

                    // Mencari pilihan mapel ke 3 dari dataProcess
                    $mapelPilihan3 = $dataProcess[$mapelId3][array_search(min($arrayColumn), $arrayColumn)];
                    $searchId = $studentData[array_search($mapelPilihan3['id_siswa'], array_column($studentData, 'id'))];
                    Log::debug([
                        "mapelPilihan3" => $mapelPilihan3, 
                        "searchId" => $searchId, 
                    ]);
                    $idMapel = $studentData[$searchId['id']]['urutan_lintas_minat'][2]['mapel_id'];
                    
                    
                    if ($mapelVector2 > $nilaiMinimal && $max_quota[$idMapel] < $courseData[$idMapel]['max_kuota_kelas']){
                        // Save data
                        $value = [
                            'id_siswa' => $studentData[$i]['id'],
                            'id_mapellm' => $mapelId2,
                            'nama_kelas' => '',
                            'jadwal' => \Carbon\Carbon::today('Asia/Jakarta')->format('Y-m-d H:i:s'),
                            'nilai' => $mapelVector2,
                        ];
                        Log::debug([
                            "note" => "switch pil 2", 
                            "nilai minimal" => $nilaiMinimal, 
                            "mapel yang diganti" => $dataProcess[$mapelId2][array_search(min($arrayColumn), $arrayColumn)],
                            "id mapel 3 yang diganti" => $idMapel,
                            "uotaa id mapel 3 yang diganti" =>$max_quota[$idMapel],
                            "max uotaa id mapel 3 yang diganti" =>$courseData[$idMapel]['max_kuota_kelas'],
                            "mapel pengganti" => $value, 
                            "student data minimal" => $studentDataMinimal

                        ]);
                        
                        // Masukkan ke ignored record index yang kereplace
                        array_push($ignoredRecord, $studentDataMinimal);
                        // Ganti index dataprocess dengan nilai yang baru
                        $dataProcess[$mapelId2][array_search(min($arrayColumn), $arrayColumn)] = $value;

                    }else{
                        array_push($ignoredRecord,  $studentData[$i]);
                    }  
                }
            }
        }
        // Ada masalah di ignored record
        \Illuminate\Support\Facades\Log::debug(["ignored record" => $ignoredRecord]);
        for ($i = 0; $i < count($ignoredRecord); $i++) {
            $mapelId3 = $ignoredRecord[$i]['urutan_lintas_minat'][2]['mapel_id'];
            $mapelVector3 = $ignoredRecord[$i]['urutan_lintas_minat'][2]['vector'];
            
            $idSelectedMapel3 = array_search($mapelId3, array_column($courseData, 'id'));

            $isSetMapelPilihan1 = array_search($ignoredRecord[$i]['id'], array_column($dataProcess[$ignoredRecord[$i]['urutan_lintas_minat'][0]['mapel_id']], 'id_siswa'));
            $isSetMapelPilihan2 = array_search($ignoredRecord[$i]['id'], array_column($dataProcess[$ignoredRecord[$i]['urutan_lintas_minat'][1]['mapel_id']], 'id_siswa'));

            Log::debug([
                "isSetMapelPilihan1" => $isSetMapelPilihan1, 
                "isSetMapelPilihan2" => $isSetMapelPilihan2]);

            if (empty( $isSetMapelPilihan1) && empty($isSetMapelPilihan2))
            {
                $value = [
                    'id_siswa' => $ignoredRecord[$i]['id'],
                    'id_mapellm' => $mapelId3,
                    'nama_kelas' => '',
                    'jadwal' => \Carbon\Carbon::today('Asia/Jakarta')->format('Y-m-d H:i:s'),
                    'nilai' => $mapelVector3,
                ];

                Log::debug([
                    "note" => "is empty", 
                    "nilai minimal" => "", 
                    "mapel set" => "",
                    "mapel set" => $value]);
                // Masukkan ke ignored record index yang kereplace
                // array_push($ignoredRecord2, $studentDataMinimal);
                // Ganti index dataprocess dengan nilai yang baru
                // $dataProcess[$mapelId3][array_search(min($arrayColumn), $arrayColumn)] = $value;
            }
            
            if ($max_quota[$mapelId3] < $courseData[$idSelectedMapel3]['max_kuota_kelas']) {

                $max_quota[$mapelId3]++;

                // Save data
                $value = [
                    'id_siswa' => $ignoredRecord[$i]['id'],
                    'id_mapellm' => $mapelId3,
                    'nama_kelas' => '',
                    'jadwal' => \Carbon\Carbon::today('Asia/Jakarta')->format('Y-m-d H:i:s'),
                    'nilai' => $mapelVector3,
                ];

                array_push($dataProcess[$mapelId3], $value);

            }elseif($max_quota[$mapelId3] >= $courseData[$idSelectedMapel3]['max_kuota_kelas']){
                // $studentScore = $studentData[$i]['nilai_raport'];
                \Illuminate\Support\Facades\Log::debug([
                    "max quota" => $max_quota,
                    "courseData" => $courseData,
                ]);

                $arrayColumn = array_column($dataProcess[$mapelId3], 'nilai');
                $nilaiMinimal = min($arrayColumn);
                $nilaiMinimalArray = $dataProcess[$mapelId3][array_search($nilaiMinimal, $arrayColumn)];
                $studentDataMinimal = $studentData[array_search($nilaiMinimalArray['id_siswa'], array_column($studentData, 'id'))];
                
                if ($mapelVector3 > $nilaiMinimal){
                    // Save data
                    $value = [
                        'id_siswa' => $studentData[$i]['id'],
                        'id_mapellm' => $mapelId3,
                        'nama_kelas' => '',
                        'jadwal' => \Carbon\Carbon::today('Asia/Jakarta')->format('Y-m-d H:i:s'),
                        'nilai' => $mapelVector3,
                    ];

                    $isSetMapelPilihan1 = array_search($ignoredRecord[$i]['id'], array_column($dataProcess[$ignoredRecord[$i]['urutan_lintas_minat'][0]['mapel_id']], 'id_siswa'));
                    $isSetMapelPilihan2 = array_search($ignoredRecord[$i]['id'], array_column($dataProcess[$ignoredRecord[$i]['urutan_lintas_minat'][1]['mapel_id']], 'id_siswa'));

                    if (empty( $isSetMapelPilihan1) && empty($isSetMapelPilihan2))
                    {
                        // Masukkan ke ignored record index yang kereplace
                        array_push($ignoredRecord2, $studentDataMinimal);
                        // Ganti index dataprocess dengan nilai yang baru
                        $dataProcess[$mapelId3][array_search(min($arrayColumn), $arrayColumn)] = $value;
                    }
                    
                }else{
                    array_push($ignoredRecord2,  $studentData[$i]);
                }  
            }
        }
        \Illuminate\Support\Facades\Log::debug($dataProcess);

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
