<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use Illuminate\Http\Request;
use App\User;
use DataTables;
use Facade\Ignition\QueryRecorder\Query;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Validator;
use Illuminate\Database\QueryException;


class UserController extends Controller
{
    private $controllerDetails;

    public function __construct()
    {
        $this->middleware('auth');

        $this->controllerDetails = [
            "currentPage" => "Users",
            "pageDescription" => "Users Management Page"
        ];
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $role = Role::get();

        return view('v1.admin.content.user')->with([
            'detailController' => $this->controllerDetails,
            'roleList' => $role
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
            'name' => 'bail|required',
            'email' => 'required',
            // 'password' => 'required',
            'role_id' => 'required'
        ]);

        $user = new User;
        $user->name = $request->username;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->role_id = $request->role_id;

        return view('v1.admin.content.user')->with([
            'detailController' => $this->controllerDetails,
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $user = User::find($id);

        return json_encode($user);
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
            'user_id' => 'bail|required',
            'username' => 'required',
            'email' => 'required',
            // 'password' => 'required',
            'role' => 'required'
        );

        $error = Validator::make($request->all(), $validatorRules);

        if ($error->fails()) {
            return response()->json(['errors' => $error->errors()->all()]);
        }

        // Save data
        $user = User::find($request->user_id);
        if (empty($user)) {
            return response()->json(['errors' => [0 => 'Data not found !']]);
        }
        $user->name = $request->username;
        $user->email = $request->email;
        $user->role_id = $request->role;

        if (!$user->save()) {
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
        if($id == 1){
            return response()->json(['errors' => [0 => 'Cannot delete Super User Account!']]);
        }

        $user = User::find($id);
        
        if (empty($user)) {
            return response()->json(['errors' => [0 => 'Data not found !']]);
        }

        if (!$user->delete()) {
            return response()->json(['errors' => [0 => 'Fail to update data']]);
        } else {
            return response()->json(['success' => 'Data is successfully updated']);
        }
    }

    public function dataTablesGetAllData()
    {
        $data = User::with('role')->get();

        return DataTables::of($data)
            ->addColumn('action', function ($data) {
                $button = '<button type="button" name="edit" id="' . $data->id . '" class="edit btn btn-primary btn-sm">Edit</button>';
                $button .= '&nbsp;&nbsp;&nbsp;<button type="button" name="delete" id="' . $data->id . '" class="delete btn btn-danger btn-sm" ' . ($data->id == 1 ? "disabled" : "") . '>Delete</button>';
                return $button;
            })
            ->rawColumns(['action'])
            ->make(true);
    }
}
