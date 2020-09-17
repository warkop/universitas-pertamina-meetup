<?php

namespace App\Http\Controllers;

use App\Http\Requests\InstitutionStoreRequest;
use App\Http\Resources\InstitutionListDataResource;
use App\Models\Institution;
use Yajra\DataTables\Facades\DataTables;

class InstitutionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $model = Institution::get();

        return DataTables::of(InstitutionListDataResource::collection($model))->toJson();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(InstitutionStoreRequest $request, Institution $institution)
    {
        $request->validated();
        $institution->name      = $request->input('name');
        $institution->email     = $request->input('email');
        $institution->address   = $request->input('address');
        $institution->phone     = $request->input('phone');
        $institution->save();

        $this->responseCode = 200;
        $this->responseMessage = 'Data berhasil disimpan';
        $this->responseData = $institution;

        return response()->json($this->getResponse(), $this->responseCode);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Institution $institution)
    {
        $this->responseCode = 200;
        $this->responseData = $institution;

        return response()->json($this->getResponse(), $this->responseCode);
    }

    public function getAll()
    {
        $institution = Institution::all()->makeHidden(['created_at', 'updated_at', 'created_by', 'updated_by']);

        $this->responseCode = 200;
        $this->responseData = $institution;

        return response()->json($this->getResponse(), $this->responseCode);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Institution $institution)
    {
        $institution->delete();

        $this->responseCode = 200;
        $this->responseMessage = 'Data berhasil dihapus';

        return response()->json($this->getResponse(), $this->responseCode);
    }
}
