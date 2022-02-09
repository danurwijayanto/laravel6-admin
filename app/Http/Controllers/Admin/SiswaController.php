<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Mapellm;
use App\Models\Siswa;
use App\Traits\ExcelDataTraits;
use DataTables;
use Validator;
use Illuminate\Support\Facades\Log;

class SiswaController extends Controller
{
    private $controllerDetails;

    use ExcelDataTraits;

    public function __construct()
    {
        $this->middleware('auth');

        $this->controllerDetails = [
            "currentPage" => "Siswa",
            "pageDescription" => "Manajemen Siswa"
        ];
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('v1.admin.content.student')->with([
            'detailController' => $this->controllerDetails,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $user = Siswa::with('detailLm1', 'detailLm2', 'detailLm3')->find($id);
        return response()->json($user);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        // Request Validation
        $validatorRules = array(
            'nis' => 'bail|required',
            'name' => 'required',
            'class' => 'required',
        );

        $error = Validator::make($request->all(), $validatorRules);

        if ($error->fails()) {
            return response()->json(['errors' => $error->errors()->all()]);
        }

        // Save data
        $student = Siswa::find($request->student_id);
        if (empty($student)) {
            return response()->json(['errors' => [0 => 'Data tidak ditemukan']]);
        }
        $student->nis = $request->nis;
        $student->nama_siswa = $request->name;
        $student->kelas = $request->class;
        $student->jenis_kelamin = $request->gender;

        if (!$student->save()) {
            return response()->json(['errors' => [0 => 'Gagal merubah atau menyimpan data']]);
        } else {
            return response()->json(['success' => 'Data berhasil dirubah']);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $siswa = Siswa::find($id);

        if (empty($siswa)) {
            return response()->json(['errors' => [0 => 'Data tidak ditemukan']]);
        }

        if (!$siswa->delete()) {
            return response()->json(['errors' => [0 => 'Gagal merubah atau menyimpan data']]);
        } else {
            return response()->json(['success' => 'Data berhasil dirubah']);
        }
    }

    public function dataTablesGetAllData()
    {
        $data = Siswa::get();

        return DataTables::of($data)
            ->addColumn('action', function ($data) {
                $button = '<button type="button" name="detail" id="' . $data->id . '" class="detail btn btn-secondary btn-sm">Detail</button>';
                $button .= '&nbsp;&nbsp;&nbsp<button type="button" name="edit" id="' . $data->id . '" class="edit btn btn-primary btn-sm">Rubah</button>';
                $button .= '&nbsp;&nbsp;&nbsp;<button type="button" name="delete" id="' . $data->id . '" class="delete btn btn-danger btn-sm" >Hapus</button>';
                return $button;
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function storeExcelData(Request $request)
    {
        if (!empty($request->student_data)) {
            $file = $request->file('student_data');
            $new_name = rand() . '.' . $file->getClientOriginalExtension();
            $file_path = public_path('file') . "/" . $new_name;
            $file->move(public_path('file'), $new_name);

            // Change process to traits
            $export = $this->doExport($file_path);
            if (isset(json_decode($export)->fail)) {
                return response()->json(['errors' => [0 => json_decode($export)->fail]]);
            }

            // Insert into database
            $data = json_decode($export, true);

            try {
                if (Siswa::insert($data['data'])) {
                    // Delete file
                    unlink($file_path);

                    return response()->json(['success' => 'Data is successfully added']);
                }
            } catch (\Exception $e) {
                return response()->json(['errors' => [0 => $e->getMessage()]]);
            }

            return response()->json(['errors' => [0 => 'Fail to adding data']]);
        } else {
            return response()->json(['errors' => [0 => 'Fail to adding data']]);
        }
    }
}
