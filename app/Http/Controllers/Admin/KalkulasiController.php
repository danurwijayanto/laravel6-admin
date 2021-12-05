<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Siswa;
use App\Traits\ClassCalculationTraits;
use Illuminate\Support\Facades\Log;
use DataTables;
use App\Traits\WeightedProductCalculationTraits;
use App\Traits\ClassCalculationTraitsV2;

class KalkulasiController extends Controller
{
    private $controllerDetails;

    use WeightedProductCalculationTraits;
    use ClassCalculationTraits;
    // use ClassCalculationTraitsV2;

    public function __construct()
    {
        $this->middleware('auth');

        $this->controllerDetails = [
            "currentPage" => "Result",
            "pageDescription" => "Calculation Result Page"
        ];
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('v1.admin.content.kalkulasi')->with([
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
        $data = Siswa::with('detailLm1', 'detailLm2', 'detailLm3')->get();

        return DataTables::of($data)->make(true);
    }

    public function calculation()
    {
        $calculate = $this->doCalculate();

        if (isset(json_decode($calculate)->fail)) {
            return response()->json(['errors' => [0 => json_decode($calculate)->fail]]);
        }

        return response()->json(['success' => 'Data is successfully added']);
    }

    public function classCalculation() {
        $calculate = $this->doClassCalculation();

        if (isset(json_decode($calculate)->fail)) {
            return response()->json(['errors' => [0 => json_decode($calculate)->fail]]);
        }

        return response()->json(['success' => 'Data is successfully added']);
    }
}
