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
        // return $insertDb;
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
                // \Illuminate\Support\Facades\Log::debug("Siswa " . $data[$i]['nama_siswa'] . " harus memilih kelas 3 atau sekolah harus menambahkan kuota kelas");

            }
            $data[$i]['mapel_terpilih'] = $mapelSelected;
        }

        $returnData = [
            'data_siswa' => $data,
            'data_mapel' => $dataMapel
        ];
        
        // \Illuminate\Support\Facades\Log::debug($returnData, $dataMapel);
        return ($returnData);
    }

    private function calculationInsertDb($data)
    {
        if (empty($data)) {
            return json_encode(['fail' => 'Class data is null']);
        }

        $studentData = $data['data_siswa'];
        $courseData = $data['data_mapel'];

        // $dataMapel = \App\Models\Mapellm::get()->toArray();

        foreach ($courseData as $value) {
            $max_quota[$value['id']] = 0;
            $max_total_class[$value['id']] = 0;
            $dataProcess[$value['id']] = [];
        }

        $ignoredRecord = [];
        $ignoredRecord2 = [];

        for ($i = 0; $i < count($studentData); $i++) {
            $className = "";

            $mapelId1 = $studentData[$i]['urutan_lintas_minat'][0]['mapel_id'];
            $mapelId2 = $studentData[$i]['urutan_lintas_minat'][1]['mapel_id'];
            // $mapelId3 = $studentData[$i]['urutan_lintas_minat'][2]['mapel_id'];

            $idSelectedMapel1 = array_search($mapelId1, array_column($courseData, 'id'));
            $idSelectedMapel2 = array_search($mapelId2, array_column($courseData, 'id'));
            // $idSelectedMapel3 = array_search($mapelId3, array_column($courseData, 'id'));
                
            if ($max_quota[$mapelId1] < $courseData[$idSelectedMapel1]['max_kuota_kelas'] || $max_quota[$mapelId2] < $courseData[$idSelectedMapel2]['max_kuota_kelas']) {
                if ($max_quota[$mapelId1] < $courseData[$idSelectedMapel1]['max_kuota_kelas']) {
                    // $className = $courseData[$idSelectedMapel1]['nama_mapel'] . '_' . chr($max_total_class[$mapelId1] + 65);
                    
                    if ($max_quota[$mapelId1] % $courseData[$idSelectedMapel1]['max_kuota_kelas'] ==  $courseData[$idSelectedMapel1]['kuota_kelas']-1) {
                        $max_total_class[$mapelId1]++;
                    }
    
                    $max_quota[$mapelId1]++;

                    // Save data
                    $value = [
                        'id_siswa' => $studentData[$i]['id'],
                        'id_mapellm' => $mapelId1,
                        'nama_kelas' => '',
                        'jadwal' => \Carbon\Carbon::today('Asia/Jakarta')->format('Y-m-d H:i:s'),
                        'nilai' => $studentData[$i]['nilai_raport'],
                    ];
                    array_push($dataProcess[$mapelId1], $value);
                    // \App\Models\Kelaslm::insert($value);

                }elseif($max_quota[$mapelId1] >= $courseData[$idSelectedMapel1]['max_kuota_kelas']){
                    // $className = $courseData[$idSelectedMapel1]['nama_mapel'] . '_' . chr($max_total_class[$mapelId1] + 65);
                    // Maka data disimpan dan hapus data dengan nilai terendah
                    $studentScore = $studentData[$i]['nilai_raport'];
                    
                    // $lowestScore = \Illuminate\Support\Facades\DB::table('mapellm_siswa')->selectRaw('*')
                    // ->whereRaw('nilai_minimal = (select min(nilai_minimal) 
                    // from mapellm_siswa 
                    // where nama_kelas=?) AND nama_kelas=?', [$className, $className])
                    // ->limit(1)
                    // ->get();
                    // \Illuminate\Support\Facades\Log::debug($lowestScore);

                    // if (!empty($lowestScore)){
                        // $lowestScore = $lowestScore[0];
                        $nilaiMinimal = max(array_column($dataProcess[$mapelId1], 'Nilai'));
                        
                        \Illuminate\Support\Facades\Log::debug("ERROR");
                        if ($studentScore > $nilaiMinimal){
                            // $className = $lowestScore->nama_kelas;
                            
                            // Delete data
                            // \App\Models\Kelaslm::where('id', $lowestScore->id)->delete();
                            
                            // Save deleted data
                            array_push($ignoredRecord, $lowestScore);

                            // Save data
                            $value = [
                                'id_siswa' => $studentData[$i]['id'],
                                'id_mapellm' => $mapelId1,
                                'nama_kelas' => '',
                                'jadwal' => \Carbon\Carbon::today('Asia/Jakarta')->format('Y-m-d H:i:s'),
                                'nilai' => $studentData[$i]['nilai_raport'],
                            ];
                            array_push($dataProcess[$mapelId1], $value);
                            // \App\Models\Kelaslm::insert($value);

                        }else{
                            array_push($ignoredRecord, $studentData[$i]);
                        }
                    // }   
                }

                // if ($max_quota[$mapelId2] < $courseData[$idSelectedMapel2]['max_kuota_kelas']) {
                //     $className = $courseData[$idSelectedMapel2]['nama_mapel'] . '_' . chr($max_total_class[$mapelId2] + 65);
                    
                //     if ($max_quota[$mapelId2] % $courseData[$idSelectedMapel2]['max_kuota_kelas'] ==  $courseData[$idSelectedMapel2]['kuota_kelas']-1) {
                //         $max_total_class[$mapelId2]++;
                //     }
    
                //     $max_quota[$mapelId2]++;

                //     // Save data
                //     $value = [
                //         'id_siswa' => $studentData[$i]['id'],
                //         'id_mapellm' => $mapelId2,
                //         'nama_kelas' => $className,
                //         'jadwal' => \Carbon\Carbon::today('Asia/Jakarta')->format('Y-m-d H:i:s'),
                //         // 'max_quota' => $max_quota[$studentData[$i]['mapel_terpilih']],
                //         // 'max_total_class' => $max_total_class[$mapelId1],
                //     ];

                //     \App\Models\Kelaslm::insert($value);
                // }elseif($max_quota[$mapelId2] >= $courseData[$idSelectedMapel2]['max_kuota_kelas']){
                //     $className = $courseData[$idSelectedMapel2]['nama_mapel'] . '_' . chr($max_total_class[$mapelId2] + 65);
                //     // Maka data disimpan dan hapus data dengan nilai terendah
                //     $studentScore = $studentData[$i]['nilai_raport'];

                //     $lowestScore = \Illuminate\Support\Facades\DB::table('mapellm_siswa')->selectRaw('*')
                //     ->whereRaw('nilai_minimal = (select min(nilai_minimal) 
                //     from mapellm_siswa 
                //     where nama_kelas=?) AND nama_kelas=?', [$className, $className])
                //     ->limit(1)
                //     ->get();
                //     \Illuminate\Support\Facades\Log::debug($lowestScore);

                //     if (!empty($lowestScore) && count($lowestScore) > 0){
                //         $lowestScore = $lowestScore[0];

                //         \Illuminate\Support\Facades\Log::debug("ERROR1");
                //         if ($studentScore > $lowestScore->nilai_minimal){
                //             $className = $lowestScore->nama_kelas;
                            
                //             // Delete data
                //             \App\Models\Kelaslm::where('id', $lowestScore->id)->delete();
                            
                //             // Save deleted data
                //             array_push($ignoredRecord, $lowestScore);

                //             // Save data
                //             $value = [
                //                 'id_siswa' => $studentData[$i]['id'],
                //                 'id_mapellm' => $mapelId2,
                //                 'nama_kelas' => $className,
                //                 'jadwal' => \Carbon\Carbon::today('Asia/Jakarta')->format('Y-m-d H:i:s'),
                //                 // 'max_quota' => $max_quota[$studentData[$i]['mapel_terpilih']],
                //                 // 'max_total_class' => $max_total_class[$mapelId1],
                //             ];
                            
                //             \App\Models\Kelaslm::insert($value);

                //         }else{
                //             array_push($ignoredRecord,  $studentData[$i]);
                //         }
                //     }    
                // }
            }
        }
        
        // \Illuminate\Support\Facades\Log::debug($ignoredRecord);
        \Illuminate\Support\Facades\Log::debug($dataProcess);
        
        // Perhitungan Tahap 3
        // $ignoredDataSiswa = [];
        
        // for ($i = 0; $i < count($ignoredRecord); $i++) {    
        //     $dataSiswa = \App\Models\Siswa::where('id', $ignoredRecord[$i]->id_siswa)
        //         ->with('detailLm1', 'detailLm2', 'detailLm3')
        //         ->get()->toArray();
        //     if (!empty($dataSiswa)){
        //         array_push($ignoredDataSiswa,  $dataSiswa[0]);
        //     }
        // }

        // $doStudentClassOrder2 = $this->studentClassOrder($ignoredDataSiswa);
        // $doAssignClass2 = $this->assignClass($doStudentClassOrder2);

        // \Illuminate\Support\Facades\Log::debug($doAssignClass2);
        // \Illuminate\Support\Facades\Log::debug("+++++++++++++++++++++++++++++++++++++++++++++++++++++");

        // $studentData2 = $doAssignClass2['data_siswa'];
        // $courseData2 = $doAssignClass2['data_mapel'];
 
        // for ($i = 0; $i < count($studentData2); $i++) {
        //     $className = "";

        //     $mapelId3 = $studentData2[$i]['urutan_lintas_minat'][2]['mapel_id'];

        //     $idSelectedMapel3 = array_search($mapelId3, array_column($courseData2, 'id'));
            
        //     if ($max_quota[$mapelId3] < $courseData2[$idSelectedMapel3]['max_kuota_kelas']) {
        //         $className = $courseData2[$idSelectedMapel3]['nama_mapel'] . '_' . chr($max_total_class[$mapelId1] + 65);
                
        //         if ($max_quota[$mapelId3] % $courseData2[$idSelectedMapel3]['max_kuota_kelas'] ==  $courseData2[$idSelectedMapel3]['kuota_kelas']-1) {
        //             $max_total_class[$mapelId3]++;
        //         }

        //         $max_quota[$mapelId3]++;

        //         // Save data
        //         $value = [
        //             'id_siswa' => $studentData2[$i]['id'],
        //             'id_mapellm' => $mapelId3,
        //             'nama_kelas' => $className,
        //             'jadwal' => \Carbon\Carbon::today('Asia/Jakarta')->format('Y-m-d H:i:s'),
        //             // 'max_quota' => $max_quota[$studentData2[$i]['mapel_terpilih']],
        //             // 'max_total_class' => $max_total_class[$mapelId1],
        //         ];

        //         \App\Models\Kelaslm::insert($value);

        //     }
        //     elseif($max_quota[$mapelId3] >= $courseData2[$idSelectedMapel3]['max_kuota_kelas']){
        //         $className = $courseData2[$idSelectedMapel3]['nama_mapel'] . '_' . chr($max_total_class[$mapelId3] + 65);
        //         // Maka data disimpan dan hapus data dengan nilai terendah
        //         $studentScore = $studentData2[$i]['nilai_raport'];
        //         \Illuminate\Support\Facades\Log::debug("---------------------------------------------------------");

        //         $lowestScore = \Illuminate\Support\Facades\DB::table('mapellm_siswa')->selectRaw('*')
        //         ->whereRaw('nilai_minimal = (select min(nilai_minimal) 
        //         from mapellm_siswa 
        //         where nama_kelas=?) AND nama_kelas=?', [$className, $className])
        //         ->limit(1)
        //         ->get();

        //         if (!empty($lowestScore) && count($lowestScore) > 0){

        //             $lowestScore = $lowestScore[0];
                    
        //             \Illuminate\Support\Facades\Log::debug("ERROR");
        //             if ($studentScore > $lowestScore->nilai_minimal){
        //                 $className = $lowestScore->nama_kelas;
                        
        //                 // Delete data
        //                 \App\Models\Kelaslm::where('id', $lowestScore->id)->delete();
                        
        //                 // Save deleted data
        //                 array_push($ignoredRecord2, $lowestScore);

        //                 // Save data
        //                 $value = [
        //                     'id_siswa' => $studentData2[$i]['id'],
        //                     'id_mapellm' => $mapelId3,
        //                     'nama_kelas' => $className,
        //                     'jadwal' => \Carbon\Carbon::today('Asia/Jakarta')->format('Y-m-d H:i:s'),
        //                     // 'max_quota' => $max_quota[$studentData2[$i]['mapel_terpilih']],
        //                     // 'max_total_class' => $max_total_class[$mapelId1],
        //                 ];
                        
        //                 \App\Models\Kelaslm::insert($value);

        //             }else{
        //                 array_push($ignoredRecord2, $studentData2[$i]);
        //             }
        //         }   
        //     }
        // }
        //     \Illuminate\Support\Facades\Log::debug($ignoredRecord2);
        // else{
        //     $className = $courseData[$idSelectedMapel3]['nama_mapel'] . '_' . chr($max_total_class[$mapelId3] + 65);
                
        //     if ($max_quota[$mapelId3] % $courseData[$idSelectedMapel3]['max_kuota_kelas'] ==  $courseData[$idSelectedMapel3]['kuota_kelas']-1) {
        //         $max_total_class[$mapelId3]++;
        //     }

        //     $max_quota[$mapelId3]++;
            
        //     // Save data
        //     $value = [
        //         'id_siswa' => $studentData[$i]['id'],
        //         'id_mapellm' => $mapelId2,
        //         'nama_kelas' => $className,
        //         'jadwal' => \Carbon\Carbon::today('Asia/Jakarta')->format('Y-m-d H:i:s'),
        //         // 'max_quota' => $max_quota[$studentData[$i]['mapel_terpilih']],
        //         // 'max_total_class' => $max_total_class[$mapelId3],
        //     ];
            
        //     // \Illuminate\Support\Facades\Log::debug($value);
        //     array_push($record, $value);
        // }

        // \Illuminate\Support\Facades\Log::debug($record);
        // if (!\App\Models\Kelaslm::insert($record)) {
        //     return response()->json(['errors' => [0 => 'Fail to update data']]);
        // }

        // return response()->json(['success' => 'Data is successfully added']);
    }
}
