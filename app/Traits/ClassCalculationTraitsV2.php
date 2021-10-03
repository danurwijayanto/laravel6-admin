<?php

namespace App\Traits;

use App\models\Mapellm;
use App\Models\Siswa;
use Illuminate\Support\Facades\Log;

trait ClassCalculationTraitsV2
{
    public function doClassCalculationV2()
    {
        $dataSiswa = \App\Models\Siswa::with('detailLm1', 'detailLm2', 'detailLm3')->get()->toArray();

        $classOrder = $this->studentClassOrder($dataSiswa);
        $assignClass = $this->assignClass($classOrder);
        $insertDb = $this->calculationInsertDb($assignClass);
        // return $insertDb;
    }

    private function studentClassOrder($dataSiswa)
    {
        if (empty($dataSiswa)) {
            return json_encode(['fail' => 'Student data is null']);
        }

        $pilihan1 = [];
        $pilihan2 = [];
        $pilihan3 = [];

        $listMapel = Mapellm::get()->toArray();

        $pilihan1 = $listMapel;
        $pilihan2 = $listMapel;
        $pilihan3 = $listMapel;

        /**
         * Sort Pilihan 1
         */
        for ($i=0; $i < count($pilihan1); $i++) { 
            // Mengurutkan berdasarkan mapel pilihan
            $user = Siswa::where('pilih_lm1', $pilihan1[$i]['id'])->get()->toArray();

            usort($user, function ($a, $b) {
                return $b['vektor_v1'] <=> $a['vektor_v1'];
            });
            $pilihan1[$i]['list_mahasiswa'] = $user;
        }

        /**
         * Sort Pilihan 2
         */
        for ($i=0; $i < count($pilihan2); $i++) { 
            // Mengurutkan berdasarkan mapel pilihan
            $user = Siswa::where('pilih_lm2', $pilihan2[$i]['id'])->get()->toArray();

            usort($user, function ($a, $b) {
                return $b['vektor_v2'] <=> $a['vektor_v2'];
            });
            $pilihan2[$i]['list_mahasiswa'] = $user;
        }

        /**
         * Sort Pilihan 3
         */
        for ($i=0; $i < count($pilihan3); $i++) { 
            // Mengurutkan berdasarkan mapel pilihan
            $user = Siswa::where('pilih_lm3', $pilihan3[$i]['id'])->get()->toArray();

            usort($user, function ($a, $b) {
                return $b['vektor_v3'] <=> $a['vektor_v3'];
            });
            $pilihan3[$i]['list_mahasiswa'] = $user;
        }
          
        return $dataSiswa = [
            "pilihan1" => $pilihan1,
            "pilihan2" => $pilihan2,
            "pilihan3" => $pilihan3,
        ];
    }

    private function assignClass($data)
    {


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

        $ignoredRecord = [];
        Log::debug($max_quota);
        $pilihan1Data = $studentData['pilihan1'];
        $pilihan2Data = $studentData['pilihan2'];
        $pilihan3Data = $studentData['pilihan3'];
        
        /**
         * Pilihan 1
         */
        for ($i = 0; $i < count($pilihan1Data); $i++) {
            $className = "";

            $mapelId = $pilihan1Data[$i]['id'];
            
            for ($j=0; $j < count($pilihan1Data[$i]['list_mahasiswa']); $j++) {
                $siswa = $pilihan1Data[$i]['list_mahasiswa'];
                $mapelVector = $siswa[$j]['vektor_v1'];
                $idSelectedMapel = array_search($mapelId, array_column($courseData, 'id'));
                
                if ($max_quota[$mapelId] < $courseData[$idSelectedMapel]['max_kuota_kelas']) {
                        
                    $max_quota[$mapelId]++;

                    // Save data
                    $value = [
                        'id_siswa' => $siswa[$j]['id'],
                        'id_mapellm' => $mapelId,
                        'nama_kelas' => '',
                        'jadwal' => \Carbon\Carbon::today('Asia/Jakarta')->format('Y-m-d H:i:s'),
                        'nilai' => $mapelVector,
                    ];

                    array_push($dataProcess[$mapelId], $value);

                }else if($max_quota[$mapelId] >= $courseData[$idSelectedMapel]['max_kuota_kelas']){
                    array_push($ignoredRecord, $siswa[$j]);
                }
            }
        }

        /**
         * Pilihan 2
         */
        for ($i = 0; $i < count($pilihan2Data); $i++) {
            $className = "";

            $mapelId = $pilihan2Data[$i]['id'];
            
            for ($j=0; $j < count($pilihan2Data[$i]['list_mahasiswa']); $j++) {
                $siswa = $pilihan2Data[$i]['list_mahasiswa'];
                $mapelVector = $siswa[$j]['vektor_v2'];
                $idSelectedMapel = array_search($mapelId, array_column($courseData, 'id'));
                
                if ($max_quota[$mapelId] < $courseData[$idSelectedMapel]['max_kuota_kelas']) {
                        
                    $max_quota[$mapelId]++;

                    // Save data
                    $value = [
                        'id_siswa' => $siswa[$j]['id'],
                        'id_mapellm' => $mapelId,
                        'nama_kelas' => '',
                        'jadwal' => \Carbon\Carbon::today('Asia/Jakarta')->format('Y-m-d H:i:s'),
                        'nilai' => $mapelVector,
                    ];

                    array_push($dataProcess[$mapelId], $value);

                }elseif($max_quota[$mapelId] >= $courseData[$idSelectedMapel]['max_kuota_kelas']){
                    array_push($ignoredRecord, $siswa[$j]);
                }
            }
        }

         /**
         * Pilihan 3
         */
        for ($i = 0; $i < count($pilihan3Data); $i++) {
            $className = "";

            $mapelId = $pilihan3Data[$i]['id'];
            
            for ($j=0; $j < count($pilihan3Data[$i]['list_mahasiswa']); $j++) {
                $siswa = $pilihan3Data[$i]['list_mahasiswa'];
                $mapelVector = $siswa[$j]['vektor_v3'];
                $idSelectedMapel = array_search($mapelId, array_column($courseData, 'id'));
                
                if ($max_quota[$mapelId] < $courseData[$idSelectedMapel]['max_kuota_kelas']) {
                        
                    $max_quota[$mapelId]++;

                    // Save data
                    $value = [
                        'id_siswa' => $siswa[$j]['id'],
                        'id_mapellm' => $mapelId,
                        'nama_kelas' => '',
                        'jadwal' => \Carbon\Carbon::today('Asia/Jakarta')->format('Y-m-d H:i:s'),
                        'nilai' => $mapelVector,
                    ];

                    array_push($dataProcess[$mapelId], $value);

                }elseif($max_quota[$mapelId] >= $courseData[$idSelectedMapel]['max_kuota_kelas']){

                    array_push($ignoredRecord, $siswa[$j]);
                }
            }
        }

        Log::debug([
            "ignored_record" => $ignoredRecord, 
            "dataProcess" => $dataProcess, 
        ]);


        // for ($i = 0; $i < count($ignoredRecord); $i++) {
        //     $mapelId3 = $ignoredRecord[$i]['urutan_lintas_minat'][2]['mapel_id'];
        //     $mapelVector3 = $ignoredRecord[$i]['urutan_lintas_minat'][2]['vector'];
            
        //     $idSelectedMapel3 = array_search($mapelId3, array_column($courseData, 'id'));

        //     $isSetMapelPilihan1 = array_search($ignoredRecord[$i]['id'], array_column($dataProcess[$ignoredRecord[$i]['urutan_lintas_minat'][0]['mapel_id']], 'id_siswa'));
        //     $isSetMapelPilihan2 = array_search($ignoredRecord[$i]['id'], array_column($dataProcess[$ignoredRecord[$i]['urutan_lintas_minat'][1]['mapel_id']], 'id_siswa'));

        //     // Log::debug([
        //     //     "isSetMapelPilihan1" => $isSetMapelPilihan1, 
        //     //     "isSetMapelPilihan2" => $isSetMapelPilihan2]);

        //     if (empty( $isSetMapelPilihan1) && empty($isSetMapelPilihan2))
        //     {
        //         $value = [
        //             'id_siswa' => $ignoredRecord[$i]['id'],
        //             'id_mapellm' => $mapelId3,
        //             'nama_kelas' => '',
        //             'jadwal' => \Carbon\Carbon::today('Asia/Jakarta')->format('Y-m-d H:i:s'),
        //             'nilai' => $mapelVector3,
        //         ];

        //         // Log::debug([
        //         //     "note" => "is empty", 
        //         //     "nilai minimal" => "", 
        //         //     "mapel set" => "",
        //         //     "mapel set" => $value]);
        //         // Masukkan ke ignored record index yang kereplace
        //         // array_push($ignoredRecord2, $studentDataMinimal);
        //         // Ganti index dataprocess dengan nilai yang baru
        //         // $dataProcess[$mapelId3][array_search(min($arrayColumn), $arrayColumn)] = $value;
        //     }
            
        //     if ($max_quota[$mapelId3] < $courseData[$idSelectedMapel3]['max_kuota_kelas']) {

        //         $max_quota[$mapelId3]++;

        //         // Save data
        //         $value = [
        //             'id_siswa' => $ignoredRecord[$i]['id'],
        //             'id_mapellm' => $mapelId3,
        //             'nama_kelas' => '',
        //             'jadwal' => \Carbon\Carbon::today('Asia/Jakarta')->format('Y-m-d H:i:s'),
        //             'nilai' => $mapelVector3,
        //         ];

        //         array_push($dataProcess[$mapelId3], $value);

        //     }elseif($max_quota[$mapelId3] >= $courseData[$idSelectedMapel3]['max_kuota_kelas']){
        //         // $studentScore = $studentData[$i]['nilai_raport'];
        //         // \Illuminate\Support\Facades\Log::debug([
        //         //     "max quota" => $max_quota,
        //         //     "courseData" => $courseData,
        //         // ]);

        //         $arrayColumn = array_column($dataProcess[$mapelId3], 'nilai');
        //         $nilaiMinimal = min($arrayColumn);
        //         $nilaiMinimalArray = $dataProcess[$mapelId3][array_search($nilaiMinimal, $arrayColumn)];
        //         $studentDataMinimal = $studentData[array_search($nilaiMinimalArray['id_siswa'], array_column($studentData, 'id'))];
                
        //         if ($mapelVector3 > $nilaiMinimal){
        //             // Save data
        //             $value = [
        //                 'id_siswa' => $studentData[$i]['id'],
        //                 'id_mapellm' => $mapelId3,
        //                 'nama_kelas' => '',
        //                 'jadwal' => \Carbon\Carbon::today('Asia/Jakarta')->format('Y-m-d H:i:s'),
        //                 'nilai' => $mapelVector3,
        //             ];

        //             $isSetMapelPilihan1 = array_search($ignoredRecord[$i]['id'], array_column($dataProcess[$ignoredRecord[$i]['urutan_lintas_minat'][0]['mapel_id']], 'id_siswa'));
        //             $isSetMapelPilihan2 = array_search($ignoredRecord[$i]['id'], array_column($dataProcess[$ignoredRecord[$i]['urutan_lintas_minat'][1]['mapel_id']], 'id_siswa'));

        //             if (empty( $isSetMapelPilihan1) && empty($isSetMapelPilihan2))
        //             {
        //                 // Masukkan ke ignored record index yang kereplace
        //                 array_push($ignoredRecord2, $studentDataMinimal);
        //                 // Ganti index dataprocess dengan nilai yang baru
        //                 $dataProcess[$mapelId3][array_search(min($arrayColumn), $arrayColumn)] = $value;
        //             }
                    
        //         }else{
        //             array_push($ignoredRecord2,  $studentData[$i]);
        //         }  
        //     }
        // }

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
