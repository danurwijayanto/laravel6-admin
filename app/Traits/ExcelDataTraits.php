<?php

namespace App\Traits;

use Illuminate\HTTP\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use Illuminate\Support\Facades\Log;
use App\Models\Mapellm;

trait ExcelDataTraits
{

    public function doExport($path = "")
    {
        $readData = $this->readData($path);
        $validationData = $this->validationData($readData);

        if (isset(json_decode($validationData)->fail)) {
            return $validationData;
        }

        return json_encode(['success' => 'Adding data success']);
    }

    public function readData($path = "")
    {
        $reader = new Xlsx();
        $reader->setReadDataOnly(true);
        $spreadsheet = $reader->load($path);

        $sheetData   = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);

        return $sheetData;
    }

    public function validationData($sheetData)
    {
        // Data Mapel
        $mapelList = Mapellm::get()->toArray();
        // Log::debug($mapelList);


        // inisialisasi data dan pengecekan apakah kode mapel benar
        for ($i = 2; $i <= count($sheetData); $i++) {
            $data['nip'][]      = $sheetData[$i]['A'];
            $data['name'][]     = $sheetData[$i]['B'];
            $data['class'][]    = $sheetData[$i]['C'];
            $data['score'][]    = $sheetData[$i]['D'];
            $data['lm1'][]     = $sheetData[$i]['E'];
            $data['lm2'][]     = $sheetData[$i]['F'];
            $data['lm3'][]     = $sheetData[$i]['G'];

            // Check mapel lintas minat 1
            if ($sheetData[$i]['A'] != '' && $sheetData[$i]['D'] != '') {
                if (!in_array(strtolower($sheetData[$i]['E']), array_column($mapelList, 'nama_mapel'), true)) {
                    return json_encode(['fail' => 'Course "' . $sheetData[$i]['E'] . '" not found ! Please create course first']);
                }

                if (!in_array(strtolower($sheetData[$i]['F']), array_column($mapelList, 'nama_mapel'), true)) {
                    return json_encode(['fail' => 'Course "' . $sheetData[$i]['F'] . '" not found ! Please create course first']);
                }

                if (!in_array(strtolower($sheetData[$i]['G']), array_column($mapelList, 'nama_mapel'), true)) {
                    return json_encode(['fail' => 'Course "' . $sheetData[$i]['G'] . '" not found ! Please create course first']);
                }
                //pembuatan list data yang diinputkan
                $value  .= "('" . $sheetData[$i]['B'] . "','" . $sheetData[$i]['A'] . "','" . $sheetData[$i]['C'] . "','" . $sheetData[$i]['D'] . "','" . $row1['ID_MAPEL'] . "','" . $row2['ID_MAPEL'] . "','" . $row3['ID_MAPEL'] . "'),";
            } else {
                return json_encode(['fail' => 'Student nip or student score is null']);
            }

            //     $row1 = $this->query($query1)->fetch_assoc();

            //     $row2 = $this->query($query2)->fetch_assoc();

            //     $row3 = $this->query($query3)->fetch_assoc();


        }
        // Log::debug($data);
    }
}
