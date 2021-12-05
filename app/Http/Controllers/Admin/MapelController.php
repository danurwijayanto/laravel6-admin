<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Mapellm;
use DataTables;
use Validator;

class MapelController extends Controller
{
    private $controllerDetails;

    public function __construct()
    {
        $this->middleware('auth');

        $this->controllerDetails = [
            "currentPage" => "Course",
            "pageDescription" => "Course Management Page"
        ];
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('v1.admin.content.mapel')->with([
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
        $request->validate([
            'course_code' => 'bail|required|max:11',
            'course_name' => 'required|max:35',
            'number_of_classes' => 'required',
            'class_quota' => 'required',
        ]);

        $course = new Mapellm;
        $course->kode_mapel = $request->course_code;
        $course->nama_mapel = strtolower($request->course_name);
        $course->jumlah_kelas = $request->number_of_classes;
        $course->kuota_kelas = $request->class_quota;

        if (!$course->save()) {
            return response()->json(['errors' => [0 => 'Fail to update data']]);
        } else {
            return response()->json(['success' => 'Data is successfully updated']);
        }
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
        $course = Mapellm::find($id);

        return json_encode($course);
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
            'course_code' => 'bail|required|max:11',
            'course_name' => 'required|max:35',
            'number_of_classes' => 'required',
            'class_quota' => 'required',
        );

        $error = Validator::make($request->all(), $validatorRules);

        if ($error->fails()) {
            return response()->json(['errors' => $error->errors()->all()]);
        }

        // Save data
        $course = Mapellm::find($request->course_id);
        if (empty($course)) {
            return response()->json(['errors' => [0 => 'Data not found !']]);
        }
        $course->kode_mapel = $request->course_code;
        $course->nama_mapel = strtolower($request->course_name);
        $course->jumlah_kelas = $request->number_of_classes;
        $course->kuota_kelas = $request->class_quota;

        if (!$course->save()) {
            return response()->json(['errors' => [0 => 'Fail to update data']]);
        } else {
            return response()->json(['success' => 'Data is successfully updated']);
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
        // Check if super user or not
        $course = Mapellm::find($id);

        if (empty($course)) {
            return response()->json(['errors' => [0 => 'Data not found !']]);
        }

        if (!$course->delete()) {
            return response()->json(['errors' => [0 => 'Fail to update data']]);
        } else {
            return response()->json(['success' => 'Data is successfully updated']);
        }
    }

    public function dataTablesGetAllData()
    {
        $data = Mapellm::get();

        return DataTables::of($data)
            ->addColumn('action', function ($data) {
                $button = '<button type="button" name="edit" id="' . $data->id . '" class="edit btn btn-primary btn-sm">Edit</button>';
                $button .= '&nbsp;&nbsp;&nbsp;<button type="button" name="delete" id="' . $data->id . '" class="delete btn btn-danger btn-sm">Delete</button>';
                return $button;
            })
            ->rawColumns(['action'])
            ->make(true);
    }
}
