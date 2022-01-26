<?php

namespace App\Traits;

use Illuminate\HTTP\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx as WriterXlsx;
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

        return $validationData;
    }

    private function readData($path = "")
    {
        $reader = new Xlsx();
        $reader->setReadDataOnly(true);
        $spreadsheet = $reader->load($path);

        $sheetData   = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);

        return $sheetData;
    }

    private function validationData($sheetData)
    {
        $value = [];
        // Get data mapel to DB
        $mapelList = Mapellm::get()->toArray();

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

                // Mencari id dari mata kuliah
                $findLm1Data = array_search(strtolower($sheetData[$i]['E']), array_column($mapelList, 'nama_mapel'));
                $findLm2Data = array_search(strtolower($sheetData[$i]['F']), array_column($mapelList, 'nama_mapel'));
                $findLm3Data = array_search(strtolower($sheetData[$i]['G']), array_column($mapelList, 'nama_mapel'));

                $lm1DataId = $mapelList[$findLm1Data]['id'];
                $lm2DataId = $mapelList[$findLm2Data]['id'];
                $lm3DataId = $mapelList[$findLm3Data]['id'];

                //pembuatan list data yang diinputkan
                // $value  .= "('" . $sheetData[$i]['A'] . "','" . $sheetData[$i]['B'] . "','" . $sheetData[$i]['C'] . "','" . $sheetData[$i]['D'] . "','" .  $lm1DataId . "','" .  $lm2DataId . "','" . $lm3DataId . "'),";
                $data = [
                    'nis' => $sheetData[$i]['A'],
                    'nama_siswa' => $sheetData[$i]['B'],
                    'kelas' => $sheetData[$i]['C'],
                    'nilai_raport' => $sheetData[$i]['D'],
                    'pilih_lm1' => $lm1DataId,
                    'pilih_lm2' => $lm2DataId,
                    'pilih_lm3' => $lm3DataId,
                    'jenis_kelamin' => $sheetData[$i]['H'],
                ];

                array_push($value, $data);
            } else {
                return json_encode(['fail' => 'Student nip or student score is null']);
            }
        }
        // Menghilangkan koma di belakang
        // $value  = substr($value,0,-1);

        return json_encode([
            'success' => 'Validation Successfully !',
            'data' => $value,
        ]);
    }

    public function writeExcel($data)
    {
        if (empty($data)) return response()->json(['errors' => [0 => 'Data kosong, tidak dapat export data !']]);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', $data[0]["nama_kelas"]);
        $sheet->setCellValue('A2', "Kelas Awal");
        $sheet->setCellValue('B2', "Nama Siswa");

        for ($i = 0; $i < count($data); $i++) {
            $row = $i + 3;
            $sheet->setCellValue('A' . $row, $data[$i]['student']["kelas"]);
            $sheet->setCellValue('B' . $row, $data[$i]['student']["nama_siswa"]);
        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $data[0]["nama_kelas"] . '.xlsx"');
        header('Cache-Control: max-age=0');

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
        // $writer->save('./' . $data[0]["nama_kelas"] . '.xlsx');
        return json_encode([
            'success' => 'Export Successfully !'
        ]);
    }
}
