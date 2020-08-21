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

class StudentController extends Controller
{
    private $controllerDetails;

    use ExcelDataTraits;

    public function __construct()
    {
        $this->middleware('auth');

        $this->controllerDetails = [
            "currentPage" => "Student",
            "pageDescription" => "Student Management Page"
        ];
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('v1.admin.content.studentList')->with([
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
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
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
        $data = Siswa::get();

        return DataTables::of($data)
            ->addColumn('action', function ($data) {
                $button = '<button type="button" name="edit" id="' . $data->id . '" class="edit btn btn-primary btn-sm">Edit</button>';
                $button .= '&nbsp;&nbsp;&nbsp;<button type="button" name="delete" id="' . $data->id . '" class="delete btn btn-danger btn-sm" >Delete</button>';
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
            $data = json_decode($export, true) ;

            if (Siswa::insert($data['data'])) {
                // Delete file
                unlink($file_path);

                return response()->json(['success' => 'Data is successfully added']);
            }

            return response()->json(['errors' => [0 => 'Fail to adding data']]);
        } else {
            return response()->json(['errors' => [0 => 'Fail to adding data']]);
        }
    }
}
