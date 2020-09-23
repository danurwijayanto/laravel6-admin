<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kelaslm;
use Illuminate\Http\Request;
use DataTables;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Validator;


class LintasMinatClassController extends Controller
{
    private $controllerDetails;

    public function __construct()
    {
        $this->middleware('auth');

        $this->controllerDetails = [
            "currentPage" => "Cross-interest Class",
            "pageDescription" => "Cross-interest Class List"
        ];
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('v1.admin.content.crossInterest')->with([
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
        $classData = Kelaslm::where("nama_kelas", $id)->first();
        if (!$classData) return view('v1.admin.content.crossInterestDetail')->with(['error' => "Warning : Class not found !"]);

        $studentList = Kelaslm::where("nama_kelas", $id)->get();
        if (!$studentList) return view('v1.admin.content.crossInterestDetail')->with(['error' => "Warning : Student not found !"]);

        return view('v1.admin.content.crossInterestDetail')->with([
            'detailController' => $this->controllerDetails,
            'classData' => $classData,
            'studentList' => $studentList
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data = Kelaslm::where('nama_kelas', $id)->with(['student','course'])->first();

        return json_encode($data);
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
            'cross_interest_class_id' => 'bail|required',
            'day' => 'required',
            'time' => 'required',
            'teacher' => 'required',
        );

        $error = Validator::make($request->all(), $validatorRules);
        if ($error->fails()) {
            return response()->json(['errors' => $error->errors()->all()]);
        }
        
        // Log::debug($request);
        // Save data
        $classData = Kelaslm::find($request->cross_interest_class_id);
        
        if (empty($classData)) {
            return response()->json(['errors' => [0 => 'Data not found !']]);
        }

        $classData->pengajar = $request->teacher;
        $classData->jadwal = $request->day.",".$request->time;

        if (!$classData->save()) {
            return response()->json(['errors' => [0 => 'Fail to update data']]);
        } else {
            return response()->json(['success' => 'Data is successfully updated']);
        }

        // Log::debug($request);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function dataTablesGetAllData()
    {
        $data = DB::table('kelaslm')->selectRaw('count("id_siswa") as jumlah_siswa ,id_mapellm, nama_kelas, jadwal')
            ->groupBy('nama_kelas', 'id_mapellm', 'jadwal')
            ->get();

        return DataTables::of($data)
            ->addColumn('action', function ($data) {
                $button = '<button type="button" name="detail" id="' . $data->nama_kelas . '" class="detail btn btn-secondary btn-sm">Detail</button>';
                $button .= '&nbsp;&nbsp;&nbsp<button type="button" name="edit" id="' . $data->nama_kelas . '" class="edit btn btn-primary btn-sm">Edit</button>';
                $button .= '&nbsp;&nbsp;&nbsp;<button type="button" name="delete" id="' . $data->nama_kelas . '" class="delete btn btn-danger btn-sm" >Delete</button>';
                return $button;
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function dataTablesGetDetailData($className)
    {
        $data = Kelaslm::where('nama_kelas', $className)->with(['student','course'])->get();

        Log::debug($data);

        return DataTables::of($data)
            ->make(true);
    }
}
